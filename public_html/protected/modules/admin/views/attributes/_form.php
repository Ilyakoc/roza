<div class="form">
<? $this->breadcrumbs = array(
    'Атрибуты товара'=>array('attributes/index'),
    $model->isNewRecord ? 'Создание атрибута' : 'Редактирование атрибута'
);?>

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'attributes-12-form',
    // Please note: When you enable ajax validation, make sure the corresponding
    // controller action is handling ajax validation correctly.
    // See class documentation of CActiveForm for details on this,
    // you need to use the performAjaxValidation()-method described there.
    'enableAjaxValidation'=>false,
)); ?>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'name'); ?>
        <?php echo $form->textField($model,'name'); ?>
        <?php echo $form->error($model,'name'); ?>
    </div>
    
    <div class="row">
        <?php echo $form->labelEx($model,'sort'); ?>
        <?php echo $form->textField($model,'sort'); ?>
        <?php echo $form->error($model,'sort'); ?>
    </div>

    <div class="row">
        <?php echo $form->checkBox($model,'fixed'); ?>
        <?php echo $form->labelEx($model, 'fixed', array('class'=>'inline')); ?>
        <?php echo $form->error($model,'fixed'); ?>
    </div>


    <div class="row buttons">
        <?php echo CHtml::submitButton('Сохранить', array('class'=>'default-button')); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->