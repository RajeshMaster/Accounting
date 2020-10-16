$(document).ready(function() {
        var cc = 0;
        $('#billsort').click(function () {
                cc++;
                if (cc == 2) {
                    $(this).change();
                    cc = 0;
                }         
        }).change (function () {
             sortingfun();
                cc = -1;
        }); 
}); 
function sortingfun() {
    pageload();
    $('#plimit').val(50);
    $('#page').val('');
    var sortselect=$('#billsort').val();
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
    $("#billingfrm").submit();
}
function pageClick(pageval) {
    $('#page').val(pageval);
    var scrname = $('#scrname').val();
    if(scrname == "billingindex"){
        $("#billingfrm").submit();
    } else if(scrname == "billinghistory"){
        $("#billhistoryfrm").submit();
    }
}
function pageLimitClick(pagelimitval) {
    $('#page').val('');
    $('#plimit').val(pagelimitval);
    var scrname = $('#scrname').val();
    if(scrname == "billingindex"){
        $("#billingfrm").submit();
    } else if(scrname == "billinghistory"){
        $("#billhistoryfrm").submit();
    }
}
function underconstruction(){
    alert("UnderConstruction");
}
function fnCancel_check() {
      cancel_check = false;
      return cancel_check;
  }
function getData(month, year, flg, prevcnt, nextcnt, account_period, lastyear, currentyear, account_val) {
    var yearmonth = year + "-" +  ("0" + month).substr(-2);
    if ((prevcnt == 0) && (flg == 0) && (parseInt(month) < account_period) && (year == lastyear)) {
        alert(err_no_previous_record);
    } else if ((nextcnt == 0) && (flg == 0) && (parseInt(month) > account_period) && (year == currentyear)) {
        alert(err_no_next_record);
    } else {
        if (flg == 1) {
            document.getElementById('previou_next_year').value = year + "-" +  ("0" + month).substr(-2);
        }
        document.getElementById("pageclick").value = "";
        document.getElementById("plimit").value = "";
        document.getElementById("page").value = "";
        document.getElementById('selMonth').value = month;
        document.getElementById('selYear').value = year;
        document.getElementById('prevcnt').value = prevcnt;
        document.getElementById('nextcnt').value = nextcnt;
        document.getElementById('account_val').value = account_val;
        document.billingfrm.submit();
    }
}
function staffselectpopup() {
    var mainmenu = $('#mainmenu').val();
    var year = $('#selYear').val();
    var month = $('#selMonth').val();
    popupopenclose(1);
    $('#staffselectpopup').load('../Billing/staffselectpopup?mainmenu='+mainmenu+'&year='+year+'&month='+month);
    $("#staffselectpopup").modal({
           backdrop: 'static',
            keyboard: false
        });
    $('#staffselectpopup').modal('show');
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
function selectpreviousdetails() {
    $('#selYear').val();
    $('#selMonth').val();
    var mainmenu="staff";
    var confirmmsg = "Do You Want To Get The Previous Details?";
    if (confirm(confirmmsg)) {
        pageload();
         $('#billingfrm').attr('action', 'getpreviousdetails?mainmenu='+mainmenu+'&time='+datetime);
         $("#billingfrm").submit();
    } 
}
function billHistory(empid,empname){
    pageload();
    $('#empid').val(empid);
    $('#empname').val(empname);
    var mainmenu = $('#mainmenu').val();
    $('#billingfrm').attr('action', 'billhistory?mainmenu='+mainmenu+'&time='+datetime);
    $("#billingfrm").submit();
}
function billviewreg(empid,empname){
    pageload();
    $('#empid').val(empid);
    $('#empname').val(empname);
    var mainmenu = $('#mainmenu').val();
    $('#billingfrm').attr('action', 'billdetailview?mainmenu='+mainmenu+'&time='+datetime);
    $("#billingfrm").submit();
}
function goindexpage(mainmenu,billinghdnback) {
    var scrname = $('#scrname').val();
    var back = $('#hdnback').val();
    pageload();
    if (back == '1') {
        $('#billhistoryfrm').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
        $("#billhistoryfrm").submit();
    }else if(billinghdnback == 1){
        $('#billhistoryfrm').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
        $("#billhistoryfrm").submit();
    } else if (scrname == 'detailview') {
        $('#billdetailfrm').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
        $("#billdetailfrm").submit();
    } else if (scrname == 'register') {
        $('#billaddeditfrm').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
        $("#billaddeditfrm").submit();
    } else if (scrname == 'edit' || scrname == 'copy') {
        $('#billaddeditfrm').attr('action', 'billdetailview?mainmenu='+mainmenu+'&time='+datetime);
        $("#billaddeditfrm").submit();
    } else if(back == ""){
        $('#billhistoryfrm').attr('action', '../Staff/index?mainmenu=staff&time='+datetime);
        $('#mainmenu').val("staff");
        $("#billhistoryfrm").submit();     
    }
}
function Addnewbillingdetails(empid,cusname,totalhrs,nickname,lastname,startdate,cusid,branch,branchid,branch_name,srtby,Amount,newrec) {
    pageload();
    var mainmenu = $('#mainmenu').val();
    if(totalhrs == "" && Amount == ""){
        $('#newrec').val(newrec);
        $('#scrname').val("register");
        $('#addbillregflg').val(1);
        $('#hdnbranchname').val(branch_name);
        $('#hdnempid').val(empid);
        $('#hdnnickname').val(nickname);
        $('#hdnlastname').val(lastname);
        $('#hdncustname').val(cusname);
        $('#hdnstartdate').val(startdate);
        $('#hdncusid').val(cusid);
        $('#sorting').val(srtby);
        $('#billingfrm').attr('action', 'billingregister?mainmenu='+mainmenu+'&time='+datetime);
        $("#billingfrm").submit();
    } else {    
        if (newrec == 1) {
            $('#newrec').val(newrec);
            $('#scrname').val("edit");
            $('#hdnempid').val(empid);
            $('#hdnnickname').val(nickname);
            $('#billingfrm').attr('action', 'billingregister?mainmenu='+mainmenu+'&time='+datetime);
            $("#billingfrm").submit();
        } else {
            $('#scrname').val("view");
            $('#addbillregflg').val(1);
            $('#hdnnickname').val(nickname);
            $('#hdnbranchname').val(branch_name);
            $('#hdnempid').val(empid);
            $('#hdnnickname').val(nickname);
            $('#hdncustname').val(cusname);
            $('#hdnstartdate').val(startdate);
            $('#hdncusid').val(cusid);
            $('#sorting').val(srtby);
            $('#billingfrm').attr('action', 'billdetailview?mainmenu='+mainmenu+'&time='+datetime);
            $("#billingfrm").submit();
        }
    }
}
function fnCalcDone(chkid,id,empid,branchid,startdate){
    if (chkid.checked) {
        $('#upcheckval').val(1);
    } else {
        $('#upcheckval').val(2);
    }
    $('#hdnempidchk').val(empid);
    $('#editbillregidchk').val(id);
    $("#billdetailfrm").submit();
}
$(document).ready(function() {
    // initialize tooltipster on text input elements
    // initialize validate plugin on the form
    $('.addeditprocess').click(function () {
        $("#billaddeditfrm").validate({
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
                clientname: {required: true},
                branchname: {required: true},
                amount: {required: true},
                time_start: {required: true},
                time_end: {required: true},
                ot_start: {required: true},
                ot_end: {required: true},
                timerange: {required: true},
                otamount: {required: true},
            },
            submitHandler: function(form) {
                if($('#scrname').val() == "edit") {
                    var confirmprocess = confirm(err_confup);
                } else {
                    var confirmprocess = confirm(err_confreg);
                }
                 if(confirmprocess) {
                    pageload();
                    $('#otamount').attr('disabled', false);
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
function gotoindexpage(viewflg,mainmenu,datetime) {
    if (cancel_check == false) {
    if (!confirm(cancel_msg)) {
      return false;
    }
  }
    if (viewflg == "1") {
        $('#frmbillingaddeditcancel').attr('action', 'billdetailview?mainmenu='+mainmenu+'&time='+datetime);
        $("#frmbillingaddeditcancel").submit();
    } else {
        $('#frmbillingaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
        $("#frmbillingaddeditcancel").submit();
    }
}
function Editbillingdetails(id,str,nickname,empid,startdate) {
    var mainmenu = $('#mainmenu').val();
    if (str=="copy") {
        $('#scrname').val("copy");
     } else {
        $('#scrname').val("edit");
     }
    $('#hdnempid').val(empid);
    $('#hdnnickname').val(nickname);
    $('#startdate').val(startdate);
    $('#billdetailfrm').attr('action', 'billingregister?mainmenu='+mainmenu+'&time='+datetime);
    $("#billdetailfrm").submit();
}
function fnGetcustomerDetail(value) {
    var clientname = $('#clientname').val();
    var mainmenu = $('#mainmenu').val();
    $('#branchname').find('option').not(':first').remove();
    $.ajax({
        type:"GET",
        dataType: "JSON",
        url: 'ajaxbranchname',
        data: {
            clientname: clientname,
            mainmenu : mainmenu
        },
        success: function(data){ // What to do if we succeed
            for (i = 0; i < data.length; i++)
            { 
             $('#branchname').append( '<option value="'+data[i]['branchname_value']+'">'+data[i]['branchname_text']+'</option>' );
             $('select[name="branchname"]').val(value);
            }
        },
        error: function(data){
            alert(data.status);
            alert('there was a problem checking the fields');
        }  
    })
}
function fnCheckboxVal(str) {
    var Chkclick = document.getElementById('chkval'); 
    document.getElementById('mandatory_hidden').style.visibility='visible';  
    var Amount = $('#amount').val().trim().replace(/[, ]+/g, "");
    var TMrang1 = $('#time_start').val();
    var TMrang2 = $('#time_end').val();
    var OTamt1 = $('#ot_start').val();
    var OTamt2 = $('#ot_end').val();
    var calOTamt1 = "";
    var calOTamt2 = "";
    if((!isEmpty(Amount) || (Amount == "0")) && !isEmpty(TMrang1) && !isEmpty(TMrang2)) {
        if (Chkclick.checked) {
        calOTamt1 = Amount / TMrang1;
        calOTamt2 = Amount / TMrang2;
        document.getElementById('ot_start').value = "-" + Math.round(calOTamt1).toLocaleString();
        document.getElementById('ot_end').value = Math.round(calOTamt2).toLocaleString();
        document.getElementById('lblmMinAmt').value = "-" + Math.round(calOTamt1).toLocaleString();
        document.getElementById('lblmMaxAmt').value = Math.round(calOTamt2).toLocaleString();
        document.getElementById('ot_start').style.border="1px solid white";
        document.getElementById('mandatory_hidden').style.visibility='hidden';
        document.getElementById('ot_start').style.background="white";
        document.getElementById('ot_start').style.boxShadow = "none";
        document.getElementById('ot_start').style.textAlign="right";
        document.getElementById("ot_start").readOnly =true;
        document.getElementById('ot_end').style.border="1px solid white";
        document.getElementById('ot_end').style.background="white";
        document.getElementById('ot_end').style.boxShadow="none";
        document.getElementById('ot_end').style.textAlign="right";
        document.getElementById("ot_end").readOnly = true;
        } else {
        document.getElementById('mandatory_hidden').style.visibility='visible';
        document.getElementById('ot_start').style.border="1px solid lightgray";
        document.getElementById('ot_start').style.background="white";
        document.getElementById("ot_start").readOnly = false;
        document.getElementById('ot_end').style.border="1px solid lightgray";
        document.getElementById('ot_end').style.background="white";
        document.getElementById("ot_end").readOnly = false;
        }
    } else {
        if(isEmpty(Amount) || (Amount == "0")) {
            alert("Please Enter The Amount");
            $('#amount').focus();
            $('#amount').select();
            return false;   
        } else if(!isNumeric(Amount.trim().replace(/[, ]+/g, ""))) {
            alert("Please Enter Number Only");
            $('#amount').focus();
            $('#amount').select();
            return false;
        } else if(isEmpty(TMrang1)) {
            alert("Please Enter The Time Range");
            $('#time_start').focus();
            $('#time_start').select();
            return false;   
        } else if(!isNumeric(TMrang1.trim().replace(/[, ]+/g, ""))) {
            alert("Please Enter Number Only");
            $('#time_start').focus();
            $('#time_start').select();
            return false;
        } else if(isEmpty(TMrang2)) {
            alert("Please Enter The Time Range");
            $('#time_end').focus();
            $('#time_end').select();
            return false;   
        } else if(!isNumeric(TMrang2.trim().replace(/[, ]+/g, ""))) {
            alert("Please Enter Number Only");
            $('#time_end').focus();
            $('#time_end').select();
            return false;
        } 
    }
}
function fnTotalCalc() {
    var Chkclick = document.getElementById('chkval'); 
    var Amount = $('#amount').val().trim().replace(/[, ]+/g, "");
    var txt_otamount= $('#otamount').val().trim().replace(/[, ]+/g, "");
    var lblMinHrs = $('#time_start').val();
    var lblMaxHrs = $('#time_end').val();
    var lblMinAmt = $('#ot_start').val();
    var lblMaxAmt = $('#ot_end').val();
    var calOTamt1 = "";
    var calOTamt2 = "";
    var txt_TMrang = $('#timerange').val().trim().replace(/[, ]+/g, "");
    var Chkclick = document.getElementById('chkvalMB');
    var lblBillingAmt = document.getElementById("lblBillingAmt");
    var txt_otamount1 = document.getElementById("otamount");
    var calOTamt = "";
    var billAmount = "";
    if(txt_TMrang != "") {
        if (Chkclick.checked) {
            if(parseFloat(txt_TMrang) < parseFloat(lblMinHrs)) {
                calOTamt = (Math.abs(lblMinHrs - txt_TMrang) * lblMinAmt.trim().replace(/[, ]+/g, ""));
                document.getElementById("otamount").value = Math.round(calOTamt).toLocaleString();
                billAmount = Amount.trim().replace(/[, ]+/g, "") - (-calOTamt);
                lblBillingAmt.innerHTML = Math.round(billAmount).toLocaleString();
                document.getElementById("hdn_lblBillingAmt").value = lblBillingAmt.innerHTML;
                document.getElementById('otamount').disabled = true;
                document.getElementById("lblBillingAmt").style.display = 'block';
            } else if(parseFloat(txt_TMrang) > parseFloat(lblMaxHrs)) {
                calOTamt = (Math.abs(lblMaxHrs - txt_TMrang) * lblMaxAmt.trim().replace(/[, ]+/g, ""));
                document.getElementById("otamount").value = Math.round(calOTamt).toLocaleString();
                billAmount = Amount.trim().replace(/[, ]+/g, "") - (-calOTamt);
                lblBillingAmt.innerHTML = Math.round(billAmount).toLocaleString();
                document.getElementById("hdn_lblBillingAmt").value = lblBillingAmt.innerHTML;
                document.getElementById('otamount').disabled = true;
            } else {
                document.getElementById("otamount").value = 0;
                txt_otamount1.innerHTML=txt_otamount.trim().replace(/[, ]+/g, "");
                document.getElementById("hdn_otamount").value =  txt_otamount1.innerHTML;
                billAmount = Amount.trim().replace(/[, ]+/g, "") - (-calOTamt);
                lblBillingAmt.innerHTML = Math.round(billAmount).toLocaleString();
                document.getElementById("hdn_lblBillingAmt").value = lblBillingAmt.innerHTML;
            }
        } else {
            document.getElementById('otamount').disabled = false;
            document.getElementById("hdn_lblBillingAmt").value = lblBillingAmt.innerHTML;
        }
    } else {
        if(isEmpty(txt_TMrang)) {
            alert("Please Enter The Time Range");
            $('#timerange').focus();
            $('#timerange').select();
            return false;   
        } else if(!isNumeric(txt_TMrang.trim().replace(/[, ]+/g, ""))) {
            alert("Please Enter Number Only");
            $('#timerange').focus();
            $('#timerange').select();
            return false;
        }  
    }
}
function fngetworkinghrs(mn,yr,empid) {
    var Chkclick = document.getElementById('chkvalTS');
    if ( document.getElementById('scrname').value == "copy" ) {
        var selectyear = document.getElementById('selYear').value;
        var selectmonth = document.getElementById('selMonth').value;
        if(selectyear == " "){
            alert(Please_Select_Anyone_Year);
            document.getElementById('selYear').focus();
            Chkclick.checked = "";
            return false;
        }else if(selectmonth == " "){
            alert(Please_Select_Anyone_Month);
            document.getElementById('selMonth').focus();
            Chkclick.checked = "";
            return false;
        } else {
            yr = selectyear;
            mn= selectmonth;
        }
     }   
    if ( document.getElementById('scrname').value == "edit" ) {
        var selectyear = document.getElementById('selYear').value;
        var selectmonth = document.getElementById('selMonth').value;
        yr = selectyear;
        mn= selectmonth;
    }
    if (Chkclick.checked){
        document.getElementById('otamount').value = '';
         document.getElementById('timerange').value = 0;
        var Chkclick = document.getElementById('chkvalMB');
        Chkclick.checked = "";
        document.getElementById('otamount').disabled = false;
        document.getElementById('lblBillingAmt').innerHTML = "";
        var xmlhttp;
        if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        } else {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function() {
            if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                alert(responseval);
                var pos = responseval.indexOf(".");
                if ( pos != "-1" ) {
                    var res = responseval.split(".");
                    if( res[1] <= 15 ) {
                        res[1] = ".25";
                    } else if ( res[1] >= 15 && res[1] <= 30 ) {
                        res[1] = ".50";
                    } else if ( res[1] >= 30 && res[1] <= 45 ) {
                        res[1] = ".75";
                    } else if ( res[1] >= 45 && res[1] <= 60 ) {
                        res[0] = res[0]-(-1);
                        res[1] = "";
                    }
                    responseval = res[0]+res[1];
                }
                document.getElementById('timerange').value = responseval;
                if (responseval != 0) {
                    document.getElementById('timerange').style.border="0";
                    document.getElementById('timerange').style.background="#dff1f4";
                    document.getElementById('timerange').readOnly = true;
                    document.getElementById('timerange').style.textAlign="right";
                }
            }
        }
    } else {
        document.getElementById("otamount").value = "";
        var Chkclick = document.getElementById('chkvalMB');
        Chkclick.checked = "";
        document.getElementById('otamount').disabled = false;
        document.getElementById('lblBillingAmt').innerHTML = "";
        document.getElementById('timerange').style.border="1px solid lightgray";
        document.getElementById('timerange').style.background="white";
        document.getElementById('timerange').readOnly = false;
    } 
    }
    function calcBillAmount() {
        document.getElementById("hdn_otamount").value = document.getElementById("otamount").value;
        var Amount = document.getElementById("amount").value.trim().replace(/[, ]+/g, "");
        var txt_OTamt = document.getElementById("otamount").value.trim().replace(/[, ]+/g, "");
        var lblBillingAmt = document.getElementById("lblBillingAmt");
        var billAmount = "";
        billAmount = Amount - (-txt_OTamt);
        lblBillingAmt.innerHTML = Math.round(billAmount).toLocaleString();
        document.getElementById("hdn_lblBillingAmt").value = lblBillingAmt.innerHTML;
    }
    function chgamountBD() {
        document.getElementById("ot_start").value = "";
        document.getElementById("ot_end").value = "";
        var Chkclick = document.getElementById('chkval');
        Chkclick.checked = "";
        document.getElementById('ot_start').style.border="1px solid lightgray";
        document.getElementById('ot_start').style.background="white";
        document.getElementById("ot_start").readOnly = false;
        document.getElementById('ot_end').style.border="1px solid lightgray";
        document.getElementById('ot_end').style.background="white";
        document.getElementById("ot_end").readOnly = false;
        document.getElementById("otamount").value = "";
        var Chkclick = document.getElementById('chkvalMB');
        Chkclick.checked = "";
        var Chkclick = document.getElementById('chkvalTS');
        Chkclick.checked = "";
        document.getElementById("lblBillingAmt").innerHTML = "";
        document.getElementById('timerange').style.border="1px solid lightgray";
        document.getElementById('timerange').style.background="white";
        document.getElementById("timerange").readOnly = false;
        document.getElementById("timerange").value = "";
        document.getElementById("otamount").value = "";
        var Chkclick = document.getElementById('chkvalMB');
        Chkclick.checked = "";
        document.getElementById('otamount').disabled = false;
        document.getElementById("lblBillingAmt").innerHTML = "";
    }
    function chgtimerange() {
        document.getElementById("otamount").value = "";
        var Chkclick = document.getElementById('chkvalMB');
        var Chkclickts = document.getElementById('chkvalTS');
        Chkclick.checked = "";
        Chkclickts.checked = "";
        document.getElementById("lblBillingAmt").innerHTML = "";
    }