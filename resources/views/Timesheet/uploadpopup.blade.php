{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::style('resources/assets/css/switch.css') }}
{{ HTML::script('resources/assets/js/timesheet.js') }}

<style>
.modal {
      position: fixed;
      top: 50% !important;
      left: 50%;
      transform: translate(-50%, -50%);
   }
</style>
<body>
{{ Form::open(array('name'=>'uploadpopup', 'id'=>'uploadpopup',
                      'url' => 'Timesheet/uploadprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
                      'files'=>true,
                      'method' => 'POST')) }}
    {{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
    {{ Form::hidden('empid', $request->empid , array('id' => 'empid')) }}
	{{ Form::hidden('selMonth', $mon , array('id' => 'selMonth')) }}
	{{ Form::hidden('selYear', $yr , array('id' => 'selYear')) }}

<article id="staff" class="DEC_flex_wrapper " data-category="staff staff_sub_3">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-md-2 pull-right mt10">
					<button type="button" data-dismiss="modal" class="close" style="color: red !important;" aria-hidden="true">&#10006;</button>
				</div>
				<div class="col-md-8 mt10" style="padding-left: 4px;">
					<h2 style="font-size:30px;" class="modal-title custom_align">
					{{ trans('messages.lbl_timesheetfileupload') }}</h2>
				</div>
			</div>
		<div class="modal-body">	
		<div class="box98per mt15" style="padding-left: 30px;margin-top: -25px">
		<fieldset style="width:98%;">
			<div class = "col-xs-12 ml20 pt20">
				<div class = "col-xs-2 pb20 pm0">{{ trans('messages.lbl_uploadfile') }}
				</div>
				<div class = "col-xs-9 pb20 ml40">
				{{ Form::file('xlfile',array('id'=>'xlfile','name' => 'xlfile',
												'data-label' => trans('messages.lbl_image'),
												'accept' =>'.xls',
												'class'=>'box60per')) }}
				</div>
			</div>
		</fieldset>
		</div>
	<div class="modal-footer bg-info" style="margin-top:-3px; width: 100%;">
		<center>
			<button type="button" onclick="return fnexcelupload('{{ $request->mainmenu }}',
			'{{ $request->empid }}','{{ $yr }}','{{ $mon }}');" name ="upload" id="upload" 
				class="btn fa fa-edit btn-warning btn box15per addcopyprocess"> {{ trans('messages.lbl_upload') }} </button>
			<button type="button" data-dismiss="modal" class="btn btn-danger fa fa-times CMN_display_block box15per button" name ="cancel" id="cancel" > {{ trans('messages.lbl_cancel') }} </button>
		</center>
	</div>
	</div>
	</div>
</article>
{{ Form::close() }}
</body>


