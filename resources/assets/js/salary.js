var data = {};
$(function () {
	var cc = 0;
	$('#salarysort').click(function () {
		cc++;
		if (cc == 2) {
			$(this).change();
			cc = 0;
		}         
	}).change (function () {
		sortingfun();
		cc = -1;
	});
	$('#salaryviewsort').click(function () {
		cc++;
		if (cc == 2) {
			$(this).change();
			cc = 0;
		}         
	}).change (function () {
		sortingfun();
		cc = -1;
	}); 
	// MOVE SORTING
	var ccd = 0;
	$('#sidedesignselector').click(function () {
		if( $('#searchmethod').val() == 1 || $('#searchmethod').val() == 2) {
			ccd++;
		}
		if (ccd % 2 == 0) {
			movediv = "+=220px"
		} else {
			movediv = "-=220px"
		}
		$('#salarysort').animate({
			'marginRight' : movediv //moves down
		});
		ccd++;
		if( $('#searchmethod').val() == 1 || $('#searchmethod').val() == 2){
			ccd--;
		}  
	});
	// initialize tooltipster on text input elements
    // initialize validate plugin on the form
    $('.addeditprocess').click(function () {
        $("#salaryaddedit").validate({
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
    // initialize tooltipster on text input elements
    // initialize validate plugin on the form
    $('.multiaddeditprocess').click(function () {
        $("#salarymultiadd").validate({
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
});
function UnderConstruction() {
	alert("Under Construction");
}
function filledcondition() {
    alert("All Salary Details Are Filled For This Year And Month.");
}
function empselecttpopupenable(datemonth,mainmenu) {
    popupopenclose(1);
    $('#datemonth').val(datemonth);
	$('#empselectionpopup').load('../Salary/empselectionpopup?mainmenu='+mainmenu+'&time='+datetime+'&datemonth='+datemonth);
	$("#empselectionpopup").modal({
           backdrop: 'static',
            keyboard: false
        });
    $('#empselectionpopup').modal('show');
}
function empselectbypopupclick() {
    if($('#to option').val() == undefined) {
        alert("Please Select the Name");
        return false;
    }else {
        var Emp_selection = "Do You Want To Select ?";
        if(confirm(Emp_selection)) {
            $('#to option').prop('selected', true);
            $('#from option').prop('selected', true);
            document.empselpopup.submit();
            return true;
        } else {
            return false;
        }
    }
}
function pageClick(pageval) {
    $('#page').val(pageval);
    if($('#id').val()!=""){
        $("#salaryviewlist").submit();
    }else if($('#id').val()==""){
        $("#salaryindex").submit();
    }
}
function pageLimitClick(pagelimitval) {
    $('#page').val('');
    $('#plimit').val(pagelimitval);
    if($('#id').val()!=""){
        $("#salaryviewlist").submit();
    }else if($('#id').val()==""){
        $("#salaryindex").submit();
    }
}
function sortingfun() {
	pageload();
    $('#plimit').val(50);
    $('#page').val('');
    if($('#id').val()!=""){
    	var sortselect=$('#salaryviewsort').val();
    }else if($('#id').val()==""){
    	var sortselect=$('#salarysort').val();
    }
    $('#sortOptn').val(sortselect);
    var alreadySelectedOptn=$('#sortOptn').val();
    var alreadySelectedOptnOrder=$('#sortOrder').val();
    if (sortselect == alreadySelectedOptn) {
        if (alreadySelectedOptnOrder == "asc") {
            $('#sortOrder').val('desc');
        } else {
            $('#sortOrder').val('asc');
        }
    }
    if($('#id').val()!=""){
	    $("#salaryviewlist").submit();
    } else {
	    $("#salaryindex").submit();
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
		$('#selMonth').val(("0" + month).substr(-2));
		$('#selYear').val(year);
		$('#prevcnt').val(prevcnt);
		$('#nextcnt').val(nextcnt);
		$('#account_val').val(account_val);
		$('#sorting').val('');
 		$('#page').val('');
		$('#plimit').val('');
		$('#salaryindex').submit();
	}
}
function gotoSingleview(id,empno,name,salary,mainmenu,bankid) {
	pageload();
    $('#ids').val(id);
    $('#id').val(empno);
    $('#empname').val(name);
    $('#salary').val(salary);
    $('#bankid').val(bankid);
    $('#salaryindex').attr('action', 'Singleview'+'?mainmenu='+mainmenu+'&time='+datetime);
    $("#salaryindex").submit();
}
function gotoViewlist(id,empno,name,mainmenu) {
	pageload();
	$('#ids').val(id);
    $('#id').val(empno);
    $('#empname').val(name);
    $('#salaryindex').attr('action', 'Viewlist'+'?mainmenu='+mainmenu+'&time='+datetime);
    $("#salaryindex").submit();
}
function gotosingviewpage(id,mainmenu,name,backflg) {
	pageload();
	$('#ids').val(id);
    $('#empname').val(name);
	$('#gobackflg').val(backflg);
    $('#salaryviewlist').attr('action', 'Singleview'+'?mainmenu='+mainmenu+'&time='+datetime);
    $("#salaryviewlist").submit();
}
function gotoaddsalary(mainmenu,flg,ids) {
    pageload();
    $('#editflg').val(flg);
    $('#ids').val(ids);
    $('#salaryviewlist').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
    $("#salaryviewlist").submit();
}
function gotomultiadd(mainmenu,flg) {
    pageload();
    $('#multiflg').val(flg);
    $('#salaryindex').attr('action', 'multiaddedit?mainmenu='+mainmenu+'&time='+datetime);
    $("#salaryindex").submit();
}
function gotosingleadd(id,name,mainmenu,flg) {
    pageload();
    $('#id').val(id);
    $('#empname').val(name);
    $('#editflg').val(flg);
    $('#salaryindex').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
    $("#salaryindex").submit();
}
function gotocopysingles(id,name,salary,mainmenu,flg,bankid) {
    pageload();
    $('#editflg').val(flg);
    $('#ids').val(id);
    $('#empname').val(name);
    $('#salary').val(salary);
    $('#bankid').val(bankid);
    $('#salaryviewlist').attr('action', 'copy?mainmenu='+mainmenu+'&time='+datetime);
    $("#salaryviewlist").submit();
}
function gotocopysingless(id,name,salary,mainmenu,flg,empno,bankid) {
    pageload();
    $('#editflg').val(flg);
    $('#ids').val(id);
    $('#id').val(empno);
    $('#empname').val(name);
    $('#salary').val(salary);
    $('#bankid').val(bankid);
    $('#salaryindex').attr('action', 'copy?mainmenu='+mainmenu+'&time='+datetime);
    $("#salaryindex").submit();
}
function gotoeditpage(id,salary,mainmenu,flg) {
	pageload();
	$('#editflg').val(flg);
	$('#ids').val(id);
	$('#salary').val(salary);
	$('#salaryview').attr('action', 'edit?mainmenu='+mainmenu+'&time='+datetime);
	$("#salaryview").submit();
}
function goviewtoindex(mainmenu) {
	pageload();
	$('#selYear').val();
	$('#selMonth').val();
	$('#salaryviewlist').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#salaryviewlist").submit();
}
function gosingletoindex(mainmenu) {
	pageload();
	if ($('#gobackflg').val() == "1") {
		$('#salaryview').attr('action', 'Viewlist?mainmenu='+mainmenu+'&time='+datetime);
		$("#salaryview").submit();
	} else{
		$('#salaryview').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#salaryview").submit();
	}
}
function getdate() {
	$('#txt_startdate').val(dates);
}
function getselectedText() {
    var mainst = $('#bank').val();
    $('#bankaccno').val(mainst);
}
function fndisablecharge(id) {
    if (id == "999") {
        $("#salaryhdn").attr("style", "margin-bottom:10px");
        $("#divcharge").attr("style", "display:none");
        $('#charge').val('');
    } else {
        $("#salaryhdn").attr("style", "");
        $("#divcharge").attr("style", "display:inline-block")
    }
}
function fndisablechargefield(cnt) {
    var id=$('#bank').val();
    var cnt=$('#fileCnt').val();
    if (id == "999") {
        for (var i = 0; i < cnt; i++) {
            $("#charge"+i).attr("disabled" , "disabled");
        }
    } else {
        for (var i = 0; i < cnt; i++) {
            $("#charge"+i).removeAttr("disabled");
        }
    }
}
function gotoindexpage(viewflg,mainmenu) {
    var back =$('#editflg').val();
    if (cancel_check == false) {
        if (!confirm("Do You Want To Cancel the Page?")) {
            return false;
        }
    }
    if (back == "1" || back == "3") {
        pageload();
        $('#salaryaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
        $("#salaryaddeditcancel").submit();
    }else if (viewflg == "1") {
        pageload();
        $('#salaryaddeditcancel').attr('action', 'Singleview?mainmenu='+mainmenu+'&time='+datetime);
        $("#salaryaddeditcancel").submit();
    } else {
        pageload();
        $('#salaryaddeditcancel').attr('action', 'Viewlist?mainmenu='+mainmenu+'&time='+datetime);
        $("#salaryaddeditcancel").submit();
    }
}
function gotoindexmultipage(viewflg,mainmenu) {
    if (cancel_check == false) {
        if (!confirm("Do You Want To Cancel the Page?")) {
            return false;
        }
    }
    if (viewflg == "1") {
      pageload();
        $('#salarymultiaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
        $("#salarymultiaddeditcancel").submit();
    } else {
      pageload();
        $('#salarymultiaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
        $("#salarymultiaddeditcancel").submit();
    }
}
function clearsearch() {
    $('#plimit').val(50);
    $('#page').val('');
    $("#filter").val('');
    $('#sorting').val('');
    $('#id').val('');
    $('#empname').val('');
    $('#salary').val('');
    $('#salarysort').val('id');
    $('#salaryindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
    $("#salaryindex").submit();
}
function fngetpreviousdetails(i,month,year,mainmenu) {
    var empNo = "";
    if (i!= "") {
        empNo = "empNo_"+i;
    } else {
        empNo = "empId";
    }
    var empid = document.getElementById(empNo).value;
    $.ajax({
        type:"GET",
        dataType: "json",
        url: 'copycheck',
        data: {
            empid: empid,
            month: month,
            year: year,
            mainmenu: mainmenu
        },
        success: function(data){ // What to do if we succeed
            split_data =data.split('-');
            $("#salary"+i).val(split_data[0]);
            $("#charge"+i).val(split_data[1]);
        },
        error: function(xhr, textStatus, errorThrown){
            alert(xhr.status);
            alert('there was a problem checking the fields');
        }  
    })
}