@extends('layouts.app')
@section('content')

{{ HTML::script('resources/assets/js/externalbank.js') }}

<script type="text/javascript">

	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';

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

<article id="external" class="DEC_flex_wrapper " data-category="external external_sub_2">

	{{ Form::open(array('name'=>'frmextbankview','id'=>'frmextbankview',
						'url' => 'ExternalUser/addedit?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,'method' => 'POST')) }}

	{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
	{{ Form::hidden('editflg', '', array('id' => 'editflg')) }}
	{{ Form::hidden('id', $bankview[0]->id , array('id' => 'id')) }}
	{{ Form::hidden('viewId', $request->viewId , array('id' => 'viewId')) }}
	{{ Form::hidden('editId', $bankview[0]->id , array('id' => 'editId')) }}

	<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt15" src="{{ URL::asset('resources/assets/images/bank.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_bankdetail') }}<span>・</span><span class="colbl">{{ trans('messages.lbl_view') }}</span></h2>
		</div>
	</div>
	<div class="pb10"></div>
	<!-- End Heading -->

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

	<div class="pl5 pr5">
	<div class="pull-left ml5">
		<a href="javascript:backindexpage();" class="pageload btn btn-info box80"><span class="fa fa-arrow-left"></span> 
		{{ trans('messages.lbl_back') }}</a>
	</div>

	@if($bankview[0]->delflg != "1")
		<div class="pull-right mr5">
			<a href="javascript:addeditview('edit','{{ $bankview[0]->id }}');" class="pageload btn btn-warning box80 pull-right pr10"><span class="fa fa-pencil"></span> {{ trans('messages.lbl_edit') }}</a>
		</div>
	@endif

	<div class="col-xs-12 pl5 pr5">
		<fieldset>

		<div class="col-xs-12 mt15">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_kananame') }}</label>
			</div>
			<div class="col-xs-9">
				{{ ($bankview[0]->bankKanaName != "") ? $bankview[0]->bankKanaName : 'Nill'}}
			</div>
		</div>

		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_account_no') }}</label>
			</div>
			<div class="col-xs-9">
				{{ ($bankview[0]->accountNo != "") ? $bankview[0]->accountNo : 'Nill'}}
			</div>
		</div>

		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_accounttype') }}</label>
			</div>
			<div class="col-xs-9">
				{{ ($bankview[0]->accountType != "") ? getJpnAccountType($bankview[0]->accountType) : 'Nill'}}
			</div>
		</div>

		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_bank_name') }}</label>
			</div>
			<div class="col-xs-9">
				{{ ($bankview[0]->bankName != "") ? $bankview[0]->bankName : 'Nill'}}
			</div>
		</div>

		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_branch_name') }}</label>
			</div>
			<div class="col-xs-9">
				{{ ($bankview[0]->branchName != "") ? $bankview[0]->branchName : 'Nill'}}
			</div>
		</div>

		<div class="col-xs-12 mt5 mb15">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_branch_number') }}</label>
			</div>
			<div class="col-xs-9">
				{{ ($bankview[0]->branchNo != "") ? $bankview[0]->branchNo : 'Nill'}}
			</div>
		</div>

		</fieldset>
	</div>

	</div>

	{{ Form::close() }}

</article>

</div>

<div class="CMN_display_block pb10"></div>



@endsection