@extends('layouts.app')
@section('content')
<?php use App\Model\Transfer; ?>
{{ HTML::style('resources/assets/css/common.css') }}
{{ HTML::style('resources/assets/css/widthbox.css') }}
{{ HTML::script('resources/assets/css/bootstrap.min.css') }}
{{ HTML::script('resources/assets/js/transfer.js') }}
{{ HTML::script('resources/assets/js/expenses.js') }}
{{ HTML::style('resources/assets/css/sidebar-bootstrap.min.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	function pageClick(pageval) {
		$('#page').val(pageval);
		$("#transferhistory").submit();
	}
	function pageLimitClick(pagelimitval) {
		$('#page').val('');
		$('#plimit').val(pagelimitval);
		$("#transferhistory").submit();
	}
	function gotosalaryhistorydownload(mainmenu){
		$('#page').val('');
		$('#transferhistorydownload').attr('action', 'salaryhistorydownload?mainmenu='+mainmenu+'&time='+datetime);
		$("#transferhistorydownload").submit();
	}
</script>
	<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	@if($request->mainmenu == "expenses")
		<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_1">
	@elseif($request->mainmenu == "company_transfer")
		<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_2">
	@else
		<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_1">
	@endif
	@if($request->pettyflg == "1")
		{{ Form::open(array('name'=>'transferhistory', 'id'=>'transferhistory', 'url' => 'Expenses/expenseshistory?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
	@else
		{{ Form::open(array('name'=>'transferhistory', 'id'=>'transferhistory', 'url' => 'Transfer/transferhistory?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
	@endif
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('hiddenplimit', $request->hiddenplimit , array('id' => 'hiddenplimit')) }}
		{{ Form::hidden('hiddenpage', $request->hiddenpage , array('id' => 'hiddenpage')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('month',$request->month, array('id' => 'month')) }}
		{{ Form::hidden('year',$request->year, array('id' => 'year')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('salaryflg',$request->salaryflg, array('id' => 'salaryflg')) }}
	    {{ Form::hidden('loan_flg',$request->loan_flg, array('id' => 'loan_flg')) }}
	    {{ Form::hidden('pettyflg',$request->pettyflg, array('id' => 'pettyflg')) }}
	    {{ Form::hidden('delflg',$request->delflg, array('id' => 'delflg')) }}
	    {{ Form::hidden('subject',$request->subject, array('id' => 'subject')) }}
	    {{ Form::hidden('bname',$request->bname, array('id' => 'bname')) }}
	    {{ Form::hidden('bankName',$request->bankName, array('id' => 'bankName')) }}
	    {{ Form::hidden('accNo',$request->accNo, array('id' => 'accNo')) }}
	    {{ Form::hidden('empid','', array('id' => 'empid')) }}
	    {{ Form::hidden('empname',$request->empname, array('id' => 'empname')) }}
	    {{ Form::hidden('backflg',$request->backflg, array('id' => 'backflg')) }}
	    {{ Form::hidden('exptype1',$request->exptype1, array('id' => 'exptype1')) }}
	    {{ Form::hidden('flgs',$request->flgs, array('id' => 'flgs')) }}
	    {{ Form::hidden('expdetails',$request->expdetails, array('id' => 'expdetails')) }}
	    {{ Form::hidden('active_select', $request->active_select, array('id' => 'active_select')) }}
	    {{ Form::hidden('filter', $request->filter, array('id' => 'filter')) }}
	    {{ Form::hidden('id','', array('id' => 'id')) }}
	    {{ Form::hidden('editflg','', array('id' => 'editflg')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
	<!-- Start Heading -->
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10 ml10" src="{{ URL::asset('resources/assets/images/expenses.png') }}">
			<h2 class="pull-left pl5 mt10">
				@if($request->salaryflg != 1)
					@if($request->pettyflg != 1)
						@if($request->loan_flg != 1)
							{{ trans('messages.lbl_exptransferhistory') }}
						@else
							{{ trans('messages.lbl_loanhistory') }}
						@endif
					@else
						@if($request->delflg == 0)
							{{ trans('messages.lbl_pettycashhistory') }}
						@else
							{{ trans('messages.lbl_pettycashsubhistory') }}
						@endif
					@endif
				@else
					{{ trans('messages.lbl_salaryhistory') }}
				@endif
			</h2>
		</div>
	</div>
	<div class="col-xs-12 pt5">
			<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
				<a href="javascript:gotoindexexpenses2('{{ $request->year }}','{{ $request->month }}','{{ $request->mainmenu }}','{{ $request->hiddenplimit }}','{{ $request->hiddenpage }}');" class="btn btn-info box80">
					<span class="fa fa-arrow-left"></span>
					{{ trans('messages.lbl_back') }}
				</a>
			</div>
	</div>
	<div class="col-xs-12 mt5">
		<div class="col-xs-9" style="text-align: left;margin-left: -15px;">
			<label class="clr_blue">{{ trans('messages.lbl_mainsubject').'   :' }}</label>
			<span class="mr40" style="color:black">
				<b>
					@if($request->salaryflg != 1)
						@if($request->pettyflg != 1)
							@if($request->loan_flg == 1)
								@if(isset($get_det[0]['bankname']) || isset($get_det[0]['AccNo']))
									{{ $get_det[0]['bankname'] }} - {{ $get_det[0]['AccNo'] }}
								@endif
							@else
								@if(count($get_det) == "")
									@if(isset($request->bname))
										{{ $request->bname }}
									@else
										{{ $request->subject }}
									@endif
								@else
									@if(Session::get('languageval') == 'en')
										{{ $get_det[0]['Subject'] }}
									@else
										{{ $get_det[0]['Subject_jp'] }}
									@endif
								@endif
							@endif
						@else
							@if($request->delflg == 0)
								{{ trans('messages.lbl_pettycash')}}
							@elseif($request->delflg == 1)
								{{ trans('messages.lbl_pettycash')}} -> {{ trans('messages.lbl_expenses')}}
							@else
								{{ trans('messages.lbl_pettycash')}} -> {{ trans('messages.lbl_cash')}}
							@endif
						@endif
					@else
						@if($get_det[0]['bankaccno'] == "")
							{{ $get_det[0]['bankname'] }}
						@else
							{{ $get_det[0]['bankname'] }} - {{ $get_det[0]['bankaccno'] }}
						@endif
					@endif
				</b> 
			</span>
		</div>
		<div class="mt5 pull-right" title="Expenses Download">
			@if($request->salaryflg == 1)
				<a href="javascript:gotosalaryhistorydownload('{{ $request->mainmenu }}');"><span class="fa fa-download mr5"></span>{{ trans('messages.lbl_expdownload')}}</a>
            @else
            	<a href="javascript:gotohistorydownload('{{ $request->mainmenu }}');"><span class="fa fa-download mr5"></span>{{ trans('messages.lbl_expdownload')}}</a>
            @endif
          </div>
	</div>
	<div class="pt43 minh200 pl15 pr15">
		<table class="tablealternate CMN_tblfixed">
			<colgroup>
				<col width="4%">
				<col width="8%">
				@if($request->mainmenu == "company_transfer")
					@if($request->flgs == "2")
						<col width="6%">
					@else
						<col width="10%">
					@endif
				@else
					@if($request->exptype1 == 1)
						<col width="12%">
					@elseif($request->type == "sub")
						<col width="6%">
					@elseif($request->exptype1 == 2)
						<col width="12%">
					@else
						<col width="14%">
					@endif
				@endif
				<col>
				<col width="12%">
				@if($request->salaryflg != 1)
					@if($request->pettyflg != 1)
						@if($request->loan_flg != 1)
							<col width="14%">
						@else
							<col width="14%">
						@endif
					@else
						@if($request->delflg == 0)
						@else
						@endif
					@endif
				@else
				<col width="14%">
				@endif
				@if($request->mainmenu == "company_transfer")
					@if($request->flgs == "2")
						<col width="14%">
					@else
						<col width="6%">
					@endif
				@else
					@if($request->exptype1 == 1)
						<col width="6%">
					@elseif($request->exptype1 == 2)
						<col width="6%">
					@else
						<col width="14%">
					@endif
				@endif
				@if($request->mainmenu == "company_transfer")
					@if($request->flgs == "2")
						<col width="4%">
					@else
						<col width="20%">
					@endif
				@else
					@if($request->exptype1 == 1)
						<col width="20%">
					@elseif($request->exptype1 == 2)
						<col width="20%">
					@else
						<col width="6%">
					@endif
				@endif
				@if($request->mainmenu == "company_transfer")
					@if($request->flgs == "2")
						<col width="14%">
					@else
						<col width="6%">
					@endif
				@else
					@if($request->exptype1 == "1")
						<col width="6%">
					@elseif($request->exptype1 == 2)
						<col width="6%">
					@else
						<col width="20%">
					@endif
				@endif
			</colgroup>
			<thead class="CMN_tbltheadcolor">
				<tr>
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam">{{ trans('messages.lbl_Date') }}</th>
					@if($request->salaryflg == 1)
						<th class="vam">{{ trans('messages.lbl_month') }}</th>
					@endif
					@if($request->salaryflg != 1)
						<th class="vam">{{ trans('messages.lbl_bank') }}</th>
					@else
						<th class="vam">{{ trans('messages.lbl_empName') }}</th>
					@endif
					@if($request->salaryflg != 1)
						<th class="vam">{{ trans('messages.lbl_subsubject') }}</th>
					@else
						<th class="vam">{{ trans('messages.lbl_subject') }}</th>
					@endif
					<th class="vam">{{ trans('messages.lbl_amount') }}</th>
					<th class="vam">{{ trans('messages.lbl_charge') }}</th>
					<th class="vam">{{ trans('messages.lbl_bill') }}</th>
					<th class="vam">{{ trans('messages.lbl_remarks') }}</th>
					@if($request->pettyflg != 1 && $request->salaryflg != 1)
						<th class="vam">{{ trans('messages.lbl_edit') }}</th>
					@endif
				</tr>
			</thead>
			<tbody>
				@php $count = count($get_det); @endphp
				{{--*/ $loc4 = '' /*--}}
				{{ $temp = ""}}
				{{ $temp1 = ""}}
				{{ $temp2 = ""}}
				{{ $temp3 = ""}}
				{{ $temp4 = ""}}
				{{ $temp5 = ""}}
				{{ $temp6 = ""}}
				{{--*/ $rowbktrclrr = 0 /*--}}
				{{--*/ $row = '0' /*--}}
				{{--*/ $row1 = '0' /*--}}
				{{--*/ $row2 = '0' /*--}}
				{{--*/ $row3 = '0' /*--}}
				{{--*/ $row4 = '0' /*--}}
				{{--*/ $tmpyr = 0 /*--}}
				@if(!empty($count))
					@for($j = 0; $j <$count; $j++)
						{{--*/ $tempdate = $get_det[$j]['date']; /*--}}
						@if($request->type != 4 && isset($get_det[$j]['salaryMonth']))
							{{--*/ $tempmonth = $get_det[$j]['salaryMonth']; /*--}}
						@endif
						@if($tempdate != $get_det[$j]['id'])
							@if($rowbktrclrr == 1)
								<?php
									$style = 'background-color:#dff1f4;';
									$rowbktrclrr = 0;
								?>
							@else
								<?php
									$style = '#FFFFFF';
									$rowbktrclrr = 1;
								?>
							@endif
						@endif
						{{--*/ $loc = $get_det[$j]['year'] /*--}}
                    {{--*/ $loc1 = $get_det[$j]['month'] /*--}}
                  	@if($loc != $temp || $loc1 != $temp1) 
						@if($row==1)
							{{--*/ $style_tr = 'background-color: #A7CEC9;' /*--}}
							{{--*/ $row = '0' /*--}}
						@else
							{{--*/ $style_tr = 'background-color: #FFFFFF;' /*--}}
							{{--*/ $row = '1' /*--}}
						@endif
						{{--*/ $style_td = 'border-bottom: 1 px dotted black;' /*--}}
					@else
						{{--*/ $style_td = 'border-top:hidden;' /*--}}
					@endif
					{{--*/ $loc2 = $get_det[$j]['date'] /*--}}
                  	@if($loc2 != $temp2) 
						@if($row1==1)
							{{--*/ $style_tr = 'background-color: #A7CEC9;' /*--}}
							{{--*/ $row1 = '0' /*--}}
						@else
							{{--*/ $style_tr = 'background-color: #FFFFFF;' /*--}}
							{{--*/ $row1 = '1' /*--}}
						@endif
						{{--*/ $style_td1 = 'border-bottom: 1 px dotted black;' /*--}}
					@else
						{{--*/ $style_td1 = 'border-top:hidden;' /*--}}
					@endif
					@if(isset($get_det[$j]['salaryMonth']))
					{{--*/ $loc3 = $get_det[$j]['salaryMonth'] /*--}}
                  	@if($loc3 != $temp3) 
						@if($row2==1)
							{{--*/ $style_tr = 'background-color: #A7CEC9;' /*--}}
							{{--*/ $row2 = '0' /*--}}
						@else
							{{--*/ $style_tr = 'background-color: #FFFFFF;' /*--}}
							{{--*/ $row2 = '1' /*--}}
						@endif
						{{--*/ $style_td2 = 'border-bottom: 1 px dotted black;' /*--}}
					@else
						{{--*/ $style_td2 = 'border-top:hidden;' /*--}}
					@endif
					@endif 
					@if(isset($get_det[$j]['bankname']))
					{{--*/ $loc4 = $get_det[$j]['bankname'] /*--}}
                  	@if($loc4 != $temp4) 
						@if($row3==1)
							{{--*/ $style_tr = 'background-color: #A7CEC9;' /*--}}
							{{--*/ $row3 = '0' /*--}}
						@else
							{{--*/ $style_tr = 'background-color: #FFFFFF;' /*--}}
							{{--*/ $row3 = '1' /*--}}
						@endif
						{{--*/ $style_td3 = 'border-bottom: hidden;' /*--}}
					@else
						{{--*/ $style_td3 = 'border-top:hidden;' /*--}}
					@endif
					@endif
					@if(isset($get_det[$j]['nickname']))
					{{--*/ $loc5 = $get_det[$j]['nickname'] /*--}}
					{{--*/ $loc6 = $get_det[$j]['bankaccno'] /*--}}
                  	@if($loc5 != $temp5 && $loc6 != $temp6) 
						@if($row4==1)
							{{--*/ $style_tr = 'background-color: #A7CEC9;' /*--}}
							{{--*/ $row4 = '0' /*--}}
						@else
							{{--*/ $style_tr = 'background-color: #FFFFFF;' /*--}}
							{{--*/ $row4 = '1' /*--}}
						@endif
						{{--*/ $style_td4 = 'border-bottom: hidden;' /*--}}
					@else
						{{--*/ $style_td4 = 'border-top:hidden;' /*--}}
					@endif
					@endif
						@if($j == 0 )
					 		<tr height="25px;">
					 		<?php if($request->salaryflg == 1){ 
					 				$colspan = "5";
					 			} else {
					 				$colspan = "4";
					 			} ?>
								<td  colspan="<?php echo $colspan;?>" style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;text-align:right;padding-right: 5px;color:blue;font-weight:bold;">
								<?php echo "Grant Total"; ?> 
								</td>
								<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;padding-right:5px;color:blue;font-weight:bold;">
								<?php echo "¥ ".number_format($amountTotal); ?> 
								</td>
								<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;padding-right:5px;color:blue;font-weight:bold;">
								 <?php echo "¥ ".number_format($chargeTotal); ?>
								 </td>
								<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;font-weight:bold;"> </td>
								<?php if($request->pettyflg != 1 && $request->salaryflg != 1){ ?>
								<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-bottom:1px dotted #136E83;font-weight:bold;"> </td>
								 <?php } ?>
								<td  style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;text-align:right;padding-right: 5px;color:blue;font-weight:bold;">
								</td>
							</tr>
						@endif
						{{--*/ $amt = '0' /*--}}
						{{--*/ $fee = '0' /*--}}
						<?php if($tmpyr!=$get_det[$j]['year'] || $tmpmth!=$get_det[$j]['month'])  { ?>
							@if($request->pettyflg != 1)
								@if($request->loan_flg == 1  && $request->flgs == "5")
									<?php $res = Transfer::loanhistorydetailsamount($request,$request->subject,$get_det[$j]['year'],$get_det[$j]['month']); ?>
								@elseif($request->salaryflg == 1)
									@if($request->flgs == 2)
										<?php $res = Transfer::salaryhistorydetailsamount($request,$request->bname,$request->accNo,$get_det[$j]['year'],$get_det[$j]['month']); ?>
									@else
										<?php $request->bankName = 999; $res = Transfer::salaryhistorydetailsamount($request,$request->bankName,$request->accNo,$get_det[$j]['year'],$get_det[$j]['month']);
										 ?>
									@endif
									
								@else
									<?php $res = Transfer::transferhistorydetailsamount($request,$request->subject,$get_det[$j]['year'],$get_det[$j]['month']); ?>
								@endif
							@else
								@if($request->delflg == 0)
									<?php $res = Transfer::pettycash_history_details($get_det[$j]['year'],$get_det[$j]['month']); ?>
								@else
									<?php $res = Transfer::pettycash_subhistory_details($request->delflg,$get_det[$j]['year'],$get_det[$j]['month']); ?>
								@endif
							@endif
							@foreach($res as $key => $value)
								@if($request->salaryflg == 1)
									<?php $resultval = str_replace(",", "", $value->amount); ?>
								@else
									<?php $resultval = $value->amount; ?>
								@endif
								@if($request->pettyflg != 1)
									@if(isset($value->fee))
										@if($request->salaryflg == 1)
											<?php $resultfee = str_replace(",", "", $value->fee); ?>
										@else
											<?php $resultfee = $value->fee; ?>
										@endif
									@else
										<?php  $resultfee = 0; ?>
									@endif
								@endif
								<?php 
								$results=$resultval;
								if($request->pettyflg != 1) {
									if(isset($value->fee)) {
										$result1=$resultfee;
										$fee=$fee+$result1; 
									}
								}
								$amt=$amt+$results; ?>
							@endforeach
							<?php if($request->salaryflg == 1){ 
					 				$colspan = "5";
					 			} else {
					 				$colspan = "4";
					 			} ?>
							<tr>
							@if($loc != $temp || $loc1 != $temp1)
								<td  colspan="<?php echo $colspan;?>" style="background-color:lightgrey;color:black;border-bottom:1px dotted #136E83;vertical-align:middle;border-right:1px dotted #136E83;font-weight:bold;">
								<?php echo $get_det[$j]['year']."年".$get_det[$j]['month']."月"; ?> 
								</td>
								<td align="right" style="background-color:lightgrey;color:black;border-bottom:1px dotted #136E83;vertical-align:middle;border-right:1px dotted #136E83;padding-right:5px;font-weight:bold;">
								<?php echo number_format($amt); ?> 
								</td>
								<td align="right" style="background-color:lightgrey;color:black;border-bottom:1px dotted #136E83;vertical-align:middle;border-right:1px dotted #136E83;padding-right:5px;font-weight:bold;">
								 <?php
								 	 echo number_format($fee); ?>
								 </td>
								<td align="right" style="background-color:lightgrey;color:black;border-bottom:1px dotted #136E83;vertical-align:middle;border-right:1px dotted #136E83;"> </td>
								<?php if($request->pettyflg != 1 && $request->salaryflg != 1){ ?>
								<td align="right" style="background-color:lightgrey;color:black;border-bottom:1px dotted #136E83;vertical-align:middle;"> </td>
								<?php }?>
								<td  style="background-color:lightgrey;color:black;border-bottom:1px dotted #136E83;vertical-align:middle;border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;text-align:right;padding-right: 5px;color:blue;">
								</td>
								@endif
							</tr>
						<?php } ?> <?php
							if($request->pettyflg != "1") {
								$tempmonth = $get_det[$j]['salaryMonth'];
							} ?>
						<tr style="<?php echo $style;?>">
							<td class="tac">
								{{ ($index->currentpage()-1) * $index->perpage() + $j + 1 }}
							</td>
							<td align="center" style="{{ $style_td1 }}">
								@if($loc2 != $temp2)
									{{ $get_det[$j]['date'] }}
								@endif
							</td>
							@if($request->salaryflg == 1)
								<td align="center" style="{{ $style_td2 }}">
									@if($loc3 != $temp3)
										{{ $get_det[$j]['salaryMonth'] }}
									@endif
								</td>
							@endif
							<?php
								$temp=0;
								if ($temp !=$get_det[$j]['bankname']) {
									$styleTDD = 'style="border-top: 1px dotted #136E83; "'; 
								} else {
									$styleTDD = 'style="border-top:0px dotted #FFFFFF; "';
								}
							?>
								 <?php if($request->pettyflg != 1){?>
								 <?php if($request->loan_flg != 1){?>
								 	<td align="left"  <?php echo $styleTDD;?>>
								     <a style="text-decoration: none;" style="color: blue;" href="javascript:gotoempnamesubhistory('{{ $get_det[$j]['empNo'] }}','{{ $get_det[$j]['EmpName'] }}','{{ $get_det[$j]['bankId'] }}','{{ $get_det[$j]['bankaccno'] }}','{{ $request->mainmenu }}',4);">
										<font color="blue">
										<?php if($request->salaryflg == 1){
											if (isset($get_det[$j]['EmpName']) && mb_strlen($get_det[$j]['EmpName'], 'UTF-8') >= 16) {
												$str = mb_substr($get_det[$j]['EmpName'], 0, 15, 'UTF-8');
												echo "<span title = '".$get_det[$j]['EmpName']."'>".$str."...</span>"; 
											} else {
												if(isset($get_det[$j]['EmpName'])) {
													echo $get_det[$j]['EmpName'];
												}
											}
										} else { ?></font></a> 
										<?php 
										     	if ($get_det[$j]['bankname']=="Cash") {
										     		if($loc4 != $temp4) {
										     			echo "Cash";
										     		}
										     	} else {
										     		if(isset($get_det[$j]['nickname'])) {
										     			if($loc5 != $temp5 && $loc6 != $temp6) {
										     				echo $get_det[$j]['nickname']."-".$get_det[$j]['bankaccno']; 
										     			}
										     		}
										     	}
								    	}?>
									</td>
								<?php } else { ?>
									<td align="left"  <?php echo $styleTDD;?>>
										<?php echo "Loan"; ?>
									</td>
								<?php } } else {?>
								    <td align="left"  <?php echo $styleTDD;?>>
										<?php 
										if ($get_det[$j]['del_flg'] == 1) {
											if (Session::get('languageval') == 'en') {
												if (mb_strlen($get_det[$j]['Subject'], 'UTF-8') >= 12) 
												{
														$str = mb_substr($get_det[$j]['Subject'], 0, 11, 'UTF-8');
														echo "<span title = '".$get_det[$j]['Subject']."'>".$str."...</span>"; 
												} else {
													echo $get_det[$j]['Subject'];
												}
										 	} else {
										 		if (mb_strlen($get_det[$j]['Subject_jp'], 'UTF-8') >= 12) 
												{
														$str = mb_substr($get_det[$j]['Subject_jp'], 0, 11, 'UTF-8');
														echo "<span title = '".$get_det[$j]['Subject_jp']."'>".$str."...</span>"; 
												} else {
													echo $get_det[$j]['Subject_jp'];
												}
										 	}
										} else {
											if (isset($get_det[$j]['Bank_NickName']) && mb_strlen($get_det[$j]['Bank_NickName']."-".$get_det[$j]['bankaccno'], 'UTF-8') >= 12) 
												{
														$str = mb_substr($get_det[$j]['Bank_NickName']."-".$get_det[$j]['bankaccno'], 0, 11, 'UTF-8');
														echo "<span title = '".$get_det[$j]['Bank_NickName']."-".$get_det[$j]['bankaccno']."'>".$str."...</span>"; 
												} else {
													if(isset($get_det[$j]['Bank_NickName'])) {													echo $get_det[$j]['Bank_NickName']."-".$get_det[$j]['bankaccno'];
												}
												}
										} ?>
									</td>
								<?php }?>
								<?php if($request->salaryflg != 1){?>
								 <?php if($request->pettyflg != 1){?>
								<?php if($request->loan_flg != 1){?>
									<td align="left" style="border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;padding-left:5px;">
								<?php
									if (Session::get('languageval') == 'en') {
										echo $get_det[$j]['sub_eng'];
									} else {
										echo $get_det[$j]['sub_jap'];
									}
									 ?>
								</td>
								<?php } else { ?>
								<td align="left" style="border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;padding-left:5px;">
									<?php echo "LoanPayment"; ?>
								</td>
								<?php } } else {?>
								<?php
								if($get_det[$j]['del_flg'] == 1){ ?>
									<td align="left" style="border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;padding-left:5px;">
										<?php
											if (Session::get('languageval') == 'en') {
												echo $get_det[$j]['sub_eng'];
											} else {
												echo $get_det[$j]['sub_jap'];
											}
									 	?>
									</td>
							    <?php } else {  ?>

									<td align="left" style="border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;padding-left:5px;">
										<?php 
											if($get_det[$j]['transaction_flg'] == 1){
												echo "Debit"; 
											} else {
												echo "Credit";
											} 
										?>
									</td>
							    <?php } } } else { ?>
							    	<td align="left" style="border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;padding-left:5px;">
										<?php echo "Salary"; ?>
									</td>
							    <?php }?>
							<td class="tar">
								@if($request->salaryflg == 1)
									{{ $get_det[$j]['amount'] }}
								@else
									{{ number_format($get_det[$j]['amount']) }}
								@endif
							</td>
							<td class="tar">
								@if($get_det[$j]['bankname']=="Cash")
								@elseif($request->salaryflg == 1)
									{{ $get_det[$j]['fee'] }}
								@elseif($request->pettyflg == 1)
								@else
									{{ number_format($get_det[$j]['fee']) }}
								@endif
							</td>
							<td class="tac">
							@if($request->pettyflg != 1)
								@if(isset($get_det[$j]['file_dtl']) && $get_det[$j]['file_dtl'] != "")
								<?php
										$file_url = '../InvoiceUpload/Expenses/' . $get_det[$j]['file_dtl'];
									 ?>
									@if($get_det[$j]['file_dtl'] != "" && file_exists($file_url))
										<a class="tac" href="javascript:download('{{ $get_det[$j]['file_dtl'] }}','../../../../InvoiceUpload/Expenses');" title="Download">
											<i class="fa fa-download" aria-hidden="true"></i>
										</a>
									@endif
								@else
								@endif
							@endif
							</td>
							<td>
								@if(isset($get_det[$j]['remarks']))
									{!! nl2br(e($get_det[$j]['remarks'])) !!}
								@else
								@endif
							</td>
							@if($request->pettyflg != 1 && $request->salaryflg != 1)
								<td class="tac">
									@if($get_det[$j]['bankname'] != "Cash")
										@if($request->loan_flg != 1)
											<a style="text-decoration: none;" style="color: blue;"  href="javascript:CopybkrsRecordhistory('{{ $get_det[$j]['id'] }}','{{ $request->mainmenu }}')" title="Copy">
												<img class="vam" src="{{ URL::asset('resources/assets/images/copy.png') }}" width="20" height="20"></a>
										@else
											<a style="text-decoration: none;" style="color: blue;"  href="javascript:CopybkrsRecord('{{ $get_det[$j]['id'] }}','{{ $request->mainmenu }}')" title="Copy">
												<img class="vam" src="{{ URL::asset('resources/assets/images/copy.png') }}" width="20" height="20"></a>
										@endif
									@else
										<a style="text-decoration: none;" style="color: blue;"  href="javascript:CopyRecordforexpenses('{{ $get_det[$j]['id'] }}','{{ $request->mainmenu }}','','')" title="Copy">
												<img class="vam" src="{{ URL::asset('resources/assets/images/copy.png') }}" width="20" height="20"></a>
									@endif
								</td>
							@endif
						</tr>
								 {{--*/ $temp = $loc /*--}}
								 {{--*/ $temp1 = $loc1 /*--}}
								 {{--*/ $temp2 = $loc2 /*--}}
								 {{--*/ $temp4 = $loc4 /*--}}
						@if(isset($get_det[$j]['salaryMonth']))
								 {{--*/ $temp3 = $loc3 /*--}}
						@endif
					@endfor
				@else 
						<tr>
						@if($request->pettyflg != 1)
							<td class="text-center colred" colspan="9">
						@elseif($request->salaryflg != 1)
							<td class="text-center colred" colspan="8">
						@else
							<td class="text-center colred" colspan="8">
						@endif
								{{ trans('messages.lbl_nodatafound') }}
							</td>
						</tr>
				@endif
			</tbody>
		</table>
	</div>
	<div class="text-center pl13">
		@if(!empty($index->total()))
			<span class="pull-left mt24">
				{{ $index->firstItem() }} ~ {{ $index->lastItem() }} / {{ $index->total() }}
			</span>
		@endif 
		{{ $index->links() }}
		<div class="CMN_display_block flr mr18">
			{{ $index->linkspagelimit() }}
		</div>
	</div>
	</article>
	{{ Form::close() }}
	{{ Form::open(array('name'=>'transferhistorydownload', 'id'=>'transferhistorydownload', 'url' => 'Transfer/historydownload?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('month', $request->month, array('id' => 'month')) }}
		{{ Form::hidden('year', $request->year, array('id' => 'year')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('salaryflg',$request->salaryflg, array('id' => 'salaryflg')) }}
	    {{ Form::hidden('loan_flg',$request->loan_flg, array('id' => 'loan_flg')) }}
	    {{ Form::hidden('pettyflg',$request->pettyflg, array('id' => 'pettyflg')) }}
	    {{ Form::hidden('delflg',$request->delflg, array('id' => 'delflg')) }}
	    {{ Form::hidden('subject',$request->subject, array('id' => 'subject')) }}
	    {{ Form::hidden('bname',$request->bname, array('id' => 'bname')) }}
	    {{ Form::hidden('bankName',$request->bankName, array('id' => 'bankName')) }}
	    {{ Form::hidden('accNo',$request->accNo, array('id' => 'accNo')) }}
	    {{ Form::hidden('empid','', array('id' => 'empid')) }}
	    {{ Form::hidden('empname',$request->empname, array('id' => 'empname')) }}
	    {{ Form::hidden('backflg',$request->backflg, array('id' => 'backflg')) }}
	    {{ Form::hidden('exptype1',$request->exptype1, array('id' => 'exptype1')) }}
	    {{ Form::hidden('flgs',$request->flgs, array('id' => 'flgs')) }}
	    {{ Form::hidden('expdetails',$request->expdetails, array('id' => 'expdetails')) }}
	    {{ Form::hidden('active_select', $request->active_select, array('id' => 'active_select')) }}
	    {{ Form::hidden('filter', $request->filter, array('id' => 'filter')) }}
	{{ Form::close() }}
	</div>
@endsection
