@extends('layouts.app')
@section('content')
@php use App\Http\Helpers; @endphp
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
	$(document).ready(function() {
		setDatePicker("from_date");
		setDatePicker("to_date");
		if($('#hidAuth').val() == "5" || mainmenu == "AuditingExpensesData"){
			$(".divdisplay").css("display", "none");
			$(".chnageorder").css("display", "none");
			$('.columnspannodata').attr('colspan','7');
			$('.columnspan1').attr('colspan','1');
  		} else {
  			$(".divdisplay").css("");
  			$(".chnageorder").css("");
  			$('.columnspannodata').attr('colspan','8');
  			$('.columnspan1').attr('colspan','2');
  		}
	});

	function pageClick(pageval) {
		$('#page').val(pageval);
		$("#frmexpensesDataindex").submit();
	}
	function pageLimitClick(pagelimitval) {
		$('#page').val('');
		$('#plimit').val(pagelimitval);
		$("#frmexpensesDataindex").submit();
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

</style>
{{ HTML::script('resources/assets/js/expensesData.js') }}
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
@if($request->mainmenu =="AuditingExpensesData")
<article id="auditing" class="DEC_flex_wrapper " data-category="auditing auditing_sub_7">
@else
<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_6">
@endif

	{{ Form::open(array('name'=>'frmexpensesDataindex', 
						'id'=>'frmexpensesDataindex', 
						'url' => 'ExpensesData/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}

		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	    {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	    {{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}

		{{ Form::hidden('edit_flg', '', array('id' => 'edit_flg')) }}
		{{ Form::hidden('editId', '', array('id' => 'editId')) }}
		{{ Form::hidden('bank_Id', '', array('id' => 'bank_Id')) }}
		{{ Form::hidden('bankNo', '', array('id' => 'bankNo')) }}
		{{ Form::hidden('accNo', '', array('id' => 'accNo')) }}
		{{ Form::hidden('delFlg', '', array('id' => 'delFlg')) }}
		{{ Form::hidden('hidAuth', Auth::user()->userclassification, array('id' => 'hidAuth')) }}

		{{ Form::hidden('bankid', '' , array('id' => 'bankid')) }}
		{{ Form::hidden('bankname', '' , array('id' => 'bankname')) }}
		{{ Form::hidden('branchname', '' , array('id' => 'branchname')) }}
		{{ Form::hidden('accno', '' , array('id' => 'accno')) }}
		{{ Form::hidden('startdate', '' , array('id' => 'startdate')) }}
		{{ Form::hidden('bankids', '' , array('id' => 'bankids')) }}
		{{ Form::hidden('branchids', '' , array('id' => 'branchids')) }}
		{{ Form::hidden('searchmethod', '', array('id' => 'searchmethod')) }}
		{{ Form::hidden('empId', '', array('id' => 'empId')) }}
		{{ Form::hidden('contentId', '', array('id' => 'contentId')) }}
		{{ Form::hidden('content_Id', '', array('id' => 'content_Id')) }}


	<!-- Start Heading -->
	<div class="row hline" >
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/expenses.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_expensesData') }}</h2>
		</div>
	</div>

	<div class="col-xs-12 pull-left pl10 pr10">
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
		<div class="col-xs-6  pm0 pull-left mt10 divdisplay">
			<a href="javascript:addedit('index','{{ $request->mainmenu }}');" class="btn btn-success box100"><span class="fa fa-plus"></span> {{ trans('messages.lbl_register') }}</a>
		</div>
	</div>

	<div class="pt43 minh300 pl10 pr10 " style="padding:3px 3px 20px">
		<table class="tablealternate CMN_tblfixed mt10">
			<colgroup>
				<col width="4%">
				<col width="11%">
				<col width="">
				<col width="8%">
				<col width="5%">
				<col width="14%">
				<col width="5%">
				<col width="11%" class="divdisplay">
			</colgroup>

			<thead class="CMN_tbltheadcolor">
				<tr id="data">
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam">{{ trans('messages.lbl_subject') }}</th>
					<th class="vam">{{ trans('messages.lbl_content') }}</th>
					<th class="vam">{{ trans('messages.lbl_amount') }}</th>
					<th class="vam">{{ trans('messages.lbl_fee') }}</th>
					<th class="vam">{{ trans('messages.lbl_remarks') }}</th>
					<th class="vam">{{ trans('messages.lbl_file') }}</th>
					<th class="vam divdisplay">{{ trans('messages.lbl_copy') }}</th>
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
					$backgroundColor ="#e5f4f9";
					$total = 0;
					$feetot = 0;
					$totAmt = 0;
				@endphp
				@forelse($expensesDetails as $key => $data)
					@if($preBankName !="")
					@if($preBankAccno != $data['accNo'] ||  $preBankName != $data['Bank_NickName'])
						<tr style="background-color: lightgrey;">
							<td colspan="3" align="right">
								{{ trans('messages.lbl_total') }}
							</td>
							<td colspan="1" align="right">
								{{ number_format($total) }}
							</td>
							<td align="right">
								{{ number_format($feetot) }}
							</td>
							<td align="right">
								<?php $totAmt = $total + $feetot; ?>
								{{ number_format($totAmt) }}
							</td>
							<td class="columnspan1" align="right">
								
							</td>
							<?php $total = 0; $feetot = 0; ?>
						</tr>
						<tr>
							<td class="text-center columnspannodata" style="background-color: white;border: none;"></td>
						</tr>
					@endif
					@endif
					@if($lastBankAccno != $data['accNo'] || $lastBankName != $data['Bank_NickName'])
						<tr style="background-color: #f1a2a2">
							<td colspan="3"> 
								@if(Auth::user()->userclassification == 5 || $request->mainmenu == "AuditingExpensesData")
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
							<td align="right"></td>
							<td align="right"></td>
							<td align="left"></td>
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
						<td>
							@if($data['subject'] != "" && Session::get('languageval') == "en")
								{{ $data['subject'] }}
							@elseif($data['Subject_jp'] != "" && Session::get('languageval') == "jp")
								{{ $data['Subject_jp'] }}
							@else
								{{ $data['content'] }}
							@endif
						</td>
						<td> 
							@if($data['empId'] != '')
								<a class="blue" href="javascript:empNameclick('{{ $data['empId'] }}');" >
									{{ $data['employeDetails'] }}
								</a>
							@else
								@if($data['contentSub'] != "" && Session::get('languageval') == "en")
									<a class="blue" href="javascript:contentclick('{{ $data['content'] }}');" >
									{{ $data['contentSub'] }}
									</a>
								@elseif($data['contentSub_jp'] != "" && Session::get('languageval') == "jp")
									<a class="blue" href="javascript:contentclick('{{ $data['content'] }}');" >
										{{ $data['contentSub_jp'] }}
									</a>
								@else
									{{ $data['content'] }} 
								@endif
							@endif
						</td>
						<td align="right">
							@php $amount = $data['amount']; @endphp
							<?php $total = $amount + $total ?>
							{{ number_format($data['amount']) }}
						</td>
						<td align="right">
							@php $fee = $data['fee']; @endphp
							<?php $feetot = $fee + $feetot ?>
							{{ number_format($data['fee']) }}
						</td>
						<td>{{ $data['remarks']}}</td>
						<td align="center">
						@if($data['fileDtl'] != "")
							<a style="text-decoration:none" href="{{ URL::asset('../../../../AccountingUpload/ExpensesData').'/'.$data['fileDtl'] }}" data-lightbox="visa-img">
							<img width="20" height="20" name="empimg" id="empimg" 
							class=" box20 viewPic3by2" src="{{ URL::asset('../../../../AccountingUpload/ExpensesData').'/'.$data['fileDtl'] }}"></a>
						@endif
						</td>
						<td class="divdisplay" align="center">
							<a href="javascript:editExpData('{{ $data['id'] }}','1','{{ $data['bankId'] }}','{{ $data['content'] }}');">
								<img class="vam" src="{{ URL::asset('resources/assets/images/edit.png') }}" width="20" height="20">
							</a>
							<a href="javascript:editExpData('{{ $data['id'] }}','2','{{ $data['bankId'] }}','{{ $data['content'] }}');">
								<img class="vam" src="{{ URL::asset('resources/assets/images/copy.png') }}" width="20" height="20">
							</a>
							&nbsp&nbsp
							@if($data['delFlg'] == 0)
								<a href = "javascript:changeDelFlg('{{ $data['id'] }}','{{ $data['delFlg'] }}')" style ="color:blue;">
									{{ trans('messages.lbl_notuse') }}
								</a>
							@else
								&nbsp
								<a href = "javascript:changeDelFlg('{{ $data['id'] }}','{{ $data['delFlg'] }}')" style ="color:red;">
									{{ trans('messages.lbl_use') }}
								</a>
							@endif
						</td>
					</tr>
					
					@php
						$lastBankAccno = $data['accNo'];
						$preBankAccno = $data['accNo'];
						$lastBankName = $data['Bank_NickName'];
						$preBankName = $data['Bank_NickName'];
						$preBankIdFrom = $data['bankIdFrom'];
						$baseamount = $data['baseAmt'];
						$i++ ;
					@endphp 
				@empty
					<tr>
						<td class="text-center columnspannodata"style="color: red;">
							{{ trans('messages.lbl_nodatafound') }}</td>
					</tr>
				@endforelse

				@if(count($expensesDetails) > 0)
					<tr style="background-color: lightgrey">
						<td colspan="3" align="right">
							{{ trans('messages.lbl_total') }}
						</td>
						<td colspan="1" align="right">
							{{ number_format($total) }}
						</td>
						<td align="right">
							{{ number_format($feetot) }}
						</td>
						<td align="right">
							<?php $totAmt = $total + $feetot; ?>
							{{ number_format($totAmt) }}
						</td>
						<td class="columnspan1" align="right">
						</td>
					</tr>
				@endif
				
			</tbody>
		</table>

	</div>
	<div class="text-center pl14">
		@if(!empty($expensesDetailsIndex->total()))
			<span class="pull-left mt24">
				{{ $expensesDetailsIndex->firstItem() }} ~ {{ $expensesDetailsIndex->lastItem() }} / {{ $expensesDetailsIndex->total() }}
			</span>
			{{ $expensesDetailsIndex->links() }}
			<div class="CMN_display_block flr pr14">
				{{ $expensesDetailsIndex->linkspagelimit() }}
			</div>
		@endif 
	</div>


	<div id="getExpDataDetails" class="modal fade">
		<div id="login-overlay">
			<div class="modal-content">
				<!-- Popup will be loaded here -->
			</div>
		</div>
	</div>

</article>
</div>

@endsection