@extends('layouts.app')
@section('content')
@php use App\Http\Helpers; @endphp
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
<style type="text/css">
	.fb{
		color: gray !important;
	}
</style>
{{ HTML::script('resources/assets/js/expdetails.js') }}
<div class="CMN_display_block box100per" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_7">
	{{ Form::open(array('name'=>'frmexpensesdetailsindex', 
						'id'=>'frmexpensesdetailsindex', 
						'url' => 'ExpensesDetails/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		
		{{ Form::hidden('sample', '', array('id' => 'sample')) }}
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
	    {{ Form::hidden('salaryflg', '' , array('id' => 'salaryflg')) }}
	    {{ Form::hidden('subject', '' , array('id' => 'subject')) }}
	    {{ Form::hidden('bname', '' , array('id' => 'bname')) }}
	    {{ Form::hidden('selMonth', '' , array('id' => 'selMonth')) }}
	    {{ Form::hidden('selYear', '' , array('id' => 'selYear')) }}
	    {{ Form::hidden('exptype1', '' , array('id' => 'exptype1')) }}
	    {{ Form::hidden('detail', '' , array('id' => 'detail')) }}
	    {{ Form::hidden('expdetails', $request->expdetails , array('id' => 'expdetails')) }}
	<!-- Start Heading -->
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/expenses_icon.png') }}">
			<h2 class="pull-left pl5 mt10 CMN_mw150">{{ trans('messages.lbl_expdetail') }}</h2>
		</div>
	</div>
	<!-- End Heading -->
	<!-- Session msg -->
	@if(Session::has('success'))
		<div align="center" class="alertboxalign" role="alert">
			<p class="alert {{ Session::get('alert', Session::get('type') ) }}">
            {{ Session::get('success') }}
          	</p>
		</div>
	@endif
	@php Session::forget('success'); @endphp
	<!-- Session msg -->
	@if($request->active_select == 3)
		<div class="box61per pm0 mt5 ml10">
			<div class="">
				{{ Helpers::displayYear_MonthEst1($account_period, $year_monthslt, $db_year_month,$date_month, $dbnext, $dbprevious, $last_year, $current_year, $account_val) }}
			</div>
		</div>
	@endif
	<div class="col-xs-12 pm0 pull-left">
			<div class="box30per pm0 CMN_display_block pull-left">
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
									{{ trans('messages.lbl_totamt') }} ({{ $date_cnt }} {{ trans('messages.lbl_month') }})
								</td>
								<td class="tar" width="25%">
									<span style="font-weight: bold; padding-right: 4px;" class="vam" id='lblgrndtl'>{{ number_format($grandTotal) }}</span>
								</td>
								<td class="fwb CMN_tbltheadcolor">
									{{ trans('messages.lbl_avgmonth')}}
								</td>
								<td class="tar" width="20%">
									<span style="font-weight: bold;padding-right: 4px;" class="vam" id='lbldivcnt'>{{ $avg }}</span>
								</td>
							</tr>
						</table>
					</div>
			@endif
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
		<div>
		@if($fileCnt >0)
		@if($request->active_select == 3)
			<div style="border: 1px solid white;overflow-x: scroll;" id="sidebar">
			<table class="tablealternate CMN_tblfixed example" style="width: 180.75%!important;">
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader fwb tac"> 
				  		<th class="tac" width="5.4%">{{ trans('messages.lbl_sno') }}</th>
				  		<th class="tac" width="28%">{{ trans('messages.lbl_mainsubject') }}</th>
				  		<th class="tac" width="23%">{{ trans('messages.lbl_subsubject') }}</th>
				  		<th class="tac" width="18%">{{ trans('messages.lbl_total') }}</th>
				  		<?php 
								$mntotcnt=$date_cnt;
								for($i=0;$i<$date_cnt;$i++){ 
									if($mntotcnt%3==0){?>
										<th class="exedet_table_text" width="
										16.2%">{{ trans('messages.lbl_3monthtotal') }}</th>
									<?php }?>
								<th class="exedet_table_text" width="
								16.2%">
									<span title=""><?php echo $dateindex[$date_cnt-$i-1];?></span>
								</th>
								<?php $mntotcnt--;} ?>
			   		</tr>
			   		</thead>
			   		<tr style=" height: 25px;vertical-align:middle;border-bottom:1px dotted;background-color:#bdbdbd;">
								<td colspan="4"></td>
								<?php
									$three_mnth_tot=$grandTotal;
									$mntotcnt=$date_cnt;
									for ($d=0;$d<$date_cnt;$d++) {
										if($mntotcnt%3==0){?>
										<td class="tar" style="color:brown;font-weight:bold;">
											<?php
											$three_mnth_tot=$grndmontotal[$d]+$grndmontotal[$d+1]+$grndmontotal[$d+2];
												if($three_mnth_tot){
													echo number_format($three_mnth_tot);
												} else{
													echo "";
												} 
											?>
										</td>
									<?php
									$three_mnth_tot = 0; 
									}?>
									<td style="color:blue;cursor:default;font-weight:bold;" class="tar">
										<?php echo number_format($grndmontotal[$d]); ?>
									</td>
								<?php
								// echo$three_mnth_tot=$grndmontotal[$d]+$grndmontotal[$d+1]+$grndmontotal[$d+2];	
								 $mntotcnt--;} ?>

							</tr>
							<?php 
								$temp = "";
								$row = "";
								$totmain=0;
								$tempmain = "";
								for ($cnt=0;$cnt<$fileCnt;$cnt++){
								$loc = $cnt + 1;
								if($loc != $temp){
								if($row == 1){
									$style='style = "background-color:#E2F0F3;"';
									$row=0;
									
								} else {
									$style='style = ""';
									$row=1;
									}$styleTD = 'style = "border-top:1px dotted #000000;vertical-align: top;"';
								} else {
									 $styleTD = 'style = "border-top:0px dotted #000000;vertical-align: top;"';
								}
								$total =0;
								for($d=0;$d<$date_cnt;$d++){
									$dates=$date[$date_cnt-$d-1];
									$total =$total+$get_det[$cnt][$dates];
								}
								if($tempmain!=$get_det[$cnt]['Subject']&& $cnt!=0){
							 ?>
							 <tr style="height: 25px;vertical-align:middle;border-top:1px dotted;border-bottom:1px dotted;background-color:#DDDDDD;">
							 	<td colspan="3"></td>
							 	<td class="tar">
							 		<?php
							 			if($totmain){
							 				echo number_format($totmain);
							 			} else {
							 				echo "";
							 			}  
							 		?>
							 	</td>
							 	<?php
							 		$three_mnth_tot=$totmain;
							 		$mntotcnt=$date_cnt;
							 		for($d=0;$d<$date_cnt;$d++){
							 		 if($mntotcnt%3==0){?>
										<td class="tar" style="color:brown;font-weight:bold;">
											<?php
											$three_mnth_tot=$montotal[$d]+$montotal[$d+1]+$montotal[$d+2];
												if($three_mnth_tot){
													echo number_format($three_mnth_tot);
												} else{
													echo "";
													} 
											?>
										</td>
										<?php 
										$three_mnth_tot =0;
										}?>
							 			<td class="tar">
								 			<?php
								 				if($montotal[$d]){ 
								 					echo number_format($montotal[$d]); 
								 				} else {
								 					echo "";
								 				}
								 			?>
							 			</td>
							 	<?php
							 	//$three_mnth_tot=$montotal[$d+1]+$montotal[$d+2]+$montotal[$d+3];
							 	$mntotcnt--;
							 	$montotal[$d]=0;
							 	} ?>
							 </tr>
							 <?php
							 	$totmain=0;
							  } ?>
							<tr <?php echo $style;?> class="tax_data_name altrclr">
								<td class="tac"><?php echo $cnt+1; ?></td>
								<td>
								<?php 
									if($tempmain!=$get_det[$cnt]['Subject']){
										if($get_det[$cnt]['Subject']!="Loan Payment" && $get_det[$cnt]['Subject']!="Petty" 
																	&& $get_det[$cnt]['Subject']!="Paid Salary"){?>
											<a style="color:#0000FF;cursor:pointer" class="anchorstyle" href="javascript:gotoexpensestransferhistory('{{$get_det[$cnt]['id']}}','0','{{$request->mainmenu}}','','',1);"
												@if(strlen($get_det[$cnt]['Subject']) > $utf_lngth) 
	    												title="{{ $get_det[$cnt]['Subject'] }}"
			    										@endif>
			    								@if(singlefieldlength($get_det[$cnt]['Subject'],$utf_lngth))
			    									{{singlefieldlength($get_det[$cnt]['Subject'],$utf_lngth)}}
												@else
													{{$get_det[$cnt]['Subject']}}
												@endif
											</a>
										<?php
											} else {
												if ( $get_det[$cnt]['Subject'] == "Loan Payment" ) {
													echo "Loan Payment";
												} else if ( $get_det[$cnt]['Subject'] == "Petty" ) {
													echo "petty Cash";
												} else {
													echo "Salary Paid";
												}
											}
									} else {
										echo "";
									}
								?>
								</td>
								<td>
									<?php 
										if($get_det[$cnt]['sub']!="Loan Payment" && $get_det[$cnt]['sub']!="Petty"
																		&& $get_det[$cnt]['sub']!="Paid Salary"){
											?>
											<a class="anchorstyle" href="javascript:gotosubhistory('{{ $get_det[$cnt]['subid'] }}','{{ $get_det[$cnt]['sub'] }}','','0','{{ $request->mainmenu }}','sub','','',1);" 
																style="color:#0000FF;"
												@if(strlen($get_det[$cnt]['sub']) > $utf_lngth) 
	    												title="{{ $get_det[$cnt]['sub'] }}"
			    										@endif>
			    								@if(singlefieldlength($get_det[$cnt]['sub'],$utf_lngth))
			    									{{singlefieldlength($get_det[$cnt]['sub'],$utf_lngth)}}
												@else
													{{$get_det[$cnt]['sub']}}
												@endif
											</a>
										<?php
											} else {
												if ( $get_det[$cnt]['sub'] == "Loan Payment" ) {
													echo "Loan Payment";
												} else if ( $get_det[$cnt]['sub'] == "Petty" ) {
													echo "petty Cash";
												} else {
													echo "Salary Paid";
												}
											}
									 ?>
								</td>
								<td class="tar">
							 		<?php
							 			if($total){
							 				echo number_format($total);
							 			} else {
							 				echo "";
							 			}  
							 		?>
							 	</td>
								<?php 
								$mntotcnt=$date_cnt;
								$three_mnth_tot=$total;
								for($d=0;$d<$date_cnt;$d++){
									if($mntotcnt%3==0){?>
										<td class="tar" style="color:brown;font-weight:bold;">
											<?php 
											$three_mnth_tot=$get_det[$cnt][$date[$date_cnt-$d-1]]+$get_det[$cnt][$date[$date_cnt-$d-2]]
															+$get_det[$cnt][$date[$date_cnt-$d-3]];
												if($three_mnth_tot){
													echo number_format($three_mnth_tot);
												} else {
													echo "";
												} 
											?>
										</td>
									<?php
									$three_mnth_tot=0;
									}
									$dates=$date[$date_cnt-$d-1]; ?>
									<td width="120px;" class="tar">
										<?php
											if($get_det[$cnt][$dates]){ 
												echo number_format($get_det[$cnt][$dates]);
											} else {
												echo "";
											} 
										?>
									</td>
								<?php $mntotcnt--;
								/*$three_mnth_tot=
								$get_det[$cnt][$date[$date_cnt-$d-2]]+$get_det[$cnt][$date[$date_cnt-$d-3]]
								+$get_det[$cnt][$date[$date_cnt-$d-4]];*/
								} ?>
							</tr>
							<?php $totmain=$totmain+$total;
								for($d=0;$d<$date_cnt;$d++){
									$dates=$date[$date_cnt-$d-1];
									if (!isset($montotal[$d])) {
										$montotal[$d] = 0;
									}
									$montotal[$d] =$montotal[$d]+$get_det[$cnt][$dates];
								}
							if($cnt==$fileCnt-1) { ?>
								<tr style="height: 25px;vertical-align:middle;border-top:1px dotted;border-bottom:1px dotted;background-color:#DDDDDD;">
								 	<td colspan="3"></td>
								 	<td class="tar">
								 		<?php
								 			if($totmain){
								 				echo number_format($totmain);
								 			} else {
								 				echo "";
								 			}  
								 		?>
								 	</td>
								 	<?php
								 		$three_mnth_tot=$totmain;
								 		$mntotcnt=$date_cnt;
								 		for($d=0;$d<$date_cnt;$d++){
								 		 if($mntotcnt%3==0){?>
											<td class="tar" style="color:brown;font-weight:bold;">
												<?php 
												$three_mnth_tot=$montotal[$d]+$montotal[$d+1]+$montotal[$d+2];
													if($three_mnth_tot){
														echo number_format($three_mnth_tot);
													} else{
														echo "";
														} 
												?>
											</td>
											<?php 
											$three_mnth_tot =0;
											}?>
								 			<td class="tar">
									 			<?php
									 				if($montotal[$d]){ 
									 					echo number_format($montotal[$d]); 
									 				} else {
									 					echo "";
									 				}
									 			?>
								 			</td>
								 	<?php
								 	//$three_mnth_tot=$montotal[$d+1]+$montotal[$d+2]+$montotal[$d+3];
								 	$mntotcnt--;
								 	$montotal[$d]=0;
								 	} ?>
								 </tr>
								<?php $totmain=0;}
								$tempmain =$get_det[$cnt]['Subject'];
							} ?>
			</table>
			</div>
		@elseif($request->active_select == 2)
		<div style="border: 1px solid white;overflow-x: scroll;" id="sidebar">
		<table class="tablealternate CMN_tblfixed" style="width: 980.75%!important;">
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader tac"> 
				  		<th class="tac" width="7%">{{ trans('messages.lbl_sno') }}</th>
				  		<th class="tac" width="34%">{{ trans('messages.lbl_mainsubject') }}</th>
				  		<th class="tac" width="23%">{{ trans('messages.lbl_subsubject') }}</th>
				  		<th class="tac" width="23%">{{ trans('messages.lbl_total') }}</th>
				  		<?php 
									for($i=0;$i<$ac_cnt;$i++){  ?>
									<th class="exedet_table_text" width="15.2%">
										<span title="">{{ $acArray[$i] }}{{ trans('messages.lbl_expmonth') }}</span>
									</th>
									<?php } ?>
				  	</tr>
				</thead>
				  	<tr style="text-align: center; height: 25px;vertical-align:middle;background-color:#DDDDDD;">
						<td style="border-left:1px dotted;border-right:1px dotted;"></td>
						<td style="border-right:1px dotted;text-align:right;"></td>
						<td style="border-right:1px dotted;text-align:right;"></td>
						<td style="border-right:1px dotted;text-align:right;"></td>
						<?php 
										for ($d=0;$d<$ac_cnt;$d++) {
									?>
										<td style="color:blue;cursor:default;font-weight:bold;" class="tar">
											<?php echo number_format($grndmontotal[$d]); ?>
										</td>
									<?php
									} ?>
					</tr>
					<?php
							$temp = 0;
							$totmain = 0;
							$montotal1 = 0;
							$row = 0;
							$tempmain = "";
						 for ($cnt=0;$cnt<$fileCnt;$cnt++){
									$loc = $cnt + 1;
									if($loc != $temp){
									if($row == 1){
										$style='style = "background-color:#E2F0F3;"';
										$row=0;
										
									} else {
										$style='style = ""';
										$row=1;
										}$styleTD = 'style = "border-top:1px dotted #000000;vertical-align: top;"';
									} else {
										 $styleTD = 'style = "border-top:0px dotted #000000;vertical-align: top;"';
									}
									$total =0;
									for($d=0;$d<$ac_cnt;$d++){
										$total =$total+$cus_det[$cnt][$acArray[$d]];
									}
									if($tempmain!=$cus_det[$cnt]['Subject']&& $cnt!=0){
								?>
								<tr style="height: 25px;vertical-align:middle;border-top:1px dotted;border-bottom:1px dotted;background-color:#DDDDDD;">
								 	<td colspan="3"></td>
								 	<td class="tar">
								 		<?php
								 			if($totmain){
								 				echo number_format($totmain);
								 			} else {
								 				echo "";
								 			}  
								 		?>
								 	</td>
								 	<?php
								 		for($d=0;$d<$ac_cnt;$d++){
								 	?>
								 			<td class="tar">
									 			<?php
									 				if($montotal[$d]){ 
									 					echo number_format($montotal[$d]); 
									 				} else {
									 					echo "";
									 				}
									 			?>
								 			</td>
								 	<?php
								 	$montotal[$d]=0;
								 	} ?>
							 </tr>
							 <?php
							 	$totmain=0;
							  } ?>
								<tr <?php echo $style;?> class="tax_data_name">
									<td class="tac"><?php echo $cnt+1; ?></td>
									<td>
									<?php 
										if($tempmain!=$cus_det[$cnt]['Subject']){
											if($cus_det[$cnt]['Subject']!="Loan Payment" && $cus_det[$cnt]['Subject']!="Petty"
																				&& $cus_det[$cnt]['Subject']!="Paid Salary"){?>
												<a class="anchorstyle" href="javascript:gotoexpensestransferhistory('{{$cus_det[$cnt]['id']}}','0','{{$request->mainmenu}}','','',2);" 
																	style="color:#0000FF;"
													@if(strlen($cus_det[$cnt]['Subject']) > $utf_lngth) 
		    												title="{{ $cus_det[$cnt]['Subject'] }}"
				    										@endif>
				    								@if(singlefieldlength($cus_det[$cnt]['Subject'],$utf_lngth))
				    									{{singlefieldlength($cus_det[$cnt]['Subject'],$utf_lngth)}}
													@else
														{{$cus_det[$cnt]['Subject']}}
													@endif
												</a>
											<?php
												} else {
													if ( $cus_det[$cnt]['Subject'] == "Loan Payment" ) {
														echo "Loan Payment";
													} else if ( $cus_det[$cnt]['Subject'] == "Petty" ) {
														echo "Petty Cash";
													} else {
														echo "Salary Paid";
													}
												}
										} else {
											echo "";
										}
									?>
									</td>
									<td>
										<?php 
											if($cus_det[$cnt]['sub']!="Loan Payment" && $cus_det[$cnt]['sub']!="Petty"
																				&& $cus_det[$cnt]['sub']!="Paid Salary"){
												?>
												<a class="anchorstyle" href="javascript:gotosubhistory('{{ $cus_det[$cnt]['subid'] }}','{{ $cus_det[$cnt]['sub'] }}','','0','{{ $request->mainmenu }}','sub','','',2);" 
																	style="color:#0000FF;"
													@if(strlen($cus_det[$cnt]['sub']) > $utf_lngth) 
		    												title="{{ $cus_det[$cnt]['sub'] }}"
				    										@endif>
				    								@if(singlefieldlength($cus_det[$cnt]['sub'],$utf_lngth))
				    									{{singlefieldlength($cus_det[$cnt]['sub'],$utf_lngth)}}
													@else
														{{$cus_det[$cnt]['sub']}}
													@endif
												</a>
											<?php
												} else {
													if ( $cus_det[$cnt]['sub'] == "Loan Payment" ) {
														echo "Loan Payment";
													} else if ( $cus_det[$cnt]['sub'] == "Petty" ) {
														echo "Petty Cash";
													} else {
														echo "Salary Paid";
													}
											}
										 ?>
									</td>
									<td class="tar">
								 		<?php
								 			if($total){
								 				echo number_format($total);
								 			} else {
								 				echo "";
								 			}  
								 		?>
								 	</td>
									<?php 
										for($d=0;$d<$ac_cnt;$d++){
									?>
									<td width="120px;" class="tar">
										<?php
											if($cus_det[$cnt][$acArray[$d]]){ 
												echo number_format($cus_det[$cnt][$acArray[$d]]);
											} else {
												echo "";
											} 
										?>
									</td>
									<?php } ?>
								</tr>
								<?php
								 $totmain=$totmain+$total;
									for($d=0;$d<$ac_cnt;$d++){
										if (!isset($montotal[$d])) {
											$montotal[$d] = 0;
										}
										$montotal[$d] =$montotal[$d]+$cus_det[$cnt][$acArray[$d]];
									}
								if($cnt==$fileCnt-1) { ?>
								<tr style="height: 25px;vertical-align:middle;border-top:1px dotted;border-bottom:1px dotted;background-color:#DDDDDD;">
								 	<td colspan="3"></td>
								 	<td class="tar">
								 		<?php
								 			if($totmain){
								 				echo number_format($totmain);
								 			} else {
								 				echo "";
								 			}  
								 		?>
								 	</td>
								 	<?php
								 		for($d=0;$d<$ac_cnt;$d++){
								 	?>
						 			<td class="tar">
							 			<?php
							 				if($montotal[$d]){ 
							 					echo number_format($montotal[$d]); 
							 				} else {
							 					echo "";
							 				}
							 			?>
						 			</td>
								 	<?php
								 	$montotal[$d]=0;
								 	} ?>
								 </tr>
								<?php $totmain=0;}
									$tempmain = $cus_det[$cnt]['Subject'];
								} ?>
			</table>
		@elseif($request->active_select == 1)
			<div style="border: 1px solid white;overflow-x: scroll;">
		<table class="tablealternate CMN_tblfixed" style="width: 152%!important;">
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader fwb tac"> 
				  		<th class="tac" width="5%"></th>
				  		<th class="tac" width="10%">{{ trans('messages.lbl_total') }}</th>
						<?php 
									for($i=0;$i<$mn_cnts;$i++){  ?>
									<th class="exedet_table_text" width="6%">
										<span title="">{{ $mnArrays[$i] }}{{ trans('messages.lbl_expmonth') }}</span>
									</th>
									<?php } ?>
				  	</tr>
				</thead>
				<tr style=" height: 25px;vertical-align:middle;border-bottom:1px dotted;background-color:#bdbdbd;">
									<td colspan="2"></td>
									<?php 
										for ($d=0;$d<$mn_cnt;$d++) {
									?>
										<td style="color:blue;cursor:default;font-weight:bold;" class="tar">
											<?php echo number_format($grndmontotal[$d]); ?>
										</td>
									<?php
									} ?>

								</tr>
				<?php 
					$temp = 0;
					$row = 1;
				for ($cnt=0;$cnt<$fileCnt;$cnt++){
									$loc = $cnt + 1;
									if($loc != $temp){
									if($row == 1){
										$style='style = "background-color:#E2F0F3;"';
										$row=0;
										
									} else {
										$style='style = ""';
										$row=1;
										}$styleTD = 'style = "border-top:1px dotted #000000;vertical-align: top;"';
									} else {
										 $styleTD = 'style = "border-top:0px dotted #000000;vertical-align: top;"';
									}
									$total =0;
									for($d=0;$d<$mn_cnt;$d++){
										$total =$total+$mon_det[$cnt][$mnArray[$d]];
									}
								?>
								<tr <?php echo $style;?> class="tax_data_name">
									<td class="tac">{{ $mon_det[$cnt]['Period'] }}{{ trans('messages.lbl_expperiod') }}</td>
									<td class="tar" style="background-color:#DDDDDD;">
								 		<?php
								 			if($total){
								 				echo number_format($total);
								 			} else {
								 				echo "";
								 			}  
								 		?>
								 	</td>
								 	<?php for($d=0;$d<$mn_cnt;$d++){ ?>
								 		<td class="tar">
								 			<?php 
								 			if($mon_det[$cnt][$mnArray[$d]]){
								 				echo number_format($mon_det[$cnt][$mnArray[$d]]); 
								 			}else{
								 				echo "";
								 			}
								 			?>
								 		</td>
								 	<?php } ?>
								</tr>
								<?php } ?>
			</table>
		@endif
		@endif
		</div>
	</div>
	{{ Form::close() }}
</article>
</div>
@endsection