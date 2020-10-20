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
				<div class="col-xs-4 clr_black text-left mt10">
					<label>
						{{ trans('messages.lbl_date') }} : {{ $request->autoDebitDate }}
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
					 	@forelse($getLoanDtls as $key  => $loan)
							<tr>
								<td align="center">
									{{ $i++ }}
								</td>
								<td align="center">
									{{ $loan->loanId}}
								</td>
								<td>
									{{ $loan->loanName}}
								</td>
								<td>
									{{ $loan->loanAmount * 10000 }}
								</td>
								<td>
									{{ $loan->monthInterest }}
								</td>
								<td align="center">
									<input  type="checkbox" id="" name="empid" onclick="">
								</td>
							</tr>
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
				 <button id="add" onclick="" class="btn btn-success CMN_display_block box100">
						<i class="fa fa-plus" aria-hidden="true"></i>
							 {{ trans('messages.lbl_add') }}
				 </button>
				 <button data-dismiss="modal" class="btn btn-danger CMN_display_block box100">
						<i class="fa fa-times" aria-hidden="true"></i>
							 {{ trans('messages.lbl_cancel') }}
				 </button>
				 <!-- onclick="javascript:return cancelpopupclick();" -->
			</center>
	 </div>
			</div>
	 </div>