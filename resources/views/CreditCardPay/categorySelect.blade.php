@extends('layouts.app')
@section('content')

@php use App\Http\Helpers; @endphp

{{ HTML::script('resources/assets/js/creditcardpay.js') }}
{{ HTML::script('resources/assets/js/lib/lightbox.js') }}
{{ HTML::style('resources/assets/css/lib/lightbox.css') }}
<script type="text/javascript">

	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>

	<div class="CMN_display_block" id="main_contents">

	<!-- article to select the main&sub menu -->
	@if($request->mainmenu =="AuditingCreditCardPay")
		<article id="auditing" class="DEC_flex_wrapper " data-category="auditing auditing_sub_5">
	@else
		<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_2">
	@endif

	{{ Form::open(array('name'=>'categoryWiseCreditCard', 'id'=>'categoryWiseCreditCard', 'url' => 'CreditCardPay/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('id', '', array('id' => 'id')) }}
		{{ Form::hidden('hidAuth', Auth::user()->userclassification, array('id' => 'hidAuth')) }}
		{{ Form::hidden('category', '', array('id' => 'category')) }}
		{{ Form::hidden('flgs',$request->flgs, array('id' => 'flgs')) }}
		
		<!-- Year Bar Start -->
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
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
		<div class="col-xs-6  pm0 pull-left mt10 " >
			<a href="javascript:gotoindex('{{ $request->selYear }}','{{ $request->selMonth }}','{{ $request->mainmenu }}','{{ $request->plimit }}','{{ $request->page }}','{{ $request->flgs }}');" class="btn btn-info box80 pull-left">
				<span class="fa fa-arrow-left"></span>
				{{ trans('messages.lbl_back') }}
			</a>
		</div>

		<div class="col-xs-6  pm0 pull-left mt10" align="right">

			@if(isset($getPreviousCount[0]->count) && $getPreviousCount[0]->count !="")
				<a href="javascript:setyearcategory('{{ $request->selYear-1 }}');" >
					<img style="vertical-align:middle;padding-bottom:3px;" src="{{ URL::asset('resources/assets/images/previousenab.png') }}" width="15" height="15" >
				</a>
			@else
				<img style="vertical-align:middle;padding-bottom:3px;" src="{{ URL::asset('resources/assets/images/previousdisab.png') }}" width="15" height="15" >
			@endif
				{{ $request->selYear }}
			@if(isset($getNextCount[0]->count) && $getNextCount[0]->count !="")
				<a href="javascript:setyearcategory('{{ $request->selYear +1 }}');"  >
					<img style="vertical-align:middle;padding-bottom:3px;" src="{{ URL::asset('resources/assets/images/nextenab.png') }}" width="15" height="15" >
				</a>
			@else
				<img style="vertical-align:middle;padding-bottom:3px;" src="{{ URL::asset('resources/assets/images/nextdisab.png') }}" width="15" height="15" >
			@endif
			<!-- <a href="javascript:addedit();" class="btn btn-success box100"><span class="fa fa-plus"></span> {{ trans('messages.lbl_register') }}</a> -->
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
				<!-- <col width="6.5%" class="divdisplay"> -->
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
					<!-- <th class="vam divdisplay"></th> -->
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
							<td colspan="5"></td>
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
							{{ $data->Category }}
						</td>
						<td>{{ $data->remarks }}</td>
						<td align="center">
							@if($data->file != "")
								<a style="text-decoration:none" href="{{ URL::asset('../../../../AccountingUpload/CreditCard').'/'.$data->file }}" data-lightbox="visa-img">
								<img width="20" height="20" name="empimg" id="empimg" 
								class=" box20 viewPic3by2" src="{{ URL::asset('../../../../AccountingUpload/CreditCard').'/'.$data->file }}"></a>
							@endif
						</td>
						<!-- <td align="center" class="divdisplay">
							<a href="javascript:fileUpload('{{ $data->id }}');">
								<img class="vam ml12" src="{{ URL::asset('resources/assets/images/uploadFile.png') }}" width="20" height="20">
							</a>
							<a href="javascript:editCreditCard('{{ $data->id }}');">
								<img class="vam ml12" src="{{ URL::asset('resources/assets/images/edit.png') }}" width="20" height="20">
							</a>	
						</td> -->
					</tr>
					@php 
						$creditCradId = $data->creditCardId;
						$CreditCardCheck = $data->creditCardId;
						$balanceAmt += $data->creditCardAmount;
					@endphp
				@empty
					<tr>
						<td class="text-center" colspan="8" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
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

	{{ Form::close() }}
	</article>
	</div>

@endsection

