@extends('layouts.app')
@section('content')

@php use App\Http\Helpers; @endphp

{{ HTML::script('resources/assets/js/creditcardpay.js') }}
{{ HTML::script('resources/assets/js/lib/lightbox.js') }}
{{ HTML::style('resources/assets/css/lib/lightbox.css') }}
<script type="text/javascript">

	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
	function getData(month, year, flg, prevcnt, nextcnt, account_period, lastyear, currentyear, account_val) {
	// alert(month + "***" + flg + "****" + currentyear);
		var yearmonth = year + "-" +  ("0" + month).substr(-2);
		if ((prevcnt == 0) && (flg == 0) && (parseInt(month) < account_period) && (year == lastyear)) {
			alert("No Previous Record.");
			//return false;
		} else if ((nextcnt == 0) && (flg == 0) && (parseInt(month) > account_period) && (year == currentyear)) {
			alert("No Next Record.");
		} else {
			if (flg == 1) {
				 $('#previou_next_year').val(year + "-" +  ("0" + month).substr(-2)); 
			}
			$('#pageclick').val('');
			$('#page').val('');
			$('#plimit').val('');
			$('#selMonth').val(("0" + month).substr(-2));
			$('#selYear').val(year);
			$('#prevcnt').val(prevcnt);
			$('#nextcnt').val(nextcnt);
			$('#account_val').val(account_val);
			$('#creditCaredPayMonthwise').submit();
		}
	}
	function pageClick(pageval) {
		$('#page').val(pageval);
		var mainmenu= $('#mainmenu').val();
		$('#creditCaredPayMonthwise').attr('action', 'monthlywiseindex?mainmenu='+mainmenu+'&time='+datetime);
		$("#creditCaredPayMonthwise").submit();
	}

	function pageLimitClick(pagelimitval) {
		$('#page').val('');
		$('#plimit').val(pagelimitval);
		var mainmenu= $('#mainmenu').val();
		$('#creditCaredPayMonthwise').attr('action', 'monthlywiseindex?mainmenu='+mainmenu+'&time='+datetime);
		$("#creditCaredPayMonthwise").submit();
	}

	function yearWise() {
		$('#creditCaredPayMonthwise').attr('action', 'yearindex?mainmenu='+mainmenu+'&time='+datetime);
		$("#creditCaredPayMonthwise").submit();
	}

	function ccMonthWise() {
		$('#creditCaredPayMonthwise').attr('action', 'index?mainmenu='+mainmenu+'&time='+datetime);
		$("#creditCaredPayMonthwise").submit();
	}
	function contentView(contet,flg) {
		$('#flgs').val(flg);
		$('#category').val(contet);
		$('#creditCaredPayMonthwise').attr('action', 'categorySelect?mainmenu='+mainmenu+'&time='+datetime);
		$("#creditCaredPayMonthwise").submit();
	}
</script>

	<div class="CMN_display_block" id="main_contents">

	<!-- article to select the main&sub menu -->
	<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_2">
	<!-- @if($request->mainmenu =="AuditingCreditCardPay")
		<article id="auditing" class="DEC_flex_wrapper " data-category="auditing auditing_sub_5">
	@else
		<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_2">
	@endif -->

	{{ Form::open(array('name'=>'creditCaredPayMonthwise', 'id'=>'creditCaredPayMonthwise', 'url' => 'CreditCardPay/monthlywiseindex?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('id', '', array('id' => 'id')) }}
		{{ Form::hidden('creditCardId', '', array('id' => 'creditCardId')) }}
		<!-- {{ Form::hidden('hidAuth', Auth::user()->userclassification, array('id' => 'hidAuth')) }} -->
		{{ Form::hidden('content', '', array('id' => 'content')) }}
		{{ Form::hidden('category', '', array('id' => 'category')) }}
		{{ Form::hidden('flgs','', array('id' => 'flgs')) }}
		
		<!-- Year Bar Start -->
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}
		<!-- Year Bar End -->

	<!-- Start Heading -->

	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/payment.png') }}">
			<h2 class="pull-left pl5 mt10 CMN_mw150">
					{{ trans('messages.lbl_creditCardPay') }}
			</h2>
		</div>
	</div>
	<!-- End Heading -->

	<div class=" pr10 pl10 ">
		<div class="mt10 ">
			{{ Helpers::displayYear_MonthEst($account_period, $year_month, $db_year_month, $date_month, $dbnext, $dbprevious, $last_year, $current_year, $account_val) }}
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
		<div class="col-xs-6  pm0 pull-left mt10">
			<a href="javascript:yearWise();" class=""><span>Year Wise</span></a> |
			<a href="javascript:ccMonthWise();" class=""><span>CC month Wise</span></a> |
			<span>Monthly Wise</span>
		</div>
		<div class="col-xs-6  pm0 pull-left mt10" align="right">
		
		</div>
	</div>


	<div class="pt43 minh300 pl10 pr10 " style="padding:3px 3px 20px">
		<table class="tablealternate CMN_tblfixed mt10">
			<colgroup>
				<col width="3%">
				<!-- <col width="8%"> -->
				<col width="8%">
				<col width="20%">
				<col width="8%">
				<col width="4%">
				<col width="15%">
				<col width="22%">
				<col width="4.5%">
			</colgroup>

			<thead class="CMN_tbltheadcolor">
				<tr id="data">
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<!-- <th class="vam">{{ trans('messages.lbl_Date') }}</th> -->
					<th class="vam">{{ trans('messages.lbl_Date') }}</th>
					<th class="vam">{{ trans('messages.lbl_content') }}</th>
					<th class="vam">{{ trans('messages.lbl_amount') }}</th>
					<th class="vam ">{{ trans('messages.lbl_bill') }}</th>
					<th class="vam">{{ trans('messages.lbl_categories') }}</th>
					<th class="vam">{{ trans('messages.lbl_remarks') }}</th>
					<th class="vam">{{ trans('messages.lbl_file') }}</th>
				</tr>
			</thead>
			<tbody>
				@php
					$creditCradId ="";
					$totalFlg = "";
					$CreditCardCheck = "0";
					$balanceAmt = 0;
				@endphp
				@forelse($creditcardDetails as $key => $data)
					@if(($CreditCardCheck != 0 && $CreditCardCheck != $data->creditCardId))
						<tr style="background-color: #f1a2a2;font-weight: bold;font-size: 15px">
							<td colspan="3" align="right">
								{{ trans('messages.lbl_total') }}
							</td>
							<td colspan="1" align="right">
								{{ number_format($balanceAmt) }}
								@php $balanceAmt = 0; @endphp
							</td>
							<td colspan="4"></td>
						</tr>
					@endif
					@if( $creditCradId != $data->creditCardId)
						<tr style="background-color: lightgrey;font-weight: bold;font-size: 15px">
							<td colspan="8"> 
								{{ $data->creditCardName }}
							</td>
						</tr>
					@endif
					<tr>
						<td align="center">{{ ($creditcardDetails->currentpage()-1) * $creditcardDetails->perpage() + $key + 1 }}</td>
						<!-- <td align="center">{{ $key+1 }}</td> -->
						<!-- <td align="center"> {{ $data->mainDate }} </td> -->
						<td align="center">{{ $data->creditCardDate }}</td>
						<td>
							{{ $data->creditCardContent }}
						</td>
						<td align="right">{{ number_format($data->creditCardAmount) }}</td>
						<td align="center">@if($data->rdoBill == "1")有@endif</td>
						<td>
							<a href="javascript:contentView('{{ $data->categoryId }}','1');">
								{{ $data->Category }}
							</a>
						</td>
						<td>{{ $data->remarks }}</td>
						<td align="center">
							@if($data->file != "")
								<a style="text-decoration:none" href="{{ URL::asset('../../../../AccountingUpload/CreditCard').'/'.$data->file }}" data-lightbox="visa-img">
								<img width="20" height="20" name="empimg" id="empimg" 
								class=" box20 viewPic3by2" src="{{ URL::asset('../../../../AccountingUpload/CreditCard').'/'.$data->file }}"></a>
							@endif
						</td>
					</tr>
					@php 
						$creditCradId = $data->creditCardId;
						$CreditCardCheck = $data->creditCardId;
						$balanceAmt += $data->creditCardAmount;
					@endphp
				@empty
					<tr>
						<td class="text-center columnspanpagination" colspan="8" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
					</tr>
				@endforelse

				@if(count($creditcardDetails) > 0)
					<tr style="background-color: #f1a2a2;font-weight: bold;font-size: 15px">
						<td colspan="3" align="right">{{ trans('messages.lbl_total') }}</td>
						<td colspan="1" align="right">{{ number_format($balanceAmt) }}</td>
						<td colspan="4"></td>
					</tr>
				@endif

			</tbody>
		</table>

	</div>
	<div class="text-center pl14">
		@if(!empty($creditcardDetails->total()))
			<span class="pull-left mt24">
				{{ $creditcardDetails->firstItem() }} ~ {{ $creditcardDetails->lastItem() }} / {{ $creditcardDetails->total() }}
			</span>
			{{ $creditcardDetails->links() }}
			<div class="CMN_display_block flr pr14">
				{{ $creditcardDetails->linkspagelimit() }}
			</div>
		@endif 
	</div>
	{{ Form::close() }}
	</article>
	</div>

@endsection

