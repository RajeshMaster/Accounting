@extends('layouts.app')
@section('content')

@php use App\Http\Helpers; @endphp

{{ HTML::script('resources/assets/js/creditcardpay.js') }}
{{ HTML::script('resources/assets/js/lib/lightbox.js') }}
{{ HTML::style('resources/assets/css/lib/lightbox.css') }}
<script type="text/javascript">

	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
	$(document).ready(function() {
		if($('#hidAuth').val() == "5" || mainmenu == "AuditingCreditCardPay"){
			$(".divdisplay").css("display", "none");
			$(".cleardata").css("display", "none");
			$('.columnspanpagination').attr('colspan','8');
			$('.columnspan1').attr('colspan','4');
		}else{
			$(".divdisplay").css("");
			$(".cleardata").css("");
			$('.columnspanpagination').attr('colspan','9');
			$('.columnspan1').attr('colspan','5');
		}
	});
</script>

	<div class="CMN_display_block" id="main_contents">

	<!-- article to select the main&sub menu -->
	@if($request->mainmenu =="AuditingCreditCardPay")
		<article id="auditing" class="DEC_flex_wrapper " data-category="auditing auditing_sub_5">
	@else
		<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_2">
	@endif

	{{ Form::open(array('name'=>'yearWiseCreditCard', 'id'=>'yearWiseCreditCard', 'url' => 'CreditCardPay/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('id', '', array('id' => 'id')) }}
		{{ Form::hidden('creditCardId', '', array('id' => 'creditCardId')) }}
		{{ Form::hidden('hidAuth', Auth::user()->userclassification, array('id' => 'hidAuth')) }}
		{{ Form::hidden('category', '', array('id' => 'category')) }}
		
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
		<div class="col-xs-6  pm0 pull-left mt10 divdisplay">
			@if(isset($getPreviousCount[0]->count) && $getPreviousCount[0]->count !="")
				<a href="javascript:setyear('{{ $request->selYear-1 }}');" >
					<img style="vertical-align:middle;padding-bottom:3px;" src="{{ URL::asset('resources/assets/images/previousenab.png') }}" width="15" height="15" >
				</a>
			@else
				<img style="vertical-align:middle;padding-bottom:3px;" src="{{ URL::asset('resources/assets/images/previousdisab.png') }}" width="15" height="15" >
			@endif
				{{ $request->selYear }}
			@if(isset($getNextCount[0]->count) && $getNextCount[0]->count !="")
				<a href="javascript:setyear('{{ $request->selYear +1 }}');"  >
					<img style="vertical-align:middle;padding-bottom:3px;" src="{{ URL::asset('resources/assets/images/nextenab.png') }}" width="15" height="15" >
				</a>
			@else
				<img style="vertical-align:middle;padding-bottom:3px;" src="{{ URL::asset('resources/assets/images/nextdisab.png') }}" width="15" height="15" >
			@endif

			


			
			<!-- <a href="javascript:addedit();" class="btn btn-success box100"><span class="fa fa-plus"></span> {{ trans('messages.lbl_register') }}</a> -->
		</div>
		<div class="col-xs-6  pm0 pull-left mt10 divdisplay" align="right">
			<a href="javascript:yearWise();" class="btn btn-success disabled" ></span>Year Wise</a>
			<a href="javascript:ccMonthWise();" class="btn btn-success "></span>CC month Wise</a>
			<a href="javascript:monthWise();" class="btn btn-success "></span>MOnthly Wise</a>
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
				<col width="6.5%" class="divdisplay">
			</colgroup>

			<thead class="CMN_tbltheadcolor">
				<tr id="data">
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam">{{ trans('messages.lbl_Date') }}</th>
					<th class="vam">{{ trans('messages.lbl_amount') }}</th>
				</tr>
			</thead>
			<tbody>
				@for($i=0;$i < count($creditcardDetails);$i++)
				<tr>
					<td>{{ $i+1 }}</td>
					<td>{{ $creditcardDetails[$i]->selectedYearMonth }}</td>
					<td>{{ number_format($creditcardDetails[$i]->amount) }}</td>
				</tr>
				@endfor
			</tbody>
		</table>

	</div>

	{{ Form::close() }}
	</article>
	</div>

@endsection

