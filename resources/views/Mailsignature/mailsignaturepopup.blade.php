<style>
  .highlight { background-color: #428eab !important; }
   .modal {
      position: fixed;
      top: 50% !important;
      left: 50%;
      transform: translate(-50%, -50%);
   }
   input[type=radio] {
    margin: 0px !important;
   }
</style>
{{ HTML::script('resources/assets/js/mailsignature.js') }}
   <div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal" style="color: red;" aria-hidden="true">&#10006;</button>
         <h3 class="modal-title custom_align"><B>{{ trans('messages.lbl_nameselection') }}</B></h3>
      </div>
      <div class="modal-body" style="height: 310px;overflow-y: scroll;width: 100%;">
         {{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
         <table id="data" class="tablealternate box100per" style="height: 40px;">
            <colgroup>
            <col width="6%">
            <col width="8%">
            <col width="15%">
            <col width="">
          </colgroup>
          <thead class="CMN_tbltheadcolor">
            <tr class="tableheader fwb tac"> 
              <th class="tac"></th>
              <th class="tac">{{ trans('messages.lbl_sno') }}</th>
              <th class="tac">{{ trans('messages.lbl_UserID') }}</th>
              <th class="tac">{{ trans('messages.lbl_user') }} {{ trans('messages.lbl_name') }} </th>
            </tr>
          </thead>
           <tbody id="search" class="staff">
            <?php $i=0; ?>
             @forelse($empname as $key => $value)
                <tr id="popup" ondblclick="fndbclick('<?php echo $value->usercode; ?>','<?php echo $value->username; ?>',
                '<?php echo $value->givenname; ?>','<?php echo $value->nickName; ?>');"  
                  onclick="fngetData('<?php echo $value->usercode; ?>','<?php echo $value->username; ?>',
                '<?php echo $value->givenname; ?>','<?php echo $value->nickName; ?>');"
                    >
                  <td align="center">
                    <input  type="radio" id="<?php echo $value->usercode; ?>" name="empid" onclick="fngetempid(this.id,this.value);">
                  </td>
                  <td align="center">
                    {{ $i + 1 }}
                  </td>
                  <td align="center">
                    {{ $value->usercode }}
                  </td>
                  <td>
                    {{ $value->username }} {{ $value->givenname }} 
                    @if($value->nickName != "")
                      ({{ $value->nickName }})
                    @else 
                    @endif 
                  </td>
                </tr>
                <?php $i++; ?>
            @empty
              <tr>
                <td class="text-center" colspan="4" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
              </tr>
             @endforelse
           </tbody>
         </table>
         </div>
   <div class="modal-footer bg-info mt10">
      <center>
         <button id="add" onclick="javascript:fnselect();" class="btn btn-success CMN_display_block box100">
            <i class="fa fa-plus" aria-hidden="true"></i>
               {{ trans('messages.lbl_select') }}
         </button>
         <button data-dismiss="modal" onclick="javascript:fnclose();" class="btn btn-danger CMN_display_block box100">
            <i class="fa fa-times" aria-hidden="true"></i>
               {{ trans('messages.lbl_cancel') }}
         </button>
         <!-- onclick="javascript:return cancelpopupclick();" -->
      </center>
   </div>
      </div>
   </div>