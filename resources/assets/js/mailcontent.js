$(document).ready(function() {
	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.addeditprocess').click(function () {
		$("#frmcontentmaddedit").validate({
			showErrors: function(errorMap, errorList) {
			// Clean up any tooltips for valid elements
				$.each(this.validElements(), function (index, element) {
						var $element = $(element);
						$element.data("title", "") // Clear the title - there is no error associated anymore
								.removeClass("error")
								.tooltip("destroy");
				});
				// Create new tooltips for invalid elements
				$.each(errorList, function (index, error) {
						var $element = $(error.element);
						$element.tooltip("destroy") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
								.data("title", error.message)
								.addClass("error")
								.tooltip(); // Create a new tooltip based on the error messsage we just set in the title
				});
			},
			rules: {
				mailname: {required: true}, 
				mailtype: {required: true},
				content: {required: true},
			},
			submitHandler: function(form) { // for demo
					var regflg=$('#editflg').val();
					if (regflg!=1) { 
							var confirmprocess = confirm("Do You Want To Register?");
					} else {
							var confirmprocess = confirm("Do You Want To Update?");
					}	
				if(confirmprocess) {
					pageload();
					return true;
				} else {
					return false
				}
			}
		});
		$.validator.messages.required = function (param, input) {
			var article = document.getElementById(input.id);
			return article.dataset.label + ' field is required';
		}
	});
});
function pageClick(pageval) {
	$('#page').val(pageval);
	$('#frmcontentmindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmcontentmindex").submit();
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$('#frmcontentmindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmcontentmindex").submit();
}
function underconstruction() {
	alert("Under Construction");
}
function fngotoregister(mainmenu) {
	pageload();
	$('#editflg').val('2');
	$('#frmcontentmindex').attr('action','addedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
    $("#frmcontentmindex").submit();
}
function fndisablecharge(id) {
	if (id==999) {
		$("#mailother").css("display", "inline-block");
	} else {
		$("#mailother").css("display", "none");
	}
}
function gotoindex(index,mainmenu) {
	if (cancel_check == false) {
        if (confirm("Do You Want To Cancel the Page?")) {
    		pageload();
            $('#mailaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
    		$("#mailaddeditcancel").submit();
        }
    } else { 
		pageload();
		$('#mailaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
    	$("#mailaddeditcancel").submit();
	}
}
function goviewtoindex(mainmenu) {
	pageload();
	$('#frmcontentmview').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
    $("#frmcontentmview").submit();
}
function changedefault(id,mainmenu) {
	pageload();
	$('#eid').val(id);
	$('#frmcontentmindex').attr('action','index'+'?mainmenu='+mainmenu+'&time='+datetime); 
    $("#frmcontentmindex").submit();
}
function gotomailview(id) {
	pageload();
	$('#emailid').val(id);
	$('#frmcontentmindex').attr('action','view'+'?mainmenu='+mainmenu+'&time='+datetime); 
    $("#frmcontentmindex").submit();
}
function gotoeditpage(id,mainmenu) {
	pageload();
	$('#emailid').val(id);
	$('#editflg').val(1);
	$('#frmcontentmview').attr('action','addedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
    $("#frmcontentmview").submit();
}
function gotoview(view,mainmenu) {
	if (cancel_check == false) {
        if (confirm("Do You Want To Cancel the Page?")) {
			pageload();
			window.history.back();
        }
    } else {
    		pageload();
			window.history.back();
    }
}