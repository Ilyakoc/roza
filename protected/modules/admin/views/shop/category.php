<?php $this->pageTitle = 'Магазин / Категория '. $model->title . ' - '. $this->appName; ?>
<h1>
    <?php echo CHtml::link('Магазин', array('shop/index')); ?>
    <?php if (count($bredcrumbs)) echo '&rarr; '. implode(' &rarr; ', $bredcrumbs); ?> &rarr;
    <?php echo $model->title; ?>
</h1>

<?php $this->renderPartial('_categories', compact('categories', 'model')); ?>

<?php $this->renderPartial('_products', array('products'=>$products)); ?>
