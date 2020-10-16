$(document).ready(function() {
    // initialize tooltipster on text input elements
    // initialize validate plugin on the form
    $('.addeditprocess').click(function () {
        $("#loanaddedit").validate({
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
                bankname: {required: true},
                loantype: {required: true},
                loanname: {required: true,specialChar: true},
                amount: {required: true,money: true},
                txt_startdate: {required: true,correctformatdate: true},
                loanperiod: {required: true},
                interest: {required: true},
                paymentday: {required: true,requiredWithgreaterthanday: true},
                currentbalance: {required: true,money: true},
                remainingmonths: {required: true},
                file1 : {extension: "pdf", filesize : (2 * 1024 * 1024)},
            },
            submitHandler: function(form) { // for demo
                if($('#editflg').val() == "1") {
                    // var confirmprocess = confirm("Do You Want To Register?");
                    if(confirm(err_confreg)) {
                        pageload();
                        return true;
                    } else {
                        return false
                    }
                } else {
                    // var confirmprocess = confirm("Do You Want To Update?");
                    if(confirm(err_confup)) {
                        pageload();
                        form.submit();
                        return true;
                    } else {
                        return false
                    }
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
    });
});
function underConstruction() { 
    alert("Under Construction");
}
function pageClick(pageval) {
    $('#page').val(pageval);
    if($('#id').val()!=""){
        $("#loanview").submit();
    }else if($('#id').val()==""){
        $("#loanindex").submit();
    }
}
function pageLimitClick(pagelimitval) {
    $('#page').val('');
    $('#plimit').val(pagelimitval);
    if($('#id').val()!=""){
        $("#loanview").submit();
    }else if($('#id').val()==""){
        $("#loanindex").submit();
    }
}
function goToSingleview(id,mainmenu,header) {
    pageload();
    $('#id').val(id);
    $('#head').val(header);
    $('#loanindex').attr('action', 'Viewlist'+'?mainmenu='+mainmenu+'&time='+datetime);
    $("#loanindex").submit();
}
function goToview(id,mainmenu) {
    pageload();
    $('#id').val(id);
    $('#loanindex').attr('action', 'Singleview'+'?mainmenu='+mainmenu+'&time='+datetime);
    $("#loanindex").submit();
}
function fnloanconfirm(chkid,flg,mainmenu) {
    pageload();
    if( flg == 1 ) {
        $('#loan_confirm').val('0');
    } else {
        $('#loan_confirm').val('1');
    }
    $('#loansingleview').attr('action', 'Loanconfirm'+'?mainmenu='+mainmenu+'&time='+datetime);
    $("#loansingleview").submit();
}
function goindexloanpage(mainmenu) {
    pageload();
    $('#loansingleview').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
    $("#loansingleview").submit();
}
function gobacktoindex(mainmenu) {
    pageload();
    $('#loanview').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
    $("#loanview").submit();
}
function gotoadd(mainmenu,flg) {
    pageload();
    $('#editflg').val(flg);
    $('#loanindex').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
    $("#loanindex").submit();
}
function goToedit(id,mainmenu,flg) {
    pageload();
    $('#editflg').val(flg);
    $('#loansingleview').attr('action', 'edit?mainmenu='+mainmenu+'&time='+datetime);
    $("#loansingleview").submit();
}
function download(file,path) {
    // var confirm_download = "Do You Want To Download?";
    if(confirm(err_download)) {
        window.location.href="../app/Http/Common/downloadfile.php?file="+file+"&path="+path+"/";
    }
}
function gotoindexpage(viewflg,mainmenu) {
    if (cancel_check == false) {
        if (!confirm("Do You Want To Cancel the Page?")) {
            return false;
        }
    }
    if (viewflg == "1") {
      pageload();
        $('#loanaddeditcancel').attr('action', 'Singleview?mainmenu='+mainmenu+'&time='+datetime);
        $("#loanaddeditcancel").submit();
    } else {
      pageload();
        $('#loanaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
        $("#loanaddeditcancel").submit();
    }
}
function cal_endyearmn(){
    var txt_startdate = $('#txt_startdate').val();
    var loanperiod = $('#loanperiod').val();
    if( loanperiod != "") {
        $('#end_date').attr("style", "display:inline-block");
    } else {
        $('#end_date').attr("style", "display:none");
    }
    /*var timestamp = Date.parse($("#loanperiod").val());
    if (isNaN(timestamp) == true) { 
        document.getElementById('loanperiod').value = "";
        document.getElementById('txt_month').value = "";
        document.getElementById('txt_end_date').value = "";
        return false;
    }*/
    var split_yr_mn_day = txt_startdate.split("-");
    var year = Number(split_yr_mn_day[0])+Number(loanperiod);
    var month = split_yr_mn_day[1];
    var now = new Date(year,split_yr_mn_day[1],split_yr_mn_day[2]);
    var first_day = new Date(now.getFullYear(), now.getMonth(), 1);
    var last_day  = new Date(now.getFullYear(), now.getMonth(), 0);
    var day = "";
    var end_date = "";
    if( last_day.getDate() >= split_yr_mn_day[2] ) {
        day = split_yr_mn_day[2];
    } else {
        day = last_day.getDate();
    }
    end_date = year + '-' + month + '-' + day;
    if( txt_startdate != "" ) {
        $('#txt_end_date').val(end_date);
        $('#end_dates').val(end_date);
    }
}
function numberonly(e) {
  e=(window.event) ? event : e;
  return (/[0-9]/.test(String.fromCharCode(e.keyCode))); 
}
function onlyNum() {
    var $this = $('#amount'); 
    $this.val($this.val().replace(/[^a-zA-Z0-9 ]/g, ''));
}
function removecheckvalue(flg) {
    var copyAmount = document.getElementById('check');
    if (flg == 1 && copyAmount.checked == true) {
        var cur_balance = document.getElementById("currentbalance");
        var remain_mon = document.getElementById("remainingmonths");
        cur_balance.value = "";
        remain_mon.value = "";
    }
    copyAmount.checked = false;
}
function cal_month(id){
    var loanperiod = $('#'+id).val();
    if( loanperiod.trim() != "" ) {
        loanperiod = loanperiod * 12;
    }
    $('#txt_month').val(loanperiod);
    removecheckvalue(1);
}
function fnCopyAmount(copyAmount){
    var copyAmount = document.getElementById('check');
    var amount = $('#amount').val();
    var loanmonth = $('#txt_month').val();
    if ( copyAmount.checked == true ) {
        $('#currentbalance').val(amount);
        $('#remainingmonths').val(loanmonth);
    } else {
        $('#currentbalance').val('');
        $('#remainingmonths').val('');
    }
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
function gototransferpage() {
    pageload();
    var mainmenu = "company_transfer";
    $('#editflg').val('1');
    $('#loandetail').val('5');
    $('#mainmenu').val(mainmenu);
    $('#loansingleview').attr('action', '../Transfer/loanedit?mainmenu='+mainmenu+'&time='+datetime);
    $("#loansingleview").submit();
}