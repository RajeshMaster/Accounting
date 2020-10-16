@extends('layouts.app')
@section('content')
@php use App\Http\Helpers; @endphp
<style type="text/css">
	.fb{
		color: gray !important;
	}
	table td{
		padding: 2px !important;
	}
	table th{
		
	}
</style>
<script type="text/javascript">
	
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';

</script>
<script type="text/javascript">	
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
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
{{ HTML::script('resources/assets/js/engineerdetailsplus.js') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper " data-category="sales sales_sub_3">
	{{ Form::open(array('name'=>'engineerindexplus', 
						'id'=>'engineerindexplus', 
						'url' => 'Engineerindexplus/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
						{{ Form::hidden('date_month', '', array('id' => 'date_month')) }}
						{{ Form::hidden('active_select', '', array('id' => 'active_select')) }}
						{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
						{{ Form::hidden('filter', $request->filter, array('id' => 'filter')) }}
						{{ Form::hidden('firstclick', $request->firstclick, array('id' => 'firstclick')) }}
						{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
						{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
						{{ Form::hidden('previou_next_year', $request->previou_next_year, 
	    												array('id' => 'previou_next_year')) }}
	    				{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	    				{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	    				{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
	    				{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
			 <img class="pull-left box40 mt5" src="{{ URL::asset('resources/assets/images/Client.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_engg_detailsplus') }}</h2>
		</div>
	</div>
<!-- End Heading --> 
	@if($request->active_select == 3)
	<div class="box61per pm0 mt5 ml10">
			<div class="">
				{{ Helpers::displayYear_MonthEst1($account_period, $year_monthslt, $db_year_month,$date_month, $dbnext, $dbprevious, $last_year, $current_year, $account_val) }}
			</div>
	</div>
	@endif
	<div class="col-xs-12 pm0 pull-left">
			<div class="box35per pm0 CMN_display_block pull-left">
				<a class="btn-link btn {{ $disabledcustomer }}" href="javascript:filter('2');"> {{ trans('messages.lbl_employee') }} </a>
				<span>|</span>
				<a class="btn-link btn {{ $disabledcurrentyear }}" href="javascript:filter('3');"> {{ trans('messages.lbl_currentyear') }} </a>
			</div>
			<div class="pull-right">
				
				<span>{{ trans('messages.lbl_unit') }}</span>
				<a class="btn-link btn {{ $disabledman }}" href="javascript:unitfilter('1');">10,000</a>
				<span>|</span>
				<a class="btn-link btn {{ $disabledsen }}" href="javascript:unitfilter('2');">1,000</a>
				<span>|</span>
				<a class="btn-link btn {{ $disabledyen }}" href="javascript:unitfilter('3');">{{ trans('messages.lbl_1yen') }}</a>
			</div>
	</div>
	<div class="mr10 ml10">
		@if($fileCnt >0)
		@if($request->active_select == 3)
			<!-- td width calculation 25/02/19 -->
		@if($tblset==2)
			<?php $tblwd="62%";?>
			<?php $thwth="10%";?>
		@elseif($tblset<=1)
			<?php $tblwd="52%";?>
			<?php $thwth="10%";?>
		@else
			<?php $tblwd="180.75%";?>
			<?php $thwth="10%";?>
		@endif
		<!-- end -->
		<div style="border: 1px solid white;overflow-x: auto;" id="sidebar">
			<table class="tablealternate CMN_tblfixed example" style="width: <?php echo $tblwd;?>!important;">
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader fwb tac"> 
				  		<th class="tac" width="4%">{{ trans('messages.lbl_sno') }}</th>
				  		<th class="tac" width="8%"  title="Employee Number">{{ trans('messages.lbl_empid') }}</th>
				  		<th class="tac" width="10%" >{{ trans('messages.lbl_empName') }}</th>
				  		<th class="tac" width="13%" >{{ trans('messages.lbl_total') }}
				  		</th>	
				  			@foreach ($arrval AS $year => $mvalue)
								@foreach ($mvalue AS $month => $mmonth)
									@if(count($mvalue) == 1 || count($mvalue) == 2 || count($mvalue) == 3 || count($mvalue) == 4 || count($mvalue) == 5 || count($mvalue) == 6 || count($mvalue) == 7)
										<?php echo "<th class='exedet_table_text' width='$thwth' id='" . $year . $month . "' align='center'>". $year.""?>{{ trans('messages.lbl_slashfield') }}<?php echo "".$month."</th>"; ?>
									@else
										<?php echo "<th class='exedet_table_text' width='$thwth' id='" . $year . $month . "' align='center'>".$year.""?>{{ trans('messages.lbl_slashfield') }}<?php echo "".$month."</th>"; ?>
									@endif
								@endforeach
							@endforeach
			   		</tr>
			   	</thead>
			   		<tr style="text-align:center;vertical-align:middle;border-bottom:1px dotted;background-color:#DDDDDD;">
							<td style="border-left:1px dotted;border-right:1px dotted;border-bottom:1px dotted;"></td>
							<td style="border-right:1px dotted;border-bottom:1px dotted;text-align:right;"></td>
							<td style="border-right:1px dotted;border-bottom:1px dotted;text-align:right;"></td>
							<td style="border-right:1px dotted;border-bottom:1px dotted;text-align:right;"></td>
							@foreach ($arrval_month1 AS $year => $mvalue)
								@foreach ($mvalue AS $month => $mmonth)	
									<?php echo "<td style='border-right:1px dotted;border-bottom:1px dotted; text-align:right;'>
													<b><span id='disp" . $year . $month . "' style='color:blue;cursor:default;vertical-align:middle;' class='mr5'></span></b>
												</td>"; ?>
								@endforeach
							@endforeach
					</tr>
					@for($cnt=0; $cnt<$fileCnt; $cnt++)
					<tr>
						<td class="text-center">
							 {{ $cnt + 1 }}
						</td>
						<td class="tac">
							{{ (isset($employee[$cnt]->empID)?$employee[$cnt]->empID:"") }}
						</td>
						<td class=""  @if(mb_strwidth($employee[$cnt]->Firstname) > 8)
							title ="{{ $employee[$cnt]->Firstname }}" @endif>
							<div class="tal">
                        @if(mb_strwidth($employee[$cnt]->Firstname) > 8) 
                        	@if(mb_strlen($employee[$cnt]->Firstname) > 8)
                            	@php echo mb_strimwidth ((isset($employee[$cnt]->Firstname)?$employee[$cnt]->Firstname:""), 0, 16,"...") @endphp
                           		 @else
                            {{ (isset($employee[$cnt]->Firstname)?$employee[$cnt]->Firstname:"") }}
                            @endif
                        		@else
                        	{{ (isset($employee[$cnt]->Firstname)?$employee[$cnt]->Firstname:"") }}
                        @endif
								<!-- @if(mb_strlen((isset($employee[$cnt]->Firstname)?$employee[$cnt]->Firstname:""), 'UTF-8') > 8)
								    @php echo mb_substr((isset($employee[$cnt]->Firstname)?$employee[$cnt]->Firstname:""), 0, 8, 'UTF-8')."..." @endphp
								@else
								    	{{ (isset($employee[$cnt]->Firstname)?$employee[$cnt]->Firstname:"") }}
								@endif -->
							</div>
						</td>
						<td  class="tar vam" style='background-color:#DDDDDD;'>
							<sapn style='font-weight: bold;' class="vam mr5" id='disp4<?php echo $cnt;?>'></span>			
						</td>
						<?php
							$fil_3 = count($array1[$cnt]);
							for ($i=0; $i < $fil_3 ; $i++) { ?>
						<td class="tar" style="padding-right: 7px !important;">
								@if ($array1[$cnt][$i] != "0")
									{{ $array1[$cnt][$i] }}
								@endif
						</td>
							<?php } ?>
					</tr>
					@endfor
			</table>
		</div>
			<div class="text-center">
				@if(!empty($getdetails->total()))
					<span class="pull-left mt24">
						{{ $getdetails->firstItem() }} ~ {{ $getdetails->lastItem() }} / {{ $getdetails->lastItem() }}
					</span>
				@endif 
					{{ $getdetails->links() }}
				<div class="CMN_display_block flr mr0">
          			{{ $getdetails->linkspagelimit() }}
        		</div>
			</div>
		@elseif($request->active_select == 2)
		<!-- td width calculation 25/02/19 -->
		@if($tblset==4)
			<?php $tblwdth="94%"; ?>
			<?php $thwth="13%";?>
			<?php $snothwth="4%";?>
			<?php $empidthwth="9%";?>
			<?php $empnamethwth="11.5%";?>
			<?php $totthwth="16%";?>
			<?php $accthwth="13%";?>
		@elseif($tblset<=3)
			<?php $tblwdth="84%"; ?>
			<?php $thwth="13%";?>
			<?php $snothwth="4%";?>
			<?php $empidthwth="9%";?>
			<?php $empnamethwth="12%";?>
			<?php $totthwth="16%";?>
			<?php $accthwth="13%";?>
		@else
			<?php $tblwdth="180.75%";?>
			<?php $thwth="9%";?>
			<?php $snothwth="3%";?>
			<?php $empidthwth="6%";?>
			<?php $empnamethwth="10%";?>
			<?php $totthwth="11%";?>
			<?php $accthwth="9%";?>
		@endif
		<!-- end -->
		<div style="border: 1px solid white;overflow-x: auto;" id="sidebar">
			<table class="tablealternate CMN_tblfixed example" style="width: <?php echo $tblwdth;?>!important;">
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader fwb tac"> 
				  		<th class="tac" width="<?php echo $snothwth ?>">{{ trans('messages.lbl_sno') }}</th>
				  		<th class="tac" width="<?php echo $empidthwth ?>" title="Employee Number">{{ trans('messages.lbl_empid') }}</th>
				  		<th class="tac" width="<?php echo $empnamethwth ?>">{{ trans('messages.lbl_empName') }}</th>
				  		<th class="tac" width="<?php echo $totthwth ?>">{{ trans('messages.lbl_total') }}</th>
				  		<th class="tac" width="<?php echo $accthwth ?>">{{ intval($account_period) + 2 }} {{ trans('messages.lbl_period') }}</th>
				  		<th class="tac" width="<?php echo $accthwth ?>">{{ intval($account_period) + 1 }} {{ trans('messages.lbl_period') }}</th>
				  			<?php 
							$endperiod = 1;
							foreach ($cnt_array AS $period => $cntvalue) {
								foreach ($cntvalue AS $key => $value) {
									if ($value->qdate > 0) {
										$endperiod = $period;
									}
								}
							}
							foreach ($cnt_array AS $period => $cntvalue) {
								if ($period >= $endperiod) { ?> 
									<th class="exedet_table_text <?php echo $period;?>" width=<?php echo  $thwth;?> align='center'>{{ intval($period) }} 
										{{ trans('messages.lbl_period') }}</th>
						<?php }
							}
						?>
			   		</tr>
			   	</thead>
			   		<tr style="text-align:center;vertical-align:middle;border-bottom:1px dotted;background-color:#DDDDDD;">
							<td style="border-left:1px dotted;border-right:1px dotted;border-bottom:1px dotted;"></td>
							<td style="border-right:1px dotted;border-bottom:1px dotted;text-align:right;"></td>
							<td style="border-right:1px dotted;border-bottom:1px dotted;text-align:right;"></td>
							<td style="border-right:1px dotted;border-bottom:1px dotted;text-align:right;"></td>
							<td style="border-right:1px dotted;border-bottom:1px dotted;text-align:right;">
							<b><span id="disp2<?php echo intval($account_period) + 2; ?>" style="color:blue;cursor:default;vertical-align: middle;" class='mr5'></span></b>
						</td>
						<td style="border-right:1px dotted;border-bottom:1px dotted;text-align:right;">
							<b><span id="disp2<?php echo intval($account_period) + 1; ?>" style="color:blue;cursor:default;vertical-align: middle;" class='mr5'></span></b>
						</td>
							@foreach ($cnt_array AS $period => $cntvalue)
								@if ($period >= $endperiod) 
									<td style='border-right:1px dotted;border-bottom:1px dotted; text-align:right;'>
										<b><span id='disp2<?php echo $period;?>' style="color:blue;cursor:default;vertical-align: middle;" class='mr5'></span></b>
									</td>
								@endif
							@endforeach
					</tr>
					@for($cnt=0; $cnt<$fileCnt; $cnt++)
					<tr>
						<td class="tac">
							{{ ($getdetails->currentpage()-1) * $getdetails->perpage() + $cnt + 1 }}
						</td>
						<td class="tac">
							{{ (isset($employee[$cnt]->empID)?$employee[$cnt]->empID:"") }}
						</td>
						<td class=""  @if(mb_strwidth($employee[$cnt]->Firstname) > 8)
							title ="{{ $employee[$cnt]->Firstname }}" @endif>
							<div class="tal">
                        @if(mb_strwidth($employee[$cnt]->Firstname) > 8) 
                        	@if(mb_strlen($employee[$cnt]->Firstname) > 8)
                            	@php echo mb_strimwidth ((isset($employee[$cnt]->Firstname)?$employee[$cnt]->Firstname:""), 0, 16,"...") @endphp
                           		 @else
                            {{ (isset($employee[$cnt]->Firstname)?$employee[$cnt]->Firstname:"") }}
                            @endif
                        		@else
                        	{{ (isset($employee[$cnt]->Firstname)?$employee[$cnt]->Firstname:"") }}
                        @endif
		                    </div>
		                </td>
						<td style='background-color:#DDDDDD;' class="tar">
							<span style="vertical-align: middle;font-weight: bold;" id='disp5<?php echo $cnt;?>' class='mr5'></span>
						</td>
						<?php $f=12-$endperiod;
								  $fil_2 = count($array2[$cnt]); ?>
					@for ($j=0; $j <$fil_2 ; $j++)
						<td align='right' style="padding-right: 7px !important;">
								@if ($array2[$cnt][$j] != "0")
									{{ $array2[$cnt][$j] }}
								@endif
						</td>
					@endfor
					</tr>	
					@endfor
			</table>
		</div>
			<div class="text-center">
				@if(!empty($getdetails->total()))
					<span class="pull-left mt24">
						{{ $getdetails->firstItem() }} ~ {{ $getdetails->lastItem() }} / {{ $getdetails->lastItem() }}
					</span>
				@endif 
					{{ $getdetails->links() }}
				<div class="CMN_display_block flr mr0">
          			{{ $getdetails->linkspagelimit() }}
        		</div>
			</div>
		@endif
		@else
		<div style="border: 1px solid white;overflow-x: auto;" id="sidebar">
			<table class="tablealternate CMN_tblfixed example" style="width: 100%!important;">
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader fwb tac"> 
				  		<th class="tac" width="4%">{{ trans('messages.lbl_sno') }}</th>
				  		<th class="tac" width="10%" title="Employee Number">{{ trans('messages.lbl_empid') }}</th>
				  		<th class="tac" width="14%">{{ trans('messages.lbl_empName') }}</th>
				  		<th class="tac" width="11%" colspan="8">{{ trans('messages.lbl_total') }}
				  		</th>
				  	</tr>
				  	<tr>
                                     <td class="text-center" colspan="11" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
                                </tr>
				 </thead>
			</table>
		</div>
		@endif
	</div>
</article>
</div>
@if($request->active_select == 3)
	<script>
	var disptotal = 0;
	<?php foreach ($jsdisparry AS $month => $monthval) { ?>
		document.getElementById('disp4'+<?php echo $month; ?>).innerText = "<?php echo number_format($monthval); ?>";	
		disptotal += <?php echo $monthval; ?>;
		<?php } ?>
	</script>
@endif
@if($request->active_select == 3)
<script>
	var disptotal=0;
	<?php 
		foreach ($month_total_array AS $mkey => $mvalue) {
			foreach ($mvalue AS $month => $monthval) { ?>
				document.getElementById('disp'+<?php echo $mkey . $month; ?>).innerText = "<?php echo number_format($monthval); ?>";
				disptotal += <?php echo $monthval; ?>;
				<?php			
			}
		}
	?>
		
</script>
@endif
@if($request->active_select == 2)
	<script>
	var disptotal = 0;
	<?php foreach ($jsdisp2arry AS $month => $monthval) { ?>
		document.getElementById('disp5'+<?php echo $month; ?>).innerText = "<?php echo number_format($monthval); ?>";	
		disptotal += <?php echo $monthval; ?>;
		<?php } ?>
	</script>
@endif
@if($request->active_select == 2)
<script>
		var disptotal = 0;
		<?php 
		foreach ($jsarry2page AS $month => $monthval) { ?>
		document.getElementById('disp2'+<?php echo $month; ?>).innerText = "<?php echo number_format($monthval); ?>";	
	disptotal += <?php echo $monthval; ?>;
	<?php
			} ?>
	</script>
@endif
@endsection