<?php $this->beginContent('//layouts/main'); ?>

<?php if (false): ?>
	<div class="sect-left-side">
		<?php echo $content; ?>
	</div>

	<div class="sect-right-side pull-right">
	    <iframe src='/inwidget/index.php?width=250&view=20' scrolling='no' frameborder='no' style='border:none;width:auto;height:425px;overflow:hidden;'>
	    </iframe>

	        

	        <script type="text/javascript" src="//vk.com/js/api/openapi.js?121"></script>
	        <!-- VK Widget -->
	        <div id="vk_groups"></div>
	        <script type="text/javascript">
	        VK.Widgets.Group("vk_groups", {mode: 0, width: "auto", height: "500", color1: 'FFFFFF', color2: '2B587A', color3: '5B7FA6'}, 119714659);
	        </script>

	</div>
<?php endif; ?>

<div class="container">
	<?= $content ?>
</div>

<?php $this->endContent(); ?>
