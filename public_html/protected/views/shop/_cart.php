<div class="title__wrap">
    <div class="title__cart">
        <div class="cart__name">Наименование</div>
        <div class="cart__count">Кол-во</div>
        <div class="cart__price">Цена, руб.</div>
      	<div class="cart__sum">Сумма, руб.</div>
    </div>
</div>
<?php foreach($products as $hash => $p): ?>
<div class="separator"></div>
<div class="order-line clearfix">
	<div class="order-remove"><a href="<?php echo $this->createUrl('shop/updateCart'); ?>" onclick="return ShopCart.removeFromCart('<?php echo $hash; ?>', this);">х</a></div>

	<div class="basket-photo">
		<img src="<?php echo $p->obj->fullImg; ?>" width="118" height="118" alt="0">
	</div>
    <div class="basket-params-wrap">
        <div class="basket-name">
            <?php echo $p->title ?><br>
            <?php if (!empty($p->obj->code)): ?>
                <span>Артикул: <span><?php echo $p->obj->code; ?></span></span>
            <?php endif; ?>
            <?php if (!empty($p->obj->offer_title)): ?>
                <span>Тип букета: <span><?php echo $p->obj->offer_title; ?></span></span>
            <?php endif; ?>
        </div>
        <div class="basket-count">
            <div class="inpwrapp">
                <div class="inp-plus">+</div>
                <div class="inp-minus">-</div>
                <input class="cc" type="text" id="COUNT"  name="count[<?php echo $hash ?>]" value="<?php echo $p->count; ?>" >
            </div>
        </div>
        <div class="basket-price">
          <p>Цена: </p><span><?php echo $p->order_price; ?></span><p> руб.</p>
        </div>
      	<div class="basket-sum">
            <p>Сумма: </p><span><?php echo $p->order_price* $p->count; ?></span><p> руб.</p>
        </div>
    </div>
</div>
<?php endforeach; ?>


<script type="text/javascript">
    /*
    $(function() {
        function updateCount(e) {
            var target = $(e.target);
            if ($(target).val() == 0) {
                var ok = confirm('Вы хотите удалить товар из корзины?');
                if (!ok) return;
            }
            $.post('/shop/updatecart', target, function(data) {
                ShopCart.update(data);
            }, 'json');
        }
        $('.count input', $('#shop-cart, #orderTable')).live('keyup', $.debounce(updateCount, 800));
    });*/

</script>


<!--<table id="orderTable" class="orderTable">
    <thead>
    <tr>
        <td width="1%"></td>
        <td>Название</td>
        <td width="15%">Кол-во</td>
        <td width="15%">Цена за шт.</td>
        <td width="1%"></td>
    </tr>
    </thead>
    <tbody>
    <?php foreach($products as $p): ?>
    <tr>
        <td class="img">
            <img src="<?php echo $p->obj->tmbImg; ?>" alt="" />
        </td>
        <td>
            <?php echo CHtml::link($p->title, array('shop/product', 'id'=>$p->id)) ?>
            <?php if (!empty($p->obj->code)): ?>
            <p><small>Артикул: <strong><?php echo $p->obj->code; ?></strong></small></p>
            <?php endif; ?>
        </td>
        <td class="count"><input type="text" name="count[<?php echo $p->id ?>]" value="<?php echo $p->count; ?>" size="7" /></td>
        <td><?php echo $p->order_price; ?> руб</td>
        <td>
            <a href="<?php echo $this->createUrl('shop/updateCart'); ?>" onclick="return ShopCart.removeFromCart(<?php echo $p->id; ?>, this);">Удалить</a>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="3" style="text-align: right; font-weight: bold;">Итого:</td>
        <td colspan="2"><span id="order-summary-price"><?php echo CmsCart::getInstance()->priceAll(); ?></span> руб</td>
    </tr>
    </tfoot>
</table>

-->
