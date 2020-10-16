<style>
   .modal {
      position: fixed;
      top: 50% !important;
      left: 50%;
      transform: translate(-50%, -50%);
   }
</style>
   <div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal" style="color: red;" aria-hidden="true">&#10006;</button>
         <h3 class="modal-title custom_align"><B>{{ trans('messages.lbl_notices') }}</B></h3>
      </div>
      <div class="modal-body">
         {{ Form::hidden('cnt',$request->cnt, array('id' => 'cnt')) }}
         {{ Form::hidden('noticeid', '', array('id' => 'noticeid')) }}
         {{ Form::hidden('noticetxt', '', array('id' => 'noticetxt')) }}
                  {{ Form::select('noticesel',$notice,'', 
                           array('name' => 'noticesel',
                                'id'=>'noticesel',
                                'size'=>'10',
                                'style'=>'min-width: 639px; min-height: 180px;',
                                'onclick' => "",
                                'ondblclick' => "return fnaddnoticeid();",
                                'onchange' => 'return fngetnoticeid(this.value,this.text)',
                                'class'=>'pl5'))}}
      
   <div class="modal-footer bg-info mt10">
      <center>
         <button id="add" onclick="return fnaddnoticeid();" class="btn btn-success CMN_display_block box100">
            <i class="fa fa-plus" aria-hidden="true"></i>
               {{ trans('messages.lbl_add') }}
         </button>
         <button data-dismiss="modal" class="btn btn-danger CMN_display_block box100">
            <i class="fa fa-times" aria-hidden="true"></i>
               {{ trans('messages.lbl_cancel') }}
         </button>
         <!-- onclick="javascript:return cancelpopupclick();" -->
      </center>
   </div>
      </div>
   </div>