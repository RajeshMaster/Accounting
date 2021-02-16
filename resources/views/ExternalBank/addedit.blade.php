@extends('layouts.app')
@section('content')

{{ HTML::script('resources/assets/js/externalbank.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}

<script type="text/javascript">

	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';

	$(document).ready(function() {
		setDatePicker18yearbefore("dob");
	});

</script>

@php use App\Http\Helpers @endphp

<div class="CMN_display_block" id="main_contents">

<!-- article to select the main&sub menu -->

<article id="external" class="DEC_flex_wrapper" data-category="external external_sub_2">

	{{ Form::open(array('name'=>'frmextbankaddedit','id'=>'frmextbankaddedit', 
						'url' => 'ExternalBank/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,'method' => 'POST')) }}

	{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
	{{ Form::hidden('editflg', $request->editflg, array('id' => 'editflg')) }}
	{{ Form::hidden('id', '', array('id' => 'id')) }}
	{{ Form::hidden('viewId', $request->editid, array('id' => 'viewId')) }}
	{{ Form::hidden('editId', $request->editid, array('id' => 'editId')) }}

	<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/employee.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_bankdetail') }}</h2>
			<h2 class="pull-left mt15">・</h2>
			@if($request->editflg != "edit")
				<h2 class="pull-left mt15 green">
					{{ trans('messages.lbl_register') }}
				</h2>
			@else
				<h2 class="pull-left mt15 red">
					{{ trans('messages.lbl_edit') }}
				</h2>
			@endif
		</div>
	</div>
	<div class="pb10"></div>

	<!-- End Heading -->

	<div class="col-xs-12 pl5 pr5">
		<fieldset>
			<div id="errorSectiondisplay" align="center" class="box100per mt5"></div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_kananame') }}
						<span class="fr ml2 red"> * </span></label>
				</div>
				<div>
					{{ Form::text('bankKanaName',(isset($bankview[0]->bankKanaName)) ? $bankview[0]->bankKanaName : '',
							array('id'=>'bankKanaName',
								'name' => 'bankKanaName',
								'maxlength' => 30,
								'data-label' => trans('messages.lbl_kananame'),
								'class'=>'box25per form-control pl5')) 
					}}
					<span>&nbsp;(Ex: microbit limited company）</span>
				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_account_no') }}
						<span class="fr ml2 red"> * </span></label>
				</div>
				<div>
					{{ Form::text('accountNo',(isset($bankview[0]->accountNo)) ? $bankview[0]->accountNo : '', 
							array('id'=>'accountNo',
									'name' => 'accountNo',
									'maxlength' => 15,
									'data-label' => trans('messages.lbl_account_no'),
									'class'=>'box12per form-control pl5')) 
					}}
					
				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_accounttype') }}
						<span class="fr ml2 red"> * </span></label>
				</div>
				<div>
					{{ Form::select('accountType', $jpnaccounttype, (isset($getdetails->accountType)) ? $getdetails->accountType : '',
							array('name' => 'accountType',
									'id'=>'accountType',
									'data-label' => trans('messages.lbl_accounttype'),
									'class'=>'pl5'))}}

				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_bank_name') }}
						<span class="fr ml2 red"> * </span></label>
				</div>
				<div>
					{{ Form::text('bankName',(isset($bankview[0]->bankName)) ? $bankview[0]->bankName : '', array('id'=>'bankName', 
									'name' => 'bankName',
									'maxlength' => 30,
									'data-label' => trans('messages.lbl_bank_name'),
									'class'=>'box25per form-control pl5')) 
					}}
				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_branch_name') }}
						<span class="fr ml2 red"> * </span></label>
				</div>
				<div>
					{{ Form::text('branchName',(isset($bankview[0]->branchName)) ? $bankview[0]->branchName : '', array('id'=>'branchName', 
									'name' => 'branchName',
									'maxlength' => 20,
									'data-label' => trans('messages.lbl_branch_name'),
									'class'=>'box25per form-control pl5')) 
					}}
				</div>
			</div>
			<div class="col-xs-12 mt5 mb10">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_branch_number') }}
						<span class="fr ml2 red"> * </span></label>
				</div>
				<div>
					{{ Form::text('branchNo',(isset($bankview[0]->branchNo)) ? $bankview[0]->branchNo : '', array('id'=>'branchNo', 
									'name' => 'branchNo',
									'maxlength' => '12',
									'data-label' => trans('messages.lbl_branch_number'),
									'class'=>'box12per form-control pl5',
									'onkeypress' => 'return isNumberKey(event)')) 
					}}
				</div>
			</div>
		</fieldset>
		<fieldset class="bg_footer_clr">
			<div class="form-group">
				<div align="center" class="mt5">
				@if($request->editflg =="edit")
					<button type="submit" class="btn edit btn-warning box100 addeditprocess">
						<i class="fa fa-edit" aria-hidden="true"></i>
							{{ trans('messages.lbl_update') }}
					</button>
					<a onclick="javascript:gotoindexpage('1');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
					</a>
				@else
					<button type="submit" class="btn btn-success add box100 addeditprocess ml5">
						<i class="fa fa-plus" aria-hidden="true"></i>
							{{ trans('messages.lbl_register') }}
					</button>
					<a onclick="javascript:gotoindexpage('2');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
					</a>
				@endif
				</div>
			</div>
		</fieldset>
	</div>

	{{ Form::close() }}


	{{ Form::open(array('name'=>'frmextbankaddeditcancel','id'=>'frmextbankaddeditcancel', 
						'url' => 'ExternalBank/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}

		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('viewId', $request->editid, array('id' => 'viewId')) }}
		{{ Form::hidden('editId', $request->editid, array('id' => 'editId')) }}
	{{ Form::close() }}

	</article>

</div>

<div class="CMN_display_block pb10"></div>

@endsection