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
