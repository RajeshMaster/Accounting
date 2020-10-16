{{ HTML::script('resources/assets/js/customer.js') }}
<script type="text/javascript">
    function closefunction() {
        if (confirm(cancel_msg)) {
            $( "body div" ).removeClass( "modalOverlay" );
            $( '#coverletterpopup' ).empty();
            $('#coverletterpopup').modal('toggle');
        } else {
            return false;
        }
    }
    function filevalidate(){
    var ext = $('#letter').val().split('.').pop().toLowerCase();
    if ($('#letter').val()!="") {
      var size = parseFloat($("#letter")[0].files[0].size / 1024);  
    }
    if($('#letter').val()==""){
      $("#empty_file").show();
      return false;
    }else if($.inArray(ext, ['xlsx','xls']) == -1) {
       $("#file_ext").show();
       $("#file_size").hide();
       $("#empty_file").hide();
       return false;
    }else if(size > "2097") {
       $("#file_ext").hide();
       $("#empty_file").hide();
       $("#file_size").show();
       return false;
    }else{
      if($('#letter').val() != ""){
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
  {{ Form::open(array('name'=>'letterupload', 'id'=>'letterupload', 'files'=>true,
                        'url' => 'Customer/letterupload?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
                        'method' => 'POST')) }}
    {{ Form::hidden('mainmenu',$request->mainmenu, array('id' => 'mainmenu')) }}
     {{ Form::hidden('custid',$request->custid, array('id' => 'custid')) }}
      {{ Form::hidden('id',$request->id, array('id' => 'id')) }}
      <div class="modal-content">
        <div class="modal-header">
            <div class="col-md-2 pull-right mt15">
                <button type="button" onclick="closefunction();" class="close fr" aria-hidden="true">&#10006;</button>
            </div>
              <div class="box70per ml10 h35 CMN_display_block mb5">
                <h2 class="fs30 mt5" style="margin-top: -5px;">{{ $employeedetail[0]->customer_name }}</h2>
              </div>
            </div>
            <div class="modal-body">
            <fieldset class="box98per ml5"> 
              <div class="box98per ml10">
                <div class="col-md-10 mt10">
                    <div class="col-md-5 text-right">
                        <label class="clr_blue">{{ trans('messages.lbl_coveringletter') }}<span class="fr ml2"> * </span></label>
                    </div>
                     <div class="box58per pl10" style="display:inline-block;">
                         {{ Form::file('letter', ['class' => 'field box90per','id'=>'letter']) }}
                    </div>
                    <div class="box100per ml60 mt5" style="display: inline-block;text-align: right;">
                        <label class="clr_blue " style="padding-right:12px;"> {{ trans('messages.lbl_filesize') }} </label>
                    </div>
                    <label class="pl143" id="empty_file" style="display: none;color:#9C0000;">
                        {{ trans('messages.lbl_uploadfilepls') }}
                    </label>
                    <label class="pl143" id="file_ext" style="display: none;color:#9C0000;">
                         {{ trans('messages.lbl_uploadvalidfile') }}
                    </label>
                    <label class="pl143" id="file_size" style="display: none;color:#9C0000;">
                          {{ trans('messages.lbl_files') }}
                    </label>
                 </div>
             </fieldset>    
       <div class="modal-footer bg-info">
         <center>
           <button  id="regbutton" type="submit" onclick="return filevalidate();" class="btn btn-success CMN_display_block box100 mt1 " >
                 <i class="fa fa-plus"></i> {{ trans('messages.lbl_register') }} </button>
           <button type="button" onclick="closefunction();" class="btn btn-danger CMN_display_block box100 button mt1" ><i class="fa fa-times" aria-hidden="true"></i>  {{ trans('messages.lbl_cancel') }}</button>
         </center>
      </div>
      </div> 
   </div> 
{{ Form::close() }}