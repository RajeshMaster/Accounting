@extends('layouts.app')
@section('content')
@php use App\Http\Helpers; @endphp
{{ HTML::style('resources/assets/css/common.css') }}
{{ HTML::style('resources/assets/css/widthbox.css') }}
{{ HTML::script('resources/assets/css/bootstrap.min.css') }}
{{ HTML::script('resources/assets/js/accbankdetails.js') }}
{{ HTML::style('resources/assets/css/sidebar-bootstrap.min.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
<style type="text/css">
	.alertboxalign {
		margin-bottom: -50px !important;
	}
	.alert {
		display:inline-block !important;
		height:30px !important;
		padding:5px !important;
	}
</style>
<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_4">
	{{ Form::open(array('name'=>'accbankdetailsview', 'id'=>'accbankdetailsview', 'url' => 'AccBankDetail/Viewlist?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('editFlg', '' , array('id' => 'editFlg')) }}
		{{ Form::hidden('intialValue', '' , array('id' => 'intialValue')) }}
		{{ Form::hidden('intialDate', '' , array('id' => 'intialDate')) }}
		{{ Form::hidden('accno', $request->accno , array('id' => 'accno')) }}
		{{ Form::hidden('bankname', $request->bankname , array('id' => 'bankname')) }}
		{{ Form::hidden('branchname', $request->branchname , array('id' => 'branchname')) }}
		{{ Form::hidden('bankid', $request->bankid , array('id' => 'bankid')) }}

		<!-- Year Bar Start -->
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('startdate', $request->startdate, array('id' => 'startdate')) }}
		<!-- Year Bar End -->


	<!-- Start Heading -->
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/bank_1.png') }}">
			<h2 class="pull-left pl5 mt10">
				{{ $request->bankname }}-{{ $request->branchname }}-{{ $request->accno }}
			</h2>
		</div>
	</div>
	
	<div class="box100per pl15 pr15 mt10">
		<div class="mt10 mb10">
			{{ Helpers::displayYear_MonthEst($account_period, $year_month, $db_year_month, $date_month, $dbnext, $dbprevious, $last_year, $current_year, $account_val) }}
		</div>
	</div>
	
	<div class="col-xs-12">
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
		<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
			<a href="javascript:gosingletoindex('{{ $request->mainmenu }}');" class="btn btn-info box80">
				<span class="fa fa-arrow-left"></span>
				{{ trans('messages.lbl_back') }}
			</a>
			<a href="javascript:gotoeditpage('1','{{ $baseAmtInsChk['0']->amount }}','{{ $baseAmtInsChk['0']->date }}');" class="btn btn-warning box100">
				<span class="fa fa-pencil"></span>
					{{ trans('messages.lbl_edit') }}
			</a>
		</div>
		<div class="col-xs-3 pull-right pt15" style="text-align: right;padding-right: 0px;">
			<span class="clr_blue fwb"> {{ trans('messages.lbl_balanceamt') }} :</span> <span class="fwb">
				¥ {{ number_format($balance) }}</span>
		</div>
	</div>
	<div class="pt43 minh350 pl15 pr15">
		<table class="tablealternate CMN_tblfixed">
			<colgroup>
				<col width="4%">
				<col width="8%">
				<col>
				<col width="10%">
				<col width="11%">
				<col width="15%">
				<col width="20%">
				<col width="3%">
			</colgroup>
			<thead class="CMN_tbltheadcolor">
				<tr>
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam">{{ trans('messages.lbl_Date') }}</th>
					<th class="vam">{{ trans('messages.lbl_content') }}</th>
					<th class="vam">{{ trans('messages.lbl_debit') }}</th>
					<th class="vam">{{ trans('messages.lbl_credit') }}</th>
					<th class="vam">{{ trans('messages.lbl_balance') }}</th>
					<th class="vam" colspan="2">{{ trans('messages.lbl_remarks') }}</th>
				</tr>
				<tr height="25px;">
					<td colspan="8"  style="color:black;vertical-align:middle;border-bottom:1px dotted #136E83;"></td>
				</tr>
				<tr height="25px;"><td colspan="8"></td></tr>
				<tr style = "background-color:#acf5e2;" class="tax_data_name">
					<td class="tax_data_name"></td>
					<td class="tax_data_name tac">
						@if($previous_date == "")
							{{ $baseAmtInsChk['0']->date }}
						@endif
					</td>
					<td class="tax_data_name">
						@if($previous_date != "")
							{{ trans('messages.lbl_brght_fwd') }}
						@else
							{{ trans('messages.lbl_ini_bal') }}
						@endif
					</td>
					<td class="tax_data_name"></td>
					<td class="tax_data_name tar"></td>
					<td class="tax_data_name tar" align="right">{{ number_format($curBal) }}</td>
					<td class="tax_data_name"></td>
					<td class="tax_data_name"></td>
				</tr>
				@php 
					$i =0;
					$balanceAmt = $curBal;
					$creditAmt = 0;
				@endphp
				@forelse($g_query as $key => $data)
					<tr>
						<td>{{ ($singleBank->currentpage()-1) * $singleBank->perpage() + $i + 1 }}</td>
						<td>{{ $data->date }}</td>
						<td>{{ $data->content }}</td>
						<td align="right">
							@if($data->transcationType == 1)
								@php $debitAmt = $data->amount + $data->fee; @endphp
								{{ number_format($debitAmt) }}
							@endif
						</td>

						<td align="right">
							@if($data->transcationType == 2 || $data->transcationType == 4)
								@php $creditAmt = $data->amount + $data->fee; @endphp
								{{ number_format($creditAmt) }}
							@endif
						</td>

						<td align="right">
							@if($data->transcationType == 1)
								<?php $balanceAmt = $balanceAmt - $debitAmt ;?>
							@else
								<?php $balanceAmt = $balanceAmt + $creditAmt ;?>
							@endif
							{{ number_format($balanceAmt) }}
						</td>
						<td></td>
						<td></td>
					</tr>
					@php $i++ @endphp
				@empty
					<tr>
						<td class="text-center" colspan="7" style="color: red;">
						{{ trans('messages.lbl_nodatafound') }}</td>
					</tr>
				@endforelse

				@if($i > 0)
					<tr style = "background-color:#acf5e2;" class="tax_data_name">
						<td class="tax_data_name"></td>
						<td class="tax_data_name tac">
						</td>
						<td class="tax_data_name">
							{{ trans('messages.lbl_car_fwd') }}
						</td>
						<td class="tax_data_name"></td>
						<td class="tax_data_name tar"></td>
						<td class="tax_data_name tar">{{ number_format($balanceAmt) }}</td>
						<td class="tax_data_name"></td>
						<td class="tax_data_name"></td>
					</tr>
				@endif
			</thead>
			<tbody>
			</tbody>
		</table>

		<div align="right" class="mt10 mr1per">
			<!-- 	<a href="javascript:fnchk();" class="btn btn-success box100">
					Checked
				</a> -->
				<a style="padding:3px 4px;" title="Get Previous Salary" disabled = "disabled" class="btn btn-disabled disabled box100">
					<span class=""></span>
						Checked
				</a>
		</div>


	</div>
	<div class="text-center pl14">
		@if(!empty($singleBank->total()))
			<span class="pull-left mt24">
				{{ $singleBank->firstItem() }} ~ {{ $singleBank->lastItem() }} / {{ $singleBank->total() }}
			</span>
		@endif 
		{{ $singleBank->links() }}
		<div class="CMN_display_block flr pr14">
			{{ $singleBank->linkspagelimit() }}
		</div>
	</div>
	{{ Form::close() }}
	</article>
</div>
@endsection
