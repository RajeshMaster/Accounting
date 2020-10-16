$(document).ready(function() {
	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.addeditprocess').click(function () {
		$("#frminvoiceaddedit").validate({
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
				quot_date: {required: true, date: true, accessDateCheck: "#accessdate" },  
				payment_date: {required: true, date: true, greaterThan: "#quot_date" },
				trading_destination_sel: {required: true}, 
				bankname_sel: {required: true},
				projecttype_sel: {required: true},
				projectpersonal: {required: true},
				project_name: {required: true},
			},
			submitHandler: function(form) { // for demo
					var regflg=$('#regflg').val();
					if (regflg==1 || regflg==2) {
							var confirmprocess = confirm("Do You Want To Register?");
					} else {
							var confirmprocess = confirm("Do You Want To Update?");
					}	
				if(confirmprocess) {
					pageload();
					for (var i = 1; i <= 15; i++) {
						$('#work_specific'+i).attr('disabled',false);
		        		$('#quantity'+i).attr('disabled',false);
						$('#unit_price'+i).attr('disabled',false);
						$('#amount'+i).attr('disabled',false);
					}
					$('#totval').attr('disabled',false);
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
	$('.editinvprocess').click(function () {
		$("#frminvoiceassignemp").validate({
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
				selYear: {required: true},
			},
			submitHandler: function(form) { // for demo
				var confirmprocess = confirm("Do You Want To Update?");
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
			return article.dataset.label + ' field is required';
		}
	});
});
$(function () {
	$('.invmulticopyprocess').click(function () {
		// alert("aaaaaaaa");return false;
		$("#frminvoicemulticopy").validate({
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
				quot_date: {required: true, date: true },  
				payment_date: {required: true, date: true, greaterThan: "#quot_date" },
			},
			submitHandler: function(form) { // for demo
				var confirmprocess = confirm("Do You Want To Register?");
				if(confirmprocess) {
					pageload();
					return true;
				} else {
					return false
				}
			}
		});
		var countcheck = $("#count").val();
		var temp = true;
		for (var i = 1; i <= countcheck; i++) {
			if ($('#quot_date'+i).val() == '' || $('#payment_date'+i).val() == '') {
				$('[name*="quot_date'+i+'"]').each(function () {
					$(this).rules('add', {
						required: true,
						messages: {
							required: "Invoice Date field is required"
						}
					});
				});
				$('[name*="payment_date'+i+'"]').each(function () {
					$(this).rules('add', {
						required: true,
						messages: {
							required: "Payment Date field is required"
						}
					});
				});
			}
		}
		$.validator.messages.required = function (param, input) {
			var article = document.getElementById(input.id);
			return article.dataset.label + ' field is required';
		}
	});
});
var data = {};
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
function underconstruction() {
	alert("Under Construction");
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
		$('#frminvoiceindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$('#frminvoiceindex').submit();
	}
}
function fngetsubsubject(mainid,subid) {
    $('#branchname_sel').find('option').not(':first').remove();
	var textval=$('#trading_destination_sel option:selected').text();
	var textid=$('#trading_destination_sel option:selected').val();
	$('#companynames').val(textval);
	$('#companyid').val(textid);
	var branchid=$('#branch_selection').val();
	$.ajax({
        type:"GET",
        dataType: "JSON",
        cache: false, 
        url: 'ajaxsubsubject',
        data: {
            mainid: mainid
        },
        success: function(data){ // What to do if we succeed
           	for (i = 0; i < data.length; i++)
            { 
                 $('#branchname_sel').append( '<option value="'+data[i]["id"]+'">'+data[i]["branch_name"]+'</option>' );
                 // $('select[name="branchname_sel"]').val(mainid);
            }
            // It empty the Branch while copy the record
         	/*if ($('#regflg').val() == 2) {
        		subid = "";
        	}*/
            // If branch having only one option means it selected by default.
            if (data.length == 1 && subid == "") {
            	$('select[name="branchname_sel"]').val(data[0]['id']);
            } else if (data.length == 1) {
            	$('select[name="branchname_sel"]').val(data[0]['id']);
            } else {
            	$('select[name="branchname_sel"]').val(subid);
            }
        },
        error: function(xhr, textStatus, errorThrown){
        }  
    })
}
function filter(val) {
	$("#filter").val(val);
	$('#plimit').val('');
	$('#pageclick').val('');
	$('#sorting').val('');
	$('#searchmethod').val(6);
	$('#frminvoiceindex').submit();
}
function fngotoindex(index,mainmenu) {
	var backflg=$('#backflg').val();
	if (backflg==1) {
		$('#frminvoicespec').attr('action', '../Payment/customerview'+'?mainmenu='+mainmenu+'&time='+datetime); 
    	$("#frminvoicespec").submit();
	} else {
		$('#frminvoicespec').attr('action', 'index'+'?mainmenu='+mainmenu+'&time='+datetime); 
    	$("#frminvoicespec").submit();
	}
}
function pageClick(pageval) {
	$('#page').val(pageval);
	var mainmenu= $('#mainmenu').val();
	$('#frminvoiceindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frminvoiceindex").submit();
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	var mainmenu= $('#mainmenu').val();
	$('#frminvoiceindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frminvoiceindex").submit();
}
function sortingfun() {
	pageload();
    $('#plimit').val(50);
    $('#page').val('');
    var sortselect=$('#invoicesort').val();
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
    $('#checkdefault').val(1);
    $("#frminvoiceindex").submit();
}
function clearsearch() {
    $('#plimit').val(50);
    $('#page').val('');
    /*$('#sortOptn').val('');*/
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
    $("#frminvoiceindex").submit();
}
function usinglesearch() {
	$('#plimit').val(50);
    $('#page').val('');
	$("#filterval").val('');
	$('#msearchusercode').val('');
    $('#msearchcustomer').val('');
    $('#msearchstdate').val('');
    $('#msearcheddate').val('');
    $('#protype1').val('');
    $('#protype2').val('');
    $('#userclassification').val('');
    var singlesearch = $("#singlesearch").val();
    if (singlesearch == "") {
		alert("Please Enter The Invoice Search.");
		$("#singlesearch").focus(); 
		return false;
	}
     else {
        $("#searchmethod").val('');
		$("#searchmethod").val(1);

    }
    $("#frminvoiceindex").submit();
}
function umultiplesearch() {
	var msearchusercode = $("#msearchusercode").val();
	var msearchcustomer = $("#msearchcustomer").val();
	var msearchstdate = $("#msearchstdate").val();
	var msearcheddate = $("#msearcheddate").val();
	var protype1 = $("#protype1").val();
	$("#searchmethod").val(2);
    $('#plimit').val(50);
    $('#page').val('');
    // $('#sortOptn').val('');
	$("#filterval").val('');
    $('#sortOrder').val('DESC'); 
    $('#singlesearch').val('');
    if (msearchusercode == "" &&  msearchcustomer == ""  && msearchstdate == "" && msearcheddate == "" && protype1 == "") {
		alert("Please Enter The Invoice Search.");
		$("#msearchusercode").focus();
		return false;
	 }
	 // else if (!isEmpty(startdate) && !checkdate(startdate)) {
	// 	alert("Please Enter A Valid Date (YYYY-MM-DD)");
	// 	$("#startdate").focus(); 
	// 	return false;
	// }else if (!isEmpty(enddate) && !checkdate(enddate)) {
	// 	alert("Please Enter A Valid Date (YYYY-MM-DD)");
	// 	$("#enddate").focus(); 
	// 	return false;
	// } else if ((!isEmpty(enddate) && !isEmpty(startdate)) && (startdate > enddate)) {
	// 	alert("End Date Must Not Greater Than Start Date");
	// 	$("#enddate").focus(); 
	// 	return false;
	// }
	else {
		$('#frminvoiceindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#frminvoiceindex").submit();
	}
}
function fnvalidatedatefield(tvalue) {
	var qdate=$('#quot_date').val();
	var pdate=$('#payment_date').val();
		if (pdate < qdate) {
			alert("Payment date should be Greater than Invoice date");
			return false;
		} else {
			return true;
		}
}
function newpdf(id,estimateno,pdfflg,pdfimg,mainmenu,custid) {
	var res = confirm("Do You want to Create New PDF?");
	if(res==true) {
		if (pdfflg == 0) {
			document.getElementById(pdfimg).src = "../resources/assets/images/pdf.png";
			$('#sendemail' + id).attr('onclick', 'sendmail("' + id + '","' + custid + '","' + estimateno + '")');
			$('#sendemail' + id).removeAttr('class');
			$('#sendemail' + id).attr('class', 'anchorstyle ml3 csrp');
		}
		document.getElementById('invoice_id').value = id;
		document.getElementById('userid').value = estimateno;
		$('#frminvoiceindex').attr('action', 'newpdf?mainmenu='+mainmenu+'&time='+datetime);
		$("#frminvoiceindex").submit();
	}
}
function fnbankaccountdetail(getbankval) {
	$.ajax({
        type:"GET",
        dataType: "JSON",
        url: 'ajaxgetbankdetails',
        data: {
            getbankval: getbankval
        },
        success: function(data){ // What to do if we succeed
        	var checkobject = jQuery.isEmptyObject( data );
        	if (!checkobject) {
        		$('#invaccount').text(data[0]["AccNo"]);
	        	$('#invoiceacctnumb').val(data[0]["AccNo"]);
	        	$('#invbranchname').text(data[0]["Branch"]);       
	        	$('#invoicebranchname').val(data[0]["BranchName"]);     
	        	$('#invactholder').text(data[0]["FirstName"]);
	        	$('#invoiceaccthold').val(data[0]["FirstName"]);
	        	$('#bank_id').val(data[0]["BankName"]);
	        	$('#invacttype').text('普通');
        	}
        	$('#invoiceaccttype').val(1);
        },
        error: function(xhr, textStatus, errorThrown){
        }  
    })
}
function addedit(type,editid,mainmenu) {
	// alert("Under Construction");
	// return false;
	if(editid!="") {
		$('#editflg').val(type);
		$('#editid').val(editid);
	}
	pageload();
    $('#frminvoiceindex').attr('action','../Estimation/addedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
    $("#frminvoiceindex").submit();
}
function fngetbranch(mainid,subid) {
	$('#branchname_sel').find('option').not(':first').remove();
	$.ajax({
        type:"GET",
        dataType: "JSON",
        url: 'ajaxbranchnamesel',
        data: {
            mainid: mainid
        },
        success: function(data){ // What to do if we succeed
           for (i = 0; i < data.length; i++)
            { 
                 $('#branchname_sel').append( '<option value="'+data[i]["customer_id"]+'">'+data[i]["branch_name"]+'</option>' );
                 // $('select[name="branchname_sel"]').val(mainid);
            }
            $('select[name="branchname_sel"]').val(subid);
        },
        error: function(xhr, textStatus, errorThrown){
        }  
    })
}
function fnCalendarcutoff(selval1, selval2) {
	var tighten_month_sel = document.getElementById('tighten_month_sel');
	var cutoff_date_sel = document.getElementById('cutoff_date_sel');

	while (cutoff_date_sel.firstChild) {
		cutoff_date_sel.removeChild(cutoff_date_sel.firstChild);
	}

	fnCalendarCommon(cutoff_date_sel, tighten_month_sel, selval1);
	fnColorChange();
}
function fnCalendarbill(selval1, selval2) {

	var billing_month_sel = document.getElementById('billing_month_sel');
	var billing_date_sel = document.getElementById('billing_date_sel');

	while (billing_date_sel.firstChild) {
		billing_date_sel.removeChild(billing_date_sel.firstChild);
	}

	fnCalendarCommon(billing_date_sel, billing_month_sel, selval2);
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
	// option.text = '末日';
	// cutoff_date_sel.add(option, cutoff_date_sel[1]);

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
function popupenable(mainmenu,cnt) {
	popupopenclose(1);
	$('#noticepopup').load('../Invoice/noticepopup?cnt='+cnt+'&mainmenu='+mainmenu);
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
	if (cnt == 1) {
		$('#noticesel1').val(selid);
		$('#note1').val(nottxt);
	} else if (cnt == 2) {
		$('#noticesel2').val(selid);
		$('#note2').val(nottxt);
	} else if (cnt == 3) {
		$('#noticesel3').val(selid);
		$('#note3').val(nottxt);
	} else if (cnt == 4) {
		$('#noticesel4').val(selid);
		$('#note4').val(nottxt);
	} else if (cnt == 5) {
		$('#noticesel5').val(selid);
		$('#note5').val(nottxt);
	}
	$('#noticepopup').modal('toggle');
}
function gotoindex(viewflg,mainmenu) {
	pageload();
	$('#frminvoiceaddedit').attr('action', viewflg+'?mainmenu='+mainmenu+'&time='+datetime);
    $("#frminvoiceaddedit").submit();
}
function gotopayment(viewflg,mainmenu) {
	pageload();
	window.history.back();
}
function gotoinvoiceedit(invid,mainmenu,keycnt) {
	pageload();
	$('#invoiceid').val(invid);
	$('#currentRec').val(keycnt);
	$('#identEdit').val(1);
	$('#frminvoiceindex').attr('action', 'addeditinv?mainmenu='+mainmenu+'&time='+datetime);
	$('#frminvoiceindex').submit();
}
function gotoinvedit(invid,mainmenu) {
	pageload();
	$('#copyflg').val();
	$('#identEdit').val(1);
	$('#frminvoicespec').attr('action', 'addeditinv?mainmenu='+mainmenu+'&time='+datetime);
	$('#frminvoicespec').submit();
}
function gotoinvoicedetails(invid,mainmenu,keycnt) {
	pageload();
	$('#invoiceid').val(invid);
	$('#estimate_id').val(invid);
	$('#currentRec').val(keycnt);
	$('#frminvoiceindex').attr('action', 'specification?mainmenu='+mainmenu+'&time='+datetime);
	$('#frminvoiceindex').submit();
}
function gotoinvcopy(invid,mainmenu) {
	pageload();
	$('#copyflg').val("1");
	$('#frminvoicespec').attr('action', 'addeditinv?mainmenu='+mainmenu+'&time='+datetime);
	$('#frminvoicespec').submit();
}
function isformatMoney(salaryname, salary,japmoney) {
	salary = salary.toString().replace(/\$|\,/g, '');
    var salaryamt = inrFormat(salary,japmoney);
  if (salaryamt != 0) {
    document.getElementById(salaryname).value = salaryamt;
  }
  return true;
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
function sendmail(id,custid,estid) {
	var res = confirm("Do You want send the mail with New PDF?");
	if(res==true) {
		document.getElementById('estimate_id').value = id;
		document.getElementById('cust_id').value = custid;
		document.getElementById('estid').value = estid;
	    $('#frminvoiceindex').attr('action', '../Estimation/sendmail?mainmenu='+mainmenu+'&time='+datetime);
	    $("#frminvoiceindex").submit();
	}
}
function mailbacktoindex() {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
	pageload();
	$('#frmsendmailcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
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
	var pdfpassword = document.getElementById('pdfpassword').value;
	// var pdfname1 = document.getElementById('pdfname1').value;
	if (isEmpty(tomail)) {
		alert("Please Enter the To mail");
		document.getElementById('tomail').focus();
		document.getElementById('tomail').select();
		return false;
	} else if (!isEmail(tomail)) {
		alert("Please Enter the To mail correctly");
		document.getElementById('tomail').focus();
		document.getElementById('tomail').select();
		return false;
	} else if ((ccname!="")&&(!isEmail(ccname))) {
		alert("Please Enter the CC mail correctly");
		document.getElementById('ccname').focus();
		document.getElementById('ccname').select();
		return false;
	} else if (isEmpty(content)) {
		alert("Please Enter the Content");
		document.getElementById('content').focus();
		document.getElementById('content').select();
		return false;
	} else if (isEmpty(pdfpassword)) {
		alert("Please Enter the PDF Password");
		document.getElementById('pdfpassword').focus();
		document.getElementById('pdfpassword').select();
		return false;
	// } else if (isEmpty(pdfname1)) {
	// 	alert("Please Enter the Pdf Name 1");
	// 	document.getElementById('pdfname1').focus();  // will be use by loop validation by Anto
	// 	document.getElementById('pdfname1').select();
	// 	return false;
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
function fnpdfremove(id,estflg) {
	var checkedcnt = $('input[name="fileCounter[]"]:checked').length;
	var totval = $('#pdfcnt').val();
	if($('#'+id).is(':checked')) {
		document.getElementById('img'+id).src = "../resources/assets/images/pdf.png";
		$('#filecnttxt').text(checkedcnt);
		$('#pdfcnt').val(+totval+1);
		$('#'+estflg).val(1);
	} else {
		document.getElementById('img'+id).src = "../resources/assets/images/nopdf.png";
		$('#filecnttxt').text(checkedcnt);
		$('#pdfcnt').val(totval-1);
		$('#'+estflg).val(0);
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
function gotoexceldownload(invid,mainmenu) {
		var confirm_create="Do you Want to Create Invoice";
	    if(confirm(confirm_create)) {
			$('#frminvoicespec').attr('action', 'exceldownloadprocess?mainmenu='+mainmenu+'&time='+datetime);
			$("#frminvoicespec").submit();
		}
}
function filedownload(path,file) {
	var confirm_download = "Do You Want To Download?";
    if(confirm(confirm_download)) {
        window.location.href="../app/Http/Common/downloadfile.php?file="+file+"&path="+path+"/";
    }
}
function fnspecialadd(val,txt) {
	var selid=$('#noticeid').val();
	var nottxt=$('#noticetxt').val();
	var cnt=$('#cnt').val();
	if (cnt == 1) {
		$('#noticesel1').val(selid);
		$('#note1').val(nottxt);
	} else if (cnt == 2) {
		$('#noticesel2').val(selid);
		$('#note2').val(nottxt);
	} else if (cnt == 3) {
		$('#noticesel3').val(selid);
		$('#note3').val(nottxt);
	} else if (cnt == 4) {
		$('#noticesel4').val(selid);
		$('#note4').val(nottxt);
	} else if (cnt == 5) {
		$('#noticesel5').val(selid);
		$('#note5').val(nottxt);
	}
	$('#noticepopup').modal('toggle');
}
function fnestimateview(id,mainmenu,flg) {
	pageload();
	$('#editid').val(id);
	$('#backflgforinvoice').val(flg);
    $('#frminvoicespec').attr('action','../Estimation/view'+'?mainmenu='+mainmenu+'&time='+datetime); 
    $("#frminvoicespec").submit();
}
function getData_view(totalRec,currentRec,date_month,id,time,invid) {
	mainmenu=$('#mainmenu').val();
	document.getElementById('invoiceid').value = id;
	document.getElementById('totalrecords').value = totalRec;
	document.getElementById('currentRec').value = currentRec;
	$('#frminvoicespec').attr('action', 'specification?mainmenu='+mainmenu+'&time='+datetime);
	$('#frminvoicespec').submit();
}
function invoicestatus(id, status) {
	$('#invoicestatusid').val(id);
	$('#invoicestatus').val(status);
	$('#frminvoiceindex').attr('action','index'+'?mainmenu='+mainmenu+'&time='+datetime); 
    $("#frminvoiceindex").submit();
}
function mainchange(tvalue) {
	if (tvalue==1) {
        $("#protype2").attr("style", "display:none");
	} else {
        $("#protype2").attr("style", "display:block");
        $("#protype2").attr("style", "background-color:white");
	}
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
    $("#searchmethod").val(3);
    $('#frminvoiceindex').attr('action','index'+'?mainmenu='+mainmenu+'&time='+datetime); 
    $("#frminvoiceindex").submit();
}
function downloadcoverletter(path,file) {
	var confirm_download = "Do You Want To Download Cover Letter?";
    if(confirm(confirm_download)) {
        window.location.href="../app/Http/Common/downloadfile.php?file="+file+"&path="+path+"/";
    }
}
function fnpaymentaddedit(id) {
	$('#estimate_id').val(id);
	$('#frminvoiceindex').attr('action','paymentaddedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
    $("#frminvoiceindex").submit();
}
function fnviewpaymentaddedit() {
	$('#frminvoicespec').attr('action','paymentaddedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
    $("#frminvoicespec").submit();
}
function checkSubmitsingle(e) {
   	if(e && e.keyCode == 13) {
   		usinglesearch();
   	}
}
function checkSubmitmulti(e) {
   	if(e && e.keyCode == 13) {
   		umultiplesearch();
   	}
}
function fncleardataset() {
	for (var i = 1; i <= 15; i++) {
		$('#work_specific'+i).val($('#work_specific_hdn'+i).val());
		$('#quantity'+i).val($('#quantity_hdn'+i).val());
		$('#unit_price'+i).val($('#unit_price_hdn'+i).val()).trigger('onkeyup');
		$('#remarks'+i).val($('#remarks_hdn'+i).val());
		$('#work_specific'+i).attr('disabled', false);
		$('#quantity'+i).attr('disabled', false);
		$('#unit_price'+i).attr('disabled', false);
		$('#fordisable_hdn'+i).val(0);
	}
	$('#getdetails').removeClass( "btn-disabled disabled");
	$('#getdetails').addClass( "btn-success");
	$('#undo').removeClass( "btn-success");
	$('#undo').addClass( "btn-disabled disabled");
}
function fngetbillingdetails() {
	/*var custid = "CST00400";
	var branchid = "CST00401";*/
	var custid = $('#customer_id').val();
	var branchid = $('#branchname_sel').val();
	var obj = jQuery.parseJSON(selectjsonArray);
	branchid = obj[branchid];
	$.ajax({
        type:"GET",
        dataType: "JSON",
        url: 'ajaxgetbillingdetails',
        data: {
            custid: custid,
            branchid: branchid
        },
        success: function(data){
        	var loop = 2;
        	if (data.length > 0) {
				for (var i = 1; i <= 15; i++) {
					$('#work_specific'+i).val("");
	        		$('#quantity'+i).val("");
	        		$('#unit_price'+i).val("");
	        		$('#amount'+i).val("");
	        		$('#remarks'+i).val("");
	        		$('#work_specific'+i).attr('disabled',false);
	        		$('#quantity'+i).attr('disabled',false);
					$('#unit_price'+i).attr('disabled',false);
					$('#fordisable_hdn'+i).val(0);
				}
	        	$.each(data, 
	        		function(i, item) {
		        		$('#work_specific'+loop).val(item.NickName);
		        		$('#quantity'+loop).val("1.0");
		        		$('#unit_price'+loop).val(item.Amount).trigger('onkeyup');
		        		$('#work_specific'+loop).attr('disabled','disabled');
		        		$('#quantity'+loop).attr('disabled','disabled');
		        		$('#unit_price'+loop).attr('disabled','disabled');
		        		$('#fordisable_hdn'+loop).val(1);
		        		if (item.Quantity > 0) {
		        			loop++;
		        			if (item.Unitprice > 0) {
		        				var ot = "    O.T";
		        			} else {
		        				var ot = "    -O.T";
		        			}
		        			$('#work_specific'+loop).val(ot);
			        		$('#quantity'+loop).val(item.Quantity);
			        		$('#unit_price'+loop).val(item.Unitprice).trigger('onkeyup');
			        		$('#work_specific'+loop).attr('disabled','disabled');
			        		$('#quantity'+loop).attr('disabled','disabled');
							$('#unit_price'+loop).attr('disabled','disabled');
							$('#fordisable_hdn'+loop).val(1);
						}
		        		loop++;
					}
				);
				$('#undo').removeClass( "btn-disabled disabled");
				$('#undo').addClass( "btn-success");
				$('#getdetails').removeClass( "btn-success");
				$('#getdetails').addClass( "btn-disabled disabled");
        	} else {
        		alert(inf_billing);
        	}
        },
        error: function(xhr, textStatus, errorThrown){
        	// alert(xhr.status);
        }  
    });
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
	    $('#frminvoicespec').attr('action','../Estimation/coverdownloadprocess'+'?mainmenu='+mainmenu+'&time='+datetime); 
	    $("#frminvoicespec").submit();
    } else {
    	return false;
    }
}
function popupenableempname(mainmenu, id) {
	popupopenclose(1);
	$('#table_id').val(id);
	$('#empnamepopup').load('../Invoice/empnamepopup?mainmenu='+mainmenu+'&time='+datetime);
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
	var name = empname.concat(" ").concat(name);
	$('#txt_empname').val(name);
	var table_id=$('#table_id').val();
	var kananame = "empKanaNames"+table_id;
	var empids = "emp_ID"+table_id;
	$('#empid').val(empid);
	$('#empKanaName').val(name);
}
function fndbclick(id,empid,empname,name) {
	$("#"+empid).prop("checked", true);
	var name = empname.concat(" ").concat(name);
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
function fngetEmpty(id) {
	var kananame = "empKanaNames"+id;
	var empids = "emp_ID"+id;
	$('#'+kananame).text('');
	$('#'+empids).val('');
	$('#divid'+id).css('display','none');
	$('#crossid'+id).css('display','none');
}
function invoiceexceldownload(mainmenu, selectedyearmonth) {
	var confirm_create="Do you Want to Create Invoice";
    if(confirm(confirm_create)) {
    	$('#selYearMonth').val(selectedyearmonth);
    	$('#frminvoiceexceldownload').attr('action', 'invoiceexceldownloadprocess?mainmenu='+mainmenu+'&time='+datetime);
		$("#frminvoiceexceldownload").submit();
    }
}
function fnassignemployee(yearmonth) {
	pageload();
    $('#frminvoiceindex').attr('action', 'assignemployee?mainmenu='+mainmenu+'&time='+datetime);
	$("#frminvoiceindex").submit();
}
function fninvoicecopy(yearmonth) {
	pageload();
    $('#frminvoiceindex').attr('action', 'invoicecopy?mainmenu='+mainmenu+'&time='+datetime);
	$("#frminvoiceindex").submit();
}
function gotoindexinv(mainmenu) {
	if (cancel_check == false) {
        if (!confirm("Do You Want To Cancel the Page?")) {
            return false;
        }
    }
	pageload();
    $('#involdeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#involdeditcancel").submit();
}
//for up&down key press
// function kewDown(e, curid) {
// 	if (e.keyCode == 40 || e.keyCode == 38) {
// 		var arr = curid.match(/\D+|\d+/g);
// 		var field = arr[0];
// 		var fieldpos = arr[1];
// 		if (e.keyCode == 40) {
// 			fieldpos = fieldpos-(-1);
// 			if (fieldpos == 16) {
// 				fieldpos = 1;
// 			}
// 		} else {
// 			fieldpos = fieldpos-(1);
// 			if (fieldpos == 0) {
// 				fieldpos = 15;
// 			}
// 		}
// 		$('#'+(field+fieldpos)).focus();
//     }
// }