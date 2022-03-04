<?php
/**
 * Виджет поля формы radio-список.
 *
 */
namespace common\widgets\form;

use common\components\widgets\form\BaseField;

class RadioListField extends BaseField
{
	/**
	 * (non-PHPDoc)
	 * @see \CActiveForm::radioButtonList() параметр $data.
	 */
	public $data;

	/**
	 * (non-PHPDoc)
	 * @see \common\components\widgets\form\BaseField::$htmlOptions
	 */
	public $htmlOptions=[
		'container'=>'div', 
		'labelOptions'=>['class'=>'inline', 'style'=>'font-weight:normal']
	];

	/**
	 * (non-PHPDoc)
	 * @see \common\components\widgets\form\BaseField::$view
	 */
	public $view='radiolist-field';
}
