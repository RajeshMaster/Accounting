$(document).ready(function() {
	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.addeditprocess').click(function () {
		$("#frmuseraddedit").validate({
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
				MstuserUserID: {required: true},
				MstuserPassword: {required: true},
				MstuserConPassword: {required: true,equalTo: "#MstuserPassword"},
				MstuserUserKbn: {required: true},
				DataView: {correctformatdate: true},
				MstuserSurNM: {required: true},
				MstuserSurNMK: {required: true},
				Mstusernickname: {required: true},
				MstuserBirthDT: {required: true, date: true,correctformatdate: true,DOB : "#DOB"},
				MstuserSex: {required: true},
				MstuserTelNO: {required: true, minlength: 2},
				MstuserTelNO1: {required: true, minlength: 4},
				MstuserTelNO2: {required: true, minlength: 4},
				MstuserMailID: {required: true, email:true},
			},
			submitHandler: function(form) { // for demo
				if($('#editid').val() == "") {
					var confirmprocess = confirm("Do You Want To Register?");
				} else {
					var confirmprocess = confirm("Do You Want To Update?");
				}
				if(confirmprocess) {
					pageload();
					// form.submit(); dont use this cause of double time insert in internet explorer
					return true;
				} else {
					return false
				}
			}
		});
		$.validator.messages.required = function (param, input) {
			var article = document.getElementById(input.id);
			return article.dataset.label + ' field is required';
		}
		$.validator.messages.equalTo = function (param, input) {
			var article = document.getElementById(input.id);
			return passwordmatch;
		}
		$.validator.messages.minlength = function (param, input) {
			var article = document.getElementById(input.id);
			if (input.id == "MstuserTelNO") {
				return "Please Enter 2 Characters";
			} else if (input.id == "MstuserTelNO1" || input.id == "MstuserTelNO2") {
				return "Please Enter 4 Characters";
			}
		}
		$('.a-middle').css('margin-top', function () {
			return ($(window).height() - $(this).height()) / 4
	    });
	});
});
$(document).ready(function() {
// PASSWORRD_CHANGE
$('.frmpasswordchange').click(function () {
		$("#frmpasswordchange").validate({
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
				password: {required: true},
				confirmpassword: {required: true,equalTo: "#password"},
			},
			submitHandler: function(form) { // for demo
				var confirmprocess = confirm("Do You Want To Change The password?");
				if(confirmprocess) {
					pageload();
					form.submit();
					return true;
				} else {
					return false
				}
			}
		});
		$.validator.messages.required = function (param, input) {
			var article = document.getElementById(input.id);
			return article.dataset.label + ' field is required';
		}
		$.validator.messages.equalTo = function (param, input) {
			var article = document.getElementById(input.id);
			return passwordmatch;
		}
	});
});
// END_PASSWORRD_CHANGE
var data = {};
$(function () {
	var cc = 0;
	$('#usersort').click(function () {
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
			movediv = "+=260px"
		} else {
			movediv = "-=260px"
		}
		$('#usersort').animate({
			'marginRight' : movediv //moves down
		});
		ccd++;
		if( $('#searchmethod').val() == 1 || $('#searchmethod').val() == 2){
			ccd--;
		}  
	});
});
function resetErrors() {
	$('form input, form select, form radio').removeClass('inputTxtError');
	$('label.error').remove();
}
function underConstruction() {
	alert("Under Construction");
}
function sortingfun() {
	pageload();
    $('#plimit').val(50);
    $('#page').val('');
    var sortselect=$('#usersort').val();
    var alreadySelectedOptn=$('#sortOptn').val();
    var alreadySelectedOptnOrder=$('#sortOrder').val();
    if (sortselect == alreadySelectedOptn) {
        if (alreadySelectedOptnOrder == "asc") {
            $('#sortOrder').val('desc');
        } else {
            $('#sortOrder').val('asc');
        }
    }
    $("#frmuserindex").submit();
}
function addedit(type) {
	$('#editflg').val(type);
	$('#frmuserindex').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmuserindex").submit();
}
function addeditview(type,id) {
	$('#editflg').val(type);
	$('#editid').val(id);
	$('#frmuserview').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmuserview").submit();
}
function userview(id) {
	var mainmenu="user";
	$('#viewid').val(id);
	$('#frmuserindex').attr('action', 'view?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmuserindex").submit();
}
function filter(val) {
	pageload();
	$("#filterval").val(val);
	$('#plimit').val('');
    $('#page').val('');
	$('#singlesearch').val('');
    $('#searchmethod').val('');
    $('#msearchempid').val('');
    $('#userclassification').val('');
	$('#frmuserindex').submit();
}
function pageClick(pageval) {
	$('#page').val(pageval);
	$("#frmuserindex").submit();
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$("#frmuserindex").submit();
}
function gotoindexpage(viewflg,mainmenu) {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
    if (viewflg == "1") {
    	pageload();
        $('#frmuseraddeditcancel').attr('action', 'view?mainmenu='+mainmenu+'&time='+datetime);
        $("#frmuseraddeditcancel").submit();
    } else {
    	pageload();
        $('#frmuseraddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
        $("#frmuseraddeditcancel").submit();
    }
}
function goindexpage(mainmenu) {
    $('#frmuserview').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
    $("#frmuserview").submit();
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
function usinglesearch() {
    var mainmenu='user';
	var singlesearchtxt = $("#singlesearch").val();
	var singlesearchtxt = document.getElementById('singlesearch').value;
	if (singlesearchtxt == "") {
		alert("Please Enter The User Search.");
		$("#singlesearch").focus(); 
		return false;
	} else {
		$("#searchmethod").val(1);
		$('#plimit').val('');
		$('#msearchempid').val('');
		$('#userclassification').val('');
		$('#page').val('');
		$('#frmuserindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmuserindex").submit();
	}
}
function clearsearch() {
    $('#plimit').val(50);
    $('#page').val('');
    $('#sortOptn').val('');
	$("#filterval").val('');
    $('#sortOrder').val('asc'); 
    $('#singlesearch').val('');
    $('#searchmethod').val('');
    $('#msearchempid').val('');
    $('#userclassification').val('');
    $("#frmuserindex").submit();
}
function umultiplesearch() {
 //    $('#plimit').val(50);
 //    $('#page').val('');
 //    $('#sortOptn').val('');
	// $("#filterval").val('');
 //    $('#sortOrder').val('asc'); 
 //    $('#singlesearch').val('');
 //    $("#searchmethod").val(2);
    // $("#frmuserindex").submit();
    var mainmenu='user';
	var name = $("#msearchempid").val();
	var name = document.getElementById('msearchempid').value;
	var userclassification = $("#userclassification").val();
	var userclassification = document.getElementById('userclassification').value;
	if (name == "" && userclassification == "") {
		alert("User search is missing.");
		$("#name").focus(); 
		return false;
    } else {
    $('#plimit').val(50);
    $('#page').val('');
    $('#sortOptn').val('');
	$("#filterval").val('');
    $('#sortOrder').val('asc'); 
    $('#singlesearch').val('');
    $("#searchmethod").val(2);
    $('#frmuserindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
    $("#frmuserindex").submit();
}
}
function fnchangeflag(userid,delflg) {
    $("#userid").val(userid);
    $("#delflag").val(delflg);
	if (confirm("Do You Want To Change The User Status?")) {
		pageload();
        $('#frmuserindex').submit();
    }
}
function nextfield(input1,input2,length,event){
	var event = event.keyCode || event.charCode;
	if(event!=8){
		if(document.getElementById(input1).value.length == length) {
			document.getElementById(input2).focus();
		}
	}
}
function passwordchange(mainmenu,id) {
	pageload();
	$('#id').val(id);
	$('#viewid').val(id);
	$('#editid').val(id);
    $('#frmuserview').attr('action', 'changepassword?mainmenu='+mainmenu+'&time='+datetime);
    $("#frmuserview").submit();
}
function cancelpassword(viewflg,mainmenu) {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
	pageload();
    $('#frmpasswordchange').attr('action', 'view?mainmenu='+mainmenu+'&time='+datetime);
    $("#frmpasswordchange").submit();
}
function fnopendate(id) {
	if(id != "4" && id != "") {
		$("#hidecheckbox").attr("style", "display:inline-block");
	} else {
		$("#dateView").attr("style", "display:none");
		$("#hidecheckbox").attr("style", "display:none");
	}
	if ($("#MstuserUserKbn option:selected").val() == "4") {
		$("#hidecheckbox").attr("style", "display:none");
	}
}
function fnempty() {
	$("#DataView").val('');
}