@extends('layouts.app')
@section('content')
@php use App\Http\Helpers @endphp
{{ HTML::script('resources/assets/js/salaryplus.js') }}
{{ HTML::script('resources/assets/js/salarycalc.js') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	$(document).ready(function() {
  		var ht;
  		var lastWindowHeight = $(window).height();
        ht= $(window).height();
        if(ht!=lastWindowHeight) {
        	$("#sidebar").height(ht);
        }
	});
	$(window).bind('resize', function () { 
  		var ht;
  		var lastWindowHeight = $(window).height();
        ht= $(window).height();
        if(ht!=lastWindowHeight) {
        	$("#sidebar").height(ht);
        }
	});
	function pageClick(pageval) {
		$('#page').val(pageval);
		$("#salarycalchistory").submit();
	}
	function pageLimitClick(pagelimitval) {
		$('#page').val('');
		$('#plimit').val(pagelimitval);
		$("#salarycalchistory").submit();
	}

	function getData(selYear,time) {
	    $('#selYear').val(selYear);
	    $("#salarycalchistory").submit();
	}
	
</script>
<style type="text/css">

	.yrBorder{
		height: 22px !important;
	}
	.alertboxalign {
		margin-bottom: -35px !important;
	}
	.alert {
		display:inline-block !important;
		height:30px !important;
		padding:5px !important;
	}
	.btn-gray {
 		 background-color: gray;
  		 border-color: white;
	}
	.btn-red {
	background-color: red;
  	border-color: white;
  	color: white;
	}
	.bg_lightgrey {
	    background-color:#D3D3D3    ! important;
	}
</style>
<div class="CMN_display_block" id="main_contents" style="width: 100%;">
	<!-- article to select the main&sub menu -->
	<article id="auditing" class="DEC_flex_wrapper " data-category="auditing auditing_sub_2">
		{{ Form::open(array('name'=>'salarycalchistory',
							'id'=>'salarycalchistory',
							'url'=>'salarycalc/history?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
							'files'=>true,
							'method' => 'POST' )) }}
			{{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
			{{ Form::hidden('tblchg', $request->tblchg , array('id' => 'tblchg')) }}
			{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
			{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
			{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
			{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
			{{ Form::hidden('previou_next_year', $request->previou_next_year, 
								array('id' => 'previou_next_year')) }}
			{{ Form::hidden('hiddenplimit', $request->hiddenplimit , array('id' => 'hiddenplimit')) }}
			{{ Form::hidden('hiddenpage', $request->hiddenpage , array('id' => 'hiddenpage')) }}
			{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
			{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
			{{ Form::hidden('id','' , array('id' => 'id')) }}
			{{ Form::hidden('Emp_ID','' , array('id' => 'Emp_ID')) }}
			{{ Form::hidden('editcheck','' , array('id' => 'editcheck')) }}
			{{ Form::hidden('firstname','' , array('id' => 'firstname')) }}
			{{ Form::hidden('lastname','' , array('id' => 'lastname')) }}
			{{ Form::hidden('mutlireg','' , array('id' => 'mutlireg')) }}
	    	{{ Form::hidden('empname', '' , array('id' => 'empname')) }}
	    	{{ Form::hidden('total', '' , array('id' => 'total')) }}
			{{ Form::hidden('multiflg','' , array('id' => 'multiflg')) }}
			{{ Form::hidden('editflg', '' , array('id' => 'editflg')) }}
			{{ Form::hidden('hdn_empid_arr', '' , array('id' => 'hdn_empid_arr')) }}
			{{ Form::hidden('salflg', '' , array('id' => 'salflg')) }}
			{{ Form::hidden('hdn_salid_arr', '' , array('id' => 'hdn_salid_arr')) }}
			{{ Form::hidden('get_prev_yr', $request->get_prev_yr , array('id' => 'get_prev_yr')) }}

		<!-- Start Heading -->
		<div class="row hline pm0">
				<div class="col-xs-12">
					<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/salary_1.png') }}">
					<h2 class="pull-left pl5 mt10">
						{{ trans('messages.lbl_salhistory') }}
					</h2>
				</div>
			</div>
		<!-- End Heading -->	
		<div class="col-xs-12 pt5">

			<div class="col-xs-6" style="text-align: left;margin-left: -15px;">

				<a href="javascript:gotoindexback('{{ $request->mainmenu }}','{{ $request->hiddenplimit }}','{{ $request->hiddenpage }}');" class="btn btn-info box80">

					<span class="fa fa-arrow-left"></span>

					{{ trans('messages.lbl_back') }}

				</a>

			</div>

		</div>

		<div class="col-xs-12 pt5">
			<div class="mt30 mb15">
	          {{ Helpers::displayYear($prev_yrs,$cur_year,$total_yrs,$curtime) }}
	        </div>
	    </div>


		<div class="col-xs-12 pm0 pull-right mb10 pl15 pr10 mt10 fwb">
	        {{ trans('messages.lbl_employeeid').':' }}
	          <span class="mr40 ml12" style="color:blue;">
	            {{ $request->Emp_ID }}
	          </span>
	            {{ trans('messages.lbl_empName').':' }}
	          <span style="color:#9C0000;margin-left: 10px">
	          {{ ucfirst($request->lastname).'.'.ucwords(mb_substr($request->firstname,0,1,'UTF-8'))  }}
	          </span>
	    </div>

		<div class="minh400 box100per pl10 pr10 mt10 pt35">
			<div style="border: 1px solid white;overflow-x: auto;" id="sidebar">
				@php $count = 52; @endphp
				@if(count($salary_det)!="")
	 				@for ($i = 0; $i < count($salary_det); $i++)
	 					@if(isset($temp_salaryDetails[$salary_det[$i]->Salarayid]) && $temp_salaryDetails[$salary_det[$i]->Salarayid] != '0')
	 						@php $count += 13; @endphp
 						@endif
	 				@endfor
 				@endif
 				@if(count($salary_ded)!="")
	 				@for ($i = 0; $i < count($salary_ded); $i++)
	 					@if(isset($temp_salaryDetails_DD[$salary_ded[$i]->Salarayid]) && $temp_salaryDetails_DD[$salary_ded[$i]->Salarayid] != '0')
	 						@php $count += 13; @endphp
 						@endif
	 				@endfor
 				@endif
 				@if($count<100)
 				@endif
			@if(count($get_det)!="")
			<table class="tablealternate CMN_tblfixed" style="width: <?php echo $count; ?>% !important;">
				
				<thead class="CMN_tbltheadcolor">
					<tr>
						<th rowspan="2" class="vam" style="width: 5%;">{{ trans('messages.lbl_sno') }}</th>
						<th rowspan="2" class="vam">{{ trans('messages.lbl_date') }}</th>
						<th rowspan="2" class="vam" style="width: 12% !important;">{{ trans('messages.lbl_paymentdate') }}</th>
						@php($sal_det_cnt = '')
						@if($tot_basic_amt != '')
	 						@php($sal_det_cnt += 1)
						@endif
						@if($tot_hra_amt != '')
	 						@php($sal_det_cnt += 1)
						@endif
	 					@if($tot_train_amt != '')
	 						@php($sal_det_cnt += 1)
						@endif
						@if($tot_exp_amt != '')
	 						@php($sal_det_cnt += 1)
						@endif
						@if(count($salary_det)!="")
			 				@for ($i = 0; $i < count($salary_det); $i++)
			 					@if(isset($temp_salaryDetails[$salary_det[$i]->Salarayid]) && $temp_salaryDetails[$salary_det[$i]->Salarayid] != '0')
	 								@php($sal_det_cnt += 1)
			 					@endif
			 				@endfor
			 			@endif
			 			@if($sal_det_cnt != '')
							<th rowspan="1" colspan="<?php echo $sal_det_cnt; ?>" class="vam">{{ trans('messages.lbl_salary_det') }}</th>
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
		 					<th rowspan="1" colspan="<?php echo $sal_ded_cnt; ?>" class="vam">{{ trans('messages.lbl_salary_ded') }}</th>
			 			@endif
						<th rowspan="2" class="vam">{{ trans('messages.lbl_totamt') }}</th>
						<th rowspan="2" class="vam">{{ trans('messages.lbl_transferred') }}</th>
					</tr>
					<tr>
						@if($tot_basic_amt != '')
		 				<th class="vam">
	 						{{ trans('messages.lbl_basic') }}
	 					</th>
	 					@endif
						@if($tot_hra_amt != '')
	 					<th class="vam">
	 						{{ trans('messages.lbl_HRA') }}
	 					</th>
	 					@endif
	 					@if($tot_train_amt != '')
	 					<th class="vam">
	 						{{ trans('messages.lbl_train_daily') }}
	 					</th>
	 					@endif
	 					@if($tot_exp_amt != '')
	 					<th class="vam">
	 						{{ trans('messages.lbl_Others') }}
	 					</th>
	 					@endif
						@if(count($salary_det)!="")
			 				@for ($i = 0; $i < count($salary_det); $i++)
			 					@if(isset($temp_salaryDetails[$salary_det[$i]->Salarayid]) && $temp_salaryDetails[$salary_det[$i]->Salarayid] != '0')
				 					<th class="vam">
				 						<label title="{{ (isset($salary_det[$i]->nick_name) && $salary_det[$i]->nick_name != '')?$salary_det[$i]->Name:'' }}" style="margin-bottom: 0px !important;">{{ (isset($salary_det[$i]->nick_name) && $salary_det[$i]->nick_name != '')?$salary_det[$i]->nick_name:$salary_det[$i]->Name }}</label>
				 					</th>
		 						@endif
			 				@endfor
		 				@endif
		 				@if(count($salary_ded)!="")
			 				@for ($j = 0; $j < count($salary_ded); $j++)
			 					@if(isset($temp_salaryDetails_DD[$salary_ded[$j]->Salarayid]) && $temp_salaryDetails_DD[$salary_ded[$j]->Salarayid] != '0')
				 					<th class="vam">
				 						<label title="{{ (isset($salary_ded[$j]->nick_name) && $salary_ded[$j]->nick_name != '')?$salary_ded[$j]->Name: ''}}" style="margin-bottom: 0px !important;">{{ (isset($salary_ded[$j]->nick_name) && $salary_ded[$j]->nick_name != '')?$salary_ded[$j]->nick_name:$salary_ded[$j]->Name }}</label>
				 					</th>
		 						@endif
			 				@endfor
		 				@endif
					</tr>
				</thead>
				<tbody>
					@if(count($get_det)!="")
			   		 @for ($i = 0; $i < count($get_det); $i++)
						<tr>
							<td class="text-center">
	                    		{{ ($g_query->currentpage()-1) * $g_query->perpage() + $i + 1 }}
	                    	</td>
	                    	<td class="tac">
	                    			{{ $get_det[$i]['year'].'-'.date('m',mktime(0, 0, 0, $get_det[$i]['month'])) }} 
				   			</td>
				   			<td class="tac">
				   				{{ $get_det[$i]['date'] }} 
				   			</td>
	                    	<?php
	                    		$arr1 = array();
	                    		$arr2 = array();
	                    		$sal_arr = array();
	                    		$val1 = '';
	                    		if ($get_det[$i]['Salary'] != '') {
				 					$Salary = explode('##', mb_substr($get_det[$i]['Salary'], 0, -2));
				 					foreach ($Salary as $key => $value) {
				 						$sal_final = explode('$', $value);
			 							$arr1[$key] = $sal_final[0];
			 							$arr2[$sal_final[0]] = $sal_final[1];
				 					}
	                    		}
	                    		if(count($salary_det) != "") {
		                    		foreach ($salary_det as $key1 => $value1) {
		                    			$sal_arr[$value1->Salarayid] = $value1->Salarayid;
		                    		}
	                    		}
	                    		$salresult_a=array_intersect($sal_arr,$arr1);
	                    		$salresult_b=array_diff($sal_arr,$arr1);
	                    		$salresult = array_merge($salresult_a,$salresult_b);
	                    		ksort($salresult);
				 			?>
				 			@if($tot_basic_amt != '')
				 			<td class="text-right pr10">
	                    		@php $val1 += $get_det[$i]['Basic'] @endphp
				 				{{ ($get_det[$i]['Basic'] != '') ? number_format($get_det[$i]['Basic']): '' }}
				 			</td>
				 			@endif
				 			@if($tot_hra_amt != '')
				 			<td class="text-right pr10">
	                    		@php $val1 += $get_det[$i]['hra'] @endphp
				 				{{ ($get_det[$i]['hra'] != '') ? number_format($get_det[$i]['hra']): '' }}
				 			</td>
				 			@endif
				 			@if($tot_train_amt != '')
				 			<td class="text-right pr10">
	                    		@php $val1 += $get_det[$i]['train_daily'] @endphp
				 				{{ ($get_det[$i]['train_daily'] != '') ? number_format($get_det[$i]['train_daily']): '' }}
				 			</td>
				 			@endif
				 			@if($tot_exp_amt != '')
				 			<td class="text-right pr10">
	                    		@php $val1 += $get_det[$i]['others'] @endphp
				 				{{ ($get_det[$i]['others'] != '') ? number_format($get_det[$i]['others']): '' }}
				 			</td>
				 			@endif
	                    	@if(count($salary_det)!="")
				 				@foreach ($salresult as $key2 => $value2)
					 				@if(isset($temp_salaryDetails[$key2]) && $temp_salaryDetails[$key2] != '0')
		                    			@if($key2 == isset($arr2[$key2]))
		                    			@php $val1 += $arr2[$key2] @endphp
		                    				<td class="text-right pr10"> {{ ($arr2[$key2] != '') ? number_format($arr2[$key2]): '' }}</td>
		                    			@else
		                    				<td></td>
		                    			@endif
	                    			@endif
	                    		@endforeach
			 				@endif
			 				<?php
	                    		$arr3 = array();
	                    		$arr4 = array();
	                    		$ded_arr = array();
	                    		$val2 = '';
	                    		if ($get_det[$i]['Deduction'] != '') {
				 					$Deduction = explode('##', mb_substr($get_det[$i]['Deduction'], 0, -2));
				 					foreach ($Deduction as $key => $value1) {
				 						$ded_final = explode('$', $value1);
			 							$arr3[$key] = $ded_final[0];
			 							$arr4[$ded_final[0]] = $ded_final[1];
				 					}
	                    		}
	                    		if(count($salary_ded) != "") {
		                    		foreach ($salary_ded as $key2 => $value2) {
		                    			$ded_arr[$value2->Salarayid] = $value2->Salarayid;
		                    		}
	                    		}
	                    		$dedresult_a=array_intersect($ded_arr,$arr3);
	                    		$dedresult_b=array_diff($ded_arr,$arr3);
	                    		$dedresult = array_merge($dedresult_a,$dedresult_b);
	                    		ksort($dedresult);
				 			?>
				 			@if(count($salary_ded)!="")
				 				@foreach ($dedresult as $key2 => $value2)
				 					@if(isset($temp_salaryDetails_DD[$key2]) && $temp_salaryDetails_DD[$key2] != '0')
		                    			@if($key2 == isset($arr4[$key2]))
		                    			@php $val2 += $arr4[$key2] @endphp
		                    				<td class="text-right pr10" style="color: red;"> {{ ($arr4[$key2] != '') ? number_format($arr4[$key2]) : '' }}</td>
		                    			@else
		                    				<td></td>
		                    			@endif
	                    			@endif
	                    		@endforeach
                			@else
                				<td></td>
			 				@endif
							<td class="text-right pr10">
								<?php 
									$calc = '0';
									$calc = $val1 + $val2;
								 ?>
								@if($calc != 0)
									{{ number_format($calc) }}
								@endif
							</td>
							<td class="text-right pr10">
								@if($get_det[$i]['Transferred'] != '')
									{{ number_format($get_det[$i]['Transferred']) }}
								@endif
							</td>
                    	</tr>
                    	@endfor
                    	@else
							<tr>
								@php($colspan = 6)
								@if(count($salary_det) == 0 && count($salary_ded) == 0)
									@php($colspan = 6)
								@elseif(count($salary_det) != 0 && count($salary_ded) == 0)
									@php($colspan = 5 + count($salary_det))
								@elseif(count($salary_det) == 0 && count($salary_ded) != 0)
									@php($colspan = 5 + count($salary_ded))
								@elseif(count($salary_det) != 0 && count($salary_ded) != 0)
									@php($colspan = 4 + count($salary_det) + count($salary_ded))
								@endif
								<td class="text-center fr" colspan="<?php echo $colspan; ?>">
								{{ trans('messages.lbl_nodatafound') }}</td>
							</tr>
						@endif
				</tbody>
			</table>
			@else
			<div class="text-center fr">{{ trans('messages.lbl_nodatafound') }}</div>
			@endif
		</div>
		@if(count($get_det)!="")
			<div class="text-center">
				@if(!empty($g_query->total()))
					<span class="pull-left mt24">
						{{ $g_query->firstItem() }} ~ {{ $g_query->lastItem() }} / {{ $g_query->total() }}
					</span>
				@endif 
				{{ $g_query->links() }}
				<div class="CMN_display_block flr">
					{{ $g_query->linkspagelimit() }}
				</div>
			</div>
		@endif
	</article>
</div>
@endsection