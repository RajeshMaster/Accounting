@extends('layouts.app')
@section('content')
{{ HTML::style('resources/assets/css/common.css') }}
{{ HTML::script('resources/assets/css/bootstrap.min.css') }}
{{ HTML::script('resources/assets/js/loan.js') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
</script>
	<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_4">
	{{ Form::open(array('name'=>'loanindex', 'id'=>'loanindex', 'url' => 'Loandetails/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('id', '' , array('id' => 'id')) }}
		{{ Form::hidden('head', '' , array('id' => 'head')) }}
		{{ Form::hidden('editflg', '' , array('id' => 'editflg')) }}
	<!-- Start Heading -->
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/loan.jpg') }}">
			<h2 class="pull-left pl5 mt10 CMN_mw150">
					{{ trans('messages.lbl_loandetail') }}
			</h2>
		</div>
	</div>
	<div class="col-xs-12 pt5">
			<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
				<a href="javascript:gotoadd('{{ $request->mainmenu }}',1);" class="btn btn-success box100">
					<span class="fa fa-plus"></span>
					{{ trans('messages.lbl_add') }}
				</a>
			</div>
			<div class="col-xs-6 pull-right pt10" style="text-align: right;padding-right: 0px;">
				<span class="clr_blue fwb"> {{ trans('messages.lbl_remtotamt') }} :</span> <span class="fwb">¥ {{ number_format($remTot) }}</span>
			</div>
		</div>
	<div class="pt43 minh350 pl15 pr15">
		<table class="tablealternate CMN_tblfixed">
			<colgroup>
				<col width="4%">
				<col width="8%">
				<col width="8%">
				<col width="8%">
				<col width="16%">
				<col width="4%">
				<col width="8%">
				<col width="8%">
				<col width="8%">
				<col width="6%">
				<col width="6%">
			</colgroup>
			<thead class="CMN_tbltheadcolor">
				<tr>
					<th class="vam" rowspan="2">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam" rowspan="2">{{ trans('messages.lbl_loanno') }}</th>
					<th class="vam" colspan="2" rowspan="1">{{ trans('messages.lbl_Date') }}</th>
					<th class="vam" rowspan="2">{{ trans('messages.lbl_bankdetail') }}</th>
					<th class="vam" rowspan="2" title="Payment Day">{{ trans('messages.lbl_epayday') }}</th>
					<th class="vam" colspan="3" rowspan="1">{{ trans('messages.lbl_loan') }}</th>
					<th class="vam" rowspan="2" title="Remaining Months">{{ trans('messages.lbl_remainmonth') }}</th>
					<th class="vam" rowspan="2" title=""></th>
				</tr>
				<tr class="CMN_tbltheadcolor">
					<th title="Start Date">{{ trans('messages.lbl_start') }}</th>
					<th title="End Date">{{ trans('messages.lbl_end') }}</th>
					<th>{{ trans('messages.lbl_amount') }}</th>
					<th>{{ trans('messages.lbl_paid') }}</th>
					<th>{{ trans('messages.lbl_remain') }}</th>
				</tr>
			</thead>
			<tbody>
				@if(!empty($loanindex))
					@for ($i = 0; $i < count($loanindex); $i++)
						<tr>
							<td class="bor_rightbot_none text-center">
								{{ ($index->currentpage()-1) * $index->perpage() + $i + 1 }}
							</td>
							<td class="tac">
								<a href="javascript:goToview('{{ $loanindex[$i]['loanno'] }}','{{ $request->mainmenu }}');" class="anchorstyle">
									<b>{{ $loanindex[$i]['loanno'] }}</b></a><br/>

							@if($loanindex[$i]['pdffile'] !="")
							<?php $file_url = 'resources/assets/uploadandtemplates/upload/Loandetails/' . $loanindex[$i]['pdffile']; ?>
							@if(file_exists($file_url))
							<a class="tac anchorstyle" href="javascript:download('{{ $loanindex[$i]['pdffile'] }}','../../../resources/assets/uploadandtemplates/upload/Loandetails');" class="tal" style='color:blue;'>
								{{ $loanindex[$i]['pdffile'] }}
							</a>
							@endif
							@endif
							</td>
							<td class="tac">
								@if($loanindex[$i]['receiveddate'] == "0000-00-00")
								@else
									{{ $loanindex[$i]['receiveddate'] }}
								@endif
							</td>
							<td class="tac">
								@if($loanindex[$i]['enddate'] == "0000-00-00")
								@else
									{{ $loanindex[$i]['enddate'] }}
								@endif
							</td>
							<td>
									{{ $loanindex[$i]['bankid'] }}</br>
									{{ $loanindex[$i]['BranchName']." - ".$loanindex[$i]['AccNo'] }}
							</td>
							<td class="tac pr5">
								@if($loanindex[$i]['repaymentday'] == "0")
								@else
									{{ $loanindex[$i]['repaymentday'] }}
								@endif
							</td>
							<td class="text-right pr5">
									{{ $loanindex[$i]['amount'] }}
							</td>
							<td class="text-right pr5">
								@php $paid =$paid = str_replace(",", "", $loanindex[$i]['amount']) - (str_replace(",", "", $loanindex[$i]['currentbalance']) - str_replace(",", "",$loanindex[$i]['payamount'])); @endphp
									{{ number_format($paid) }}
							</td>
							<td class="text-right pr5">
								@php $remTot = ""; $rem = str_replace(",", "", $loanindex[$i]['currentbalance']) - str_replace(",", "", $loanindex[$i]['payamount']) ; $remTot += $rem; @endphp
									{{ number_format($rem) }}
							</td>
							<td class="tac pr5">
								@php $month = $loanindex[$i]['remainingmonths'] - $loanindex[$i]['paycount']; @endphp
									{{ $month }}
							</td>
							<td class="tac">
								<a href="javascript:goToSingleview('{{ $loanindex[$i]['loanno'] }}','{{ $request->mainmenu }}','{{ $loanindex[$i]['bankid']." - ". $loanindex[$i]['BranchName']." - ".$loanindex[$i]['AccNo'] }}');" class="anchorstyle">
									{{ trans('messages.lbl_Details') }}
								</a>
							</td>
						</tr>
					@endfor
				@else 
						<tr>
							<td class="text-center colred" colspan="11">
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
	<!-- End Heading -->
	{{ Form::close() }}
	</article>
	</div>
@endsection
