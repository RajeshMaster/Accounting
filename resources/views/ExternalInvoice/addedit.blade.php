@extends('layouts.app')
@section('content')

<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var accessDate = '<?php echo Auth::user()->accessDate; ?>';
	var userclassification = '<?php echo Auth::user()->userclassification; ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
	$(document).ready(function() {
		if (userclassification == 1) {
			accessDate = setNextDay(accessDate);
			setDatePickerAfterAccessDate("quot_date", accessDate);
			setDatePickerAfterAccessDate("payment_date", accessDate);
		} else {
			setDatePicker("quot_date");
			setDatePicker("payment_date");
		}
	});
</script>

{{ HTML::script('resources/assets/js/common.js') }}
{{ HTML::script('resources/assets/js/externalinvoice.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}

<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="external" class="DEC_flex_wrapper " data-category="external external_sub_2">
	{{ Form::open(array('name'=>'frmextinvoiceaddedit', 
						'id'=>'frmextinvoiceaddedit', 
						'url' => 'ExternalInvoice/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	{{ Form::hidden('editflg', $request->editflg, array('id' => 'editflg')) }}
	{{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
	{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
	{{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
	{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	{{ Form::hidden('filter', $request->filter, array('id' => 'filter')) }}
	{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	{{ Form::hidden('sorting', $request->sorting, array('id' => 'sorting')) }}
	{{ Form::hidden('lastsortvalue', $request->lastsortvalue, array('id' => 'lastsortvalue')) }}
	{{ Form::hidden('lastordervalue', $request->lastordervalue, array('id' => 'lastordervalue')) }}
	{{ Form::hidden('ordervalue', $request->ordervalue, array('id' => 'ordervalue')) }}
	{{ Form::hidden('totalrecords', $request->totalrecords, array('id' => 'totalrecords')) }}
	{{ Form::hidden('currentRec', $request->currentRec, array('id' => 'currentRec')) }}
	{{ Form::hidden('id', $request->id ,array('id' => 'id')) }}
	{{ Form::hidden('rowCount','',array('id' => 'rowCount')) }}
	{{ Form::hidden('invoiceId', $request->invoiceId ,array('id' => 'invoiceId')) }}
	@if (Auth::user()->userclassification == 1) 
		{{ Form::hidden('accessdate',Auth::user()->accessDate, array('id' => 'accessdate')) }}
	@else
	    {{ Form::hidden('accessdate','0001-01-01', array('id' => 'accessdate')) }}
	@endif
	{{ Form::hidden('tablecount', $amtcount, array('id' => 'tablecount')) }}
	<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-8">
			<img class="pull-left box25 mt10" src="{{ URL::asset('resources/assets/images/invoice.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_invoice') }}</h2>
			<h2 class="pull-left mt15">・</h2>
			@if($request->editflg=="copy")
			<h2 class="pull-left mt15 green">{{ trans('messages.lbl_multible') }}</h2>
			@elseif($request->editflg=="edit" || $request->editflg=="viewedit")
			<h2 class="pull-left mt15 red">{{ trans('messages.lbl_edit') }}</h2>
			@else
			<h2 class="pull-left mt15 green">{{ trans('messages.lbl_register') }}</h2>@endif
		</div>
	</div>
	<div class="pb10"></div>
	<fieldset class="ml10">
		<div class="col-xs-12  mt15">
			<div class="col-xs-6">
        	<fieldset class="ml10 mr10" style="border: 1px solid #79d7ec !important;">
				<div class="col-xs-12 mt10">
					<div class="col-xs-4 text-right clr_blue">
						<label>{{ trans('messages.lbl_usernamesign') }}<span class="fr ml2 red"> * </span></label>
					</div>
					<div class="col-xs-6 pm0">
						{{Form::select('userId',[null=>'']+$getUserDetails,(isset($invoicedata[0]->userId)) ? $invoicedata[0]->userId : '', 
							array('name' => 'userId',
									'id'=>'userId',
									'style'=>'min-width: 30%;',
									'class'=>'pl5',
									'data-label' => trans('messages.lbl_usernamesign'),
									'onchange' => 'return fnGetBankDetails(this.value)')) }}
					</div>
				</div>
				<div class="col-xs-12 mt5">
					<div class="col-xs-4 text-right clr_blue">
						<label>{{ trans('messages.lbl_projecttitle') }}
							<span class="fr ml2 red"> * </span></label>
					</div>
					<div class="col-xs-8 pm0">
						{{ Form::text('projectName',(isset($invoicedata[0]->projectName)) ? $invoicedata[0]->projectName : '',
								array('id'=>'projectName',
									'name' => 'projectName',
									'maxlength' => '32',
									'data-label' => trans('messages.lbl_projecttitle'),
									'class'=>'form-control pl5 box60per')) }}
					</div>
				</div>
				<div class="col-xs-12 mt5">
					<div class="col-xs-4 text-right clr_blue">
						<label>{{ trans('messages.lbl_projecttype') }}<span class="fr ml2 red"> * </span></label>
					</div>
					<div class="col-xs-8 pm0">
						{{ Form::select('projectType',[null=>''],(isset($invoicedata[0]->projectType)) ? $invoicedata[0]->projectType : '', 
								array('name' => 'projectType',
									'id'=>'projectType',
									'style'=>'min-width: 30%;',
									'data-label' => trans('messages.lbl_projecttype'),
									'class'=>'pl5'))}}
					</div>
				</div>
				<div class="col-xs-12 mt5">
					<div class="col-xs-4 text-right clr_blue">
						<label>{{ trans('messages.lbl_bank_name') }}
							<span class="fr ml2 red"> </span></label>
					</div>
					<div class="col-xs-8 pm0">
						<label id="invbankname"> </label>
					</div>
				</div>
				<div class="col-xs-12 mt5">
					<div class="col-xs-4 text-right clr_blue">
						<label>{{ trans('messages.lbl_branch_name') }}
							<span class="fr ml2 red">  </span></label>
					</div>
					<div class="col-xs-8 pm0">
						<label id="invbranchname"> </label>
					</div>
				</div>
				<div class="col-xs-12 mt5">
					<div class="col-xs-4 text-right clr_blue">
						<label>{{ trans('messages.lbl_account') }}
							<span class="fr ml2 red">  </span></label>
					</div>
					<div class="col-xs-8 pm0">
						<label id="invaccount"> </label>
					</div>
				</div>
				<div class="col-xs-12 mt5 mb10">
					<div class="col-xs-4 text-right clr_blue">
						<label>{{ trans('messages.lbl_accountholder') }}
							<span class="fr ml2 red">  </span></label>
					</div>
					<div class="col-xs-8 pm0">
						<label id="invactholder"> </label>
					</div>
				</div>
			</fieldset>
			</div>
			<div class="col-xs-6">
			<fieldset style="border: 1px solid #79d7ec !important;">
				<div class="col-xs-12 mt10">
					<div class="col-xs-4 text-right clr_blue">
						<label>{{ trans('messages.lbl_invoicedate') }}<span class="fr ml2 red"> * </span></label>
					</div>
					<div class="col-xs-8 pm0">
						<?php if($request->editflg =="copy") { $invoicedata[0]->quot_date = ""; } ?>
							{{ Form::text('quot_date',(isset($invoicedata[0]->quot_date)) ? $invoicedata[0]->quot_date : '',
								array('id'=>'quot_date',
									'name' => 'quot_date',
									'autocomplete'=>'off',
									'class'=>'box30per form-control quot_date',
									'data-label' => trans('messages.lbl_invoicedate'),
									'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
									'maxlength' => '10')) 
							}}
						<label class="mt10 ml2 fa fa-calendar fa-lg" for="quot_date" aria-hidden="true"></label>
					</div>
				</div>
				<div class="col-xs-12 mt5">
					<div class="col-xs-4 text-right clr_blue">
						<label>{{ trans('messages.lbl_tax') }}<span class="fr ml2 red"> * </span></label>
					</div>
					<div class="col-xs-8 pm0">
						<label>
							<div class="fll">
								{{ Form::radio('tax', '1', (isset($invoicedata[0]->tax) && ($invoicedata[0]->tax)=="1") ? $invoicedata[0]->tax : '', 
									array('id' =>'tax1',
											'name' => 'tax',
											'data-label' => trans('messages.lbl_tax'),
											'checked' => 'checked',
											'class' => 'amtrup')) 
								}}
							</div>
							<div class="fll">
								<label class="ml5 mt3 black fwn" for="tax1">
								{{ trans('messages.lbl_withoutax') }}</label>
							</div>
						</label>
						<label>
							<div class="fll">
								{{ Form::radio('tax', '2', (isset($invoice[0]->tax) && ($invoice[0]->tax)=="2") ? $invoice[0]->tax : '', 
									array('id' =>'tax2',
											'name' => 'tax',
											'data-label' => trans('messages.lbl_tax'),
											'class' => 'amtrup')) }}
							</div>
							<div class="fll">
								<label class="ml5 mt3 black fwn" for="tax2">
								{{ trans('messages.lbl_withtax') }}</label>
							</div>
						</label>
					</div>
				</div>
				<div class="col-xs-12 mt5">
					<div class="col-xs-4 text-right clr_blue">
						<label>{{ trans('messages.lbl_paymentday') }}
							<span class="fr ml2 red"> * </span>
						</label>
					</div>
					<div class="col-xs-8 pm0" style="display: inline-block;">
						{{ Form::text('payment_date', (isset($invoicedata[0]->payment_date)?$invoicedata[0]->payment_date:""),
							array('id'=>'payment_date',
									'autocomplete'=>'off',
									'name' => 'payment_date',
									'class'=>'box30per form-control payment_date',
									'data-label' => trans('messages.lbl_paymentday'),
									'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
									'maxlength' => '10')) 
						}}
					<label class="mt10 ml2 fa fa-calendar fa-lg" for="payment_date" aria-hidden="true"></label>
					</div>
				</div>
				<div class="col-xs-12 mt5 mb110">
					<div class="col-xs-4 text-right clr_blue">
						<label>{{ trans('messages.lbl_personalmark') }}
							<span class="fr ml2 red"> * </span>
						</label>
					</div>
					<div class="col-xs-8 pm0" style="display: inline-block;">
						{{ Form::select('personalmark',[null=>''],'',
								array('name' => 'personalmark',
									'id'=>'personalmark',
									'data-label' => trans('messages.lbl_projectype'),
									'style'=>'min-width: 30%;background-color:#EEEEEE;',
									'disabled' => 'disabled',
									'class'=>'pl5'))
						}}
					</div>
				</div>
			</fieldset>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<script>
			fnGetBrachByAjax('trading_destination_sel');
		</script>
		<div class="col-xs-12 mt15">
			<table class="box98per CMN_tblfixed  ml10" id = "workspectable">
				<colgroup>
					<col class="tdhead box13per">
					<col class="tdhead box7per">
					<col class="tdhead box7per">
					<col class="tdhead box9per">
					<col class="tdhead box10per">
					<col class="tdhead box3per">
					<col class="tdhead box2per">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
					<tr class="">
						<th style="background: #66b8d6">
							{{ trans('messages.lbl_workspec') }}</th>
						<th style="background: #66b8d6">
							{{ trans('messages.lbl_quantity') }}</th>
						<th style="background: #66b8d6">
							{{ trans('messages.lbl_unitprice') }}</th>
						<th style="background: #66b8d6">
							{{ trans('messages.lbl_amount') }}</th>
						<th style="background: #66b8d6">
							{{ trans('messages.lbl_remarks') }}</th>
						<th style="background: #66b8d6" colspan="2" 
							onclick="javascript:cloneaddblade();">
							<a class=" pull-center ml10 imgtableheight box3per csrp"
								id="add_row" style="cursor: pointer; color: white;">
								{{ trans('messages.lbl_add') }}</a>
						</th>
					</tr>
				</thead>
				<tbody id="forccappend">
					<?php 
						if($amtcount<15) {
							$a = 15;
						} else {
							$a = $amtcount;
						}
					?>
					@for ($i = 1; $i <= $a; $i++)
						<?php $workloop = "work_specific"; ?>
						<?php $quantityloop = "quantity"; ?>
						<?php $unit_priceloop = "unit_price"; ?>
						<?php $amountloop = "amount"; ?>
						<?php $remarksloop = "remarks"; ?>
						<tr id="othercc_<?php echo $i ?>">
							<td>
								<div class="">
								{{ Form::text('work_specific'.$i,(isset($invoicedata[$i-1]->$workloop)) ? $invoicedata[$i-1]->$workloop : '', 
									array('id'=>'work_specific'.$i,
										'name' => 'work_specific'.$i,
										'maxlength' => '20',
										'autocomplete'=>'off',
										"onkeyup" => "return fnControlAddOrRemove('$i')",
										"onfocus" => "return fnControlAddOrRemove('$i')",
										"onblur" => "return fnControlAddOrRemove('$i')",
										'class'=>'input_text box99per form-control pl5 mt3')) }}
								{{ Form::hidden('fordisable_hdn'.$i, 0,
										array('id'=>'fordisable_hdn'.$i,
											'name' => 'fordisable_hdn'.$i)) 
								}}
								</div>
							</td>
							<td>
								<div class="">
								{{ Form::text('quantity'.$i,(isset($invoicedata[$i-1]->$quantityloop)) ? $invoicedata[$i-1]->$quantityloop : '', 
									array('id'=>'quantity'.$i,
										'name' => 'quantity'.$i,
										'maxlength' => '7',
										'onkeypress' => 'return isDotNumberKey(event,this.value,1)',
										'autocomplete'=>'off',
										'ondragstart'=>'return false',
										'ondrop'=>'return false',
										"onkeyup" => "return fnCalculateAmount('$i', '', '',$a)",
										"onfocus" => "return fnControlAddOrRemove('$i')",
										"onblur" => "return fnControlAddOrRemove('$i')",
										'class'=>'box99per form-control pl5 mt3 tar')) }}
								</div>
							</td>
							<td>
								<div class="">
								<?php 
									$color = "";
									$bordercolor = "";
									if (isset($invoice[$i-1]->$unit_priceloop)) {
										if ($invoice[$i-1]->$unit_priceloop < 0) {
											$color = 'color:red;';
											$bordercolor = 'border-color:red;';
										}
									} 
								?>
								{{ Form::text('unit_price'.$i,(isset($invoicedata[$i-1]->$unit_priceloop)) ? $invoicedata[$i-1]->$unit_priceloop : '', 
									array('id'=>'unit_price'.$i,
											'name' => 'unit_price'.$i,
											'maxlength' => '11',
											'style' => $color.$bordercolor,
											'autocomplete'=>'off',
											'onkeypress' => 'return isNumberKeywithminus(event)',
											'ondragstart'=>'return false',
											'ondrop'=>'return false',
											"onkeyup" => "return fnCalculateAmount('$i', '','',$a)",
											"onfocus" => "return fnControlAddOrRemove('$i')",
											"onblur" => "return fnControlAddOrRemove('$i')",
											'class'=>'box99per form-control pl5 mt3 tar numonly')) }}
								</div>
							</td>
							<td>
								<div class="">
								<?php 
									$color = "";
									$bordercolor = "";
									if (isset($invoicedata[$i-1]->$amountloop)) {
										if ($invoicedata[$i-1]->$amountloop < 0) {
											$color = 'color:red;';
											$bordercolor = 'border-color:red;';
										}
									} 
								?>
								{{ Form::text('amount'.$i,(isset($invoicedata[$i-1]->$amountloop)) ? $invoicedata[$i-1]->$amountloop : '', 
									array('id'=>'amount'.$i,
										'name' => 'amount'.$i,
										'style' => $color.$bordercolor,
										'disabled' => 'true',
										'class'=>'box99per form-control pl5 mt3 tar')) }}
								</div>
							</td>
							<td style="padding: 0px;">
								<div class="" style="padding: 0px;padding-left: 5px;">
								{{ Form::text('remarks'.$i,(isset($invoicedata[$i-1]->$remarksloop)) ? $invoicedata[$i-1]->$remarksloop : '', 
									array('id'=>'remarks'.$i,
										'name' => 'remarks'.$i,
										'maxlength' => '10',
										'autocomplete'=>'off',
										"onkeyup" => "return fnControlAddOrRemove('$i')",
										"onfocus" => "return fnControlAddOrRemove('$i')",
										"onblur" => "return fnControlAddOrRemove('$i')",
										'class'=>'box99per form-control pl5 mt3 tal')) }}
								</div>
							</td>
							<td style="text-align: center;">
								<div style="display: inline-block;">
									<a onclick="return fnAddTR('<?php echo $i; ?>');" 
									id="addrow{{ $i }}" name = "addrow"　class="csrp">
									<i class="fa fa-plus" aria-hidden="true"></i>
									</a>
								</div>
								<div class="ml10" style="display: inline-block;">
									<a onclick="return fnRemoveTR('<?php echo $i; ?>');"　id="removerow{{ $i }}" name = "removerow" class="csrp"><i class="fa fa-minus" aria-hidden="true"></i></a>
								</div>
								<td style="padding: 0px;" style="text-align: center;">
									<div style="display: inline-block;">
									<?php
										if($i==1){
											$style="cursor: pointer; display: none;";
										} else {
											$style="cursor: pointer;";
										}
									?>
									<a class="pull-center ml5 imgtableheight dispnone"
										id="removeiconid_{{ $i }}"
										onclick="javascript:cloneremoveabove(this);"
										style="{{$style}}" >
										<img class="pull-center box30 ml5"
										style="max-height: 19px; max-width: 19px;"
										src="{{ URL::asset('resources/assets/images/close.png') }}">
									</a>
									</div>
								</td>
							</td>
						</tr>
					@endfor	
				</tbody>
			</table>
		</div>
		<script>
			fnAddTR('<?php echo $i; ?>');
		</script>
		{{ Form::hidden('tableamountcount', $i-1, array('id' => 'tableamountcount')) }}
		<div>
			<div class="box98per mt5" style="display: inline-block;">
				<div class="box53per text-right" style="display: inline-block;">
					<label class="clr_blue mt8 mr5">
						{{ trans('messages.lbl_totamt') }}
					</label>
				</div>
				<div class="box46per pull-right" style="display: inline-block;">
					{{ Form::text('totval',(isset($extinvoice[0]->totalval))?$extinvoice[0]->totalval:'',array('id'=>'totval',
										'name' => 'totval',
										'disabled' => 'true',
										'class'=>'box36per form-control pl5 mt3 tar')) }}
				</div>
			</div>
		</div>
		<div>
			<div class="col-xs-6 mt3 mb20">
				@for($i = 1; $i <= 5; $i++)
					<?php $noteloop = "special_ins".$i; ?>
					<?php $g_id = substr(("0" . $i), -2); ?>
					<div class="col-xs-2 text-right" style="width: 108px !important">
						<label class="clr_blue">
							{{ trans('messages.lbl_notices') }}{{ "0".$i }}
						</label>
					</div>
					<div>
						{{ Form::text('special_ins'.$i,(isset($invoice[0]->$noteloop))?$invoice[0]->$noteloop:'', 
							array('id'=>'special_ins'.$i,
									'name' => 'special_ins'.$i,
									'class'=>'box60per form-control pl5 mt3')) 
						}}
						{{ Form::hidden('noticesel'.$i, '', 
							array('id' => 'noticesel'.$i)) 
						}}
					</div>
				@endfor
			</div>
			<div class="col-xs-6 mt3 mb20">
				<div class="text-left">
					<label class="clr_blue">
						{{ trans('messages.lbl_memo') }}
					</label>
				</div>
				<div class="mt8">
					{{ Form::textarea('memo',(isset($invoice[0]->memo)) ? $invoice[0]->memo : '', array('id'=>'memo',
										'name' => 'memo',
										'class'=>'box100per form-control',
										'data-label' => trans('messages.lbl_payday'),
										'style' => 'height: 146px !important;',
										'size' => '30x7')) 
					}}
				</div>
			</div>
		</div>
		{{ Form::hidden('tablespecialcount', $i-1, array('id' => 'tablespecialcount')) }}
	</fieldset>
	<fieldset style="background-color: #DDF1FA;">
		<div class="form-group">
			<div align="center" class="mt5">
			@if($request->editflg =="viewedit")
				{{ Form::hidden('invoiceId', $invoicedata[0]->invoiceId, array('id' => 'invoiceId')) }}
				<button type="submit" class="btn edit btn-warning box100 addeditprocess">
					<i class="fa fa-edit" aria-hidden="true"></i> {{ trans('messages.lbl_update') }}
				</button>
				<a onclick="javascript:gotoviewpage('edit','{{$request->editid}}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				</a>
			@else
				@if($request->editflg =="edit")
				{{ Form::hidden('invoiceId', $invoicedata[0]->invoiceId, array('id' => 'invoiceId')) }}
					<button type="submit" class="btn edit btn-warning box100 addeditprocess">
						<i class="fa fa-edit" aria-hidden="true"></i> 
						{{ trans('messages.lbl_update') }}
					</button>
				@else
					<button type="submit" class="btn btn-success add box100 ml5 addeditprocess">
						<i class="fa fa-plus" aria-hidden="true"></i> {{trans('messages.lbl_register')}}
					</button>
				@endif
				<a onclick="javascript:gotoindexpage();" 
					class="btn btn-danger box120 white">
					<i class="fa fa-times"  aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				</a>
			@endif
			</div>
		</div>
	</fieldset>

	@if(!empty($selectval))
	<script type="text/javascript">
		fnGetBankDetails('{{ $selectval }}')
	</script>
	@endif
	<!-- End Heading -->
	{{ Form::close() }}

	{{ Form::open(array('name'=>'frmextinvoiceaddeditcancel','id'=>'frmextinvoiceaddeditcancel', 
				'url' => 'ExternalInvoice/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
				'files'=>true,'method' => 'POST')) }}

		{{ Form::hidden('editflg', $request->editflg, array('id' => 'editflg')) }}
		{{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('filter', $request->filter, array('id' => 'filter')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('sorting', $request->sorting, array('id' => 'sorting')) }}

	{{ Form::close() }}
</article>
</div>
<div id="noticepopup" class="modal fade">
	<div id="login-overlay">
		<div class="modal-content">
			<!-- Popup will be loaded here -->
		</div>
	</div>
</div>
@endsection