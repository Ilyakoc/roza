<script type="text/javascript">
    $(function(){
        $("#Page_title").keyup(function(){
            $('#Page_title').translit('send', '#Page_alias');
        });
    });
</script>

<div class="row">
    <?php echo $form->labelEx($model,'title'); ?>
    <?php echo $form->textField($model,'title'); ?>
    <?php echo $form->error($model,'title'); ?>
</div>

<?php if ($model->blog_id): ?>
<div class="row">
    <?php echo $form->labelEx($model, 'blog_id'); ?>
    <?php echo CHtml::textField('blog_name', $model->blog->title, array('readonly'=>'readonly'))?>
    <?php echo $form->hiddenField($model, 'blog_id'); ?>
    <?php echo $form->error($model, 'blog_id'); ?>
</div>
<?php endif; ?>

<div class="row">
    <?php echo $form->labelEx($model,'alias'); ?>
    <?php echo $form->textField($model,'alias'); ?>
    <?php echo $form->error($model,'alias'); ?>
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

<div class="row">
    <?php /*$this->widget('widget.adminImages.adminImages', array('model'=>$model, 'form'=>$form));*/ ?>
</div>

<div class="row">
    <?php /*$this->widget('widget.adminFiles.adminFiles', array('model'=>$model, 'form'=>$form));*/ ?>
</div>
<?php endif; ?>
