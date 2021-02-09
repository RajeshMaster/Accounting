$(document).ready(function() {

	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.addeditprocess').click(function () {
		$("#frmaddEdit").validate({
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
				bankIdAccNo: {required: true},
				expensesDataAmount: {requiredWithZero: true},
				txt_empname: {Anyonerequired: "#expensesDataContent"},
				expensesDataContent: {Anyonerequired: "#txt_empname"},
				expensesDataBill : {extension: "jpg,jpeg,png,JPG,JPEG,PNG", filesize : (2 * 1024 * 1024)},
			},
			submitHandler: function(form) { // for demo
				if($('#edit_flg').val() != 1) { 
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

function intdisplaymessage() {
	document.getElementById('errorSectiondisplay').style.display='none';
}

function addedit(page,mainmenu) {
	$('#edit_flg').val('');
 	$('#frmexpensesDataindex').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmexpensesDataindex").submit();
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
function editExpData(id, editflg) {
	$('#editId').val(id);
	$('#edit_flg').val(editflg);
	$('#frmexpensesDataindex').attr('action', 'expensesDataEdit?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmexpensesDataindex").submit();
}

function popupenable() {
	var mainmenu = $('#mainmenu').val();
	popupopenclose(1);
	$('#empnamepopup').load('../ExpensesData/empnamepopup?mainmenu='+mainmenu+'&time='+datetime);
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
		$("#expensesDataContent").attr("disabled", "disabled");
		$("#contentrequired").css("visibility", "hidden");
	} else {
		$("#expensesDataContent").removeAttr("disabled");
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
		$("#expensesDataContent").attr("disabled", "disabled");
		$("#contentrequired").css("visibility", "hidden");
	} else {
		$("#expensesDataContent").removeAttr("disabled");
		$("#contentrequired").css("visibility", "visible");
	}
	$('#empnamepopup').modal('toggle');
}

function gotoindexpage(mainmenu) {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
	$('#addeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#addeditcancel").submit();
}

function disabledemp(){
	if ($('#expensesDataContent').val() != "") {
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
	$("#expensesDataContent").removeAttr("disabled");
	$("#contentrequired").css("visibility", "visible");
}

function changeOrderpopUp(bankId,AccNo){
	$('#bankNo').val('');
	$('#accNo').val('');
	$('#bankNo').val(bankId);
	$('#accNo').val(AccNo);
	var mainmenu = $('#mainmenu').val();
	popupopenclose(1);
	$('#getExpDataDetails').load('../ExpensesData/getExpDataDetails?mainmenu='+mainmenu+'&time='+datetime+'&bankId='+bankId+'&AccNo='+AccNo);
	$("#getExpDataDetails").modal({
		backdrop: 'static',
		keyboard: false
		});
	$('#getExpDataDetails').modal('show');
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
			$('#frmexpensesDataindex').submit();
		},
		error: function(data) {
			 alert(data.status);
		}
	});
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
	$('#frmexpensesDataindex').attr('action', '../AccBankDetail/Viewlist?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmexpensesDataindex").submit();
}

function changeDelFlg(editId,delFlg){
	if(confirm("Do You Want to Change the Flg?")) {
		$('#editId').val(editId);
		$('#delFlg').val(delFlg);
		$('#frmexpensesDataindex').attr('action', 'changeDelFlg?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmexpensesDataindex").submit();
	}
}