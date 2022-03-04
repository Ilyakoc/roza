<?php
/* @var ShopController $this */
/* @var array $products */
$isProductsOnly=isset($isProductsOnly) && ($isProductsOnly===true);
?>
<?php if (count($products)): ?>
<? if(!$isProductsOnly): ?>
<div class="product-list">
	<ul class="products">
<? endif; // !$isProductsOnly ?>
    <?php $i=1; foreach((array)$products as $id=>$product): ?>
		<?php $last_in_row = ($i++) % 4 == 0 ? true : false; ?>
		<?php
		$this->renderPartial('//shop/_products_li', ['product' => $product, 'last_in_row' => $last_in_row]);
		?>
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