{{ HTML::script('resources/assets/js/accounts.js') }}
{{ HTML::script('resources/assets/js/common.js') }}
<style>
	.highlight { background-color: #428eab !important; }
	.modal {
			position: fixed;
			top: 50% !important;
			left: 50%;
			transform: translate(-50%, -50%);
	}
	select{
		min-width: 100px;
	}
	 
</style>
{{ Form::open(array('name'=>'expensesDataPopup', 'id'=>'expensesDataPopup',
							'class' => 'form-horizontal',
							'url' => 'Accounting/expensesDataaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
							'method' => 'POST','files'=>true)) }}

	{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
	{{ Form::hidden('accDate', $request->expensesDate , array('id' => 'accDate')) }}
	{{ Form::hidden('hidempid', '', array('id' => 'hidempid')) }}
	{{ Form::hidden('hidchkExp', '', array('id' => 'hidchkExp')) }}
	<div class="modal-content">
		<div class="modal-header">
			 <button type="button" class="close" data-dismiss="modal" style="color: red;" aria-hidden="true">&#10006;</button>
			 <h3 class="modal-title custom_align"><B>{{ trans('messages.lbl_expensesDtls') }}</B></h3>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-6 clr_black text-left mt10">
				<label>
					{{ trans('messages.lbl_date') }} : {{ $request->expensesDate }}
				</label>
			</div>
			<div class="col-xs-6 clr_black text-right mt10">
				<label>
					{{ trans('messages.lbl_bank') }} : 
					{{ Form::select('bankIdAccNo',[null=>'']+$getBankDtls,$request->bankIdAccNo, array('name' =>'bankIdAccNo',
									'id'=>'bankIdAccNo',
									'onchange'=>'javascript:fnGetExpDtls(this.value);',
									'data-label' => trans('messages.lbl_bank'),
									'class'=>'pl5 widthauto' ))}}
				</label>
			</div>
			
		</div>
		<div class="modal-body" style="height: 310px;overflow-y: scroll;width: 100%;">
			<table id="data" class="tablealternate box100per" style="height: 40px;">
				<colgroup>
					<col width="5%">
					<col width="25%">
					<col width="15%">
					<col width="10%">
					<col width="5%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
					<tr class="tableheader fwb tac"> 
						<th class="tac">{{ trans('messages.lbl_sno') }}</th>
						<th class="tac">{{ trans('messages.lbl_content') }}</th>
						<th class="tac">{{ trans('messages.lbl_amount') }}</th>
						<th class="tac">{{ trans('messages.lbl_fee') }}</th>
						<th class="tac">
							{{ trans('messages.lbl_notneed') }}
							@if(count($expensesData) != 0)
								<input  type="checkbox" name="expensesAllCheck" 
									id="expensesAllCheck" class="expensesAllCheck" 
									onclick="expensesDataAllCheck();">
							@endif
						</th>
					</tr>
				</thead>
				<tbody>
				 	@php $i = 1 @endphp
				 	@php $j = 1 @endphp
				 	@forelse($expensesData as $key  => $value)
						<tr>
							<td align="center">
								{{ $i++ }}
							</td>
							<td align="center">
								@if($value->empId != "")
									{{ $value->empId }} - {{ $value->Empname }}
								@else
									{{ $value->content }}
								@endif
							</td>
							<td align="right">
								{{ Form::text('expensesDataAmt'.$j,(isset($value->amount)) ? number_format($value->amount) : 0,
								array('id'=>'expensesDataAmt'.$j,
										'name' => 'expensesDataAmt'.$j,
										'style'=>'text-align:right;padding-right:4px;',
										'autocomplete' =>'off',
										'class'=>'box96per ime_mode_disable ml7 numonly',
										'onblur' => 'return fnSetZero11(this.id);',
										'onfocus' => 'return fnRemoveZero(this.id);',
										'onclick' => 'return fnRemoveZero(this.id);',
										'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
										'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
										'data-label' => trans('messages.lbl_amount'))) }}
										<br/>
							</td>
							<td align="right">
								{{ Form::text('expensesDataFee'.$j , (isset($value->fee)) ? number_format($value->fee) : 0,
								array('id'=>'expensesDataFee'.$j,
										'name' => 'expensesDataFee'.$j,
										'style'=>'text-align:right;padding-right:4px;',
										'autocomplete' =>'off',
										'class'=>'box96per ime_mode_disable ml7 numonly',
										'onblur' => 'return fnSetZero11(this.id);',
										'onfocus' => 'return fnRemoveZero(this.id);',
										'onclick' => 'return fnRemoveZero(this.id);',
										'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
										'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
										'data-label' => trans('messages.lbl_fee'))) }}
							</td>
							<td align="center">
								<input  type="checkbox" name="expensesData[]" 
									id="expensesData[]" class="expensesDatachk" 
									value="<?php  echo $value->empId."$".$value->subjectId."$".$value->amount."$".$value->fee."$".$j; ?>">
							</td>
						</tr>
						@php $j++; @endphp
					@empty
						<tr>
							<td class="text-center" colspan="5" style="color: red;">
								{{ trans('messages.lbl_nodatafound') }}
							</td>
						</tr>
					@endforelse
					
				</tbody>
			 </table>
		</div>
		<div class="modal-footer bg-info mt10">
			<center>
				<button id="add" class="btn btn-success CMN_display_block box100 selectExpenses">
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