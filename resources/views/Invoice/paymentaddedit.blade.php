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
<?php if($request->frompayindex=="1") { ?>
<article id="sales" class="DEC_flex_wrapper" data-category="sales sales_sub_4">
<?php } else { ?>
<article id="sales" class="DEC_flex_wrapper" data-category="sales sales_sub_2">
<?php } ?>
	{{ Form::open(array('name'=>'frminvoiceaddedit', 
						'id'=>'frminvoiceaddedit', 
						'url' => 'Invoice/paymentaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('estimate_id', $request->estimate_id, array('id' => 'estimate_id')) }}
		{{ Form::hidden('invoiceid', $request->estimate_id, array('id' => 'invoiceid')) }}
		{{ Form::hidden('sortOptn',$request->sortOptn , array('id' => 'sortOptn')) }}
	    {{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
		{{ Form::hidden('project_name', (isset($get_data[0]->project_name)) ? $get_data[0]->project_name : '', array('id' => 'project_name')) }}
		{{ Form::hidden('company_name', (isset($get_data[0]->company_name)) ? $get_data[0]->company_name : '', array('id' => 'company_name')) }}
		{{ Form::hidden('invoice_payment_date', (isset($get_data[0]->payment_date)) ? $get_data[0]->payment_date : '', array('id' => 'invoice_payment_date')) }}
		{{ Form::hidden('quot_date', (isset($get_data[0]->quot_date)) ? $get_data[0]->quot_date : '', array('id' => 'quot_date')) }}
		{{ Form::hidden('date_month', $date_month, array('id' => 'date_month')) }}
		{{ Form::hidden('bank_id', $request->bank_id, array('id' => 'bank_id')) }}
		{{ Form::hidden('hididconcade', '', array('id' => 'hididconcade')) }}
		{{ Form::hidden('acc_no', $request->acc_no, array('id' => 'acc_no')) }}
		{{ Form::hidden('bankbranch_id', $request->bankbranch_id, array('id' => 'bankbranch_id')) }}
		{{ Form::hidden('type', 'add', array('id' => 'type')) }}
	    {{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}

		{{ Form::hidden('estimateid', $request->estimateid, array('id' => 'estimateid')) }}
		{{ Form::hidden('filter', $request->filter, array('id' => 'filter')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	    {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('topclick', $request->topclick, array('id' => 'topclick')) }}
		{{ Form::hidden('ordervalue', $request->ordervalue, array('id' => 'ordervalue')) }}
		{{ Form::hidden('totalrecords', $request->totalrecords, array('id' => 'totalrecords')) }}
		{{ Form::hidden('currentRec', $request->currentRec, array('id' => 'currentRec')) }}
		{{ Form::hidden('backflg', $request->backflg, array('id' => 'backflg')) }}
		@if (Auth::user()->userclassification == 1) 
	    	{{ Form::hidden('accessdate',Auth::user()->accessDate, array('id' => 'accessdate')) }}
		@else
	    	{{ Form::hidden('accessdate','0001-01-01', array('id' => 'accessdate')) }}
		@endif
	<!-- Start Heading --> 
		<div class="row hline">
		<div class="col-xs-8">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/invoices-icon-3.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_paymentcreated') }}</h2>
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
					<th class="tac vam" width="17%">{{ trans('messages.lbl_payamount') }}</th>
					<th class="tac vam" width="5%">{{ trans('messages.lbl_edit') }}</th>
				</tr>
				<?php
					$i = 0;
					$j = 1;
					$grandtotal = 0;
					for ($i = 0; $i < count($disp_record); $i++) {
						if ($i%2 != 0) {
							$style = "background-color:#dff1f4;";
						} else {
							$style = "background-color:#FFFFFF;";
						}
						if(!isset($disp_record[$i]['oldRec'])) { $disp_record[$i]['oldRec']=""; }
						if(!isset($disp_record[$i]['pre_paid_status'])) { $disp_record[$i]['pre_paid_status']=""; }
						$grandtotal1 = 0;
						if (!empty($disp_record[$i]['totalval'])) {
							if (($disp_record[$i]['tax'] != 2) && ((trim($disp_record[$i]['company_name']) != "入金") && $disp_record[$i]['oldRec'] != "1")) {
								$totroundval = preg_replace("/,/", "", $disp_record[$i]['totalval']);
								$dispva = (($totroundval * intval($execute_tax[0]->Tax))/100);
								$dis = number_format($dispva);
								$dispval = preg_replace("/,/", "", $dis);
								$grandtotal1 = $totroundval + $dispval;
							} else {
								$totroundval = preg_replace("/,/", "", $disp_record[$i]['totalval']);
								$dispval = 0;
								$grandtotal1 = $totroundval + $dispval;
							}
						}
						$grandtotal += $grandtotal1;

						$disp_flg = false;
						$disp_class = 'tdcontent';
						$checkboxChecked = " checked ";
						if (($disp_record[$i]['company_name'] == "入金") || 
								(trim($disp_record[$i]['company_name']) == "pre_unpaid_record")
								) {
							$disp_flg = true;
							$checkboxChecked = "";
						} else {
							if ((isset($disp_record[$i]['oldRec']) && $disp_record[$i]['oldRec'] == "1") && ($disp_record[$i]['paid_status'] != "1")) {$checkboxChecked = "";}
							$disp_flg = false;
							$disp_class = 'tdcontent';
						}
				?>
				<tr style="<?php echo $style;?>">
				<?php if ($disp_record[$i]['company_name'] == "pre_unpaid_record") { $rowSpan = 8; $title = "NEW RECORD"; } else { $rowSpan = 0;$title = ""; } ?>
					<td colspan="<?php echo $rowSpan;?>" class="<?php echo $disp_class; ?>" 
						<?php if ($disp_record[$i]['company_name'] == "pre_unpaid_record") { ?>
						align="left"
						<?php } else {?> align="center" <?php }?> 
						style="vertical-align:top;">
						<?php
							if ((trim($disp_record[$i]['company_name']) != "入金") && (trim($disp_record[$i]['company_name']) != "pre_unpaid_record")) {
								echo $j; $j++;
							}?>
							<div class="color_blue pl40 font15"><b><?php echo $title;?></b></div>
					</td>
					<?php if ($disp_record[$i]['company_name'] == "pre_unpaid_record") { continue; }?>
					<td class="vat <?php echo $disp_class; ?>" align="center">
						{{ (isset($disp_record[$i]['user_id'])) ? $disp_record[$i]['user_id'] : '' }}
					</td>
					<td class="vat <?php echo $disp_class; ?>" align="center">
						{{ (isset($disp_record[$i]['quot_date'])) ? $disp_record[$i]['quot_date'] : '' }}
					</td>
					<td class="vat <?php echo $disp_class; ?>" align="left">
						<div class="ml5">
						{{ (isset($disp_record[$i]['company_name'])) ? $disp_record[$i]['company_name'] : '' }}
						</div>
					</td>
					<td class="vat <?php echo $disp_class; ?>" align="center">
						{{ (isset($disp_record[$i]['payment_date'])) ? $disp_record[$i]['payment_date'] : '' }}
					</td>
					<td class="vat font_bold <?php echo $disp_class; ?>" align="right" >
						<div class="mr5">
							{{ number_format($grandtotal1) }}
						</div>
					</td>
					<td class="vat font_bold <?php echo $disp_class; ?>" align="right" >
						<div class="mr5" id = "div_totalval<?php echo $i; ?>">
							<?php
							$grandtotal2 = $grandtotal1;
								if ($disp_record[$i]['oldRec'] == "1" && (trim($disp_record[$i]['company_name']) == "未払残高")) {
									$grandtotal = $grandtotal - $grandtotal1;
									$grandtotal1 = 0;
									$dotOccur = strpos($balance_invoice_temp, "-");
									if ($dotOccur !== false) { 
	  										$pre_paidval=substr($balance_invoice_temp, 1);
	  									} else {
	  										$pre_paidval=$balance_invoice_temp;
	  									}
										if($pre_paidval <= $value){
											$grandtotal1=$balance_invoice_temp;
											$grandtotal = $grandtotal + $grandtotal1;
											$checkboxChecked = " checked ";
										}
										
								}else{
									if($disp_record[$i]['pre_paid_status'] == "3"){
										$grandtotal = $grandtotal - $grandtotal1;
										$grandtotal1 = 0;
									}
								}
								echo number_format($grandtotal1);
							?>
						</div>
						<input type = 'hidden' id = 'hdntotalval<?php echo $i; ?>' name = 'hdntotalval' 
						value = '<?php echo number_format($grandtotal2); ?>'>
					</td>
					<td class="tdcontent vat" align="center">
						<?php

						if (!$disp_flg || ($i == 0)) {
							?>
							<input type = "checkbox" id = "addcheck<?php echo $i; ?>" name = "addcheck" 
							value = "1" onclick = "return fnCheckboxVal1(<?php echo $i; ?>);" <?php echo $checkboxChecked; echo $checkboxdisabled; ?>>
							<input type = "hidden" id = "hidid<?php echo $i; ?>" name = "hidid" value = "{{ (isset($disp_record[$i]['id'])) ? $disp_record[$i]['id'] : '' }}">
							<?php
						}
						?>
					</td>
				</tr>
				<?php
					}
				?>
				<tr>
					<td colspan="8" class="boxhei30 bg_color_white">
					</td>
				</tr>
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
			<table class="customerviewtable ml15 mt10 mb10" border="0" width="80%" >
				<tr>
					<td colspan="5" width="68%" height="25px;" style="border : none;">
					</td>
					<td class="totdisp fontbold bg_color_darkblue color_white" align="right" width="10%">
						<div class="mr5">
							{{ trans('messages.lbl_total') }}
						</div>
					</td>
					<td class="totdisp" align="right" width="17%">
						<div <?php echo $style; ?> id="div_grandtotalval">
							<?php
							echo number_format($grandtotal);

							if ($grandtotal < 1) {
								$disabled = "disabled";	
							} else {
								$disabled = "";
							}
							?>
						</div>
						<input type = "hidden" id = "hdn_totalval" name = "hdn_totalval" value = "<?php echo number_format($grandtotal); ?>">
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
					{{ Form::text('payment_date',(isset($estimate[0]->payment_date)) ? $estimate[0]->payment_date : '',array(
											'id'=>'payment_date',
											'name' => 'payment_date',
											'class'=>'box23per form-control payment_date',
											'data-label' => trans('messages.lbl_payday'),
											'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
											'maxlength' => '10')) }}
					<label class="mt10 ml2 fa fa-calendar fa-lg" for="payment_date" aria-hidden="true"></label>
					@if (Session::get('userclassification') == 4)
					&nbsp;&nbsp;{{ Form::checkbox('accessrights', 1, 1, 
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
							($g_bank->AccNo == $get_data[0]->acc_no)) {?>selected<?php } ?>><?php echo $g_bank->BankName." - ".$g_bank->AccNo; ?></option>
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
					¥ {{ number_format($grandtotal) }}
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
					{{ Form::text('deposit_amount',(isset($estimate[0]->deposit_amount)) ? $estimate[0]->deposit_amount : '',array(
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
					¥ {{ number_format($grandtotal) }}
				</div>
					{{ Form::hidden('totalval', number_format($grandtotal), array('id' => 'totalval')) }}
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
					{{ Form::text('bank_charge',(isset($estimate[0]->bank_charge)) ? $estimate[0]->bank_charge : '',array(
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
					{{ Form::textarea('remarks',(isset($estimate[0]->remarks)) ? $estimate[0]->remarks : '',array(
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
			@if(1==1)
					<button type="button" class="btn btn-success add box100 ml5" <?php  if ($grandtotal >= 1) { ?> onclick="return fnPaymentRegistration(1);" <?php } else { ?> style="background: grey; border-color: #808080;cursor: default;" <?php } ?>>
					<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
					</button>
			@else
					<button type="button" class="btn edit btn-warning addeditprocess box100">
							<i class="fa fa-edit" aria-hidden="true"></i>{{ trans('messages.lbl_update') }}
					</button>
			@endif
			@if($request->frompayindex == 1)
				<a onclick="javascript:gotoindex('../Payment/index','{{$request->mainmenu}}');" 
						class="btn btn-danger box120 white">
								<i class="fa fa-times" aria-hidden="true"></i> 
									{{trans('messages.lbl_cancel')}}
				</a>
			@elseif($request->frmview != 1)
				<a onclick="javascript:gotoindex('index','{{$request->mainmenu}}');" 
						class="btn btn-danger box120 white">
								<i class="fa fa-times" aria-hidden="true"></i> 
									{{trans('messages.lbl_cancel')}}
				</a>
			@else
				<a onclick="javascript:gotoindex('specification','{{$request->mainmenu}}');" 
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
	fnCheckboxVal1("nil");
	fnbankaccountdetail('bankname_sel_pay');
</script>
</div>
@endsection