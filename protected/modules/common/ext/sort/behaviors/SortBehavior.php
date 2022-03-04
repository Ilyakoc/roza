<?php
/**
 * Поведение сортировки для модели \CActiveRecord
 * 
 */
namespace common\ext\sort\behaviors;

use common\components\helpers\HArray as A;
use common\components\helpers\HDb;
use common\ext\sort\models\SortCategory; 
use common\ext\sort\models\SortData;

class SortBehavior extends \CBehavior
{
	/**
	 * @var boolean запустить миграцию для базы данных.
	 * По умолчанию (false) не запускать.
	 */
	public $migrate=false;
	
	/**
	 * (non-PHPdoc)
	 * @see \CBehavior::attach()
	 */
	public function attach($owner) 
	{
		parent::attach($owner);
		
		if($this->migrate) {
			HDb::migrate('common.ext.sort.migrations');
		}
	}
	
	/**
	 * Scope. Сортировка.
	 * @param string $category имя категории сортировки
	 * @param int|null $key ключ категории сортировки.
	 * @param boolean $revers обратная сортировка. По умолчанию FALSE (прямая сортировка).
	 * @param string $tableNameAlias псевдоним таблицы модели для которой в запросе происходит сортировка. 
	 * По умолчанию "t".
	 * @param string $attributeId имя атрибута id модели. По умолчанию "id".
	 */
	public function scopeSort($category, $key=null, $revers=false, $tableNameAlias='t', $attributeId='id')
	{
		$criteria=new \CDbCriteria;
		
		$columnId=HDb::qc($attributeId);
		$tableName=HDb::qt($tableNameAlias);
		$tnSortCategory=HDb::qt(SortCategory::model()->tableName());
		$tnSortData=HDb::qt(SortData::model()->tableName());
		
		$conditionCategoryKey="{$tnSortCategory}.`key`" . ($key ? "=:sortCategoryKey" : ' IS NULL'); 
		$criteria->join=" LEFT JOIN {$tnSortCategory} ON ({$tnSortCategory}.`name`=:sortCategoryName AND {$conditionCategoryKey})";
		$criteria->join.=" LEFT JOIN {$tnSortData} ON ({$tnSortData}.`category_id`={$tnSortCategory}.`id` AND {$tnSortData}.`model_id`={$tableName}.{$columnId})";
		$criteria->params[':sortCategoryName']=$category;
		
		if($key) {
			$criteria->params[':sortCategoryKey']=$key;
		}
		
		$criteria->order="IF(ISNULL({$tnSortData}.`order_number`),1,0), {$tnSortData}.`level`, {$tnSortData}.`order_number` " . ($revers ? 'DESC' : 'ASC');
		$this->owner->getDbCriteria()->mergeWith($criteria);
		
		return $this->owner;
	}
	
} 
