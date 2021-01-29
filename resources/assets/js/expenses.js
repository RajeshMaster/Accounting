var sum = 0;
$(document).ready(function() {
	var cc = 0;
	$('#usersort').click(function () {
		cc++;
		if (cc == 2) {
			$(this).change();
			cc = 0;
		}         
	}).change (function () {
		sortingfun();
		cc = -1;
	}); 
	// MOVE SORTING
	var ccd = 0;
	$('#sidedesignselector').click(function () {
		if( $('#searchmethod').val() == 1 || $('#searchmethod').val() == 2) {
			ccd++;
		}
		if (ccd % 2 == 0) {
			movediv = "+=220px"
		} else {
			movediv = "-=220px"
		}
		$('#usersort').animate({
			'marginRight' : movediv //moves down
		});
		ccd++;
		if( $('#searchmethod').val() == 1 || $('#searchmethod').val() == 2){
			ccd--;
		}  
	});
	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.addeditprocess').click(function () {
		$("#frmexpenseaddedit").validate({
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
				date: {required: true, date: true,minlength:10,correctformatdate: true, accessDateCheck: "#accessdate"},
				mainsubject: {required: true},
				subsubject: {required: true},
				amount: {requiredWithZero: true,money: true},
				file1 : {extension: "jpg,jpeg,png,JPG,JPEG,PNG", filesize : (2 * 1024 * 1024)},
			},
			submitHandler: function(form) { // for demo
				if($('#edit_flg').val() == 2 || $('#edit_flg').val() == 3) {
					// var confirmprocess = confirm("Do You Want To Register?");
					if(confirm(err_confreg)) {
						pageload();
						return true;
					} else {
						return false
					}
				} else {
					// var confirmprocess = confirm("Do You Want To Update?");
					if(confirm(err_confup)) {
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
		$.validator.messages.minlength = function (param, input) {
			var article = document.getElementById(input.id);
			return "Please Enter valid 10 Number";
		}
	});
	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.cashaddeditprocess').click(function () {
		$("#frmcashaddedit").validate({
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
				date: {required: true, date: true,minlength:10,correctformatdate: true, accessDateCheck: "#accessdate"},
				bank: {required: true},
				transfer: {required: true},
				transtype: {required: true},
				amount: {requiredWithZero: true},
			},
			submitHandler: function(form) { // for demo
				if($('#cashflg').val() == "2") {
					if(confirm(err_confup)) {
						pageload();
						return true;
					} else {
						return false
					}
				} else {
					if(confirm(err_confreg)) {
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
		$.validator.messages.minlength = function (param, input) {
			var article = document.getElementById(input.id);
			return "Please Enter valid 10 Number";
		}
	});
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
				date: {required: true, date: true,minlength:10,correctformatdate: true},
				cash: {required: true,money: true},
				expenses: {required: true,money: true},
			},
			submitHandler: function(form) { // for demo
					// var confirmprocess = confirm("Do You Want To Register?");
				if(confirm(err_confreg)) {
					pageload();
					return true;
				} else {
					return false
				}
			}
		});
		$('[name*="cash"],[name*="expenses"]').each(function () {
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
		$.validator.messages.minlength = function (param, input) {
			var article = document.getElementById(input.id);
			return "Please Enter valid 10 Number";
		}
	});
});

function underconstruction() {
	alert("Under Construction");
}
function fnCancel_check() {
	cancel_check = false;
	return cancel_check;
}
function gotoindexpettycash(yr,mnth,mainmenu,limit,page) {
	pageload();
	$('#page').val(page);
	$('#plimit').val(limit);
	$('#selMonth').val(mnth);
	$('#selYear').val(yr);
	$('#pettyhistory').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#pettyhistory").submit();
}
function gotoindexexpenses(yr,mnth,mainmenu,limit,page) {
	pageload();
	$('#page').val(page);
	$('#plimit').val(limit);
	$('#selMonth').val(mnth);
	$('#selYear').val(yr);
	if (mainmenu == "company_transfer") {
		$('#exphistory').attr('action', '../Transfer/index?mainmenu='+mainmenu+'&time='+datetime);
		$("#exphistory").submit();
	}
	else{
	$('#exphistory').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#exphistory").submit();
	}
}
function backhistorytoindex(flg,mainmenu) {
	if (flg == "1") {
		// $('#exphistory').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		// $("#exphistory").submit();
		window.history.back();
	}
}
function gotopettycash(subject,pettyflg,mainmenu,month,yr) {
	pageload();
	$('#hiddenplimit').val($('#plimit').val());
	$('#hiddenpage').val($('#page').val());
	$('#subject').val(subject);
	$('#mainmenu').val(mainmenu);
	$('#pettyflg').val(pettyflg);
	$('#month').val(month);
	$('#year').val(yr);
	$('#delflg').val('0');
	$('#type').val('4');
	$('#page').val('');
	$('#plimit').val('');
	$('#subject_type').val('bank_main_subject');
	$('#frmexpenseindex').attr('action', 'expenseshistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmexpenseindex").submit();
}
function gotoexpenses_history(bname,accno,nickname,mainmenu,month,yr) {
	pageload();
	$('#hiddenplimit').val($('#plimit').val());
	$('#hiddenpage').val($('#page').val());
	$('#bname').val(bname);
	$('#accNo').val(accno);
	$('#bankName').val(nickname);
	$('#mainmenu').val(mainmenu);
	$('#month').val(month);
	$('#year').val(yr);
	$('#trans_flg').val('');
	$('#selMonth').val('');
	$('#selYear').val('');
	$('#page').val('');
	$('#plimit').val('');	
	$('#subject_type').val('bank_main_subject');
	$('#frmexpenseindex').attr('action', 'expenseshistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmexpenseindex").submit();
}
function gotoexpenses1_history(bname,accno,nickname,transflg,mainmenu,month,yr) {
	pageload();
	$('#hiddenplimit').val($('#plimit').val());
	$('#hiddenpage').val($('#page').val());
	$('#bname').val(bname);
	$('#accNo').val(accno);
	$('#bankName').val(nickname);
	$('#mainmenu').val(mainmenu);
	$('#trans_flg').val(transflg);
	$('#month').val(month);
	$('#year').val(yr);
	$('#selMonth').val('');
	$('#selYear').val('');
	$('#page').val('');
	$('#plimit').val('');
	$('#subject_type').val('banksub');
	$('#frmexpenseindex').attr('action', 'expenseshistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmexpenseindex").submit();
}
function gotomainexpenseshistory(id,maincat,mainmenu,month,yr) {
	if(mainmenu == "expenses") {
		pageload();
		$('#hiddenplimit').val($('#plimit').val());
		$('#hiddenpage').val($('#page').val());
		$('#bname').val(maincat);
		$('#subject').val(id);
		$('#mainmenu').val(mainmenu);
		$('#month').val(month);
		$('#year').val(yr);
		$('#selMonth').val('');
		$('#selYear').val('');
		$('#subject_type').val('main_subject');
		$('#exptype1').val('2');
		$('#frmexpenseindex').attr('action', 'transferhistory?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmexpenseindex").submit();
	} else {
		pageload();
		$('#hiddenplimit').val($('#plimit').val());
		$('#hiddenpage').val($('#page').val());
		$('#bname').val(maincat);
		$('#subject').val(id);
		$('#mainmenu').val(mainmenu);
		$('#month').val(month);
		$('#year').val(yr);
		$('#selMonth').val('');
		$('#selYear').val('');
		$('#subject_type').val('main_subject');
		$('#exptype1').val('2');
		$('#page').val('');
		$('#plimit').val('');
		$('#frmexpenseindex').attr('action', 'pettycashhistory?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmexpenseindex").submit();
	}
}
function gotosubexphistory(id,subid,subcat,maincat,mainmenu,month,yr) {
	if(mainmenu == "expenses") {
		pageload();
		$('#hiddenplimit').val($('#plimit').val());
		$('#hiddenpage').val($('#page').val());
		$('#subject').val(subid);
		$('#mainmenu').val(mainmenu);
		$('#month').val(month);
		$('#year').val(yr);
		$('#selMonth').val('');
		$('#selYear').val('');
		$('#subject_type').val('sub_subject');
		$('#exptype1').val('2');
		$('#page').val('');
		$('#plimit').val('');
		$('#frmexpenseindex').attr('action', 'transfersubhistory?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmexpenseindex").submit();
	} else {
		pageload();
		$('#hiddenplimit').val($('#plimit').val());
		$('#hiddenpage').val($('#page').val());
		$('#detail').val(subid);
		$('#bname').val(maincat);
		$('#bankName').val(subcat);
		$('#mainmenu').val(mainmenu);
		$('#month').val(month);
		$('#year').val(yr);
		$('#selMonth').val('');
		$('#selYear').val('');
		$('#subject_type').val('sub_subject');
		$('#exptype1').val('2');
		$('#page').val('');
		$('#plimit').val('');
		$('#frmexpenseindex').attr('action', 'pettycashhistory?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmexpenseindex").submit();
	}
}
function gotopettycashsubhistoryexpenses(subject,pettyflg,delflg,mainmenu,month,yr) {
	pageload();
	$('#hiddenplimit').val($('#plimit').val());
	$('#hiddenpage').val($('#page').val());
	$('#subject').val(subject);
	$('#mainmenu').val(mainmenu);
	$('#pettyflg').val(pettyflg);
	$('#delflg').val(delflg);
	$('#month').val(month);
	$('#year').val(yr);
	$('#page').val('');
	$('#plimit').val('');
	$('#subject_type').val('bank_main_subject');
	$('#frmexpenseindex').attr('action', 'expenseshistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmexpenseindex").submit();
}
function gotoexpensestransferhistory(subject,salaryflg,mainmenu,month,yr) {
	pageload();
	$('#hiddenplimit').val($('#plimit').val());
	$('#hiddenpage').val($('#page').val());
	$('#salaryflg').val(salaryflg);
	$('#subject').val(subject);
	$('#mainmenu').val(mainmenu);
	$('#month').val(month);
	$('#year').val(yr);
	$('#selMonth').val('');
	$('#selYear').val('');
	$('#exptype1').val('1');
	$('#flgs').val('1');
	$('#page').val('');
	$('#plimit').val('');
	$('#frmexpenseindex').attr('action', 'transferhistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmexpenseindex").submit();
}
function gotosubhistory(subject_type,subject,details,detail,salaryflg,mainmenu,sub,month,yr) {
	if(subject_type == "expenses_history") {
		pageload();
		$('#hiddenplimit').val($('#plimit').val());
		$('#hiddenpage').val($('#page').val());
		$('#salaryflg').val(salaryflg);
		$('#subject').val(details);
		$('#detail').val(detail);
		$('#mainmenu').val(mainmenu);
		$('#month').val(month);
		$('#year').val(yr);
		$('#selMonth').val('');
		$('#selYear').val('');
		$('#page').val('');
		$('#plimit').val('');
		$('#frmexpenseindex').attr('action', 'transfersubhistory?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmexpenseindex").submit();
	} else {
		pageload();
		$('#hiddenplimit').val($('#plimit').val());
		$('#hiddenpage').val($('#page').val());
		$('#salaryflg').val(salaryflg);
		$('#subject').val(details);
		$('#detail').val(detail);
		$('#mainmenu').val(mainmenu);
		$('#month').val(month);
		$('#year').val(yr);
		$('#selMonth').val('');
		$('#selYear').val('');
		$('#type').val(sub);
		$('#page').val('');
		$('#plimit').val('');
		$('#frmexpenseindex').attr('action', 'transferhistory?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmexpenseindex").submit();
	}
}
function gotoempnamehistory(empno,name,bankid,bankaccno,mainmenu,month,yr) {
	pageload();
	$('#hiddenplimit').val($('#plimit').val());
	$('#hiddenpage').val($('#page').val());
	$('#bname').val(bankid);
	$('#accNo').val(bankaccno);
	$('#empid').val(empno);
	$('#empname').val(name);
	$('#mainmenu').val(mainmenu);
	$('#month').val(month);
	$('#year').val(yr);
	$('#exptype1').val('6');
	$('#page').val('');
	$('#plimit').val('');
	$('#frmexpenseindex').attr('action', 'empnamehistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmexpenseindex").submit();
}
function gotopettyhistory(subject,bank,mainmenu,month,yr) {
	pageload();
	$('#hiddenplimit').val($('#plimit').val());
	$('#hiddenpage').val($('#page').val());
	$('#subject').val(subject);
	$('#bname').val(bank);
	$('#subject_type').val('main_subject');
	$('#month').val(month);
	$('#year').val(yr);
	$('#selMonth').val('');
	$('#selYear').val('');
	$('#page').val('');
	$('#plimit').val('');
	$('#frmexpenseindex').attr('action', 'pettycashhistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmexpenseindex").submit();
}
function gotonamepettycash(bankvalue,bankaccno,bankname,delflg,mainmenu,month,yr) {
	pageload();
	$('#hiddenplimit').val($('#plimit').val());
	$('#hiddenpage').val($('#page').val());
	$('#delflg').val(delflg);
	$('#bname').val(bankvalue);
	$('#accNo').val(bankaccno);
	$('#detail').val(bankname);
	$('#month').val(month);
	$('#year').val(yr);
	$('#subject_type').val('bank_main_subject');
	$('#selMonth').val('');
	$('#selYear').val('');
	$('#page').val('');
	$('#plimit').val('');
	$('#frmexpenseindex').attr('action', 'pettycashhistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmexpenseindex").submit();
}
function gotopettycashsubhistory(subject,bankname,bankaccno,transflg,bankvalue,delflg,mainmenu,month,yr) {
	pageload();
	$('#hiddenplimit').val($('#plimit').val());
	$('#hiddenpage').val($('#page').val());
	$('#subject').val(subject);
	$('#bname').val(bankname);
	$('#accNo').val(bankaccno);
	$('#bankName').val(bankvalue);
	$('#mainmenu').val(mainmenu);
	$('#trans_flg').val(transflg);
	$('#delflg').val(delflg);
	$('#month').val(month);
	$('#year').val(yr);
	$('#selMonth').val('');
	$('#selYear').val('');
	$('#page').val('');
	$('#plimit').val('');
	$('#subject_type').val('bank_sub_subject');
	$('#frmexpenseindex').attr('action', 'pettycashhistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmexpenseindex").submit();
}
function gotopettycashsubhistory1(subject,details,detail,bank,mainmenu,month,yr) {
	pageload();
	$('#hiddenplimit').val($('#plimit').val());
	$('#hiddenpage').val($('#page').val());
	$('#subject').val(subject);
	$('#detail').val(details);
	$('#bname').val(bank);
	$('#accNo').val(detail);
	$('#mainmenu').val(mainmenu);
	$('#month').val(month);
	$('#year').val(yr);
	$('#selMonth').val('');
	$('#exptype1').val('2');
	$('#selYear').val('');
	$('#page').val('');
	$('#plimit').val('');
	$('#subject_type').val('sub_subject');
	$('#frmexpenseindex').attr('action', 'pettycashhistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmexpenseindex").submit();
}
function pageClick(pageval) {
	$('#page').val(pageval);
	if($('#bankName').val()!="" || $('#subject').val()!="" || $('#bname').val()!=""){
		$("#exphistory").submit();
	}else if($('#bankName').val()==""){
		$("#frmexpenseindex").submit();
	}
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	if($('#bankName').val()!="" || $('#subject').val()!="" || $('#bname').val()!=""){
		$("#exphistory").submit();
	}else if($('#bankName').val()==""){
		$("#frmexpenseindex").submit();
	}
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
		$('#frmexpenseindex').submit();
	}
}
function funsubmit(val) {
	if (confirm("Do You Want to Submit the Expenses")) {
		$('#submitflg').val(val);
		$('#frmexpenseindex').submit();
	} 
}
function fnrevert(val) {
	if (confirm("Do You Want to Revert the Expenses")) {
		$('#submitflg').val(val);
		$('#frmexpenseindex').submit();
	} 
}
function expenses_download(mainmenu) {
	$('#frmexpensesdownloadindex').attr('action', 'download?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmexpensesdownloadindex").submit();
}
function pettycash_download(mainmenu) {
	$('#frmexpensedownloadindex').attr('action', 'pettycashdownload?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmexpensedownloadindex").submit();
}
function pettycash_nodownload() {
	alert("There is no record to Download.");
}
function filedownload(path,file) {
	var confirm_download = "Do You Want To Download?";
    if(confirm(confirm_download)) {
        window.location.href="../app/Http/Common/downloadfile.php?file="+file+"&path="+path+"/";
    }
}
function EditCashRecord(id,mainmenu) {
	pageload();
	$('#id').val(id);
	$('#cashflg').val('2');
	$('#frmexpenseindex').attr('action', 'cashedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmexpenseindex").submit();
}
function copyCashRecord(id,mainmenu,date) {
	pageload();
	$('#id').val(id);
	$('#cashflg').val('3');
	$('#dateflg').val(date);
	$('#frmexpenseindex').attr('action', 'cashedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmexpenseindex").submit();
}
function copyCashRecordhistory(id,mainmenu,date) {
	pageload();
	$('#id').val(id);
	$('#cashflg').val('3');
	$('#dateflg').val(date);
	$('#exphistory').attr('action', 'cashedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#exphistory").submit();
}
function EditRecord(id,mainmenu) {
	pageload();
	$('#id').val(id);
	$('#frmexpenseindex').attr('action', 'edit'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmexpenseindex").submit();
}
function gotoexpcopy(id,mainmenu) {
	pageload();
	$('#id').val(id);
	$('#frmexpenseindex').attr('action', 'copyexp'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmexpenseindex").submit();
}
function edit_view(cal) {
    $('#registration').val('');
	e=0;
	while(cal>e){
		var x = document.getElementById('edt'+e);
		if (x.style.display === 'none') {
			x.style.display = 'inline-block';
		} else {
        	x.style.display = 'none';
    	}
		$("#mul"+e).attr("style", "display:none");
		$("#add").attr("style", "display:none");
		$("#Chk_all").attr("style", "display:none");
		$("#edit_header").attr("style", "display:inline-block");
	e++;
	}
}
function multi_view(i,transcount,flg){
	$('#whichprocess').val(flg);
	$('input:checkbox').attr('checked',false);
	$('#viewtotal').text("");
	sum=0;
	e=0;
	while(i>e) {
		document.getElementById('mul'+e).style.display="inline-block";
		document.getElementById('edt'+e).style.display="none";
		if((i-transcount)>0 && flg == 1){
			document.getElementById('add').style.display="inline-block";
		} else {
			document.getElementById('add').style.display="none";
		}
		if (flg == 1) {
			document.getElementById('Chk_all').style.display="inline-block";
			document.getElementById('edit_header').style.display="none";
		} else {
			document.getElementById('Chk_all').style.display="none";
			document.getElementById('edit_header').style.display="inline-block";
		}
		// document.getElementById('edt'+e).style.width="10px;"
		e++;
	}
}
function total(val) {
	$('#viewtotal').attr("value", "");
	$('input:checkbox').removeAttr('checked');
	e=1;
	while(val>e){
		$("#tot"+e).attr("style", "display:inline-block");
		$("#edt"+e).attr("style", "display:none");
		$("#mul"+e).attr("style", "display:none");
		// $("#mul"+e).attr("disabled", "disabled");
		$("#add").attr("style", "display:none");
		$("#Chk_all").attr("style", "display:none");
		$("#edit_header").attr("style", "display:inline-block");
		$("#viewtotal").attr("style", "display:inline-block");
		e++;
	}
}
function fnregister(mainmenu,flag) {
	pageload();
	$('#registration').val(1);
	$('#frmexpenseindex').attr('action', 'addedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
    $("#frmexpenseindex").submit();
}
function fngetsubsubject(mainid,subid) {
	var mainmenu = $('#mainmenu').val();
	var lang = $('#lang').val();
	if(lang == "jp") {
		$('#subsubject').find('option').not(':first').remove();
		$.ajax({
	        type:"GET",
	        dataType: "JSON",
	        url: 'ajaxsubsubject',
	        data: {
	            mainid: mainid,
	            mainmenu: mainmenu
	        },
	        success: function(data){ // What to do if we succeed
	           for (i = 0; i < data.length; i++)
	            { 
	                 $('#subsubject').append( '<option value="'+data[i]["id"]+'">'+data[i]["sub_jap"]+'</option>' );
	                 // $('select[name="subsubject"]').val(mainid);
	            }
	            $('select[name="subsubject"]').val(subid);
	        },
	        error: function(xhr, textStatus, errorThrown){
	        }  
	    })
	} else {
	    $('#subsubject').find('option').not(':first').remove();
		$.ajax({
	        type:"GET",
	        dataType: "JSON",
	        url: 'ajaxsubsubject',
	        data: {
	            mainid: mainid,
	            mainmenu: mainmenu
	        },
	        success: function(data){ // What to do if we succeed
	           for (i = 0; i < data.length; i++)
	            { 
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
function getselectedTexts(mainid,subid,selval,edit) {
	if(selval != "-" && edit != "" && edit != undefined && selval != undefined){
		$("#transfer").attr("style", "display:inline-block");
	}
    $('#transfer').find('option').not(':first').remove();
	$.ajax({
        type:"GET",
        dataType: "JSON",
        url: 'ajaxmainsubject',
        data: {
            mainid: mainid
        },
        success: function(data){ // What to do if we succeed
           for (i = 0; i < data.length; i++)
            { 
                 $('#transfer').append( '<option value="'+data[i]["BankName"]+'-'+data[i]["AccNo"]+'">'+data[i]["Bank_NickName"]+'-'+data[i]["AccNo"]+'</option>' );
                 // $('select[name="transfer"]').val(data[i]["banknameTransfer"]);
            }
            $('select[name="transfer"]').val(subid);
        },
        error: function(xhr, textStatus, errorThrown){
        }  
    })
}
function fnMoneyFormatwithINR(name) {
    var value = document.getElementById(name).value;
        fnMoneyFormatWithoutleadingzero(name, value, 'jp');
}
function fnMoneyFormatWithoutleadingzero(name, value,japmoney) {
  value = value.replace(/[ ]*,[ ]*|[ ]+/g, '');
  var x = event.keyCode;
  var passvalue = value;
  if ((value.length > 15) && (value.indexOf(',') == -1)) {
    passvalue = value.substr(0, 15);
  }
  passvalue = Number(passvalue).toString();
  //if (x != 37 && x != 39 && x != 8 && x != 46 && x != 36 && x != 9) {
    isformatMoneyINR(name, passvalue,japmoney);
  //}
  
}
function isformatMoneyINR(salaryname, salary,japmoney) {
    var salaryamt = inrFormat(salary,japmoney);
  if (salaryamt != 0) {
    document.getElementById(salaryname).value = salaryamt;
  }
  return true;
}
function isNumberFormat(evt) { 	
	var charCode = (evt.which) ? evt.which : event.keyCode
	var parts = evt.srcElement.value.split('-');
	if (charCode == 08) {
		return true;
	}
	if (charCode == 45 && parts[0].length == 0) {
		if(parts.length == 2)
			return false;
		return true;
	}
	if (charCode > 31 && (charCode < 48 || charCode > 57 ))
		return false;
}
function fnMoneyFormatcashadd(name, value) {
	var transflg = $("#transtype1").is(":checked");
	if (transflg) {
		$('#amount').attr('maxlength',11);
		transflg = $('#transtype1').val();
	} else {
		$('#amount').attr('maxlength',10);
		transflg = $('#transtype').val();
	}
	fnMoneyFormatWithMinus(name, value, transflg);
}
function fnMoneyFormatWithMinus(name, value, transflg) {
	var x = event.keyCode;
	var passvalue = value;
	if ((value.length > 15) && (value.indexOf(',') == -1)) {
		passvalue = value.substr(0, 15);
	}
	//if (x != 37 && x != 39 && x != 8 && x != 46 && x != 36 && x != 9) {
		isformatMoneyWithMinus(name, passvalue, transflg);
	//}
}
function isformatMoneyWithMinus(salaryname, salary, transflg) {
	var salaryamt = accounting.formatMoney(salary);
	if (salaryamt != 0) {
		if (transflg == 1) {
			$('#'+salaryname).val(salaryamt);
		} else {
			salaryamt = salaryamt.replace(/[- ]+/g, "");
			document.getElementById(salaryname).value = "-"+salaryamt;
		}
	}
	return true;
}
function inrFormat(nStr,japmoney) { // nStr is the input string
  nStr += '';
  x = nStr.split('.');
  x1 = x[0];
  x2 = x.length > 1 ? '.' + x[1] : '';
  var rgx = /(\d+)(\d{3})/;
  var z = 0;
  var len = String(x1).length;
  var num = parseInt((len/2)-1);

  while (rgx.test(x1))
  {
  if(z > 0)
  {
    x1 = x1.replace(rgx, '$1' + ',' + '$2');
  }
  else
  {
    x1 = x1.replace(rgx, '$1' + ',' + '$2');
    if(japmoney=="jp") {
      rgx = /(\d+)(\d{3})/;
    } else {
      rgx = /(\d+)(\d{2})/;
    }
  }
  z++;
  num--;
  if(num == 0)
  {
    break;
  }
  }
  return x1 + x2;
}
function gotoindextransfer(viewflg,mainmenu) {
	if (cancel_check == false) {
        if (!confirm("Do You Want To Cancel the Page?")) {
            return false;
        }
    }
    pageload();
		$('#expensesaddeditcancel').attr('action', '../Transfer/index?mainmenu='+mainmenu+'&time='+datetime);
		$("#expensesaddeditcancel").submit();
}
function gotoindex(viewflg,mainmenu) {
	if (cancel_check == false) {
        if (!confirm("Do You Want To Cancel the Page?")) {
            return false;
        }
    }
    pageload();
	$('#expensesaddeditcancel').attr('action', viewflg+'?mainmenu='+mainmenu+'&time='+datetime);
    $("#expensesaddeditcancel").submit();
}
function gotocashindextransfer(viewflg,mainmenu) {
	if (cancel_check == false) {
        if (!confirm("Do You Want To Cancel the Page?")) {
            return false;
        }
    }
    pageload();
		$('#cashaddeditcancel').attr('action', '../Transfer/index?mainmenu='+mainmenu+'&time='+datetime);
    	$("#cashaddeditcancel").submit();
}
function gotocashindex(viewflg,mainmenu) {
	if (cancel_check == false) {
        if (!confirm("Do You Want To Cancel the Page?")) {
            return false;
        }
    }
    pageload();
    	$('#cashaddeditcancel').attr('action', viewflg+'?mainmenu='+mainmenu+'&time='+datetime);
    	$("#cashaddeditcancel").submit();
}
function gotomultoindex(viewflg,mainmenu) {
	if (cancel_check == false) {
        if (!confirm("Do You Want To Cancel the Page?")) {
            return false;
        }
    }
    if(mainmenu=="expenses"){
    	pageload();
		$('#multiaddcancel').attr('action', '../Expenses/index?mainmenu='+mainmenu+'&time='+datetime);
		$("#multiaddcancel").submit();
    } else{
    	pageload();
		$('#multiaddcancel').attr('action', '../Transfer/index?mainmenu='+mainmenu+'&time='+datetime);
	    $("#multiaddcancel").submit();
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
	// $("#viewtotal").attr("style", "display:inline-block");
}
function checkfunction(id,item,val) {
	if ($('#whichprocess').val() == 2) {
		var result = val.replace(",", "");
		var result1 = result.replace(",", "");
		if(item.checked){
			var total=sum+= parseInt(result1);
		}else{
			var total=sum-= parseInt(result1);
		}
		totals = total.toLocaleString();
		$('#viewtotal').text(totals);
	}
}
function expensesaddreg(mainmenu) {
    var chk = false;
	$('input[type=checkbox]').each(function () {
        if ($(this).is(":checked")) {
            chk = true;
        }
    });
    if (chk == false) {
    	alert("Please Select any Checkbox");
    } else if (chk == true) {
		$('#frmexpenseindex').attr('action', 'multipleregister'+'?mainmenu='+mainmenu+'&time='+datetime); 
    	$("#frmexpenseindex").submit();
    } 
}
function gotocashadd(mainmenu,flg) {
	pageload();
	$('#cashflg').val(flg);
	$('#date').val('');
	$('#amount').val('');
	$('#remarks').val('');
	$('#frmgotocashadd').attr('action', 'cashaddedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmgotocashadd").submit();
}
function gotoexpensesadd(mainmenu) {
	pageload();
	$('#date').val('');
	$('#amount').val('');
	$('#remarks').val('');
	$('#frgotoexpensesadd').attr('action', 'addedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frgotoexpensesadd").submit();
}
function gotoexpensescopy(id,mainmenu,flg,date) {
	pageload();
	$('#id').val(id);
	$('#expcopyflg').val(flg);
	$('#dateflg').val(date);
	$('#frmexpenseindex').attr('action', 'copy'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmexpenseindex").submit();
}
function CopyRecordforexpenses(id,mainmenu,flg,date) {
	pageload();
	var mainmenu = 'expenses';
	$('#id').val(id);
	$('#expcopyflg').val(flg);
	$('#dateflg').val(date);
	$('#mainmenu').val('expenses');
	$('#transferhistory').attr('action', 'copy'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#transferhistory").submit();
}
function CopyRecordforexpensessub(id,mainmenu,flg,date) {
	pageload();
	var mainmenu = 'expenses';
	$('#id').val(id);
	$('#expcopyflg').val(flg);
	$('#dateflg').val(date);
	$('#mainmenu').val('expenses');
	$('#subhistory').attr('action', 'copy'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#subhistory").submit();
}
function CopybkrsRecordhistory(id,mainmenu) {
	pageload();
	$('#id').val(id);
	$('#editflg').val('3');
	$('#transferhistory').attr('action', '../Transfer/edit?mainmenu='+mainmenu+'&time='+datetime);
	$("#transferhistory").submit();
}
function CopybkrsRecord(id,mainmenu) {
	pageload();
	$('#id').val(id);
	$('#editflg').val('3');
	$('#transferhistory').attr('action', '../Transfer/loanedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#transferhistory").submit();
}
function CopybkrsRecordhistory1(id,mainmenu) {
	pageload();
	$('#id').val(id);
	$('#editflg').val('3');
	$('#subhistory').attr('action', '../Transfer/edit?mainmenu='+mainmenu+'&time='+datetime);
	$("#subhistory").submit();
}
function numberonly(e) {
  e=(window.event) ? event : e;
  return (/[0-9]/.test(String.fromCharCode(e.keyCode))); 
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
function fnRemoveZero(fname) {
	var getvalue = document.getElementById(fname);
	if (getvalue.value.trim() == 0) {
		getvalue.value = '';
		getvalue.focus();
		getvalue.select();
	}
}
function fnSetZero11(fid) {
	var getvalue = document.getElementById(fid);
	if (getvalue.value.trim() == "") {
		getvalue.value = 0;
	}
	return fnCancel_check();
}
function fnCancel_check() {
	cancel_check = false;
	return cancel_check;
}
function gotopettycashhistorydownload(mainmenu) {
	$('#frmdownloadindex').attr('action', 'pettycashmainhistory'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmdownloadindex").submit();
}
function gotopettycashhistorydownload1(mainmenu) {
	$('#frmdownloadindexes').attr('action', 'pettycashsubhistorydownload'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmdownloadindexes").submit();
}
function CopyBankRecord(id,mainmenu) {
	pageload();
	$('#id').val(id);
	$('#cashflg').val('3');
	$('#pettyhistory').attr('action', 'cashedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#pettyhistory").submit();
}
function CopyRecord(id,mainmenu) {
	pageload();
	$('#id').val(id);
	$('#pettyhistory').attr('action', 'copy'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#pettyhistory").submit();
}
function expensesexceldownload(mainmenu, selectedyearmonth) {
	var confirm_create="Do you Want to Download Transfer details";
    if(confirm(confirm_create)) {
    	$('#actionName').val("expensesexceldownload");
    	$('#selYearMonth').val(selectedyearmonth);
    	$('#frmexpensesexceldownload').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmexpensesexceldownload").submit();
    }
}
function gotoexpensesnamehistory(mainmenu) {
	if($('#subject_type').val() == "banksub") {
		$('#frmdownloadindex12').attr('action', 'expensessubhistorydownload'+'?mainmenu='+mainmenu+'&time='+datetime); 
		$("#frmdownloadindex12").submit();
	} else {
		$('#frmdownloadindex123').attr('action', 'expensesmainhistorydownload'+'?mainmenu='+mainmenu+'&time='+datetime); 
		$("#frmdownloadindex123").submit();
	}
}