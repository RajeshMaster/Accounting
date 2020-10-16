@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/salaryplus.js') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
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
	.mln15 {
		margin-left: -20px !important;
	}
	.mln28 {
		margin-left: -28px !important;
	}
	.width {
		width: 19% !important;
		float: left;
		position: relative;
		min-height: 1px;
		padding-right: 15px;
		padding-left: 15px;
	}
	.width1 {
		width: 71% !important;
		float: left;
		position: relative;
		min-height: 1px;
		padding-right: 15px;
		padding-left: 19px;
	}
</style>
<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_10">
		{{ Form::open(array('name'=>'addeditsalaryplus', 
						'id'=>'addeditsalaryplus', 
						'url' => 'Salaryplus/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files' => true,
						'method' => 'POST')) }}
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
						<span class="blue">
							{{ trans('messages.lbl_view') }}
						</span>
					</h2>
				</div>
			</div>
			<div class="col-xs-12 pt5">
				<!-- Session msg -->
				@if(Session::has('success'))
					<div align="center" class="alertboxalign" role="alert">
						<p class="alert {{ Session::get('alert', Session::get('type') ) }}">
			            	{{ Session::get('success') }}
			          	</p>
					</div>
				@endif
				<!-- Session msg -->
					<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
						<a href="javascript:gotoindexsalaryplus('{{ $request->mainmenu }}');" class="btn btn-info box80">
							<span class="fa fa-arrow-left"></span>
							{{ trans('messages.lbl_back') }}
						</a>
						<a href="javascript:fngotoedit('{{ $request->mainmenu }}');" class="pageload btn btn-warning box100"><span class="fa fa-pencil"></span> {{ trans('messages.lbl_edit') }}</a>
					</div>
			</div>
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
			          <span class="mr40 ml12" style="color:#9C0000;margin-left: 10px">
			         	 {{ $request->lastname }} {{ $request->firstname }}
			          </span>
			          	 {{ trans('messages.lbl_saldate').':' }}
			          <span class="mr40 ml12" style="margin-left: 10px">
			       		 {{ $detedit['date'] }}
			          </span>
			    </div>
				</fieldset>
				<fieldset class="col-xs-12 mt10 ml15" style="width: 48% !important;">
				<legend align="left" 
				style="width: auto !important; background-color: white; border:none !important;float: left; font-size: 15px; position: relative; margin-top: -13px;margin-bottom: 0px !important;margin-left: -5px !important;">
					<b>{{ trans('messages.lbl_basic') }}</b></legend>
					<div class="col-xs-12" style="margin-top: 0px;">
						
						<div class="col-xs-12 mt10">
							<div class="col-xs-7 text-right clr_blue">
								<label>{{ trans('messages.lbl_basic') }}</label>
							</div>
							<div class="col-xs-5">
								<span class="col-xs-6 text-right">
									{{ $detedit['Basic'] }}
								</span>
								<span class="pm0 col-xs-2">
									円
								</span>
							</div>
						</div>
						<div class="col-xs-12 mt9">
							<div class="col-xs-7 text-right clr_blue">
								<label>{{ trans('messages.lbl_House_Rent_allowance') }}</label>
							</div>
							<div class="col-xs-5">
								<span class="col-xs-6 text-right">
									{{ $detedit['HrAllowance'] }}
								</span>
								<span class="pm0 col-xs-2">
									円
								</span>
							</div>
						</div>
						<div class="col-xs-12 mt9">
							<div class="col-xs-7 text-right clr_blue">
								<label>{{ trans('messages.lbl_OT') }}</label>
							</div>
							<div class="col-xs-5">
								<span class="col-xs-6 text-right">
									{{ $detedit['OT'] }}
								</span>
								<span class="pm0 col-xs-2">
									円
								</span>
							</div>
						</div>
						<div class="col-xs-12 mt9">
							<div class="col-xs-7 text-right clr_blue">
								<label>{{ trans('messages.lbl_leave') }}</label>
							</div>
							<div class="col-xs-5">
								<span class="col-xs-6 text-right red">
									@if($detedit['leaveAmount'] == '-0')
										0
									@else
										-{{ $detedit['leaveAmount'] }}
									@endif
								</span>
								<span class="pm0 col-xs-2">
									円
								</span>
							</div>
						</div>
						<div class="col-xs-12 mt9 mb10">
							<div class="col-xs-7 text-right clr_blue">
								<label>{{ trans('messages.lbl_bonus') }}</label>
							</div>
							<div class="col-xs-5">
								<span class="col-xs-6 text-right">
									{{ $detedit['Bonus'] }}
								</span>
								<span class="pm0 col-xs-2">
									円
								</span>
							</div>
						</div>
					</div>
				</fieldset>
				<fieldset class="col-xs-12 mt10 ml15" style="width: 48% !important;">
				<legend align="left" 
				style="width: auto !important; background-color: white; border:none !important;float: left; font-size: 15px; position: relative; margin-top: -13px;margin-bottom: 0px !important;margin-left: -5px !important;">
					<b>{{ trans('messages.lbl_esi_it') }}</b></legend>
					<div class="col-xs-12" style="margin-top: 0px;">
						<div class="col-xs-12 mt10">
							<div class="col-xs-6 text-right clr_blue">
								<label>{{ trans('messages.lbl_esi') }}</label>
							</div>
							<div class="col-xs-6">
								<span class="col-xs-6 text-right red">
									@if($detedit['ESI'] == '-0')
										0
									@else
										-{{ $detedit['ESI'] }}
									@endif
								</span>
								<span class="pm0 col-xs-2">
									円
								</span>
							</div>
							</div>
						<div class="col-xs-12 mt5 mb10">
							<div class="col-xs-6 text-right clr_blue">
								<label>{{ trans('messages.lbl_it') }}</label>
							</div>
							<div class="col-xs-6">
								<span class="col-xs-6 text-right red">
									@if($detedit['IT'] == '-0')
										0
									@else
										-{{ $detedit['IT'] }}
									@endif
								</span>
								<span class="pm0 col-xs-2">
									円
								</span>
							</div>
						</div>
					</div>
				</fieldset>
				<fieldset class="col-xs-12 mt1 ml15" style="width: 48% !important;">
				<legend align="left" 
				style="width: auto !important; background-color: white; border:none !important;float: left; font-size: 15px; position: relative; margin-top: -13px;margin-bottom: 0px !important;margin-left: -5px !important;">
					<b>{{ trans('messages.lbl_travel_exp') }}</b></legend>
					<div class="col-xs-12" style="margin-top: 5px;">
						<div class="col-xs-12 mt10">
							<div class="col-xs-6 text-right clr_blue">
								<label>{{ trans('messages.lbl_travel') }}</label>
							</div>
							<div class="col-xs-6">
								<span class="col-xs-6 text-right">
									{{ $detedit['Travel'] }}
								</span>
								<span class="pm0 col-xs-2">
									円
								</span>
							</div>
							</div>
						<div class="col-xs-12 mt5 mb10">
							<div class="col-xs-6 text-right clr_blue">
								<label>{{ trans('messages.lbl_monthlytravel') }}</label>
							</div>
							<div class="col-xs-6">
								<span class="col-xs-6 text-right">
									{{ $detedit['MonthlyTravel'] }}
								</span>
								<span class="pm0 col-xs-2">
									円
								</span>
							</div>
						</div>
					</div>
				</fieldset>
				<div class="col-xs-12 mt5 mb10 ml140">
				<div class="col-xs-7 mb10 text-right pl50">
						<label style="font-size: 130%;">{{ trans('messages.lbl_totamt') }}</label>
					</div>
					<div class="col-xs-5 mb10 pl100">
						<span class=" text-right mln28 ">
							<span class="fwb clr_blue1" style="color: blue;font-size: 130%;">{{ $total->Total }}</span> <span class="fwb" style="font-size: 130%;">円</span>
						</span>
					</div>
				</div>
			</div>
	    {{ Form::close() }}
	</article>
</div>
@endsection