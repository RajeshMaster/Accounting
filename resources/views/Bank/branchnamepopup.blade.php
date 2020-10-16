  <script type="text/javascript">
  $(document).ready(function() {
        $('#swaptable tr').click(function(event) {
              if (event.target.type !== 'radio') {
                if (event.target.nodeName != "SPAN") {
                  $(':radio', this).trigger('click');
                }
             }
        });
      });
   function enablebtn(bid,branchnumber,id) {
      $('#branchval').val(bid);
      $('#branchid').val(id);
      $('#branchnumber').val(branchnumber);
      $("#selectbtn" ).css( "background-color", "orange" );
      $("#selectbtn" ).removeAttr("disabled");
  }
  function closefunction() {
        if (confirm(cancel_msg)) {
            // $( "body div" ).removeClass( "modalOverlay" );
            $( '#branchnamepopup' ).empty();
            $('#branchnamepopup').modal('toggle');
        } else {
            return false;
        }
    }
    function  closepopup(){
      document.getElementById('txt_branchname').value=$('#branchval').val();
      document.getElementById('txt_branchnumber').value=$('#branchnumber').val();
    }
    function divpopclose() {
        if (confirm(cancel_msg)) {
            //$( "body div" ).removeClass( "modalOverlay" );
            $( '#branchnamepopup' ).empty();
            $('#branchnamepopup').modal('toggle');
        } else {
            return false;
        }
    }
   </script>   
 <style>
    .scrollbar {
      overflow-y: auto;
      padding: 4px;
      height: 80%;
      width:100%;
      max-width: 100%;
      margin-bottom: 0px;
    }
    .table_Scroll_limit_set{
      overflow-y: scroll;
      overflow-x: hidden;
      height: 220px;
      background-color: #FFFFFF;
    }
    .modal {
      position: fixed;
      top: 49% !important;
      left: 50%;
      transform: translate(-50%, -50%);
   }
  </style>
            {{ Form::hidden('loc', $request->loc , array('id' => 'loc')) }}
            {{ Form::hidden('bankuid', $request->id , array('id' => 'bankid')) }}
            {{ Form::hidden('bnkname', $request->bname , array('id' => 'bnkname')) }}
      <div class="modal-content">
          <div class="modal-header">
            <div class="col-md-2 pull-right">
                <button type="button" onclick="closefunction();" class="close fr" aria-hidden="true">&#10006;</button>
            </div>
            <div class="box50per mr10 h30.5">
               <h2 class="fs30 mt5">{{ trans('messages.lbl_bankdetail') }}<span class="">・</span>@if ($request->loc==1)<span class="colbl">{{ trans('messages.lbl_india') }}</span>@elseif ($request->loc==2)<span class="colbl">{{ trans('messages.lbl_japan') }}</span>@endif</h2>
            </div>
           </div>
            <input type="hidden" name="branchval" id="branchval">
            <input type="hidden" name="bid" id="bid">
            <input type="hidden" name="branchnumber" id="branchnumber">
            <input type="hidden" name="branchid" id="branchid"> 
            <div>
              <button id="selectbtn" data-dismiss="modal" onclick="closepopup();" class="btn  CMN_display_block box100 flr white bg_grey mr10" disabled="disabled" style="cursor: pointer">{{ trans('messages.lbl_select') }}</button>
            </div>
            <div class="CMN_display_block box100per">
            <table id="swaptable" class=" ml10 table popuptoptable table-striped  table-bordered CMN_tblfixed box97per">
             <colgroup>
               <col width="4%">
               <col width="3.5%">
               <col width="19%">
               <col width="10%">
            </colgroup>
             <thead class="CMN_tbltheadcolor h30">
                  <tr class="h37">
                  <th class="fs14 pb3 CMN_tbltheadcolor">{{ trans('messages.lbl_select') }}</th>
                  <th class="fs14 pb3 CMN_tbltheadcolor">{{ trans('messages.lbl_sno') }}</th>
                  <th class="fs14 pb3 CMN_tbltheadcolor">{{ trans('messages.lbl_branch_name') }}</th>
                  <th class="fs14 pb3 CMN_tbltheadcolor">{{ trans('messages.lbl_branch_number') }}</th>
                  </tr>
               </thead>
            </table>
            </div>
            <div class="table_Scroll_limit_set box97per CMN_display_block bdg ml11" style="margin-top: -6px;">
                <table id="swaptable" class="table table-striped table-bordered CMN_tblfixed">
                   <colgroup>
                           <col width="4%">
                           <col width="4.5%">
                           <col width="25.2%" >
                           <col width="13%">
                        </colgroup>
                        <tbody class="box100per h35">
                         @php ($i = 1)
                          @forelse($details as $count => $data)
                            <tr class="h37">
                               <td class="text-center fs14">
                                  <input type="radio" name="selectradio" id="selectradio" class="h13 w13" onclick="enablebtn('{{ $data->BranchName }}','{{ $data->BranchNo }}','{{ $data->id }}');">     
                               </td>
                               <td class="text-center fs14 pt5">{{ $i++ }}</td>
                               <td class="pl10 fs14 pt5">
                               {{ $data->BranchName }}</td>
                                <td class="pl10 fs14 pt5">
                                {{ $data->BranchNo }}</td>
                            </tr>
                                @empty
                            <tr>
                               <td class="text-center red fs15" colspan="9"> No Data Found</td>
                            </tr>
                         @endforelse
                        </tbody>
                  </table>
              </div>
            <div class="box97per mt5 ml10">
            <fieldset>
              <div class="col-md-12 mt5">
                <div class="col-md-4 text-right">
                    <label class="clr_blue mt2">{{ trans('messages.lbl_bank_location') }}<span class="fr ml12">      </span></label>
                </div>
                <div class="col-md-8">
                    <label class="fwb">
                        @if($request->loc==1)
                          {{ trans('messages.lbl_india') }}
                        @else
                        {{ trans('messages.lbl_japan') }}
                        @endif
                    </label>
                </div>
               </div> 
                <div class="col-md-12 mt5">
                    <div class="col-md-4 text-right">
                        <label class="clr_blue mt2 ml20">{{ trans('messages.lbl_bank_name') }}<span class="fr ml2 red" style="visibility: hidden;"> * </span></label>
                    </div>
                    <div class="col-md-8">
                             {{ Form::text('bnkname',$bankname,array('id'=>'bnkname', 'name' => 'bnkname','class'=>'box40per form-control','maxlength' => 12,'readonly','readonly')) }}
                    </div>
                  </div>  
                <div class="col-md-12 mt5">
                    <div class="col-md-4 text-right">
                        <label class="clr_blue mt2">{{ trans('messages.lbl_branch_name') }}<span class="fr ml2 red"> * </span></label>
                    </div>
                    <div class="col-md-8">
                    @if($request->loc==1)
                             {{ Form::text('branchs','',array('id'=>'branchs', 'name' => 'branchs','class'=>'box40per form-control ime_mode_disable','maxlength' => 40)) }}
                        @else
                             {{ Form::text('branchs','',array('id'=>'branchs', 'name' => 'branchs','class'=>'box40per form-control ime_mode_active','maxlength' => 40)) }}
                        @endif
                        <label id="empty_branchname" class="registernamecolor display_none">
                            Please Enter The Branch Name
                        </label>
                    </div>
                  </div>  
               <div class="col-md-12 mt5 mb5">
                    <div class="col-md-4 text-right">
                        <label class="clr_blue mt2">{{ trans('messages.lbl_branch_number') }}. <span class="fr ml2 red"> * </span></label>
                    </div>
                  <div class="col-md-8">
                           {{ Form::text('bno','',array('id'=>'bno', 'name' => 'bno','class'=>'box40per form-control ime_mode_disable',
                                          'maxlength' => 12,'onkeypress' => 'return isNumberKey(event)')) }}
                       <label id="empty_bno" class="registernamecolor display_none">
                            Please Enter The Branch No
                        </label>
                  </div>
                </div>
            </fieldset>
            </div>
            <div class="modal-footer">
              <div class="bg-info" style="margin-top: -3%;">
               <center>
                 <button  id="regbutton" type="submit" onclick="return branchvalidation('{{ $request->mainmenu }}');" class="btn btn-success CMN_display_block box100 button mt10 mb10" >
                       <i class="fa fa-plus"></i> {{ trans('messages.lbl_add') }}</button>
                 <button type="button" onclick="divpopclose();" class="btn btn-danger CMN_display_block box100 button mt10 mb10" ><i class="fa fa-times" aria-hidden="true"></i> {{ trans('messages.lbl_cancel') }}</button>
               </center>
             </div>
           </div>
       </div>