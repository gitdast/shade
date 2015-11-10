/**
 * 
 * common scripts of shade.cd
 * Copyright (c) 2012 Dast(http://dastsoft.cz)
 */


getWindowSize = function(){
	var winH, winW;
	if(document.body && document.body.offsetHeight){
		winW = document.body.offsetWidth;
		winH = document.body.offsetHeight;
	}
	if(document.compatMode=='CSS1Compat' && document.documentElement && document.documentElement.offsetHeight){
		winW = document.documentElement.offsetWidth;
		winH = document.documentElement.offsetHeight;
	}
	if(window.innerWidth && window.innerHeight){
		winW = window.innerWidth;
		winH = window.innerHeight;
	}
	return [winW,winH];
}

$(document).ready(function(){
	
    $('<div id="ajax-spinner"></div>').appendTo("body")
		.ajaxStop(function(){
			$(this).hide();
		})
	.hide();

})

