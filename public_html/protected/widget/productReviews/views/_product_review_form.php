<?php
/**
 * File: _product_review_form.php
 * User: Mobyman
 * Date: 10.04.13
 * Time: 12:30
 */
?>
<?php CmsHtml::js('/js/jquery.rating.pack.js'); ?>
<style type="text/css">
    .row {margin:20px 0 10px !important;}
    div.star-rating{float:left;width:18px;height:16px;text-indent:-999em;cursor:pointer;display:block;background:transparent;overflow:hidden;}
    div.star-rating {background: url(<?php echo Yii::app()->baseUrl; ?>/images/marks/star.png) 0 16px; height:16px;}
    div.star-rating-hover {background: url(<?php echo Yii::app()->baseUrl; ?>/images/marks/star.png); height:16px;}
    div.star-rating-on {background: url(<?php echo Yii::app()->baseUrl; ?>/images/marks/star.png); height:16px;}
    span.star-view {background: url(<?php echo Yii::app()->baseUrl; ?>/images/marks/star.png); height:16px; display: inline-block;vertical-align: top; float: right;}
    .star-1 {width:18px;}
    .star-2 {width:36px;}
    .star-3 {width:54px;}
    .star-4 {width:72px;}
    .star-5 {width:90px;}
</style>
<div style="display: none;">
<div id="review-form-div" class="form">
    <h2>Написать отзыв</h2>
    <?php /** @var CActiveForm $form */ ?>
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'product-review-form',
        'enableClientValidation'=>true,
        'action' => Yii::app()->createUrl('review'),
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange'=>false,
            'afterValidate'=>'js: function(form, data, hasError) {submitForm(form, hasError);}',
        ),
    )); ?>

    <div class="row">
        <?php echo $form->labelEx($model,'mark', array('style' => 'display:inline-block; width:100px')); ?>
        <span style="display: inline-block;" id="ProductReview_mark">
            <input class="star required" type="radio" name="ProductReview[mark]" value="1"/>
            <input class="star" type="radio" name="ProductReview[mark]" value="2"/>
            <input class="star" type="radio" name="ProductReview[mark]" value="3"/>
            <input class="star" type="radio" name="ProductReview[mark]" value="4"/>
            <input class="star" type="radio" name="ProductReview[mark]" value="5"/>
        </span>
        <?php echo $form->error($model,'mark'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'username'); ?>
        <?php echo $form->textField($model,'username',array('maxlength'=>255)); ?>
        <?php echo $form->error($model,'username'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'text'); ?>
        <?php echo $form->textArea($model,'text',array('maxlength'=>255)); ?>
        <?php echo $form->error($model,'text'); ?>
    </div>

    <?php echo $form->hiddenField($model, 'product_id', array('value' => $product->id)); ?>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Отправить'); ?>
    </div>
    <?php $this->endWidget(); ?>
</div><!-- form -->
</div>
