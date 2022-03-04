<?php
/**
 * Модель. Категория сортировки.
 * 
 */
namespace common\ext\sort\models;

class SortCategory extends \common\components\base\ActiveRecord
{
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return 'sort_category';
	}
	
	/**
	 * Scope. Выборка категории.
	 * @param string $name имя категории.
	 * @param string $key дополнительный ключ категории. По умолчанию NULL.
	 * @param string $tableAlias алиас таблицы. По умолчанию "`t`".
	 * @return common\ext\sort\models\SortCategory
	 */
	public function category($name, $key=null, $tableAlias='`t`')
	{
		$criteria=$this->getDbCriteria();
		$criteria->addColumnCondition([
			$tableAlias.'.`name`'=>$name, 
			$tableAlias.'.`key`'=>$key
		]);
		
		return $this;
	}
}