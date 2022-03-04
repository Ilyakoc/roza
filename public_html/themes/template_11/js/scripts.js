var shopItemCount = function(){
	var k = $(".products li").length;
	var n = (k%4);
	if (n > 0) {

		for (var i = 0; i < 4-n+1; i++) {
			$(".product-list ul.products").append("<li></li>");
		}
	}
}

$(document).ready(function(){
	$('.slick_slider-for').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: false,
		fade: true,
		asNavFor: '.slick_slider-nav',
		responsive: [
		    {
		      breakpoint: 450,
		      settings: {
		        arrows: true,
		      }
		    }]
	});


	$('.slick_slider-nav').slick({
		slidesToShow: 4,
		slidesToScroll: 1,
		asNavFor: '.slick_slider-for',
		arrows: true,
		focusOnSelect: true,
		vertical: true,
		infinite: true,
		speed: 500,
		draggable:true,
		centerMode:false
	});

	if($(".product-list ul.products").length) {
		shopItemCount();
	}

	$(".control").click(function(){
		if ($(this).hasClass("next")) {
			$('.slideshow').cycle('next');

		} else{
			$('.slideshow').cycle('prev');
		}
	});


	$(".shop-menu-tuggle-batton").click(function(){
		$(this).toggleClass('active');
		$(".shop-menu-collapsed").stop(true, true).slideToggle();
	});

	

	$(".filter-menu-tuggle-batton").click(function(){
		$(".filter-list").slideToggle('fast');
	});

	$(".shop-menu > li > span").click(function(e){
		if($(window).width()<768) {
			window.location.href=$(e.target).closest("span").data("url");
		}
	});

});





$(document)

    //Кастомный селект (section)
    .on("click", ".section-header", function () {
        var sect = $(this).closest(".section");
        sect.toggleClass("section__active");

        if (sect.hasClass("section__active")) {
            $(".section").removeClass("section__active");
            sect.addClass("section__active");
        }
    })
    .on("click", ".section-body li", function () {
        var li_html = $(this).html();
        var li_data = $(this).data("select");
        var wrap = $(this).closest(".section");
        wrap.find(".section-title__name").html(li_html);
        wrap.find(".section-input").val(li_data).trigger('change');
        wrap.removeClass("section__active")
    })



.ready(function() {


    //Загрузка select
    $('.section .section-input').each(function () {
        if (!!$(this).val().trim()) {
            var $thisVal = $(this).val();
            var $data = $(this).closest('.section').find('.section-body li[data-select=' + $thisVal + ']').html();
            $(this).closest('.section').find('.section-title__name').html($data);
        } else {
            var wrap = $(this).closest('.section');
            var title = wrap.find('.section-title').data("title");
            wrap.find('.section-title__name').html(title);
        }
    })

    //Скрытие элементов при клике мимо
    $(document).on("click touchend", function (e) {
        if ($(e.target).closest(".section").length == 0) $('.section').removeClass('section__active');
    });

    $('#Order_delivery .dib').on('click', function(){
    	$('#Order_delivery .dib').each(function() {
    		$(this).removeClass('act');
    	});

    	if($(this).find('.inline').checked = true) {
    		$(this).addClass('act');
    	}
    });

    $('.opl .dib').on('click', function(){
    	$('.opl .dib').each(function() {
    		$(this).removeClass('dibH');
    	});

    	if($(this).find('.inline').checked = true) {
    		$(this).addClass('dibH');
    	}
    });
});
