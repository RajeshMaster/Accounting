var data = {};
$(document).ready(function() {
	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.addeditprocess').click(function () {
		$("#bankdetailaddedit").validate({
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
			txt_startdate: {required: true,date:true,correctformatdate: true},
			txt_salary: {required: true,money: true},
		},
			submitHandler: function(form) { // for demo
				if($('#editflg').val() == "1") {
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
	});
});
function UnderConstruction() {
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
		$('#selMonth').val(("0" + month).substr(-2));
		$('#selYear').val(year);
		$('#prevcnt').val(prevcnt);
		$('#nextcnt').val(nextcnt);
		$('#account_val').val(account_val);
 		$('#page').val('');
		$('#plimit').val('');
		$('#bankdetailsview').submit();
	}
}
function pageClick(pageval) {
	$('#page').val(pageval);
	if($('#checkflg').val()=="1"){
		$("#bankdetailsview").submit();
	}else if($('#checkflg').val()=="") {
		$("#bankdetailsindex").submit();
	}
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	if($('#checkflg').val()=="1"){
		$("#bankdetailsview").submit();
	}else if($('#checkflg').val()=="") {
		$("#bankdetailsindex").submit();
	}
}
function gotoadd(bnkname,branchname,accno,startdate,bank_id,bankid,branchid,balbankid,id,mainmenu,flg) {
	pageload();
	$('#bankid').val(bank_id);
	$('#bankname').val(bnkname);
	$('#branchname').val(branchname);
	$('#accno').val(accno);
	$('#id').val(id);
	$('#startdate').val(startdate);
	$('#bankids').val(bankid);
	$('#branchids').val(branchid);
	$('#balbankid').val(balbankid);
	$('#editflg').val(flg);
	$('#bankdetailsindex').attr('action', 'add?mainmenu='+mainmenu+'&time='+datetime);
	$("#bankdetailsindex").submit();
}
function gotoeditpage(startdate,balance,mainmenu,flg) {
	pageload();
	$('#start_date').val(startdate);
	$('#balance').val(balance);
	$('#editflg').val(flg);
	$('#bankdetailsview').attr('action', 'edit?mainmenu='+mainmenu+'&time='+datetime);
	$("#bankdetailsview").submit();
}
function gosingletoindex(mainmenu) {
	pageload();
	$('#checkflg').val('');
	$('#bankdetailsview').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#bankdetailsview").submit();
}
function gotoviewlist(bnkname,branchname,accno,startdate,bank_id,bankid,branchid,balbankid,id,mainmenu,checkflg) {
	pageload();
	$('#bankid').val(bank_id);
	$('#bankname').val(bnkname);
	$('#branchname').val(branchname);
	$('#accno').val(accno);
	$('#id').val(id);
	$('#startdate').val(startdate);
	$('#bankids').val(bankid);
	$('#branchids').val(branchid);
	$('#checkflg').val(checkflg);
	$('#balbankid').val(balbankid);
	$('#bankdetailsindex').attr('action', 'Viewlist?mainmenu='+mainmenu+'&time='+datetime);
	$("#bankdetailsindex").submit();
}
function fnchk(pay,idcheck) {
	$('#idcheck').val(idcheck);
	$('#pay').val(pay);
	var confirmprocess = confirm("Are You Confirm to mark as Checked?");
	if(confirmprocess) {
		pageload();
		$('#bankdetailsview').attr('action', 'checked?mainmenu='+mainmenu+'&time='+datetime);
		$("#bankdetailsview").submit();
	} else {
		return false;
	}
}
function gotoindexpage(viewflg,mainmenu) {
    if (cancel_check == false) {
        if (!confirm("Do You Want To Cancel the Page?")) {
            return false;
        }
    }
    if (viewflg == "1") {
      pageload();
        $('#bankdetailaddeditcancel').attr('action', 'Viewlist?mainmenu='+mainmenu+'&time='+datetime);
        $("#bankdetailaddeditcancel").submit();
    } else {
      pageload();
        $('#bankdetailaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
        $("#bankdetailaddeditcancel").submit();
    }
}
function filterview(sendfilter) {
	pageload();
	$('#detfilter').val(sendfilter);
	$('#page').val('');
	$('#plimit').val('');
	$('#bankdetailsview').attr('action', 'Viewlist?mainmenu='+mainmenu+'&time='+datetime);
	$("#bankdetailsview").submit();
}