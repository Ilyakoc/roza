<div id="images-block" class="images-block">
    <?php foreach($images as $id => $img): ?>
    <a class="image<?php if ($id > 0) echo ' hidden'; ?>" rel="images-block"
       title="<?php echo $img->description; ?>" href="/images/<?php echo $img->model.'/'.$img->filename; ?>">
        <img src="/images/<?php echo $img->model.'/tmb_'.$img->filename; ?>" alt="" />
    </a>
    <?php endforeach; ?>

    <?php if (count($images) > 1): ?>
    <div class="show-all-images">
        <a class="link">Посмотреть все фото</a>
    </div>
    <?php endif; ?>
</div>

<script type="text/javascript">
    $(function(){
        $("#images-block a.image").fancybox({
            'transitionIn'	: 'elastic',
		    'transitionOut'	: 'elastic',
            'titlePosition' : 'over',
            'autoScale'     : false
        });

        $('#images-block .show-all-images a').click(function() {
            var first = $(this).parents('.images-block').find('a:first');
            $(first).trigger('click');
        });
    });
</script>
