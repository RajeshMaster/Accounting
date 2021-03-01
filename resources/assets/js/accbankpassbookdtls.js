var data = {};
$(document).ready(function() {
	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.addeditprocess').click(function () {
		$("#frmBankPassportaddEdit").validate({
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
				bankId: {required: true},
				pageNoFrom: {required: true, minlength: 2, number: true},
				pageNoTo: {required: true, minlength: 2, number: true},
				dateRangeFrom: {required: true, date:true, correctformatdate: true},
				dateRangeTo: {required: true, date:true, correctformatdate: true},
				bankPassbook : {extension: "jpg,jpeg,png,JPG,JPEG,PNG", filesize : (2 * 1024 * 1024)},
			},
			submitHandler: function(form) { // for demo
				var edit_id = $('#edit_id').val();
				var pageNoFrom = $('#pageNoFrom').val();
				var pageNoTo = $('#pageNoTo').val();
				var pageNo = pageNoFrom + "-" + pageNoTo;
				$.ajax({
					type: 'GET',
					url: 'pageNoExists',
					data: { "edit_id": edit_id, "pageNo": pageNo },

					success: function(resp) {
						if (resp != 0) {
							document.getElementById('errorSectiondisplay').innerHTML = "";
							err_invalidcer = "Page Number Already exists";
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
							if($('#edit_flg').val() == "2") {
								var confirmprocess = confirm("Do You Want To Update?");
							} else {
								var confirmprocess = confirm("Do You Want To Register?");
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

				/*if($('#edit_flg').val() == "2") {
					var confirmprocess = confirm("Do You Want To Update?");
				} else {
					var confirmprocess = confirm("Do You Want To Register?");
				}
				if(confirmprocess) {
					pageload();
					return true;
				} else {
					return false;
				}*/
			}
		});

		$.validator.messages.required = function (param, input) {
			var article = document.getElementById(input.id);
			return article.dataset.label + err_fieldreq;
		}

		$.validator.messages.minlength = function (param, input) {
			var article = document.getElementById(input.id);
			return "Please Enter 2 Characters";
		}

	});
});

function intdisplaymessage() {
	document.getElementById('errorSectiondisplay').style.display='none';
}

function pageClick(pageval) {
	$('#page').val(pageval);
	$("#frmAccBankPassbook").submit();
}

function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$("#frmAccBankPassbook").submit();
}

function addedit(flg,id) {
	$('#edit_flg').val(flg);
	if (flg != 1) {
		$('#edit_id').val(id);
	}
	$('#frmAccBankPassbook').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmAccBankPassbook").submit();
}

function nextData(flg,id) {
	var confirmgroup = confirm("Do You Want To Next Record?");
	if(confirmgroup) {
		$('#edit_flg').val(flg);
		$('#edit_id').val(id);
		$('#frmAccBankPassbook').attr('action', 'addeditprocess?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmAccBankPassbook").submit();
	} 
}

function getdate(flg) {
	if (flg == 1) {
		$('#dateRangeFrom').val(dates);
	} else if (flg == 2) {
		$('#dateRangeTo').val(dates);
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
		$('#page').val('');
		$('#plimit').val('');
		$('#selMonth').val(("0" + month).substr(-2));
		$('#selYear').val(year);
		$('#prevcnt').val(prevcnt);
		$('#nextcnt').val(nextcnt);
		$('#account_val').val(account_val);
		$('#frmAccBankPassbook').submit();
	}
}
