/**
 * scripts of Identities component; shade.cd
 * Copyright (c) 2012 Dast(http://dastsoft.cz)
 */

$(document).ready(function(){
	$(".identities .logocont img").on({
		mouseenter: function(){
			$(this).addClass($(this).attr('rel'));
		},
		mouseleave: function(){
			$(this).removeClass($(this).attr('rel'));
		}
		/*
		,
		click: function(e){
			$.get("?do=ShowDetail&showid="+this.id);
		}*/
	});
	
	$('.lightbox').magnificPopup({type:'image', gallery:{ enabled:true }});
	
})

