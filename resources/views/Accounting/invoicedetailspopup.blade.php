{{ HTML::script('resources/assets/js/accounts.js') }}
@php use App\Http\Helpers; @endphp
<style>
	.highlight { background-color: #428eab !important; }
	 .modal {
			position: fixed;
			top: 50% !important;
			left: 50%;
			transform: translate(-50%, -50%);
	 }
	 
</style>
{{ Form::open(array('name'=>'invoiceDtlsPopup', 'id'=>'invoiceDtlsPopup',
							'class' => 'form-horizontal',
							'url' => 'Accounting/tranferaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
							'method' => 'POST','files'=>true)) }}

	{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
	{{ Form::hidden('accDate', $request->invoiceDate , array('id' => 'accDate')) }}
	{{ Form::hidden('hidempid', '', array('id' => 'hidempid')) }}
	{{ Form::hidden('hidchkTrans', '', array('id' => 'hidchkTrans')) }}
	<div class="modal-content">
		<div class="modal-header">
			 <button type="button" class="close" data-dismiss="modal" style="color: red;" aria-hidden="true">&#10006;</button>
			 <h3 class="modal-title custom_align"><B>{{ trans('messages.lbl_invoiceDtl') }}</B></h3>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-5 clr_black text-left mt10">
				<label>
					{{ trans('messages.lbl_date') }} : {{ $request->invoiceDate }}
				</label>
			</div>
			<div class="col-xs-6 clr_black text-right mt10">
				<label>

				</label>
			</div>
		</div>
		<div class="modal-body" style="height: 310px;overflow-y: scroll;width: 100%;">
			<table id="data" class="tablealternate box100per" style="height: 40px;">
				<colgroup>
					<col width="4%">
					<col width="10%">
					<col width="12%">
					<col width="12%">
					<col width="10%">
					<col width="8%">
					<col width="6%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
					<tr class="tableheader fwb tac"> 
						<th class="tac">{{ trans('messages.lbl_sno') }}</th>
						<th class="tac">{{ trans('messages.lbl_invoiceno') }}</th>
						<th class="tac">{{ trans('messages.lbl_dateofissue') }}</th>
						<th class="tac">{{ trans('messages.lbl_paymentdate') }}</th>
						<th class="tac">{{ trans('messages.lbl_custname') }}</th>
						<th class="tac">{{ trans('messages.lbl_paidamt') }}</th>
						<th class="tac">{{ trans('messages.lbl_notneed') }}</th>
					</tr>
				</thead>
				<tbody>
					@php 
						$i=0;
						$paymenttotal = 0; 
						$paidtotal = 0; 
						$differencetotal = 0; 
					@endphp
					@forelse($TotEstquery as $key => $data)
						<tr>
							<td>
							</td>
							<td class="tal pr10 vam pt5">
								<div class="text-center vam">
									<label class="pm0 vam" style="color:#136E83;">
										{{ $data->user_id }}
									</label>
								</div>
							</td>
							
							<td>
								<div class="ml5 pt5">
									<div class="mb2">
										{{$data->quot_date}}
									</div>
								</div>
							</td>

							<td class="" align="center" >
								{{ $data->payment_date }}
							</td>

							<td class="" align="left" >
								<div class="ml5 pt5">
									<div class="mb2">
										<b class="blue">{{$data->company_name}}</b>
									</div>
								</div>
							</td>
							<td></td>



						</tr>
					@empty
						<tr>
							<td class="text-center" colspan="10" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
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