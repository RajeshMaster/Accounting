@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/salarycalc.js') }}
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
	<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_2">
		{{ Form::open(array('name'=>'addeditsalarycalc', 
						'id'=>'addeditsalarycalc', 
						'url' => 'salarycalc/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
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
			{{ Form::hidden('get_prev_yr', $request->get_prev_yr , array('id' => 'get_prev_yr')) }}
			{{ Form::hidden('tblchg', $request->tblchg , array('id' => 'tblchg')) }}
	    	<div class="row hline pm0">
				<div class="col-xs-12">
					<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/salary_1.png') }}">
					<h2 class="pull-left pl5 mt10">
						{{ trans('messages.lbl_salary_calc') }}
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
						<a href="javascript:gotoindexsalarycalc('{{ $request->mainmenu }}');" class="btn btn-info box80">
							<span class="fa fa-arrow-left"></span>
							{{ trans('messages.lbl_back') }}
						</a>
					</div>
			</div>
			<?php
					$arr1 = array();
					$arr2 = array();
					$arr3 = array();
					$arr4 = array();
					$sal_arr = array();
					$ded_arr = array();
					$sal_arr_name = array();
					$ded_arr_name = array();
					$val1 = '';
					$val2 = '';
					// For Salary Details
					if ($detedit->Salary != '') {
						$Salary = explode('##', mb_substr($detedit->Salary, 0, -2));
						foreach ($Salary as $key => $value) {
							$sal_final = explode('$', $value);
							$arr1[$key] = $sal_final[0];
							$arr2[$sal_final[0]] = $sal_final[1];
						}
					}
					if(count($salary_det) != "") {
			    		foreach ($salary_det as $key1 => $value1) {
			    			$sal_arr[$value1->Salarayid] = $value1->Salarayid;
			    			$sal_arr_name[$value1->Salarayid] = $value1->Name;
			    		}
					}
					$salresult_a=array_intersect($sal_arr,$arr1);
					$salresult_b=array_diff($sal_arr,$arr1);
					$salresult = array_merge($salresult_a,$salresult_b);
					sort($salresult);
					
					// For Deduction Details
					if ($detedit->Deduction != '') {
						$Deduction = explode('##', mb_substr($detedit->Deduction, 0, -2));
						foreach ($Deduction as $key => $value) {
							$ded_final = explode('$', $value);
							$arr3[$key] = $ded_final[0];
							$arr4[$ded_final[0]] = $ded_final[1];
						}
					}
					if(count($salary_ded) != "") {
						foreach ($salary_ded as $key1 => $value1) {
							$ded_arr[$value1->Salarayid] = $value1->Salarayid;
							$ded_arr_name[$value1->Salarayid] = $value1->Name;
						}
					}
					$dedresult_a=array_intersect($ded_arr,$arr3);
					$dedresult_b=array_diff($ded_arr,$arr3);
					$dedresult = array_merge($dedresult_a,$dedresult_b);
					sort($dedresult);
				?>
			<div class="col-xs-6">
				<fieldset class="col-xs-12 mt20" style="display: inline-block;">
				<legend align="left" 
				style="width: auto !important; background-color: white; border:none !important;float: left; font-size: 15px; position: relative; margin-top: -13px;margin-bottom: 0px !important;margin-left: -5px !important;">
					<b>{{ trans('messages.lbl_empdetails') }}</b></legend>
					<div class="col-xs-12 pm0 pull-right mb10 pl20 pr10 mt10 fwb">
			        	{{ trans('messages.lbl_employeeid').':' }}
			          <span class="mr40 ml12" style="color:blue;">
			            {{ $request->Emp_ID }}
			          </span>
			            {{ trans('messages.lbl_empName').':' }}
			          <span class="mr40 ml12" style="color:#9C0000;margin-left: 10px">
			         	 {{ empnamelength($request->lastname, $request->firstname,12) }}
			          </span>
			    	</div>
				</fieldset>
				<fieldset class="col-xs-12 mt10" style="display: inline-block;">
				<legend align="left" 
				style="width: auto !important; background-color: white; border:none !important;float: left; font-size: 15px; position: relative; margin-top: -13px;margin-bottom: 0px !important;margin-left: -5px !important;">
					<b>{{ trans('messages.lbl_salary_det') }}</b></legend>
					<div class="col-xs-12" style="margin-top: 0px;">
						<div class="col-xs-12 mt10">
							<div class="col-xs-7 text-right clr_blue">
								<label>
									{{ trans('Basic') }}
								</label>
							</div>
							<div class="col-xs-5">
								<span class="col-xs-6 text-right">
									@php $val1 += isset($detedit->Basic)?$detedit->Basic:'' @endphp
									{{ !empty($detedit->Basic)?number_format($detedit->Basic):'' }}
								</span>
								<span class="pm0 col-xs-2">
									円
								</span>
							</div>
						</div>
						<div class="col-xs-12 mt10">
							<div class="col-xs-7 text-right clr_blue">
								<label>
									{{ trans('HRA') }}
								</label>
							</div>
							<div class="col-xs-5">
								<span class="col-xs-6 text-right">
									@php $val1 += isset($detedit->hra)?$detedit->hra:'' @endphp
									{{ !empty($detedit->hra)?number_format($detedit->hra):'' }}
								</span>
								<span class="pm0 col-xs-2">
									円
								</span>
							</div>
						</div>
						<div class="col-xs-12 mt10">
							<div class="col-xs-7 text-right clr_blue">
								<label>
									{{ trans('messages.lbl_train_daily') }}
								</label>
							</div>
							<div class="col-xs-5">
								<span class="col-xs-6 text-right">
									@php $val1 += isset($detedit->train_daily)?$detedit->train_daily:'' @endphp
									{{ !empty($detedit->train_daily)?number_format($detedit->train_daily):'' }}
								</span>
								<span class="pm0 col-xs-2">
									円
								</span>
							</div>
						</div>
						<div class="col-xs-12 mt10">
							<div class="col-xs-7 text-right clr_blue">
								<label>
									{{ trans('messages.lbl_Others') }}
								</label>
							</div>
							<div class="col-xs-5">
								<span class="col-xs-6 text-right">
									@php $val1 += isset($detedit->others)?$detedit->others:'' @endphp
									{{ !empty($detedit->others)?number_format($detedit->others):'' }}
								</span>
								<span class="pm0 col-xs-2">
									円
								</span>
							</div>
						</div>
						@if(count($salary_det)!="")
							@foreach ($salresult as $key2 => $value2)
							@php($len1 = count($sal_arr_name))
							@php $x1 = 0 @endphp
								@foreach ($sal_arr_name as $key3 => $value3)
									@if($key3 == $value2)
										@php $val1 += isset($arr2[$key3])?$arr2[$key3]:'' @endphp
										<div class="col-xs-12 mt10 @if($x1 == $len1 - 1) mb10 @endif">
											<div class="col-xs-7 text-right clr_blue">
												<label>
													{{ $value3 }}
												</label>
											</div>
											<div class="col-xs-5">
												<span class="col-xs-6 text-right">
													{{ (isset($arr2[$key3]) && $arr2[$key3] != '') ? number_format($arr2[$key3]): '' }}
												</span>
												<span class="pm0 col-xs-2">
													円
												</span>
											</div>
										</div>
									@endif
									@php $x1++; @endphp
								@endforeach
							@endforeach
						@endif
					</div>
				</fieldset>
			</div>
			<div style="float: right;" class="col-xs-6">
				<fieldset class="col-xs-12 mt20" style="display: inline-block;">
				<legend align="left" 
				style="width: auto !important; background-color: white; border:none !important;float: left; font-size: 15px; position: relative; margin-top: -13px;margin-bottom: 0px !important;margin-left: -5px !important;">
					<b>{{ trans('messages.lbl_saldate') }}</b></legend>
					<div class="col-xs-12 pm0 pull-right mb10 pl70 pr10 mt10 fwb">
			        	{{ trans('messages.lbl_date').':' }}
			          <span class="mr40 ml12" style="color:#9C0000;">
			            {{ $detedit->year.'-'.sprintf("%02d", $detedit->month) }}
			          </span>
			            {{ trans('messages.lbl_saldate').':' }}
			          <span class="mr40 ml12" style="color:#9C0000;margin-left: 10px">
			         	 {{ $detedit->date }}
			          </span>
			    	</div>
				</fieldset>
				<fieldset class="col-xs-12 mt10" style="display: inline-block;">
				<legend align="left" 
				style="width: auto !important; background-color: white; border:none !important;float: left; font-size: 15px; position: relative; margin-top: -13px;margin-bottom: 0px !important;margin-left: -5px !important;">
					<b>{{ trans('messages.lbl_salary_ded') }}</b></legend>
					<div class="col-xs-12" style="margin-top: 0px;">
						@if(count($salary_ded)!="")
							@foreach ($dedresult as $key2 => $value2)
							<?php $len2 = count($ded_arr_name);
									$x2 = 0; ?>
								@foreach ($ded_arr_name as $key3 => $value3)
									@if($key3 == $value2)
										@php $val2 += isset($arr4[$key3])?$arr4[$key3]:'' @endphp
										<div class="col-xs-12 mt10 @if($x2 == $len2 - 1) mb10 @endif">
											<div class="col-xs-7 text-right clr_blue">
												<label>
													{{ $value3 }}
												</label>
											</div>
											<div class="col-xs-5">
												<span class="col-xs-6 text-right">
													{{ (isset($arr4[$key3]) && $arr4[$key3] != '') ? number_format($arr4[$key3]): '' }}
												</span>
												<span class="pm0 col-xs-2">
													円
												</span>
											</div>
										</div>
									@endif
									<?php $x2++; ?>
								@endforeach
						@endforeach
						@endif
					</div>
				</fieldset>
				<fieldset class="col-xs-12 mt10">
				<legend align="left" 
				style="width: auto !important; background-color: white; border:none !important;float: left; font-size: 15px; position: relative; margin-top: -13px;margin-bottom: 0px !important;margin-left: -5px !important;">
					<b>{{ trans('messages.lbl_transferred') }}</b></legend>
					<div class="col-xs-12" style="margin-top: 0px;">
						<div class="col-xs-12 mt10">
							<div class="col-xs-7 text-right clr_blue">
								<label>
									{{ trans('messages.lbl_totamt') }}
								</label>
							</div>
							<div class="col-xs-5">
								<span class="fwb col-xs-6 text-right" style="color: blue;font-size: 130%;">
								<?php 
									$calc = '0';
									$calc = $val1 + $val2;
								 ?>
									{{ number_format($calc) }}
								</span>
								<span class="pm0 col-xs-2" style="margin-left: 5px !important;">
									円
								</span>
							</div>
						</div>
						<div class="col-xs-12 mt10 mb10">
							<div class="col-xs-7 text-right clr_blue">
								<label>
									{{ trans('messages.lbl_transferred') }}
								</label>
							</div>
							<div class="col-xs-5">
								<span class="col-xs-6 text-right">
									@if($detedit->Transferred != '')
										{{ number_format($detedit->Transferred) }}
									@endif
								</span>
								<span class="pm0 col-xs-2" style="margin-left: 5px !important;">
									円
								</span>
							</div>
						</div>
					</div>
				</fieldset>
				@if($detedit->leave_cnt != '')
				<fieldset class="col-xs-12 mt10">
				<legend align="left" 
				style="width: auto !important; background-color: white; border:none !important;float: left; font-size: 15px; position: relative; margin-top: -13px;margin-bottom: 0px !important;margin-left: -5px !important;">
					<b>{{ trans('messages.lbl_leave') }}</b></legend>
					<div class="col-xs-12" style="margin-top: 0px;">
						<div class="col-xs-12 mt10 mb10">
							<div class="col-xs-7 text-right clr_blue">
								<label>
									{{ trans('messages.lbl_leave') }}
								</label>
							</div>
							<div class="col-xs-5">
								@if($detedit->leave_cnt != '')
									{{ $detedit->leave_cnt }}
								@endif
							</div>
						</div>
					</div>
				</fieldset>
				@endif
				@if($detedit->remarks != '')
				<fieldset class="col-xs-12 mt20">
				<legend align="left" 
				style="width: auto !important; background-color: white; border:none !important;float: left; font-size: 15px; position: relative; margin-top: -13px;margin-bottom: 0px !important;margin-left: -5px !important;">
					<b>{{ trans('messages.lbl_remarks') }}</b></legend>
					<div class="col-xs-12" style="margin-top: 0px;">
						<div class="col-xs-12 mt10 mb10">
							<div class="col-xs-3 text-right clr_blue">
								<label>{{ trans('messages.lbl_remarks') }}</label><span class="fr ml2"></span>
							</div>
							<div class="col-xs-9">
								{!! nl2br(e((!empty($detedit->remarks) ? $detedit->remarks : ""))) !!}
							</div>
						</div>
					</div>
					</div>
				</fieldset>
				@endif
			</div>
	    {{ Form::close() }}
	</article>
</div>
@endsection