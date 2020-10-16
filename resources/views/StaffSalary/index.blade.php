@extends('layouts.app')
@section('content')
@php use App\Http\Helpers @endphp
{{ HTML::script('resources/assets/js/staffsalary.js') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
</script>
<style type="text/css">
	span.panelTitleTxt {
	text-align: center;
	}
</style>

<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="master" class="DEC_flex_wrapper " data-category="staff staff_sub_5">
		{{ Form::open(array('name'=>'staffslyfrm',
							'id'=>'staffslyfrm',
							'url'=>'StaffSalary/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
							'files'=>true,
							'method' => 'POST' )) }}
		{{ Form::hidden('viewid', '', array('id' => 'viewid')) }}
		{{ Form::hidden('empid', '', array('id' => 'empid')) }}
		{{ Form::hidden('DOJ', '', array('id' => 'DOJ')) }}
		{{ Form::hidden('lastname', '', array('id' => 'lastname')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, 
								array('id' => 'previou_next_year')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('empname', '', array('id' => 'empname')) }}
		{{ Form::hidden('hdnback', 1, array('id' => 'hdnback')) }}
		<div class="row hline" >
					<div style="width: 100%;" class="col-xs-12 ml1000" ></div>
					<img class="pull-left box35 mt10 ml10" src="{{ URL::asset('resources/assets/images/salary_1.png') }}">
					<h2 class="pull-left pl5 mt15">
						{{ trans('messages.lbl_salarylist') }}</h2>
		</div>
			<div class="pb10"></div>
			<div class="box100per pr10 pl10 mt10">
				<div class="mt10">
				</div>
			</div>
			<div class="box100per pr10 pl10">
				<div style="margin-top: -10px;">
				</div>
			</div>
			<div class="box100per pr10 pl10">
				<div style="margin-top:10px;">
					{{ Helpers::displayYear_MonthEst($account_period, $year_month, $db_year_month, $date_month, $dbnext, $dbprevious, $last_year, $current_year, $account_val) }}
				</div>
			</div>
		<!-- 	Table data -->
		<div class="mt25">
			<div class="minh400 box100per pl10 pr10">
				<table class="tablealternate box100per CMN_tblfixed">
					<colgroup>
						<col width="4%">
						<col width="7%">
						<col width="">
						@for ($i=0; $i < count($settingDetail); $i++)  
							@if (isset($total_allow[$i]['allow']) && 
									$total_allow[$i]['allow'] == 'allow') 
								<col width="10%">
							@endif
						@endfor
						<col width="8%">
						<col width="8%">
					</colgroup>
					<thead class="CMN_tbltheadcolor">
							<th class="vam">{{ trans('messages.lbl_sno') }}</th>
							<th class="vam">{{ trans('messages.lbl_empno') }}</th>
							<th class="vam" style="padding-right: 3px;">
								{{ trans('messages.lbl_name') }}</th>
							@for ($i=0; $i < count($settingDetail); $i++)  
								@if (isset($total_allow[$i]['allow']) && 
										$total_allow[$i]['allow'] == 'allow') 
									<th class="tdhead vam">
										{{  $settingDetail[$i]['mainflg'] }}
								@endif
									</th>
							@endfor
							<th class="vam">{{ trans('messages.lbl_total') }}</th>
							<th class="vam">{{ trans('messages.lbl_status') }}</th>
						</thead>
					<tbody>
						<tr>
							<td colspan="0" bgcolor="lightgrey" class="tar fwb" style="color:blue;">
							</td>
							<td bgcolor="lightgrey" class="tar fwb" style="color:blue;">
							</td>
							<td bgcolor="lightgrey" class="tar fwb" style="color:blue;">
								{{ trans('messages.lbl_grandtot') }}
							</td>
							@for ($i=0; $i < count($salary_tot_value); $i++)
							<td class="fwb" align="right"  style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;padding-right:5px;color:blue;">
									{{  number_format($salary_tot_value[$i]) }} 
							</td>
							@endfor
							<td bgcolor="lightgrey" class="tar fwb" style="color:blue;">
								{{ number_format($totalval) }}</td>
							<td bgcolor="lightgrey"></td>
						</tr>
						@for ($j = 0; $j < count($get_det); $j++)
							<tr>
								<td align="center"> 
									{{ ($g_query->currentpage()-1) * $g_query->perpage() + $j + 1 }}
								</td>
								<td class="tac fwb">
									<a href="javascript:gotosalaryview('{{ $get_det[$j]['Emp_ID'] }}','{{ $request->	mainmenu }}','{{ $get_det[$j]['LastName'] }}', '{{ $get_det[$j]['DOJ'] }}');"
									class="anchorstyle">
									{{ $get_det[$j]['Emp_ID'] }} </a>
								</td>
								<td>  
									@if($get_det[$j]['grand_total'])
									<a style="color:blue;" href="javascript:view('{{ $request->mainmenu }}', '{{ $get_det[$j]['id'] }}',
										'{{ $get_det[$j]['LastName']  }}')">
										<?php
											if (mb_strlen($get_det[$j]['LastName'], 'UTF-8') >= 14) {
											$str = mb_substr(ucwords(strtolower($get_det[$j]['LastName'])), 0, 13, 'UTF-8');
											echo "<span title = '".ucwords(strtolower($get_det[$j]['LastName'])) . "." . 
											ucwords(mb_substr($get_det[$j]['FirstName'], 0, 1, 'UTF-8'))."'>".$str."...</span>"; 
											} else {
											echo ucwords(strtolower($get_det[$j]['LastName'])) . "." . ucwords(mb_substr($get_det[$j]['FirstName'], 0, 1, 'UTF-8'));
											}
										?>
									</a>
									@else
									<?php
											if (mb_strlen($get_det[$j]['LastName'], 'UTF-8') >= 14) {
											$str = mb_substr(ucwords(strtolower($get_det[$j]['LastName'])), 0, 13, 'UTF-8');
											echo "<span title = '".ucwords(strtolower($get_det[$j]['LastName'])) . "." . 
											ucwords(mb_substr($get_det[$j]['FirstName'], 0, 1, 'UTF-8'))."'>".$str."...</span>"; 
											} else {
											echo ucwords(strtolower($get_det[$j]['LastName'])) . "." . ucwords(mb_substr($get_det[$j]['FirstName'], 0, 1, 'UTF-8'));
											}
										?>
									@endif

								</td>
									@if(isset($get_det))
								@for ($i=0; $i < count($settingDetail); $i++)
									@if(isset($total_allow[$i]['allow']))
										@if($total_allow[$i]['allow'] == "allow")
								<td class="tdcontent tar">
									@if ($get_det[$j][$i] == "" && $get_det[$j]['status'] == 1)
										
									@elseif ($get_det[$j][$i] == "" && $get_det[$j]['Total'] != "")
										
									@elseif ($get_det[$j][$i] == "" && $get_det[$j]['status'] == 0)
										 
									@else
									  	{{  number_format($get_det[$j][$i]) }}
									@endif
									@else
									@endif
								@endif
								</td>
								@endfor
								<td class="tdcontent tar"> 
									@if(isset($get_det[$j]['grand_total']))
										{{ number_format($get_det[$j]['grand_total']) }}
									@endif
								</td>
								<td align="center">
								<?php  
									$month = $get_det[$j]['month_ln'];  
									$Year = $get_det[$j]['year_ln']; 
									$monyear = $Year."-".$month;
									$futuremonth = date ('n', strtotime ( '+1 month' , strtotime ( $monyear."-01" )));
									$futureyear = date ('Y', strtotime ( '+1 month' , strtotime ( $monyear."-01" )));
									$copy_month_flg = $get_det[$j]['copy_month_flg'];?>

								@if(isset($get_det[$j]['grand_total']))
									@if($get_det[$j]['status'] == 1) 
										
											<div class="inb"><a href="javascript:underconstruction();" id="linkcolor" align="right" style="text-decoration: none;color:blue;">
												@if($get_det[$j]['copy'] != "1" || $copy_month_flg == "1")
													<div style= "vertical-align: middle;"> 
														<a href="javascript:salaryCopymonth();" id="linkcolor" 
													style="text-decoration: none;color:blue;">  
														<img class="vam" src="{{ URL::asset('resources/assets/images/copy.png') }}" width="20" height="20">
												@else 
													<div style="display: inline-block;" class="col-xs-12">
														<div class="mr30" style="vertical-align: middle;background-color: red;width:10px;height:10px;border:1px solid red; margin-left: 17px;">
													</div>
												@endif
											</a></div>
									@elseif($get_det[$j]['status'] == 0 && isset($get_det[$j]['Total']))
										<div class="mr30" style="vertical-align: middle;background-color: red;width:10px;height:10px;border:1px solid red; margin-left: 21px;">
									@endif
								@endif
								</td>
									<!-- @endif -->
							</tr>
						@endfor
					</tbody>
				</table>
				<div class="pb10"></div>
			</div>
		</div>
		<div class="text-center pl13">
			@if(!empty($g_query->total()))
				<span class="pull-left mt24">
					{{ $g_query->firstItem() }} ~ {{ $g_query->lastItem() }} / {{ $g_query->total() }}
				</span>
			@endif 
			{{ $g_query->links() }}
			<div class="CMN_display_block flr mr10">
				{{ $g_query->linkspagelimit() }}
			</div>
	</div>
		{{ Form::close() }}
	</article>
</div>
@endsection