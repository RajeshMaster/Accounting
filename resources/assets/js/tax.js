function fngetid(mainmenu){
    var removed="";
    var selected="";
    $.each($('.fromHere input[type="checkbox"]'), function(){     
            if (this.checked) {
                selected += $(this).val()+",";
            } else {
                removed += $(this).val()+",";
            }     
        });
    selected = selected.replace(/,(\s+)?$/, '');
    removed = removed.replace(/,(\s+)?$/, '');
    var Emp_selection = "Do You Want To Select ?";
    if(confirm(Emp_selection)) {
        $('#removed').val(removed);
        $('#selected').val(selected);
        $('#hdnflg').val('1');
        pageload();
        $('#taxviewform').attr('action', 'familyselectionprocess?mainmenu='+mainmenu+'&time='+datetime);
        $("#taxviewform").submit();
        return true;
    }
}
$(document).ready(function() {
    // $( ".checkSingle" ).trigger( "click" );
    var checkedLength = $('.fromHere input[type="checkbox"]:checked').length;
    var totalLength = $('.fromHere input[type="checkbox"]').length;
    if (checkedLength == totalLength) {
        $("#checkedAll").prop( "checked", true );
    }
    $("#checkedAll").change(function() {
        if (this.checked) {
            $(".checkSingle").each(function() {
                this.checked=true;
            });
        } else {
            $(".checkSingle").each(function() {
                this.checked=false;
            });
        }
    });

    $(".checkSingle").click(function () {
        if ($(this).is(":checked")) {
            var isAllChecked = 0;

            $(".checkSingle").each(function() {
                if (!this.checked)
                    isAllChecked = 1;
            });

            if (isAllChecked == 0) {
                $("#checkedAll").prop("checked", true);
            }     
        }
        else {
            $("#checkedAll").prop("checked", false);
        }
    });
});
function fnTaxDetailsImport(mainmenu) {
	$('#importpopup').load('taximportpopup?mainmenu='+mainmenu+'&time='+datetime);
	$("#importpopup").modal({
           backdrop: 'static',
            keyboard: false
        });
    $('#importpopup').modal('show');
}
function underconstruction() {
	alert("Under Construction");
}
function pageClick(pageval) {
	$('#page').val(pageval);
	$("#taxdetailsform").submit();
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$("#taxdetailsform").submit();
}
function fnTaxExcelDownload(empid,empname,checkval,imgid,downloadflg) {
	$('#empid').val(empid);
    $('#empname').val(empname);
    $('#checkflg').val(checkval);
    var afterexcel=document.getElementById('afterexcel').value;
    if(confirm("Do You Want Download The Excel")) {
        if (downloadflg != 1) {
            document.getElementById(imgid).src =afterexcel;
        }
        $('#taxdetailsform').attr('action', 'taxPersonalDownload'+'?mainmenu='+mainmenu+'&time='+datetime);
    	$("#taxdetailsform").submit();
    }    
}
function fnViewPage(Emp_ID) {
    pageload();
    $('#empid').val(Emp_ID);
    $('#taxdetailsform').attr('action', 'taxview'+'?mainmenu='+mainmenu+'&time='+datetime);
    $("#taxdetailsform").submit();
}
function goindexpage(mainmenu) {
    pageload();
    $('#taxviewform').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
    $("#taxviewform").submit();
}
function showempselectionpopup(){
    $('#empselectionpopup').load('empselectionpopup?mainmenu='+mainmenu);
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
            document.empselectionform.submit();
            return true;
        } else {
            return false;
        }
    }
}