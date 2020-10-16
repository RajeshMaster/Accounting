var data = {};
$(function () {
	var cc = 0;
	$('#paymentsort').click(function () {
		cc++;
		if (cc == 2) {
			$(this).change();
			cc = 0;
		}         
	}).change (function () {
		sortingfun();
		cc = -1;
	}); 
});
function underconstruction() {
	alert("underconstruction");
}
function getData(month, year, flg, prevcnt, nextcnt, account_period, lastyear, currentyear, account_val) {
	var yearmonth = year + "-" +  ("0" + month).substr(-2);
		
	var mainmenu = $('#mainmenu').val();
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
		$('#selMonth').val(("0" + month).substr(-2));
		$('#selYear').val(year);
		$('#prevcnt').val(prevcnt);
		$('#nextcnt').val(nextcnt);
		$('#account_val').val(account_val);
		$('#topclick').val('1');
		$('#sorting').val('');
		$('#frmpaymentindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$('#frmpaymentindex').submit();
	}
}
function sortingfun() {
	pageload();
    $('#plimit').val(100);
    $('#page').val('');
    var sortselect=$('#paymentsort').val();
    $('#sortOptn').val(sortselect);
    var alreadySelectedOptn=$('#sortOptn').val();
    var alreadySelectedOptnOrder=$('#sortOrder').val();
    if (sortselect == alreadySelectedOptn) {
        if (alreadySelectedOptnOrder == "asc") {
            $('#sortOrder').val('desc');
        } else {
            $('#sortOrder').val('asc');
        }
    }
    $("#frmpaymentindex").submit();
}
function pageClick(pageval) {
	$('#page').val(pageval);
	var mainmenu= $('#mainmenu').val();
	$('#frmpaymentindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmpaymentindex").submit();
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	var mainmenu= $('#mainmenu').val();
	$('#frmpaymentindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmpaymentindex").submit();
}
function fnbankaccountdetail(bankid) {
	var getbankval = document.getElementById(bankid);

	if (getbankval.value.trim() == "") {
		return false;
	}
	$.ajax({
        type:"GET",
        url: 'getaccount',
        data: {
            bank_id: getbankval.value
        },
        success: function(data){ // What to do if we succeed
           if (data != '' || data != '$$') {
				var split_dollar = data.split('$');
				if( bankid == "bankname_sel" ) {
					document.getElementById('account_type').innerText = split_dollar[0];
					document.getElementById('account_no').innerText = split_dollar[1];
					document.getElementById('account_holder').innerText = split_dollar[2];
				}	
					document.getElementById('acc_no').value = split_dollar[1];
					document.getElementById('bank_id').value = split_dollar[4];
					document.getElementById('bankbranch_id').value = split_dollar[5];
					document.getElementById('bankbranchname_sel').innerHTML = split_dollar[3];
			}
        },
        error: function(data){
        	alert(data.status);
			$("#regbutton").attr("data-dismiss","modal");
        }  
    });
}
function fnGetGrandTotal(name, fvalue, totalval1) {
	$("#copyAmount").attr("checked", false);
	$("#copyAmountbal").attr("disabled", false);
	$("#bank_charge").val(0);
	$("#copyAmountbal").attr("checked", false);
	var x = event.keyCode;
	if (x != 37 && x != 39 /*&& x != 8 && x != 46*/ && x != 36 && x != 9) {
		isformatMoney(name, fvalue,'jp');
		var deposit_amount = document.getElementById('deposit_amount');
		var bank_charge = document.getElementById('bank_charge');
		var grandtotal_disp = document.getElementById('grandtotal_disp');
		var hdn_totalval = document.getElementById('hdn_totalval');
		//var paytotalval = document.getElementById('paytotalval');
		var totalval = hdn_totalval.value.trim().replace(/[, ]+/g, "");

		var newvalue = 0;
		if (deposit_amount.value.trim().replace(/[, ]+/g, "") != "" && bank_charge.value.trim().replace(/[, ]+/g, "") == "") {
			newvalue = parseInt(deposit_amount.value.replace(/[, ]+/g, ""));
		} else if (deposit_amount.value.trim().replace(/[, ]+/g, "") == "" && bank_charge.value.trim().replace(/[, ]+/g, "") != "") {
			newvalue = parseInt(bank_charge.value.replace(/[, ]+/g, ""));
		} else if (deposit_amount.value.trim().replace(/[, ]+/g, "") == "" && bank_charge.value.trim().replace(/[, ]+/g, "") == "") {
			newvalue = "";
		} else {
			newvalue = parseInt(deposit_amount.value.trim().replace(/[, ]+/g, "")) + parseInt(bank_charge.value.trim().replace(/[, ]+/g, ""));
		}
		var totalval1 = $('#td_dispgrandtotal').html().trim().replace(/[,¥ ]+/g, "")
		if(newvalue==totalval1) {
			$("#copyAmount").prop("checked", true);
			$("#copyAmountbal").attr("disabled", true);
			$("#bank_charge").val(0);
			$("#copyAmountbal").attr("checked", true);
		}

		var disptot = (totalval-newvalue);
		grandtotal_disp.innerText = "¥ " + disptot.toLocaleString("ja-JP");
		if (disptot > 0) {
			grandtotal_disp.style.color = "red";
		} else {
			grandtotal_disp.style.color = "green";
		}
	}
}
function fnSetZero11(fid) {
	var getvalue = document.getElementById(fid);
	if (getvalue.value.trim() == "") {
		getvalue.value = 0;
	}
}
function fnRemoveZero(fname) {
	var getvalue = document.getElementById(fname);
	if (getvalue.value.trim() == 0) {
		getvalue.value = '';
		getvalue.focus();
		getvalue.select();
	}
}
function fnCopyAmount(copyAmount) {
	var deposit_amount = document.getElementById("deposit_amount");
	var bank_charge = document.getElementById('bank_charge');
	var grandtotal_disp = document.getElementById('grandtotal_disp');
	var hdn_totalval = document.getElementById("hdn_totalval").value.trim().replace(/[, ]+/g, "");
	var grandtotal_disp = document.getElementById('grandtotal_disp');

	if (copyAmount.checked) {
		$("#copyAmountbal").attr("checked", false);
		$("#copyAmountbal").attr("disabled", "disabled");
		$('#bank_charge').val('');
		deposit_amount.value = parseInt(hdn_totalval).toLocaleString("ja-JP");
	} else {
		$("#copyAmountbal").attr("disabled", false);
		deposit_amount.value = 0;
	}

	var newvalue = 0;
	if (deposit_amount.value.trim().replace(/[, ]+/g, "") != "" && bank_charge.value.trim().replace(/[, ]+/g, "") == "") {
		newvalue = parseInt(deposit_amount.value.replace(/[, ]+/g, ""));
	} else if (deposit_amount.value.trim().replace(/[, ]+/g, "") == "" && bank_charge.value.trim().replace(/[, ]+/g, "") != "") {
		newvalue = parseInt(bank_charge.value.replace(/[, ]+/g, ""));
	} else if (deposit_amount.value.trim().replace(/[, ]+/g, "") == "" && bank_charge.value.trim().replace(/[, ]+/g, "") == "") {
		newvalue = "";
	} else {
		newvalue = parseInt(deposit_amount.value.trim().replace(/[, ]+/g, "")) + parseInt(bank_charge.value.trim().replace(/[, ]+/g, ""));
	}

	var disptot = (hdn_totalval-newvalue);
	grandtotal_disp.innerText = "¥ " + disptot.toLocaleString("ja-JP");
	if (disptot > 0) {
		grandtotal_disp.style.color = "red";
	} else {
		grandtotal_disp.style.color = "green";
	}
}
function fnCanceVal(pos) {
	var div_totalval = document.getElementById('div_totalval' + pos);
	var div_totalval_value = div_totalval.innerText.trim().replace(/[, ]+/g, "");

	var div_grandtotalval = document.getElementById('div_grandtotalval');
	var div_grandtotalval_value = div_grandtotalval.innerText.trim().replace(/[, ]+/g, "");

	var hdn_totalval = document.getElementById('hdn_totalval');
	var td_dispgrandtotal = document.getElementById('td_dispgrandtotal');
	var grandtotal_disp = document.getElementById('grandtotal_disp');

	var deposit_amount = document.getElementById('deposit_amount');
	var bank_charge = document.getElementById('bank_charge');

	var btn_Add_edit = document.getElementById('update');
	
	var disptotal = div_grandtotalval_value - div_totalval_value;
	div_grandtotalval.innerText = disptotal.toLocaleString("ja-JP");
	div_totalval.innerText = "0";
	hdn_totalval.value = disptotal;
	td_dispgrandtotal.innerText = "¥ " + disptotal.toLocaleString("ja-JP");
	grandtotal_disp.innerText = "¥ " + disptotal.toLocaleString("ja-JP");
	if (disptotal > 0) {
		grandtotal_disp.style.color = "red";
		div_grandtotalval.style.color = "red";
		//btn_Add_edit.disabled = false;
	} else {
		grandtotal_disp.style.color = "green";
		div_grandtotalval.style.color = "green";
		//btn_Add_edit.disabled = true;
	}

	if (disptotal == 0) {
		// btn_Add_edit.disabled = true;
	} else {
		// btn_Add_edit.disabled = false;
	}
	deposit_amount.value = "";
	bank_charge.value = "";
}

function fnAddVal(pos) {
	var div_totalval = document.getElementById('div_totalval' + pos);
	var div_totalval_value = div_totalval.innerText.trim().replace(/[, ]+/g, "");

	var div_grandtotalval = document.getElementById('div_grandtotalval');
	var div_grandtotalval_value = div_grandtotalval.innerText.trim().replace(/[, ]+/g, "");

	var hdn_totalval = document.getElementById('hdn_totalval');
	var td_dispgrandtotal = document.getElementById('td_dispgrandtotal');
	var grandtotal_disp = document.getElementById('grandtotal_disp');

	var deposit_amount = document.getElementById('deposit_amount');
	var bank_charge = document.getElementById('bank_charge');

	var hdntotalval = document.getElementById('hdntotalval' + pos);
	var hdntotalval_value = hdntotalval.value.trim().replace(/[, ]+/g, "");

	var btn_Add_edit = document.getElementById('update');

	if (div_totalval_value == 0) {
		var disptotal = parseInt(hdntotalval_value) + parseInt(div_grandtotalval_value);
		div_grandtotalval.innerText = disptotal.toLocaleString("ja-JP");
		div_totalval.innerText = parseInt(hdntotalval_value).toLocaleString("ja-JP");
		hdn_totalval.value = disptotal;
		td_dispgrandtotal.innerText = "¥ " + disptotal.toLocaleString("ja-JP");
		grandtotal_disp.innerText = "¥ " + disptotal.toLocaleString("ja-JP");
		if (disptotal > 0) {
			grandtotal_disp.style.color = "red";
			div_grandtotalval.style.color = "red";
			//btn_Add_edit.disabled = false;
		} else {
			grandtotal_disp.style.color = "green";
			div_grandtotalval.style.color = "green";
			//btn_Add_edit.disabled = true;
		}

		if (disptotal == 0) {
			// btn_Add_edit.disabled = true;
		} else {
			// btn_Add_edit.disabled = false;
		}
		deposit_amount.value = "";
		bank_charge.value = "";
	}
}

function fnCheckboxVal1(pos) {
	if (pos != "nil") {
		var addcheck = document.getElementById('addcheck' + pos);
	}
	var addcheckname = document.getElementsByName('addcheck');
	var hidid = document.getElementsByName("hidid");
	var concadeid = "";

	for (i = 0; i < addcheckname.length; i++) {
		if (addcheckname[i].checked) {
			if (hidid[i].value.trim() != "") {
				concadeid += hidid[i].value.trim() + ",";
			}
		}
	}

	if (concadeid != "") {
		concadeid = concadeid.substr(0, (concadeid.length-1));
	}

	document.getElementById('hididconcade').value = concadeid;
	
	if (pos != "nil") {
		if (addcheck.checked) {
			fnAddVal(pos);
		} else {
			fnCanceVal(pos);
		}
	}
}
function fnPaymentRegistration(mode) {
	var payment_date = document.getElementById('payment_date');
	var totalval = document.getElementById('totalval');
	var deposit_amount = document.getElementById('deposit_amount');
	var bank_charge = document.getElementById('bank_charge');
	var dateformat = /^\d{4}-\d{2}-\d{2}$/;
	var accessdate = $('#accessdate').val();
	if (isEmpty(payment_date.value.trim())) {
		alert("Payment Date is Not Entered");
		payment_date.focus();
		return false;
	} else if(!payment_date.value.trim().match(dateformat)) {
		alert("Date Format Should Be YYYY-MM-DD");
		payment_date.focus();
		return false;
	} else if ((userclassification == 1) && (accessdate > payment_date.value)) {
		alert("Payment Date Must be Greater Than Access Date");
		payment_date.focus();
		return false;
	} else if (isEmpty(deposit_amount.value.trim()) || (deposit_amount.value.trim() == 0)) {
		alert("Payment Amount Missing");
		deposit_amount.focus();
		return false;
	} else {
		var msg = "";
		if (mode == 1) {
			msg = err_confreg;
		} else {
			msg = err_confup;
		}
		totalval.value = document.getElementById('grandtotal_disp').innerText.trim().replace('¥', '');
		if (confirm(msg)) {
			$('#frminvoiceaddedit').attr('action', 'paymentaddeditprocess?mainmenu='+mainmenu+'&time='+datetime);
			$('#frminvoiceaddedit').submit();
		} else {
			return false;
		}
	}
}
function fnCopyAmountbal(copyAmountbal) {
	var bank_charge = document.getElementById("bank_charge");
	var hdn_totalval = $('#grandtotal_disp').html().trim().replace(/[,¥ ]+/g, "");
	if (copyAmountbal.checked) {
		bank_charge.value = parseInt(hdn_totalval).toLocaleString("ja-JP");
		grandtotal_disp.innerText = "¥ 0";
	} else {
		grandtotal_disp.innerText = "¥ "+bank_charge.value;
		bank_charge.value = 0;
	}
}
function addedit(type,editid) {
	$('#frmpaymentindex').attr('action','../Estimation/addedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmpaymentindex").submit();
}
function fnpaymentadd(id,quot_date) {
	$('#estimate_id').val(id);
	$('#quot_date').val(quot_date);
	$('#frmpaymentindex').attr('action','../Invoice/paymentaddedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
    $("#frmpaymentindex").submit();
}
function fnPaymentEdit(type,estimate_id) {
	$('#type').val(type);
	$('#estimate_id').val(estimate_id);
	$('#frmpaymentindex').attr('action','../Payment/PaymentEdit'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmpaymentindex").submit();
}
function fncustomerview(cname) {
    $('#companyname').val(cname);
    $('#frmpaymentindex').attr('action','customerview'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmpaymentindex").submit();
}
function fnpaymentindex() {
	$('#frmcustomerview').attr('action','index'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmcustomerview").submit();
}
function fnpaymentind() {
	$('#frmpaymentindex').attr('action','index'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmpaymentindex").submit();
}
function fngotospecification(payid,invoiceid) {
	$('#payid').val(payid);
	$('#invoiceid').val(invoiceid);
	$('#frmpaymentindex').attr('action','customerspecification'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmpaymentindex").submit();
}
function fngotoinvoiceview(invid) {
    $('#invoiceid').val(invid);
    $('#backflg').val(1);
    $('#frmcustomerview').attr('action','../Invoice/specification'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmcustomerview").submit();
}
function fnpaymentedit(backflg) {
    var estid=$('#payid').val();
    $('#estimate_id').val(estid);
    $('#backflg').val(backflg);
    $('#frmpaymentindex').attr('action','PaymentEdit'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmpaymentindex").submit();
}