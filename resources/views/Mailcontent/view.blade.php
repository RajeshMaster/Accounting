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
</style>
{{ HTML::script('resources/assets/js/mailcontent.js') }}
<div class="CMN_display_block box100per" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="mail" class="DEC_flex_wrapper " data-category="mail mail_sub_2">
	{{ Form::open(array('name'=>'frmcontentmview', 
						'id'=>'frmcontentmview', 
						'url' => 'Mailcontent/view?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	    {{ Form::hidden('emailid','', array('id' => 'emailid')) }}
	    {{ Form::hidden('editflg','', array('id' => 'editflg')) }}
		<!-- Start Heading --> 
		<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/mailcontent.png') }}">
			<h2 class="pull-left pl5 mt10">{{ trans('messages.lbl_mailcontent') }}</h2>
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
			<a href="javascript:goviewtoindex('{{ $request->mainmenu }}');" class="btn btn-info box80"><span class="fa fa-arrow-left"></span> {{ trans('messages.lbl_back') }}</a>

			<a href="javascript:gotoeditpage('{{ $getmaildetails[0]->id }}','{{ $request->mainmenu }}');" class="btn btn-warning box100"><span class="fa fa-pencil"></span> {{ trans('messages.lbl_edit') }}</a>
		</div>
		</div>
		<div class="ml10 mr5">
			<fieldset>
				<div class="col-xs-12 mt10">
					<div class="col-xs-4 clr_blue text-right">
						<label>{{ trans('messages.lbl_mailid') }}</label>
					</div>
					<div>
						<b>
							<span style="color:brown;">
								{{ (!empty($getmaildetails[0]->mailId)?$getmaildetails[0]->mailId:"Nill") }}
							</span>
						</b>
					</div>
				</div>
				<div class="col-xs-12 mt5">
					<div class="col-xs-4 clr_blue text-right">
						<label>{{ trans('messages.lbl_mailname') }}</label>
					</div>
					<div>
						{{ (!empty($getmaildetails[0]->mailName) ? $getmaildetails[0]->mailName : "Nill") }}
					</div>
				</div>
				<div class="col-xs-12 mt5">
					<div class="col-xs-4 clr_blue text-right">
						<label>{{ trans('messages.lbl_mailtype') }}</label>
					</div>
					<div>
						{{ $getmaildetails[0]->typeName }}
					</div>
				</div>
				<div class="col-xs-12 mt5">
					<div class="col-xs-4 clr_blue text-right">
						<label>{{ trans('messages.lbl_subject') }}</label>
					</div>
					<div>
						{{ (!empty($getmaildetails[0]->subject) ? $getmaildetails[0]->subject : "Nill")}}
					</div>
				</div>
				<div class="col-xs-12 mt5 mb10">
					<div class="col-xs-4 clr_blue text-right">
						<label>{{ trans('messages.lbl_content') }}</label>
					</div>
					<div class="col-xs-8 pm0"> 
						{!! nl2br(e((!empty($getmaildetails[0]->content) ? $getmaildetails[0]->content : "Nill"))) !!}
					</div>
				</div>
			</fieldset>
		</div>
	{{ Form::close() }}
</article>
</div>
@endsection