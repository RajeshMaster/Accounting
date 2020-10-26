{{ HTML::script('resources/assets/js/accounts.js') }}
<style>
	.highlight { background-color: #428eab !important; }
	 .modal {
			position: fixed;
			top: 50% !important;
			left: 50%;
			transform: translate(-50%, -50%);
	 }
	 
</style>
{{ Form::open(array('name'=>'salaryDtlsPopup', 'id'=>'salaryDtlsPopup',
							'class' => 'form-horizontal',
							'url' => 'Accounting/tranferaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
							'method' => 'POST','files'=>true)) }}

	{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
	{{ Form::hidden('accDate', $request->transferDate , array('id' => 'accDate')) }}
	{{ Form::hidden('hidempid', '', array('id' => 'hidempid')) }}
	{{ Form::hidden('hidchkTrans', '', array('id' => 'hidchkTrans')) }}
	<div class="modal-content">
		<div class="modal-header">
			 <button type="button" class="close" data-dismiss="modal" style="color: red;" aria-hidden="true">&#10006;</button>
			 <h3 class="modal-title custom_align"><B>{{ trans('messages.lbl_salary_det') }}</B></h3>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-5 clr_black text-left mt10">
				<label>
					{{ trans('messages.lbl_date') }} : {{ $request->transferDate }}
				</label>
			</div>
			<div class="col-xs-6 clr_black text-right mt10">
				<label>
					{{ trans('messages.lbl_bank') }} : 
					{{ Form::select('salaryBank',[null=>'']+$getBankDtls,'',
									array('name' =>'salaryBank',
											'id'=>'salaryBank',
											'data-label' => trans('messages.lbl_bank'),
											'class'=>'pl5 box50per' ))}}
				</label>
			</div>
		</div>
		<div class="modal-body" style="height: 310px;overflow-y: scroll;width: 100%;">
			<table id="data" class="tablealternate box100per" style="height: 40px;">
				<colgroup>
					<col width="6%">
					<col width="13%">
					<col width="25%">
					<col width="15%">
					<col width="10%">
					<col width="6%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
					<tr class="tableheader fwb tac"> 
						<th class="tac">{{ trans('messages.lbl_sno') }}</th>
						<th class="tac">{{ trans('messages.lbl_empid') }}</th>
						<th class="tac">{{ trans('messages.lbl_empName') }}</th>
						<th class="tac">{{ trans('messages.lbl_amount') }}</th>
						<th class="tac">{{ trans('messages.lbl_fee') }}</th>
						<th class="tac">{{ trans('messages.lbl_notneed') }}</th>
					</tr>
				</thead>
				<tbody>
				 	@php $i = 1 @endphp
				 	@php $j = 1 @endphp
				 	@forelse($getSalaryDtls as $key  => $salary)
						<tr>
							<td align="center">
								{{ $i++ }}
							</td>
							<td align="center">
								{{ $salary->Emp_ID }}
							</td>
							<td align="left">
								{{ $SalaryDtls[$salary->Emp_ID]['empName'] }}
							</td>
							<td align="right">
								{{ Form::text('salAmt'.$j,(isset($SalaryDtls[$salary->Emp_ID]['Amount'])) ? number_format($SalaryDtls[$salary->Emp_ID]['Amount']) : 0,
								array('id'=>'salAmt'.$j,
										'name' => 'salAmt'.$j,
										'style'=>'text-align:right;padding-right:4px;',
										'autocomplete' =>'off',
										'class'=>'box96per ime_mode_disable ml7',
										'onblur' => 'return fnSetZero11(this.id);',
										'onfocus' => 'return fnRemoveZero(this.id);',
										'onclick' => 'return fnRemoveZero(this.id);',
										'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
										'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
										'data-label' => trans('messages.lbl_amount'))) }}
										<br/>
								<!-- {{ $SalaryDtls[$salary->Emp_ID]['Amount'] }} -->
							</td>
							<td align="right">
								{{ Form::text('salFee'.$j , 0,
								array('id'=>'salFee'.$j,
										'name' => 'salFee'.$j,
										'style'=>'text-align:right;padding-right:4px;',
										'autocomplete' =>'off',
										'class'=>'box96per ime_mode_disable ml7',
										'onblur' => 'return fnSetZero11(this.id);',
										'onfocus' => 'return fnRemoveZero(this.id);',
										'onclick' => 'return fnRemoveZero(this.id);',
										'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
										'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
										'data-label' => trans('messages.lbl_fee'))) }}
							</td>
							<td align="center">
								<input  type="checkbox" name="salary[]" id="salary[]" 
									class="salarychk" 
									value="<?php  echo $SalaryDtls[$salary->Emp_ID]['empName']."$".$salary->Emp_ID."$".$SalaryDtls[$salary->Emp_ID]['Amount']."$".""."$".$j; ?>">
							</td>
						</tr>
						@php $j++; @endphp
					@empty
						<tr>
							<td class="text-center" colspan="6" style="color: red;">
								{{ trans('messages.lbl_nodatafound') }}
							</td>
						</tr>
					@endforelse
					
				</tbody>
			 </table>
		</div>
		<div class="modal-footer bg-info mt10">
			<center>
				<button id="add" class="btn btn-success CMN_display_block box100 selectsalary">
						<i class="fa fa-plus" aria-hidden="true"></i>
							 {{ trans('messages.lbl_add') }}
				</button>
				<button data-dismiss="modal" class="btn btn-danger CMN_display_block box100">
						<i class="fa fa-times" aria-hidden="true"></i>
							 {{ trans('messages.lbl_cancel') }}
				</button>
			</center>
		</div>
	</div>
{{ Form::close() }}