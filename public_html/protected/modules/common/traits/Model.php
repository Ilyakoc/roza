<?php
/**
 * Trait: для моделей.
 * 
 * Переопределение методов
 * __get()
 * __set()
 * attachBehaviors()
 *
 */
namespace common\traits;

use common\components\helpers\HArray as A;

/**
 * Класс дополнительных данных для составного метода модели.
 */
class ModelMethodData  
{
	/**
	 * @var string имя метода
	 */
	public $name;
	
	/**
	 * @var array[MethodDataItem] данные для метода
	 */	
	public $data=[];
	
	/**
	 * @var array массив объединенных данных элементов.
	 */
	private $itemsData=[];
	private $itemsDataInitialized=false;
	
	/**
	 * Конструктор класса
	 * @param string|NULL $name имя метода
	 * @param array[ModelMethodDataItem] $data данные
	 */
	public function __construct($name=null, $data=[])
	{
		$this->name=$name;
		$this->data=$data;
	}
	
	/**
	 * @param array $data начальный массив данных
	 * @param boolean $prepend добавить переданный массив $data в начало. 
	 * По умолчанию (FALSE) добавить в конец.
	 * @param array $refresh обновить массив.
	 * @return array массив объединенных данных элементов. 
	 */
	public function getItemsData($data=[], $prepend=false, $refresh=false)
	{
		if($refresh || !$this->itemsDataInitialized) {
			$itemData=[];
			if(!empty($this->data)) {
				foreach($this->data as $item) {
					$itemData=A::m($itemData, $item->data);
				}
			}
			
			if($prepend) $itemData=A::m($data, $itemData);
			else $itemData=A::m($itemData, $data);
			 
			$this->itemsData=$itemData;
			$this->itemsDataInitialized=true;
		}
		return $this->itemsData;
	}
}

/**
 * Класс элемента дополнительных данных для составного метода модели.
 */
class ModelMethodDataItem  
{
	/**
	 * @var \CBehavior объект поведения.
	 */
	public $behavior;
	
	/**
	 * @var string имя поведения.
	 */
	public $behaviorName;
	
	/**
	 * @var string приоритет исполнения.
	 * @todo в разработке
	 */
	public $priority=0;
	
	/**
	 * @var mixed данные
	 */
	public $data;
	
	/**
	 * Конструктор класса
	 * @param \CBehavior|NULL $behavior объект поведения.
	 * @param string|NULL $behaviorName имя поведения.
	 * @param mixed|NULL $data данные.
	 */
	public function __construct($behavior=null, $behaviorName=null, $data=null)
	{
		$this->behavior=$behavior;
		$this->behaviorName=$behaviorName;
		$this->data=$data;
	}
}

/**
 * Основной трейт модели
 *
 */
trait Model
{
	/**
	 * @var array[ModelMethodData] массив данных объединяемых методов модели
	 * Предопределенные ключи и методы.
	 * 's' - 'scopes', 
	 * 'r' - 'rules', 
	 * 'rl' - 'relations', 
	 * 'al' - 'attributeLabels', 
	 * 'aal' - 'afterAttributeLabels'
	 */
	private $_bm=[];
	private $_bmInitialized=false;
	
	private $_bhget=null;
	private $_bhset=null;
	
	/**
	 * @var array массив имен подключенных поведений. 
	 * Массив вида array(name=>true)
	 */
	private $_bn=[];
	
	/**
	 * @var boolean поведения подключены. По умолчанию (FALSE) не подключены.
	 */
	protected $_attached=false;
	
	/**
	 * @var string динамические атрибуты
	 */
	private $_dynamicAttributes=array();

	/**
	 * Модель в процессе подключения.
	 * @return boolean
	 */
	public function isAttaching()
	{
		return ($this->_attached !== true); 
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \CComponent::attachBehaviors()
	 */
	public function attachBehaviors($behaviors)
	{
		parent::attachBehaviors($behaviors);
		
		$this->_attached=true;
	}
		
	/**
	 * (non-PHPdoc)
	 * @see \CComponent::attachBehavior()
	 */
	public function attachBehavior($name, $behavior)
	{
		if(isset($this->_bn[$name])) {
			return $this->asa($name);
		} 
		$this->_bn[$name]=true;
		
		$obj=parent::attachBehavior($name, $behavior);
		
		$bm=&$this->_get_BM();
		foreach($bm as $k=>&$m) {
			if(method_exists($obj, $m->name)) {
				$m->data[]=new ModelMethodDataItem($obj, $name, call_user_func([$obj, $m->name]));
			}
		}
		
		return $obj;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::__get()
	 * 
	 * Поведения или сама модель могут содержать метод __handlerGet($name)
	 * возвращать данный метод должен callable значение, которому будет передан 
	 * параметр $name.
	 * Метод ОБЯЗАТЕЛЬНО должен бросать исключение \common\components\exceptions\PropertyNotFound, 
	 * если атрибут не найден.
	 */
	public function __get($name)
	{
		if(array_key_exists($name, $this->_dynamicAttributes)) {
			return $this->_dynamicAttributes[$name];
		}
		else {
			try {
				if(method_exists($this, '__handlerGet')) {
					$callable=$this->__handlerGet($name);
					if(is_callable($callable)) {
						return call_user_func($callable, $name);
					}
				}
				elseif($behaviors=$this->behaviors()) {
					if(!$this->_attached) {
						return parent::__get($name);
					}
					
					if($this->_bhget === null) {
						$handlers=[];
						foreach($behaviors as $bName=>$config) {
							if(method_exists($this->asa($bName), '__handlerGet')) {
								$handlers[]=$this->asa($bName)->__handlerGet($name);
							}
						}
						$this->_bhget=$handlers;
					}
					if(!empty($this->_bhget)) {
						$value=null;
						$founded=false;
						foreach($this->_bhget as $handler) {
							if(is_callable($handler)) {
								try {
									$value=call_user_func($handler, $name);
									$founded=true;
								}
								catch (\Exception $e) {
									if(!($e instanceof \common\components\exceptions\PropertyNotFound)) {
										throw $e;
									}
								}
							}
						}
						
						if(!$founded) {
							return parent::__get($name);
						}
						
						return $value;
					}
				}
			}
			catch(\Exception $e) {
				if(!($e instanceof \common\components\exceptions\PropertyNotFound)) {
					throw $e;
				}
			}
		}
		
		return parent::__get($name);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::__set()
	 * 
	 * Поведения или сама модель могут содержать метод __handlerSet($name, $value)
	 * возвращать данный метод должен callable значение, которому будут переданы 
	 * два параметра $name и $value.
	 * Метод ОБЯЗАТЕЛЬНО должен бросать исключение \common\components\exceptions\PropertyNotFound, 
	 * если атрибут не найден.
	 */
	public function __set($name, $value)
	{
		if(array_key_exists($name, $this->_dynamicAttributes)) {
			$this->_dynamicAttributes[$name]=$value;
		}
		else {
			try { 
				if(method_exists($this, '__handlerSet')) {
					$callable=$this->__handlerSet($name, $value);
					if(is_callable($callable)) {
						call_user_func($callable, $name, $value);
					}
				}
				elseif($behaviors=$this->behaviors()) {
					if(!$this->_attached) {
						return parent::__set($name, $value);
					}
					
					if($this->_bhset === null) {
						$handlers=[];
						foreach($behaviors as $bName=>$bConfig) {
							if(method_exists($this->asa($bName), '__handlerSet')) {
								$handlers[]=$this->asa($bName)->__handlerSet($name, $value);
							}
						}
						$this->_bhset=$handlers;
					}
					if(!empty($this->_bhset)) {
						$founded=false;
						foreach($this->_bhset as $handler) {
							if(is_callable($handler)) {
								try {
									$value=call_user_func($handler, $name, $value);
									$founded=true;
								}
								catch (\Exception $e) {
									if(!($e instanceof \common\components\exceptions\PropertyNotFound)) {
										throw $e;
									}
								}
							}
						}
						
						if(!$founded) {
							parent::__set($name, $value);
						}
					}
					else {
						parent::__set($name, $value);
					}
				}
				else {
					parent::__set($name, $value);
				}
			}
			catch(\Exception $e) {
				if($e instanceof \common\components\exceptions\PropertyNotFound) {
					parent::__set($name, $value);
				}
				else {
					throw $e;
				}
			}
		}
	}
	
	/**
	 * Добавление динамического атрибута
	 * @param string $name имя атрибута
	 * @param mixed $value значение. По умолчанию NULL.
	 */
	public function addDynamicAttribute($name, $value=null)
	{
		$this->_dynamicAttributes[$name]=$value;
	}
	
	/**
	 * Получить объединенные данные для метода scopes()
	 * @param array $scopes массив scopes() модели.
	 * @param boolean $prepend добавить переданный массив $scopes в начало. 
	 * По умолчанию (FALSE) добавить в конец.
	 * @return array
	 */
	public function getScopes($scopes=[], $prepend=false)
	{
		return $this->_get_BM('s')->getItemsData($scopes, $prepend);
	}
	
	/**
	 * Получить объединенные данные для метода rules()
	 * @param array $rules массив rules() модели.
	 * @param boolean $prepend добавить переданный массив $rules в начало.
	 * По умолчанию (FALSE) добавить в конец.
	 * @return array
	 */
	public function getRules($rules=[], $prepend=false)
	{
		return $this->_get_BM('r')->getItemsData($rules, $prepend);
	}
	
	/**
	 * Получить объединенные данные для метода attributeLabels()
	 * @param array $attributeLabels массив attributeLabels() модели.
	 * @param boolean $prepend добавить переданный массив $attributeLabels в начало.
	 * По умолчанию (FALSE) добавить в конец.
	 * @return array
	 */
	public function getAttributeLabels($attributeLabels=[], $prepend=false)
	{
		$labels=$this->_get_BM('al')->getItemsData($attributeLabels, $prepend);
		
		if($this->_get_BM('aal')) {
			foreach($this->_get_BM('aal')->data as $item) {
				foreach($item->data as $handler) {
					if(is_callable($handler)) {
						$labels=call_user_func($handler, $labels, $item->behavior->owner, $item->behaviorName);
					}
				}
			}
		}
		return $labels;
	}
	
	/**
	 * Получить объединенный массив связей модели.
	 * @param array $relations массив relations() модели.
	 * @param boolean $prepend добавить переданный массив $relations в начало. 
	 * По умолчанию (FALSE) добавить в конец.
	 * @return array <multitype:, mixed, unknown>
	 */
	public function getRelations($relations=[], $prepend=false)
	{
		// необходимо так, как в начале подключаются связи, затем поведения.
		if(!$this->_attached) {
			foreach($this->behaviors() as $name=>$behavior) {
				try { $this->attachBehavior($name, $behavior); }
				catch(\Exception $e) {}
			}
			$this->_attached=true;
		}
		
		return $this->_get_BM('rl')->getItemsData($relations, $prepend);
	}
	
	/**
	 * Получить методы модели,
	 */
	private function &_get_BM($key=null)
	{
		if(!$this->_bmInitialized) {
			$methods=[
				's'=>'scopes',
				'r'=>'rules',
				'rl'=>'relations',
				'al'=>'attributeLabels',
				'aal'=>'afterAttributeLabels'
			];
			foreach($methods as $k=>$name) {
				$this->_bm[$k]=new ModelMethodData($name);
			}
				
			$this->_bmInitialized=true;
		}
		
		if($key) {
			return $this->_bm[$key];
		}
		
		return $this->_bm;
	}
}