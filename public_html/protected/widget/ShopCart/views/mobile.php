<div class="basket-top basket-top-mobile">
  <?php if($cart->summary_count): ?>
    <span class="basket-top-mobile-text">
      Ваша корзина
    </span>
  <?php else: ?>
    <span class="basket-top-mobile-text">
      В корзине нет товаров
    </span>
  <?php endif ?>
  <a href="/shop/order" class="basket-count-mobile">
    (<span id="summary-count" ><?php echo  $cart->summary_count ?></span>)
  </a>
</div>
