@extends('layouts.app')
@section('content')
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
	//start function denotes multisearch link..
	function mulclick(divid){
		if($('#'+divid).css('display') == 'block'){
			document.getElementById(divid).style.display = 'none';
			document.getElementById(divid).style.height= "173px";
		}else {
			document.getElementById(divid).style.display = 'block';
		}
	}
	//end function denotes multisearch link..
</script>
<style type="text/css">
	.alertboxalign {
		margin-bottom: -60px !important;
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
		background-image:url({{ URL::asset('resources/assets/images/downArroW.png') }}) !important;
	}
</style>
{{ HTML::script('resources/assets/js/user.js') }}
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="master" class="DEC_flex_wrapper " data-category="master master_sub_1">
	{{ Form::open(array('name'=>'frmuserindex', 
						'id'=>'frmuserindex', 
						'url' => 'User/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('editflg', '', array('id' => 'editflg')) }}
		{{ Form::hidden('filterval', $request->filterval, array('id' => 'filterval')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('id', '', array('id' => 'id')) }}
		{{ Form::hidden('sortOptn',$request->usersort , array('id' => 'sortOptn')) }}
		{{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
		{{ Form::hidden('searchmethod', $request->searchmethod, array('id' => 'searchmethod')) }}
		{{ Form::hidden('viewid', '', array('id' => 'viewid')) }}
		{{ Form::hidden('userid', '', array('id' => 'userid')) }}
		{{ Form::hidden('delflag', '', array('id' => 'delflag')) }}
	<!-- Start Heading -->
	<div class="row hline">
	<div class="col-xs-12 mr10">
			<img class="pull-left box35 mt15" src="{{ URL::asset('resources/assets/images/employee.png') }}">
			<h2 class="pull-left pl5 mt15">@if(Session::get('userclassification') == "4"){{ trans('messages.lbl_alluserlist') }}@else User Details
@endif</h2>
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
	<div class="col-xs-12 pm0 pull-left" >
		@if(Session::get('userclassification') == "4")
		<div class="col-xs-6 ml10 pm0 pull-left">
			<a href="javascript:addedit('add');" class="pageload btn btn-success box100"><span class="fa fa-plus"></span> {{ trans('messages.lbl_register') }}</a>
		</div>
		<div class="col-xs-12 pm0 pull-left">
			<div class="col-xs-7 pm0 CMN_display_block pull-left">
				{{ Form::button(
								trans('messages.lbl_all'),
								array('class'=>'pageload btn btn-link '.$disabledall,
								'type'=>'button',
								'onclick' => 'javascript:return filter(1)')) 
				}}
				<span>|</span>
				{{ Form::button(
								trans('messages.lbl_unused'),
								array('class'=>'pageload btn btn-link '.$disabledunused,
								'type'=>'button',
								'onclick' => 'javascript:return filter(2)')) 
				}}
				<span>|</span>
				{{ Form::button(
								trans('messages.lbl_staff'),
								array('class'=>'pageload btn btn-link '.$disabledstaff,
								'type'=>'button',
								'onclick' => 'javascript:return filter(3)')) 
				}}
				<span>|</span>
				{{ Form::button(
								trans('messages.lbl_conEmployee'),
								array('class'=>'pageload btn btn-link '.$disabledcontract,
								'type'=>'button',
								'onclick' => 'javascript:return filter(4)')) 
				}}
				<span>|</span>
				{{ Form::button(
								trans('messages.lbl_subEmployee'),
								array('class'=>'pageload btn btn-link '.$disabledsubcontract,
								'type'=>'button',
								'onclick' => 'javascript:return filter(5)')) 
				}}
				<span>|</span>
				{{ Form::button(
								trans('messages.lbl_pvtPerson'),
								array('class'=>'pageload btn btn-link '.$disabledprivate,
								'type'=>'button',
								'onclick' => 'javascript:return filter(6)')) 
				}}
			</div>
			<div class="col-xs-5 pm0 pr12">
				<div class="form-group pm0 pull-right moveleft nodropdownsymbol" id="moveleft">
					<a href="javascript:clearsearch()" title="Clear Search">
						<img class="pull-left box30 mr5 pageload" src="{{ URL::asset('resources/assets/images/clearsearch.png') }}">
					</a>
					{{ Form::select('usersort', $sortarray, $request->usersort,
									array('class' => 'form-control'.' ' .$request->sortstyle.' '.'CMN_sorting pull-right',
									'id' => 'usersort',
									'style' => $sortMargin,
									'name' => 'usersort'))
					}}
				</div>
			</div>
	</div>
	@endif
	<div>
	<div class="mr10 ml10">
		<div class="minh400">
			<table class="tablealternate box100per">
				<colgroup>
					<col width="10%">
					<col width="">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
					<tr class="tableheader fwb tac"> 
						<th class="tac">{{ trans('messages.lbl_usercode') }}</th>
						<th class="tac">{{ trans('messages.lbl_Details') }}</th>
					</tr>
				</thead>
				<tbody>
				@forelse($userdetails as $count => $data)
				<tr>
					<td>
						<div class="">
							@if($data->delflg == 0)
								<label class="pm0 vam colbl">
									{{ $data->usercode }}
								</label>
							@else
								<label class="pm0 vam colred">
									{{ $data->usercode }}
								</label>
							@endif
						</div>
						<div>
							<span class="estStatusDIV_New_1">
								@if($data->userclassification==0 && $data->delflg==0)
										{{trans('messages.lbl_staff')}} 
								@elseif($data->userclassification==1 && $data->delflg==0)
										{{trans('messages.lbl_conEmployee')}}
								@elseif($data->userclassification==2 && $data->delflg==0)
										{{trans('messages.lbl_subEmployee')}} 
								@elseif($data->userclassification==3 && $data->delflg==0)
										{{trans('messages.lbl_pvtPerson')}} 	
								@elseif($data->userclassification==4 && $data->delflg==0)
										{{trans('messages.lbl_superadmin')}} 
								@else
										{{trans('messages.lbl_unused')}} 
								@endif
							</span>
						</div>
					</td>
					<td>
						<div class="ml5 pt5 pb8">
							<div class="mb8">
								<b>{{$data->username}}@if(!empty($data->nickName)) ({{ $data->nickName }}) @endif</b>
							</div>
							<div class="f12 vam label_gray boxhei24">
								<span class="f12"> 
									{{ trans('messages.lbl_Gender') }} :
								</span>
								<span class="f12">
									@if($data->gender == 1)
										{{ trans('messages.lbl_male') }}
									@else 
										{{ trans('messages.lbl_female')  }} 
									@endif
								</span>
								<span class="f12 ml20">
									{{ trans('messages.lbl_Creater') }} :
								</span>
								<span class="f12">
									{{ (!empty($data->UpdatedBy) ?  $data->UpdatedBy : "Nill")  }}
								</span>
							</div>
						</div class="">
							<div class="ml5 mb8 smallBlue CMN_display_block">
								<div class="CMN_display_block ml3">
									<a href="javascript:userview('{{ $data->id }}');" class="pageload">{{ trans('messages.lbl_Details') }}</a>&nbsp;@if(Session::get('userclassification') == "4")<span class="ml3">|</span>
								</div>
								<div class="CMN_display_block ml3">
									@if($data->delflg==1)
										<a href="javascript:fnchangeflag('{{ $data->id }}','{{ $data->delflg }}');" class="colbl">
											{{ trans('messages.lbl_use') }}
										</a>
									@else
										<a href="javascript:fnchangeflag('{{ $data->id }}','{{ $data->delflg }}');" class="colred">		{{trans('messages.lbl_notuse') }}
										</a> 
									@endif
								</div>
									@endif
							</div>
						<div>
						</div>
					</td>
				</tr>
				@empty
				<tr>
					<td class="text-center colred" colspan="2" >{{ trans('messages.lbl_nodatafound') }}</td>
				</tr>
				@endforelse
			</tbody>
			</table>
		</div>
		@if(Session::get('userclassification') == "4")
			<div class="text-center">
				@if(!empty($userdetails->total()))
					<span class="pull-left mt24">
						{{ $userdetails->firstItem() }} ~ {{ $userdetails->lastItem() }} / {{ $userdetails->total() }}
					</span>
				@endif 
				{{ $userdetails->links() }}
			<div class="CMN_display_block flr">
				{{ $userdetails->linkspagelimit() }}
			</div>
			</div>
		@endif
		<!-- SEARCH -->
			<div style="top: 137px!important;position: fixed;" @if ($request->searchmethod == 1 || $request->searchmethod == 2) 
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
							{!! Form::text('singlesearch', $request->singlesearch,
											array('','class'=>' form-control box80per pull-left',
											'style'=>'height:30px;',
											'id'=>'singlesearch'))
							!!}
							{{ Form::button('<i class="fa fa-search" aria-hidden="true"></i>', 
											array('class'=>'ml5 mt2 pull-left search box15per btn btn-info btn-sm', 
											'type'=>'button',
											'name' => 'advsearch',
											'onclick' => 'javascript:return usinglesearch()',
											'style'=>'border: none;' ))
							}}
						<div>
					</li>
				</ul>
				<div class="mt5 ml10 pull-left mb5">
					<a href="#demo" onclick="mulclick('demo');" class="font_arial_verdana">
						{{ trans('messages.lbl_multi_search') }}
					</a>
				</div>
				<ul id="demo"  @if ($request->searchmethod == 2) class="collapse in ml5 pull-left"
						@else class="collapse ml5 pull-left" @endif>
					<li class="theme-option" onKeyPress="return checkSubmitmulti(event)">
						<span class="mt2" class="font_arial_verdana">{{ trans('messages.lbl_usercode') }}</span>
						<div class="mt5 box88per">
							{!! Form::text('msearchempid', $request->msearchempid,
											array('','id' => 'msearchempid',
											'style'=>'height:30px;',
											'class'=>'form-control box93per')) 
							!!}
						</div>
						<div class="mt5">
						<span class="pt3" class="font_arial_verdana">{{ trans('messages.lbl_userstate') }}</span>
						<div class="mt5 box88per">
							{{Form::select('userclassification', [null=>''] + $Classificationarray , null,
											array('id' => 'userclassification',
											'class' => 'input-sm form-control box93per',
											'style' => 'background-color: white;'))
							}}
						</div>
						</div>
						<div class="mt5 mb6">
							{{ Form::button(
											'<i class="fa fa-search" aria-hidden="true"></i> '.trans('messages.lbl_search'),
											array('class'=>'mt10 btn btn-info btn-sm',
											'onclick' => 'javascript:return umultiplesearch()',
											'type'=>'button')) 
							}}
						</div>
					</li>
				</ul>
			</div>
			<!-- END SEARCH -->
	{{ Form::close() }}
	</article>
</div>
@endsection