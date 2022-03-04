<?php
/**
 * Тип поля "Текст".
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

echo \CHtml::textArea($name, $value, A::m(A::get($params, 'htmlOptions', []), ['class'=>'daw-inpt form-control', 'disabled'=>$isTemplate]));
?>