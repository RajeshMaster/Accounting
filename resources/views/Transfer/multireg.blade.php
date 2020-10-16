@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/transfer.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::script('resources/assets/js/lib/additional-methods.min.js') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	$(document).ready(function() {
		setDatePicker("txt_startdate");
	});
</script>
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_2">
	{{ Form::open(array('name'=>'frmmultireg',
						'id'=>'frmmultireg',
						'url' => 'Transfer/multiregprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
		{{ Form::hidden('count', $count, array('id' => 'count')) }}
	<!-- Start Heading -->
	<div class="row hline pm0">
	<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/expenses_icon.png') }}">
			<h2 class="pull-left pl5 mt10">{{ trans('messages.lbl_transfer') }}<span>・</span><span class="colbl">{{ trans('messages.lbl_multireg') }}</span></h2>
		</div>
	</div>
	<div class="pb10"></div>
	<!-- End Heading -->
	<div class="col-xs-12 mt5 mb10">
		<div class="col-xs-3 text-right clr_blue">
			<label>{{ trans('messages.lbl_Date') }}<span class="fr ml2 red"> * </span></label>
		</div>
		<div class="">
			{{ Form::text('txt_startdate','',array(
												'id'=>'txt_startdate',
												'name' => 'txt_startdate',
												'class'=>'box9per txt_startdate form-control',
												'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
												'data-label' => trans('messages.lbl_Date'),
												'maxlength' => '10')) }}
			<label class="fa fa-calendar fa-lg" for="txt_startdate" aria-hidden="true"></label>
			@if (Session::get('userclassification') == 4)
				&nbsp;&nbsp;{{ Form::checkbox('accessrights', 1,1, ['id' => 'accessrights']) }}
				&nbsp;<label for="accessrights"><span class="grey fb">{{ trans('messages.lbl_accessrights') }}</span></label>
			@endif
		</div>
	</div>
	<div class="mr10 ml10">
		<div class="minh400">
			<table class="tablealternate box100per">
				<colgroup>
					<col width="3%">
					<col width="19%">
					<col width="19%">
					<col width="12%">
					<col width="12%">
					<col width="15%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
					<tr> 
						<th class="tac">{{ trans('messages.lbl_sno') }}</th>
						<th class="tac">{{ trans('messages.lbl_mainsubject') }}</th>
						<th class="tac">{{ trans('messages.lbl_subsubject') }}</th>
						<th class="tac">{{ trans('messages.lbl_amount') }}</th>
						<th class="tac">{{ trans('messages.lbl_charge') }}</th>
						<th class="tac">{{ trans('messages.lbl_remarks') }}</th>
					</tr>
				</thead>
				<tbody>
					<?php
									$loc = "";
									$temp1 = "";
									$cnt = "";
									for ($j=0;$j<count($getreg_det);$j++) {
										$loc = $getreg_det[$j]['loc'];
										$loc1 = $getreg_det[$j]['BankName']."-".$getreg_det[$j]['bankaccno'];
										$loc2 = $getreg_det[$j]['bankaccno'];
										
										if( $loc1 != $temp1){
											$style='style="background-color:white;"';
											$styleTD1 = 'style="border-top:1px dotted black;"'; 
										} else {
											$style='style="background-color:#dff1f4;"';
											$styleTD1 = 'style="border-top:1px dotted black;vertical-align: top;"';
										}	
								if($loc1 != $temp1 || $loc2 != $temp2) { ?>
								<tr class="box100per boxhei25">
										<td  align="left" colspan="6" class="box100per" style="background-color:lightgrey;border-top: 1.1px dotted black;font-weight:bold;" >
											<?php
												echo $getreg_det[$j]["BankName"]."-".$getreg_det[$j]["bankaccno"];
											?>
										</td>
								</tr>
								<?php }  ?>
								{{ Form::hidden('id_'.$j, $getreg_det[$j]['id'], array('id' => 'id_'.$j)) }}
								{{ Form::hidden('loanno_'.$j, $getreg_det[$j]['billno'], array('id' => 'loanno_'.$j)) }}
								{{ Form::hidden('bankname_'.$j, $getreg_det[$j]['bankname_id'], array('id' => 'bankname_'.$j)) }}
								{{ Form::hidden('bankaccno_'.$j, $getreg_det[$j]['bankaccno'], array('id' => 'bankaccno_'.$j)) }}
								{{ Form::hidden('loan_flg_'.$j, $getreg_det[$j]['loan_flg'], array('id' => 'loan_flg_'.$j)) }}
								{{ Form::hidden('loanType_'.$j, $getreg_det[$j]['loanType'], array('id' => 'loanType_'.$j)) }}
								{{ Form::hidden('subject_'.$j, $getreg_det[$j]['subject'], array('id' => 'subject_'.$j)) }}
								{{ Form::hidden('details_'.$j, $getreg_det[$j]['details'], array('id' => 'details_'.$j)) }}
								<tr loanType_<?php echo $style;?> id="<?php echo $cnt;?>" style="width: 100%;">
										<td width="3%" align="center"
											align="center" >
											<div style="cursor: text;" id="orderno_<?php echo $cnt;?>">
												{{ $j + 1 }}
											</div>
										</td>
										<?php if (Session::get('languageval') == "en") { ?>
										<td  style="width: 13%" align="left">
											<?php
												if (mb_strlen($getreg_det[$j]["mainSubject"], 'UTF-8') >= 16) {
														$str = mb_substr(ucwords($getreg_det[$j]["mainSubject"]), 0, 15, 'UTF-8');
														echo "<span title = '".ucwords($getreg_det[$j]["mainSubject"])."'>".$str."...</span>"; 
													} else {
														if($getreg_det[$j]['salaryFlg'] != 1){
															echo ucwords(strtolower($getreg_det[$j]["mainSubject"])) ;
														} else {
															echo "Salary";
														}
												}?>
										</td>
										<?php } else { ?>
										<td  style="width: 13%;" align="left">
											<?php
												if (mb_strlen($getreg_det[$j]["Subject_jp"], 'UTF-8') >= 16) {
														$str = mb_substr(ucwords($getreg_det[$j]["Subject_jp"]), 0, 15, 'UTF-8');
														echo "<span title = '".ucwords($getreg_det[$j]["Subject_jp"])."'>".$str."...</span>"; 
													} else {
														if($getreg_det[$j]['salaryFlg'] != 1){
															echo ucwords(strtolower($getreg_det[$j]["Subject_jp"])) ;
														} else {
															echo "Salary";
														}
												} ?>
										</td>
										<?php }?>
										<?php if($getreg_det[$j]['loan_flg'] != 1) {?>
										<?php if (Session::get('languageval') == "en") { ?>
										<td  style="width: 13%;" align="left">
											<?php
												if (mb_strlen($getreg_det[$j]["sub_eng"], 'UTF-8') >= 16) {
														$str = mb_substr(ucwords($getreg_det[$j]["sub_eng"]), 0, 15, 'UTF-8');
														echo "<span title = '".ucwords($getreg_det[$j]["sub_eng"])."'>".$str."...</span>"; 
													} else {
														if($getreg_det[$j]['salaryFlg'] != 1){
															echo ucwords(strtolower($getreg_det[$j]["sub_eng"])) ;
														} else {
															echo "Salary";
														}
												} ?>
										</td>
										<?php } else { ?>
										<td  style="width: 13%;" align="left">
											<?php
												if (mb_strlen($getreg_det[$j]["sub_jap"], 'UTF-8') >= 16) {
														$str = mb_substr(ucwords($getreg_det[$j]["sub_jap"]), 0, 15, 'UTF-8');
														echo "<span title = '".ucwords($getreg_det[$j]["sub_jap"])."'>".$str."...</span>"; 
													} else {
														if($getreg_det[$j]['salaryFlg'] != 1){
															echo ucwords(strtolower($getreg_det[$j]["sub_jap"])) ;
														} else {
															echo "Salary";
														}
												} ?>
										</td>
										<?php }	} else {?>
										<td style="width: 13%;" align="left">
											{{ trans('messages.lbl_loanpay') }}
										</td>
										<?php } ?>
										<td>
											@if($getreg_det[$j]['salaryFlg']!="1")
												{{ Form::text('amount'.$j,(isset($getreg_det[$j]["amount"])) ? $getreg_det[$j]["amount"] : '',array('id'=>'amount'.$j, 
																		'name' => 'amount'.$j,
																		'maxlength' => '14',
																		'style'=>'text-align:right;padding-right:4px;',
																		'onkeypress' => 'return isNumberKey(event)',
																		'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
																		'class'=>'box99per form-control')) }}
											@else
												{{ Form::text('amount'.$j,'',array('id'=>'amount'.$j, 
																		'name' => 'amount'.$j,
																		'disabled' => 'disabled',
																		'onkeypress' => 'return isNumberKey(event)',
																		'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
																		'class'=>'box99per form-control')) }}
											@endif
										</td>
										<td>
											@if($getreg_det[$j]['salaryFlg']!="1")
												{{ Form::text('charge'.$j,(isset($getreg_det[$j]["fee"])) ? $getreg_det[$j]["fee"] : '',array('id'=>'charge'.$j, 
																		'name' => 'charge'.$j,
																		'maxlength' => '14',
																		'style'=>'text-align:right;padding-right:4px;',
																		'onkeypress' => 'return isNumberKey(event)',
																		'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
																		'class'=>'box99per form-control')) }}
											@else
												{{ Form::text('charge'.$j,(isset($getreg_det[$j]["remark_dtl"])) ? $getreg_det[$j]["remark_dtl"] : '',array('id'=>'charge'.$j, 
																		'name' => 'charge'.$j,
																		'disabled' => 'disabled',
																		'class'=>'box99per form-control')) }}
											@endif
										</td>
										<td>
											{{ Form::textarea('remarks'.$j,'', array('name' => 'remarks'.$j,
																				'id' => 'remarks'.$j,
																				'class' => 'box99per form-control',
																				'size' => '20x1')) }}
										</td>
									<?php $temp = $loc;
										  $temp1 = $loc1;
										  $temp2 = $loc2; 	
									} ?>
								</tr>
				</tbody>
			</table>
		</div>
	<fieldset style="background-color: #DDF1FA;">
		<div class="form-group">
			<div align="center" class="mt5">
				<button type="submit" class="btn btn-success add box100 multiregprocess ml5">
						<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
					</button>
				<a onclick="javascript:gotoindexpageback('2','{{$request->mainmenu}}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				</a>
			</div>
		</div>
	</fieldset>
	</div>
</article>
</div>
<div class="CMN_display_block pb10"></div>
{{ Form::close() }}
{{ Form::open(array('name'=>'transfermultiaddeditcancel', 'id'=>'transfermultiaddeditcancel', 'url' => 'Transfer/multiregprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
		{{ Form::hidden('count', $count, array('id' => 'count')) }}
@endsection