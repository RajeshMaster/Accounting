@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/loan.js') }}
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
	<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_4">
	@if(isset($detedit))
		{{ Form::model($detedit, array('name'=>'loanaddedit', 'id'=>'loanaddedit', 'files'=>true,'type'=>'file', 'method' => 'POST','class'=>'form-horizontal','url' => 'Loandetails/addeditprocess?time='.date('YmdHis') ) ) }}
	@else
		{{ Form::open(array('name'=>'loanaddedit', 'id'=>'loanaddedit','type'=>'file', 'url' => 'Loandetails/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
	@endif
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('id', $request->id , array('id' => 'id')) }}
		{{ Form::hidden('editflg', $request->editflg , array('id' => 'editflg')) }}
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/loan.jpg') }}">
			<h2 class="pull-left pl5 mt10">
					{{ trans('messages.lbl_loanamt') }}<span class="">・</span>@if ($request->editflg=="2")<span style="color:red">{{ trans('messages.lbl_edit') }}</span>@else<span style="color:green">{{ trans('messages.lbl_register') }}</span>@endif
			</h2>
		</div>
	</div>
	<div class="col-xs-12 pl5 pr5">
	<fieldset>
		<div class="col-xs-12 mt15">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_bank_name') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::select('bankname',[null=>'']+ $bankname,old('bankname'),array(
															'id'=>'bankname',
															'name' => 'bankname',
															'class'=>'widthauto ime_mode_active',
															'maxlength' => 10,
															'data-label' => trans('messages.lbl_bank_name'))) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_loantype') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::select('loantype',[null=>'']+ $loantype,old('loantype'),array(
															'id'=>'loantype',
															'name' => 'loantype',
															'class'=>'widthauto ime_mode_active',
															'maxlength' => 10,
															'data-label' => trans('messages.lbl_loantype'))) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_loanname') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::text('loanname',old('loanname'),array(
										'id'=>'loanname',
										'name' => 'loanname',
										'maxlength' => '40',
										'style'=>'text-align:left;padding-left:4px;',
										'class'=>'box25per',
										'data-label' => trans('messages.lbl_loanname'))) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_amount') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::text('amount',old('amount'),array(
										'id'=>'amount',
										'name' => 'amount',
										'maxlength' => '14',
										'style'=>'text-align:right;padding-right:4px;ime-mode: enabled;',
										'class'=>'box15per ime_mode_disable',
										'onkeypress' => 'return isNumberKey(event);',
										'onclick' => 'return isNumberKey(event);javascript:onlyNum();',
										'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
										'data-label' => trans('messages.lbl_amount'))) }}
				<input type="checkbox" id="reflectpass" name="reflectpass"
									<?php if ( isset($fetchdetail[0]->reflectPassbookflg) && $fetchdetail[0]->reflectPassbookflg == 0) {?> 
									checked="checked"
									<?php	} ?> 
									>
				<label for = "reflectpass" class="clr_blue"><span>&nbsp;{{ trans('messages.lbl_notreflect') }}</label>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_rded') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9 CMN_display_block">
				{{ Form::text('txt_startdate',old('txt_startdate'),array(
										'id'=>'txt_startdate',
										'name' => 'txt_startdate',
										'class'=>'box12per txt_startdate form-control ime_mode_disable',
										'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
										'data-label' => trans('messages.lbl_rded'),
										'maxlength' => '10',
										'onchange' => 'javascript:cal_endyearmn();')) }}
				<label class="fa fa-calendar fa-lg" for="txt_startdate" aria-hidden="true"></label>
				<div id="end_date" style="display: none;">
				<span style="font: bold;">&nbsp; ~</span>
					{{ Form::text('txt_end_date',null,array('id'=>'txt_end_date', 
										'name' => 'txt_end_date',
										'style'=>'ime-mode:disabled;background-color:#eee9e9;',
										'class'=>'box48per form-control',
										'disabled'=>'disabled','onfocus'=>'this.blur();')) }}
					@if(isset($detedit))
						{{ Form::hidden('end_dates', $fetchdetail[0]->endDate , array('id' => 'end_dates')) }}
					@else
						{{ Form::hidden('end_dates', $request->txt_end_date , array('id' => 'end_dates')) }}
					@endif
				</div>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_period') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::text('loanperiod',old('loanperiod'),array(
										'id'=>'loanperiod',
										'name' => 'loanperiod',
										'maxlength' => '2',
										'style'=>'text-align:center;',
										'onkeypress' => 'return isNumberKey(event);javascript:cal_endyearmn();',
										'onclick' => 'javascript:cal_endyearmn();',
										'class'=>'box5per ime_mode_disable',
										'data-label' => trans('messages.lbl_period'),
										'onkeyup' => 'javascript:cal_endyearmn();cal_month(this.id);')) }}
				<span>&nbsp;{{ trans('messages.lbl_inyrs') }}</span>
				<span> 
					{{ Form::text('txt_month',null,array('id'=>'txt_month', 
										'name' => 'txt_month',
										'style'=>'ime-mode:disabled;background-color:#eee9e9;text-align:center;',
										'class'=>'box6per',
										'disabled'=>'disabled',
										'onfocus'=>'this.blur();')) }}
				</span>
				<span>&nbsp;{{ trans('messages.lbl_inmonth') }}</span>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_interest') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::text('interest',old('interest'),array(
										'id'=>'interest',
										'name' => 'interest',
 										'style'=>'text-align:right;padding-right:4px;',
										'onkeypress'=>'return interestcheck(event)',
										'class'=>'box5per ime_mode_disable',
										'data-label' => trans('messages.lbl_interest'))) }}
				<span>&nbsp;%</span>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_rpday') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::text('paymentday',old('paymentday'),array(
										'id'=>'paymentday',
										'name' => 'paymentday',
										'style'=>'text-align:center;',
										'maxlength' => '2',
										'onkeypress' => 'return isNumberKey(event)',
										'class'=>'box5per ime_mode_disable',
										'data-label' => trans('messages.lbl_rpday'))) }}
				<span>&nbsp;{{ trans('messages.lbl_dayofem') }}</span>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_currbal') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::text('currentbalance',old('currentbalance'),array(
										'id'=>'currentbalance',
										'maxlength' => '14',
										'name' => 'currentbalance',
										'style'=>'text-align:right;padding-right:4px;',
										'class'=>'box15per ime_mode_disable',
										'onkeypress'=>'return numberonly(event)',
										'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
										'data-label' => trans('messages.lbl_currbal'))) }}
			@if(isset($detedit))
				<input type="checkbox" id="check" name="check"
									<?php if ( $fetchdetail[0]->checkFlg == 1) {?> 
									checked="checked"
									<?php	} ?> 
									onclick="return fnCopyAmount(this.id);">
				<label for = "check" class="clr_blue">&nbsp;{{ trans('messages.lbl_asabove') }}</label>
			@else
				<input type="checkbox" id="check" name="check"
									onclick="return fnCopyAmount(this.id);">
				<label for = "check" class="clr_blue">&nbsp;{{ trans('messages.lbl_asabove') }}</label>
			@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_remainmonths') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::text('remainingmonths',old('remainingmonths'),array(
										'id'=>'remainingmonths',
										'name' => 'remainingmonths',
										'style'=>'text-align:center;',
										'maxlength' => '4',
										'onkeydown'=>'return removecheckvalue(3)',
										'onkeypress' => 'return isNumberKey(event)',
										'class'=>'box6per ime_mode_disable',
										'data-label' => trans('messages.lbl_remainmonths'))) }}
				<span>&nbsp;{{ trans('messages.lbl_month') }}</span>
			<div>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_payup') }}<span class="fr ml2 red">  &nbsp;&nbsp;&nbsp;</span></label>
			</div>
			<div class="col-xs-9 pl7">
				{{ Form::file('file1',array(
										'class' => 'pull-left box350',
										'id' => 'file1',
										'name' => 'file1',
										'style' => 'height:23px;',
										'data-label' => trans('messages.lbl_payup'))) }}
				<span>&nbsp;(Ex: PDF Only)</span>
				@if(isset($detedit))
				<?php
					$file_url = 'resources/assets/uploadandtemplates/upload/Loandetails/' . $fetchdetail[0]->pdfFile;
				 ?>
					@if(isset($detedit) && file_exists($file_url))
						<a href="javascript:download('{{ $fetchdetail[0]->pdfFile }}','../../../resources/assets/uploadandtemplates/upload/Loandetails');">
							<span>{{ $fetchdetail[0]->pdfFile }}</span>
						</a>
						{{ Form::hidden('pdffiles', $fetchdetail[0]->pdfFile , array('id' => 'pdffiles')) }}
					@else
					@endif
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5 mb10">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_remarks') }}<span class="fr ml2 red">  &nbsp;&nbsp;&nbsp;</span></label>
			</div>
			<div class="col-xs-9 pl7">
				{{ Form::textarea('Remarks',old('Remarks'),array('id'=>'Remarks', 
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
	{{ Form::close() }}
	<script type="text/javascript">
		if($('#editflg').val() == "2") {
			document.getElementById('end_date').style.display="inline-block";
		    if( document.getElementById('loanperiod').value != "" ) {
		        loanperiod = document.getElementById('loanperiod').value * 12;
		    }
		    document.getElementById('txt_month').value = loanperiod;
		}
	</script>
	{{ Form::open(array('name'=>'loanaddeditcancel', 'id'=>'loanaddeditcancel', 'url' => 'Loandetails/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('id', $request->id , array('id' => 'id')) }}
		{{ Form::hidden('editflg', $request->editflg , array('id' => 'editflg')) }}
		{{ Form::close() }}
	</div>
@endsection