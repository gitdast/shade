/**
 * 
 * admin scripts of shade.cd
 * Copyright (c) 2012 Dast(http://dastsoft.cz)
 */

$(document).ready(function(){

	$("#addlist").on('change', 'input[type="checkbox"]', function(){
		var int_checked = this.checked ? 1 : 0 ;
		$.get("?do=ChangeDisplay&addid="+this.id+"&checked="+int_checked);
	});
	
	$("#addlist").on('click', 'a.delete', function(e){
		e.preventDefault();
        e.stopImmediatePropagation();
		if(confirm("Potvrďte smazání")){
			$.get(this.href);
		}
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

switchIcon = function(){
	if($("#help #hcontent").is(':hidden')){
		$("#help #hcontrol").css('background-image','url("/css/images/ico_down.png")');
	}else{
		$("#help #hcontrol").css('background-image','url("/css/images/ico_up.png")');
	}
}
