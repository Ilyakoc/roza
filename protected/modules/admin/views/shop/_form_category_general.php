    <div class="row">
        <?php echo $form->checkBox($model, 'hide_menu'); ?>
        <?php echo $form->labelEx($model, 'hide_menu', ['class' => 'inline']); ?>
        <?php echo $form->error($model, 'hide_menu'); ?>
    </div>    

    <div class="row">
        <?php echo $form->labelEx($model, 'title'); ?>
        <?php echo $form->textField($model, 'title'); ?>
        <?php echo $form->error($model, 'title'); ?>
    </div>

    <div class="row">
    	<? $this->widget('admin.widget.Alias.AliasFieldWidget', compact('form', 'model')); ?>
    </div>
    
    <div class="row">
        <?php echo $form->checkBox($model, 'front'); ?>
        <?php echo $form->labelEx($model, 'front', array('class'=>'inline')); ?>
        <?php echo $form->error($model, 'front'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'sort_slider'); ?>
        <?php echo $form->textField($model, 'sort_slider'); ?>
        <?php echo $form->error($model, 'sort_slider'); ?>
    </div>

    <!--div class="row">
        <?php echo $form->labelEx($model, 'parent_id'); ?>
        <?php /*echo $form->textField($model, '');*/ ?>
        <?php echo $form->error($model, 'title'); ?>
    </div-->

    <div class="row">
        <?php echo $form->labelEx($model, 'description'); ?>
        <?php $this->widget('admin.widget.CmsEditor.CmsEditor', array('model'=>$model, 'attribute'=>'description')); ?>
        <?php echo $form->error($model, 'description'); ?>
    </div>

	<div class="row">
        <?php echo $form->labelEx($model, 'under_description'); ?>
        <?php $this->widget('admin.widget.CmsEditor.CmsEditor', array('model'=>$model, 'attribute'=>'under_description')); ?>
        <?php echo $form->error($model, 'under_description'); ?>
    </div>

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
