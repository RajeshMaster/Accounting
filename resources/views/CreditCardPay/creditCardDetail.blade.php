@extends('layouts.app')
@section('content')

{{ HTML::script('resources/assets/js/creditcardpay.js') }}

<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
	function pageClick(pageval) {
		$('#page').val(pageval);
			$("#creditCaredPayIndex").submit();
	}
	function pageLimitClick(pagelimitval) {
		$('#page').val('');
		$('#plimit').val(pagelimitval);
		$("#creditCaredPayIndex").submit();
	}
</script>

	<div class="CMN_display_block" id="main_contents">

	<!-- article to select the main&sub menu -->

	<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_2">

	{{ Form::open(array('name'=>'creditCaredPayIndex', 'id'=>'creditCaredPayIndex', 'url' => 'CreditCardPay/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}

		
	<!-- Start Heading -->

	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/bank_1.png') }}">
			<h2 class="pull-left pl5 mt10 CMN_mw150">
				Data View
			</h2>
		</div>
	</div>
	<!-- End Heading -->

	<div class="pt43 minh300 pl10 pr10 " style="padding:3px 3px 20px">
		<table class="tablealternate CMN_tblfixed mt10">
			<colgroup>
				<col width="4%">
				<col width="9%">
				<col width="14%">
				<col width="16%">
				<col width="8%">
				<col width="8%">
				<!-- <col width="8%"> -->
				<col width="">
			</colgroup>

			<thead class="CMN_tbltheadcolor">
				<tr id="data">
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam">{{ trans('messages.lbl_Date') }}</th>
					<th class="vam">{{ trans('messages.lbl_content') }}</th>
					<th class="vam">{{ trans('messages.lbl_amount') }}</th>
					<th class="vam ">{{ trans('messages.lbl_bill') }}</th>
					<th class="vam">{{ trans('messages.lbl_categorie') }}</th>
					<th class="vam">{{ trans('messages.lbl_remarks') }}</th>
				</tr>
			</thead>
			<tbody>
				@php
					$i = 1;
				@endphp
				@forelse($sheetData as $key => $data)
					@if($key != 0 )
						<tr>
							<td>{{ $i }}</td>
							<td>{{ $data[0] }}</td>
							<td>{{ $data[1] }}</td>
							<td align="right">{{ $data[2] }}</td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						@php
							$i++;
						@endphp
					@endif
				
				@empty
					<tr>
						<td class="text-center columnspan" colspan="9" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
					</tr>
				@endforelse

		
			</tbody>
		</table>

	</div>
	{{ Form::close() }}
	</article>
	</div>

@endsection

