<?$this->widget('widget.breadcrumbs.BreadcrumbsShopWidget', compact('category'))?>
<h1><?php echo $category->getMetaH1(); ?></h1>
<?
$subcategories=$category->children()->findAll(['select'=>'id,lft,root,rgt,level,title','order'=>'lft']);
?>
<? /*
<div id="category-list-module">
    <?php $this->renderPartial('/shop/_categories', array('categories'=>$categories, 'category_id'=>$category->id)); ?>
</div>
*/ ?>
<? /* ?>
<div class="ns-category-list">
	<?php
		$category2 = [
			[
				'name'=> 'Розы',
				'img'=> 'ns-sb-1.jpg',
				'url'=> ''
			],
			[
				'name'=> 'Хризантемы',
				'img'=> 'ns-sb-2.jpg',
				'url'=> ''
			],
			[
				'name'=> 'Герберы',
				'img'=> 'ns-sb-3.jpg',
				'url'=> ''
			],
			[
				'name'=> 'ТЮЛЬПАНЫ',
				'img'=> 'ns-sb-4.jpg',
				'url'=> ''
			],
			[
				'name'=> 'ЛИЛИИ',
				'img'=> 'ns-sb-5.jpg',
				'url'=> ''
			],
			[
				'name'=> 'ОРХИДЕИ',
				'img'=> 'ns-sb-6.jpg',
				'url'=> ''
			],
			[
				'name'=> 'Тематические букеты',
				'img'=> 'ns-sb-7.jpg',
				'url'=> ''
			],
			[
				'name'=> 'Корзины с цветами',
				'img'=> 'ns-sb-8.jpg',
				'url'=> ''
			],
			[
				'name'=> 'Розы в шляпной коробке и т.д.',
				'img'=> 'ns-sb-9.jpg',
				'url'=> ''
			],
		];

		foreach ($category2 as $key => $value) {

			?>

				<div class="ns-category-item-wrap">
					<div class="ns-category-item">
						<span class="ns-category-item-img">
							<img src="<?=$this->template?>/img/<?echo $value['img'];?>" alt="" />
						</span>
						<span class="ns-category-item-name">
							<span><?echo $value['name'];?></span>
						</span>
					</div>
				</div>
			<?
		}
	?>
</div>
<? /**/ ?>

<? if(!empty($subcategories)): ?>
<div class="ns-roses-list subcategories">
	<? foreach ($subcategories as $subcategory) { ?>
		<div class="ns-roses-item-wrap">
			<div class="ns-roses-item">
				<p><?= CHtml::link($subcategory->title, ['/shop/category', 'id'=>$subcategory->id], ['title'=>$subcategory->title]); ?></p>
			</div>
		</div>
	<? } ?>
</div>
<? endif; ?>

<?php $this->renderPartial('_category_js'); ?>
<div class="clear"></div>
<?php if ($category->under_description): ?>
	<div id="category-description" class="category-description">
		<?php echo $category->under_description; ?>
	</div>
<?php endif; ?>

<?php if ($category->description): ?>
	<div id="category-description" class="category-description">
		<?php echo $category->description; ?>
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
