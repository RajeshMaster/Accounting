@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/accbankdetails.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::script('resources/assets/js/lib/additional-methods.min.js') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	$(document).ready(function() {
		setDatePicker("txt_startdate");
	});
</script>
<style type="text/css">
	.ime_mode_disable {
		ime-mode:disabled;
	}
</style>

	<div class="CMN_display_block" id="main_contents">

	<!-- article to select the main&sub menu -->

	<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_4">
		{{ Form::open(array('name'=>'bankdetailaddedit', 'id'=>'bankdetailaddedit','type'=>'file', 'url' => 'AccBankDetail/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
			{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
			{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
			{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
			{{ Form::hidden('id', $request->id , array('id' => 'id')) }}
			{{ Form::hidden('bankid', $request->bankid , array('id' => 'bankid')) }}
			{{ Form::hidden('bankname', $request->bankname , array('id' => 'bankname')) }}
			{{ Form::hidden('branchname', $request->branchname , array('id' => 'branchname')) }}
			{{ Form::hidden('accno', $request->accno , array('id' => 'accno')) }}
			{{ Form::hidden('intialDate', $request->intialDate , array('id' => 'intialDate')) }}
			{{ Form::hidden('bankids', $request->bankids , array('id' => 'bankids')) }}
			{{ Form::hidden('branchids', $request->branchids , array('id' => 'branchids')) }}
			{{ Form::hidden('balbankid', $request->balbankid , array('id' => 'balbankid')) }}
			{{ Form::hidden('editFlg', $request->editFlg , array('id' => 'editFlg')) }}
			{{ Form::hidden('intialDate', $request->intialDate , array('id' => 'intialDate')) }}
			{{ Form::hidden('intialValue', $request->intialValue , array('id' => 'intialValue')) }}
			{{ Form::hidden('date_month', $request->date_month  , array('id' => 'date_month')) }}
			{{ Form::hidden('checkflg', $request->checkflg , array('id' => 'checkflg')) }}
			{{ Form::hidden('startdate', $request->startdate , array('id' => 'startdate')) }}

	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/bank_1.png') }}">
			<h2 class="pull-left pl5 mt10">
					{{ trans('messages.lbl_balance_entry') }}<span class="">・</span>@if ($request->editFlg=="1")<span style="color:red">{{ trans('messages.lbl_edit') }}</span>@else<span style="color:green">{{ trans('messages.lbl_register') }}</span>@endif
			</h2>
		</div>
	</div>

	<div class="col-xs-12 pl5 pr5">
	<fieldset>
		<div class="col-xs-12 mt15">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_bank_name') }}<span class="fr ml2 red"> &nbsp;&nbsp;</span></label>
			</div>

			<div class="col-xs-9">
				<a style="color: blue;"> 
					<b>
						{{ $request->bankname }}
					</b>
				</a>
			</div>
		</div>

		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_branch_name') }}<span class="fr ml2 red"> &nbsp;&nbsp;</span></label>
			</div>
			<div class="col-xs-9">
				{{ $request->branchname }}
			</div>
		</div>

		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_accnumber') }}<span class="fr ml2 red"> &nbsp;&nbsp;</span></label>
			</div>
			<div class="col-xs-9">
				{{ $request->accno }}
			</div>
		</div>

		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_Start_date') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				@if($request->editFlg =='1')
					{{ Form::text('txt_startdate',$request->intialDate,array(
										'id'=>'txt_startdate',
										'name' => 'txt_startdate',
										'autocomplete' =>'off',
										'class'=>'box12per txt_startdate form-control ime_mode_disable',
										'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
										'data-label' => trans('messages.lbl_Start_date'),
										'maxlength' => '10')) }}
				@else
					{{ Form::text('txt_startdate','',array(
										'id'=>'txt_startdate',
										'name' => 'txt_startdate',
										'autocomplete' =>'off',
										'class'=>'box12per txt_startdate form-control ime_mode_disable',
										'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
										'data-label' => trans('messages.lbl_Start_date'),
										'maxlength' => '10')) }}
				@endif

				<label class="fa fa-calendar fa-lg" for="txt_startdate" aria-hidden="true"></label>
			</div>

		</div>

		<div class="col-xs-12 mt5 mb15">

			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_amount') }}<span class="fr ml2 red"> * </span></label>
			</div>

			<div class="col-xs-9 CMN_display_block">
				@if($request->editFlg=="1")
					{{ Form::text('txt_salary',$request->intialValue,array(
										'id'=>'txt_salary',
										'name' => 'txt_salary',
										'maxlength' => '15',
										'style'=>'text-align:right;padding-right:4px;',
										'onkeypress' => 'return isNumberKey(event)',
										'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
										'class'=>'box25per ime_mode_disable numonly',
										'data-label' => trans('messages.lbl_amount'))) }}
				@else

					{{ Form::text('txt_salary','',array(
										'id'=>'txt_salary',
										'name' => 'txt_salary',
										'maxlength' => '15',
										'style'=>'text-align:right;padding-right:4px;',
										'onkeypress' => 'return isNumberKey(event)',
										'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
										'class'=>'box25per ime_mode_disable numonly',
										'data-label' => trans('messages.lbl_amount'))) }}
				@endif
			</div>
		</div>
	</fieldset>
	</div>

	<fieldset style="background-color: #DDF1FA;">
		<div class="form-group">
			<div align="center" class="mt5">
				@if($request->editFlg=="1")
					<button type="submit" class="btn edit btn-warning box100 addeditprocess">
						<i class="fa fa-edit" aria-hidden="true"></i> {{ trans('messages.lbl_update') }}
					</button>
					<a onclick="javascript:gotoindexpage('1','{{ $request->mainmenu }}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
					</a>
				@else
					<button type="submit" class="btn btn-success add box100 ml5 addeditprocess">
						<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
					</button>
					<a onclick="javascript:gotoindexpage('2','{{ $request->mainmenu }}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
					</a>
				@endif
			</div>
		</div>
	</fieldset>
	</div>
	</article>

	{{ Form::close() }}

	{{ Form::open(array('name'=>'bankdetailaddeditcancel', 'id'=>'bankdetailaddeditcancel', 'url' => 'Bankdetails/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
			{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
			{{ Form::hidden('plimit', $request->plimit, array('id' => 'plimit')) }}
			{{ Form::hidden('page', $request->page, array('id' => 'page')) }}
			{{ Form::hidden('id', $request->id, array('id' => 'id')) }}
			{{ Form::hidden('bankid', $request->bankid , array('id' => 'bankid')) }}
			{{ Form::hidden('bankname', $request->bankname , array('id' => 'bankname')) }}
			{{ Form::hidden('branchname', $request->branchname , array('id' => 'branchname')) }}
			{{ Form::hidden('accno', $request->accno , array('id' => 'accno')) }}
			{{ Form::hidden('intialDate', $request->intialDate , array('id' => 'intialDate')) }}
			{{ Form::hidden('bankids', $request->bankids , array('id' => 'bankids')) }}
			{{ Form::hidden('branchids', $request->branchids , array('id' => 'branchids')) }}
			{{ Form::hidden('balbankid', $request->balbankid , array('id' => 'balbankid')) }}
			{{ Form::hidden('editFlg', $request->editFlg , array('id' => 'editFlg')) }}
			{{ Form::hidden('intialDate', $request->intialDate , array('id' => 'intialDate')) }}
			{{ Form::hidden('balance', $request->balance , array('id' => 'balance')) }}
			{{ Form::hidden('date_month', $request->date_month  , array('id' => 'date_month')) }}
			{{ Form::hidden('checkflg', $request->checkflg , array('id' => 'checkflg')) }}
			{{ Form::hidden('startdate',  $request->startdate , array('id' => 'startdate')) }}
	</div>

@endsection

