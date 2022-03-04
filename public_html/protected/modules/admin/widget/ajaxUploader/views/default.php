<div class="row upload-row">
    <label><?php echo $this->fieldLabel; ?></label>

    <?php  echo CHtml::fileField($this->createFieldName(), '', array(
        'multiple'=>'multiple',
        'id'=>$this->createFieldName()
    )); ?>
    <div class="status" style="display: none;"></div>
    <div class="loader" style="display: none;"></div>
    <div class="already_loaded">
        <?php $this->render($this->fileType .'_list', compact('items')); ?>
    </div>

    <script type="text/javascript">
        $(function () {
            var upload_id = "#<?php echo $this->createFieldName(); ?>";
            var parent_upload = $(upload_id).parent();

            $(upload_id).fileupload({
                dataType: 'html',
                url: '<?php echo Yii::app()->createUrl('admin/ajax/upload'); ?>',
                limitMultiFileUploads: 5,
                sequentialUploads: true,
                formData: <?php echo $this->sendParams; ?>,
                start: function(e) {
                     $(parent_upload).find('.status').hide();
                     $(parent_upload).find('.loader').show();
                },

                done: function (e, data) {
                    var list = $(this).parent().find('.already_loaded .uploadedList');
                    if (list.length) {
                        $(list).append(data.result);
                    }
                    $(upload_id).parent().find('.loader').hide();
                },

                error: function(e){
                    console.log(this);
                    $(parent_upload).find('.loader').hide();
                    $(parent_upload).find('.status').html("Ошибка при загрузке!").show();
                }
            });
        });
    </script>
</div>
