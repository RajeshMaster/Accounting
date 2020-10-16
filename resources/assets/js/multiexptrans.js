//register button Click Process
$(document).ready(function() {
	$('.multiaddeditprocess').click(function () {
		$("#frmexpensesmultiaddedit").validate({
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
				date: {required: true, date: true,minlength:10,correctformatdate: true, accessDateCheck: "#accessdate"},
				mainsubject: {required: true},
				subsubject: {required: true},
				bankname: {required: true},
				banknameloan: {required: true},
				bank: {required: true},
				amount: {requiredWithZero: true},
				charge: {required: true,money: true},
				loanname: {required: true},
				loantype: {required: true},
				interest: {required: true},
				transtype: {required: true},
				charge_1: {required: true},
				file1 : {extension: "jpg,jpeg,png,JPG,JPEG,PNG", filesize : (2 * 1024 * 1024)},
			},
			submitHandler: function(form) { // for demo
				
					if(confirm(err_confreg)) {
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
		$.validator.messages.minlength = function (param, input) {
			var article = document.getElementById(input.id);
			return "Please Enter valid 10 Number";
		}
	});
});
//Expenses Radio button click process
function expensesrdo(mainmenu){
	$('#cashid').val('1');
	$('.bankname').addClass('display_none');
	$('.cashbank').addClass('display_none');
	$('.transaction').addClass('display_none');
	$('#expenses_btn').removeClass('btn btn-success');
	$('#transfer_btn').removeClass('btn1');
	$('#transfer_btn').addClass('btn btn-success');
	$('#pettycash_btn').removeClass('btn1');
	$('#pettycash_btn').addClass('btn btn-success');
	$('#others_btn').removeClass('btn1');
	$('#others_btn').addClass('btn btn-success');
	$('#loan_btn').removeClass('btn1');
	$('#loan_btn').addClass('btn btn-success');
	$('#cash_btn').removeClass('btn1');
	$('#cash_btn').addClass('btn btn-success');
	$('#expenses_btn').addClass('btn btn1');
	$('.charge').addClass('display_none');
	$('.tranfer').addClass('display_none');
	$('.pettycash').addClass('display_none');
	$('.expenicon').removeClass('display_none');
	$('.expenses_icon').addClass('display_none');
	$('.others_icon').addClass('display_none');
	$('.loan_icon').addClass('display_none');
	$('.pettycashicon').addClass('display_none');
	$('.expenses').removeClass('display_none');
	$('.Cash').addClass('display_none');
	$('.Loan').addClass('display_none');
	$('.anchorstyle').addClass('display_none');
	$('.mainsub').addClass('display_none');
	$('.mainsub').removeClass('display_none');
	$('.subsub').addClass('display_none');
	$('.subsub').removeClass('display_none');
	$('.bill').addClass('display_none');
	$('.bill').removeClass('display_none');
	$('#empselect').removeClass('display_none');
	$('.banknameloan').addClass('display_none');
	$('.loanname').addClass('display_none');
	$('.loantype').addClass('display_none');
	$('.interest').addClass('display_none');
	$('.Others').addClass('display_none');
	$('#date').val('');
	$('#txt_empname').val('');
	$('#emp_IDs').val('');
	$('#amount').val('');
	$('#file1').val('');
	$('#remarks').val('');
	$('#bankname').val('');
	$('#charge_1').val('');
	$("#mainsubject option").remove();
	$("#subsubject option").remove();
	$("#mainsubject").prepend("<option value='' selected='selected'></option>");
	//Get the value Main Subject
		$.ajax({
	        	type:"GET",
        		dataType: "JSON",
	        	url: 'getmainsubject',
	        	data: {
           			mainmenu: mainmenu
	        	},
	        	success: function(data){
					$.each( data, function( key, value ) {
						$('#mainsubject').append( '<option value="'+key+'">'+value+'</option>' );
					})
		        },
		        error: function(xhr, textStatus, errorThrown){
		        }  
		    })
}
//Transfer Radio button click process
function transferrdo(mainmenu){
	$('#cashid').val('2');
	$('#transfer_btn').removeClass('btn btn-success');
	$('#expenses_btn').removeClass('btn1');
	$('#expenses_btn').addClass('btn btn-success');
	$('#pettycash_btn').removeClass('btn1');
	$('#pettycash_btn').addClass('btn btn-success');
	$('#others_btn').removeClass('btn1');
	$('#others_btn').addClass('btn btn-success');
	$('#cash_btn').removeClass('btn1');
	$('#cash_btn').addClass('btn btn-success');
	$('#loan_btn').removeClass('btn1');
	$('#loan_btn').addClass('btn btn-success');
	$('#transfer_btn').addClass('btn btn1');
	$('.charge').removeClass('display_none');
	$('.bankname').removeClass('display_none');
	$('.expenses').addClass('display_none');
	$('.tranfer').removeClass('display_none');
	$('.pettycash').addClass('display_none');
	$('.expenicon').addClass('display_none');
	$('.pettycashicon').addClass('display_none');
	$('.expenses_icon').removeClass('display_none');
	$('.others_icon').addClass('display_none');
	$('.loan_icon').addClass('display_none');
	$('.anchorstyle').removeClass('display_none');
	$('.mainsub').addClass('display_none');
	$('.mainsub').removeClass('display_none');
	$('.subsub').addClass('display_none');
	$('.subsub').removeClass('display_none');
	$('.bill').addClass('display_none');
	$('.bill').removeClass('display_none');
	$('.Cash').addClass('display_none');
	$('.Loan').addClass('display_none');
	$('#empselect').removeClass('display_none');
	$('.transaction').addClass('display_none');
	$('.cashbank').addClass('display_none');
	$('.banknameloan').addClass('display_none');
	$('.Others').addClass('display_none');
	$('.loanname').addClass('display_none');
	$('.loantype').addClass('display_none');
	$('.interest').addClass('display_none');	
	$('#date').val('');
	$('#txt_empname').val('');
	$('#emp_IDs').val('');
	$('#amount').val('');
	$('#file1').val('');
	$('#remarks').val('');
	$('#bankname').val('');
	$('#charge_1').val('');
	$("#mainsubject option").remove();
	$("#subsubject option").remove();
	$("#mainsubject").prepend("<option value='' selected='selected'></option>");
	//Get the value Main Subject
	$.ajax({
	        type:"GET",
	        dataType: "JSON",
	        url: 'getmainsubject',
	        data: {
	            mainmenu: mainmenu
	        },
	        success: function(data){
				$.each( data, function( key, value ) {
					$('#mainsubject').append( '<option value="'+key+'">'+value+'</option>' );
				})
	        },
	        error: function(xhr, textStatus, errorThrown){
	        }  
	    })
}
//Petty cash Radio button click process
function pettycashrdo(mainmenu){
	$('#cashid').val('3');
	$('.bankname').addClass('display_none');
	$('#pettycash_btn').removeClass('btn btn-success');
	$('#expenses_btn').removeClass('btn1');
	$('#expenses_btn').addClass('btn btn-success');
	$('#transfer_btn').removeClass('btn1');
	$('#transfer_btn').addClass('btn btn-success');
	$('#others_btn').removeClass('btn1');
	$('#others_btn').addClass('btn btn-success');
	$('#cash_btn').removeClass('btn1');
	$('#cash_btn').addClass('btn btn-success');
	$('#loan_btn').removeClass('btn1');
	$('#loan_btn').addClass('btn btn-success');
	$('#pettycash_btn').addClass('btn btn1');
	$('.charge').addClass('display_none');
	$('.tranfer').addClass('display_none');
	$('.pettycash').removeClass('display_none');
	$('.Others').addClass('display_none');
	$('.expenses').addClass('display_none');
	$('.expenicon').addClass('display_none');
	$('.pettycashicon').removeClass('display_none');
	$('.expenses_icon').addClass('display_none');
	$('.others_icon').addClass('display_none');
	$('.loan_icon').addClass('display_none');
	$('.anchorstyle').addClass('display_none');
	$('.mainsub').removeClass('display_none');
	$('.subsub').removeClass('display_none');
	$('.bill').removeClass('display_none');
	$('.Cash').addClass('display_none');
	$('#empselect').removeClass('display_none');
	$('.transaction').addClass('display_none');
	$('.cashbank').addClass('display_none');
	$('.Loan').addClass('display_none');
	$('.loanname').addClass('display_none');
	$('.loantype').addClass('display_none');
	$('.interest').addClass('display_none');
	$('.banknameloan').addClass('display_none');
	$('.Others').addClass('display_none');
	$("#mainsubject option").remove();
	$("#subsubject option").remove();
	$("#mainsubject option").remove();
	$("#subsubject option").remove();
	$('#date').val('');
	$('#txt_empname').val('');
	$('#emp_IDs').val('');
	$('#amount').val('');
	$('#file1').val('');
	$('#remarks').val('');
	$("#mainsubject").prepend("<option value='' selected='selected'></option>");
	//Get the value Main Subject
	$.ajax({
	        type:"GET",
	        dataType: "JSON",
	        url: 'getpettymainsubject',
	        data: {
	            mainmenu: mainmenu
	        },
	        success: function(data){
				$.each( data, function( key, value ) {
					$('#mainsubject').append( '<option value="'+key+'">'+value+'</option>' );
				})
	        },
	        error: function(xhr, textStatus, errorThrown){
	        }  
	    })
}
function loanprocess(mainmenu){
	$('#cashid').val('4');
	$('.Loan').removeClass('display_none');
	$('.banknameloan').removeClass('display_none');
	$('#loan_btn').removeClass('btn btn-success');
	$('#transfer_btn').removeClass('btn1');
	$('#transfer_btn').addClass('btn btn-success');
	$('#expenses_btn').removeClass('btn1');
	$('#expenses_btn').addClass('btn btn-success');
	$('#pettycash_btn').removeClass('btn1');
	$('#pettycash_btn').addClass('btn btn-success');
	$('#others_btn').removeClass('btn1');
	$('#others_btn').addClass('btn btn-success');
	$('#cash_btn').removeClass('btn1');
	$('#cash_btn').addClass('btn btn-success');
	$('#loan_btn').addClass('btn btn1');
	$('.charge').addClass('display_none');
	$('.bankname').addClass('display_none');
	$('.expenses').addClass('display_none');
	$('.mainsub').addClass('display_none');
	$('.subsub').addClass('display_none');
	$('#empselect').addClass('display_none');
	$('.Others').addClass('display_none');
	$('.Cash').addClass('display_none');
	$('.tranfer').addClass('display_none');
	$('.pettycash').addClass('display_none');
	$('.transaction').addClass('display_none');
	$('.cashbank').addClass('display_none');
	$('.expenicon').addClass('display_none');
	$('.pettycashicon').addClass('display_none');
	$('.loan_icon').removeClass('display_none');
	$('.expenses_icon').addClass('display_none');
	$('.others_icon').addClass('display_none');
	$('.anchorstyle').removeClass('display_none');
	$('.bill').addClass('display_none');
	$('.loanname').removeClass('display_none');
	$('.loantype').removeClass('display_none');
	$('.loanamount').removeClass('display_none');
	$('.interest').removeClass('display_none');
	$('#date').val('');
	$('#amount').val('');
	$('#remarks').val('');
	$('#banknameloan').val('');
	$('#loanname').val('');
	$('#loantype').val('');
	$('#interest').val('');
	$.ajax({
	        type:"GET",
	        dataType: "JSON",
	        url: 'gotoindex',
	        data: {
	            mainmenu: mainmenu
	        },
	   //      success: function(data){
				// $.each( data, function( key, value ) {
				// 	$('#mainsubject').append( '<option value="'+key+'">'+value+'</option>' );
				// })
	   //      },
	        error: function(xhr, textStatus, errorThrown){
	        }  
	    })
}

function cashprocess(mainmenu){
	$('#cashid').val('5');
	$('.Cash').removeClass('display_none');
	$('.Loan').addClass('display_none');
	$('#cash_btn').removeClass('btn btn-success');
	$('#transfer_btn').removeClass('btn1');
	$('#transfer_btn').addClass('btn btn-success');
	$('#expenses_btn').removeClass('btn1');
	$('#expenses_btn').addClass('btn btn-success');
	$('#pettycash_btn').removeClass('btn1');
	$('#pettycash_btn').addClass('btn btn-success');
	$('#others_btn').removeClass('btn1');
	$('#others_btn').addClass('btn btn-success');
	$('#loan_btn').removeClass('btn1');
	$('#loan_btn').addClass('btn btn-success');
	$('#cash_btn').addClass('btn btn1');
	$('.charge').addClass('display_none');
	$('.bankname').addClass('display_none');
	$('.expenses').addClass('display_none');
	$('.mainsub').addClass('display_none');
	$('.subsub').addClass('display_none');
	$('#empselect').removeClass('display_none');
	$('.Others').addClass('display_none');
	$('.cash').removeClass('display_none');
	$('.tranfer').addClass('display_none');
	$('.pettycash').addClass('display_none');
	$('.transaction').removeClass('display_none');
	$('.loan').addClass('display_none');
	$('.cashbank').removeClass('display_none');
	$('.expenicon').addClass('display_none');
	$('.pettycashicon').removeClass('display_none');
	$('.expenses_icon').addClass('display_none');
	$('.loan_icon').addClass('display_none');
	$('.others_icon').addClass('display_none');
	$('.anchorstyle').removeClass('display_none');
	$('.banknameloan').addClass('display_none');
	$('.loanname').addClass('display_none');
	$('.loantype').addClass('display_none');
	$('.interest').addClass('display_none');
	$('.bill').addClass('display_none');
	$('#date').val('');
	$('#txt_empname').val('');
	$('#emp_IDs').val('');
	$('#amount').val('');
	$('#remarks').val('');
	$('#bank').val('');
	// $.ajax({
	//         type:"GET",
	//         dataType: "JSON",
	//         url: 'gotoindex',
	//         data: {
	//             mainmenu: mainmenu
	//         },
	//         success: function(data){
	// 			$.each( data, function( key, value ) {
	// 				$('#mainsubject').append( '<option value="'+key+'">'+value+'</option>' );
	// 			})
	//         },
	//         error: function(xhr, textStatus, errorThrown){
	//         }  
	//     })
}
function otherprocess(mainmenu){
	$('#cashid').val('6');
	$('.Cash').addClass('display_none');
	$('.Loan').addClass('display_none');
	$('#others_btn').removeClass('btn btn-success');
	$('#transfer_btn').removeClass('btn1');
	$('#transfer_btn').addClass('btn btn-success');
	$('#expenses_btn').removeClass('btn1');
	$('#expenses_btn').addClass('btn btn-success');
	$('#pettycash_btn').removeClass('btn1');
	$('#pettycash_btn').addClass('btn btn-success');
	$('#cash_btn').removeClass('btn1');
	$('#cash_btn').addClass('btn btn-success');
	$('#loan_btn').removeClass('btn1');
	$('#loan_btn').addClass('btn btn-success');
	$('#others_btn').addClass('btn btn1');
	$('.charge').addClass('display_none');
	$('.bankname').removeClass('display_none');
	$('.expenses').addClass('display_none');
	$('.mainsub').addClass('display_none');
	$('.subsub').addClass('display_none');
	$('#empselect').addClass('display_none');
	$('.bill').addClass('display_none');
	$('.Others').removeClass('display_none');
	$('.transaction').addClass('display_none');
	$('.cashbank').addClass('display_none');
	$('.tranfer').addClass('display_none');
	$('.pettycash').addClass('display_none');
	$('.expenicon').addClass('display_none');
	$('.pettycashicon').addClass('display_none');
	$('.expenses_icon').addClass('display_none');
	$('.others_icon').removeClass('display_none');
	$('.loan_icon').addClass('display_none');
	$('.anchorstyle').removeClass('display_none');
	$('.banknameloan').addClass('display_none');
	$('.loanname').addClass('display_none');
	$('.loantype').addClass('display_none');
	$('.interest').addClass('display_none');
	$('.bill').addClass('display_none');
	$('#date').val('');
	$('#amount').val('');
	$('#remarks').val('');
	$('#bankname').val('');
	$.ajax({
	        type:"GET",
	        dataType: "JSON",
	        url: 'gotoindex',
	        data: {
	            mainmenu: mainmenu
	        },
	   //      success: function(data){
				// $.each( data, function( key, value ) {
				// 	$('#mainsubject').append( '<option value="'+key+'">'+value+'</option>' );
				// })
	   //      },
	        error: function(xhr, textStatus, errorThrown){
	        }  
	    })
}
//Expenses,Transfer Sub Subject getdata
function fngetsubsubjects(mainid){
	var lang = $('#lang').val();
	var cashid = $('#cashid').val();
	$("#subsubject").prepend("<option value='' selected='selected'></option>");
	if(lang == "jp") {
		$('#subsubject').find('option').not(':first').remove();
		$.ajax({
	        type:"GET",
	        dataType: "JSON",
	        url: 'ajaxsubsubject',
	        data: {
	            mainid: mainid,
	            cashid: cashid
	        },
	        success: function(data){ // What to do if we succeed
	           for (i = 0; i < data.length; i++)
	            { 
	                 $('#subsubject').append( '<option value="'+data[i]["id"]+'">'+data[i]["sub_jap"]+'</option>' );
	                 // $('select[name="subsubject"]').val(mainid);
	            }
	            // $('select[name="subsubject"]').val(subid);
	        },
	        error: function(xhr, textStatus, errorThrown){
	        }  
	    })
	} else {
	    $('#subsubject').find('option').not(':first').remove();
		$.ajax({
	        type:"GET",
	        dataType: "JSON",
	        url: 'ajaxsubsubject',
	        data: {
	            mainid: mainid,
	            cashid: cashid
	        },
	        success: function(data){ // What to do if we succeed
	           for (i = 0; i < data.length; i++)
	            { 
	                 $('#subsubject').append( '<option value="'+data[i]["id"]+'">'+data[i]["sub_eng"]+'</option>' );
	                 // $('select[name="subsubject"]').val(mainid);
	            }
	            // $('select[name="subsubject"]').val(subid);
	        },
	        error: function(xhr, textStatus, errorThrown){
	        }  
	    })
	}
}
//Petty Cash Sub Subject getdata
function fngetpettysubsubjects(mainid){
	var lang = $('#lang').val();
	var cashid = $('#cashid').val();
	$("#subsubject").prepend("<option value='' selected='selected'></option>");
	if(lang == "jp") {
		$('#subsubject').find('option').not(':first').remove();
		$.ajax({
	        type:"GET",
	        dataType: "JSON",
	        url: 'getpettysubsubject',
	        data: {
	            mainid: mainid,
	            cashid: cashid
	        },
	        success: function(data){ // What to do if we succeed
	           for (i = 0; i < data.length; i++)
	            { 
	                 $('#subsubject').append( '<option value="'+data[i]["id"]+'">'+data[i]["sub_jap"]+'</option>' );
	                 // $('select[name="subsubject"]').val(mainid);
	            }
	            // $('select[name="subsubject"]').val(subid);
	        },
	        error: function(xhr, textStatus, errorThrown){
	        }  
	    })
	} else {
	    $('#subsubject').find('option').not(':first').remove();
		$.ajax({
	        type:"GET",
	        dataType: "JSON",
	        url: 'getpettysubsubject',
	        data: {
	            mainid: mainid,
	            cashid: cashid
	        },
	        success: function(data){ // What to do if we succeed
	           for (i = 0; i < data.length; i++)
	            { 
	                 $('#subsubject').append( '<option value="'+data[i]["id"]+'">'+data[i]["sub_eng"]+'</option>' );
	                 // $('select[name="subsubject"]').val(mainid);
	            }
	            // $('select[name="subsubject"]').val(subid);
	        },
	        error: function(xhr, textStatus, errorThrown){
	        }  
	    })
	}
}
//Cancel Button Click Process
function gotoindex(viewflg,mainmenu) {
	if (cancel_check == false) {
        if (!confirm("Do You Want To Cancel the Page?")) {
            return false;
        }
    }
    pageload();
    if (mainmenu == "expenses") {
		$('#expmultiaddeditcancel').attr('action','../Expenses/index?mainmenu=expenses&time='+datetime);
		$("#expmultiaddeditcancel").submit();
	} else if (mainmenu == "pettycash") {
		$('#expmultiaddeditcancel').attr('action','../Expenses/index?mainmenu=pettycash&time='+datetime);
		$("#expmultiaddeditcancel").submit();
	} else {
		$('#expmultiaddeditcancel').attr('action','../Transfer/index?mainmenu=company_transfer&time='+datetime);
		$("#expmultiaddeditcancel").submit();
	}
}
//Transfer current date click Process
function getdate() {
	$('#date').val(dates);
}
//Number Only type
function numberonly(e) {
  e=(window.event) ? event : e;
  return (/[0-9]/.test(String.fromCharCode(e.keyCode))); 
}
function fngetDetexp(empid,empname,name) {
	var name = empname.concat(" ").concat(name);
	$("#"+empid).prop("checked", true);
	$('#txt_empname').val(name);
	$('#empid').val(empid);
	$('#empKanaName').val(name);
}
function fndbclickexp(empid,empname,name) {
	var name = empname.concat(" ").concat(name);
	$("#"+empid).prop("checked", true);
	$('#txt_empname').val(name);
	$('#emp_IDs').val(empid);
	$('#empnamepopup').modal('toggle');
}
function fnaddempid(){
	var empid=$('#empid').val();
	var empKanaName=$('#empKanaName').val();
	$('#txt_empname').val(empKanaName);
	$('#emp_IDs').val(empid);
	$('#empnamepopup').modal('toggle');
}
function getselectedTexts(mainid,subid,selval,edit) {
	if(selval != "-" && edit != "" && edit != undefined && selval != undefined){
		$("#transfer").attr("style", "display:inline-block");
	}
    $('#transfer').find('option').not(':first').remove();
	$.ajax({
        type:"GET",
        dataType: "JSON",
        url: 'ajaxmainsubject',
        data: {
            mainid: mainid
        },
        success: function(data){ // What to do if we succeed
           for (i = 0; i < data.length; i++)
            { 
                 $('#transfer').append( '<option value="'+data[i]["BankName"]+'-'+data[i]["AccNo"]+'">'+data[i]["Bank_NickName"]+'-'+data[i]["AccNo"]+'</option>' );
                 // $('select[name="transfer"]').val(data[i]["banknameTransfer"]);
            }
            $('select[name="transfer"]').val(subid);
        },
        error: function(xhr, textStatus, errorThrown){
        }  
    })
}
function fngetloanname(mainid,subid) {
	$('#loanname').find('option').not(':first').remove();
	$.ajax({
		type:"GET",
		dataType: "JSON",
		url: 'ajaxloanname',
		data: {
			mainid: mainid
		},
		success: function(data){ // What to do if we succeed
			$("#empidd").text('');
			for (i = 0; i < data.length; i++) { 
				$('#loanname').append( '<option value="'+data[i]["loanNo"]+'">'+data[i]["loanName"]+'</option>' );
				// $('select[name="subsubject"]').val(mainid);
				$("#empidd").text(data[i]["loanNo"]);
				if (data.length == 1) {
					$('select[name="loanname"]').val(data[i]["loanNo"]);
					fnSetLoanNo('loanname');
				} else {
					$("#empidd").text('');
			    }
				// $('select[name="loanname"]').val(subid);
			}
			if (subid != undefined) {
				$('select[name="loanname"]').val(subid);
				fnSetLoanNo('loanname');
			}
		},
		error: function(xhr, textStatus, errorThrown){
		}  
	})
}
function fnSetLoanNo(id) {
	var getlnNo = document.getElementById(id);
	document.getElementById('empidd').innerHTML = getlnNo.value;
	document.getElementById('empidd').style.color = "#9C0000";
	document.getElementById('empidd').style.fontWeight = "bold";
}
function interestcheck(evt) { 
	if (!(evt.keyCode == 46 || (evt.keyCode >= 48 && evt.keyCode <= 57))) return false;
	var parts = evt.srcElement.value.split('.');
	var txtleng = $('#interest').val();
	if (parts == "" && evt.keyCode == 46) return false;
	if (evt.srcElement.value.length > 1 && !(evt.keyCode == 46) && parts[1] != '') 
		{ if (parts.length >= 2) {} else { return false }}; 
	if (parts.length > 2) return false;
	if (evt.keyCode == 46) return (parts.length == 1);
	if (parts[0].length > 3) return false;
	if (parts.length == 2 && parts[1].length > 1) return false;
}