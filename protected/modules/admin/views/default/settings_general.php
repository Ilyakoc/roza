<div class="row">
    <?php echo $form->label($model,'header_link'); ?>
    <?php echo $form->textField($model,'header_link'); ?>
    <?php echo $form->error($model,'header_link'); ?>
</div>

<div class="row">
    <?php echo $form->label($model,'header_link_text'); ?>
    <?php echo $form->textField($model,'header_link_text'); ?>
    <?php echo $form->error($model,'header_link_text'); ?>
</div>

<hr>

<div class="row">
    <?php echo $form->label($model,'sitename'); ?>
    <?php echo $form->textField($model,'sitename', array('style'=>'width: 96%')); ?>
    <?php echo $form->error($model,'sitename'); ?>
</div>

<div class="row">
    <?php echo $form->label($model,'firm_name'); ?>
    <?php echo $form->textField($model,'firm_name'); ?>
    <?php echo $form->error($model,'firm_name'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'email'); ?>
    <?php echo $form->textField($model, 'email'); ?>
    <?php echo $form->error($model,'email'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'instagram'); ?>
    <?php echo $form->textField($model, 'instagram'); ?>
    <?php echo $form->error($model,'instagram'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'vk'); ?>
    <?php echo $form->textField($model, 'vk'); ?>
    <?php echo $form->error($model,'vk'); ?>
</div>

<div class="row">
    <?php echo $form->label($model,'phone'); ?>
    <?php echo $form->textField($model,'phone_code',array('class'=>'inline w10')); ?>
    <?php echo $form->textField($model,'phone', array('class'=>'inline w35')); ?>
    <?php echo $form->error($model,'phone'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'slogan'); ?>
    <?php $this->widget('admin.widget.CmsEditor.CmsEditor', array('model'=>$model, 'attribute'=>'slogan', 'full'=>false)); ?>
    <?php echo $form->error($model,'slogan'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'address'); ?>
    <?php $this->widget('admin.widget.CmsEditor.CmsEditor', array('model'=>$model, 'attribute'=>'address', 'full'=>false)); ?>
    <?php echo $form->error($model,'address'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'counter'); ?>
    <?php echo $form->textArea($model, 'counter'); ?>
    <?php echo $form->error($model,'counter'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'hide_news'); ?>
    <?php echo $form->dropDownList($model, 'hide_news', array(0=>'Нет', 1=>'Да')); ?>
    <?php echo $form->error($model, 'hide_news'); ?>
</div>

<?php if (Yii::app()->params['watermark']): ?>
<div class="row">
    <?php echo $form->label($model, 'watermark'); ?>
    <?php echo $form->dropDownList($model, 'watermark', array(0=>'Нет', 1=>'Да')); ?>
    <?php echo $form->error($model,'watermark'); ?>
</div>
<?php endif; ?>

<div class="row">
    <?php echo $form->label($model, 'cropImages'); ?>
    <?php echo $form->dropDownList($model, 'cropImages', array(0=>'Нет', 1=>'Да')); ?>
    &nbsp;
    <?php echo CHtml::link('Очистить кеш', array('default/clearImageCache'), array('class'=>'js-link', 'id'=>'clearCache')); ?>
    <?php echo $form->error($model,'cropImages'); ?>
    <script type="text/javascript">
        $(function() {
            var changer = $('#SettingsForm_cropImages');
            var old_value = $(changer).val();
            //$(changer).data('old_value', $(changer).val());
            $(changer).change(function() {
                if ($(this).val() != old_value) {
                    $('#clearCache').hide();
                } else {
                    $('#clearCache').show();
                }
            });

            $('#clearCache').click(function(e) {
                var t = this;
                e.preventDefault();
                $.get($(t).attr('href'), function() {
                    $(t).replaceWith('<span style="color:green;">очищен</span>');
                });
            });
        });
    </script>
</div>

<div class="row">
    <?php echo $form->label($model, 'menu_limit'); ?>
    <?php echo $form->textField($model, 'menu_limit', array('class'=>'w10')); ?>
    <?php echo $form->error($model,'menu_limit'); ?>
</div>

<div class="row">
    <?php echo $form->label($model, 'comments'); ?>
    <?php echo $form->textArea($model, 'comments'); ?>
    <?php echo $form->error($model,'comments'); ?>
</div>
