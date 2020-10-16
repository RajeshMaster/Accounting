{{ HTML::script('resources/assets/js/common.js') }}
<style>
	.modal {
      position: fixed;
      top: 50% !important;
      left: 50%;
      transform: translate(-50%, -50%);
   }
</style>
<div class="modal-content">
	{{ Form::open(array('name'=>'empselpopup', 'id'=>'empselpopup', 
                          'url' => 'Salary/empselectionprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
                          'method' => 'POST')) }}
      {{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('datemonth', $request->datemonth , array('id' => 'datemonth')) }}
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#10006;</button>
			<h3 class="modal-title custom_align"><B>Employee Selection</B></h3>
		</div>
		<div class="modal-body">
			<div class="form-group" style="min-height: 230px;">
				<div for="cardno" class="col-md-1 fwb clr_blue">
				</div>
			<div class="col-md-12">
				<div class="col-md-4">
					<div style="color: red;">Unselected</div>
					<div>
						<select multiple size="15" id="from" name="removed[]" class="border" style="height: 228px;width: 250px;">
							@foreach($employeeUnselect as $key=>$employeesdeselect)
								<option value="{{ $employeesdeselect->Emp_ID }}">
									{{ strtoupper(ucwords($employeesdeselect->LastName)) }}.{{ strtoupper(substr($employeesdeselect->FirstName,0,1)) }}
								</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="col-md-2 mt90 pl90">
					<div class="controls CMN_display_block"> 
						<a class="tdn" href="javascript:moveSelected('from', 'to')">&gt;</a>
						</br>
						<a class="tdn" href="javascript:moveSelected('to', 'from')">&lt;</a> 
					</div>
				</div>
				<div class="col-md-3 pr120 ml30">
					<div style="color: green;">Selected</div>
						<div>
							<select multiple size="15" id="to" name="selected[]" class="border" style="height: 230px;width: 240px;">
								@foreach($employeeSelect as $key=>$employeesselected)
									<option value="{{ $employeesselected->Emp_ID }}">
										{{ strtoupper(ucwords($employeesselected->LastName)) }}.{{ strtoupper(substr($employeesselected->FirstName,0,1)) }}
									</option>
								@endforeach
							</select>
						</div>
				</div>
			</div>
	</div>
	<div class="modal-footer bg-info mt30">
		<center>
			<button id="add" onclick="javascript:return empselectbypopupclick();" class="btn btn-success CMN_display_block box100">
				<i class="fa fa-edit" aria-hidden="true"></i> Add
			</button>
			<button data-dismiss="modal" class="btn btn-danger CMN_display_block box100">
				<i class="fa fa-times" aria-hidden="true"></i>
				Cancel
			</button>
			<!-- onclick="javascript:return cancelpopupclick();" -->
		</center>
	</div>
	</div>
</div>
