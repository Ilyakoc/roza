<?php
/**
 * Виджет списка
 * 
 */
namespace crud\widgets;

use common\components\base\Widget;
use common\components\helpers\HArray as A;
use crud\components\helpers\HCrud;

class ListWidget extends Widget
{
	/**
	 * @var string индетификатор настроек CRUD для модели.
	 */
	public $cid;
	
	/**
	 * @var array параметры для \CActiveDataProvider
	 */
	public $options=[];
	
	/**
	 * @var string|NULL имя категории сортировки для scopeSort().
	 */
	public $sort=null;
	
	/**
	 * @var string|NULL дополнительный ключ сортировки для scopeSort().
	 */
	public $sortKey=null;
	
	/**
	 * @var string тэг обертки элементов
	 * @see \CListView::$itemsTagName
	 */
	public $itemsTagName='ul';
	
	/**
	 * @var string css класс для тэга обертки элементов
	 * @see \CListView::$itemsCssClass
	 */
	public $itemsCssClass='';
	
	/**
	 * @var string текст при отсутствии элементов
	 * @see \CListView::$itemsCssClass
	 */	
	public $emptyText='';
	
	/**
	 * (non-PHPdoc)
	 * @see \common\components\base\Widget::$view
	 */
	public $view='list';
	
	/**
	 * @var srting имя шаблона отображения элемента.
	 * @see \CListView::$itemView
	 */
	public $itemView='_list_item';
	
	/**
	 * @var array дополнительные параметры для \CListView. 
	 * Являются приоритетными перед параметрами виджета.
	 */
	public $listViewOptions=[];
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		if($this->sort) {
			A::rset($this->options, 'criteria.scopes', [
					'scopeSort'=>[$this->sort, $this->sortKey]
			], true, 1, '.', true);
		}

		parent::init();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \common\components\base\Widget::run()
	 */
	public function run()
	{
		$this->params['dataProvider']=HCrud::getById($this->cid, true)->getDataProvider($this->options);
		
		$this->render($this->view, $this->params);
	}
}