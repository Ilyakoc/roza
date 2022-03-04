<?php $this->pageTitle = 'Настройки сайта - '. $this->appName; ?>

<h1>Настройки</h1>

<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'=>'settings-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
        ),
        'htmlOptions' => array('enctype'=>'multipart/form-data'),
    )); ?>

    <?php
    $tabs = array(
        'Общие'=>array('content'=>$this->renderPartial('settings_general', compact('model', 'form'), true), 'id'=>'tab-general'),
        'Блог' =>array('content'=>$this->renderPartial('settings_blog'   , compact('model', 'form'), true), 'id'=>'tab-blog'),
        'Seo'  =>array('content'=>$this->renderPartial('settings_seo'    , compact('model', 'form'), true), 'id'=>'tab-seo'),
        'Каталог'  =>array('content'=>$this->renderPartial('settings_shop'    , compact('model', 'form'), true), 'id'=>'tab-shop'),
    );

    if (SettingsForm::$files) {
        $tabs['Файлы'] = array('content'=>$this->renderPartial('settings_files', compact('model', 'form'), true), 'id'=>'tab-files');
    }
    ?>

    <?php $this->widget('zii.widgets.jui.CJuiTabs', array(
        'tabs'=>$tabs,
        'options'=>array(
            /*'collapsible'=>true,*/
        )
    )); ?>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Сохранить', array('class'=>'default-button')); ?>
        <?php echo CHtml::link('отмена', array('default/index')); ?>
    </div>

    <?php $this->endWidget();  ?>
</div>

