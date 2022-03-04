
$(document).ready(function () {


    $("#COUNT").mask("9",{placeholder:"_"});
$('input[placeholder], textarea[placeholder]').placeholder();

    //change big photo catalog-item
    
    $('.sm-ph').on('click', function () {
        var SRC = $(this).attr('src');
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
        $('.bigphoto').attr('src', SRC);
        $('.bigphoto').parent().attr('href', SRC);
    })
    
    //end change


    //add description order

    $('.b-card-add').on('click', function(){
        if($(this).hasClass('active')){
            $(this).removeClass('active');
            $(this).next().hide();
        }
        else{
        $(this).addClass('active');
            $(this).next().show();
        }
    })

    //tovar change price scrpit


    //actions minus + plus

    $('.inp-minus').click(function (e) {

            var $input = $(this).parent().find('input');
            var count = parseInt($input.val()) - 1;
            count = count < 1 ? 1 : count;
            $input.val(count);
            $input.change();
            
            var target = $(e.target);
            var cc = $(target).parents('.inpwrapp').find('.cc').val();
            $.post('/shop/updatecart', $(target).parents('.inpwrapp').find('.cc'), function(data) {
                ShopCart.update(data);
            }, 'json');
            
            return false;
    });
    $('.inp-plus').click(function (e) {
            var $input = $(this).parent().find('input');
            $input.val(parseInt($input.val()) + 1);
            $input.change();
         
            var target = $(e.target);
            var cc = $(target).parents('.inpwrapp').find('.cc').val();
            $.post('/shop/updatecart', $(target).parents('.inpwrapp').find('.cc'), function(data) {
                ShopCart.update(data);
            }, 'json');
            
            return false;
    });


    //end act minus+PLUS

    $('.for-action-inp').on('change',function(){
        var PR = $(this).parent().find('.tt-dlina').html();
        $('.t-dlina span').html(PR);
        $('.mysecelt-content').toggle();

    })
    $('.for-action-inp-basket').on('change',function(){

        $(this).parent().parent().toggle();

    })



    $('.myselect').on('click',function(){

        $(this).find('.mysecelt-content').toggle();
    })

    //tovar change price end

    // $('.slider-topj').bxSlider({
    //     infiniteLoop: true,
    //     auto: true,
    //     pager: true,
    //     controls: false,
    //     touchEnabled: false,
    //     pause: 10000

    // });

    $('.slider-topj').slick({
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 5000,
        arrows: false,
        dots: true,
        // adaptiveHeight: true
    });


    // $('.slider-block-list').slick({
    //     // slidesToScroll: 1,
    //     autoplay: false,
    //     autoplaySpeed: 5000,
    //     slidesToShow: 4,
    //     slidesToScroll: 3,
    //     responsive: [
    //         {
    //           breakpoint: 992,
    //           settings: {
    //             slidesToShow: 2,
    //             slidesToScroll: 2,
    //           }
    //         },
    //         {
    //           breakpoint: 768,
    //           settings: {
    //             slidesToShow: 3,
    //             slidesToScroll: 3,
    //           }
    //         },
    //         {
    //           breakpoint: 600,
    //           settings: {
    //             slidesToShow: 2,
    //             slidesToScroll: 2,
    //           }
    //         },
    //         {
    //           breakpoint: 400,
    //           settings: {
    //             slidesToShow: 2,
    //             slidesToScroll: 1,
    //           }
    //         }
    //       ]
    // });

    // $('.slider-block-list').bxSlider({
    //     auto: false,
    //     pager: false,
    //     controls: true,
    //     touchEnabled: false,
    //     minSlides: 3,
    //     maxSlides: 3,
    //     slideWidth: 272,
    //     slideMargin: 15,
    //     moveSlides: 1


    // });

    $("a.rel-photos").fancybox({
        openEffect  : 'elastic',
        closeEffect : 'elastic',
        helpers: {
            overlay: {
                locked : false,
            }
        }
    });

    //скрол плавный

    $('.for-event').on('click',function(){
        var el = $(this).attr('data-href');
        $('html, body').animate({
            scrollTop: $(el).offset().top}, 500);
        return false;
    });


});

