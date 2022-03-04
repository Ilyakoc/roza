<?php $this->pageTitle = 'Магазин - '. $this->appName; ?>

<div class="left">
    <h1>Магазин</h1>
</div>
<div class="right">
    <?php echo CHtml::link('Очистить кеш картинок', array('shop/clearImageCache')); ?>
    <a class="shop-settings" href="<?php echo $this->createUrl('shopSettings/index'); ?>">Настройки</a>
</div>
<div class="clr"></div>

<?php $this->renderPartial('_categories', compact('categories')); ?>
<?php $this->renderPartial('_products', compact('products')); ?>
