$(document).ready(function() {
	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.editothersprocess').click(function () {
		$("#editothers").validate({
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
				txt_startdate: {required: true},
				amount_1: {required: true,money: true},
				bankname: {required: true},
			},
			submitHandler: function(form) { // for demo
				if($('#editflg').val() == "edit") {
					var confirmprocess = confirm("Do You Want To Update?");
				} else {
					var confirmprocess = confirm("Do You Want To Register?");
				}
				if(confirmprocess) {
					pageload();
					form.submit();
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
	$('.addeditprocess').click(function () {
		$("#transferaddedit").validate({
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
				txt_startdate: {required: true,date: true,correctformatdate: true, accessDateCheck: "#accessdate"},
				mainsubject: {required: true},
				bankname: {required: true},
				amount_1: {required: true,money: true},
				charge_1: {required: true,money: true},
				file1 : {extension: "jpg,jpeg,png,JPG,JPEG,PNG", filesize : (2 * 1024 * 1024)},
			},
			submitHandler: function(form) { // for demo
				if($('#editflg').val() == "1" || $('#editflg').val() == "3") {
					var confirmprocess = confirm("Do You Want To Register?");
				} else {
					var confirmprocess = confirm("Do You Want To Update?");
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
	$('.loanaddeditprocess').click(function () {
		$("#loanaddedit").validate({
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
				bankname: {required: true},
				loanname: {required: true},
				loantype: {required: true},
				txt_startdate: {required: true,date: true,correctformatdate: true, accessDateCheck: "#accessdate"},
				amount: {required: true,money: true},
				interest: {required: true},
			},
			submitHandler: function(form) { // for demo
				if($('#editflg').val() == "1" || $('#editflg').val() == "3") {
					var confirmprocess = confirm("Do You Want To Register?");
				} else {
					var confirmprocess = confirm("Do You Want To Update?");
				}
				if(confirmprocess) {
					// alert("Under Construction");
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
	});
	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.multiregprocess').click(function () {
		$("#frmmultireg").validate({
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
				txt_startdate: {required: true,date: true,correctformatdate: true},
				amount: {required: true,numberwithcomma: "[0-9,]+"},
				charge: {required: true,numberwithcomma: "[0-9,]+"},
			},
			submitHandler: function(form) { // for demo
				if($('#editflg').val() == "1" || $('#editflg').val() == "3") {
					var confirmprocess = confirm("Do You Want To Register?");
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
		$('[name*="charge"],[name*="amount"]').each(function () {
			$(this).rules('add', {
				required: true,
				messages: {
					required: "Please Enter The Value"
				}
			});
		});
		$.validator.messages.required = function (param, input) {
			var article = document.getElementById(input.id);
			return article.dataset.label + err_fieldreq;
		}
	});
});

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
		$('#selMonth').val(("0" + month).substr(-2));
		$('#selYear').val(year);
		$('#prevcnt').val(prevcnt);
		$('#nextcnt').val(nextcnt);
		$('#account_val').val(account_val);
		$('#page').val('');
		$('#plimit').val('');
		$('#transferindex').submit();
	}
}
function gotoindexexpenses2(yr,mnth,mainmenu,limit,page,flg) {
	pageload();
	if(mainmenu == 'expdetails') {
		$('#page').val('');
		$('#plimit').val('');
		$('#transferhistory').attr('action', '../ExpensesDetails/index?mainmenu='+mainmenu+'&time='+datetime);
		$("#transferhistory").submit();
	} else if($('#backflg').val() == "4") {
		$('#page').val('');
		$('#plimit').val('');
		$('#selMonth').val(mnth);
		$('#selYear').val(yr);
		$('#transferhistory').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#transferhistory").submit();
	} else {
		$('#page').val(page);
		$('#plimit').val(limit);
		$('#selMonth').val(mnth);
		$('#selYear').val(yr);
		$('#transferhistory').attr('action', '../Expenses/index?mainmenu='+mainmenu+'&time='+datetime);
		$("#transferhistory").submit();
	}
}
function gotoindexexpenses1(yr,mnth,mainmenu,limit,page) {
	pageload();
	if(mainmenu === 'expdetails') {
		$('#page').val('');
		$('#plimit').val('');
		$('#subhistory').attr('action', '../ExpensesDetails/index?mainmenu='+mainmenu+'&time='+datetime);
		$("#subhistory").submit();
	} else if($('#backflg').val() == "4") {
		$('#page').val('');
		$('#plimit').val('');
		$('#selMonth').val(mnth);
		$('#selYear').val(yr);
		$('#subhistory').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#subhistory").submit();
	} else {
		$('#page').val(page);
		$('#plimit').val(limit);
		$('#selMonth').val(mnth);
		$('#selYear').val(yr);
		$('#subhistory').attr('action', '../Expenses/index?mainmenu='+mainmenu+'&time='+datetime);
		$("#subhistory").submit();
	}
}
function gotoindexexpensesname(yr,mnth,mainmenu) {
	pageload();
	$('#page').val('');
	$('#plimit').val('');
	if($('#backflg').val() == "4") {
		$('#selMonth').val(mnth);
		$('#selYear').val(yr);
		$('#salaryhistory').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#salaryhistory").submit();
	} else {
		$('#selMonth').val(mnth);
		$('#selYear').val(yr);
		$('#salaryhistory').attr('action', '../Expenses/index?mainmenu='+mainmenu+'&time='+datetime);
		$("#salaryhistory").submit();
	}
}
function transferhistory(empno,name,bankid,bankaccno,mainmenu,flg,month,year) {
	pageload();
	$('#month').val(month);
	$('#year').val(year);
	$('#bname').val(bankid);
	$('#accNo').val(bankaccno);
	$('#empid').val(empno);
	$('#empname').val(name);
	$('#mainmenu').val(mainmenu);
	$('#backflg').val(flg);
	$('#transferindex').attr('action', 'empnamehistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#transferindex").submit();
}
function gotoexp_history(subject,salflg,bankid,bankaccno,mainmenu,flg) {
	pageload();
	$('#month').val($('#selMonth').val());
	$('#year').val($('#selYear').val());
	$('#subject').val(subject);
	$('#salaryflg').val('');
	$('#bname').val(bankid);
	$('#accNo').val(bankaccno);
	$('#flgs').val('1');
	$('#selMonth').val('');
	$('#selYear').val('');
	$('#backflg').val(flg);
	$('#mainmenu').val(mainmenu);
	$('#transferindex').attr('action', 'transferhistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#transferindex").submit();
}
function gotoCash(bname,accno,nickname,transflg,mainmenu,month,yr) {
	pageload();
	$('#bname').val(bname);
	$('#accNo').val(accno);
	$('#bankNamen').val(nickname);
	$('#month').val(month);
	$('#trans_flg').val(transflg);
	$('#year').val(yr);
	$('#sub_type').val('banksub');
	$('#mainmenu').val(mainmenu);
	$('#selMonth').val('');
	$('#selYear').val('');
	$('#delflg').val('');
	$('#transferindex').attr('action', '../Expenses/expenseshistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#transferindex").submit();
}
function gototransferhistory(subject,loanflg,mainmenu,flg,month,year) {
	pageload();
	$('#month').val(month);
	$('#year').val(year);
	$('#loan_flg').val(loanflg);
	$('#subject').val(subject);
	$('#mainmenu').val(mainmenu);
	$('#selMonth').val('');
	$('#selYear').val('');
	$('#flgs').val('5');
	$('#backflg').val(flg);
	$('#transferindex').attr('action', 'transferhistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#transferindex").submit();
}
function gotomainexpenseshistory(id,maincat,mainmenu,flg) {
	pageload();
	var month = $('#selMonth').val();
	var year = $('#selYear').val();
	$('#bname').val(maincat);
	$('#subject').val(id);
	$('#mainmenu').val(mainmenu);
	$('#month').val(month);
	$('#year').val(year);
	$('#selMonth').val('');
	$('#selYear').val('');
	$('#subject_type').val('main_subject');
	$('#exptype1').val('2');
	$('#backflg').val(flg);
	$('#transferindex').attr('action', 'transferhistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#transferindex").submit();
}
function gotosubexphistory(id,subid,subcat,maincat,mainmenu) {
	pageload();
	var month = $('#selMonth').val();
	var year = $('#selYear').val();
	$('#month').val(month);
	$('#year').val(year);
	$('#subject').val(subid);
	$('#subcat').val(subcat);
	$('#mainmenu').val(mainmenu);
	$('#selMonth').val('');
	$('#selYear').val('');
	$('#subject_type').val('sub_subject');
	$('#exptype1').val('2');
	$('#page').val('');
	$('#plimit').val('');
	$('#transferindex').attr('action', 'transfersubhistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#transferindex").submit();
}
function gototransfersubhistory(details,subject,detail,salaryflg,bankid,accno,mainmenu,flg,transaction_flg) {
	if(salaryflg == "1") {
		pageload();
		$('#salaryflg').val(salaryflg);
		$('#subject').val(details);
		$('#accNo').val(accno);
		$('#bname').val(bankid);
		$('#mainmenu').val(mainmenu);
		$('#selMonth').val('');
		$('#selYear').val('');
		$('#flgs').val('2');
		$('#backflg').val(flg);
		$('#transaction_flg').val(transaction_flg);
		$('#transferindex').attr('action', 'transferhistory?mainmenu='+mainmenu+'&time='+datetime);
		$("#transferindex").submit();
	}
	else if(transaction_flg == "1" || transaction_flg == "2" || transaction_flg == "3" ) {
		pageload();
		$('#salaryflg').val(salaryflg);
		$('#bname').val(details);
		$('#accNo').val(accno);
		$('#bname').val(bankid);
		$('#mainmenu').val(mainmenu);
		$('#selMonth').val('');
		$('#selYear').val('');
		$('#flgs').val('2');
		$('#backflg').val(flg);
		$('#transaction_flg').val(transaction_flg);
		$('#transferindex').attr('action', 'transfersubhistory?mainmenu='+mainmenu+'&time='+datetime);
		$("#transferindex").submit();
	}

	 else {
		pageload();
		$('#salaryflg').val(salaryflg);
		$('#subject').val(details);
		$('#accNo').val(accno);
		$('#bname').val(bankid);
		$('#mainmenu').val(mainmenu);
		$('#selMonth').val('');
		$('#selYear').val('');
		$('#flgs').val('2');
		$('#backflg').val(flg);
		$('#page').val('');
		$('#plimit').val('');
		$('#plimit').val('');
		$('#transaction_flg').val(transaction_flg);
		$('#transferindex').attr('action', 'transfersubhistory?mainmenu='+mainmenu+'&time='+datetime);
		$("#transferindex").submit();
	}
}
function underConstruction() { 
	alert("Under Construction");
}
function backhistorytoindex(mainmenu) {
	window.history.back();
}
function download(file,path) {
	// var confirm_download = "Do You Want To Download?";
	if(confirm(err_download)) {
		window.location.href="../app/Http/Common/downloadfile.php?file="+file+"&path="+path+"/";
	}
}
function pageClick(pageval) {
	$('#page').val(pageval);
	$("#transferindex").submit();
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$("#transferindex").submit();
}
function gotoadd(mainmenu,flg) {
	pageload();
	$('#editflg').val(flg);
	$('#transferindex').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#transferindex").submit();
}
function gotoindexpage(viewflg,mainmenu) {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
	if (viewflg == "1") {
	  pageload();
		$('#transferaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#transferaddeditcancel").submit();
	} else {
	  pageload();
		$('#transferaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#transferaddeditcancel").submit();
	}
}
function gotoindexpage_1(viewflg,mainmenu) {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
	if (viewflg == "1") {
	  pageload();
		$('#loanaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#loanaddeditcancel").submit();
	} else {
	  pageload();
		$('#loanaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#loanaddeditcancel").submit();
	}
}
function gotoindexpageback(viewflg,mainmenu) {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
	if (viewflg == "1") {
	  pageload();
		$('#transfermultiaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#transfermultiaddeditcancel").submit();
	} else {
	  pageload();
		$('#transfermultiaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#transfermultiaddeditcancel").submit();
	}
}
function fngetsubsubject(mainid,subid) {
	$('#subsubject').find('option').not(':first').remove();
	var lang = $('#lang').val();
	if(lang == "jp") {
		$.ajax({
			type:"GET",
			dataType: "JSON",
			url: 'ajaxsubsubject',
			data: {
				mainid: mainid
			},
			success: function(data){ // What to do if we succeed
				for (i = 0; i < data.length; i++) { 
					$('#subsubject').append( '<option value="'+data[i]["id"]+'">'+data[i]["sub_jap"]+'</option>' );
					// $('select[name="subsubject"]').val(mainid);
				}
				$('select[name="subsubject"]').val(subid);
			},
			error: function(xhr, textStatus, errorThrown){
			}  
		})
	} else {
		$.ajax({
			type:"GET",
			dataType: "JSON",
			url: 'ajaxsubsubject',
			data: {
				mainid: mainid
			},
			success: function(data){ // What to do if we succeed
				for (i = 0; i < data.length; i++) { 
					$('#subsubject').append( '<option value="'+data[i]["id"]+'">'+data[i]["sub_eng"]+'</option>' );
					// $('select[name="subsubject"]').val(mainid);
				}
				$('select[name="subsubject"]').val(subid);
			},
			error: function(xhr, textStatus, errorThrown){
			}  
		})
	}
}
function fngetloanname(mainid,subid) {
	$('#loanname').find('option').not(':first').remove();
	$.ajax({
		type:"GET",
		dataType: "JSON",
		url: 'ajaxloanname',
		data: {
			mainid: mainid
		},
		success: function(data){ // What to do if we succeed
			$("#empidd").text('');
			for (i = 0; i < data.length; i++) { 
				$('#loanname').append( '<option value="'+data[i]["loanNo"]+'">'+data[i]["loanName"]+'</option>' );
				// $('select[name="subsubject"]').val(mainid);
				$("#empidd").text(data[i]["loanNo"]);
				if (data.length == 1) {
					$('select[name="loanname"]').val(data[i]["loanNo"]);
					fnSetLoanNo('loanname');
				} else {
					$("#empidd").text('');
				}
				// $('select[name="loanname"]').val(subid);
			}
			if (subid != undefined) {
				$('select[name="loanname"]').val(subid);
				fnSetLoanNo('loanname');
			}
		},
		error: function(xhr, textStatus, errorThrown){
		}  
	})
}
function edit_view(j) {
	e=0;
	while(j>e){
		var salary = document.getElementById('salary_'+e).value;
		var transfercredit = document.getElementById('transfer_'+e).value;
		var x = document.getElementById('edt'+e);
		if (salary != 1 && transfercredit != 23) {
			if (x.style.display === 'none') {
				x.style.display = 'inline-block';
			} else {
				x.style.display = 'none';
			}
			$("#mul"+e).attr("style", "display:none");
			$("#add").attr("style", "display:none");
			$("#Chk_all").attr("style", "display:none");
			$("#edit_header").attr("style", "display:inline-block");

		}
		e++;
		// document.getElementById('edt'+e).style.width="10px;"
	}
}
function EditCashRecord(id,mainmenu) {
	pageload();
	$('#id').val(id);
	$('#cashflg').val('2');
	$('#transferindex').attr('action', '../Expenses/cashedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#transferindex").submit();
}
function multi_view(j) {
	e=0;
	while(j>e){
		var salary = document.getElementById('salary_'+e).value;
		if (salary != 1) {
			$("#mul"+e).attr("style", "display:inline-block");
			$("#edt"+e).attr("style", "display:none");
			$("#add").attr("style", "display:inline-block;text-decoration:none;");
			$("#Chk_all").attr("style", "display:inline-block");
			$("#edit_header").attr("style", "display:none");
		}
		// document.getElementById('edt'+e).style.width="10px;"
		e++;
	}
}
function checkAll(ele) {
	var checkboxes = document.getElementsByTagName('input');
	if (ele.checked) {
	document.getElementById('Chk_all').title = "Uncheck All";
		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].type == 'checkbox') {
				checkboxes[i].checked = true;
			}
		}
	} else {
		document.getElementById('Chk_all').title = "Check All";
		for(var i = 0; i < checkboxes.length; i++) {
			console.log(i)
			if(checkboxes[i].type == 'checkbox') {
				checkboxes[i].checked = false;
			}
		}
	}
}
function uncheckheader(val,id) {
	$('#mulid').val(id);
	document.getElementById("Chk_all").checked=false;
	document.getElementById('Chk_all').title = "Check All";
}
function transfer_addreg(mainmenu){
	var chk = false;
	$('input[type=checkbox]').each(function () {
		if ($(this).is(":checked")) {
			chk = true;
		}
	});
	if (chk == false) {
		alert("Please Select Any Checkbox");
	} else {
		$('#transferindex').attr('action', 'mulreg'+'?mainmenu='+mainmenu+'&time='+datetime); 
		$("#transferindex").submit();
	}
}
function EditbkrsRecord(id,delflg,mainmenu,flg) {
	if(delflg != 1){
		pageload();
		$('#id').val(id);
		$('#editflg').val(flg);
		$('#transferindex').attr('action', 'edit?mainmenu='+mainmenu+'&time='+datetime);
		$("#transferindex").submit();
	} else {
		pageload();
		$('#id').val(id);
		$('#editflg').val(flg);
		$('#transferindex').attr('action', 'loanedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#transferindex").submit();
	}
}
function EditRecord(id,mainmenu) {
	pageload();
		$('#id').val(id);
		$('#transferindex').attr('action', '../Expenses/edit?mainmenu='+mainmenu+'&time='+datetime);
		$("#transferindex").submit();
}
function copyCashRecord(id,mainmenu,date) {
	pageload();
	$('#id').val(id);
	$('#cashflg').val('3');
	$('#dateflg').val(date);
	$('#transferindex').attr('action', '../Expenses/cashedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#transferindex").submit();
}
function gotoexpensescopy(id,mainmenu,flg,date) {
	pageload();
	$('#id').val(id);
	$('#expcopyflg').val(flg);
	$('#dateflg').val(date);
	$('#transferindex').attr('action', 'copy'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#transferindex").submit();
}

function CopybkrsRecord(id,delflg,mainmenu,flg,date) {
	if(delflg != 1){
		pageload();
		$('#id').val(id);
		$('#editflg').val(flg);
		$('#dateflg').val(date);
		$('#transferindex').attr('action', 'edit?mainmenu='+mainmenu+'&time='+datetime);
		$("#transferindex").submit();
	} else if(loan_flg == 1) {
		pageload();
		$('#id').val(id);
		$('#editflg').val(flg);
		$('#dateflg').val(date);
		$('#transferindex').attr('action', 'loanedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#transferindex").submit();
	}else {
		pageload();
		$('#id').val(id);
		$('#editflg').val(flg);
		$('#dateflg').val(date);
		$('#transferindex').attr('action', 'loanedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#transferindex").submit();
	}
}
function gotoloanadd(mainmenu) {
	pageload();
	$('#txt_startdate').val('');
	$('#transferaddedit').attr('action', 'loanaddedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#transferaddedit").submit();
}
function gototransadd(mainmenu) {
	pageload();
	$('#txt_startdate').val('');
	$('#loanaddedit').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#loanaddedit").submit();
}
function filedownload(path,file) {
	var confirm_download = "Do You Want To Download?";
	if(confirm(confirm_download)) {
		window.location.href="../app/Http/Common/downloadfile.php?file="+file+"&path="+path+"/";
	}
}
function getdate() {
	$('#txt_startdate').val(dates);
}
function transfer_download(mainmenu) {
	$('#transferdownload').attr('action', 'download?mainmenu='+mainmenu+'&time='+datetime);
	$("#transferdownload").submit();
}
function fnSetLoanNo(id) {
	var getlnNo = document.getElementById(id);
	document.getElementById('empidd').innerHTML = getlnNo.value;
	document.getElementById('empidd').style.color = "#9C0000";
	document.getElementById('empidd').style.fontWeight = "bold";
}
function interestcheck(evt) { 
	if (!(evt.keyCode == 46 || (evt.keyCode >= 48 && evt.keyCode <= 57))) return false;
	var parts = evt.srcElement.value.split('.');
	var txtleng = $('#interest').val();
	if (parts == "" && evt.keyCode == 46) return false;
	if (evt.srcElement.value.length > 1 && !(evt.keyCode == 46) && parts[1] != '') 
		{ if (parts.length >= 2) {} else { return false }}; 
	if (parts.length > 2) return false;
	if (evt.keyCode == 46) return (parts.length == 1);
	if (parts[0].length > 3) return false;
	if (parts.length == 2 && parts[1].length > 1) return false;
}
function gotohistorydownload(mainmenu){
	$('#transferhistorydownload').attr('action', 'historydownload?mainmenu='+mainmenu+'&time='+datetime);
	$("#transferhistorydownload").submit();
}
function gotoempnamehistory(mainmenu){
	$('#frmdownloadindexsalary').attr('action', 'salaryhistorydownload?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmdownloadindexsalary").submit();
}
function gototransfersubhistory5(mainmenu) {
	$('#subhistorydownload').attr('action', 'transfersubhistorydownload?mainmenu='+mainmenu+'&time='+datetime);
	$("#subhistorydownload").submit();
}
function gotoempnamesubhistory(empno,empname,bankid,bankacc,mainmenu,flg) {
	pageload();
	$('#bname').val(bankid);
	$('#accNo').val(bankacc);
	$('#empid').val(empno);
	$('#empname').val(empname);
	$('#backflg').val(flg);
	$('#transferhistory').attr('action', 'empnamehistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#transferhistory").submit();
}
function fnRemoveZero(fname) {
	var getvalue = document.getElementById(fname);
	if (getvalue.value.trim() == 0) {
		getvalue.value = '';
		getvalue.focus();
		getvalue.select();
	}
}
function transferexceldownload(mainmenu, selectedyearmonth) {
	var confirm_create="Do you Want to Download Transfer details";
	if(confirm(confirm_create)) {
		$('#actionName').val("transfersexceldownload");
		$('#selYearMonth').val(selectedyearmonth);
		$('#frmtransferexceldownload').attr('action', 'transferexceldownload?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmtransferexceldownload").submit();
	}
}
function EditothersRecord(type,id,mainmenu){
	$('#editflg').val(type);
	$('#editid').val(id);
	$('#transferindex').attr('action', 'others?mainmenu='+mainmenu+'&time='+datetime);
	$("#transferindex").submit();
}
function gotoindexpageothers(viewflg,mainmenu){
	 if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
	if (viewflg == "1") {
		 pageload();
		$('#editotherscancel').attr('action', '../Transfer/index?mainmenu='+mainmenu+'&time='+datetime);
		$("#editotherscancel").submit();
	}else{
		 pageload();
		$('#editotherscancel').attr('action', '../Transfer/index?mainmenu='+mainmenu+'&time='+datetime);
		$("#editotherscancel").submit();
	}
}
function CopyothersRecord(id,mainmenu,date){
	$('#editid').val(id);
	$('#dateflg').val(date);
	$('#transferindex').attr('action', 'others?mainmenu='+mainmenu+'&time='+datetime);
	$("#transferindex").submit();
}
function popupenable(mainmenu) {
	popupopenclose(1);
	$('#empnamepopup').load('../Transfer/empnamepopup?mainmenu='+mainmenu+'&time='+datetime);
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

