var data = {};
$(function () {
	var cc = 0;
	$('#staffsort').click(function () {
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
		$('#staffsort').animate({
			'marginRight' : movediv //moves down
		});
		ccd++;
		if( $('#searchmethod').val() == 1 || $('#searchmethod').val() == 2){
			ccd--;
		}  
	});
});
function staffview(id) { 
	var mainmenu="staff";
	$('#viewid').val(id);
	// $('#title').val('2');
	$('#employeefrm').attr('action', 'view?mainmenu='+mainmenu+'&time='+datetime);
	$("#employeefrm").submit();
}
function goindexpage(mainmenu) {
	var mainmenu="staff";
    $('#frmstaffview').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
    $("#frmstaffview").submit();
}
function resignpage(mainmenu,empid,resignid) {
		$('#viewid').val(empid);
		$('#resignid').val(resignid);
    	$('#resign').load('resign?resignid='+resignid+'&empid='+empid+'&mainmenu='+mainmenu+'&time='+datetime);
		$("#resign").modal({
	           backdrop: 'static',
	            keyboard: false
	        });
	    $('#resign').modal('show');
}
function rejoinpage(mainmenu,empid,resignid) {
	 var rejoin= "Do You Want To Rejoin?";
    if(confirm(rejoin)) {
    	$('#frmstaffview').attr('action', 'rejoin?mainmenu='+mainmenu+'&time='+datetime);
        $("#frmstaffview").submit();
    }
}
function editview(type,id) {
	var mainmenu="staff";
	$('#editflg').val(type);
	$('#editid').val(id);
	$('#frmstaffview').attr('action', 'staffaddedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmstaffview").submit();
}
function clearAll(div) {
    if (div == 1) {
       document.getElementById('employeeno').value = '';
        document.getElementById('startdate').value = '';
        document.getElementById('enddate').value = '';
        document.getElementById('employeename').value = '';
        // document.getElementById('single_table').style.display = "none";
        document.getElementById('multisearch').style.display = "block";
    } else {
        document.getElementById('employeeno').value = '';
        document.getElementById('startdate').value = '';
        document.getElementById('enddate').value = '';
        document.getElementById('employeename').value = '';
        //document.getElementById('single_table').style.display = "block";
        document.getElementById('multisearch').style.display = "none";
    }
}

function sortingfun() {
    pageload();
    $('#plimit').val(50);
    $('#page').val('');
    var sortselect=$('#staffsort').val();
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
   $("#employeefrm").submit();
}
function selectActive(val,title) {
	// document.getElementById('pageclick').value = '1';
	// document.employeefrm.resignid.value = val;
	// document.employeefrm.submit();
	$('#plimit').val(50);
    $('#page').val('');
	$('#resignid').val(val);
	$('#title').val(title);
	//$('#employeefrm').attr('action', 'singleview?mainmenu='+mainmenu+'&time='+datetime);
	$("#employeefrm").submit();
}
function selectbox(){
	//alert();
	var sort = $('#staffsort').val();
	//alert(sort);
	$('#sorting').val(sort);
	$("#employeefrm").submit();
}
function usinglesearch() {
    var mainmenu='staff';
	var singlesearchtxt = $("#singlesearch").val();
	if (singlesearchtxt == "") {
		alert("Please Enter The Staff Search.");
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
		$('#employeefrm').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#employeefrm").submit();
	}
}
function staffaddedit(type) {
	var mainmenu="staff";
	$('#editflg').val(type);
	$('#employeefrm').attr('action', 'staffaddedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#employeefrm").submit();
}
$(document).ready(function() {
	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.addeditprocess').click(function () {
		$("#frmstaffaddedit").validate({
			showErrors: function(errorMap, errorList) {
			// Clean up any tooltips for valid elements
				$.each(this.validElements(), function (index, element) {
						var $element = $(element);
						$element.data("title", "") // Clear the title - there is no error associated anymore
								.removeClass("error")
								.tooltip("destroy");
				});
				// Create new tooltips for invalid elements
				$.each(errorList, function (index, error) {
						var $element = $(error.element);
						$element.tooltip("destroy") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
								.data("title", error.message)
								.addClass("error")
								.tooltip(); // Create a new tooltip based on the error messsage we just set in the title
				});
			},
			rules: {
				EmployeeId: {required: true},
				OpenDate: {required: true, date: true,correctformatdate: true},
				Surname: {required: true},
				Name: {required: true},
				NinkName: {required: true},
				Gender: {required: true},
				picture: {extension: "jpeg|jpg|png|gif|bmp", filesize : (2 * 1024 * 1024)},
				DateofBirth: {required: true, date: true,correctformatdate: true,DOB : "#DOB"},
				BankName: {required: true},
				BranchName: {required: true},
				AccountNo: {required: true},
				BranchNo: {required: true},
			},
			submitHandler: function(form) { // for demo
				if($('#editid').val() == "") {
					var confirmprocess = confirm("Do You Want To Register?");
				} else {
					var confirmprocess = confirm("Do You Want To Update?");
				}
				 if(confirmprocess) {
					pageload();
					form.submit();
					return true;
				} else {
					return false;
				}
			}
		});
		$.validator.messages.required = function (param, input) {
			var article = document.getElementById(input.id);
			return article.dataset.label + err_fieldreq;
		}
		$.validator.messages.extension = function (param, input) {
			return err_extension;
		}
	});
});
function gotoindexpage(viewflg,mainmenu) {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
    if (viewflg == "1") {
    	pageload();
        $('#frmstaffaddeditcancel').attr('action', 'view?mainmenu='+mainmenu+'&time='+datetime);
        $("#frmstaffaddeditcancel").submit();
    } else {
    	pageload();
        $('#frmstaffaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
        $("#frmstaffaddeditcancel").submit();
    }
	
}
function umultiplesearch() {
    var mainmenu='staff';
	var employeeno = $("#employeeno").val();
	var employeeno = document.getElementById('employeeno').value;
	var employeename = $("#employeename").val();
	var employeename = document.getElementById('employeename').value;
	var startdate = $("#startdate").val();
	var startdate = document.getElementById('startdate').value;
	var enddate = $("#enddate").val();
	var enddate = document.getElementById('enddate').value;
	if (employeeno == "" && employeename == "" && startdate == "" && enddate == "") {
		alert("Staff search is missing.");
		$("#employeeno").focus(); 
		return false;
    } else if (Date.parse(startdate) > Date.parse(enddate)) {
        alert("Please enter date greater than startdate");
         document.getElementById('enddate').focus();
        return false;  
	} else {
		$('#plimit').val(50);
	    $('#page').val('');
	    $('#singlesearch').val('');
	    $("#searchmethod").val(2);
	    $('#employeefrm').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	    $("#employeefrm").submit();
	}
}

function pageClick(pageval) {
	$('#page').val(pageval);
	$("#employeefrm").submit();
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$("#employeefrm").submit();
}
function clearsearch() {
    $('#plimit').val(50);
    $('#page').val('');
    //$('#sortOrder').val('asc'); 
    $('#singlesearch').val('');
    $('#employeeno').val('');
    $('#employeename').val('');
    $('#startdate').val('');
    $('#enddate').val('');
    $('#searchmethod').val('');
    $('#msearchempid').val('');
    $("#employeefrm").submit();
}
//open Popup
function importpopupenable(mainmenu) {
	 popupopenclose(1);
	$('#importpopup').load('importpopup?mainmenu='+mainmenu+'&time='+datetime);
	$("#importpopup").modal({
           backdrop: 'static',
            keyboard: false
        });
    $('#importpopup').modal('show');
}

function gotosalaryview(empid,lastname,DOJ,datetime) {
	pageload();
  	$('#empid').val(empid);
	$('#empname').val(lastname);
	$('#DOJ').val(DOJ);
   	$('#hdnback').val();
	var mainmenu="staff";
    $('#employeefrm').attr('action', '../StaffSalary/viewsalary?mainmenu='+mainmenu+'&time='+datetime);
    $("#employeefrm").submit();
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
function billinghistory(empid,lastname,datetime){
	pageload();
	$('#empid').val(empid);
	$('#empname').val(lastname);
   	$('#hdnback').val();
	var mainmenu="billing";
	$('#employeefrm').attr('action', '../Billing/billhistory?mainmenu='+mainmenu+'&time='+datetime);
    $("#employeefrm").submit();
}
function cushistory(empid,lastname,datetime){
	pageload();
	$('#id').val(empid);
	$('#hdnempid').val(empid);
	$('#hdnempname').val(lastname);
   	$('#hdnback').val();
	var mainmenu="Customer";
	$('#employeefrm').attr('action', '../Customer/Onsitehistory?mainmenu='+mainmenu+'&time='+datetime);
    $("#employeefrm").submit();
}
function timesheethistory(empid,lastname,datetime){
	//alert(lastname);
	pageload();
	$('#empid').val(empid);
	$('#empname').val(lastname);
   	$('#hdnback').val();
	var mainmenu="timesheet";
	$('#employeefrm').attr('action', '../Timesheet/timeSheetHistorydetails?mainmenu='+mainmenu+'&time='+datetime);
    $("#employeefrm").submit();
}