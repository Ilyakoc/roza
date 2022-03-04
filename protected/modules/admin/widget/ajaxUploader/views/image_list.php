<div id="<?php echo $this->createFieldName(); ?>_container" class="uploadedList photos_container clear_fix">
    <?php foreach($items as $item) $this->render('_item_image', compact('item')); ?>
</div>

<script type="text/javascript">
    $(function() {
        var iboxs = $('#<?php echo $this->createFieldName(); ?>_container .img');

        $('#<?php echo $this->createFieldName(); ?>_container').sortable({
            scrollSensitivity: 50,
            distance: 5,
            stop: function(event, ui) {
                var order = $(this).sortable('serialize');
                $.post('<?php echo $this->controller->createUrl('default/imageOrder'); ?>', order);
                $(iboxs).removeClass('hover');
            }
        });

        $(iboxs).live('hover', function() {
            $(this).toggleClass('hover');
        });

        $('#<?php echo $this->createFieldName(); ?>_container .remove-icon').live('click', function(e) {
            e.preventDefault();
            var t = $(this);

            $.get($(this).attr('href'), function(data) {
                if (data == 'ok')
                    $(t).parents('.photo_box').remove();
            });
        });
    });

    function saveImageDesc(id) {
        var data = {'desc': $('#desc-'+id).val(), 'id': id};

        $.post('<?php echo Yii::app()->createUrl('admin/default/saveImageDesc'); ?>', data, function(result) {
            if (result == 'ok') {
                $('#status-'+id).text('Сохранено!').show(100).delay(2000).hide(100);
            } else {
                $('#status-'+id).text('Ошибка сохранения!');
            }
        });
    }

    function openDialog(id) {
        $('#uplImg-'+id).modal({
            minWidth: 300,
            persist: true
        });
    }

    function insertImage(self) {
        var src = $(self).parents('.photo_box').find('img').attr('src');
        var src_full = src.replace('tmb_', '');

        var ed  = tinyMCE.activeEditor;
        ed.focus();
        ed.execCommand('mceInsertContent', false, '<a class="image-full" href="'+ src_full +'"><img id="__img_tmp" /></a>');
        ed.dom.setAttrib('__img_tmp', 'src', src);
        ed.dom.setAttrib('__img_tmp', 'id', '');
    }
</script>
