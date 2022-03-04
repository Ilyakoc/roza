<div id="question-form-div" class="form">
    <h2>Написать отзыв</h2>

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'question-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange'=>false,
            'afterValidate'=>'js: function(form, data, hasError) {submitForm(form, hasError);}'
        )
    )); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'username'); ?>
        <?php echo $form->textField($model,'username',array('maxlength'=>255)); ?>
        <?php echo $form->error($model,'username'); ?>
    </div>

	<div class="row">
		<?php echo $form->labelEx($model,'mail'); ?>
		<?php echo $form->textField($model,'mail',array('maxlength'=>255)); ?>
		<?php echo $form->error($model,'mail'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'question'); ?>
		<?php echo $form->textArea($model,'question',array('maxlength'=>255)); ?>
		<?php echo $form->error($model,'question'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Отправить'); ?>
	</div>
    <?php $this->endWidget(); ?>
</div><!-- form -->
