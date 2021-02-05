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

	.bor_rightbot_none {
		border-bottom: none ! important;
		border-top: none ! important;
	}

	.bor_none {border:none ! important; border-right: 1px solid #ddd ! important;}
	
	.totAmt {
		color:blue;
		font-size: 16px !important;
	}

	.pastMnth {
		background-color: #f0f0f0 ! important;
	}

	a.disabled {
		pointer-events: none;
		cursor: default;
	}

</style>

<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';

	// Year BarProcess
	function getData(selYear,time) {
	    $('#selYear').val(selYear);
	    var mainmenu = $('#mainmenu').val();
	    $('#LoanDetailslistview').attr('action', 'listview?mainmenu='+mainmenu+'&time='+time);
	    $("#LoanDetailslistview").submit();
	}
	
</script>

<div class="CMN_display_block" id="main_contents" style="width: 100%">
<!-- article to select the main&sub menu -->
@if($request->mainmenu =="AuditingLoanView")
<article id="auditing" class="DEC_flex_wrapper " data-category="auditing auditing_sub_6">
@else
<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_5">
@endif

	{{ Form::open(array('name'=>'LoanDetailslistview',
			'id'=>'LoanDetailslistview',
			'url' => 'LoanView/listview?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'), 
			'method' => 'POST')) }}
	{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
	{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	{{ Form::hidden('userId', $request->userId , array('id' => 'userId')) }}

    {{ Form::hidden('selYear', $request->selYear , array('id' => 'selYear')) }}
    {{ Form::hidden('selMonth', $request->selMonth , array('id' => 'selMonth')) }}
	
	<div class="row hline" >
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/loan.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_loandetail') }}</h2>
		</div>
	</div>

	<div class="pr10 pl10">
		<div class="mt30">
			{{ Helpers::displayYear($prev_yrs,$cur_year,$total_yrs,$curtime) }}
		</div>
	</div>
	
	@if (session('danger'))
		<div class="col-xs-12 mt10" align="center">
			<span class="alert-danger">{{ session('danger') }}</span>
		</div>
	@elseif (session('message'))
		<div class="col-xs-12 mt10" align="center">
			<span class="alert-success">{{ session('message') }}</span>
		</div>
	@endif

	<div class="mt30">
		<div class="pull-left mb10">
			<a href="javascript:;" class="disabled" style="color: black;">
				{{ trans('messages.lbl_yearwise') }}</a>&nbsp;|&nbsp;
			<a href="javascript:loanMonthWise();" class="textDecNone">
				{{ trans('messages.lbl_monthwise') }}</a>
		</div>
		<div class="pull-right">
			<label>単位：万円</label>
		</div>

		<table class="tablealternate CMN_tblfixed mt10">
			<colgroup>
				<col width="3%">
				<col width="7%">
				<col width="8%">
				<col width="5%">
				<col width="4%">
				<col width="4%">
				<col width="4%">
				<col width="5%">
				@for($col=1; $col <= 12; $col++)
					<col width="4%">
				@endfor
			</colgroup>
			<thead class="CMN_tbltheadcolor">
				<th>{{ trans('messages.lbl_sno') }}</th>
				<th>{{ trans('messages.lbl_bank') }}</th>
				<th>{{ trans('messages.lbl_loan_name') }}</th>
				<th>{{ trans('messages.lbl_loan_amt') }}</th>
				<th>{{ trans('messages.lbl_int') }}％</th>
				<th>{{ trans('messages.lbl_princ') }}</th>
				<th>{{ trans('messages.lbl_int') }}</th>
				<th>{{ trans('messages.lbl_tot') }}</th>
				@for($m=1; $m <= 12; $m++)
					<th>{{ $m }}月</th>
				@endfor
			</thead>

			<tbody>
				@php 
					$i = 1;
					$currYrMnthVal = strtotime(date('Y-m'));
					$tempRel= '';
					$loanAmtTotal = '';
					$principleTotal = '';
					$interestTotal = '';
					$monthTotArr = array();
					for($j=1; $j <= 12; $j++){
						$monthTotArr[$j] = 0;
					}
				@endphp

				@forelse($loanArrBelongsTo as $belongsToId => $relation)

					<tr style="background-color: #c7e1f7 !important;">
						<td colspan="3" >{{ $relation }}</td>

						<td class="totAmt tar">{{ number_format($totArr[$belongsToId]['loanAmountTotal']) }}</td>
						<td></td>
						<td class="totAmt tar">{{ number_format((float)$totArr[$belongsToId]['principleTotal'], 1) }}</td>
						<td class="totAmt tar">{{ number_format((float)$totArr[$belongsToId]['interestTotal'], 1) }}</td>
						<td class="totAmt tar">{{ number_format((float)$totArr[$belongsToId]['relationTotal'], 1) }}</td>
						@for($mnth=1; $mnth <= 12; $mnth++)
							@if($totArr[$belongsToId]['monthPayTotal'][$mnth] > 0)
								<td class="totAmt tar">
									{{ number_format((float)$totArr[$belongsToId]['monthPayTotal'][$mnth], 1) }}
								</td>
							@else
								<td class="tar">-</td>
							@endif
						@endfor
					</tr>

					@php
						$temp = '';
						$k = 0;
						$loanAmtTotal += $totArr[$belongsToId]['loanAmountTotal'];
						$principleTotal += $totArr[$belongsToId]['principleTotal'];
						$interestTotal += $totArr[$belongsToId]['interestTotal'];
					@endphp

					@foreach($loanArrVal[$relation] as $bankId => $bank)

						@if(is_numeric($bankId))
							@foreach($bank as $key => $value)

							@php
								$loc = $bank; 
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

									<td title="{{ $value['bank'] }}" class="bor_none">
										@if($temp != $loc)
											@if(strlen($value['bank']) > 5)
												{{ singlefieldlength($value['bank'],5) }}
											@else
												{{ $value['bank'] }}
											@endif
										@endif
									</td>

									<td title="{{ $value['loanName'] }}" class="clr_blue">
										@if(strlen($value['loanName']) > 7)
											{{ singlefieldlength($value['loanName'],7) }}
										@else
											{{ $value['loanName'] }}
										@endif
									</td>

									<td class="tar">
										{{ number_format($value['loanAmount']) }}
									</td>
									
									<td class="pl10" style="border-right: 2px solid black;">
										{{ $value['interestRate'] }} %
									</td>

									<td class="tar">
										{{ number_format((float)$value['principle'], 1) }}
									</td>
									<td class="tar">
										{{ number_format((float)$value['interest'], 1) }}
									</td>

									<td class="tar totAmt" style="border-right: 2px solid black;">
										@if(isset($loanArrVal[$relation][$bankId][$value['loanId']]['monthPayTotal']))
											{{ number_format((float)$loanArrVal[$relation][$bankId][$value['loanId']]['monthPayTotal'], 1) }}
										@endif
									</td>

									@for($j=1; $j <= 12; $j++)

										@php
											$monthTotArr[$j] += $loanArrVal[$relation][$bankId][$value['loanId']]['monthPayment'][$j];
											$selYrMnthVal = strtotime($request->selYear."-".$j);
										@endphp

										@if($loanArrVal[$relation][$bankId][$value['loanId']]['monthPayment'][$j] > 0)
											<td @if($currYrMnthVal >= $selYrMnthVal)
													class="tar pastMnth"
												@else 
													class="tar" 
												@endif>
												{{ number_format((float)$loanArrVal[$relation][$bankId][$value['loanId']]['monthPayment'][$j], 1) }}
											</td>
										@else
											<td @if ($currYrMnthVal >= $selYrMnthVal)
													class="tar pastMnth" 
												@else 
													class="tar" 
												@endif> - </td>
										@endif
									@endfor
								</tr>
							@endif

							@php 
								$temp = $loc;
								$k = 1;
							@endphp

							@endforeach

						@endif
					@endforeach
				@empty
					<tr>
						<td colspan="20" class="tac" id="nodatafound" style="color: red;">
							{{ trans('messages.lbl_nodatafound')}}
						</td>
					</tr>
				@endforelse

				@if(!empty($loanArrBelongsTo))
					<tr style="background-color: #fad6c3 !important">
						<td colspan="3" class="tar">{{ trans('messages.lbl_grandtot') }}</td>

						<td class="tar totAmt">{{ number_format($loanAmtTotal) }}</td>
						<td></td>

						<td class="tar totAmt">{{ number_format((float)$principleTotal, 1) }}</td>
						<td class="tar totAmt">{{ number_format((float)$interestTotal, 1) }}</td>
						<td class="tar totAmt">
							@if($grandTot != "")
								{{ number_format((float)$grandTot, 1) }}
							@endif
						</td>
						@for($j=1; $j <= 12; $j++)
							<td class="tar totAmt">
								{{ number_format((float)$monthTotArr[$j], 1) }}
							</td>
						@endfor
					</tr>
				@endif
			</tbody>
		</table>
	</div>
	{{ Form::close() }}

	@if($loanDetails->total())
		<div class="text-center col-xs-12 pm0 mt50">
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