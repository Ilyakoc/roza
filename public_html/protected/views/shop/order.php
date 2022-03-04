<?php
/*$orderView = '_order_form';

if (!Yii::app()->user->isGuest) {
	$orderView = '_order_form_new';
}*/

$orderView = '_order_form_new'
?>

<h1>Корзина</h1>

<?php if (Yii::app()->user->hasFlash('order')): ?>
<div class="flash-success">
    <?php echo Yii::app()->user->getFlash('order'); ?>
</div>
<?php else: ?>
    <?php $this->renderPartial('_cart', compact('products')); ?>
    <?php $this->renderPartial($orderView, compact('model')); ?>
<?php endif; ?>
