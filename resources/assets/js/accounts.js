$(document).ready(function() {

	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
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

	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.tranferaddeditprocess').click(function () {
		$("#frmtransferaddedit").validate({
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
				transferDate: {required: true, date: true,minlength:10,correctformatdate: true},
				transferMainExp: {required: true},
				transferBank: {required: true},
				transferAmount: {requiredWithZero: true},
				transferFee: {requiredWithZero: true},
				// transferBill : {extension: "jpg,jpeg,png,JPG,JPEG,PNG", filesize : (2  1024  1024)},
			},
			submitHandler: function(form) { // for demo
				if($('#editflg').val() == "edit") {
					var confirmprocess = confirm("Do You Want To Update?");
				} else {
					var confirmprocess = confirm("Do You Want To Register?");
				}
				if(confirmprocess) {
					pageload();
					return true;
				} else {
					return false;
				}
			}
		});
		$.validator.messages.required = function (param, input) {
			var article = document.getElementById(input.id);
			return article.dataset.label + err_fieldreq;
		}
		$.validator.messages.extension = function (param, input) {
			return err_extension;
		}
	});

	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.AutoDebitRegprocess').click(function () {
		$("#frmAutoDebitReg").validate({
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
				autoDebitDate: {required: true, date: true,minlength:10,correctformatdate: true},
				autoDebitBank: {required: true},
				autoDebitMainExp: {required: true},
				autoDebitAmount: {requiredWithZero: true},
				autoDebitFee: {requiredWithZero: true},
				// autoDebitBill : {extension: "jpg,jpeg,png,JPG,JPEG,PNG", filesize : (2  1024  1024)},
			},
			submitHandler: function(form) { // for demo
				if($('#editflg').val() == "edit") {
					var confirmprocess = confirm("Do You Want To Update?");
				} else {
					var confirmprocess = confirm("Do You Want To Register?");
				}
				if(confirmprocess) {
					pageload();
					return true;
				} else {
					return false;
				}
			}
		});
		$.validator.messages.required = function (param, input) {
			var article = document.getElementById(input.id);
			return article.dataset.label + err_fieldreq;
		}
		$.validator.messages.extension = function (param, input) {
			return err_extension;
		}
	});

});

function resetErrors() {
	$('form input, form select, form radio').removeClass('inputTxtError');
	$('label.error').remove();
}    

function addedit(page,mainmenu) {
	if (page == "index") {
	 	$('#frmaccountingindex').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmaccountingindex").submit();
	} else if (page == "cashTransfer") {
		$('#addeditcancel').attr('action', 'transferaddedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#addeditcancel").submit();
	} else if (page == "cashAutoDebit") {
		$('#addeditcancel').attr('action', 'autoDebitReg?mainmenu='+mainmenu+'&time='+datetime);
		$("#addeditcancel").submit();
	} else if (page == "transferCash") {
		$('#transferaddeditcancel').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#transferaddeditcancel").submit();
	} else if (page == "transferAutoDebit") {
		$('#transferaddeditcancel').attr('action', 'autoDebitReg?mainmenu='+mainmenu+'&time='+datetime);
		$("#transferaddeditcancel").submit();
	} else if (page == "autoDebitCash") {
		$('#AutoDebitRegcancel').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#AutoDebitRegcancel").submit();
	} else if (page == "autoDebitTransfer") {
		$('#AutoDebitRegcancel').attr('action', 'transferaddedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#AutoDebitRegcancel").submit();
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
				$('#transfer').append( '<option value="'+resp[i]["id"]+'">'+resp[i]["BANKNAME"]+'</option>' );
				// $('select[name="inchargeDetails"]').val(value);
			}
		},
		error: function(data) {
			// alert(data.status);
		}
	});
}

function getdate() {
	$('#date').val(dates);
}

function popupenable() {
	var mainmenu = $('#mainmenu').val();
	popupopenclose(1);
	$('#empnamepopup').load('../Accounting/empnamepopup?mainmenu='+mainmenu+'&time='+datetime);
	$("#empnamepopup").modal({
		backdrop: 'static',
		keyboard: false
		});
	$('#empnamepopup').modal('show');
}

function fnaddempid(){
	var table_id=$('#table_id').val();
	var kananame = "empKanaNames"+table_id;
	var empids = "emp_ID"+table_id;
	var empid=$('#empid').val();
	var empKanaName=$('#empKanaName').val();
	$('#'+kananame).text(empKanaName);
	$('#'+empids).val(empid);
	$('#'+table_id).addClass("highlight1");
	$('#crossid'+table_id).css('display','inline');
	$('#divid'+table_id).css('display','inline');
	$('#empnamepopup').modal('toggle');
}

function fngetDet(id,empid,empname,name) {
	$("#"+empid).prop("checked", true);
	if($.trim(name) == "" || $.trim(name) == null) {
		name = empname;
	}
	// var name = empname.concat(" ").concat(name);
	$('#txt_empname').val(name);
	var table_id=$('#table_id').val();
	var kananame = "empKanaNames"+table_id;
	var empids = "emp_ID"+table_id;
	$('#empid').val(empid);
	$('#empKanaName').val(name);
}

function fndbclick(id,empid,empname,name) {
	$("#"+empid).prop("checked", true);
	//var name = empname.concat(" ").concat(name);
	if($.trim(name) == "" || $.trim(name) == null) {
		name = empname;
	}
	$('#txt_empname').val(name);
	var table_id=$('#table_id').val();
	var kananame = "empKanaNames"+table_id;
	var empids = "emp_ID"+table_id;
	var empid=$('#empid').val();
	var empKanaName=$('#empKanaName').val();
	$('#'+kananame).text(empKanaName);
	$('#'+empids).val(empid);
	$('#'+table_id).addClass("highlight1");
	$('#crossid'+table_id).css('display','inline');
	$('#divid'+table_id).css('display','inline');
	$('#empnamepopup').modal('toggle');
}

function gotoindexpage(page,mainmenu) {
	if (!confirm("Do You Want To Cancel the Page?")) {
		return false;
	} else {
		if (page == "Cash") {
			$('#addeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
			$("#addeditcancel").submit();
		} else if (page == "Transfer") {
			$('#transferaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
			$("#transferaddeditcancel").submit();
		} else if (page == "AutoDebit") {
			$('#transferaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
			$("#transferaddeditcancel").submit();
		} else {
			return false;
		}
	}
}

function Getsalarypopup() {
	var mainmenu = $('#mainmenu').val();
	popupopenclose(1);
	$('#getsalarypopup').load('../Accounting/getsalarypopup?mainmenu='+mainmenu+'&time='+datetime);
	$("#getsalarypopup").modal({
		backdrop: 'static',
		keyboard: false
		});
	$('#getsalarypopup').modal('show');
}

function Getloanpopup() {
	var mainmenu = $('#mainmenu').val();
	popupopenclose(1);
	$('#getloanpopup').load('../Accounting/getloanpopup?mainmenu='+mainmenu+'&time='+datetime);
	$("#getloanpopup").modal({
		backdrop: 'static',
		keyboard: false
		});
	$('#getloanpopup').modal('show');
}

