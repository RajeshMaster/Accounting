@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/transfer.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
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
</style>
	<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_2">
		<?php if (!empty($query)) { ?>
			{{ Form::model($query, array('name'=>'loanaddedit', 'id'=>'loanaddedit', 'files'=>true,'type'=>'file', 'method' => 'POST','class'=>'form-horizontal','url' => 'Transfer/loanaddeditprocess?time='.date('YmdHis') ) ) }}
				{{ Form::hidden('billno', $query[0]->billno , array('id' => 'billno')) }}
				{{ Form::hidden('ids', $query[0]->id , array('id' => 'ids')) }}
				{{ Form::hidden('id', $request->id , array('id' => 'id')) }}
		<?php } else { ?>
			{{ Form::open(array('name'=>'loanaddedit', 'id'=>'loanaddedit','type'=>'file', 'url' => 'Transfer/loanaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		<?php } ?>
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('editflg', $request->editflg , array('id' => 'editflg')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('dateflg', $request->dateflg, array('id' => 'dateflg')) }}
		{{ Form::hidden('id', $request->id, array('id' => 'id')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
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
					{{ trans('messages.lbl_loanpay') }}<span class="">・</span>@if ($request->editflg=="2")<span style="color:red">{{ trans('messages.lbl_edit') }}</span>@elseif ($request->editflg=="3")<span style="color:Blue">{{ trans('messages.lbl_copy') }}</span>@else<span style="color:green">{{ trans('messages.lbl_register') }}</span>@endif
			</h2>
		</div>
	</div>
	@if($request->editflg == "1")
		<div class="col-xs-12 pt10">
				<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
				<a href="javascript:gototransadd('{{ $request->mainmenu }}');" class="btn btn-success box25per"><span class="fa fa-plus"></span> {{ trans('messages.lbl_transfer') }}</a>
			</div>
		</div>
	@endif
	<div class="col-xs-12 pl5 pr5">
	<fieldset>
		<div class="col-xs-12 mt15">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_bank_name') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::select('bankname',[null=>''] + $bankname,(isset($query[0]->bankname)) ? $query[0]->bankname : '',array(
															'id'=>'bankname',
															'name' => 'bankname',
															'class'=>'widthauto ime_mode_active',
															'maxlength' => 10,
															'onchange'=>'javascript:fngetloanname(this.value);',
															'data-label' => trans('messages.lbl_bank_name'))) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_loanname') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::select('loanname',[null=>''],(isset($query[0]->bankId)) ? $query[0]->bankId : '',array(
															'id'=>'loanname',
															'name' => 'loanname',
															'class'=>'widthauto ime_mode_active loanname',
															'maxlength' => 10,
															'style'=>'min-width:100px;',
															'onchange'=>'javascript:fnSetLoanNo(this.id);',
															'data-label' => trans('messages.lbl_bank_name'))) }}
				<span class="pl5 fwb" style="color: brown;" id="empidd"></span>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_loantype') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::select('loantype',[null=>''] + $loantype,(isset($query[0]->loanType)) ? $query[0]->loanType : '',array(
															'id'=>'loantype',
															'name' => 'loantype',
															'class'=>'widthauto ime_mode_active',
															'maxlength' => 10,
															'data-label' => trans('messages.lbl_bank_name'))) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_Date') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				@if ($request->loandetail == "5")
					<?php
						if (!empty($query)) {
							if ($query[0]->bankdate != "") {
								$dateYM = date('Y-m', strtotime($query[0]->bankdate));
								$dateYMD = $dateYM."-". str_pad($query[0]->repaymentDate, 2, 0, STR_PAD_LEFT) ;
								$query[0]->bankdate = $dateYMD;
								$date = new DateTime($query[0]->bankdate);
								$date->modify("+1 month");
								$date->format('Y-m-d');
								$dayinWeek = date('l', strtotime( $date->format('Y-m-d')));
								if ($dayinWeek == 'Saturday' || $dayinWeek == 'Sunday') {
									$query[0]->bankdate = date('Y-m-d', strtotime('next Monday '.$date->format('Y-m-d').''));
								} else {
									$dateYM = date('Y-m', strtotime($date->format('Y-m-d')));
									$dateYMD = $dateYM."-". str_pad($query[0]->repaymentDate, 2, 0, STR_PAD_LEFT) ;
									$query[0]->bankdate = $dateYMD;
								}
							}
						}
					 ?>
				@endif
				@if($request->editflg == "3")
					{{ Form::text('txt_startdate',(isset($request->dateflg)) ? $request->dateflg : '',array(
															'id'=>'txt_startdate',
															'name' => 'txt_startdate',
															'class'=>'box12per txt_startdate form-control',
															'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
															'data-label' => trans('messages.lbl_Date'),
															'maxlength' => '10')) }}
				@else
					{{ Form::text('txt_startdate',(isset($query[0]->bankdate)) ? $query[0]->bankdate : '',array(
															'id'=>'txt_startdate',
															'name' => 'txt_startdate',
															'class'=>'box12per txt_startdate form-control',
															'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
															'data-label' => trans('messages.lbl_Date'),
															'maxlength' => '10')) }}
				@endif
					<label class="fa fa-calendar fa-lg" for="txt_startdate" aria-hidden="true"></label>
					<a href="javascript:getdate();" class="anchorstyle">
						<img title="Current Date" class="box15" src="{{ URL::asset('resources/assets/images/add_date.png') }}"></a>
				@if (Auth::user()->userclassification == 4)
					@if($request->editflg == "3")
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
				{{ Form::text('amount',(isset($query[0]->amount)) ? $query[0]->amount : '',array(
															'id'=>'amount',
															'name' => 'amount',
															'maxlength' => '14',
															'style'=>'text-align:right;padding-right:4px;',
															'class'=>'box15per ime_mode_disable',
															'onkeypress' => 'return isNumberKey(event)',
															'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
															'data-label' => trans('messages.lbl_amount'))) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_interest') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::text('interest',(isset($query[0]->fee)) ? $query[0]->fee : '',array(
										'id'=>'interest',
										'name' => 'interest',
 										'style'=>'text-align:right;padding-right:4px;',
										'onkeypress'=>'return interestcheck(event)',
										'class'=>'box5per ime_mode_disable',
										'data-label' => trans('messages.lbl_interest'))) }}
				<span>&nbsp;%</span>
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
		@if($request->editflg == "2")
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_created_date') }}<span class="fr ml2 red"> &nbsp; </span></label>
				</div>
				<div class="col-xs-9">
					<span  style="color: black;">
						<b>
							{{ $query[0]->Ins_DT }}
						</b>
					</span>
				</div>
			</div>
		@endif
		<div class="CMN_display_block pb10"></div>
	</fieldset>
	<fieldset style="background-color: #DDF1FA;">
		<div class="form-group">
			<div align="center" class="mt5">
			@if($request->editflg=="2")
				<button type="submit" class="btn edit btn-warning box100 loanaddeditprocess">
					<i class="fa fa-edit" aria-hidden="true"></i>{{ trans('messages.lbl_update') }}
				</button>
				<a onclick="javascript:gotoindexpage_1('1','{{ $request->mainmenu }}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				</a>
			@else
				<button type="submit" class="btn btn-success add box100 ml5 loanaddeditprocess">
					<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
				</button>
				<a onclick="javascript:gotoindexpage_1('2','{{ $request->mainmenu }}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				</a>
			@endif
			</div>
		</div>
	</fieldset>
	</div>
	</article>
	<?php if (!empty($query)) { ?>
		<script type="text/javascript">
			fngetloanname('{{ $query[0]->bankname }}','{{ $query[0]->billno }}')
		</script>
	<?php } ?>
	</div>
	{{ Form::close() }}
	{{ Form::open(array('name'=>'loanaddeditcancel', 'id'=>'loanaddeditcancel', 'url' => 'Transfer/loanaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
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
