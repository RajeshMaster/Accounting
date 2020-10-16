$(document).ready(function() {
	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	var accessdate = $("#accessdate").val();
	$('.addeditprocess').click(function () {
		$("#frmestimationaddedit").validate({
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
				trading_destination_sel: {required: true},
				quot_date: {required: true, date: true, accessDateCheck: "#accessdate"},
				branchname_sel: {required: true},
				projectpersonal: {required: true},
				tax: {required: true},
				project_name: {required: true},
				cutoff_date_sel: {required: true},
				projecttype_sel: {required: true},
				billing_date_sel: {required: true},
			},
			submitHandler: function(form) { // for demo
					var regflg=$('#editflg').val();
					if (regflg=="edit" || regflg=="viewedit") {
							var confirmprocess = confirm("Do You Want Update The Quotation?");
					} else {
							var confirmprocess = confirm("Do You Want Register The Quotation?");
					}
					
				if(confirmprocess) {
					var rowCount = $('#workspectable tr').length-1;

					var k = rowCount;

					if(k<15) {
                        var a=15;
                    } else {
                        a=k;
                    }
					pageload();
					for (var i = 1; i <=a; i++) {
						if (document.getElementById('amount' + i).disabled == true) {
							document.getElementById('amount' + i).disabled = false;
						}
					}
						document.getElementById('totval').disabled = false;
						$('#rowCount').val(rowCount);
					return true;
				} else {
					return false
				}
			}
		});
		$.validator.messages.required = function (param, input) {
			var article = document.getElementById(input.id);
			return article.dataset.label + ' field is required';
		}
	});
	$('#nopassword').click(function () {
		if (this.checked) {
			$('#pdf_password').css('color', 'grey');
		} else {
			$('#pdf_password').css('color', 'brown');
		}
	});
});
function up() {
	alert("Under Construction");
}
function addedit(type,editid) {
	// alert("Under Construction");
	// return false;
	if(editid!="") {
		$('#editid').val(editid);
	}
	$('#editflg').val(type);
	pageload();
	if(type=="add") {
	    $('#frmestimationindex').attr('action','addedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
	    $("#frmestimationindex").submit();
	} else if(type=="edit") {
	    $('#frmestimationindex').attr('action','addedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
	    $("#frmestimationindex").submit();
	} else {
	    $('#frmEstimationView').attr('action','addedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
	    $("#frmEstimationView").submit();
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
		$('#selMonth').val(("0" + month).substr(-2));
		$('#selYear').val(year);
		$('#prevcnt').val(prevcnt);
		$('#nextcnt').val(nextcnt);
		$('#account_val').val(account_val);
		$('#topclick').val('1');
		$('#sorting').val('');
		$('#frmestimationindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$('#frmestimationindex').submit();
	}
}
function sortemp(srtby) {
	$('#sorting').val(srtby);
	var lastsort = $('#lastsortvalue').val();
	var lastorder = $('#lastordervalue').val();
	if (lastsort == srtby) {
		if (lastorder == '0') {
			$('#ordervalue').val('1');
		} else {
			$('#ordervalue').val('0');
		}
	} else {
		$('#ordervalue').val('0');
	}
	$('#topclick').val('0');
	$('#frmestimationindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$('#frmestimationindex').submit();
}
function filter(val){
	$("#filter").val(val);
	$('#plimit').val('');
	$('#page').val('');
	$('#sorting').val('');
	$("#estimateno").val('');
	$("#companyname").val('');
	$("#startdate").val('');
	$("#enddate").val('');
	$("#projecttype").val('');
	$("#taxSearch").val('');
	$("#searchmethod").val();
	$('#singlesearchtxt').val('');
	$('#companynameClick').val('');
	$('#frmestimationindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$('#frmestimationindex').submit();
}
function pageClick(pageval) {
	$('#page').val(pageval);
	$('#frmestimationindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmestimationindex").submit();
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$('#frmestimationindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmestimationindex").submit();
}
var data = {};
$(function () {
	var cc = 0;
	$('#estimationsort').click(function () {
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
		$('#estimationsort').animate({
			'marginRight' : movediv //moves down
		});
		ccd++;
		if( $('#searchmethod').val() == 1 || $('#searchmethod').val() == 2){
			ccd--;
		}  
	});
});
function sortingfun() {
	pageload();
    $('#plimit').val(50);
    $('#page').val('');
    var sortselect=$('#estimationsort').val();
    $('#sorting').val(sortselect);
    var alreadySelectedOptn=$('#sorting').val();
    var alreadySelectedOptnOrder=$('#ordervalue').val();
    if (sortselect == alreadySelectedOptn) {
        if (alreadySelectedOptnOrder == "asc") {
            $('#ordervalue').val('desc');
        } else {
            $('#ordervalue').val('asc');
        }
    }
    $('#checkdefault').val(1);
	$('#frmestimationindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
    $("#frmestimationindex").submit();
}
function fnSingleSearch() {
	// $('#companynameClick').val('');
	var singlesearchtxt = $("#singlesearchtxt").val();
	if (singlesearchtxt == "") {
		alert("Please Enter The Estimated Search.");
		$("#singlesearchtxt").focus(); 
		return false;
	} else {
		$("#estimateno").val('');
		$("#companyname").val('');
		$("#startdate").val('');
		$("#enddate").val('');
		$("#projecttype").val('');
		$("#taxSearch").val('');
       	$("#searchmethod").val(1);
		$('#plimit').val('');
		$('#page').val('');
		$('#frmestimationindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmestimationindex").submit();
	}
}

function fnMultiSearch() {
	// $('#companynameClick').val('');
	var estimateno = $("#estimateno").val();
	var companyname = $("#companyname").val();
	var startdate = $("#startdate").val();
	var enddate = $("#enddate").val();
	var projecttype = $("#projecttype").val();
	var tax = $("#taxSearch").val();
	if (estimateno == "" && tax == "" && projecttype == ""  && companyname == "" && startdate == "" && enddate == "") {
		alert("Please Enter The Estimated Search.");
		return false;
	}else if (!isEmpty(startdate) && !checkdate(startdate)) {
		alert("Please Enter A Valid Date (YYYY-MM-DD)");
		$("#startdate").focus(); 
		return false;
	}else if (!isEmpty(enddate) && !checkdate(enddate)) {
		alert("Please Enter A Valid Date (YYYY-MM-DD)");
		$("#enddate").focus(); 
		return false;
	} else if ((!isEmpty(enddate) && !isEmpty(startdate)) && (startdate > enddate)) {
		alert("End Date Must Not Greater Than Start Date");
		$("#enddate").focus(); 
		return false;
	} else {
		$('#plimit').val(50);
		$('#page').val('');
		$('#sorting').val('');
		$('#singlesearchtxt').val('');
		$("#searchmethod").val(2);
		$('#frmestimationindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmestimationindex").submit();
	}
}
function isEmpty(str) {
	if (str==null || str==''){
		return true;
	}
	else{
		return false;
	}
}
function checkdate(doj){
	var validformat=/^\d{4}\-\d{2}\-\d{2}$/; 
	var returnval=false;
	if(!validformat.test(doj)){
	return false;
	}
	return true;
}
function clearsearch() {
    $('#plimit').val(50);
    $('#page').val('');
	$("#filter").val('');
    $('#singlesearchtxt').val('');
    $('#sorting').val('');
    $('#startdate').val('');
    $('#enddate').val('');
    $('#projecttype').val('');
    $('#taxSearch').val('');
    $('#estimateno').val('');
    $('#companyname').val('');
	$('#frmestimationindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
    $("#frmestimationindex").submit();
}
function fnGetBrachByAjax(id) {
	var customerid = $('#'+id).val();
	var selindex = document.getElementById(id).selectedIndex;
	var seltext = document.getElementById(id)[selindex].text;
	document.getElementById("company_name").value = seltext;
	$.ajax({
		type: 'GET',
        dataType: "JSON",
		url: 'branch_ajax',
		data: {"customerid": customerid,"mainmenu": mainmenu},
		success: function(resp) {
			$('#branchname_sel').find('option').remove().end();
            for (i = 0; i < resp.length; i++) { 
                 $('#branchname_sel').append( '<option value="'+resp[i]["id"]+'">'+resp[i]["branch_name"]+'</option>' );
                 $('select[name="branchname_sel"]').val(id);
            }
            if (resp.length == 1 && $('#hidebranchname').val() == "") {
            	$('#branchname_sel').val(resp[0]['id']);
            } else if (resp.length == 1) {
            	$('#branchname_sel').val(resp[0]['id']);
            } else {
				$('#branchname_sel').val($('#hidebranchname').val());
            }
		},
		error: function(data) {
			alert(data.status);
			$("#regbutton").attr("data-dismiss","modal");
		}
	});
}
function resetErrors() {
    $('form input, form select, form radio').removeClass('inputTxtError');
    $('label.error').remove();
}  
function fnCalendarcutoff(selval1, selval2) {
	var tighten_month_sel = document.getElementById('tighten_month_sel');
	var cutoff_date_sel = document.getElementById('cutoff_date_sel');

	while (cutoff_date_sel.firstChild) {
		cutoff_date_sel.removeChild(cutoff_date_sel.firstChild);
	}

	fnCalendarCommon(cutoff_date_sel, tighten_month_sel, selval1);
	// fnColorChange();
}

function fnCalendarbill(selval1, selval2) {

	var billing_month_sel = document.getElementById('billing_month_sel');
	var billing_date_sel = document.getElementById('billing_date_sel');

	while (billing_date_sel.firstChild) {
		billing_date_sel.removeChild(billing_date_sel.firstChild);
	}

	fnCalendarCommon(billing_date_sel, billing_month_sel, selval2);
	// fnColorChange();
}

function fnCalendarCommon(cutoff_date_sel, tighten_month_sel, selval) {
	var date = new Date();
	var cur_year = date.getFullYear();
	var cur_month = date.getMonth() + 1;
	var cnt = 0;

	var option = document.createElement("option");
	option.value = '';
	option.text = '';
	cutoff_date_sel.add(option, cutoff_date_sel[0]);

	var option = document.createElement("option");
	option.value = '0';
	if (selval == option.value) {
		option.selected = true;
	}
	option.text = '末日';
	cutoff_date_sel.add(option, cutoff_date_sel[1]);

	if (tighten_month_sel.value == 1) {
		var k = 2;
		cnt = daysInMonth(cur_month, cur_year);
		for (i = 1; i <= cnt; i++) {
			var option = document.createElement("option");
			option.value = i;
			if (selval == option.value) {
				option.selected = true;
			}
			option.text = i;
			cutoff_date_sel.add(option, cutoff_date_sel[k]);
			k++;
		}
		
	} else if (tighten_month_sel.value == 2) {
		cnt = daysInMonth((cur_month+1), cur_year);
		var k = 2;
		for (i = 1; i <= cnt; i++) {
			var option = document.createElement("option");
			option.value = i;
			if (selval == option.value) {
				option.selected = true;
			}
			option.text = i;
			cutoff_date_sel.add(option, cutoff_date_sel[k]);
			k++;
		}
	} else if (tighten_month_sel.value == 3) {
		cnt = daysInMonth((cur_month+2), cur_year);
		var k = 2;
		for (i = 1; i <= cnt; i++) {
			var option = document.createElement("option");
			option.value = i;
			if (selval == option.value) {
				option.selected = true;
			}
			option.text = i;
			cutoff_date_sel.add(option, cutoff_date_sel[k]);
			k++;
		}
	} else {
		cnt = 31;
		var k = 2;
		for (i = 1; i <= cnt; i++) {
			var option = document.createElement("option");
			option.value = i;
			if (selval == option.value) {
				option.selected = true;
			}
			option.text = i;
			cutoff_date_sel.add(option, cutoff_date_sel[k]);
			k++;
		}
	}
}
function gotoinvoicecreate(invid,mainme) {
    $('#invoiceid').val(invid);
	$('#frmestimationindex').attr('action', '../Invoice/addeditinv?mainmenu='+mainme+'&time='+datetime+'&estflg='+1);
	$('#frmestimationindex').submit();
}
function gotoinvoicecreatefrmview(invid,mainme) {
    $('#invoiceid').val(invid);
    $('#estimateid').val(invid);
	$('#frmEstimationView').attr('action', '../Invoice/addeditinv?mainmenu='+mainme+'&time='+datetime+'&estflg='+1);
	$('#frmEstimationView').submit();
}
function daysInMonth(month, year) {
	return new Date(year, month, 0).getDate();
}
function fnCalculateAmount(i, name, value,tbn) {
	var x = event.keyCode;
	// x != 46 && 
	// && x != 8
	if (x != 37 && x != 39 && x != 36 && x != 9) {
		if (name != "" || value != "") {
			var japmoney="jp";
			isformatMoney(name, value,japmoney);
		}
		var quantity = document.getElementById('quantity' + i);
		var unit_price = document.getElementById('unit_price' + i);
		var amount = document.getElementById('amount' + i);
		var totval  = document.getElementById('totval');
		var m = 0, n = 0, l = 0;
		m = quantity.value;
		n = unit_price.value;
		if (Number(n.replace(/,/g, "")) < 0) {
    		$(unit_price).css({"color":"red"});
    		$(unit_price).css('border-color', 'red');
    		$(amount).css({"color":"red"});
    		$(amount).css('border-color', 'red');
    	} else {
    		$(unit_price).css({"color":"#333333"});
    		$(unit_price).css('border-color', '#CCCCCC');
    		$(amount).css({"color":"#333333"});
    		$(amount).css('border-color', '#CCCCCC');
    	}
		if ((m.trim() != "") || (n.trim() != "")) {
			l = Math.round(Number(m.replace(/,/g, "")) * Number(n.replace(/,/g, "")));
		}

		if (l) {
			amount.value = l.toLocaleString("ja-JP");
		}

		if ((m.trim() == 0) || (n.trim() == 0) || (m.trim() == "") || (n.trim() == "")) {
			amount.value = "";
		}
		fnCalculateTotal(tbn);
		fnControlAddOrRemove(i);
	}
}
function isformatMoney(salaryname, salary,japmoney) {
	salary = salary.toString().replace(/\$|\,/g, '');
    var salaryamt = inrFormat(salary,japmoney);
  if (salaryamt != 0) {
    document.getElementById(salaryname).value = salaryamt;
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
function gotoviewpage(viewflg,id) {
	var hdncancel=$('#hdncancel').val();
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
    if (viewflg == "add" && hdncancel == 1) {
    	pageload();
    	$('#frmestimationaddedit').attr('action', '../Customer/View?mainmenu=Customer&time='+datetime);
    	$('#mainmenu').val("Customer");
        $("#frmestimationaddedit").submit();
    }else if (viewflg == "add") {
    	pageload();
      	$('#frmestimationaddedit').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
        $("#frmestimationaddedit").submit();    
    } else {
    	pageload();
        $('#frmestimationaddedit').attr('action', 'view?mainmenu='+mainmenu+'&time='+datetime);
        $("#frmestimationaddedit").submit();
    }
}
function newpdf(id,estimateno,pdfflg,pdfimg,custid) {
	var res = confirm("Do You want to Create New PDF?");
	if(res==true) {
		if (pdfflg == 0) {
			document.getElementById(pdfimg).src = "../resources/assets/images/pdf.png";
			$('#sendemail' + id).attr('onclick', 'sendmail("' + id + '","' + custid + '","' + estimateno + '")');
			$('#sendemail' + id).removeAttr('class');
			$('#sendemail' + id).attr('class', 'anchorstyle ml3 csrp');
			
		}
		document.getElementById('estimate_id').value = id;
		$('#frmestimationindex').attr('action', 'newpdf?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmestimationindex").submit();
	}
}
function sendmail(id,custid,estid) {
	var res = confirm("Do You want send the mail with New PDF?");
	if(res==true) {
		document.getElementById('estimate_id').value = id;
		document.getElementById('cust_id').value = custid;
		document.getElementById('estid').value = estid;
	    $('#frmestimationindex').attr('action', 'sendmail?mainmenu='+mainmenu+'&time='+datetime);
	    $("#frmestimationindex").submit();
	}
}
function mailbacktoindex(to) {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
	if (confirm("Do You Want To Save This Mail to Draft?")) {
		pageload();
		$('#fordraft').val(1);
		$('#frmsendmail').attr('action', 'sendmailprocess?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmsendmail").submit();
	} else {
		pageload();
		$('#frmsendmailcancel').attr('action', '../'+to+'/index?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmsendmailcancel").submit();
	}
}
function mailbacktomailview() {
	$('#frmsendmailcancel').attr('action', '../Mailstatus/index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmsendmailcancel").submit();

}
function validationmail(sendrights) {
	if(sendrights!="1") {
		alert("This Person Can not Send Mail to Anyone");
		return false;
	}
	var tomail = document.getElementById('tomail').value;
	var ccname = document.getElementById('ccname').value;
	var subject = document.getElementById('subject').value;
	var content = document.getElementById('content').value;
	var exp_file = document.getElementById('file1').value;
	if (isEmpty(tomail)) {
		alert("Please Enter the To mail");
		document.getElementById('tomail').focus();
		document.getElementById('tomail').select();
		return false;
	// } else if (!isEmail(tomail)) {
	// 	alert("Please Enter the To mail correctly");
	// 	document.getElementById('tomail').focus();
	// 	document.getElementById('tomail').select();
	// 	return false;
	// } else if ((ccname!="")&&(!isEmail(ccname))) {
	// 	alert("Please Enter the CC mail correctly");
	// 	document.getElementById('ccname').focus();
	// 	document.getElementById('ccname').select();
	// 	return false;
	} else if (isEmpty(content)) {
		alert("Please Enter the Content");
		document.getElementById('content').focus();
		document.getElementById('content').select();
		return false;
	} else if (exp_file != "") {
		var arr1 = new Array;
		arr1 = exp_file.split("\\");
		var len = arr1.length;
		var img1 = arr1[len-1];
		var size = '0';
		var filext = img1.substring(img1.lastIndexOf(".")+1);
		if (filext == "xlsx" || filext == "xls" || filext == "pdf" || filext == "doc" || filext == "docx") {
			var size = parseFloat($("#file1")[0].files[0].size / 1024);
			if (size >= "2097") {
				alert(err_2mb);
				return false;
			}
		} else {
			alert("Only Excel,Pdf & doc files are allowed");
			return false;
		}
	}
	if (subject =="") {
		var res = confirm("Do you want to send the mail without subject?");
		if(res) {
			pageload();
			$('#frmsendmail').attr('action', 'sendmailprocess?mainmenu='+mainmenu+'&time='+datetime);
			$("#frmsendmail").submit();
			return true;
		} else {
			return false;
		}
	}
	if (confirm("Do you want to send this mail?.")) {
		pageload();
		$('#frmsendmail').attr('action', 'sendmailprocess?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmsendmail").submit();
		return true;
	} else {
		return false;
	}
}
function fnpdfremove(id,estflg,subject) {
	var checkedcnt = $('input[name="fileCounter[]"]:checked').length;
    var totval = $('#pdfcnt').val();
	if($('#'+id).is(':checked')) {
		$('#filecnttxt').text(checkedcnt);
		$('#pdfcnt').val(+totval+1);
		$('#'+estflg).val(1);
	} else {
		$('#filecnttxt').text(checkedcnt);
		$('#pdfcnt').val(totval-1);
		$('#'+estflg).val(0);
	}
    var totcnt = $('#pdftotcnt').val();
	var firstcnt=1;
    var secondcnt = $('#pdfcnt').val();
    var enable = false;
    for (var i = 1; i <= totcnt; i++) {
		if($('#tick'+i).is(':checked')) {
			document.getElementById('imgtick'+i).src = "../resources/assets/images/pdf.png";
			if(totcnt==1) {
				$('#filenametxt'+i).text(checkedcnt);
				$('#pdfname'+i).val(subject);
			} else {
				$('#filenametxt'+i).text(subject+firstcnt+"-"+secondcnt);
				$('#pdfname'+i).val(subject+firstcnt+"-"+secondcnt);
			}
			var firstcnt= firstcnt+1;
			var enable = true;
			if (enable == true) {
				$('#nopassword').attr("disabled", false);
				$('#pdf_password').css('color', 'brown');
			}
		} else {
			document.getElementById('imgtick'+i).src = "../resources/assets/images/nopdf.png";
			var estname = $('#estimatename'+i).val();
			$('#filenametxt'+i).text("( "+estname+" )");
			$('#pdfname'+i).val("");
			if (enable == false) {
				$('#pdf_password').css('color', 'grey');
				$('#nopassword').attr("disabled", true);
			}
		}
	}
}
function popupenable(mainmenu,cnt) {
	$('#noticepopup').load('../Estimation/noticepopup?cnt='+cnt+'&mainmenu='+mainmenu);
	$("#noticepopup").modal({
           backdrop: 'static',
           keyboard: false
        });
    $('#noticepopup').modal('show');
}
function fngetnoticeid(val,txt){
	var seltext=$('#noticesel option:selected').text();
	$('#noticeid').val(val);
	$('#noticetxt').val(seltext);
}
function fnaddnoticeid(){
	var selid=$('#noticeid').val();
	var nottxt=$('#noticetxt').val();
	var cnt=$('#cnt').val();
	$('#special_ins'+cnt).val(selid);
		
	$('#noticepopup').modal('toggle');
}
function fnview(id,keycnt) {
	pageload();
	$('#editid').val(id);
	$('#currentRec').val(keycnt);
    $('#frmestimationindex').attr('action','view'+'?mainmenu='+mainmenu+'&time='+datetime); 
    $("#frmestimationindex").submit();
}
function fngotoindex(index,mainmenu) {
	if ($('#backflgforinvoice').val() == "1") {
		$('#frmEstimationView').attr('action', '../Invoice/specification'+'?mainmenu='+mainmenu+'&time='+datetime); 
    	$("#frmEstimationView").submit();
	} else {
		$('#frmEstimationView').attr('action', 'index'+'?mainmenu='+mainmenu+'&time='+datetime); 
    	$("#frmEstimationView").submit();
	}
}
function filedownload(path,file) {
	var confirm_download = "Do You Want To Download?";
    if(confirm(confirm_download)) {
        window.location.href="../app/Http/Common/downloadfile.php?file="+file+"&path="+path+"/";
    }
}
function fnexceldownload(id,mainmenu) {
	var confirm_download = "Do You Want To Download The Excel?";
    if(confirm(confirm_download)) {
		$('#estimate_id').val(id);
	    $('#frmEstimationView').attr('action','exceldownloadprocess'+'?mainmenu='+mainmenu+'&time='+datetime); 
	    $("#frmEstimationView").submit();
    }
}
function fnexceldownloadnew(id,mainmenu) {
    var confirm_download = "Do You Want To Download The Excel?";
    if(confirm(confirm_download)) {
        $('#estimate_id').val(id);
        $('#frmEstimationView').attr('action','newexceldownloadprocess'+'?mainmenu='+mainmenu+'&time='+datetime); 
        $("#frmEstimationView").submit();
    }
}
function getData_view(totalRec,currentRec,date_month,id,time,invid) {
	mainmenu=$('#mainmenu').val();
	document.getElementById('editid').value = id;
	document.getElementById('totalrecords').value = totalRec;
	document.getElementById('currentRec').value = currentRec;
	$('#frmEstimationView').attr('action', 'view?mainmenu='+mainmenu+'&time='+datetime);
	$('#frmEstimationView').submit();
}
function customernameclick(company_name){ 
	pageload();
	$('#companynameClick').val(company_name);
	$('#companyname').val('');
	$('#startdate').val('');
	$('#enddate').val('');
	$('#projecttype').val('');
	$('#estimateno').val('');
	$('#taxSearch').val('');
	$('#singlesearchtxt').val('');
    $('#frmestimationindex').attr('action','index'+'?mainmenu='+mainmenu+'&time='+datetime); 
    $("#frmestimationindex").submit();
}
function estimatestatus(id, status) {
	$('#estimatestatusid').val(id);
	$('#estimatestatus').val(status);
	$('#frmestimationindex').attr('action','index'+'?mainmenu='+mainmenu+'&time='+datetime); 
    $("#frmestimationindex").submit();
}
function downloadcoverletter(path,file) {
	var confirm_download = "Do You Want To Download Cover Letter?";
    if(confirm(confirm_download)) {
        window.location.href="../app/Http/Common/downloadfile.php?file="+file+"&path="+path+"/";
    }
}
function checkSubmitsingle(e) {
   	if(e && e.keyCode == 13) {
   		fnSingleSearch();
   	}
}
function checkSubmitmulti(e) {
   	if(e && e.keyCode == 13) {
   		fnMultiSearch();
   	}
}
function fnbrowsepopup(type,cust_id) {
	if(type=="cc") {
		var anothertxt = $('#tomail').val();
		var anothertxt = anothertxt.replace(/\</g,"$");
		var anothertxt = anothertxt.replace(/\>/g,"-");
		$browsepopupid = "CCbrowsepopup";
		$("#CCbrowsepopup").load('../Estimation/browsepopup?type='+type+'&cust_id='+cust_id+'&anothertxt='+anothertxt+'&mainmenu='+mainmenu);
		$("#CCbrowsepopup").modal({
	        	backdrop: 'static',
	        	keyboard: false
	        });
	    $("#CCbrowsepopup").modal('show');
	} else {
		var anothertxt = $('#ccname').val();
		var anothertxt = anothertxt.replace(/\</g,"$");
		var anothertxt = anothertxt.replace(/\>/g,"-");
		$browsepopupid = "browsepopup";
		$("#browsepopup").load('../Estimation/browsepopup?type='+type+'&cust_id='+cust_id+'&anothertxt='+anothertxt+'&mainmenu='+mainmenu);
		$("#browsepopup").modal({
	        	backdrop: 'static',
	        	keyboard: false
	        });
	    $("#browsepopup").modal('show');
	}
	
}
function fncustplus(add,cusrow) {
	if(add=="1") {
		document.getElementById('inchargehid'+cusrow).style.display = 'block';
		document.getElementById('custopen'+cusrow).style.display = 'none';
		document.getElementById('custclose'+cusrow).style.display = 'block';
	} else {
		document.getElementById('inchargehid'+cusrow).style.display = 'none';
		document.getElementById('custopen'+cusrow).style.display = 'block';
		document.getElementById('custclose'+cusrow).style.display = 'none';

	}
}
function checkmailid() {
	document.getElementById("tomail").value ="";
	$(document).ready( function () {
		var tomailname_1 = $('#tomailname_1').val();
		var tomailname_2 = $('#tomailname_2').val();
		var tomailname_3 = $('#tomailname_3').val();
		var secondMailStatus = 0;
		var thirdMailStatus = 0;
		if($('#othertomail_2').length > 0) {
			if($('#othertomail_2').val() !="" || tomailname_2 !="") {
				secondMailStatus = 1;
			}
		}
		if($('#othertomail_3').length > 0) {
			if($('#othertomail_3').val() !="" || tomailname_3 !="") {
				thirdMailStatus = 1;
			}
		}
		if($("[name='mail[]']:checked").length <= 0 && $('#othertomail_1').val() =="" && tomailname_1 =="" && secondMailStatus == 0 && thirdMailStatus == 0) {
			alert("Please select atleast one Mail");
			return false;
		} else {
			if($('#othertomail_1').val() !="") {
				var reg = /(.+)@(.+){2,}/;
				if(reg.test($('#othertomail_1').val()) == false){
					alert("Please Enter The Correct Mail Id");
					$('#othertomail_1').focus()
					return false;
				}
			} if(tomailname_1 =="" && $('#othertomail_1').val() !="") {
					alert("Please Enter Name");
					$('#tomailname_1').focus();
					return false;
				} else if(tomailname_1 !="" && $('#othertomail_1').val() =="") {
					alert("Please Enter Email Address");
					$('#othertomail_1').focus();
					return false;
			}
			if ($('#othertomail_2').length > 0) {
				if($('#othertomail_2').val() !="") {
				var reg = /(.+)@(.+){2,}/;
					if(reg.test($('#othertomail_2').val()) == false){
						alert("Please Enter The Correct Mail Id");
						$('#othertomail_2').focus();
						return false;
					}
				} if(tomailname_2 =="" && $('#othertomail_2').val() !="") {
						alert("Please Enter Name");
						$('#tomailname_2').focus();
						return false;
					} else if($('#othertomail_2').val() =="" && tomailname_2 !="") {
						alert("Please Enter Email Address");
						$('#othertomail_2').focus();
						return false;
					}
			}
			if ($('#othertomail_3').length > 0) {
				if($('#othertomail_3').val() !="") {
				var reg = /(.+)@(.+){2,}/;
					if(reg.test($('#othertomail_3').val()) == false){
						alert("Please Enter The Correct Mail Id");
						$('#othertomail_3').focus();
						return false;
					}
				} if(tomailname_3 =="" && $('#othertomail_3').val() !="") {
						alert("Please Enter Name");
						$('#tomailname_3').focus();
						return false;
					} else if(tomailname_3 !="" && $('#othertomail_3').val() =="") {
						alert("Please Enter Email Address");
						$('#othertomail_3').focus();
						return false;
					}
			}
		   	$("[name='mail[]']:checked").each( function () {
				var res = $(this).val().split("$");
				$('#tomail').val($('#tomail').val()+","+ res[1]);
		   	});
		   	//forall 
			document.getElementById("allhidden").value ="";
			if($("[name='check_all[]']:checked").length <= 0) {
			} else {
		  		$("[name='check_all[]']:checked").each( function () {
				$('#allhidden').val($('#allhidden').val()+","+ $(this).val());
		   		});
			var s=document.getElementById("allhidden").value;
			document.getElementById("allhidden").value=s.substring(1)+",";
		  	}
			// forall end
			if($('#othertomail_1').val() !="" && $('#othertomail_1').val() != undefined) {
				var othertotext1 = $('#othertomail_1').val();
			} if($('#othertomail_2').val() !="" && $('#othertomail_2').val() != undefined) {
				var othertotext2 = $('#othertomail_2').val();
			} if($('#othertomail_3').val() !="" && $('#othertomail_3').val() != undefined) {
				var othertotext3 = $('#othertomail_3').val();
			}
			if(tomailname_1 == undefined) {
				tomailname_1 = "";
			} if(tomailname_2 == undefined) {
				tomailname_2 = "";
			} if(tomailname_3 == undefined) {
				tomailname_3 = "";
			}
			if(othertotext1 != undefined) {
				var othertotext = tomailname_1+"<"+othertotext1+">";
			} if (othertotext2 != undefined) {
				var othertotext = tomailname_2+"<"+othertotext2+">";
			} if (othertotext3 != undefined) {
				var othertotext = tomailname_3+"<"+othertotext3+">";
			} if (othertotext1 != undefined && othertotext2 != undefined) {
				var othertotext = tomailname_1+"<"+othertotext1+">"+","+tomailname_2+"<"+othertotext2+">";
			} if (othertotext2 != undefined && othertotext3 != undefined) {
				var othertotext = tomailname_2+"<"+othertotext2+">"+","+tomailname_3+"<"+othertotext3+">";
			} if (othertotext1 != undefined && othertotext3 != undefined) {
				var othertotext = tomailname_1+"<"+othertotext1+">"+","+tomailname_3+"<"+othertotext3+">";
			} if (othertotext1 != undefined && othertotext2 != undefined && othertotext3 != undefined) {
				var othertotext = tomailname_1+"<"+othertotext1+">"+","+tomailname_2+"<"+othertotext2+">"+","+tomailname_3+"<"+othertotext3+">";
			}
			if(othertotext != undefined) {
				$('#tomail').val($('#tomail').val()+","+ othertotext);
			} else {
				$('#tomail').val($('#tomail').val());
			}
			var s=document.getElementById("tomail").value;
			tocounter = 2;
			document.getElementById("tomail").value=s.substring(1);
			$('#browsepopup').modal('toggle');
		}
	});
}
function fnselectallemail(id) {
    if($('.checksingle'+id).prop("checked") == true){
       $('.checkboxt'+id).prop('checked',true);
    }
    else if($('.checksingle'+id).prop("checked") == false){
        $('.checkboxt'+id).prop('checked',false);
	   document.getElementById("allhidden").value ="";
    }
}
function fnunselectallemail(id){
	document.getElementById("CCallhidden").value ="";
    $('.checksingle'+id).prop('checked',false);
    var n = jQuery('.checkboxt'+id).length;
    var count = 0;
    if (n > 0) {
        jQuery('.checkboxt'+id+':checked').each(function() {
            count++;
        });
    }
    if (n == count) {
        $('.checksingle'+id).prop('checked', true);
    } else {
        $('.checksingle'+id).prop('checked', false);
    }
}
//-----------------------
function fnCCcustplus(add,cusrow) {
	if(add=="1") {
		document.getElementById('CCinchargehid'+cusrow).style.display = 'block';
		document.getElementById('CCcustopen'+cusrow).style.display = 'none';
		document.getElementById('CCcustclose'+cusrow).style.display = 'block';
	} else {
		document.getElementById('CCinchargehid'+cusrow).style.display = 'none';
		document.getElementById('CCcustopen'+cusrow).style.display = 'block';
		document.getElementById('CCcustclose'+cusrow).style.display = 'none';

	}
}
function CCcheckmailid() {
	document.getElementById("ccname").value ="";
	$(document).ready( function () {
		var otherccmail_1 = $('#otherccmail_1').val();
		var otherccmail_2 = $('#otherccmail_2').val();
		var otherccmail_3 = $('#otherccmail_3').val();
		var otherccName_1 = $('#name_1').val();
		var otherccName_2 = $('#name_2').val();
		var otherccName_3 = $('#name_3').val();
		var secondMailccStatus = 0;
		var thirdMailccStatus = 0;
		if($('#otherccmail_2').length > 0) {
			if($('#otherccmail_2').val() !="" || otherccName_2 !="") {
				secondMailccStatus = 1;
			}
		}
		if($('#otherccmail_3').length > 0) {
			if($('#otherccmail_3').val() !="" || otherccName_3 !="") {
				thirdMailccStatus = 1;
			}
		}
		if($("[name='CCmail[]']:checked").length <= 0 && $('#otherccmail_1').val() == "" && otherccName_1 == "" && secondMailccStatus == 0 && thirdMailccStatus == 0) {
			alert("Please select atleast one Mail");
			return false;
		} else {
			if($('#otherccmail_1').val() !="") {
				var reg = /(.+)@(.+){2,}/;
				if(reg.test($('#otherccmail_1').val()) == false){
					alert("Please Enter The Correct Mail Id");
					$('#otherccmail_1').focus();
					return false;
				}
			} if(otherccName_1 =="" && $('#otherccmail_1').val() !="") {
					alert("Please Enter Name");
					$('#name_1').focus();
					return false;
				} else if(otherccName_1 !="" && $('#otherccmail_1').val() =="") {
					alert("Please Enter Email Address");
					$('#otherccmail_1').focus();
					return false;
			}
			if ($('#otherccmail_2').length > 0) {
				if($('#otherccmail_2').val() !="") {
					var reg = /(.+)@(.+){2,}/;
					if(reg.test($('#otherccmail_2').val()) == false){
						alert("Please Enter The Correct Mail Id");
						$('#otherccmail_2').focus();
						return false;
					}
				} if(otherccName_2 =="" && $('#otherccmail_2').val() !="") {
						alert("Please Enter Name");
						$('#name_2').focus();
						return false;
					} else if($('#otherccmail_2').val() =="" && otherccName_2 !="") {
						alert("Please Enter Email Address");
						$('#otherccmail_2').focus();
						return false;
					}
			}
			if ($('#otherccmail_3').length > 0) {
				if($('#otherccmail_3').val() !="") {
				var reg = /(.+)@(.+){2,}/;
					if(reg.test($('#otherccmail_3').val()) == false){
						alert("Please Enter The Correct Mail Id");
						$('#otherccmail_3').focus();
						return false;
					}
				} if(otherccName_3 =="" && $('#otherccmail_3').val() !="") {
						alert("Please Enter Name");
						$('#name_3').focus();
						return false;
					} else if(otherccName_3 !="" && $('#otherccmail_3').val() =="") {
						alert("Please Enter Email Address");
						$('#otherccmail_3').focus();
						return false;
					}
			}
		   $("[name='CCmail[]']:checked").each( function () {
				var res = $(this).val().split("$");
				$('#ccname').val($('#ccname').val()+","+ res[1]);
		   });
		   //forall 
			document.getElementById("CCallhidden").value ="";
			if($("[name='CCcheck_all[]']:checked").length <= 0) {
			} else {
		  		$("[name='CCcheck_all[]']:checked").each( function () {
				$('#CCallhidden').val($('#CCallhidden').val()+","+ $(this).val());
		   		});
			var s=document.getElementById("CCallhidden").value;
			document.getElementById("CCallhidden").value=s.substring(1)+",";
		  	}
			// forall end
			if($('#otherccmail_1').val() !="" && $('#otherccmail_1').val() != undefined) {
				var othercctext1 = $('#otherccmail_1').val();
			} if($('#otherccmail_2').val() !="" && $('#otherccmail_2').val() != undefined) {
				var othercctext2 = $('#otherccmail_2').val();
			} if($('#otherccmail_3').val() !="" && $('#otherccmail_3').val() != undefined) {
				var othercctext3 = $('#otherccmail_3').val();
			}
			if(otherccName_1 == undefined) {
				otherccName_1 = "";
			} if(otherccName_2 == undefined) {
				otherccName_2 = "";
			} if(otherccName_3 == undefined) {
				otherccName_3 = "";
			}
			if(othercctext1 != undefined) {
				var othercctext = otherccName_1+"<"+othercctext1+">";
			} if (othercctext2 != undefined) {
				var othercctext = otherccName_2+"<"+othercctext2+">";
			} if (othercctext3 != undefined) {
				var othercctext = otherccName_3+"<"+othercctext3+">";
			} if (othercctext1 != undefined && othercctext2 != undefined) {
				var othercctext = otherccName_1+"<"+othercctext1+">"+","+otherccName_2+"<"+othercctext2+">";
			} if (othercctext2 != undefined && othercctext3 != undefined) {
				var othercctext = otherccName_2+"<"+othercctext2+">"+","+otherccName_3+"<"+othercctext3+">";
			} if (othercctext1 != undefined && othercctext3 != undefined) {
				var othercctext = otherccName_1+"<"+othercctext1+">"+","+otherccName_3+"<"+othercctext3+">";
			} if (othercctext1 != undefined && othercctext2 != undefined && othercctext3 != undefined) {
				var othercctext = otherccName_1+"<"+othercctext1+">"+","+otherccName_2+"<"+othercctext2+">"+","+otherccName_3+"<"+othercctext3+">";
			}
			if(othercctext != undefined) {
				$('#ccname').val($('#ccname').val()+","+ othercctext);
			} else {
				$('#ccname').val($('#ccname').val());
			}
			var s=document.getElementById("ccname").value;
			counter = 2;
			document.getElementById("ccname").value=s.substring(1);
			$('#CCbrowsepopup').modal('toggle');
		}
	});
}
function fnCCselectallemail(id) {
    if($('.CCchecksingle'+id).prop("checked") == true){
       $('.CCcheckboxt'+id).prop('checked',true);
    }
    else if($('.CCchecksingle'+id).prop("checked") == false){
        $('.CCcheckboxt'+id).prop('checked',false);
	    document.getElementById("CCallhidden").value ="";
    }
}
function fnCCunselectallemail(id){
	document.getElementById("allhidden").value ="";
    $('.CCchecksingle'+id).prop('checked',false);
    var n = jQuery('.CCcheckboxt'+id).length;
    var count = 0;
    if (n > 0) {
        jQuery('.CCcheckboxt'+id+':checked').each(function() {
            count++;
        });
    }
    if (n == count) {
        $('.CCchecksingle'+id).prop('checked', true);
    } else {
        $('.CCchecksingle'+id).prop('checked', false);
    }
}
function fntoclear() {
	document.getElementById('tomail').value="";
}
function fnccclear() {
	document.getElementById('ccname').value="";
}
function fnccdisableall(i) {
	$('#ccalldivid'+i).css('style', 'display:none');
}
function fntodisableall(i) {
	$('#toalldivid'+i).css('style', 'display:none');
}
function fncoverpopup(custid) {
	$('#coverpopup').load('../Estimation/coverpopup?custid='+custid+'&mainmenu='+mainmenu);
	$("#coverpopup").modal({
           backdrop: 'static',
           keyboard: false
        });
    $('#coverpopup').modal('show');
}
function fncoverdownload() {
	var confirm_download = "Do You Want To Download The Cover Details?";
    if(confirm(confirm_download)) {
	    $('#frmEstimationView').attr('action','coverdownloadprocess'+'?mainmenu='+mainmenu+'&time='+datetime); 
	    $("#frmEstimationView").submit();
    } else {
    	return false;
    }
}
//Clone Add
var counter = 2;
function cloneadd() {
	if (counter > 3) {
		alert("Not Exceeding More Than 3 Rows!");
		return false;
	}
	var $button = $("#othercc_1").clone();
	$button.attr("id", "othercc_"+counter);
	$button.find('#otherccmail_1').attr({id: "otherccmail_"+counter, name: "otherccmail_"+counter});
	$button.find('#name_1').attr({id: "name_"+counter, name: "name_"+counter});
	$button.find("input:text").val("");
	$button.find('#otheremaillbl_1').text(emailid+counter);
	$button.find('#otheremaillbl_1').attr({id: "otheremaillbl_"+counter});
	$button.find('#removeemailid_1').css('display', 'inline-block');
	$button.find('#removeemailid_1').attr({id: "removeemailid_"+counter, name: "remove_emailid_"+counter});
	$("#forccappend").append($button);
	counter++;
	return false;
}
// Clone Remove
function cloneremove(thisattr) {
	var rearrange = 2;
	var totallen = $("#forccappend > div").length;
	var currentid = thisattr.id;
	var splitid = currentid.split("_");
	$("#othercc_"+splitid[1]).remove();
	$("#forccappend>div").each(function() {
		var from = $(this).attr("id");
		var splitfrom = from.split("_");
		var $rearrangeid = $("#"+from);
		$rearrangeid.attr("id", "othercc_"+rearrange);
		$rearrangeid.find('#otherccmail_'+splitfrom[1]).attr({id: "otherccmail_"+rearrange, name: "otherccmail_"+rearrange, value: ""});
		$rearrangeid.find('#name_'+splitfrom[1]).attr({id: "name_"+rearrange, name: "name_"+rearrange, value: ""});
		$rearrangeid.find('#otheremaillbl_'+splitfrom[1]).text(emailid+rearrange);
		$rearrangeid.find('#otheremaillbl_'+splitfrom[1]).attr({id: "otheremaillbl_"+rearrange});
		$rearrangeid.find('#removeemailid_'+splitfrom[1]).attr({id: "removeemailid_"+rearrange, name: "remove_emailid_"+rearrange});
		rearrange++;
	});
counter--;
}
function ccPopupClose() {
	counter = 2;
	$("body div").removeClass( "modalOverlay" );
    $('#CCbrowsepopup').empty();
    $('#CCbrowsepopup').modal('toggle');
}
function toPopupClose() {
	tocounter = 2;
	$("body div").removeClass( "modalOverlay" );
    $('#browsepopup').empty();
    $('#browsepopup').modal('toggle');
}
var tocounter = 2;
function cloneaddTo() {
	if (tocounter > 3) {
		alert("Not Exceeding More Than 3 Rows!");
		return false;
	}
	var $button = $("#otherto_1").clone();
	$button.attr("id", "otherto_"+tocounter);
	$button.find('#othertomail_1').attr({id: "othertomail_"+tocounter, name: "othertomail_"+tocounter});
	$button.find('#tomailname_1').attr({id: "tomailname_"+tocounter, name: "tomailname_"+tocounter});
	$button.find("input:text").val("");
	$button.find('#otheremaillbl_1').text(emailid+tocounter);
	$button.find('#otheremaillbl_1').attr({id: "otheremaillbl_"+tocounter});
	$button.find('#removeemailid_1').css('display', 'inline-block');
	$button.find('#removeemailid_1').attr({id: "removeemailid_"+tocounter, name: "remove_emailid_"+tocounter});
	$("#forappend").append($button);
	tocounter++;
	return false;
}
// Clone Remove
function cloneremoveTo(thisattr) {
	var rearrange = 2;
	var totallen = $("#forappend > div").length;
	var currentid = thisattr.id;
	var splitid = currentid.split("_");
	$("#otherto_"+splitid[1]).remove();
	$("#forappend>div").each(function() {
		var from = $(this).attr("id");
		var splitfrom = from.split("_");
		var $rearrangeid = $("#"+from);
		$rearrangeid.attr("id", "otherto_"+rearrange);
		$rearrangeid.find('#othertomail_'+splitfrom[1]).attr({id: "othertomail_"+rearrange, name: "othertomail_"+rearrange, value: ""});
		$rearrangeid.find('#tomailname_'+splitfrom[1]).attr({id: "tomailname_"+rearrange, name: "tomailname_"+rearrange, value: ""});
		$rearrangeid.find('#otheremaillbl_'+splitfrom[1]).text(emailid+rearrange);
		$rearrangeid.find('#otheremaillbl_'+splitfrom[1]).attr({id: "otheremaillbl_"+rearrange});
		$rearrangeid.find('#removeemailid_'+splitfrom[1]).attr({id: "removeemailid_"+rearrange, name: "remove_emailid_"+rearrange});
		rearrange++;
	});
tocounter--;
}
function fnCancel_check() {
  	cancel_check = false;
 	return cancel_check;
}
// var counter1 = 15;
function cloneaddblade() {
	var $button = $("#othercc_1").clone();
	var counter1=document.getElementsByClassName("input_text");
	var counter1=counter1.length+1;
	if (counter1 >50) {
		alert("Not Exceeding More Than 50 Rows!");
		return false;
	}
	var rowCount = $('#workspectable tr').length-1;
	$button.find("input:text").val("");
	$button.attr("id", "othercc_"+counter1);
	$button.find('#work_specific1').attr({id: "work_specific"+counter1,name: "work_specific"+counter1})
	.attr("onfocus","return fnControlAddOrRemove("+counter1+")")
	.attr("onblur","return fnControlAddOrRemove("+counter1+")")
	.attr("onkeyup","return fnControlAddOrRemove("+counter1+")");
	$button.find('#quantity1').attr({id: "quantity"+counter1, name: "quantity"+counter1})
	.attr("onkeyup","fnCalculateAmount("+counter1+", '', '',"+rowCount+")")
	.attr("onfocus","return fnControlAddOrRemove("+counter1+")")
	.attr("onblur","return fnControlAddOrRemove("+counter1+")")

	// .attr("onkeypress","isDotNumberKey("+this.event+","+this.value+",'1')")
	.attr("ondragstart","return false").attr("ondrop","return false");
	$button.find('#unit_price1').attr({id: "unit_price"+counter1, name: "unit_price"+counter1})
	// .attr("onkeypress","isNumberKeywithminus("+this.event+")")
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
	$button.find('#fordisable_hdn1').attr({id: "fordisable_hdn"+counter1});

	$("#forccappend").append($button);
	counter++;
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
		$('#work_specific'+(currentid-(-i))).attr({id: "work_specific"+newattribute,name: "work_specific"+newattribute})
		.attr("onfocus","return fnControlAddOrRemove("+newattribute+")")
		.attr("onblur","return fnControlAddOrRemove("+newattribute+")")
		.attr("onkeyup","return fnControlAddOrRemove("+newattribute+")");
		$('#quantity'+(currentid-(-i))).attr({id: "quantity"+newattribute,name: "quantity"+newattribute})
		.attr("onkeyup","fnCalculateAmount("+newattribute+", '', '',"+rowCount+")")
		.attr("onfocus","return fnControlAddOrRemove("+newattribute+")")
		.attr("onblur","return fnControlAddOrRemove("+newattribute+")");
		$('#unit_price'+(currentid-(-i))).attr({id: "unit_price"+newattribute,name: "unit_price"+newattribute})
		.attr("onkeyup","return fnCalculateAmount("+newattribute+",unit_price"+newattribute+",'',"+rowCount+")")
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
		newattribute++;
	}
	fnCalculateTotal(rowCount);
   fnControlAddOrRemove(newattribute);

}
}