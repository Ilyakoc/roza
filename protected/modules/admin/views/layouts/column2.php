<?php $this->beginContent('/layouts/main'); ?>
    <div class="left-col">
        <?php $this->widget('zii.widgets.CMenu', array(
            'id'=>'site-menu',
            'items'=>CmsMenu::getInstance()->adminMenu(),
            'encodeLabel'=>false,
            'htmlOptions'=>array('class'=>'site-menu'),
        )); ?>

        <script type="text/javascript">
            $(function() {
                $("#site-menu").sortable({
                    items: "li:not(.ui-state-disabled)",
                    placeholder: "ui-state-highlight",
                    axis: "y",
                    helper: "original",
                    //cursor: "move",
                    stop: function(event, ui) {
                        var order = $(this).sortable('serialize');
                        $.post('<?php echo $this->createUrl('default/menuorder'); ?>', order);
                    }
                });
                $("#site-menu").disableSelection();

                $('<li></li>').attr('class', 'ui-state-disabled last').insertAfter($("#site-menu .ui-state-disabled:last"));
            });
        </script>
    </div>

    <div class="right-col">
        <div id="content" class="content">
            <?php echo $content; ?>
        </div>
    </div>

    <div class="clr"></div>
<?php $this->endContent(); ?>
