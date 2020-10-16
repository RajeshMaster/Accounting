@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/staffsalary.js') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
</script>
<style type="text/css">
table {
	border: none;
	background-color: #D8BFD8;
	border: none !important;
}
table td {
	border:none;
}
</style>
<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
<article id="master" class="DEC_flex_wrapper " data-category="staff staff_sub_5">
	{{ Form::open(array('name'=>'salaryview',
						'id'=>'salaryview',
						'url'=>'StaffSalary/salaryview?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST' )) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
		<div class="row hline">
					<div class="">
						<img class="pull-left box35 mt5 ml0" src="{{ URL::asset('resources/assets/images/salary_1.png') }}">
						<h2 class="pull-left pl5 fwb mt10 ">{{ trans('messages.lbl_salary') }}</h2>
						<h2 class="pull-left pl5 fwb mt5 f50 ">.</h2>
						<h2 class="pull-left pl5 fwb mt10 colbl ">{{trans('messages.lbl_view') }}</h2>
					</div>
		</div>
		<div class="pull-left ml1 mt5">
				<a href="javascript:index();" class="pageload btn btn-info box80"><span class="fa fa-arrow-left"></span> 
					{{ trans('messages.lbl_back') }}</a><br>
		</div>
		<div class="mb60"></div>
		<div class="box100per pr10 pl10 mt20">
			<div class="mt10">
			</div>
		</div> 
		<div class="box100per pr10 pl10">
			<div style="margin-top: -15px;"></div>
		</div>
			<table border="0" width="100%" cellspacing="0" cellpadding="0" 
									style="background-color: #DDF1FA;">
				<tr>
					<td>
						<span class="fwb" style="color:#136E83;">
							{{ trans('messages.lbl_employeeid') }} 
						</span>
						<span class="fwb ml25" style="color:blue;"> 
							{{ $salaryview[0]->Emp_ID }}
						</span>
						<span class="ml90 fwb" style="color:#136E83;"> 
							{{ trans('messages.lbl_name') }} 
						</span>
						<span class="ml25">	
							<?php 
								echo ucwords(strtolower($salaryview[0]->LastName)) . "." . ucwords(mb_substr($salaryview[0]->FirstName, 0, 1, 'UTF-8'));
							?>
						</span>
						<span class="ml492 fwb" style="color:#136E83;"> 
							{{ trans('messages.lbl_date') }} 
						</span>

						<span class=" ml20">
							{{ $request->selYear."年".substr("0" . $request->selMonth, -2)."月" }} 
						</span>
					</td>
				</tr>
			</table>
			<div style="margin-top: -20px;"></div>
			@if(isset($settingDetails))
			<table width="100%" cellspacing="0" cellpadding="0" style="background-color: #DDF1FA;" >
 				<div class="mt40"></div>
				{{ $temp = ""}}
				{{--*/ $row = '0' /*--}}
				{{--*/ $x = '0' /*--}}
				{{--*/ $j = '0' /*--}}
				{{--*/ $xx = '0' /*--}}
				{{--*/ $temp_i = '1' /*--}}
				@if(count($settingDetails)!="")
				{{--*/ $k = 1 /*--}}
				@for ($i = 0; $i < count($settingDetails); $i++)
					<?php 
						$j++;
						$totalName = "total".$x;
						$total_name = "total_".$x;
						$sVal = array("Salary","OverTime","Travel","Others","Main5_","Main6_","Main7_","Main8_","Main9_","Main10_");
						if ($settingDetails[$i]['mainField'] != $temp  && $i != 0) { 
							$j= 1;
							$temp_i++;
							$temp_count = 0;
						}
						if (isset($settingDetails[$i+1])) {
						if ($settingDetails[$i+1]['mainField'] == $settingDetails[$i]['mainField']) { 
							$field = $sVal[$xx].$j; 
						} else {
							$field = $sVal[$xx].$j;
							$xx++;
							$x++;
						} 
						}
						if($settingDetails[$i]['subdelflg'] == 1) { 
							$get_det[0][$field] = 0; 
						}
					?>
	  				{{--*/ $loc = $settingDetails[$i]['mainField'] /*--}}
	  				@if($loc != $temp)
						@if($row==1)
							{{--*/ $style_tr = '' /*--}}
							{{--*/ $row = '0' /*--}}
						@else
							{{--*/ $style_tr = '' /*--}}
							{{--*/ $row = '1' /*--}}
						@endif
							{{--*/ $style_td = '' /*--}}
					@else
						{{--*/ $style_td = 'border-top: hidden;' /*--}}
					@endif
						<tr class="mt20" <?php echo $style_tr; ?>>
							<td width="15%" class="tar fwb" style="color:#136E83;">
								@if($loc != $temp)
									{{ $settingDetails[$i]['mainField'] }}
								@endif
							</td>
							<td width="13%" class="tar fwb" style="color:#136E83;">
								{{  $settingDetails[$i]['subField'] }}</td>
							<td width="10%" class="tar">
								@if(count($get_det)!="")
									{{ number_format($get_det[0][$field]) }}
								@else
								@endif
							</td>
							<td width="20%" class="tar" style="padding-right: 200px !important;
								<?php //echo $style_td; ?>">
								@if(isset($settingDetails[$i+1]['mainField']))
									@if($settingDetails[$i+1]['mainField'] != $settingDetails[$i]['mainField'])
										@if(!isset($get_detailstot))
											$get_detailstot = "";
										@endif
										@if($get_detailstot != "")
											@if(isset($get_detailstot[0]['total_'.$k]))
												{{ number_format($get_detailstot[0]['total_'.$k]) }}
												{{--*/ $k = $k+1 /*--}}
												{{ "円" }}
											@else
											@endif
										@else
										@endif
									@endif
								@else
									@if(isset($get_detailstot[0]['total_'.$k]))
										{{ number_format($get_detailstot[0]['total_'.$k]) }}
										{{ "円" }}
									@endif
								@endif
							</td>
						</tr>
						{{--*/ $temp = $loc /*--}}
					@endfor
				@endif
				<tr>
					<td width="20%" class="tar fwb" style="color:#136E83;">
						{{ trans('messages.lbl_total') }} 
					</td>
					<td></td>
					<td></td>
					<td class="tar fwb vab" style="padding-right: 200px !important;"> 
						<span>
							@if(count($get_details_total)!="")
								{{ number_format($get_details_total[0]['Total']) }}
						</span>
								{{ "円" }}
							@else
							@endif
					</td>
				</tr>
				<tr>
					<td width="20%" class="tar fwb" style="color:#136E83;">
						{{ trans('messages.lbl_remarks') }} 
					</td>
					<td></td>
					<td>	
						@if(isset($get_det[0]['remarks']))	
							{!! nl2br(e($get_det[0]['remarks'])) !!}
						@endif
					</td>
				</tr>
			</table>
			@else
				{{ "No data found!!!" }}
			@endif
			</div>
	{{ Form::close() }}
</article>
</div>
@endsection