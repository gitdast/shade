/**
 * 
 * admin scripts of sections
 * Copyright (c) 2012 Dast(http://dastsoft.cz)
 */

switchIcon = function(){
	if($("#help #hcontent").is(':hidden')){
		$("#help #hcontrol").css('background-image','url("/css/images/ico_down.png")');
	}else{
		$("#help #hcontrol").css('background-image','url("/css/images/ico_up.png")');
	}
}

$(document).ready(function(){
	tinyMCE.init({
		mode: "specific_textareas",
		editor_selector: "mceEditor",
		convert_urls : false,
		theme : "advanced",
        theme_advanced_buttons3_add_before : "tablecontrols,separator"
	});
	
	$("#help #hcontrol").on({
		mouseenter: function(){
			$(this).css('background-position','right -17px');
		},
		mouseleave: function(){
			$(this).css('background-position','right 0px');
		},
		click: function(){
			$("#help #hcontent").toggle('fast', switchIcon);
		}
	});

})

