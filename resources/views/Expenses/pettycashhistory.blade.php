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
		$("#pettyhistory").submit();
	}
	function pageLimitClick(pagelimitval) {
		$('#page').val('');
		$('#plimit').val(pagelimitval);
		$("#pettyhistory").submit();
	}
</script>
	<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_8">
	{{ Form::open(array('name'=>'pettyhistory', 'id'=>'pettyhistory', 'url' => 'Expenses/pettycashhistory?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('hiddenplimit', $request->hiddenplimit , array('id' => 'hiddenplimit')) }}
		{{ Form::hidden('hiddenpage', $request->hiddenpage , array('id' => 'hiddenpage')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('month', $request->month, array('id' => 'month')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('year', $request->year, array('id' => 'year')) }}
		{{ Form::hidden('salaryflg',$request->salaryflg, array('id' => 'salaryflg')) }}
	    {{ Form::hidden('loan_flg',$request->loan_flg, array('id' => 'loan_flg')) }}
	    {{ Form::hidden('pettyflg',$request->pettyflg, array('id' => 'pettyflg')) }}
	    {{ Form::hidden('delflg',$request->delflg, array('id' => 'delflg')) }}
	    {{ Form::hidden('subject',$request->subject, array('id' => 'subject')) }}
	    {{ Form::hidden('subject_type',$request->subject_type, array('id' => 'subject_type')) }}
	    {{ Form::hidden('bankName',$request->bankName, array('id' => 'bankName')) }}
	    {{ Form::hidden('accNo',$request->accNo, array('id' => 'accNo')) }}
	    {{ Form::hidden('detail',$request->detail, array('id' => 'detail')) }}
	    {{ Form::hidden('trans_flg',$request->trans_flg, array('id' => 'trans_flg')) }}
	    {{ Form::hidden('bname',$request->bname, array('id' => 'bname')) }}
	    {{ Form::hidden('cashflg','', array('id' => 'cashflg')) }}
	    {{ Form::hidden('id','', array('id' => 'id')) }}
	<!-- Start Heading -->
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/pettycash.jpg') }}">
			<h2 class="pull-left pl5 mt10">
				@if($request->subject_type == "main_subject" || $request->subject_type == "sub_subject")
					{{ trans('messages.lbl_pettycashhistory') }}
				@else
					{{ trans('messages.lbl_cashhistory') }}
				@endif
			</h2>
		</div>
	</div>
	<div class="col-xs-12 pt5">
			<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
				<a href="javascript:gotoindexpettycash('{{ $request->year }}','{{ $request->month }}','{{ $request->mainmenu }}','{{ $request->hiddenplimit }}','{{ $request->hiddenpage }}');" class="btn btn-info box80">
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
						@if($request->exptype1 == 2)
							{{ $request->bname }}
						@else
							@if(Session::get('languageval') == 'en')
								{{ $get_det[0]['main_eng'] }}
							@else
								{{ $get_det[0]['main_jap'] }}
							@endif
						@endif
					@elseif($request->subject_type == "sub_subject")
						@if($request->exptype1 == 2)
							{{ $request->bname }} -> @if(Session::get('languageval') == 'en')
								{{ $get_det[0]['sub_eng'] }}
							@else
								{{ $get_det[0]['sub_jap'] }}
							@endif
						@else
							@if(Session::get('languageval') == 'en')
								{{ $get_det[0]['main_eng'] }}
							@else
								{{ $get_det[0]['main_jap'] }}
							@endif
						@endif
					@elseif($request->subject_type == "bank_main_subject")
						{{ $request->detail }} - {{ $request->accNo }}
					@else
						@if($request->trans_flg == 1)
							{{ $request->bname }} - {{ $request->accNo }} -> Debit
						@else
							{{ $request->bname }} - {{ $request->accNo }} -> Credit
						@endif
					@endif
				</b> 
			</span>
		</div>
		<div class="mt10 pull-right" title="Expenses Download">
			@if($request->subject_type == "main_subject" || $request->subject_type == "bank_main_subject")
	          <a href="javascript:gotopettycashhistorydownload('{{$request->mainmenu}}');"><span class="fa fa-download mr5"></span>{{ trans('messages.lbl_expdownload')}}</a>
	        @else
	        	<a href="javascript:gotopettycashhistorydownload1('{{$request->mainmenu}}');"><span class="fa fa-download mr5"></span>{{ trans('messages.lbl_expdownload')}}</a>
	        @endif
	    </div>
	</div>
	<div class="pt43 minh200 pl15 pr15">
		<table class="tablealternate CMN_tblfixed">
			<colgroup>
				<col width="4%">
				@if($request->exptype1 == 2)
					<col width="20%">
				@else
					<col width="8%">
				@endif
				@if($request->subject_type == "main_subject" || $request->subject_type == "bank_main_subject")
					<col width="25%">
				@endif
				@if($request->exptype1 == 2)
					<col width="20%">
				@else
					<col width="12%">
				@endif
				<col width="6%">
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
					<th class="vam">{{ trans('messages.lbl_bill') }}</th>
					<th class="vam">{{ trans('messages.lbl_remarks') }}</th>
					<th class="vam">{{ trans('messages.lbl_edit') }}</th>
				</tr>
			</thead>
			<tbody>
			{{ $temp = "" }}
			{{--*/ $row = '0' /*--}}
			@php $count = count($get_det); @endphp
				@if(!empty($count))
					<?php $tmpyr = 0;
			for ($j = 0; $j <count($get_det); $j++) {
				if ($j%2 != 0) {
					$style = "background-color:#dff1f4;";
				} else {
					$style = "background-color:#FFFFFF;";
				} 
		?>
		<?php  if ( $j==0 ) {?>
			<tr class="tableheader" height="25px;">
				<td 
				<?php if ($request->subject_type == "main_subject"||$request->subject_type == "bank_main_subject") { ?> colspan="3" <?php } else {?> colspan="2"<?php } ?>
				style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;color:blue;text-align: right;padding-right:5px;"><b><?php echo "Grant Total"; ?></b> </td>
				<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;padding-right:5px;border-bottom:1px dotted #136E83;color:blue;"><b><?php echo "¥ "." ".number_format($amountTotal); ?></b> </td>
				<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-bottom:1px dotted #136E83;color:blue;"> </td>
				<td  style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;text-align:right;padding-right: 5px;color:blue;"></td>
				<td  style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;text-align:right;padding-right: 5px;color:blue;"></td>
			</tr>
		<?php }?>
			<?php  if($tmpyr!=$get_det[$j]['year']||$tmpmth!=$get_det[$j]['month']) {
				$amt=0;$results=0;
				if($request->subject_type == "main_subject") {
			    	$viewdata=Expenses::expenses_historydetails($request,$request->subject,$get_det[$j]['year'],$get_det[$j]['month']);
			  	} else if($request->subject_type == "sub_subject") {
					$viewdata=Expenses::expenses_historydetails_subSubject1($request->detail,$get_det[$j]['year'],$get_det[$j]['month']);	
				} else if ($request->subject_type == "bank_main_subject") {
					$viewdata=Expenses::expenses_history_bankdetailspageview($request,$get_det[$j]['bankname'],$get_det[$j]['bankaccno'],$get_det[$j]['year'],$get_det[$j]['month']);	
				} else {
					$viewdata=Expenses::expenses_history_bankdetails_subSubjectpetty($request,$get_det[$j]['bankname'],$get_det[$j]['bankaccno'],$get_det[$j]['transaction_flg'],$get_det[$j]['year'],$get_det[$j]['month']);	
				}
				foreach ($viewdata as $key => $value) {
					 $results=$value->amount;
					 $amt=$amt+$results;
				}
				?>
			<tr class="tableheader" height="25px;">
				<td 
				<?php if ($request->subject_type == "main_subject"||$request->subject_type == "bank_main_subject") { ?> colspan="3" <?php } else {?> colspan="2"<?php } ?>
				style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;"><b><?php echo $get_det[$j]['year']."年".$get_det[$j]['month']."月"; ?></b> </td>
				<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;padding-right:5px;font-weight: bold;"><?php echo number_format($amt); ?> </td>
				<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;"> </td>
				<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;"> </td>
				<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;"> </td>
			</tr>
			<?php $tmpyr=0;	}	$tmpyr=$get_det[$j]['year'];$tmpmth=$get_det[$j]['month']; ?>	
			{{--*/ $loc = $get_det[$j]['date'] /*--}}
                  	@if($loc != $temp) 
						@if($row==1)
							{{--*/ $style_tr = 'background-color: #A7CEC9;' /*--}}
							{{--*/ $row = '0' /*--}}
						@else
							{{--*/ $style_tr = 'background-color: #FFFFFF;' /*--}}
							{{--*/ $row = '1' /*--}}
						@endif
						{{--*/ $style_td = 'border-bottom: 1px dotted black;' /*--}}
					@else
						{{--*/ $style_td = 'border-top:hidden;' /*--}}
					@endif
		<tr height="22px" style="<?php echo $style;?>">
			<td align="center" class="tdcontenthistory">
				{{ ($view->currentpage()-1) * $view->perpage() + $j + 1 }}
			</td>
			<td align="center" style="{{ $style_td }}" class="tdcontenthistory">
				@if($loc != $temp)
					{{ $get_det[$j]['date'] }}
				@endif
			</td>
			<?php if ($request->subject_type == "main_subject"||$request->subject_type == "bank_main_subject") { ?>
			<td align="left" class="tdcontenthistory" style="padding-left:5px;">
			<?php 
				if ($request->subject_type == "main_subject") {
					if (Session::get('languageval') == 'en') {
						echo $get_det[$j]['sub_eng'];
					} else {
						echo $get_det[$j]['sub_jap'];
					}
				} 
				if ($request->subject_type == "bank_main_subject") {
					if ($get_det[$j]['transaction_flg'] == 1) {
						echo "Debit";
					} else {
						echo "Credit";
					}
				}
				 ?>
			</td>
			<?php } ?>
			<td align="right" style="padding-right:5px;" class="tdcontenthistory">
				{{ number_format($get_det[$j]['amount']) }}
			</td>
			<td align="center">
				@if(isset($get_det[$j]['file_dtl']))
					<?php
						$file_url = '../InvoiceUpload/Expenses/' . $get_det[$j]['file_dtl'];
					 ?>
						@if($get_det[$j]['file_dtl'] != "" && file_exists($file_url))
							<a class="tac" href="javascript:download('{{ $get_det[$j]['file_dtl'] }}','../../../../InvoiceUpload/Expenses');" title="Download">
								<i class="fa fa-download" aria-hidden="true"></i>
							</a>
						@endif
				@else
				@endif
			</td>
			<td align="left" style="padding-left:5px;" class="tdcontenthistory">
				{!! nl2br(e($get_det[$j]['remarks'])) !!}
			</td>
			<td align="center">
				@if($request->delflg != "2")
					<a style="color: blue;"  href="javascript:CopyRecord('{{ $get_det[$j]['pettyid'] }}','{{ $request->mainmenu }}')" title="Copy">
						<img class="vam" src="{{ URL::asset('resources/assets/images/copy.png') }}" width="20" height="20"></a>
				@else
					<a style="color: blue;"  href="javascript:CopyBankRecord('{{ $get_det[$j]['id'] }}','{{ $request->mainmenu }}')" title="Copy">
						<img class="vam" src="{{ URL::asset('resources/assets/images/copy.png') }}" width="20" height="20"></a>
				@endif
			</td>
			{{--*/ $temp = $loc /*--}}
		</tr>
	<?php  } ?>
				@else
					<tr>
						<td class="text-center colred" colspan="7">
							{{ trans('messages.lbl_nodatafound') }}
						</td>
					</tr>
				@endif
			</tbody>
		</table>
	</div>
	<div class="text-center pl13">
		@if(!empty($view->total()))
			<span class="pull-left mt24">
				{{ $view->firstItem() }} ~ {{ $view->lastItem() }} / {{ $view->total() }}
			</span>
		@endif 
		{{ $view->links() }}
		<div class="CMN_display_block flr mr18">
			{{ $view->linkspagelimit() }}
		</div>
	</div>
	</article>
	{{ Form::close() }}
	@if($request->subject_type == "main_subject" || $request->subject_type == "bank_main_subject")
		{{ Form::open(array('name'=>'frmdownloadindex', 
						'id'=>'frmdownloadindex', 
						'url' => 'Expenses/pettycashmainhistory?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('month', $request->month, array('id' => 'month')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('year', $request->year, array('id' => 'year')) }}
		{{ Form::hidden('salaryflg',$request->salaryflg, array('id' => 'salaryflg')) }}
	    {{ Form::hidden('loan_flg',$request->loan_flg, array('id' => 'loan_flg')) }}
	    {{ Form::hidden('pettyflg',$request->pettyflg, array('id' => 'pettyflg')) }}
	    {{ Form::hidden('delflg',$request->delflg, array('id' => 'delflg')) }}
	    {{ Form::hidden('subject',$request->subject, array('id' => 'subject')) }}
	    {{ Form::hidden('subject_type',$request->subject_type, array('id' => 'subject_type')) }}
	    {{ Form::hidden('bankName',$request->bankName, array('id' => 'bankName')) }}
	    {{ Form::hidden('accNo',$request->accNo, array('id' => 'accNo')) }}
	    {{ Form::hidden('detail',$request->detail, array('id' => 'detail')) }}
	    {{ Form::hidden('trans_flg',$request->trans_flg, array('id' => 'trans_flg')) }}
	    {{ Form::hidden('bname',$request->bname, array('id' => 'bname')) }}
	{{ Form::close() }}
	@else
	    {{ Form::open(array('name'=>'frmdownloadindexes', 
						'id'=>'frmdownloadindexes', 
						'url' => 'Expenses/pettycashsubhistorydownload?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('month', $request->month, array('id' => 'month')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('year', $request->year, array('id' => 'year')) }}
		{{ Form::hidden('salaryflg',$request->salaryflg, array('id' => 'salaryflg')) }}
	    {{ Form::hidden('loan_flg',$request->loan_flg, array('id' => 'loan_flg')) }}
	    {{ Form::hidden('pettyflg',$request->pettyflg, array('id' => 'pettyflg')) }}
	    {{ Form::hidden('delflg',$request->delflg, array('id' => 'delflg')) }}
	    {{ Form::hidden('subject',$request->subject, array('id' => 'subject')) }}
	    {{ Form::hidden('subject_type',$request->subject_type, array('id' => 'subject_type')) }}
	    {{ Form::hidden('bankName',$request->bankName, array('id' => 'bankName')) }}
	    {{ Form::hidden('accNo',$request->accNo, array('id' => 'accNo')) }}
	    {{ Form::hidden('detail',$request->detail, array('id' => 'detail')) }}
	    {{ Form::hidden('trans_flg',$request->trans_flg, array('id' => 'trans_flg')) }}
	    {{ Form::hidden('bname',$request->bname, array('id' => 'bname')) }}
	{{ Form::close() }}
	        @endif
	</div>
@endsection
