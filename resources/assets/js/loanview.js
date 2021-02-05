var data = {};

function pageClick(pageval) {
	$('#page').val(pageval);
	$("#loanDetailsIndex").submit();
}

function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$("#loanDetailsIndex").submit();
}

function getDataMonth(selMonth, selYear,time) {
    $('#page').val('');
    $('#plimit').val('');
    $('#selMonth').val(selMonth);
    $('#selYear').val(selYear);
    $('#parentmonth').val(selMonth);
    $('#parentyr').val(selYear);
    var mainmenu = $('#mainmenu').val();
    $('#loanDetailsIndex').attr('action', 'index?mainmenu='+mainmenu+'&time='+time);
    $("#loanDetailsIndex").submit();
}

function loanYearWise(){
    var mainmenu = $('#mainmenu').val();
    $('#loanDetailsIndex').attr('action', 'listview?mainmenu='+mainmenu+'&time='+datetime);
    $("#loanDetailsIndex").submit();
}

function loanMonthWise(){
    var mainmenu = $('#mainmenu').val();
    $('#LoanDetailslistview').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
    $("#LoanDetailslistview").submit();
}

