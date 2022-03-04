<?php $this->pageTitle = 'Настройки магазина - '. $this->appName; ?>
<h1>Настройки магазина</h1>

<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'=>'shop-settings-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'validateOnChange'=>false
        )
    )); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'cropTop'); ?>
        <?php echo $form->dropDownList($model, 'cropTop', array('top'=>'Верх', 'center'=>'Центр', 0=>'Нет')); ?>
        <?php echo $form->error($model, 'cropTop'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Сохранить', array('class'=>'default-button')); ?>
        <?php echo CHtml::link('отмена', array('shop/index')); ?>
    </div>
    <?php $this->endWidget(); ?>
</div>
