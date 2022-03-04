<li <?php if(isset($last_in_row) && $last_in_row) echo ' class="last"'; ?>>
	<div class="list-bl-wrapp">
		<div class="<?= $product->new ? 'list-bl-wrapp-item new-product' : 'list-bl-wrapp-item' ?>">
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
			<?php echo CHtml::link('<img src="' . HFile::thumb(strstr($product->fullImg,'?',true),270,240,3600) . '" alt="' . (trim(str_replace('"', '', $product->title)) . ' фото') . '" />', array('shop/product', 'id'=>$product->id), array('class'=>'slider-hed-image')) ?>
			<div class="prod__bot clearfix">
			<div class="title__m">
				<?php echo CHtml::link(str_replace(' "', '<br/>"', $product->title), array('shop/product', 'id'=>$product->id), array('class'=>'slider-hed-name slider__n')) ?>
			</div>

			<div class="card_features_description">
				<div class="action_block">
					<?php if ($product->height > 0): ?>
						<div class="action_block__elem">
							<p><img src="/themes/template_11/img/action_arrow.png" alt=""/><?= $product->height ?></p>
						</div>
					<?php endif ?>
					<?php if ($product->diameter > 0): ?>
						<div class="action_block__elem">
							<p><img src="/themes/template_11/img/action_circle.png" alt=""/><?= $product->diameter ?></p>
						</div>
					<?php endif ?>
					<?php if ($product->sale_value > 0): ?>
						<div class="action_block__elem action_block__elem__active">
							<p>-<span><?= $product->sale_value ?></span>%</p>
						</div>
					<?php endif ?>
				</div>
			</div>
		<div class="slid__bots">
			<div class="slider-priceje_bottom">
				<div class="slider-priceje_left">
					<div class="slider-priceje">
						<?php if ($product->old_price > 0): ?>
							<span class="slider-priceje_old"><?php echo $product->old_price; ?> руб.</span>
						<?php endif ?>

						<span><?php echo $product->price; ?> руб.</span>
					</div>
				</div>
				<div class="slider-priceje_right">
					<?php if (!$product->notexist): ?>
						<!--noindex--><a rel="nofollow" class="goobasket-btn goorder-button " href="javascript:;" data-href="<?php echo $this->createUrl('shop/addtocart', array('id'=>$product->id)) ?>"><span>Купить</span><img src="/dist/img/shopping-cart-of-checkered-design.png" alt=""></a><!--/noindex-->
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</li>
