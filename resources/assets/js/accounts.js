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
				if(confirm(err_confreg)) {
					pageload();
					return true;
				} else {
					return false
				}
			}
		});
		$.validator.messages.required = function (param, input) {
			var article = document.getElementById(input.id);
			return article.dataset.label + "Required";
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

function addedit(page) {
	var mainmenu = $('#mainmenu').val();
	if (page == "index") {
	 	$('#frmaccountingindex').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmaccountingindex").submit();
	} else if (page == "cashTransfer") {
		$('#frmaccountingaddedit').attr('action', 'transferaddedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmaccountingaddedit").submit();
	} else if (page == "cashAutoDebit") {
		$('#frmaccountingaddedit').attr('action', 'autoDebitReg?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmaccountingaddedit").submit();
	} else if (page == "transferCash") {
		$('#frmtransferaddedit').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmtransferaddedit").submit();
	} else if (page == "transferAutoDebit") {
		$('#frmtransferaddedit').attr('action', 'autoDebitReg?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmtransferaddedit").submit();
	} else if (page == "autoDebitCash") {
		$('#frmAutoDebitReg').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmAutoDebitReg").submit();
	} else if (page == "autoDebitTransfer") {
		$('#frmAutoDebitReg').attr('action', 'transferaddedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmAutoDebitReg").submit();
	} else {
		return false;
	}
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

function currentDate(){

	if ($('#hidGetDate').val() == 0) {
		$('#hidGetDate').val('1');
		var today = new Date();
		var dd = String(today.getDate()).padStart(2, '0');
		var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
		var yyyy = today.getFullYear();
		today =  yyyy + '-' + mm + '-' + dd;
		$('#date').val(today);

	} else {
		$('#date').val('');
		$('#hidGetDate').val('0');
	}
}

function fnGetbankDetails() {

	$('#transfer').find('option').not(':first').remove();
	$('#transfer').val("");
	bank =  $('#bank').val().split("-");
	var bank = bank[1];

	$.ajax({
		type: 'GET',
		dataType: "JSON",
		url: 'bank_ajax',
		data: {"bankacc": bank},
		success: function(resp) {
			for (i = 0; i < resp.length; i++) {
				$('#transfer').append( '<option value="'+resp[i]["ID"]+'">'+resp[i]["BANKNAME"]+'</option>' );
				// $('select[name="inchargeDetails"]').val(value);
			}
		},
		error: function(data) {
			// alert(data.status);
		}
	});
}