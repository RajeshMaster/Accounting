$(document).ready(function() {
	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.addeditprocess').click(function () {
		$("#frmourdetailaddedit").validate({
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
				txt_cmyname: {required: true},
				txt_kananame: {required: true},
				txt_pincode1: {required: true, minlength: 3},
				txt_pincode2: {required: true, minlength: 4},
				txt_prefectname: {required: true},
				txt_jpaddress: {required: true},
				txt_buildingname: {required: true},
				Tel1: {required: true, minlength: 2},
				Tel2: {required: true, minlength: 4},
				Tel3: {required: true, minlength: 4},
				fax1: {required: true, minlength: 2},
				fax2: {required: true, minlength: 4},
				fax3: {required: true, minlength: 4},
				txt_commonmail: {required: true, email:true},
				txt_websiteurl: {required: true,urlvalidation: true},
				txt_establishdate: {required: true, date: true,correctformatdate: true},
				txt_clsmonth: {required: true, minlength: 2},
				txt_clsdate: {required: true, minlength: 2},
				txt_systemname: {required: true},
			},
			submitHandler: function(form) { // for demo
				if($('#editflg').val() == "1") {
					if(confirm(err_confup)) {
						pageload();
						//form.submit();
						return true;
					} else {
						return false
					}
				} else {
					if(confirm(err_confreg)) {
						pageload();
						//form.submit();
						return true;
					} else {
						return false
					}
				}
				
			}
		});
		$.validator.messages.required = function (param, input) {
			var article = document.getElementById(input.id);
			return article.dataset.label + ' field is required';
		}
	    $.validator.addMethod("urlvalidation", function(value, element) {
	      return this.optional(element) || validateURL(value);
	    }, "URL is not valid");
		$.validator.messages.minlength = function (param, input) {
			var article = document.getElementById(input.id);
			if (input.id == "txt_pincode1") {
				return "Please Enter 3 Characters";
			} else if (input.id == "txt_pincode2" || input.id == "Tel2" || input.id == "Tel3" || input.id == "fax2" || input.id == "fax3") {
				return "Please Enter 4 Characters";
			} else if (input.id == "txt_clsmonth" || input.id == "txt_clsdate" || input.id == "Tel1" || input.id == "fax1") {
				return "Please Enter 2 Characters";
			}
		}
	});
});
function validateURL(value){
      // URL validation from http://stackoverflow.com/questions/3809401/what-is-a-good-regular-expression-to-match-a-url
      var expression = /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi;
      var regex = new RegExp(expression);
      return value.match(regex);
    }
function underConstruction() {
	alert("Under Construction");
}
function register() {
	pageload();
	$('#editflg').val('2');
	$('#frmourdetailindex').attr('action', 'add'+'?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmourdetailindex").submit();
}
function edit(id,mainmenu) {
	pageload();
	$('#editflg').val('1');
	$('#editid').val(id);
	$('#frmourdetailindex').attr('action', 'edit'+'?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmourdetailindex").submit();
}
function gotoindexpage(viewflg,mainmenu) {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
    if (viewflg == "1") {
    	pageload();
        $('#frmuseraddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
        $("#frmuseraddeditcancel").submit();
    } else {
    	pageload();
        $('#frmuseraddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
        $("#frmuseraddeditcancel").submit();
    }
}
function nextfield(input1,input2,length,event){
	var event = event.keyCode || event.charCode;
	if(event!=8){
		if(document.getElementById(input1).value.length == length) {
			document.getElementById(input2).focus();
		}
	}
}
function taxpopupenable(mainmenu) {
    popupopenclose(1);
	$('#taxpopup').load('../Ourdetail/taxpopup?mainmenu='+mainmenu+'&time='+datetime);
	$("#taxpopup").modal({
           backdrop: 'static',
            keyboard: false
        });
    $('#taxpopup').modal('show');
}
function balsheetpopupenable(id,mainmenu) {
    popupopenclose(1);
	if(id != "") {
		$('#balid').val(id);
	}
	$('#balancesheetpopup').load('../Ourdetail/balancesheetpopup?balid='+id+'&mainmenu='+mainmenu+'&time='+datetime);
	$("#balancesheetpopup").modal({
           backdrop: 'static',
            keyboard: false
        });
    $('#balancesheetpopup').modal('show');
}