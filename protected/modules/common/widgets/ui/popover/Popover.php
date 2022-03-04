<?php
/**
 * Виджет подсказки
 */
namespace common\widgets\ui\popover;

use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
use common\components\helpers\HHash;

class Popover extends \common\components\base\Widget
{
	/**
	 * @var string текст подсказки.
	 */
	public $content;
	
	/**
	 * @var string расположение подсказки. По умолчанию "right".
	 * Варианты: top, left, right, bottom.
	 */
	public $placement = 'right';
	
	/**
	 * @var string заголовок. По умолчанию (false) не задан.
	 */
	public $title = false;
	
	/**
	 * @var string выражение выборки элементов, при наведении которых будет отображатся подсказка.
	 */
	public $selector = 'input, textarea, label, select';
	
	/**
	 * {@inheritDoc}
	 * @see \common\components\base\Widget::$view
	 * Доступны шаблоны: popover, tooltip.
	 */
	public $view = 'popover';
	
	/**
	 * {@inheritDoc}
	 * @see \common\components\base\Widget::run()
	 */
	public function run()
	{
		$id=A::get($this->htmlOptions, 'id', HHash::ujs());
		
		$this->htmlOptions['id'] = $id;
		
		$cssClass = A::get($this->htmlOptions, 'class', false);
		$this->htmlOptions['class'] = ($cssClass ? "{$cssClass} " : '') . ' popover '. $this->placement;
		
		Y::js($id, '$("#'.$id.'").parent().css("position", "relative");$("#'.$id.'").parent().find("'.$this->selector.'").hover(function(){$("#'.$id.'").show();},function(){$("#'.$id.'").hide();});');
		
		$this->render($this->view, $this->params);
	}
}