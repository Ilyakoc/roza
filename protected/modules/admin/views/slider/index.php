<?php
/* @var SliderController $this */
$this->pageTitle = 'Магазин - '. $this->appName;
?>

<h1>Слайдер</h1>

<p><a id="create_slide" href="<?php echo $this->createUrl('slider/create') ?>">Новый слайд</a></p>

<ul class="sliders-list" id="sliders-list">
    <?php foreach($slides as $slide): ?>
    <li id="item-<?php echo $slide->id; ?>">
        <a href="<?php echo $this->createUrl('slider/update', array('id'=>$slide->id)); ?>"><?php echo $slide->title; ?></a>
        <a class="remove" href="<?php echo $this->createUrl('slider/remove', array('id'=>$slide->id)); ?>">Удалить</a>
        <div class="clr"></div>
    </li>
    <?php endforeach; ?>
</ul>

<script type="text/javascript">
    $(function() {
        $('#sliders-list').sortable({
            //items: "li:not(.ui-state-disabled)",
            placeholder: "ui-state-highlight",
            axis: "y",
            helper: "original",
            stop: function(event, ui) {
                var order = $(this).sortable('serialize');
                $.post('<?php echo $this->createUrl('slider/order'); ?>', order);
            }
        });

        /*$('#create_slide').click(function(e) {
            e.preventDefault();
            var t = this;

            $.get($(t).attr('href'), function(result) {
                console.log(result)
            }, 'html');
        });*/
    });
</script>

<style type="text/css">
    .sliders-list .ui-state-highlight {
        height: 17px;
        background: #eee;
        border: solid 1px #ddd;
    }
</style>
