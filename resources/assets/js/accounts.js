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
				transferBill : {extension: "jpg,jpeg,png,JPG,JPEG,PNG", filesize : (2 * 1024 * 1024)},
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
				autoDebitBill : {extension: "jpg,jpeg,png,JPG,JPEG,PNG", filesize : (2 * 1024 * 1024)},
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

	// For Loan Select Popup
	$('.selectloan').click(function(){
		var hidloan = $("#hidloan").val();
		var confirmgroup = confirm("Do You Want To Select Loan?");
		if(confirmgroup) {
			$("#hidcheckDeb").val('1');
			var getchecked = $("#hidcheckDeb").val();
			$('input[type=checkbox]:not(:checked)').each(function(){
				var res = $(this).val().split("$"); 
				if (getchecked == 1) {
					getchecked = 2;
					$('#hidloan').val($('#hidloan').val() + res);
					$('#loanName').val($('#loanName').val() + res[0]);
					$('#autoDebitAmount').val($('#autoDebitAmount').val() + res[2]);
					$('#autoDebitFee').val($('#autoDebitFee').val() + res[3]);
				} else {
					$('#hidloan').val($('#hidloan').val() + ";" + res);
					$('#loanName').val($('#loanName').val() + ";" + res[0]);
					$('#autoDebitAmount').val($('#autoDebitAmount').val() + ";" + res[2]);
					$('#autoDebitFee').val($('#autoDebitFee').val() + ";" + res[3]);
				}
			});
			$("#autoDebitContent").attr("disabled", "disabled");
			$("#loanbutton").attr("disabled", "disabled");
			$("body div").removeClass("modalOverlay");
			$('#getloanpopup').empty();
			$('#getloanpopup').modal('toggle');
		} else {
			return false;
		}
	});

	// For Salary Select Popup
	$('.selectsalary').click(function(){
		$('#txt_empname').val("");
		document.getElementById("empid").value = "";
		var hidchkTrans = $("#hidchkTrans").val();
		var confirmgroup = confirm("Do You Want To Select Salary?");
		if(confirmgroup) {
			$("#hidchkTrans").val('1');
			var getchecked = $("#hidchkTrans").val();
			$('input[type=checkbox]:not(:checked)').each(function(){
				var res = $(this).val().split("$"); 
				if (getchecked == 1) {
					getchecked = 2;
					$('#hidempid').val($('#hidempid').val() + res);
					$('#txt_empname').val($('#txt_empname').val() + res[0]);
					$('#transferAmount').val($('#transferAmount').val() + res[2]);
					$('#transferFee').val($('#transferFee').val() + res[3]);
				} else {
					$('#hidempid').val($('#hidempid').val() + ";" + res);
					$('#txt_empname').val($('#txt_empname').val() + ";" + res[0]);
					$('#transferAmount').val($('#transferAmount').val() + ";" + res[2]);
					$('#transferFee').val($('#transferFee').val() + ";" + res[3]);
				}
			});
			$("#transferContent").attr("disabled", "disabled");
			$("#salarybutton").attr("disabled", "disabled");
			$("#browseEmp").attr("style", "display: none");
			$("#clearEmp").attr("style", "display: none");
			$("#clearSal").attr("style", "display: inline-block");
			$("body div").removeClass("modalOverlay");
			$('#getsalarypopup').empty();
			$('#getsalarypopup').modal('toggle');
		} else {
			return false;
		}
	});

});

function resetErrors() {
	$('form input, form select, form radio').removeClass('inputTxtError');
	$('label.error').remove();
}    

function addedit(page,mainmenu) {
	$('#edit_flg').val('');
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
	$(".feeclass").attr("style", "display:inline-block");
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
	$(".feeclass").attr("style", "display:none");
	$('#fee').val('');
	var amt = $('#amount').val();
	amt = Number(amt.trim().replace(/[, ]+/g, ""));
	if (amt == "") {
		$('#amount').focus();  
		$('#amount').val('');
	} else {
		$('#amount').focus(); 
		if (amt>0) {
			value1 = amt;
			tot = value1.toLocaleString();
			// amount = "-"+tot;
			document.getElementById("amount").value = '';
		}
	}
	$("#transfer").attr("style", "display:none");
}

function banktransferselect() {
	$(".feeclass").attr("style", "display:inline-block");
	
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
function editCashDtl(id, editflg, pgFlg) {
	$('#edit_flg').val(editflg);
	$('#editId').val(id);
	if(pgFlg == 2) {
		$('#frmaccountingindex').attr('action', 'transferaddedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmaccountingindex").submit();
	} else if(pgFlg == 3){
		$('#frmaccountingindex').attr('action', 'autoDebitReg?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmaccountingindex").submit();
	} else {
		$('#frmaccountingindex').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmaccountingindex").submit();
	}

}
function getdate(page) {
	if (page == "Cash") {
		$('#date').val(dates);
	} else if (page == "Transfer") {
		$('#transferDate').val(dates);
	} else if (page == "AutoDebit") {
		$('#autoDebitDate').val(dates);
	} else {
		$('#date').val(dates);
	}
	
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
	var table_id = $('#table_id').val();
	var kananame = "empKanaNames"+table_id;
	var empids = "emp_ID"+table_id;
	var empid = $('#empid').val();
	var empKanaName = $('#empKanaName').val();
	$('#'+kananame).text(empKanaName);
	$('#'+empids).val(empid);
	$('#'+table_id).addClass("highlight1");
	$('#crossid'+table_id).css('display','inline');
	$('#divid'+table_id).css('display','inline');
	if ($('#txt_empname').val() != "") {
		$("#transferContent").attr("disabled", "disabled");
	} else {
		$("#transferContent").removeAttr("disabled");
	}
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
	var table_id = $('#table_id').val();
	var kananame = "empKanaNames"+table_id;
	var empids = "emp_ID"+table_id;
	var empid = $('#empid').val();
	var empKanaName=$('#empKanaName').val();
	$('#'+kananame).text(empKanaName);
	$('#'+empids).val(empid);
	$('#'+table_id).addClass("highlight1");
	$('#crossid'+table_id).css('display','inline');
	$('#divid'+table_id).css('display','inline');
	if ($('#txt_empname').val() != "") {
		$("#transferContent").attr("disabled", "disabled");
	} else {
		$("#transferContent").removeAttr("disabled");
	}
	$('#empnamepopup').modal('toggle');
}

function gotoindexpage(page,mainmenu) {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
	if (page == "Cash") {
		$('#addeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#addeditcancel").submit();
	} else if (page == "Transfer") {
		$('#transferaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#transferaddeditcancel").submit();
	} else {
		$('#AutoDebitRegcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#AutoDebitRegcancel").submit();
	}
}

function Getsalarypopup() {
	var mainmenu = $('#mainmenu').val();
	var transferDate = $('#transferDate').val();
	if (transferDate != "") {
		popupopenclose(1);
		$('#getsalarypopup').load('../Accounting/getsalarypopup?mainmenu='+mainmenu+'&time='+datetime+'&transferDate='+encodeURIComponent(transferDate));
		$("#getsalarypopup").modal({
			backdrop: 'static',
			keyboard: false
			});
		$('#getsalarypopup').modal('show');
	} else {
		alert("Please select Date field");
	}
}

function Getloanpopup() {
	var mainmenu = $('#mainmenu').val();
	var autoDebitDate = $('#autoDebitDate').val();
	if (autoDebitDate != "") {
		popupopenclose(1);
		$('#getloanpopup').load('../Accounting/getloanpopup?mainmenu='+mainmenu+'&time='+datetime+'&autoDebitDate='+encodeURIComponent(autoDebitDate));
		$("#getloanpopup").modal({
			backdrop: 'static',
			keyboard: false
			});
		$('#getloanpopup').modal('show');
	} else {
		alert("Please select Date field");
	}
}

function disabledemp(){
	if ($('#transferContent').val() != "") {
		$("#browseEmp").attr("disabled", "disabled");
		$("#clearEmp").attr("disabled", "disabled");
	} else {
		$("#browseEmp").removeAttr("disabled");
		$("#clearEmp").attr("disabled", "disabled");
	}
}

function fnclear(){
	document.getElementById("txt_empname").value = "";
	$("#transferContent").removeAttr("disabled");
}

function fndebitclear(){
	document.getElementById("hidloan").value = "";
	document.getElementById("loanName").value = "";
	document.getElementById("autoDebitAmount").value = "";
	document.getElementById("autoDebitFee").value = "";
	$("#loanbutton").removeAttr("disabled");
	$("#autoDebitContent").removeAttr("disabled");
}

function fntransclear(){
	document.getElementById("hidempid").value = "";
	document.getElementById("txt_empname").value = "";
	document.getElementById("transferAmount").value = "";
	document.getElementById("transferFee").value = "";
	$("#salarybutton").removeAttr("disabled");
	$("#transferContent").removeAttr("disabled");
	$("#browseEmp").attr("style", "display: inline-block");
	$("#clearEmp").attr("style", "display: inline-block");
	$("#clearSal").attr("style", "display: none");
}



