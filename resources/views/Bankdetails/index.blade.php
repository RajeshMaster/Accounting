@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/bankdetails.js') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
</script>
	<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_6">
	{{ Form::open(array('name'=>'bankdetailsindex', 'id'=>'bankdetailsindex', 'url' => 'Bankdetails/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
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
			<span class="clr_blue fwb"> {{ trans('messages.lbl_balanceamt') }} :</span> <span class="fwb">¥ {{ $bal }}</span>
		</div>
	</div>
	<div class="pt43 minh300 pl15 pr15" style="padding:3px 3px 20px">
		<table class="tablealternate CMN_tblfixed">
			<colgroup>
				<col width="4%">
				<col>
				<col width="12%">
				<col width="12%">
				<col width="8%">
				<col width="16%">
				<col width="12%">
			</colgroup>
			<thead class="CMN_tbltheadcolor">
				<tr>
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam">{{ trans('messages.lbl_bank_name') }}</th>
					<th class="vam">{{ trans('messages.lbl_branch_name') }}</th>
					<th class="vam">{{ trans('messages.lbl_account_no') }}</th>
					<th class="vam">{{ trans('messages.lbl_Start_date') }}</th>
					<th class="vam">{{ trans('messages.lbl_balanceamt') }}</th>
					<th class="vam"></th>
				</tr>
			</thead>
			<tbody>
				@if(count($get_det)!="")
	 				@for ($i = 0; $i < count($get_det); $i++)
	 					<tr>
							<td class="bor_rightbot_none text-center">
								{{ ($index->currentpage()-1) * $index->perpage() + $i + 1 }}
							</td>
							<td>
								{{ $get_det[$i][1] }}
							</td>
							<td>
								{{ $get_det[$i][2] }}
							</td>
							<td>
								{{ $get_det[$i][0] }}
							</td>
							<td class="tac">
								{{ $get_det[$i][6] }}
							</td>
							<td class="tar pr5">
								@if($get_det[$i][4] == "")
									0
								@else
									@if(isset($get_bankdet[$i]['blnc']))
								@php $sad = 0; $sad += $get_bankdet[$i]['blnc']; @endphp
										{{ number_format($get_bankdet[$i]['blnc']) }}
									@endif
								@endif
							</td>
							<td class="tac">
								@if($get_det[$i][5] != 1)
									<a href="javascript:gotoadd('{{ $get_det[$i][1] }}','{{ $get_det[$i][2] }}','{{ $get_det[$i][0] }}','{{ $get_det[$i][6] }}','{{ $get_det[$i][3] }}','{{ $get_det[$i][7] }}','{{ $get_det[$i][8] }}','{{ $get_det[$i][9] }}','{{ $get_det[$i]['id'] }}','{{ $request->mainmenu }}',1)" class="anchorstyle">
										{{ trans('messages.lbl_balance_entry') }}
									</a>
								@else
									<a href="javascript:gotoviewlist('{{ $get_det[$i][1] }}','{{ $get_det[$i][2] }}','{{ $get_det[$i][0] }}','{{ $get_det[$i][6] }}','{{ $get_det[$i][3] }}','{{ $get_det[$i][7] }}','{{ $get_det[$i][8] }}','{{ $get_det[$i][9] }}','{{ $get_det[$i]['id'] }}','{{ $request->mainmenu }}',1)" class="anchorstyle">
										{{ trans('messages.lbl_Details') }}
									</a>
										@if(isset($get_bankdet[$i]['chk_flg'])) 
											@if($get_bankdet[$i]['chk_flg'] == 1)
												<img class="box19" src="{{ URL::asset('resources/assets/images/tick_1.png') }}">
											@elseif(isset($get_bankdet[$i]['chk_flg']) 
												&& $get_bankdet[$i]['chk_flg'] == 0)
													<img class="box19" src="{{ URL::asset('resources/assets/images/close.png') }}">
											@endif
										@else
												<img class="box19" src="{{ URL::asset('resources/assets/images/norecord.png') }}">
										@endif
								@endif
							</td>
						</tr>
					@endfor
				@else
						<tr>
							<td class="text-center colred" colspan="9">
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
