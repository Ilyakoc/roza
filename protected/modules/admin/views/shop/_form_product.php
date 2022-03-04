<?php
/* @var ShopController $this */
?>
<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'=>'product-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange'=>false
        ),
        'htmlOptions'=>array('enctype'=>'multipart/form-data'),
    )); ?>

    <?php 
    $tabs = array(
      'Основное'=>array('content'=>$this->renderPartial('_form_product_general', compact('model', 'form'), true), 'id'=>'tab-general'),
      'Seo'=>array('content'=>$this->renderPartial('_form_product_seo', compact('model', 'form'), true), 'id'=>'tab-seo'),            
    );

    $tabs['Соп. товары'] = array('content'=>$this->renderPartial('_form_product_related', compact('model', 'form'), true), 'id'=>'tab-related');

    if(!$model->isNewRecord) {
    	$tabs['Доп категории'] = array('content'=>$this->renderPartial('_form_product_categories', compact('model', 'form', 'categoryList', 'relatedCategories'), true), 'id'=>'tab-categories');
        $tabs['Торг. пр.'] = array('content'=>$this->renderPartial('_form_product_offers', compact('model', 'form'), true), 'id'=>'tab-offers');
    }

    $tabs['Атрибуты'] = array('content'=>$this->renderPartial('_form_product_attributes', compact('model', 'form', 'fixAttributes'), true), 'id'=>'tab-attrs');
       
    $this->widget('zii.widgets.jui.CJuiTabs', array(
        'tabs'=> $tabs,
        'options'=>array()
    )); ?>

	<div class="row buttons">
        <div class="left">
		    <?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class'=>'default-button')); ?>
            <?php echo CHtml::link('Отмена', array('shop/category', 'id'=>$model->category_id)); ?>
        </div>

        <?php if (!$model->isNewRecord): ?>
        <div class="right with-default-button">
            <a href="<?php echo $this->createUrl('shop/productClone', array('id'=>$model->id)); ?>">Клонировать товар</a>

            <a href="<?php echo $this->createUrl('shop/productDelete', array('id'=>$model->id)); ?>"
               onclick="return confirm('Вы действительно хотите удалить товар?')">Удалить товар</a>
        </div>
        <?php endif; ?>
        <div class="clr"></div>
	</div>
    <?php $this->endWidget(); ?>
</div><!-- form -->
