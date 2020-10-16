function pageClick(pageval) {
	pageload();
	$('#page').val(pageval);
	$('#frmsignatureindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmsignatureindex").submit();
}
function pageLimitClick(pagelimitval) {
	pageload();
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$('#frmsignatureindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmsignatureindex").submit();
}
function fnreg(flg,flgs) {
	pageload();
	$('#editflg').val(flg);
	if(flgs == "1") {
		$('#frmsignatureview').attr('action','addedit'+'?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmsignatureview").submit();
	} else {
		$('#frmsignatureindex').attr('action','addedit'+'?mainmenu='+mainmenu+'&time='+datetime); 
		$("#frmsignatureindex").submit();
	}
}
function gotoindex(index,mainmenu,updatehdn,editflg) {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	} if(editflg == "1"){
		pageload();
		$('#mailsignaddeditcancel').attr('action', 'view?mainmenu='+mainmenu+'&time='+datetime);
		$("#mailsignaddeditcancel").submit();
	} else if(updatehdn == "2" && editflg == "1") { 
		pageload();
		$('#mailsignaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#mailsignaddeditcancel").submit();
	}  else { 
		pageload();
		$('#mailsignaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#mailsignaddeditcancel").submit();
	}
}
function gotoviewscrn(index,mainmenu) {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
	pageload();
	$('#mailsignaddeditcancel').attr('action', 'view?mainmenu='+mainmenu+'&time='+datetime);
	$("#mailsignaddeditcancel").submit();
}
function fnclose() {
	$('#txtuserid').val('');
	$('#content').val('');
}
function popupenable(mainmenu) {
	popupopenclose(1);
	var useridhdn = $('#useridhdn').val();
	$('#mailsignaturepopup').load('../Mailsignature/mailsignaturepopup?mainmenu='+mainmenu+'&time='+datetime+'&useridhdn='+useridhdn);
	$("#mailsignaturepopup").modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#mailsignaturepopup').modal('show');
}
function fndbclick(userid,uname,givennm,nicknm) {
	var name = uname.concat(" ").concat(givennm).concat(" ").concat(nicknm);
	$("#"+userid).prop("checked", true);
	$('#txtuserid').val(name);
	$('#userid').val(userid);
 	$.ajax({
		type: 'GET',
		url: 'getdatexist',
		dataType: "json",
		data: {"userid": userid},
		success: function(resp) {
			if (resp!="") {
				$('#content').val(resp.content);
				$('#edithead').show();
				$('#updatebtn').show();
				$('#updatecancel').show();
				$('#updateprocess').val(2);
				$('#editflg').val(1);
				$('#reghead').hide();
				$('#regbtn').hide();
				$('#regcancel').hide();
			} else {
				$('#content').val('');
				$('#updateprocess').val('');
				$('#editflg').val(2);
				$('#edithead').hide();
				$('#updatebtn').hide();
				$('#updatecancel').hide();
				$('#reghead').show();
				$('#regbtn').show();
				$('#regcancel').show();
			}
		},
		error: function(data) {
			alert(data);
		}
	});
	$('#mailsignaturepopup').modal('toggle');
}
function fngetData(userid,uname,givennm,nicknm) {
	var name = uname.concat(" ").concat(givennm).concat(" ").concat(nicknm);
	$("#"+userid).prop("checked", true);
	$('#txtuserid').val(name);
	$('#userid').val(userid);
}
function fnselect(){
	var txtuserid=$('#txtuserid').val();
	var userid=$('#userid').val();
	$('#txtuserid').val(txtuserid);
	$('#userid').val(userid);
	$.ajax({
		type: 'GET',
		url: 'getdatexist',
		dataType: "json",
		data: {"userid": userid},
		success: function(resp) {
			if (resp!="") {
				$('#content').val(resp.content);
				$('#edithead').show();
				$('#updatebtn').show();
				$('#updatecancel').show();
				$('#updateprocess').val(2);
				$('#editflg').val(1);
				$('#reghead').hide();
				$('#regbtn').hide();
				$('#regcancel').hide();
			} else {
				$('#content').val('');
				$('#updateprocess').val('');
				$('#editflg').val(2);
				$('#edithead').hide();
				$('#updatebtn').hide();
				$('#updatecancel').hide();
				$('#reghead').show();
				$('#regbtn').show();
				$('#regcancel').show();
			}
		},
		error: function(data) {
			alert(data);
		}
	});
	$('#mailsignaturepopup').modal('toggle');
}
function signeditpage(id,mainmenu) {
	pageload();
	$('#id').val(id);
	$('#editflg').val(1);
	$('#frmsignatureview').attr('action','addedit?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmsignatureview").submit();
}
$(document).ready(function() {
	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.addeditprocess').click(function () {
		$("#frmaddedit").validate({
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
				txtuserid: {required: true}, 
				content: {required: true},
			},
			submitHandler: function(form) { // for demo
				var regflg=$('#editflg').val();
				var upflg=$('#updateprocess').val();
				if(upflg == 2){
					var confirmprocess = confirm("Do You Want To Update?");	
				} else {
					if (regflg!=1) { 
						var confirmprocess = confirm("Do You Want To Register?");
					} else {
						var confirmprocess = confirm("Do You Want To Update?");
					}
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
			return article.dataset.label + ' field is required';
		}
	});
});
function gotosignview(id){
	pageload();
	$('#signid').val(id);
	$('#frmsignatureindex').attr('action', 'view?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmsignatureindex").submit();
}
function displaymessage() {
	document.getElementById('errorSectiondisplay').style.display='none';
}