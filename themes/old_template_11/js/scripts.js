$(document).ready(function(){

	$(".control").click(function(){
		if ($(this).hasClass("next")) {
			$('.slideshow').cycle('next');
			
		} else{
			$('.slideshow').cycle('prev');
		};	
	})

});