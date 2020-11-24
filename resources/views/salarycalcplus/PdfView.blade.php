
<?php ini_set('memory_limit', '-1');
?>
<!DOCTYPE html>
<html lang="jp">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link rel="stylesheet" type="text/css" href="../resources/assets/css/lib/font-awesome.min.css" />
	<link rel="stylesheet" type="text/css" href="../resources/assets/css/lib/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="../resources/assets/css/common.css" />
	<link rel="stylesheet" type="text/css" href="../resources/assets/css/widthbox.css" />
	<link rel="stylesheet" type="text/css" href="../resources/assets/css/paddingmargin.css" />
	<link rel="stylesheet" type="text/css" href="../resources/assets/css/lib/sidebar-bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="../resources/assets/css/lib/navigation-font-awesome.min.css" />
	<link rel="stylesheet" type="text/css" href="../resources/assets/css/lib/switch.css" />
	<link rel="stylesheet" type="text/css" href="../resources/assets/css/lib/hoe.css" />
	<link rel="stylesheet" type="text/css" href="../resources/assets/css/lib/envatopages.css" />
	<script type="text/javascript" src="../resources/assets/js/render.js"></script>
</head>
<style type="text/css">
	.linehight {
		line-height: 30px ! important;
	} 
	@font-face{
		font-family: ipag;
		font-style: normal;
		font-weight: normal;
		src:url('{{ storage_path('fonts/ipag.ttf')}}');
	}
	@font-face{
		font-family: ipag;
		font-style: bold;
		font-weight: bold;
		src:url('{{ storage_path('fonts/ipag.ttf')}}');
	}
	body {
		font-family: ipag;
		font-size: 12px;
	}
	.btnstyle { height: 25px ! important; background: gray; line-height: 10px; }
	.sizefont{
		font-size: 120%!important;
	}
	td{ border:  1px solid #a7cec9 ! important;}

	.text-right {
		text-align: right;
	}
</style>
<body>
	<div style="width: 100%;margin-bottom: 20%;">
		<div style="min-height: 50px">
		<div align="center" class="sizefont">
			株式会社Microbit<br><br>
			@if(!isset($request->payrollPdf))
				{{ $request->selYear.'年分   給与一覧' }}
			@else
				{{ $request->selYear.'年 '.$request->selMonth.'月分   給与一覧' }}
			@endif
		</div>
		@if($salArr != "")
		<table class="table box100per mt125 breakword" align="center" 
			style="table-layout: fixed;">
			<thead id="multiadd_thead"  class="CMN_tbltheadcolor">
				<tr class="box100per table">
					<th class="res resbg tac vam" rowspan="2">{{ trans('messages.lbl_sno') }}</th>
					<th class="res resbg tac vam" rowspan="2">{{ trans('messages.lbl_empno') }}</th>
					<th class="res resbg tac vam " rowspan="2">{{ trans('messages.lbl_name') }}</th>
					@php($sal_det_cnt = '')
					@if(count($salary_det)!="")
						@for ($i = 0; $i < count($salary_det); $i++)
							@if(isset($temp_salaryDetails[$salary_det[$i]->Salarayid]) && $temp_salaryDetails[$salary_det[$i]->Salarayid] != '0')
								@php($sal_det_cnt += 1)
							@endif
						@endfor
					@endif
					@if($sal_det_cnt != '')
						<th rowspan="1" colspan="<?php echo $sal_det_cnt; ?>" 
							class="res resbg tac vam">
						{{ trans('messages.lbl_salary_det') }}</th>
						<th rowspan="2" colspan="1" class="tac vam" style="min-width: 110px;background-color: #f39c1280 !important">
						{{ trans('messages.lbl_totpay_amt') }}</th>
					@endif
					@php($sal_ded_cnt = '')
					@if(count($salary_ded)!="")
						@for ($j = 0; $j < count($salary_ded); $j++)
							@if(isset($temp_salaryDetails_DD[$salary_ded[$j]->Salarayid]) && $temp_salaryDetails_DD[$salary_ded[$j]->Salarayid] != '0')
								@php($sal_ded_cnt += 1)
							@endif
						@endfor
					@endif
					@if($sal_ded_cnt!="")
						<th rowspan="1" colspan="<?php echo $sal_ded_cnt; ?>" 
						class="vam multiadd_th res resbg tac">
							{{ trans('messages.lbl_salary_ded') }}</th>
						<th rowspan="2" colspan="1" class="vam multiadd_th tac" style="min-width: 110px;background-color: #f39c1280 !important">{{ trans('messages.lbl_totded_amt') }}</th>
					@endif
					@php($sal_trv_cnt = '')
					@if($tot_travel_amt != '')
						@php($sal_trv_cnt += 1)
						<th rowspan="2" colspan="1" class="vam multiadd_th　res resbg tac">{{ trans('messages.lbl_travel_exp') }}</th>
						<th rowspan="2" colspan="1" class="vam multiadd_th tac" style="min-width: 110px;background-color: #f39c1280 !important">{{ trans('messages.lbl_total') }}</th>
					@endif
					<th rowspan="2" colspan="1" class="vam multiadd_th　res resbg tac" 
					style="min-width: 120px;">{{ trans('messages.lbl_grandtot') }}</th>
				</tr>
				<tr>
					@if(count($salary_det)!="")
						@for ($i = 0; $i < count($salary_det); $i++)
							@if(isset($temp_salaryDetails[$salary_det[$i]->Salarayid]) && $temp_salaryDetails[$salary_det[$i]->Salarayid] != '0')
								<th class="vam multiadd_th res resbg tac">
									<label title="{{ (isset($salary_det[$i]->nick_name) && $salary_det[$i]->nick_name != '')?$salary_det[$i]->Name:'' }}" style="margin-bottom: 0px !important;">{{ (isset($salary_det[$i]->nick_name) && $salary_det[$i]->nick_name != '')?$salary_det[$i]->nick_name:$salary_det[$i]->Name }}</label>
								</th>
							@endif
						@endfor
					@endif
					@if(count($salary_ded)!="")
						@for ($j = 0; $j < count($salary_ded); $j++)
						@if(isset($temp_salaryDetails_DD[$salary_ded[$j]->Salarayid]) && $temp_salaryDetails_DD[$salary_ded[$j]->Salarayid] != '0')
							<th class="vam multiadd_th res resbg tac">
								<label title="{{ (isset($salary_ded[$j]->nick_name) && $salary_ded[$j]->nick_name != '')?$salary_ded[$j]->Name: ''}}" style="margin-bottom: 0px !important;">{{ (isset($salary_ded[$j]->nick_name) && $salary_ded[$j]->nick_name != '')?$salary_ded[$j]->nick_name:$salary_ded[$j]->Name }}</label>
								</th>
							@endif
						@endfor
					@endif
				</tr>
			</thead>
			<tbody id="multiadd_tbody">
				@php $sNo = 1 @endphp
				@foreach($salArr as $key => $sal)
				@if(isset($sal['LastName']))
				@php 
					$resignYear = substr($sal['resigndate'], 0,4);
					$resignMnth = substr($sal['resigndate'], 5,2);
				@endphp
				@if($sal['resign_id'] == 0 || $resignYear > $request->selYear || ($resignYear == $request->selYear && $resignMnth >= $request->selMonth))
					<tr class="box100per table" style="height: 100px;">
						<td class="res tal vam tac">
							{{ $sNo }}
						</td>
						<td class="res tal vam tac">
							{{ $sal['Emp_ID'] }}
						</td>
						<td class="res tal vam">
							{{ empnamelength($sal['LastName'], $sal['FirstName'],14) }}
						</td>
						@php($x = 0)
						@foreach ($salresult as $key2 => $value2)
							@if(isset($temp_salaryDetails[$key2]) && $temp_salaryDetails[$key2] != '0')
								@if($sal['salExist'])
									@if($key2 == isset($sal['salDetTotal'][$key2]))
										<td class="text-right pr10 multiadd_td SD_{{ $x }}"> {{ ($sal['salDetTotal'][$key2] != '') ? number_format($sal['salDetTotal'][$key2]): '0' }}</td>
									@else
										<td class="text-right pr10 multiadd_td">{{"0"}}</td>
									@endif
								@else
									<td class="text-right pr10 multiadd_td"></td>
								@endif
							@endif
							@php($x++)
						@endforeach
						@if($sal_det_cnt != '')
							<td class="text-right pr10 multiadd_td" style="min-width: 110px;background-color: #f39c1280 !important">
								@if($sal['totSalary'] != "" && $sal['salExist'])
									{{ number_format($sal['totSalary'])}}
								@else
								@endif
							</td>
						@endif
						@php($y = 0)
						@foreach ($dedresult as $key2 => $value2)
							@if(isset($temp_salaryDetails_DD[$key2]) && $temp_salaryDetails_DD[$key2] != '0')
								@if($sal['salExist'])
									@if($key2 == isset($sal['dedDetTotal'][$key2]))
										<td class="text-right pr10 multiadd_td DD_{{ $y }}" style="color: red;"> {{ ($sal['dedDetTotal'][$key2] != '') ? number_format($sal['dedDetTotal'][$key2]): '0' }}</td>
									@else
										<td class="text-right pr10 multiadd_td" style="color:red;">{{"0"}}</td>
									@endif
								@else
									<td class="text-right pr10 multiadd_td"></td>
								@endif
							@endif
							@php($y++)
						@endforeach
						@if($sal_ded_cnt != '')
							<td class="text-right pr10 multiadd_td" style="min-width: 110px;color: red;background-color: #f39c1280 !important;">
								@if($sal['totDetuct'] != "" && $sal['salExist'])
										{{ number_format($sal['totDetuct'])}}
								@else
								@endif
							</td>
						@endif
						@if($tot_travel_amt != '')
							<td class="text-right pr10 multiadd_td">
								@if($sal['totTravel'] != "" && $sal['salExist'])
									{{ number_format($sal['totTravel'])}}
								@elseif($sal['totTravel'] == "" && $sal['salExist'])
									0
								@endif
							</td>

							<td class="text-right pr10 multiadd_td" style="min-width: 110px;background-color: #f39c1280 !important">
								@if($sal['totTravel'] != "" && $sal['salExist'])
										{{ number_format($sal['totTravel']) }}
								@elseif($sal['totTravel'] == "" && $sal['salExist'])
									0
								@endif
							</td>
						@endif
						<td class="text-right pr10 multiadd_td res resbg" style="min-width: 110px;background-color: #00a65a45 !important;">
							@if($sal['grandTotal'] != "" && $sal['salExist'])
								{{ number_format($sal['grandTotal']) }}
							@else
							@endif
						</td>
					</tr>
				@endif
				@endif
				@php $sNo++ @endphp
				@endforeach
				<tr>
					<td colspan="3" class="tac">合計</td>
					@php $tot_over_amt = '' @endphp
					@php($tot1 = '')
					@if(count($salary_det)!="")
						@for ($i = 0; $i < count($salary_det); $i++)
							@if(count($salArr)!="")
								@if(isset($temp_salaryDetails[$salary_det[$i]->Salarayid]) && $temp_salaryDetails[$salary_det[$i]->Salarayid] != '0')
									<th class="vam multiadd_th text-right">
										<span id="totalSDspan<?php echo $i ?>" class="text-right multiadd_td" style="color:blue;">
											@php($tot1 += $temp_salaryDetails[$salary_det[$i]->Salarayid])
											@php $tot_over_amt += $temp_salaryDetails[$salary_det[$i]->Salarayid] @endphp
											{{ number_format($temp_salaryDetails[$salary_det[$i]->Salarayid]) }}
										</span>
									</th>
								@endif
							@endif
						@endfor
					@endif
					@if($sal_det_cnt != '')
					<th class="vam multiadd_th text-right pr10" style="color:blue;background-color: #f39c1280 !important;">
						{{ number_format($tot1) }}
					</th>
					@endif
					@php($tot2 = '')
					@if(count($salary_ded)!="")
						@for ($j = 0; $j < count($salary_ded); $j++)
							@if(count($salArr)!="")
								@if(isset($temp_salaryDetails_DD[$salary_ded[$j]->Salarayid]) && $temp_salaryDetails_DD[$salary_ded[$j]->Salarayid] != '0')
									<th class="vam multiadd_th text-right">
										<span id="totalDDspan<?php echo $j ?>" class="text-right multiadd_td" style="color:red;">
											@php($tot2 += $temp_salaryDetails_DD[$salary_ded[$j]->Salarayid])
											@php $tot_over_amt += $temp_salaryDetails_DD[$salary_ded[$j]->Salarayid] @endphp
											{{ number_format($temp_salaryDetails_DD[$salary_ded[$j]->Salarayid]) }}
										</span>
									</th>
								@endif
							@endif
						@endfor
					@else
						<th class="vam multiadd_th text-right" style="min-width: 287px;"></th>
					@endif
					@if($sal_ded_cnt != '')
					<th class="vam multiadd_th text-right pr10" style="color:red;background-color: #f39c1280 !important;">
						{{ number_format($tot2) }}
					</th>
					@endif
					@if($tot_travel_amt != '')
					<th class="vam multiadd_th text-right pr10" style="color:blue;">
						@php $tot_over_amt += $tot_travel_amt @endphp
						{{ number_format($tot_travel_amt) }}
					</th>
					<th class="vam multiadd_th text-right pr10" style="color:blue;background-color: #f39c1280 !important;">
						{{ number_format($tot_travel_amt) }}
					</th>
					@endif
					<th class="vam multiadd_th text-right pr10" style="color:blue;background-color: #00a65a45 !important;">
					@if($tot_over_amt != '')
						{{ number_format($tot_over_amt) }}
					@endif
					</th>
				</tr>
			</tbody>
		</table>
		@endif
	</div>
</div>
</body>
</html>