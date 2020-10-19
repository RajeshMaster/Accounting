@extends('layouts.app')
@section('content')
@php use App\Http\Helpers @endphp
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
        $('#multiadd_table tbody').scroll(function(e) { 
			$('#multiadd_table thead').css("left", -$("tbody").scrollLeft()); //fix the thead relative to the body scrolling
			$('#multiadd_table thead th:nth-child(1)').css("left", $("tbody").scrollLeft()); //fix the first cell of the header
			$('#multiadd_table thead th:nth-child(2)').css("left", $("tbody").scrollLeft()); //fix the second cell of the header
			$('#multiadd_table thead th:nth-child(3)').css("left", $("tbody").scrollLeft()); //fix the third cell of the header
			$('tbody td:nth-child(1)').css("left", $("tbody").scrollLeft()); //fix the first column of tdbody
			$('tbody td:nth-child(2)').css("left", $("tbody").scrollLeft()); //fix the second column of tdbody
			$('tbody td:nth-child(3)').css("left", $("tbody").scrollLeft()); //fix the third column of tdbody
		});

		// For Masterwise Total Calculation
			var totempcount = $('#plus_count').val();
			// For Total Amount
			for (var i = 0; i <= (totempcount-1); i++) {
				var sum = '';
	    		$('.sum'+i).each(function(){
					sum += Number($(this).text().replace(",", ""));
					sum = parseFloat(sum);
				});
				if (sum != '') {
					$("#totalspan"+i).text(parseInt(sum).toLocaleString());
				}
	    	}
			// For Total Amount Difference
			for (var i = 0; i <= (totempcount-1); i++) {
				var transfer = '';
				var sum_sub = '';
				var sum_diff = '';
				var transfer = $('#transfertd'+i).text().replace(",", "").replace(",", "").replace(/\s\s+/g, ' ');
				var sum_sub = $('#totalspan'+i).text().replace(",", "").replace(",", "").replace(/\s\s+/g, ' ');
				var sum_diff = transfer - sum_sub;
				if (sum_diff != '') {
					$("#amtdiff"+i).text(parseInt(sum_diff).toLocaleString());
				}
	    	}

        // For Salary Details
		/*var totdetcount = $('#sal_det_count').val();
		for (var i = 0; i <= totdetcount; i++) {
			var sum = '';
    		$('.SD_'+i).each(function(){
				sum += Number($(this).text().replace(",", "").replace(",", ""));
				sum = parseFloat(sum);
			});
			if (sum != '') {
				$("#totalSDspan"+i).text(parseInt(sum).toLocaleString());
			}
    	}*/

    	// For Salary Deduction
		/*var totdedcount = $('#sal_ded_count').val();
		for (var j = 0; j < totdedcount; j++) {
			var sum1 = '';
    		$('.DD_'+j).each(function(){
				sum1 -= parseFloat(Number($(this).text().replace(",", "").replace(",", "")));
			});
			if (sum1 != '') {
				$("#totalDDspan"+j).text('-'+parseInt(sum1).toLocaleString());
			}
    	}*/
	});
	$(window).bind('resize', function () { 
  		var ht;
  		var lastWindowHeight = $(window).height();
        ht= $(window).height();
        if(ht!=lastWindowHeight) {
        	$("#sidebar").height(ht);
        }
	});
</script>
<style type="text/css">
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
	table td.multiadd_td {
		padding: 3px;
	}

	table#multiadd_table {
	  position: relative;
	  width: auto;
	  /*min-width: 1184px;*/
	  /*max-width: 358px;*/
	  max-width: 1184px;
	  overflow: hidden;
	  border-collapse: collapse;
	}


	/*thead*/
	thead#multiadd_thead {
	  position: relative;
	  display: block; /*seperates the header from the body allowing it to be positioned*/
	  width: 1184px;
	  max-width: 358px;
	  overflow: visible;
	}

	thead th.multiadd_th {
	  min-width: 120px;
	  height: 32px;
	}


	/*tbody*/
	tbody#multiadd_tbody {
	  position: relative;
	  display: block; /*seperates the tbody from the header*/
	  /*width: 1184px;*/
	  min-width: 358px;
	  max-width: 1184px;
	  min-height: 50px;
	  /*max-height: 350px;*/
	  max-height: 525px;
	  overflow: auto;
	}

	tbody td.multiadd_td, thead th.multiadd_th 	{
	  width: 120px;
	  min-width: 120px;
	}

	thead th.w_1, tbody td.w_1 {
		width: 40px;
		min-width: 40px;
	}
	thead th.w_2, tbody td.w_2 {
		width: 75px;
		min-width: 75px;
	}

	th.sticky {
		position: -webkit-sticky;
		position: relative;
		left: 0;
		z-index: 2;
		background: #ccc;
	}
	thead th.sticky {
		z-index: 5;
	}
	td.sticky {
		position: -webkit-sticky;
		position: relative;
		left: 0;
		z-index: 2;
		background: #ccc;
	}
	tbody td.sticky {
		z-index: 5;
	}

	th.sticky1 {
		position: -webkit-sticky;
		position: relative;
		left: 0;
		z-index: 2;
		background: #ccc;
	}
	thead th.sticky1 {
		z-index: 5;
	}
	td.sticky1 {
		position: -webkit-sticky;
		position: relative;
		left: 0;
		z-index: 2;
		background: #ccc;
	}
	tbody td.sticky1 {
		z-index: 5;
	}

	th.sticky2 {
		position: -webkit-sticky;
		position: relative;
		left: 0;
		z-index: 2;
		background: #ccc;
	}
	thead th.sticky2 {
		z-index: 5;
	}
	td.sticky2 {
		position: -webkit-sticky;
		position: relative;
		left: 0;
		z-index: 2;
		background: #ccc;
	}
	tbody td.sticky2 {
		z-index: 5;
	}
</style>
<div class="CMN_display_block" id="main_contents" style="width: 100%;">
	<!-- article to select the main&sub menu -->
	<article id="auditing" class="DEC_flex_wrapper " data-category="auditing auditing_sub_2">
		{{ Form::open(array('name'=>'salarycalcindex',
							'id'=>'salarycalcindex',
							'url'=>'salarycalc/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
							'files'=>true,
							'method' => 'POST' )) }}
			{{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
			{{ Form::hidden('tblchg', $request->tblchg , array('id' => 'tblchg')) }}
			{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
			{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
			{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
			{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
			{{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}
			{{ Form::hidden('previou_next_year', $request->previou_next_year, 
								array('id' => 'previou_next_year')) }}
			{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
			{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
			{{ Form::hidden('hiddenplimit', '' , array('id' => 'hiddenplimit')) }}
	    	{{ Form::hidden('hiddenpage', '' , array('id' => 'hiddenpage')) }}
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
			{{ Form::hidden('multiflg_reg', '' , array('id' => 'multiflg_reg')) }}
			{{ Form::hidden('sal_det_count', count($salary_det) , array('id' => 'sal_det_count')) }}
			{{ Form::hidden('sal_ded_count', count($salary_ded) , array('id' => 'sal_ded_count')) }}
			{{ Form::hidden('get_prev_yr', '' , array('id' => 'get_prev_yr')) }}
			{{ Form::hidden('plus_count', count($get_det) , array('id' => 'plus_count')) }}

		<!-- Start Heading -->
		<div class="row hline pm0">
				<div class="col-xs-12">
					<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/salary_1.png') }}">
					<h2 class="pull-left pl5 mt10">
						{{ trans('messages.lbl_salary_calc') }}
					</h2>
					<div class="mt25" style="float: right">
						@if($request->tblchg == 1)
							<a href="javascript:gotoemployeewise();">{{ trans('messages.lbl_empwise') }}</a>
						@else
							<a href="javascript:gotomaster();">{{ trans('messages.lbl_mastwise') }}</a>
						@endif
					</div>
				</div>
			</div>
		<!-- End Heading -->	
		<div class="box100per pr10 pl10 ">
			<div class="mt10 mb10">
				{{ Helpers::displayYear_MonthEst($account_period, $year_month, $db_year_month, $date_month, $dbnext, $dbprevious, $last_year, $current_year, $account_val) }}
			</div>
		</div>
		<div class="col-xs-12 mt5 pm0 pull-left pl10">
			<!-- Session msg -->
			@if(Session::has('success'))
				<div align="center" class="alertboxalign" role="alert">
					<p class="alert {{ Session::get('alert', Session::get('type') ) }}">
		            	{{ Session::get('success') }}
		          	</p>
				</div>
			@endif
			<!-- Session msg -->
			
			@php
				$salExist = ($tot_transferred_amt > 0) ? 1 : 0;
			@endphp
			<div style="display: inline-block;" class="mr10 mb10 pull-right">
				<a href="javascript:salarydownload({{ $salExist }});" class="btn btn-primary" title="Download" style="color: white;"><span class="fa fa-download"></span>
					{{ trans('messages.lbl_download') }}
				</a>
			</div>
		</div>
		@if($request->tblchg == 0)
			<table id="multiadd_table" class="tablealternate">
				
				<thead id="multiadd_thead"  class="CMN_tbltheadcolor">
					<tr>
						<th rowspan="3" class="vam sticky w_1 multiadd_th">{{ trans('messages.lbl_sno') }}</th>
						<th rowspan="3" class="vam sticky1 w_2 multiadd_th">{{ trans('messages.lbl_empno') }}</th>
						<th rowspan="3" class="vam sticky2 multiadd_th" style="min-width: 205px;">{{ trans('messages.lbl_name') }}</th>
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
			 					@if($temp_salaryDetails[$salary_det[$i]->Salarayid] != '0')
	 								@php($sal_det_cnt += 1)
			 					@endif
			 				@endfor
			 			@endif
	 					@if($sal_det_cnt != '')
						<th rowspan="1" colspan="<?php echo $sal_det_cnt; ?>" class="vam multiadd_th">{{ trans('messages.lbl_salary_det') }}</th>
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
		 				<th rowspan="1" colspan="<?php echo $sal_ded_cnt; ?>" class="vam multiadd_th">{{ trans('messages.lbl_salary_ded') }}</th>
			 			@endif
						<th rowspan="2" colspan="1" class="vam multiadd_th">{{ trans('messages.lbl_amount') }}</th>
						<th rowspan="2" colspan="1" class="vam multiadd_th">{{ trans('messages.lbl_transferred') }}</th>
						<th rowspan="2" colspan="1" class="vam multiadd_th" title="{{ trans('messages.lbl_amt_differ') }}">{{ trans('messages.lbl_amt_dif') }}</th>
						<th rowspan="3" class="vam multiadd_th" style="min-width: 40px;">{{ Form::checkbox('checkall', 1,'',['id' => 'checkall']) }}</th>
					</tr>
					<tr>
						@if($tot_basic_amt != '')
		 				<th class="vam multiadd_th">
	 						{{ trans('messages.lbl_basic') }}
	 					</th>
	 					@endif
						@if($tot_hra_amt != '')
	 					<th class="vam multiadd_th">
	 						{{ trans('messages.lbl_HRA') }}
	 					</th>
	 					@endif
	 					@if($tot_train_amt != '')
	 					<th class="vam multiadd_th">
	 						{{ trans('messages.lbl_train_daily') }}
	 					</th>
	 					@endif
	 					@if($tot_exp_amt != '')
	 					<th class="vam multiadd_th">
	 						{{ trans('messages.lbl_Others') }}
	 					</th>
	 					@endif
						@if(count($salary_det)!="")
			 				@for ($i = 0; $i < count($salary_det); $i++)
			 					@if($temp_salaryDetails[$salary_det[$i]->Salarayid] != '0')
				 					<th class="vam multiadd_th">
				 						<label title="{{ (isset($salary_det[$i]->nick_name) && $salary_det[$i]->nick_name != '')?$salary_det[$i]->Name: ''}}" style="margin-bottom: 0px !important;">{{ (isset($salary_det[$i]->nick_name) && $salary_det[$i]->nick_name != '')?$salary_det[$i]->nick_name:$salary_det[$i]->Name }}</label>
				 					</th>
		 						@endif
			 				@endfor
		 				@endif
		 				@if(count($salary_ded)!="")
			 				@for ($j = 0; $j < count($salary_ded); $j++)
			 					@if(isset($temp_salaryDetails_DD[$salary_ded[$j]->Salarayid]) && $temp_salaryDetails_DD[$salary_ded[$j]->Salarayid] != '0')
				 					<th class="vam multiadd_th">
				 						<label title="{{ (isset($salary_ded[$j]->nick_name) && $salary_ded[$j]->nick_name != '')?$salary_ded[$j]->Name: ''}}" style="margin-bottom: 0px !important;">{{ (isset($salary_ded[$j]->nick_name) && $salary_ded[$j]->nick_name != '')?$salary_ded[$j]->nick_name:$salary_ded[$j]->Name }}</label>
				 					</th>
		 						@endif
			 				@endfor
		 				@endif
					</tr>
					<tr>
						@php $tot_over_amt = '' @endphp
						@if($tot_basic_amt != '')
						<th class="vam multiadd_th" style="color:blue;">
							@php $tot_over_amt += $tot_basic_amt @endphp
	 						{{ number_format($tot_basic_amt) }}
						</th>
		 				@endif
						@if($tot_hra_amt != '')
						<th class="vam multiadd_th" style="color:blue;">
							@php $tot_over_amt += $tot_hra_amt @endphp
	 						{{ number_format($tot_hra_amt) }}
						</th>
		 				@endif
		 				@if($tot_train_amt != '')
		 				<th class="vam multiadd_th" style="color:blue;">
							@php $tot_over_amt += $tot_train_amt @endphp
	 						{{ number_format($tot_train_amt) }}
	 					</th>
		 				@endif
	 					@if($tot_exp_amt != '')
	 					<th class="vam multiadd_th" style="color:blue;">
							@php $tot_over_amt += $tot_exp_amt @endphp
 							{{ number_format($tot_exp_amt) }}
	 					</th>
		 				@endif
						@if(count($salary_det)!="")
			 				@for ($i = 0; $i < count($salary_det); $i++)
			 					@if(count($get_det)!="")
				 					@if($temp_salaryDetails[$salary_det[$i]->Salarayid] != '0')
					 					<th class="vam multiadd_th">
					 						<span id="totalSDspan<?php echo $i ?>" class="text-right multiadd_td" style="color:blue;">
												@php $tot_over_amt += $temp_salaryDetails[$salary_det[$i]->Salarayid] @endphp
						 						{{ number_format($temp_salaryDetails[$salary_det[$i]->Salarayid]) }}
			   		 						</span>
					 					</th>
				 					@endif
			 					@endif
			 				@endfor
		 				@endif
		 				@if(count($salary_ded)!="")
			 				@for ($j = 0; $j < count($salary_ded); $j++)
			 					@if(count($get_det)!="")
				 					@if(isset($temp_salaryDetails_DD[$salary_ded[$j]->Salarayid]) && $temp_salaryDetails_DD[$salary_ded[$j]->Salarayid] != '0')
					 					<th class="vam multiadd_th">
					 						<span id="totalDDspan<?php echo $j ?>" class="text-right multiadd_td" style="color:red;">
												@php $tot_over_amt += $temp_salaryDetails_DD[$salary_ded[$j]->Salarayid] @endphp
						 						{{ number_format($temp_salaryDetails_DD[$salary_ded[$j]->Salarayid]) }}
			   		 						</span>
					 					</th>
			 						@endif
			 					@endif
			 				@endfor
			 			@else
			 				<th class="vam multiadd_th" style="min-width: 287px;"></th>
		 				@endif
	 					<th class="vam multiadd_th" style="color:blue;">
	 						@if($tot_over_amt != '')
	 							{{ number_format($tot_over_amt) }}
		 					@endif
	 					</th>
	 					<th class="vam multiadd_th" style="color:blue;">
	 						@if($tot_transferred_amt != '')
	 							{{ number_format($tot_transferred_amt) }}
		 					@endif
	 					</th>
	 					<th class="vam multiadd_th" style="color:blue;">
	 						@if($tot_transferred_amt != '' || $tot_over_amt != '')
		 						@php
		 							$amt_diff = '';
	                                $amt_diff = $tot_transferred_amt - $tot_over_amt; 
	                            @endphp
	                            @if($amt_diff != 0)
		 							{{ number_format($amt_diff) }}
			 					@endif
		 					@endif
	 					</th>
					</tr>
				</thead>
				<tbody id="multiadd_tbody">
					@if(count($get_det)!="")
			   		 @for ($i = 0; $i < count($get_det); $i++)
						<tr>
							<td class="text-center sticky w_1 multiadd_td">
	                    		{{ $i + 1 }}
	                    	</td>
	                    	<td class="sticky1 tac multiadd_td w_2">
	                    		<a class="colbl fwb anchorstyle" href="javascript:fngotohistory('{{ $get_det[$i]['Emp_ID'] }}','{{ $request->mainmenu }}','{{ $get_det[$i]['FirstName'] }}','{{ $get_det[$i]['LastName'] }}')">
	                    			{{ $get_det[$i]['Emp_ID'] }}
	                    		</a>
				   			</td>
	                    	<td class="sticky2 multiadd_td" style="min-width: 205px;">
	                    		@if($get_det[$i]['editcheck'] != 0)
	                    			<a class="colbl anchorstyle"  href="javascript:fngotoadd('{{ $get_det[$i]['id'] }}','{{ $get_det[$i]['Emp_ID'] }}','{{ $get_det[$i]['editcheck'] }}','{{ $request->mainmenu }}','{{ $get_det[$i]['FirstName'] }}','{{ $get_det[$i]['LastName'] }}');" title="{{ empnameontitle($get_det[$i]['LastName'], $get_det[$i]['FirstName'],50) }}">{{ empnamelength($get_det[$i]['LastName'], $get_det[$i]['FirstName'],10) }}
	                    			</a>
	                    		@else
		                    		<span class="colbl" title="{{ empnameontitle($get_det[$i]['LastName'], $get_det[$i]['FirstName'],50) }}"> 
		                    			{{ empnamelength($get_det[$i]['LastName'], $get_det[$i]['FirstName'],10) }}
		                    		</span>
		                    	@endif
	                    	</td>
	                    	<?php $val1 = ''; ?>
			 				@if($tot_basic_amt != '')
			 				<td class="text-right pr10 multiadd_td">
			 					@if($get_det[$i]['Basic'] != '')
			 						@php $val1 += $get_det[$i]['Basic'] @endphp
									{{ number_format($get_det[$i]['Basic']) }}
								@endif
			 				</td>
							@endif
			 				@if($tot_hra_amt != '')
			 				<td class="text-right pr10 multiadd_td">
			 					@if($get_det[$i]['hra'] != '')
			 						@php $val1 += $get_det[$i]['hra'] @endphp
									{{ number_format($get_det[$i]['hra']) }}
								@endif
			 				</td>
							@endif
			 				@if($tot_train_amt != '')
			 				<td class="text-right pr10 multiadd_td">
			 					@if($get_det[$i]['train_daily'] != '')
			 						@php $val1 += $get_det[$i]['train_daily'] @endphp
									{{ number_format($get_det[$i]['train_daily']) }}
								@endif
			 				</td>
							@endif
	 						@if($tot_exp_amt != '')
			 				<td class="text-right pr10 multiadd_td">
			 					@if($get_det[$i]['others'] != '')
			 						@php $val1 += $get_det[$i]['others'] @endphp
									{{ number_format($get_det[$i]['others']) }}
								@endif
			 				</td>
							@endif
	                    	<?php
	                    		$arr1 = array();
	                    		$arr2 = array();
	                    		$sal_arr = array();
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
	                    	@if(count($salary_det)!="")
	                    		@php($x = 0)
				 				@foreach ($salresult as $key2 => $value2)
					 				@if($temp_salaryDetails[$key2] != '0')
		                    			@if($key2 == isset($arr2[$key2]))
		                    			@php $val1 += $arr2[$key2] @endphp
		                    				<td class="text-right pr10 multiadd_td SD_{{ $x }}"> {{ ($arr2[$key2] != '') ? number_format($arr2[$key2]): '' }}</td>
		                    			@else
		                    				<td class="text-right pr10 multiadd_td"></td>
		                    			@endif
		                    		@endif
	                    			@php($x++)
	                    		@endforeach
                    		@else
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
				 				@php($y = 0)
				 				@foreach ($dedresult as $key2 => $value2)
				 					@if(isset($temp_salaryDetails_DD[$key2]) && $temp_salaryDetails_DD[$key2] != '0')
		                    			@if($key2 == isset($arr4[$key2]))
		                    			@php $val2 += $arr4[$key2] @endphp
		                    				<td class="text-right pr10 multiadd_td DD_{{ $y }}" style="color: red;"> {{ ($arr4[$key2] != '') ? number_format($arr4[$key2]) : '' }}</td>
		                    			@else
		                    				<td class="text-right pr10 multiadd_td" style="color: red;"></td>
		                    			@endif
	                    			@endif
	                    			@php($y++)
	                    		@endforeach
                			@else
                				<td class="multiadd_td" style="min-width: 287px;"></td>
			 				@endif
							<td class="text-right pr10 multiadd_td">
								<?php 
									$calc = '0';
									$calc = $val1 + $val2;
								 ?>
								@if($get_det[$i]['Salary'] != '' || $get_det[$i]['Deduction'] != '' || $get_det[$i]['Basic'] != '' || $get_det[$i]['hra'] != '' || $get_det[$i]['train_daily'] != '' || $get_det[$i]['others'] != '')
									{{ number_format($calc) }}
								@endif
							</td>
							<td class="text-right pr10 multiadd_td">
								@if($get_det[$i]['Transferred'] != '')
									{{ number_format($get_det[$i]['Transferred']) }}
								@endif
							</td>
							<td class="text-right pr10 multiadd_td">
								@if($get_det[$i]['Transferred'] != '' || $calc != '')
	                    			@php $emp_amt_diff = $get_det[$i]['Transferred'] - $calc; @endphp
	                    			@if($emp_amt_diff != 0)
										{{ number_format($emp_amt_diff) }}
									@endif
								@endif
							</td>
							<td class="tac multiadd_td" style="width: 40px;min-width: 40px;">
								@if($get_det[$i]['mail_need'] == 0)
									@if($get_det[$i]['Transferred'] != '')
										@if($get_det[$i]['mailFlg'] == 0)
											{{ Form::checkbox('salarycheck', $get_det[$i]['Emp_ID'],'',['id' => 'salarycheck','class' => 'checkbox','style' => 'display:inline-block','data-name-empid' => $get_det[$i]['LastName'],'data-mailflg' => $get_det[$i]['mailFlg']]) }}
										@else
											<img class="box18 mt1" id="pdfimg" src="{{ URL::asset('resources/assets/images/sendmail.png') }}">
										@endif
									@endif
								@endif
							</td>
                    	</tr>
                    	@endfor
                    	@else
							<tr>
								@php($colspan = 9)
								@if(count($salary_det) == 0 && count($salary_ded) == 0)
									@php($colspan = 9)
								@elseif(count($salary_det) != 0 && count($salary_ded) == 0)
									@php($colspan = 8 + count($salary_det))
								@elseif(count($salary_det) == 0 && count($salary_ded) != 0)
									@php($colspan = 8 + count($salary_ded))
								@elseif(count($salary_det) != 0 && count($salary_ded) != 0)
									@php($colspan = 7 + count($salary_det) + count($salary_ded))
								@endif
								<td class="text-center fr" colspan="<?php echo $colspan; ?>" style="color: red;width: 1300px;">
								{{ trans('messages.lbl_nodatafound') }}</td>
							</tr>
						@endif
				</tbody>
			</table>
		@else
			<table id="multiadd_table" class="tablealternate">
				<thead id="multiadd_thead"  class="CMN_tbltheadcolor">
					<tr>
						<th rowspan="2" class="vam sticky multiadd_th"></th>
						<th rowspan="2" class="vam sticky1 multiadd_th"></th>
						@if(count($get_det)!="")
					   		@for ($i = 0; $i < count($get_det); $i++)
					   		<th class="vam multiadd_th">
					   			<a class="colbl fwb anchorstyle" href="javascript:fngotohistory('{{ $get_det[$i]['Emp_ID'] }}','{{ $request->mainmenu }}','{{ $get_det[$i]['FirstName'] }}','{{ $get_det[$i]['LastName'] }}')">
					   				{{ $get_det[$i]['Emp_ID'] }}
					   			</a>
					   		</th>
					   		@endfor
				   		@endif
					</tr>
					<tr>
						@if(count($get_det)!="")
					   		@for ($i = 0; $i < count($get_det); $i++)
					   		<th class="vam multiadd_th">
					   			@if($get_det[$i]['editcheck'] != 0)
						   			<a class="colbl anchorstyle"  href="javascript:fngotoadd('{{ $get_det[$i]['id'] }}','{{ $get_det[$i]['Emp_ID'] }}','{{ $get_det[$i]['editcheck'] }}','{{ $request->mainmenu }}','{{ $get_det[$i]['FirstName'] }}','{{ $get_det[$i]['LastName'] }}');" title="{{ empnameontitle($get_det[$i]['LastName'], $get_det[$i]['FirstName'],50) }}">
		                    			{{ empnamelength($get_det[$i]['LastName'], $get_det[$i]['FirstName'],10) }}
		                    		</a>
	                    		@else
		                    		<span class="colbl" title="{{ empnameontitle($get_det[$i]['LastName'], $get_det[$i]['FirstName'],50) }}"> 
		                    			{{ empnamelength($get_det[$i]['LastName'], $get_det[$i]['FirstName'],10) }}
		                    		</span>
		                    	@endif
					   		</th>
					   		@endfor
				   		@endif
					</tr>
				</thead>
				<tbody id="multiadd_tbody">
					@php($mast_tot_amt = '')
					@if(count($main_array)!="")
			   			@foreach ($main_array as $key => $value)
			   				@php($key_name = explode('$',$key))
			   				@if($key_name[2] == 0)
			   					@php($style_head = 'color:blue;font-weight:bold;')
			   					@php($style_body = '')
			   				@else
			   					@php($style_head = 'color:red;font-weight:bold;')
			   					@php($style_body = 'color:red;')
			   				@endif
				   			@if($key_name[3] != 0)
				   			<tr>
				   				<td class="tac sticky multiadd_td" style="">
				   					<label title="{{ (isset($key_name[4]) && $key_name[4] != '')?$key_name[0]: ''}}" style="margin-bottom: 0px !important;font-weight: 100">{{ (isset($key_name[4]) && $key_name[4] != '')?$key_name[4]:$key_name[0] }}</label>
				   				</td>
				   				<td class="tac sticky1 multiadd_td" style="{{ $style_head }}">
				   					@if($key_name[3] != 0)
				   						@php($mast_tot_amt += $key_name[3])
				   						{{ number_format($key_name[3]) }}
				   					@endif
				   				</td>
				   				@foreach ($value as $key1 => $value1)
				   					<td class="text-right pr10 multiadd_td sum<?php echo $key1; ?>" style="{{ $style_body }}">
				   						{{ $value1 }}
				   					</td>
				   				@endforeach
				   			</tr>
				   			@endif
			   			@endforeach
			   		@endif
	 				<tr>
	 					<td class="tac sticky multiadd_td">{{ trans('messages.lbl_amount') }}</td>
	 					<td class="tac sticky1 multiadd_td" style="color: blue;font-weight: bold;">
	 						@php($tot_calc_diff1 = '')
	 						@if($mast_tot_amt != 0 || $mast_tot_amt != '')
	 							@php($tot_calc_diff1 = $mast_tot_amt)
	 							{{ number_format($mast_tot_amt)  }}
	   		 				@endif
		 				</td>
	 					@if(count($get_det)!="")
	   		 				@for ($l = 0; $l < count($get_det); $l++)
	   		 					<td id="totalspan<?php echo $l; ?>" class="text-right pr10 multiadd_td">
	   		 						
	   		 					</td>
	   		 				@endfor
	   		 			@endif
	 				</tr>
	 				<tr>
	 					<td class="tac sticky multiadd_td">{{ trans('messages.lbl_transferred') }}</td>
	 					<td class="tac sticky1 multiadd_td" style="color: blue;font-weight: bold;">
	 						@php($tot_calc_diff2 = '')
	 						@if($tot_transferred_amt != '')
	 							@php($tot_calc_diff2 = $tot_transferred_amt)
	 							{{ number_format($tot_transferred_amt) }}
		 					@endif
		 				</td>
	 					@if(count($get_det)!="")
	   		 				@for ($l = 0; $l < count($get_det); $l++)
	   		 					<td id="transfertd<?php echo $l; ?>" class="text-right pr10 multiadd_td">
	   		 						@if($get_det[$l]['Transferred'] != '')
	   		 							{{ number_format($get_det[$l]['Transferred']) }}
	   		 						@endif
	   		 					</td>
	   		 				@endfor
	   		 			@endif
	 				</tr>
	 				<tr>
	 					<td class="tac sticky multiadd_td" title="{{ trans('messages.lbl_amt_differ') }}">{{ trans('messages.lbl_amt_dif') }}</td>
	 					<td class="tac sticky1 multiadd_td" style="color: blue;font-weight: bold;">
	 						@php $tot_calc_diff = $tot_calc_diff2 - $tot_calc_diff1; @endphp
                			@if($tot_calc_diff != 0)
								{{ number_format($tot_calc_diff) }}
							@endif
		 				</td>
	 					@if(count($get_det)!="")
	   		 				@for ($l = 0; $l < count($get_det); $l++)
	   		 					<td id="amtdiff<?php echo $l; ?>" class="text-right pr10 multiadd_td">
	   		 						
	   		 					</td>
	   		 				@endfor
	   		 			@endif
	 				</tr>
	 				<tr>
	 					<td colspan="2" class="tac sticky multiadd_td">{{ Form::checkbox('checkall', 1,'',['id' => 'checkall']) }}</td>
	 					@if(count($get_det)!="")
	   		 				@for ($l = 0; $l < count($get_det); $l++)
	   		 					<td class="tac multiadd_td">
	   		 						@if($get_det[$l]['mail_need'] == 0)
		   		 						@if($get_det[$l]['Transferred'] != '')
		   		 							@if($get_det[$l]['mailFlg'] == 0)
												{{ Form::checkbox('salarycheck', $get_det[$l]['Emp_ID'],'',['id' => 'salarycheck','class' => 'checkbox','style' => 'display:inline-block','data-name-empid' => $get_det[$l]['LastName'],'data-mailflg' => $get_det[$l]['mailFlg']]) }}
											@else
												<img class="box18 mt1" id="pdfimg" src="{{ URL::asset('resources/assets/images/sendmail.png') }}">
											@endif
		   		 						@endif
	   		 						@endif
	   		 					</td>
	   		 				@endfor
	   		 			@endif
	 				</tr>
				</tbody>
			</table>
		@endif
				
	{{ Form::close() }}

	<!-- Madasamy - 07/08/2020 -->
	{{ Form::open(array('name'=>'salcalcindexExcel',
							'id'=>'salcalcindexExcel',
							'url'=>'salarycalc/salarydownload?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
							'files'=>true,
							'method' => 'POST' )) }}
			{{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
			{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
			{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
			{{ Form::hidden('get_prev_yr', '' , array('id' => 'get_prev_yr')) }}
	{{ Form::close() }}


	<div id="salarypopup" class="modal fade">
		<div id="login-overlay">
			<div class="modal-content">
				<!-- Popup will be loaded here -->
			</div>
		</div>
	</div>
	<style type="text/css">
		.modal {

		    position: fixed;

		    top: 50% !important;

		    left: 50%;

		    transform: translate(-50%, -50%);

		}
	</style>
	<div id="mailsendconfirmpopup" class="modal fade">
		<div id="login-overlay">
			<div class="modal-content">
				<!-- Popup will be loaded here -->
				<div class="modal-content">

				    <div class="modal-header" style="padding: 3px !important;">

				        <div class="col-md-12" style="padding-left: 15px;">

				          <h2 style="font-size:30px;" class="modal-title custom_align">{{ trans('messages.lbl_sel_emp_name') }}</h2>

				        </div>

				    </div>
				    <div class="modal-body" style="padding: 1px !important;">
				    	<div class="col-xs-12 mt10 mb10" id="sel_mem" style="display: none;">
				    		<div class="pr10" style="width: 36%;display: inline-block;text-align: right;font-weight: bold;">{{ trans('messages.lbl_employee_selected') }}</div>
				    		<div class="flr" style="width: 63%;display: inline-block;">
				    			<span id="span_sel_mem" style="word-wrap: break-word;"></span>
				    		</div>
				    	</div>
				    	<div class="col-xs-12 mt10 mb10" id="sent_mem" style="display: none;">
				    		<div class="pr10" style="width: 36%;display: inline-block;text-align: right;font-weight: bold;">{{ trans('messages.lbl_already_sent_emp_name') }}</div>
				    		<div class="flr" style="width: 63%;display: inline-block;">
				    			<span id="span_sent_mem" style="word-wrap: break-word;"></span>
				    		</div>
				    	</div>
				    	<div class="col-xs-12 mt10 mb20" id="sent_mem_caution" style="display: none;">
				    		<span style="color: red;">Note :</span> Already Mail Sent Employees Names cannot send mail.
				    	</div>
				    	<center class="mt10 mb10">

				          <button id="index_send_mail_btn"

				                  onclick="javascript:sendmail();"

				                  class="btn btn-success CMN_display_block box100">{{ trans('messages.lbl_sendmail') }}

				          </button>

				          <button data-dismiss="modal"

				           class="btn btn-danger CMN_display_block box100">{{ trans('messages.lbl_close') }}

				          </button>


				        </center>
				    </div>
			    </div>
			</div>
		</div>
	</div>
	</article>
</div>
@endsection