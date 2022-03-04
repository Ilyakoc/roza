<h1>Корзина</h1>

<?php if (Yii::app()->user->hasFlash('order')): ?>
    <div class="flash-success">
        <?php echo Yii::app()->user->getFlash('order'); ?>
    </div>
<?php else: ?>
<p>Ваша корзина пуста</p>
<?php endif; ?>