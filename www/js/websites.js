/**
 * scripts of Websites component; shade.cd
 * Copyright (c) 2012 Dast(http://dastsoft.cz)
 */
jQuery.fn.initPanel = function(){
	$(".websites .panel").not(this).css('display','none');
	
	winW = getWindowSize()[0];
	var docW = $(document).width();
	//var panW = docW > this.width() ? docW : this.width();
	var panW = Math.max(docW,this.width());
	
	var delW = panW - winW;
	var initX = -delW/2;
	//maxX = 0;
	//minX = -delW;
	this.attr('rel', -delW);
	
	var bind = (delW > 0) ? true : false;
	bindPanelEvents(this, bind);
	
	this.css('left', initX+'px');
	if(!bind){ this.css('cursor','auto'); }
	$(".websites .panel").not(this).css('display','inline-block');
}

bindPanelEvents = function(panel, bind){
	panels++;
	if(bind){
		$(panel).off();
		$(panel).mousemove(function(e){
			step = (winW/2 - e.pageX) * 0.02;
			step = (Math.abs(step) < 1) ? 1*step/Math.abs(step) : step;
			step = (Math.abs(step) > 50) ? 50*step/Math.abs(step) : step;
		});
		
		$(panel).mouseenter(function(e){
			$(this).addClass('animation');
			step = (winW/2 - e.pageX) * 0.02;
			step = (Math.abs(step) < 1) ? 1*step/Math.abs(step) : step;
			step = (Math.abs(step) > 50) ? 50*step/Math.abs(step) : step;
			movePanel();
		});
		
		$(panel).mouseleave(function(){
			$(this).removeClass('animation');
		});
	}
		
	if(panels == 2){
		$(".websites").css('overflow','hidden');
	}
}

movePanel = function(){
	if($(".animation").length > 0){
		var pos, next, minpos;
		pos = $(".animation").position().left;
		minpos = $(".animation").attr('rel');
		next = pos + step;
	
		if(next < minpos){
			$(".animation").css('left', minpos+'px');
		}else if(next > 0){
			$(".animation").css('left', '0px');
		}else{
			$(".animation").css('left', next+'px');
		}
	
		window.setTimeout('movePanel()',40);
	}
}

bindImgEvents = function(){
	$(".websites .itemcont img").on({
		mouseenter: function(){
			$(this).removeClass('black').addClass('color');
		},
		mouseleave: function(){
			$(this).removeClass('color').addClass('black');
		},
		click: function(){
			$.get("?do=ShowDetail&showid="+this.id);
		}
	});
}
		

$(document).ready(function(){
	panels = 0;
	$.each($(".websites .panel"),function(){ $(this).initPanel(); });
	bindImgEvents();
	
	$(window).resize(function(){
		$.each($(".websites .panel"),function(){ $(this).initPanel(); });
	});
})

