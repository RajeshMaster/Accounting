<style>
.modal {
      position: fixed;
      top: 40% !important;
      left: 50%;
      width: 800px;
      transform: translate(-50%, -50%);
   }
.border {
  border: 1px rgb(28, 184, 65) solid;
  padding-top: 2px;
  width: 300px;
  height: 252px;
}
.popupsize {
  margin:auto 18%;
  width:800px;
  height:80%;
}
</style>
{{ Form::open(array('name'=>'taxempselform', 'id'=>'taxempselform', 
                      'url' => 'Tax/empselectionprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
                      'method' => 'POST')) }}
     {{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
<div class="modal-content">
	<div class="modal-header pm0">
		<div class="col-xs-12 pm0">
			<div class="col-xs-2 pm0 pull-right mt15 mr10">
				<button type="button" data-dismiss="modal" class="close" style="color: red !important;" aria-hidden="true">&#10006;</button>
			</div>
			<div class="col-xs-8 pm0 ml10">
				<h2 class="modal-title custom_align">{{ trans('messages.lbl_cempsel') }}</h2>
			</div>
		</div>
	</div>
	<div class="modal-body">
        <div class="form-group" style="min-height: 230px;">
          <div for="cardno" class="col-md-1 fwb clr_blue">
          </div>
          <table class="main mb20" style="border: 0px;">
            <tr>
                <td valign="top" style="border: 0px;">
                  <div style="color: red;">Unselected</div>
                  <div class="setwebcamera">
                      <select multiple size="15" id="from" name="removed[]" class="border" style="height: 272px;">
                        @foreach($employeeUnselect as $key=>$employeesdeselect)
                          <option value="{{ $employeesdeselect->Emp_ID }}">
                            {{ ucwords($employeesdeselect->LastName) }}.{{ strtoupper(substr($employeesdeselect->FirstName,0,1)) }}
                          </option>   
                        @endforeach
                      </select>
                  </div>
                </td>
                <td style="border: 0px;">
                  <div class="controls CMN_display_block"> 
                      <a class="tdn" href="javascript:moveSelected('from', 'to')">&gt;</a>
                      </br>
                      <a class="tdn" href="javascript:moveSelected('to', 'from')">&lt;</a> 
                  </div>
                </td>
                <td valign="top" style="border: 0px;">
                  <div style="color: green;">Selected</div>
                  <div id="upload_results">
                      <select multiple size="15" id="to" name="selected[]" class="border" style="height: 273px;">
                        @foreach($employeeSelect as $key=>$employeesselected)
                          <option value="{{ $employeesselected->Emp_ID }}">
                            {{ ucwords($employeesselected->LastName) }}.{{ strtoupper(substr($employeesselected->FirstName,0,1)) }}
                          </option>
                        @endforeach
                      </select>
                  </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="modal-footer bg-info" style="margin-top:-3px; width: 100%;">
		<center>
			<button id="add"
                  onclick="javascript:return empselectbypopupclick();"
                  class="btn btn-success CMN_display_block box100"><i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_add') }}
          </button>
          <button data-dismiss="modal" class="btn btn-danger CMN_display_block box100">
            <i class="fa fa-times" aria-hidden="true"></i> {{ trans('messages.lbl_cancel') }}
          </button>
		</center>
	</div>
</div>
{{ Form::close() }}