<?php
namespace common\widgets\ui\flash;

use common\components\base\Widget;

class Yii extends Widget
{
	/**
	 * @var string идентификатор флеш сообщения.
	 */
	public $id=null;
	
	/**
	 * (non-PHPdoc)
	 * @see Widget::$view
	 * 
	 * Шаблоны по умолчанию:
	 * yii_flash - стандартный шаблон
	 * yii_flash_success
	 */
	public $view='default';
	
	/**
	 * @var array дополнительные HTML-атрибуты для основного элемента.
	 */
	public $htmlOptions=['class'=>'alert alert-info'];
}