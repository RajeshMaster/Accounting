@extends('layouts.app')
@section('content')
<?php use App\Model\Transfer; ?>
{{ HTML::style('resources/assets/css/common.css') }}
{{ HTML::script('resources/assets/js/transfer.js') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	function pageClick(pageval) {
		$('#page').val(pageval);
		$("#salaryhistory").submit();
	}
	function pageLimitClick(pagelimitval) {
		$('#page').val('');
		$('#plimit').val(pagelimitval);
		$("#salaryhistory").submit();
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
	{{ Form::open(array('name'=>'salaryhistory', 'id'=>'salaryhistory', 'url' => 'Transfer/empnamehistory?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
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
	    {{ Form::hidden('empid',$request->empid, array('id' => 'empid')) }}
	    {{ Form::hidden('exptype1',$request->exptype1, array('id' => 'exptype1')) }}
	    {{ Form::hidden('backflg',$request->backflg, array('id' => 'backflg')) }}
	    {{ Form::hidden('empname',$request->empname, array('id' => 'empname')) }}
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/expenses.png') }}">
			<h2 class="pull-left pl5 mt10">
				{{ trans('messages.lbl_employee') }} {{ trans('messages.lbl_salaryhistory') }}
			</h2>
		</div>
	</div>
	<div class="col-xs-12 pt5">
			<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
				<a href="javascript:gotoindexexpensesname('{{ $request->year }}','{{ $request->month }}','{{ $request->mainmenu }}');" class="btn btn-info box80">
					<span class="fa fa-arrow-left"></span>
					{{ trans('messages.lbl_back') }}
				</a>
			</div>
	</div>
	<div class="col-xs-12 mt5">
		<div class="col-xs-9" style="text-align: left;margin-left: -15px;">
			<label class="clr_blue">{{ trans('messages.lbl_empName').'   :' }}</label>
			<span class="mr40" style="color:black">
				<b>
					{{ $request->empname }}
				</b> 
			</span>
		</div>
		<div class="mt10 pull-right" title="Expenses Download">
              <a href="javascript:gotoempnamehistory('{{$request->mainmenu}}');"><span class="fa fa-download mr5"></span>{{ trans('messages.lbl_expdownload')}}</a>
          </div>
	</div>
	<div class="pt43 minh200 pl15 pr15">
		<table class="tablealternate CMN_tblfixed">
			<colgroup>
				<col width="4%">
				<col width="8%">
				<col width="4%">
				<col width="12%">
				<col width="12%">
				<col width="12%">
				<col width="12%">
				<col width="5%">
				<col>
			</colgroup>
			<thead class="CMN_tbltheadcolor">
				<tr>
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam">{{ trans('messages.lbl_Date') }}</th>
					<th class="vam">{{ trans('messages.lbl_month') }}</th>
					<th class="vam">{{ trans('messages.lbl_subject') }}</th>
					<th class="vam">{{ trans('messages.lbl_salary_type') }}</th>
					<th class="vam">{{ trans('messages.lbl_amount') }}</th>
					<th class="vam">{{ trans('messages.lbl_charge') }}</th>
					<th class="vam">{{ trans('messages.lbl_bill') }}</th>
					<th class="vam">{{ trans('messages.lbl_remarks') }}</th>
				</tr>
			</thead>
			<tbody>
				@php $count = count($getsalary); @endphp
				{{ $temp = ""}}
				{{--*/ $row = '0' /*--}}
				{{--*/ $tmpyr  = "" /*--}}
				{{--*/ $rowbktrclrr = 0 /*--}}
				{{--*/ $i = 0 /*--}}
				@if(!empty($count))
						<tr height="25px;">
					 			<td  colspan="5" style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;text-align:right;padding-right: 5px;color:blue;font-weight:bold;">
								<?php echo "Grant Total"; ?> 
								</td>
								<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;padding-right:5px;color:blue;font-weight:bold;">
								<?php echo "¥ ".number_format($amountTotal); ?> 
								</td>
								<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;padding-right:5px;color:blue;font-weight:bold;">
								 <?php echo "¥ ".number_format($chargeTotal); ?>
								 </td>
								 <td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;font-weight:bold;"> </td>
								<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-bottom:1px dotted #136E83;font-weight:bold;"> </td>
								
							</tr>
							<?php 
								$tempmonth="";
								$tempdate="";
								for ($i=0; $i < $filecount; $i++) {
								if($rowbktrclrr==1){
									$style='background-color:#dff1f4;';
									$rowbktrclrr=0;
								} else {
									$style='#FFFFFF';
									$rowbktrclrr=1;
								}
							?>
							{{--*/ $loc = $getsalary[$i]['bankdate'] /*--}}
		                  	@if($loc != $temp) 
								@if($row==1)
									{{--*/ $style_tr = 'background-color: #dff1f4;' /*--}}
									{{--*/ $row = '0' /*--}}
								@else
									{{--*/ $style_tr = 'background-color: #FFFFFF;' /*--}}
									{{--*/ $row = '1' /*--}}
								@endif
								{{--*/ $style_td = 'border-bottom: 1 px dotted black;' /*--}}
							@else
								{{--*/ $style_td = 'border-top:hidden;' /*--}}
							@endif
						<?php
							$amt=0;$amt=0;$fee=0;$fee=0; 	
							$view=transfer::fnsalaryDatatotal($request,$request->bname,$request->accNo,$getsalary[$i]['year'],$getsalary[$i]['month']);

								foreach ($view as $key => $value) {
									$resultval =  $value->amount;
									$resultfee =  $value->fee;
									$amt = $amt+str_replace(",", "", $value->amount);
								 	$fee = $fee+str_replace(",", "", $value->fee);
								}
						 ?>
						 <?php if($tmpyr!=$getsalary[$i]['year']||$tmpmth!=$getsalary[$i]['month']) { ?>
							<tr>
								<td colspan="5" style="background-color:lightgrey;color:black;border-bottom:1px dotted #136E83;vertical-align:middle;border-right:1px dotted #136E83;font-weight:bold;">
								<?php echo $getsalary[$i]['year']."年".$getsalary[$i]['month']."月"; ?> 
								</td>
								<td align="right" style="background-color:lightgrey;color:black;border-bottom:1px dotted #136E83;vertical-align:middle;border-right:1px dotted #136E83;padding-right:5px;font-weight:bold;">
								<?php echo number_format($amt); ?> 
								</td>
								<td align="right" style="background-color:lightgrey;color:black;border-bottom:1px dotted #136E83;vertical-align:middle;border-right:1px dotted #136E83;padding-right:5px;font-weight:bold;">
								 <?php echo number_format($fee); ?>
								 </td>
								<td align="right" style="background-color:lightgrey;color:black;border-bottom:1px dotted #136E83;vertical-align:middle;border-right:1px dotted #136E83;"> </td>
								<td align="right" style="background-color:lightgrey;color:black;border-bottom:1px dotted #136E83;vertical-align:middle;"> </td>
								
							</tr>
							<?php $tmpyr=$getsalary[$i]['year'];$tmpmth=$getsalary[$i]['month']; }?>
							<tr style="{{ $style_tr }}">
								<td class="tac" style="border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;">
									{{ ($index->currentpage()-1) * $index->perpage() + $i + 1}}
									
								</td>
								<?php
									if ($tempdate !=$getsalary[$i]['bankdate']) {
										$styleTDs = 'style="border-top:1px dotted #136E83;border-right: 1px dotted #136E83; "';
										} else {
										$styleTDs = 'style="border-top:0px dotted #FFFFFF; border-right: 1px dotted #136E83;"';
									}
								?>
								<td align="center" style="{{ $style_td }}">
									@if($loc != $temp)
										{{ $getsalary[$i]['bankdate'] }}
									@endif
								</td>
								<?php 
									if ($tempmonth !=$getsalary[$i]['month']) {
										$styleTD = 'style="border-top: 1px dotted #136E83;border-right: 1px dotted #136E83; "';
										} else {
										$styleTD = 'style="border-top:0px dotted #FFFFFF; border-right: 1px dotted #136E83;"';
									}
								?>
								<td align="center" <?php echo $styleTD;?> style="border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;" >
									<?php if ($tempmonth !=$getsalary[$i]['month']) {
										echo $getsalary[$i]['month'];
									 } ?>
									<?php $getsalary[$i]['month']; ?>
								</td>
								<td align="left" style="border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;">
									<?php /*echo $getsalary[$i]['Subject'];*/ ?>
									<?php echo "Salary"; ?>
								</td>
								<td style="border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;">
								<?php if(($getsalary[$i]['bankId'])==999){
										echo "Cash";
									  } else { ?>
								<?php echo $getsalary[$i]['BankName']."-".$getsalary[$i]['bankaccno']; }?>
								</td>
								<td align="right" style="border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;">
									<?php echo $getsalary[$i]['amount']; ?>
								</td>
								<td align="right" style="border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;">
									<?php echo $getsalary[$i]['fee']; ?>
								</td>
								<td style="border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;">
									<?php /*echo $getsalary[$i]['file_dtl'];*/ ?>
								</td>
								<td style="border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;">
									@if(isset($getsalary[$i]['remarks']))
									  {{ $getsalary[$i]['remarks'] }}
									@endif
								</td>
							</tr>
							{{--*/ $temp = $loc /*--}}
							<?php $tempmonth = $getsalary[$i]['month'];
									$tempdate = $getsalary[$i]['bankdate']; ?>
							<?php } ?>
				@else 
					<tr>
						<td class="text-center colred" colspan="9">
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
{{ Form::open(array('name'=>'frmdownloadindexsalary', 
						'id'=>'frmdownloadindexsalary', 
						'url' => 'Transfer/salaryhistorydownload?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
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
	    {{ Form::hidden('empid',$request->empid, array('id' => 'empid')) }}
	    {{ Form::hidden('exptype1',$request->exptype1, array('id' => 'exptype1')) }}
	    {{ Form::hidden('backflg',$request->backflg, array('id' => 'backflg')) }}
	    {{ Form::hidden('empname',$request->empname, array('id' => 'empname')) }}
	{{ Form::close() }}
	</div>
@endsection
