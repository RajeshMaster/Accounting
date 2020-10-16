@extends('layouts.app')
@section('content')
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
	$(document).ready(function() {
		if($('#editflg').val()=="2"){
			$('#reghead').show();
			$('#regbtn').show();
			$('#regcancel').show();
		} else {
			$('#edithead').show();
			$('#updatebtn').show();
			$('#updatecancel').show();
		}
	});
</script>
{{ HTML::script('resources/assets/js/mailsignature.js') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="mail" class="DEC_flex_wrapper " data-category="mail mail_sub_3">
	{{ Form::open(array('name'=>'frmaddedit', 
						'id'=>'frmaddedit', 
						'url' => 'Mailsignature/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('useridhdn',$request->useridhdn , array('id' => 'useridhdn')) }}
		{{ Form::hidden('editflg',$request->editflg, array('id' => 'editflg')) }}
		{{ Form::hidden('updateprocess',$request->updateprocess, array('id' => 'updateprocess')) }}
	    {{ Form::hidden('id',$request->id, array('id' => 'id')) }}
		<!-- Start Heading -->
		@if(Session::get('userclassification') == "4")
			<div class="row hline">
		@else
			<div class="row hline" style="width:123%">
		@endif
			<div class="col-xs-12">
				<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/signature.png') }}">
				<h2 class="pull-left pl5 mt10">{{ trans('messages.lbl_mailsignature') }}</h2>
				<h2 class="pull-left mt10">・</h2>

				<h2 class="pull-left mt10"><span id="reghead" class="green" style="display: none;">
				{{ trans('messages.lbl_register') }}</span>
				<span id="edithead" style="display: none;" class="red">
					{{ trans('messages.lbl_edit') }}</h2></span>
			</div>
		</div>
			<div id="errorSectiondisplay" align="center" class="box100per"></div>
		@if(Session::get('userclassification') == "4")
			<div class="ml10 mr10 box99per">
		@else
			<div class="ml10 mr10" style="width:121%">
		@endif
			<fieldset>
				@if(Session::get('userclassification') == "4")
				<div class="col-xs-12 mt10">
					<div class="col-xs-3 text-right clr_blue mr5">
						<label>{{ trans('messages.lbl_usernamesign') }}<span class="fr ml2 red"> * </span></label>
					</div>
					<div class="box25per fll pl10">
						{{ Form::hidden('userid','',array('id'=>'userid')) }}
						{{ Form::text('txtuserid',(isset($getname)?$getname:""),array('id'=>'txtuserid', 'name' => 'txtuserid',
						'class'=>'form-control',
						'readonly','readonly','data-label' => trans('messages.lbl_usernamesign'))) }}
					</div>
					<div class="col-xs-3 mr25">
						<button type="button" id="bnkpopup" class="btn btn-success box75 pt3 h30" 
						style ="color:white;background-color: green;cursor: pointer;" 
						onclick="return popupenable('{{ $request->mainmenu }}');">{{ trans('messages.lbl_Browse') }}
						</button>
					</div>
				</div>
				@endif
				<div class="col-xs-12 mt5 mb10">
					<div class="col-xs-3 clr_blue text-right">
						<label>{{ trans('messages.lbl_mailsignature') }}<span class="fr ml2 red"> * </span></label>
					</div>
					<div class="col-xs-9">
						{{ Form::textarea('content',(isset($getdataforupdate[0]->content)?$getdataforupdate[0]->content:""), 
													array('name' => 'content',
													'id' => 'content',
													'data-label' => trans('messages.lbl_mailsignature'),
													'class' => 'box60per form-control','size' => '100x9')) }}
					</div>
				</div>
			</fieldset>
			<fieldset style="background-color: #DDF1FA;">
			<div class="form-group">
				<div align="center" class="mt5">
				<button type="submit" class="btn edit btn-warning addeditprocess box100" id="updatebtn" style="display: none;">
					<i class="fa fa-edit" aria-hidden="true"></i> {{ trans('messages.lbl_update') }}
				</button>
				<a onclick="javascript:gotoindex('1','{{$request->mainmenu}}','{{$request->updateprocess}}','{{$request->editflg}}');" 
						class="btn btn-danger box120 white" id="updatecancel" style="display: none;">
								<i class="fa fa-times" aria-hidden="true"></i> 
									{{trans('messages.lbl_cancel')}}
				</a>
				<button type="submit" class="btn btn-success add box100 addeditprocess ml5" id="regbtn" style="display: none;">
					<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
				</button>
				@if(Session::get('userclassification') == "4")
				<a onclick="javascript:gotoindex('2','{{$request->mainmenu}}');" 
						class="btn btn-danger box120 white" id="regcancel" style="display: none;">
								<i class="fa fa-times" aria-hidden="true"></i> 
									{{trans('messages.lbl_cancel')}}
				</a>
				@else
				<a onclick="javascript:gotoviewscrn('2','{{$request->mainmenu}}');" 
						class="btn btn-danger box120 white" id="regcancel" style="display: none;">
								<i class="fa fa-times" aria-hidden="true"></i> 
									{{trans('messages.lbl_cancel')}}
				</a>
				@endif
				</div>
			</div>
			</fieldset>
		</div>
	{{ Form::close() }}
	{{ Form::open(array('name'=>'mailsignaddeditcancel', 'id'=>'mailsignaddeditcancel', 'url' => 'Mailsignature/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
	{{ Form::hidden('editflg','', array('id' => 'editflg')) }}
	{{ Form::hidden('id',$request->id, array('id' => 'id')) }}
	{{ Form::hidden('signid',$request->signid, array('id' => 'signid')) }}
	{{ Form::close() }}
	<div id="mailsignaturepopup" class="modal fade">
		<div id="login-overlay">
			<div class="modal-content">
			<!-- Popup will be loaded here -->
			</div>
		</div>
	</div>
</article>
</div>
@endsection