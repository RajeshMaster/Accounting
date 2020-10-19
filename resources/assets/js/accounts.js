$(document).ready(function() {

	// initialize tooltipster on text input elements

	// initialize validate plugin on the form

	$('#swaptable tr').click(function(event) {

		if (event.target.type !== 'radio') {

			if (event.target.nodeName != "SPAN") {

				$(':radio', this).trigger('click');

			}

		}

	});

	$('.addeditprocess').click(function () {
		alert();
	});

});

function resetErrors() {
	$('form input, form select, form radio').removeClass('inputTxtError');
	$('label.error').remove();
}    

function addedit() {
 	$('#frmaccountingindex').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmaccountingindex").submit();
}

