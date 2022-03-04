<?php
/**
 * Advanced ActiveRecord
 * 
 */
class DActiveRecord extends CActiveRecord
{
	/**
	 * @var array методы модели, которые проверяются на наличие 
	 * в подключаемых поведениях для объединения.
	 * Результат объединения из поведений хранится здесь же.
	 * array(methodName=>data)
	 */
	protected $_bm=array(
		'scopes'=>array(),
		'rules'=>array(), 
		'relations'=>array(), 
		'attributeLabels'=>array()
	);
	
	/**
	 * @var boolean поведения подключены. По умолчанию FALSE - не подключены.
	 */
	private $_attached=false;
	
	/**
	 * @var string динамические атрибуты
	 */
	private $_dynamicAttributes=array();
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return the static called model class
	 */
	public static function model($className=null)
	{
		if($className===null) $className=get_called_class();
		
		return parent::model($className);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::__get()
	 */
	public function __get($name)
	{
		if(array_key_exists($name, $this->_dynamicAttributes)) {
			return $this->_dynamicAttributes[$name];
		}
		return parent::__get($name);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::__set()
	 */
	public function __set($name, $value)
	{
		if(array_key_exists($name, $this->_dynamicAttributes)) {
			$this->_dynamicAttributes[$name]=$value;
		}
		else { 
			parent::__set($name, $value);
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
     * Use it, if you wont use "scopes" and "CActiveDataProvider".
     * @author Rafael Garcia
     * @link http://www.yiiframework.com/wiki/173/an-easy-way-to-use-escopes-and-cactivedataprovider/
     *
     * @ Param Criteria $ CDbCriteria
     * @ Return CActiveDataProvider
     */
    public function getDataProvider($criteria=null, $pagination=null)
    {
    	if($criteria===null) {
    		$criteria=$this->getDbCriteria();
    	}
        elseif(is_array($criteria)) {
        	$criteria=new CDbCriteria($criteria);
        	$criteria->mergeWith($this->getDbCriteria());
        }
        elseif($criteria instanceof CDbCriteria) {
        	$criteria->mergeWith($this->getDbCriteria());
        }
        
        if($pagination !== false)
	        $pagination=CMap::mergeArray(array('pageSize'=>20), (array)$pagination);
        
        return new CActiveDataProvider($this, array(
			'criteria'=>$criteria, 
            'pagination' => $pagination
        ));
    }
    
    /**
     * (non-PHPdoc)
     * @see CComponent::attachBehaviors()
     */
    public function attachBehaviors($behaviors)
    {
    	foreach($behaviors as $name=>$behavior) {
    		$obj=$this->attachBehavior($name, $behavior);
    		
    		foreach($this->_bm as $method=>$data) {
    			if(method_exists($obj, $method)) 
    				$this->_bm[$method]=\CMap::mergeArray($data, $obj->$method());
    		} 
    	}
    	
    	$this->_attached=true;
    }
    
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::scopes()
     */
    public function scopes($scopes=array())
    {
    	return \CMap::mergeArray($this->_bm['scopes'], $scopes);
    }
    
    /**
     * (non-PHPdoc)
     * @see CModel::rules()
     */
    public function rules($rules=array())
    {
    	return \CMap::mergeArray($this->_bm['rules'], $rules);
    }
    
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::relations()
     */
    public function relations($relations=array())
    {
    	// необходимо так, как в начале подключаются связи, затем поведения.
    	if(!$this->_attached) 
    		$this->attachBehaviors($this->behaviors());
    	
    	return \CMap::mergeArray($this->_bm['relations'], $relations);
    }
    
    /**
     * (non-PHPdoc)
     * @see CModel::attributeLabels()
     */
    public function attributeLabels($attributeLabels=array())
    {
    	return \CMap::mergeArray($this->_bm['attributeLabels'], $attributeLabels);
    }
}