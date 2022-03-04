var AdminShop = {
    removeMainImg: function(id, t) {
        jQuery.post($(t).attr('href'), {product_id: id}, function(result) {
            if (result == '1') {
                var p = $(t).parents('.row');
                $(p).find(':file').removeClass('hidden');
                $(p).find('.mainImg').remove();
            } else {

            }
        });
        return false;
    }
};
