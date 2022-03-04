<?php
/**
 * Тип поля "Число".
 * 
 * В $params может быть передан параметр "step" - шаг. По умолчанию 1(единица).
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

$unit=A::get($params, 'unit');

echo \CHtml::numberField($name, $value, A::m(A::get($params, 'htmlOptions', []), [
	'class'=>'daw-inpt form-control' . ($unit ? ' inline' : ''),
	'step'=>A::get($params, 'step', 1),
	'maxlength'=>255,
	'disabled'=>$isTemplate
]));
if($unit) {
	echo \CHtml::tag(A::get($params, 'unitTag', 'span'), A::get($params, 'unitOptions', ['class'=>'inline']), $unit);
}
?>
