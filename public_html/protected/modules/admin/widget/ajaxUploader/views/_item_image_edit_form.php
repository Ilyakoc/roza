<?php
    Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl.'/js/jquery.jcrop.min.js'
    );
?>

<script type="text/javascript">
$(function(){

    $('#target').Jcrop({
        onChange: showPreview,
        onSelect: showPreview,
    onRelease: hidePreview,
        aspectRatio: 1
    });

  var $preview = $('#preview');
  // Our simple event handler, called from onChange and onSelect
  // event handlers, as per the Jcrop invocation above
  function showPreview(coords)
  {
    if (parseInt(coords.w) > 0)
    {
      var rx = 100 / coords.w;
      var ry = 100 / coords.h;

      $preview.css({
        width: Math.round(rx * 500) + 'px',
        height: Math.round(ry * 370) + 'px',
        marginLeft: '-' + Math.round(rx * coords.x) + 'px',
        marginTop: '-' + Math.round(ry * coords.y) + 'px'
      }).show();
    }
  }

  function hidePreview()
  {
    $preview.stop().fadeOut('fast');
  }

});
</script>
<div id="uplImg-<?php echo $image->id; ?>">
    <div class="form">
        <div class="row">
            <img src="<?php echo $image->url; ?>" alt="" width="300" id="target" />
        </div>

        <div class="row">
            <div id="status-<?php echo $image->id; ?>" class="right"></div>
            <label for="desc-<?php echo $image->id; ?>">Описание123</label>
            <textarea id="desc-<?php echo $image->id; ?>" name="desc-<?php echo $image->id; ?>"><?php echo $image->description; ?></textarea>
            <div style="width:100px;height:100px;overflow:hidden;margin-left:5px;">
                <img src="<?php echo $image->url; ?>" id="preview" />
            </div>
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

