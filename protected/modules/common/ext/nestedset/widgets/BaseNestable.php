<?php
/**
 * Базовый класс для виджетов  common\ext\nestedset\widgets\*Nestable
 *
 * Виджет использует JQuery плагин Nestable.
 * @link https://github.com/dbushell/Nestable
 */
namespace common\ext\nestedset\widgets;

use YiiHelper as Y;
use AttributeHelper as A;

abstract class BaseNestable extends \CWidget
{
	/**
	 * @var string id элемента обертки виджета.
	 * Для DOM-элемента с данным "id" будет инициализирован 
	 * jquery-плагин Nestable.  
	 */
	public $id='nestable-widget';
	
	/**
	 * @var \IDataProvider дата-провайдер элементов.
	 */
	public $dataProvider=null;

	/**
	 * @var string имя атрибута уровня. По умолчанию "level".
	 */
	public $attributeLevel='level';
	
	/**
	 * @var string имя атрибута "id". По умолчанию "id".
	 * Требуется для отображения атрибута data-id="<id>"
	 * для DOM-элемента контейнера элемента.  
	 */
	public $attributeId='id';
	
	/**
	 * @var string имя атрибута заголовка. По умолчанию "title".
	 * Значение данного атрибута может потребоваться, если не задан 
	 * метод или шаблон отображения содержимого элемента. 
	 */
	public $attributeTitle='title';
	
	/**
	 * @var string имя атрибута id корневого элемента. По умолчанию "root".
	 */
	public $attributeRoot='root';
	
	/**
	 * @var int максимальная глубина вложенности. 
	 * Значение 0(нуль) устанавливает глубину вложенности без ограничения.
	 * Значение данного свойства будет проигнорировано, если оно будет задано в 
	 * свойстве $nestableOptions. 
	 * По умолчанию 0(нуль).
	 */
	public $maxDepth=0;
	
	/**
	 * @var int (nestable option) group ID to allow dragging between lists (default 0)
	 * Значение данного свойства будет проигнорировано, если оно будет задано в 
	 * свойстве $nestableOptions. 
	 */
	public $group=0;
	
	/**
 	 * @var string|array имя шаблона отображения содержимого контейнера элемента, 
 	 * или массив [className, method]. В шаблон отображения или метод будет передан 
 	 * параметр "$data" (текущий элемент из $dataProvider).
	 */
	public $itemView=false;
	
	/**
	 * @var array|null дополнительный массив параметров.
	 */
	public $itemViewData=null;

	/**
	 * @var string текст, который будет отображаться, если ни одного элемента не найдено.
	 */
	public $emptyText='';
	
	/**
	 * @var array опции для query.nestable
	 * @see https://github.com/dbushell/Nestable
	 */
	public $nestableOptions=[];
	
	/**
	 * @var array html атрибуты элемента обертки списка.
	 */
	public $htmlOptions=[];
	
	/**
	 * (non-PHPdoc)
	 * @see \CWidget::init()
	 */
	public function init()
	{
		\AssetHelper::publish([
			'path'=>dirname(__FILE__) . Y::DS . 'assets',
			'js'=>['jquery/jquery.nestable.js', 'kontur/ext/nestedset/classes/NestableWidget.js'],
			'css'=>'jquery/jquery.nestable.css'
		]);
		
		$this->nestableOptions['maxDepth']=A::get($this->nestableOptions, 'maxDepth', $this->maxDepth);
		if(empty($this->nestableOptions['maxDepth']))
			unset($this->nestableOptions['maxDepth']);
		
		$this->nestableOptions['group']=A::get($this->nestableOptions, 'group', $this->group);
		if(empty($this->nestableOptions['group']))
			unset($this->nestableOptions['group']);
		
		Y::cs()->registerScript(
			'js'.$this->id, 
			"NestableWidget.init('{$this->id}', ".\CJavaScript::encode($this->nestableOptions).');',
			\CClientScript::POS_READY
		);
	}
	
	/**
	 * Получить id элемента.
	 * @param mixed $data данные элемента.
	 */
	public function getItemId($data) 
	{
		return ($this->dataProvider instanceof \CArrayDataProvider) ? $data[$this->attributeId] : $data->{$this->attributeId};
	}
	
	/**
	 * Получить уровень вложенности элемента.
	 * @param mixed $data данные элемента.
	 * Для \CArrayDataProvider если уровень не передан, 
	 * будет возвращено 1(единица).
	 */
	public function getItemLevel($data) 
	{
		return ($this->dataProvider instanceof \CArrayDataProvider) 
			? A::get($data, $this->attributeLevel, 1) 
			: $data->{$this->attributeLevel};
	}
	
	/**
	 * Получить id корневого элемента.
	 * @param mixed $data данные элемента.
	 */
	public function getItemRoot($data) 
	{
		return ($this->dataProvider instanceof \CArrayDataProvider) 
			? A::get($data, $this->attributeRoot) 
			: $data->{$this->attributeRoot};
	}
	
	/**
	 * Получить заголовок элемента.
	 * @param mixed $data данные элемента.
	 */
	public function getItemTitle($data) 
	{
		return ($this->dataProvider instanceof \CArrayDataProvider) ? $data[$this->attributeTitle] : $data->{$this->attributeTitle};
	}
	
	/**
	 * Получить содержимое контейнера элемента.
	 * @param mixed $data данные элемента.
	 * @return string
	 */
	public function getItemContent($data)
	{
		$content=null;
		
		if(is_callable($this->itemView) || is_array($this->itemView)) {
			$content=call_user_func($this->itemView, $data, $this->itemViewData);
		}
		elseif(!empty($this->itemView)) {
			$content=$this->render($this->itemView, ['data'=>$data, 'viewData'=>$this->itemViewData], true);	
		}
		else {
			$content=\CHtml::encode($this->getItemTitle($data));
		}
		
		return $content;
	}
}