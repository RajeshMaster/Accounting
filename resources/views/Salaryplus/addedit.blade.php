@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/expenses.js') }}
{{ HTML::script('resources/assets/js/salaryplus.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::script('resources/assets/js/lib/lightbox.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::style('resources/assets/css/lib/lightbox.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var dates = '<?php echo date('Y-m-d'); ?>';
	$(document).ready(function() {
		setDatePicker("saldate");
		//this calculates values automatically 
	    calculateSum();

	    $(".txt").on("keydown keyup", function() {
	        calculateSum();
	    });
	});
	function negativeamt(id,amt) {
		var amt = $('#'+id).val();
		amt = $('#'+id).val().replace(/[^0-9]/gi, '');
		//amt = Number(amt.trim().replace(/[, ]+/g, ""));
		if (amt == "") {
			$('#'+id).focus();  
			$('#'+id).val('-');
		} else {
			$('#'+id).focus(); 
			if (amt>0) {
				value1 = amt;
				tot = value1.toLocaleString();
				amount = "-"+tot;
				document.getElementById(""+id).value = amount;
			}
		}
	}
	function calculateSum() {
	    var sum = 0;
	    //iterate through each textboxes and add the values
	    $(".txt").each(function() {
	    	var remnum = Number(this.value.trim().replace(/[, ]+/g, ""));
	        //add only if the value is number
	        if (!isNaN(remnum) && this.value.length != 0) {
	            sum += parseFloat(remnum);
	            // $(this).css("background-color", "#FEFFB0");
	        }
	        else if (this.value.length != 0){
	            // $(this).css("background-color", "red");
	        }
	    });
	    var amount = Math.abs(sum.toFixed(0));
		var value1 = amount;
		var tot = value1.toLocaleString();
		var tott = tot;
		$("#totamt").text(tott);
	}
</script>
<style type="text/css">
	.clr_brown{
		 color: #9C0000 ! important;
	}
	.clr_blue1{
		 color: blue ! important;
	}
	.alertboxalign {
		margin-top: 5px !important;
		margin-bottom: -50px !important;
	}
	.alert {
		display:inline-block !important;
		height:30px !important;
		padding:5px !important;
	}
</style>
<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_10">
		@if(isset($detedit))
		    {{ Form::model($detedit, ['name'=>'addeditsalaryplus', 
									'id'=>'addeditsalaryplus', 
									'url' => 'Salaryplus/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
									'files' => true,
									'method' => 'POST']) }}
	    	{{ Form::hidden('datecheck',$detedit['date'] , array('id' => 'datecheck')) }}
		@else
			{{ Form::open(array('name'=>'addeditsalaryplus', 
						'id'=>'addeditsalaryplus', 
						'url' => 'Salaryplus/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files' => true,
						'method' => 'POST')) }}
		@endif
			{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
			{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	    	{{ Form::hidden('mainmenu',$request->mainmenu, array('id' => 'mainmenu')) }}
	    	{{ Form::hidden('Emp_ID',$request->Emp_ID , array('id' => 'Emp_ID')) }}
	    	{{ Form::hidden('id',$request->id , array('id' => 'id')) }}
	    	{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
			{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
			{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
			{{ Form::hidden('previou_next_year', $request->previou_next_year, 
								array('id' => 'previou_next_year')) }}
			{{ Form::hidden('editcheck', $request->editcheck, array('id' => 'editcheck')) }}
			{{ Form::hidden('firstname',$request->firstname , array('id' => 'firstname')) }}
			{{ Form::hidden('lastname',$request->lastname , array('id' => 'lastname')) }}
			{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
			{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	    	<div class="row hline pm0">
				<div class="col-xs-12">
					<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/salary_1.png') }}">
					<h2 class="pull-left pl5 mt10">
						{{ trans('messages.lbl_salaryplus') }}
					</h2>
					<h2 class="pull-left mt10">・</h2>
					<h2 class="pull-left mt10">
						@if($request->editcheck == 1)
							<span class="red">
								{{ trans('messages.lbl_edit') }}
							</span>
						@else
							<span class="green">
								{{ trans('messages.lbl_register') }}
							</span>
						@endif
					</h2>
				</div>
			</div>
			<div id="errorSectiondisplay" align="center" class="box100per"></div>
			<div>
				<fieldset class="col-xs-12 mt20 ml15" style="width: 98% !important;">
				<legend align="left" 
				style="width: auto !important; background-color: white; border:none !important;float: left; font-size: 15px; position: relative; margin-top: -13px;margin-bottom: 0px !important;margin-left: -5px !important;">
					<b>{{ trans('messages.lbl_empdetails') }}</b></legend>
				<div class="col-xs-12 pm0 pull-right mb10 pl200 pr10 mt10 fwb">
			        {{ trans('messages.lbl_employeeid').':' }}
			          <span class="mr40 ml12" style="color:blue;">
			            {{ $request->Emp_ID }}
			          </span>
			            {{ trans('messages.lbl_empName').':' }}
			          <span style="color:#9C0000;margin-left: 10px">
			          {{ $request->lastname }} {{ $request->firstname }}
			          </span>
			    </div>
				</fieldset>
				@if($request->editcheck != 0)
				<fieldset class="col-xs-12 ml15" style="width: 48% !important;">
				@else
				<fieldset class="col-xs-12 ml15" style="width: 48% !important;">
				@endif
				<legend align="left" 
				style="width: auto !important; background-color: white; border:none !important;float: left; font-size: 15px; position: relative; margin-top: -13px;margin-bottom: 0px !important;margin-left: -5px !important;">
					<b>{{ trans('messages.lbl_basic') }}</b></legend>
					<div class="col-xs-12" style="margin-top: 0px;">
						<div class="col-xs-12 mt10">
							<div class="col-xs-5 text-right clr_blue">
								<label>{{ trans('messages.lbl_saldate') }}</label>@if($request->editcheck != 2)<span class="fr ml2">*</span>@endif
							</div>
							<div class="col-xs-7">
								{{ Form::text('date',null,array('id'=>'date', 
																'name' => 'date',
																'data-label' => trans('messages.lbl_saldate'),
																'class'=>'box37per form-control pl5 saldate')) }}
						<label class="mt10 ml2 fa fa-calendar fa-lg" for="date" aria-hidden="true"></label>
						<a href="javascript:getdate();" class="anchorstyle">
						<img title="Current Date" class="box15" src="{{ URL::asset('resources/assets/images/add_date.png') }}"></a>
							</div>
						</div>
						<div class="col-xs-12 mt5">
							<div class="col-xs-5 text-right clr_blue">
								<label>{{ trans('messages.lbl_basic') }}</label>@if($request->editcheck != 2)<span class="fr ml2">*</span>@endif
							</div>
							<div class="col-xs-7">
								{{ Form::text('Basic',null,array('id'=>'Basic',
										'name' => 'Basic',
										'class'=>'txt Basic ime_mode_disable form-control box37per',
										'maxlength' => '10',
										'style'=>'text-align:right;',
										'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
										'onkeyup'=>'fnMoneyFormat(this.id,"jp");',
										'onkeydown'=>'fnMoneyFormat(this.id,"jp");',
										'data-label' => trans('messages.lbl_basic'))) }} 
							</div>
						</div>
						<div class="col-xs-12 mt5">
							<div class="col-xs-5 text-right clr_blue">
								<label>{{ trans('messages.lbl_House_Rent_allowance') }}</label>@if($request->editcheck != 2)<span class="fr ml2">*</span>@endif
							</div>
							<div class="col-xs-7">
								{{ Form::text('HrAllowance',null,array('id'=>'HrAllowance',
										'name' => 'HrAllowance',
										'class'=>'txt HrAllowance ime_mode_disable form-control box37per',
										'maxlength' => '10',
										'style'=>'text-align:right;',
										'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
										'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
										'data-label' => trans('messages.lbl_HR_allowance'))) }}
							</div>
						</div>
						<div class="col-xs-12 mt5">
							<div class="col-xs-5 text-right clr_blue">
								<label>{{ trans('messages.lbl_OT') }}</label>@if($request->editcheck != 2)<span class="fr ml2">*</span>@endif
							</div>
							<div class="col-xs-7">
								{{ Form::text('OT',null,array('id'=>'OT',
										'name' => 'OT',
										'class'=>'txt OT ime_mode_disable form-control box37per',
										'maxlength' => '10',
										'style'=>'text-align:right;',
										'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
										'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
										'data-label' => trans('messages.lbl_OT'))) }}
							</div>
						</div>
						<div class="col-xs-12 mt5">
							<div class="col-xs-5 text-right clr_blue">
								<label>{{ trans('messages.lbl_leave') }}</label>@if($request->editcheck != 2)<span class="fr ml2"></span>@endif
							</div>
							<div class="col-xs-7">
								{{ Form::text('Leave',(
									isset($detedit['leaveAmount'])) 
									? $detedit['leaveAmount']
									: '',
									array(
									'id'=>'Leave', 
									'name' => 'Leave',
									'maxlength' => '10',
									'onblur' => 'return fnSetZero11(this.id);',
									'onfocus' => 'return fnRemoveZero(this.id);',
									'onclick' => 'return fnRemoveZero(this.id);',
									'data-label' => trans('messages.lbl_leave'),
									'onkeyup'=>'return fnMoneyFormatNegative(this.id,"jp");negativeamt(this.id,this.value)',
									'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
									'class'=>'txt Leave ime_mode_disable form-control box37per',
									'style'=>'text-align:right;color:red;',
									'data-label' => trans('messages.lbl_leave')))
								}}
							</div>
						</div>
						<div class="col-xs-12 mt5 mb10">
							<div class="col-xs-5 text-right clr_blue">
								<label>{{ trans('messages.lbl_bonus') }}</label>@if($request->editcheck != 2)<span class="fr ml2"></span>@endif
							</div>
							<div class="col-xs-7">
								{{ Form::text('Bonus',null,
												array('id'=>'Bonus', 
												'name' => 'Bonus',
												'class'=>'txt ime_mode_disable form-control box37per',
												'maxlength' => '10',
												'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
												'style'=>'text-align:right;',
												'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
												'data-label' => trans('messages.lbl_bonus'))) }}
							</div>
						</div>
					</div>
				</fieldset>
				<fieldset class="col-xs-12 ml15" style="width: 48% !important;">
				<legend align="left" 
				style="width: auto !important; background-color: white; border:none !important;float: left; font-size: 15px; position: relative; margin-top: -13px;margin-bottom: 0px !important;margin-left: -5px !important;">
					<b>{{ trans('messages.lbl_esi_it') }}</b></legend>
					<div class="col-xs-12" style="margin-top: 0px;">
						<div class="col-xs-12 mt10">
							<div class="col-xs-5 text-right clr_blue">
								<label>{{ trans('messages.lbl_esi') }}</label>@if($request->editcheck != 2)<span class="fr ml2">*</span>@endif
							</div>
							<div class="col-xs-7">
								{{ Form::text('ESI',null,array('id'=>'ESI',
										'name' => 'ESI',
										'style'=>'text-align:right;color:red;',
										'class'=>'txt ESI ime_mode_disable form-control box37per',
										'maxlength' => '10',
										'onblur' => 'return fnSetZero11(this.id);',
										'onfocus' => 'return fnRemoveZero(this.id);',
										'onclick' => 'return fnRemoveZero(this.id);',
										'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
										'onkeyup'=>'return fnMoneyFormatNegative(this.id,"jp");negativeamt(this.id,this.value)',
										'data-label' => trans('messages.lbl_esi'))) }}
							</div>
							</div>
						<div class="col-xs-12 mt5 mb10">
							<div class="col-xs-5 text-right clr_blue">
								<label>{{ trans('messages.lbl_it') }}</label>@if($request->editcheck != 2)<span class="fr ml2">*</span>@endif
							</div>
							<div class="col-xs-7">
								{{ Form::text('IT',null,
											array('id'=>'IT', 
											'name' => 'IT',
											'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
											'maxlength' => '10',
											'onblur' => 'return fnSetZero11(this.id);',
											'onfocus' => 'return fnRemoveZero(this.id);',
											'onclick' => 'return fnRemoveZero(this.id);',
											'onkeyup'=>'return fnMoneyFormatNegative(this.id,"jp");negativeamt(this.id,this.value)',
											'style'=>'text-align:right;color:red;',
											'class'=>'txt IT ime_mode_disable form-control box37per',
											'data-label' => trans('messages.lbl_it'))) }}
							</div>
						</div>
					</div>
				</fieldset>
				<fieldset class="col-xs-12 mt20 ml15" style="width: 48% !important; ">
				<legend align="left" 
				style="width: auto !important; background-color: white; border:none !important;float: left; font-size: 15px; position: relative; margin-top: -13px;margin-bottom: 0px !important;margin-left: -5px !important;">
					<b>{{ trans('messages.lbl_travel_exp') }}</b></legend>
					<div class="col-xs-12" style="margin-top: 0px;">
						<div class="col-xs-12 mt10">
							<div class="col-xs-5 text-right clr_blue">
								<label>{{ trans('messages.lbl_travel') }}</label>@if($request->editcheck != 2)<span class="fr ml2">*</span>@endif
							</div>
							<div class="col-xs-7">
								{{ Form::text('Travel',null,array('id'=>'Travel',
										'name' => 'Travel',
										'style'=>'text-align:right;',
										'class'=>'txt ime_mode_disable form-control box37per',
										'maxlength' => '10',
										'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
										'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
										'data-label' => trans('messages.lbl_travel'))) }}
							</div>
							</div>
						<div class="col-xs-12 mt5 mb10">
							<div class="col-xs-5 text-right clr_blue">
								<label>{{ trans('messages.lbl_monthlytravel') }}</label>@if($request->editcheck != 2)<span class="fr ml2">*</span>@endif
							</div>
							<div class="col-xs-7">
								{{ Form::text('MonthlyTravel',null,
											array('id'=>'MonthlyTravel', 
											'name' => 'MonthlyTravel',
											'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
											'maxlength' => '10',
											'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
											'style'=>'text-align:right;',
											'class'=>'txt ime_mode_disable form-control box37per',
											'data-label' => trans('messages.lbl_monthlytravel'))) }}
							</div>
						</div>
					</div>
				</fieldset>
			</div>
			<div class="col-xs-12  ml140 ">
				<div class="col-xs-7 mb5 text-right">
					<label style="font-size: 120%;">{{ trans('messages.lbl_totamt') }} <span>:</span></label>
				</div>
				<div class="col-xs-5 ">
					<span id="totamt" class="fwb clr_blue1" style="font-size: 120%">
					</span>
					<span class="fwb" style="font-size: 120%;">円</span>
				</div>
			</div>					
			<fieldset class="col-xs-12 mt1" style="background-color: #DDF1FA;width: 98% !important;">
				<div class="form-group">
					<div align="center" class="mt5">
						@if($request->editcheck == 1)
							<button type="submit" class="btn btn-warning add box100 addeditprocess ml5">
								<i class="fa fa-edit" aria-hidden="true"></i> {{ trans('messages.lbl_update') }}
							</button>
						@else
							<button type="submit" class="btn btn-success add box100 addeditprocess ml5">
								<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
							</button>
						@endif
						<a onclick="javascript:gotoindex('index','{{$request->mainmenu}}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
						</a>
					</div>
				</div>
			</fieldset>
	    {{ Form::close() }}
	    {{ Form::open(array('name'=>'salaryplusaddeditcancel', 'id'=>'salaryplusaddeditcancel', 'url' => 'Salaryplus/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
			{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
			{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	    	{{ Form::hidden('mainmenu',$request->mainmenu, array('id' => 'mainmenu')) }}
	    	{{ Form::hidden('Emp_ID',$request->Emp_ID , array('id' => 'Emp_ID')) }}
	    	{{ Form::hidden('id',$request->id , array('id' => 'id')) }}
	    	{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
			{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
			{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
			{{ Form::hidden('previou_next_year', $request->previou_next_year, 
								array('id' => 'previou_next_year')) }}
			{{ Form::hidden('editcheck', $request->editcheck, array('id' => 'editcheck')) }}
			{{ Form::hidden('firstname',$request->firstname , array('id' => 'firstname')) }}
			{{ Form::hidden('lastname',$request->lastname , array('id' => 'lastname')) }}
			{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
			{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::close() }}
	</article>
</div>
@endsection