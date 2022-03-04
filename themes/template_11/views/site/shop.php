<div class="left-side">
    <h1>Магазин</h1>
</div>

<div class="right-side content">
    <div id="category-list-module">
        <?php $this->renderPartial('/shop/_categories', compact('categories')); ?>
    </div>

    <div id="product-list-module">
        <?php $this->renderPartial('/shop/_products', compact('products')); ?>
    </div>

    <?php $this->renderPartial('/shop/_category_js'); ?>
</div>