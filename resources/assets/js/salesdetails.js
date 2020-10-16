function filter(val) {
		pageload();
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
		$('#frmsalesindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$('#frmsalesindex').submit();
}
function unitfilter(val) {
		pageload();
		$('#firstclick').val('');
		$('#filter').val(val);
		$('#plimit').val('');
		$('#page').val('');
		$('#frmsalesindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$('#frmsalesindex').submit();
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
		$('#frmsalesindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$('#frmsalesindex').submit();
	}
}
function pageClick(pageval) {
	$('#page').val(pageval);
	var mainmenu= $('#mainmenu').val();
	$('#frmsalesindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmsalesindex").submit();
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	var mainmenu= $('#mainmenu').val();
	$('#frmsalesindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmsalesindex").submit();
}
function Activemonth_invoice(yearmonth,period) {
	
	document.getElementById('selyearmonth').value = yearmonth;
	document.getElementById('account_val').value = period;
	document.getElementById('date_month').value = yearmonth;
	$('#frmsalesindex').attr('action', '../Invoice/index'+'?mainmenu='+mainmenu+'&time='+datetime); 
    $("#frmsalesindex").submit();
}
function Salesdetailsexceldownload(val, filter) {
	var confirm_create="Do you Want to Create Sales Details";
    if(confirm(confirm_create)) {
    	var setdate = new Date();
		var setyear = setdate.getFullYear();
		var setmonth = setdate.getMonth()+1;
		$('#selYear').val(setyear);
		$('#selMonth').val(setmonth);
    	$('#actionName').val("salesexceldownload");
    	$('#active_select').val(val);
    	$('#filter').val(filter);
    	$('#frmsalesexceldownload').attr('action', 'salesexceldownloadprocess?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmsalesexceldownload").submit();
    }
}