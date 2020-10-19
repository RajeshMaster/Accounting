$(document).ready(function() {

	// initialize tooltipster on text input elements

	// initialize validate plugin on the form

	$('#swaptable tr').click(function(event) {

		if (event.target.type !== 'radio') {

			if (event.target.nodeName != "SPAN") {

				$(':radio', this).trigger('click');

			}

		}

	});

	$('.addeditprocess').click(function () {
		alert();
		$("#frmaccountingaddedit").validate({
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
				date: {required: true, date: true,minlength:10,correctformatdate: true},
				bank: {required: true},
				transfer: {required: true},
				transtype: {required: true},
				amount: {requiredWithZero: true},
			},
			submitHandler: function(form) {
				alert("sss");
			}
		});
		$.validator.messages.required = function (param, input) {
			alert();
			var article = document.getElementById(input.id);
			return article.dataset.label + err_fieldreq;
		}
		$.validator.messages.minlength = function (param, input) {
			var article = document.getElementById(input.id);
			return "Please Enter valid 10 Number";
		}
	});

});

function resetErrors() {
	$('form input, form select, form radio').removeClass('inputTxtError');
	$('label.error').remove();
}    

function addedit() {
 	$('#frmaccountingindex').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmaccountingindex").submit();
}

// 
function debitAmount() {
	var amt = $('#amount').val();
	if (amt == "-") {
		amt = "";
		$('#amount').focus(); 
		$('#amount').val(amt);
	}
	amt = Number(amt.trim().replace(/[, ]+/g, ""));
	if (amt == "") {
		$('#amount').focus();  
	} else {
		$('#amount').focus(); 
		if (amt<0) {
			amount = Math.abs(amt);
			value1 = amount;
			tot = value1.toLocaleString();
			document.getElementById("amount").value = tot;
		} 
	}
	$("#transfer").attr("style", "display:none");
}

// 
function creditAmount() {
	var amt = $('#amount').val();
	amt = Number(amt.trim().replace(/[, ]+/g, ""));
	if (amt == "") {
		$('#amount').focus();  
		$('#amount').val('-');
	} else {
		$('#amount').focus(); 
		if (amt>0) {
			value1 = amt;
			tot = value1.toLocaleString();
			amount = "-"+tot;
			document.getElementById("amount").value = amount;
		}
	}
	$("#transfer").attr("style", "display:none");
}

function banktransferselect() {
	var amt = $('#amount').val();
	if (amt == "-") {
		amt = "";
		$('#amount').focus();
		$('#amount').val(amt);
	}
	amt = Number(amt.trim().replace(/[, ]+/g, ""));
	if (amt == "") {
		$('#amount').focus();  
	} else {
		$('#amount').focus(); 
		if (amt<0) {
			amount = Math.abs(amt);
			value1 = amount;
			tot = value1.toLocaleString();
			document.getElementById("amount").value = tot;
		} 
	}
	$("#transfer").attr("style", "display:inline-block");
}

// 
function numberonly(e) {
  e=(window.event) ? event : e;
  return (/[0-9]/.test(String.fromCharCode(e.keyCode))); 
}

// 
function fnCancel_check() {
	cancel_check = false;
	return cancel_check;
}

// 
function fnSetZero11(fid) {
	var getvalue = document.getElementById(fid);
	if (getvalue.value.trim() == "") {
		getvalue.value = 0;
	}
	return fnCancel_check();
}

// 
function fnRemoveZero(fname) {
	var getvalue = document.getElementById(fname);
	if (getvalue.value.trim() == 0) {
		getvalue.value = '';
		getvalue.focus();
		getvalue.select();
	}
}