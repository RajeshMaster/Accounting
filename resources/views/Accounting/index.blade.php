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
{{ HTML::script('resources/assets/js/accounts.js') }}
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::style('resources/assets/css/switch.css') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
<div class="CMN_display_block" id="main_contents" style="width: 100%">
<!-- article to select the main&sub menu -->
<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_1">
	{{ Form::open(array('name'=>'frmaccountingindex', 
						'id'=>'frmaccountingindex', 
						'url' => 'Accounting/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('filter', $request->filter, array('id' => 'filter')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	    {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	    {{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
		{{ Form::hidden('sortOptn',$request->invoicesort , array('id' => 'sortOptn')) }}
	    {{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
		{{ Form::hidden('searchmethod', $request->searchmethod, array('id' => 'searchmethod')) }}
		{{ Form::hidden('edit_flg', '', array('id' => 'edit_flg')) }}
		{{ Form::hidden('editId', '', array('id' => 'editId')) }}

		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}

<!-- Start Heading -->
	<div class="row hline" >
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/pettycash.jpg') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_accounting') }}</h2>
		</div>
	</div>

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
			<a href="javascript:addedit('index','{{ $request->mainmenu }}');" class="btn btn-success box100"><span class="fa fa-plus"></span> {{ trans('messages.lbl_register') }}</a>
		</div>
	</div>

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
				<col width="8%">
				<col width="8%">
			</colgroup>

			<thead class="CMN_tbltheadcolor">
				<tr>
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam">{{ trans('messages.lbl_Date') }}</th>
					<th class="vam">{{ trans('messages.lbl_subject') }}</th>
					<th class="vam">{{ trans('messages.lbl_content') }}</th>
					<th class="vam">{{ trans('messages.lbl_debit') }}</th>
					<th class="vam">{{ trans('messages.lbl_credit') }}</th>
					<!-- <th class="vam">{{ trans('messages.lbl_balance') }}</th> -->
					<th class="vam">{{ trans('messages.lbl_remarks') }}</th>
					<th class="vam">{{ trans('messages.lbl_file') }}</th>
					<th class="vam">{{ trans('messages.lbl_copy') }}</th>
				</tr>
			</thead>
			<tbody>
				@php 
					$i = 0;
					$balanceAmt = 0;
					$debitAmt = 0;
					$creditAmt = 0;
					$lastBankName = "";
					$preBankName = "";
				@endphp
				@forelse($cashDetails as $key => $data)
					@if($preBankName != $data['Bank_NickName'] && $preBankName !="")
						<tr style="background-color: #f1a2a2">
							<td colspan="6" >
								{{ trans('messages.lbl_total') }}
							</td>
							<td colspan="3">
								{{ $balanceAmt }}
								@php $balanceAmt = 0; @endphp
							</td>
						</tr>
					@endif
					@if($lastBankName != $data['Bank_NickName'])
						<tr style="background-color: lightgrey">
							<td colspan="9" > {{ $data['Bank_NickName'] }} </td>
						</tr>
					@endif
					<tr style="background-color: #FCE1F0">
						<td>{{ $i+1 }}</td>
						<td align="center">
							{{ $data['date'] }}
						</td>
						<td> {{ $data['subject'] }} </td>
						<td>{{ $data['content'] }}</td>
						<td align="right">
							@if($data['transcationType'] == 1)
								@php $debitAmt = $data['amount'] + $data['fee']; @endphp
								{{ number_format($debitAmt) }}
							@endif
						</td>
						<td align="right">
							@if($data['transcationType'] == 2 || $data['transcationType'] == 4)
								@php $creditAmt = $data['amount'] + $data['fee']; @endphp
								{{ number_format($creditAmt) }}
							@endif
						</td>
							
						<td>{{ $data['remarks']}}</td>
						<td>{{ $data['fileDtl'] }}</td>
							
						<td>
							@if($data['id'] != $data['transferId'])
							<a href="javascript:editCashDtl('{{ $data['id'] }}','1','{{ $data['pageFlg'] }}');">
								<img class="vam" src="{{ URL::asset('resources/assets/images/edit.png') }}" width="20" height="20">
							</a>
							<a href="javascript:editCashDtl('{{ $data['id'] }}','2','{{ $data['pageFlg'] }}');">
								<img class="vam" src="{{ URL::asset('resources/assets/images/copy.png') }}" width="20" height="20">
							</a>
							@endif
						</td>
					</tr>
					@if($data['transcationType'] == 1)
						<?php $balanceAmt = $balanceAmt - $debitAmt ;?>
					@else
						<?php $balanceAmt = $balanceAmt + $creditAmt ;?>
					@endif
					@php
						$lastBankName = $data['Bank_NickName'];
						$preBankName = $data['Bank_NickName'];
						$i++ ;
					@endphp 
				@empty
					<tr>
						<td class="text-center" colspan="10" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
					</tr>
				@endforelse

				@if(count($cashDetails) > 0)
					<tr style="background-color: #f1a2a2">
						<td colspan="6">{{ trans('messages.lbl_total') }}</td>
						<td colspan="3">{{ $balanceAmt }}</td>
					</tr>
				@endif
				
			</tbody>
		</table>

	</div>

</article>
</div>

@endsection