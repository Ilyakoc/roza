<?php
/* @var ShopController $this */
?>
<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'=>'category-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange'=>false
        )
    )); ?>

    <?php $this->widget('zii.widgets.jui.CJuiTabs', array(
        'tabs'=>array(
            'Основное'=>array('content'=>$this->renderPartial('_form_category_general', compact('model', 'form'), true), 'id'=>'tab-general'),
            'Seo'=>array('content'=>$this->renderPartial('_form_category_seo'    , compact('model', 'form'), true), 'id'=>'tab-seo'),
            'Настройки'=>array('content'=>$this->renderPartial('_form_category_settings'    , compact('model', 'form'), true), 'id'=>'tab-settings'),
        ),
        'options'=>array()
    )); ?>

    <div class="row buttons">
        <div class="left">
		    <?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class'=>'default-button')); ?>
            <?php echo CHtml::link('Отмена', array('index')); ?>
        </div>

        <?php if (!$model->isNewRecord && !count($model->tovars)): ?>
        <div class="right with-default-button">
            <a href="<?php echo $this->createUrl('shop/categoryDelete', array('id'=>$model->id)); ?>"
               onclick="return confirm('Вы действительно хотите удалить категорию?')">Удалить категорию</a>
        </div>
        <?php endif; ?>
        <div class="clr"></div>
	</div>

    <?php $this->endWidget(); ?>
</div><!-- form -->
