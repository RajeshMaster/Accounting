$(document).ready(function() {
	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.addeditprocess').click(function () {
		$("#frvisarenewaddedit").validate({
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
				extyear: {required: true},
				resonforext: {required: true},
				crime: {required: true},
				crimedetail: {required: true},
			},
			submitHandler: function(form) { // for demo
				var confirmprocess = confirm("Do You Want To Renew the Visa?");
				if(confirmprocess) {
					pageload();
					// form.submit(); dont use this cause of double time insert in internet explorer
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
		$.validator.messages.equalTo = function (param, input) {
			var article = document.getElementById(input.id);
			return passwordmatch;
		}
		$.validator.messages.minlength = function (param, input) {
			var article = document.getElementById(input.id);
			if (input.id == "MstuserTelNO") {
				return "Please Enter 3 Characters";
			} else if (input.id == "MstuserTelNO1" || input.id == "MstuserTelNO2") {
				return "Please Enter 4 Characters";
			}
		}
		$('.a-middle').css('margin-top', function () {
			return ($(window).height() - $(this).height()) / 4
	    });
	});
});
function importvisapopupenable(mainmenu) {
	$('#importpopup').load('visaimportpopup?mainmenu='+mainmenu+'&time='+datetime);
	$("#importpopup").modal({
           backdrop: 'static',
            keyboard: false
        });
    $('#importpopup').modal('show');
}
function pageClick(pageval) {
	$('#page').val(pageval);
	$("#frvisarenewindex").submit();
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$("#frvisarenewindex").submit();
}
function underconstruction() {
	alert("Under Construction");
}
function fngotoaddedit(Emp_ID,visanumber) {
	$('#Emp_ID').val(Emp_ID);
	$('#visanumb').val(visanumber);
	$('#frvisarenewindex').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#frvisarenewindex").submit();
}
function fngotoindexpage() {
	if (cancel_check == false) {
		if (!confirm(cancel_msg)) {
			return false;
		}
	}
		pageload();
		$('#frvisarenewindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#frvisarenewindex").submit();
}
function fncrimestatus(val) {
	if (val == 1) {
		$('#crimedetails').css({"display": "block"});
	} else {
		$('#crimedetails').css({"display": "none"});
	}
}
function fngovisaview(empid) {
	$('#Emp_ID').val(empid);
	$('#frvisarenewindex').attr('action', 'visaview?mainmenu='+mainmenu+'&time='+datetime);
	$("#frvisarenewindex").submit();
}
function fngotovisaindex() {
	pageload();
	$('#frmvisaview').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmvisaview").submit();
}
function setEnddate () {
	var timestamp = Date.parse($("#startdate").val());
    var myLength = $("#startdate").val().length;
    if (isNaN(timestamp) == true || myLength !=10) { 
        document.getElementById('visaExpiryDate').value = "";
        document.getElementById('EdDate').value = "";
    }
    if (document.getElementById('startdate').value != "" 
    		&& document.getElementById('stayperiod').value != "" 
    			&& isNaN(timestamp) == false 
    				&& myLength ==10) {
        var ContractTerm = document.getElementById('stayperiod').value;
        var dateMin = document.getElementById('startdate').value;
        dateMin = new Date(dateMin);
        var rMax = new Date(dateMin.getFullYear() + parseInt(ContractTerm), dateMin.getMonth(),dateMin.getDate());
        var setEddate = (rMax.getFullYear())+"-"+("0"+(rMax.getMonth()+1)).slice(-2)+"-"+("0"+rMax.getDate()).slice(-2);
        document.getElementById('visaExpiryDate').value = setEddate;
        document.getElementById('EdDate').value = setEddate;
    }
}
function fnvisaextensionform(Emp_ID,visanumber,empname) {
	$('#Emp_ID').val(Emp_ID);
	$('#visanumb').val(visanumber);
	$('#empname').val(empname);
	if(confirm(err_convisadownload)) {
		$('#frvisarenewindex').attr('action', 'visaExtensionFormDownload?mainmenu='+mainmenu+'&time='+datetime);
		$("#frvisarenewindex").submit();
	}
}