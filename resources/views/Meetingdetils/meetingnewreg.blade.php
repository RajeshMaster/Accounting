{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::script('resources/assets/js/meetingpopup.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::style('resources/assets/css/switch.css') }}
<style>
.modal {
    position: fixed;
    top: 50% !important;
    left: 50%;
    transform: translate(-50%, -50%);
}
.popF_color {
      background-color: #ccf2ff;
      margin-left: 0px;
      width: 100%;
      border-radius: 0px;
    }
</style>
<script type="text/javascript">
	    var datetime = '@php echo date('Ymdhis') @endphp';
</script>
{{ Form::open(array('name'=>'customer_register', 'id'=>'customer_register', 
                      'url' => 'MeetingDetails/newcustomerregpopup?mainmenu='.$request->mainmenu.
                      '&time='.date('YmdHis'),
                      'method' => 'POST')) }}
    {{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
            <div class="modal-content">
                <div class="modal-header">
                	<div class="fwb">
					<button type="button" data-dismiss="modal" class="close red" aria-hidden="true">&#10006;
					</button>
					<h2 style="font-size:30px;" class="modal-title custom_align">{{ trans('messages.lbl_newCustomerReg') }}</h2>
					<hr></hr>
				</div>
		<fieldset style="width:100%;" id="outerdiv">
			<div id="errorForexistCus" align="center" class="box100per pt5"></div>
			<div class="box100per CMN_display_block">
					<div class="col-xs-6 tar mt10">
						<label>{{ trans('messages.lbl_custname(JP & Eng)') }}</label>
						<span class="fr ml2 red"> * </span>
					</div>
					<div class="col-xs-6 tal mt10">
						{{ Form::text('customer_name', null,array('id'=>'customer_name', 'name' => 'customer_name','data-label' =>trans('messages.lbl_custname'),
						'class'=>'box60per form-control pl5 alphaonly',
						'onkeypress' =>'return blockSpecialChar(event);fnCancel_check()',
						'style' => 'ime-mode:active;',
						'maxlength'=>'30')) }}
					</div>
					<div class="col-xs-6 tar mt10">
						<label>{{ trans('messages.lbl_custname(kana)') }}</label>
						<span class="fr ml2 red"> * </span>
					</div>	
					<div class="col-xs-6 tal mt10">
						{{ Form::text('txt_romaji', null,array('id'=>'txt_romaji', 'name' => 'txt_romaji','data-label' => trans('messages.lbl_custname(kana)'),
						'class'=>'box60per form-control pl5',
						'onkeypress' =>'return blockSpecialChar(event);fnCancel_check()',
						'style' => 'ime-mode:active;','maxlength'=>'30')) }}
					</div>
					<div class="col-xs-6 tar mt10">
						<label>{{ trans('messages.lbl_branchname') }}</label>
						<span class="fr ml2 red">*</span>
					</div>
					<div class="col-xs-6 tal mt10">
						{{ Form::text('branch_name', null,array('id'=>'branch_name', 'name' => 'branch_name','data-label' => trans('messages.lbl_branchname'),
						'class'=>'box60per form-control pl5',
						'onkeypress' =>'return blockSpecialChar(event);fnCancel_check()',
						'style' => 'ime-mode:active;','maxlength'=>'30')) }}
					</div>
			</div>		
		</fieldset>
		<div class="modal-footer popF_color mt10">
			<center>
				<button class="btn btn-success add box100 ml5 registerprocess pageload">
					<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
				</button>
				<a onclick="javascript:divpopupclose();" class="btn btn-danger box120 white">
				<i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				</a>
			</center>
		</div>
</div>
</div>
{{ Form::close() }}