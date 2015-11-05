/**
 * scripts of Packaging component; shade.cd
 * Copyright (c) 2012 Dast(http://dastsoft.cz)
 */

$(document).ready(function(){
	$(".packaging .packcont img").on({
		mouseenter: function(){
			$(this).removeClass('black').addClass('color');
		},
		mouseleave: function(){
			$(this).removeClass('color').addClass('black');
		},
		click: function(e){
			$.get("?do=ShowDetail&showid="+this.id);
		}
	});
	
})

