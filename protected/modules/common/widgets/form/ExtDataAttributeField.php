<?php
/**
 * Виджет поля формы для атрибута поведения \common\ext\dataAttribute\behaviors\DataAttributeBehavior. 
 * 
 */
namespace common\widgets\form;

use common\components\widgets\form\BaseField;

class ExtDataAttributeField extends BaseField
{
	/**
	 * @var \common\ext\dataAttribute\behaviors\DataAttributeBehavior поведение атрибута
	 */
	public $behavior;

	/**
	 * (non-PHPDoc)
	 * @see \common\components\widgets\form\BaseField::$view
	 */
	public $view='extdataattribute-field';
}