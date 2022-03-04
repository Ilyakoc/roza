<?php
/**
 * Тип поля "Модель"
 *
 * @var \common\ext\dataAttribute\widgets\DataAttribute $this
 * @var string $name имя поля
 * @var string $value значение поля
 * @var string|array $data данные типа
 * @var string $view шаблон отображения
 * @var array $params дополнительные параметры
 * @var boolean $isTemplate генерируется шаблон нового элемента.
 * @var \RangeofItemSettings $model
 * 
 * При ajax запросе 
 * @var \RangeofItemSettings $model
 */
use common\components\helpers\HArray as A;

$form=new \CActiveForm;

$name=\CHtml::modelName($model) . '['.$model->id.']';
$idValue=\CHtml::modelName($model) . '_'.$model->id;

echo \CHtml::hiddenField($name.'[id]', $model->id);

$this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
	'attribute'=>'title',
	'htmlOptions'=>['name'=>$name.'[title]', 'id'=>$idValue.'_title', 'class'=>'form-control']
]));

echo \CHtml::link('больше параметров', '#RangeofItemSettings_'.$model->id, ['class'=>'btn btn-default btn-xs', 'data-toggle'=>'collapse']);
echo \CHtml::openTag('div', ['id'=>'RangeofItemSettings_'.$model->id, 'class'=>'panel-collapse collapse']);

$this->widget('\common\widgets\form\AliasField', A::m(compact('form', 'model'), [
	'attribute'=>'code',
	'titleActiveId'=>$idValue.'_title',
	'aliasActiveId'=>$idValue.'_code',
	'htmlOptions'=>['name'=>$name.'[code]', 'id'=>$idValue.'_code', 'class'=>'form-control inline']
]));

$this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), [
	'attribute'=>'active',
	'htmlOptions'=>['name'=>$name.'[active]']
]));

$this->widget('\common\ext\file\widgets\UploadFile', [
	'behavior'=>$model->imageBehavior,
	'form'=>$form,
	'actionDelete'=>\Yii::app()->getController()->createAction('removeImage'),
	'tmbWidth'=>260,
	'tmbHeight'=>115,
	'view'=>'admin.views.settings._rangeof_item_panel_upload_image'
]);

// $this->widget('\common\widgets\form\TextAreaField', A::m(compact('form', 'model'), [
// 	'attribute'=>'url',
// 	'htmlOptions'=>['name'=>$name.'[url]', 'class'=>'form-control']
// ]));

echo \CHtml::closeTag('div');
?>