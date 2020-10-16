@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/transfer.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::script('resources/assets/js/lib/lightbox.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::style('resources/assets/css/lib/lightbox.css') }}
{{ HTML::script('resources/assets/js/lib/additional-methods.min.js') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var dates = '<?php echo date('Y-m-d'); ?>';
	var accessDate = '<?php echo Auth::user()->accessDate; ?>';
	var userclassification = '<?php echo Auth::user()->userclassification; ?>';
	$(document).ready(function() {
		if (userclassification == 1) {
			accessDate = setNextDay(accessDate);
			setDatePickerAfterAccessDate("txt_startdate", accessDate);
		} else {
			setDatePicker("txt_startdate");
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
<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_2">
	{{ Form::open(array('name'=>'editothers', 'id'=>'editothers', 'url' => 'Transfer/editothersprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('dateflg', $request->dateflg, array('id' => 'dateflg')) }}
	    {{ Form::hidden('id',$request->id, array('id' => 'id')) }}
	    {{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('editid', $request->editid , array('id' => 'editid')) }}
		{{ Form::hidden('editflg', $request->editflg , array('id' => 'editflg')) }}
		{{ Form::hidden('flg', $request->flg , array('id' => 'flg')) }}

	    @if (Auth::user()->userclassification == 1) 
	    	{{ Form::hidden('accessdate',Auth::user()->accessDate, array('id' => 'accessdate')) }}
	    @else
	    	{{ Form::hidden('accessdate','0001-01-01', array('id' => 'accessdate')) }}
	    @endif
<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/others_icon.jpg') }}">
			<h2 class="pull-left pl5 mt10 CMN_mw150">{{ trans('messages.lbl_Others') }}<span class="">・</span>
				@if($request->editflg =="edit")<span style="color:red">{{ trans('messages.lbl_edit') }}</span>@else<span style="color:red">{{ trans('messages.lbl_copy') }}</span>@endif
			</h2>
		</div>
</div>
	
<div class="col-xs-12 pl5 pr5" ondragstart="return false;" ondrop="return false;">
	<fieldset>
		<div class="col-xs-12 mt15">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_Date') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				@if($request->editflg == "edit")
					{{ Form::text('txt_startdate',(isset($query[0]->bankdate)) ? $query[0]->bankdate : '',array(
															'id'=>'txt_startdate',
															'name' => 'txt_startdate',
															'autocomplete' => 'off',
															'class'=>'box12per txt_startdate form-control',
															'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
															'data-label' => trans('messages.lbl_Date'),
															'maxlength' => '10')) }}
					<label class="fa fa-calendar fa-lg" for="txt_startdate" aria-hidden="true"></label>
				@else
					{{ Form::text('txt_startdate',(isset($request->dateflg)) ? $request->dateflg : '',array(
															'id'=>'txt_startdate',
															'name' => 'txt_startdate',
															'autocomplete' => 'off',
															'class'=>'box12per txt_startdate form-control',
															'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
															'data-label' => trans('messages.lbl_Date'),
															'maxlength' => '10')) }}
					<label class="fa fa-calendar fa-lg" for="txt_startdate" aria-hidden="true"></label>
				@endif
				<a href="javascript:getdate();" class="anchorstyle">
						<img title="Current Date" class="box15" src="{{ URL::asset('resources/assets/images/add_date.png') }}"></a>
				@if (Auth::user()->userclassification == 4)
					@if($request->editflg == "edit")
						&nbsp;&nbsp;{{ Form::checkbox('accessrights', 1, 1, ['id' => 'accessrights']) }}
						&nbsp;<label for="accessrights"><span class="grey fb">{{ trans('messages.lbl_accessrights') }}</span></label>
					@else
						&nbsp;&nbsp;{{ Form::checkbox('accessrights', 1, (isset($query[0]->accessFlg)) ? $query[0]->accessFlg : 1, ['id' => 'accessrights']) }}
						&nbsp;<label for="accessrights"><span class="grey fb">{{ trans('messages.lbl_accessrights') }}</span></label>
					@endif
				@endif
			</div>
		</div>
		
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_amount') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9 CMN_display_block">
				@php 
				$amount = 0;
				if(isset($query[0]->amount)) {
					$amount = number_format($query[0]->amount);
				} 
				@endphp
					{{ Form::text('amount_1',(isset($amount)) ? $amount : 0,array(
															'id'=>'amount_1',
															'name' => 'amount_1',
															'maxlength' => '14',
															'style'=>'text-align:right;padding-right:4px;',
															'class'=>'box15per ime_mode_disable',
															'onblur' => 'return fnSetZero11(this.id);',
															'onfocus' => 'return fnRemoveZero(this.id);',
															'onclick' => 'return fnRemoveZero(this.id);',
															'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
															'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
															'data-label' => trans('messages.lbl_amount'))) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_bank_name') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::select('bankname',[null=>'']+ $bankname,(isset($bankedit)) ? $bankedit : '',array(
															'id'=>'bankname',
															'name' => 'bankname',
															'class'=>'widthauto ime_mode_active',
															'maxlength' => 10,
															'data-label' => trans('messages.lbl_bank_name'))) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_remarks') }}<span class="fr ml2 red" style="visibility: hidden;"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::textarea('Remarks',(isset($query[0]->remark_dtl)) ? $query[0]->remark_dtl : '',array('id'=>'Remarks', 
											'name' => 'Remarks',
											'class' => 'box45per',
											'style'=>'text-align:left;padding-left:4px;',
											'size' => '60x5',
											'data-label' => trans('messages.lbl_remarks'))) }}
			</div>
		</div>
	</fieldset>
	<fieldset style="background-color: #DDF1FA;">
		<div class="form-group">
			<div align="center" class="mt5">
				@if($request->editflg =="edit")
					<button type="submit" class="btn btn-warning  box100 editothersprocess">
					<i class="fa fa-edit" aria-hidden="true"></i> {{ trans('messages.lbl_update') }}
					</button>
					<a onclick="javascript:gotoindexpageothers('1','{{ $request->mainmenu }}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
					</a>
				@else
					<button type="submit" class="btn btn-success  box100 editothersprocess">
					<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
					</button>
					<a onclick="javascript:gotoindexpageothers('2','{{ $request->mainmenu }}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
					</a>
				@endif
			</div>
		</div>
	</fieldset>
</div>
{{ Form::close() }}
</article>
</div>
{{ Form::open(array('name'=>'editotherscancel', 'id'=>'editotherscancel', 'url' => 'Transfer/editothersprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('dateflg', $request->dateflg, array('id' => 'dateflg')) }}
	    {{ Form::hidden('id',$request->id, array('id' => 'id')) }}
	    {{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('editid', $request->editid , array('id' => 'editid')) }}
	    {{ Form::hidden('lang',Session::get('languageval') , array('id' => 'lang')) }}
	    
@endsection