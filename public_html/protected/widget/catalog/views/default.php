<?php if (count($products)): ?>
	<h2><?php echo 'Дополнительно к товару' . '(' . CHtml::link(count($products), array('shop/category','id' =>32)) . ')';?></h2>
	 <div class="slider-block">
		<ul class="slider-block-list">
			<?php foreach($products as $product) {?>
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
							<noindex><a rel="nofollow" class="goobasket-btn goorder-button " href="<?php echo Yii::app()->createUrl('shop/addtocart', array('id'=>$product->id)) ?>">ЗАКАЗАТЬ</a></noindex>
						<?php endif; ?>
					</div>
				</div>
			</li>
			<?php }?>
		</ul>
	</div>
<?php else: ?>
<p>Нет товаров</p>
<?php endif;?>
