<?php
/**
 * SaveAction
 * 
 * Действие сохранения элеметов модели с подключенным 
 * \common\ext\nestedset\behaviors\NestedSetBehavior
 *
 */
namespace common\ext\nestedset\actions;

use common\components\helpers\HYii as Y;
use common\components\helpers\HDb;
use common\components\helpers\HAjax;
use common\components\helpers\common\components\helpers;

class SaveAction extends \CAction
{
	/**
	 * @var string имя класса модели \CActiveRecord 
	 * с поведением \common\ext\nestedset\behaviors\NestedSetBehavior. 
	 */
	public $modelClass;
	
	/**
	 * @var string имя параметра данных.
	 */
	public $paramData='data';
	
	/**
	 * @var string имя поведения \common\ext\nestedset\behaviors\NestedSetBehavior
	 * в модели.
	 */
	public $nestedSetBehaviorName='nestedSetBehavior';
	
	/**
	 * @var string имя атрибута сортировки в модели. 
	 * Если не задано, сортировка сохранена не будет.
	 * По умолчанию NULL.
	 */
	public $attributeOrdering=null;
	
	/**
	 * @var mixed условие обновления (WHERE {$updateCondition})
	 * Может быть передано:
	 * 1) строка
	 * 2) функция, которая возвращает строку
	 * 3) массив [класс, метод], который возвращает строку
	 * 4) NULL без условия. 
	 */
	public $updateCondition=null;
	
	/**
	 * @var mixed дополнительное выражение обновления данных.
	 * Может быть передано:
	 * 1) строка
	 * 2) функция, которая возвращает строку
	 * 3) массив [класс, метод], который возвращает строку
	 * 4) NULL без условия. 
	 */
	public $setExpression=null;
	
	/**
	 * (non-PHPDoc)
	 * @see http://yiiframework.ru/doc/guide/ru/basics.controller
	 */
	public function run()
	{
		$ajax=new HAjax();
		
		$modelClass=$this->modelClass;
		$model=$modelClass::model();
		
		$data=json_decode(Y::request()->getPost($this->paramData, '[]'));
		if(is_array($data)) {
			$cases=[
				$model->{$this->nestedSetBehaviorName}->rootAttribute => '',
				$model->{$this->nestedSetBehaviorName}->leftAttribute => '',
				$model->{$this->nestedSetBehaviorName}->rightAttribute => '',
				$model->{$this->nestedSetBehaviorName}->levelAttribute => ''
			];
			if($this->attributeOrdering) {
				$cases[$this->attributeOrdering]='';
			}

			$ids=[];
			foreach($data as $item) {
				$ids[]=(int)$item->id;
				array_walk($cases, function(&$expression, $attribute) use ($item) {
					$expression.=' WHEN '.(int)$item->id.' THEN '.(int)$item->$attribute;
				});
			}
			array_walk($cases, function(&$expression, $attribute) {
				$expression="`t`.`{$attribute}`=CASE `t`.`id` {$expression} ELSE `t`.`{$attribute}` END";
			});
	
			$query='UPDATE '.HDb::qt($model->tableName()).' AS `t` SET '.implode(',', $cases);
			
			if(is_callable($this->setExpression) && ($setExpression=call_user_func($this->setExpression))) {
				$query.=', ' . $setExpression;
			}
			elseif(is_string($this->setExpression) && !empty($this->setExpression)) {
				$query.=', ' . $this->setExpression;
			}
			
			$query.=' WHERE `t`.`id` IN (' . implode(',', $ids) . ')';
			if(is_callable($this->updateCondition) && ($updateCondition=call_user_func($this->updateCondition))) {
				$query.=' AND (' . $updateCondition . ')';
			}
			elseif(is_string($this->updateCondition) && !empty($this->updateCondition)) {
				$query.=' AND (' . $this->updateCondition . ')';
			}
			
			try { 
				echo $query;
				HAR::db($model)->createCommand($query)->execute();
				$success=true;
			}
			catch (\CDbException $e) {
				if(Y::request()->isAjaxRequest)
					$error=$e->getMessage();
				else throw $e;
			}
		}
		
		if(Y::request()->isAjaxRequest)
			HAjax::end($success, [], $error);
		
		return false;
	}
}