<style>
   .modal {
      position: fixed;
      top: 50% !important;
      left: 50%;
      transform: translate(-50%, -50%);
   }
</style>
   <div class="modal-content">
      <div class="modal-header" style="padding:8px;">
         <button type="button" class="close" data-dismiss="modal" style="color: red;" aria-hidden="true">&#10006;</button>
         <h3 class="modal-title custom_align"><B>{{ trans('messages.lbl_coverletter') }}</B></h3>
      </div>
      <div class="modal-body">
         <div class="form-group mt5" style="min-height: 75px;">
            <div class="col-md-4 tar pm0 ml90">
                 <label class="clr_blue">{{ trans('messages.lbl_estimation') }}</label>
            </div>
            <div class="col-md-4 pm0 ml10">
                  {{ Form::text('estcnt','0', array('id' => 'estcnt','onkeypress'=>'return event.charCode >=6 && event.charCode <=58','onblur' => 'return fnSetZero11(this.id)','onfocus' => 'return fnRemoveZero(this.id)','class' => 'box40 pl5','style' => 'min-width: 150px;')) }}
            </div>
            <div class="col-md-4 tar pm0 ml90 mt7">
                 <label class="clr_blue">{{ trans('messages.lbl_invoice') }}</label>
            </div>
            <div class="col-md-4 pm0 ml10 mt7">
                  {{ Form::text('invcnt','0', array('id' => 'invcnt','onkeypress'=>'return event.charCode >=6 && event.charCode <=58','onblur' => 'return fnSetZero11(this.id)','onfocus' => 'return fnRemoveZero(this.id)','class' => 'box40 pl5','style' => 'min-width: 150px;')) }}
            </div>
         </div>
   
   <div class="modal-footer bg-info mt3">
      <center>
         <button id="add" onclick="return fncoverdownload();" class="btn btn-success CMN_display_block box100"><i class="fa fa-download" aria-hidden="true"></i> {{ trans('messages.lbl_download') }}
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