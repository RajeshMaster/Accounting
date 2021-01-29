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
	$('#frmAuditingindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmAuditingindex").submit();
}
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
		$('#frmAuditingindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$('#frmAuditingindex').submit();
	}
}
function pageClick(pageval) {
	$('#page').val(pageval);
	var mainmenu= $('#mainmenu').val();
	$('#frmAuditingindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmAuditingindex").submit();
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	var mainmenu= $('#mainmenu').val();
	$('#frmAuditingindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmAuditingindex").submit();
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
	$('#frmAuditingindex').attr('action','index'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmAuditingindex").submit();
}
function invoiceexceldownload(mainmenu, selectedyearmonth) {
	var confirm_create = "Do you Want to Create Invoice";
	if(confirm(confirm_create)) {
		$('#selYearMonth').val(selectedyearmonth);
		$('#frmAuditingexceldownload').attr('action', 'auditingexldwnldprocess?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmAuditingexceldownload").submit();
	}
}
function filter(val) {
	$("#filter").val(val);
	$('#plimit').val('');
	$('#pageclick').val('');
	$('#sorting').val('');
	$('#searchmethod').val(6);
	$('#frmAuditingindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$('#frmAuditingindex').submit();
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
	$("#frmAuditingindex").submit();
}

function confirmProcess(flg,invcId,i) {
	
	$.ajax({
		type: 'GET',
		dataType: "JSON",
		url: 'confirmProcess_ajax',
		data: {"flg": flg,
				"invcId": invcId},
		success: function(resp) {
			if (resp) {
				if (flg == 0) {
					$(".aaaaa"+i).css("display", "");
					$(".bbbb"+i).css("display", "none");
				} else {
					$(".bbbb"+i).css("display", "");
					$(".aaaaa"+i).css("display", "none");
				}
			}
		},
		error: function(data) {
			// alert(data.status);
		}
	});
}

function allpdfdownloadaudit(mainmenu,countData) {
	var confirm_create="Do you Want to Create Invoice?";
	if(confirm(confirm_create)) {
		$('#frmallinvoicepdfdownloadAudit').attr('action', 'invoiceallPdfdownloadAudit?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmallinvoicepdfdownloadAudit").submit();
	}
}