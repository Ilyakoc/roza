<?php
/* @var SliderController $this */
$this->pageTitle = 'Магазин - '. $this->appName;
?>

<h1>Баннер</h1>

<p><a id="create_slide" href="<?php echo $this->createUrl('banner/create') ?>">Новый баннер</a></p>

<ul class="sliders-list" id="sliders-list">
	<?php foreach($banners as $banner): ?>
		<li id="item-<?php echo $banner->id; ?>">
			<a href="<?php echo $this->createUrl('banner/update', array('id'=>$banner->id)); ?>"><?php echo $banner->title; ?></a>
			<?php if($banner->type == Banner::BANNER_FLASH) echo "[Flash]"; ?>
			<a class="remove" href="<?php echo $this->createUrl('banner/remove', array('id'=>$banner->id)); ?>">Удалить</a>
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
				$.post('<?php echo $this->createUrl('banner/order'); ?>', order);
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
