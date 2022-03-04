<?php
/**
 * Виджет поля формы "Дата и времени".
 *
 */
namespace common\widgets\form;

use common\components\widgets\form\BaseField;

class DateTimeField extends BaseField
{
	/**
	 * (non-PHPDoc)
	 * @see \EJuiTimePicker::$language
	 */
	public $language='ru';
	
	/**
	 * (non-PHPDoc)
	 * @see \EJuiTimePicker::$mode
	 */
	public $mode='datetime';

	/**
	 * (non-PHPDoc)
	 * @see \EJuiTimePicker::$options
	 */
	public $options=['dateFormat'=>'yy-mm-dd hh:mm:ss', 'hourMax'=>24, 'minuteMax'=>60];
	
	/**
	 * @var array HTML options EJuiTimePicker
	 * @see \EJuiTimePicker::$htmlOptions
	*/
	public $htmlOptions=['class'=>'form-control', 'readonly'=>true];
	
	/**
	 * (non-PHPDoc)
	 * @see \common\components\widgets\form\BaseField::$view
	 */
	public $view='datetime-field';
}