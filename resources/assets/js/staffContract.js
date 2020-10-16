var data = {};
$(function () {
	var cc = 0;
	$('#selectsort').click(function () {
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
			movediv = "+=260px"
		} else {
			movediv = "-=260px"
		}
		$('#selectsort').animate({
			'marginRight' : movediv //moves down
		});
		ccd++;
		if( $('#searchmethod').val() == 1 || $('#searchmethod').val() == 2){
			ccd--;
		}  
	});
})
function sortingfun() {
	pageload();
    $('#plimit').val(50);
    $('#page').val('');
    var sortselect=$('#selectsort').val();
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
    $("#empcontract").submit();
}
function underConstruction() { 
    alert("Under Construction");
}
function selectActive(val) {
	document.getElementById('pageclick').value = '1';
	document.empcontract.resignid.value = val;
	document.empcontract.submit();
}
function selectbox(){
	var sort = $('#selectsort').val();
	$('#selectsorted').val(sort);
	$("#empcontract").submit();
}
function usinglesearch() {
    var mainmenu='staff';
	var singlesearchtxt = $("#singlesearch").val();
	if (singlesearchtxt == "") {
		alert("Please Enter The Staff Search.");
		$("#singlesearch").focus(); 
		return false;
	} else {
	if ($('#singlesearch').val()) {
        $("#searchmethod").val(1);
        $('#employeeno').val('');
		$('#employeename').val('');
		$('#startdate').val('');
		$('#enddate').val('');
    	} else {
       	$("#searchmethod").val('');
    	}
		$('#plimit').val('');
		$('#page').val('');
		$('#empcontract').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#empcontract").submit();
	}
}

function clearsearch(){
	$('#plimit').val(50); 
	$('#page').val('');
	$('#selectsorted').val('');
	$('#singlesearch').val('');
	$('#employeeno').val('');
	$('#employeename').val('');
	$('#startdate').val('');
	$('#enddate').val('');
	$('#searchmethod').val('');
    $('#empcontract').submit();
}
function pageClick(pageval) {
	$('#page').val(pageval);
	$("#empcontract").submit();
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$("#empcontract").submit();
}
function umultiplesearch() {
    var mainmenu='staff';
	var employeeno = $("#employeeno").val();
	var employeeno = document.getElementById('employeeno').value;
	var employeename = $("#employeename").val();
	var employeename = document.getElementById('employeename').value;
	var startdate = $("#startdate").val();
	var startdate = document.getElementById('startdate').value;
	var enddate = $("#enddate").val();
	var enddate = document.getElementById('enddate').value;
	if (employeeno == "" && employeename == "" && startdate == "" && enddate == "") {
		alert("Staffcont search is missing.");
		$("#employeeno").focus(); 
		return false;
    } else if (Date.parse(startdate) > Date.parse(enddate)) {
        alert("Please enter date greater than startdate");
         document.getElementById('enddate').focus();
        return false;  
	} else {
		$('#plimit').val(50);
	    $('#page').val('');
	    $('#singlesearch').val('');
	    $("#searchmethod").val(2);
	    $('#empcontract').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	    $("#empcontract").submit();
	}
}
function staffview(id) { 
	var mainmenu="StaffContr";
	$('#viewid').val(id);
	$('#empcontract').attr('action','contractdetails?mainmenu='+mainmenu+'&time='+datetime);
	$("#empcontract").submit();
}
function goindexpage(mainmenu) {
    $('#employeefrm').attr('action','index?mainmenu='+mainmenu+'&time='+datetime);
    $("#employeefrm").submit();
}
function contractemployeeview(empnoadd,radio_emp){
	var mainmenu="StaffContr";
	$('#rid').val(3);
	$("#empnoadd").val(empnoadd);
	$("#radio_emp").val(radio_emp);
	$('#employeefrm').attr('action','contractview?mainmenu='+mainmenu+'&time='+datetime);
	$("#employeefrm").submit();
}
function goback(mainmenu) { 
	var mainmenu="StaffContr";
	$('#employeeView').attr('action','index?mainmenu='+mainmenu+'&time='+datetime);
    $("#employeeView").submit();
}
function contractemployeeadd(empnoadd, empName){
	var mainmenu="StaffContr";
	$('#rid').val(1);
	$("#empnoadd").val(empnoadd);
	$("#Name").val(empName);
	$('#employeefrm').attr('action','contractview?mainmenu='+mainmenu+'&time='+datetime);
	$("#employeefrm").submit();
}
function test(){
	if ($("#year"). prop("checked") == true) {
		$("#numyear").prop('disabled', false);
	} else {
		$("#numyear").val('');
		$("#numyear").prop('disabled', true);
	}
}
function contractemployeeedit(empnoadd, radio_emp){
	var mainmenu="StaffContr";
	$('#rid').val(2);
	$("#empnoadd").val(empnoadd);
	$("#radio").val(radio_emp);
	$('#staffContaddedit').attr('action','contractview?mainmenu='+mainmenu+'&time='+datetime);
	$("#staffContaddedit").submit();
}
function contractdownload(empno,empname) {
	var mainmenu="StaffContr";
	$("#empid").val(empno);
	$("#empname").val(empname);
	$('#employeefrm').attr('action','contractdownload?mainmenu='+mainmenu+'&time='+datetime);
	$("#employeefrm").submit();
}
function checkSubmitsingle(e) {
   	if(e && e.keyCode == 13) {
   		usinglesearch();
   	}
}
function checkSubmitmulti(e) {
   	if(e && e.keyCode == 13) {
   		umultiplesearch();
   	}
}
function godetailspage(mainmenu) { 
	var mainmenu="StaffContr";
	$('#staffContaddedit').attr('action','contractdetails?mainmenu='+mainmenu+'&time='+datetime);
    $("#staffContaddedit").submit();
}
function add_date(){
	var startdate = $("#StartDate").val();
	var start = new Date(startdate);
	var nyear = $("#numyear").val();
	var currentdate = new Date();
	var enddate = $("#EndDate").val();
	var dateval = "";
	var dateval1 = "";
	if(isEmpty(startdate) || enddate == "NaN") {
		$("#StartDate").val("");
	}
	if (!isEmpty(startdate)) {
			var start_year = start.getFullYear();
			if(!isEmpty(nyear)){
				start_year = start_year+Number(nyear);
			} else {
				start_year = start_year+1;
			}
		var start_month= pad(start.getMonth()+1,2);
		var start_date= pad(start.getDate(),2);
		dateval1 = start_year+"-"+start_month+"-"+start_date;
		var dDate = new Date(dateval1);
 		dDate.setDate(dDate.getDate()-1);
		var SubYear= dDate.getFullYear();
		var SubMonth= pad(dDate.getMonth()+1,2);
		var SubDay= pad(dDate.getDate(),2);
		dateval = SubYear+"-"+SubMonth+"-"+SubDay;
	} else {
		$("#EndDate").val("");
	}

   if(isDate(dateval)) {
		$("#EndDate").val(dateval);
		
	}  else if(!isDate(dateval)) {
		if( start_month == '02' &&  start_date ==29){
			var dateval1 = start_year+"-"+start_month+"- 28";
			$("#EndDate").val(dateval1);
		}else{
			$("#EndDate").val(dateval1);
		}
	} else{
		$("#EndDate").val("");
	}
}

function gotoindexpage(viewflg,mainmenu) {
	var mainmenu="StaffContr";
	if (cancel_check == false) {
		if (!confirm("Do You Want To Cancel the Page?")) {
			return false;
		}
	}
    if (viewflg == "2") {
    	pageload();
        $('#frmcontractaddeditcancel').attr('action', 'contractdetails?mainmenu='+mainmenu+'&time='+datetime);
        $("#frmcontractaddeditcancel").submit();
    } 
	
}
function fnCancel_check() {
	cancel_check = false;
	return cancel_check;
}
//Common Function
function pad(number, length) {
	var str = '' + number;
	while (str.length < length) {
		str = '0' + str;
	}
	return str;
}
function isDate(dt){
	if (isValidateDate(dt)==false){
		return false;
	}
    return true;
 }
 function isValidateDate(dtStr){
	dtStr=dtStr.replace(".", "/");
	dtStr=dtStr.replace(".", "/");
	dtStr=dtStr.replace("-", "/");
	dtStr=dtStr.replace("-", "/");
	var dtCh= "/";
	var minYear=1900;
	var maxYear=2100;
	
	var daysInMonth = DaysArray(12)
	//alert(daysInMonth);
	var pos1=dtStr.indexOf(dtCh);
	var pos2=dtStr.indexOf(dtCh,pos1+1);
	var strDay=dtStr.substring(0,pos1);
	var strMonth=dtStr.substring(pos1+1,pos2);
	var strYear=dtStr.substring(pos2+1);
	if((strYear.length<=2)&&(strDay.length==4)){
		var str='';
		str=strDay;
		strDay=strYear;
		strYear=str;
	}
	
	strYr=strYear;
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1);
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1);
	}
	month=parseInt(strMonth);
	day=parseInt(strDay);
	year=parseInt(strYr);
	if (pos1==-1 || pos2==-1){
		//alert("The date format should be : dd/mm/yyyy")
		return false;
	}
	if (strMonth.length<1 || month<1 || month>12){
		//alert("Please Enter a valid month.");
		return false;
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		//alert("Please Enter a valid day.");
		return false;
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		//alert("Please Enter a valid 4 digit year between "+minYear+" and "+maxYear+".");
		return false;
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		//alert("Please Enter a valid date.");
		return false;
	}
return true;
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   } 
   return this
}
function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}
/*
function name 	: stripCharsInBag()
input			: string,string
retutn type		: Boolean
Discription 	: Supporting function for isDate() function
 */
function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}
function allowancecal(){
	var salary = document.getElementById('Salary').value.replace(',','');	
	var rowcount = document.getElementById('allowancecount').value;
	var  inc = document.getElementById('edit_id').value;
    var allow = document.getElementById('allow');
	var totalOthers=0;	
	var split_id = inc.split("-");
	if(isEmpty(split_id)){
		var totalOthers =0;
	}else{
		for (var i=0;i<split_id.length-1;i++){
    	var allowancetotalval=document.getElementById('allowance_'+split_id[i]);
    	var totalOthers = totalOthers+allowancetotalval;
	}}
	if(isEmpty(salary) || allow == "NaN") {
		document.getElementById('allow').value="";
	} else if (!isEmpty(salary)) {
		var sal=Number(salary)+Number( totalOthers);
		isformatMoney("allow",sal);
	}
}

function isformatMoney(salaryname,salary){
	salary = salary.toString().replace(/\$|\,/g, '');
	var japmoney="jp"; 
	var tot=0;
	for (i = 1; i <= 5; i++) {
		val = document.getElementById('allowance_'+i).value;
		num = val.toString().replace(/\$|\,/g, '');
		tot = +tot + +num;
	    var salamt = inrFormat(val,japmoney);
		document.getElementById('allowance_'+i).value=salamt;
	}

		salaryval = document.getElementById('Salary').value;

		num = salaryval.toString().replace(/\$|\,/g, '');

		tot = +tot + +num;
    var salaryamt = inrFormat(salary,japmoney);
	document.getElementById(salaryname).value=salaryamt;
    var tot = inrFormat(tot,japmoney);
	document.getElementById('allow').innerHTML=tot;
	document.getElementById('total').value=tot;
	return true;
}
var lib = {};
var formatMoney = lib.formatMoney = function(number, symbol, precision, thousand, decimal, format) {
		// Resursively format arrays:
		if (isArray(number)) {
			return map(number, function(val){
				return formatMoney(val, symbol, precision, thousand, decimal, format);
			});
		}

		// Clean up number:
		number = unformat(number);

		// Build options object from second param (if object) or all params, extending defaults:
		var opts = defaults(
				(isObject(symbol) ? symbol : {
					symbol : symbol,
					precision : precision,
					thousand : thousand,
					decimal : decimal,
					format : format
				}),
				lib.settings.currency
			),

			// Check format (returns object with pos, neg and zero):
			formats = checkCurrencyFormat(opts.format),

			// Choose which format to use for this value:
			useFormat = number > 0 ? formats.pos : number < 0 ? formats.neg : formats.zero;

		// Return with currency symbol added:
		return useFormat.replace('%s', opts.symbol).replace('%v', formatNumber(Math.abs(number), checkPrecision(opts.precision), opts.thousand, opts.decimal));
	};