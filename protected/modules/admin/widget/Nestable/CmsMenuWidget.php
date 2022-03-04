<?php
/**
 * Виджет редактирования вложенности меню CMS Dishman
 * @see https://github.com/dbushell/Nestable
 */
use common\components\helpers\HArray as A;

Yii::import('admin.widget.Nestable.NestableWidget');

class CmsMenuWidget extends NestableWidget
{
	/**
	 * @const string режим отображения базовый. Без возможности вложенности.
	 */
	const MODE_BASIC='basic';
	
	/**
	 * @const string режим отображения многоуровневый. 
	 */
	const MODE_TREE='tree';
	
	/**
	 * @see NestableWidget::$id
	 */
	public $id;
	
	/**
	 * @var string режим отображения. Доступны "basic", "tree".
	 */
	public $mode=self::MODE_BASIC;
	
	/**
	 * @var boolean отображать id меню или нет.
	 */
	public $showId=false;
	
	/**
	 * @var string имя класса модели с поведением NestedSetBehavior
	 * @see https://github.com/yiiext/nested-set-behavior
	 */
	public $modelClass='Menu';
	
	/**
 	 * @see NestableWidget::$attributeId
	 */
	public $attributeId='id';
	
	/**
 	 * @see NestableWidget::$attributeParentId
	 */
	public $attributeParentId='parent_id';
	
	/**
 	 * @see NestableWidget::$attributeTitle
	 */
	public $attributeTitle='title';
	
	/**
 	 * @see NestableWidget::$attributeOrdering
	 */
	public $attributeOrdering='ordering';
	
	/**
	 * (non-PHPdoc)
	 * @see NestableWidget::run()
	 */
	public function run()
	{
		$method=($this->mode==self::MODE_TREE) ? 'prepareData' : 'prepareDataBasic'; 
		$this->render('cmsmenu', array('data'=>$this->$method($this->findAll(array(
			'condition'=>'`system` <> 1'
		)))));
	}
	
	/**
	 * получить элемент для дерева
	 * @param \CModel $model
	 * @return array
	 */
	protected function getItem(&$model)
	{
		$route=CmsMenuHelper::adminRoute($model);
		
		$urlDelete=(in_array($model->options['model'], array('page','link'))) ? \Yii::app()->createUrl('/cp/'.$model->options['model'].'/delete', array('id'=>$route['id'])) : null;
		$url=is_array($route) ? array_shift($route) : $route;
		$params=is_array($route) ? $route : array();
		$url=\Yii::app()->createUrl($url, $params);
		
		if($isSubmenuRoot=in_array((int)$model->id, explode(',', str_replace(' ', '', D::cms('treemenu_fixed_id'))))) {
			$urlDelete=null;
			$url=null;
		}

		return array(
			$this->attributeId=>(int)$model->{$this->attributeId},
			$this->attributeParentId=>(int)$model->{$this->attributeParentId},
			$this->attributeOrdering=>(int)$model->{$this->attributeOrdering},
			$this->attributeTitle=>$model->{$this->attributeTitle},
			'isSubmenuRoot'=>$isSubmenuRoot,
			'url'=>$url,
			'urlDelete'=>$urlDelete,
      		'disabledClass'=>$urlDelete ? '' : 'disabled_edit_page',
      		'level'=>1
		);
	}
	
	/**
	 * Подготовка данных для шаблона отображения в режиме базовый. 
	 * @param array $models массив моделей.
	 * @return array
	 */
	protected function prepareDataBasic(&$models)
	{
		if(empty($models)) return array();
		
		$data=array();
		foreach($models as $model) 
			$data[]=$this->getItem($model);
		
		return $data;
	}
}