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
					<col width="6%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
					<col width="25%">
					<col width="14%">
					<col width="10%">
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
						$i = 1;
					@endphp
					@forelse($TotEstquery as $key => $data)
						<tr>

							<td align="center">
								{{ $i++ }}
							</td>

							<td class="tal pr10 vam pt5">
								<div class="text-center vam">
									<label class="pm0 vam" style="color:#136E83;">
										{{ $data->user_id }}
									</label>
								</div>
							</td>
							
							<td align="center">
								<div class="ml5 pt5">
									<div class="mb2">
										{{$data->quot_date}}
									</div>
								</div>
							</td>

							<td align="center">
								{{ $data->payment_date }}
							</td>

							<td align="left">
								<div class="ml5 pt5">
									<div class="mb2">
										<b class="blue">{{$data->company_name}}</b>
									</div>
								</div>
							</td>

							<td align="right">
								<?php  $totalval += preg_replace('/,/', '', $data->totalval); ?>
			   					{{--*/ $getTaxquery = Helpers::fnGetTaxDetails($data->quot_date); /*--}}
								<?php 
									if(!empty($data->totalval)) {
										if($data->tax != 2) {
											$totroundval = preg_replace("/,/", "", $data->totalval);
											$dispval = (($totroundval * intval((isset($getTaxquery[0]->Tax)?$getTaxquery[0]->Tax:0)))/100);
											$dispval1 = number_format($dispval);
											$grandtotal = $totroundval + $dispval;
										} else {
											$totroundval = preg_replace("/,/", "", $data->totalval);
											$dispval = 0;
											$grandtotal = $totroundval + $dispval;
											$dispval1 = $dispval;
										}
									}

									$grand_total = number_format($grandtotal);
									$divtotal += str_replace(",", "",$grand_total);

									if ($data->paid_status != 1) {
										$grand_style = "style='font-weight:bold;color:red;'";
										$balance += $grandtotal;
									} else {
										$grand_style = "style='font-weight:bold;color:green;'";
										$paid_amo += $grandtotal;
									}

									if($data->paid_status == 1) {
										$pay_balance = str_replace(",", "",(isset($invoice_balance[$key][0]->totalval)?$invoice_balance[$key][0]->totalval:0));
										$gr_total = number_format($grandtotal);
										$grand_tot = str_replace(",", "",$gr_total);
										$paid_amount += (isset($invoice_balance[$key][0]->deposit_amount)?$invoice_balance[$key][0]->deposit_amount:0);
										$bal_amount = $divtotal - $paid_amount;
									}

									if($data->paid_status != 1) {
										$gr_total = number_format($grandtotal);
										$grand_tot = str_replace(",", "",$gr_total);
										$bal_amount = $divtotal-$paid_amount;
									}
									if(isset($invbal[$key])) {
										if($invbal[$key]['bal_amount'] > 0) {
											$balance_style = "style='font-weight:bold;color:red;'";
										} else {
											$balance_style = "style='font-weight:bold;color:green;'";
										}
									}

								?>
								@if(isset($invbal[$key]))
									@if($invbal[$key]['bal_amount'] > 0)
										@if($invbal[$key]['bal_amount']==0)
											@php 
												$paidAmount = 0;
											@endphp 
											 {{ 0 }} 
										@else
											@php 
												$paidAmount = $grandtotal - $invbal[$key]['bal_amount'] ;
											@endphp
												{{ number_format($paidAmount) }}
											@endif
									@else
										@if($invbal[$key]['bal_amount'] == 0)
											@php 
												$paidAmount = 0;
											@endphp 
											{{ number_format($grandtotal) }}
										@else
											@php 
												$paidAmount = $grandtotal - $invbal[$key]['bal_amount'] 
											@endphp
											{{ number_format($paidAmount ) }}
										@endif
									@endif
								@else
									@php  
									$paidAmount = 0;
									@endphp
									{{ 0 }}	
								@endif
							</td>

							<td align="center">
								<input  type="checkbox" name="invoice[]" id="invoice[]" 
									class="<?php echo $data->user_id; ?>" 
									value="">
							</td>

						</tr>
					@empty
						<tr>
							<td class="text-center" colspan="7" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
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