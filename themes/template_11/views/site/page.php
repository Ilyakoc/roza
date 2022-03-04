<?php if ($this->isIndex()): ?>
	<div class="block-promise">
	<div class="block-promise-content">
		<div class="block-promise-content-item">
			<img src="/dist/img/free-shipping.png" alt="prom">
			<p>Бесплатная доставка
	за 2 часа</p>
		</div>
		<div class="block-promise-content-item">
			<img src="/dist/img/spring.png" alt="prom">
			<p>Стойкость до
	двух недель</p>
		</div>
		<div class="block-promise-content-item">
			<img src="/dist/img/landline.png" alt="prom">
			<p>Доставка только
	по номеру телефона</p>
		</div>
		<div class="block-promise-content-item">
			<img src="/dist/img/postcard.png" alt="prom">
			<p>Открытка в подарок</p>
		</div>
		<div class="block-promise-content-item">
			<img src="/dist/img/gift.png" alt="prom">
			<p>Подарки к каждому
	празднику</p>
		</div>
	</div>
	</div>
<?php endif ?>

<?php
$criteria = new CDbCriteria();
$criteria->select = 'id';
$criteria->index = 'id';

$products = Product::model()->findAll($criteria);
$productsIds = array_keys($products);

$limit = 20;

if(count((array) $productsIds) > $limit)
{
    shuffle($productsIds);
    $productsIds = array_slice($productsIds, 0, $limit);
}

$criteria = new CDbCriteria();
$criteria->addInCondition('id', $productsIds);

$products = Product::model()->findAll($criteria);

?>


<?php
$products = Product::model()->findAll(['condition' => 'sale = 1']);
$productsnew = array_reverse($products);
?>
 <div class="slider-block">
	<ul class="slider-block-list products">
		<?php foreach($productsnew as $product) {?>
			<?php
				$this->renderPartial('//shop/_products_li', ['product' => $product]);
			?>
		<?php }?>
	</ul>
</div>

<?php if ($this->isIndex()): ?>
	<?php
	$this->widget('widget.Events.Events', ['view' => 'wiki', 'type' => 'wiki']);
	?>

	<?php
	$this->widget('widget.Events.Events', ['view' => 'index']);
	?>
<?php endif; ?>

<?php if (!$this->isIndex()): ?>
	<div class="content">
		<?= $page->text; ?>
	</div>
<?php endif; ?>

<?php $this->renderPartial('//shop/_category_js'); ?>
