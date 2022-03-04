<?php
/* @var $this ButtonController */
/* @var $model Button */
/* @var $form CActiveForm */

$classList = [
	'mother' => 'Букеты на день матери',
	'teacher-day' => 'Букеты на день учителя',
	'the_end' => 'Букеты на выпускной',
	'twenty-three_few' => 'Букеты на 23 февраля',
	'new-year' => 'Букеты к Новому году',
	'love_day' => 'День всех влюбленных',
	'last_bell' => 'Последний звонок',
	'birhday' => 'Букеты на день рождения',
	'frist-sent' => 'Букеты на 1 сентября',
	'ybel' => 'Букеты на юбилей',
];
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'button-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
	'clientOptions'=>array(
	    'validateOnSubmit'=>true,
	),
)); ?>

	<div class="row">
		<?php echo $form->checkBox($model,'active'); ?>
		<?php echo $form->labelEx($model,'active', ['class' => 'inline']); ?>
		<?php echo $form->error($model,'active'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array()); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model,'preview'); ?>
	    <?php echo $form->fileField($model,'preview'); ?>
	    <?php echo $form->error($model,'preview'); ?>
	</div>

	<?php if($model->preview):?>
	    <div class="row">
	        <img src="/images/button/<?php echo $model->preview;?>" style="max-width: 300px;">
	    </div>
	<?php endif;?>

	<div class="row">
		<?php echo $form->labelEx($model,'link'); ?>
		<?php echo $form->textField($model,'link',array()); ?>
		<?php echo $form->error($model,'link'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'image_class'); ?>
		<?php echo $form->dropDownList($model,'image_class', $classList); ?>
		<?php echo $form->error($model,'image_class'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Сохранить', ['class' => 'default-button']); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->