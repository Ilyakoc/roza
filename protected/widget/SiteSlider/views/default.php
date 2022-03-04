<ul id="site-slider">
    <?php foreach($slides as $slide): ?>
    <li>
        <div class="img">
            <a href="<?php echo $slide->link; ?>"><img src="<?php echo $slide->src; ?>" alt="" /></a>
        </div>
        <div class="title">
            <a href="<?php echo $slide->link; ?>"><?php echo $slide->title; ?></a>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
<script type="text/javascript">
    $(function() {
        $('#site-slider').jcarousel();
    });
</script>
