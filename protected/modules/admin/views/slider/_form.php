<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
    'id'=>'slide-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array(
        'validateOnSubmit'=>true,
    ),
    'htmlOptions' => array('enctype'=>'multipart/form-data'),
)); ?>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->checkBox($model,'hide_title'); ?>
        <?php echo $form->labelEx($model,'hide_title', ['class' => 'inline']); ?>
        <?php echo $form->error($model,'hide_title'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'title'); ?>
        <?php echo $form->textField($model,'title'); ?>
        <?php echo $form->error($model,'title'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'link'); ?>
        <?php echo $form->textField($model,'link'); ?>
        <?php echo $form->error($model,'link'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'type'); ?>
        <?php echo $form->dropDownList($model,'type', array(/*1 => 'Карусель', */2 => 'Слайд-шоу')); ?>
        <?php echo $form->error($model,'type'); ?>
    </div>

    <?php if ($src = $model->src): ?>
    <div class="row">
        <img src="<?php echo $src; ?>" alt="" style="max-width:100%" />
    </div>
    <div class="row">
        <a id="change-file" class="js-link">Сменить</a>
    </div>
    <script type="text/javascript">
        $(function(){
            $('#file_field').hide();
            $('#change-file').click(function() {
                $('#file_field').show();
                $(this).remove();
            });
        });
    </script>
    <?php endif; ?>

    <div class="row" id="file_field">
        <?php echo $form->labelEx($model,'file'); ?>
        <?php echo $form->fileField($model, 'file'); ?>
        <?php echo $form->error($model,'file'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class'=>'default-button')); ?>
        <?php echo CHtml::link('Отмена', array('index')); ?>
    </div>

    <?php $this->endWidget(); ?>
</div><!-- form -->
