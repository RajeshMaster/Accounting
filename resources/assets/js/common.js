var cancel_check = true;
$(document).ready(function(){
    // For triming empty space in front of text box
    $("form").on('submit',function(e){
      $('form input, textarea').each(function(i, v){
        if(v.type != 'file'){
        $(this).val(jQuery.trim($(this).val()));
        }
      });
    });
    // end of trim function
    $(".CMN_gmenu").click(function(){
      $(".se-pre-con").show();
    });
    $(".pageload").click(function(){
      $(".se-pre-con").show();
    });
    jQuery.validator.addMethod('greaterThanStartTime', function(value, element, param) {
      return ( value > jQuery(param).val() );
    }, 'Must be greater than Start Time');
    // Checking 2MB File Size
    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param)
    }, err_2mb);
    jQuery.validator.addMethod("time24", function(value, element) { 
        return /^([01]?[0-9]|2[0-3])(:[0-5][0-9])$/.test(value);
    }, "Invalid time format.");

    jQuery.validator.addMethod("specialChar", function(value, element) {
       return this.optional(element) || /([0-9a-zA-Z\s])$/.test(value);
    }, "Special Characters are not allowed.");
    // For Number With Comma Validation
    jQuery.validator.addMethod("numberwithcomma", function(value, element, param) {
        return value.match(new RegExp("." + param + "$"));
    }, "Please enter a valid number");
    // For Number With Comma Validation
    jQuery.validator.addMethod(
    "money",
    function(value, element) {
        var isValidMoney =/^\$?(([1-9][0-9]{0,2}(,[0-9]{3})*)|[0-9]+)?(.[0-9]{2})?$/.test(value);
        return this.optional(element) || isValidMoney;
    },
          "Please enter a valid number "
      );
    // Date Format
    $.validator.addMethod('correctformatdate', function (value) {
      if(value == "") {
        return true;
      }
      return /^\d{4}-\d{2}-\d{2}$/.test(value);
    }, 'Date Format Should Be YYYY-MM-DD');
    // Required Validation with zero
    $.validator.addMethod('requiredWithZero', function (value) {
      if(value != "") {
        if(value == 0 || value == '-') {
          return false;
        } else {
          return true;
        }
      }
    }, 'Amount Field is Required');
    // END Required Validation with zero
    // Required Validation with greater than 31
    $.validator.addMethod('requiredWithgreaterthanday', function (value) {
      if(value != "") {
        if(value.trim() > 31 || value.trim() == 0) {
          return false;
        } else {
          return true;
        }
      }
    }, 'Day Not Greater than 31');
    // END Required Validation with greater than 31
    // Script_for_Cancel_Check//
    $('input, select, textarea').bind("change keyup paste", function() {
        cancel_check = false;
    });
    // End_Script_for_Cancel_Check//
    // Jquery CUstom Greater than Start Date Validation Check
    jQuery.validator.addMethod("greaterThan", 
      function(value, element, params) {
          if (!/Invalid|NaN/.test(new Date(value))) {
              return new Date(value) >= new Date($(params).val());
          }
          return isNaN(value) && isNaN($(params).val()) 
              || (Number(value) >= Number($(params).val())); 
      },'Must be greater than Invoice Date.');
    // End Jquery CUstom Greater than Start Date Validation Check
     // Jquery CUstom Greater than Access Date Validation Check
    jQuery.validator.addMethod("accessDateCheck", 
      function(value, element, params) {
          if (!/Invalid|NaN/.test(new Date(value))) {
              return new Date(value) > new Date($(params).val());
          }
          return isNaN(value) && isNaN($(params).val()) 
              || (Number(value) > Number($(params).val())); 
      },'Must be greater than Access Date.');
    // End Jquery CUstom Greater than Access Date Validation Check
    // Jquery CUstom Greater than Start Date Validation Check
    jQuery.validator.addMethod("greaterThanStartdate", 
      function(value, element, params) {
          if (!/Invalid|NaN/.test(new Date(value))) {
              return new Date(value) >= new Date($(params).val());
          }
          return isNaN(value) && isNaN($(params).val()) 
              || (Number(value) >= Number($(params).val())); 
      },'Must be greater than startDate.');
    // End Jquery CUstom Greater than Start Date Validation Check
     // Jquery CUstom Less than Start Date Validation Check
    jQuery.validator.addMethod("lessThanStartdate", 
      function(value, element, params) {
          if (!/Invalid|NaN/.test(new Date(value))) {
              return new Date(value) <= new Date($(params).val());
          }
          return isNaN(value) && isNaN($(params).val()) 
              || (Number(value) <= Number($(params).val())); 
      },'Must be less than startDate.');
    // End Jquery CUstom Less than Start Date Validation Check
    // Birth Validation
    jQuery.validator.addMethod("DOB", 
      function(value, element, params) {
          if (!/Invalid|NaN/.test(new Date(value))) {
              return new Date(value) <= new Date($(params).val());
          }
          return isNaN(value) && isNaN($(params).val()) 
              || (Number(value) <= Number($(params).val())); 
      },'Please Select Date Less Than '+$('#DOB').val());
    // End Birth Validation
});
$(document).on('keyup keypress', 'form input[type="text"]', function(e) {
  if(e.keyCode == 13) {
    e.preventDefault();
    return false;
  }
});
$(window).bind("pageshow", function(event) {
  // Animate loader off screen
  $(".se-pre-con").fadeOut();
});
function pageload() {
  $(".se-pre-con").show();
}
function changelanguage() {
	// var confm = confirm("Do You Want To Change The Language?.");
	if (!confirm(err_changelanguage)) {
		return false;
	}
	$.ajax({
		type:'GET',
		url:'changelanguage',
		data: {
			langvalue: $('#langvalue').val()
		},
		success:function(data){
			location.reload(true);
		},
		error: function (data) {
			// alert(data.status);
		}
	});
}
function isNumberKey(evt) { 
  	var charCode = (evt.which) ? evt.which : event.keyCode
  	if (charCode > 31 && (charCode < 48 || charCode > 57))
  		return false;
  	return true;
}
function isNumberKeywithminus(evt) { 
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode == 45) {
      return true;
    }
    if (charCode > 31 && (charCode < 48 || charCode > 57))
      return false;
    return true;
}
function up() {
	alert("Under Construction");
	// return false;
}
function setDatePicker(datefield) {
  $('.'+datefield).datetimepicker({
    format: 'yyyy-mm-dd',
    language:  'eng',
    weekStart: 1,
    todayBtn:  1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    minView: 2,
    forceParse: 0
  });
}
function setDatePicker15yearbefore(datefield) {
  $('.'+datefield).datetimepicker({
    format: 'yyyy-mm-dd',
    language:  'eng',
    weekStart: 1,
    todayBtn:  1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    minView: 2,
    forceParse: 0,
    endDate : '-15y'
  });
}
function setDatePicker18yearbefore(datefield) {
  $('.'+datefield).datetimepicker({
    format: 'yyyy-mm-dd',
    language:  'eng',
    weekStart: 1,
    todayBtn:  1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    minView: 2,
    forceParse: 0,
    endDate : '-18y'
  });
}
function setDatePickercur_date_before(datefield) {
  $('.'+datefield).datetimepicker({
    format: 'yyyy-mm-dd',
    language:  'eng',
    weekStart: 1,
    todayBtn:  1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    minView: 2,
    forceParse: 0,
    endDate : '-0d'
  });
}
function setDatePickerAfterAccessDate(datefield, accessdate) {
  $('.'+datefield).datetimepicker({
    format: 'yyyy-mm-dd',
    language:  'eng',
    weekStart: 1,
    todayBtn:  1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    minView: 2,
    forceParse: 0,
    startDate : accessdate
  });
}
function setNextDay(accessdate) {
  var date = new Date(accessDate);
  var newdate = new Date(date);
  newdate.setDate(newdate.getDate() + 1);
  var dd = newdate.getDate();
  var mm = newdate.getMonth() + 1;
  var y = newdate.getFullYear();
  return y + '-' + mm + '-' + dd;
}
function changecursordefault() {
  document.body.style.cursor='default';
}
function popupopenclose(flg) {
  if (flg == 1) {
    // Open
    $('#fixeddiv').removeClass('CMN_menu_fixed').addClass('CMN_positionabsolute');
    $('#sectiondiv').attr('style','margin-top:2px;');
  } else {
    // Close
    $('#fixeddiv').removeClass('CMN_positionabsolute').addClass('CMN_menu_fixed');
    $('#sectiondiv').attr('style','margin-top:125px;');
  }

}
// MONEY FORMAT
function fnMoneyFormat(name,flg) {
  var timestamp = parseInt($('#'+name).val());
    if (isNaN(timestamp) == true) { 
        $('#'+name).val('');
        return false;
    }
    var value = $('#'+name).val();
    value = value.replace(/[ ]*,[ ]*|[ ]+/g, '');
    // alert(value);
    fnMoneyFormatWithoutleadingzero(name, value,flg);
}

// MONEY FORMAT
function fnMoneyFormatNegative(name,flg) {
  var timestamp = $('#'+name).val().replace(/[^0-9]/gi, '');
    if (isNaN(timestamp) == true) { 
        $('#'+name).val('');
        return false;
    }
    var value = $('#'+name).val();
    value = value.replace(/[ ]*,[ ]*|[ ]+/g, '');
    value = value.replace(/[^0-9]/gi, '');
    // alert(value);
    fnMoneyFormatWithoutleadingzero(name, value,flg);
    var value = $('#'+name);
    value.val('-' + value.val());
}
function fnMoneyFormatWithoutleadingzero(name, value,japmoney) {
  value = value.replace(/[ ]*,[ ]*|[ ]+/g, '');
  var x = event.keyCode;
  var passvalue = value;
  if ((value.length > 15) && (value.indexOf(',') == -1)) {
    passvalue = value.substr(0, 15);
  }
  passvalue = Number(passvalue).toString();
  //if (x != 37 && x != 39 && x != 8 && x != 46 && x != 36 && x != 9) {
    isformatMoneyINR(name, passvalue,japmoney);
  //}
  
}
function isformatMoneyINR(salaryname, salary,japmoney) {
  var salaryamt = inrFormat(salary,japmoney);
  if (salaryamt != 0) {
    $('#'+salaryname).val(salaryamt);
  }
  return true;
}
function inrFormat(nStr,japmoney) { // nStr is the input string
  nStr += '';
  x = nStr.split('.');
  x1 = x[0];
  x2 = x.length > 1 ? '.' + x[1] : '';
  var rgx = /(\d+)(\d{3})/;
  var z = 0;
  var len = String(x1).length;
  var num = parseInt((len/2)-1);

  while (rgx.test(x1))
  {
  if(z > 0)
  {
    x1 = x1.replace(rgx, '$1' + ',' + '$2');
  }
  else
  {
    x1 = x1.replace(rgx, '$1' + ',' + '$2');
    if(japmoney=="jp") {
      rgx = /(\d+)(\d{3})/;
    } else {
      rgx = /(\d+)(\d{2})/;
    }
  }
  z++;
  num--;
  if(num == 0)
  {
    break;
  }
  }
  return x1 + x2;
}
function moveSelected(from, to) {
    $('#'+from+' option:selected').remove().appendTo('#'+to); 
}
function isEmail(str)
{
  var reg = /(.+)@(.+){2,}\.(.+){2,}/;
  if(reg.test(str) == false){
    return false;
  }
  else{
    return true;
  }
}
function isEmpty(str) {
  if (str==null || str==''){
    return true;
  }
  else{
    return false;
  }
}
function isNumeric(strString) //  check for valid numeric strings 
{
  if(!/\D/.test(strString)){
    return true;  //IF NUMBER
  }else if(/^\d+\.\d+$/.test(strString)){
    return true;  //IF A DECIMAL NUMBER HAVING AN INTEGER ON EITHER SIDE OF THE DOT(.)
  }else{
    return false;
  }
}
function isDotNumberKey(evt, val, flg) {
  var charCode = (evt.which) ? evt.which : event.keyCode
  if (charCode == 45 && flg != 1) {
    return true;
  }
  var sval = String(val);
  if ((val.length == 0) && (charCode == 46)) {
    return false;
  }
  
  if ((val.length > 0) && (val.indexOf(".") != -1) && (charCode == 46)) {
    return false;
  }

  if ((val.length == 4) && (val.indexOf(".") == -1) && (charCode != 46)) {
    return false;
  }

  if ((val.length == 5) && (val.indexOf(".") == -1) && (charCode == 46)) {
    return false;
  }

  if (val.indexOf(".") != -1) {
    var splitdot = val.split(".");
    if (splitdot[1].length == 2)
      return false;
  }

  if (charCode > 31 && (charCode < 48 || charCode > 57) && (charCode != 46)) {
    return false;
  } 
  return true;
}
function fnCalculateAmount(i, name, value,tbn) {
  var x = event.keyCode;
  // x != 46 && 
  // && x != 8
  if (x != 37 && x != 39 && x != 36 && x != 9) {
    if (name != "" || value != "") {
      var japmoney="jp";
      isformatMoney(name, value,japmoney);
    }
    var quantity = document.getElementById('quantity' + i);
    var unit_price = document.getElementById('unit_price' + i);
    var amount = document.getElementById('amount' + i);
    var totval  = document.getElementById('totval');
    var m = 0, n = 0, l = 0;
    m = quantity.value;
    n = unit_price.value;
    if (Number(n.replace(/,/g, "")) < 0) {
      $(unit_price).css({"color":"red"});
      $(unit_price).css('border-color', 'red');
      $(amount).css({"color":"red"});
      $(amount).css('border-color', 'red');
    } else {
      $(unit_price).css({"color":"#333333"});
      $(unit_price).css('border-color', '#CCCCCC');
      $(amount).css({"color":"#333333"});
      $(amount).css('border-color', '#CCCCCC');
    }
    if ((m.trim() != "") || (n.trim() != "")) {
      l = Math.round(Number(m.replace(/,/g, "")) * Number(n.replace(/,/g, "")));
    }

    if (l) {
      amount.value = l.toLocaleString("ja-JP");
    }

    if ((m.trim() == 0) || (n.trim() == 0) || (m.trim() == "") || (n.trim() == "")) {
      amount.value = "";
    }

    
    fnCalculateTotal(tbn);
    fnControlAddOrRemove(i);
  }
}
function fnCalculateTotal(tbn) {
  var total_text=document.getElementsByClassName("input_text");
  var tbn=total_text.length;
  var tot = 0;
  if(tbn<15){
    var a = 15;
  } else {
    var a = tbn;
  }
  for (i = 1; i <= a; i++) {
    if(document.getElementById('amount'+i) != null){
    val = document.getElementById('amount'+i).value;
    num = val.toString().replace(/\$|\,/g, '');
    tot += Number(num);
  }
}
  if (tot) {
    document.getElementById('totval').value = tot.toLocaleString("ja-JP");
    // document.getElementById('tabamount').value = tot.toLocaleString("ja-JP");
  } else {
    document.getElementById('totval').value = "";
  }
}
function fnControlAddOrRemove(cnt,invflg) {
    var rowCount = $('#workspectable tr').length-1;

    var addrow = document.getElementsByName('addrow');
    var removerow = document.getElementsByName('removerow');

    var work_specific = document.getElementById('work_specific' + cnt);
    var quantity = document.getElementById('quantity' + cnt);
    var unit_price = document.getElementById('unit_price' + cnt);
    var amount = document.getElementById('amount' + cnt);
    var remarks = document.getElementById('remarks' + cnt);

    var work_specific15 = document.getElementById('work_specific' + rowCount);
    var quantity15 = document.getElementById('quantity' + rowCount);
    var unit_price15 = document.getElementById('unit_price' + rowCount);
    var amount15 = document.getElementById('amount' + rowCount);
    var remarks15 = document.getElementById('remarks' + rowCount);
    if(document.getElementById(work_specific) != null && document.getElementById(quantity) != null  && document.getElementById(remarks) != null
       && document.getElementById(unit_price) != null  && document.getElementById(amount) != null){
    var work_specific_len = work_specific.value.length;
    var quantity_len = quantity.value.length;
    var unit_price_len = unit_price.value.length;
    var amount_len = amount.value.length;
    var remarks_len = remarks.value.length;
    }
    if ((work_specific15.value.trim() != "") || (quantity15.value.trim() != "") || (unit_price15.value.trim() != "") 
      || (amount15.value.trim() != "") || (remarks15 .value.trim() != "")) {
      for (i = 1; i <= addrow.length; i++) {
        // addrow[i].disabled = true;
      }
      document.getElementById('removerow'+rowCount).disabled = true;

      for (i = 1; i <= rowCount; i++) {
        var work = document.getElementById('work_specific' + i).value;
        var quan = document.getElementById('quantity' + i).value;
        var unit = document.getElementById('unit_price' + i).value;
        var amnt = document.getElementById('amount' + i).value;
        var remark = document.getElementById('remarks' + i).value;

        if ((work.trim() != "") || (quan.trim() != "") || (unit.trim() != "") 
          || (amnt.trim() != "") || (remark.trim() != "")) {
          // document.getElementById('removerow' + i).disabled = true
          $('#removerow' + i).removeAttr("onclick");
          $('#removerow' + i).removeClass("csrp");
        } else {
          // document.getElementById('removerow' + i).disabled = false;
          $('#removerow' + i).attr('onclick', 'fnRemoveTR("' + i + '",  "' + invflg + '")');//onclick event which u 
          $('#removerow' + i).addClass('csrp');
        }
        //document.getElementById('addrow' + i).disabled = false;
      }
      
    } else {
      if (cnt != rowCount) {
        if ((work_specific_len > 0) || (quantity_len > 0) || (unit_price_len > 0) 
          || (amount_len > 0) || (remarks_len > 0)) {
          // document.getElementById('removerow' + cnt).disabled = true;
          $('#removerow' + cnt).removeAttr("onclick");
          $('#removerow' + cnt).removeClass("csrp");
      } else {
          // document.getElementById('removerow' + cnt).disabled = false;
          $('#removerow' + cnt).attr('onclick', 'fnRemoveTR("' + i + '",  "' + invflg + '")');//onclick event which u 
          $('#removerow' + cnt).addClass('csrp');
      }
    } else {
      for (i = 1; i <= rowCount; i++) {
        var work = document.getElementById('work_specific' + i).value;
        var quan = document.getElementById('quantity' + i).value;
        var unit = document.getElementById('unit_price' + i).value;
        var amnt = document.getElementById('amount' + i).value;
        var remark = document.getElementById('remarks' + i).value;

        if ((work.trim() != "") || (quan.trim() != "") || (unit.trim() != "") 
          || (amnt.trim() != "") || (remark.trim() != "")) {
          // document.getElementById('removerow' + i).disabled = true
          $('#removerow' + i).removeAttr("onclick");
          $('#removerow' + i).removeClass("csrp");
        } else {
          // document.getElementById('removerow' + i).disabled = false;
          $('#removerow' + i).attr('onclick', 'fnRemoveTR("' + i + '",  "' + invflg + '")');//onclick event which u 
          $('#removerow' + i).addClass('csrp');
        }
        document.getElementById('addrow' + i).disabled = false;
      }
    }
  }
}
function fnAddTR(cnt,flg=null) {
  var invflg = "";
  if (flg == 1) {
    invflg = 1;
  }
  var rowCount = $('#workspectable tr').length-1;

  var workspectable = document.getElementById('workspectable');

  var addrow = document.getElementsByName('addrow');
  var add = document.getElementsByName('add');

  var removerow = document.getElementsByName('removerow');

  var work_specific = document.getElementById('work_specific' + cnt);
  var quantity = document.getElementById('quantity' + cnt);
  var unit_price = document.getElementById('unit_price' + cnt);
  var amount = document.getElementById('amount' + cnt);
  var remarks = document.getElementById('remarks' + cnt);

  var work_specific15 = document.getElementById('work_specific' + rowCount);
  var quantity15 = document.getElementById('quantity' + rowCount);
  var unit_price15 = document.getElementById('unit_price' + rowCount);
  var amount15 = document.getElementById('amount' + rowCount);
  var remarks15 = document.getElementById('remarks' + rowCount);

  var cnt1 = parseInt(cnt) + 1;

  if (work_specific15.value.trim() != "" || quantity15.value.trim() != "" 
    || unit_price15.value.trim() || amount15.value.trim() || remarks15.value.trim()) {
    //alert("You can't Insert New Table Row");
    //return false;
    
  } else {
    for (i = rowCount; i > cnt1; i--) {
      document.getElementById('work_specific' + i).value = document.getElementById('work_specific' + (i-1)).value;
      document.getElementById('work_specific' + (i-1)).value = "";

      if (document.getElementById('removerow' + (i-1)).disabled == true) {
        // document.getElementById('removerow' + i).disabled = true;
        $('#removerow' + i).removeAttr("onclick");
        $('#removerow' + i).removeClass("csrp");
      } else {
        // document.getElementById('removerow' + i).disabled = false;
        $('#removerow' + i).attr('onclick', 'fnRemoveTR("' + i + '", "' + invflg + '")');//onclick event which u 
        $('#removerow' + i).addClass('csrp');
      }
      // document.getElementById('removerow' + (i-1)).disabled = false;
      $('#removerow' + (i+1)).attr('onclick', 'fnRemoveTR("' + i + '", "' + invflg + '")');//onclick event which u 
      $('#removerow' + (i+1)).addClass('csrp');
      
      document.getElementById('quantity' + i).value = document.getElementById('quantity' + (i-1)).value;
      document.getElementById('quantity' + (i-1)).value = "";
      
      document.getElementById('unit_price' + i).value = document.getElementById('unit_price' + (i-1)).value;
      document.getElementById('unit_price' + (i-1)).value = "";
      $(document.getElementById('unit_price' + (i-1))).css({"color":"#333333"});
      $(document.getElementById('unit_price' + (i-1))).css('border-color', '#CCCCCC');

      if (Number(document.getElementById('unit_price' + i).value.replace(/,/g, ""))  < 0) {
        $(document.getElementById('unit_price' + i)).css({"color":"red"});
        $(document.getElementById('unit_price' + i)).css('border-color', 'red');
      } else {
        $(document.getElementById('unit_price' + i)).css({"color":"#333333"});
        $(document.getElementById('unit_price' + i)).css('border-color', '#CCCCCC');
      }

      document.getElementById('amount' + i).value = document.getElementById('amount' + (i-1)).value;
      document.getElementById('amount' + (i-1)).value = "";
      $(document.getElementById('amount' + (i-1))).css({"color":"#333333"});
      $(document.getElementById('amount' + (i-1))).css('border-color', '#CCCCCC');
      
      if (Number(document.getElementById('amount' + i).value.replace(/,/g, ""))  < 0) {
        $(document.getElementById('amount' + i)).css({"color":"red"});
        $(document.getElementById('amount' + i)).css('border-color', 'red');
      } else {
        $(document.getElementById('amount' + i)).css({"color":"#333333"});
        $(document.getElementById('amount' + i)).css('border-color', '#CCCCCC');
      }

      document.getElementById('remarks' + i).value = document.getElementById('remarks' + (i-1)).value;
      document.getElementById('remarks' + (i-1)).value = "";

      if (flg == 1) {
        $('#divid' + i).css('display', $( '#divid' + (i-1) ).css('display'));
        $('#divid' + (i-1)).css('display','none');

        $('#crossid' + i).css('display', $( '#crossid' + (i-1) ).css('display'));
        $('#crossid' + (i-1)).css('display','none');

        document.getElementById('emp_ID' + i).value = document.getElementById('emp_ID' + (i-1)).value;
        document.getElementById('emp_ID' + (i-1)).value = "";

        document.getElementById('empKanaNames' + i).innerHTML = document.getElementById('empKanaNames' + (i-1)).innerHTML;
        document.getElementById('empKanaNames' + (i-1)).innerHTML = "";
      }
      document.getElementById('fordisable_hdn' + i).value = document.getElementById('fordisable_hdn' + (i-1)).value;
      document.getElementById('fordisable_hdn' + (i-1)).value = 0;
    }
  }
  toenavledisable();
  fnControlAddOrRemove(rowCount,invflg);
}
function toenavledisable() {
  var rowCount = $('#workspectable tr').length-1;

  for (i = 1; i <= rowCount; i++) {
    if ($('#fordisable_hdn'+i).val() == 0) {
        $('#work_specific'+i).attr('disabled', false);
        $('#quantity'+i).attr('disabled', false);
        $('#unit_price'+i).attr('disabled', false);
      }
      if ($('#fordisable_hdn'+i).val() == 1) {
        $('#work_specific'+i).attr('disabled', true);
        $('#quantity'+i).attr('disabled', true);
        $('#unit_price'+i).attr('disabled', true);
      }
    }
}
function fnRemoveTR(cnt,flg=null) {
  var invflg = "";
  if (flg == 1) {
    invflg = 1;
  }
  var rowCount = $('#workspectable tr').length-1;

  var workspectable = document.getElementById('workspectable');

  var addrow = document.getElementsByName('addrow');
  var removerow = document.getElementsByName('removerow');

  var work_specific = document.getElementById('work_specific' + cnt);
  var quantity = document.getElementById('quantity' + cnt);
  var unit_price = document.getElementById('unit_price' + cnt);
  var amount = document.getElementById('amount' + cnt);
  var remarks = document.getElementById('remarks' + cnt);

  var work_specific1 = document.getElementById('work_specific1');
  var quantity1 = document.getElementById('quantity1');
  var unit_price1 = document.getElementById('unit_price1');
  var amount1 = document.getElementById('amount1');
  var remarks1 = document.getElementById('remarks1');

  var cnt1 = parseInt(cnt);

  /*if (work_specific1.value.trim() != "" || quantity1.value.trim() != "" 
    || unit_price1.value.trim() || amount1.value.trim() || remarks1.value.trim()) {
    alert("You can't Delete Old Table Row");
    //return false;
    
  } else {*/
    for (i = cnt1; i < rowCount; i++) {
      document.getElementById('work_specific' + i).value = document.getElementById('work_specific' + (i+1)).value;
      document.getElementById('work_specific' + (i+1)).value = "";

      if (document.getElementById('removerow' + (i+1)).disabled == true) {
        // document.getElementById('removerow' + i).disabled = true;
        $('#removerow' + i).removeAttr("onclick");
        $('#removerow' + i).removeClass("csrp");
      } else {
        // document.getElementById('removerow' + i).disabled = false;
        $('#removerow' + i).attr('onclick', 'fnRemoveTR("' + i + '", "' + invflg + '")');//onclick event which u 
        $('#removerow' + i).addClass('csrp');
      }
      // document.getElementById('removerow' + (i+1)).disabled = false;
      $('#removerow' + (i+1)).attr('onclick', 'fnRemoveTR("' + i + '",  "' + invflg + '")');//onclick event which u 
      $('#removerow' + (i+1)).addClass('csrp');
      
      document.getElementById('quantity' + i).value = document.getElementById('quantity' + (i+1)).value;
      document.getElementById('quantity' + (i+1)).value = "";
      
      document.getElementById('unit_price' + i).value = document.getElementById('unit_price' + (i+1)).value;
      document.getElementById('unit_price' + (i+1)).value = "";
      $(document.getElementById('unit_price' + (i+1))).css({"color":"#333333"});
      $(document.getElementById('unit_price' + (i+1))).css('border-color', '#CCCCCC');
      
      if (Number(document.getElementById('unit_price' + i).value.replace(/,/g, ""))  < 0) {
        $(document.getElementById('unit_price' + i)).css({"color":"red"});
        $(document.getElementById('unit_price' + i)).css('border-color', 'red');
      } else {
        $(document.getElementById('unit_price' + i)).css({"color":"#333333"});
        $(document.getElementById('unit_price' + i)).css('border-color', '#CCCCCC');
      }

      document.getElementById('amount' + i).value = document.getElementById('amount' + (i+1)).value;
      document.getElementById('amount' + (i+1)).value = "";
      $(document.getElementById('amount' + (i+1))).css({"color":"#333333"});
      $(document.getElementById('amount' + (i+1))).css('border-color', '#CCCCCC');

      if (Number(document.getElementById('amount' + i).value.replace(/,/g, ""))  < 0) {
        $(document.getElementById('amount' + i)).css({"color":"red"});
        $(document.getElementById('amount' + i)).css('border-color', 'red');
      } else {
        $(document.getElementById('amount' + i)).css({"color":"#333333"});
        $(document.getElementById('amount' + i)).css('border-color', '#CCCCCC');
      }

      document.getElementById('remarks' + i).value = document.getElementById('remarks' + (i+1)).value;
      document.getElementById('remarks' + (i+1)).value = "";

      if (flg== 1) {
        $('#divid' + i).css('display', $( '#divid' + (i+1) ).css('display'));
        $('#divid' + (i+1)).css('display','none');

        $('#crossid' + i).css('display', $( '#crossid' + (i+1) ).css('display'));
        $('#crossid' + (i+1)).css('display','none');

        document.getElementById('emp_ID' + i).value = document.getElementById('emp_ID' + (i+1)).value;
        document.getElementById('emp_ID' + (i+1)).value = "";
        
        document.getElementById('empKanaNames' + i).innerHTML = document.getElementById('empKanaNames' + (i+1)).innerHTML;
        document.getElementById('empKanaNames' + (i+1)).innerHTML = "";
      }

      document.getElementById('fordisable_hdn' + i).value = document.getElementById('fordisable_hdn' + (i+1)).value;
      document.getElementById('fordisable_hdn' + (i+1)).value = 0;
    }
    fnControlAddOrRemove(rowCount,invflg);
    fnCalculateTotal();
    toenavledisable();
  }
function fnRemoveZero(fname) {
  var getvalue = document.getElementById(fname);
  if (getvalue.value.trim() == 0) {
    getvalue.value = '';
    getvalue.focus();
    getvalue.select();
  }
}
function fnSetZero11(fid) {
  var getvalue = document.getElementById(fid);
  if (getvalue.value.trim() == "") {
    getvalue.value = 0;
  }
}
function blockSpecialChar(e){
  var k;
  document.all ? k = e.keyCode : k = e.which;
  return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
}