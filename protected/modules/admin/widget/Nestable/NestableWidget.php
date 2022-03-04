<?php
/**
 * Nestable widget
 * Виджет редактирования вложенной структуры
 * @see https://github.com/dbushell/Nestable
 */
class NestableWidget extends CWidget
{
	public $id;
	
	/**
	 * @var string имя класса модели с поведением NestedSetBehavior
	 * @see https://github.com/yiiext/nested-set-behavior
	 */
	public $modelClass;
	
	/**
 	 * @var string имя атрибута id модели
	 */
	public $attributeId='id';
	
	/**
 	 * @var string имя атрибута parent_id модели
	 */
	public $attributeParentId='parent_id';
	
	/**
 	 * @var string имя атрибута заголовка
	 */
	public $attributeTitle='title';
	
	/**
 	 * @var string имя атрибута сортировки
	 */
	public $attributeOrdering='ordering';
	
	/**
	 * @var boolean использовать скин dd3
	 */
	public $skinDd3=false;
	 
	/**
	 * (non-PHPdoc)
	 * @see CWidget::init()
	 */
	public function init()
	{
		AssetHelper::publish(array(
			'path'=>dirname(__FILE__).DS.'assets',
			'js'=>array('jquery.nestable.js', 'NestableWidget.js'),
			'css'=>'jquery.nestable.css'
		));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{
		$this->render('default', array('data'=>$this->prepareData($this->findAll())));
	}
	
	/**
	 * Получить все модели
	 * @return Ambigous<array, null> 
	 */
	protected function findAll($criteria=null)
	{
		if($criteria===null)  
			$criteria['select']="{$this->attributeId}, {$this->attributeParentId}, {$this->attributeTitle}, {$this->attributeOrdering}";
		
		if($criteria instanceof \CDbCriteria) $criteria->order=$this->attributeOrdering;
		else $criteria['order']=$this->attributeOrdering;

		$modelClass=$this->modelClass;
		return $modelClass::model()->findAll($criteria);
	}
	
	/**
	 * получить элемент для дерева
	 * @param \CModel $model
	 * @return array
	 */
	protected function getItem(&$model)
	{
		return array(
			$this->attributeId=>(int)$model->{$this->attributeId},
			$this->attributeParentId=>(int)$model->{$this->attributeParentId},
			$this->attributeOrdering=>(int)$model->{$this->attributeOrdering},
			$this->attributeTitle=>$model->{$this->attributeTitle},
		);
	}
	
	/**
	 * Подготовка данных для шаблона отображения
	 * @param array $models массив моделей.
	 * @return array
	 */
	protected function prepareData(&$models)
	{
		if(empty($models)) return array();
		
		$aId=$this->attributeId;
		$aPId=$this->attributeParentId;
		$aOrd=$this->attributeOrdering;

		$roots=array();
		$childrens=array();
		foreach($models as $model) {
			$item=$this->getItem($model);
			if($item[$aPId]) $childrens[ $item[$aPId] ][ $item[$aId] ]=$item; 
			else $roots[ $item[$aId] ]=$item;
		}
		
		$data=array();
		$fFillData=function($array, $level=1) use (&$data, $childrens, &$fFillData, $aOrd) {
			$orders=array();
			foreach($array as $id=>$item) $orders[$id]=$item[$aOrd];
			asort($orders, SORT_NUMERIC);
			foreach($orders as $id=>$order) {
				$array[$id]['level']=$level;
				$data[]=$array[$id];
				if(isset($childrens[$id])) $fFillData($childrens[$id], $level+1);
			}
		};
		$fFillData($roots);
		
		return $data;
	}
}