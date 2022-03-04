<?php
/* @var $this OfferController */
/* @var $model Offer */
/* @var $form CActiveForm */

if (!$model->product_id) {
    $model->product_id = Yii::app()->request->getQuery('product_id');
}
?>

<div class="row">
    <?php echo $form->labelEx($model,'title'); ?>
    <?php echo $form->textField($model,'title',array('class'=>'form-control')); ?>
    <?php echo $form->error($model,'title'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($model,'sort'); ?>
    <?php echo $form->textField($model,'sort',array('class'=>'form-control')); ?>
    <?php echo $form->error($model,'sort'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($model,'product_id'); ?>
    <?php echo $form->dropDownList($model,'product_id', Product::getAllProducts(),array('class'=>'form-control')); ?>
    <?php echo $form->error($model,'product_id'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($model, 'diameter'); ?>
    <?php echo $form->textField($model, 'diameter', array('class'=>'w10 inline')); ?>
    <?php echo $form->error($model, 'diameter'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($model, 'height'); ?>
    <?php echo $form->textField($model, 'height', array('class'=>'w10 inline')); ?>
    <?php echo $form->error($model, 'height'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($model, 'sale_value'); ?>
    <?php echo $form->textField($model, 'sale_value', array('class'=>'w10 inline')); ?>
    <?php echo $form->error($model, 'sale_value'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($model,'price'); ?>
    <?php echo $form->textField($model,'price',array('class'=>'form-control')); ?>
    <?php echo $form->error($model,'price'); ?>
</div>
