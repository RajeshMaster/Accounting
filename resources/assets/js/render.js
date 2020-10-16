$(function(){
	//Å‰‚ÉheaderEfooter‚ÌƒZƒbƒg
	render.header(render.event.header);
	/*render.footer();*/
});

var render = function(){}

render.header = function(fnc){
	render.get('../resources/views/layouts/app.blade.php','app.blade',fnc);
}

render.get = function(url,target,fnc){
	$.ajax({
	    beforeSend : function(xhr) {
	        xhr.overrideMimeType("text/plain; charset=shift_jis");
	    },
	    url : url,
	    dataType : 'text'
	}).done(function(htmlobj) {
	    $(target).html(htmlobj);
	    if(typeof(fnc) == 'function'){
	    	fnc();
	    }
	}).fail(function(){
		//to do ‚à‚¤ˆê‰ñtry‚·‚éH
	})
}

render.event = function(){}
render.event.header = function(){ 
	var CATEGORY = $('#main_contents article').data('category');
	// Split Main&Sub Category by space
	var splitglobal = CATEGORY.split(" ");
	// Activate Main Category
	$('#CMN_gmenu .btn_' + splitglobal[0]).addClass('active');
	/*$('#CMN_sub_gmenu .btn_' + CATEGORY).css({display: "block"});*/
	// Display Sub Category
	$('.' + splitglobal[0]+'_sub').css({display: "block"});
	// ACtivate Sub Category Corressponding link.
	$('#' + splitglobal[1]).addClass('active');
}