@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/mailsignature.js') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
function goviewtosignindex(mainmenu) {
	pageload();
	$('#frmsignatureview').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
	$("#frmsignatureview").submit();
}
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
<div class="CMN_display_block box100per" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="mail" class="DEC_flex_wrapper " data-category="mail mail_sub_3">
	{{ Form::open(array('name'=>'frmsignatureview', 
						'id'=>'frmsignatureview', 
						'url' => 'Mailsignature/view?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('page', $request->page, array('id' => 'page')) }}
		{{ Form::hidden('plimit', $request->plimit, array('id' => 'plimit')) }}
		{{ Form::hidden('id','', array('id' => 'id')) }}
		{{ Form::hidden('editflg','', array('id' => 'editflg')) }}
		{{ Form::hidden('signid',$request->signid, array('id' => 'signid')) }}
		<!-- Start Heading --> 
		<div class="row hline">
			<div class="col-xs-12">
				<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/signature.png') }}">
				<h2 class="pull-left pl5 mt10">{{ trans('messages.lbl_mailsignature') }}</h2>
				<h2 class="pull-left mt10">・</h2>
				<h2 class="pull-left mt10 colbl">{{ trans('messages.lbl_view') }}</h2>
			</div>
		</div>
		<div class="col-xs-12 pm0 mt10 mb10 pull-left">
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
			<div class="col-xs-6 ml10 pm0 pull-left">
				@if(Session::get('userclassification') == "4")
					<a href="javascript:goviewtosignindex('{{ $request->mainmenu }}');" class="btn btn-info box80"><span class="fa fa-arrow-left"></span> {{ trans('messages.lbl_back') }}</a>
					<a href="javascript:signeditpage('{{ $getlist[0]->signID }}','{{ $request->mainmenu }}');" class="btn btn-warning box100"><span class="fa fa-pencil"></span> {{ trans('messages.lbl_edit') }}</a>
				@endif
				@if(Session::get('userclassification') != "4")
					@if(!empty($getlist))
						<a href="javascript:signeditpage('{{ $getlist[0]->signID }}','{{ $request->mainmenu }}');" class="btn btn-warning box100"><span class="fa fa-pencil"></span> {{ trans('messages.lbl_edit') }}</a>
					@else
						<a href="javascript:fnreg('2','1');" class="btn btn-success box125"><span class="fa fa-plus"></span> {{ trans('messages.lbl_register') }}</a>
					@endif
				@endif
			</div>
		</div>
		<div class="ml10 mr5">
			<fieldset>
				<div class="col-xs-12 mt10">
					<div class="col-xs-4 clr_blue text-right">
						<label>{{ trans('messages.lbl_usernamesign') }}</label>
					</div>
					<div>
						<b>
							<span style="color:brown;">
							@if(!empty($getlist[0]->username) || !empty($getlist[0]->givenname) || !empty($getlist[0]->nickName))
							{{ $getlist[0]->username }} {{ $getlist[0]->givenname }} 
							@if($getlist[0]->nickName != "")
							({{ $getlist[0]->nickName }})
							@else 
							@endif
							@else 
								{{ "Nill" }}
							@endif	
							</span>
						</b>
					</div>
				</div>
				<div class="col-xs-12 mt5 mb10">
					<div class="col-xs-4 clr_blue text-right">
						<label>{{ trans('messages.lbl_mailsignature') }}</label>
					</div>
					<div class="col-xs-8 pm0">
						{!! nl2br(e((!empty($getlist[0]->content) ? $getlist[0]->content : "Nill"))) !!}
					</div>
				</div>
			</fieldset>
		</div>
	{{ Form::close() }}
</article>
</div>
@endsection