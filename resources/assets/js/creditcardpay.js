$(document).ready(function() {

	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.creditCardAddedit').click(function () {
		$("#creditCardPayaddedit").validate({
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
				mainDate: {required: true, date: true,minlength:10,correctformatdate: true},
				creditCard: {required: true},
				// transferBill : {extension: "csv", filesize : (2 * 1024 * 1024)},
			},
			submitHandler: function(form) { // for demo
				var checkboxes = $('input:checkbox:checked').length;
				if (checkboxes == 0) {
					alert("Please Select The CheckBox");
					return false;

				} else if(checkboxes > 1){
					alert("Please Select single The CheckBox");
					return false;
				}
				$('#selectedMonth').val($('input:checkbox:checked').val());
				
				if ($('#fileToUpload').val() == "") {
					alert("Please select the file");
					return false;
				} else if ($('#fileToUpload').val() != "") {
					var nameArr = $('#fileToUpload').val().split('.');
					if (nameArr[nameArr.length - 1] != "csv") {
						alert("The File Format is Worng,Please upload CSV file");
						return false;
					}
				}


			


				if($('#edit_flg').val() == "" || $('#edit_flg').val() == 2) {
					var confirmprocess = confirm("Do You Want To Register?");
				}
				if(confirmprocess) {
					pageload();
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
	$('.creditcashprocess').click(function () {
		$("#creditCardDtls").validate({
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
				
			},
			submitHandler: function(form) { // for demo
				var confirmprocess = confirm("Do You Want To Submit?");
				if(confirmprocess) {
					pageload();
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



	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.detailedit').click(function () {
		$("#creditdetailedit").validate({
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
				mainDate: {required: true, date: true,minlength:10,correctformatdate: true},
				creditCard: {required: true},
				creditCardDate: {required: true, date: true,minlength:10,correctformatdate: true},
				content: {required: true},
				amount: {requiredWithZero: true},
				transferBill : {extension: "jpg,jpeg,png,JPG,JPEG,PNG", filesize : (2 * 1024 * 1024)},
				// remarks: {required: true},
				// transferBill : {extension: "csv", filesize : (2 * 1024 * 1024)},
			},
			submitHandler: function(form) { // for demo
				
		
				var confirmprocess = confirm("Do You Want To Update?");
				if(confirmprocess) {
					pageload();
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

function resetErrors() {
	$('form input, form select, form radio').removeClass('inputTxtError');
	$('label.error').remove();
}    

function addedit() {
	$('#creditCaredPayIndex').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#creditCaredPayIndex").submit();
}

function getdate() {
	$('#mainDate').val(dates);
}
function gotoindexpage() {
	$('#creditCardPayaddedit').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#creditCardPayaddedit").submit();
}

function gotoregister() {
	$('#creditCardDtls').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#creditCardDtls").submit();
}

function fileUpload(id) {
	$('#id').val(id);
	$('#creditCaredPayIndex').attr('action', 'detailsaddedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#creditCaredPayIndex").submit();
}

function editCreditCard(id) {
	$('#id').val(id);
	$('#creditCaredPayIndex').attr('action', 'detailsaddedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#creditCaredPayIndex").submit();
}
function backToindex() {
	$('#creditdetailedit').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#creditdetailedit").submit();
}

// For Year BAr Click
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
		$('#page').val('');
		$('#plimit').val('');
		$('#selMonth').val(("0" + month).substr(-2));
		$('#selYear').val(year);
		$('#prevcnt').val(prevcnt);
		$('#nextcnt').val(nextcnt);
		$('#account_val').val(account_val);
		$('#creditCaredPayIndex').submit();
	}
}

function fnGetInsertedValue(){
	creditCardVal = $('#creditCard').val();
	$(".checkboxid").prop("disabled", false);
	$(":checkbox").attr("checked", false);
	if(creditCardVal != "") {
		$.ajax({
			type: 'GET',
			dataType: "JSON",
			url: 'GetInsMonth_ajax',
			data: {"creditCardVal": creditCardVal},
			success: function(resp) {
				for (i = 0; i < resp.length; i++) {
					$("#month"+resp[i]).prop("disabled", true);
				}
			},
			error: function(data) {
				// alert(data.status);
			}
		});
	}
}

function clearRecords(crediCard,year,month) {
	$('#creditCardId').val(crediCard);
	$('#creditCaredPayIndex').attr('action', 'deleteRecords?mainmenu='+mainmenu+'&time='+datetime);
	$("#creditCaredPayIndex").submit();

}