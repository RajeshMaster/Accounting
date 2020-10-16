@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/salaryplus.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::script('resources/assets/js/lib/additional-methods.min.js') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var dates = '<?php echo date('Y-m-d'); ?>';
	$(document).ready(function() {
		setDatePicker("txt_startdate");
	});
	$(document).ready(function(){
		$("#bank option").each(function()
		{
			if ($(this).val() == "999") {
				$(this).css('font-weight','bold');
				$(this).css('color','brown');
			}
		});
	});
</script>
<style type="text/css">
	.ime_mode_disable {
		ime-mode:disabled;
	}
</style>
	<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_10">
	@if(isset($detedit))
		{{ Form::model($detedit, array('name'=>'salaryplusaddedit', 'id'=>'salaryplusaddedit', 'files'=>true,'type'=>'file', 'method' => 'POST','class'=>'form-horizontal','url' => 'Salaryplus/addeditprocessnew?time='.date('YmdHis') ) ) }}
	@else
		{{ Form::open(array('name'=>'salaryplusaddedit', 'id'=>'salaryplusaddedit','type'=>'file', 'url' => 'Salaryplus/addeditprocessnew?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
	@endif
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	    {{ Form::hidden('empname', $request->empname , array('id' => 'empname')) }}
		{{ Form::hidden('id', $request->id , array('id' => 'id')) }}
		{{ Form::hidden('ids', $request->ids , array('id' => 'ids')) }}
	    {{ Form::hidden('empname', $request->empname , array('id' => 'empname')) }}
	    {{ Form::hidden('total', $request->total , array('id' => 'total')) }}
	    {{ Form::hidden('salary', $request->salary , array('id' => 'salary')) }}
		{{ Form::hidden('gobackflg',$request->gobackflg , array('id' => 'gobackflg')) }}
		{{ Form::hidden('editflg', $request->editflg , array('id' => 'editflg')) }}
		{{ Form::hidden('bankaccno', '' , array('id' => 'bankaccno')) }}
		{{ Form::hidden('total', $request->total , array('id' => 'total')) }}
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/salary_1.png') }}">
			<h2 class="pull-left pl5 mt10">
					{{ trans('messages.lbl_salaryplus') }}<span class="">・</span>@if ($request->editflg=="2")<span style="color:red">{{ trans('messages.lbl_edit') }}</span>@elseif ($request->editflg=="3")<span style="color:green">{{ trans('messages.lbl_copy') }}</span>@else<span style="color:green">{{ trans('messages.lbl_register') }}</span>@endif
			</h2>
		</div>
	</div>
	<div class="col-xs-12 pl5 pr5">
	<fieldset>
		<div class="col-xs-12 mt15">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_empid') }}<span class="fr ml2 red"> &nbsp;&nbsp; </span></label>
			</div>
			<div class="col-xs-4">
				<b>
					<span style="color:blue;">
						{{ $request->id }}
					</span>
				</b>
			</div>
			<div class="col-xs-5" style="">
				<label class="col-xs-3 clr_blue" style="text-align:right;padding:0px;">{{ trans('messages.lbl_totamt') }}<span class="fr ml2 red"> &nbsp;&nbsp; </span></label>
				<div class="col-xs-2" style="text-align:right;padding:0px;">{{ number_format($request->total) }}</div>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_empName') }}<span class="fr ml2 red"> &nbsp;&nbsp; </span></label>
			</div>
			<div class="col-xs-4">
				<b>
					<span style="color:brown;">
						{{ $request->empname }}
					</span>
				</b>
			</div>
			<div class="col-xs-5">
				<label class="col-xs-3 clr_blue" style="text-align:right;padding:0px;">{{ trans('messages.lbl_alreadypaid') }}<span class="fr ml2 red"> &nbsp;&nbsp; </span></label>
				<div class="col-xs-2" style="text-align:right;padding:0px;">{{ number_format($salary[0]->Salary) }}</div>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_saldate') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-4">
				@if(isset($detedit))
					@if ($request->editflg=="3")
						{{ Form::text('txt_startdate','',array(
											'id'=>'txt_startdate',
											'name' => 'txt_startdate',
											'class'=>'box28per txt_startdate form-control ime_mode_disable',
											'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
											'data-label' => trans('messages.lbl_saldate'),
											'maxlength' => '10')) }}
					@else
						{{ Form::text('txt_startdate',$detedit[0]->txt_startdate,array(
											'id'=>'txt_startdate',
											'name' => 'txt_startdate',
											'class'=>'box28per txt_startdate form-control ime_mode_disable',
											'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
											'data-label' => trans('messages.lbl_saldate'),
											'maxlength' => '10')) }}
					@endif
				@else
					{{ Form::text('txt_startdate','',array(
										'id'=>'txt_startdate',
										'name' => 'txt_startdate',
										'class'=>'box28per txt_startdate form-control',
										'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
										'data-label' => trans('messages.lbl_saldate'),
										'maxlength' => '10')) }}
				@endif
				<label class="fa fa-calendar fa-lg" for="txt_startdate" aria-hidden="true"></label>
				<a href="javascript:getdate();" class="anchorstyle">
										<img title="Current Date" class="box15" src="{{ URL::asset('resources/assets/images/add_date.png') }}">
									</a>
			</div>
			<div class="col-xs-5">
				<label class="col-xs-3 clr_blue" style="text-align:right;padding:0px;">{{ trans('messages.lbl_balance') }}<span class="fr ml2 red"> &nbsp;&nbsp; </span></label>
				<div class="col-xs-2" style="color:red;text-align:right;padding:0px;">{{ number_format($request->total - $salary[0]->Salary) }}</div>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_salmonth') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				@if(isset($detedit))
					@if ($request->editflg=="3")
						{{ Form::select('salarymonth',array('' => '')+ range(1,12),'',array(
										'id'=>'salarymonth',
										'name' => 'salarymonth',
										'class'=>'widthauto ime_mode_active',
										'maxlength' => 10,
										'data-label' => trans('messages.lbl_salmonth'))) }}
					@else
						{{ Form::select('salarymonth',array('' => '')+ range(1,12),($detedit[0]->salaryMonth-1),array(
										'id'=>'salarymonth',
										'name' => 'salarymonth',
										'class'=>'widthauto ime_mode_active',
										'maxlength' => 10,
										'data-label' => trans('messages.lbl_salmonth'))) }}
					@endif
				@else
					{{ Form::select('salarymonth',array('' => '')+ range(1,12),'',array(
										'id'=>'salarymonth',
										'name' => 'salarymonth',
										'class'=>'widthauto ime_mode_active',
										'maxlength' => 10,
										'data-label' => trans('messages.lbl_salmonth'))) }}
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_bank') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9 CMN_display_block">
				@if(isset($detedit))
					{{ Form::select('bank',[null=>''] + $bankname+['999'=>'Cash'],$detedit[0]->bankId,array(
										'id'=>'bank',
										'name' => 'bank',
										'class'=>'widthauto ime_mode_active',
										'maxlength' => 10,
										'onchange' => 'javascript:getselectedText();fndisablecharge(this.value);',
										'data-label' => trans('messages.lbl_bank'))) }}
				@else
					{{ Form::select('bank',[null=>''] + $bankname+['999'=>'Cash'],(isset($register[0]->bankId)) ? $register[0]->bankId : '',array(
										'id'=>'bank',
										'name' => 'bank',
										'class'=>'widthauto ime_mode_active',
										'maxlength' => 10,
										'onchange' => 'javascript:getselectedText();fndisablecharge(this.value);',
										'data-label' => trans('messages.lbl_bank'))) }}
				@endif
			</div>
		</div>
		<div id="salaryhdn" class="col-xs-12 mt5" <?php if(isset($detedit[0]->bankId) && $detedit[0]->bankId == "999" || $request->bank == "999"){ ?> style="margin-bottom: 10px !important;" <?php } ?>>
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_salary') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				@if(isset($detedit))
					{{ Form::text('txt_salary',$detedit[0]->salary,array(
										'id'=>'txt_salary',
										'name' => 'txt_salary',
										'maxlength' => '15',
										'style'=>'text-align:right;padding-right:4px;',
										'onkeypress' => 'return isNumberKey(event)',
										'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
										'class'=>'box25per ime_mode_disable',
										'data-label' => trans('messages.lbl_salary'))) }}
				@else
					{{ Form::text('txt_salary','',array(
										'id'=>'txt_salary',
										'name' => 'txt_salary',
										'maxlength' => '15',
										'style'=>'text-align:right;padding-right:4px;',
										'onkeypress' => 'return isNumberKey(event)',
										'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
										'class'=>'box25per ime_mode_disable',
										'data-label' => trans('messages.lbl_salary'))) }}
				@endif
			</div>
		</div>
		@php 
			$chargedef = false;
			if(isset($detedit[0]->bankId) && $detedit[0]->bankId == "999") {
				$chargedef = true;
			} else if(isset($register[0]->bankId) && $register[0]->bankId == "999") {
				$chargedef = true;
			} 
		@endphp
		<div class="CMN_display_block pb10"></div>
	</fieldset>
	<fieldset style="background-color: #DDF1FA;">
		<div class="form-group">
			<div align="center" class="mt5">
			@if($request->editflg=="2")
				<button type="submit" class="btn edit btn-warning box100 addeditprocessnew">
					<i class="fa fa-edit" aria-hidden="true"></i> {{ trans('messages.lbl_update') }}
				</button>
				<a onclick="javascript:gotoidx('1','{{ $request->mainmenu }}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				</a>
			@else
				<button type="submit" class="btn btn-success add box100 ml5 addeditprocessnew">
					<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
				</button>
				<a onclick="javascript:gotoidx('2','{{ $request->mainmenu }}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				</a>
			@endif
			</div>
		</div>
	</fieldset>
	</div>
	</article>
	{{ Form::close() }}
	</div>
	{{ Form::open(array('name'=>'salaryplusaddeditcancel', 'id'=>'salaryplusaddeditcancel', 'url' => 'Salaryplus/addeditprocessnew?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	    {{ Form::hidden('empname', $request->empname , array('id' => 'empname')) }}
		{{ Form::hidden('id', $request->id , array('id' => 'id')) }}
		{{ Form::hidden('ids', $request->ids , array('id' => 'ids')) }}
	    {{ Form::hidden('empname', $request->empname , array('id' => 'empname')) }}
	    {{ Form::hidden('salary', $request->salary , array('id' => 'salary')) }}
		{{ Form::hidden('editflg', $request->editflg , array('id' => 'editflg')) }}
@endsection
