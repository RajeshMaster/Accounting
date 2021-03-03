@extends('layouts.app')
@section('content')
@php use App\Http\Helpers; @endphp
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
	$(document).ready(function() {
		setDatePicker("from_date");
		setDatePicker("to_date");
		if($('#hidAuth').val() == "5" || mainmenu == "AuditingAccounting"){
			$(".divdisplay").css("display", "none");
			$(".chnageorder").css("display", "none");
			$('.columnspan').attr('colspan','2');
			$('.columnspannodata').attr('colspan','9');
			$('.columnspan1').attr('colspan','2');
			$(".Auddivdisplay").css("");
  		} else {
  			$(".divdisplay").css("");
  			$(".chnageorder").css("");
  			$('.columnspan').attr('colspan','2');
  			$('.columnspannodata').attr('colspan','9');
  			$('.columnspan1').attr('colspan','2');
  			$(".Auddivdisplay").css("display", "none");
  		}
	});

	function mulclick(divid){
	    if($('#'+divid).css('display') == 'block'){
	      document.getElementById(divid).style.display = 'none';
	    }else {
	      document.getElementById(divid).style.display = 'block';
	    }
  	}

	function pageClick(pageval) {
		$('#page').val(pageval);
		var mainmenu = $('#mainmenu').val();
		$('#frmaccountingindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmaccountingindex").submit();
	}
	function pageLimitClick(pagelimitval) {
		$('#page').val('');
		$('#plimit').val(pagelimitval);
		var mainmenu = $('#mainmenu').val();
		$('#frmaccountingindex').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#frmaccountingindex").submit();
	}
</script>
<style type="text/css">
	.alertboxalign {
    	margin-bottom: -60px !important;
	}
	.alert {
		margin-top: 10px;
	    display:inline-block !important;
	    height:30px !important;
	    padding:5px !important;
	}
	.fb{
		color: gray !important;
	}
	.sort_asc {
		background-image:url({{ URL::asset('resources/assets/images/upArrow.png') }}) !important;
	}
	.sort_desc {
		background-image:url({{ URL::asset('resources/assets/images/downArrow.png') }}) !important;
	}
	.scrollbar
  	{
    float: left;
    max-height: 485px;
    width: 270px;
    overflow-x: hidden !important;
    overflow-y: scroll !important;
    margin-bottom: 5px;
  	}
	/* Dropdown Button */
	.dropbtn {
	    background-color: #4CAF50;
	    color: white;
	    padding: 16px;
	    font-size: 16px;
	    border: none;
	    cursor: pointer;
	}

	/* The container <div> - needed to position the dropdown content */
	.dropdown {
	    position: relative;
	    display: inline-block;
	}

	/* Dropdown Content (Hidden by Default) */
	.dropdown-content {
	    display: none;
	    position: absolute;
	    background-color: #f9f9f9;
	    min-width: 160px;
	    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
	    z-index: 1;
	}

	/* Links inside the dropdown */
	.dropdown-content a {
	    color: black;
	    padding: 5px 7px;
	    text-decoration: none;
	    display: block;
	}

	/* Change color of dropdown links on hover */
	.dropdown-content a:hover {background-color: #e5f4f9}

	/* Show the dropdown menu on hover */
	.dropdown:hover .dropdown-content {
	    display: block;
	}

	/* Change the background color of the dropdown button when the dropdown content is shown */
	.dropdown:hover .dropbtn {
	    background-color: #3e8e41;
	}
	.border_btm_solid_line{
	border-bottom:1px solid #A7D4DD;
	}
	/*.collapse {
    display: none ;
	}
	.collapse.in {
    display: block ;
	}*/

</style>
{{ HTML::script('resources/assets/js/accounts.js') }}
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::style('resources/assets/css/switch.css') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::script('resources/assets/js/lib/lightbox.js') }}
{{ HTML::style('resources/assets/css/lib/lightbox.css') }}
<div class="CMN_display_block" id="main_contents" style="width: 100%">
<!-- article to select the main&sub menu -->
@if($request->mainmenu =="AuditingAccounting")
<article id="auditing" class="DEC_flex_wrapper " data-category="auditing auditing_sub_3">
@else
<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_1">
@endif

	{{ Form::open(array('name'=>'frmaccountingindex', 
						'id'=>'frmaccountingindex', 
						'url' => 'Accounting/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('filter', $request->filter, array('id' => 'filter')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	    {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	    {{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
		{{ Form::hidden('sortOptn',$request->invoicesort , array('id' => 'sortOptn')) }}
	    {{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
		{{ Form::hidden('edit_flg', '', array('id' => 'edit_flg')) }}
		{{ Form::hidden('editId', '', array('id' => 'editId')) }}
		{{ Form::hidden('bank_Id', '', array('id' => 'bank_Id')) }}
		{{ Form::hidden('bankNo', '', array('id' => 'bankNo')) }}
		{{ Form::hidden('accNo', '', array('id' => 'accNo')) }}
		{{ Form::hidden('completedFlg', '', array('id' => 'completedFlg')) }}
		{{ Form::hidden('hidAuth', Auth::user()->userclassification, array('id' => 'hidAuth')) }}

		<!-- Year Bar Start -->
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}
		<!-- Year Bar End -->

		{{ Form::hidden('bankid', '' , array('id' => 'bankid')) }}
		{{ Form::hidden('bankname', '' , array('id' => 'bankname')) }}
		{{ Form::hidden('branchname', '' , array('id' => 'branchname')) }}
		{{ Form::hidden('accno', '' , array('id' => 'accno')) }}
		{{ Form::hidden('startdate', '' , array('id' => 'startdate')) }}
		{{ Form::hidden('bankids', '' , array('id' => 'bankids')) }}
		{{ Form::hidden('branchids', '' , array('id' => 'branchids')) }}
		{{ Form::hidden('searchmethod', '', array('id' => 'searchmethod')) }}
		{{ Form::hidden('empId', '', array('id' => 'empId')) }}
		{{ Form::hidden('loanId', '', array('id' => 'loanId')) }}
		{{ Form::hidden('contentId', '', array('id' => 'contentId')) }}


	<!-- Start Heading -->
	<div class="row hline" >
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/pettycash.jpg') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_accounting') }}</h2>
		</div>
	</div>
	<div class=" pr10 pl10 ">
		<div class="mt10 ">
			@if($request->searchmethod == "6" || $request->searchmethod == "")
				{{ Helpers::displayYear_MonthEst($account_period, $year_month, $db_year_month, $date_month, $dbnext, $dbprevious, $last_year, $current_year, $account_val) }}
			@endif
		</div>
	</div>

	<div class="col-xs-12">
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
		<div class="col-xs-6 pm0 mt10 divdisplay">
			<a href="javascript:addedit('index','{{ $request->mainmenu }}');" 
				class="btn btn-success pull-left"><span class="fa fa-plus"></span> 
			{{ trans('messages.lbl_register') }}</a>
		</div>
		<div class="col-xs-6 pm0 mt10 divdisplay">
			<a href="javascript:accExlwnld();" 
				class="btn btn-primary pull-right"><span class="fa fa-download"></span>
			{{ trans('messages.lbl_exldwnld') }}</a>
		</div>
	</div>

	<div class="pt43 minh300 pl10 pr10 " style="padding:3px 3px 20px">
		<table class="tablealternate CMN_tblfixed mt10">
			<colgroup>
				<col width="4%">
				<col width="9%">
				<col width="11%">
				<col width="">
				<col width="8%">
				<col width="8%">
				<!-- <col width="8%"> -->
				<col width="14%">
				<col width="5%">
				<col width="9%">
			</colgroup>

			<thead class="CMN_tbltheadcolor">
				<tr id="data">
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam">{{ trans('messages.lbl_Date') }}</th>
					<th class="vam">{{ trans('messages.lbl_subject') }}</th>
					<th class="vam">{{ trans('messages.lbl_content') }}</th>
					<th class="vam">{{ trans('messages.lbl_debit') }}</th>
					<th class="vam">{{ trans('messages.lbl_credit') }}</th>
					<!-- <th class="vam">{{ trans('messages.lbl_balance') }}</th> -->
					<th class="vam">{{ trans('messages.lbl_remarks') }}</th>
					<th class="vam">{{ trans('messages.lbl_file') }}</th>
					<th class="vam divdisplay">{{ trans('messages.lbl_copy') }}</th>
					<th class="vam Auddivdisplay">{{ trans('messages.lbl_flag') }}</th>
				</tr>
			</thead>
			<tbody>
				@php 
					$i = 0;
					$balanceAmt = 0;
					$debitAmt = 0;
					$creditAmt = 0;
					$lastBankAccno = "";
					$lastBankName = "";
					$preBankAccno = "";
					$preBankName = "";
					$baseamount = "";
					$balancebyBank = "";
					$realBalanceAmount = 0;
					$backgroundColor ="#e5f4f9";

					$debitToal = 0;
					$creditToal = 0;

				@endphp
				@forelse($cashDetails as $key => $data)
					@if($preBankName !="")
					@if($preBankAccno != $data['accNo'] ||  $preBankName != $data['Bank_NickName'])
						<tr style="background-color: lightgrey;">
							<td colspan="4" align="right" <?php if ($preCompletedFlg != 0) { ?> style = "font-weight: bold;"<?php } ?>>
								{{ trans('messages.lbl_total') }}
							</td>
							<td colspan="1" align="right" <?php if ($preCompletedFlg != 0) { ?> style = "font-weight: bold;"<?php } ?>>
								{{ number_format($debitToal) }}
							</td>
							<td align="right" <?php if ($preCompletedFlg != 0) { ?>style = "font-weight: bold;"<?php } ?>>
								{{ number_format($creditToal) }}
							</td>
							<td align="right" <?php if ($preCompletedFlg != 0) { ?> style = "font-weight: bold;"<?php } ?>>
								{{ number_format($realBalanceAmount) }}
								@php $balanceAmt = 0; @endphp
								@php $realBalanceAmount = 0; @endphp
							</td>
							<td class="columnspan1" align="right" <?php if ($preCompletedFlg != 0) { ?> style = "font-weight: bold;"<?php } ?>>
								<?php $currentYearMonth = date('Y-m');
								$previousYearMonth = date("Y-m",strtotime("-1 months", strtotime(date('Y-m'))));
								$selectedYearMonth = $request->selYear."-".$request->selMonth; ?>
								@if($currentYearMonth == $selectedYearMonth || $previousYearMonth == $selectedYearMonth)
								<div style="text-align: right;display: inline-block;">
									<a href="javascript:completedflg('{{ $preBankIdFrom }}','{{ $preBankAccno }}','{{ $preCompletedFlg }}');">
										@if($preCompletedFlg == 0)
											<span style="color: red;">
												{{ trans('messages.lbl_notconfirmed') }}
											</span>
										@else
											<span style="color: green;">
												{{ trans('messages.lbl_confirmed') }}
											</span>
										@endif
									</a>
								</div>
								@endif
							</td>
							<?php 
								$debitToal = 0;
								$creditToal = 0;
							?>
						</tr>
						<tr>
							<td class="text-center columnspannodata" style="background-color: white;border: none;"></td>
						</tr>
					@endif
					@endif
					@if($lastBankAccno != $data['accNo'] || $lastBankName != $data['Bank_NickName'])
						<tr style="background-color: #f1a2a2">
							<td colspan="5"> 
								@if(Auth::user()->userclassification == 5 || $request->mainmenu == "AuditingAccounting")
									{{ $data['Bank_NickName'] }} - {{ $data['accNo'] }}  
								@else
									@if($data['baseAmt'])
										<a href="javascript:bankViewlist('{{ $data['banknm'] }}','{{ $data['brnchnm'] }}','{{ $data['accNo'] }}','{{ $data['startDate'] }}','{{ $data['bankIdFrom'] }}','{{ $data['brnchid'] }}')" class="anchorstyle">
											{{ $data['Bank_NickName'] }} - {{ $data['accNo'] }}  
										</a>
									@else
										{{ $data['Bank_NickName'] }} - {{ $data['accNo'] }}  
									@endif
								@endif
							</td>
							<td></td>
							@if($data['baseAmt'])
								<?php $realBalanceAmount = $data['balanceAmtonDownTr']; ?>
							@else
								<?php $realBalanceAmount = $data['balanceAmtonDownTr']; ?>
							@endif
							<td align="right">{{ number_format($data['balanceAmtonDownTr']) }}</td>
							<td class="columnspan1" align="right">
								<div style="text-align: right;display: inline-block;" class="chnageorder">
									<a href="javascript:changeOrderpopUp('{{ $data['bankIdFrom'] }}','{{ $data['accNo'] }}');">
										{{ trans('messages.lbl_changeOrder') }}
									</a>
								</div>
							</td>
						</tr>
					@endif

				
					<tr style="">
						<td>{{ $i+1 }}</td>
						<td align="center">
							{{ $data['date'] }}
						</td>
						<td>
							@if($data['pageFlg'] == 1)
								{{ $data['pagecashSubject'] }}
							@elseif($data['subject'] != "" && Session::get('languageval') == "en")
								{{ $data['subject'] }}
							@elseif($data['Subject_jp'] != "" && Session::get('languageval') == "jp")
								{{ $data['Subject_jp'] }}
							@else
								{{ $data['content'] }}
							@endif
						</td>
						<td> 
							@if($data['pageFlg'] == '999')
								{{ $data['pagecashSubject'] }}
							@else
								@if($data['content'] == 'Salary')
									<a class="blue" href="javascript:empNameclick('{{ $data['emp_ID'] }}');" >
										{{ $data['employeDetails'] }}
									</a>
								@elseif($data['content'] == 'Invoice')
									<a class="blue" href="javascript:loanNameclick('{{ $data['loan_ID'] }}');" >
										{{ $data['invoiceDetails'] }}
									</a>
								@elseif($data['content'] == 'Loan')
									<a class="blue" href="javascript:loanNameclick('{{ $data['loan_ID'] }}');" >
										{{ $data['loanDetails'] }}
									</a>
								@elseif($data['pageFlg'] == 5 && $data['employeDetails'] != "")
									<a class="blue" href="javascript:empNameclick('{{ $data['emp_ID'] }}');" >
										{{ $data['employeDetails'] }}
									</a>
								@else
									<a class="blue" href="javascript:contentclick('{{ $data['content'] }}');" >
										@if($data['contentSub'] != "" && Session::get('languageval') == "en")
											{{ $data['contentSub'] }}
										@elseif($data['contentSub_jp'] != "" && Session::get('languageval') == "jp")
											{{ $data['contentSub_jp'] }}
										@else
											{{ $data['content'] }} 
										@endif
									</a>
								@endif

							@endif
						</td>
						<td align="right">
							@if($data['transcationType'] == 1)
								@php $debitAmt = $data['amount'] + $data['fee']; @endphp
								<?php $debitToal = $debitAmt + $debitToal ?>
								{{ number_format($data['amount']) }}
							@endif
						</td>
						<td align="right">
							@if($data['transcationType'] == 2 || $data['transcationType'] == 4)
								@php $creditAmt = $data['amount'] + $data['fee']; @endphp
								{{ number_format($data['amount']) }}
								<?php $creditToal = $creditAmt + $creditToal?>
							@endif
						</td>
							
						<td>{{ $data['remarks']}}</td>
						<td align="center">
						@if($data['fileDtl'] != "")
							<a style="text-decoration:none" href="{{ URL::asset('../../../../AccountingUpload/Accounting').'/'.$data['fileDtl'] }}" data-lightbox="visa-img">
							<img width="20" height="20" name="empimg" id="empimg" 
							class=" box20 viewPic3by2" src="{{ URL::asset('../../../../AccountingUpload/Accounting').'/'.$data['fileDtl'] }}"></a>
						@endif
						</td>
						<td class="divdisplay" align="center">
							@if($data['id'] != $data['transferId'] && $data['pageFlg'] != '999')
								<a href="javascript:editCashDtl('{{ $data['id'] }}','1','{{ $data['pageFlg'] }}','{{ $data['bankId'] }}');">
									<img class="vam" src="{{ URL::asset('resources/assets/images/edit.png') }}" width="20" height="20">
								</a>
								<a href="javascript:editCashDtl('{{ $data['id'] }}','2','{{ $data['pageFlg'] }}','{{ $data['bankId'] }}');">
									<img class="vam" src="{{ URL::asset('resources/assets/images/copy.png') }}" width="20" height="20">
								</a>
							@endif
						</td>
						<td class="Auddivdisplay"></td>
					</tr>

					
					@if($data['fee'] != "")
						<tr >
							<td  colspan="4" align="right">
								@if($data['content'] == 'Loan')
									{{ trans('messages.lbl_interest') }}
								@else
									{{ trans('messages.lbl_fee') }}
								@endif
							</td>
							<td colspan="" align="right"> 
								{{ number_format($data['fee']) }}
							</td>
							<td></td>
							<td align="right">
								{{ number_format($data['amount'] + $data['fee']) }}
							</td>
							<td class="columnspan"></td>
						</tr>
					@endif
					

					@if($data['transcationType'] == 1)
						<!-- <?php $balanceAmt = $balanceAmt - $debitAmt ;?> -->
						<?php $realBalanceAmount = $realBalanceAmount - $debitAmt ?>
					@else
						<!-- <?php $balanceAmt = $balanceAmt + $creditAmt ;?> -->
						<?php $realBalanceAmount = $realBalanceAmount + $creditAmt ?>
					@endif
					@php
						$lastBankAccno = $data['accNo'];
						$preBankAccno = $data['accNo'];
						$lastBankName = $data['Bank_NickName'];
						$preBankName = $data['Bank_NickName'];
						$preBankIdFrom = $data['bankIdFrom'];
						$preCompletedFlg = $data['completedFlg'];
						$baseamount =$data['baseAmt'];
						$i++ ;
					@endphp 
				@empty
					<tr>
						<td class="text-center columnspannodata"style="color: red;">
							{{ trans('messages.lbl_nodatafound') }}</td>
					</tr>
				@endforelse

				@if(count($cashDetails) > 0)
					<tr style="background-color: lightgrey">
						<td colspan="4" align="right" <?php if ($data['completedFlg'] != 0) { ?> style = "font-weight: bold;"<?php } ?>>
							{{ trans('messages.lbl_total') }}
						</td>
						<td colspan="1" align="right" <?php if ($data['completedFlg'] != 0) { ?> style = "font-weight: bold;"<?php } ?>>
							{{ number_format($debitToal) }}
						</td>
						<td align="right" <?php if ($data['completedFlg'] != 0) { ?> style = "font-weight: bold;"<?php } ?>>
							{{ number_format($creditToal) }}
						</td>
						<td align="right" <?php if ($data['completedFlg'] != 0) { ?> style = "font-weight: bold;"<?php } ?>>
							{{ number_format($realBalanceAmount) }}
						</td>
						<td class="columnspan1" align="right" <?php if ($data['completedFlg'] != 0) { ?> style = "font-weight: bold;"<?php } ?>>
							<?php $currentYearMonth = date('Y-m');
								$previousYearMonth = date("Y-m",strtotime("-1 months", strtotime(date('Y-m'))));
							$selectedYearMonth = $request->selYear."-".$request->selMonth; ?>
							@if($currentYearMonth == $selectedYearMonth || $previousYearMonth == $selectedYearMonth)
							<div style="text-align: right;display: inline-block;">
								<a href="javascript:completedflg('{{ $data['bankIdFrom'] }}','{{ $data['accNo'] }}','{{ $data['completedFlg'] }}');">
									@if($data['completedFlg'] == 0)
										<span style="color: red;">
											{{ trans('messages.lbl_notconfirmed') }}
										</span>
									@else
										<span style="color: green;">
											{{ trans('messages.lbl_confirmed') }}
										</span>
										
									@endif
								</a>
							</div>
							@endif
						</td>
					</tr>
				@endif
				
			</tbody>
		</table>

	</div>
	<div class="text-center pl14">
		@if(!empty($cashDetailsIndex->total()))
			<span class="pull-left mt24">
				{{ $cashDetailsIndex->firstItem() }} ~ {{ $cashDetailsIndex->lastItem() }} / {{ $cashDetailsIndex->total() }}
			</span>
			{{ $cashDetailsIndex->links() }}
			<div class="CMN_display_block flr pr14">
				{{ $cashDetailsIndex->linkspagelimit() }}
			</div>
		@endif 
	</div>


	<div id="getregisterpopup" class="modal fade">
		<div id="login-overlay">
			<div class="modal-content">
				<!-- Popup will be loaded here -->
			</div>
		</div>
	</div>

</article>
</div>

@endsection