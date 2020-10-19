@extends('layouts.app')
@section('content')
@php use App\Http\Helpers; @endphp
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
	$(document).ready(function() {
		setDatePicker("from_date");
		setDatePicker("to_date");
	});
		function mulclick(divid){
	    if($('#'+divid).css('display') == 'block'){
	      document.getElementById(divid).style.display = 'none';
	    }else {
	      document.getElementById(divid).style.display = 'block';
	    }
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
	.fb{
		color: gray !important;
	}
	.sort_asc {
		background-image:url({{ URL::asset('resources/assets/images/upArrow.png') }}) !important;
	}
	.sort_desc {
		background-image:url({{ URL::asset('resources/assets/images/downArrow.png') }}) !important;
	}
	.scrollbar
  	{
    float: left;
    max-height: 485px;
    width: 270px;
    overflow-x: hidden !important;
    overflow-y: scroll !important;
    margin-bottom: 5px;
  	}
	/* Dropdown Button */
	.dropbtn {
	    background-color: #4CAF50;
	    color: white;
	    padding: 16px;
	    font-size: 16px;
	    border: none;
	    cursor: pointer;
	}

	/* The container <div> - needed to position the dropdown content */
	.dropdown {
	    position: relative;
	    display: inline-block;
	}

	/* Dropdown Content (Hidden by Default) */
	.dropdown-content {
	    display: none;
	    position: absolute;
	    background-color: #f9f9f9;
	    min-width: 160px;
	    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
	    z-index: 1;
	}

	/* Links inside the dropdown */
	.dropdown-content a {
	    color: black;
	    padding: 5px 7px;
	    text-decoration: none;
	    display: block;
	}

	/* Change color of dropdown links on hover */
	.dropdown-content a:hover {background-color: #e5f4f9}

	/* Show the dropdown menu on hover */
	.dropdown:hover .dropdown-content {
	    display: block;
	}

	/* Change the background color of the dropdown button when the dropdown content is shown */
	.dropdown:hover .dropbtn {
	    background-color: #3e8e41;
	}
	.border_btm_solid_line{
	border-bottom:1px solid #A7D4DD;
	}
	/*.collapse {
    display: none ;
	}
	.collapse.in {
    display: block ;
	}*/
</style>
{{ HTML::script('resources/assets/js/invoice.js') }}
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::style('resources/assets/css/switch.css') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_1">
	{{ Form::open(array('name'=>'frminvoiceindex', 
						'id'=>'frminvoiceindex', 
						'url' => 'Invoice/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('filter', $request->filter, array('id' => 'filter')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	    {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	    {{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
	    {{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('topclick', $request->topclick, array('id' => 'topclick')) }}
		{{ Form::hidden('sortOptn',$request->invoicesort , array('id' => 'sortOptn')) }}
	    {{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
		{{ Form::hidden('ordervalue', $request->ordervalue, array('id' => 'ordervalue')) }}
		{{ Form::hidden('year_month', $date_month, array('id' => 'year_month')) }}
		{{ Form::hidden('searchmethod', $request->searchmethod, array('id' => 'searchmethod')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
		{{ Form::hidden('invoice_id', '', array('id' => 'invoice_id')) }}
		{{ Form::hidden('userid', '', array('id' => 'userid')) }}
		{{ Form::hidden('editflg', $request->editflg, array('id' => 'editflg')) }}
		{{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
		{{ Form::hidden('invoiceid', '', array('id' => 'invoiceid')) }}
		{{ Form::hidden('cust_id', $request->cust_id, array('id' => 'cust_id')) }}
		{{ Form::hidden('sendmailfrom', 'Invoice', array('id' => 'sendmailfrom')) }}
		{{ Form::hidden('estimate_id', '', array('id' => 'estimate_id')) }}
		{{ Form::hidden('currentRec', '', array('id' => 'currentRec')) }}
		{{ Form::hidden('invoicestatus', '', array('id' => 'invoicestatus')) }}
		{{ Form::hidden('invoicestatusid', '', array('id' => 'invoicestatusid')) }}
		{{ Form::hidden('companynameClick', $request->companynameClick, array('id' => 'companynameClick')) }}
		{{ Form::hidden('estid', '', array('id' => 'estid')) }}
		{{ Form::hidden('checkdefault', '', array('id' => 'checkdefault')) }}
		{{ Form::hidden('identEdit', 0, array('id' => 'identEdit')) }}

<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/invoices-icon-3.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_invoice') }}</h2>
		</div>
	</div>
	@if($request->searchmethod=="6" || $request->searchmethod=="")
	<div class="box100per pr10 pl10 mt10">
		<div class="mt10">
			{{ Helpers::displayYear_MonthEst($account_period, $year_month, $db_year_month, $date_month, $dbnext, $dbprevious,$last_year, $current_year, $account_val) }}
		</div>
	</div>
	@endif
<!-- End Heading -->
	<div class="col-xs-12 pm0 pull-left">
		
		<!-- Session msg -->
			@if(Session::has('success'))
				<div class="alertboxalign" role="alert">
					<p class="alert {{ Session::get('alert', Session::get('type') ) }}">
		            {{ Session::get('success') }}
		          	</p>
				</div>
			@endif
			@php Session::forget('success'); @endphp
		<!-- Session msg -->

		<div class="mr10 ml10 mt10">
		<div class="minh300">
			<table class="tablealternate box100per">
				<colgroup>
					<col width="5%">
					<col width="10%">
					<col width="">
					<col width="15%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader fwb tac"> 
			   			<th class="tac">{{ trans('messages.lbl_sno') }}</th>
						<th class="tac">{{ trans('messages.lbl_invoiceno') }}</th>
						<th class="tac">{{ trans('messages.lbl_dateofissue') }}</th>
						<th class="tac">{{ trans('messages.lbl_custname') }}</th>
						<th class="tac">{{ trans('messages.lbl_projecttitle') }}</th>
						<th class="tac">{{ trans('messages.lbl_paymentdate') }}</th>
						<th class="tac">{{ trans('messages.lbl_payamount') }}</th>
						<th class="tac">{{ trans('messages.lbl_paidamt') }}</th>
						<th class="tac">{{ trans('messages.lbl_difference') }}</th>
						<th class="tac">{{ trans('messages.lbl_status') }}</th>
			   		</tr>
			   	</thead>
			   	<tbody>
			   		<?php $i=0; ?>
			   		@php 

	   				$paymenttotal = 0; 
	   				$paidtotal = 0; 
	   				$differencetotal = 0; 


   				@endphp
			   		@forelse($TotEstquery as $key => $data)
			   		{{--*/ $invoice_balance[$key] = Helpers::fnfetchinvoicebalance($data->id); /*--}}
			   			<tr>
							<td class="text-center">
								{{ ($TotEstquery->currentpage()-1) * $TotEstquery->perpage() + $i + 1 }}
							</td>
							<td class="tal pr10 vam pt5">
								<div class="text-center vam">
									<label class="pm0 vam" style="color:#136E83;">
										{{ $data->user_id }}
									</label>
								</div>
							</td>
							<td>
								<div class="ml5 pt5">
									<div class="mb2">
										{{$data->quot_date}}
									</div>
								</div>
							</td>
							<td class="" align="left" >
								<div class="ml5 pt5">
									<div class="mb2">
										<b class="blue">{{$data->company_name}}</b>
									</div>
								</div>
							</td>

							<td class="" align="left" >
								{{$data->ProjectType}}
							</td>

							<td class="" align="center" >
								{{ $data->payment_date }}
							</td>

							<td class="" align="center" >

											{{ $data->totalval }}
			   					<?php  $totalval += preg_replace('/,/', '', $data->totalval); ?>
			   					{{--*/ $getTaxquery = Helpers::fnGetTaxDetails($data->quot_date); /*--}}
							<?php 
									if(!empty($data->totalval)) {
										if($data->tax != 2) {
			   								$totroundval = preg_replace("/,/", "", $data->totalval);
			   								$dispval = (($totroundval * intval((isset($getTaxquery[0]->Tax)?$getTaxquery[0]->Tax:0)))/100);
			   								$dispval1 = number_format($dispval);
			   								$grandtotal = $totroundval + $dispval;
			   							} else {
			   								$totroundval = preg_replace("/,/", "", $data->totalval);
											$dispval = 0;
											$grandtotal = $totroundval + $dispval;
											$dispval1 = $dispval;
										}
									}
			   						$grand_total = number_format($grandtotal);
									$divtotal += str_replace(",", "",$grand_total);

									if ($data->paid_status != 1) {
										$grand_style = "style='font-weight:bold;color:red;'";
										$balance += $grandtotal;
									} else {
										$grand_style = "style='font-weight:bold;color:green;'";
										$paid_amo += $grandtotal;
									}
									if($data->paid_status == 1) {
										$pay_balance = str_replace(",", "",(isset($invoice_balance[$key][0]->totalval)?$invoice_balance[$key][0]->totalval:0));
										$gr_total = number_format($grandtotal);
										$grand_tot = str_replace(",", "",$gr_total);
										$paid_amount += (isset($invoice_balance[$key][0]->deposit_amount)?$invoice_balance[$key][0]->deposit_amount:0);
										$bal_amount = $divtotal-$paid_amount;
									}
									if($data->paid_status != 1) {
										$gr_total = number_format($grandtotal);
										$grand_tot = str_replace(",", "",$gr_total);
										$bal_amount = $divtotal-$paid_amount;
									}
			   						if(isset($invbal[$key])) {
			   							if($invbal[$key]['bal_amount'] > 0) {
			   								$balance_style = "style='font-weight:bold;color:red;'";
			   							} else {
			   								$balance_style = "style='font-weight:bold;color:green;'";
			   							}
			   						}
			   						?>
			   					@if(!empty($data->totalval))
			   					<div class="ml5 mb2 smallBlue">
			   					<?php echo "<span style='background-color:#136E83;color:white;'>"; ?> {{trans('messages.lbl_tax')}}<?php echo"</span>&nbsp;" . $dispval1;$dispval1 = ''; ?>
			   					</div>
			   					@endif
			   					<div class="ml5 mb2 smallBlue" <?php echo $grand_style; ?>>
			   						{{ number_format($grandtotal) }}
			   					</div>
			   					
							</td>

							<td class="" align="center" >
								@if(isset($invbal[$key]))
	   									@if($invbal[$key]['bal_amount'] > 0)
	   										@if($invbal[$key]['bal_amount']==0)
	   										@php 
				   								$paidAmount = 0;
				   							@endphp 
	   										 {{ 0 }} 
	   										@else
	   										@php 
	   											$paidAmount = $grandtotal - $invbal[$key]['bal_amount'] ;
	   										 @endphp
	   										{{ number_format($paidAmount) }}
	   										@endif
	   									@else
	   										@if($invbal[$key]['bal_amount']==0)
	   										@php 
				   								$paidAmount = 0;
				   							@endphp 
	   										{{ 0 }}
	   										@else
	   										@php 
	   											$paidAmount = $grandtotal - $invbal[$key]['bal_amount'] 
	   										 @endphp
	   										{{ number_format($paidAmount ) }}
	   										@endif
	   									@endif
	   							@else
	   								@php  
										$paidAmount = 0;
	   								@endphp
	   								{{ 0 }}	
	   							@endif
							</td>

							<td class="" align="center" >
								<div class="ml5 mb2 smallBlue" <?php echo $balance_style; ?>>
	   								@if(isset($invbal[$key]))
	   									@if($invbal[$key]['bal_amount'] > 0)
	   										@if($invbal[$key]['bal_amount']==0)
	   										@php  
												$difftot = 0;
	   										@endphp
	   											{{ 0 }}
	   										@else
	   										<span class="vat font-s15">△</span>
												@php  
													$difftot = $invbal[$key]['bal_amount'];
	   											@endphp

	   										{{ number_format($invbal[$key]['bal_amount']) }}
	   										@endif
	   									@else
	   										@if($invbal[$key]['bal_amount']==0)
												@php  
													$difftot = 0;
		   										@endphp
	   											{{ 0 }}
	   										@else
	   										<span class="font-s20">●</span>
		   										@php  
													$difftot = $invbal[$key]['bal_amount'];
		   										@endphp
	   										{{ number_format($invbal[$key]['bal_amount']) }}
	   										@endif
	   									@endif
	   									@else
	   									@php  
											$difftot = 0;
		   								@endphp
	   								{{ 0 }}
	   								@endif
			   					</div>
							</td>
			   				@php 

				   				$paymenttotal += $grandtotal; 
				   				$paidtotal += $paidAmount; 
				   				$differencetotal += $difftot; 


			   				@endphp


			   				@php $grandtotal=0; @endphp

							<td class="" align="center">

							</td>
						</tr>
						<?php $i=$i+1; ?>
			   		@empty
						<tr>
							<td class="text-center" colspan="10" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
						</tr>
					@endforelse
					@if(count($TotEstquery) != 0)
						<tr style="background-color: #bfbbbb;">
							<td colspan="6" align="left">
								Total
							</td>
							<td align="right">
								{{ number_format($paymenttotal) }}
							</td>
							<td  align="right">
								{{ number_format($paidtotal) }}
							</td>
							<td align="right">
								{{ number_format($differencetotal) }}
							</td>
							<td align="right">

								@php
									$statusamout = $paymenttotal - $paidtotal -$differencetotal;
								@endphp
								
								{{ number_format($statusamout) }}
							</td>
						</tr>
					@endif
			   	</tbody>
			</table>
		</div>
		<div class="text-center">
			@if(!empty($TotEstquery->total()))
				<span class="pull-left mt24">
					{{ $TotEstquery->firstItem() }} ~ {{ $TotEstquery->lastItem() }} / {{ $TotEstquery->total() }}
				</span>
			@endif 
			{{ $TotEstquery->links() }}
			<div class="CMN_display_block flr mr0">
          		{{ $TotEstquery->linkspagelimit() }}
        	</div>
		</div>
		</div>

	{{ Form::hidden('totalrecords', $TotEstquery->total(), array('id' => 'totalrecords')) }}
	{{ Form::close() }}
	{{ Form::open(array('name'=>'frminvoiceexceldownload', 
						'id'=>'frminvoiceexceldownload', 
						'url' => 'Invoice/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	{{ Form::hidden('selYearMonth', '', array('id' => 'selYearMonth')) }}
	{{ Form::close() }}

	{{ Form::open(array('name'=>'frmallinvoiceexceldownload', 
						'id'=>'frmallinvoiceexceldownload', 
						'url' => 'Invoice/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('hdn_invoice_arr', '', array('id' => 'hdn_invoice_arr')) }}
	{{ Form::close() }}
</article>
</div>
<script type="text/javascript">
	var recordTotal = '<?php echo $TotEstquery->total(); ?>';
	$('#totalrecords').val(recordTotal);
</script>
@endsection