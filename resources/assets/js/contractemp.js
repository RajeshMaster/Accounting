$(document).ready(function() {
	$("#checkall").change(function(){  //"select all" change 
		$(".checkbox").prop('checked', $(this).prop("checked")); //change all ".checkbox" checked status
	});

	//".checkbox" change 
	$('.checkbox').change(function(){ 
		//uncheck "select all", if one of the listed checkbox item is unchecked
		if(false == $(this).prop("checked")){ //if this item is unchecked
			$("#checkall").prop('checked', false); //change "select all" checked status to false
		}
		//check "select all" if all checkbox items are checked
		if ($('.checkbox:checked').length == $('.checkbox').length ){
			$("#checkall").prop('checked', true);
		}
	});

	$('.addeditprocess').click(function () {
		$("#addeditcontractemp").validate({
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
				month: {required: true},
				date: {required: true, date: true,minlength:10,correctformatdate: true},
			},
			submitHandler: function(form) { // for demo
				if($('#editcheck').val() == 0 || $('#editcheck').val() == 3) {
					var month = $('#month').val();
					var Emp_ID = $('#Emp_ID').val();
					var selYear = $('#selYear').val();
					$.ajax({
						type: 'GET',
						url: 'getdataExists',
						data: {"Emp_ID": Emp_ID,"selYear": selYear,"month": month},
						success: function(resp) {
							if (resp > 0) { 
								document.getElementById('errorSectiondisplay').innerHTML = "";
								err_invalidcer = "Data Already Exists";
								var error='<div align="center" style="padding: 0px;" id="inform">';
								error+='<table cellspacing="0" class="statusBg1" cellpadding="0" border="0">';
								error+='<tbody><tr><td style="padding: 4px 10px" align="center"><span class="innerBg" id="mc_msg_txt">'+err_invalidcer+'</span></td>';
								error+='<td width="20" valign="top" style="padding-top: 4px; _padding-top: 2px;"><span>';
								error+='<a href="javascript:displaymessage();" class="fa fa-times" style="color:white;"/>';
								error+='</span></td>';
								error+='</tr></tbody></table></div>';
								document.getElementById('errorSectiondisplay').style.display = 'block';
								document.getElementById('errorSectiondisplay').innerHTML = error;
								return false;
							} else {
								var confirmprocess = confirm("Do You Want To Register?");
								if(confirmprocess) {
									if ($('#editcheck').val() == 3) {
										pageload();
										form.submit();
										return true;
									} else {
										if ($("#stop_next").val() == '1' || $("#stop_next_hdn").val() == '1') {
											$.ajax({
												type: 'GET',
												url: 'dataReg',
												data: $('#addeditcontractemp').serialize(),
												success: function(resp) {
													$('#hdn_id').val(resp);
													gotoviewpage();
												},
												error: function(data) {
													alert(data);
												}
											});
										} else {
											$.ajax({
												type: 'GET',
												url: 'dataReg',
												data: $('#addeditcontractemp').serialize(),
												success: function(resp) {
													$('#hdn_id').val(resp);
													popupopenclose(1);
													$("#salconfirmpopup").modal({
														backdrop: 'static',
														keyboard: false
													});
													$('#salconfirmpopup').modal('show');
												},
												error: function(data) {
													alert(data);
												}
											});
										}
									}
								} else {
									return false;
								}
							}
						},
						error: function(data) {
							alert(data);
							// $("#regbutton").attr("data-dismiss","modal");
						}
					});
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
			return article.dataset.label + ' field is required';
		}
	});

});

function resetErrors() {
	$('form input, form select, form radio').removeClass('inputTxtError');
	$('label.error').remove();
}

function displaymessage() {
	document.getElementById('errorSectiondisplay').style.display='none';
}

function gotoviewpage() {
	pageload();
	$('#hiddenform').attr('action','../contractEmp/view?mainmenu='+mainmenu+'&time='+datetime);
	$("#hiddenform").submit();
}

function getnextrecordclick() {
	pageload();
	$('#next_record').val('1');
	$('#hdn_id').val('');
	$('#Emp_ID').val('');
	$('#firstname').val('');
	$('#lastname').val('');
	$('#hiddenform').attr('action','../contractEmp/addedit?mainmenu='+mainmenu+'&time='+datetime);
	$("#hiddenform").submit();
}

function pageClick(pageval) {
	$('#page').val(pageval);
	$("#contractempindex").submit();
}

function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$("#contractempindex").submit();
}

function salaryselectpopup_main() {
	var mainmenu = $('#mainmenu').val();
	var year = $('#selYear').val();
	var month = $('#selMonth').val();
	popupopenclose(1);
	$('#contractemppopup').load('../contractEmp/contractemppopup?mainmenu='+mainmenu+'&year='+year+'&month='+month);
	$("#contractemppopup").modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#contractemppopup').modal('show');
}

function getData(month, year, flg, prevcnt, nextcnt, account_period, lastyear, currentyear, account_val) {
	var yearmonth = year + "-" +  ("0" + month).substr(-2);
	var mainmenu = $('#mainmenu').val();
	if ((prevcnt == 0) && (flg == 0) && (parseInt(month) < account_period) && (year == lastyear)) {
		alert(err_no_previous_record);
	} else if ((nextcnt == 0) && (flg == 0) && (parseInt(month) > account_period) && (year == currentyear)) {
		alert(err_no_next_record);
	} else {
		if (flg == 1) {
			document.getElementById('previou_next_year').value = year + "-" +  ("0" + month).substr(-2);
		}
		document.getElementById('selMonth').value = month;
		document.getElementById('selYear').value = year;
		document.getElementById('prevcnt').value = prevcnt;
		document.getElementById('nextcnt').value = nextcnt;
		document.getElementById('account_val').value = account_val;
		$('#pageclick').val('');
		$('#page').val('');
		$('#plimit').val('');
		$('#get_prev_yr').val('1');
		$('#contractempindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#contractempindex").submit();
	}
}

function fngotoadd(id,empid,editcheck,mainmenu,firstname,lastname) {
	pageload();
	$('#id').val(id);
	$('#firstname').val(firstname);
	$('#lastname').val(lastname);
	$('#Emp_ID').val(empid);
	if (editcheck == 0) {
		$('#editcheck').val(editcheck);
		$('#contractempindex').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#contractempindex").submit();	
	} else {
		$('#editcheck').val('1');
		$('#contractempindex').attr('action', 'view?mainmenu='+mainmenu+'&time='+datetime);
		$("#contractempindex").submit();
	}
}

function gotoindexsalarycalc(mainmenu) {
	pageload();
	$('#addeditcontractemp').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#addeditcontractemp").submit();
}

function fngotoedit(mainmenu) {
	pageload();
	$('#editcheck').val('1');
	$('#addeditcontractemp').attr('action', 'edit?mainmenu='+mainmenu+'&time='+datetime);
	$("#addeditcontractemp").submit();
}

function fngotocopy(mainmenu) {
	pageload();
	$('#editcheck').val('3');
	$('#addeditcontractemp').attr('action', 'edit?mainmenu='+mainmenu+'&time='+datetime);
	$("#addeditcontractemp").submit();
}

function undercos() {
	alert('Under Construction');
}

function gotoindex(viewflg,mainmenu) {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
	pageload();
	if ($('#editcheck').val() == 1 || $('#editcheck').val() == 3) {
		$('#editcheck').val('2');
		$('#contractempaddeditcancel').attr('action', 'view?mainmenu='+mainmenu+'&time='+datetime);
		$("#contractempaddeditcancel").submit();
	} else {
		$('#contractempaddeditcancel').attr('action', viewflg+'?mainmenu='+mainmenu+'&time='+datetime);
		$("#contractempaddeditcancel").submit();
	}
}

function getdate() {
	$('#date').val(saldate);
}

function getdate_multicheck() {
	$('#txt_startdate').val(saldate);
}

function fngotohistory(empid,mainmenu,firstname,lastname) {
	$('#hiddenplimit').val($('#plimit').val());
	$('#hiddenpage').val($('#page').val());
	$('#Emp_ID').val(empid);
	$('#firstname').val(firstname);
	$('#lastname').val(lastname);
	$('#contractempindex').attr('action', 'history?mainmenu='+mainmenu+'&time='+datetime);
	$("#contractempindex").submit();
}

function gotoindexback(mainmenu,limit,page) {
	var date = new Date();
	var year = date.getFullYear();
	var month = date.getMonth()+1;
	$('#page').val(page);
	$('#plimit').val(limit);
	$('#selYear').val(year);
	$('#selMonth').val(month);
	$('#contractemphistory').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#contractemphistory").submit();
}

function empselectbypopupclick() {
	var value = $("#selectedEmp option:selected").text(); 
	if(value == "" || value == null) {
		alert("Please Select atleast One Employee")
		return false;
	}
	var Emp_selection = "Do You Want To Add?";
	if(confirm(Emp_selection)) {
		document.empselectform.submit();
		return true;
	} else {
		return false;
	}
}

function getTransferedAmount() {
	var totalamount = $('#totamt').data('totalamt');
	$.ajax({
		type: 'GET',
		url: 'getTransferedAmount',
		data: $('#addeditcontractemp').serialize(),
		success: function(resp) {
			var diff_amt = parseInt(Number(resp.trim().replace(/[, ]+/g, ""))) - parseInt(Number(totalamount.trim().replace(/[, ]+/g, "")));
			var diff_amnt = '<span style="font-weight: bold;">'+lbl_amount_difference+'</span>&nbsp;<span style="color: red;">' + diff_amt.toLocaleString() + '</span>';
			$("#difference_amount").html(diff_amnt);
			$('#salamt').val(resp);
		},
		error: function(data) {
			$('#salamt').val('');
		}
	});
}

function getlastmonthdetails() {
	var month = $('#month').val();
	var Emp_ID = $('#Emp_ID').val();
	var selYear = $('#selYear').val();
	$.ajax({
		type: 'GET',
		url: 'getlastmonthdet',
		data: {"Emp_ID": Emp_ID,"selYear": selYear,"month": month},
		success: function(resp) {
			var obj = jQuery.parseJSON(resp);
			var sum = 0;
			$("#totamt").text('');
			$.each( obj, function( index, value ){
				$('#'+index).val(value);
				if (index !== 'remarks') {
					var remnum = Number(value.trim().replace(/[, ]+/g, ""));
					//add only if the value is number
					if (!isNaN(remnum) && value.length != 0) {
						sum += parseFloat(remnum);
						// $(this).css("background-color", "#FEFFB0");
					}
				}
			});
			var isNeg = sum < 0;
			var amount = isNeg ? sum : Math.abs(sum.toFixed(0));
			var tot = amount.toLocaleString();
			var tott = tot;
			$("#totamt").text(tott);
			$("#totamt").data('totalamt', tott);
		},
		error: function(data) {
			alert(data);
		}
	});
}

function fngodownloadempid(mainmenu) {
	var confirmprocess_download = confirm("Do You Want To Download?");
	if(confirmprocess_download) {
		$('#addeditcontractemp').attr('action','../contractEmp/salarydownloadprocess?mainmenu='+mainmenu+'&time='+datetime);
		$("#addeditcontractemp").submit();
	}
}

function transferdetailsdownload(mainmenu) {
	$('#hdn_empid_arr').val('');
	var cbChecked = new Array();
	var cbChecked_text_mailflg_0 = new Array();
	var cbChecked_text_mailflg_1 = new Array();
	if($('.checkbox:checkbox:checked').length > 0){
		$('.checkbox:checkbox:checked').each(function() {
			if ($(this).attr("data-mailflg") == 0) {
				cbChecked[cbChecked.length] = this.value;            
				cbChecked_text_mailflg_0[cbChecked_text_mailflg_0.length] = $(this).attr("data-name-empid").toUpperCase();
			} else {
				cbChecked_text_mailflg_1[cbChecked_text_mailflg_1.length] = $(this).attr("data-name-empid").toUpperCase();
			}
		});
		$('#hdn_empid_arr').val(cbChecked);
		var confirmprocess_download = confirm("Do You Want To Download?");
		if(confirmprocess_download) {
		$('#contractempindex').attr('action','../contractEmp/transferdetailsdownload?mainmenu='+mainmenu+'&time='+datetime);
		$("#contractempindex").submit();
		}
	} else {
		alert("Please Select Employee ID");return;
	}
}

function salaryplusdownload(mainmenu) {
	var variable = $("#selMonth").val();
	if(typeof(variable) != "undefined" && variable !== null) {
		$("#payrollExcel").val(variable);
		var form = '#contractempindex';
	} else{
		var form = '#contractemphistoryTotal';
	}

	$('#hdn_empid_arr').val('');
	var cbChecked = new Array();
	var cbChecked_text_mailflg_0 = new Array();
	var cbChecked_text_mailflg_1 = new Array();
	if($('.checkbox:checkbox:checked').length > 0){
		$('.checkbox:checkbox:checked').each(function() {
			if ($(this).attr("data-mailflg") == 0) {
					cbChecked[cbChecked.length] = this.value;            
					cbChecked_text_mailflg_0[cbChecked_text_mailflg_0.length] = $(this).attr("data-name-empid").toUpperCase();            
			} else {
					cbChecked_text_mailflg_1[cbChecked_text_mailflg_1.length] = $(this).attr("data-name-empid").toUpperCase();            
			}
		});
		$('#hdn_empid_arr').val(cbChecked);
		var confirmprocess_download = confirm("Do You Want To Download?");
		if(confirmprocess_download) {
			$(form).attr('action','../contractEmp/salaryplusdownload?mainmenu='+mainmenu+'&time='+datetime);
			$(form).submit();
		}
	} else {
		alert("Please Select Employee ID");return;
	}
}

function salarypluspdfdownload(mainmenu) {
	var variable = $("#selMonth").val();
	if(typeof(variable) != "undefined" && variable !== null) {
		$("#payrollPdf").val(variable);
		var form = '#contractempindex';
	} else{
		var form = '#contractemphistoryTotal';
	}

	$('#hdn_empid_arr').val('');
	var cbChecked = new Array();
	var cbChecked_text_mailflg_0 = new Array();
	var cbChecked_text_mailflg_1 = new Array();
	if($('.checkbox:checkbox:checked').length > 0){
		$('.checkbox:checkbox:checked').each(function() {
			if ($(this).attr("data-mailflg") == 0) {
				cbChecked[cbChecked.length] = this.value;            
				cbChecked_text_mailflg_0[cbChecked_text_mailflg_0.length] = $(this).attr("data-name-empid").toUpperCase();            
			} else {
				cbChecked_text_mailflg_1[cbChecked_text_mailflg_1.length] = $(this).attr("data-name-empid").toUpperCase();            
			}
		});
		$('#hdn_empid_arr').val(cbChecked);
		var confirmprocess_download = confirm("Do You Want To Download?");
		if(confirmprocess_download) {
			$(form).attr('action','../contractEmp/salarypluspdfdownload?mainmenu='+mainmenu+'&time='+datetime);
			$(form).submit();
		}
	} else {
		alert("Please Select Employee ID");return;
	}
}

function salplusPayrollSingledownload(mainmenu,dataCount) {
	if (dataCount != "" && dataCount > 0) {
		var confirmprocess_download = confirm("Do You Want To Download?");
		if(confirmprocess_download) {
			$('#contractemphistorydwnld').attr('action','../contractEmp/salaryplusPayrollSingleDownload?mainmenu='+mainmenu+'&time='+datetime);
			$("#contractemphistorydwnld").submit();
		}
	} else {
		alert('No data found...'); return false;
	}
}

function historyTotal(mainmenu){
	$('#contractempindex').attr('action','../contractEmp/historyTotal?mainmenu='+mainmenu+'&time='+datetime);
	$("#contractempindex").submit();
}
