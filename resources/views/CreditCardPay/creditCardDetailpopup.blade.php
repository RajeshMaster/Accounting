{{ HTML::script('resources/assets/js/creditcardpay.js') }}
<style>
	.highlight { background-color: #428eab !important; }
	 .modal {
			position: fixed;
			top: 50% !important;
			left: 50%;
			transform: translate(-50%, -50%);
	 }
	 
</style>
{{ Form::open(array('name'=>'creditCardDtlsPopup', 'id'=>'creditCardDtlsPopup',
							'class' => 'form-horizontal',
							'url' => 'CreditCardPay/PopupUploadProcess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
							'method' => 'POST','files'=>true)) }}

	{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
	{{ Form::hidden('accDate', $request->autoDebitDate , array('id' => 'accDate')) }}
	{{ Form::hidden('hidloan', '', array('id' => 'hidloan')) }}
	{{ Form::hidden('hidcheckDeb', '', array('id' => 'hidcheckDeb')) }}

	<div class="modal-content">
		<div class="modal-header">
			 <button type="button" class="close" data-dismiss="modal" style="color: red;" aria-hidden="true">&#10006;</button>
			 <h3 class="modal-title custom_align"><B>{{ trans('messages.lbl_loandetail') }}</B></h3>
		</div>
		
		<div class="col-xs-12 mt5">
			<div class="col-xs-5 clr_black text-left mt10">
				<label>
					{{ trans('messages.lbl_date') }} 
				</label>
			</div>
		
		</div>
		<div class="modal-body" style="height: 310px;overflow-y: scroll;width: 100%;">
			 <table id="data" class="tablealternate box100per" style="height: 40px;">
				<colgroup>
					<col width="6%">
					<col width="13%">
					<col width="25%">
					<col width="25%">
					<col width="15%">
					<col width="10%">
					<col width="6%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
					<tr class="tableheader fwb tac"> 
						<th class="tac">{{ trans('messages.lbl_sno') }}</th>
						<th class="tac">{{ trans('messages.lbl_Date') }}</th>
						<th class="tac">{{ trans('messages.lbl_content') }}</th>
						<th class="tac">{{ trans('messages.lbl_amount') }}</th>
						<th class="tac">{{ trans('messages.lbl_bill') }}</th>
						<th class="tac">{{ trans('messages.lbl_categorie') }}</th>
						<th class="tac">{{ trans('messages.lbl_remarks') }}</th>
					</tr>
				</thead>
				<tbody>

					<tr>
						<td class="text-center" colspan="7" style="color: red;">
							{{ trans('messages.lbl_nodatafound') }}
						</td>
					</tr>
					
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
{{ Form::close() }}
