@extends('layouts.app')
@section('content')
{{ HTML::style('resources/assets/css/common.css') }}
{{ HTML::style('resources/assets/css/widthbox.css') }}
{{ HTML::script('resources/assets/css/bootstrap.min.css') }}
{{ HTML::script('resources/assets/js/salaryplus.js') }}
{{ HTML::style('resources/assets/css/sidebar-bootstrap.min.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
</script>
<style type="text/css">
	.alertboxalign {
		margin-bottom: -50px !important;
	}
	.alert {
		display:inline-block !important;
		height:30px !important;
		padding:5px !important;
	}
</style>
	<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_10">
	{{ Form::open(array('name'=>'salaryplusview', 'id'=>'salaryplusview', 'url' => 'Salaryplus/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
		{{ Form::hidden('id', $request->id , array('id' => 'id')) }}
		{{ Form::hidden('ids', $request->ids , array('id' => 'ids')) }}
	    {{ Form::hidden('empname', $request->empname , array('id' => 'empname')) }}
	    {{ Form::hidden('salary', $request->salary , array('id' => 'salary')) }}
		{{ Form::hidden('gobackflg',$request->gobackflg , array('id' => 'gobackflg')) }}
		{{ Form::hidden('bankid',$request->bankid , array('id' => 'bankid')) }}
		{{ Form::hidden('editflg', '' , array('id' => 'editflg')) }}
		{{ Form::hidden('total', $request->total , array('id' => 'total')) }}

	<!-- Start Heading -->
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/salary_1.png') }}">
			<h2 class="pull-left pl5 mt10">
					{{ trans('messages.lbl_salaryplus') }}<span>・</span><span class="colbl">{{ trans('messages.lbl_view') }}</span>
			</h2>
		</div>
	</div>
	<div class="col-xs-12 pt10">
		<!-- Session msg -->
		@if(Session::has('success'))
			<div align="center" class="alertboxalign" role="alert">
				<p class="alert {{ Session::get('alert', Session::get('type') ) }}">
	            	{{ Session::get('success') }}
	          	</p>
			</div>
		@endif
		@php Session::forget('success'); @endphp
		<!-- Session msg -->
			<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
				<a href="javascript:gosingletoindex('{{ $request->mainmenu }}');" class="btn btn-info box80"><span class="fa fa-arrow-left"></span> {{ trans('messages.lbl_back') }}</a>
					<a href="javascript:gotoeditpage('{{ $request->ids }}','{{ $singleview[0]->salary }}','{{ $request->mainmenu }}',2);" class="btn btn-warning box100"><span class="fa fa-pencil"></span> {{ trans('messages.lbl_edit') }}</a>
			</div>
	</div>
	<div class="col-xs-12 pl15 pr15">
	<fieldset>
		<div class="col-xs-12 mt15">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_empid') }}</label>
			</div>
			<div>
				<b>
					<span style="color:blue;">
						{{ $request->id }}
					</span>
				</b>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_empName') }}</label>
			</div>
			<div>
				<b>
					<span style="color:brown;">
						{{ $request->empname }}
					</span>
				</b>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_saldate') }}</label>
			</div>
			<div>
				@if(isset($singleview[0]->salaryDate))
					{{ $singleview[0]->salaryDate }}
				@else
					NIL
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_salmonth') }}</label>
			</div>
			<div>
				@if(isset($singleview[0]->salaryMonth))
					{{ $singleview[0]->salaryMonth }}
				@else
					NIL
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_accnumber') }}</label>
			</div>
			<div>
				@if(isset($singleview[0]->AccNo))
					{{ $singleview[0]->AccNo }}
				@else
					NIL
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>
					@if(isset($singleview[0]->BankName) || $request->bankid == 999)
						@if($request->bankid == 999)
							{{ trans('messages.lbl_salary_type') }}
						@else
							{{ trans('messages.lbl_bank_name') }}
						@endif
					@else
						NIL
					@endif
				</label>
			</div>
			<div>
				@if(isset($singleview[0]->BankName) || $request->bankid == 999)
					@if($request->bankid == 999)
						CASH
					@else
						{{ $singleview[0]->BankName }}
					@endif
				@else
					NIL
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_salary') }}</label>
			</div>
			<div>
				@if(isset($singleview[0]->salary))
					{{ $singleview[0]->salary }}
				@else
					NIL
				@endif
			</div>
		</div>
	</fieldset>
	</div>
	</article>
	{{ Form::close() }}
	</div>
@endsection
