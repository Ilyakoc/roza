<?php
/**
 * Виджет скрытого поля формы. 
 * 
 */
namespace common\widgets\form;

use common\components\widgets\form\BaseField;

class HiddenField extends BaseField
{
	/**
	 * (non-PHPDoc)
	 * @see \common\components\widgets\form\BaseField::$view
	 */
	public $view='hidden-field';
}