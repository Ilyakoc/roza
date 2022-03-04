<?php $this->beginContent('//layouts/main'); ?>
<div class="sect-right-side pull-right">
<div>
<script type="text/javascript" src="//vk.com/js/api/openapi.js?127"></script>

<!-- VK Widget -->
<div id="vk_groups"></div>
<script type="text/javascript">
VK.Widgets.Group("vk_groups", {mode: 4, wide: 1, width: "250", height: "500", color1: 'FFFFFF', color2: '000000', color3: '5E81A8'}, 119714659);
</script>
</div>
	
</div>
<div class="sect-left-side">
	<?php echo $content; ?>
</div>
<div class="clearfix"></div>
<?php $this->endContent(); ?>
