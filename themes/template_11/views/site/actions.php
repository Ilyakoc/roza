<?$this->widget('widget.breadcrumbs.BreadcrumbsArrayWidget', ['breadcrumbs' => [array('url'=>false, 'title'=>'Акции')]])?>

<h1>Акции</h1>

<div id="product-list-module">
    <?php $this->renderPartial('//shop/_products', compact('products')); ?>
</div>
