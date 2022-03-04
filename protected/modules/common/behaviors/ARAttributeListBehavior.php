<?php
/**
 * Поведение атрибута списка идентификаторов связной модели.
 */
namespace common\behaviors;

use common\components\helpers\HArray as A;
use common\components\helpers\HDb;

class ARAttributeListBehavior extends \CBehavior
{
	/**
	 * @var boolean добавить автоматически в таблицу базы данных поле для хранения данных
	 * данного атрибута. По умолчанию (TRUE) добавить.
	 */
	use \common\traits\models\AddColumn;
	
	/**
	 * @var string имя cвязи. 
	 */
	public $rel;
	
	/**
	 * @var string имя класса связной модели. Если установлено FALSE, предполагается, что 
	 * связь уже задана во внешней модели. Имя связи в этом случае берется из параметра
	 * ARAttributeListBehavior::$rel. По умолчанию FALSE.
	 * Связная модель должна быть:
	 * 1 наследуемой от \common\components\base\ActiveRecord
	 * 2) содержать поведение \common\ext\updateTime\behaviors\UpdateTimeBehavior, либо 
	 * содержать метод "public getDbCacheDependency()". 
	 */
	public $relClass = false;
	
	/**
	 * @var string имя атрибута
	 */
	public $attribute;
	
	/**
	 * @var string имя атрибута идетификатора связной модели.
	 * По умолчанию "id"
	 */
	public $attributeId='id';
	
	/**
	 * @var string имя атрибута, для которого передается значение поиска.
	 * По умолчанию "title".
	 */
	public $searchAttribute='title';
	
	/**
	 * @var string оператор поиска в связной модели.
	 * Может быть задан любой, в том числе: "LIKE", "REGEXP".    
	 * По умолчанию "LIKE".
	 */
	public $searchOperator='LIKE';
	
	/**
	 * @var string дополнительная часть выражения слева.
	 * Например, для оператора "LIKE" можно задать "%".
	 * По умолчанию пустая строка.
	 */
	public $searchExprBefore='';
	
	/**
	 * @var string дополнительная часть выражения справа.
	 * Например, для оператора "LIKE" можно задать "%".
	 * По умолчанию пустая строка.
	 */
	public $searchExprAfter='';
	
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
	 * @var integer время кэширования запроса получения данных связной модели (в секундах).
	 * По умолчанию 60 секунд.
	 */
	public $cacheTime=60;
	
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
			elseif($name == $this->rel) {
				if(!$this->owner->hasRelated($this->rel)) {
					if($relations=$this->relations()) {
						$this->owner->metaData->addRelation($this->rel, $relations[$this->rel]);
						return $this->owner->getRelated($this->rel);
					}
				}
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
		
		if(!$this->relClass) {
			$this->relClass=$this->rel;
		}
	
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
	 * (non-PHPDoc)
	 * @see \CActiveRecord::relations()
	 */
	public function relations()
	{
		$condition='1<>1';
		
		$value=$this->owner->{$this->attribute};
		if($value) {
			if(is_array($value)) {
				$value=implode(',', $value);
			}
			$value=trim(str_replace($this->delimiter, ',', $value), ',');
			$value=preg_replace('/[^0-9,]+/', '', $value);
			if($value) {
				$condition=HDb::qt($this->rel) . ".`id` IN ({$value})"; 
			}
		}
		
		return [
			$this->rel => [\CActiveRecord::HAS_MANY, $this->getRelModel(true), false, 'condition'=>$condition]
		];
	}
	
	/**
	 * Получить данные связи. 
	 * @param boolean $refresh заного получить связные данные. 
	 * @param array|CDbCriteria $params дополнительные параметры для выборки. 
	 */
	public function getRelated($refresh=false, $params=[])
	{
		$relations=$this->owner->{$this->rel};
		return $this->owner->getRelated($this->rel, $refresh, $params);
	}
	
	/**
	 * Получить объект связной модели. 
	 * Объект модели будет возвращен статическим методом $relClassName::model().
	 * @param boolean $returnClassName возвратить только имя класса связной модели.
	 * По умолчанию (FALSE) - возвратить объект.
	 * @return \CActiveRecord|string
	 */
	protected function getRelModel($returnClassName=false)
	{
		if($this->relClass) {
			$relClassName=$this->relClass;
		}
		elseif($this->owner->hasRelated($this->rel)) {
			$relClassName=$this->owner->getRelated($this->rel)->className;
		}
		else {
			return null;
		}
		
		if($returnClassName) {
			return $relClassName;
		}
		
		return $relClassName::model();
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
					$condition=HDb::qc($this->searchAttribute).' '.$this->searchOperator.' :search';
					$relModel=$this->getRelModel();
					
					if(!is_array($values)) $values=[$values];
					foreach($values as $value) {
						if($value=trim($value)) {
							$params=[':search'=>$this->searchExprBefore.$value.$this->searchExprAfter];
							$id=$relModel->cache($this->cacheTime, $relModel->getDbCacheDependency($condition, $params))
								->fetchScalar($this->attributeId, $condition, $params);
							
							if($id) $criteria->addSearchCondition($this->attribute, '|'.$id.'|', true, $columnOperator);
							else $criteria->addCondition('1<>1');
						}
					}				 
					return $criteria;				
				}
				return null;
			}
		];
	}
	
	/**
	 * Возвращает массив данных связной модели. 
	 * Массив будет возвращен в виде array(valueField=>textField)
     * @param string $textField имя атрибута текста.
     * @param array|\CDbCriteria|NULL $criteria дополнительный критерий выборки. 
     * По умолчанию (NULL) - не задан. 
     * @param string|array|NULL $empty пустой элемент. 
     * Может быть передан строкой, в таком случае значение будет пустым значением.
     * Может быть передано массивом array(value=>text)
     * По умолчанию (NULL) - не задан.
     * @param string $valueField имя атрибута значения. По умолчанию "id".
     * @param string $groupField имя атрибута группировки. По умолчанию (пустая строка) - не задан.
     * @param string $tableAlias псевдоним таблицы для выбираемых атрибутов. По умолчанию "t".
     * @return array
	 */
	public function listData($textField, $criteria=null, $empty=null, $valueField='id', $groupField='', $tableAlias='t')
	{
		$value=$this->owner->{$this->attribute};
		if(empty($value)) return [];
		 
		$criteria=new \CDbCriteria();
		$criteria->addInCondition('id', $this->owner->{$this->attribute});
		
		return $this->getRelModel()->listData($textField, $criteria, $empty, $valueField, $groupField, $tableAlias);
	}
}