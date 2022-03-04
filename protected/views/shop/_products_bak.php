<?php
/* @var ShopController $this */
/* @var array $products */
$isProductsOnly=isset($isProductsOnly) && ($isProductsOnly===true);
?>
<?php if (count($products)): ?>
<? if(!$isProductsOnly): ?>
<div class="product-list">
	<ul class="products cart_sale">
<? endif; // !$isProductsOnly ?>
    <?php $i=1; foreach((array)$products as $id=>$product): ?>
		<?php $last_in_row = ($i++) % 4 == 0 ? true : false; ?>

			<li <?php if($last_in_row) echo ' class="last"'; ?>>
				<div class="list-bl-wrapp">
					<div class="cart-sale-item">
						<div class="cart_sale-atributs">
							<?php if ($product->height > 0): ?>
								<div class="frist-atr">
									<p><?= $product->height ?></p>
									<img src="/dist/img/up-and-down-arrow.png" alt="" class="atr-arrows">
								</div>
							<?php endif ?>
							<?php if ($product->sale_value > 0): ?>
								<div class="second-atr">
									<p>-<?= $product->sale_value ?>%</p>
								</div>
							<?php elseif ($product->diameter > 0): ?>
							<div class="frist-atr">
								<p><?= $product->diameter ?></p>
								<img src="/themes/template_11/img/action_circle.png" alt="" class="atr-arrows">
							</div>
							<?php endif ?>
						</div>
						<?php echo CHtml::link('<img src="' . HFile::thumb(strstr($product->fullImg,'?',true),173,157,3600) . '" alt="' . (trim(str_replace('"', '', $product->title)) . ' фото') . '" />', array('shop/product', 'id'=>$product->id), array('class'=>'')) ?>
						<?php echo CHtml::link(str_replace(' "', '<br/>"', $product->title), array('shop/product', 'id'=>$product->id), array('class'=>'cart_sale__title')) ?>
						<div class="cart_sale--price <?= $product->old_price > 0 ? 'cart_sale--price_2' : 'cart_sale--price_1' ?>">
							<?php if ($product->old_price > 0): ?>
							<p class="old-price"><?php echo $product->old_price; ?></p>
							<?php endif; ?>
							<p><?php echo $product->price; ?></p>
						</div>
						<?php if (!$product->notexist): ?>
							<noindex><a rel="nofollow" class="to-cart js-goobasket-btn" href="javascript:;" data-href="<?php echo $this->createUrl('shop/addtocart', array('id'=>$product->id)) ?>"><span>В КОРЗИНУ<i><img src="/dist/img/shopping-cart-of-checkered-design.png" alt=""></i></span></a></noindex>
						<?php endif; ?>
					</div>
				</div>
			</li>
    <?php endforeach; ?>
    
<? if(!$isProductsOnly): ?>    

    </ul>
    <div class="clr"></div>
</div>

<?php if (isset($pages) && ($pages->pageCount>1)): ?>
    <?php /* $this->widget('CLinkPager', array(
        'header'=>'Страницы: ',
        'pages'=>$pages,
        'nextPageLabel'=>'&gt;',
        'prevPageLabel'=>'&lt;',
        'cssFile'=>false,
        'htmlOptions'=>array('class'=>'news-pager')
    )); /**/ ?>
    <div class="btn__more-products"><span>Показать еще</span></div>
    <? 
Yii::app()->clientScript->registerScript('btn__more-products', 
    'window.productListPageNumber=2;$(document).on("click", ".btn__more-products span", function(e){
    	$.post(window.location.href, {listpage:window.productListPageNumber}, function(html){
    		if(window.productListPageNumber <= '.$pages->pageCount.') $(".product-list ul.products").append($.parseHTML(html));
    		if(window.productListPageNumber >= '.$pages->pageCount.') $(".btn__more-products").hide();
    		window.productListPageNumber++;
    	});
    });', 
CClientScript::POS_READY); ?>
<?php endif; ?>

<? endif; // !$isProductsOnly ?>

<?php else: ?><? if(!$isProductsOnly): ?> 
<p>Нет товаров</p>
<? endif; // !$isProductsOnly ?><?php endif;?>