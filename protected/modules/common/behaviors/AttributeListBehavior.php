<?php
/**
 * Поведение атрибута списка для модели содержащей
 * трейт \common\traits\Model. 
 */
namespace common\behaviors;

class AttributeListBehavior extends \CBehavior
{
	/**
	 * @var boolean добавить автоматически в таблицу базы данных поле для хранения данных
	 * данного атрибута. По умолчанию (TRUE) добавить.
	 */
	use \common\traits\models\AddColumn;
	
	/**
	 * @var string имя атрибута
	 */
	public $attribute;
	
	/**
	 * @var string название атрибута.
	 * По умолчанию (NULL) не задано.
	 */
	public $attributeLabel=null;
	
	/**
	 * @var string символ разделения/склейки значений.
	 * По умолчанию "|".
	 */
	public $delimiter='|';
	
	/**
	 * @var string the LIKE operator. Defaults to 'LIKE'. You may also set this to be 'NOT LIKE'.
	 */
	public $filterLike='LIKE';
	
	/**
	 * @var string тип поля в базе данных. 
	 * По умолчанию "LONGTEXT".
	 */
	public $columnType='LONGTEXT';
	
	/**
	 * (non-PHPdoc)
	 * @see \common\traits\Model::__get()
	 */
	public function __handlerGet($name)
	{
		return function ($name) {
			if($name == $this->attribute) {		
				$value=$this->owner->getAttribute($this->attribute);
				if(!empty($value) && !is_array($value)) {
					return explode($this->delimiter, trim($value, $this->delimiter));
				}
				
				return $value;
			}		
			throw new \common\components\exceptions\PropertyNotFound;
		};
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \common\traits\Model::__set()
	 */
	public function __handlerSet($name, $value)
	{
		return function($name, $value) {
			if($name == $this->attribute) {		
				if(is_array($value)) {
					$value=$this->delimiter . implode($this->delimiter, $value) . $this->delimiter;
				}
				else {
					$value='';
				}
				$this->owner->setAttribute($this->attribute, $value);
			}
			else {
				throw new \common\components\exceptions\PropertyNotFound;
			}
		};
	}
	
	/**
	 * (non-PHPDoc)
	 * @see CBehavior::attach($owner)
	 */
	public function attach($owner)
	{
		parent::attach($owner);
	
		$this->addColumn($this->owner->tableName(), $this->attribute, $this->columnType);
	}
	
	/**
	 * (non-PHPDoc)
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return [
			[$this->attribute, 'safe']
		];
	}
	
	/**
	 * (non-PHPDoc)
	 * @see CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		if($this->attributeLabel) {
			return [
				$this->attribute=>$this->attributeLabel
			];
		}
		
		return [];
	}
	
	/**
	 * Получить обработчик для фильтра.
	 * Для параметра $handlers метода 
	 * \common\components\base\ActiveRecord::filter()
	 */
	public function getFilterHandler()
	{
		return [
			$this->attribute=>function($model, $name, $values, $columnOperator) {
				if(!empty($values)) {				 
					$criteria=new \CDbCriteria();
					if(!is_array($values)) $values=[$values];
					foreach($values as $value) {
						if($value=trim($value)) {
							$criteria->addSearchCondition(
								$this->attribute, 
								$this->delimiter . $value . $this->delimiter, 
								true, 
								$columnOperator, 
								$this->filterLike
							);
						}
					}				 
					return $criteria;				
				}
				return null;
			}
		];
	}
}