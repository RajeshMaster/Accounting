var data = {};
$(function () {
	var cc = 0;
	$('#cussort').click(function () {
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
		$('#cussort').animate({
			'marginRight' : movediv //moves down
		});
		ccd++;
		if( $('#searchmethod').val() == 1 || $('#searchmethod').val() == 2){
			ccd--;
		}  
	});
});
$(document).ready(function() {
    // initialize tooltipster on text input elements
    // initialize validate plugin on the form
    $('.addeditprocess').click(function () {
        $("#frmcustaddedit").validate({
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
                txt_custnamejp: {required: true},
                txt_kananame: {required: true},
                txt_repname: {required: true},
                txt_custagreement: {required: true,date: true},
                txt_branch_name: {required: true},
                txt_mobilenumber: {required: true,minlength:10},
                txt_fax: {required: true,minlength:10},
                txt_url: {required: true},
                txt_address: {required: true},
            },
            submitHandler: function(form) { // for demo
                if($('#frmcustaddedit #editid').val() == "") {
                    var confirmprocess = confirm(err_confreg);
                 } else {
                    var confirmprocess = confirm(err_confup);
                 }
                if(confirmprocess) {
                    pageload();
                    //form.submit();
                    return true;
                } else {
                    return false
                }
            }
        });
        $.validator.messages.required = function (param, input) {
            var article = document.getElementById(input.id);
            return article.dataset.label + ' field is required';
        }
        $.validator.messages.minlength = function (param, input) {
          var article = document.getElementById(input.id);
          return "Please Enter valid 10 Number";
        }
    });
    $('.Branchaddeditprocess').click(function () {
        $("#frmbranchaddedit").validate({
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
                txt_branch_name: {required: true},
                txt_mobilenumber: {required: true,minlength:10},
                txt_fax: {required: true,minlength:10},
                txt_address: {required: true},
            },
            submitHandler: function(form) { // for demo
                if($('#frmbranchaddedit #editid').val() == "") {
                    var confirmstatus = confirm("Do You Want To Register?");
                 } else {
                    var confirmstatus = confirm("Do You Want To Update?");
                 }
                if(confirmstatus) {
                    pageload();
                    //form.submit();
                    return true;
                } else {
                    return false
                }
            }
        });
        $.validator.messages.required = function (param, input) {
            var article = document.getElementById(input.id);
            return article.dataset.label + ' field is required';
        }
        $.validator.messages.minlength = function (param, input) {
          var article = document.getElementById(input.id);
          return "Please Enter valid 10 Number";
        }
    });
    $('.Inchargeaddeditprocess').click(function () {
        $("#frminchargeaddedit").validate({
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
                txt_incharge_name: {required: true},
                txt_incharge_namekana: {required: true},
                txt_mobilenumber: {minlength:10},
                bname: {required: true},
                designation: {required: true},
                txt_mailid: {email:true},
            },
            submitHandler: function(form) { // for demo
                if($('#frminchargeaddedit #editid').val() == "") {
                    var confirmincharge = confirm("Do You Want To Register?");
                 } else {
                    var confirmincharge = confirm("Do You Want To Update?");
                 }
                if(confirmincharge) {
                    pageload();
                    //form.submit();
                    return true;
                } else {
                    return false
                }
            }
        });
        $.validator.messages.required = function (param, input) {
            var article = document.getElementById(input.id);
            return article.dataset.label + ' field is required';
        }
        $.validator.messages.minlength = function (param, input) {
          var article = document.getElementById(input.id);
          return "Please Enter valid 10 Number";
        }
    });
});
function UnderConstruction(){
	alert("Under Construction");
}
function pageClick(pageval) {
	$('#page').val(pageval);
	var pageing = $('#viewflg').val();
    var pagflg = $('#pageflg').val();
	if (pagflg == '1') {
	$("#emphistoryform").submit();
	} else if(pageing == '2') {
	$("#customerindexform").submit();
	} else {
    $("#emphistoryviewform").submit();
    }
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	var pageing = $('#viewflg').val();
    var pagflg = $('#pageflg').val();
	if (pagflg == '1') {
    $("#emphistoryform").submit();
    } else if(pageing == '2') {
    $("#customerindexform").submit();
    } else {
    $("#emphistoryviewform").submit();
    }
}
function getdetails(empid,empname,datetime,id){
	$('#hdnempid').val(empid);
	$('#hdnempname').val(empname);
    $('#hdnback').val(id);
	var mainmenu="Customer";
	$('#emphistoryform').attr('action', '../Customer/Onsitehistory?mainmenu='+mainmenu+'&time='+datetime);
    $("#emphistoryform").submit();
}
function gotoBack(datetime) {
    var back = $('#hdnback').val();
    if(back ==1){
        $('#emphistoryviewform').attr('action', '../EmpHistory/index?mainmenu=Employee&time='+datetime);
        $("#emphistoryviewform").submit();
    } else if(back==3){
        $('#emphistoryviewform').attr('action', '../Customer/View?mainmenu=Customer&time='+datetime);
        $('#mainmenu').val("Customer");
        $("#emphistoryviewform").submit();
    } else {
        $('#emphistoryviewform').attr('action', '../Staff/index?mainmenu=staff&time='+datetime);
        $('#mainmenu').val("staff");
        $("#emphistoryviewform").submit();
    }
}
function filter(val) {
    $('#plimit').val(50);
    $('#page').val('');
    $("#filter").val('');
    $('#singlesearchtxt').val('');
    $('#sorting').val('');
    $('#startdate').val('');
    $('#enddate').val('');
    $('#name').val('');
	$('#filterval').val(val);
    $('#cussort').val('');
	$("#customerindexform").submit();
}
function fnSingleSearch() {
    var mainmenu='Customer';
	var singlesearchtxt = $("#singlesearchtxt").val();
	var singlesearchtxt = document.getElementById('singlesearchtxt').value;
	if (singlesearchtxt == "") {
		alert("Please Enter The customer Search.");
		$("#singlesearchtxt").focus(); 
		return false;
	} else {
		$('#plimit').val('');
		$('#page').val('');
        if ($('#singlesearchtxt').val()) {
            $('#searchmethod').val(1);
        } else {
            $('#searchmethod').val('');
        }
        $('#startdate').val('');
        $('#enddate').val('');
        $('#name').val('');
        $('#address').val('');
		$('#customerindexform').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#customerindexform").submit();
	}
}
function checkSubmitsingle(e) {
    if(e && e.keyCode == 13) {
        fnSingleSearch();
    }
}

function umultiplesearch() {
    var mainmenu='Customer';
	var name = $("#name").val();
	var name = document.getElementById('name').value;
	var address = $("#address").val();
	var address = document.getElementById('address').value;
	var startdate = $("#startdate").val();
	var startdate = document.getElementById('startdate').value;
	var enddate = $("#enddate").val();
	var enddate = document.getElementById('enddate').value;
	if (name == "" && address == "" && startdate == "" && enddate == "") {
		alert("Customer search is missing.");
		$("#name").focus(); 
		return false;
    } else if (Date.parse(startdate) > Date.parse(enddate)) {
        alert("Please enter date greater than startdate");
         document.getElementById('enddate').focus();
        return false;
	} else {
    $('#plimit').val(50);
    $('#page').val('');
    //$('#sortOptn').val('');
    $("#filterval").val('');
    //$('#sortOrder').val('DESC');
   // $('#sortOrder').val('asc'); 
    $('#singlesearchtxt').val('');
    $('#searchmethod').val(2);
    $('#customerindexform').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
    $("#customerindexform").submit();
}
}
function checkSubmitmulti(e) {
    if(e && e.keyCode == 13) {
        umultiplesearch();
    }
}
function selectbox(){
	var sort = $('#cussort').val();
	$('#sorting').val(sort);
	$("#customerindexform").submit();
}
function clearsearch() {
    $('#plimit').val(50);
    $('#page').val('');
	$("#filter").val('');
    $('#singlesearchtxt').val('');
    $('#sorting').val('');
    $('#cussort').val('');
    $('#startdate').val('');
    $('#enddate').val('');
    $('#name').val('');
    $("#customerindexform").submit();
}
function ChangecutomerUse(val,id) {
	var confirmprocess = confirm("Do You Want To Change the flag?");
	if(confirmprocess){
		$('#useval').val(val);
		$('#id').val(id);
		$("#customerindexform").submit();
	} else {
		return false;
	}
}

function customerreg(datetime) {
    pageload();
    var mainmenu=$('#customerindexform #mainmenu').val();
    $('#customerindexform').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
    $("#customerindexform").submit();
}
function custview(datetime,id,custid) {
   //alert(id);
    //alert(custid);
    $('#id').val(id);
    $('#custid').val(custid);
    
    var mainmenu=$('#customerindexform #mainmenu').val();
    $('#customerindexform').attr('action', 'View?mainmenu='+mainmenu+'&time='+datetime);
    $("#customerindexform").submit();
}
function edit(datetime,id,custid) {
    $('#id').val(id);
    $('#editid').val(id);
    $('#flg').val("1");
    $('#custid').val(custid);
    var mainmenu="Customer";
    $('#customerviewform').attr('action', 'addedit?mainmenu='+mainmenu+'&time='+datetime);
    $("#customerviewform").submit();
}
function popupopen(datetime,custid,id){
	// var custeid= $("#hid_custid").val(custid);
	// alert(custeid);
	var employeeid=$("#emp_id").val();
	var mainmenu="Customer";
	$('#empnamepopup').load('../Customer/empnamepopup?custid='+custid+'&id='+id+'&mainmenu='+mainmenu+'&employeeid='+employeeid+'&time='+datetime);
	    $("#empnamepopup").modal({
	           backdrop: 'static',
	            keyboard: false
	        });
	    $('#empnamepopup').modal('show');
	 //$("#customerviewform").submit();
} 
function disablededittrue(empoid) {
	//alert(empoid);
    $("#emp_id").val(empoid);
    $("#select" ).css( "background-color", "orange" );
    $("#select" ).removeAttr("disabled");
}
function gotoindexpage(viewflg,mainmenu,datetime) {
    if (cancel_check == false) {
    if (!confirm("Do You Want To Cancel the Page?")) {
      return false;
    }
  }
    if (viewflg == "1") {
      pageload();
        $('#frmcustaddeditcancel').attr('action', 'View?mainmenu='+mainmenu+'&time='+datetime);
        $("#frmcustaddeditcancel").submit();
    } else {
        $('#frmcustaddeditcancel').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
        $("#frmcustaddeditcancel").submit();
    }
}
function fnGetBrachByAjax(id) {
	var customerid = $('#'+id).val();
	// var selindex = document.getElementById(id).selectedIndex;
	// var seltext = document.getElementById(id)[selindex].text;
	var mainmenu = "Customer";
	$.ajax({
		type: 'POST',
        dataType: "JSON",
		url: 'branchname_ajax',
		data: {"customerid": customerid,"mainmenu": mainmenu},
		success: function(resp) {
            $('#newbranch').find('option').remove().end();
            $.each(resp, function(key,value) {
                $('#newbranch').append( '<option value="'+key+'">'+value+'</option>' );
                $('select[name="newbranch"]').val(id);
            }); 
			$('#newbranch').val($('#hidebranchname').val());
		},
		error: function(data) {
			$("#updatebutton").attr("data-dismiss","modal");
		}
	});
}
function branchadd(datetime) {
    pageload();
    var mainmenu="Customer";
    $('#customerviewform').attr('action', 'Branchaddedit?mainmenu='+mainmenu+'&time='+datetime);
    $("#customerviewform").submit();
}
function branchedit(datetime,branchid){
	//alert(branchid);
    $('#flg').val("1");
     $('#editid').val(branchid);
    $('#branchid').val(branchid);
    var mainmenu="Customer";
    $('#customerviewform').attr('action', 'Branchaddedit?mainmenu='+mainmenu+'&time='+datetime);
    $("#customerviewform").submit();
}

function gotoinpage(mainmenu,datetime) {
    if (cancel_check == false) {
    if (!confirm("Do You Want To Cancel the Page?")) {
      return false;
    }
  }
      pageload();
        $('#frmbranchaddeditcancel').attr('action', 'View?mainmenu='+mainmenu+'&time='+datetime);
        $("#frmbranchaddeditcancel").submit();
}
function goindexpage(mainmenu,datetime) {
    pageload();
    $('#customerviewform').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
    $("#customerviewform").submit();
}
function goempindexpage(mainmenu,datetime) {
    pageload();
    $('#customerviewform').attr('action', '../EmpHistory/index?mainmenu='+mainmenu+'&time='+datetime);
    $("#customerviewform").submit();
}
function fnCancel_check() {
      cancel_check = false;
      return cancel_check;
  }
  function inchargeadd(datetime) {
    pageload();
    var mainmenu="Customer";
    $('#customerviewform').attr('action', 'Inchargeaddedit?mainmenu='+mainmenu+'&time='+datetime);
    $("#customerviewform").submit();
}
function inchargeedit(datetime,inchargeid){
    $('#flg').val("1");
    $('#editid').val(inchargeid);
    $('#inchargeid').val(inchargeid);
    var mainmenu="Customer";
    $('#customerviewform').attr('action', 'Inchargeaddedit?mainmenu='+mainmenu+'&time='+datetime);
    $("#customerviewform").submit();
}
function gotoviewpage(mainmenu,datetime) {
    if (cancel_check == false) {
    if (!confirm("Do You Want To Cancel the Page?")) {
      return false;
    }
  }
      pageload();
        $('#frminchargeaddeditcancel').attr('action', 'View?mainmenu='+mainmenu+'&time='+datetime);
        $("#frminchargeaddeditcancel").submit();
}
function sortingfun() {
    pageload();
    $('#plimit').val(50);
    $('#page').val('');
    var sortselect=$('#cussort').val();
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
    $("#customerindexform").submit();
}
 function customerview(datetime,id,custid) {
    $('#id').val(id);
    $('#custid').val(custid);
     $('#empid').val(1);
    var mainmenu=$('#emphistoryform #mainmenu').val();
    $('#emphistoryform').attr('action', '../Customer/View?mainmenu='+mainmenu+'&time='+datetime);
    $("#emphistoryform").submit();
}
function empselectionpopupadd(datetime,custid,id){
    popupopenclose(1);
    var selectionid = $('#selectionid').val();
    var mainmenu="Customer";
    $('#empnamepopup').load('../Customer/empnamepopup?custid='+custid+'&id='+id+'&selectionid='+selectionid+'&mainmenu='+mainmenu+'&time='+datetime);
        $("#empnamepopup").modal({
               backdrop: 'static',
                keyboard: false
            });
        $('#empnamepopup').modal('show');
     //$("#customerviewform").submit();
} 
function coverletter(datetime,custid,id){
    //var selectionid = $('#selectionid').val();
    var mainmenu="Customer";
    $('#coverletterpopup').load('../Customer/coverletterpopup?custid='+custid+'&id='+id+'&mainmenu='+mainmenu+'&time='+datetime);
        $("#coverletterpopup").modal({
               backdrop: 'static',
                keyboard: false
            });
        $('#coverletterpopup').modal('show');
     //$("#customerviewform").submit();
}
function coverdownload(file,path) {
    var confirm_download = "Do You Want To Download?";
    if(confirm(confirm_download)) {
        window.location.href="../app/Http/Common/downloadfile.php?file="+file+"&path="+path+"/";
    }
}  
function getchangeempdetails(datetime,empid,empname){
    //alert(empname);
    $('#hdnempid').val(empid);
    $('#hdnempname').val(empname);
    var mainmenu="Customer";
    $('#customerviewform').attr('action', '../Customer/Onsitehistory?mainmenu='+mainmenu+'&time='+datetime);
    $("#customerviewform").submit();
}  
function clearAll(div) {
    if (div == 1) {
       document.getElementById('name').value = '';
        document.getElementById('startdate').value = '';
        document.getElementById('enddate').value = '';
        document.getElementById('address').value = '';
        // document.getElementById('single_table').style.display = "none";
        document.getElementById('multisearch').style.display = "block";
    } else {
        document.getElementById('name').value = '';
        document.getElementById('startdate').value = '';
        document.getElementById('enddate').value = '';
        document.getElementById('address').value = '';
        //document.getElementById('single_table').style.display = "block";
        document.getElementById('multisearch').style.display = "none";
    }
}
function valchange() {
    var txt=$("#newemployeename option:selected").val();
    $('#empno').text(txt);
}
function gotoestimation(datetime) {
   $('#hdncancel').val(1);
  // var mainmenu="sales";
    $('#customerviewform').attr('action', '../Estimation/addedit?mainmenu=estimation&time='+datetime);
    //$('#mainmenu').val("sales");
    $("#customerviewform").submit();
}
