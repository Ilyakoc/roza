<?php
/**
 * Виджет отображения в виде дерева моделей NestedSet
 *
 */
namespace common\ext\nestedset\widgets;

class NestedList extends BaseNestable
{
	public $models;
	
	public $printDataId=false;
	
	public $tagName='ul'; //'div';
	public $htmlOptions=[]; // ['class'=>'nsd__list'];
	public $itemTagName='li'; // 'div';
	public $itemHtmlOptions=[]; // ['class'=>'nsd__list-item'];
	
	/**
	 * (non-PHPdoc)
	 * @see \common\ext\nestedset\widgets\BaseNestable::init()
	 */
	public function init()
	{
		// unset parent::init()	
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CWidget::run()
	 */
	public function run()
	{
		if(!empty($this->models)) {
			$this->render('common.ext.nestedset.widgets.views.nested_list'); 
		}
	}
}