@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/meeting.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
<script type="text/javascript">
	var datetime = '@php echo date('Ymdhis') @endphp';
	var mainmenu = '@php echo $request->mainmenu @endphp';
	$(document).ready(function() {
		setDatePicker("date");
	});
	$(document).ready(function() {
		$('salary').blur(function() {
		$('.salary').formatCurrency();
		});
	});
</script>
<div class="CMN_display_block box100per" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper " data-category="customer customer_sub_3">
	@if($request->editflg == 'edit')
		{{ Form::model($viewdetails, array('name'=>'meetingdetailsviewfrm', 'id'=>'meetingdetailsviewfrm', 'files'=>true,'type'=>'file', 'method' => 'POST','class'=>'form-horizontal','url' => 'MeetingDetails/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis') ) ) }}
	@else
		{{ Form::open(array('name'=>'meetingdetailsviewfrm',
							'id'=>'meetingdetailsviewfrm',
							'url' => 'MeetingDetails/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
							'files'=>true,
							'method' => 'POST')) }}
	@endif
		{{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
		{{ Form::hidden('editflg', $request->editflg, array('id' => 'editflg')) }}
		{{ Form::hidden('viewid', $request->viewid , array('id' => 'viewid')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('byajax', 0 , array('id' => 'byajax')) }}
	<!-- Start Heading -->
	<div class="row hline">
	<div class="col-xs-12">
		<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/meetingdet.png') }}">
		@if($request->editflg == 'edit')
		<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_meetingdet') }}<span>・</span><span class="green">{{ trans('messages.lbl_edit') }}</span></h2>
		@elseif($request->editflg == 'copy')
		<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_meetingdet') }}<span>・</span><span class="green">{{ trans('messages.lbl_copy') }}</span></h2>
		@else
		<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_meetingdet') }}<span>・</span><span class="green">{{ trans('messages.lbl_register') }}</span></h2>
		@endif
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
	<div id="errorSectiondisplay" align="center" class="box100per"></div>
	<div class="pl5 pr5" >
		<div class="col-xs-12 pl5 pr5">
		<fieldset>
		<div class="box60per CMN_display_block">
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue box40per mt5">
					<label>{{ trans('messages.lbl_Date') }}</label>
					<span class="fr ml2 red"> * </span>
				</div>
				<div>
					<span class="CMN_display_block box14per ml2 mt5 vat">
					{{ Form::text('date', (isset($viewdetails[0]->date)&&$request->editflg!='copy'?$viewdetails[0]->date:""),array('id'=>'date', 'name' => 'date','data-label' => trans('messages.lbl_Date'),'class'=>'box100per form-control pl5 date'
					,'onkeypress' => 'return isNumberKey(event);',
					'style' => 'ime-mode:disabled;')) }}
					</span>
					<label class="mt15 ml2 fa fa-calendar fa-lg CMN_display_block vat CMN_display_block" 
					for="date" aria-hidden="true">
					</label>
					<span class="CMN_display_block box13per mt14">
					<a href="javascript:dateadd('{{ date('Y-m-d') }}');" 
					title="{{ trans('messages.lbl_currentdate') }}">
					<img class="pull-left box18" src="{{ URL::asset('resources/assets/images/add_date.png') }}">
					</a>
					</span>
				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_startTime') }} ~ {{ trans('messages.lbl_endTime') }}
					</label>
					<span class="fr ml2 red"> * </span>
				</div>
				<div>
					<span  class="CMN_display_block box9per ml2">
					{{ Form::text('startTime',(isset($viewdetails[0]->startTime)&&$request->editflg!='copy'?$viewdetails[0]->startTime:""),array('id'=>'startTime', 'name' => 'startTime','data-label' => trans('messages.lbl_startTime'),'class'=>'box50 form-control pl5',
					'onkeypress'=>'return decimalvalues(this.value)','maxlength'=>'5',
					'style' => 'ime-mode:disabled;')) }}
					</span>
					~
					<span  class="CMN_display_block box9per ml2">
					{{ Form::text('endTime',(isset($viewdetails[0]->endTime)&&$request->editflg!='copy'?$viewdetails[0]->endTime:""),array('id'=>'endTime', 'name' => 'endTime','data-label' => trans('messages.lbl_endTime'),'class'=>'box50 form-control pl5',
					'onkeypress'=>'return decimalvalue(this.value)','maxlength'=>'5',
					'style' => 'ime-mode:disabled;')) }}
					</span>
				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_cusname') }}</label>
					<span class="fr ml2 red"> * </span>
				</div>
				<div>
					<span class="CMN_display_block box34per ml2"> 
					{{ Form::select('customerId',[null=>'']+$customerarray,(isset($viewdetails[0]->customerId)?$viewdetails[0]->customerId:""), 
								array('name' => 'customerId',
									  'id'=>'customerId',
									  'onchange'=>'fnGetbranchDetail();',
									  'data-label' => trans('messages.lbl_cusname'),
									  'class'=>'box100per pl5'))}}
	                </span>
	                <div id="customerdiv" class="CMN_display_block box20per ml1">
						 <button data-toggle="modal" type="button" class="btn btn-success add box128" style="height:30px;width: 140px; margin-top: -3px;" 
						 onclick="return newcustomerpopup('{{ $request->mainmenu }}','{{ $request->editflg }}');">
						 	<i class="fa fa-plus vat">
						 		{{ trans('messages.lbl_newCustomer') }}
						 	</i>
						 </button>
	                </div>
				</div>
				<script type="text/javascript">
					fnGetbranchDetail();
				</script>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue box40per pr20">
					<label>{{ trans('messages.lbl_branchname') }}</label>
					<span class="fr ml5"> </span>
				</div>
				<div>
					<span class="CMN_display_block box25per ml2">
						{{ Form::select('branchId',[null=>''],(isset($viewdetails[0]->branchId)?$viewdetails[0]->branchId:""), 
								array('name' => 'branchId',
									  'id'=>'branchId',
									  'data-label' => trans('messages.lbl_branchname'),
									  'class'=>'box100per pl5'))}}
		                {{ Form::hidden('hidebranchname', (isset($viewdetails[0]->branchId)) ? $viewdetails[0]->branchId : '', array('id' => 'hidebranchname')) }}
	                </span>
				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_personName') }}</label>
					<span class="fr ml2 red"> * </span>
				</div>
				<div>
					<span  class="CMN_display_block box33per ml2">
					{{ Form::text('personName',
					(isset($viewdetails[0]->personName)?$viewdetails[0]->personName:""),array('id'=>'personName', 'name' => 'personName','data-label' => trans('messages.lbl_personName'),'class'=>'box100per form-control pl5',
					'style' => 'ime-mode:disabled;','maxlength'=>'30')) }}
					</span>
				</div>
			</div>
			<div class="col-xs-12 mt5 mb10">
				<div class="col-xs-3 text-right clr_blue box40per pr20">
					<label>{{ trans('messages.lbl_remarks') }}</label>
					<span class="fr ml5"> </span>
				</div>
				<div>
					<span  class="CMN_display_block box23per ml2">
					{{ Form::textarea('Remarks', 
					(isset($viewdetails[0]->Remarks)?$viewdetails[0]->Remarks:""),array('id'=>'Remarks', 
							'name' => 'Remarks',
							'data-label' => trans('messages.lbl_remarks'),
							'style' => 'height:120px;width:260px;ime-mode:disabled;')) }}
					</span>
					</div>
				</div>
				</div>
		</fieldset>
		<fieldset class="bg_footer_clr">
		<div class="form-group mt15">
			<div align="center" class="mt5">
				@if($request->editflg == 'edit')
				<button type="submit" class="btn btn-warning add box100 addeditprocess" >
					<i class="fa fa-edit ml7"></i>
					{{ trans('messages.lbl_update') }}
				</button>
				@else
				<button type="submit" class="btn btn-success add box100 addeditprocess" >
					<i class="fa fa-plus ml7"></i>
					{{ trans('messages.lbl_register') }}
				</button>
				@endif
				@if($request->editflg == 'edit'||$request->editflg == 'copy')
				<a onclick="javascript:gotoindexpage('view','{{ $request->mainmenu }}');" 
				id="cancelclk" class="btn btn-danger box100 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				</a>
				@else
				<a onclick="javascript:gotoindexpage('index','{{ $request->mainmenu }}');" class="btn btn-danger box100 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				</a>
				@endif
			</div>
		</div>
		</fieldset>
		</div>
	</div>
	{{ Form::close() }}
	{{ Form::open(array('name'=>'frmmeetingaddeditcancel', 'id'=>'frmmeetingaddeditcancel', 'url' => 'MeetingDetails/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
		{{ Form::hidden('viewid', '' , array('id' => 'viewid')) }}
		{{ Form::close() }}
</article>
</div>
<div class="CMN_display_block pb10"></div>
<div id="meetingnewRegpopup" class="modal fade">
    <div id="login-overlay">
        <div class="modal-content">
            <!-- Popup will be loaded here -->
        </div>
    </div>
</div>
@endsection