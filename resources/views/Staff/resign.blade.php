{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
<style>
  .modal {
      position: fixed;
      top: 50% !important;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 35% !important;
   }
</style>
<script type="text/javascript">
    var datetime = '<?php echo date('Ymdhis'); ?>';
    $( document ).ready(function() {
      setDatePicker("txt_startdate");
      $('.addeditprocess').click(function () {
        $("#frmresign").validate({
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
            txt_date: {required: true, date: true},
          },
          submitHandler: function(form) { // for demo
              var confirmprocess = confirm("Do You Want To Register?");
             if(confirmprocess) {
              pageload();
               form.submit();
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
    function divpopclose() {
        if (confirm(cancel_msg)) {
            //$( "body div" ).removeClass( "modalOverlay" );
            $( '#resign' ).empty();
            $('#resign').modal('toggle');
        } else {
            return false;
        }
    }
</script>
{{ Form::open(array('name'=>'frmresign', 'id'=>'frmresign', 
                            'class' => 'form-horizontal',
                            'files'=>true,
                            'url' => 'Staff/resignadd?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'), 
                            'method' => 'POST')) }}
  {{ Form::hidden('viewid', $request->empid , array('id' => 'viewid')) }}
  {{ Form::hidden('resignid', '1' , array('id' => 'resignid')) }}
  {{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
  <div class="modal-content">
  <div class="modal-header pm0">
    <div class="col-xs-12 pm0">
      <div class="col-xs-2 pm0 pull-right mt15 mr10">
        <button type="button" onclick="divpopclose();" class="close" style="color: red !important;" aria-hidden="true">&#10006;</button>
      </div>
      <div class="col-xs-8 pm0 ml10">
        <h2 class="modal-title custom_align">{{ trans('messages.lbl_resign') }} {{ trans('messages.lbl_date') }}</h2>
      </div>
    </div>
  </div>
    <div class="col-md-12 mb10 mt10">
                <div class="col-md-4 text-right mt5">
                    <label class="clr_blue">{{ trans('messages.lbl_date') }}<span class="fr ml12">*</span></label>
                </div>
               <div class="col-md-8">
                  {{ Form::text('txt_date','',array(
                                    'id'=>'txt_date',
                                    'name' => 'txt_date',
                                    'class'=>'box40per form-control ime_mode_active txt_startdate',
                                    'maxlength' => 10,
                                    'data-label' => trans('messages.lbl_date'))) }}
                  <label class="mt10 ml2 fa fa-calendar fa-lg" for="txt_date" aria-hidden="true"></label>
                </div>
  </div>
  <div class="modal-footer bg-info" style="margin-top: 51px; width: 100%;">
    <center>
      <button type="submit" class="btn btn-success add box100 ml5 addeditprocess">
        <i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_add') }}
      </button>
      <button type="button" onclick="divpopclose();" class="btn btn-danger CMN_display_block box100 button" ><i class="fa fa-times" aria-hidden="true"></i>
       {{ trans('messages.lbl_cancel') }}</button>
    </center>
 </div>
</div>
{{ Form::close() }}
