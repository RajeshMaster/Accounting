var data = {};
$(function () {
    var cc = 0;
    $('#timesheetviewsort').click(function () {
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
    var sortselect=$('#timesheetviewsort').val();
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
    $("#timesheetdetails").submit();
}
function staffpopup(mainmenu) {
	popupopenclose(1);
    $('#importstaffpopup').load('../Timesheet/importstaffpopup');
    $("#importstaffpopup").modal({
           backdrop: 'static',
            keyboard: false
        });
    $('#importstaffpopup').modal('show');
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
		document.getElementById('selMonth').value = month;
		document.getElementById('selYear').value = year;
		document.getElementById('prevcnt').value = prevcnt;
		document.getElementById('nextcnt').value = nextcnt;
		document.getElementById('account_val').value = account_val;
		/*document.getElementById('topclick').value = '1';*/
		document.timesheetfrm.submit();
	}
}
function viewTS_entry(empidval,flagval) {
	$('#empid').val(empidval);
    $('#flag').val(flagval);
	$('#timesheetfrm').attr('action', 'timesheetview?mainmenu='+mainmenu+'&time='+datetime);
	$("#timesheetfrm").submit();
}
function Byidview(empidval) {
    $('#pagenxt').val($('#page').val());
    $('#plimitnxt').val($('#plimit').val());
    $('#page').val('');
    $('#plimit').val('');
	$('#empid').val(empidval);
	$('#timesheetfrm').attr('action', 'timeSheetHistorydetails?mainmenu='+mainmenu+'&time='+datetime);
	$("#timesheetfrm").submit();
}
function goindexpage() {
    var hdnback=$('#hdnback').val();
	var mainmenu="staff";
    $('#page').val($('#pagenxt').val());
    $('#plimit').val($('#plimitnxt').val());
    if(hdnback == 1){
        $('#timesheetdetails').attr('action', 'timesheetindex?mainmenu='+mainmenu+'&time='+datetime);
        $("#timesheetdetails").submit();
    } else {
        $('#timesheetdetails').attr('action', '../Staff/index?mainmenu=staff&time='+datetime);
        $("#timesheetdetails").submit();
    }
   
}
function goindex(flag) {
    var mainmenu="staff";
    if (flag == 1) {
        $('#page').val($('#pagenxt').val());
        $('#plimit').val($('#plimitnxt').val());
        $('#timesheetview').attr('action', 'timesheetindex?mainmenu='+mainmenu+'&time='+datetime);
        $("#timesheetview").submit();
    } else {
        $('#timesheetview').attr('action', 'timeSheetHistorydetails?mainmenu='+mainmenu+'&time='+datetime);
        $("#timesheetview").submit();
    }
   
}
function pageClick(pageval) {
    $('#page').val(pageval);
    $("#timesheetdetails").submit();
}
function pageLimitClick(pagelimitval) {
    $('#page').val('');
    $('#plimit').val(pagelimitval);
    $("#timesheetdetails").submit();
}
function addeditreg(mainmenu,empid,flag) {
	$('#empid').val(empid);
	$('#flagval').val(flag);
	$('#timesheetdetails').attr('action', 'addeditreg?mainmenu='+mainmenu+'&time='+datetime);
	$("#timesheetdetails").submit();
}
function addeditupdate(mainmenu,empid,flag) {
	$('#empid').val(empid);
	$('#flagval').val(flag);
	$('#timesheetview').attr('action', 'addeditupdate?mainmenu='+mainmenu+'&time='+datetime);
	$("#timesheetview").submit();
}
function gotoview(mainmenu,empid,flag) {
    $('#empid').val(empid);
    $('#flagval').val(flag);
    $('#timesheetdetails').attr('action', 'timesheetview?mainmenu='+mainmenu+'&time='+datetime);
    $("#timesheetdetails").submit();
}
function gototimesheetview(empid,date,workyear,workmonth) {
    $('#empid').val(empid);
    $('#selYear').val(workyear);
    $('#selMonth').val(workmonth);
    $('#timesheetdetails').attr('action', 'timesheetview?mainmenu='+mainmenu+'&time='+datetime);
    $("#timesheetdetails").submit();
}

function specfy(filtervalue) {
	$('#filtervalue').val(filtervalue);
	$('#timesheetadd').attr('action', 'addeditupdate?mainmenu='+mainmenu+'&time='+datetime);
	$("#timesheetadd").submit();
}
function add_timesheet(empid,i,flagval) {
    var err_work_place = "Please Enter Work Place";
    var err_start_time = "Please Enter Start Time";
    var err_end_time = "Please Enter End Time";
    var enablechk=$("#enable"+i).val();
    var worktxt = $("#worktxt"+i).val();  
    var starttime = $("#start1"+i).val(); 
    var endtime = $("#end1"+i).val(); 
    var remarks= $("#remarks"+i).val();  
    var workingdate= $("#workingdate"+i).val();  
    var operation= $("#operation"+i).val();
    if (enablechk == "") {
        $("#worktxt"+i).attr("style", "display:inline-block");
        $("#labelworktxt"+i).attr("style", "display:none");
        $("#start1"+i).attr("style", "display:inline-block");
        $("#labelstart1"+i).attr("style", "display:none");
        $("#end1"+i).attr("style", "display:inline-block");
        $("#labelend1"+i).attr("style", "display:none");
        $("#remarks"+i).attr("style", "display:inline-block");
        $("#labelremarks"+i).attr("style", "display:none");
        $("#enable"+i).val('a');
    } else {
        if (worktxt=="") {
            alert(err_work_place+" "+i);
            $("#worktxt"+i).focus();
        } else if(starttime=="") {
            alert(err_start_time+" "+i);
            $("#start1"+i).focus();
        } else if(endtime=="") {
            alert(err_end_time+" "+i);
            $("#end1"+i).focus();
        } else {
            $('#timesheetsingleadd #seldate').val(i);
            $('#timesheetsingleadd #empid').val(empid);
            $('#timesheetsingleadd #sectionhdn').val($('#timesheetadd #classification'+i).val());
            $('#timesheetsingleadd #worktxthdn').val($('#timesheetadd #worktxt'+i).val());
            $('#timesheetsingleadd #start1hdn').val($('#timesheetadd #start1'+i).val());
            $('#timesheetsingleadd #end1hdn').val($('#timesheetadd #end1'+i).val());
            $('#timesheetsingleadd #start2hdn').val($('#timesheetadd #start2'+i).val());
            $('#timesheetsingleadd #end2hdn').val($('#timesheetadd #end2'+i).val());
            $('#timesheetsingleadd #remarkshdn').val($('#timesheetadd #remarks'+i).val());
            if (flagval == "1") {
                var confirmmsg = "Do You Want Register?";
                if (confirm(confirmmsg)) {
                $('#flagval').val(flag);
        	    $('#timesheetsingleadd').attr('action', 'singlerow1?mainmenu='+mainmenu+'&time='+datetime);
        	    $("#timesheetsingleadd").submit(); 
                }
            }  else {
                var confirmmsg = "Do You Want Update?";
                if (confirm(confirmmsg)) {
                $('#flagval').val(flag);
                $('#timesheetsingleadd').attr('action', 'singlerow1?mainmenu='+mainmenu+'&time='+datetime);
                $("#timesheetsingleadd").submit();
                }
            }
        }
    }
}
function isTimeColon(timeId,event){
    var event = event.keyCode;
    if(event!=8){
        var inputTime = document.getElementById(timeId).value; 
        var timeLength = inputTime.length;  
        var outTime;  
        var h = "";
        var m = "";
        var colon //":"一時領域
        if(timeLength == 1){
            h = inputTime.substr(0,1);
            if(h >= 0 && h < 3){
                outTime = h;
            }else if(h > 3 && h < 10){
                outTime = "0" + h + ":";
            }else{
                outTime = "";
            }
        }else if(timeLength == 2){
            h = inputTime.substr(0,2);
            if(h >= 0 && h < 24){
                outTime = h + ":";
            }else{
                outTime = "";
            }
        }else if(timeLength == 3){
            h = inputTime.substr(0,2);
            colon = inputTime.substr(2,1);
            if(h >= 0 && h < 24 && colon == ":"){
                outTime = h + ":";
            }else{
                outTime = "";
            }
        }else if(timeLength == 4){
            h = inputTime.substr(0,2);
            colon = inputTime.substr(2,1);
            m = inputTime.substr(3,1);
            if(h >= 0 && h < 24 && colon == ":"){
                //outTime = h + ":";
            }else{
                outTime = "";
            }
            if(m >= 0 && m < 6){
                outTime = h + ":" + m;
            }else if(m >= 6 && m < 10){
                outTime = h + ":" + "0" + m;
            }
        }else if(timeLength == 5){
            h = inputTime.substr(0,2);
            colon = inputTime.substr(2,1);
            m = inputTime.substr(3,2);
            if(h >= 0 && h < 24 && colon == ":"){
                //outTime = h + ":";
            }else{
                outTime = "";
            }
            if(m >= 0 && m < 60){
                outTime = h + ":" + m;
            }else{
                outTime = h + ":";
            }
        }else{
            outTime = "";
        }
        document.getElementById(timeId).value = outTime;
        return true;
    }
}
function fncancel() {
	var confirmmsg = "Do You Want To Cancel?";
    if (confirm(confirmmsg)) {
        pageload();
        var mainmenu="staff";
        $('#timesheetadd').attr('action', 'timeSheetHistorydetails?mainmenu='+mainmenu+'&time='+datetime);
        $("#timesheetadd").submit();
    } 
    return false;
}
function fncancelgoview() {
    var confirmmsg = "Do You Want To Cancel?";
    if (confirm(confirmmsg)) {
        pageload();
        var mainmenu="staff";
        $('#timesheetadd').attr('action', 'timesheetview?mainmenu='+mainmenu+'&time='+datetime);
        $("#timesheetadd").submit();
    } 
    return false;
    
}
// Update
function gotoView(empidval) {
	$('#empid').val(empidval);
	$('#timesheetfrm').attr('action', 'timesheetview?mainmenu='+mainmenu+'&time='+datetime);
	$("#timesheetfrm").submit();
}
function copy(i) {
    if(sessionStorage.getItem("previouslink")) {
    var prevlink=sessionStorage.getItem("previouslink");
    document.getElementById("timesheetcopy"+prevlink).style.color = "green";
    }
    var classification=$("#classification"+i).val();
    var worktxt=$("#worktxt"+i).val(); 
    var start1= $("#start1"+i).val(); 
    var end1=$("#end1"+i).val(); 
    var start2=$("#start2"+i).val(); 
    var end2=$("#end2"+i).val();
    var remarks=$("#remarks"+i).val(); 
    sessionStorage.setItem('classification', classification);
    sessionStorage.setItem('worktxt', worktxt);
    sessionStorage.setItem('start1', start1);
    sessionStorage.setItem('end1', end1);
    sessionStorage.setItem('start2', start2);
    sessionStorage.setItem('end2', end2);
    sessionStorage.setItem('remarks', remarks);
    sessionStorage.setItem('previouslink', i);
    alert("Copied Successfully and Choose field to Paste..");
    $("#timesheetcopy"+i).css("color","red");
}
function paste(i) {
    if((sessionStorage.getItem("classification")==undefined)&&(sessionStorage.getItem("worktxt")==undefined)&&(sessionStorage.getItem("start1")==undefined)&&(sessionStorage.getItem("end1")==undefined)
        &&(sessionStorage.getItem("start2")==undefined)&&(sessionStorage.getItem("end2")==undefined)&&(sessionStorage.getItem("remarks")==undefined)) {
        alert("Choose Copy and Then Paste it.");
    } else {
    document.forms['timesheetadd']["worktxt"+i].style.display="block";
    document.getElementById("labelworktxt"+i).style.display="none";
    document.forms['timesheetadd']["start1"+i].style.display="block";
    document.getElementById("labelstart1"+i).style.display="none";
    document.forms['timesheetadd']["end1"+i].style.display="block";
    document.getElementById("labelend1"+i).style.display="none";
    document.forms['timesheetadd']["remarks"+i].style.display="block";
    document.getElementById("labelremarks"+i).style.display="none";

    document.getElementById("classification"+i).value=sessionStorage.getItem("classification");
    document.getElementById("worktxt"+i).value=sessionStorage.getItem("worktxt");
    document.getElementById("start1"+i).value=sessionStorage.getItem("start1");
    document.getElementById("end1"+i).value=sessionStorage.getItem("end1");
    document.getElementById("start2"+i).value=sessionStorage.getItem("start2");
    document.getElementById("end2"+i).value=sessionStorage.getItem("end2");
    $("#remarks"+i).val(sessionStorage.getItem("remarks"));
    // document.getElementById("remarks"+i).value=sessionStorage.getItem("remarks");
    }
}
function resetdata(id) {
    chk="reset"+id;             
    if (document.getElementById(chk).checked == true) {
        for (var i = id-1; i >=1 ; i--) {
            var worktxt=document.getElementById("worktxt"+i).value;
            var start1=document.getElementById("start1"+i).value;
            var end1=document.getElementById("end1"+i).value;
            var start2=document.getElementById("start2"+i).value;
            var end2=document.getElementById("end2"+i).value;
            var remarks=document.getElementById("remarks"+i).value;
            if(worktxt!="" || start1!="" || end1!="")
            {
            document.getElementById("worktxt"+id).value=worktxt;
            document.getElementById("start1"+id).value=start1;
            document.getElementById("end1"+id).value=end1;
            document.getElementById("start2"+id).value=start2;
            document.getElementById("end2"+id).value=end2;
            document.getElementById("remarks"+id).value=remarks;
            return false;
            }
        }   
    } else{

            document.getElementById("worktxt"+id).value="";
            document.getElementById("start1"+id).value="";
            document.getElementById("end1"+id).value="";
            document.getElementById("start2"+id).value="";
            document.getElementById("end2"+id).value="";
            document.getElementById("remarks"+id).value="";

    }
}
function timesheetdownload(staff,downloadflg) {
    var workhour = $("actualTotal").val();
    var overtime = $("overTotal").val();
    var latetime = $("laTotal").val();
    var extratime = $("dutTotal").val();
    var mainmenu="staff";
    if (downloadflg == 1) {
        alert("Under Construction"); 
        // if (confirm("Do you want Upload Excel?")) {
        // $('#timesheetdetails #downloadflg').val(downloadflg);
        // $('#timesheetdetails').attr('action', 'downloadexcel?mainmenu='+"mainmenu"+'&workhour='+"workhour"+'&overtime='+"overtime"+'&latetime='+"latetime"+'&extratime='+"extratime"+'&time='+"time");
        // $("#timesheetdetails").submit();    
        // }
    } else if (downloadflg == 2) {
        alert("Under Construction");
        // if (confirm("Do you want download PDF?")) {
        // $('#timesheetdetails #downloadflg').val(downloadflg);
        // $('#timesheetdetails').attr('action', 'pdfview?mainmenu='+"mainmenu"+'&workhour='+"workhour"+'&overtime='+"overtime"+'&latetime='+"latetime"+'&extratime='+"extratime"+'&time='+"time");
        // $("#timesheetdetails").submit();
        // }  
    } else {

        if (confirm("Do you want download Excel?")) {
        $('#timesheetview #downloadflg').val(downloadflg);
        $('#timesheetview').attr('action', 'downloadexcel?mainmenu='+"mainmenu"+'&workhour='+"workhour"+'&overtime='+"overtime"+'&latetime='+"latetime"+'&extratime='+"extratime"+'&time='+"time");
        $("#timesheetview").submit();
        }
    }
}
function checkboxpaste(id) {   
    chk="paste"+id;             
    if (document.getElementById(chk).checked == true) {
        for (var i = id-1; i >=1 ; i--) {
            var worktxt=document.getElementById("worktxt"+i).value;
            var start1=document.getElementById("start1"+i).value;
            var end1=document.getElementById("end1"+i).value;
            var start2=document.getElementById("start2"+i).value;
            var end2=document.getElementById("end2"+i).value;
            var remarks=document.getElementById("remarks"+i).value;
            if(worktxt!="" || start1!="" || end1!="")
            {
            document.getElementById("worktxt"+id).value=worktxt;
            document.getElementById("start1"+id).value=start1;
            document.getElementById("end1"+id).value=end1;
            document.getElementById("start2"+id).value=start2;
            document.getElementById("end2"+id).value=end2;
            document.getElementById("remarks"+id).value=remarks;
            return false;
            }
        }
    } else{
            document.getElementById("worktxt"+id).value="";
            document.getElementById("start1"+id).value="";
            document.getElementById("end1"+id).value="";
            document.getElementById("start2"+id).value="";
            document.getElementById("end2"+id).value="";
            document.getElementById("remarks"+id).value="";
    }
}
function addeditall(empid,flagval,count) {
    var i = 1 ;
    var classification="classification"+i;
    var starttime="start1"+i;
    var endtime="end1"+i;
    var cpyname="worktxt"+i;
    var start = $("#start1"+i).val();
    var end = $("#end1"+i).val();  
    var sectionchk=document.getElementById(classification).value; 
    var starttimechk = $("#start1"+i).val();
    var endtimechk = document.getElementById(endtime).value;
    var err_kanji_nodata = "Please Select Particular Data";
    var valid = true;
    for (var frst = 1; frst<count ; frst++) {
        $('input:text').each(function () {
            if ($(this).val() != '') {
            valid = false;
            }
        });
    }
    if(valid==true){
       alert(err_kanji_nodata);
       document.getElementById(classification).focus();
       return false;
    } else {
        var err_end_time = "Please Enter End Time";
        if(starttimechk&&(endtimechk=="")) {
                alert(err_end_time);
                document.getElementById(endtime).focus();
                document.getElementById(endtime).select();
        } else {
            if (flagval == "1") {
            var confirmmsg = "Do You Want Register?";
                if (confirm(confirmmsg)) {
                $('#timesheetadd #empid').val(empid);
                $('#timesheetadd #flag').val(flagval);
                $('#timesheetadd').attr('action', 'timeSheetReg?mainmenu='+mainmenu+'&time='+datetime);
                $("#timesheetadd").submit();
                }
            } else{
                var confirmmsg = "Do You Want Update?";
                if (confirm(confirmmsg)) {
                $('#timesheetadd #empid').val(empid);
                $('#timesheetadd #flag').val(flagval);
                $('#timesheetadd').attr('action', 'timeSheetReg?mainmenu='+mainmenu+'&time='+datetime);
                $("#timesheetadd").submit();
                }
            }
        }
    }
}
function uploadpopup(mainmenu,empid,year,month) {
    var mainmenu = "staff";
    popupopenclose(1);
    $('#empid').val(empid);
    $('#selMonth').val(month);
    $('#selYear').val(year);
    $('#timesheetdetails').load('../Timesheet/uploadpopup?empid='+empid+'&mainmenu='+mainmenu+'&year='+year+'&month='+month);
    $("#timesheetdetails").modal({
           backdrop: 'static',
            keyboard: false
        });
    $('#timesheetdetails').modal('show');
}
 function filevalidate(){
    var ext = $('#xlfile').val().split('.').pop().toLowerCase();
    if ($('#xlfile').val()!="") {
      var size = parseFloat($("#xlfile")[0].files[0].size / 1024);  
    }
    if($('#xlfile').val()==""){
      $("#empty_file").show();
    }else if($.inArray(ext, ['xlsx','xls']) == -1) {
       $("#file_ext").show();
       $("#file_size").hide();
       $("#empty_file").hide();
    }else if(size > "2097") {
       $("#file_ext").hide();
       $("#empty_file").hide();
       $("#file_size").show();
    }else{
      if($('#xlfile').val() != ""){
        if(confirm("Do You Want To Upload The File")) {
          pageload();
          $("#file_ext").hide();
          $("#file_size").hide();
          $("#empty_file").hide();
          $("form").submit();
        }
      }
    }
  }
function fnexcelupload(mainmenu,empid,yr,mon) {
    var ifile = document.getElementById('xlfile').value;
    var empid = document.getElementById('empid').value;
    var yr = document.getElementById('selMonth').value;
    var mon = document.getElementById('selYear').value;

    if (ifile == "") {
        alert("please_upload_file");
        document.getElementById('xlfile').focus();
        document.getElementById('xlfile').select();
        return false;
    } else if (ifile != "") {
        var arr1 = new Array;
        arr1 = ifile.split("\\");
        var len = arr1.length;
        var doc1 = arr1[len - 1];
        var filext = doc1.substring(doc1.lastIndexOf(".") + 1);
        // Checking Extension
        if (filext == "xls" || filext == "xlsx") {
        } else {
            alert("Uploaded_File_xls_xlsx_Format");
            return false;
        }
    }
    var cmsg = "Do You Want Update File";
    if (confirm(cmsg)) {
        document.getElementById("upload").disabled = true;
        document.getElementById("cancel").disabled = true;
        document.forms["uploadpopup"].submit();

        return true;
    } else {
        return false;
    }
}