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
</style>
<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_11">
		{{ Form::open(array('name'=>'salarycalcindex',
							'id'=>'salarycalcindex',
							'url'=>'salarycalc/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
							'files'=>true,
							'method' => 'POST' )) }}
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

		<!-- Start Heading -->
		<div class="row hline pm0">
				<div class="col-xs-12">
					<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/salary_1.png') }}">
					<h2 class="pull-left pl5 mt10">
						{{ trans('messages.lbl_salary_calc') }}
					</h2>
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
			<a class="pull-left" href="javascript:salaryselectpopup();">
	          	<img class="box19" src="{{ URL::asset('resources/assets/images/edit.png') }}"></a>
				<a href="javascript:salaryselectpopup_main();" class="pull-left pr10 ml5 anchorstyle" title="{{ trans('messages.lbl_cempsel') }}">
				{{ trans('messages.lbl_cempsel') }}
			</a>

			<div style="display: inline-block;" class="mr10 mb10 pull-right">
				<a href="javascript:sendmail();" class="btn btn-primary" title="Multiple Register" style="color: white;">
					{{ trans('messages.lbl_sendmail') }}
				</a>
			</div>
			
			<div style="display: inline-block;" class="mr10 mb10 pull-right">
				<a href="javascript:multi_reg_calc();" class="btn btn-success" title="Multiple Register" style="color: white;">
					{{ trans('messages.lbl_multi_register') }}
				</a>
			</div>
		</div>
		<div class="minh400 box100per pl10 pr10 mt10">
			<div style="border: 1px solid white;overflow-x: auto;" id="sidebar">
				@php $count = 58; @endphp
				@if(count($salary_det)!="")
	 				@for ($i = 0; $i < count($salary_det); $i++)
	 					@php $count += 13; @endphp
	 				@endfor
 				@endif
 				@if(count($salary_ded)!="")
	 				@for ($i = 0; $i < count($salary_ded); $i++)
	 					@php $count += 13; @endphp
	 				@endfor
 				@endif
 				@if($count<100)
 					@php $count = 100; @endphp
 				@endif
			<table class="tablealternate CMN_tblfixed" style="width: <?php echo $count; ?>% !important;">
				<colgroup>
					<col width="4%">
					<col width="7%">
					<col width="18%">
					@if(count($salary_det)!="")
		 				@for ($i = 0; $i < count($salary_det); $i++)
		 					<col width="13%">
		 				@endfor
		 			@else
		 				<col width="13%">
	 				@endif
	 				@if(count($salary_ded)!="")
		 				@for ($i = 0; $i < count($salary_ded); $i++)
		 					<col width="13%">
		 				@endfor
	 				@else
	 					<col width="13%">
	 				@endif
					<col width="13%">
					<col width="13%">
					<col width="3%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
					<tr>
						<th rowspan="2" class="vam">{{ trans('messages.lbl_sno') }}</th>
						<th rowspan="2" class="vam">{{ trans('messages.lbl_empno') }}</th>
						<th rowspan="2" class="vam">{{ trans('messages.lbl_name') }}</th>
						<th rowspan="1" colspan="<?php echo count($salary_det); ?>" class="vam">{{ trans('messages.lbl_salary_det') }}</th>
		 				<th rowspan="1" colspan="<?php echo count($salary_ded); ?>" class="vam">{{ trans('messages.lbl_salary_ded') }}</th>
						<th rowspan="2" class="vam">{{ trans('messages.lbl_totamt') }}</th>
						<th rowspan="2" class="vam">{{ trans('messages.lbl_transferred') }}</th>
						<th rowspan="2" class="vam">{{ Form::checkbox('checkall', 1,'',['id' => 'checkall']) }}</th>
					</tr>
					<tr>
						@if(count($salary_det)!="")
			 				@for ($i = 0; $i < count($salary_det); $i++)
			 					<th class="vam">
			 						{{ $salary_det[$i]->Name }}
			 						</br>
			 						{{ Form::checkbox('salarycheckid', $salary_det[$i]->Salarayid,'',['id' => 'salarycheckid','class' => 'checkboxid','style' => 'display:inline-block']) }}
			 					</th>
			 				@endfor
		 				@endif
		 				@if(count($salary_ded)!="")
			 				@for ($j = 0; $j < count($salary_ded); $j++)
			 					<th class="vam">
			 						{{ $salary_ded[$j]->Name }}
			 						</br>
			 						{{ Form::checkbox('dedcheckid', $salary_ded[$j]->Salarayid,'',['id' => 'dedcheckid','class' => 'checkboxid','style' => 'display:inline-block']) }}
			 					</th>
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
	                    		<a class="colbl fwb anchorstyle" href="#">
	                    			{{ $get_det[$i]['Emp_ID'] }}
	                    		</a>
				   			</td>
	                    	<td>
	                    		<a class="colbl anchorstyle"  href="javascript:fngotoadd('{{ $get_det[$i]['id'] }}','{{ $get_det[$i]['Emp_ID'] }}','{{ $get_det[$i]['editcheck'] }}','{{ $request->mainmenu }}','{{ $get_det[$i]['FirstName'] }}','{{ $get_det[$i]['LastName'] }}');" title="{{ empnameontitle($get_det[$i]['LastName'], $get_det[$i]['FirstName'],50) }}">
	                    			{{ empnamelength($get_det[$i]['LastName'], $get_det[$i]['FirstName'],12) }}
	                    		</a>
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
	                    	@if(count($salary_det)!="")
				 				@foreach ($salresult as $key2 => $value2)
	                    			@if($key2 == isset($arr2[$key2]))
	                    			@php $val1 += $arr2[$key2] @endphp
	                    				<td class="text-right pr10"> {{ ($arr2[$key2] != '') ? number_format($arr2[$key2]): '' }}</td>
	                    			@else
	                    				<td></td>
	                    			@endif
	                    		@endforeach
                    		@else
                    			<td></td>
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
	                    			@if($key2 == isset($arr4[$key2]))
	                    			@php $val2 += $arr4[$key2] @endphp
	                    				<td class="text-right pr10"> {{ ($arr4[$key2] != '') ? number_format($arr4[$key2]) : '' }}</td>
	                    			@else
	                    				<td></td>
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
								@if($get_det[$i]['Salary'] != '')
									{{ number_format($calc) }}
								@endif
							</td>
							<td class="text-right pr10">
								@if($get_det[$i]['Transferred'] != '')
									{{ number_format($get_det[$i]['Transferred']) }}
								@endif
							</td>
							<td class="tac">
								@if($get_det[$i]['mailFlg'] == 0 && $get_det[$i]['Transferred'] != '')
									{{ Form::checkbox('salarycheck', $get_det[$i]['Emp_ID'],'',['id' => 'salarycheck','class' => 'checkbox','style' => 'display:inline-block']) }}
								@endif
							</td>
                    	</tr>
                    	@endfor
                    	@else
							<tr>
								<td class="text-center fr" colspan="11">
								{{ trans('messages.lbl_nodatafound') }}</td>
							</tr>
						@endif
				</tbody>
			</table>
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
	{{ Form::close() }}
	<div id="salarypopup" class="modal fade">
		<div id="login-overlay">
			<div class="modal-content">
				<!-- Popup will be loaded here -->
			</div>
		</div>
	</div>
	</article>
</div>
@endsection