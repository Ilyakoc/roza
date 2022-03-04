<?php
/**
 * Виджет однострочного текстового поля формы (number). 
 * 
 */
namespace common\widgets\form;

use common\components\widgets\form\BaseField;

class NumberField extends BaseField
{
	/**
	 * @var string единица измерения. 
	 */
	public $unit;
	
	/**
	 * @var array имя HTML-тэга для элемента обретка единицы измерения.
	 */
	public $unitTag='span';
	
	/**
	 * @var array дополнительные HTML-атрибуты для элемента обретки единицы измерения.
	 */
	public $unitOptions=[];
	
	/**
	 * (non-PHPDoc)
	 * @see \common\components\widgets\form\BaseField::$view
	 */
	public $view='number-field';
}