@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/accounts.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
@php use App\Http\Helpers; @endphp
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var accessDate = '<?php echo Auth::user()->accessDate; ?>';
	var userclassification = '<?php echo Auth::user()->userclassification; ?>';
	$(document).ready(function() {
		if (userclassification == 1) {
			accessDate = setNextDay(accessDate);
			setDatePickerAfterAccessDate("dob", accessDate);
		} else {
			setDatePicker("dob");
		}
	});
</script>
<style type="text/css">
	.clr_brown{
		 color: #9C0000 ! important;
	}
	.ime_mode_disable {
		ime-mode:disabled;
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
	  
<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/pettycash.jpg') }}">
			<h2 class="pull-left pl5 mt10">
				{{ trans('messages.lbl_cash') }}
			</h2>
			<h2 class="pull-left mt10">・</h2>
			<h2 class="pull-left mt10">
				<span class="green">
					{{ trans('messages.lbl_register') }}
				</span>
			</h2>
		</div>
</div>
	<div class="col-xs-12 pt10">
			<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
				<a href="#" class="btn btn-success box20per disabled">
					<span class="fa fa-plus"></span> {{ trans('messages.lbl_cash') }}</a>

				<a href="javascript:addedit('cashTransfer','{{ $request->mainmenu }}');" 
					class="btn btn-success box25per">
					<span class="fa fa-plus"></span>{{ trans('messages.lbl_transfer') }}</a>

				<a href="javascript:addedit('cashAutoDebit','{{ $request->mainmenu }}');"
					class="btn btn-success box25per">
					<span class="fa fa-plus"></span>{{ trans('messages.lbl_autodebit') }}</a>

			</div>
		</div>
<div class="col-xs-12 pl5 pr5" ondragstart="return false;" ondrop="return false;">
	<fieldset>
	
		<div class="col-xs-12 mt10">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_Date') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
					{{ Form::text('date',(isset($expcash_sql[0]->date)) ? $expcash_sql[0]->date : '',array('id'=>'date', 
															'name' => 'date',
															'data-label' => trans('messages.lbl_Date'),
															'autocomplete' =>'off',
															'class'=>'box11per form-control pl5 dob')) }}
					<label class="mt10 ml2 fa fa-calendar fa-lg" for="date" aria-hidden="true"></label>
					&nbsp;&nbsp;{{ Form::checkbox('getDate','', '', ['id' => 'getDate',	
																		'onchange' => 'currentDate()',
																		]) }}</label>
			</div>
		</div>

		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_bank') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::select('bank',[null=>'']+$bankDetail,(isset($expcash_sql[0]->bankname)) ? 
														$expcash_sql[0]->bankname.'-'.$expcash_sql[0]->bankaccno : '',						array('name' =>'bank',
																	'id'=>'bank',
																	'data-label' => trans('messages.lbl_bank'),
																	'onchange'=>'fnGetbankDetails();',
																	'class'=>'pl5 widthauto'))}}
				{{ Form::select('transfer',[null=>'']+$bankDetail,'', array('name' =>'transfer',
																	'id'=>'transfer',
																	'data-label' =>  trans('messages.lbl_bank'),
																	'style' => 'display:none;',
																	'class'=>'pl5 widthauto'))}}
			</div>
		</div>

		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_transaction') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				<label style="font-weight: normal;">
				<?php $disableRadio = "";
					if ($request->cashflg == 2 && $expcash_sql[0]->transaction_flg == 3) {
									// $disableRadio = "disabled='disabled'"; 
				}?>
					{{ Form::radio('transtype', '1', (isset($expcash_sql[0]->transaction_flg) && ($expcash_sql[0]->transaction_flg)=="1") ? $expcash_sql[0]->transaction_flg : '', array('id' =>'transtype',
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
					{{ Form::radio('transtype', '2', (isset($expcash_sql[0]->transaction_flg) && ($expcash_sql[0]->transaction_flg)=="2") ? $expcash_sql[0]->transaction_flg : '', array('id' =>'transtype1',
																'name' => 'transtype',
																$disableRadio,
																'style' => 'margin-bottom:5px;',
																'data-label' => trans('messages.lbl_transaction'),
																'onchange' => 'creditAmount()',
																'class' => 'transtype1')) }}
					&nbsp {{ trans('messages.lbl_credit') }} &nbsp
				</label>
				<label style="font-weight: normal;">
					{{ Form::radio('transtype', '3', (isset($expcash_sql[0]->transaction_flg) && ($expcash_sql[0]->transaction_flg)=="3") ? $expcash_sql[0]->transaction_flg : '', array('id' =>'transtype2',
																'name' => 'transtype',
																$disableRadio,
																'style' => 'margin-bottom:5px;',
																'data-label' => trans('messages.lbl_transaction'),
																'onchange' => 'banktransferselect()',
																'class' => '')) }}
					&nbsp {{ trans('messages.lbl_transfer') }} &nbsp
				</label>

				<label style="font-weight: normal;">
					{{ Form::radio('transtype', '4', (isset($expcash_sql[0]->transaction_flg) && ($expcash_sql[0]->transaction_flg)=="3") ? $expcash_sql[0]->transaction_flg : '', 
								array('id' =>'transtype3',
																'name' => 'transtype',
																$disableRadio,
																'style' => 'margin-bottom:5px;',
																'data-label' => trans('messages.lbl_income'),
																'onchange' => 'creditAmount()',
																'class' => '')) }}
					&nbsp {{ trans('messages.lbl_income') }} &nbsp
				</label>
			</div>
		</div>

		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_content') }}<span class="fr ml2 red" style="visibility: hidden"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::text('content',(isset($expcash_sql[0]->content)) ? $expcash_sql[0]->content : '',
									array('id'=>'content', 
															'name' => 'content',
															'data-label' => trans('messages.lbl_content'),
															'class'=>'box31per form-control pl5')) }}
			</div>
		</div>

		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_amount') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::text('amount',(isset($expcash_sql[0]->amount)) ? number_format($expcash_sql[0]->amount) : 0,array('id'=>'amount', 
														'name' => 'amount',
														'style'=>'text-align:right;',
														'maxlength' => 10,
														'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
														'onchange'=>'return fnCancel_check();',
														'onblur' => 'return fnSetZero11(this.id);',
														'onfocus' => 'return fnRemoveZero(this.id);',
														'onclick' => 'return fnRemoveZero(this.id);',
														'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
														'data-label' => trans('messages.lbl_amount'),
														'class'=>'box15per form-control pl5 ime_mode_disable')) }}

				<span> / </span>
				{{ Form::text('fee',(isset($expcash_sql[0]->fee)) ? number_format($expcash_sql[0]->fee) : 0,array('id'=>'fee', 
														'name' => 'fee',
														'style'=>'text-align:right;',
														'maxlength' => 10,
														'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
														'onchange'=>'return fnCancel_check();',
														'onblur' => 'return fnSetZero11(this.id);',
														'onfocus' => 'return fnRemoveZero(this.id);',
														'onclick' => 'return fnRemoveZero(this.id);',
														'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
														'data-label' => trans('messages.lbl_fee'),
														'class'=>'box7per form-control pl5 ime_mode_disable')) }}
			</div>
		</div>
		
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_remarks') }}<span class="fr ml2 red" style="visibility: hidden;"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::textarea('remarks',(isset($expcash_sql[0]->remark_dtl)) ? $expcash_sql[0]->remark_dtl : '', 
						array('name' => 'remarks',
							  'class' => 'box40per form-control','size' => '30x4')) }}
			</div>
		</div>
		
		<div class="col-xs-12 mt10"></div>
	</fieldset>

	<fieldset style="background-color: #DDF1FA;">
		<div class="form-group">
			<div align="center" class="mt5">
				<button type="submit" class="btn btn-success add box100 addeditprocess ml5">
					<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
				</button>
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

	{{ Form::close() }}

</article>
</div>
@endsection