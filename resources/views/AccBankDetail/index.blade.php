@extends('layouts.app')

@section('content')

{{ HTML::script('resources/assets/js/accbankdetails.js') }}

<script type="text/javascript">

	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
	function pageClick(pageval) {
		$('#page').val(pageval);
			$("#Accbankdetailsindex").submit();
	}
	function pageLimitClick(pagelimitval) {
		$('#page').val('');
		$('#plimit').val(pagelimitval);
		$("#Accbankdetailsindex").submit();
	}
</script>

	<div class="CMN_display_block" id="main_contents">

	<!-- article to select the main&sub menu -->

	<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_4">

	{{ Form::open(array('name'=>'Accbankdetailsindex', 'id'=>'Accbankdetailsindex', 'url' => 'AccBankDetail/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,

		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('id', '' , array('id' => 'id')) }}
		{{ Form::hidden('bankid', '' , array('id' => 'bankid')) }}
		{{ Form::hidden('bankname', '' , array('id' => 'bankname')) }}
		{{ Form::hidden('branchname', '' , array('id' => 'branchname')) }}
		{{ Form::hidden('accno', '' , array('id' => 'accno')) }}
		{{ Form::hidden('startdate', '' , array('id' => 'startdate')) }}
		{{ Form::hidden('bankids', '' , array('id' => 'bankids')) }}
		{{ Form::hidden('branchids', '' , array('id' => 'branchids')) }}
		{{ Form::hidden('balbankid', '' , array('id' => 'balbankid')) }}
		{{ Form::hidden('editflg', '' , array('id' => 'editflg')) }}
		{{ Form::hidden('checkflg', '' , array('id' => 'checkflg')) }}
	<!-- Start Heading -->

	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/bank_1.png') }}">
			<h2 class="pull-left pl5 mt10 CMN_mw150">
					{{ trans('messages.lbl_allbnkdetail') }}
			</h2>
		</div>
	</div>

	<div class="col-xs-12 pt5">
		<div class="col-xs-6 pull-right pt10" style="text-align: right;padding-right: 0px;">
			<span class="clr_blue fwb"> {{ trans('messages.lbl_balanceamt') }} :</span> <span class="fwb">¥ {{ number_format($totalBalance) }}</span>
		</div>
	</div>

	<div class="pt43 minh300 pl15 pr15" style="padding:3px 3px 20px">
		<table class="tablealternate CMN_tblfixed">
			<colgroup>
				<col width="4%">
				<col>
				<col width="12%">
				<col width="12%">
				<col width="13%">
				<col width="8%">
				<col width="12%">
			</colgroup>

			<thead class="CMN_tbltheadcolor">
				<tr>
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam">{{ trans('messages.lbl_bank_name') }}</th>
					<th class="vam">{{ trans('messages.lbl_nickname') }}</th>
					<th class="vam">{{ trans('messages.lbl_branch_name') }}</th>
					<th class="vam">{{ trans('messages.lbl_account_no') }}</th>
					<th class="vam">{{ trans('messages.lbl_Start_date') }}</th>
					<th class="vam">{{ trans('messages.lbl_balanceamt') }}</th>
					<th class="vam"></th>
				</tr>
			</thead>

			<tbody>
				@php $i =0 @endphp
				@forelse($bankdetailindex as $key => $data)
				<tr>
					<td class="bor_rightbot_none text-center">
						{{ ($index->currentpage()-1) * $index->perpage() + $i + 1 }}
					</td>

					<td>
						{{ $data['banknm'] }}
					</td>

					<td>
						{{ $data['nickName'] }}
					</td>

					<td>
						{{ $data['brnchnm'] }}
					</td>

					<td>
						{{ $data['AccNo'] }}
					</td>

					<td>
						{{ $data['startDate'] }}
					</td>

					<td align="right">
						{{ number_format($data['balanceAmt']) }}
					</td>

					<td>
						@if($data['baseAmtInsChk'] == 0)
							<a href="javascript:gotoadd('{{ $data['banknm'] }}','{{ $data['brnchnm'] }}','{{ $data['AccNo'] }}','{{ $data['startDate'] }}','{{ $data['bankId'] }}','{{ $data['brnchid'] }}')" class="anchorstyle">
								{{ trans('messages.lbl_balance_entry') }}
							</a>
						@else
							<a href="javascript:gotoviewlist('{{ $data['banknm'] }}','{{ $data['brnchnm'] }}','{{ $data['AccNo'] }}','{{ $data['startDate'] }}','{{ $data['bankId'] }}','{{ $data['brnchid'] }}')" class="anchorstyle">
								{{ trans('messages.lbl_Details') }}
							</a>
						@endif
					</td>
				</tr>
				@php $i++ @endphp
				@empty
					<tr>
						<td class="text-center" colspan="6" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
					</tr>
				@endforelse
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

