<?php if ($cart->summary_count): ?>
    В <?php echo CHtml::link('корзине', array('shop/order'), array('id'=>'open-cart')); ?>
    <strong id="summary-count"><?php echo $cart->summary_count; ?></strong> товаров
    на <strong id="summary-price"><?php echo $cart->summary_price; ?></strong> руб.
<?php else : ?>
    <a href="#" class="pull-right basket-top">Ваша корзина (<span>0</span>)</a>
<?php endif; ?>
