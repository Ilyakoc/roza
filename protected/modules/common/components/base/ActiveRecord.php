<?php
namespace common\components\base;

use common\components\helpers\HArray as A;
use common\components\helpers\HDb;

abstract class ActiveRecord extends \CActiveRecord
{
	use \common\traits\Model;

	/**
	 * Returns the static model of the specified AR class.
	 * @param null $className
	 * @return static the static called model class
	 */
	public static function model($className=null)
	{
		if($className===null) $className=get_called_class();
	
		return parent::model($className);
	}
	
	/**
	 * Статическая обертка для метода \CActiveRecord::findByPk()
	 * @see \CActiveRecord::findByPk()
	 */
	public static function modelById($pk, $condition='', $params=[])
	{
		return self::model()->findByPk($pk, $condition, $params);
	}
	
	/**
	 * Events
	 * @return array
	 */
	public function events()
	{
		return A::m(parent::events(), [
			'onBeforeUpdate'=>'beforeUpdate',	
			'onAfterUpdate'=>'afterUpdate'	
		]);
	}
	
	/**
	 * Event: onAfterUpdate
	 */
	public function onBeforeUpdate($event)
	{
		$this->raiseEvent('onBeforeUpdate', $event);
	}
	
	/**
	 * Event: onAfterUpdate
	 */
	public function onAfterUpdate($event)
	{
		$this->raiseEvent('onAfterUpdate', $event);
	}
	
	/**
	 * Обработчик на событие onBeforeUpdate
	 * @return boolean
	 */
	public function beforeUpdate()
	{
		return true;
	}
	
	/**
	 * Обработчик на событие onAfterUpdate
	 * @return boolean
	 */
	public function afterUpdate()
	{
		return true;
	}
	
	/**
	 * Scope: выборка по значениям атрибутов.
	 * @param array $columns массив значений вида array(attribute=>value).
	 * @see \CDbCriteria::addColumnCondition()
	 * @return \common\components\base\ActiveRecord
	 */
	public function wcolumns($columns, $columnOperator='AND', $opertator='AND')
	{
		if(property_exists($this, 'owner')) $owner=$this->owner;
        else $owner=$this;

		$criteria=new \CDbCriteria();
		$criteria->addColumnCondition($columns, $columnOperator, $opertator);
		
		$owner->getDbCriteria()->mergeWith($criteria);
		
		return $owner;
	}
	
	/**
	 * Scope: установить выбираемые поля.
	 * @param string $select выбираемые поля.
	 * @param boolean $replace заменить установленное значение.
	 * Если передано FALSE будет добавлено к текущему.
	 * По умолчанию (TRUE) - заменить. 
	 */
	public function select($select, $replace=true)
	{
		if(property_exists($this, 'owner')) $owner=$this->owner;
        else $owner=$this;

		if($select) {
			if(!$replace && $owner->getDbCriteria()->select && ($owner->getDbCriteria()->select != '*')) {
				$select.=','.$owner->getDbCriteria()->select;
			}
			
			$owner->getDbCriteria()->mergeWith(['select'=>$select]);
		}
		
		return $owner;
	}
	
	/**
	 * Use it, if you wont use "scopes" and "CActiveDataProvider".
	 * @author Rafael Garcia
	 * @link http://www.yiiframework.com/wiki/173/an-easy-way-to-use-escopes-and-cactivedataprovider/
	 *
	 * @param array $options параметры для \CActiveDataProvider. Для совместимости 
	 * со старым кодом, логика такая:
	 * если передан хотя бы один из параметров "criteria", "pagination" или "sort", то 
	 * воспринимается, как новая версия ($options), если ни один из перечисленных параметров
	 * передан не был, то воспринимается как $criteria (старое имя параметра).
	 * @param array|null $pagination опции для постраничной навигации. Используется только для 
	 * поддержки старой версии метода.  
	 * @return \CActiveDataProvider
	 */
	public function getDataProvider($options=[], $pagination=null)
	{
		if(!is_array($options)) $options=[];

		if(!array_key_exists('criteria', $options) 
			&& !array_key_exists('pagination', $options)
			&& !array_key_exists('sort', $options)) 
		{
			$options=['criteria'=>$options];
			if($pagination !== false) {
				$options['pagination']=A::m(['pageSize'=>20], (array)$pagination);
			}
		}
		
		if(empty($options['criteria'])) {
			$options['criteria']=$this->getDbCriteria();
		}
		elseif(is_array($options['criteria'])) {
			$criteria=new \CDbCriteria($options['criteria']);
			$criteria->mergeWith($this->getDbCriteria());
		}
		elseif($options['criteria'] instanceof \CDbCriteria) {
			$options['criteria']->mergeWith($this->getDbCriteria());
		}
	
		return new \CActiveDataProvider($this, $options);
	}
    
	/**
     * Scope: фильтрация
     * @param string $filterName имя в переменной $_GET (или $_POST) 
     * в которой передаются значения для фильтра. По умолчанию "filter".
     * @param array $handlers массив обработчиков вида array(name=>callable), где
     * name - имя параметра (переданного в запросе), callable - обработчик, 
     * который генерируют критерий \CDbCriteria для параметра.
     * 	1) в обработчик передаются параметры ($model, $name, $values, $columnOperator), где
     * 		$model \CActiveRecord текущий объект модели ($this); 
     * 		$name string имя параметра;
     * 		$value mixed значения параметра;
     * 		$columnOperator string оператор объединения полей;
     * 	2) обработчик должен возвращать объект критерия \CDbCriteria, либо NULL.
     * - обработчик может быть передан без имени, тогда он будет применен ко 
     * всем параметрам;
     * - порядок применения обработчиков. В начале применяется назначенный параметру,
     * затем общие, в том порядке, в котором они переданы;
     * - применение общих обработчиков при заданном параметру игнорируется,
     * если параметр $ignore=true;
     * - если обработчик не будет передан, то параметр игнорируется.
     * @param array $operators массив операторов объединения запросов вида
     * array(name=>operator, name2=>[columnOperator, operator]), где
     * 	name - имя параметра;
     *  columnOperator - оператор объединения нескольких значений параметра. 
     *  По умолчанию "OR";
     *  operator - оператор объединения запросов, по умолчанию "AND";
     * - если оператор передан без имени, то он будет являтся общим для всех.
     * - учитывается только первый общий оператор, остальные игнорируются.
     * Поведение аналогично параметру $handlers.
     * @param boolean $post брать данные из $_POST. По умолчанию TRUE.
     * @param boolean $ignore не применять общие обработчики, если 
     * параметру задан свой обработчик. По умолчанию TRUE. 
     */
    public function filter($filterName='filter', $handlers=[], $operators=[], $post=true, $ignore=true)
    {
    	if(empty($handlers)) return $this;
    	
    	if($post && !empty($_POST[$filterName])) $params=$_POST[$filterName];
    	elseif(!$post && !empty($_GET[$filterName])) $params=$_GET[$filterName];
    	else return $this;

    	$defaults=[];
    	foreach($handlers as $name=>$handler) {
    		if(!is_string($name) && is_callable($handler)) $defaults[]=$handler;
    	}
    	
    	$defaultOperator=false;
    	foreach($operators as $name=>$operator) {
    		if(is_string($operator)) {
    			$operator=['OR', strtoupper($operator)];
    		}
    		elseif(is_array($operator) && (count($operator) == 2)) {
    			$operator=[strtoupper($operator[0]), strtoupper($operator[1])];
    		}
    		else {
    			unset($operators[$name]);
    			continue;
    		}
    		    		
    		if(!is_string($name)) {
    			if(!$defaultOperator) $defaultOperator=$operator;
    			unset($operators[$name]);
    		}
    	}
    	
    	if(!$defaultOperator) $defaultOperator=['OR', 'AND'];
    	
    	foreach($params as $name=>$value) {
    		$operator=A::get($operators, $name, $defaultOperator);
    		$mergeDefault=!$ignore;
    		if(($handler=A::get($handlers, $name)) && is_callable($handler)) {
    			$criteria=call_user_func_array($handler, [$this, $name, $value, $operator[0]]);
    			if($criteria instanceof \CDbCriteria) {
    				$this->getDbCriteria()->mergeWith($criteria, $operator[1]);
    			}
    		}
    		else $mergeDefault=true;
    		
    		if($mergeDefault) {
    			foreach($defaults as $default) {
    				$criteria=call_user_func_array($default, [$this, $name, $value, $operator[0]]);
    				if($criteria instanceof \CDbCriteria) {
    					$this->getDbCriteria()->mergeWith($criteria, $operator[1]);
    				}
    			}
    		}
    	}
    	
    	return $this;
    }
    
    /**
     * Получить параметры запроса для фильтра
     * @param unknown $filterName
     * @return Ambigous <multitype:, mixed, unknown>|string
     */
    public function getFilterRequestParams($filterName, $delimiter=';')
    {
    	return A::m($_GET, [
    		$filterName=>array_map(function($v) use ($delimiter) { 
    			return implode($delimiter,$v); 
    		}, A::get($_POST, $filterName, []))
    	]);
    }
    
    /**
     * Получить массив array(valueField=>textField)
     * @param string|array $textField имя атрибута текста. Может быть передан в виде массива
     * array(textField=>callable), где callable - функция изменения текстового значения вида
     * function(&$model, $attribute) { $model->$textField='myTextFieldValue'; }
     * @param array|\CDbCriteria|NULL $criteria дополнительный критерий выборки. 
     * По умолчанию (NULL) - не задан. 
     * @param string|array|NULL $empty пустой элемент. 
     * Может быть передан строкой, в таком случае значение будет пустым значением.
     * Может быть передано массивом array(value=>text)
     * По умолчанию (NULL) - не задан.
     * @param string|array $valueField имя атрибута значения. По умолчанию "id".
     * Может быть передан в виде массива array(valueField=>callable), где callable - функция изменения
     * текстового значения вида function(&$model, $attribute) { $model->$valueField='myValueFieldValue'; }
     * @param string|array $groupField имя атрибута группировки. По умолчанию (пустая строка) - не задан.
     * Может быть передан в виде массива array(groupField=>callable), где callable - функция изменения
     * текстового значения вида function(&$model, $attribute) { $model->$groupField='myGroupFieldValue'; }
     * @param string $tableAlias псевдоним таблицы для выбираемых атрибутов. По умолчанию "t".
     * @return array
     */
    public function listData($textField, $criteria=null, $empty=null, $valueField='id', $groupField='', $tableAlias='t')
    {
    	$listData=[];
    	
    	$criteria=HDb::criteria($criteria);
    	
    	if($empty !== null) {
    		if(!is_array($empty)) $empty=[''=>$empty];  
    		$listData=A::m($listData, $empty);
    	}
    	
    	$callables=[];
    	foreach(['textField', 'valueField', 'groupField'] as $field) {
    		if(is_array($$field)) {
    			$f=$$field;
    			$name=key($f);
    			if(is_callable($f[$name])) $callables[$name]=$f[$name];
    			$$field=$name;
    		}
    	}

    	$criteria->scopes[]=[
    		'select'=>["`{$tableAlias}`.".HDb::qc($valueField) . ',' . "`{$tableAlias}`.".HDb::qc($textField), false]
    	];
    	if($groupField) {
    		$criteria->scopes[]=['select'=>["`{$tableAlias}`.".HDb::qc($groupField), false]];
    	}
    	
    	if($models=$this->findAll($criteria)) {
	    	if(!empty($callables)) {
	    		foreach($callables as $attribute=>$callable) {
    				foreach($models as $idx=>$model) {
    					call_user_func_array($callable, [&$models[$idx], $attribute]);
    				}
	    		}
	    	}
    	}
    	else {
    		$models=[];
    	}

    	$listData=A::m($listData, \CHtml::listData($models, $valueField, $textField, $groupField));
    	
    	return $listData;
    }
    
    /**
     * Получить значение атрибута
     * @param string $attribute имя атрибута.
     * @param string $condition дополнительное условие выборки. По умолчанию пустая строка.
	 * @param array $params параметры для условия выборки. По умолчанию пустой массив.
     * @return mixed|NULL значение атрибута. Если модель не найдена будет возвращено NULL.
     */
    public function fetchScalar($attribute, $condition='', $params=[])
    {
    	if($model=$this->find(['select'=>$attribute, 'condition'=>$condition, 'params'=>$params])) {
    		return $model->$attribute;
    	}
    	
    	return null;
    }
}
