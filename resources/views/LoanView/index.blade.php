@extends('layouts.app')
@section('content')
@php use App\Http\Helpers; @endphp

{{ HTML::script('resources/assets/js/loanview.js') }}
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::style('resources/assets/css/switch.css') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::script('resources/assets/js/lib/lightbox.js') }}
{{ HTML::style('resources/assets/css/lib/lightbox.css') }}

<style type="text/css">

	.alterBorder{border-top: 2px solid black !important;}

	.bor_none {border:none ! important; border-right: 1px solid #ddd ! important;}
	
	.totAmt {
		color:blue;
		font-size: 18px !important;
	}

	a.disabled {
		pointer-events: none;
		cursor: default;
	}
	
</style>

<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
</script>

<div class="CMN_display_block" id="main_contents" style="width: 100%">
<!-- article to select the main&sub menu -->
@if($request->mainmenu =="AuditingLoanView")
<article id="auditing" class="DEC_flex_wrapper " data-category="auditing auditing_sub_6">
@else
<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_5">
@endif

	{{ Form::open(array('name'=>'loanDetailsIndex',
			'id'=>'loanDetailsIndex',
			'url' => 'LoanView/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'), 
			'method' => 'POST')) }}

	{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
	{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	{{ Form::hidden('searchmethod', $request->searchmethod , array('id' => 'searchmethod')) }}
	{{ Form::hidden('loanId', $request->loanId , array('id' => 'loanId')) }}
	{{ Form::hidden('userId', $request->userId , array('id' => 'userId')) }}
	{{ Form::hidden('editChk', 0 , array('id' => 'editChk')) }}

	{{ Form::hidden('selMonth', $request->selMonth , array('id' => 'selMonth')) }}
    {{ Form::hidden('selYear', $request->selYear , array('id' => 'selYear')) }}
    {{ Form::hidden('parentmonth', $request->parentmonth , array('id' => 'parentmonth')) }}
    {{ Form::hidden('parentyr', $request->parentyr , array('id' => 'parentyr')) }}


    <div class="row hline" >
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/loan.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_loandetail') }}</h2>
		</div>
	</div>

	<div class="pr10 pl10">
		<div class="mt30">
			{{ Helpers::displayYear_Month($prev_yrs,$cur_year,$cur_month,$total_yrs,$curtime) }}
		</div>
	</div>

	<div class="col-xs-12 pull-left pl10 pr10">
		@if (session('danger'))
			<div class="col-xs-12 mt10" align="center">
				<span class="alert-danger">{{ session('danger') }}</span>
			</div>
		@elseif (session('message'))
			<div class="col-xs-12 mt10" align="center">
				<span class="alert-success">{{ session('message') }}</span>
			</div>
		@endif
	</div>

	<div class="mt30">
		<div class="pull-left mb10">
			<a href="javascript:loanYearWise();" class="textDecNone">
				{{ trans('messages.lbl_yearwise') }}</a>&nbsp;|&nbsp;
			<a href="javascript:;" class="disabled" style="color: black;">
				{{ trans('messages.lbl_monthwise') }}</a>
		</div>
		<div class="pull-right">
			<label>単位：万円</label>
		</div>

		<table class="tablealternate CMN_tblfixed mt10">
		<colgroup>
			<col width="3%">
			<col width="8%">
			<col width="15%">
			<col width="6%">
			<col width="6%">
			<col width="6%">
			<col width="6%">
			<col width="6%">
			<col width="6%">
			<col width="6%">
			<col width="6%">
			<col width="6%">
			<col width="7%">
		</colgroup>
		<thead class="CMN_tbltheadcolor">
			<th>{{ trans('messages.lbl_sno') }}</th>
			<th>{{ trans('messages.lbl_bank') }}</th>
			<th>{{ trans('messages.lbl_loan_name') }}</th>
			<th>{{ trans('messages.lbl_loan_amt') }}</th>
			<th>{{ trans('messages.lbl_interest') }}</th>
			<th>{{ trans('messages.lbl_period') }}</th>
			<th>{{ trans('messages.lbl_remain_amt') }}</th>
			<th>{{ trans('messages.lbl_principle') }}</th>
			<th>{{ trans('messages.lbl_interest_amt') }}</th>
			<th>{{ trans('messages.lbl_emi_amt') }}</th>
			<th>{{ trans('messages.lbl_tot') }} {{ trans('messages.lbl_princ') }}</th>
			<th>{{ trans('messages.lbl_tot') }} {{ trans('messages.lbl_int') }}</th>
			<th>{{ trans('messages.lbl_totemi_amt') }}</th>
		</thead>
		<tbody class="">

		@php 
			$i=1;
			$tempRelCount= '';
			$loanAmtTotal = '';
			$grandTotal = '';
			$grandTotRemain = '';
			$grandTotPrinciple = '';
			$grandTotInterest = '';
			$grandTotEMI = '';
		@endphp
		@foreach($loanArrBelongsTo as $belongsToId => $relation)

			<tr style="background-color: #c7e1f7 !important;">
				<td colspan="3" >{{ $relation }}</td>

				<td class="totAmt tar">{{ number_format($totArr[$belongsToId]['loanAmountTotal']) }}</td>
				<td></td>
				<td></td>
				<td class="totAmt tar">{{ number_format($totArr[$belongsToId]['nextLoanBalance']) }}</td>
				<td></td>
				<td></td>
				<td></td>
				<td class="tar totAmt">{{ number_format((float)$totArr[$belongsToId]['totPrinciple'], 1) }}</td>
				<td class="tar totAmt">{{ number_format((float)$totArr[$belongsToId]['totInterest'], 1) }}</td>
				<td class="tar totAmt" style="background-color: #bae7d2 !important">
					{{ number_format((float)$totArr[$belongsToId]['totEMI'], 1) }}</td>
			</tr>
			@php
				$k = 0; 
				$temp = '';
				$tempPrin = '';
				$tempInt = '';
				$tempMnthAmt = '';	
				$loanAmtTotal += $totArr[$belongsToId]['loanAmountTotal'];				
				$loctotRelAmount = $loanArrVal[$relation]['totRelAmount'];
				$grandTotal += $loctotRelAmount;
				$grandTotRemain += $totArr[$belongsToId]['nextLoanBalance'];
				$grandTotPrinciple += $totArr[$belongsToId]['totPrinciple'];
				$grandTotInterest += $totArr[$belongsToId]['totInterest'];
				$grandTotEMI += $totArr[$belongsToId]['totEMI'];
			@endphp

			@foreach($loanArrVal[$relation] as $bankId => $bank)

				@if(is_numeric($bankId))
					@php $count=""; @endphp
					@foreach($bank as $key => $value)

						@php 
							$count = count(array_filter(array_keys($bank), function($key) {
									    return is_int($key);
									}));
							$loc = $bank; 
							$locPrin = (isset($bank['totPrinciple'])) ? $bank['totPrinciple'] : 0;
							$locInt = (isset($bank['totInterest'])) ? $bank['totPrinciple'] : 0;
							$locMnthAmt = (isset($bank['totMonthAmount'])) ? $bank['totPrinciple'] : 0;
						@endphp
						@if(is_numeric($key))
							<tr @if ($temp != $loc && $k != 0)
								class="alterBorder" 
								@else 
								@endif>
								<td class="tac">
									{{ ($loanDetails->currentpage()-1) * $loanDetails->perpage() + $i }}
								</td>
								@php $i++; @endphp

								<td class="bor_none">
									@if($temp != $loc)
										{{ $value['bank'] }}
									@endif
								</td>

								<td title="{{ $value['loanName'] }}">
									@if(strlen($value['loanName']) > 15)
										{{ singlefieldlength($value['loanName'],15) }}
									@else
										{{ $value['loanName'] }}
									@endif
								</td>
								<td class="tar">{{ number_format($value['loanAmount']) }}</td>
								<td class="pl10" style="border-right: 2px solid black;">{{ $value['interestRate'] }} %</td>
								<td class="tac">{{ $value['nextCount'] }}/{{ $value['loanTerm'] * $value['paymentCount'] }}</td>
								<td class="tar">{{ number_format($value['nextLoanBalance']) }}</td>

								@php
									$principle = (isset($value['monthPrinciple'])) ? $value['monthPrinciple']/10000 : 0;
									$interest = (isset($value['monthInterest'])) ? $value['monthInterest']/10000 : 0;
									$emiAmt = $principle + $interest;
								@endphp
								<td class="tar">{{ number_format((float)$principle,1) }}</td>
								<td class="tar">{{ number_format((float)$interest,1) }}</td>
								<td class="tar" style="border-right: 2px solid black;">{{ number_format((float)$emiAmt,1) }}</td>

								@if($tempPrin != $locPrin)
									<td class="tar vam" rowspan="{{ $count }}">
										@if(isset($bank['totPrinciple']))
											{{ number_format((float)$bank['totPrinciple'], 1) }}
										@else 0
										@endif
									</td>
								@endif	

								@if($tempInt != $locInt)
									<td class="tar vam" rowspan="{{ $count }}">
										@if(isset($bank['totInterest']))
											{{ number_format((float)$bank['totInterest'], 1) }}
										@else 0
										@endif
									</td>
								@endif	

								@if($tempMnthAmt != $locMnthAmt)
									<td class="tar vam" rowspan="{{ $count }}" style="background-color: #bae7d2 !important">
										@if(isset($bank['totMonthAmount']))
											{{ number_format((float)$bank['totMonthAmount'], 1) }}
										@else 0
										@endif
									</td>
								@endif
							</tr>
						@endif					
						@php
							$k = 1; 
							$temp = $loc;
							$tempPrin = $locPrin;
							$tempInt = $locInt;
							$tempMnthAmt = $locMnthAmt;
							$tempRelCount = $loctotRelAmount;
						@endphp
					@endforeach
				@endif

			@endforeach
		@endforeach

		@if($grandTotal != "")
			<tr style="background-color: #fad6c3 !important">
				<td colspan="3" class="tar">{{ trans('messages.lbl_grandtot') }}</td>

				<td class="tar totAmt">{{ number_format($loanAmtTotal) }}</td>
				<td></td>
				<td></td>
				<td class="tar totAmt">{{ number_format($grandTotRemain) }}</td>
				<td></td>
				<td></td>
				<td></td>

				<td class="tar totAmt">{{ number_format((float)$grandTotPrinciple, 1) }}</td>
				<td class="tar totAmt">{{ number_format((float)$grandTotInterest, 1) }}</td>
				<td class="tar totAmt" style="background-color: #bae7d2 !important">{{ number_format((float)$grandTotEMI, 1) }}</td>
			</tr>
		@else
			<tr>
				<td colspan="13" class="tac" id="nodatafound" style="color: red;">
					{{ trans('messages.lbl_nodatafound')}}
				</td>
			</tr>
		@endif

		</tbody>
		</table>
	</div>

	{{ Form::close() }}

	@if($loanDetails->total())
		<div class="text-center col-xs-12 pm0　mt50">
			@if(!empty($loanDetails->total()))
				<span class="pull-left mt24">{{ $loanDetails->firstItem() }}
					<span class="mt5">～</span>
					{{ $loanDetails->lastItem() }} / {{ $loanDetails->total() }}
				</span>
			　@endif 
			{{ $loanDetails->links() }}
			<span class="pull-right">
				{{ $loanDetails->linkspagelimit() }}
			</span>
		</div>
	@endif 

	</article>
</div>

@endsection