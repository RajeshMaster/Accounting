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
	.ime_mode_disable {
		ime-mode:disabled;
	}
	/*.viewPic3by2{
		display: block;
		max-width:180px;
		max-height:180px;
		width: auto;
		height: auto;
	}*/
</style>
	<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_2">
	@if(isset($query))
		{{ Form::model($query, array('name'=>'transferaddedit', 'id'=>'transferaddedit', 'files'=>true,'type'=>'file', 'method' => 'POST','class'=>'form-horizontal','url' => 'Transfer/addeditprocess?time='.date('YmdHis') ) ) }}
		{{ Form::hidden('billno', $query[0]->billno , array('id' => 'billno')) }}
		{{ Form::hidden('id', $request->id , array('id' => 'id')) }}
	@else
		{{ Form::open(array('name'=>'transferaddedit', 'id'=>'transferaddedit','type'=>'file', 'url' => 'Transfer/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
	@endif
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('editflg', $request->editflg , array('id' => 'editflg')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
	    {{ Form::hidden('lang',Session::get('languageval') , array('id' => 'lang')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('dateflg', $request->dateflg, array('id' => 'dateflg')) }}
		{{ Form::hidden('id', $request->id, array('id' => 'id')) }}
		{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
		@if (Auth::user()->userclassification == 1) 
	    	{{ Form::hidden('accessdate',Auth::user()->accessDate, array('id' => 'accessdate')) }}
	    @else
	    	{{ Form::hidden('accessdate','0001-01-01', array('id' => 'accessdate')) }}
	    @endif
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/expenses_icon.png') }}">
			<h2 class="pull-left pl5 mt10">
					{{ trans('messages.lbl_transfer') }}<span class="">・</span>@if ($request->editflg=="2")<span style="color:red">{{ trans('messages.lbl_edit') }}</span>@elseif ($request->editflg=="3")<span style="color:Blue">{{ trans('messages.lbl_copy') }}</span>@else<span style="color:green"> {{ trans('messages.lbl_register') }}</span>@endif
			</h2>
		</div>
	</div>
	@if($request->editflg == 1)
		<div class="col-xs-12 pt10">
				<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
				<a href="javascript:gotoloanadd('{{ $request->mainmenu }}');" class="btn btn-success box25per"><span class="fa fa-plus"></span> {{ trans('messages.lbl_loanpay') }}</a>
			</div>
		</div>
	@endif
	<div class="col-xs-12 pl5 pr5">
	<fieldset>
		@if($request->editflg == 2)
		<div class="col-xs-12 mt15">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_billno') }}<span class="fr ml2 red"> &nbsp; </span></label>
			</div>
			<div class="col-xs-9">
				<span  style="color: brown;">
					<b>
						{{ $query[0]->billno }}
					</b>
				</span>
			</div>
		</div>
		<div class="col-xs-12 mt5">
		@else
		<div class="col-xs-12 mt15">
		@endif
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_Date') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				@if($request->editflg == 3)
					{{ Form::text('txt_startdate',(isset($request->dateflg)) ? $request->dateflg : '',array(
															'id'=>'txt_startdate',
															'name' => 'txt_startdate',
															'class'=>'box12per txt_startdate form-control',
															'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
															'data-label' => trans('messages.lbl_Date'),
															'maxlength' => '10')) }}
					<label class="fa fa-calendar fa-lg" for="txt_startdate" aria-hidden="true"></label>
				@else
					{{ Form::text('txt_startdate',(isset($query[0]->bankdate)) ? $query[0]->bankdate : '',array(
															'id'=>'txt_startdate',
															'name' => 'txt_startdate',
															'class'=>'box12per txt_startdate form-control',
															'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
															'data-label' => trans('messages.lbl_Date'),
															'maxlength' => '10')) }}
					<label class="fa fa-calendar fa-lg" for="txt_startdate" aria-hidden="true"></label>
				@endif
				<a href="javascript:getdate();" class="anchorstyle">
						<img title="Current Date" class="box15" src="{{ URL::asset('resources/assets/images/add_date.png') }}"></a>
				@if (Auth::user()->userclassification == 4)
					@if($request->editflg == 3)
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
				<label>{{ trans('messages.lbl_mainsubject') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::select('mainsubject',[null=>''] + $mainsub,(isset($query[0]->subject)) ? $query[0]->subject : '',array(
															'id'=>'mainsubject',
															'name' => 'mainsubject',
															'class'=>'widthauto ime_mode_active',
															'maxlength' => 10,
															'onchange'=>'javascript:fngetsubsubject(this.value);',
															'data-label' => trans('messages.lbl_mainsubject'))) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_subsubject') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::select('subsubject',[null=>''],'', array(
															'name' =>'subsubject',
															'id'=>'subsubject',
															'style'=>'min-width:100px;',
															'data-label' => trans('messages.lbl_subsubject'),
															'class'=>'pl5 box35per widthauto'))}}
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
				<label>{{ trans('messages.lbl_charge') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				@php 
				$fee = 0;
				if(isset($query[0]->fee)) {
					$fee = number_format($query[0]->fee);
				} 
				@endphp
				{{ Form::text('charge_1',(isset($fee)) ? $fee : 0,array(
															'id'=>'charge_1',
															'name' => 'charge_1',
															'maxlength' => '14',
															'style'=>'text-align:right;padding-right:4px;',
															'class'=>'box15per ime_mode_disable',
															'onblur' => 'return fnSetZero11(this.id);',
															'onfocus' => 'return fnRemoveZero(this.id);',
															'onclick' => 'return fnRemoveZero(this.id);',
															'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
															'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
															'data-label' => trans('messages.lbl_charge'))) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_bill') }}<span class="fr ml2 red"> &nbsp;&nbsp; </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::file('file1',array(
										'class' => 'pull-left box350',
										'id' => 'file1',
										'name' => 'file1',
										'style' => 'height:23px;',
										'data-label' => trans('messages.lbl_payup'))) }}
				<span>&nbsp;(Ex: Image File Only)</span>
				@if(isset($query)&& $request->editflg!="1")
					<?php
						$file_url = '../InvoiceUpload/Expenses/' . $query[0]->file_dtl;
					 ?>
					@if(isset($query[0]->file_dtl) && file_exists($file_url))
						<a style="text-decoration:none" href="{{ URL::asset('resources/assets/images/Expenses/'.$query[0]->file_dtl) }}" data-lightbox="visa-img">
						<img width="20" height="20" name="empimg" id="empimg" 
						class="ml5 box20 viewPic3by2" src="{{ URL::asset('../../../../InvoiceUpload/Expenses').'/'.$query[0]->file_dtl }}"></a>
						{{ Form::hidden('pdffiles', $query[0]->file_dtl , array('id' => 'pdffiles')) }}
					@else
					@endif
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5 mb10">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_remarks') }}<span class="fr ml2 red"> &nbsp;&nbsp; </span></label>
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
		<div class="CMN_display_block pb10"></div>
	</fieldset>
	<fieldset style="background-color: #DDF1FA;">
		<div class="form-group">
			<div align="center" class="mt5">
			@if($request->editflg=="2")
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
	@if(isset($query))
		<script type="text/javascript">
			fngetsubsubject('{{ $query[0]->subject }}','{{ $query[0]->details }}')
		</script>
	@endif
	{{ Form::close() }}
	</div>
	{{ Form::open(array('name'=>'transferaddeditcancel', 'id'=>'transferaddeditcancel', 'url' => 'Transfer/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('editflg', $request->editflg , array('id' => 'editflg')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
@endsection
