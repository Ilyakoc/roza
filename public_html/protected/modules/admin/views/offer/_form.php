<?php
/* @var $this OfferController */
/* @var $model Offer */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'offer-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php
	$tabs = array(
		'Основное'=>array('content'=>$this->renderPartial('_form_general', compact('model', 'form'), true), 'id'=>'tab-general'),
	);

	$tabs['Атрибуты'] = array('content'=>$this->renderPartial('_form_attributes', compact('model', 'form', 'fixAttributes'), true), 'id'=>'tab-attrs');

	$this->widget('zii.widgets.jui.CJuiTabs', array(
		'tabs' => $tabs,
		'options' => [],
	)); ?>

	<div class="row buttons">
		<div class="left">
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class'=>'default-button btn btn-primary')); ?>
			<?=CHtml::submitButton($model->isNewRecord ? 'Создать и выйти' : 'Сохранить и выйти', array('class'=>'default-button btn btn-info', 'name'=>'save_out'))?>
		</div>

		<?php if (!$model->isNewRecord): ?>
		<div class='left'>
		<a class='default-button btn btn-danger delete-b' href="<?=$this->createUrl('/cp/offer/delete', array("id"=>$model->id))?>"
		onclick="return confirm('Вы действительно хотите удалить запись?');">
			<span>Удалить</span></a>
		</div>
		<?php endif; ?>
		<div class="clr"></div>

	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->