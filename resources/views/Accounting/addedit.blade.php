@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/accounts.js') }}
{{ HTML::script(asset('resources/assets/js/Setting.js')) }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
@php use App\Http\Helpers; @endphp
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var dates = '<?php echo date('Y-m-d'); ?>';
	var accessDate = '<?php echo Auth::user()->accessDate; ?>';
	var userclassification = '<?php echo Auth::user()->userclassification; ?>';
	$(document).ready(function() {
		if (userclassification == 1) {
			accessDate = setNextDay(accessDate);
			setDatePickerAfterAccessDate("dob", accessDate);
		} else {
			setDatePicker("dob");
		}
		$("#cashbutton").attr("disabled", "disabled");
	});
</script>
<style type="text/css">
	.clr_brown{
		 color: #9C0000 ! important;
	}
	.ime_mode_disable {
		ime-mode:disabled;
	}
	select{
		min-width: 100px;
	}
</style>
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->

<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_1">
	{{ Form::open(array('name'=>'frmaccountingaddedit', 
						'id'=>'frmaccountingaddedit', 
						'url' => 'Accounting/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files' => true,
						'method' => 'POST')) }}
		{{ Form::hidden('mainmenu',$request->mainmenu, array('id' => 'mainmenu')) }}
		{{ Form::hidden('hidGetDate','0', array('id' => 'hidGetDate')) }}
		{{ Form::hidden('edit_flg', $request->edit_flg, array('id' => 'edit_flg')) }}
		{{ Form::hidden('editId', $request->editId, array('id' => 'editId')) }}
		<div class="row hline pm0">
			<div class="col-xs-12">
				<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/pettycash.jpg') }}">
				<h2 class="pull-left pl5 mt10">
					{{ trans('messages.lbl_cash') }}
				</h2>
				<h2 class="pull-left mt10">・</h2>
				@if($request->edit_flg == 1)
					<h2 class="pull-left mt10 red">{{ trans('messages.lbl_edit') }}</h2>
				@elseif($request->edit_flg == 2)
					<h2 class="pull-left mt10 blue">{{ trans('messages.lbl_copy') }}</h2>
				@else
					<h2 class="pull-left mt10 green">{{ trans('messages.lbl_register') }}</h2>
				@endif
			</div>
		</div>
		<div class="col-xs-12 pt10">
			<div class="col-xs-4" style="text-align: left;margin-left: -25px;">
				<button type="button" id="cashbutton" class="btn btn-success box25per pt9 pb8">
					<span class="fa fa-plus"></span>&nbsp;{{ trans('messages.lbl_cash') }}
				</button> 
				<button type="button" onclick="javascript:addedit('cashTransfer','{{ $request->mainmenu }}');" class="btn btn-success box33per pt9 pb8">
					<span class="fa fa-plus"></span>&nbsp;{{ trans('messages.lbl_transfer') }}
				</button> 
				<button type="button" onclick="javascript:addedit('cashAutoDebit','{{ $request->mainmenu }}');"  class="btn btn-success box39per pt9 pb8">
					<span class="fa fa-plus"></span>&nbsp;{{ trans('messages.lbl_autodebit') }}
				</button> 
			</div>
			<div class="col-xs-8 pull-right" style="text-align: right;padding: 0px;">
				@if($request->edit_flg != 1)
					{{ Form::text('accDate',(isset($editData[0]->date)) ? $editData[0]->date : '',
							array('id'=>'accDate', 
								'name' => 'accDate',
								'data-label' => trans('messages.lbl_Date'),
								'autocomplete' =>'off',
								'class'=>' box13per form-control dob')) }}
					<label class="mt10 ml2 fa fa-calendar fa-lg" for="accDate" aria-hidden="true">
					</label>
					<a href="javascript:getdate();" class="anchorstyle">
						<img title="Current Date" class="box15" 
							src="{{ URL::asset('resources/assets/images/add_date.png') }}"></a>
				@else
					{{ Form::text('accDate',(isset($editData[0]->date)) ? $editData[0]->date : '',
							array('id'=>'accDate', 
								'name' => 'accDate',
								'readonly' => 'true',
								'data-label' => trans('messages.lbl_Date'),
								'autocomplete' =>'off',
								'class'=>' box13per form-control disabled')) }}
				@endif
				@if($request->edit_flg != 1)
				<button type="button" id="salarybutton" style="background-color: purple; color: #fff;" 
					onclick="return Getsalarypopup();"  
					class="btn box15per pt9 pb8 ml2">
					{{ trans('messages.lbl_getsalary') }}
				</button> 
				<button type="button" id="loanbutton" style="background-color: purple; color: #fff;" 
					onclick="return Getloanpopup('','');"
					class="btn box15per pt9 pb8 ml2">
					{{ trans('messages.lbl_getloan') }}
				</button> 
				<button type="button" id="invoicebutton" style="background-color: purple; color: #fff;"
					onclick="return GetInvoicepopup();"
					class="btn box15per pt9 pb8 ml2">
					{{ trans('messages.lbl_getinvoiceDtl') }}
				</button> 
				<button type="button" id="expensesDatabutton" style="background-color: purple; color: #fff;" 
					onclick="return GetExpensespopup('');"  
					class="btn box15per pt9 pb8 ml2">
					{{ trans('messages.lbl_getexpensesDtls') }}
				</button> 
				@endif
			</div>
		</div>
	<div class="col-xs-12 pl5 pr5" ondragstart="return false;" ondrop="return false;">
	<fieldset>
	<div id="errorSectiondisplay" align="center" class="box100per"></div>
		<!-- <div class="col-xs-12 mt10">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_Date') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
					{{ Form::text('date',(isset($editData[0]->date)) ? $editData[0]->date : '',array('id'=>'date', 
															'name' => 'date',
															'data-label' => trans('messages.lbl_Date'),
															'autocomplete' =>'off',
															'class'=>'box11per form-control pl5 dob')) }}
					<label class="mt10 ml2 fa fa-calendar fa-lg" for="date" aria-hidden="true"></label>
					&nbsp;&nbsp;
					<a href="javascript:getdate('Cash');" class="anchorstyle">
						<img title="Current Date" class="box15" src="{{ URL::asset('resources/assets/images/add_date.png') }}"></a></label>
			</div>
		</div> -->

		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_bank') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				@if($request->edit_flg == 1 && $editData[0]->completedFlg == 1)
					{{ Form::text('bankName',$editData[0]->Bank_NickName.'-'.$editData[0]->accountNumberFrom, array('id'=>'bankName', 
										'name' => 'bankName',
										'readonly' => 'true',
										'data-label' => trans('messages.lbl_bank'),
										'class'=>'pl5 widthauto disabled')) }}
					{{ Form::hidden('bank', $editData[0]->bankIdFrom.'-'.$editData[0]->accountNumberFrom , array('id' =>'bank','name' =>'bank')) }}
				@else
					{{ Form::select('bank',[null=>'']+$bankDetail,(isset($editData[0]->bankIdFrom)) ? $editData[0]->bankIdFrom.'-'.$editData[0]->accountNumberFrom : '',
						array('name' =>'bank',
								'id'=>'bank',
								'data-label' => trans('messages.lbl_bank'),
								'onchange'=>'fnGetbankDetails();',
								'class'=>'pl5 widthauto'))}}
				@endif
				<?php $style = "style='display:none'";
					if (isset($editData[0]) && $editData[0]->transferId != "" && $editData[0]->transcationType == 1) {
						$style = "style=''";
				}?>

				@if($request->edit_flg == 1 && $editData[0]->completedFlg == 1)
					{{ Form::text('transferName', (isset($editDataTo[0]->Bank_NickName)) ? $editDataTo[0]->Bank_NickName.'-'.$editData[0]->accountNumberTo : '', 
								array('id'=>'transferName', 
										'name' => 'transferName',
										'readonly' => 'true',$style,
										'data-label' => trans('messages.lbl_bank'),
										'class'=>'pl5 widthauto disabled')) }}
					{{ Form::hidden('transfer', $editData[0]->bankIdTo.'-'.$editData[0]->accountNumberTo , array('id' =>'transfer','name' =>'transfer')) }}
				@else
					{{ Form::select('transfer',[null=>'']+$bankDetail,(isset($editData[0]->bankIdTo)) ? $editData[0]->bankIdTo.'-'.$editData[0]->accountNumberTo : '',
							array('name' =>'transfer',
									'id'=>'transfer',
									'data-label' =>  trans('messages.lbl_bank'),$style,
									'class'=>'pl5 widthauto'))}}
				@endif
			</div>
		</div>

		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_transaction') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				<?php $disableRadio = "";
					if (isset($editData[0]) && $editData[0]->transferId != "" && $editData[0]->transcationType == 1) {
						$editData[0]->transcationType = 3; 
					} 
				?>
				@if($request->edit_flg == 1 && $editData[0]->completedFlg != 0)
					@if($editData[0]->transcationType == 1)
						{{ Form::hidden('transtype', $editData[0]->transcationType, array('id' =>'transtype','name' =>'transtype')) }}
						{{ trans('messages.lbl_debit') }}
					@elseif($editData[0]->transcationType == 2)
						{{ Form::hidden('transtype', $editData[0]->transcationType, array('id' =>'transtype','name' =>'transtype')) }}
						{{ trans('messages.lbl_credit') }}
					@elseif($editData[0]->transcationType == 3)
						{{ Form::hidden('transtype', $editData[0]->transcationType, array('id' =>'transtype','name' =>'transtype')) }}
						{{ trans('messages.lbl_transfer') }}
					@else
						{{ Form::hidden('transtype', $editData[0]->transcationType, array('id' =>'transtype','name' =>'transtype')) }}
						{{ trans('messages.lbl_income') }}
					@endif
				@else
				<label style="font-weight: normal;">
					{{ Form::radio('transtype', '1', (isset($editData[0]->transcationType) && ($editData[0]->transcationType)=="1") ? $editData[0]->transcationType : '', 
							array('id' =>'transtype',
									'name' => 'transtype',
									$disableRadio,
									'onkeypress'=>'return numberonly(event)',
									'style' => 'margin-bottom:5px;',
									'data-label' => trans('messages.lbl_transaction'),
									'onchange' => 'debitAmount()',
									'checked' => 'true',
									'class' => '')) }}
					&nbsp {{ trans('messages.lbl_debit') }} &nbsp
				</label>
				<label style="font-weight: normal;">
					{{ Form::radio('transtype', '2', (isset($editData[0]->transcationType) && ($editData[0]->transcationType)=="2") ? $editData[0]->transcationType : '', 
							array('id' =>'transtype1',
									'name' => 'transtype',
									$disableRadio,
									'style' => 'margin-bottom:5px;',
									'data-label' => trans('messages.lbl_transaction'),
									'onchange' => 'creditAmount()',
									'class' => 'transtype1')) }}
					&nbsp {{ trans('messages.lbl_credit') }} &nbsp
				</label>
				<label style="font-weight: normal;">
					{{ Form::radio('transtype', '3', (isset($editData[0]->transcationType) && ($editData[0]->transcationType)=="3") ? $editData[0]->transcationType : '', 
							array('id' =>'transtype2',
									'name' => 'transtype',
									$disableRadio,
									'style' => 'margin-bottom:5px;',
									'data-label' => trans('messages.lbl_transaction'),
									'onchange' => 'banktransferselect()',
									'class' => '')) }}
					&nbsp {{ trans('messages.lbl_transfer') }} &nbsp
				</label>
				<label style="font-weight: normal;">
					{{ Form::radio('transtype', '4', (isset($editData[0]->transcationType) && ($editData[0]->transcationType)=="4") ? $editData[0]->transcationType : '', 
								array('id' =>'transtype3',
										'name' => 'transtype',
										$disableRadio,
										'style' => 'margin-bottom:5px;',
										'data-label' => trans('messages.lbl_income'),
										'onchange' => 'creditAmount()',
										'class' => '')) }}
					&nbsp {{ trans('messages.lbl_income') }} &nbsp
				</label>
				@endif
				@if(isset($editData[0]))
					{{ Form::hidden('oldTransType', $editData[0]->transcationType, array('id' => 'oldTransType')) }}
					{{ Form::hidden('oldTransferId', $editData[0]->transferId, array('id' => 'oldTransferId')) }}
				@endif
			</div>
		</div>

		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_content') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::select('content',[null=>'']+$contentDetail,(isset($editData[0]->content)) ? $editData[0]->content : '',
							array('name' =>'content',
									'id'=>'content',
									'data-label' =>  trans('messages.lbl_content'),
									'class'=>'pl5 widthauto'))}}
				@php
					$tbl_name = 'acc_contentsetting';
				@endphp
				<button type="button" style="background-color: green; color: #fff;" 
					id="salarybutton" class="btn box10per fa fa-plus"
					onclick = "javascript:settingpopupsinglefield('twotextpopup','{{ $tbl_name}}')" >
					{{ trans('messages.lbl_add') }}
				</button> 
			</div>
		</div>

		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>
					{{ trans('messages.lbl_amount') }}
					<span class="black ml2">&#47;</span>
					{{ trans('messages.lbl_fee') }}
					<span class="fr ml2 red"> * </span>
				</label>
			</div>
			<div class="col-xs-9">
				{{ Form::text('amount',(isset($editData[0]->amount)) ? number_format($editData[0]->amount) : 0,
						array('id'=>'amount', 
								'name' => 'amount',
								'style'=>'text-align:right;',
								'maxlength' => 10,
								'autocomplete' =>'off',
								'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
								'onchange'=>'return fnCancel_check();',
								'onblur' => 'return fnSetZero11(this.id);',
								'onfocus' => 'return fnRemoveZero(this.id);',
								'onclick' => 'return fnRemoveZero(this.id);',
								'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
								($request->edit_flg == 1 && isset($editData[0]->completedFlg) && ($editData[0]->completedFlg != 0)?'readonly':''), 
								'data-label' => trans('messages.lbl_amount'),
								'class'=>'box15per form-control pl5 ime_mode_disable numonly')) }}

				<span class="feeclass"> / </span>
				{{ Form::text('fee',(isset($editData[0]->fee)) ? number_format($editData[0]->fee) : 0,
						array('id'=>'fee', 
								'name' => 'fee',
								'style'=>'text-align:right;',
								'maxlength' => 10,
								'autocomplete' =>'off',
								'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
								'onchange'=>'return fnCancel_check();',
								'onblur' => 'return fnSetZero11(this.id);',
								'onfocus' => 'return fnRemoveZero(this.id);',
								'onclick' => 'return fnRemoveZero(this.id);',
								'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
								($request->edit_flg == 1 && isset($editData[0]->completedFlg) && ($editData[0]->completedFlg != 0)?'readonly':''), 
								'data-label' => trans('messages.lbl_fee'),
								'class'=>'box7per form-control pl5 ime_mode_disable feeclass numonly')) }}
			</div>
		</div>
		
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_remarks') }}<span class="fr ml2 red" style="visibility: hidden;"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::textarea('remarks',(isset($editData[0]->remarks)) ? $editData[0]->remarks : '', 
						array('name' => 'remarks',
								'autocomplete' =>'off',
							  	'class' => 'box40per form-control',
							  	'size' => '30x4')) }}
			</div>
		</div>
		
		<div class="col-xs-12 mt10"></div>
	</fieldset>

	<fieldset style="background-color: #DDF1FA;">
		<div class="form-group">
			<div align="center" class="mt5">
				@if($request->edit_flg == 1)
					<button type="submit" class="btn btn-warning add box100 addeditprocess ml5" title="Edit">
						<i class="fa fa-edit" aria-hidden="true"></i> {{ trans('messages.lbl_update') }}
					</button>
				@else
					<button type="submit" class="btn btn-success add box100 addeditprocess ml5">
						<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
					</button>
				@endif
				<a href = "javascript:gotoindexpage('Cash','{{$request->mainmenu}}');" 
					class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{ trans('messages.lbl_cancel') }}
				</a>
			</div>
		</div>
	</fieldset>
</div>
{{ Form::close() }}
{{ Form::open(array('name'=>'addeditcancel', 'id'=>'addeditcancel', 
						'url' => 'Accounting/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,'method' => 'POST')) }}

	{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
	{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
	{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}

{{ Form::close() }}

<div id="getsalarypopup" class="modal fade">
	<div id="login-overlay">
		<div class="modal-content">
			<!-- Popup will be loaded here -->
		</div>
	</div>
</div>

<div id="getloanpopup" class="modal fade">
	<div id="login-overlay">
		<div class="modal-content">
			<!-- Popup will be loaded here -->
		</div>
	</div>
</div>

<div id="getinvoicepopup" class="modal fade">
	<div id="login-overlay">
		<div class="modal-content">
			<!-- Popup will be loaded here -->
		</div>
	</div>
</div>

<div id="getExpensespopup" class="modal fade">
	<div id="login-overlay">
		<div class="modal-content">
			<!-- Popup will be loaded here -->
		</div>
	</div>
</div>

<div id="showpopup" class="modal fade" style="width: 775px;">
	<div id="login-overlay">
		<div class="modal-content">
			<!-- Popup will be loaded here -->
		</div>
	</div>
</div>

</article>
</div>
@endsection