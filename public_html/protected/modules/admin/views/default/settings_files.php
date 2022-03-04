<?php foreach (SettingsForm::$files as $fileAttribute): ?>
    <div class="row file-row">
        <?php if ($model->{$fileAttribute} && file_exists(Yii::getPathOfAlias('webroot') . Yii::app()->params['uploadSettingsPath'] . $model->{$fileAttribute})): ?>
            <div class="file-info">
                <?= $model->{$fileAttribute} ?>
                <a href="#" class="delete-file" data-attribute="<?= $fileAttribute ?>">Удалить</a>
            </div>    
        <?php endif; ?>

        <?php echo $form->label($model, $fileAttribute); ?>
        <?php echo $form->fileField($model, $fileAttribute)?>
        <?php echo CHtml::hiddenField($fileAttribute . '_file', $model->{$fileAttribute}, array('class' => 'old-file-input'))?>
        <?php echo $form->error($model, $fileAttribute); ?>
    </div>
<?php endforeach; ?>

<script>
    $(function() {
        $('.delete-file').click(function() {
            var $self = $(this);

            $.post('/admin/default/deleteSettingFile', {attribute: $self.data('attribute')}, function() {
                $self.closest('.file-row').find('.old-file-input').val('');
                $self.closest('.file-info').remove();
            });

            return false;
        });
    })
</script>