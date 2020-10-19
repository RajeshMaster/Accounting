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
	$('#hiddenform').attr('action','../salarycalcplus/view?mainmenu='+mainmenu+'&time='+datetime);
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

function salaryselectpopup_main() {
	var mainmenu = $('#mainmenu').val();
	var year = $('#selYear').val();
	var month = $('#selMonth').val();
	var get_prev_yr = $('#get_prev_yr').val();
	popupopenclose(1);
	$('#salarypopup').load('../salarycalcplus/salarypopup?mainmenu='+mainmenu+'&year='+year+'&month='+month+'&get_prev_yr='+get_prev_yr);
	$("#salarypopup").modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#salarypopup').modal('show');
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

function gotoindex(viewflg,mainmenu) {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
	pageload();
	if ($('#editcheck').val() == 1 || $('#editcheck').val() == 3) {
		$('#editcheck').val('2');
		$('#salaryplusaddeditcancel').attr('action', 'view?mainmenu='+mainmenu+'&time='+datetime);
		$("#salaryplusaddeditcancel").submit();
	} else {
		$('#salaryplusaddeditcancel').attr('action', viewflg+'?mainmenu='+mainmenu+'&time='+datetime);
		$("#salaryplusaddeditcancel").submit();
	}
}
function getdate() {
	$('#date').val(saldate);
}

function getdate_multicheck() {
	$('#txt_startdate').val(saldate);
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
	var date = new Date();
	var year = date.getFullYear();
	var month = date.getMonth()+1;
	$('#page').val(page);
	$('#plimit').val(limit);
	$('#selYear').val(year);
	$('#selMonth').val(month);
	$('#salarycalchistory').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#salarycalchistory").submit();
}

function goindex(viewflg,mainmenu) {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
	pageload();
	$('#salaryplusmultieditcancel').attr('action', viewflg+'?mainmenu='+mainmenu+'&time='+datetime);
	$("#salaryplusmultieditcancel").submit();
}

function gototsalamount() {
	var totalamount = $('#totamt').data('totalamt');

	$.ajax({
        type: 'GET',
        url: 'getsalamount',
        data: $('#addeditsalarycalc').serialize(),
        success: function(resp) {
        	var diff_amt = parseInt(Number(resp.trim().replace(/[, ]+/g, ""))) - parseInt(Number(totalamount.trim().replace(/[, ]+/g, "")));
        	var diff_amnt = '<span style="font-weight: bold;">Dif amt : </span>&nbsp;<span style="color: red;">' + diff_amt.toLocaleString() + '</span>';
        	$("#difference_amount").html(diff_amnt);
        	$('#salamt').val(resp);
        },
        error: function(data) {
            alert(data);
        }
    });
	// $('#transferred').val($('#totamt').text());
}

function getTransferedAmount() {
	var totalamount = $('#totamt').data('totalamt');

	$.ajax({
        type: 'GET',
        url: 'getTransferedAmount',
        data: $('#addeditsalarycalc').serialize(),
        success: function(resp) {
        	var diff_amt = parseInt(Number(resp.trim().replace(/[, ]+/g, ""))) - parseInt(Number(totalamount.trim().replace(/[, ]+/g, "")));
        	var diff_amnt = '<span style="font-weight: bold;">'+lbl_amount_difference+'</span>&nbsp;<span style="color: red;">' + diff_amt.toLocaleString() + '</span>';
        	$("#difference_amount").html(diff_amnt);
        	$('#salamt').val(resp);
        },
        error: function(data) {
            $('#salamt').val('');
        }
    });
	// $('#transferred').val($('#totamt').text());
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

function getlastmonthdetails() {
	var month = $('#month').val();
	var Emp_ID = $('#Emp_ID').val();
	var selYear = $('#selYear').val();
	$.ajax({
        type: 'GET',
        url: 'getlastmonthdet',
        data: {"Emp_ID": Emp_ID,"selYear": selYear,"month": month},
        success: function(resp) {
        	var obj = jQuery.parseJSON(resp);
        	var sum = 0;
        	$("#totamt").text('');
        	$.each( obj, function( index, value ){
        		$('#'+index).val(value);
        		if (index !== 'remarks') {
        			var remnum = Number(value.trim().replace(/[, ]+/g, ""));
			        //add only if the value is number
			        if (!isNaN(remnum) && value.length != 0) {
			            sum += parseFloat(remnum);
			            // $(this).css("background-color", "#FEFFB0");
			        }
        		}
        		
			});
			var isNeg = sum < 0;
		    var amount = isNeg ? sum : Math.abs(sum.toFixed(0));
			var tot = amount.toLocaleString();
			var tott = tot;
			$("#totamt").text(tott);
			$("#totamt").data('totalamt', tott);
        },
        error: function(data) {
            alert(data);
        }
    });
}

function fngodownloadempid(mainmenu) {
	var confirmprocess_download = confirm("Do You Want To Download?");
    if(confirmprocess_download) {
		$('#addeditsalarycalc').attr('action','../salarycalcplus/salarydownloadprocess?mainmenu='+mainmenu+'&time='+datetime);
		$("#addeditsalarycalc").submit();
	}
}

function transferdetailsdownload(mainmenu) {
	$('#hdn_empid_arr').val('');
	var cbChecked = new Array();
	var cbChecked_text_mailflg_0 = new Array();
	var cbChecked_text_mailflg_1 = new Array();
	if($('.checkbox:checkbox:checked').length > 0){
		$('.checkbox:checkbox:checked').each(function() {
			if ($(this).attr("data-mailflg") == 0) {
	      		cbChecked[cbChecked.length] = this.value;            
	      		cbChecked_text_mailflg_0[cbChecked_text_mailflg_0.length] = $(this).attr("data-name-empid").toUpperCase();            
			} else {
	      		cbChecked_text_mailflg_1[cbChecked_text_mailflg_1.length] = $(this).attr("data-name-empid").toUpperCase();            
			}
	    });
	    $('#hdn_empid_arr').val(cbChecked);
	    var confirmprocess_download = confirm("Do You Want To Download?");
	    if(confirmprocess_download) {
			$('#salarycalcindex').attr('action','../salarycalcplus/transferdetailsdownload?mainmenu='+mainmenu+'&time='+datetime);
			$("#salarycalcindex").submit();
		}
	} else {
		alert("Please Select Employee ID");return;
	}
}

// Start Madasamy 03/08/2020
function salaryplusdownload(mainmenu) {

	var variable = $("#selMonth").val();
	if(typeof(variable) != "undefined" && variable !== null) {
	    $("#payrollExcel").val(variable);
	    var form = '#salarycalcindex';
	} else{
		var form = '#salarycalchistoryTotal';
	}

	$('#hdn_empid_arr').val('');
	var cbChecked = new Array();
	var cbChecked_text_mailflg_0 = new Array();
	var cbChecked_text_mailflg_1 = new Array();
	if($('.checkbox:checkbox:checked').length > 0){
		$('.checkbox:checkbox:checked').each(function() {
			if ($(this).attr("data-mailflg") == 0) {
	      		cbChecked[cbChecked.length] = this.value;            
	      		cbChecked_text_mailflg_0[cbChecked_text_mailflg_0.length] = $(this).attr("data-name-empid").toUpperCase();            
			} else {
	      		cbChecked_text_mailflg_1[cbChecked_text_mailflg_1.length] = $(this).attr("data-name-empid").toUpperCase();            
			}
	    });
	    $('#hdn_empid_arr').val(cbChecked);
	    var confirmprocess_download = confirm("Do You Want To Download?");
	    if(confirmprocess_download) {
			$(form).attr('action','../salarycalcplus/salaryplusdownload?mainmenu='+mainmenu+'&time='+datetime);
			$(form).submit();
		}
	} else {
		alert("Please Select Employee ID");return;
	}
}

function salplusPayrollSingledownload(mainmenu,dataCount) {
	if (dataCount != "" && dataCount > 0) {
		var confirmprocess_download = confirm("Do You Want To Download?");
	    if(confirmprocess_download) {
			$('#salarycalchistorydwnld').attr('action','../salarycalcplus/salaryplusPayrollSingleDownload?mainmenu='+mainmenu+'&time='+datetime);
			$("#salarycalchistorydwnld").submit();
		}
	} else {
		alert('No data found...'); return false;
	}
}

function historyTotal(mainmenu){
	$('#salarycalcindex').attr('action','../salarycalcplus/historyTotal?mainmenu='+mainmenu+'&time='+datetime);
	$("#salarycalcindex").submit();
}
// End Madasamy 03/08/2020
