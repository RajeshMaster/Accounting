$(document).ready(function() {
	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	var countcheck = $("#count").val();
	$('.multiplereg').click(function () {
		$("#frmmultireg").validate({
		//alert();
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
					Basic: {required: true,money: true},
					HrAllowance: {required: true,money: true},
					OT: {required: true,money: true},
					Leave: {required: true},
					Bonus: {required: true,money: true},
					ESI: {required: true},
					IT: {required: true},
					Travel: {required: true,money: true},
					MonthlyTravel: {required: true,money: true},
			},
			submitHandler: function(form) { // for demo
				var confirmprocess = confirm("Do You Want To Register?");
				if(confirmprocess) {
					 pageload();
					 return true;
				} else {
					 return false;
				}
			}
		});
		var temp = true;
		for (var i = 0; i < countcheck; i++) {
			if ($('#basic'+i).val() != '' || $('#hra'+i).val() != '' || $('#ot'+i).val() != ''
				 || $('#esi'+i).val() != '' || $('#it'+i).val() != '' || $('#travel'+i).val() != ''
				  || $('#mtravel'+i).val() != '' || $('#bonus'+i).val() != '' || $('#leave'+i).val() != '') {
				var temp = false;
			}
			if ($('#hra'+i).val() != '' || $('#ot'+i).val() != ''
				 || $('#esi'+i).val() != '' || $('#it'+i).val() != '' || $('#travel'+i).val() != ''
				  || $('#mtravel'+i).val() != '' || $('#bonus'+i).val() != '' || $('#leave'+i).val() != '') {
				$('[name*="basic'+i+'"]').each(function () {
					$(this).rules('add', {
						required: true,
						messages: {
							required: "Please Enter The Value"
						}
					});
				});
			}
		}
		if (temp) {
			alert("Please Enter Atlease One Value");
			$('#basic0').focus();
			return false;
		};
		$.validator.messages.required = function (param, input) {
			var article = document.getElementById(input.id);
			return article.dataset.label + err_fieldreq;
		}
	});

	$('.addeditprocess').click(function () {
		$("#addeditsalaryplus").validate({
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
				Basic: {required: true,money: true},
				HrAllowance: {required: true,money: true},
				OT: {required: true,money: true},
				// Leave: {required: true},
				// Bonus: {required: true,money: true},
				ESI: {required: true},
				IT: {required: true},
				Travel: {required: true,money: true},
				MonthlyTravel: {required: true,money: true},
			},
			submitHandler: function(form) { // for demo
				var date = $('#date').val();
				var Emp_ID = $('#Emp_ID').val();
				if($('#editcheck').val() == 0) {
					// var confirmprocess = confirm("Do You Want To Register?");
					if(confirm(err_confreg)) {
				 	$.ajax({
						type: 'GET',
						url: 'getdatexist',
						data: {"date": date,
								"Emp_ID": Emp_ID},
						success: function(resp) {
							if (resp > 0) { 
								document.getElementById('errorSectiondisplay').innerHTML = "";
								err_invalidcer = "Data Entry Already Exists For this month.";
								var error='<div align="center" style="padding: 0px;margin-top:5px;" id="inform">';
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
							alert(data);
						}
					});
					} else {
						return false;
					}
				} else {
					var datecheck = $('#datecheck').val();
					// var confirmprocess = confirm("Do You Want To Update?");
					if(confirm(err_confup)) {
						if ((datecheck.substr(0, 4) == date.substr(0, 4)) && (datecheck.substr(5, 2) == date.substr(5, 2))) {
							pageload();
							return true;
						}
						$.ajax({
						type: 'GET',
						url: 'getdatexist',
						data: {"date": date,
								"Emp_ID": Emp_ID},
						success: function(resp) {
							if (resp > 0) { 
								document.getElementById('errorSectiondisplay').innerHTML = "";
								err_invalidcer = "Data Entry Already Exists For this month.";
								var error='<div align="center" style="padding: 0px;margin-top:5px;" id="inform">';
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
							alert(data);
						}
					});
					} else {
						return false;
					}
				}
			}
		});
		$.validator.messages.required = function (param, input) {
			var article = document.getElementById(input.id);
			return article.dataset.label + err_fieldreq;
		}
	});
	$('.multiaddeditprocess').click(function () {
	        $("#salarypayment").validate({
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
	                txt_startdate: {required: true,date:true,correctformatdate: true},
	                salarymonth: {required: true},
	                bank: {required: true},
	            },
	            submitHandler: function(form) { // for demo
	                if($('#multiflg').val() == "1") {
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
	            return article.dataset.label + ' field is required';
	        }
	    });

		$("#select_all").change(function(){  //"select all" change 
		    $(".checkpayment").prop('checked', $(this).prop("checked")); //change all ".checkpayment" checked status
		});

		//".checkpayment" change 
		$('.checkpayment').change(function(){ 
			//uncheck "select all", if one of the listed checkpayment item is unchecked
		    if(false == $(this).prop("checked")){ //if this item is unchecked
		        $("#select_all").prop('checked', false); //change "select all" checked status to false
		    }
			//check "select all" if all checkpayment items are checked
			if ($('.checkpayment:checked').length == $('.checkpayment').length ){
				$("#select_all").prop('checked', true);
			}
		});
		$('.addeditprocessnew').click(function () {
        $("#salaryplusaddedit").validate({
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
                txt_startdate: {required: true,date:true,correctformatdate: true},
                salarymonth: {required: true},
                bank: {required: true},
                txt_salary: {required: true,money: true},
                charge: {required: true},
            },
            submitHandler: function(form) { // for demo
                if($('#editflg').val() == "1" || $('#editflg').val() == "3") {
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
    });
});

 
function gotoadd(id,name,total,mainmenu,flg) {
	pageload();
	$('#id').val(id);
	$('#empname').val(name);
	$('#total').val(total);
	$('#editflg').val(flg);
	$('#salaryplusindex').attr('action', 'addeditnew?mainmenu='+mainmenu+'&time='+datetime);
	$("#salaryplusindex").submit();
}
function gotoeditpage(id,salary,mainmenu,flg) {
	pageload();
	$('#editflg').val(flg);
	$('#ids').val(id);
	$('#salary').val(salary);
	$('#salaryplusview').attr('action', 'editprocess?mainmenu='+mainmenu+'&time='+datetime);
	$("#salaryplusview").submit();
}
function isNumberFormat(evt) { 	
	var charCode = (evt.which) ? evt.which : event.keyCode
	var parts = evt.srcElement.value.split('-');
	if (charCode == 08) {
		return true;
	}
	if (charCode == 45 && parts[0].length == 0) {
		if(parts.length == 2)
			return false;
		return true;
	}
	if (charCode > 31 && (charCode < 48 || charCode > 57 ))
		return false;
}
function displaymessage() {
	document.getElementById('errorSectiondisplay').style.display='none';
}
function getdate() {
	$('#date').val(dates);
}
function pageClick(pageval) {
	$('#page').val(pageval);
	$("#salaryplusindex").submit();
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$("#salaryplusindex").submit();
}
function getData(month, year, flg, prevcnt, nextcnt, account_period, lastyear, currentyear, account_val) {
	// alert(month+"###"+year+"###"+ flg+"###"+ prevcnt+"###"+ nextcnt+"###"+ account_period+"###"+ lastyear+"###"+ currentyear+"###"+ account_val);
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
	$('#salaryplusindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#salaryplusindex").submit();
	}
}
function underconstruction() {
	alert('Under Construction');
}
function gotoindexsalaryplus(mainmenu) {
	pageload();
	$('#addeditsalaryplus').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#addeditsalaryplus").submit();
}
function fngotoadd(id,empid,editcheck,mainmenu,firstname,lastname) {
	pageload();
	$('#id').val(id);
	$('#firstname').val(firstname);
	$('#lastname').val(lastname);
	$('#Emp_ID').val(empid);
	if (editcheck == 0) {
		$('#editcheck').val(editcheck);
		$('#salaryplusindex').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#salaryplusindex").submit();	
	} else {
		$('#editcheck').val('2');
		$('#salaryplusindex').attr('action', 'view?mainmenu='+mainmenu+'&time='+datetime);
		$("#salaryplusindex").submit();
	}
}
function fngotoedit(mainmenu) {
	pageload();
	$('#editcheck').val('1');
	$('#addeditsalaryplus').attr('action', 'edit?mainmenu='+mainmenu+'&time='+datetime);
	$("#addeditsalaryplus").submit();
}
function salaryselectpopup() {
	var mainmenu = $('#mainmenu').val();
	var year = $('#selYear').val();
	var month = $('#selMonth').val();
	popupopenclose(1);
	$('#salarypluspopup').load('../Salaryplus/salarypluspopup?mainmenu='+mainmenu+'&year='+year+'&month='+month);
	$("#salarypluspopup").modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#salarypluspopup').modal('show');
}
function empselectbypopupclick() {
	var length = $("#to option").length;
	if(length==0) {
		alert("Please Select atleast One Employee")
		return false;
	}
	var Emp_selection = "Do You Want To Add?";
	if(confirm(Emp_selection)) {
		$('#to option').prop('selected', true);
		$('#from option').prop('selected', true);
		document.empselectform.submit();
		return true;
	} else {
		return false;
	}
}
function gotoindex(viewflg,mainmenu) {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
	pageload();
	if ($('#editcheck').val() == 1) {
		$('#editcheck').val('2');
		$('#salaryplusaddeditcancel').attr('action', 'view?mainmenu='+mainmenu+'&time='+datetime);
		$("#salaryplusaddeditcancel").submit();
	} else {
		$('#salaryplusaddeditcancel').attr('action', viewflg+'?mainmenu='+mainmenu+'&time='+datetime);
		$("#salaryplusaddeditcancel").submit();
	}
}
function goindex(viewflg,mainmenu) {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
	pageload();
	$('#salaryplusmultieditcancel').attr('action', viewflg+'?mainmenu='+mainmenu+'&time='+datetime);
	$("#salaryplusmultieditcancel").submit();
}
function gosingletoindex(mainmenu) {
	pageload();
	if ($('#gobackflg').val() == "1") {
		$('#salaryplusview').attr('action', 'Viewlist?mainmenu='+mainmenu+'&time='+datetime);
		$("#salaryplusview").submit();
	} else{
		$('#salaryplusview').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#salaryplusview").submit();
	}
}
/*function fnsetesi(id) {
	var result = id.replace(",", "");
	var result = result.replace(",", "");
	var total = result*0.005/100;
	total = Math.round(total);
	totals = total.toLocaleString();
	if (totals == 0) {
		totals = 1;
	}
	if ($('#Basic').val() == "") {
		totals = "";
	}
	$('#ESI').val(totals);
}*/
function multi_reg(){
	var mainmenu = $('#mainmenu').val();
	//alert(mainmenu);
	$('#salaryplusindex').attr('action','../Salaryplus/multieditprocess?mainmenu='+mainmenu+'&time='+datetime);
	$("#salaryplusindex").submit();
}
function pay_reg(flg){
	var mainmenu = $('#mainmenu').val();
	$('#multiflg').val(flg);
	$('#salaryplusindex').attr('action','../Salaryplus/multipaymentscreen?mainmenu='+mainmenu+'&time='+datetime);
	$("#salaryplusindex").submit();
}
function gotoindexpage(viewflg,mainmenu) {
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
	pageload();
	$('#salaryplusmulticancel').attr('action', viewflg+'?mainmenu='+mainmenu+'&time='+datetime);
	$("#salaryplusmulticancel").submit();
}
function gotoidx(viewflg,mainmenu) {
    var back =$('#editflg').val();
    if (cancel_check == false) {
        if (!confirm("Do You Want To Cancel the Page?")) {
            return false;
        }
    }
    if (back == "1" || back == "3") {
        pageload();
        $('#salaryplusaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
        $("#salaryplusaddeditcancel").submit();
    }else if (viewflg == "1") {
        pageload();
        $('#salaryplusaddeditcancel').attr('action', 'singleview?mainmenu='+mainmenu+'&time='+datetime);
        $("#salaryplusaddeditcancel").submit();
    } else {
        pageload();
        $('#salaryplusaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
        $("#salaryplusaddeditcancel").submit();
    }
}
function monthchange(month) {
	var mainmenu = $('#mainmenu').val();
	$('#monthonchangefrm #selMonth').val(month);
	$('#monthonchangefrm').attr('action','../Salaryplus/multieditprocess?mainmenu='+mainmenu+'&time='+datetime);
	$("#monthonchangefrm").submit();
}

function paymentonchange(month) {
	var mainmenu = $('#mainmenu').val();
	$('#monthonchangefrm #selMonth').val(month);
	$('#monthonchangefrm').attr('action','../Salaryplus/multipaymentscreen?mainmenu='+mainmenu+'&time='+datetime);
	$("#monthonchangefrm").submit();
}

function EditRecord(){
	alert('Under Construction');
	
}
function gotomultoindex(viewflg,mainmenu) {
	if (cancel_check == false) {
        if (!confirm("Do You Want To Cancel the Page?")) {
            return false;
        }
    }
    //pageload();
	$('#frmmultireg').attr('action', viewflg+'?mainmenu='+mainmenu+'&time='+datetime);
    $("#frmmultireg").submit();
}