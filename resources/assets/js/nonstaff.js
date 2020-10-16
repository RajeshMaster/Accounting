$(function () {
	var cc = 0;
	$('#nonstaffsort').click(function () {
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
		$('#nonstaffsort').animate({
			'marginRight' : movediv //moves down
		});
		ccd++;
		if( $('#searchmethod').val() == 1 || $('#searchmethod').val() == 2){
			ccd--;
		}  
	}); 
});
function staffview(id) {
	pageload();
	var mainmenu = "nonstaff";
	$('#viewid').val(id);
	$('#nonemployeefrm').attr('action', 'nonstaffview?mainmenu='+mainmenu+'&time='+datetime);
	$("#nonemployeefrm").submit();
}
function selectActive(val) {
	pageload();
	$('#resignid').val(val);
	$("#nonemployeefrm").submit();
}
function selectbox(){
	var sort = $('#nonstaffsort').val();
	$('#sorting').val(sort);
	$("#nonemployeefrm").submit();
}
function checkSubmitsingle(e) {
   	if(e && e.keyCode == 13) {
   		usinglesearch();
   	}
}
function checkSubmitmulti(e) {
   	if(e && e.keyCode == 13) {
   		amultiplesearch();
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
    $('#msearchempid').val('');
	$("#nonemployeefrm").submit();
}
function usinglesearch() {
    var mainmenu='nonstaff';
	var singlesearchtxt = $("#singlesearch").val();
	if (singlesearchtxt == "") {
		alert("Please Enter The Non Staff Search.");
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
		$('#nonemployeefrm').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#nonemployeefrm").submit();
	}
}
function amultiplesearch() {
	var mainmenu="nonstaff";
	var employeeno = $("#employeeno").val();
	var startdate = $("#startdate").val();
	var enddate = $("#enddate").val();
	if (employeeno == "" && startdate == "" && enddate == "") {
		alert("NonStaff search is missing.");
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
	    $('#nonemployeefrm').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	    $("#nonemployeefrm").submit();
	}
}
function sortingfun() {
    pageload();
    $('#plimit').val(50);
    $('#page').val('');
    var sortselect=$('#nonstaffsort').val();
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
   $("#nonemployeefrm").submit();
}
function selectActive(val) {
	$('#resignid').val(val);
	$("#nonemployeefrm").submit();
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
function pageClick(pageval) {
	$('#page').val(pageval);
	$("#nonemployeefrm").submit();
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$("#nonemployeefrm").submit();
}
function nonstaff() {
	var mainmenu="nonstaff";
	$('#nonemployeefrm').attr('action', 'nonstaffadd?mainmenu='+mainmenu+'&time='+datetime);
	$("#nonemployeefrm").submit();
}
function gotoidxpage(viewflg,mainmenu) {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
	if (viewflg == "1") {
		pageload();
		$('#frmnonstaffaddeditcancel').attr('action', 'nonstaffview?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmnonstaffaddeditcancel").submit();
	} else {
		pageload();
		$('#frmnonstaffaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmnonstaffaddeditcancel").submit();
	}
}
function fnunderconstr() {
	alert("Under Construction");
}
function goindexpage(mainmenu) {
	var mainmenu="nonstaff";
	$('#frmnonstaffview').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmnonstaffview").submit();
}
function editview(type,id) {
	var mainmenu="nonstaff";
	$('#editflg').val(type);
	$('#editid').val(id);
	$('#frmnonstaffview').attr('action', 'nonstaffadd?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmnonstaffview").submit();
}
$(document).ready(function() {
	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.nonstfaddeditprocess').click(function () {
		$("#frmnonstaffaddedit").validate({
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