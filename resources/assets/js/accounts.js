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
				accDate: {required: true, date: true,minlength:10,correctformatdate: true},
				bank: {required: true},
				transfer: {required: true},
				transtype: {required: true},
				content: {required: true},
				amount: {requiredWithZero: true},
			},
			submitHandler: function(form) {
				if($('#edit_flg').val() != 1) { 
					var accDate = $('#accDate').val();
					var bank =  $('#bank').val().split("-");
					var bankIdFrom = bank[0];
					var accountNumberFrom = bank[1];
					$.ajax({
						type: 'GET',
						url: 'dateBankExists',
						data: {	"accDate": accDate,
								"bankIdFrom": bankIdFrom,
								"accountNumberFrom": accountNumberFrom
							},
						success: function(resp) {
							if (resp != 0) {
								document.getElementById('errorSectiondisplay').innerHTML = "";
								err_invalidcer = "After the Completed Month";
								var error='<div align="center" style="padding: 0px;" id="inform">';
								error+='<table cellspacing="0" class="statusBg1" cellpadding="0" border="0">';
								error+='<tbody><tr><td style="padding: 4px 10px" align="center"><span class="innerBg" id="mc_msg_txt">'+err_invalidcer+'</span></td>';
								error+='<td width="20" valign="top" style="padding-top: 4px; _padding-top: 2px;"><span>';
								error+='<a href="javascript:intdisplaymessage();" class="fa fa-times" style="color:white;"/>';
								error+='</span></td>';
								error+='</tr></tbody></table></div>';
								document.getElementById('errorSectiondisplay').style.display = 'block';
								document.getElementById('errorSectiondisplay').innerHTML = error;
								$("#accDate").focus();
								return false;
							} else {
								var confirmprocess = confirm("Do You Want To Register?");
								if(confirmprocess) {
									pageload();
									form.submit();
									return true;
								} else {
									return false
								}
							}
						},
						error: function(data) {
							// alert(data);
						}
					});
				} else {
					var confirmprocess = confirm("Do You Want To Update?");
					if(confirmprocess) {
						pageload();
						return true;
					} else {
						return false
					}
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
				accDate: {required: true, date: true,minlength:10,correctformatdate: true},
				// transferMainExp: {required: true},
				transferBank: {required: true},
				transferAmount: {requiredWithZero: true},
				txt_empname: {Anyonerequired: "#transferContent"},
				transferContent: {Anyonerequired: "#txt_empname"},
				transferBill : {extension: "jpg,jpeg,png,JPG,JPEG,PNG", filesize : (2 * 1024 * 1024)},
			},
			submitHandler: function(form) { // for demo
				if($('#edit_flg').val() != 1) { 
					var accDate = $('#accDate').val();
					var bank =  $('#transferBank').val().split("-");
					var bankIdFrom = bank[0];
					var accountNumberFrom = bank[1];
					$.ajax({
						type: 'GET',
						url: 'dateBankExists',
						data: {	"accDate": accDate,
								"bankIdFrom": bankIdFrom,
								"accountNumberFrom": accountNumberFrom
							},
						success: function(resp) {
							if (resp != 0) {
								document.getElementById('errorSectiondisplay').innerHTML = "";
								err_invalidcer = "After the Completed Month";
								var error='<div align="center" style="padding: 0px;" id="inform">';
								error+='<table cellspacing="0" class="statusBg1" cellpadding="0" border="0">';
								error+='<tbody><tr><td style="padding: 4px 10px" align="center"><span class="innerBg" id="mc_msg_txt">'+err_invalidcer+'</span></td>';
								error+='<td width="20" valign="top" style="padding-top: 4px; _padding-top: 2px;"><span>';
								error+='<a href="javascript:intdisplaymessage();" class="fa fa-times" style="color:white;"/>';
								error+='</span></td>';
								error+='</tr></tbody></table></div>';
								document.getElementById('errorSectiondisplay').style.display = 'block';
								document.getElementById('errorSectiondisplay').innerHTML = error;
								$("#accDate").focus();
								return false;
							} else {
								var confirmprocess = confirm("Do You Want To Register?");
								if(confirmprocess) {
									pageload();
									form.submit();
									return true;
								} else {
									return false
								}
							}
						},
						error: function(data) {
							// alert(data);
						}
					});
				} else {
					var confirmprocess = confirm("Do You Want To Update?");
					if(confirmprocess) {
						pageload();
						return true;
					} else {
						return false
					}
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
				accDate: {required: true, date: true,minlength:10,correctformatdate: true},
				autoDebitBank: {required: true},
				// autoDebitMainExp: {required: true},
				autoDebitAmount: {requiredWithZero: true},
				loanName: {Anyonerequired: "#autoDebitContent"},
				autoDebitContent: {Anyonerequired: "#loanName"},
				autoDebitBill : {extension: "jpg,jpeg,png,JPG,JPEG,PNG", filesize : (2 * 1024 * 1024)},
			},
			submitHandler: function(form) { // for demo
				if($('#edit_flg').val() != 1) { 
					var accDate = $('#accDate').val();
					var bank =  $('#autoDebitBank').val().split("-");
					var bankIdFrom = bank[0];
					var accountNumberFrom = bank[1];
					$.ajax({
						type: 'GET',
						url: 'dateBankExists',
						data: {	"accDate": accDate,
								"bankIdFrom": bankIdFrom,
								"accountNumberFrom": accountNumberFrom
							},
						success: function(resp) {
							if (resp != 0) {
								document.getElementById('errorSectiondisplay').innerHTML = "";
								err_invalidcer = "After the Completed Month";
								var error='<div align="center" style="padding: 0px;" id="inform">';
								error+='<table cellspacing="0" class="statusBg1" cellpadding="0" border="0">';
								error+='<tbody><tr><td style="padding: 4px 10px" align="center"><span class="innerBg" id="mc_msg_txt">'+err_invalidcer+'</span></td>';
								error+='<td width="20" valign="top" style="padding-top: 4px; _padding-top: 2px;"><span>';
								error+='<a href="javascript:intdisplaymessage();" class="fa fa-times" style="color:white;"/>';
								error+='</span></td>';
								error+='</tr></tbody></table></div>';
								document.getElementById('errorSectiondisplay').style.display = 'block';
								document.getElementById('errorSectiondisplay').innerHTML = error;
								$("#accDate").focus();
								return false;
							} else {
								var confirmprocess = confirm("Do You Want To Register?");
								if(confirmprocess) {
									pageload();
									form.submit();
									return true;
								} else {
									return false
								}
							}
						},
						error: function(data) {
							// alert(data);
						}
					});
				} else {
					var confirmprocess = confirm("Do You Want To Update?");
					if(confirmprocess) {
						pageload();
						return true;
					} else {
						return false
					}
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

		var loanerr = 0;
		var loansuberr = 0;
		// document.getElementById("autoDebitContent").value = "";
		var lengthOfUnchecked = $('input[class=loanchk]:not(:checked)').length;
		if(lengthOfUnchecked == 0){
			alert("Please select data");
			return false;
		}
		if ($('#loanSub').val() == "undefined" || $('#loanSub').val() == "") {
			$('#loanSub').attr("style", "background-color: #E88F8F");
			loansuberr = 1;
			return false;
		} else {
			$('#loanSub').attr("style", "background-color:none");
		}
		$('input[class=loanchk]:not(:checked)').each(function(){
			var res = $(this).val().split("$"); 
			if ($('#'+"loanBank"+res[4]).val() == "") {
				$('#'+"loanBank"+res[4]).attr("style", "background-color: #E88F8F");
				loanerr = 1;
				return false;
			} else {
				$('#'+"loanBank"+res[4]).attr("style", "background-color:none");
			}
			if ($('#'+"loanAmt"+res[4]).val() == "") {
				$('#'+"loanAmt"+res[4]).attr("style", "background-color: #E88F8F;text-align:right;");
				loanerr = 1;
				return false;
			} else {
				$('#'+"loanAmt"+res[4]).attr("style", "background-color:none;text-align:right;");
			}
		});
		if (loanerr) {
			return false;
		}
		if (loansuberr) {
			return false;
		}

		var confirmgroup = confirm("Do You Want To Select Loan?");
		if(confirmgroup) {
		
			$("#hidcheckDeb").val('1');
			var getchecked = $("#hidcheckDeb").val();

			$('input[class=loanchk]:not(:checked)').each(function(){
				var res = $(this).val().split("$"); 
				if (getchecked == 1) {
					getchecked = 2;
					$('#hidloan').val(res[0] + ":" + res[1] + ":" + $('#'+"loanAmt"+res[4]).val() + ":" + $('#'+"loanFee"+res[4]).val() + ":" + $('#'+"loanBank"+res[4]).val() + ":" + res[5]);
					// $('#loanName').val(res[0]);
					// $('#hidloanId').val(res[1]);
					// document.getElementById('autoDebitAmountloan').innerHTML = $('#'+"loanAmt"+res[4]).val();
					// document.getElementById('autoDebitFeeloan').innerHTML = $('#'+"loanFee"+res[4]).val();
				} else {
					$('#hidloan').val($('#hidloan').val() + ";" + res[0] + ":" + res[1] + ":" + $('#'+"loanAmt"+res[4]).val() + ":" + $('#'+"loanFee"+res[4]).val() + ":" + $('#'+"loanBank"+res[4]).val() + ":" + res[5]);
					// $('#loanName').val($('#loanName').val() + ";" + res[0]);
					// $('#hidloanId').val($('#hidloanId').val() + ";" + res[1]);
					// document.getElementById('autoDebitAmountloan').innerHTML = document.getElementById('autoDebitAmountloan').innerHTML + ";" + $('#'+"loanAmt"+res[4]).val();
					// document.getElementById('autoDebitFeeloan').innerHTML = document.getElementById('autoDebitFeeloan').innerHTML + ";" + $('#'+"loanFee"+res[4]).val();
				}
				// $('#hidempId').val($('#assetsUser').val());
			});
			pageload();
			form.submit();
			return true;
			// $("#enableamt").attr("style", "display: block");
			// $("#enablefee").attr("style", "display: block");
			// $("#hidamtfee").attr("style", "display: none");
			// $("#autoDebitContent").attr("disabled", "disabled");
			// $("#loanbutton").attr("disabled", "disabled");
			// $("#debitrequired").css("visibility", "hidden");
			// $("body div").removeClass("modalOverlay");
			// $('#getloanpopup').empty();
			// $('#getloanpopup').modal('toggle');
		} else {
			return false;
		}
	});

	// For Salary Select Popup
	$('.selectsalary').click(function(){
		var lengthOfUnchecked = $('input[class=salarychk]:not(:checked)').length;
		if(lengthOfUnchecked == 0){
			alert("Please select data");
			return false;
		}
		var salerr = 0;
		var salsuberr = 0;
		// $('#txt_empname').val("");
		// document.getElementById("empid").value = "";
		// document.getElementById("transferContent").value = "";
		if ($('#salaryBank').val() == "") {
			$('#salaryBank').attr("style", "background-color: #E88F8F");
			salerr = 1;
			return false;
		} else {
			$('#salaryBank').attr("style", "background-color:none");
		}
		if ($('#salarySub').val() == "undefined" || $('#salarySub').val() == "") {
			$('#salarySub').attr("style", "background-color: #E88F8F");
			salsuberr = 1;
			return false;
		} else {
			$('#salarySub').attr("style", "background-color:none");
		}
		$('input[class=salarychk]:not(:checked)').each(function(){
			var res = $(this).val().split("$"); 
			
			if ($('#'+"salAmt"+res[4]).val() =="" || $('#'+"salAmt"+res[4]).val() ==0) {
				$('#'+"salAmt"+res[4]).attr("style", "background-color: #E88F8F;text-align:right;");
				salerr = 1;
				return false;
			} else {
				$('#'+"salAmt"+res[4]).attr("style", "background-color:none;text-align:right;");
			}
		});
		if (salsuberr) {
			return false;
		}
		if (salerr) {
			return false;
		}
		var hidchkTrans = $("#hidchkTrans").val();
		var confirmgroup = confirm("Do You Want To Select Salary?");

		if(confirmgroup) {
			$("#hidchkTrans").val('1');
			var getchecked = $("#hidchkTrans").val();
			$('input[class=salarychk]:not(:checked)').each(function(){
				var res = $(this).val().split("$"); 
				if (getchecked == 1) {
					getchecked = 2;
					$('#hidempid').val(res[0] + ":" + res[1] + ":" + $('#'+"salAmt"+res[4]).val() + ":" + $('#'+"salFee"+res[4]).val());
					// $('#txt_empname').val(res[0]);
					// $('#hidemp').val(res[1]);
					// document.getElementById('transferAmountsalary').innerHTML = $('#'+"salAmt"+res[4]).val();
					// document.getElementById('transferFeesalary').innerHTML = $('#'+"salFee"+res[4]).val();
				} else {
					$('#hidempid').val($('#hidempid').val() + ";" + res[0] + ":" + res[1] + ":" + $('#'+"salAmt"+res[4]).val() + ":" + $('#'+"salFee"+res[4]).val());
					// $('#txt_empname').val($('#txt_empname').val() + ";" + res[0]);
					// $('#hidemp').val($('#hidemp').val() + ";" + res[1]);
					// document.getElementById('transferAmountsalary').innerHTML = document.getElementById('transferAmountsalary').innerHTML + ";" + $('#'+"salAmt"+res[4]).val();
					// document.getElementById('transferFeesalary').innerHTML = document.getElementById('transferFeesalary').innerHTML + ";" + $('#'+"salFee"+res[4]).val();
				}
			});
			pageload();
			form.submit();
			return true;
			// $("#enableamt").attr("style", "display: block");
			// $("#enablefee").attr("style", "display: block");
			// $("#hidamtfee").attr("style", "display: none");
			// $("#transferContent").attr("disabled", "disabled");
			// $("#contentrequired").css("visibility", "hidden");
			// $("#salarybutton").attr("disabled", "disabled");
			// $("#browseEmp").attr("style", "display: none");
			// $("#clearEmp").attr("style", "display: none");
			// $("#clearSal").attr("style", "display: inline-block");
			// $("body div").removeClass("modalOverlay");
			// $('#getsalarypopup').empty();
			// $('#getsalarypopup').modal('toggle');
		} else {
			return false;
		}
	});

	// For Loan Select Popup
	$('.selectinvoice').click(function(){

		var invoiceerr = 0;
		var invSuberr = 0;
		// document.getElementById("autoDebitContent").value = "";
		var lengthOfUnchecked = $('input[class=invoicechk]:not(:checked)').length;
		if(lengthOfUnchecked == 0){
			alert("Please select data");
			return false;
		}
		if ($('#invSub').val() == "undefined" || $('#invSub').val() == "") {
			$('#invSub').attr("style", "background-color: #E88F8F");
			invSuberr = 1;
			return false;
		} else {
			$('#invSub').attr("style", "background-color:none");
		}
		$('input[class=invoicechk]:checked').each(function(){
			var res = $(this).val().split("$"); 
			$('#'+"loanBank"+res[3]).attr("style", "background-color:none");
			$('#'+"invoiceAmt"+res[3]).attr("style", "background-color:none;text-align:right;");
		});

		$('input[class=invoicechk]:not(:checked)').each(function(){
			var res = $(this).val().split("$"); 

			if ($('#'+"loanBank"+res[3]).val() == "") {
				$('#'+"loanBank"+res[3]).attr("style", "background-color: #E88F8F");
				invoiceerr = 1;
				return false;
			} else {
				$('#'+"loanBank"+res[3]).attr("style", "background-color:none");
			}

			if ($('#'+"invoiceAmt"+res[3]).val() == "" || $('#'+"invoiceAmt"+res[3]).val() == 0) {
				$('#'+"invoiceAmt"+res[3]).attr("style", "background-color: #E88F8F;text-align:right;");
				invoiceerr = 1;
				return false;
			} else {
				$('#'+"invoiceAmt"+res[3]).attr("style", "background-color:none;text-align:right;");
			}
		});
		if (invoiceerr) {
			return false;
		}
		if (invSuberr) {
			return false;
		}
		var confirmgroup = confirm("Do You Want To Select Invoice?");
		if(confirmgroup) {
			$("#hidchkInv").val('1');
			var getchecked = $("#hidchkInv").val();
			$('input[class=invoicechk]:not(:checked)').each(function(){
				var res = $(this).val().split("$"); 
				if (getchecked == 1) {
					getchecked = 2;
					$('#hidInvid').val(res[0] + ":" + res[1] + ":" + $('#'+"invoiceAmt"+res[3]).val() + ":" + $('#'+"loanBank"+res[3]).val() + ":" + $('#'+"hidInvPaidId"+res[3]).val());
				} else {
					$('#hidInvid').val($('#hidInvid').val() + ";" + res[0] + ":" + res[1] + ":" + $('#'+"invoiceAmt"+res[3]).val() + ":" + $('#'+"loanBank"+res[3]).val() + ":" + $('#'+"hidInvPaidId"+res[3]).val());
				}
			});
			pageload();
			form.submit();
			return true;

		} else {
			return false;
		}
	});

	// For Salary Select Popup
	$('.selectExpenses').click(function(){
	
		var expbankerr = 0;
		var experr = 0;
		
		if ($('#bankIdAccNo').val() == "undefined" || $('#bankIdAccNo').val() == "") {
			$('#bankIdAccNo').attr("style", "background-color: #E88F8F");
			expbankerr = 1;
			return false;
		} else {
			$('#bankIdAccNo').attr("style", "background-color:none");
		}

		var lengthOfUnchecked = $('input[class=expensesDatachk]:not(:checked)').length;
		if(lengthOfUnchecked == 0){
			alert("Please select data");
			return false;
		}
		
		$('input[class=expensesDatachk]:not(:checked)').each(function(){
			var res = $(this).val().split("$"); 
			if ($('#'+"expensesDataAmt"+res[4]).val() == "" || $('#'+"expensesDataAmt"+res[4]).val() == 0) {
				$('#'+"expensesDataAmt"+res[4]).attr("style", "background-color: #E88F8F;text-align:right;");
				experr = 1;
				return false;
			} else {
				$('#'+"expensesDataAmt"+res[4]).attr("style", "background-color:none;text-align:right;");
			}
		});
		if (expbankerr) {
			return false;
		}
		if (experr) {
			return false;
		}
		var hidchkExp = $("#hidchkExp").val();
		var confirmgroup = confirm("Do You Want To Select Expenses Data?");

		if(confirmgroup) {
			$("#hidchkExp").val('1');
			var getchecked = $("#hidchkExp").val();
			$('input[class=expensesDatachk]:not(:checked)').each(function(){
				var res = $(this).val().split("$"); 
				if (getchecked == 1) {
					getchecked = 2;
					$('#hidempid').val(res[0] + ":" + res[1] + ":" + $('#'+"expensesDataAmt"+res[4]).val() + ":" + $('#'+"expensesDataFee"+res[4]).val() + ":" + res[5]);
				} else {
					$('#hidempid').val($('#hidempid').val() + ";" + res[0] + ":" + res[1] + ":" + $('#'+"expensesDataAmt"+res[4]).val() + ":" + $('#'+"expensesDataFee"+res[4]).val() + ":" + res[5]);
				}
			});
			pageload();
			form.submit();
			return true;
		} else {
			return false;
		}
	});

});

function resetErrors() {
	$('form input, form select, form radio').removeClass('inputTxtError');
	$('label.error').remove();
}    

function intdisplaymessage() {
	document.getElementById('errorSectiondisplay').style.display='none';
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
function editCashDtl(id, editflg, pgFlg, bankId, content) {
	$('#bank_Id').val(bankId);
	$('#content_Id').val(content);
	$('#edit_flg').val(editflg);
	$('#editId').val(id);
	if(pgFlg == 2 || pgFlg == 5) {
		$('#frmaccountingindex').attr('action', 'transferedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmaccountingindex").submit();
	} else if(pgFlg == 3){
		$('#frmaccountingindex').attr('action', 'autoDebitedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmaccountingindex").submit();
	}  else if(pgFlg == 4){
		$('#frmaccountingindex').attr('action', 'autoDebitedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmaccountingindex").submit();
	} else {
		$('#frmaccountingindex').attr('action', 'cashedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmaccountingindex").submit();
	}

}

/*function getdate(page) {
	if (page == "Cash") {
		$('#date').val(dates);
	} else if (page == "Transfer") {
		$('#transferDate').val(dates);
	} else if (page == "AutoDebit") {
		$('#autoDebitDate').val(dates);
	} else {
		$('#date').val(dates);
	}	
}*/

function getdate() {
	$('#accDate').val(dates);
}

function popupenable() {
	var mainmenu = $('#mainmenu').val();
	var transferDate = $('#accDate').val();
	if (transferDate != "") {
		popupopenclose(1);
		$('#empnamepopup').load('../Accounting/empnamepopup?mainmenu='+mainmenu+'&time='+datetime+'&transferDate='+encodeURIComponent(transferDate));
		$("#empnamepopup").modal({
			backdrop: 'static',
			keyboard: false
			});
		$('#empnamepopup').modal('show');
	} else {
		alert("Please select Date field");
	}
	
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
		$("#addtranscontent").attr("disabled", "disabled");
		$("#contentrequired").css("visibility", "hidden");
	} else {
		$("#transferContent").removeAttr("disabled");
		$("#addtranscontent").removeAttr("disabled");
		$("#contentrequired").css("visibility", "visible");
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
		$("#addtranscontent").attr("disabled", "disabled");
		$("#contentrequired").css("visibility", "hidden");
	} else {
		$("#transferContent").removeAttr("disabled");
		$("#addtranscontent").removeAttr("disabled");
		$("#contentrequired").css("visibility", "visible");
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
	var transferDate = $('#accDate').val();
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

function Getloanpopup(userId,belongsTo) {
	var mainmenu = $('#mainmenu').val();
	var autoDebitDate = $('#accDate').val();
	if (autoDebitDate != "") {
		popupopenclose(1);
		$('#getloanpopup').load('../Accounting/getloanpopup?mainmenu='+mainmenu+'&time='+datetime+'&autoDebitDate='+encodeURIComponent(autoDebitDate)+'&userId='+userId+'&belongsTo='+belongsTo);
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
		$("#emprequired").css("visibility", "hidden");
	} else {
		$("#browseEmp").removeAttr("disabled");
		$("#clearEmp").removeAttr("disabled");
		$("#emprequired").css("visibility", "visible");
	}
}

function fnclear(){
	document.getElementById("txt_empname").value = "";
	$("#transferContent").removeAttr("disabled");
	$("#addtranscontent").removeAttr("disabled");
	$("#contentrequired").css("visibility", "visible");
}

function fndebitclear(){
	document.getElementById("hidloan").value = "";
	document.getElementById("hidloanId").value = "";
	document.getElementById("hidempId").value = "";
	document.getElementById("loanName").value = "";
	document.getElementById("autoDebitAmountloan").value = "";
	document.getElementById("autoDebitFeeloan").value = "";
	$("#loanbutton").removeAttr("disabled");
	$("#debitrequired").css("visibility", "visible");
	$("#autoDebitContent").removeAttr("disabled");
	$("#addautoDebitContent").removeAttr("disabled");
	$("#enableamt").attr("style", "display: none");
	$("#enablefee").attr("style", "display: none");
	$("#hidamtfee").attr("style", "display: block");
}

function fntransclear(){
	document.getElementById("hidempid").value = "";
	document.getElementById("hidemp").value = "";
	document.getElementById("txt_empname").value = "";
	document.getElementById("transferAmount").value = "";
	document.getElementById("transferFee").value = "";
	$("#salarybutton").removeAttr("disabled");
	$("#transferContent").removeAttr("disabled");
	$("#addtranscontent").removeAttr("disabled");
	$("#browseEmp").attr("style", "display: inline-block");
	$("#clearEmp").attr("style", "display: inline-block");
	$("#clearSal").attr("style", "display: none");
	$("#enableamt").attr("style", "display: none");
	$("#enablefee").attr("style", "display: none");
	$("#hidamtfee").attr("style", "display: block");
	$("#contentrequired").css("visibility", "visible");
}

function disabledloan(){
	if ($('#autoDebitContent').val() != "") {
		$("#clearloan").attr("disabled", "disabled");
		$("#loanrequired").css("visibility", "hidden");
	} else {
		$("#clearloan").removeAttr("disabled");
		$("#loanrequired").css("visibility", "visible");
	}
}

function fnGetLoanDtls(userId){
	var belongsTo = "";
	Getloanpopup(userId,belongsTo);
}

function fnGetBelongsToDtls(belongsTo){
	var userId = $('#assetsUser').val();
	Getloanpopup(userId,belongsTo);
}

function getData(month, year, flg, prevcnt, nextcnt, account_period, lastyear, currentyear, account_val) {
	// alert(month + "***" + flg + "****" + currentyear);
	var yearmonth = year + "-" +  ("0" + month).substr(-2);
	if ((prevcnt == 0) && (flg == 0) && (parseInt(month) < account_period) && (year == lastyear)) {
		alert("No Previous Record.");
		//return false;
	} else if ((nextcnt == 0) && (flg == 0) && (parseInt(month) > account_period) && (year == currentyear)) {
		alert("No Next Record.");
	} else {
		if (flg == 1) {
			 $('#previou_next_year').val(year + "-" +  ("0" + month).substr(-2)); 
		}
		$('#pageclick').val('');
		$('#page').val('');
		$('#plimit').val('');
		$('#selMonth').val(("0" + month).substr(-2));
		$('#selYear').val(year);
		$('#prevcnt').val(prevcnt);
		$('#nextcnt').val(nextcnt);
		$('#account_val').val(account_val);
		$('#frmaccountingindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$('#frmaccountingindex').submit();
	}
}

function changeOrderpopUp(bankId,AccNo){

	$('#bankNo').val('');
	$('#accNo').val('');
	$('#bankNo').val(bankId);
	$('#accNo').val(AccNo);
	var mainmenu = $('#mainmenu').val();
	var selMonth = $('#selMonth').val();
	var selYear = $('#selYear').val();
	popupopenclose(1);
	$('#getregisterpopup').load('../Accounting/getcashDetails?mainmenu='+mainmenu+'&time='+datetime+'&selMonth='+selMonth+'&selYear='+selYear+'&bankId='+bankId+'&AccNo='+AccNo);
	$("#getregisterpopup").modal({
		backdrop: 'static',
		keyboard: false
		});
	$('#getregisterpopup').modal('show');
}

// Single text popup radio button check
function fnrdocheck(textbox1,editid,totalcount,val) {
	var rowCount = $('#swaptable1 tr').length;
	$('#rdoid').val(editid);
	// EDIT BUTTON ENABLE
	if(rowCount == 1) {
		
		document.getElementById("dwnArrow").disabled = true;
		$("#dwnArrow").css("color","#bbb5b5");
		document.getElementById("upArrow").disabled = true;
		$("#upArrow").css("color","#bbb5b5");
	} else {
	
		document.getElementById("dwnArrow").disabled = false;
		$("#dwnArrow").css("color","#5cb85c");
		document.getElementById("upArrow").disabled = true;
		$("#rdoedit"+editid).attr("checked", true);
		updownArrowEnableDisable(val, totalcount);
	}
}

function updownArrowEnableDisable(val, totalcount) {
	if (val == 0) {
		document.getElementById("upArrow").disabled = true;
		$("#upArrow").css("color","#bbb5b5");
		document.getElementById("dwnArrow").disabled = false;
		// $("#dwnArrow").css("background-color","#5cb85c");
	} else if (val == totalcount-1) {
		document.getElementById("upArrow").disabled = false;
		$("#upArrow").css("color","#5cb85c");
		document.getElementById("dwnArrow").disabled = true;
		$("#dwnArrow").css("color","#bbb5b5");
	} else {
		// enable_arrow();
		document.getElementById("dwnArrow").disabled = false;
		document.getElementById("upArrow").disabled = false;
		$("#dwnArrow").css("color","#5cb85c");
		$("#upArrow").css("color","#5cb85c");
	}
}

//setting Down Arrow process
function getdowndata(){
	//GO TO COMMIT ENABLE
	Commit_buttonenable();
	// 
	document.getElementById("upArrow").disabled = false;
	$("#upArrow").css("color","#5cb85c");
	var upid = document.getElementsByName("rdoedit");
	var radioLength = upid.length;
	for(var i = 0; i <radioLength; i++) {
		if (upid[i].checked) {
			selid =  i;         
		}
	};
	if (selid+1 == radioLength-1) {
		$("#commit_button").css("background-color","#5cb85c");
		document.getElementById("upArrow").disabled = false;
		$("#upArrow").css("color","#5cb85c");
		document.getElementById("dwnArrow").disabled = true;
		$("#dwnArrow").css("color","#bbb5b5");
	} else {
		document.getElementById("dwnArrow").disabled = false;
		$("#commit_button").css("background-color","#5cb85c");
		document.getElementById("upArrow").disabled = false;
	}

	if (selid < radioLength-1){
		exchange(selid,selid+1,'swaptable1');
		document.getElementsByName('hdnNewOrderid')[(selid+1)].value = document.getElementsByName('hdnNewOrderid')[selid].value;
		document.getElementsByName('hdnNewOrderid')[selid].value = (document.getElementsByName('hdnNewOrderid')[(selid+1)].value) - 1;
		document.getElementById('upArrow').disabled=false;
		if (selid == radioLength-2){
			// enable_disable_arrow('upArrow','dwnArrow');
			document.getElementById('dwnArrow').disabled=true;
		}
	} else {   
		return false;
	}
}

// setting UpArrow Process
function getupdata(){
	Commit_buttonenable();
	var upid = document.getElementsByName("rdoedit");
	var radioLength = upid.length;
	$("#commit_button").css("background-color","#5cb85c");
	document.getElementById('dwnArrow').disabled=false;
	$("#dwnArrow").css("color","#5cb85c");
	for(var i = 0; i <radioLength; i++) {
		if (upid[i].checked) {
			selid =  i;
		}
	}
	var checkid = selid+1;
	if (selid > 0){
		exchange(selid,selid-1,'swaptable1');
		document.getElementsByName('hdnNewOrderid')[(selid-1)].value = document.getElementsByName('hdnNewOrderid')[selid].value;
		document.getElementsByName('hdnNewOrderid')[selid].value = (document.getElementsByName('hdnNewOrderid')[(selid-1)].value)-(-1) ;
		if (selid ==1){
			document.getElementById('upArrow').disabled=true;
			$("#upArrow").css("color","#bbb5b5");
			$("#commit_button").css("background-color","#5cb85c");
		}
		if (selid != radioLength) {
			document.getElementById('dwnArrow').disabled=false;
			$("#dwnArrow").css("color","#5cb85c");
		}
	}else { 
		return false;
	}
}

function Commit_buttonenable() {
	document.getElementById("commit_button").disabled = false;
}

function exchange(i, j,tableID){
	var oTable = document.getElementById(tableID);
	var trs = oTable.tBodies[0].getElementsByTagName("tr");
	if (i >= 0 && j >= 0 && i < trs.length && j < trs.length)
	{
		if (i == j+1) {
			oTable.tBodies[0].insertBefore(trs[i], trs[j]);         
		} else if (j == i+1) {
			oTable.tBodies[0].insertBefore(trs[j], trs[i]);
		} else {
			var tmpNode = oTable.tBodies[0].replaceChild(trs[i], trs[j]);
			if (typeof(trs[i]) != "undefined") {
				oTable.tBodies[0].insertBefore(tmpNode, trs[i]);
			} else {
				oTable.appendChild(tmpNode);
			}
		}
	}
	else {
		alert("Invalid Value")
	}
}

// for commit 
function getcommitCheck(tablename,screenname,tableselect) {
	var idnew = "";
	var actualId =  $('#idOriginalOrder').val();
	var id = $("[name=id]");
	var upid = $("[name=rdoedit]");
	var radioLength = upid.length;
	$('#process').val(4);
	for(var i = 0; i <radioLength; i++) {
		if (i == (radioLength-1)) {
			idnew += id[i].value;
		} else {
			idnew += id[i].value+',';
		}   
	}

	if ( confirm("Do You Want To Commit ?")) {
		 fnsettingcommitajax(actualId,idnew,tablename,screenname,tableselect);
	}
} 

function fnsettingcommitajax(actualId,idnew,tablename,screenname,tableselect){
    pageload();
	
	$.ajax({
		async: true,
		type: 'GET',
		url: 'commitProcess',
		data: {"actualId": actualId,"idnew": idnew,"tablename": tablename},
		success: function(data) {
			$('#frmaccountingindex').submit();
		},
		error: function(data) {
			 alert(data.status);
		}
	});
}

function GetInvoicepopup() {
	var mainmenu = $('#mainmenu').val();
	var invoiceDate = $('#accDate').val();
	if (invoiceDate != "") {
		popupopenclose(1);
		$('#getinvoicepopup').load('../Accounting/getInvoicePopup?mainmenu='+mainmenu+'&time='+datetime+'&invoiceDate='+encodeURIComponent(invoiceDate));
		$("#getinvoicepopup").modal({
			backdrop: 'static',
			keyboard: false
			});
		$('#getinvoicepopup').modal('show');
	} else {
		alert("Please select Date field");
	}
}

function GetExpensespopup(bankIdAccNo) {
	var mainmenu = $('#mainmenu').val();
	var expensesDate = $('#accDate').val();
	if (expensesDate != "") {
		popupopenclose(1);
		$('#getExpensespopup').load('../Accounting/getExpensespopup?mainmenu='+mainmenu+'&time='+datetime+'&expensesDate='+encodeURIComponent(expensesDate)+'&bankIdAccNo='+bankIdAccNo);
		$("#getExpensespopup").modal({
			backdrop: 'static',
			keyboard: false
		});
		$('#getExpensespopup').modal('show');
	} else {
		alert("Please select Date field");
	}
}

function fnGetExpDtls(bankIdAccNo){
	GetExpensespopup(bankIdAccNo);
}

function salAllCheck() {
	var salaryAllCheck = $('input[class=salaryAllCheck]:not(:checked)').val();
	if (salaryAllCheck == undefined) {
		$('.salarychk').prop("checked",true);
	} else {
		$('.salarychk').prop("checked",false);
	}
}

function invAllCheck() {
	var invoiceAllCheck = $('input[class=invoiceAllCheck]:not(:checked)').val();
	if (invoiceAllCheck == undefined) {
		$('.invoicechk').prop("checked",true);
	} else {
		$('.invoicechk').prop("checked",false);
	}
}
function loaAllCheck() {
	var loanAllCheck = $('input[class=loanAllCheck]:not(:checked)').val();
	if (loanAllCheck == undefined) {
		$('.loanchk').prop("checked",true);
	} else {
		$('.loanchk').prop("checked",false);
	}
}

function expensesDataAllCheck() {
	var expensesAllCheck = $('input[class=expensesAllCheck]:not(:checked)').val();
	if (expensesAllCheck == undefined) {
		$('.expensesDatachk').prop("checked",true);
	} else {
		$('.expensesDatachk').prop("checked",false);
	}

}
function bankViewlist(bnkname,branchname,accno,startdate,bank_id,branchid) {
	pageload();
	$('#accNo').val(accno);
	$('#bankid').val(bank_id);
	$('#bankname').val(bnkname);
	$('#branchname').val(branchname);
	$('#accno').val(accno);
	$('#startdate').val(startdate);
	$('#bankids').val(bankid);
	$('#branchids').val(branchid);
	$('#frmaccountingindex').attr('action', '../AccBankDetail/Viewlist?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmaccountingindex").submit();
}
function fngetValue(val,flg,loopVal) {
	var loopVal = loopVal.charAt(loopVal.length-1);
	if (flg == 1) {
		var loanAmt = val;
		var loanFee = $('#loanFee'+loopVal).val();
	} else {
		var loanAmt = $('#loanAmt'+loopVal).val();
		var loanFee = val;
	}
	var amount = loanAmt.replace(",","");
	var fee = loanFee.replace(",","");
	if (amount == "") {
		amount = 0;
	}
	if (fee == "") {
		fee = 0;
	}
	var total =  parseInt(amount) + parseInt(fee);
	$('#totalAmt'+loopVal).val(total);
}

function completedflg(bankId,AccNo,completedFlg){
	if(confirm("Do You Want to Change the Flg?")) {
		$('#bankNo').val(bankId);
		$('#accNo').val(AccNo);
		$('#completedFlg').val(completedFlg);
		$('#frmaccountingindex').attr('action', 'completedflg?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmaccountingindex").submit();
	}
}

function accExlwnld(){
	if(confirm("Do You Want to Download the Excel?")) {
		var mainmenu = $('#mainmenu').val();
		$('#frmaccountingindex').attr('action', 'accExlwnld?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmaccountingindex").submit();
	}
}

function empNameclick(empId){ 
	pageload();
	$('#empId').val(empId);
	$('#loanId').val("");
	$('#contentId').val("");
	$('#plimit').val(50);
	$('#page').val('');
	$("#searchmethod").val(3);
	$('#frmaccountingindex').attr('action','index'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmaccountingindex").submit();
}

function loanNameclick(loanId){ 
	pageload();
	$('#empId').val("");
	$('#loanId').val(loanId);
	$('#contentId').val("");
	$('#plimit').val(50);
	$('#page').val('');
	$("#searchmethod").val(3);
	$('#frmaccountingindex').attr('action','index'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmaccountingindex").submit();
}

function contentclick(contentId){ 
	pageload();
	$('#empId').val("");
	$('#loanId').val("");
	$('#contentId').val(contentId);
	$('#plimit').val(50);
	$('#page').val('');
	$("#searchmethod").val(3);
	$('#frmaccountingindex').attr('action','index'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmaccountingindex").submit();
}