<div id="photos_container" class="photos_container clear_fix">
    <?php foreach($images as $img) : ?>
    <div class="photo_box" id="image-<?php echo $img->id; ?>">
        <div class="img link1">
            <?php echo CHtml::link('', array('default/removeImage', 'id'=>$img->id), array('class'=>'remove-icon')); ?>
            <img src="<?php echo $img->tmbUrl; ?>" alt="" />
        </div>
        <div class="buttons clear_fix">
            <a class="js-link left" onclick="openDialog(<?php echo $img->id; ?>);">изменить</a>
            <a class="js-link right" onclick="insertImage(this)">вставить</a>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script type="text/javascript">
    $(function() {
        var iboxs = $('#photos_container .img');

        $('#photos_container').sortable({
            helper: 'original',
            scrollSensitivity: 50,
            distance: 5,
            stop: function(event, ui) {
                var order = $(this).sortable('serialize');
                $.post('<?php echo $this->controller->createUrl('default/imageOrder'); ?>', order);
                $(iboxs).removeClass('hover');
            }
        });

        $(iboxs).hover(function() {
            $(this).toggleClass('hover');
        });

        $('#photos_container .remove-icon').click(function(e) {
            e.preventDefault();
            var t = $(this);

            $.get($(this).attr('href'), function(data) {
                if (data == 'ok')
                    $(t).parents('.photo_box').remove();
            });
        });
    });
</script>

<div class="edit_forms" style="display: none">
    <?php foreach($images as $image) : ?>
    <div id="uplImg-<?php echo $image->id; ?>">
        <div class="form">
            <div class="row">
                <img src="<?php echo $image->url; ?>" alt="" width="300" />
            </div>

            <div class="row">
                <div id="status-<?php echo $image->id; ?>" class="right"></div>
                <label for="desc-<?php echo $image->id; ?>">Описание</label>
                <textarea id="desc-<?php echo $image->id; ?>" name="desc-<?php echo $image->id; ?>"><?php echo $image->description; ?></textarea>
            </div>

            <div class="left">
                <input type="button" class="default-button" value="Сохранить описание" onclick="saveImageDesc(<?php echo $image->id; ?>)" />
            </div>
            <div class="right with-default-button">
                <a class="link" href="<?php echo Yii::app()->createUrl('admin/default/removeImage', array('id'=>$image->id)); ?>">Удалить фото</a>
            </div>
            <div class="clr"></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script type="text/javascript">
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



