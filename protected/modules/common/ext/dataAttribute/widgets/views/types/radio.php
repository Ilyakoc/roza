<?php
/**
 * Тип поля "Cписок radio элементов".
 *
 * @var \common\ext\dataAttribute\widgets\DataAttribute $this 
 * @var string $name имя поля
 * @var string $value значение поля
 * @var string|array $data данные типа
 * @var string $view шаблон отображения
 * @var array $params дополнительные параметры
 * @var boolean $isTemplate генерируется шаблон нового элемента.
 */

echo \CHtml::radioButtonList($name, $value, $data, [
	'class'=>'daw-radio', 
	'disabled'=>$isTemplate, 
	'separator'=>'<span class="separator"></span>',
	'labelOptions'=>['style'=>'display:inline-block;']
]);
?>