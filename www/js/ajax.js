/**
 * 
 * ajax for main sections
 * Copyright (c) 2012 Dast(http://dastsoft.cz)
 */

jQuery.extend({
	nette: {
		updateSnippet: function (id, html) {
			$("#" + id).html(html);
			//changeBgdDown();
		},

		success: function (payload) {
			// redirect
			if (payload.redirect) {
				window.location.href = payload.redirect;
				return;
			}

			// snippets
			if (payload.snippets){
				if(payload.operation == 'sectionChange'){
					$("#content").animate({top: payload.direction * 1500+'px'},1000, function(){ applyChange(payload) });
				}else if(payload.operation == 'sectionInit'){
					applyChange(payload);
				}else{
					for (var i in payload.snippets) {
						jQuery.nette.updateSnippet(i, payload.snippets[i]);
					}
				}
			}
		}
	}
});

function applyChange(payload){
	for (var i in payload.snippets) {
		jQuery.nette.updateSnippet(i, payload.snippets[i]);
	}
	
	$("#content").css('top', payload.direction * -1500+'px');	
	$("#content").animate({top:'0px'},1000);
	
	
	if(payload.direction == -1){
		changeBgdDown();
	}else{
		changeBgdUp();
	}
}


jQuery.ajaxSetup({
	success: jQuery.nette.success,
	dataType: "json"
});


$("body").on("click", "a.ajax", function(event){
    event.preventDefault();
    $.get(this.href);
	
    $("#ajax-spinner").show().css({
        position: "absolute",
        //left: event.pageX + 20,
        //top: event.pageY + 40
		left: "50%",
        top: "50%"
    });
});

$("body").on("click", "input.ajax", function(event){
    $("#ajax-spinner").show().css({
        position: "absolute",
        //left: event.pageX + 20,
        //top: event.pageY + 40
		left: "50%",
        top: "50%"
    });
});

