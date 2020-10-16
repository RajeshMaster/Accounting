function divpopupclose() {
    var confirmmsg = cancel_msg;
    if (confirm(confirmmsg)) {
        $( "body div" ).removeClass( "modalOverlay" );
        $( '#meetingnewRegpopup' ).empty();
        $('#meetingnewRegpopup').modal('toggle');
    } else {
        return false;
    }
}
$( document ).ready(function() {
	$('.registerprocess').click(function () {
	$("#customer_register").validate({
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
			customer_name: {required: true},
			txt_romaji: {required: true},
			branch_name: {required: true},
		},
		submitHandler: function(form) { // for demo
				var confirmprocess = confirm("Do you want to Register the Customer details");
				var customer_name = $('#customer_name').val();
			if(confirmprocess) {
				$.ajax({
					type: 'GET',
			        // dataType: "JSON",
					url: 'cust_name_exist',
					data: {"customer_name": customer_name,"mainmenu": mainmenu},
					success: function(resp) {
						if (resp > 0) { 
						document.getElementById('errorForexistCus').innerHTML = "";
						err_invalidcer = "This Customer ("+customer_name+ ") Was Already Exist";
						var error='<div align="center" style="padding: 0px; id="inform">';
								error+='<table cellspacing="0" class="statusBg1" cellpadding="0" border="0">';
								error+='<tbody><tr><td style="padding: 4px 10px" align="center"><span class="innerBg" id="mc_msg_txt">'+err_invalidcer+'</span></td>';
								error+='</span>';
								error+='</tr></tbody></table></div>';
						document.getElementById('errorForexistCus').style.display = 'block';
						document.getElementById('errorForexistCus').innerHTML = error;
						return false;
					} else {
						// pageload();
						// form.submit();
						var customer_name = $('#customer_name').val();
						var txt_romaji = $('#txt_romaji').val();
						var branch_name = $('#branch_name').val();
							$.ajax({
						        type:"GET",
						        // dataType: "JSON",
						        url: 'customerregister',
						        data: {
						            customer_name: customer_name,
						            txt_romaji: txt_romaji,
						            branch_name: branch_name
						        },
						        success: function(data){
						        	var obj = jQuery.parseJSON(data);
						        	 $.each(obj, function(index, value) {
										$('#customerId').append( '<option value="'+index+'">'+value+'</option>' );
              							$('select[name="customerId"]').val(index).trigger('onchange');
						            });
									$('#meetingnewRegpopup').modal('hide');
									$('#byajax').val(1);
						        },
						        error: function(xhr, textStatus, errorThrown){
						        	alert(xhr.status);
						        }  
						    });
						return false;
					}
					},
					error: function(data) {
						$("#regbutton").attr("data-dismiss","modal");
					}
				});
			} else {
				return false
			}
		}
		});
		$.validator.messages.required = function (param, input) {
            var article = document.getElementById(input.id);
            return article.dataset.label + ' field is required';
        }
	});
});
function dblclick() {
	$('.registerprocess').click();
	submitHandler();
}