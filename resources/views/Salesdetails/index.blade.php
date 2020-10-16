@extends('layouts.app')
@section('content')
@php use App\Http\Helpers; @endphp
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
<style type="text/css">
	.fb{
		color: gray !important;
	}
	table td{
		padding: 2px !important;
	}
</style>
{{ HTML::script('resources/assets/js/salesdetails.js') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper " data-category="sales sales_sub_5">
	{{ Form::open(array('name'=>'frmsalesindex', 
						'id'=>'frmsalesindex', 
						'url' => 'Salesdetails/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	    {{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
	    {{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	    {{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
	    {{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
	    {{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
	    {{ Form::hidden('previou_next_year', $request->previou_next_year, 
	    												array('id' => 'previou_next_year')) }}
	    {{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
	    {{ Form::hidden('active_select', $request->active_select, array('id' => 'active_select')) }}
	    {{ Form::hidden('firstclick', $request->firstclick, array('id' => 'firstclick')) }}
	    {{ Form::hidden('filter', $request->filter, array('id' => 'filter')) }}
	    {{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	    {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	    {{ Form::hidden('selyearmonth', '', array('id' => 'selyearmonth')) }}
	    {{ Form::hidden('date_month', '', array('id' => 'date_month')) }}
<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/salesdetails.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_salesdetails') }}</h2>
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
				<a class="btn-link btn {{ $disabledmonthly }}" href="javascript:filter('1');"> {{ trans('messages.lbl_monthly') }} </a>
				<span>|</span>
				<a class="btn-link btn {{ $disabledcustomer }}" href="javascript:filter('2');"> {{ trans('messages.lbl_customer') }} </a>
				<span>|</span>
				<a class="btn-link btn {{ $disabledcurrentyear }}" href="javascript:filter('3');"> {{ trans('messages.lbl_currentyear') }} </a>
			</div>
			@if($request->active_select == 3)
					<div class="box45per pm0 CMN_display_block mt5">
						<table>
							<tr>
								<td class="fwb CMN_tbltheadcolor">
									<input type="hidden" name="cnttl" id="cnttl" value="<?php echo $avgmnth ?>">
									{{ trans('messages.lbl_totamt') }} ({{ $avgmnth }} {{ trans('messages.lbl_month') }})
								</td>
								<td class="tar" width="25%">
									<span style="font-weight: bold; padding-right: 4px;" class="vam" id='lblgrndtl'></span>
								</td>
								<td class="fwb CMN_tbltheadcolor">
									{{ trans('messages.lbl_avgmonth')}}
								</td>
								<td class="tar" width="20%">
									<span style="font-weight: bold;padding-right: 4px;" class="vam" id='lbldivcnt'></span>
								</td>
							</tr>
						</table>
					</div>
			@endif
			<div class="pull-right">
				<a href="javascript:Salesdetailsexceldownload('{{ $request->active_select }}','{{ $request->filter }}');"><span class="fa fa-download mr5"></span>{{ trans('messages.lbl_download')}}</a>
				<span>{{ trans('messages.lbl_unit') }}</span>
				<a class="btn-link btn {{ $disabledman }}" href="javascript:unitfilter('1');">10,000</a>
				<span>|</span>
				<a class="btn-link btn {{ $disabledsen }}" href="javascript:unitfilter('2');">1,000</a>
				<span>|</span>
				<a class="btn-link btn {{ $disabledyen }}" href="javascript:unitfilter('3');">{{ trans('messages.lbl_1yen') }}</a>
			</div>
	</div>
	<div class="mr10 ml10">
		<div>
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
				  		<th class="tac" width="8%" title="Customer Number">{{ trans('messages.lbl_cust_no') }}</th>
				  		<th class="tac" width="12%">{{ trans('messages.lbl_customername') }}</th>
				  		<th class="tac" width="11%">{{ trans('messages.lbl_total') }}
				  		</th>
				  		@foreach ($arrval AS $year => $mvalue)
								@foreach ($mvalue AS $month => $mmonth)
									@if(count($mvalue) == 1 || count($mvalue) == 2 || count($mvalue) == 3 || count($mvalue) == 4 || count($mvalue) == 5 || count($mvalue) == 6 || count($mvalue) == 7)
										<?php echo "<th class='exedet_table_text' width='$thwth' id='elastic" . $year . $month . "' align='center'>". $year.""?>{{ trans('messages.lbl_slashfield') }}<?php echo "".$month."</th>"; ?>
									@else
										<?php echo "<th class='exedet_table_text' width='$thwth' id='elastic" . $year . $month . "' align='center'>".$year.""?>{{ trans('messages.lbl_slashfield') }}<?php echo "".$month."</th>"; ?>
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
						<td class="tac">
							{{ ($sql->currentpage()-1) * $sql->perpage() + $cnt + 1 }}
						</td>
						<td class="tac">
							{{ (isset($cus_list[$cnt][0]->customer_id)?$cus_list[$cnt][0]->customer_id:"") }}
						</td>
						<td @if(isset($cus_list[$cnt][0]->customer_name) &&strlen($cus_list[$cnt][0]->customer_name) > 8) 
	    						title="{{ $cus_list[$cnt][0]->customer_name }}"
			    			@endif>
			    		 @if(mb_strlen((isset($cus_list[$cnt][0]->customer_name)?$cus_list[$cnt][0]->customer_name:""), 'UTF-8') > 8)
                       		 @php echo mb_substr((isset($cus_list[$cnt][0]->customer_name)?$cus_list[$cnt][0]->customer_name:""), 0, 8, 'UTF-8')."..." @endphp
                     	 @else
                        	{{ (isset($cus_list[$cnt][0]->customer_name)?$cus_list[$cnt][0]->customer_name:"") }}
                      @endif
						</td>
						<td style='background-color:#DDDDDD;' class="tar vam">
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
				@if(!empty($sql->total()))
					<span class="pull-left mt24">
						{{ $sql->firstItem() }} ~ {{ $sql->lastItem() }} / {{ $sql->total() }}
					</span>
				@endif 
					{{ $sql->links() }}
				<div class="CMN_display_block flr mr0">
          			{{ $sql->linkspagelimit() }}
        		</div>
			</div>
			
		@elseif($request->active_select == 2)
		<!-- td width calculation 25/02/19 -->
		@if($tblset==4)
			<?php $tblwdth="94%"; ?>
			<?php $thwth="13%";?>
			<?php $snothwth="4%";?>
			<?php $cusidthwth="9%";?>
			<?php $cusnamethwth="13%";?>
			<?php $totthwth="16%";?>
			<?php $accthwth="13%";?>
		@elseif($tblset<=3)
			<?php $tblwdth="84%"; ?>
			<?php $thwth="13%";?>
			<?php $snothwth="4%";?>
			<?php $cusidthwth="9%";?>
			<?php $cusnamethwth="13%";?>
			<?php $totthwth="16%";?>
			<?php $accthwth="13%";?>
		@else
			<?php $tblwdth="180.75%";?>
			<?php $thwth="8%";?>
			<?php $snothwth="2.5%";?>
			<?php $cusidthwth="6%";?>
			<?php $cusnamethwth="9.5%";?>
			<?php $totthwth="10%";?>
			<?php $accthwth="8%";?>
		@endif
		<!-- end -->
		<div style="border: 1px solid white;overflow-x: auto;" id="sidebar">
		<table class="tablealternate CMN_tblfixed" style="width: <?php echo $tblwdth;?>!important;">
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader tac"> 
				  		<th class="tac" width="<?php echo $snothwth ?>">{{ trans('messages.lbl_sno') }}</th>
				  		<th class="tac" width="<?php echo $cusidthwth ?>" title="Customer Number">{{ trans('messages.lbl_cust_no') }}</th>
				  		<th class="tac" width="<?php echo $cusnamethwth ?>">{{ trans('messages.lbl_customername') }}</th>
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
									<th class="exedet_table_text " id="elastic_cus<?php echo $period;?>"
									 width=<?php echo $thwth;?> align='center'>{{ intval($period) }} 
										{{ trans('messages.lbl_period') }}</th>
						<?php }
							}
						?>
				  	</tr>
				</thead>

				  	<tr style="text-align: center; height: 25px;vertical-align:middle;border-bottom:1px dotted;background-color:#DDDDDD;">
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
							{{ ($sql->currentpage()-1) * $sql->perpage() + $cnt + 1 }}
						</td>
						<td class="tac">
							{{ (isset($cus_list[$cnt][0]->customer_id)?$cus_list[$cnt][0]->customer_id:"") }}
						</td>
						<td @if(isset($cus_list[$cnt][0]->customer_name) &&strlen($cus_list[$cnt][0]->customer_name) > 6) 
	    						title="{{ $cus_list[$cnt][0]->customer_name }}"
			    			@endif>
			    		@if(mb_strlen((isset($cus_list[$cnt][0]->customer_name)?$cus_list[$cnt][0]->customer_name:""), 'UTF-8') > 6)
                       	 @php echo mb_substr((isset($cus_list[$cnt][0]->customer_name)?$cus_list[$cnt][0]->customer_name:""), 0, 6, 'UTF-8')."..." @endphp
                      @else
                        	{{ (isset($cus_list[$cnt][0]->customer_name)?$cus_list[$cnt][0]->customer_name:"") }}
                      @endif
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
				@if(!empty($sql->total()))
					<span class="pull-left mt24">
						{{ $sql->firstItem() }} ~ {{ $sql->lastItem() }} / {{ $sql->total() }}
					</span>
				@endif 
					{{ $sql->links() }}
				<div class="CMN_display_block flr mr0">
          			{{ $sql->linkspagelimit() }}
        		</div>
			</div>
			
		@elseif($request->active_select == 1)
			<div style="border: 1px solid white;overflow-x: scroll;">
		<table class="tablealternate CMN_tblfixed" style="width: 152%!important;">
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader fwb tac"> 
				  		<th class="tac" width="5%"></th>
				  		<th class="tac" width="">{{ trans('messages.lbl_total') }}</th>
							@foreach ($year_monthslt AS $year => $month)
								@foreach ($month as $key => $value)
								<th class="exedet_table_text" width="6.9%" align='center'>
									{{ $value }} {{ trans('messages.lbl_monthnumb') }}
								</th>
							@endforeach
							@endforeach
				  	</tr>
				</thead>
				  	<tr style="text-align: center;vertical-align:middle;background-color:#DDDDDD;">
						<td style=""></td>
						<td style=""></td>
						@foreach ($year_monthslt AS $year => $month)
								@foreach ($month as $key => $value)
								<td class="exedet_table_text" align='right'>
									<b><span id='disp6<?php echo $key;?>' style="color:blue;cursor:default;vertical-align: middle;" class='mr5'></span></b>
								</td>
							@endforeach
							@endforeach
						
					</tr>
						<?php $endperiod = 1;
							foreach ($cnt_array AS $period => $cntvalue) {
								foreach ($cntvalue AS $key => $value) {
									if ($value->qdate > 0) {
										$endperiod = $period;
									}
								}
							}
							$row = 0;
							$g = 0;
							foreach ($cnt_array AS $period => $cntvalue) {
								if ($period >= $endperiod) {?>
									<tr>
										<td  align='center'><?php 
											echo intval($period);?> {{ trans('messages.lbl_period') }}</td>
										<td style='background-color:#DDDDDD;' align='right'>
											<span style="vertical-align: middle;font-weight: bold;" id='disp3<?php echo $period;?>' class='mr5'></span>
										</td>
								<?php $fil_1 = count($array3[$g]);		  
									for ($i = 0; $i < $fil_1; $i++) { ?>
										<td align='right' style="padding-right: 7px !important;">
											<?php 
												if ($array3[$g][$i] != "0") {?>
													<a style="color: blue;" href = "javascript:Activemonth_invoice('{{ $year_mon_array[$g][$i] }}','{{ $period }}');" class="fontlink">
													<?php echo $array3[$g][$i];?> </a>
											<?php }?>
										</td>
											<?php }
											?></tr> <?php 
										}	
									$g++;
									}?>
			</table>
		@endif
		@else
		<div style="border: 1px solid white;overflow-x: auto;" id="sidebar">
			<table class="tablealternate CMN_tblfixed example" style="width: 100%!important;">
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader fwb tac"> 
				  		<th class="tac" width="5%">{{ trans('messages.lbl_sno') }}</th>
				  		<th class="tac" width="11%" title="Customer Number">{{ trans('messages.lbl_cust_no') }}</th>
				  		<th class="tac" width="25%">{{ trans('messages.lbl_customername') }}</th>
				  		<th class="tac" width="18%" colspan="7">{{ trans('messages.lbl_total') }}
				  		</th>
				  	</tr>
				  	<tr>
                                     <td class="text-center" colspan="10" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
                                </tr>
				 </thead>
			</table>
		</div>
		@endif
		</div>
	</div>
	{{ Form::close() }}
	{{ Form::open(array('name'=>'frmsalesexceldownload', 
						'id'=>'frmsalesexceldownload',
						'files'=>true,
						'method' => 'POST')) }}
	{{ Form::hidden('actionName', '', array('id' => 'actionName')) }}
	{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	    {{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
	{{ Form::hidden('active_select', '', array('id' => 'active_select')) }}
	{{ Form::hidden('filter', '', array('id' => 'filter')) }}
	{{ Form::close() }}
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
@if($request->active_select == 2)
	<script>
	var disptotal = 0;
	<?php foreach ($jsdisp2arry AS $month => $monthval) { ?>
		document.getElementById('disp5'+<?php echo $month; ?>).innerText = "<?php echo number_format($monthval); ?>";	
		disptotal += <?php echo $monthval; ?>;
		<?php } ?>
	</script>
@endif
@if($request->active_select == 1)
	<script>
	var disptotal = 0;
	<?php foreach ($jsarry3 AS $month => $monthval) { ?>
			document.getElementById('disp3'+<?php echo $month; ?>).innerText = "<?php echo number_format($monthval); ?>";	
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
		document.getElementById('lblgrndtl').innerHTML = Math.round(disptotal).toLocaleString("en-US");	
		if (Math.round(disptotal/document.getElementById('cnttl').value).toLocaleString("en-US") == "-NaN" || Math.round(disptotal/document.getElementById('cnttl').value).toLocaleString("en-US") == "") {
			document.getElementById('lbldivcnt').innerHTML = "0";
		} else {
			document.getElementById('lbldivcnt').innerHTML = Math.round(disptotal/document.getElementById('cnttl').value).toLocaleString("en-US");
		}
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
@if($request->active_select == 1)
<script>
		var disptotal = 0;
		<?php 
		foreach ($jsdisp1arry AS $month => $monthval) {?>

		var myElem = document.getElementById('disp6'+<?php echo $month; ?>);
		if (myElem === null) {
		} else {
			document.getElementById('disp6'+<?php echo $month; ?>).innerText = "<?php echo number_format($monthval); ?>";
		}
	disptotal += <?php echo $monthval; ?>;
	<?php } ?>
	</script>
@endif
@endsection