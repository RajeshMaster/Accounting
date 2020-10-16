function pageClick(pageval) {
	// alert(pageval);
    $('#page').val(pageval);
    var mainmenu= $('#mainmenu').val();
    $('#staffslyfrm').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
    $("#staffslyfrm").submit();
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
    var mainmenu= $('#mainmenu').val();
    $('#staffslyfrm').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
    $("#staffslyfrm").submit();
}
function underconstruction(){
    alert("Under Construction");
}
function index() {
     var mainmenu= $('#mainmenu').val();
     $('#salaryview').attr('action','index?mainmenu='+mainmenu+'&time='+datetime);
     $("#salaryview").submit();
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
        $('#staffslyfrm').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
        $("#staffslyfrm").submit();
    }
}
function view(mainmenu,id,lastname) {
    pageload();
    $('#viewid').val(id);
    $('#lastname').val(lastname);
    $('#staffslyfrm').attr('action', 'salaryview?mainmenu='+mainmenu+'&time='+datetime);
    $("#staffslyfrm").submit();
}
function gotosalaryview(Emp_ID,mainmenu,LastName,DOJ) {
    $("#empid").val(Emp_ID);
    $("#empname").val(LastName);
    $("#DOJ").val(DOJ);
    $('#staffslyfrm').attr('action', 'viewsalary'+'?mainmenu='+mainmenu+'&time='+datetime); 
    $('#staffslyfrm').submit();
}
function salary() {
     var mainmenu="staff";
    $('#salarydetailfrm').attr('action', 'singleview?mainmenu='+mainmenu+'&time='+datetime);
    $("#salarydetailfrm").submit();
 }
function goindexpage(mainmenu) {
    var back = $('#hdnback').val();
    if (back == '1') {
    $('#staffslyviewfrm').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
    $("#staffslyviewfrm").submit();
    }
    else if(back == ""){
    $('#staffslyviewfrm').attr('action', '../Staff/index?mainmenu='+mainmenu+'&time='+datetime);
    $("#staffslyviewfrm").submit();
    }
}
Number.prototype.format = function(n, x) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
};
var data = {};
function fnviewbyajax (empdate, empid) {
    var mainmenu = $("#mainmenu").val();
    var count = $("#mainCount").val();
    $.ajax({
        type: 'GET',
        // dataType: "JSON",
        url: 'salarystaff_ajax',
        data: {
                "empdate": empdate,
                "empid": empid,
                "mainmenu": mainmenu,

            },
        success: function(resp){ // What to do if we succeed
            $("#empdate").text(empdate);
            $("#empid").text(empid);
            var responseval = resp;
            var responseval1 = responseval.split('^');
            var split_dollar1 = responseval1[0].split('$$');
            var split_dollar2 = responseval1[1].split('##');
            var totalval=0;
            if (split_dollar1=="") {
                for (var i = 0; i<count; i++) {
                    split_dollar1[i]=0;
                }
            }
            for (var i = 0; i<count; i++) {
                var j=i+1;
                var mt = "MainTotal"+j;
                if (split_dollar1[i] != "") {
                    var subval = split_dollar1[i];
                } else if (split_dollar1[i] == "") {
                    var subval = 0;
                } else {
                    var subval = 0;
                }
                document.getElementById(mt).innerHTML=parseFloat(subval).format();
                totalval += parseFloat(subval);
                totall = totalval.toLocaleString();
            }
            document.getElementById('total').innerHTML=totall;
            document.getElementById('empamt').innerHTML=split_dollar2[0];
            document.getElementById('empotamt').innerHTML=split_dollar2[1];
            result = split_dollar2[0].replace(",", "");
            result1 = result.replace(",", "");
            result2 = split_dollar2[1].replace(",", "");
            result3 = result2.replace(",", "");
            total = parseInt(result1) + parseInt(result3); 
            totals = total.toLocaleString();
            $('#totalamt').text(totals);
            $("#styleSelector").css("display","block");
            $("#styleSelector").addClass("open");
        },
        error: function(xhr, textStatus, errorThrown){
            alert(data.status);
        }
    })
}
