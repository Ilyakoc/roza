$(function() {
    var shop_module = $('#shop-cart .module');

    function hoverCartModule() {
        var self = $(shop_module);
        if ($(self).hasClass('open') || $(self).hasClass('empty'))
            return;

        $(self).toggleClass('hover');

        var module_main = $(self).find('.module-main');
        if ($(self).hasClass('hover')) {
            $(module_main).animate({'padding-top': '+=6px'}, 'fast');
        } else {
            $(module_main).animate({'padding-top': '-=6px'}, 'fast');
        }
    }
    $(document).on('hover', shop_module, $.throttle(hoverCartModule, 1000));

    $('.cart-open-link, .module-head, #cart-minimize', $('#shop-cart')).click(function() {
        if (!$('#open-cart').is(':focus') && !$(shop_module).hasClass('empty'))
            $(shop_module).toggleClass('open');
    });

    var buttons = $('body .shop-button');

    $(document).on('mousedown mouseup', buttons, function() {
        $(this).toggleClass('click');
    });
    $(document).on('mouseleave', buttons, function() {
        $(this).removeClass('click');
    });
});

ShopCart = {
    update: function(data) {
        if (!data) {
            return;
        }
        var $summary_count = $('#summary-count');
        var $summary_price = $('#summary-price');
        var $summary_order = $('.b-r-t-3-summary');
        var $product       = $('#cart-product-'+data.id);

        if ($summary_count.length) {
            $summary_count.text(data.summary_count);
            $summary_price.text(data.summary_price);

            if (data.summary_count >= 1) {
              $('.basket-top-mobile-text').text('Ваша корзина');
            } else {
              $('.basket-top-mobile-text').text('В корзине нет товаров');
            }

            if ($product.length) {
                var el = $product.find('.count input');
                if ($(el).val() != data.count)
                    $(el).val(data.count);
                if ($(el).val() == 0) {
                    $product.remove();
                }
            } else
                $('#cart-products').html(data.products);

            if (data.summary_count == 0 && !$summary_order.length) {
                this.close();
            }
        } else {
            $('.summary', $('#shop-cart')).html(data.summary);
            $('#cart-products').html(data.products);
            $('.module', $('#shop-cart')).removeClass('empty');
        }

        if ($summary_order.length) {
            $summary_order.text(data.summary_price);
        }

        if (!$('#cart-products li').length && $summary_order.length) {
            window.location.reload();
        }
    },
    removeFromCart: function(id, self) {
        var t = this, data = {};
        data['count['+id+']'] = 0;

        $.post($(self).attr('href'), data, function(data) {
            $(self).parents('.order-line').remove();
            $('.b-r-t-3-summary').text(data.summary_price);
            t.update(data);
        }, 'json');

        return false;
    },
    clear: function(self) {
        var t = this;
        $.get($(self).attr('href'), function() {
            if ($('#orderTable').length)
                window.location.reload();
            t.close();
        });
        return false;
    },
    close: function() {
        $('#shop-cart .summary').text('Ваша корзина пуста');
        $('#shop-cart .module').addClass('empty').removeClass('open hover');
        $('#shop-cart .module-main').css('padding-top', 0);
    }
};
