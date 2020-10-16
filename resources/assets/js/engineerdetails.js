$(function () {
	
	var cc = 0;
	$('#engineerdetailssort').click(function () {
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
			movediv = "+=230px"
		} else {
			movediv = "-=230px"
		}
		$('#engineerdetailssort').animate({
			'marginRight' : movediv //moves down
		});
		ccd++;
		if( $('#searchmethod').val() == 1 || $('#searchmethod').val() == 2){
			ccd--;
		}  
	});
});

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
		if ($('#mainmenu').val() == 'engineerexpdetails') {
			$('#enggexpenseindex').attr('action', 'expenseindex?mainmenu='+mainmenu+'&time='+datetime);
			$('#enggexpenseindex').submit();
		} else {
			$('#frmcustomerplusindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
			$('#frmcustomerplusindex').submit();
		}
	}
}
function pageClick(pageval) {
	$('#page').val(pageval);
	if ($('#mainmenu').val() == 'engineerexpdetails') {
		$("#enggexpenseindex").submit();
	} else {
		$("#frmcustomerplusindex").submit();
	}
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	if ($('#mainmenu').val() == 'engineerexpdetails') {
		$("#enggexpenseindex").submit();
	} else {
		$("#frmcustomerplusindex").submit();
	}
}
function sortingfun() {
	pageload();
    $('#plimit').val(50);
    $('#page').val('');
    var sortselect=$('#engineerdetailssort').val();
    $('#sorting').val(sortselect);
    var alreadySelectedOptn=$('#sorting').val();
    var alreadySelectedOptnOrder=$('#sortOrder').val();
    if (sortselect == alreadySelectedOptn) {
        if (alreadySelectedOptnOrder == "asc") {
            $('#sortOrder').val('desc');
        } else {
            $('#sortOrder').val('asc');
        }
    }
    if ($('#mainmenu').val() == 'engineerexpdetails') {
		$("#enggexpenseindex").submit();
	} else {
		$("#frmcustomerplusindex").submit();
	}
}
function clearsearch() {
    $('#plimit').val(50);
    $('#page').val('');
    $('#singlesearch').val('');
    $('#startdate').val('');
    $('#enddate').val('');
    $('#employeeno').val('');
    $('#employeename').val('');
    $('#searchmethod').val('');
	$('#frmcustomerplusindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
    $("#frmcustomerplusindex").submit();
}
function clearsearchexp() {
	$('#plimit').val(50);
    $('#page').val('');
    $('#sorting').val('');
    $('#engineerdetailssort').val('');
    $('#enggexpenseindex').attr('action', 'expenseindex?mainmenu='+mainmenu+'&time='+datetime);
    $("#enggexpenseindex").submit();
}
function usinglesearch() {
	var singlesearchtxt = $("#singlesearch").val();
	if (singlesearchtxt == "") {
		alert("Please Enter The Engineerdetails Search.");
		$("#singlesearch").focus(); 
		return false;
	} else {
		 if ($('#singlesearch').val()) {
         	$("#searchmethod").val(1);
    	} else {
       		$("#searchmethod").val('');
    	}
		$('#plimit').val('');
		$('#page').val('');
		$('#frmcustomerplusindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmcustomerplusindex").submit();
	}
}
function umultiplesearch() {
	var employeeno = $("#employeeno").val();
	var employeeno = document.getElementById('employeeno').value;
	var employeename = $("#employeename").val();
	var employeename = document.getElementById('employeename').value;
	
	if (employeeno == "" && employeename == "") {
		alert("Engineerdetails search is missing.");
		$("#employeeno").focus(); 
		return false;
    }  else {
		$('#plimit').val(50);
	    $('#page').val('');
	    $('#singlesearch').val('');
	    $("#searchmethod").val(2);
	    $('#frmcustomerplusindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	    $("#frmcustomerplusindex").submit();
	}
}
function checkSubmitsingle(e) {
   	if(e && e.keyCode == 13) {
   		usinglesearch();
   	}
}
function checkSubmitmulti(e) {
   	if(e && e.keyCode == 13) {
   		umultiplesearch();
   	}
}
function engineeridclick(EMPID){ 
	pageload();
	$('#engineeridClick').val(EMPID);
	$('#EMPID').val('');
	$('#startdate').val('');
	$('#enddate').val('');
	$('#projecttype').val('');
	$('#estimateno').val('');
	$('#singlesearchtxt').val('');
	$('#hdnempid').val('');
    $("#searchmethod").val(3);
    $('#frmcustomerplusindex').attr('action','index'+'?mainmenu='+mainmenu+'&time='+datetime); 
    $("#frmcustomerplusindex").submit();
}
function filedownload(path,file) {
	var confirm_download = "Do You Want To Download?";
    if(confirm(confirm_download)) {
        window.location.href="../app/Http/Common/downloadfile.php?file="+file+"&path="+path+"/";
    }
}
function fngetEmpDet(empid) {
	pageload();
	$('#empid').val(empid);
	$('#historypage').val('1');
	$('#enggexpenseindex').attr('action', 'expenseindex?mainmenu='+mainmenu+'&time='+datetime);
	$('#enggexpenseindex').submit();
}