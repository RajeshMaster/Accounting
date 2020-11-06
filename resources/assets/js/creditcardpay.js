$(document).ready(function() {

	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.addeditprocess').click(function () {
		$("#frmaccountingaddedit").validate({
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
				accDate: {required: true, date: true,minlength:10,correctformatdate: true},
				bank: {required: true},
				transfer: {required: true},
				transtype: {required: true},
				content: {required: true},
				amount: {requiredWithZero: true},
			},
			submitHandler: function(form) {
				if($('#edit_flg').val() == "" || $('#edit_flg').val() == 2) {
					var confirmprocess = confirm("Do You Want To Register?");
				} else {
					var confirmprocess = confirm("Do You Want To Update?");
				}
				if(confirmprocess) {
					pageload();
					return true;
				} else {
					return false
				}
			}
		});
		$.validator.messages.required = function (param, input) {
			var article = document.getElementById(input.id);
			return article.dataset.label + "Required";
		}
		$.validator.messages.minlength = function (param, input) {
			var article = document.getElementById(input.id);
			return "Please Enter valid 10 Number";
		}
	});

	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.tranferaddeditprocess').click(function () {
		$("#frmtransferaddedit").validate({
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
				accDate: {required: true, date: true,minlength:10,correctformatdate: true},
				transferMainExp: {required: true},
				transferBank: {required: true},
				transferAmount: {requiredWithZero: true},
				txt_empname: {Anyonerequired: "#transferContent"},
				transferContent: {Anyonerequired: "#txt_empname"},
				transferBill : {extension: "jpg,jpeg,png,JPG,JPEG,PNG", filesize : (2 * 1024 * 1024)},
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

	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.AutoDebitRegprocess').click(function () {
		$("#frmAutoDebitReg").validate({
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
				accDate: {required: true, date: true,minlength:10,correctformatdate: true},
				autoDebitBank: {required: true},
				autoDebitMainExp: {required: true},
				autoDebitAmount: {requiredWithZero: true},
				loanName: {Anyonerequired: "#autoDebitContent"},
				autoDebitContent: {Anyonerequired: "#loanName"},
				autoDebitBill : {extension: "jpg,jpeg,png,JPG,JPEG,PNG", filesize : (2 * 1024 * 1024)},
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


});

function resetErrors() {
	$('form input, form select, form radio').removeClass('inputTxtError');
	$('label.error').remove();
}    

function addedit() {
		$('#creditCaredPayIndex').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#creditCaredPayIndex").submit();
}
