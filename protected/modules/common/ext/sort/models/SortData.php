<?php
/**
 * Модель. Данные сортировки.
 */
namespace common\ext\sort\models;

use common\components\helpers\HDb;

class SortData extends \common\components\base\ActiveRecord
{
	/**
	 * (non-PHPdoc)
	 * @see \CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return 'sort_data';
	}

	/**
	 * Сохранение данных сортировки
	 * @param int $categoryId id категории.
	 * @param array $data отсортированный массив идентификаторов моделей.
	 * Если ХОТЯ БЫ ОДНО значение массива не является числом, сортировка сохранена не будет.
	 * @return boolean
	 */
	public function saveData($categoryId, $data, $level=0)
	{
		if($categoryId && !empty($data)) {
			$values=[];
			foreach($data as $n=>$id) {
				if(!is_numeric($id)) return false;
				$values[]="({$categoryId}, {$id}, ".($n+1).', '.(int)$level.')';
			}
				
			$sql='REPLACE '.HDb::qt($this->tableName()).' (`category_id`, `model_id`, `order_number`, `level`) VALUES ';
			$sql.=implode(',', $values);
				
			return HDb::execute($sql);
		}
		
		return false;
	}
}
