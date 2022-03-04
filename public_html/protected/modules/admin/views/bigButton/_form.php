<?php
/* @var $this BigButtonController */
/* @var $model BigButton */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'big-button-form',
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
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
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
		<?php echo $form->textField($model,'link',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'link'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'sort'); ?>
		<?php echo $form->textField($model,'sort'); ?>
		<?php echo $form->error($model,'sort'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'alt'); ?>
		<?php echo $form->textField($model,'alt',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'alt'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Сохранить', ['class' => 'default-button']); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->