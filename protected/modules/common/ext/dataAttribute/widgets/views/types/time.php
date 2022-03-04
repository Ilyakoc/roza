<?php
/**
 * Тип поля "Время".
 *
 * @use common.vendors.EJuiTimePicker.EJuiTimePicker
 *
 * @var \common\ext\dataAttribute\widgets\DataAttribute $this 
 * @var string $name имя поля
 * @var string $value значение поля
 * @var string|array $data данные типа
 * @var string $view шаблон отображения
 * @var array $params дополнительные параметры
 * @var boolean $isTemplate генерируется шаблон нового элемента.
 */
use common\components\helpers\HYii as Y;

/** @todo заменить подключение jQuery плагина Timepicker через EJuiTimePicker */  
$this->owner->widget('common.vendors.EJuiTimePicker.EJuiTimePicker', ['model'=>$this->behavior->owner, 'attribute'=>'id'], true);
?><style>.daw-inpt{width:100% !important;}.ui-tpicker-grid-label{border-collapse:separate !important;}</style><?

$jsVar='js5h6MKsZz';
Y::js($jsId,
	';window.'.$jsVar.'=function(_this) {
		if(!$(_this).data("picker-initialized")) { 
			$(_this).timepicker({hourGrid: 3, minuteGrid: 15, timeFormat:"hh:mm", hourMax: 24, minuteMax: 60, showButtonPanel: false, timeOnlyTitle: $(_this).data("picker-header")});
			$(_this).attr("data-picker-initialized", 1);
			$(_this).timepicker("show");
		}
	};',
	\CClientScript::POS_READY
);

echo \CHtml::textField($name, $value, [
	'class'=>'daw-inpt form-control', 
	'maxlength'=>255,
	'disabled'=>$isTemplate, 
	'readonly'=>true,
	'onclick'=>$jsVar.'(this)'
]);

?>