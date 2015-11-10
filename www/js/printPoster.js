/**
 * scripts of PrintPoster component; shade.cd
 * Copyright (c) 2012 Dast(http://dastsoft.cz)
 */

$(document).ready(function(){
	$(".printPoster .itemcont img").on({
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
	$('.youtubeLink').magnificPopup({
		disableOn: 700,
		type: 'iframe',
		mainClass: 'mfp-fade',
		removalDelay: 160,
		preloader: false,

		fixedContentPos: false
		/*iframe: {patterns: {youtube: {id: 'be/'}}}*/
	});
})

