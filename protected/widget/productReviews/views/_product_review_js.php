<?php
/**
 * File: _review_js.php
 * User: Mobyman
 * Date: 10.04.13
 * Time: 12:24
 */
?>
<script type="text/javascript">
    $(function() {
        $(':radio.star').rating();
        $('#add-review a').click(function() {
            $.fancybox({
                'href': '#review-form-div',
                'scrolling': 'no',
                'titleShow': false,
                'onComplete': function(a, b, c) {
                    $('#fancybox-wrap').addClass('formBox');
                }
            });
        });

        $('.reviews').on('click', '.cutlink', function(){
            var p = $(this).parent();
            if(p.find('.cut').is(":visible")) {
                p.find('.cutlink').text('Развернуть всё');
            } else {
                p.find('.cutlink').text('Свернуть всё');
            }
            p.find('.cut').toggleClass('hide');
        });
    });

    function submitForm(form, hasError) {
        if (!hasError) {
            $.post($(form).attr('action'), $(form).serialize(), function(data) {
                if (data == 'ok')
                    $('#review-form-div').html('<h2>Ваш отзыв отправлен</h2>');
                else
                    $('#review-form-div').html('<h2>При отправке отзыва возникла ошибка</h2>');
            });
        }
    }
</script>
