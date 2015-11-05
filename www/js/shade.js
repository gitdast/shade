/**
 * 
 * front scripts of shade.cd
 * Copyright (c) 2012 Dast(http://dastsoft.cz)
 */


flashMenuResponse = function(){
	$.each(	$("#menucont #shademenu a.menuItem") ,function(){
		//$(this).css('background-position','0px 0px');
		$(this).addClass('pasive');
	});
	$("#menucont #shademenu span.menuItem").addClass('active');
	
	$("#menucont #shademenu #flashContent").fadeOut('slow', initSection);
}

function initSection(){
	if($(location).attr('href').lastIndexOf('reference') == -1){
		if($(location).attr('href').lastIndexOf('section') != -1){
			$.get($(location).attr('href')+"&do=SectionClick");
		}else{
			$.get("?do=SectionClick&section=About");
		}
	}
}

function changeBgdDown(){
	move_in = true;
	intervalDown = 12;
	intervalUp = 12;
	yDown = 240;
	yUp = 0;
	targetDown = 0;
	targetUp = -2000;

	moveImagesDown();
	window.setTimeout(function(){intervalUp = 100},300);
}

function moveImagesDown() {
	yDown = yDown - intervalDown; 
	yUp = yUp - intervalUp;
	
	if(yDown > targetDown){
		$("#containerDown").css('backgroundPosition', '50% '+yDown+'px');
		move_in = true;
	}else{
		$("#containerDown").css('backgroundPosition', '50% '+targetDown+'px');
		move_in = false;
	}
	
	if(yUp > targetUp){
		$("#containerUp").css('backgroundPosition', '50% '+yUp+'px');
		move_in = true;
	}else{
		$("#containerUp").css('backgroundPosition', '50% '+targetUp+'px');
		move_in = false;
	}
	

	if(move_in){
		window.setTimeout('moveImagesDown()',40);
	}
}

function changeBgdUp(){
	move_in = true;
	intervalDown = 12;
	intervalUp = 12;
	yDown = -240;
	yUp = 0;
	targetDown = 0;
	targetUp = 2000;

	moveImagesUp();
	window.setTimeout(function(){intervalUp = 100},300);
}

function moveImagesUp() {
	yDown = yDown + intervalDown; 
	yUp = yUp + intervalUp;

	if(yDown < targetDown){
		$("#containerDown").css('backgroundPosition', '50% '+yDown+'px');
	}else{
		$("#containerDown").css('backgroundPosition', '50% '+targetDown+'px');
		move_in = false;
	}
	
	if(yUp < targetUp){
		$("#containerUp").css('backgroundPosition', '50% '+yUp+'px');
	}

	if(move_in){
		window.setTimeout('moveImagesUp()',40);
	}
}

preloadBgdImages = function(){
	img0 = new Image();
	img0.src = "css/images/background-black.gif";
	img1 = new Image();
	img1.src = "css/images/background1.jpg";
	img2 = new Image();
	img2.src = "css/images/background2.jpg";
	img3 = new Image();
	img3.src = "css/images/background3.jpg";
	img4 = new Image();
	img4.src = "css/images/background4.jpg";
	img5 = new Image();
	img5.src = "css/images/background5.gif";
}


$(document).ready(function(){
	preloadBgdImages();
	
	$("#menucont #shademenu a.menuItem").on({
		mouseenter: function(){
			//$(this).css("background-position", "0px -151px");
			$(this).removeClass('pasive').addClass('over');
		},
		mouseleave: function(){
			//$(this).css("background-position", "0px 0px");
			$(this).removeClass('over').addClass('pasive');
		}
	});
	
	//vyjmuto z ajax.js, protoze to v administacni casti nechci
	$("img.ajax").live("click", function (event){
		$("#ajax-spinner").show().css({
			position: "absolute",
			//left: event.pageX + 20,
			//top: event.pageY + 40
			left: "50%",
			top: "50%"
		});
	});
})

