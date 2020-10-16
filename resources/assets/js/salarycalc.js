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
        $("#addeditsalarycalc").validate({
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
                transferred: {required: true,money: true},
            },
            submitHandler: function(form) { // for demo
                if($('#editcheck').val() == 0) {
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
});
function pageClick(pageval) {
	$('#page').val(pageval);
	$("#salarycalcindex").submit();
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$("#salarycalcindex").submit();
}

function multi_reg_calc(){
	var mainmenu = $('#mainmenu').val();
	var salChecked = new Array();
	if($('.checkboxid:checkbox:checked').length > 0){
		$('.checkboxid:checkbox:checked').each(function() {
	      salChecked[salChecked.length] = this.value;            
	    });
		$('#hdn_salid_arr').val(salChecked);
		$('#salflg').val('1');
	}
	$('#salarycalcindex').attr('action','../salarycalc/multieditprocess?mainmenu='+mainmenu+'&time='+datetime);
	$("#salarycalcindex").submit();
}

function monthchangecalc(month) {
	var mainmenu = $('#mainmenu').val();
	$('#frmmultireg #selMonth').val(month);
	$('#frmmultireg').attr('action','../salarycalc/multieditprocess?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmmultireg").submit();
}

function salaryselectpopup_main() {
	var mainmenu = $('#mainmenu').val();
	var year = $('#selYear').val();
	var month = $('#selMonth').val();
	popupopenclose(1);
	$('#salarypopup').load('../salarycalc/salarypopup?mainmenu='+mainmenu+'&year='+year+'&month='+month);
	$("#salarypopup").modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#salarypopup').modal('show');
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
	$('#salarycalcindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#salarycalcindex").submit();
	}
}

function sendmail() {
	var cbChecked = new Array();
	if($('.checkbox:checkbox:checked').length > 0){
		$('.checkbox:checkbox:checked').each(function() {
	      cbChecked[cbChecked.length] = this.value;            
	    });


		$('#hdn_empid_arr').val(cbChecked);
		var mainmenu = $('#mainmenu').val();
		$('#salarycalcindex').attr('action','../salarycalc/mailsendprocess?mainmenu='+mainmenu+'&time='+datetime);
		$("#salarycalcindex").submit();
	} else {
		alert("Please Select Employee ID");return;
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
		$('#salarycalcindex').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
		$("#salarycalcindex").submit();	
	} else {
		$('#editcheck').val('2');
		$('#salarycalcindex').attr('action', 'view?mainmenu='+mainmenu+'&time='+datetime);
		$("#salarycalcindex").submit();
	}
}

function gotoindexsalarycalc(mainmenu) {
	pageload();
	$('#addeditsalarycalc').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#addeditsalarycalc").submit();
}

function fngotoedit(mainmenu) {
	pageload();
	$('#editcheck').val('1');
	$('#addeditsalarycalc').attr('action', 'edit?mainmenu='+mainmenu+'&time='+datetime);
	$("#addeditsalarycalc").submit();
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
	if ($('#editcheck').val() == 1) {
		$('#editcheck').val('2');
		$('#salaryplusaddeditcancel').attr('action', 'view?mainmenu='+mainmenu+'&time='+datetime);
		$("#salaryplusaddeditcancel").submit();
	} else {
		$('#salaryplusaddeditcancel').attr('action', viewflg+'?mainmenu='+mainmenu+'&time='+datetime);
		$("#salaryplusaddeditcancel").submit();
	}
}
function getdate() {
	$('#date').val(dates);
}