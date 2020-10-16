<script type="text/javascript">
  $(document).ready(function() {
    $('#swaptable1').delegate('tr', 'click' , function(){
      if (event.target.type !== 'radio') {
        if (event.target.nodeName != "SPAN") {
          $(this).find('input[type=radio]').prop('checked', true).trigger("click");
        }
      }
    });
  });
  var datetime = '<?php echo date('Ymdhis'); ?>';
</script>
<style>
  .scrollbar {
    overflow-y: 220px;
    padding: 5px;
    height: 100%;
    width:100%;
    max-width: 100%;
    margin-bottom: 0px;
  }
  .table_Scroll_limit_set{
    overflow-y: scroll;
    overflow-x: hidden;
    height: 214px;
    background-color: #FFFFFF;
  }
  .alertboxalign {
	margin-bottom: -50px !important;
	}
	.alert {
	    display:inline-block !important;
	    height:30px !important;
	    padding:5px !important;
	}
	.modal {
      position: fixed;
      top: 50% !important;
      left: 50%;
      transform: translate(-50%, -50%);
   }
</style>
{{ Form::open(array('name'=>'settingform','action' => 'SettingController@index', 'method'=>'POST',
'files'=>true,'class' => '','id' => 'settingform')) }}
{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
{{ Form::hidden('location', $request->location , array('id' => 'location')) }}
{{ Form::hidden('tablename', $request->tablename , array('id' => 'tablename')) }}
{{ Form::hidden('type', '1', array('id' => 'type')) }}
{{ Form::hidden('process', '1', array('id' => 'process')) }}
{{ Form::hidden('hid_txtval', '', array('id' => 'hid_txtval')) }}
{{ Form::hidden('confirmflg', '', array('id' => 'confirmflg')) }}
{{ Form::hidden('flag', '', array('id' => 'flag')) }}
{{ Form::hidden('flashmessage', '', array('id' => 'flashmessage')) }}
  <div class="modal-content">
    <div class="modal-header">
      <div class="box98per ml5">
        <div class="col-md-2 pull-right mt10">
           <a data-dismiss="modal" onclick="fnclosepopdig();" class="close fr">
            &#10006; </a>
        </div>
          <div class="box70per ml10 CMN_display_block">
            <h2 class="fs30 mt10">{{ $headinglbl }}
            </h2>
          </div> 
    </div>
  </div>
  <input type="hidden" name="nameval" id="nameval">
  <input type="hidden" name="bid" id="bid">
  <div class="box98per mt5">
  	<div align="center" class="alertboxalign" id="popupsessionreg" style="display: none;">
  		<p class="alert alert-success">
  			Inserted Successfully!
  		</p>
  	</div>
  	<div align="center" class="alertboxalign" id="popupsessionupd" style="display: none;">
  		<p class="alert alert-success">
  			Updated Successfully!
  		</p>
  	</div>
    <button id="edit" data-dismiss="modal" onclick="return fneditcheck();" 
            class="btn CMN_display_block box100 " disabled="disabled"
            style="background-color: #bbb5b5;margin-left: 87%;">
      <i class="fa fa-edit"></i>
      {{ trans('messages.lbl_edit') }}
    </button>
  </div>
  <div class="modal-body">
  <div class="mt3 CMN_display_block box100per">
    <table id="swaptable" class="table popuptoptable table-striped table-bordered 
      CMN_tblfixed box100per">
      <colgroup>
        <col width="10%">
        <col width="10%">
        <col width="65%">
        <col width="18%">
      </colgroup>
      <thead class="CMN_tbltheadcolor h30">
        <tr class="h37">
          <th class="fs14 pb3 CMN_tbltheadcolor">
            {{ trans('messages.lbl_select') }}
          </th>
          <th class="fs14 pb3 CMN_tbltheadcolor">
            {{ trans('messages.lbl_sno') }}
          </th>
          <th class="fs14 pb3 CMN_tbltheadcolor">
            {{ $field1lbl }}
          </th>
          <th class="fs14 pb3  CMN_tbltheadcolor">
            {{ trans('messages.lbl_use') }}/{{ trans('messages.lbl_notuse') }}
          </th>
        </tr>
      </thead>
    </table>
  </div>
  <div class="table_Scroll_limit_set  CMN_display_block box100per bdg" 
       style="margin-top: -6px;">
    <table id="swaptable1" class="table table-striped table-bordered CMN_tblfixed ">
      <col width="10%">
      <col width="10%">
      <col width="65%">
      <col width="18%">
      <tbody class="box100per h35">
        @php ($i = 1)
        @forelse($getdetails as $count => $data)
        <tr class="h37" onclick="fnrdocheck('{{ $data->$selectfiled['1'] }}','{{ $data->$selectfiled['0'] }}')">
          <td class="text-center" title="Select">
            <input type="radio" name="rdoedit" id="rdoedit{{ $data->$selectfiled['0'] }}" 
                   class="h13 w13"  onclick="fnrdocheck('{{ $data->$selectfiled['1'] }}','{{ $data->$selectfiled['0'] }}');">
            {{ Form::hidden('rdoid', $data->$selectfiled['0'] , array('id' => 'rdoid')) }}  
          </td>
          <td class="text-center pt7">{{ $i++ }}
          </td>
          <td class="pl5 pt7" id="dataname{{$data->$selectfiled['0']}}">{{ $data->$selectfiled['1'] }}</td>
          <td class="tac pt7" title="Use/Not Use">
            <a href="javascript:useNotuse('{{ $data->$selectfiled['0'] }}',
                     '{{$i}}');" class="btn-link anchorstyle" style="color:blue;cursor: pointer;">
              @if ($data->$selectfiled['3'] != 1) 
              <span class="btn-link" id="usenotuselabel{{$i}}" data-type="{{ $data->$selectfiled['3'] }}" style="color:blue;cursor: pointer;">{{ trans('messages.lbl_use') }}
              </span>
              @else 
              <span class="btn-link" id="usenotuselabel{{$i}}" data-type="{{ $data->$selectfiled['3'] }}" style="color:red;cursor: pointer;"> {{ trans('messages.lbl_notuse') }}
              </span>
              @endif
            </a>
            {{ Form::hidden('curtFlg'.$i, $data->$selectfiled['3'] , array('id' => 'curtFlg'.$i)) }} 
            {{ Form::hidden('editid'.$i, $data->$selectfiled['0'], array('id' => 'editid'.$i)) }} 
          </td>
        </tr>
        @empty
			<tr class="nodata">
				<td class="text-center red" colspan="9">
					{{ trans('messages.lbl_nodatafound') }}
				</td>
			</tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="box100per">
    <fieldset class="h50">
      <div class="col-md-12 mt10 ml50">
        <div class="box25per pull-left text-right clr_blue fwb mt5">
            {{ $field1lbl }}
              <span style="color:red;"> * 
              </span>
          </div>
          <div class="ml15 pull-left box70per mb5">
            {{ Form::text('textbox1','',array('id'=>'textbox1', 'name' => 'textbox1','class'=>'box40per form-control ime_mode_active','maxlength' => 40,
            'onkeypress' =>'return blockSpecialChar(event)',
            'onblur'=>'this.value=jQuery.trim(this.value);')) }}
            <label id="empty_textbox1" class="registernamecolor display_none">
              This Field is required.
            </label>
          </div>
        </div>  
        </fieldset>
      </div>
    <div class="modal-footer bg-info">
      <center>
        <div class="box100per text-center" id="">
          <div class="CMN_display_block" id="add_var">
            <button  id="btnadd" type="button"
                                 onclick="return fnaddeditsinglefield('{{ $request->location  }}',
                                                                      '{{ $request->mainmenu }}',
                                                                      '{{ $request->tablename }}',
                                                                      '{{ 1 }}','{{ 1 }}');" 
                                  class="btn btn-success CMN_display_block box100" >
              <i class="fa fa-plus" id="plusicon"></i>
              {{ trans('messages.lbl_add') }}
            </button>
             <button type="button" onclick="divpopupclose();" 
                  class="btn btn-danger CMN_display_block box110 button" >
            <i class="fa fa-remove" aria-hidden="true">
            </i> 
            {{ trans('messages.lbl_cancel') }}
          </button>
          </div>
          <div class="CMN_display_block" id="update_var" style="display: none;">
            <button  id="btnadd" type="button" 
                                 onclick="return fnaddeditsinglefield('{{ $request->location  }}',
                                                                      '{{ $request->mainmenu }}',
                                                                      '{{ $request->tablename }}',
                                                                      '{{ 2 }}','{{ 2 }}');" 
                                 class="CMN_display_block box100 btn add btn-warning">
              <i class="fa fa-edit" id="plusicon"></i>
              {{ trans('messages.lbl_update') }}
            </button> 
            <button type="button" onclick="divpopupclose();" 
                  class="btn btn-danger CMN_display_block box110 button" >
            <i class="fa fa-remove" aria-hidden="true">
            </i> 
            {{ trans('messages.lbl_cancel') }}
          </button>
          </div>
        </div>
      </center>
    </div>
    </div>
  </div>
</div>
