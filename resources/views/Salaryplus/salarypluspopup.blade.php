<style>
.modal {
      position: fixed;
      top: 50% !important;
      left: 50%;
      transform: translate(-50%, -50%);
   }
</style>
  <div class="modal-content">
    <div class="modal-header" style="padding: 3px !important;">
      <div class="col-md-2 pull-right mt15">
          <button type="button" data-dismiss="modal" class="close" style="color: red !important;" aria-hidden="true">&#10006;</button>
        </div>
        <div class="col-md-8" style="padding-left: 15px;">
          <h2 style="font-size:30px;" class="modal-title custom_align">{{ trans('messages.lbl_cempsel') }}</h2>
        </div>
    </div>
    {{ Form::open(array('name'=>'empselectform', 'id'=>'empselectform', 
    'url' => 'Salaryplus/empselectprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
                      'method' => 'POST')) }}
     {{ Form::hidden('mainmenu', $request->mainmenu ,array('id' => 'mainmenu')) }}
     {{ Form::hidden('year', $request->year , array('id' => 'year')) }}
     {{ Form::hidden('month', $request->month , array('id' => 'month')) }}
    <div class="modal-body" style="padding: 1px !important;">
        <div class="form-group" style="min-height: 300px;">
          <table class="main">
            <tr>
                <td valign="top" style="border: none !important;width: 50%;align-items: right">
                  <div style="color: red;margin-left: 10%;">{{ trans('messages.lbl_Unselected') }}</div>
                  <div>
                      <select multiple size="15" id="from" name="removed[]" class="border" style="height: 275px;width: 90%;margin-left: 10%;">
                       @foreach($employeeUnselect as $key=>$employeesdeselect)
                          <option value="{{ $employeesdeselect->Emp_ID }}">
                            {{ $employeesdeselect->LastName }}
                          </option>
                        @endforeach
                      </select>
                  </div>
                </td>
                <td style="border: none !important;">
                  <div class="controls CMN_display_block"> 
                      <a class="tdn" href="javascript:moveSelected('from', 'to')">&gt;</a>
                      </br>
                      <a class="tdn" href="javascript:moveSelected('to', 'from')">&lt;</a> 
                  </div>
                </td>
                <td valign="top" style="border: none !important;width: 50%;">
                  <div style="color: green;"> {{ trans('messages.lbl_Selected') }}</div>
                  <div>
                      <select multiple size="15" id="to" name="selected[]" class="border"  style="height: 275px;width: 90%">
                      	@foreach($employeeSelect as $key=>$employeesselected)
                          <option value="{{ $employeesselected->Emp_ID }}">
                            {{ $employeesselected->LastName }}
                          </option>
                        @endforeach
                      </select>
                  </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="modal-footer  bg-info mb10 mr10 ml10" style="padding: 6px !important;border-radius: 5px !important;">
        <center>
          <button id="add"
                  onclick="javascript:return empselectbypopupclick();"
                  class="btn btn-success CMN_display_block box100">
                  <i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_add') }}
          </button>
          <button data-dismiss="modal" 
           class="btn btn-danger CMN_display_block box100">
           <i class="fa fa-times" aria-hidden="true"></i>
            {{ trans('messages.lbl_cancel') }}
          </button>
          <!-- onclick="javascript:return cancelpopupclick();" -->
        </center>
    </div>
</div>
</div>
{{ Form::close() }}