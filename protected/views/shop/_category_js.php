<?php

CmsHtml::js('/js/jquery-impromptu.3.2.min.js');
CmsHtml::js('/js/jquery.debounce-1.0.5.js');
CmsHtml::js('/js/shop.js');

?>
<script type="text/javascript">
    $(function() {
        $.prompt.close = function() {
            var $t = $('#'+ $.prompt.currentPrefix);
            var speed = $.prompt.defaults.promptspeed;
            $t.animate({top: 0}, speed, 'swing', function() {
                $('#'+ $.prompt.currentPrefix + 'box').css('display', 0).remove();
            });
        };

        $('.js-goobasket-btn, .goobasket-btn').live('click', function(e) {
            e.preventDefault();
            var count;
            if($('input').is('#COUNT'))
				 count = $('input#COUNT').val();
			else
				count = 1;

            var data = $(this).data("cart-attributes");

            var _data = {};

            if (data) {
                for (var key in data) {
                    if($(data[key]).length) {
                        var value = $(data[key]).val();
                        
                        _data[key] = value;
                    }
                }
            }

            data = { data: _data };

            $.post(($(this).data('href') ? $(this).data('href') : $(this).attr('href'))+'?count='+count, data, function(data){
				ga('send', 'event', 'cart_pick', 'click');
                $.prompt('Товар добавлен в корзину!', {buttons: [], opacity: 0.8, top: '30%', timeout: 1500, persistent: false});
                ShopCart.update(data);
				window.location.href = "/shop/order"
            }, 'json');
        });
    });
</script>