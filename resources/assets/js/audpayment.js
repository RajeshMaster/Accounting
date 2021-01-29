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









