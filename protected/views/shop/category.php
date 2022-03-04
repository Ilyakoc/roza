<?$this->widget('widget.breadcrumbs.BreadcrumbsShopWidget', compact('category'))?>
<h1><?php echo $category->getMetaH1(); ?></h1>
<?php if ($category->description): ?>
	<div id="category-description" class="category-description">
		<?php echo $category->description; ?>
	</div>
<?php endif; ?>
<?php if (!isset(Yii::app()->params['hide_shop_categories']) && ($category->id !== 0)): ?>
<div id="category-list-module">
    <?php $this->renderPartial('/shop/_categories', array('categories'=>$categories, 'category_id'=>$category->id)); ?>
</div>
<?php endif; ?>


<div id="product-list-module">
    <?php $this->renderPartial('_products', compact('products', 'pages')); ?>
</div>

<?php $this->renderPartial('_category_js'); ?>
<div class="clear"></div>
<?php if ($category->under_description): ?>
	<div id="category-description" class="category-description">
		<?php echo $category->under_description; ?>
	</div>
<?php endif; ?>

<?php
$image = CImage::model()->find(array('condition'=>"model='category' AND item_id={$category->id}", 'order'=>'ordering'));

if($image && is_file(Yii::getPathOfAlias('webroot.images.category') . DS . $image->filename)):
	CmsHtml::fancybox();
?>
	<a id="category_image" href="<?php echo "/images/category/{$image->filename}"; ?>" style="display:none"><img /></a>
	<script type="text/javascript">
	jQuery(function() {
		$("#category_image").fancybox({
			hideOnContentClick: true,
			onStart: function() {
				setTimeout(function() { $.fancybox.close(); }, 3000);
			}
		});
	//$("#category_image img").click();
	});
	</script>
<?php endif; ?>