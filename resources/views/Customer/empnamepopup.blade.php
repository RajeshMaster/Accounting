{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::script('resources/assets/js/customer.js') }}
<script type="text/javascript">
    $(document).ready(function() {
      setDatePicker("txt_start_date");
      setDatePicker("txt_end_date");
      setDatePicker("txt_clientdt");

      $('.empaddeditprocess').click(function () {
        $("#frmempnameedit").validate({
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
                newemployeename: {required: true},
                txt_start_date: {required: true,date:true,correctformatdate: true},
                txt_end_date: {required: true,date:true,correctformatdate: true, greaterThanStartdate: "#txt_start_date"},
                txt_clientdt: {required: true},
                newemployee: {required: true},
            },
            submitHandler: function(form) { // for demo
                if($('#frmempnameedit #editid').val() == 1) {
                    var confirmprocess = confirm(err_confreg);
                } else {
                    var confirmprocess = confirm(err_confup);
                }
                 if(confirmprocess) {
                    pageload();
                   // $('#otamount').attr('disabled', false);
                    //$('#otamount').disabled=false;
                    //document.getElementById('otamount').disabled = false;
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
    var datetime = '<?php echo date('Ymdhis'); ?>';
    function enableselect(bid,bname) {    
      $('#nameval').val(bname);
      $('#bid').val(bid);
      $("#select" ).css( "background-color", "orange" );
      $("#select" ).removeAttr("disabled");
  }
  function fnCancel_check() {
      cancel_check = false;
      return cancel_check;
  }
    
    function closefunction() {
        if (confirm(cancel_msg)) {
            $( "body div" ).removeClass( "modalOverlay" );
            $( '#empnamepopup' ).empty();
            $('#empnamepopup').modal('toggle');
        } else {
            return false;
        }
    }
    function test(){
      var status=document.getElementById('status').value;
      document.getElementById("newemployee").style.backgroundColor = "#D3D3D3";
      document.getElementById("newbranch").style.backgroundColor = "#D3D3D3";
    if(status == "3" ) {
      document.getElementById("clientdate").style.display = 'block';
      document.getElementById("newemployee").disabled = false;
      document.getElementById("newbranch").disabled = false;
      document.getElementById("newemployee").classList.remove('bg_lightgrey');
      document.getElementById("newbranch").classList.remove('bg_lightgrey');
      document.getElementById("newemployee").style.backgroundColor = "#FFFFFF";
      document.getElementById("newbranch").style.backgroundColor = "#FFFFFF";
      document.getElementById("newemployee").style.border = "2px solid #2693DD";
      document.getElementById("newbranch").style.border = "2px solid #2693DD";
      document.getElementById("newemp").style.visibility='visible';
    } else {
      document.getElementById("clientdate").style.display = 'none';
      document.getElementById("newemployee").disabled = true;
      document.getElementById("newbranch").disabled = true;
      document.getElementById("newemployee").style.backgroundColor = "#D3D3D3";
      document.getElementById("newbranch").style.backgroundColor = "#D3D3D3";
      document.getElementById("newemp").style.visibility='hidden';
    }
  }
    
  //   function addeditvalidationa(id){
  //     if(id == 1){
  //     var newemp=$("#newemployeename").val();
  //     var startdate=$("#txt_start_date").val();
  //   } else {
  //     var startdate=$("#txt_start_date").val();
  //     var enddate=$("#txt_end_date").val();
  //     var status=$("#status").val();
  //     var clientdt=$("#txt_clientdt").val();
  //     var newmpl=$("#newemployee").val();
  //   }
  //     if (newemp == "") {         
  //        alert("Please Select The EmployeeName");
  //        document.getElementById('newemployeename').focus();
  //        return false;
  //     } else if (startdate == "") {       
  //        alert("Start Date is NotEntered");
  //        document.getElementById('txt_start_date').focus();
  //        return false;
  //     } else if(enddate == "") {         
  //         alert("End Date is NotEntered");
  //         document.getElementById('txt_end_date').focus();
  //        return false;
  //      } else if ((!isEmpty(enddate)) && (Date.parse(startdate) > Date.parse(enddate))) {
  //         alert("Please Enter Valid End Date");
  //         document.getElementById('txt_end_date').focus();
  //         return false;
  //     }  else if ((status == "3") && (isEmpty(clientdt))) {
  //         alert("Please Enter Clientdate");
  //         document.getElementById('txt_clientdt').focus();
  //         document.getElementById('txt_clientdt').select();
  //         return false;
  //       } else {
  //       if(id == 1)
  //       {
  //          var Emp_selection = "Do You Want to Register the details?";
  //       } else {
  //           var Emp_selection = "Do You Want to Update the details?";
  //       }
  //        if(confirm(Emp_selection)) {
  //         return true;
  //        } else {
  //          return false;
  //        }
  //     }       
  // }
  </script>
<style>
    .scrollbar {
      overflow-y: auto;
      padding: 5px;
      height: 100%;
      width:100%;
      max-width: 100%;
      margin-bottom: 0px;
    }
    .table_Scroll_limit_set{
      overflow-y: scroll;
      overflow-x: hidden;
      height: 225px;
      background-color: #FFFFFF;
    }
    .modal {
      position: fixed;
      top: 50% !important;
      left: 50%;
      transform: translate(-50%, -50%);
   }
  </style>
   @if(!empty($cemployeeview))
        {{ Form::model($cemployeeview,array('name'=>'frmempnameedit','method' => 'POST',
                                         'class'=>'form-horizontal',
                                         'id'=>'frmempnameedit', 
                                         'url' => 'Customer/empnamepopupeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'))) }}
            {{ Form::hidden('custid',$request->custid,array('id' => 'custid')) }}
            {{ Form::hidden('id',$request->id,array('id' => 'custid')) }}
            {{ Form::hidden('empidd',$request->employeeid,array('id' => 'empidd')) }}
            {{ Form::hidden('selectionid','',array('id' => 'selectionid')) }} 
    @else
        {{ Form::open(array('name'=>'frmempnameedit', 
            'id'=>'frmempnameedit', 
            'url' => 'Customer/empnamepopupeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
            'files'=>true,
            'method' => 'POST')) }}
            {{ Form::hidden('custid',$request->custid,array('id' => 'custid')) }}
           
    @endif
            {{ Form::hidden('id',$request->id,array('id' => 'id')) }}
            {{ Form::hidden('empidd',$request->employeeid,array('id' => 'empidd')) }}
            {{ Form::hidden('selectionid','',array('id' => 'selectionid')) }}
            {{ Form::hidden('editid',$request->selectionid,array('id' => 'editid')) }}
      <div class="modal-content">
          <div class="modal-header">
            <div class="col-md-2 pull-right mt15">
                <button type="button" onclick="closefunction();" class="close fr" aria-hidden="true">&#10006;</button>
            </div>
              <div class="box70per ml10 h30 CMN_display_block mb5">
                <h2 class="fs30 mt5" style="margin-top: -9px;">{{ trans('messages.lbl_employeenameselection') }}</h2>
              </div>
        </div>
        <fieldset class="box98per ml5"> 
          <div class="box98per ml10">
            <div class="col-md-12 mt10">
                <div class="col-md-4 text-right">
                    <label class="clr_blue">{{ trans('messages.lbl_custname(JP & Eng)') }}<span class="fr ml2" style="visibility: hidden;"> * </span></label>
                </div>
                <div>
                    <label class="fwb mr10">
                         @if(isset($employeedetail[0]->customer_name))
                          {{ $employeedetail[0]->customer_name}}
                         @else
                          {{ "NILL"}}
                         @endif 
                    </label>
                </div>
               </div> 
               <div class="col-md-12 mt10">
                <div class="col-md-4 text-right">
                    <label class="clr_blue">{{ trans('messages.lbl_branch_name') }}<span class="fr ml2" style="visibility: hidden;"> * </span></label>
                </div>
               @if($request->selectionid !=1)
                <div>
                    <label class="fwb">
                          @if(isset($cemployeeview[0]['branch_name']))
                            {{ $cemployeeview[0]['branch_name']}}
                          @else
                            {{ "NILL"}}
                       @endif
                    </label>
                </div>
               @else
                <div>
                  {{ Form::select('newbranches',[null=>'']+$bname, null,array('name' => 'newbranches','id'=>'newbranches','data-label' => trans('messages.lbl_branch_name'),'style' => 'min-width:80px;', 'selected'))}}
                    {{ Form::hidden('hidebranchname','', array('id' => 'hidebranchname')) }}
                </div>
                @endif
               </div> 
               <div class="col-md-12 mt10">
                <div class="col-md-4 text-right">
                    <label class="clr_blue">{{ trans('messages.lbl_empName') }}<span class="fr ml2"> * </span></label>
                </div>
                 @if($request->selectionid !=1)
                <div>
                    <label class="fwb">
                      @if(isset($cemployeeview[0]['LastName']))
                        {{ $cemployeeview[0]['LastName']}}
                       @else
                        {{ "NILL"}}
                       @endif 
                    </label>
                     <label class="fwb ml5 colbl">
                      @if(isset($cemployeeview[0]['Emp_ID']))
                        {{ $cemployeeview[0]['Emp_ID']}}
                       @else
                        {{ "NILL"}}
                       @endif 
                    </label>
                </div>
                @else
                <div>
                    {{ Form::select('newemployeename',[null=>'']+$empname1, null,array('name' => 'newemployeename','id'=>'newemployeename','data-label' => trans('messages.lbl_empName'),'style' => 'min-width:80px;','onselect'=>'return valchange()','onchange'=>'return valchange()'))}}
                    <span class="pl10 fwb" style="color: blue;" id="empno"></span>
                </div>
                @endif
               </div> 
               <div class="col-md-12 mt10">
                <div class="col-md-4 text-right">
                    <label class="clr_blue">{{ trans('messages.lbl_Start_date') }}<span class="fr ml2" > * </span></label>
                </div>
                <div>
                       {{ Form::text('txt_start_date',(isset($cemployeeview[0]['start_date'])) ? $cemployeeview[0]['start_date'] : '',array(
                                        'id'=>'txt_start_date',
                                        'name' => 'txt_start_date',
                                        'class'=>'box13per txt_start_date',
                                        'data-label' => trans('messages.lbl_Start_date'),
                                        'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
                                        'maxlength' => '10')) }}
                    <label class="mt10 ml2 fa fa-calendar fa-lg" 
                                    for="txt_start_date" aria-hidden="true" style="display: inline-block!important;">
                    </label>
                </div>
               </div> 
               @if($request->selectionid !=1)
               <div class="col-md-12 mt5">
                <div class="col-md-4 text-right">
                    <label class="clr_blue">{{ trans('messages.lbl_enddate') }}<span class="fr ml2" > * </span></label>
                </div>
                <div >
                        {{ Form::text('txt_end_date',(isset($cemployeeview[0]['end_date'])) ? $cemployeeview[0]['end_date'] : '',array(
                                        'id'=>'txt_end_date',
                                        'name' => 'txt_end_date',
                                        'class'=>'box13per txt_end_date',
                                        'data-label' => trans('messages.lbl_enddate'),
                                         'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
                                        'maxlength' => '10')) }}
                    <label class="mt10 ml2 fa fa-calendar fa-lg" 
                                    for="txt_end_date" aria-hidden="true" style="display: inline-block!important;">
                    </label>
                </div>
               </div> 
               @else

               @endif
                @if($request->selectionid !=1)
               <div class="col-md-12 mt5">
                <div class="col-md-4 text-right">
                    <label class="clr_blue">{{ trans('messages.lbl_status') }}<span class="fr ml2"> * </span></label>
                </div>
                <div>
                    {{ Form::select('status',$clientstatus, (isset($cemployeeview[0]['status'])) ? $cemployeeview[0]['status'] : '',array('name' => 'status','id'=>'status','style' => 'min-width:80px;','onchange' => 'test(this)'))}}
                </div>
               </div>
               @else

               @endif
               @if($request->selectionid !=1)
               <div class="col-md-12 mt5" style="display: none;" id="clientdate">
                <div class="col-md-4 text-right">
                    <label class="clr_blue">{{ trans('messages.lbl_newclientstartdate') }}<span class="fr ml2"> * </span></label>
                </div>
                <div>
                    {{ Form::text('txt_clientdt','',array(
                                        'id'=>'txt_clientdt',
                                        'name' => 'txt_clientdt',
                                        'class'=>'box13per txt_clientdt',
                                        'data-label' => trans('messages.lbl_newclientstartdate'),
                                        'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
                                        'maxlength' => '10')) }}
                    <label class="mt10 ml2 fa fa-calendar fa-lg" 
                                    for="txt_clientdt" aria-hidden="true" style="display: inline-block!important;">
                    </label>
                </div>
               </div>
               @else

               @endif
               @if($request->selectionid !=1)
               <div class="col-md-12 mt5">
                <div class="col-md-4 text-right">
                    <label class="clr_blue">{{ trans('messages.lbl_newemployees') }}<span class="fr ml2" id="newemp" style="visibility: hidden;"> * </span></label>
                </div>
                <div>
                    {{ Form::select('newemployee',[null=>'']+$empname, null,array('name' => 'newemployee','style' => 'min-width:80px;','id'=>'newemployee','class' => 'bg_lightgrey', 'disabled', 'selected', 'data-label' => trans('messages.lbl_newemployees'),
                              'onchange' => 'return fnGetBrachByAjax(this.id)'))}}
                </div>
               </div>
               @else

               @endif
               @if($request->selectionid !=1)
               <div class="col-md-12 mt5">
                <div class="col-md-4 text-right">
                    <label class="clr_blue">{{ trans('messages.lbl_newbranchname') }}<span class="fr ml2" style="visibility: hidden;"> * </span></label>
                </div>
                <div>
                    {{ Form::select('newbranch',[null=>''], null,array('name' => 'newbranch','style' => 'min-width:80px;','id'=>'newbranch','class'=>'bg_lightgrey','disabled', 'selected'))}}
                    {{ Form::hidden('hidebranchname','', array('id' => 'hidebranchname')) }}
                </div>
               </div>
               @else

               @endif
               <div class="col-md-12  mt10"></div>
              </div> 
              </fieldset>
               @if($request->selectionid !=1)
               <div class="modal-footer" style="border: none !important;">
                <div class="bg-info">
                 <center>
                   <button  id="updatebutton" type="submit" class="btn btn-warning CMN_display_block box100 mt15 mb15 empaddeditprocess" >
                         <i class="fa fa-edit"></i> {{ trans('messages.lbl_update') }}</button>
                   <button type="button" onclick="closefunction();" class="btn btn-danger CMN_display_block box100 button mt15 mb15" ><i class="fa fa-times" aria-hidden="true"></i> {{ trans('messages.lbl_cancel') }}</button>
                 </center>
               </div>
              </div> 
              @else
               <div class="modal-footer" style="border: none !important;">
                <div class="bg-info">
                 <center>
                   <button  id="regbutton" type="submit" class="btn btn-success CMN_display_block box100 mt15 mb15 empaddeditprocess" >
                         <i class="fa fa-plus"></i> {{ trans('messages.lbl_register') }}</button>
                   <button type="button" onclick="closefunction();" class="btn btn-danger CMN_display_block box100 button mt15 mb15" ><i class="fa fa-times" aria-hidden="true"></i> {{ trans('messages.lbl_cancel') }}</button>
                 </center>
                 </div>
              </div> 
              @endif
           </div> 
          </div>
          {{ Form::close() }}