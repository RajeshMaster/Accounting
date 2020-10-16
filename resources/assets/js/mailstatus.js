function pageClick(pageval) {
	$('#page').val(pageval);
	$('#frmmailstatusindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmmailstatusindex").submit();
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$('#frmmailstatusindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmmailstatusindex").submit();
}
function fnstatusView(id) {
	pageload();
	$('#statusid').val(id);
	$('#frmmailstatusindex').attr('action', 'mailstatusview?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmmailstatusindex").submit();
}
function fngotoindex(flg) {
	if(flg == 1) {
		pageload();
		$('#frmmailstatusview').attr('action', 'mailhistory?mainmenu='+mainmenu+'&time='+datetime);
	    $("#frmmailstatusview").submit();
	} else {
		pageload();
		$('#frmmailstatusview').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	    $("#frmmailstatusview").submit();
	}
}
function filedownload(path,file) {
	var confirm_download = "Do You Want To Download?";
    if(confirm(confirm_download)) {
        window.location.href="../app/Http/Common/downloadfile.php?file="+file+"&path="+path+"/";
    }
}
function filtermail(sendfilter) {
	pageload();
	$('#sendfilter').val(sendfilter);
	$('#page').val('');
	$('#plimit').val('');
	$('#frmmailstatusindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmmailstatusindex").submit();
}
function fnresend() {
	pageload();
	$('#frmmailstatusview').attr('action', '../Estimation/sendmail?mainmenu=estimation&time='+datetime);
	$("#frmmailstatusview").submit();
}
function fnmailhistoryview(customerid,mainmenu,customer_name) {
	pageload();
	$('#hiddenpage').val($('#page').val());
	$('#hiddenplimit').val($('#plimit').val());
	$('#hiddensendfilter').val($('#sendfilter').val());
	$("#sendfilter").val('');
	$("#page").val('');
	$("#plimit").val('');
	$("#customerid").val(customerid);
	$("#customer_name").val(customer_name);
	$('#frmmailstatusindex').attr('action', 'mailhistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmmailstatusindex").submit();
}
function filtermailhistory(sendfilter) {
	pageload();
	$('#historyfilter').val(sendfilter);
	$('#page').val('');
	$('#plimit').val('');
	$('#frmmailhistory').attr('action', 'mailhistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmmailhistory").submit();
}
function goindexpage(mainmenu,filter,page,plimit) {
	$("#customerid").val('');
	$("#customer_name").val('');
	$('#sendfilter').val(filter);
	$('#page').val(page);
	$('#plimit').val(plimit);
    $('#frmmailhistory').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
    $("#frmmailhistory").submit();
}
function fnstatushistoryView(id,flg) {
	pageload();
	$('#statusid').val(id);
	$('#backflg').val(flg);
	$('#frmmailhistory').attr('action', 'mailstatusview?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmmailhistory").submit();
}