
<?php foreach($sliders as $slider){ ?>
	<h2><?php echo $slider->title . '(' . CHtml::link(count($slider->tovars), array('shop/category','id' =>$slider->id)) . ')';?></h2>
	 <div class="slider-block">
		<ul class="slider-block-list">
			<?php foreach($slider->tovars as $product) {?>
			<li>
				<div class="list-bl-wrapp">
					<div class="list-bl-wrapp-item">
						<div class="list-actions">
							<?php if (!empty($product->size)): ?>
								<div class="t-arrr dib"><?php echo $product->size; ?></div>
							<?php endif; ?>
							<?php if (!empty($product->weight)): ?>
								<div class="t-circle dib"><?php echo $product->weight; ?></div>
							<?php endif; ?>
							<?php if ($product->sale): ?>
								<div class="t-percent dib">%</div>
							<?php endif; ?>
						</div>
						<?php echo CHtml::link('<img src="' . HFile::thumb(strstr($product->mainImg,'?',true),270,240,3600) . '" alt="" />', array('shop/product', 'id'=>$product->id), array('class'=>'slider-hed-name')) ?>
						<?php echo CHtml::link($product->title, array('shop/product', 'id'=>$product->id), array('class'=>'slider-hed-name')) ?>
						<div class="slider-priceje"><span><?php echo $product->price; ?></span> руб.</div>
						<?php if (!$product->notexist): ?>
							<a class="goobasket-btn goorder-button " href="<?php echo $this->createUrl('shop/addtocart', array('id'=>$product->id)) ?>">ЗАКАЗАТЬ</a>
						<?php endif; ?>
					</div>
				</div>
			</li>
			<?php }?>
		</ul>
	</div>
<?php }?>

<div class="content">
	<?= $page->text; ?>
</div>