<?php
/**
 * Тип поля "Строка".
 *
 * @var \common\ext\dataAttribute\widgets\DataAttribute $this 
 * @var string $name имя поля
 * @var string $value значение поля
 * @var string|array $data данные типа
 * @var string $view шаблон отображения
 * @var array $params дополнительные параметры
 * @var boolean $isTemplate генерируется шаблон нового элемента.
 */
use common\components\helpers\HArray as A;

echo \CHtml::textField($name, $value, A::m(A::get($params, 'htmlOptions', []), ['class'=>'daw-inpt form-control', 'maxlength'=>255, 'disabled'=>$isTemplate]));
if($unit=A::get($params, 'unit')) {
	echo \CHtml::tag(A::get($params, 'unitTag', 'span'), A::get($params, 'unitOptions', ['class'=>'inline']), $unit);
}
?>