<ul id="site-banner" style="visibility:hidden">
    <?php foreach($banners as $banner): ?>
    <li>
        <div class="img">
            <a href="<?php echo $banner->link; ?>"><img src="<?php echo $banner->src; ?>" alt="" style="<?php printf("width:%dpx;height:%dpx", Yii::app()->params['banner']['carousel']['width'], Yii::app()->params['banner']['carousel']['height']); ?>;" /></a>
        </div>
        <div class="title">
            <a href="<?php echo $banner->link; ?>"><?php echo $banner->title; ?></a>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
<script type="text/javascript">
    $(function() {
        $('#site-banner').jcarousel();
	    $('#site-banner').attr('visibility', 'visible');
    });
</script>
