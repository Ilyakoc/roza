<?php
/**
 * Виджет checkbox-поля формы.
 *
 */
namespace common\widgets\form;

use common\components\widgets\form\BaseField;

class CheckboxField extends BaseField
{
	/**
	 * (non-PHPDoc)
	 * @see \common\components\widgets\form\BaseField::$htmlOptions
	 */
	public $htmlOptions=[];
	
	/**
	 * (non-PHPDoc)
	 * @see \common\components\widgets\form\BaseField::$labelHtmlOptions
	 */
	public $labelOptions=['class'=>'inline'];

	/**
	 * (non-PHPDoc)
	 * @see \common\components\widgets\form\BaseField::$view
	 */
	public $view='checkbox-field';
}