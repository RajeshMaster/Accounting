var data = {};

function pageClick(pageval) {
	$('#page').val(pageval);
	$('#frmextuserindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmextuserindex").submit();
}

function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$('#frmextuserindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmextuserindex").submit();
}

$(document).ready(function() {

	// initialize tooltipster on text input elements
	// initialize validate plugin on the form

	$('.addeditprocess').click(function () {

		$("#frmextuseraddedit").validate({

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

				userName: {required: true},
				emailId: {required: true, email:true},
				userPassword: {required: true},
				userConPassword: {required: true,equalTo: "#userPassword"},
				address: {required: true},
				buildingName: {required: true},
				pincode: {required: true, minlength: 6, number: true},
				userTelNo1: {required: true, minlength: 2, number: true},
				userTelNo2: {required: true, minlength: 4, number: true},
				userTelNo3: {required: true, minlength: 4, number: true},
				bankKanaName: {required: true},
				accountNo: {required: true, minlength: 6, number: true},
				accountType: {required: true},
				bankName: {required: true},
				branchName: {required: true},
				branchNo: {required: true, minlength: 6, number: true},

			},

			submitHandler: function(form) { // for demo

				var editId = $('#editId').val();
				var emailId = $('#emailId').val();

				$.ajax({
					type: 'GET',
					url: 'emailIdExists',
					data: { "editId": editId, "emailId": emailId },

					success: function(resp) {
						if (resp != 0) {
							document.getElementById('errorSectiondisplay').innerHTML = "";
							err_invalidcer = "Email Id Already exists";
							var error='<div align="center" style="padding: 0px;" id="inform">';
							error+='<table cellspacing="0" class="statusBg1" cellpadding="0" border="0">';
							error+='<tbody><tr><td style="padding: 4px 10px" align="center"><span class="innerBg" id="mc_msg_txt">'+err_invalidcer+'</span></td>';
							error+='<td width="20" valign="top" style="padding-top: 4px; _padding-top: 2px;"><span>';
							error+='<a href="javascript:intdisplaymessage();" class="fa fa-times" style="color:white;"/>';
							error+='</span></td>';
							error+='</tr></tbody></table></div>';
							document.getElementById('errorSectiondisplay').style.display = 'block';
							document.getElementById('errorSectiondisplay').innerHTML = error;
							$("#emailId").focus();
							return false;
						} else {
							if($('#editId').val() == "") {
								var confirmprocess = confirm("Do You Want To Register?");
							} else {
								var confirmprocess = confirm("Do You Want To Update?");
							}
							if(confirmprocess) {
								pageload();
								form.submit(); // dont use this cause of double time insert in internet explorer
								return true;
							} else {
								return false;
							}
						}
					},

					error: function(data) {
						// alert(data);
					}

				});

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
			if (input.id == "userTelNo1") {
				return "Please Enter 2 Characters";
			} else if (input.id == "userTelNo2" || input.id == "userTelNo3") {
				return "Please Enter 4 Characters";
			} else if (input.id == "pincode") {
				return "Please Enter 6 Characters";
			}

		}

		$('.a-middle').css('margin-top', function () {
			return ($(window).height() - $(this).height()) / 4
	    });

	});

});


function intdisplaymessage() {
	document.getElementById('errorSectiondisplay').style.display='none';
}

function resetErrors() {

	$('form input, form select, form radio').removeClass('inputTxtError');
	$('label.error').remove();
}

function underConstruction() {
	alert("Under Construction");
}

function addedit(type) {
	$('#editflg').val(type);
	$('#frmextuserindex').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmextuserindex").submit();
}

function addeditview(type,id) {
	$('#editflg').val(type);
	$('#editId').val(id);
	$('#frmextuserview').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmextuserview").submit();
}

function userView(id) {
	$('#viewId').val(id);
	$('#frmextuserindex').attr('action', 'userView?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmextuserindex").submit();
}

function gotoindexpage(viewflg) {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}

	if (viewflg == "1") {
		pageload();
		$('#frmextuseraddeditcancel').attr('action', 'userView?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmextuseraddeditcancel").submit();
	} else {
		pageload();
		$('#frmextuseraddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmextuseraddeditcancel").submit();
	}
}

function backindexpage() {
	pageload();
	$('#frmextuserview').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmextuserview").submit();
}

function changeDelFlg(id,delflg) {
	if (confirm("Do You Want To Change The User Status?")) {
		$("#id").val(id);
		$("#delflg").val(delflg);
		pageload();
		$('#frmextuserindex').attr('action', 'changeDelFlg?mainmenu='+mainmenu+'&time='+datetime);
		$('#frmextuserindex').submit();
	}
}

function nextfield(input1,input2,length,event) {
	var event = event.keyCode || event.charCode;
	if(event != 8){
		if(document.getElementById(input1).value.length == length) {
			document.getElementById(input2).focus();
		}
	}
}
