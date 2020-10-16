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
    function  popupclose(){
      $('#txt_branchname').val('');
      $('#txt_branchnumber').val('');
      $('#brchpopup').css( "background-color", "hsl(120, 39%, 54%)" );  
      $("#brchpopup").removeAttr("disabled"); 
      document.getElementById('txt_bankname').value=$('#nameval').val();
      document.getElementById('branchid').value=$('#bid').val();
      document.getElementById('bankuid').value=$('#bid').val();
    }
     function divpopclose() {
        if (confirm(cancel_msg)) {
            //$( "body div" ).removeClass( "modalOverlay" );
            $( '#banknamepopup' ).empty();
            $('#banknamepopup').modal('toggle');
        } else {
            return false;
        }
    }
  </script>
<style>
    .scrollbar {
      overflow-y: auto;
      padding: 4px;
      height: 90%;
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
      top: 49% !important;
      left: 50%;
      transform: translate(-50%, -50%);
   }
  </style>
                {{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
                {{ Form::hidden('loc', $request->loc , array('id' => 'loc')) }}
<div class="modal-content">
  <div class="modal-header">
        <div class="col-md-2 pull-right">
          <button type="button" onclick="divpopclose();" class="close fr" aria-hidden="true">&#10006;</button>
        </div> 
        <div class="box80per mr10 h35.1">
            <h2 class="fs30 mt5">{{ trans('messages.lbl_bankdetail') }}<span class="">・</span>@if ($request->loc==1)<span class="colbl">{{ trans('messages.lbl_india') }}</span>@elseif ($request->loc==2)<span class="colbl">{{ trans('messages.lbl_japan') }}</span>@endif</h2>
        </div>
  </div>      
        <input type="hidden" name="nameval" id="nameval">
        <input type="hidden" name="bid" id="bid">
        <div>
          <button id="select" data-dismiss="modal" onclick="popupclose();" class="btn CMN_display_block box100 flr mr10 white bg_grey" disabled="disabled" style="cursor: pointer">Select</button>
        </div>
        <div>
        <div class="mt3 CMN_display_block box100per">
          <table id="swaptable" class=" table popuptoptable table-striped  table-bordered CMN_tblfixed box99per ml3">
             <colgroup>
              @if($request->loc==1)
               <col width="7%">
              @else
               <col width="10%">
              @endif
              @if($request->loc==1)
               <col width="5.2%">
              @else
               <col width="8.2%">
              @endif
               <col width="50%">
                 @if($request->loc==1)
                 @else
               <col width="">
                 @endif
            </colgroup>
             <thead class="CMN_tbltheadcolor h20">
                  <tr class="h10">
                  <th class="fs14 pb3 CMN_tbltheadcolor">{{ trans('messages.lbl_select') }}</th>
                  <th class="fs14 pb3 CMN_tbltheadcolor">{{ trans('messages.lbl_sno') }}</th>
                  <th class="fs14 pb3 CMN_tbltheadcolor">{{ trans('messages.lbl_bank_name') }}</th>
                  @if($request->loc==1)
                  @else
                  <th class="fs14 pb3 CMN_tbltheadcolor">{{ trans('messages.lbl_romaji') }}</th>
                  @endif
                  </tr>
               </thead>
            </table>
        </div>
        <div class="table_Scroll_limit_set ml11 CMN_display_block box98per bdg" style="margin-top: -6px;">
                <table id="swaptable" class="table table-striped table-bordered CMN_tblfixed">
                   <colgroup>
                     @if($request->loc==1)
                       <col width="8%">
                      @else
                       <col width="6.5%">
                      @endif
                       <col width="7.2%">
                       <col width="">
                      @if($request->loc==1)
                      @else
                      <col width="36.1%">
                      @endif
                  </colgroup>
                  <tbody class="box100per h35">
                     @php ($i = 1)
                      @forelse($details as $count => $data)
                        <tr class="h37" style="border: 1px solid red;">
                           <td class="text-center">
                                      <input type="radio" name="selectradio" id="selectradio" class="h13 w13" onclick="enableselect('{{ $data->id }}','{{ $data->BankName }}');">     
                           </td>
                           <td class="text-center pt7">{{ $i++ }}</td>
                           <td class="pl5 pt7">{{ $data->BankName }}</td>
                            @if($request->loc==1)
                            @else
                            <td class="pl5 pt7">{{ $data->romaji }}</td>
                            @endif
                        </tr>
                         @empty
                        <tr>
                           <td class="text-center red" colspan="9"> No Data Found</td>
                        </tr>
                     @endforelse
                  </tbody>
                </table>
          </div>
          <div>
            <fieldset class="h50 box99per ml3">
              <div class="col-md-12 mt10">
                <div class="col-md-4 text-right">
                    <label class="clr_blue">{{ trans('messages.lbl_bank_location') }}<span class="fr ml12">  </span></label>
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
                     @if($request->loc ==1)
                      <div class="col-md-4 text-right mb10">
                     @else
                       <div class="col-md-4 text-right">
                     @endif  
                        <label class="clr_blue mt5">{{ trans('messages.lbl_bank_name') }}<span class="fr ml2 red"> * </span></label>
                    </div>
                    <div class="col-md-8">
                    @if($request->loc ==2)
                               {{ Form::text('bnkname','',array('id'=>'bnkname', 'name' => 'bnkname','class'=>'box40per form-control ime_mode_active','maxlength' => 40,'onchange' => 'return fnCancel_check();')) }}
                          @else
                             {{ Form::text('bnkname','',array('id'=>'bnkname', 'name' => 'bnkname','class'=>'box40per form-control ime_mode_disable','maxlength' => 40,'onchange' => 'return fnCancel_check();')) }}
                          @endif
                        <label id="empty_bnkname" class="registernamecolor display_none">
                            Please Enter The {{ trans('messages.lbl_bank_name') }}
                        </label>
                    </div>
                  </div>  
                  @if($request->loc ==2)
                 <div class="col-md-12 mt10 mb10">
                    <div class="col-md-4 text-right">
                        <label class="clr_blue">{{ trans('messages.lbl_romaji') }}<span class="fr ml2 red"> * </span></label>
                    </div>
                  <div class="col-md-8">
                           {{ Form::text('romaji','',array('id'=>'romaji', 'name' => 'romaji','class'=>'ime_mode_active box40per form-control','maxlength' => 12,'onchange' => 'return fnCancel_check();')) }}
                      <label id="empty_romname" class="mr20 registernamecolor display_none">
                            Please Enter The {{ trans('messages.lbl_romaji') }} 
                        </label>
                  </div>
                </div>
                @endif
              </div>
            </fieldset>
          </div>
          <div class="modal-footer">
            <div class="bg-info">
               <center>
                 <button  id="regbutton" type="submit" onclick="return addeditvalidationa('{{ $request->loc  }}','{{ $request->mainmenu }}');" class="btn btn-success CMN_display_block box100 mt10 mb10" >
                       <i class="fa fa-plus"></i> {{ trans('messages.lbl_add') }}</button>
                 <button type="button" onclick="divpopclose();" class="btn btn-danger CMN_display_block box100 button mt10 mb10" ><i class="fa fa-times" aria-hidden="true"></i> {{ trans('messages.lbl_cancel') }}</button>
               </center>
            </div>   
          </div>
        </div>
</div>