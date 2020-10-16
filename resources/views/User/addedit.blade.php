@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/user.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
	$(document).ready(function() {
		setDatePicker("dataview");
		setDatePicker18yearbefore("dob");
	});
</script>
@php use App\Http\Helpers @endphp
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="master" class="DEC_flex_wrapper " data-category="master master_sub_1">
	{{ Form::open(array('name'=>'frmuseraddedit','id'=>'frmuseraddedit', 
			'url' => 'User/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
			'files'=>true,'method' => 'POST')) }}
	{{ Form::hidden('filterval', $request->filterval, array('id' => 'filterval')) }}
	{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
	{{ Form::hidden('sortOptn',$request->usersort , array('id' => 'sortOptn')) }}
	{{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
	{{ Form::hidden('searchmethod', $request->searchmethod, array('id' => 'searchmethod')) }}
	{{ Form::hidden('editflg', $request->editflg, array('id' => 'editflg')) }}
	{{ Form::hidden('id', '', array('id' => 'id')) }}
	{{ Form::hidden('viewid', $request->editid, array('id' => 'viewid')) }}
	{{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
	{{ Form::hidden('DOB', $dob_year, array('id' => 'DOB')) }}
	{{ Form::hidden('hdnuserclassification', Session::get('userclassification'), array('id' => 'hdnuserclassification')) }}
	<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/employee.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_user') }}</h2>
			<h2 class="pull-left mt15">・</h2>
			<h2 class="pull-left mt15 green">@if($request->editflg!="edit"){{ trans('messages.lbl_register') }}</h2>@else<h2 class="pull-left mt15 red">{{ trans('messages.lbl_edit') }}@endif</h2>
		</div>
	</div>
	<div class="pb10"></div>
	<!-- End Heading -->
	<div class="col-xs-12 pl5 pr5">
	<fieldset>
		@if($request->editflg =="edit")
		<div class="col-xs-12 mt15">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_usercode') }}<span class="fr ml2 white"> &nbsp; </span></label>
			</div>
			<div>
				<label class="">{{ ($userview[0]->usercode != "") ? $userview[0]->usercode : 'Nill'}}</label>
			</div>
		</div>
		<div class="col-xs-12  mt5">
		@else
		<div class="col-xs-12  mt15">
		@endif
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_UserID') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				{{ Form::text('MstuserUserID',(isset($userview[0]->userid)) ? $userview[0]->userid : 										'',array('id'=>'MstuserUserID',
							'name' => 'MstuserUserID',
							'data-label' => trans('messages.lbl_UserID'),
							'class'=>'box25per form-control pl5')) 
				}}
			</div>
		</div>
		@if($request->editflg !="edit")
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_password') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				{{ Form::password('MstuserPassword',array('id'=>'MstuserPassword',
															'name' => 'MstuserPassword',
															'data-label' => trans('messages.lbl_password'),
															'class'=>'box25per form-control pl5')) 
				}}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_conpassword') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				{{ Form::password('MstuserConPassword',array('id'=>'MstuserConPassword',
															'name' => 'MstuserConPassword',
															'data-label' => trans('messages.lbl_conpassword'),
															'class'=>'box25per form-control pl5')) 
				}}
			</div>
		</div>
		@endif
		<div class="col-xs-12 mt5" style="height: 30px !important;">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_userclassification') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				{{ Form::select('MstuserUserKbn',[null=>''] + $Classificationarray,(isset($userview[0]->userclassification)) ? 	$userview[0]->userclassification : '', array('name' => 'MstuserUserKbn',
															'id'=>'MstuserUserKbn',
															'data-label' => trans('messages.lbl_userclassification'),
															'onchange'=>'javascript:fnopendate(this.value);fnempty();',
															'class'=>'pl5'))
				}}
			</div>
		</div>
		@if(Session::get('userclassification') != 4)
			{{ Form::hidden('DataView', (isset($userview[0]->accessDate)) ? $userview[0]->accessDate : 										'', array('id' => 'DataView')) }}
		@endif
		@if(Session::get('userclassification') == 4)
			<div id="hidecheckbox" class="col-xs-12 mt5" style="display: none;">
				<div class="col-xs-3 text-right clr_blue">
					<label>Data View Eligible From Date<span class="fr ml2 red"> &nbsp; </span></label>
				</div>
				<div>
					{{ Form::text('DataView',(isset($userview[0]->accessDate)) ? $userview[0]->accessDate : 										'',array('id'=>'DataView',
																	'name' => 'DataView',
																	'data-label' => trans('Data View Date'),
																	'class'=>'form-control pl5 box8per dataview')) 
					}}
					<label class=" ml2 fa fa-calendar fa-lg" for="DataView" aria-hidden="true"></label>
				</div>
			</div>
		@endif
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_unamesurname') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				{{ Form::text('MstuserSurNM',(isset($userview[0]->username)) ? $userview[0]->username : 													'',array('id'=>'MstuserSurNM',
													'name' => 'MstuserSurNM',
													'data-label' => trans('messages.lbl_unamesurname'),
													'class'=>'box25per form-control pl5')) 
				}}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_givenname') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				{{ Form::text('MstuserSurNMK',(isset($userview[0]->givenname)) ? $userview[0]->givenname : 														'',array('id'=>'MstuserSurNMK',
												 'name' => 'MstuserSurNMK',
												 'data-label' => trans('messages.lbl_givenname'),
												 'class'=>'box25per form-control pl5')) 
				}}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_nickname') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				{{ Form::text('Mstusernickname',(isset($userview[0]->nickName)) ? $userview[0]->nickName : 														'',array('id'=>'Mstusernickname', 
												'name' => 'Mstusernickname',
												'data-label' => trans('messages.lbl_nickname'),
												'class'=>'box25per form-control pl5')) 
				}}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_dob') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				{{ Form::text('MstuserBirthDT',(isset($userview[0]->dob) && ($userview[0]->dob)!="0000-00-00") ? $userview[0]->								dob : '',array('id'=>'MstuserBirthDT',
															'name' => 'MstuserBirthDT',
															'data-label' => trans('messages.lbl_dob'),
															'class'=>'box8per form-control pl5 dob')) 
				}}
				<label class="mt10 ml2 fa fa-calendar fa-lg" for="MstuserBirthDT" aria-hidden="true"></label>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_gender') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-md-6 pm0">
				<label class="fwn">
				{{ Form::radio('MstuserSex', '1',(isset($userview[0]->gender) && ($userview[0]->gender)=="1") ? $userview[0]->							gender : '', array('id' =>'MstuserSex1',
															'name' => 'MstuserSex',
															'class' => 'comp',
															'data-label' => trans('messages.lbl_gender'))) 
				}}
				<span class="vam">&nbsp;{{ trans('messages.lbl_male') }}&nbsp;</span>
				</label>
				<label class="fwn">
					{{ Form::radio('MstuserSex', '2',(isset($userview[0]->gender) && ($userview[0]->gender)=="2") ? $userview[						0]->gender : '', array('id' =>'MstuserSex2',
																'name' => 'MstuserSex',
																'class' => 'ntcomp',
																'data-label' => trans('messages.lbl_gender')))
					}}
					<span class="vam">&nbsp;{{ trans('messages.lbl_female') }}&nbsp;</span>
				</label>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_mobilenumber') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
			@if(isset($userview[0]->mobileno))
			{{--*/ $mobile = explode('-',$userview[0]->mobileno);/*--}}
				@if(!isset($mobile[0]))  {{$mobile[0]=""}} @endif
				@if(!isset($mobile[1]))  {{$mobile[1]=""}} @endif
				@if(!isset($mobile[2]))  {{$mobile[2]=""}} @endif
			@else
				 {{$mobile[0]=""}}
				 {{$mobile[1]=""}}
				 {{$mobile[2]=""}}
			@endif
				{{ Form::text('MstuserTelNO',$mobile[0],array('id'=>'MstuserTelNO', 
														'name' => 'MstuserTelNO',
														'maxlength' => '3',
														'class'=>'box4per form-control pl5',
														'data-label' => trans('messages.lbl_mobilenumber'),
														'onkeydown' => 'return nextfield("MstuserTelNO","MstuserTelNO1","3",event)',
														'onkeypress' => 'return isNumberKey(event)')) }} -
				{{ Form::text('MstuserTelNO1',$mobile[1],array('id'=>'MstuserTelNO1',
														'name' => 'MstuserTelNO1',
														'maxlength' => '4',
														'class'=>'box5per form-control pl5',
														'data-label' => trans('messages.lbl_mobilenumber'),
														'onkeydown' => 'return nextfield("MstuserTelNO1","MstuserTelNO2","4",event)',
														'onkeypress' => 'return isNumberKey(event)')) }} -
				{{ Form::text('MstuserTelNO2',$mobile[2],array('id'=>'MstuserTelNO2',
														'name' => 'MstuserTelNO2',
														'maxlength' => '4',
														'class'=>'box5per form-control pl5',
														'data-label' => trans('messages.lbl_mobilenumber'),
														'onkeypress' => 'return isNumberKey(event)')) }}
				<span>&nbsp;(Ex: 080-3138-4449）</span>
			</div>
		</div>
		<div class="col-xs-12 mt5 mb10">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_mailid') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				{{ Form::text('MstuserMailID',(isset($userview[0]->email)) ? $userview[0]->email : 															'',array('id'=>'MstuserMailID', 
													'name' => 'MstuserMailID',
													'data-label' => trans('messages.lbl_mailid'),
													'class'=>'box25per form-control pl5')) 
				}}
				<span>&nbsp;(Ex: info@XXXXX.co.jp）</span>
			</div>
		</div>
	</fieldset>
	<fieldset class="bg_footer_clr">
		<div class="form-group">
			<div align="center" class="mt5">
			@if($request->editflg =="edit")
				<button type="submit" class="btn edit btn-warning box100 addeditprocess">
					<i class="fa fa-edit" aria-hidden="true"></i> {{ trans('messages.lbl_update') }}
				</button>
				<a onclick="javascript:gotoindexpage('1','{{ $request->mainmenu }}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				</a>
			@else
				<button type="submit" class="btn btn-success add box100 addeditprocess ml5">
					<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
				</button>
				<a onclick="javascript:gotoindexpage('2','{{ $request->mainmenu }}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				</a>
			@endif
			</div>
		</div>
	</fieldset>
	</div>
	{{ Form::close() }}
	{{ Form::open(array('name'=>'frmuseraddeditcancel', 'id'=>'frmuseraddeditcancel', 'url' => 'Ourdetail/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
	{{ Form::hidden('filterval', $request->filterval, array('id' => 'filterval')) }}
	{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
	{{ Form::hidden('sortOptn',$request->usersort , array('id' => 'sortOptn')) }}
	{{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
	{{ Form::hidden('searchmethod', $request->searchmethod, array('id' => 'searchmethod')) }}
	{{ Form::hidden('viewid', $request->editid, array('id' => 'viewid')) }}
	{{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
	{{ Form::close() }}
</article>
</div>
<div class="CMN_display_block pb10"></div>
@if($request->editflg =="edit")
<script type="text/javascript">
	fnopendate('{{ $userview[0]->userclassification }}');
</script>
@endif
@endsection