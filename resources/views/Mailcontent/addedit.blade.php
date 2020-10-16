@extends('layouts.app')
@section('content')
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
		$(document).ready(function(){
		$("#mailtype option").each(function()
		{
			if ($(this).val() == "999") {
				$(this).css('font-weight','bold');
				$(this).css('color','brown');
			}
		});
	});
</script>
{{ HTML::script('resources/assets/js/mailcontent.js') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="mail" class="DEC_flex_wrapper " data-category="mail mail_sub_2">
	{{ Form::open(array('name'=>'frmcontentmaddedit', 
						'id'=>'frmcontentmaddedit', 
						'url' => 'Mailcontent/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	    {{ Form::hidden('editflg','', array('id' => 'editflg')) }}
	    {{ Form::hidden('emailid',$request->emailid, array('id' => 'emailid')) }}
		<!-- Start Heading --> 
		<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/mailcontent.png') }}">
			<h2 class="pull-left pl5 mt10">{{ trans('messages.lbl_mailcontent') }}</h2>
			<h2 class="pull-left mt10">・</h2>
			@if ($request->editflg=="1")
				<h2 class="pull-left mt10 red">{{ trans('messages.lbl_edit') }}</h2>
		    {{ Form::hidden('mainid',$getdataforupdate[0]->mailId, array('id' => 'mainid')) }}
			@else
				<h2 class="pull-left mt10 green">{{ trans('messages.lbl_register') }}</h2>
			@endif
		</div>
		</div>
		<div class="ml10 mr10 box99per">
			<fieldset>
				@if($request->editflg=="1")
				<div class="col-xs-12 mt10">
					<div class="col-xs-3 clr_blue text-right">
						<label>{{ trans('messages.lbl_mailid') }}<span class="fr ml2 red" style="visibility: hidden;"> * </span></label>
					</div>
					<div class="col-xs-9">
						<b>
							<span style="color:brown;">
								{{ (isset($getdataforupdate[0]->mailId)?$getdataforupdate[0]->mailId:"") }}
							</span>
						</b>
					</div>
				</div>
				@else
				@endif
				<div class="col-xs-12 mt10">
					<div class="col-xs-3 clr_blue text-right">
						<label>{{ trans('messages.lbl_mailname') }}<span class="fr ml2 red"> * </span></label>
					</div>
					<div class="col-xs-9">
						{{ Form::text('mailname',(isset($getdataforupdate[0]->mailName)?$getdataforupdate[0]->mailName:""),array(
											'id'=>'mailname',
											'name' => 'mailname',
											'class'=>'box30per form-control',
											'data-label' => trans('messages.lbl_mailname'))) }}
					</div>
				</div>
				<div class="col-xs-12 mt5">
					<div class="col-xs-3 clr_blue text-right">
						<label>{{ trans('messages.lbl_mailtype') }}<span class="fr ml2 red"> * </span></label>
					</div>
					<div class="col-xs-9">
						{{ Form::select('mailtype',[null=>''] + $getmailtypes + ['999'=>'Other'], (isset($getdataforupdate[0]->mailType)?$getdataforupdate[0]->mailType:""),
									array('name' => 'mailtype',
										  'id'=>'mailtype',
										  'onchange' => 'javascript:fndisablecharge(this.value);',
										  'data-label' => trans('messages.lbl_mailtype'),
										  'style' => 'display:inline-block;',
										  'class'=>'pl5 width-auto'))}}
						{{ Form::text('mailother','',array(
											'id'=>'mailother',
											'name' => 'mailother',
											'class'=>'box30per form-control',
											'style' => 'display:none;padding:0px !important;margin:0px !important;',
											'data-label' => trans('messages.lbl_mailother'))) }}
					</div>
				</div>
				<div class="col-xs-12 mt5">
					<div class="col-xs-3 clr_blue text-right">
						<label>{{ trans('messages.lbl_subject') }}<span class="fr ml2 red" style="visibility: hidden;"> * </span></label>
					</div>
					<div class="col-xs-9">
						{{ Form::text('subject',(isset($getdataforupdate[0]->subject)?$getdataforupdate[0]->subject:""),array(
											'id'=>'subject',
											'name' => 'subject',
											'class'=>'box30per form-control',
											'data-label' => trans('messages.lbl_subject'))) }}
					</div>
				</div>
				<div class="col-xs-12 mt5 mb10">
					<div class="col-xs-3 clr_blue text-right">
						<label>{{ trans('messages.lbl_content') }}<span class="fr ml2 red"> * </span></label>
					</div>
					<div class="col-xs-9">
						{{ Form::textarea('content',(isset($getdataforupdate[0]->content)?$getdataforupdate[0]->content:""), 
                        				array('name' => 'content',
                        						'id' => 'content',
                        						'data-label' => trans('messages.lbl_content'),
                              			'class' => 'box45per form-control','size' => '100x14')) }}
					</div>
				</div>
			</fieldset>
			<fieldset style="background-color: #DDF1FA;">
		<div class="form-group">
			<div align="center" class="mt5">
				@if ($request->editflg=="1")
				<button type="submit" class="btn edit btn-warning addeditprocess box100">
							<i class="fa fa-edit" aria-hidden="true"></i> {{ trans('messages.lbl_update') }}
				</button>
				<a onclick="javascript:gotoview('view','{{$request->mainmenu}}');" 
						class="btn btn-danger box120 white">
								<i class="fa fa-times" aria-hidden="true"></i> 
									{{trans('messages.lbl_cancel')}}
				</a>
				@else
				<button type="submit" class="btn btn-success add box100 addeditprocess ml5">
					<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
				</button>
				<a onclick="javascript:gotoindex('index','{{$request->mainmenu}}');" 
						class="btn btn-danger box120 white">
								<i class="fa fa-times" aria-hidden="true"></i> 
									{{trans('messages.lbl_cancel')}}
				</a>
				@endif
			</div>
		</div>
	</fieldset>
		</div>
	{{ Form::close() }}
</article>
</div>
{{ Form::open(array('name'=>'mailaddeditcancel', 'id'=>'mailaddeditcancel', 'url' => 'Mailcontent/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
		{{ Form::hidden('editflg','', array('id' => 'editflg')) }}
	    {{ Form::hidden('emailid',$request->emailid, array('id' => 'emailid')) }}
@endsection