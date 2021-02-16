@extends('layouts.app')
@section('content')

<script type="text/javascript">

	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';

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

	.fb {
		color: gray !important;
	}

	.sort_asc {
		background-image:url({{ URL::asset('resources/assets/images/upArrow.png') }}) !important;
	}

	.sort_desc {
		background-image:url({{ URL::asset('resources/assets/images/downArroW.png') }}) !important;
	}

</style>

{{ HTML::script('resources/assets/js/externaluser.js') }}
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}

<div class="CMN_display_block" id="main_contents">

<!-- article to select the main&sub menu -->

<article id="external" class="DEC_flex_wrapper" data-category="external external_sub_1">

	{{ Form::open(array('name'=>'frmextuserindex', 'id'=>'frmextuserindex', 
						'url' => 'ExternalUser/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true, 'method' => 'POST')) }}

		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('editflg', '', array('id' => 'editflg')) }}
		{{ Form::hidden('id', '', array('id' => 'id')) }}
		{{ Form::hidden('viewId', '', array('id' => 'viewId')) }}
		{{ Form::hidden('delflg', '', array('id' => 'delflg')) }}

		<!-- Start Heading -->

		<div class="row hline">
			<div class="col-xs-12 mr10">
				<img class="pull-left box35 mt15" src="{{ URL::asset('resources/assets/images/employee.png') }}">
				<h2 class="pull-left pl5 mt15">
					{{ trans('messages.lbl_alluserlist') }}
				</h2>
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

		<div class="col-xs-12 mb10 pm0">
			<div class="col-xs-6 ml10 pm0 pull-left">
				<a href="javascript:addedit('add');" 
					class="pageload btn btn-success box100">
					<span class="fa fa-plus"></span> 
					{{ trans('messages.lbl_register') }}
				</a>
			</div>
		</div>

		<div class="mr10 ml10 minh400">
			<table class="tablealternate box100per">
				<colgroup>
					<col width="20%">
					<col width="">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
					<tr class="tableheader fwb tac"> 
						<th class="tac">{{ trans('messages.lbl_email') }}</th>
						<th class="tac">{{ trans('messages.lbl_Details') }}</th>
					</tr>

				</thead>

				<tbody>

					@forelse($userdetails as $key => $data)
						<tr>
						<td>
							<div>
								<label class="pm0 vam colbl">
									{{ $data->emailId }}
								</label>
							</div>
						</td>
						<td>
							<div class="ml5 pt5 pb8">
								<div class="mb8">
									<b> {{ $data->userName }} </b>
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
							</div>
							<div class="ml5 mb8 smallBlue CMN_display_block">
								<div class="CMN_display_block ml3">
									<a href="javascript:userView('{{ $data->id }}');"
										class="pageload">
									{{ trans('messages.lbl_Details') }}</a>&nbsp;
									<span class="ml3">|</span>
								</div>
								<div class="CMN_display_block ml3">
									@if($data->delflg == 1)
										<a href="javascript:changeDelFlg('{{ $data->id }}','{{ $data->delflg }}');" class="colbl">
											{{ trans('messages.lbl_use') }}
										</a>
									@else
										<a href="javascript:changeDelFlg('{{ $data->id }}','{{ $data->delflg }}');" class="colred">		{{trans('messages.lbl_notuse') }}
										</a> 
									@endif
								</div>
							</div>
						</td>
						</tr>
					@empty
						<tr>
							<td class="text-center colred" colspan="2">
								{{ trans('messages.lbl_nodatafound') }}
							</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		<div class="text-center">
			@if(!empty($userdetails->total()))
				<span class="pull-left mt24">
					{{ $userdetails->firstItem() }} ~ {{ $userdetails->lastItem() }} / {{ $userdetails->total() }}
				</span>
				{{ $userdetails->links() }}
				<div class="CMN_display_block flr">
					{{ $userdetails->linkspagelimit() }}
				</div>
			@endif 
		</div>

	{{ Form::close() }}

	</article>

</div>

@endsection