<ul id="onlyImages" class="onlyImages modelImages">
    <?php foreach($images as $image): ?>
    <li>
        <div class="img">
            <?php echo CHtml::link('', array('default/removeImage', 'id'=>$image->id), array('class'=>'remove-icon ajax-del')); ?>
            <img src="<?php echo $image->tmbUrl; ?>" alt="" data-raw="<?php echo $image->url; ?>" data-w="<?php echo $image->getWidth($image->url); ?>" data-h="<?php echo $image->getHeight($image->url); ?>"/>
        </div>
        <a class="js-link" onclick="return openDialog(<?php echo $image->id; ?>);">Изменить</a>
    </li>
    <?php endforeach; ?>
</ul>

<div id="imagesFormList" style="display: none;">
    <?php foreach($images as $image): ?>
    <div id="imageForm-<?php echo $image->id; ?>" class="form">
        <div class="row">
            <p>Описание фото</p>
            <textarea name="desc" id="desc-<?php echo $image->id; ?>" cols="30" rows="10"><?php echo $image->description; ?></textarea>
            <input class="image_id" type="hidden" value="<?php echo $image->id; ?>" />
        </div>
        <div class="row buttons">
            <div class="left"><input class="saveButton" type="button" value="Сохранить описание" /></div>
            <div class="right"><a class="link" onclick="$.modal.close(); return false;">Отмена</a></div>
            <div class="clr"></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script type="text/javascript">
    $(function() {
        $('#onlyImages .ajax-del').click(function(e) {
            e.preventDefault();
            var t = $(this);
            $.get($(this).attr('href'), function(data) {
                if (data == 'ok') { $(t).parents('li').remove();}
            });
        });

        $('#onlyImages .img').hover(function() {
            $(this).toggleClass('hover');
        });

        $('#imagesFormList .saveButton').click(function(e) {
            e.preventDefault();
            var url = '<?php echo Yii::app()->createUrl('admin/default/saveImageDesc'); ?>';
            var form = $(this).parents('.form');
            var data = {
                desc: $(form).find('textarea').val(),
                id: $(form).find('.image_id').val()
            };
            $.post(url, data, function(data) {
                if (data == 'ok') {$.modal.close();}
            });
        });
    });

    function openDialog(id) {
        $('#imageForm-'+id).modal({
            minWidth: 300,
            persist: true
        });
        return false;
    }
</script>
