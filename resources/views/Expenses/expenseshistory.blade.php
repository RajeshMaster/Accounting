@extends('layouts.app')
@section('content')
<?php use App\Model\Expenses; ?>
{{ HTML::style('resources/assets/css/common.css') }}
{{ HTML::style('resources/assets/css/widthbox.css') }}
{{ HTML::script('resources/assets/css/bootstrap.min.css') }}
{{ HTML::script('resources/assets/js/expenses.js') }}
{{ HTML::style('resources/assets/css/sidebar-bootstrap.min.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	function pageClick(pageval) {
		$('#page').val(pageval);
		$("#exphistory").submit();
	}
	function pageLimitClick(pagelimitval) {
		$('#page').val('');
		$('#plimit').val(pagelimitval);
		$("#exphistory").submit();
	}
</script>
	<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_1">
	{{ Form::open(array('name'=>'exphistory', 'id'=>'exphistory', 'url' => 'Expenses/expenseshistory?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('hiddenplimit', $request->hiddenplimit , array('id' => 'hiddenplimit')) }}
		{{ Form::hidden('hiddenpage', $request->hiddenpage , array('id' => 'hiddenpage')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
		{{ Form::hidden('month', $request->month, array('id' => 'month')) }}
		{{ Form::hidden('year', $request->year, array('id' => 'year')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('salaryflg',$request->salaryflg, array('id' => 'salaryflg')) }}
	    {{ Form::hidden('loan_flg',$request->loan_flg, array('id' => 'loan_flg')) }}
	    {{ Form::hidden('pettyflg',$request->pettyflg, array('id' => 'pettyflg')) }}
	    {{ Form::hidden('delflg',$request->delflg, array('id' => 'delflg')) }}
	    {{ Form::hidden('subject',$request->subject, array('id' => 'subject')) }}
	    {{ Form::hidden('bname',$request->bname, array('id' => 'bname')) }}
	    {{ Form::hidden('subject_type',$request->subject_type, array('id' => 'subject_type')) }}
	    {{ Form::hidden('bankName',$request->bankName, array('id' => 'bankName')) }}
	    {{ Form::hidden('accNo',$request->accNo, array('id' => 'accNo')) }}
	    {{ Form::hidden('trans_flg',$request->trans_flg, array('id' => 'trans_flg')) }}
	    {{ Form::hidden('id','', array('id' => 'id')) }}
	    {{ Form::hidden('cashflg','', array('id' => 'cashflg')) }}
	<!-- Start Heading -->
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/expenses.png') }}">
			<h2 class="pull-left pl5 mt10">
				@if($request->pettyflg != 1)
					@if($request->subject_type == "main_subject" || $request->subject_type == "sub_subject")
						{{ trans('messages.lbl_exphistory') }}
					@else
						{{ trans('messages.lbl_cashhistory') }}
					@endif
				@else
					{{ trans('messages.lbl_pettycashhistory') }}
				@endif
			</h2>
		</div>
	</div>
	<div class="col-xs-12 pt5">
			<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
				<a href="javascript:gotoindexexpenses('{{ $request->year }}','{{ $request->month }}','{{ $request->mainmenu }}','{{ $request->hiddenplimit }}','{{ $request->hiddenpage }}');" class="btn btn-info box80">
					<span class="fa fa-arrow-left"></span>
					{{ trans('messages.lbl_back') }}
				</a>
			</div>
	</div>
	<div class="col-xs-12 mt5">
		<div class="col-xs-8" style="text-align: left;margin-left: -15px;">
			<label class="clr_blue">{{ trans('messages.lbl_mainsubject').'   :' }}</label>
			<span class="mr40" style="color:black">
				<b>
					@if($request->subject_type == "main_subject")
						@if(Session::get('languageval') == 'en')
							{{ $get_det[0]['Subject'] }}
						@else
							{{ $get_det[0]['Subject_jp'] }}
						@endif
					@elseif($request->subject_type == "sub_subject")
						@if(Session::get('languageval') == 'en')
							{{ $get_det[0]['Subject'] }} -> {{ $request->accNo }}
						@else
							{{ $get_det[0]['Subject_jp'] }} -> {{ $request->accNo }}
						@endif
					@elseif($request->subject_type == "bank_main_subject")
						{{ $request->bankName }} - {{ $request->accNo }}
					@else
						@if($request->trans_flg == 1)
							{{ $request->bankName }} - {{ $request->accNo }} -> Debit
						@else
							{{ $request->bankName }} - {{ $request->accNo }} -> Credit
						@endif
					@endif
				</b> 
			</span>
		</div>
		<div class="mt10 pull-right" title="Expenses Download">
              <a href="javascript:gotoexpensesnamehistory('{{$request->mainmenu}}');"><span class="fa fa-download mr5"></span>{{ trans('messages.lbl_expdownload')}}</a>
          </div>
	</div>
	<div class="pt43 minh200 pl15 pr15">
		<table class="tablealternate CMN_tblfixed">
			<colgroup>
				<col width="4%">
				@if($request->subject_type == "banksub")
					<col width="10%">
				@else
					<col width="8%">
				@endif
				@if($request->subject_type == "main_subject" || $request->subject_type == "bank_main_subject")
					<col width="25%">
				@endif
				@if($request->subject_type == "banksub")
					<col width="18%">
				@else
					<col width="10%">
				@endif
				<col width="">
				<col width="8%">
			</colgroup>
			<thead class="CMN_tbltheadcolor">
				<tr>
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam">{{ trans('messages.lbl_Date') }}</th>
					@if($request->subject_type == "main_subject" || $request->subject_type == "bank_main_subject")
						<th class="vam">{{ trans('messages.lbl_subsubject') }}</th>
					@endif
					<th class="vam">{{ trans('messages.lbl_amount') }}</th>
					<th class="vam">{{ trans('messages.lbl_remarks') }}</th>
					<th class="vam">{{ trans('messages.lbl_edit') }}</th>
				</tr>
			</thead>
			<tbody>
			@php $count = count($get_det); @endphp
			{{ $temp = ""}}
						{{ $temp1 = ""}}
                  {{--*/ $row = '0' /*--}}
						{{--*/ $tmpyr = 0 /*--}}
				@if(!empty($count))
					@for($j = 0; $j < $count; $j++)
						@if($j%2 != 0)
							{{--*/ $style = 'background-color:#dff1f4;' /*--}}
						@else
							{{--*/ $style = 'background-color:#FFFFFF;' /*--}}
						@endif
                    {{--*/ $loc = $get_det[$j]['year'] /*--}}
                    {{--*/ $loc1 = $get_det[$j]['month'] /*--}}
                  @if($loc != $temp || $loc1 != $temp1) 
					@if($row==1)
						{{--*/ $style_tr = 'background-color: #A7CEC9;' /*--}}
						{{--*/ $row = '0' /*--}}
					@else
						{{--*/ $style_tr = 'background-color: #FFFFFF;' /*--}}
						{{--*/ $row = '1' /*--}}
					@endif
					{{--*/ $style_td = 'border-bottom: 1 px dotted black;' /*--}}
				@else
					{{--*/ $style_td = 'border-top:hidden;' /*--}}
				@endif
						@if($j==0 )
							<tr height="25px;" style="border-bottom:1px dotted #136E83;">
								<td 
								<?php if ($request->subject_type == "main_subject"||$request->subject_type == "bank_main_subject") { ?> colspan="3" <?php } else {?> colspan="2"<?php } ?>
								style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;color:blue;text-align: right;padding-right:5px;font-weight:bold;"><?php echo "Grant Total"; ?> </td>
								<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;padding-right:5px;color:blue;font-weight:bold;"><?php echo "¥ ".number_format($amountTotal); ?> </td>
								<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;color:blue;font-weight:bold;"> </td>
								<td  style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;text-align:right;padding-right: 5px;color:blue;font-weight:bold;"></td>
							</tr>
						@endif
						<?php  if($tmpyr!=$get_det[$j]['year']||$tmpmth!=$get_det[$j]['month']) {
									$amt=0;$results=0;
									if($request->subject_type == "main_subject")
								    $displaydata=Expenses::expenses_historydetails($mnsub,$get_det[$j]['year'],$get_det[$j]['month']);
								  	else if($request->subject_type == "sub_subject") {
										$displaydata=Expenses::expenses_historydetails_subSubject($mnsub,$subsub,$get_det[$j]['year'],$get_det[$j]['month']);	
									} else if ($request->subject_type == "bank_main_subject") {
										$displaydata=Expenses::expenses_history_bankdetails($request,$request->bname,$request->accNo,$get_det[$j]['year'],$get_det[$j]['month']);	
									} else {
										$displaydata=Expenses::expenses_history_bankdetails_subSubject($request,$request->bname,$request->accNo,$request->trans_flg,$get_det[$j]['year'],$get_det[$j]['month']);	
									}
									foreach ($displaydata as $key => $value) {
										$results=$value->amount;
										$amt=$amt+$results;
									}
									?>

								<tr>
								@if($loc != $temp || $loc1 != $temp1)
									<td 
									<?php if ($request->subject_type == "main_subject"||$request->subject_type == "bank_main_subject") { ?> colspan="3" <?php } else {?> colspan="2"<?php } ?>
									style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;font-weight:bold;{{ $style_td }}"><?php echo $get_det[$j]['year']."年".$get_det[$j]['month']."月"; ?> </td>
									<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;padding-right:5px;font-weight:bold;{{ $style_td }}"><?php echo number_format($amt); ?> </td>
									<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;{{ $style_td }}"> </td>
									<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;{{ $style_td }}"> </td>
								</tr>
								@endif
								<?php 	} ?>
								<tr height="22px" style="<?php echo $style;?>">
									<td class="tac">
										{{ ($view->currentpage()-1) * $view->perpage() + $j + 1 }}
									</td>
									<td class="tac">
										{{ $get_det[$j]['date'] }}
									</td>
									@if($request->subject_type == "main_subject" || $request->subject_type == "bank_main_subject")
										<td>
											@if($request->subject_type == "main_subject")
												@if(Session::get('languageval') == 'en')
													{{ $get_det[0]['sub_eng'] }}
												@else
													{{ $get_det[0]['sub_jap'] }}
												@endif
											@endif
											@if($request->subject_type == "bank_main_subject")
												@if($get_det[$j]['transaction_flg'] == 1)
													Debit
												@else
													Credit
												@endif
											@endif
										</td>
									@endif
									<td class="tar pl5">
										{{ number_format($get_det[$j]['amount']) }}
									</td>
									<td>
										{!! nl2br(e($get_det[$j]['remarks'])) !!}
									</td>
									<td class="tac">
										<a style="text-decoration: none;" style="color: blue;"  href="javascript:copyCashRecordhistory('{{ $get_det[$j]['id'] }}','{{ $request->mainmenu }}')" title="Copy">
												<img class="vam" src="{{ URL::asset('resources/assets/images/copy.png') }}" width="20" height="20"></a>
									</td>
								</tr>
								 {{--*/ $temp = $loc /*--}}
								 {{--*/ $temp1 = $loc1 /*--}}
					@endfor
				@else
					<tr>
						@if($request->subject_type == "main_subject" || $request->subject_type == "bank_main_subject")
							<td class="text-center colred" colspan="6">
								{{ trans('messages.lbl_nodatafound') }}
							</td>
						@else
							<td class="text-center colred" colspan="5">
								{{ trans('messages.lbl_nodatafound') }}
							</td>
						@endif
					</tr>
				@endif
			</tbody>
		</table>
	</div>
	@if(!empty($view))
	<div class="text-center pl13">
		@if(!empty($view->total()))
			<span class="pull-left mt24">
				{{ $view->firstItem() }} ~ {{ $view->lastItem() }} / {{ $view->total() }}
			</span>
		@endif 
		{{ $view->links() }}
		<div class="CMN_display_block flr mr14">
			{{ $view->linkspagelimit() }}
		</div>
	</div>
	@endif
	</article>
	{{ Form::close() }}
	{{ Form::open(array('name'=>'frmdownloadindex12', 
						'id'=>'frmdownloadindex12', 
						'url' => 'Expenses/expensessubhistorydownload?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
		{{ Form::hidden('month', $request->month, array('id' => 'month')) }}
		{{ Form::hidden('year', $request->year, array('id' => 'year')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('salaryflg',$request->salaryflg, array('id' => 'salaryflg')) }}
	    {{ Form::hidden('loan_flg',$request->loan_flg, array('id' => 'loan_flg')) }}
	    {{ Form::hidden('pettyflg',$request->pettyflg, array('id' => 'pettyflg')) }}
	    {{ Form::hidden('delflg',$request->delflg, array('id' => 'delflg')) }}
	    {{ Form::hidden('subject',$request->subject, array('id' => 'subject')) }}
	    {{ Form::hidden('bname',$request->bname, array('id' => 'bname')) }}
	    {{ Form::hidden('subject_type',$request->subject_type, array('id' => 'subject_type')) }}
	    {{ Form::hidden('bankName',$request->bankName, array('id' => 'bankName')) }}
	    {{ Form::hidden('accNo',$request->accNo, array('id' => 'accNo')) }}
	    {{ Form::hidden('trans_flg',$request->trans_flg, array('id' => 'trans_flg')) }}
	{{ Form::close() }}
	{{ Form::open(array('name'=>'frmdownloadindex123', 
						'id'=>'frmdownloadindex123', 
						'url' => 'Expenses/expensesmainhistorydownload?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
		{{ Form::hidden('month', $request->month, array('id' => 'month')) }}
		{{ Form::hidden('year', $request->year, array('id' => 'year')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('salaryflg',$request->salaryflg, array('id' => 'salaryflg')) }}
	    {{ Form::hidden('loan_flg',$request->loan_flg, array('id' => 'loan_flg')) }}
	    {{ Form::hidden('pettyflg',$request->pettyflg, array('id' => 'pettyflg')) }}
	    {{ Form::hidden('delflg',$request->delflg, array('id' => 'delflg')) }}
	    {{ Form::hidden('subject',$request->subject, array('id' => 'subject')) }}
	    {{ Form::hidden('bname',$request->bname, array('id' => 'bname')) }}
	    {{ Form::hidden('subject_type',$request->subject_type, array('id' => 'subject_type')) }}
	    {{ Form::hidden('bankName',$request->bankName, array('id' => 'bankName')) }}
	    {{ Form::hidden('accNo',$request->accNo, array('id' => 'accNo')) }}
	    {{ Form::hidden('trans_flg',$request->trans_flg, array('id' => 'trans_flg')) }}
	{{ Form::close() }}
	</div>
@endsection