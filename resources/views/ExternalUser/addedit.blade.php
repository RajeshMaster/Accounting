@extends('layouts.app')
@section('content')

{{ HTML::script('resources/assets/js/externaluser.js') }}
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

<article id="external" class="DEC_flex_wrapper" data-category="external external_sub_1">

	{{ Form::open(array('name'=>'frmextuseraddedit','id'=>'frmextuseraddedit', 
						'url' => 'ExternalUser/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
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
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_user') }}</h2>
			<h2 class="pull-left mt15">・</h2>
			@if($request->editflg!="edit")
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
			<div class="col-xs-12 mt15">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_usernamesign') }}
						<span class="fr ml2 red"> * </span></label>
				</div>
				<div>
					{{ Form::text('userName',(isset($userview[0]->userName)) ? $userview[0]->userName : '',
							array('id'=>'userName',
								'name' => 'userName',
								'data-label' => trans('messages.lbl_usernamesign'),
								'class'=>'box25per form-control pl5')) 
					}}
				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_mailid') }}
						<span class="fr ml2 red"> * </span></label>
				</div>
				<div>
					{{ Form::text('emailId',(isset($userview[0]->emailId)) ? $userview[0]->emailId : '', array('id'=>'emailId', 
									'name' => 'emailId',
									'data-label' => trans('messages.lbl_mailid'),
									'class'=>'box25per form-control pl5')) 
					}}

					<span>&nbsp;(Ex: info@XXXXX.co.jp）</span>
				</div>
			</div>
			@if($request->editflg != "edit")
				<div class="col-xs-12 mt5">
					<div class="col-xs-3 text-right clr_blue">
						<label>{{ trans('messages.lbl_password') }}
							<span class="fr ml2 red"> * </span></label>
					</div>
					<div>
						{{ Form::password('userPassword',
								array('id'=>'userPassword',
									'name' => 'userPassword',
									'data-label' => trans('messages.lbl_password'),
									'class'=>'box25per form-control pl5')) 

						}}
					</div>
				</div>
				<div class="col-xs-12 mt5">
					<div class="col-xs-3 text-right clr_blue">
						<label>{{ trans('messages.lbl_conpassword') }}
							<span class="fr ml2 red"> * </span></label>
					</div>
					<div>
						{{ Form::password('userConPassword',
								array('id'=>'userConPassword',
									'name' => 'userConPassword',
									'data-label' => trans('messages.lbl_conpassword'),
									'class'=>'box25per form-control pl5')) 
						}}
					</div>
				</div>
			@endif
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_address') }}
						<span class="fr ml2 red"> * </span></label>
				</div>
				<div>
					{{ Form::text('address',(isset($userview[0]->address)) ? $userview[0]->address : '', array('id'=>'address', 
									'name' => 'address',
									'data-label' => trans('messages.lbl_address'),
									'class'=>'box25per form-control pl5')) 
					}}
				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_buildingname') }}
						<span class="fr ml2 red"> * </span></label>
				</div>
				<div>
					{{ Form::text('buildingName',(isset($userview[0]->buildingName)) ? $userview[0]->buildingName : '', array('id'=>'buildingName', 
									'name' => 'buildingName',
									'data-label' => trans('messages.lbl_buildingname'),
									'class'=>'box25per form-control pl5')) 
					}}
				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_pincode') }}
						<span class="fr ml2 red"> * </span></label>
				</div>
				<div>
					{{ Form::text('pincode',(isset($userview[0]->pincode)) ? $userview[0]->pincode : '', array('id'=>'pincode', 
									'name' => 'pincode',
									'maxlength' => '6',
									'data-label' => trans('messages.lbl_pincode'),
									'class'=>'box10per form-control pl5',
									'onkeypress' => 'return isNumberKey(event)')) 
					}}
				</div>
			</div>
			<div class="col-xs-12 mt5 mb15">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_mobilenumber') }}
						<span class="fr ml2 red"> * </span></label>
				</div>
				<div>
					@if(isset($userview[0]->mobileno))
						{{--*/ $mobile = explode('-',$userview[0]->mobileno);/*--}}

						@if(!isset($mobile[0]))  {{ $mobile[0]="" }} @endif
						@if(!isset($mobile[1]))  {{ $mobile[1]="" }} @endif
						@if(!isset($mobile[2]))  {{ $mobile[2]="" }} @endif

					@else

						{{ $mobile[0]="" }}
						{{ $mobile[1]="" }}
						{{ $mobile[2]="" }}

					@endif

					{{ Form::text('userTelNo1',$mobile[0],
								array('id'=>'userTelNo1', 
									'name' => 'userTelNo1',
									'maxlength' => '3',
									'class'=>'box4per form-control pl5',
									'data-label' => trans('messages.lbl_mobilenumber'),
									'onkeydown' => 'return nextfield("userTelNo1","userTelNo2","3",event)',
									'onkeypress' => 'return isNumberKey(event)')) }} -

					{{ Form::text('userTelNo2',$mobile[1],
								array('id'=>'userTelNo2',
									'name' => 'userTelNo2',
									'maxlength' => '4',
									'class'=>'box5per form-control pl5',
									'data-label' => trans('messages.lbl_mobilenumber'),
									'onkeydown' => 'return nextfield("userTelNo2","userTelNo3","4",event)',
									'onkeypress' => 'return isNumberKey(event)')) }} -

					{{ Form::text('userTelNo3',$mobile[2],
								array('id'=>'userTelNo3',
								'name' => 'userTelNo3',
								'maxlength' => '4',
								'class'=>'box5per form-control pl5',
								'data-label' => trans('messages.lbl_mobilenumber'),
								'onkeypress' => 'return isNumberKey(event)')) }}

					<span>&nbsp;(Ex: 080-3138-4449）</span>

				</div>
			</div>
		</fieldset>
		<fieldset>
			<div class="col-xs-12 mt15">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_kananame') }}
						<span class="fr ml2 red"> * </span></label>
				</div>
				<div>
					{{ Form::text('bankKanaName',(isset($userview[0]->bankKanaName)) ? $userview[0]->bankKanaName : '',
							array('id'=>'bankKanaName',
								'name' => 'bankKanaName',
								'maxlength' => 30,
								'data-label' => trans('messages.lbl_kananame'),
								'class'=>'box25per form-control pl5')) 
					}}
					<span>&nbsp;(Ex: カ）マイクロビット）</span>
				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_account_no') }}
						<span class="fr ml2 red"> * </span></label>
				</div>
				<div>
					{{ Form::text('accountNo',(isset($userview[0]->accountNo)) ? $userview[0]->accountNo : '', 
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
					{{ Form::text('bankName',(isset($userview[0]->bankName)) ? $userview[0]->bankName : '', array('id'=>'bankName', 
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
					{{ Form::text('branchName',(isset($userview[0]->branchName)) ? $userview[0]->branchName : '', array('id'=>'branchName', 
									'name' => 'branchName',
									'maxlength' => 20,
									'data-label' => trans('messages.lbl_branch_name'),
									'class'=>'box25per form-control pl5')) 
					}}
				</div>
			</div>
			<div class="col-xs-12 mt5 mb15">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_branch_number') }}
						<span class="fr ml2 red" style="display: none;"> * </span>
					</label>
				</div>
				<div>
					{{ Form::text('branchNo',(isset($userview[0]->branchNo)) ? $userview[0]->branchNo : '', array('id'=>'branchNo', 
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


	{{ Form::open(array('name'=>'frmextuseraddeditcancel','id'=>'frmextuseraddeditcancel', 
						'url' => 'ExternalUser/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}

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