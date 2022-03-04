<?php
/* @var AdminController $this */
/* @var LoginForm $model */
?>
<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array('id'=>'login-form')); /* @var CActiveForm $form */ ?>
        <div class="row">
            <?php echo $form->label($model, 'username', array('class'=>'placeholder hidden')); ?>
            <?php echo $form->textField($model,'username'); ?>
            <?php echo $form->error($model,'username'); ?>
        </div>

        <div class="row">
            <?php echo $form->label($model, 'password', array('class'=>'placeholder hidden')); ?>
            <?php echo $form->passwordField($model,'password'); ?>
            <?php echo $form->error($model,'password'); ?>
        </div>

        <div class="row enter">
            <?php echo CHtml::submitButton('Вход', array('class'=>'default-button')); ?>
            <a id="send-login" class="custom-submit-button">Вход</a>
        </div>
    <?php $this->endWidget(); ?>
</div>
