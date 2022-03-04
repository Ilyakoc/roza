<?php
/** @var common\ext\file\widgets\UploadFile $this */
/** @var boolean $labelDisable не отображать наименование атрибута. По умолчанию (FALSE) отображать. */
use common\components\helpers\HYii as Y;
use common\components\helpers\HArray as A;

$tbtn=Y::ct('CommonModule.btn', 'common');
$talert=Y::ct('CommonModule.alerts', 'common');

$uid=uniqid('id');
$labelDisable=isset($labelDisable) && $labelDisable;
?>
<div class="filebehavior__content">
<? if($b->exists()): ?>
	<div class="filebehavior__item" id="<?= $uid; ?>">
		<div class="filebehavior__item-file"> 
		<?= $b->downloadLink(); ?>
		<? 
		if(($this->actionDelete instanceof \CAction) || is_string($this->actionDelete)) {
			if(is_string($this->actionDelete)) {
				$url=$this->actionDelete;
				$ajaxMode=true;
			}
			else { 
				$url=$this->actionDelete->getController()->createUrl($this->actionDelete->getId(), ['id'=>$b->getId()]);
				$ajaxMode=$this->actionDelete->ajaxMode;
			}
			if($ajaxMode) {
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
			<? $this->widget('\common\widgets\form\CheckboxField', A::m(compact('form', 'model'), ['attribute'=>$b->attributeEnable])); ?>
		</div>
		<? endif; ?>
		<? if($b->hasAlt()): ?>
		<div class="filebehavior__item-alt">
			<? $this->widget('\common\widgets\form\TextField', A::m(compact('form', 'model'), ['attribute'=>$b->attributeAlt])); ?>
		</div>
		<? endif; ?>
	</div>
<? endif; ?>

<? $this->widget('\common\widgets\form\FileField', A::m(compact('form', 'model'), ['attribute'=>$b->attributeFile])); ?>
</div>