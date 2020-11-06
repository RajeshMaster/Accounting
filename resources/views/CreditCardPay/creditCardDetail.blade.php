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

	{{ Form::open(array('name'=>'creditCardDtls', 'id'=>'creditCardDtls', 
						'url' => 'CreditCardPay/creditCardAddDtls?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,'method' => 'POST')) }}

		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('mainDate', $request->mainDate , array('id' => 'mainDate')) }}
		{{ Form::hidden('creditCardId', $request->creditCard , array('id' => 'creditCardId')) }}
		{{ Form::hidden('sheetData', count($sheetData) , array('id' => 'sheetData')) }}

		
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

	<div class="minh300 mt10 mb10" style="padding:2px 2px 2px 2px">
		<table class="tablealternate CMN_tblfixed mt10">
			<colgroup>
				<col width="5%">
				<col width="10%">
				<col width="20%">
				<col width="10%">
				<col width="12%">
				<col width="15%">
				<col width="38%">
			</colgroup>

			<thead class="CMN_tbltheadcolor">
				<tr id="data">
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam">{{ trans('messages.lbl_Date') }}</th>
					<th class="vam">{{ trans('messages.lbl_content') }}</th>
					<th class="vam">{{ trans('messages.lbl_amount') }}</th>
					<th class="vam ">{{ trans('messages.lbl_bill') }}</th>
					<th class="vam">{{ trans('messages.lbl_categories') }}</th>
					<th class="vam">{{ trans('messages.lbl_remarks') }}</th>
				</tr>
			</thead>
			<tbody>
				@php
					$i = 1;
				@endphp
				@forelse($sheetData as $key => $data)
					@if($key != 0 && $i != count($sheetData)-1)
						<tr>
							<td class="tac">{{ $i }}</td>
							<td class="tac">
								{{ Form::hidden('creditCardDate'.$i, $data[0] , array('id' => 'creditCardDate'.$i)) }}
								{{ $data[0] }}
							</td>
							<td>
								{{ Form::hidden('creditCardContent'.$i, $data[1] , array('id' => 'creditCardContent'.$i)) }}
								{{ $data[1] }}</td>
							<td align="right">
								{{ Form::hidden('creditCardAmount'.$i, $data[2] , array('id' => 'creditCardAmount'.$i)) }}
								{{ $data[2] }}</td>
							<td class="tac">
							<label style="font-weight: normal;display: inline-block;">
								{{ Form::radio('rdoBill'.$i, '1',1, 
											array('id' =>'Bill1'.$i,
												  'name' => 'rdoBill'.$i,
												  'class' => 'Bill1'.$i,
												  'style' => 'margin:-2px 0 0 !important',
												  'checked' => 'true',
												  'data-label' => trans('messages.lbl_bill'))) }}
								<span class="vam">&nbsp; 有 &nbsp;</span>
							</label>
							<label style="font-weight: normal;display: inline-block;">
								{{ Form::radio('rdoBill'.$i, '2',2, 
											array('id' =>'Bill2'.$i,
												  'name' => 'rdoBill'.$i,
												  'class' => 'Bill2'.$i,
												  'style' => 'margin:-2px 0 0 !important',
												  'data-label' => trans('messages.lbl_bill'))) }}
								<span class="vam">&nbsp; 無 &nbsp;</span>
							</label>
							</td>
							<td class="tac">
								{{ Form::select('categoryId'.$i,[null=>'']+$categoryName,		array('name' =>'categoryId'.$i,
										'id'=>'categoryId'.$i,
										'data-label' =>trans('messages.lbl_categories'),
										'class'=>'pl10 widthauto'))}}	
							</td>
							<td class="tac">
								{{ Form::text('remarks'.$i,$data[6], 
										array('name' => 'remarks'.$i,
										 		'id' => 'remarks'.$i,
												'class' => 'pl10 box95per form-control')) }}
							</td>
						</tr>
						@php
							$i++;
						@endphp
					@endif
				@empty
					<tr>
						<td class="text-center columnspan" colspan="7" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
					</tr>
				@endforelse

			</tbody>
		</table>
	</div>
	<div class="col-xs-12">
		<div class="col-xs-5"></div>
		<div class="col-xs-2">
			<button type="submit" class="btn btn-success mt10 mb10 creditcashprocess">
				{{ trans('messages.lbl_submit') }}
			</button>
		</div>
		<div class="col-xs-5"></div>
	</div>
	{{ Form::close() }}
	</article>
	</div>

@endsection

