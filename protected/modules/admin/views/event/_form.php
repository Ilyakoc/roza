<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'event-form',
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
        ),
        'htmlOptions'=>array('enctype'=>'multipart/form-data'),
    )); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>
	
	<? /* ?><div class="row">
    	<? $this->widget('admin.widget.Alias.AliasFieldWidget', compact('form', 'model')); ?>
    </div>
	<? */ ?>
	
	<div class="row">
        <?php echo $form->labelEx($model,'type'); ?>
        <?php echo $form->dropDownList($model,'type', array('news' => 'Новости', 'article' => 'Статья', 'wiki' => 'Энциклопедия')); ?>
        <?php echo $form->error($model,'type'); ?>
    </div>

	<div class="row">
		<?php echo $form->labelEx($model,'text'); ?>
        <?php $this->widget('admin.widget.CmsEditor.CmsEditor', array(
            'model'=>$model,
            'attribute'=>'text',
            'htmlOptions'=>array('class'=>'big')
        )); ?>
		<?php echo $form->error($model,'text'); ?>
	</div>

    <?php echo $form->hiddenField($model, 'created'); ?>

    <?php if (!$model->isNewRecord): ?>
        <?php $this->widget('admin.widget.ajaxUploader.ajaxUploader', array(
            'fieldName'=>'images',
            'fieldLabel'=>'Загрузка фото',
            'model'=>$model,
            'fileType'=>'image'
        )); ?>

        <?php $this->widget('admin.widget.ajaxUploader.ajaxUploader', array(
            'fieldName'=>'files',
            'fieldLabel'=>'Загрузка файлов',
            'model'=>$model,
        )); ?>
    <?php endif; ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', array('class'=>'default-button')); ?>
        <?php echo CHtml::link('Отмена', array('index')); ?>
	</div>

    <?php $this->endWidget(); ?>

</div><!-- form -->
