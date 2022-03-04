<?php
/**
 * Поведение атрибута хэша
 * 
 */
namespace common\behaviors;

use common\components\helpers\HArray as A;
use common\components\helpers\HHash;

class ARHashAttributeBehavior extends \CBehavior
{
	/**
	 * @var boolean добавить автоматически в таблицу базы данных поле для хранения данных
	 * данного атрибута. По умолчанию (TRUE) добавить.
	 */
	use \common\traits\models\AddColumn;
	
	/**
	 * @var string имя аттрибута. По умолчанию "hash".
	 */
	public $attribute='hash';
	
	/**
	 * @var string метка аттрибута.
	 * По умолчанию (false) не определена.
	 */
	public $attributeLabel=false;
	
	/**
	 * @var array массив атрибутов внешней модели, которые 
	 * участвуют в генерации хэша. По умолчанию array("id").
	 */
	public $attributes=['id'];
	
	/**
	 * @var string символ объединения значений. 
	 * По умолчанию "|".
	 */
	public $glue='|';
	
	/**
	 * (non-PHPDoc)
	 * @see CBehavior::attach($owner)
	 */
	public function attach($owner)
	{
		parent::attach($owner);
		
		$this->addColumn($this->owner->tableName(), $this->attribute, 'BIGINT(20)');
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
	 * Scope: Выборка по хэшу.
	 * @param integer|boolean $hash значение хэша.
	 * По умолчанию (false) значение хэша будет получено
	 * из значений текущей модели. 
	 * @return \extend\modules\regions\models\Data
	 */
	public function byHash($hash=false)
	{
		$criteria=new \CDbCriteria();
		
		if($hash === false) {
			$hash=$this->generateHash();
		}
		
		$criteria->addColumnCondition([$this->attribute=>new \CDbExpression($hash)]);
		
		$this->owner->getDbCriteria()->mergeWith($criteria);
		
		return $this->owner;
	}
	
	/**
	 * Генерация хэша.
	 *
	 * Для прямого SQL запроса можно использовать выражение:
	 * hash=CRC32(CONCAT(model_name, "|", attribute_1, "|", ..., "|", attribute_N))),
	 * где
	 * model_name - имя класса модели 
	 * "|" - символ объединения из параметра HashAttributeBehavior::$glue.
	 * 
	 * @param array|false массив дополнительных параметров генерации хэша.
	 * array(
	 * 	'model'=>имя класса модели или объект модели, для которого генерится хэш,
	 *  'attributes'=>array(attribute=>value), напр: array('id'=>10),
	 *  'glue'=>символ объединения значений
	 * )
	 * @return string
	 */
	public function generateHash($params=false)
	{
		$attributes=[];
		
		$model=A::get($params, 'model', $this->owner);
		if(is_object($model)) {
			$attributes[]=get_class($model);
		}
		else {
			$attributes[]=(string)$model;
		}
		
		if(is_object($model)) {
			foreach($this->attributes as $attribute) {
				$attributes[$attribute]=$model->$attribute;
			}
		}
		$attributes=A::m($attributes, A::get($params, 'attributes', []));
		
		$value=implode(A::get($params, 'glue', $this->glue), array_values($attributes));			
		
		return HHash::ucrc32($value);
	}
}