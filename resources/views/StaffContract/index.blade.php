@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::script('resources/assets/js/staffContract.js') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	$(document).ready(function() {
		$("#mulclick").click(function(){
        $("#demo").toggle();
   		});
    	setDatePicker("datarange");
	});
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
	.fb{
		color: gray !important;
	}
	.sort_asc {
		background-image:url({{ URL::asset('resources/assets/images/upArrow.png') }}) !important;
	}
	.sort_desc {
		background-image:url({{ URL::asset('resources/assets/images/downArrow.png') }}) !important;
	}
</style>
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="staff" class="DEC_flex_wrapper " data-category="staff staff_sub_2">
	{{ Form::open(array('name'=>'empcontract', 
						'id'=>'empcontract', 
						'url' => 'StaffContr/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	{{ Form::hidden('pageclick', $request->pageclick , array('id' => 'pageclick')) }}
	{{ Form::hidden('resignid', $request->resignid , array('id' => 'resignid')) }} 
	{{ Form::hidden('selectsorted','$request->selectsorted', array('id' => 'selectsorted')) }}
	{{ Form::hidden('searchmethod', $request->searchmethod, array('id' => 'searchmethod')) }}
	{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	{{ Form::hidden('viewid', '' , array('id' => 'viewid')) }}
	{{ Form::hidden('sortOptn',$request->selectsort , array('id' => 'sortOptn')) }}
	{{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
	{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box30 mt15" src="{{ URL::asset('resources/assets/images/contractImg.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_contractList') }}</h2>
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
	<div class="col-xs-12 pm0 pull-left">
		<div class="col-xs-12 pm0 pull-left">
			<div class="col-xs-7 pm0 CMN_display_block pull-left vam pt5 box50per">
				@if($resignid == 0)
				<a href="javascript:selectActive(1);" style="color:blue;" class="pl10 pb5">
					{{ trans('messages.lbl_completed') }}
				</a>
				@else
				<a href="javascript:selectActive(0);" style="color:blue;" class="pl10 pb5">
					{{ trans('messages.lbl_existing') }}
				</a>
				@endif
			</div>
			<div class="col-xs-5 pm0 pr12 mb5 box50per">
				<div class="form-group pm0 pull-right moveleft nodropdownsymbol" id="moveleft">
								
					<!-- <a href="javascript:taxpopupenable('{{ $request->mainmenu }}');" id="" class="pull-right pr10 mt15 anchorstyle">
          			<span class="fa fa-percent"></span>{{ trans('messages.lbl_tax_setting') }}</a> -->
				<a href="javascript:clearsearch()" title="Clear Search">
					<img class="box30 mr5 " src="{{ URL::asset('resources/assets/images/clearsearch.png') }}">
				</a>
					{{ Form::select('selectsort', [null=>''] + $selectVal, $request->selectsort,
									array('class' => 'form-control'.' ' .$request->sortstyle.' '.'CMN_sorting pull-right',
									'id' => 'selectsort',
									'style' => $sortMargin,
									'name' => 'selectsort'))
					}}
				</div>
			</div>
		</div>
	</div>
	<div>
	<div class="mr10 ml10">
		<div class="minh300">
			<table class="tablealternate box100per">
				<colgroup>
					<col width="3.5%">
					<col width="9.5%">
					<col width="15.5%">
					<col width="25.5%">
					<col width="11.5%">
					<col width="7.5%">
					<col width="8.5%">
					<col width="11%">
					<col width="5.5%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
					<tr class="tableheader fwb tac"> 
						<th class="tac">{{ trans('messages.lbl_sno') }}</th>
						<th class="tac">{{ trans('messages.lbl_empid') }}</th>
						<th class="tac">{{ trans('messages.lbl_empName') }}</th>
						<th class="tac">{{ trans('messages.lbl_periodofWork') }}</th>
						<th class="tac">{{ trans('messages.lbl_salary') }}</th>
						<th class="tac">{{ trans('messages.lbl_Others') }}</th>
						<th class="tac">{{ trans('messages.lbl_total') }}</th>
						<th class="tac" title="Contract Date">{{ trans('messages.lbl_cdate') }}</th>
						<th class="tac">{{ trans('messages.lbl_validity') }}</th>
						</tr>
					</thead>
				<tbody>
					{{ $temp = ""}}
					{{--*/ $row = '0' /*--}}
					{{--*/ $row1 = '0' /*--}}
					{{ $tempcomp = ""}}
					{{ $tempcomp1 = ""}}
					{{--*/ $rowcomp = '0' /*--}}
					{{--*/ $rowcomp1 = '0' /*--}}
					{{ $tempold = ""}}
					{{--*/ $rowold = '0' /*--}}
					<?php $style_tr="";$style_tdold="";$style_td1=""; ?>
					@if(count($contractdet)!="")
						@for ($i = 0; $i < count($contractdet); $i++)
							{{--*/ $loc = $contractdet[$i]['Emp_ID'] /*--}}
							{{--*/ $loccomp = $contractdet[$i]['LastName'] /*--}}
							{{--*/ $loccomp1 = $contractdet[$i]['FirstName'] /*--}}
						@if($loc != $temp) 
							@if($row==1)
								{{--*/ $style_tr = 'background-color: #E5F4F9;' /*--}}
								{{--*/ $row = '0' /*--}}
							@else
								{{--*/ $style_tr = 'background-color: #FFFFFF;' /*--}}
								{{--*/ $row = '1' /*--}}
						@endif
							{{--*/ $style_td = '' /*--}}
						@else
							{{--*/ $style_td = 'border-top: hidden;' /*--}}
						@endif
						@if($loccomp != $tempcomp && $loccomp1 != $tempcomp1) 
							@if($row1==1)
								{{--*/ $style_tr = 'background-color:#dff1f4;' /*--}}
								{{--*/ $row1 = '0' /*--}}
							@else
								{{--*/ $style_tr = 'background-color: #FFFFFF;' /*--}}
								{{--*/ $row1 = '1' /*--}}
							@endif
							{{--*/ $style_td1 = 'border-bottom: hidden;' /*--}}
						@else
							{{--*/ $style_td1 = 'border-top:hidden;' /*--}}
						@endif
				<tr style="{{$style_tr}}">
					<td class="tac">
					 {{ ($contract->currentpage()-1) * $contract->perpage() + $i + 1 }}
					</td>
					<td class="tac" style="{{$style_td}}">
						<a href="javascript:staffview('{{ $contractdet[$i]['Emp_ID'] }}');" class="fwb blue">
						@if($loc!=$temp)
							{{ $contractdet[$i]['Emp_ID'] }}
						@endif
						</a>
					</td>
					<td style="{{$style_td}}" title="{{ empnameontitle($contractdet[$i]['LastName'], $contractdet[$i]['FirstName'],25) }}">
						@if($loccomp != $tempcomp && $loccomp1 != $tempcomp1) 
							{{ empnamelength($contractdet[$i]['LastName'], $contractdet[$i]['FirstName'],25) }}
						@endif
					</td>
					<td class="tac">
					@if($contractdet[$i]['StartDate'] == "" && $contractdet[$i]['EndDate'] == "")
					
					@else
						{{--*/ $stdate_split = explode('-',$contractdet[$i]['StartDate']);
								$endate_split = explode('-',$contractdet[$i]['EndDate']); 
								$stdate_yr = $stdate_split[0];
								$stdate_mon = $stdate_split[1];
								$stdate_day = $stdate_split[2];
								$stend_yr = $endate_split[0];
								$endate_mnth = $endate_split[1];
								$endate_day = $endate_split[2];
								$Date = $stdate_yr."年".$stdate_mon."月".$stdate_day."日～"
									.$stend_yr."年".$endate_mnth."月".$endate_day."日";/*--}}
								{{ $Date  }}
					@endif
					</td>
					<td class="tar">
						@if($contractdet[$i]['Salary'] == "")
						@else
						{{ "¥".$contractdet[$i]['Salary'] }}
						@endif
					</td>
					<td class="tar">
						{{--*/ $Others = $contractdet[$i]['Allowance9']+$contractdet[$i]['Allowance10']
										 +$contractdet[$i]['Allowance1']+$contractdet[$i]['Allowance2']
										 +$contractdet[$i]['Allowance3']+$contractdet[$i]['Allowance4']
										 +$contractdet[$i]['Allowance5']+$contractdet[$i]['Allowance6']
										 +$contractdet[$i]['Allowance7']+$contractdet[$i]['Allowance8']; /*--}}
						@if($Others == "" && $contractdet[$i]['Salary'] == "")
						
						@else
						{{ "¥".$Others }}
						@endif
					</td>
					<td class="tar">
						@if($contractdet[$i]['Total'] == "")
						@else
						{{ "¥".$contractdet[$i]['Total'] }}
						@endif
					</td>
					<td class="tac">
						{{ $contractdet[$i]['Contract_date'] }}
					</td>
					@if($contractdet[$i]['Validity']< 90)
						@if($contractdet[$i]['Validity']< 0)
						<td class="tac">
							-
						</td>
						@else
						<td class="tac fwb red">
							{{ $contractdet[$i]['Validity'] }}
						</td>
						@endif
					@else
						<td class="tac fwb">
							{{ $contractdet[$i]['Validity'] }}
						</td>
					@endif
				</tr>
					{{--*/ $temp = $loc /*--}}
					{{--*/ $tempcomp = $loccomp /*--}}
					{{--*/ $tempcomp1 = $loccomp1 /*--}}
				@endfor
				@else
				<tr>
					<td class="text-center" colspan="9" style="color: red;"> No Data Found</td>
				</tr>
				@endif
			</tbody>
			</table>
		</div>
		<div class="text-center">
			@if(!empty($contract->total()))
				<span class="pull-left mt24">
					{{ $contract->firstItem() }} ~ {{ $contract->lastItem() }} / {{ $contract->total() }}
				</span>
			@endif 
			{{ $contract->links() }}
			<div class="CMN_display_block flr">
				{{ $contract->linkspagelimit() }}
			</div>
		</div>
		</div></div>
		<!-- SEARCH -->
		<div style="top: 136px!important;position: fixed;"
				@if ($request->searchmethod == 1 || $request->searchmethod == 2) 
					class="open CMN_fixed pm0" 
				@else 
					class="CMN_fixed pm0 pr0" 
				@endif 
					id="styleSelector">
			<div class="selector-toggle">
				<a id="sidedesignselector" href="javascript:void(0)"></a>
			</div>
			<ul>
				<span>
					<li style="">
						<p class="selector-title">{{ trans('messages.lbl_search') }}</p>
					</li>
				</span>
			<li class="theme-option ml6">
				<div class="box100per mt5" onKeyPress="return checkSubmitsingle(event)">
					{!! Form::text('singlesearch', null,
									array('',
									'class'=>' form-controlbox80perpull-left',
									'style'=>'height:30px;',
									'id'=>'singlesearch')) !!}
									{{ Form::button('<i class="fa fa-search" aria-hidden="true"></i>', 
									array(
									'class'=>'mr45 mt2 pull-right search box15per btn btn-info btn-sm', 
									'type'=>'button',
									'name' => 'advsearch',
									'onclick' => 'javascript:usinglesearch();',
									'style'=>'border: none;' 
					)) }}
				<div>
			</li>
			</ul>
			<div class="mt5 ml10 pull-left mb5">
				<a href="#demo" id="mulclick" style="font-family: arial, verdana;">
					{{ 	trans('messages.lbl_multi_search') }}
				</a>
			</div>
			<ul id="demo"	@if ($request->searchmethod == 2) class="collapse in ml5 pull-left" 
							@else class="collapse ml5 pull-left"  @endif>
				<li class="theme-option" onKeyPress="return checkSubmitmulti(event)">
					<div class="mt5">
						<span class="pt3" style="font-family: arial, verdana;">
							{{ trans('messages.lbl_employeeid') }}
						</span>
						<div class="mt5 box88per" style="display: inline-block!important;">
							{!! Form::text('employeeno', $request->employeeno,
								array('','id' =>'employeeno',
								'style'=>'height:30px;',
								'class'=>'box93per')) !!}
						</div>
					</div>
					<div class="mt5">
						<span class="pt3" style="font-family: arial, verdana;">
							{{ trans('messages.lbl_empName') }}
						</span>
						<div class="mt5 box88per" style="display: inline-block!important;">
							{!! Form::text('employeename', $request->employeename,
											array('',
											'id'=>'employeename',
											'style'=>'height:30px;',
											'class'=>'box93per')) !!}
						</div>
					</div>
					<div class="mt5">
						<span class="pt3" style="font-family: arial, verdana;">
							{{ trans('messages.lbl_doj') }}
						</span>
						<div class="mt5 box88per" style="display: inline-block!important;">
							<span class="CMN_display_block box35per " style="display: inline-block!important;">
								{{ Form::text('startdate','',
								array('id'=>'startdate',
									'name' => 'startdate',
									'data-label' => trans('messages.lbl_dob'),
									'class'=>'box100per datarange',
									'onkeypress' => 'return isNumberKey(event)')) }}
							</span>
						<label class="mt10 ml2 fa fa-calendar fa-lg CMN_display_block pr5" 
							for="startdate" aria-hidden="true" style="display: inline-block!important;">
						</label>
						<span class="CMN_display_block box35per " style="display: inline-block!important;">
						{{ Form::text('enddate','',array('id'=>'enddate', 'name' => 'enddate','data-label' => trans('messages.lbl_dob'),'class'=>'box100per datarange','onkeypress' => 'return isNumberKey(event)')) }}
						</span>
						<label class="mt10 ml2 fa fa-calendar fa-lg CMN_display_block" 
							for="enddate" aria-hidden="true" style="display: inline-block!important;">
						</label>
						</div>
					</div>
					<div class="mt5 mb6">
						{{ Form::button('<i class="fa fa-search" aria-hidden="true"></i>'.trans('messages.lbl_search'),
						array('class'=>'mt10 btn btn-info btn-sm ',
						'onclick' => 'javascript:return umultiplesearch()',
						'type'=>'button')) 	
						}}
					</div>
				</li>
			</ul>
		</div>
</div>
{{ Form::close() }}
@endsection