<?//$this->widget('widget.breadcrumbs.BreadcrumbsShopWidget')?>
<? /* <h1>Каталог</h1> */ ?>

<div id="category-list-module">
    <?php // $this->renderPartial('/shop/_categories', compact('categories')); ?>
</div>

<div id="product-list-module">
    <?php $this->renderPartial('/shop/_products', compact('products')); ?>
</div>

<?php $this->renderPartial('/shop/_category_js'); ?>
