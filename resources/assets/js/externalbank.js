var data = {};

function pageClick(pageval) {
	$('#page').val(pageval);
	$('#frmextbankindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmextbankindex").submit();
}

function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$('#frmextbankindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmextbankindex").submit();
}

$(document).ready(function() {

	// initialize tooltipster on text input elements
	// initialize validate plugin on the form

	$('.addeditprocess').click(function () {

		$("#frmextbankaddedit").validate({

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

				bankKanaName: {required: true},
				accountNo: {required: true, minlength: 6, number: true},
				accountType: {required: true},
				bankName: {required: true},
				branchName: {required: true},
				branchNo: {required: true, minlength: 6, number: true},

			},

			submitHandler: function(form) { // for demo

				var editId = $('#editId').val();
				var accountNo = $('#accountNo').val();

				$.ajax({
					type: 'GET',
					url: 'accountNoExists',
					data: { "editId": editId, "accountNo": accountNo },

					success: function(resp) {
						if (resp != 0) {
							document.getElementById('errorSectiondisplay').innerHTML = "";
							err_invalidcer = "Account Number Already exists";
							var error='<div align="center" style="padding: 0px;" id="inform">';
							error+='<table cellspacing="0" class="statusBg1" cellpadding="0" border="0">';
							error+='<tbody><tr><td style="padding: 4px 10px" align="center"><span class="innerBg" id="mc_msg_txt">'+err_invalidcer+'</span></td>';
							error+='<td width="20" valign="top" style="padding-top: 4px; _padding-top: 2px;"><span>';
							error+='<a href="javascript:intdisplaymessage();" class="fa fa-times" style="color:white;"/>';
							error+='</span></td>';
							error+='</tr></tbody></table></div>';
							document.getElementById('errorSectiondisplay').style.display = 'block';
							document.getElementById('errorSectiondisplay').innerHTML = error;
							$("#accountNo").focus();
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

		$.validator.messages.minlength = function (param, input) {
			var article = document.getElementById(input.id);
			return "Please Enter 6 Characters";

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
	$('#frmextbankindex').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmextbankindex").submit();
}

function addeditview(type,id) {
	$('#editflg').val(type);
	$('#editId').val(id);
	$('#frmextbankview').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmextbankview").submit();
}

function getbankview(id) {
	$('#viewId').val(id);
	$('#frmextbankindex').attr('action', 'bankView?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmextbankindex").submit();
}

function gotoindexpage(viewflg) {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}

	if (viewflg == "1") {
		pageload();
		$('#frmextbankaddeditcancel').attr('action', 'bankView?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmextbankaddeditcancel").submit();
	} else {
		pageload();
		$('#frmextbankaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmextbankaddeditcancel").submit();
	}
}

function backindexpage() {
	pageload();
	$('#frmextbankview').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmextbankview").submit();
}

function changeDelFlg(id,delflg) {
	if (confirm("Do You Want To Change The Bank Status?")) {
		$("#id").val(id);
		$("#delflg").val(delflg);
		pageload();
		$('#frmextbankindex').attr('action', 'changeDelFlg?mainmenu='+mainmenu+'&time='+datetime);
		$('#frmextbankindex').submit();
	}
}

function changeMainFlg(id,mainflg) {
	if (confirm("Do You Want To Change The Main Bank Status?")) {
		$("#id").val(id);
		$("#mainflg").val(mainflg);
		pageload();
		$('#frmextbankindex').attr('action', 'changeMainFlg?mainmenu='+mainmenu+'&time='+datetime);
		$('#frmextbankindex').submit();
	}
}
