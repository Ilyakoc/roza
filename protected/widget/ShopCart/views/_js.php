<script type="text/javascript">
    $(function() {
        function updateCount(e) {
            var target = $(e.target);
            if ($(target).val() == 0) {
                var ok = confirm('Вы хотите удалить товар из корзины?');
                if (!ok) return;
            }
            $.post('<?php echo Yii::app()->createUrl('shop/updatecart'); ?>', target, function(data) {
                ShopCart.update(data);
            }, 'json');
        }
        $('.count input', $('#shop-cart, #orderTable')).live('keyup', $.debounce(updateCount, 800));
    });
</script>
