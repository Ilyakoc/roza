<?php
/** @var common\ext\file\widgets\UploadFile $this */
/** @var boolean $labelDisable не отображать наименование атрибута. По умолчанию (FALSE) отображать. */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;
$labelDisable=true;

$modelName=\CHtml::modelName($model) . '['.$model->id.']';
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<?= $form->labelEx($model, $b->attribute); ?>
	</div>
	<div class="panel-body"><?		
$t=Y::ct('common\ext\file\widgets\UploadFile.uploadImage', 'common');
$tbtn=Y::ct('CommonModule.btn', 'common');
$talert=Y::ct('CommonModule.alerts', 'common');

$uid=uniqid('id');
$labelDisable=isset($labelDisable) && $labelDisable;
?>
<div class="filebehavior__content"><? 
if(!$labelDisable) echo $form->labelEx($model, $b->attribute); 
if(!$b->owner->{$b->attribute}) {
	$b->owner->{$b->attribute}=$b->getAttributeValueForcy(); 
}
if($b->exists()): ?>
	<div class="filebehavior__item" id="<?= $uid; ?>">
		<div class="filebehavior__item-image">
		<?= $b->img($this->tmbWidth, $this->tmbHeight); ?> 
		<? 
		if($this->actionDelete instanceof \CAction) { 
			$url=$this->actionDelete->getController()->createUrl($this->actionDelete->getId(), ['id'=>$b->getId()]);
			if(!empty($this->actionDelete) && $this->actionDelete->ajaxMode) {
				echo \CHtml::ajaxLink($tbtn('remove'), $url, [
					'beforeSend'=>'js:function() { return confirm("'.$talert('confirm.remove').'"); }',
					'success'=>'js:function() { $("#'.$uid.'").remove(); }'
				], ['class'=>'btn btn-danger']);
			}
			else {
				echo \CHtml::link($tbtn('remove'), $url, ['class'=>'btn btn-danger']);
			}
		} 
		?>
		</div>
		<? if($b->hasEnabled()): ?>
		<div class="filebehavior__item-enable">
			<? $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), [
                'attribute'=>$b->attributeEnable,
                'htmlOptions'=>[
                    'name'=>$modelName."[{$b->attributeEnable}]"
                ]
            ])); ?>
		</div>
		<? endif; ?>
		<? if($b->hasAlt()): ?>
		<div class="filebehavior__item-alt">
			<? $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), [
                'attribute'=>$b->attributeAlt,
                'htmlOptions'=>[
                    'name'=>$modelName."[{$b->attributeAlt}]"
                ]
            ])); ?>
		</div>
		<? endif; ?>
	</div>
<? endif; ?>

<? $this->widget('\common\widgets\form\FileField', A::m(compact('form', 'model'), [
    'attribute'=>$b->attributeFile, 
    'note'=>$t('warning.gifAnimateBroken'),
    'htmlOptions'=>[
        'name'=>$modelName."[{$b->attributeFile}]"
    ]
])); ?>
</div>
	</div>
</div>