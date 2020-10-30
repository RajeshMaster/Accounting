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
		$('#frmaudpaymentindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$('#frmaudpaymentindex').submit();

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

    $("#frmaudpaymentindex").submit();
}

function pageClick(pageval) {
	$('#page').val(pageval);
	var mainmenu= $('#mainmenu').val();
	$('#frmaudpaymentindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmaudpaymentindex").submit();
}

function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	var mainmenu= $('#mainmenu').val();
	$('#frmaudpaymentindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmaudpaymentindex").submit();
}

function fngotospecification(payid,invoiceid) {
	$('#payid').val(payid);
	$('#invoiceid').val(invoiceid);
	$('#frmaudpaymentindex').attr('action','customerspecification'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmaudpaymentindex").submit();
}

function fncustomerview(cname) {
    $('#companyname').val(cname);
    $('#frmaudpaymentindex').attr('action','customerview'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmaudpaymentindex").submit();
}









