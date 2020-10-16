function getData(month, year, flg, prevcnt, nextcnt, account_period, lastyear, currentyear, account_val) {
	var yearmonth = year + "-" +  ("0" + month).substr(-2);
	if ((prevcnt == 0) && (flg == 0) && (parseInt(month) < account_period) && (year == lastyear)) {
		alert("No Previous Record.");
	} else if ((nextcnt == 0) && (flg == 0) && (parseInt(month) > account_period) && (year == currentyear)) {
		alert("No Next Record.");
	} else {
		if (flg == 1) {
			 $('#previou_next_year').val(year + "-" +  ("0" + month).substr(-2)); 
		}
		$('#selMonth').val(("0" + month).substr(-2));
		$('#selYear').val(year);
		$('#prevcnt').val(prevcnt);
		$('#nextcnt').val(nextcnt);
		$('#account_val').val(account_val);
		$('#page').val("");
		$('#plimit').val("");
		$('#meetingindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$('#meetingindex').submit();
	}
}
function UnderConstruction() {
 	alert("UnderConstruction");
 }
function getmeetingView(id) { 
	var mainmenu="MeetingDetails";
	$('#viewid').val(id);
	$('#meetingindex').attr('action','view?mainmenu='+mainmenu+'&time='+datetime);
	$("#meetingindex").submit();
}
function goindexpage(mainmenu) {
	var editflg = $('#editflg').val();
	var mainmenu="MeetingDetails";
    pageload();
    if (editflg == 'edit' || editflg == 'copy') {
	    $('#meetingdetailsviewfrm').attr('action', 'view?mainmenu='+mainmenu+'&time='+datetime);
	    $("#meetingdetailsviewfrm").submit();
	} else {
		$('#meetingdetailsviewfrm').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	    $("#meetingdetailsviewfrm").submit();
	}
} 
function goindexpages(mainmenu) {
	$('#page').val("");
	$('#plimit').val("");
	var mainmenu="MeetingDetails";
    $('#meetinghistoryfrm').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
    $("#meetinghistoryfrm").submit();
}
function getmeetingReg(type){
	$('#editflg').val(type);
	$('#meetingindex').attr('action', 'meetingaddedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#meetingindex").submit();
}
function fnGetbranchDetail(value){
    $('#branchId').find('option').not(':first').remove();
	var getbankval = $('#customerId').val();
	if ($('#customerId').val() != "") {
		$("#customerdiv").hide();
	} else {
		$("#customerdiv").show();
	}
	$.ajax({
		type: 'GET',
        dataType: "JSON",
		url: 'branch_ajax',
		data: {"getbankval": getbankval,"mainmenu": mainmenu},
		success: function(resp) {
            for (i = 0; i < resp.length; i++) {
              $('#branchId').append( '<option value="'+resp[i]["branch_id"]+'">'+resp[i]["branch_name"]+'</option>' );
              $('select[name="branchId"]').val(value);
            }
			$('#branchId').val($('#hidebranchname').val());
			if ($('#byajax').val() == 1) {
				$("#branchId").val($("#branchId option:last").val());
			}
		},
		error: function(data) {
			alert(data.status);
		}
	});
}
$(document).ready(function() {
	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	var customerId = $('#customerId').val();
	if (customerId!='') {
		$("#customerdiv").hide();
	}
	$('.addeditprocess').click(function () {
		$("#meetingdetailsviewfrm").validate({
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
				date: {required: true, date: true,minlength:10,correctformatdate: true},
				startTime: {required: true, time24: true},
				endTime: {required: true,  time24: true, greaterThanStartTime: "#startTime"},
				customerId: {required: true},
				personName: {required: true},
			},
			submitHandler: function(form) {
				if($('#editflg').val() == "edit") {
				 	var confirmprocess = confirm("Do You Want To Update?");
				 } else {
					var confirmprocess = confirm("Do You Want To Register?");
				}
				var txt_date = $('#date').val();
				var starttime = $('#startTime').val();
				var endtime = $('#endTime').val();
				var editid = $('#editid').val();
				var editflg = $('#editflg').val();
				 if(confirmprocess) {
				 	$.ajax({
						type: 'GET',
						url: 'getmettingtiming',
						data: {"txt_date": txt_date,
								"starttime": starttime,
								"endtime": endtime,
								"editid": editid,
								"editflg": editflg,
								"mainmenu": mainmenu},
						success: function(resp) {
							if (resp > 0) { 
							document.getElementById('errorSectiondisplay').innerHTML = "";
							err_invalidcer = "Meeting Was Already Arranged in this Time";
							var error='<div align="center" style="padding: 0px;" id="inform">';
									error+='<table cellspacing="0" class="statusBg1" cellpadding="0" border="0">';
									error+='<tbody><tr><td style="padding: 4px 10px" align="center"><span class="innerBg" id="mc_msg_txt">'+err_invalidcer+'</span></td>';
									error+='<td width="20" valign="top"	style="padding-top: 4px; _padding-top: 2px;"><span>';
									error+='<a href="javascript:displaymessage();" class="fa fa-times" style="color:white;"/>';
									error+='</span></td>';
									error+='</tr></tbody></table></div>';
							document.getElementById('errorSectiondisplay').style.display = 'block';
							document.getElementById('errorSectiondisplay').innerHTML = error;
							return false;
						} else {
							pageload();
							form.submit();
							return true;
						}
						},
						error: function(data) {
							$("#regbutton").attr("data-dismiss","modal");
						}
					});
				} else {
					return false;
				}
			}
		});
		$.validator.messages.required = function (param, input) {
            var article = document.getElementById(input.id);
            return article.dataset.label + ' field is required';
        }
	});
});
function dateadd(date){
	$('#date').val(date);
}
function contractemployeeedit(type,id) {
	 $('#editflg').val(type);
	 $('#editid').val(id);
	 $('#meetingdetailsviewfrm').attr('action', 'meetingaddedit?mainmenu='+mainmenu+'&time='+datetime);
	 $("#meetingdetailsviewfrm").submit();
}
function getmeetingHistory(cust_name){
	$('#page').val("");
	$('#plimit').val("");
	$('#customer_name').val(cust_name); 
	$('#meetingindex').attr('action', 'meetinghistory?mainmenu='+mainmenu+'&time='+datetime);
	$("#meetingindex").submit();
}
//open Popup
function newcustomerpopup(mainmenu,page) {
	 popupopenclose(1);
	$('#meetingnewRegpopup').load('newcustomerpopup?mainmenu='+mainmenu+'&editflg='+page+'&time='+datetime);
	$("#meetingnewRegpopup").modal({
           backdrop: 'static',
            keyboard: false
        });
    $('#meetingnewRegpopup').modal('show');
}
function decimalvalues(e) {
    var s=e.length;
    if(s=="2") {
        var b=e+":";
        $('#startTime').val(b);
    }
    return event.charCode >= 48 && event.charCode <= 57;
}
function decimalvalue(e) {
    var s=e.length;
    if(s=="2") {
        var b=e+":";
        $('#endTime').val(b);
    }
    return event.charCode >= 48 && event.charCode <= 57;
}
function gotoindexpage(viewflg,mainmenu) {
	$('#editflg').val('');
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
	pageload();
    $('#frmmeetingaddeditcancel').attr('action', viewflg+'?mainmenu='+mainmenu+'&time='+datetime);
    $("#frmmeetingaddeditcancel").submit();
}
function displaymessage() {
	document.getElementById('errorSectiondisplay').style.display='none';
}
function pageClick(pageval) {
	var hisflg = $('#hisFlg').val();
	$('#page').val(pageval);
	if (hisflg == '1') {
		$('#meetinghistoryfrm').attr('action', 'meetinghistory?mainmenu='+mainmenu+'&time='+datetime);
        $("#meetinghistoryfrm").submit();
	} else {
		$("#meetingindex").submit();
	}
}
function pageLimitClick(pagelimitval) {
	var hisflg = $('#hisFlg').val();
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	if (hisflg == 1) {
		$('#meetinghistoryfrm').attr('action', 'meetinghistory?mainmenu='+mainmenu+'&time='+datetime);
        $("#meetinghistoryfrm").submit();
	} else {
		$("#meetingindex").submit();
	}
}
function fnCancel_check() {
	cancel_check = false;
	return cancel_check;
}
function divpopclose() {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Popup?")) {
			return false;
		} else {
			$('#meetingnewRegpopup').empty();
            $('#meetingnewRegpopup').modal('toggle');
		}
	} else {
		$('#meetingnewRegpopup').empty();
        $('#meetingnewRegpopup').modal('toggle');
	}
}
