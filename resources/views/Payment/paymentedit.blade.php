@extends('layouts.app')
@section('content')
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
	var accessDate = '<?php echo Auth::user()->accessDate; ?>';
	var userclassification = '<?php echo Auth::user()->userclassification; ?>';
	$(document).ready(function() {
		if (userclassification == 1) {
			accessDate = setNextDay(accessDate);
			setDatePickerAfterAccessDate("payment_date", accessDate);
		} else {
			setDatePicker("payment_date");
		}
	});
</script>
{{ HTML::script('resources/assets/js/common.js') }}
{{ HTML::script('resources/assets/js/invoice.js') }}
{{ HTML::script('resources/assets/js/payment.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
<div class="CMN_display_block box100per" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper" data-category="sales sales_sub_4">
	{{ Form::open(array('name'=>'frminvoiceaddedit', 
						'id'=>'frminvoiceaddedit', 
						'url' => 'Payment/paymentaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('estimate_id', $request->estimate_id, array('id' => 'estimate_id')) }}
		{{ Form::hidden('project_name', (isset($get_data[0]->project_name)) ? $get_data[0]->project_name : '', array('id' => 'project_name')) }}
		{{ Form::hidden('company_name', (isset($get_data[0]->company_name)) ? $get_data[0]->company_name : '', array('id' => 'company_name')) }}
		{{ Form::hidden('invoice_payment_date', (isset($get_data[0]->payment_date)) ? $get_data[0]->payment_date : '', array('id' => 'invoice_payment_date')) }}
		{{ Form::hidden('quot_date', (isset($get_data[0]->quot_date)) ? $get_data[0]->quot_date : '', array('id' => 'quot_date')) }}
		{{ Form::hidden('bank_id', $request->bank_id, array('id' => 'bank_id')) }}
		{{ Form::hidden('hididconcade', (isset($get_paymentdata[0]->paid_id)) ? $get_paymentdata[0]->paid_id : '', array('id' => 'hididconcade')) }}
		{{ Form::hidden('acc_no', $request->acc_no, array('id' => 'acc_no')) }}
		{{ Form::hidden('bankbranch_id', $request->bankbranch_id, array('id' => 'bankbranch_id')) }}
		{{ Form::hidden('type', 'edit', array('id' => 'type')) }}
	    {{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		@if (Auth::user()->userclassification == 1) 
	    	{{ Form::hidden('accessdate',Auth::user()->accessDate, array('id' => 'accessdate')) }}
		@else
	    	{{ Form::hidden('accessdate','0001-01-01', array('id' => 'accessdate')) }}
		@endif
	<!-- Start Heading --> 
		<div class="row hline">
		<div class="col-xs-8">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/payment.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_payment') }}</h2>
			<h2 class="pull-left mt15">・</h2>
			<h2 class="pull-left mt15 red">{{ trans('messages.lbl_edit') }}</h2>
		</div>
	</div>
	<div class="pb10"></div>
	<fieldset class="ml10 mr10">
		<div class="col-xs-12  mt15">
			<table class="tablealternate box80per mt10 ml15"
					cellspacing="0" cellpadding="3" id="swaptable">
				<tr class="tableheader" height="25px;">
					<th class="tac vam" width="5%">{{ trans('messages.lbl_sno') }}</th>
					<th class="tac vam" width="10%">{{ trans('messages.lbl_invoiceno') }}</th>
					<th class="tac vam" width="10%">{{ trans('messages.lbl_date') }}</th>
					<th class="tac vam" width="21%">{{ trans('messages.lbl_cusname') }}</th>
					<th class="tac vam" width="15%">{{ trans('messages.lbl_payday') }}</th>
					<th class="tac vam" width="17%">{{ trans('messages.lbl_amount') }}</th>
					<th class="tac vam" width="5%">{{ trans('messages.lbl_edit') }}</th>
				</tr>
				<?php
					$i = 0;
					$j = 1;
					$grandtotal = 0;
					for ($i = 0; $i < count($get_row); $i++) {
						if ($i%2 != 0) {
							$style = "background-color:#dff1f4;";
						} else {
							$style = "background-color:#FFFFFF;";
						}
						// if(!isset($get_row[$i]->oldRec)) { $get_row[$i]->oldRec=""; }
						// if(!isset($get_row[$i]->pre_paid_status)) { $get_row[$i]->pre_paid_status=""; }
						$grandtotal1 = 0;
						if (!empty($get_row[$i]->totalval)) {
							if ($get_row[$i]->tax != 2) {
								$totroundval = preg_replace("/,/", "", $get_row[$i]->totalval);
								$dispva = (($totroundval * intval($execute_tax[0]->Tax))/100);
								$dis = number_format($dispva);
								$dispval = preg_replace("/,/", "", $dis);
								$grandtotal1 = $totroundval + $dispval;
							} else {
								$totroundval = preg_replace("/,/", "", $get_row[$i]->totalval);
								$dispval = 0;
								$grandtotal1 = $totroundval + $dispval;
							}
						}
						$grandtotal += $grandtotal1;

						// $disp_flg = false;
						// $disp_class = 'tdcontent';
						// $checkboxChecked = " checked ";
						// if (($get_row[$i]->company_name == "入金") || 
						// 		(trim($get_row[$i]->company_name) == "pre_unpaid_record")
						// 		) {
						// 	$disp_flg = true;
						// 	$checkboxChecked = "";
						// } else {
						// 	if ((isset($get_row[$i]->oldRec) && $get_row[$i]->oldRec == "1") && ($get_row[$i]->paid_status != "1")) {$checkboxChecked = "";}
						// 	$disp_flg = false;
						// 	$disp_class = 'tdcontent';
						// }
				?>
				<tr style="<?php echo $style;?>">
					<td class="tdcontent vat" align="center">
						<?php
							echo $j; $j++;
						?>
					</td>
					<td class="tdcontent vat" align="center">
						{{ (isset($get_row[$i]->user_id)) ? $get_row[$i]->user_id : '' }}
					</td>
					<td class="tdcontent vat" align="center">
						{{ (isset($get_row[$i]->quot_date)) ? $get_row[$i]->quot_date : '' }}
					</td>
					<td class="tdcontent vat" align="left">
						<div class="ml5">
						{{ (isset($get_row[$i]->company_name)) ? $get_row[$i]->company_name : '' }}
						</div>
					</td>
					<td class="tdcontent vat" align="center">
						{{ (isset($get_row[$i]->payment_date)) ? $get_row[$i]->payment_date : '' }}
					</td>
					<td class="tdcontent vat" align="right">
						<div class="mr5" id = "div_totalval<?php echo $i; ?>">
						<?php echo number_format($grandtotal1); ?>
						</div>
						<input type = 'hidden' id = 'hdntotalval<?php echo $i; ?>' name = 'hdntotalval' 
										value = '<?php echo number_format($grandtotal1); ?>'>
					</td>
					<td class="tdcontent vat" align="center">
						<div style="display: inline-block;">
								<a onclick="return fnAddVal('<?php echo $i; ?>');" class="csrp"><i class="fa fa-plus" aria-hidden="true"></i></a>
							</div>
							<div class="ml10" style="display: inline-block;">
								<a onclick="return fnCanceVal('<?php echo $i; ?>');" class="csrp"><i class="fa fa-minus" aria-hidden="true"></i></a>
							</div>
					</td>
				</tr>
				<?php 
				}
				?>
			</table>
			<?php
				$style = '';
				if ($grandtotal > 0) {
					$style = "style = 'margin-right:4px;font-weight:bold;color:red'";
				} else if ($grandtotal <= 0) {
					$style = "style = 'margin-right:4px;font-weight:bold;color:green'";
				} else {
					$style = "style = 'margin-right:4px;font-weight:bold;'";
				}
			?>
			<table class="customerviewtable mt10 mb10" border="0" width="81.3%" >
				<tr>
					<td colspan="5" width="53.9%" height="25px;" style="border : none;">
					</td>
					<td class="totdisp fontbold bg_color_darkblue color_white" align="right" width="8%">
						<div class="mr5">
							{{ trans('messages.lbl_total') }}
						</div>
					</td>
					<td class="totdisp" align="right" width="17%">
						<div id="div_grandtotalval">
							<?php echo number_format($grandtotal); ?>
						</div>
						<input type = "hidden" id = "hdn_totalval" name = "hdn_totalval" 
							value = "<?php  $aaa = preg_replace('/,/', '', $get_paymentdata[0]->totalval) + 
												preg_replace('/,/', '', $get_paymentdata[0]->deposit_amount) + 
												preg_replace('/,/', '', $get_paymentdata[0]->bank_charge);
												echo number_format($grandtotal); ?>">
						<!-- <input type = "hidden" id = "hdn_totalval" name = "hdn_totalval" 
							value = "<?php  $aaa //= preg_replace('/,/', '', $get_paymentdata[0]->totalval) + 
												//preg_replace('/,/', '', $get_paymentdata[0]->deposit_amount) + 
												//preg_replace('/,/', '', $get_paymentdata[0]->bank_charge);
												//echo number_format($aaa); ?>"> -->
					</td>
					<td class="" width="5%"  style="border :none;">
					</td>
				</tr>
			</table>
		</div>
	</fieldset>
	<fieldset class="ml10 mr10">
		<div class="col-xs-12 mt5">
			<div class="col-xs-5">
				<div class="col-xs-4 text-right clr_blue">
					<label>{{ trans('messages.lbl_paymentdate') }}<span class="ml2 white"> * </span></label>
				</div>
				<div class="col-xs-6 pm0">
					{{ (isset($get_data[0]->payment_date)) ? $get_data[0]->payment_date : '' }}
				</div>
			</div>
			<div class="col-xs-7">
				<div class="col-xs-4 text-right clr_blue">
					<label>{{ trans('messages.lbl_payday') }}<span class="ml2 white"> * </span></label>
				</div>
				<div class="col-xs-8 pm0">
					{{ Form::text('payment_date',(isset($get_paymentdata[0]->payment_date)) ? $get_paymentdata[0]->payment_date : '',array(
											'id'=>'payment_date',
											'name' => 'payment_date',
											'class'=>'box23per form-control payment_date',
											'data-label' => trans('messages.lbl_payday'),
											'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
											'maxlength' => '10')) }}
					<label class="mt10 ml2 fa fa-calendar fa-lg" for="payment_date" aria-hidden="true"></label>
					@if (Session::get('userclassification') == 4)
							&nbsp;&nbsp;{{ Form::checkbox('accessrights', 1, (isset($get_paymentdata[0]->accessFlg)) ? $get_paymentdata[0]->accessFlg : 1, 
							['id' => 'accessrights']) }}
							&nbsp;<label for="accessrights"><span class="grey fb">{{ trans('messages.lbl_accessrights') }}</span></label>
					@endif
				</div>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-5">
				<div class="col-xs-4 text-right clr_blue">
					<label>{{ trans('messages.lbl_bank_name') }}<span class="ml2 white"> * </span></label>
				</div>
				<div class="col-xs-6 pm0">
					<select id = "bankname_sel_pay" name = "bankname_sel_pay" onchange="return fnbankaccountdetail(this.id)">
						<option value = ""></option>
						<?php foreach ($g_bank as $key => $g_bank) { ?>
							<option value = "<?php echo $g_bank->id; ?>"
							<?php if (($g_bank->banid == $get_data[0]->bankid) &&
							($g_bank->braid == $get_data[0]->bankbranchid) && 
							($g_bank->AccNo == $get_data[0]->acc_no)) {?> selected <?php } ?>><?php echo $g_bank->BankName." - ".$g_bank->AccNo; ?></option>
						<?php
							}
						?>
						</select>
				</div>
			</div>
			<div class="col-xs-7">
				<div class="col-xs-4 text-right clr_blue">
					<label>{{ trans('messages.lbl_schedduledamount') }}<span class="ml2 white"> * </span></label>
				</div>
				<div class="col-xs-6 pm0 fwb" id = "td_dispgrandtotal">
					¥{{ number_format($grandtotal) }}
				</div>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-5">
				<div class="col-xs-4 text-right clr_blue">
					<label>{{ trans('messages.lbl_branchname') }}<span class="ml2 white"> * </span></label>
				</div>
				<div class="col-xs-6 pm0" id="bankbranchname_sel">
				</div>
			</div>
			<div class="col-xs-7">
				<div class="col-xs-4 text-right clr_blue">
					<label>{{ trans('messages.lbl_payamount') }}<span class="ml2 white"> * </span></label>
				</div>
				<div class="col-xs-6 pm0">
					{{ Form::text('deposit_amount',(isset($get_paymentdata[0]->deposit_amount)) ? $get_paymentdata[0]->deposit_amount : '',array(
											'id'=>'deposit_amount',
											'name' => 'deposit_amount',
											'class'=>'box45per form-control tar',
											'data-label' => trans('messages.lbl_payday'),
											'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
										  	'onkeyup' => 'return fnMoneyFormat(this.id,"jp"), fnGetGrandTotal(this.name, this.value,'.$grandtotal.')',
										  	'onpaste' => 'return fnGetGrandTotal(this.name, this.value,'.$grandtotal.')',
										  	'onblur' => 'return fnSetZero11(this.id)',
										  	'onfocus' => 'return fnRemoveZero(this.id, this.name, this.value,'.$grandtotal.')',
										  	'onclick' => 'return fnRemoveZero(this.id, this.name, this.value,'.$grandtotal.')',
											'maxlength' => '10')) }}
					<input type = "checkbox" id = "copyAmount" onclick = "return fnCopyAmount(this);">
					<label for="copyAmount"><span class="grey fb" style="vertical-align: text-bottom">{{ trans('messages.lbl_asabove') }}</span></label>
				</div>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-5">
			</div>
			<div class="col-xs-7">
				<div class="col-xs-4 text-right clr_blue">
					<label>{{ trans('messages.lbl_balance') }}<span class="ml2 white"> * </span></label>
				</div>
				<div class="col-xs-6 pm0 fwb red" id="grandtotal_disp">
					¥ {{ $get_paymentdata[0]->totalval }}
				</div>
					{{ Form::hidden('totalval', '', array('id' => 'totalval')) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-5">
			</div>
			<div class="col-xs-7">
				<div class="col-xs-4 text-right clr_blue">
					<label>{{ trans('messages.lbl_bankcharges') }}<span class="ml2 white"> * </span></label>
				</div>
				<div class="col-xs-6 pm0">
					{{ Form::text('bank_charge',(isset($get_paymentdata[0]->bank_charge)) ? $get_paymentdata[0]->bank_charge : '',array(
											'id'=>'bank_charge',
											'name' => 'bank_charge',
											'class'=>'box45per form-control tar',
											'data-label' => trans('messages.lbl_payday'),
											'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
										  	'onkeyup' => 'return fnMoneyFormat(this.id,"jp"), fnGetGrandTotal(this.name, this.value,'.$grandtotal.')',
										  	'onpaste' => 'return fnGetGrandTotal(this.name, this.value,'.$grandtotal.')',
										  	'onblur' => 'return fnSetZero11(this.id)',
										  	'onfocus' => 'return fnRemoveZero(this.id, this.name, this.value,'.$grandtotal.')',
										  	'onclick' => 'return fnRemoveZero(this.id, this.name, this.value,'.$grandtotal.')',
											'maxlength' => '10')) }}
					<input type = "checkbox" id = "copyAmountbal" onclick = "return fnCopyAmountbal(this);">
					<label for="copyAmountbal"><span class="grey fb" style="vertical-align: text-bottom">{{ trans('messages.lbl_asabove') }}</span></label>
				</div>
			</div>
		</div>
		<div class="col-xs-12 mt5">
				<div class="col-xs-1 box15per text-right clr_blue">
					<label>{{ trans('messages.lbl_remarks') }}<span class="ml2 white"> * </span></label>
				</div>
				<div class="col-xs-8 box60per pm0">
					{{ Form::textarea('remarks',(isset($get_paymentdata[0]->remarks)) ? $get_paymentdata[0]->remarks : '',array(
											'id'=>'remarks',
											'name' => 'remarks',
											'class'=>'box100per form-control',
											'data-label' => trans('messages.lbl_payday'),
											'size' => '30x4')) }}
				</div>
		</div>
		<div class="col-xs-12 mt5">
		</div>
	</fieldset>
	<fieldset style="background-color: #DDF1FA;" class="ml10 mr10">
		<div class="form-group">
			<div align="center" class="mt5">
				<button type="button" class="btn edit btn-warning box100" onclick="return fnPaymentRegistration(2);">
						<i class="fa fa-edit" aria-hidden="true"></i>
						{{ trans('messages.lbl_update') }}
				</button>
				@if($request->backflg==1)
					<a onclick="javascript:gotopayment();" 
						class="btn btn-danger box120 white">
								<i class="fa fa-times" aria-hidden="true"></i> 
									{{trans('messages.lbl_cancel')}}
					</a>
				@else
					<a onclick="javascript:gotoindex('index','{{$request->mainmenu}}');" 
						class="btn btn-danger box120 white">
								<i class="fa fa-times" aria-hidden="true"></i> 
									{{trans('messages.lbl_cancel')}}
					</a>
				@endif
			</div>
		</div>
	</fieldset>
<!-- End Heading -->
	{{ Form::close() }}
</article>
<script>
	fnbankaccountdetail('bankname_sel_pay');
</script>
</div>
@endsection