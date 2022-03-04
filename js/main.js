$(function() {

  $('.header-mobile-humburger').click(function() {
    $(this).toggleClass('active');
    $('.header-mobile-dropdown').stop(true, true).slideToggle();
  });

  $('.ullvl2').closest('li').addClass('hasSub').find('>a').append('<span class="menu-arrow"><i class="fa fa-angle-right" aria-hidden="true"></i><span>');

  $(".menu-arrow").click(function() {
		$(this).toggleClass('active');
    $(this).closest('li').toggleClass('active');
		$(this).closest('li').find('>ul').stop(true, true).slideToggle();
		return false;
	});

  offset = $('header').height();

  // if ($(document).scrollTop() > 0) {
  //   $('.header-mobile').addClass('fixed');
  //   $('body').css('padding-top', 71);
  // } else {
  //   $('.header-mobile').removeClass('fixed');
  //   $('body').removeAttr('style');
  // }

  if ($(window).scrollTop() > offset) {
    $('.shop-menu-wrap').addClass('fixed');
    $('body').css('padding-top', 46);
  } else {
    $('.shop-menu-wrap').removeClass('fixed');
    $('body').removeAttr('style');
  }

  $(window).scroll(function() {
    // if ($(document).scrollTop() > 0) {
    //   $('.header-mobile').addClass('fixed');
    //   $('body').css('padding-top', 71);
    // } else {
    //   $('.header-mobile').removeClass('fixed');
    //   $('body').removeAttr('style');
    // }

    if ($(window).scrollTop() > offset) {
      $('.shop-menu-wrap').addClass('fixed');
      $('body').css('padding-top', 46);
    } else {
      $('.shop-menu-wrap').removeClass('fixed');
      $('body').removeAttr('style');
    }
  });

  $(window).resize(function() {
    offset = $('header').height();

    $(window).scroll(function() {
      // if ($(document).scrollTop() > 0) {
      //   $('.header-mobile').addClass('fixed');
      //   $('body').css('padding-top', 71);
      // } else {
      //   $('.header-mobile').removeClass('fixed');
      //   $('body').removeAttr('style');
      // }

      if ($(window).scrollTop() > offset) {
        $('.shop-menu-wrap').addClass('fixed');
        $('body').css('padding-top', 46);
      } else {
        $('.shop-menu-wrap').removeClass('fixed');
        $('body').removeAttr('style');
      }
    });
  });



    var fancyboxImages = $('a.image-full', $('.content-sect'));

    if (fancyboxImages.length) {
        $(fancyboxImages).fancybox({
            overlayColor: '#333',
            overlayOpacity: 0.8,
            titlePosition : 'over'
        });
    }
});
