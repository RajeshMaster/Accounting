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
<div class="modal-content">
	<div class="modal-header">
		 <button type="button" class="close" data-dismiss="modal" style="color: red;" aria-hidden="true">&#10006;</button>
		 <h3 class="modal-title custom_align"><B>{{ trans('messages.lbl_loandetail') }}</B></h3>
	</div>
	<div class="col-xs-12 mt5">
		<div class="col-xs-5 clr_black text-left mt10">
			<label>
				{{ trans('messages.lbl_date') }} : {{ $request->autoDebitDate }}
			</label>
		</div>
		<div class="col-xs-6 clr_black text-right mt10">
			<label>
				{{ trans('messages.lbl_usernamesign') }} : 
				{{ Form::select('assetsUser',[null=>'']+$getUserDtls,$request->userId,
								array('name' =>'assetsUser',
										'id'=>'assetsUser',
										'onchange'=>'javascript:fnGetLoanDtls(this.value);',
										'class'=>'pl5 widthauto'))}}
			</label>
		</div>
	</div>
	<div class="modal-body" style="height: 310px;overflow-y: scroll;width: 100%;">
		 <table id="data" class="tablealternate box100per" style="height: 40px;">
				<colgroup>
				<col width="6%">
				<col width="8%">
				<col width="15%">
				<col width="8%">
				<col width="8%">
				<col width="8%">
			</colgroup>
			<thead class="CMN_tbltheadcolor">
				<tr class="tableheader fwb tac"> 
					<th class="tac">{{ trans('messages.lbl_sno') }}</th>
					<th class="tac">{{ trans('messages.lbl_loanno') }}</th>
					<th class="tac">{{ trans('messages.lbl_loanname') }}</th>
					<th class="tac">{{ trans('messages.lbl_amount') }}</th>
					<th class="tac">{{ trans('messages.lbl_interest') }}</th>
					<th class="tac">{{ trans('messages.lbl_notneed') }}</th>
				</tr>
			</thead>
			<tbody>
			 	@php $i = 1 @endphp
			 	@php $j = 1 @endphp
			 	@forelse($getLoanDtls as $key  => $loan)
					<tr>
						<td align="center">
							{{ $i++ }}
						</td>
						<td align="center">
							{{ $loan->loanId}}
						</td>
						<td align="left">
							{{ $loan->loanName}}
						</td>
						<td align="right">
							{{ Form::text('loanAmt'.$j,(isset($loan->loanAmount)) ? number_format($loan->loanAmount * 10000) : "",
							array('id'=>'loanAmt'.$j,
									'name' => 'loanAmt'.$j,
									'style'=>'text-align:right;padding-right:4px;',
									'autocomplete' =>'off',
									'class'=>'box96per ime_mode_disable ml7',
									'onblur' => 'return fnSetZero11(this.id);',
									'onfocus' => 'return fnRemoveZero(this.id);',
									'onclick' => 'return fnRemoveZero(this.id);',
									'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
									'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
									'data-label' => trans('messages.lbl_fee'))) }}
									<br/>
							<!-- {{ $loan->loanAmount * 10000 }} -->
						</td>
						<td align="right">
							{{ Form::text('loanFee'.$j,(isset($loan->monthInterest)) ? number_format($loan->monthInterest) : "",
							array('id'=>'loanFee'.$j,
									'name' => 'loanFee'.$j,
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
							<input  type="checkbox" name="loan[]" id="loan[]" 
								class="<?php echo $loan->loanId; ?>" 
								value="<?php  echo $loan->loanName."$".$loan->loanId."$".($loan->loanAmount * 10000)."$".$loan->monthInterest."$".$j; ?>">
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
			 <button id="add" class="btn btn-success CMN_display_block box100 selectloan">
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
