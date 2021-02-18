var data = {};

function pageClick(pageval) {
	$('#page').val(pageval);
	$('#frmextinvoiceindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmextinvoiceindex").submit();
}

function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$('#frmextinvoiceindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmextinvoiceindex").submit();
}

$(document).ready(function() {

	// initialize tooltipster on text input elements
	// initialize validate plugin on the form

	$('.addeditprocess').click(function () {

		$("#frmextinvoiceaddedit").validate({

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

				quot_date: {required: true, date: true, accessDateCheck: "#accessdate"},
				payment_date: {required: true, date: true, greaterThan: "#quot_date"},
				userId: {required: true},
				projectName: {required: true},
				// projectType: {required: true},
				bankName: {required: true},
				branchName: {required: true},
				branchNo: {required: true},
				bankKanaName: {required: true},

			},

			submitHandler: function(form) { // for demo
				if($('#editid').val() == "") {
					var confirmprocess = confirm("Do You Want To Register?");
				} else {
					var confirmprocess = confirm("Do You Want To Update?");
				}
				if(confirmprocess) {
					pageload();
					form.submit(); // dont use this cause of double time insert in internet explorer
					return true;
				} else {
					return false;
				}
						
			}

		});
		$.validator.messages.required = function (param, input) {
			var article = document.getElementById(input.id);
			return article.dataset.label + ' field is required';
		}
		$('.a-middle').css('margin-top', function () {
			return ($(window).height() - $(this).height()) / 4
		});
	});
});

$(function () {
	var cc = 0;
	$('#invoicesort').click(function () {
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
			movediv = "+=260px"
		} else {
			movediv = "-=260px"
		}
		$('#invoicesort').animate({
			'marginRight' : movediv //moves down
		});
		ccd++;
		if( $('#searchmethod').val() == 1 || $('#searchmethod').val() == 2){
			ccd--;
		}  
	});

});


function intdisplaymessage() {
	document.getElementById('errorSectiondisplay').style.display='none';
}

function resetErrors() {

	$('form input, form select, form radio').removeClass('inputTxtError');
	$('label.error').remove();
}

function underConstruction() {
	alert("Under Construction");
}

function addedit(type,editid) {
	if(editid != "") {
		$('#editflg').val(type);
		$('#editid').val(editid);
	}
	pageload();
	$('#frmextinvoiceindex').attr('action','addedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmextinvoiceindex").submit();

}

function gotoindexpage() {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
	pageload();
	$('#frmextinvoiceaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmextinvoiceaddeditcancel").submit();
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
		$('#selMonth').val(("0" + month).substr(-2));
		$('#selYear').val(year);
		$('#prevcnt').val(prevcnt);
		$('#nextcnt').val(nextcnt);
		$('#account_val').val(account_val);
		$('#topclick').val('1');
		$('#sorting').val('');
		$('#frmextinvoiceindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$('#frmextinvoiceindex').submit();
	}
}

function filter(val){
	$("#filter").val(val);
	$('#plimit').val('');
	$('#page').val('');
	$('#sorting').val('');
	$("#searchmethod").val();
	$('#frmextinvoiceindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$('#frmextinvoiceindex').submit();
}

function sortingfun() {
	pageload();
	$('#plimit').val(50);
	$('#page').val('');
	var sortselect = $('#invoicesort').val();
	$('#sortOptn').val(sortselect);
	var alreadySelectedOptn = $('#sortOptn').val();
	var alreadySelectedOptnOrder = $('#sortOrder').val();
	if (sortselect == alreadySelectedOptn) {
		if (alreadySelectedOptnOrder == "asc") {
			$('#sortOrder').val('desc');
		} else {
			$('#sortOrder').val('asc');
		}
	}
	$('#checkdefault').val(1);
	$('#frmextinvoiceindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$('#frmextinvoiceindex').submit();
}

function clearsearch() {
	$('#plimit').val(50);
	$('#page').val('');
	$("#filterval").val('');
	$('#sortOrder').val('asc'); 
	$('#singlesearch').val('');
	$('#searchmethod').val('');
	$('#msearchusercode').val('');
	$('#msearchcustomer').val('');
	$('#msearchstdate').val('');
	$('#msearcheddate').val('');
	$('#protype1').val('');
	$('#protype2').val('');
	$('#checkdefault').val('');
	$('#frmextinvoiceindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$('#frmextinvoiceindex').submit();
}

function usinglesearch() {
	$('#plimit').val(50);
	$('#page').val('');
	$("#filterval").val('');
	$('#msearchusercode').val('');
	$('#msearchcustomer').val('');
	$('#msearchstdate').val('');
	$('#msearcheddate').val('');
	$('#userclassification').val('');
	var singlesearch = $("#singlesearch").val();
	if (singlesearch == "") {
		alert("Please Enter The Invoice Search.");
		$("#singlesearch").focus(); 
		return false;
	} else {
		$("#searchmethod").val('');
		$("#searchmethod").val(1);
	}
	$('#frmextinvoiceindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$('#frmextinvoiceindex').submit();
}

function umultiplesearch() {
	var msearchusercode = $("#msearchusercode").val();
	var msearchcustomer = $("#msearchcustomer").val();
	var msearchstdate = $("#msearchstdate").val();
	var msearcheddate = $("#msearcheddate").val();
	$('#plimit').val(50);
	$('#page').val('');
	$("#filterval").val('');
	$('#sortOrder').val('DESC'); 
	$('#singlesearch').val('');
	if (msearchusercode == "" &&  msearchcustomer == ""  && msearchstdate == "" && msearcheddate == "") {
		alert("Please Enter The Invoice Search.");
		$("#msearchusercode").focus();
		return false;
	} else {
		$("#searchmethod").val(2);
		$('#frmextinvoiceindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$('#frmextinvoiceindex').submit();
	}
}

function cloneaddblade() {
	var $button = $("#othercc_1").clone();
	var counter1 = document.getElementsByClassName("input_text");
	var counter1 = counter1.length+1;
	if (counter1 >50) {
		alert("Not Exceeding More Than 50 Rows!");
		return false;
	}
	$button.find("input:text").val("");
	var rowCount = $('#workspectable tr').length-1;
	$button.attr("id", "othercc_"+counter1);
	$button.find('#work_specific1').attr({id: "work_specific"+counter1,name: "work_specific"+counter1});
	$button.find('#quantity1').attr({id: "quantity"+counter1, name: "quantity"+counter1})
	.attr("onkeyup","fnCalculateAmount("+counter1+", '', '',"+rowCount+")")
	.attr("onfocus","return fnControlAddOrRemove("+counter1+")")
	.attr("onblur","return fnControlAddOrRemove("+counter1+")")
	.attr("ondragstart","return false").attr("ondrop","return false");
	$button.find('#unit_price1').attr({id: "unit_price"+counter1, name: "unit_price"+counter1})
	.attr("onkeyup","return fnCalculateAmount("+counter1+",unit_price"+counter1+",'',"+rowCount+")")
	.attr("onfocus","return fnControlAddOrRemove("+counter1+")").attr("onblur","return fnControlAddOrRemove("+counter1+")")
	.attr("ondragstart","return false").attr("ondrop","return false");
	$button.find('#amount1').attr({id: "amount"+counter1, name: "amount"+counter1});
	$button.find('#remarks1').attr({id: "remarks"+counter1, name: "remarks"+counter1})
	.attr("onfocus","return fnControlAddOrRemove("+counter1+")").attr("onblur","return fnControlAddOrRemove("+counter1+")")
	.attr("onkeyup","return fnControlAddOrRemove("+counter1+")");
	$button.find('#addrow1').attr({id: "addrow"+counter1,name: "addrow"}).attr("onclick","return fnAddTR("+counter1+")");
	$button.find('#removerow1').attr({id: "removerow"+counter1,name: "removerow"}).attr("onclick","return fnRemoveTR("+counter1+")");
	$button.find('#removeiconid_1').attr({id: "removeiconid_"+counter1, style: "display"});
	$button.find('#work_specific_hdn1').attr({id: "work_specific_hdn"+counter1});
	$button.find('#quantity_hdn1').attr({id: "quantity_hdn"+counter1});
	$button.find('#unit_price_hdn1').attr({id: "unit_price_hdn"+counter1});
	$button.find('#amount_hdn1').attr({id: "amount_hdn"+counter1});
	$button.find('#amountfif1').attr({id: "amountfif"+counter1});
	$button.find('#remarks_hdn1').attr({id: "remarks_hdn"+counter1});
	$button.find('#fordisable_hdn1').attr({id: "fordisable_hdn"+counter1});
	$button.find('#emp_ID1').attr({id: "emp_ID"+counter1,name:"emp_ID"+counter1,value:""});
	$button.find('#empKanaNames1').attr({id: "empKanaNames"+counter1,name:"empKanaNames"+counter1});
	$button.find('#crossid1').attr({id: "crossid"+counter1,name:"crossid"+counter1})
	.attr("onclick","return fngetEmpty('"+counter1+"')");
	$button.find('#divid1').hide().attr({id: "divid"+counter1,name:"divid"+counter1});
	$button.find('#emp').attr("onclick","return popupenableempname('invoice','"+counter1+"')");
	$("#forccappend").append($button);

	fnCalculateTotal(rowCount);
	fnControlAddOrRemove(counter1);
	return false;
}

function cloneremoveabove(thisattr) {
	if((thisattr.id)!="removeiconid_1"){
		var rowCount = $('#workspectable tr').length-1;
		var currentidsplit = thisattr.id.split('_');
		var currentid = currentidsplit[1];
		$button = $("#othercc_"+currentid).remove();
		var newattribute = currentid;
		for (var i = 1; i <= (rowCount-currentid); i++) {
			$('#othercc_'+(currentid-(-i))).attr({id: "othercc_"+newattribute});
			$('#work_specific'+(currentid-(-i))).attr({id: "work_specific"+newattribute,name: "work_specific"+newattribute});
			$('#quantity'+(currentid-(-i))).attr({id: "quantity"+newattribute,name: "quantity"+newattribute})
			.attr("onkeyup","fnCalculateAmount("+newattribute+", '', '',"+rowCount+")")
			.attr("onfocus","return fnControlAddOrRemove("+newattribute+")")
			.attr("onblur","return fnControlAddOrRemove("+newattribute+")")
			.attr("ondragstart","return false").attr("ondrop","return false");
			$('#unit_price'+(currentid-(-i))).attr({id: "unit_price"+newattribute,name: "unit_price"+newattribute})
			.attr("onkeyup","return fnCalculateAmount("+newattribute+",'','',"+rowCount+")")
			.attr("onfocus","return fnControlAddOrRemove("+newattribute+")").attr("onblur","return fnControlAddOrRemove("+newattribute+")")
			.attr("ondragstart","return false").attr("ondrop","return false");
			$('#amount'+(currentid-(-i))).attr({id: "amount"+newattribute,name: "amount"+newattribute});
			$('#remarks'+(currentid-(-i))).attr({id: "remarks"+newattribute,name: "remarks"+newattribute})
			.attr("onfocus","return fnControlAddOrRemove("+newattribute+")").attr("onblur","return fnControlAddOrRemove("+newattribute+")")
			.attr("onkeyup","return fnControlAddOrRemove("+newattribute+")");
			$('#addrow'+(currentid-(-i))).attr({id: "addrow"+newattribute,name: "addrow"})
			.attr("onclick","return fnAddTR("+newattribute+")");
			$('#removerow'+(currentid-(-i))).attr({id: "removerow"+newattribute,name: "removerow"})
			.attr("onclick","return fnRemoveTR("+newattribute+")");
			$('#removeiconid_'+(currentid-(-i))).attr({id: "removeiconid_"+newattribute});
			$('#fordisable_hdn'+(currentid-(-i))).attr({id: "fordisable_hdn"+newattribute});
			$('#work_specific_hdn'+(currentid-(-i))).attr({id: "work_specific_hdn"+newattribute});
			$('#quantity_hdn'+(currentid-(-i))).attr({id: "quantity_hdn"+newattribute});
			$('#amount_hdn'+(currentid-(-i))).attr({id: "amount_hdn"+newattribute});
			$('#amountfif'+(currentid-(-i))).attr({id: "amountfif"+newattribute});
			$('#remarks_hdn'+(currentid-(-i))).attr({id: "remarks_hdn"+newattribute});
			$('#fordisable_hdn'+(currentid-(-i))).attr({id: "fordisable_hdn"+newattribute});
			$('#emp_ID'+(currentid-(-i))).attr({id: "emp_ID"+newattribute,name:"emp_ID"+newattribute});
			$('#empKanaNames'+(currentid-(-i))).attr({id: "empKanaNames"+newattribute,name:"empKanaNames"+newattribute});
			$('#crossid'+(currentid-(-i))).attr({id: "crossid"+newattribute,name:"crossid"+newattribute})
			.attr("onclick","return fngetEmpty('"+newattribute+"')");;
			$('#divid'+(currentid-(-i))).attr({id: "divid"+newattribute,name:"divid"+newattribute});
			$('#emp').attr("onclick","return popupenableempname('invoice','"+newattribute+"')");
			newattribute++;
		}

		fnCalculateTotal(rowCount);
		fnControlAddOrRemove(newattribute);
	}
}

function fnGetBankDetails(userId) {
	$.ajax({
		type:"GET",
		dataType: "JSON",
		url: 'getBankDetails',
		data: {
			userId: userId
		},
		success: function(data){ // What to do if we succeed
			var checkobject = jQuery.isEmptyObject(data);
			if (!checkobject) {
				$('#invbankname').text(data[0]["bankName"]);
				$('#invbranchname').text(data[0]["branchName"]);       
				$('#invaccount').text(data[0]["accountNo"]);
				$('#invactholder').text(data[0]["bankKanaName"]);
				$('#invacttype').text('普通');
			} else {
				$('#invbankname').text('');
				$('#invbranchname').text('');       
				$('#invaccount').text('');
				$('#invactholder').text('');
				$('#invacttype').text('');
			}
		},
		error: function(xhr, textStatus, errorThrown){
		}  
	})

}

function usernameclick(userName){ 
	alert("Under Construction");
	// pageload();
	// $('#usernameclick').val(userName);
	// $('#username').val('');
	// $('#startdate').val('');
	// $('#enddate').val('');
	// $('#projecttype').val('');
	// $('#estimateno').val('');
	// $('#taxSearch').val('');
	// $('#singlesearchtxt').val('');
	// $("#searchmethod").val(3);
	// $('#frminvoiceindex').attr('action','index'+'?mainmenu='+mainmenu+'&time='+datetime); 
	// $("#frminvoiceindex").submit();
}

function invoicestatus(id, status) {
	alert("Under Construction");
	// $('#invoicestatusid').val(id);
	// $('#invoicestatus').val(status);
	// $('#frmextinvoiceindex').attr('action','index'+'?mainmenu='+mainmenu+'&time='+datetime); 
	// $("#frmextinvoiceindex").submit();
}

function fnpaymentaddedit(id) {
	alert("Under Construction");
	// $('#estimate_id').val(id);
	// $('#frmextinvoiceindex').attr('action','paymentaddedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
	// $("#frmextinvoiceindex").submit();
}

function sendmail(id,custid,estid) {
	alert("Under Construction");
	// var res = confirm("Do You want send the mail with New PDF?");
	// if(res==true) {
	// 	document.getElementById('estimate_id').value = id;
	// 	document.getElementById('cust_id').value = custid;
	// 	document.getElementById('estid').value = estid;
	// 	$('#frmextinvoiceindex').attr('action', '../Estimation/sendmail?mainmenu='+mainmenu+'&time='+datetime);
	// 	$("#frmextinvoiceindex").submit();
	// }
}

function gotoinvoicedetails(invid,keycnt) {
	alert("Under Construction");
	// pageload();
	// $('#invoiceid').val(invid);
	// $('#estimate_id').val(invid);
	// $('#currentRec').val(keycnt);
	// $('#frmextinvoiceindex').attr('action', 'specification?mainmenu='+mainmenu+'&time='+datetime);
	// $('#frmextinvoiceindex').submit();
}

function gotoinvoiceedit(id,keycnt) {
	pageload();
	$('#editid').val(id);
	$('#editflg').val('edit');
	$('#currentRec').val(keycnt);
	$('#identEdit').val(1);
	$('#frmextinvoiceindex').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
	$('#frmextinvoiceindex').submit();
}

function newpdf(id,estimateno,pdfflg,pdfimg,custid) {
	alert("Under Construction");
	/*var res = confirm("Do You want to Create New PDF?");
	if(res==true) {
		if (pdfflg == 0) {
			document.getElementById(pdfimg).src = "../resources/assets/images/pdf.png";
			$('#sendemail' + id).attr('onclick', 'sendmail("' + id + '","' + custid + '","' + estimateno + '")');
			$('#sendemail' + id).removeAttr('class');
			$('#sendemail' + id).attr('class', 'anchorstyle ml3 csrp');
		}
		document.getElementById('invoice_id').value = id;
		document.getElementById('userid').value = estimateno;
		$('#frmextinvoiceindex').attr('action', 'newpdf?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmextinvoiceindex").submit();
	}*/
}