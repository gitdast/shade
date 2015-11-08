/**
 * scripts of Advertising component; shade.cd
 * Copyright (c) 2012 Dast(http://dastsoft.cz)
 */

$(document).ready(function(){
	$(".advertising .addcont img").on({
		mouseenter: function(){
			$(this).removeClass('black').addClass('color');
		},
		mouseleave: function(){
			$(this).removeClass('color').addClass('black');
		}
		/*,
		click: function(){
			$.get("?do=ShowDetail&showid="+this.id);
		}*/
	});
	
	$('.lightbox').magnificPopup({type:'image', gallery:{ enabled:true }});
})

