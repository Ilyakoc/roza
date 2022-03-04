<?php
/**
 * Виджет поля формы dropdown-список.
 *
 */
namespace common\widgets\form;

use common\components\widgets\form\BaseField;

class DropDownListField extends BaseField
{
	/**
	 * (non-PHPDoc)
	 * @see \CActiveForm::dropDownList() параметр $data.
	 */
	public $data;

	/**
	 * (non-PHPDoc)
	 * @see \common\components\widgets\form\BaseField::$htmlOptions
	 */
	public $htmlOptions=[
		'class'=>'from-control'
	];

	/**
	 * (non-PHPDoc)
	 * @see \common\components\widgets\form\BaseField::$view
	 */
	public $view='dropdownlist-field';
}