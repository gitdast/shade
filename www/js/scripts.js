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

detailBoxInit = function(){
	$("#detailBox").css('width', 'auto'); //pri resize okna nejdrive srazime na 'auto', aby doc.width nebyla vyssi jen prave kvuli sobe samemu
	$("#detailBox").css('width', $(document).width());
	$("#detailBox #img_cont").css('left', Math.max(0,getWindowSize()[0]/2 - $("#detailBox #img_cont img").width()/2)+"px");
	$("#detailBox #img_cont").css('top', Math.max(0,getWindowSize()[1]/2 - $("#detailBox #img_cont img").height()/2)+"px");
	//detailBox ma vychozi vlastnost opacity = 0, kvuli probliknuti;
	$("#detailBox").css('height',200).css('top',-500).css('opacity',0.25).css('display','block').animate({ opacity: 1, height: $(document).height(), top: 0}, 1200);
}

detailBoxBindEvents = function(){
	$("#detailBox a").on({ 
		click: function(){
			$("#detailBox").stop(true,true).animate({ opacity: 0.25, height: 'toggle', top: -500}, 1200, function(){$("#snippet--detailBox").html("")});
		}
	});
}


$(document).ready(function(){
	
	$(function(){
	    $('<div id="ajax-spinner"></div>').appendTo("body").ajaxStop(function () {
	        $(this).hide().css({
    	        position: "fixed",
        	    left: "50%",
            	top: "50%"
	        });
    	}).hide();
	});
	
	$(window).resize(function(){
		if($("#detailBox").length > 0){
			detailBoxInit();
		}
	});
	
})

