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

{{ HTML::script('resources/assets/js/externalbank.js') }}
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}

<div class="CMN_display_block" id="main_contents">

<!-- article to select the main&sub menu -->

<article id="external" class="DEC_flex_wrapper" data-category="external external_sub_2">

	{{ Form::open(array('name'=>'frmextbankindex', 'id'=>'frmextbankindex', 
						'url' => 'ExternalBank/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true, 'method' => 'POST')) }}

		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('editflg', '', array('id' => 'editflg')) }}
		{{ Form::hidden('id', '', array('id' => 'id')) }}
		{{ Form::hidden('viewId', '', array('id' => 'viewId')) }}
		{{ Form::hidden('delflg', '', array('id' => 'delflg')) }}
		{{ Form::hidden('mainflg', '', array('id' => 'mainflg')) }}
		<!-- Start Heading -->

		<div class="row hline">
			<div class="col-xs-12 mr10">
				<img class="pull-left box35 mt15" src="{{ URL::asset('resources/assets/images/bank.png') }}">
				<h2 class="pull-left pl5 mt15">
					{{ trans('messages.lbl_bankdetail') }}
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

		<div class="pt40 minh400">
			<table class="tablealternate CMN_tblfixed">
				<colgroup>

					<col width="4%">
					<col width="11%">
					<col>
					<col width="20%">
					<col width="15%">
					<col width="11%">
					<col width="5%">
					<col width="7%">
					<col width="10%">

				</colgroup>
				<thead class="CMN_tbltheadcolor">

					<tr class="tableheader fwb tac"> 
						<th class="vam">{{ trans('messages.lbl_sno') }}</th>
						<th class="vam">{{ trans('messages.lbl_account_no') }}</th>
						<th class="vam">{{ trans('messages.lbl_kananame') }}</th>
						<th class="vam">{{ trans('messages.lbl_bank_name') }}</th>
						<th class="vam">{{ trans('messages.lbl_branch_name') }}</th>
						<th class="vam">{{ trans('messages.lbl_branch_number') }}</th>
						<th class="vam">{{ trans('messages.lbl_mtype') }}</th>
						<th class="vam">{{ trans('messages.lbl_main') }}</th>
						<th class="vam">
							{{ trans('messages.lbl_use') }} / {{ trans('messages.lbl_notuse') }}
						</th>
					</tr>

				</thead>

				<tbody>
					@php $i = 0; @endphp
					@forelse($bankdetails as $key => $data)
						<tr>
						<td class="bor_rightbot_none text-center">
							{{ ($bankdetails->currentpage()-1) * $bankdetails->perpage() + $i + 1 }}
						</td>
						<td>
							<a href="javascript:getbankview('{{ $data->id }}')" 
								class="anchorstyle">
									{{ $data->accountNo }}
							</a>
						</td>
						<td @if(strlen($data->bankKanaName) > 17) 
								title="{{ $data->bankKanaName }}"
							@endif>
							@if(singlefieldlength($data->bankKanaName,17))
								{{ singlefieldlength($data->bankKanaName,17) }}
							@else
								{{ $data->bankKanaName }}
							@endif
						</td>
						<td @if(strlen($data->bankName) > 20) 
								title="{{ $data->bankName }}"
							@endif>
							@if(singlefieldlength($data->bankName,20))
								{{ singlefieldlength($data->bankName,20) }}
							@else
								{{ $data->bankName }}
							@endif
						</td>
						<td @if(strlen($data->branchName) > 15) 
								title="{{ $data->branchName }}"
							@endif>
							@if(singlefieldlength($data->branchName,15))
								{{ singlefieldlength($data->branchName,15) }}
							@else
								{{ $data->branchName }}
							@endif
						</td>
						<td @if(strlen($data->branchNo) > 13) 
								title="{{ $data->branchNo }}"
							@endif>
							@if(singlefieldlength($data->branchNo,13))
								{{ singlefieldlength($data->branchNo,13) }}
							@else
								{{ $data->branchNo }}
							@endif
						</td>
						<td class="text-center">
							{{ getJpnAccountType($data->accountType) }}
						</td>
						<td class= "vam tac">
							@if($data->delflg == 0)
								@if($data->mainflg == 1)
									<a href="javascript:changeMainFlg('{{ $data->id }}','{{ $data->mainflg }}');" class="green">
										{{ trans('messages.lbl_main') }}
									</a>
								@else
									<a href="javascript:changeMainFlg('{{ $data->id }}','{{ $data->mainflg }}');" class="colbl">
										{{ trans('messages.lbl_tomain') }}
									</a>
								@endif
							@else
								<label class="colbl">
									{{ trans('messages.lbl_tomain') }}
								</label>
							@endif
						</td>
						<td class= "vam tac">
							@if($data->delflg == 1)
								<a href="javascript:changeDelFlg('{{ $data->id }}','{{ $data->delflg }}');" class="colred">
									{{ trans('messages.lbl_use') }}
								</a>
							@else
								<a href="javascript:changeDelFlg('{{ $data->id }}','{{ $data->delflg }}');" class="colbl">		{{trans('messages.lbl_notuse') }}
								</a> 
							@endif
						</td>
						</tr>
						@php $i++; @endphp
					@empty
						<tr>
							<td class="text-center colred" colspan="9">
								{{ trans('messages.lbl_nodatafound') }}
							</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		<div class="text-center">
			@if(!empty($bankdetails->total()))
				<span class="pull-left mt24">
					{{ $bankdetails->firstItem() }} ~ {{ $bankdetails->lastItem() }} / {{ $bankdetails->total() }}
				</span>
				{{ $bankdetails->links() }}
				<div class="CMN_display_block flr">
					{{ $bankdetails->linkspagelimit() }}
				</div>
			@endif 
		</div>

	{{ Form::close() }}

	</article>

</div>

@endsection