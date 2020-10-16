@extends('layouts.app')
@section('content')
@php use App\Http\Helpers; @endphp
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
</style>
{{ HTML::script('resources/assets/js/mailsignature.js') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="mail" class="DEC_flex_wrapper " data-category="mail mail_sub_3">
		
	{{ Form::open(array('name'=>'frmsignatureindex', 
						'id'=>'frmsignatureindex', 
						'url' => 'Mailsignature/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}

		<div class="col-xs-6 ml10 pm0 pull-left">
		</div>
		{{ Form::hidden('signid', '', array('id' => 'signid')) }}
		{{ Form::hidden('editflg', '', array('id' => 'editflg')) }}
		{{ Form::hidden('page', $request->page, array('id' => 'page')) }}
		{{ Form::hidden('plimit', $request->plimit, array('id' => 'plimit')) }}
<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/signature.png') }}">
			<h2 class="pull-left pl5 mt10">{{ trans('messages.lbl_mailsignature') }}</h2>
		</div>
	</div>
<!-- End Heading -->
	<div class="col-xs-12 pm0 mt10 mb10 pull-left">
		<!-- Session msg -->
		<div align="center" class="alertboxalign" role="alert">
			<p class="alert {{ Session::get('alert', Session::get('type') ) }}">
		</p>
		</div>
		<div class="col-xs-6 ml10 pm0 pull-left">
			<a href="javascript:fnreg('2');" class="btn btn-success box125"><span class="fa fa-plus"></span> {{ trans('messages.lbl_register') }}</a>
		</div>
	</div>
	<div class="mr10 ml10 mt10">
		<div class="minh395">
			<table class="tablealternate box100per">
				<colgroup>
					<col width="4%">
					<col width="8%">
					<col width="8%">
					<col width="30%">
					<col width="">
				</colgroup>
					<thead class="CMN_tbltheadcolor">
							<tr class="tableheader fwb tac"> 
							<th class="tac">{{ trans('messages.lbl_sno') }}</th>
							<th class="tac">{{ trans('messages.lbl_signid') }}</th>
							<th class="tac">{{ trans('messages.lbl_UserID') }}</th>
							<th class="tac">{{ trans('messages.lbl_usernamesign') }}</th>
							<th class="tac">{{ trans('messages.lbl_signcontent') }}</th>
					</tr>
				</thead>
					<tbody>
						@forelse($getlist as $key => $data)
						<tr>
							<td class="text-center">
								{{ ($getlist->currentpage()-1) * $getlist->perpage()+$key+1 }}
							</td>
							<td class="text-center">
								<a class="anchorstyle" href="javascript:gotosignview('{{ $data->signID }}');">{{ $data->signID }}</a>
							</td>
							<td class="text-center">
								{{ $data->user_ID }}
							</td>
							<td class="text-left">
								{{ $data->username }} {{ $data->givenname }} 
								@if($data->nickName != "")
									({{ $data->nickName }})
								@else 
								@endif 
							</td>
							<td class="">
								{!! nl2br(e(($data->content))) !!}
							</td>
						</tr>
						@empty
						<tr>
							<td class="text-center" colspan="5" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
	<div class="text-center ml10 mt90">
		@if(!empty($getlist->total()))
			<span class="pull-left mt24">
				{{ $getlist->firstItem() }} ~ {{ $getlist->lastItem() }} / {{ $getlist->total() }}
			</span>
		{{ $getlist->links() }}
		<div class="CMN_display_block flr pr10">
			{{ $getlist->linkspagelimit() }}
		</div>
		@endif 
	</div>
	{{ Form::close() }}
	<div id="mailsignpopup" class="modal fade">
		<div id="login-overlay">
			<div class="modal-content">
                <!-- Popup will be loaded here -->
			</div>
		</div>
	</div>
</article>
</div>
@endsection