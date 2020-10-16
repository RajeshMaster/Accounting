$(document).ready(function() {
	$("#checkall").change(function(){  //"select all" change 
	    $(".checkbox").prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
	});

	//".checkbox" change 
	$('.checkbox').change(function(){ 
		//uncheck "select all", if one of the listed checkbox item is unchecked
	    if(false == $(this).prop("checked")){ //if this item is unchecked
	        $("#checkall").prop('checked', false); //change "select all" checked status to false
	    }
		//check "select all" if all checkbox items are checked
		if ($('.checkbox:checked').length == $('.checkbox').length ){
			$("#checkall").prop('checked', true);
		}
	});
	
});

function displaymessage() {
    document.getElementById('errorSectiondisplay').style.display='none';
}
function gotoviewpage() {
	pageload();
	$('#hiddenform').attr('action','../salarycalc/view?mainmenu='+mainmenu+'&time='+datetime);
	$("#hiddenform").submit();
}

function pageClick(pageval) {
	$('#page').val(pageval);
	$("#salarycalcindex").submit();
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$("#salarycalcindex").submit();
}

function getData(month, year, flg, prevcnt, nextcnt, account_period, lastyear, currentyear, account_val) {
	var yearmonth = year + "-" +  ("0" + month).substr(-2);
	var mainmenu = $('#mainmenu').val();
	if ((prevcnt == 0) && (flg == 0) && (parseInt(month) < account_period) && (year == lastyear)) {
		alert(err_no_previous_record);
	} else if ((nextcnt == 0) && (flg == 0) && (parseInt(month) > account_period) && (year == currentyear)) {
		alert(err_no_next_record);
	} else {
		if (flg == 1) {
			document.getElementById('previou_next_year').value = year + "-" +  ("0" + month).substr(-2);
		}
	document.getElementById('selMonth').value = month;
	document.getElementById('selYear').value = year;
	document.getElementById('prevcnt').value = prevcnt;
	document.getElementById('nextcnt').value = nextcnt;
	document.getElementById('account_val').value = account_val;
	$('#pageclick').val('');
	$('#page').val('');
	$('#plimit').val('');
	$('#get_prev_yr').val('1');
	$('#salarycalcindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#salarycalcindex").submit();
	}
}

function salarydownload(val) {
	var mainmenu = $('#mainmenu').val();
	if(val == 1){
		var confirmprocess_download = confirm("Do You Want To Download?");
	    if(confirmprocess_download) {
			$("#salcalcindexExcel").submit();
		}
	} else {
		alert('No data found...'); return false;
	}
}

function fngotoadd(id,empid,editcheck,mainmenu,firstname,lastname) {
	pageload();
	$('#id').val(id);
	$('#firstname').val(firstname);
	$('#lastname').val(lastname);
	$('#Emp_ID').val(empid);
	$('#editcheck').val('1');
	$('#salarycalcindex').attr('action', 'view?mainmenu='+mainmenu+'&time='+datetime);
	$("#salarycalcindex").submit();
}

function gotoindexsalarycalc(mainmenu) {
	pageload();
	$('#addeditsalarycalc').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#addeditsalarycalc").submit();
}

function undercos() {
	alert('Under Construction');
}

function fngotohistory(empid,mainmenu,firstname,lastname) {
	$('#hiddenplimit').val($('#plimit').val());
	$('#hiddenpage').val($('#page').val());
	$('#Emp_ID').val(empid);
	$('#firstname').val(firstname);
	$('#lastname').val(lastname);
	$('#salarycalcindex').attr('action', 'history?mainmenu='+mainmenu+'&time='+datetime);
	$("#salarycalcindex").submit();
}

function gotoindexback(mainmenu,limit,page) {
	$('#page').val(page);
	$('#plimit').val(limit);

	var today = new Date();
	$('#selMonth').val(today.getMonth() + 1);
	$('#selYear').val(today.getFullYear());

	$('#salarycalchistory').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#salarycalchistory").submit();
}

function gotoamount() {
	$('#transferred').val($('#totamt').text());
}
function gotoemployeewise() {
	pageload();
	$('#tblchg').val('0');
	$('#salarycalcindex').submit();
}

function gotomaster() {
	pageload();
	$('#tblchg').val('1');
	$('#salarycalcindex').submit();
}