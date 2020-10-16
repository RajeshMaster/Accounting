@extends('layouts.app')
@section('content')
<style type="text/css">
	.alertboxalign {
		margin-bottom: -50px !important;
	}
	.alert {
		display:inline-block !important;
		height:30px !important;
		padding:5px !important;
	}
	.fb{
		color: gray !important;
	}
</style>
{{ HTML::script('resources/assets/js/bank.js') }}
		{{ Form::open(array('name'=>'frmbankview',
						'url' => 'Bank/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'id'=>'frmbankview',
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('filterval', $request->filterval, array('id' => 'filterval')) }}	
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('loc', $request->loc , array('id' => 'loc')) }}
		{{ Form::hidden('flg', $request->flg , array('id' => 'flg')) }}
		{{ Form::hidden('id', $request->id , array('id' => 'id')) }}
		{{ Form::hidden('name', $request->name , array('id' => 'name')) }}
		{{ Form::hidden('editid', $view[0]->id, array('id' => 'editid')) }}
		{{ Form::hidden('viewid', $view[0]->id , array('id' => 'viewid')) }}
		{{ Form::hidden('bankuid', $view[0]->id, array('id' => 'bankuid')) }}
		{{ Form::hidden('branchid', $view[0]->id, array('id' => 'branchid')) }}
	<!-- Start Heading -->
<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="master" class="DEC_flex_wrapper " data-category="master master_sub_2">
		<div class="row hline">
			<div class="col-xs-8 pm0">
				<img class="pull-left box35 mt15 ml10" src="{{ URL::asset('resources/assets/images/bank.png') }}">
				<h2 class="pull-left pl5 mt15 CMN_mw150">{{ trans('messages.lbl_bank_acc_dtl') }}</h2>
			</div>
		</div>
		<!-- End Heading -->
		<div class="ml10 mt10">
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
			<a href="javascript:goindexpage('Bank_invoice',{{ date('YmdHis') }});" class="pageload btn btn-info box80"><span class="fa fa-arrow-left"></span> {{ trans('messages.lbl_back') }}</a>
			<a href="javascript:edit('{{ $request->mainmenu }}','{{ $request->id }}',{{ date('YmdHis') }});" class="btn btn-warning box100"><span class="fa fa-pencil"></span> {{ trans('messages.lbl_edit') }}</a>
		</div>	
		<div class="col-xs-12 pl10 pr10">
			<fieldset style="width: 1180px;">
				<div class="col-xs-12 mt10">
					<div class="col-xs-3 text-right clr_blue">
						<label>{{ trans('messages.lbl_account_no') }}</label>
					</div>
					<div>
						@if($view[0]->AccNo !="")
							{{ $view[0]->AccNo }}
						@else
							{{ "Nill" }}
						@endif
					</div>
				</div>
				<div class="col-xs-12">
					<div class="col-xs-3 text-right clr_blue">
						<label>{{ trans('messages.lbl_name') }}</label>
					</div>
					<div>
						@if($view[0]->FirstName !="")
							{{ $view[0]->FirstName }}
						@else
							{{ "Nill" }}
						@endif
					</div>
				</div>
				<div class="col-xs-12">
					<div class="col-xs-3 text-right clr_blue">
						<label>{{ trans('messages.lbl_location') }}</label>
					</div>
					<div>
						@if($view[0]->Location == 1)
							{{ "India" }}
						@else
							{{ "Japan" }}
						@endif
					</div>
				</div>
				<div class="col-xs-12">
					<div class="col-xs-3 text-right clr_blue">
						<label>{{ trans('messages.lbl_bank_name') }}</label>
					</div>
					<div>
						@if($view[0]->BankName !="")
							{{ $view[0]->BankName }}
						@else
							{{ "Nill" }}
						@endif
					</div>
				</div>
				<div class="col-xs-12">
					<div class="col-xs-3 text-right clr_blue">
						<label>{{ trans('messages.lbl_nickname') }}</label>
					</div>
					<div>
						@if($view[0]->Bank_NickName !="")
							{{ $view[0]->Bank_NickName }}
						@else
							{{ "Nill" }}
						@endif
					</div>
				</div>
				<div class="col-xs-12">
					<div class="col-xs-3 text-right clr_blue">
						<label>{{ trans('messages.lbl_branch_name') }}</label>
					</div>
					<div>
						@if($view[0]->BranchName !="")
							{{ $view[0]->BranchName }}
						@else
							{{ "Nill" }}
						@endif 
					</div>
				</div>
				<div class="col-xs-12">
					<div class="col-xs-3 text-right clr_blue">
						<label>{{ trans('messages.lbl_branch_number') }}</label>
					</div>
					<div>
						@if($view[0]->BranchNo !="")
							{{ $view[0]->BranchNo }}
						@else
							{{ "Nill" }}
						@endif
					</div>
				</div>
				<div class="col-xs-12 mb10">
					<div class="col-xs-3 text-right clr_blue">
						<label>{{ trans('messages.lbl_accounttype') }}</label>
					</div>
					<div>
						@if($view[0]->Type!="")
							@if($view[0]->Location==1)
								{{  getAccountType($view[0]->Type) }}
							@else  
								{{ getJpnAccountType($view[0]->Type) }}
							@endif
						@else
							NILL
						@endif 
					</div>
				</div>
			</fieldset>	
		</div>
	</article>
</div>
@endsection