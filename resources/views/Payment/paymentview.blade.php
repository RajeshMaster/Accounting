@extends('layouts.app')
@section('content')
@php use App\Http\Helpers; @endphp
@php use App\model\Estimation; @endphp
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
{{ HTML::script('resources/assets/js/payment.js') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper " data-category="sales sales_sub_4">
	{{ Form::open(array('name'=>'frmpaymentindex', 
						'id'=>'frmpaymentindex', 
						'url' => 'Payment/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	    {{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
	    {{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('topclick', $request->topclick, array('id' => 'topclick')) }}
		{{ Form::hidden('sortOptn', $request->paymentsort , array('id' => 'sortOptn')) }}
	    {{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	    {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	    {{ Form::hidden('payid', $request->payid , array('id' => 'payid')) }}
	    {{ Form::hidden('invoiceid', $request->invoiceid , array('id' => 'invoiceid')) }}
	    {{ Form::hidden('estimate_id', '', array('id' => 'estimate_id')) }}
	    {{ Form::hidden('backflg', '', array('id' => 'backflg')) }}
<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/payment.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_payment') }} - {{ trans('messages.lbl_Details') }}</h2>
		</div>
	</div>
	<div class="col-xs-12 mt10">
	<a href="javascript:fnpaymentind();"
        class="btn btn-info box80"><span class="fa fa-arrow-left"></span>{{ trans('messages.lbl_back') }}</a>
    <a href="javascript:fnpaymentedit('1');"  class="btn btn-warning box80">
				<span class="fa fa-edit"></span>
					{{ trans('messages.lbl_edit') }}
	</a>
	</div>
	<div class="col-xs-12 mt10 pm0">
				<div class="col-xs-3 pm0 ml10 mb10" style="border :1px solid #136E83">
					<div class="col-xs-12 text-left clr_blue" style="background: #b0e0f2">
						<label class="fwn" style="color: black;">{{ trans('messages.lbl_cusname') }}</label>
					</div>
					@if (isset($get_customer_detail[0]->customer_name))
					<div class="col-xs-12" style="background: #e5f4f9">
						<label class="fwn">{{ (isset($get_customer_detail[0]->customer_name)?$get_customer_detail[0]->customer_name:"Nill") }}</label>
					</div>
					@endif
					@if (!empty($get_customer_detail[0]->customer_address))
					<div class="col-xs-12" style="background: #e5f4f9">
						<span class="">{{ (isset($get_customer_detail[0]->customer_address)?$get_customer_detail[0]->customer_address:"") }}</span>
					@endif
					<?php echo "<br>"; ?>
					@if (!empty($get_customer_detail[0]->customer_contact_no)) 
						<span class="">{{ (isset($get_customer_detail[0]->customer_contact_no)?$get_customer_detail[0]->customer_contact_no:"") }}</span>
					</div>
					@endif
				</div>
				<?php
							$get_total = preg_replace("/,/", "", $get_estimate_query[0]->totalval);
							$a_style = "";
							$a_style = "style='color:black;font-weight:bold'";

							$disp_total = preg_replace("/,/", "", $get_estimate_query[0]->deposit_amount) 									+ 
										preg_replace("/,/", "", $get_estimate_query[0]->bank_charge);
						?>
				<div class="col-xs-3 pm0 ml20 mb10" style="border :1px solid #136E83">
					<div class="col-xs-12 clr_blue pm0">
						<div class="col-xs-5 pm0" style="background: #b0e0f2; text-align: right;">
							<label class="fwn mr6" style="color: black;">{{ trans('messages.lbl_payamount') }}</label>
						</div>
						<div class="col-xs-7" style="color: black;">
							<b>¥ {{ number_format($disp_total) }}</b>
						</div>
					</div>
					<div class="col-xs-12 clr_blue pm0">
						<div class="col-xs-5 pm0" style="background: #b0e0f2;text-align: right;">
							<label class="fwn mr6" style="color: black;">{{ trans('messages.lbl_bank_name') }}</label>
						</div>
						<?php 
							$g_bank = Estimation::fnGetBankName($get_estimate_query[0]->bankid);
						?>
						<div class="col-xs-7" style="color: black;">
							{{ $g_bank[0]->BankName }}
						</div>
					</div>
					<div class="col-xs-12 clr_blue pm0">
						<div class="col-xs-5 pm0" style="background: #b0e0f2;text-align: right;">
							<label class="fwn mr6" style="color: black;">{{ trans('messages.lbl_branch_name') }}</label>
						</div>
						<?php 
							$g_branch = Estimation::fnGetBranchName($get_estimate_query[0]->bankid,$get_estimate_query[0]->branchid);
						?>
						<div class="col-xs-7" style="color: black;">
							{{ $g_branch[0]->BranchName }}
						</div>
					</div>
				</div>
				<div class="col-xs-4 pm0 ml20 mb10" style="border :1px solid #136E83">
					<div class="col-xs-12 clr_blue pm0">
						<div class="col-xs-5 pm0" style="background: #b0e0f2;text-align: right;">
							<label class="fwn mr6" style="color: black;">{{ trans('messages.lbl_paymentnumber') }} </label>
						</div>
						<div class="col-xs-4">
							<b>{{ $get_estimate_query[0]->user_id }}</b>
						</div>
					</div>
					<div class="col-xs-12 clr_blue pm0">
						<div class="col-xs-5 pm0" style="background: #b0e0f2;text-align: right;">
							<label class="fwn mr6" style="color: black;">{{ trans('messages.lbl_paymentdate') }} </label>
						</div>
						<div class="col-xs-4" style="color: black;">
							{{ $get_estimate_query[0]->invoice_payment_date }}
						</div>
					</div>
					<div class="col-xs-12 clr_blue pm0">
						<div class="col-xs-5 pm0" style="background: #b0e0f2;text-align: right;">
							<label class="fwn mr6" style="color: black;">{{ trans('messages.lbl_paymentstate') }} </label>
						</div>
						<div class="col-xs-4" style="color: black;">
							{{ $get_estimate_query[0]->payment_date }}
						</div>
					</div>
				</div>
	</div>
	<div class="mr10 ml10">
		<div class="minh400">
			<table class="tablealternate box78per">
				<colgroup>
				   <col width="4%">
				   <col width="8%">
				   <col width="9%">
				   <col width="">
				   <col width="10%">
				   <col width="20%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader tac"> 
				  		<th class="tac">{{ trans('messages.lbl_sno') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_invoiceno') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_date') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_custname') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_paymentday') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_amount') }}</th>
			   		</tr>
			   	</thead>
			   	<tbody>
			   		<?php
						$i = 0;
						$j = 1;
						$grandtotal = 0;
						//while ($get_row = mysql_fetch_assoc($exe_invoice_query)) {
						for ($i = 0; $i < count($disp_record); $i++) {
							$getTaxquery = Helpers::fnGetTaxDetails($disp_record[$i]['quot_date']);
							if ($i%2 != 0) {
								$style = "background-color:#dff1f4;";
							} else {
								$style = "background-color:#FFFFFF;";
							}
							$grandtotal1 = 0;
							//$grandtotal += preg_replace('/,/', '', $get_row['totalval']);
							if (!empty($disp_record[$i]['totalval'])) {
								if (($disp_record[$i]['tax'] != 2) && (trim($disp_record[$i]['company_name']) != "入金")) {
									$totroundval = preg_replace("/,/", "", $disp_record[$i]['totalval']);
									$dispval = (($totroundval * intval($getTaxquery[0]->Tax))/100);
									//echo number_format($dispval);
									$grandtotal1 = $totroundval + $dispval;
								} else {
									$totroundval = preg_replace("/,/", "", $disp_record[$i]['totalval']);
									$dispval = 0;
									//echo $dispval;
									$grandtotal1 = $totroundval + $dispval;
								}
							}
							$grandtotal += $grandtotal1;

							$disp_flg = false;
							$disp_class = 'tdcontent';
							if ((/*(count($disp_record)-1) == $i*/$i > 0) && ($disp_record[$i]['company_name'] == "入金")) {
								$disp_flg = true;
								// if ($i%2 != 0) {
								// 	$disp_class = 'tdpaycontent1';
								// } else {
								// 	$disp_class = 'tdpaycontent';
								// }
							} else {
								$disp_flg = false;
								$disp_class = 'tdcontent';
							}
						?>
						<tr style="{{$style}}">
							<td class="tac">
								<?php
									if ((trim($disp_record[$i]['company_name']) != "入金") && (trim($disp_record[$i]['company_name']) != "未払残高")) {
										echo $j; $j++;
									}
								?>
							</td>
							<td class="tac">
								{{ $disp_record[$i]['user_id'] }}
							</td>
							<td class="tac">
								{{ $disp_record[$i]['quot_date'] }}
							</td>
							<td>
								{{ $disp_record[$i]['company_name'] }}
							</td>
							<td class="tac">
								{{ $disp_record[$i]['payment_date'] }}
							</td>
							<td class="tar">
								<b>{{ number_format($grandtotal1) }}</b>
							</td>
						</tr>
				<?php } ?>
						<tr>
							<td colspan="6" style="background-color: white;"></td>
						</tr>
						<?php
							$style = '';
								if ($grandtotal > 0) {
								$style = "style = margin-right:4px;font-weight:bold;color:red";
								} else if ($grandtotal <= 0) {
								$style = "style = margin-right:4px;font-weight:bold;color:green";
								} else {
									$style = "style = margin-right:4px;font-weight:bold;";
								}
						?>
						<tr>
							<td colspan="5" class="tar" style="background-color:#E5F4F9;color:black;"><b>{{ trans('messages.lbl_total') }}</b></td>
							<td {{$style}} class="tar">
									{{ number_format($grandtotal) }}
							</td>
						</tr>
			   	</tbody>
			</table>
		</div>
	</div>
	{{ Form::close() }}
</article>
</div>
@endsection