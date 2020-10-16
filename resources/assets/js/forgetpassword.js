var data = {};
 $(document).ready(function() {
    $('.addeditprocess').on('click', function() {
    $(":submit").attr("disabled", true);
    resetErrors();
      var url = 'formValidation';
      $.each($('form input'), function(i, v) {
            if (v.type !== 'submit') {
                data[v.name] = v.value;
            }
        }); //end each
      $.ajax({
          dataType: 'json',
          type: 'POST',
          url: url,
          data: data,
          success: function(resp) {
              if (resp === true) {
                var conf =true;
                $(":submit").attr("disabled", false);
                if (conf == true) {
                    var msg =$("#msg").val();
                    if(confirm("Do You Want To Change The Password")) {
                        $(".add").attr("disabled", false);
                        $("#forgetpassfrm").submit();
                    } else {
                        $(":submit").attr("disabled", false);
                    }
                }
              } else {
                  $.each(resp, function(i, v) {
                    // alert(i + " => " + v); // view in console for error messages
                    var msg = '<label class="error pl5" style="color:#9C0000;" for="'+i+'">'+v+'</label>';
                    // if ($('input[name="' + i + '"]').hasClass('email')) {
                    //     $('input[name="' + i + '"]').addClass('inputTxtError');
                    //     $('.email_err').append(msg)
                    // } else {
                        $('input[name="' + i + '"]').addClass('inputTxtError').after(msg);
                    // }
                    // }
                  });
                  $(":submit").attr("disabled", false);
                  var keys = Object.keys(resp);
                  $('input[name="'+keys[0]+'"]').focus();
              }
              return false;
          },
          error: function(data) {
               alert(data.status);
              alert('there was a problem checking the fields');
          }
      });
      return false;
    });
    //
 });
 function resetErrors() {
    $('form input').removeClass('inputTxtError');
    $('label.error').remove();
}
function changelanguage() {
    var confm = confirm("Do You Want To Change The Language?.");
    if (!confm) {
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
function cancel() {
  window.history.back();
}
/*function cancel() {
    if (cancel_check == false) {
        if (confirm(cancel_msg)) {
            pageload();
            var mainmenu=document.getElementById('mainmenu').value;
            $('#nippoaddedit').attr('action', viewflg+'?mainmenu='+mainmenu+'&time='+datetime);
            $("#nippoaddedit").submit();
        }
    } else {
        pageload();
        var mainmenu=document.getElementById('mainmenu').value;
        $('#nippoaddedit').attr('action', viewflg+'?mainmenu='+mainmenu+'&time='+datetime);
        $("#nippoaddedit").submit();
    }
}*/