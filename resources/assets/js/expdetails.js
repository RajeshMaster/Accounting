function filter(val) {
	var setdate = new Date();
	var setyear = setdate.getFullYear();
	var setmonth = setdate.getMonth()+1;
		$('#selYear').val(setyear);
		$('#selMonth').val(setmonth);
		$('#previou_next_year').val('');
		$('#account_val').val('');
		$('#active_select').val(val);
		$('#filter').val('');
		$('#plimit').val('');
		$('#page').val('');
		// $('#frmexpensesdetailsindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$('#frmexpensesdetailsindex').submit();
}
function unitfilter(val) {
		$('#firstclick').val('');
		$('#filter').val(val);
		$('#plimit').val('');
		$('#page').val('');
		// $('#frmexpensesdetailsindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$('#frmexpensesdetailsindex').submit();
}
function getData(month, year, flg, prevcnt, nextcnt, account_period, lastyear, currentyear, account_val) {
	var yearmonth = year + "-" +  ("0" + month).substr(-2);
	if ((prevcnt == 0) && (flg == 0) && (parseInt(month) < account_period) && (year == lastyear)) {
		alert("No Previous Record.");
	} else if ((nextcnt == 0) && (flg == 0) && (parseInt(month) > account_period) && (year == currentyear)) {
		alert("No Next Record.");
	} else {
		if (flg == 1) {
			document.getElementById('previou_next_year').value = year + "-" +  ("0" + month).substr(-2);
		}
		document.getElementById('selMonth').value = month;
		document.getElementById('selYear').value = year;
		document.getElementById('prevcnt').value = prevcnt;
		document.getElementById('nextcnt').value = nextcnt;
		document.getElementById('active_select').value = 3;
		document.getElementById('account_val').value = account_val;
		$('#frmexpensesdetailsindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$('#frmexpensesdetailsindex').submit();
	}
}
function pageClick(pageval) {
	$('#page').val(pageval);
	var mainmenu= $('#mainmenu').val();
	$('#frmexpensesdetailsindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmexpensesdetailsindex").submit();
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	var mainmenu= $('#mainmenu').val();
	$('#frmexpensesdetailsindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmexpensesdetailsindex").submit();
}
function underconstruction() {
	alert("Under Construction");
}
function gotoexpensestransferhistory(subject,salaryflg,mainmenu,month,yr,flg) {
	pageload();
	// var mainmenu = "expenses";
	$('#salaryflg').val(salaryflg);
	$('#subject').val(subject);
	$('#mainmenu').val(mainmenu);
	$('#selMonth').val('');
	$('#selYear').val('');
	$('#exptype1').val('1');
	$('#expdetails').val(flg);
	$('#frmexpensesdetailsindex').attr('action', '../Expenses/transferhistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmexpensesdetailsindex").submit();
}
function gotosubhistory(id,name,empty,salaryflg,mainmenu,sub,yr,mth,flg) {
	pageload();
	$('#salaryflg').val(salaryflg);
	$('#subject').val(id);
	$('#detail').val(name);
	$('#mainmenu').val(mainmenu);
	$('#expdetails').val(flg);
	$('#selMonth').val('');
	$('#selYear').val('');
	$('#frmexpensesdetailsindex').attr('action', '../Expenses/transfersubhistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmexpensesdetailsindex").submit();

}