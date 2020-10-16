@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/ourdetail.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
<?php use App\Http\Helpers; ?>
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	$(document).ready(function() {
		setDatePicker("txt_establishdate");
	});
</script>
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="ourdetails" class="DEC_flex_wrapper " data-category="ourdetails our_details_sub_1">
	{{ Form::open(array('name'=>'frmourdetailaddedit', 'id'=>'frmourdetailaddedit', 'url' => 'Ourdetail/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('editflg', $request->editflg, array('id' => 'editflg')) }}
		{{ Form::hidden('id', '', array('id' => 'id')) }}
		{{ Form::hidden('editid', $request->editid , array('id' => 'editid')) }}
	<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-8 pl5">
			<img class="pull-left box35 mt15" src="{{ URL::asset('resources/assets/images/ourdetails.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_ourdetails') }}</h2>
            <h2 class="pull-left mt15">・</h2>
            <h2 class="pull-left mt15 red">@if(isset($detedit)){{ trans('messages.lbl_edit') }}</h2>@else<h2 class="pull-left mt15 green">{{ trans('messages.lbl_register') }}@endif</h2>
		</div>
	</div>
	<div class="pb10"></div>
	<!-- End Heading -->
	<div class="col-xs-12 pl5 pr5">
	<fieldset>
		<div class="col-xs-12 mt15">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_companyname') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				@if(isset($detedit))
					{{ Form::text('txt_cmyname',$detedit[0]->CompanyName,array(
										'id'=>'txt_cmyname',
										'name' => 'txt_cmyname',
										'class'=>'box25per form-control',
										'data-label' => trans('messages.lbl_companyname'))) }}
				@else
					{{ Form::text('txt_cmyname','',array(
										'id'=>'txt_cmyname',
										'name' => 'txt_cmyname',
										'class'=>'box25per form-control',
										'data-label' => trans('messages.lbl_companyname'))) }}
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_companynamekana') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				@if(isset($detedit))
					{{ Form::text('txt_kananame',$detedit[0]->CompanyNamekana,array(
										'id'=>'txt_kananame',
										'name' => 'txt_kananame',
										'class'=>'box25per form-control',
										'data-label' => trans('messages.lbl_companynamekana'))) }}
				@else
					{{ Form::text('txt_kananame',null,array(
										'id'=>'txt_kananame',
										'name' => 'txt_kananame',
										'class'=>'box25per form-control',
										'data-label' => trans('messages.lbl_companynamekana'))) }}
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_postalservice') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				@if(isset($detedit))
					{{ Form::text('txt_pincode1',substr($detedit[0]->pincode,0,3),array(
										'id'=>'txt_pincode1',
										'name' => 'txt_pincode1',
										'class'=>'box5per form-control',
										'onkeydown' => 'return nextfield("txt_pincode1","txt_pincode2","3",event)',
										'onkeypress' => 'return isNumberKey(event)',
										'data-label' => trans('messages.lbl_postalservice'))) }}
					{{ Form::text('txt_pincode2',substr($detedit[0]->pincode,4,8),array(
										'id'=>'txt_pincode2',
										'name' => 'txt_pincode2', 
										'maxlength' => '4',
										'class'=>'box5per form-control',
										'onkeypress' => 'return isNumberKey(event)',
										'data-label' => trans('messages.lbl_postalservice'))) }}
					<span>&nbsp;(Ex: 313-4449)</span>
				@else
					{{ Form::text('txt_pincode1',null,array(
										'id'=>'txt_pincode1',
										'name' => 'txt_pincode1',
										'class'=>'box5per form-control',
										'onkeydown' => 'return nextfield("txt_pincode1","txt_pincode2","3",event)',
										'onkeypress' => 'return isNumberKey(event)',
										'data-label' => trans('messages.lbl_postalservice'))) }}
					{{ Form::text('txt_pincode2',null,array(
										'id'=>'txt_pincode2',
										'name' => 'txt_pincode2',
										'maxlength' => '4',
										'class'=>'box5per form-control',
										'onkeypress' => 'return isNumberKey(event)',
										'data-label' => trans('messages.lbl_postalservice'))) }}
					<span>&nbsp;(Ex: 313-4449)</span>
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_perfecturename') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				@if(isset($detedit))
					{{ Form::text('txt_prefectname',$detedit[0]->Prefecturename,array(
										'id'=>'txt_prefectname',
										'name' => 'txt_prefectname',
										'class'=>'box25per form-control',
										'data-label' => trans('messages.lbl_perfecturename'))) }}
				@else
					{{ Form::text('txt_prefectname',null,array(
										'id'=>'txt_prefectname',
										'name' => 'txt_prefectname',
										'class'=>'box25per form-control',
										'data-label' => trans('messages.lbl_perfecturename'))) }}
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_address') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				@if(isset($detedit))
					{{ Form::text('txt_jpaddress',$detedit[0]->Streetaddress,array(
										'id'=>'txt_jpaddress',
										'name' => 'txt_jpaddress',
										'class'=>'box25per form-control',
										'data-label' => trans('messages.lbl_address'))) }}
				@else
					{{ Form::text('txt_jpaddress',null,array(
										'id'=>'txt_jpaddress',
										'name' => 'txt_jpaddress',
										'class'=>'box25per form-control',
										'data-label' => trans('messages.lbl_address'))) }}
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_buildingname') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				@if(isset($detedit))
					{{ Form::text('txt_buildingname',$detedit[0]->BuildingName,array(
										'id'=>'txt_buildingname',
										'name' => 'txt_buildingname',
										'class'=>'box25per form-control',
										'data-label' => trans('messages.lbl_buildingname'))) }}
				@else
					{{ Form::text('txt_buildingname',null,array(
										'id'=>'txt_buildingname',
										'name' => 'txt_buildingname',
										'class'=>'box25per form-control',
										'data-label' => trans('messages.lbl_buildingname'))) }}
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_tel') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				@if(isset($detedit))
					{{ Form::text('Tel1',$tel[0],array(
										'id'=>'Tel1',
										'name' => 'Tel1',
										'maxlength' => '3',
										'class'=>'box5per form-control',
										'onkeydown' => 'return nextfield("Tel1","Tel2","3",event)',
										'onkeypress' => 'return isNumberKey(event)',
										'data-label' => trans('messages.lbl_tel'))) }}
					{{ Form::text('Tel2',$tel[1],array(
										'id'=>'Tel2',
										'name' => 'Tel2',
										'maxlength' => '4',
										'class'=>'box5per form-control',
										'onkeydown' => 'return nextfield("Tel2","Tel3","4",event)',
										'onkeypress' => 'return isNumberKey(event)',
										'data-label' => trans('messages.lbl_tel'))) }}
					{{ Form::text('Tel3',$tel[2],array(
										'id'=>'Tel3',
										'name' => 'Tel3',
										'maxlength' => '4',
										'class'=>'box5per form-control',
										'onkeypress' => 'return isNumberKey(event)',
										'data-label' => trans('messages.lbl_tel'))) }}
					<span>&nbsp;(Ex: 080-3138-4449）</span>
				@else
					{{ Form::text('Tel1',null,array(
										'id'=>'Tel1',
										'name' => 'Tel1',
										'class'=>'box5per form-control',
										'maxlength' => '3',
										'onkeydown' => 'return nextfield("Tel1","Tel2","3",event)',
										'onkeypress' => 'return isNumberKey(event)',
										'data-label' => trans('messages.lbl_tel'))) }}
					{{ Form::text('Tel2',null,array(
										'id'=>'Tel2',
										'name' => 'Tel2',
										'class'=>'box5per form-control',
										'maxlength' => '4',
										'onkeydown' => 'return nextfield("Tel2","Tel3","4",event)',
										'onkeypress' => 'return isNumberKey(event)',
										'data-label' => trans('messages.lbl_tel'))) }}
					{{ Form::text('Tel3',null,array(
										'id'=>'Tel3',
										'name' => 'Tel3',
										'maxlength' => '4',
										'class'=>'box5per form-control',
										'onkeypress' => 'return isNumberKey(event)',
										'data-label' => trans('messages.lbl_tel'))) }}
					<span>&nbsp;(Ex: 080-3138-4449）</span>
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_fax') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				@if(isset($detedit))
					{{ Form::text('fax1',$fax[0],array(
										'id'=>'fax1',
										'name' => 'fax1',
										'maxlength' => '3',
										'class'=>'box5per form-control',
										'onkeydown' => 'return nextfield("fax1","fax2","3",event)',
										'onkeypress' => 'return isNumberKey(event)',
										'data-label' => trans('messages.lbl_fax'))) }}
					{{ Form::text('fax2',$fax[1],array(
										'id'=>'fax2',
										'name' => 'fax2',
										'maxlength' => '4',
										'class'=>'box5per form-control',
										'onkeydown' => 'return nextfield("fax2","fax3","4",event)',
										'onkeypress' => 'return isNumberKey(event)',
										'data-label' => trans('messages.lbl_fax'))) }}
					{{ Form::text('fax3',$fax[2],array(
										'id'=>'fax3',
										'name' => 'fax3',
										'maxlength' => '4',
										'class'=>'box5per form-control',
										'onkeypress' => 'return isNumberKey(event)',
										'data-label' => trans('messages.lbl_fax'))) }}
				@else
					{{ Form::text('fax1',null,array(
										'id'=>'fax1',
										'name' => 'fax1',
										'class'=>'box5per form-control',
										'maxlength' => '3',
										'onkeydown' => 'return nextfield("fax1","fax2","3",event)',
										'onkeypress' => 'return isNumberKey(event)',
										'data-label' => trans('messages.lbl_fax'))) }}
					{{ Form::text('fax2',null,array(
										'id'=>'fax2',
										'name' => 'fax2',
										'class'=>'box5per form-control',
										'maxlength' => '4',
										'onkeydown' => 'return nextfield("fax2","fax3","4",event)',
										'onkeypress' => 'return isNumberKey(event)',
										'data-label' => trans('messages.lbl_fax'))) }}
					{{ Form::text('fax3',null,array(
										'id'=>'fax3',
										'name' => 'fax3',
										'maxlength' => '4',
										'class'=>'box5per form-control',
										'onkeypress' => 'return isNumberKey(event)',
										'data-label' => trans('messages.lbl_fax'))) }}
				@endif
				<span>&nbsp;(Ex: 080-3138-4449）</span>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_commonmail') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				@if(isset($detedit))
					{{ Form::text('txt_commonmail',$detedit[0]->Commonmail,array(
										'id'=>'txt_commonmail',
										'name' => 'txt_commonmail',
										'class'=>'box25per form-control',
										'data-label' => trans('messages.lbl_commonmail'))) }}
				@else
					{{ Form::text('txt_commonmail',null,array(
										'id'=>'txt_commonmail',
										'name' => 'txt_commonmail',
										'class'=>'box25per form-control',
										'data-label' => trans('messages.lbl_commonmail'))) }}
				@endif
				<span>&nbsp;(Ex: info@.XXXXX.co.jp)</span>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_url') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				@if(isset($detedit))
					{{ Form::text('txt_websiteurl',$detedit[0]->URL,array(
										'id'=>'txt_websiteurl',
										'name' => 'txt_websiteurl',
										'class'=>'box25per form-control',
										'data-label' => trans('messages.lbl_url'))) }}
				@else
					{{ Form::text('txt_websiteurl',null,array(
										'id'=>'txt_websiteurl',
										'name' => 'txt_websiteurl',
										'class'=>'box25per form-control',
										'data-label' => trans('messages.lbl_url'))) }}
				@endif
				<span>&nbsp;(Ex: www.microbit.co.jp)</span>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_establisheddate') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				@if(isset($detedit))
					{{ Form::text('txt_establishdate',$detedit[0]->Establisheddate,array(
										'id'=>'txt_establishdate',
										'name' => 'txt_establishdate',
										'class'=>'box8per form-control pl5 txt_establishdate',
										'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
										'data-label' => trans('messages.lbl_establisheddate'),
										'maxlength' => '10')) }}
				@else
					{{ Form::text('txt_establishdate',null,array(
										'id'=>'txt_establishdate',
										'name' => 'txt_establishdate',
										'class'=>'box8per form-control pl5 txt_establishdate',
										'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
										'data-label' => trans('messages.lbl_establisheddate'),
										'maxlength' => '10')) }}
				@endif
				<label class="mt10 ml2 fa fa-calendar fa-lg" for="txt_establishdate" aria-hidden="true"></label>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_closingdate') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				@if(isset($detedit))
					{{ Form::text('txt_clsmonth',$detedit[0]->Closingmonth,array(
										'id'=>'txt_clsmonth',
										'name' => 'txt_clsmonth',
										'class'=>'box5per form-control',
										'data-label' => trans('messages.lbl_month'),
										'maxlength' => '2')) }} Mn
					{{ Form::text('txt_clsdate',$detedit[0]->Closingdate,array(
										'id'=>'txt_clsdate',
										'name' => 'txt_clsdate',
										'class'=>'box5per form-control',
										'data-label' => trans('messages.lbl_day'),
										'maxlength' => '2')) }} Day
				@else
					{{ Form::text('txt_clsmonth',null,array(
										'id'=>'txt_clsmonth',
										'name' => 'txt_clsmonth',
										'class'=>'box5per form-control',
										'data-label' => trans('messages.lbl_month'),
										'maxlength' => '2')) }} Mn
					{{ Form::text('txt_clsdate',null,array(
										'id'=>'txt_clsdate',
										'name' => 'txt_clsdate',
										'class'=>'box5per form-control',
										'data-label' => trans('messages.lbl_day'),
										'maxlength' => '2')) }} Day
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5 mb15">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_systemname') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				@if(isset($detedit))
					{{ Form::text('txt_systemname',$detedit[0]->systemname,array(
										'id'=>'txt_systemname',
										'name' => 'txt_systemname',
										'class'=>'box25per form-control',
										'data-label' => trans('messages.lbl_systemname'))) }}
				@else
					{{ Form::text('txt_systemname',null,array(
										'id'=>'txt_systemname',
										'name' => 'txt_systemname',
										'class'=>'box25per form-control',
										'data-label' => trans('messages.lbl_systemname'))) }}
				@endif
			</div>
		</div>
		<div class="CMN_display_block pb10"></div>
	</fieldset>
	<fieldset style="background-color: #DDF1FA;">
		<div class="form-group">
			<div align="center" class="mt5">
			@if(isset($detedit))
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
	{{ Form::close() }}
	{{ Form::open(array('name'=>'frmuseraddeditcancel', 'id'=>'frmuseraddeditcancel', 'url' => 'Ourdetail/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
	{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
	{{ Form::hidden('editflg', $request->editflg, array('id' => 'editflg')) }}
	{{ Form::hidden('id', '', array('id' => 'id')) }}
	{{ Form::hidden('editid', $request->editid , array('id' => 'editid')) }}
	{{ Form::close() }}
</div>
</article>
<div class="CMN_display_block pb10"></div>
@endsection