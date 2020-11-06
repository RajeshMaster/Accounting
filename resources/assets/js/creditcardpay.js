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
				if($('#edit_flg').val() == "" || $('#edit_flg').val() == 2) {
					var confirmprocess = confirm("Do You Want To Register?");
				} else {
					var confirmprocess = confirm("Do You Want To Update?");
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
