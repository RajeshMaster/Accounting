@extends('layouts.app')
@section('content')
<?php use App\Model\Transfer; ?>
{{ HTML::style('resources/assets/css/common.css') }}
{{ HTML::style('resources/assets/css/widthbox.css') }}
{{ HTML::script('resources/assets/css/bootstrap.min.css') }}
{{ HTML::script('resources/assets/js/transfer.js') }}
{{ HTML::script('resources/assets/js/expenses.js') }}
{{ HTML::style('resources/assets/css/sidebar-bootstrap.min.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	function pageClick(pageval) {
		$('#page').val(pageval);
		$("#subhistory").submit();
	}
	function pageLimitClick(pagelimitval) {
		$('#page').val('');
		$('#plimit').val(pagelimitval);
		$("#subhistory").submit();
	}
</script>
	<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	@if($request->mainmenu == "expenses")
		<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_1">
	@elseif($request->mainmenu == "company_transfer")
		<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_2">
	@else
		<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_1">
	@endif
	{{ Form::open(array('name'=>'subhistory', 'id'=>'subhistory', 'url' => 'Transfer/transfersubhistory?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('hiddenplimit', $request->hiddenplimit , array('id' => 'hiddenplimit')) }}
		{{ Form::hidden('hiddenpage', $request->hiddenpage , array('id' => 'hiddenpage')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('month', $request->month, array('id' => 'month')) }}
		{{ Form::hidden('year', $request->year, array('id' => 'year')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('salaryflg',$request->salaryflg, array('id' => 'salaryflg')) }}
	    {{ Form::hidden('loan_flg',$request->loan_flg, array('id' => 'loan_flg')) }}
	    {{ Form::hidden('pettyflg',$request->pettyflg, array('id' => 'pettyflg')) }}
	    {{ Form::hidden('delflg',$request->delflg, array('id' => 'delflg')) }}
	    {{ Form::hidden('subject',$request->subject, array('id' => 'subject')) }}
	    {{ Form::hidden('bankName',$request->bankName, array('id' => 'bankName')) }}
	    {{ Form::hidden('backflg',$request->backflg, array('id' => 'backflg')) }}
	    {{ Form::hidden('expdetails',$request->expdetails, array('id' => 'expdetails')) }}
	    {{ Form::hidden('accNo',$request->accNo, array('id' => 'accNo')) }}
	    {{ Form::hidden('active_select', $request->active_select, array('id' => 'active_select')) }}
	    {{ Form::hidden('filter', $request->filter, array('id' => 'filter')) }}
	    {{ Form::hidden('id','', array('id' => 'id')) }}
	    {{ Form::hidden('editflg','', array('id' => 'editflg')) }}
	    {{ Form::hidden('subcat',$request->subcat, array('id' => 'subcat')) }}
	<!-- Start Heading -->
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/expenses.png') }}">
			<h2 class="pull-left pl5 mt10">
				{{ trans('messages.lbl_exptransfersubhistory') }}
			</h2>
		</div>
	</div>
	<div class="col-xs-12 pt5">
			<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
				<a href="javascript:gotoindexexpenses1('{{ $request->year }}','{{ $request->month }}','{{ $request->mainmenu }}','{{ $request->hiddenplimit }}','{{ $request->hiddenpage }}');" class="btn btn-info box80">
					<span class="fa fa-arrow-left"></span>
					{{ trans('messages.lbl_back') }}
				</a>
			</div>
	</div>
	<div class="col-xs-12 mt5">
		<div class="col-xs-9" style="text-align: left;margin-left: -15px;">
			<label class="clr_blue">{{ trans('messages.lbl_mainsubject').'   :' }}</label>
			<span class="mr40" style="color:black">
				<b>
					@if(count($get_det) == "")
					@else
						@if(Session::get('languageval') == 'en')
							{{ $get_det[0]['Subject'] }} -> {{ $get_det[0]['sub_eng'] }}
						@else
							{{ $get_det[0]['Subject_jp'] }} -> {{ $get_det[0]['sub_jap'] }}
						@endif
					@endif
				</b> 
			</span>
		</div>
		<div class="mt10 pull-right" title="Expenses Download">
              <a href="javascript:gototransfersubhistory5('{{$request->mainmenu}}');"><span class="fa fa-download mr5"></span>{{ trans('messages.lbl_expdownload')}}</a>
          </div>
	</div>
	<div class="pt43 minh200 pl15 pr15">
		<table class="tablealternate CMN_tblfixed">
			<colgroup>
				<col width="4%">
				<col width="8%">
				<col width="18%">
				<col width="14%">
				<col width="14%">
				<col width="8%">
				<col>
				<col width="8%">
			</colgroup>
			<thead class="CMN_tbltheadcolor">
				<tr>
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam">{{ trans('messages.lbl_Date') }}</th>
					<th class="vam">{{ trans('messages.lbl_bank') }}</th>
					<th class="vam">{{ trans('messages.lbl_amount') }}</th>
					<th class="vam">{{ trans('messages.lbl_charge') }}</th>
					<th class="vam">{{ trans('messages.lbl_bill') }}</th>
					<th class="vam">{{ trans('messages.lbl_remarks') }}</th>
					<th class="vam">{{ trans('messages.lbl_edit') }}</th>
				</tr>
			</thead>
			<tbody>
				@php $count = count($get_det); @endphp
				{{--*/ $temp = "" /*--}}
				{{ $tmpyr  = "" }}
				{{--*/ $rowbktrclrr = 0 /*--}}
				{{--*/ $amt = 0 /*--}}
				{{--*/ $row1 = 0 /*--}}
				{{--*/ $row3 = 0 /*--}}
				{{--*/ $row4 = 0 /*--}}
				{{--*/ $fee = 0 /*--}}
				{{--*/ $style_td4 = "" /*--}}
				{{--*/ $temp1 = "" /*--}}
				{{--*/ $temp2 = "" /*--}}
				{{--*/ $temp3 = "" /*--}}
				{{--*/ $temp4 = "" /*--}}
				{{--*/ $temp5 = "" /*--}}
				{{--*/ $temp6 = "" /*--}}
				@if(!empty($count))
					@for($j = 0; $j <$count; $j++)
						@if($j == 0)
						 	<tr height="25px;">
								<td  colspan="3" style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;text-align:right;padding-right: 5px;color:blue;font-weight:bold;"><?php echo "Grant Total"; ?> </td>
								<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;padding-right:5px;border-bottom:1px dotted #136E83;padding-right:5px;color:blue;font-weight:bold;">
								<?php echo "¥ ".number_format($amountTotal); ?> 
								</td>
								<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;padding-right:5px;border-bottom:1px dotted #136E83;padding-right:5px;color:blue;font-weight:bold;">
								 <?php echo "¥ ".number_format($chargeTotal); ?>
								 </td>
								<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;padding-right:5px;color:blue;"> </td>
								<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-bottom:1px dotted #136E83;padding-right:5px;color:blue;"> </td>
								<td  style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;text-align:right;padding-right: 5px;color:blue;font-weight:bold;"></td>
							</tr>
						@endif
						{{--*/ $tempdate = $get_det[$j]['date'] /*--}}
								{{--*/ $loc1 = $get_det[$j]['year'] /*--}}
								{{--*/ $loc2 = $get_det[$j]['month'] /*--}}
								{{--*/ $loc3 = $get_det[$j]['date'] /*--}}
									@if($loc1 != $temp1 && $loc2 != $temp2) 
										@if($row==1)
											{{--*/ $style_tr = 'background-color:#dff1f4;' /*--}}
											{{--*/ $row = '0' /*--}}
										@else
											{{--*/ $style_tr = 'background-color: #FFFFFF;' /*--}}
											{{--*/ $row = '1' /*--}}
										@endif
										{{--*/ $style_td = 'border-bottom: hidden;' /*--}}
									@else
										{{--*/ $style_td = 'border-top:hidden;' /*--}}
									@endif
									{{--*/ $loc3 = $get_det[$j]['date'] /*--}}
								@if($loc3 != $temp3) 
									@if($row1==1)
										{{--*/ $style_tr1 = 'background-color:#dff1f4;' /*--}}
										{{--*/ $row1 = '0' /*--}}
									@else
										{{--*/ $style_tr1 = 'background-color: #FFFFFF;' /*--}}
										{{--*/ $row1= '1' /*--}}
									@endif
									{{--*/ $style_td1 = 'border-bottom: 1 px dotted black;' /*--}}
								@else
									{{--*/ $style_td1 = 'border-top:hidden;' /*--}}
								@endif
								@if(isset($get_det[$j]['bankname']))
								{{--*/ $loc4 = $get_det[$j]['bankname'] /*--}}
			                  	@if($loc4 != $temp4) 
									@if($row3==1)
										{{--*/ $style_tr = 'background-color: #A7CEC9;' /*--}}
										{{--*/ $row3 = '0' /*--}}
									@else
										{{--*/ $style_tr = 'background-color: #FFFFFF;' /*--}}
										{{--*/ $row3 = '1' /*--}}
									@endif
									{{--*/ $style_td3 = 'border-bottom: 1px dotted black;' /*--}}
								@else
									{{--*/ $style_td3 = 'border-top:hidden;' /*--}}
								@endif
								@endif
								@if(isset($get_det[$j]['nickname']))
								{{--*/ $loc5 = $get_det[$j]['nickname'] /*--}}
								{{--*/ $loc6 = $get_det[$j]['bankaccno'] /*--}}
			                  	@if($loc5 != $temp5 && $loc6 != $temp6) 
									@if($row4==1)
										{{--*/ $style_tr = 'background-color: #A7CEC9;' /*--}}
										{{--*/ $row4 = '0' /*--}}
									@else
										{{--*/ $style_tr = 'background-color: #FFFFFF;' /*--}}
										{{--*/ $row4 = '1' /*--}}
									@endif
									{{--*/ $style_td4 = 'border-bottom: 1px dotted black;' /*--}}
								@else
									{{--*/ $style_td4 = 'border-top:hidden;' /*--}}
								@endif
								@endif
							<?php
								if ($tempdate !=$get_det[$j]['id']) {
									if($rowbktrclrr==1){
										$styletd='background-color:#dff1f4;';
										$rowbktrclrr=0;
									} else {
										$styletd='#FFFFFF';
										$rowbktrclrr=1;
									}
								} 
							 ?>
						<?php if($tmpyr!=$get_det[$j]['year']||$tmpmth!=$get_det[$j]['month']){
						$res =  Transfer::transfer_subhistorydetailsamount1($request,$request->subject,$get_det[$j]['year'],$get_det[$j]['month']); ?>
						<?php $results = array();
								$amt = 0;
								$fee = 0;
								$result1 = array(); ?>
							@foreach($res as $key => $value)
								<?php $results=$value->amount;
								$result1=$value->fee;
								$amt=$amt+$results;
								$fee=$fee+$result1; ?>
							@endforeach
							<tr height="25px;" style="{{ $style_td }}">
								<td  colspan="3" style="background-color:lightgrey;color:black;vertical-align:middle;border-bottom:1px dotted #136E83;border-right:1px dotted #136E83;font-weight:bold;font-weight:bold;"><?php echo $get_det[$j]['year']."年".$get_det[$j]['month']."月"; ?> </td>
								<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-bottom:1px dotted #136E83;border-right:1px dotted #136E83;padding-right:5px;font-weight:bold;">
								<?php echo number_format($amt); ?> 
								</td>
								<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-bottom:1px dotted #136E83;border-right:1px dotted #136E83;padding-right:5px;font-weight:bold;">
								 <?php echo number_format($fee); ?>
								 </td>
								<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-bottom:1px dotted #136E83;border-right:1px dotted #136E83;"> </td>
								<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-bottom:1px dotted #136E83;"> </td>
								<td  style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;text-align:right;padding-right: 5px;color:blue;"></td>
							</tr>
							<?php $tmpyr=0; } $tmpyr=$get_det[$j]['year'];$tmpmth=$get_det[$j]['month']; ?>
							<tr style="{{ $style_tr1 }}">
								<td align="center">
									{{ ($index->currentpage()-1) * $index->perpage() + $j + 1 }}
								</td>
								<td align="center" style="{{ $style_td1 }}">
									@if($loc3 != $temp3)
										{{ $get_det[$j]['date'] }}
									@endif
								</td>
									@if($get_det[$j]['bankname'] == "Cash")
										<td style="{{ $style_td3 }}">
											@if($loc4 != $temp4)
												Cash
											@endif
										</td>
									@else
										<td style="{{ $style_td4 }}">
											@if(isset($get_det[$j]['nickname']))
												@if($loc5 != $temp5 && $loc6 != $temp6)
													{{ $get_det[$j]['nickname'] }} - {{ $get_det[$j]['bankaccno'] }}
												@endif
											@else
												- {{ $get_det[$j]['bankaccno']  }}
											@endif
										</td>
									@endif
								<td class="tar">
									{{ number_format($get_det[$j]['amount']) }}
								</td>
								<td class="tar">
									@if($get_det[$j]['bankname']=="Cash")
									@else
									 {{ number_format($get_det[$j]['fee']) }}
									@endif
								</td>
								<td class="tac">
									@if($get_det[$j]['file_dtl']!="")
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
								<td>
									{!! nl2br(e($get_det[$j]['remarks'])) !!}
								</td>
								<td class="tac">
									@if($get_det[$j]['bankname'] != "Cash")
										<a style="text-decoration: none;" style="color: blue;"  href="javascript:CopybkrsRecordhistory1('{{ $get_det[$j]['id'] }}','{{ $request->mainmenu }}')" title="Copy">
												<img class="vam" src="{{ URL::asset('resources/assets/images/copy.png') }}" width="20" height="20">
										</a>
									@else
										<a style="text-decoration: none;" style="color: blue;"  href="javascript:CopyRecordforexpensessub('{{ $get_det[$j]['id'] }}','{{ $request->mainmenu }}','','')" title="Copy">
												<img class="vam" src="{{ URL::asset('resources/assets/images/copy.png') }}" width="20" height="20">
										</a>
									@endif
								</td>
							</tr>
							{{--*/ $temp1 = $loc1 /*--}}
							{{--*/ $temp2 = $loc2 /*--}}
							{{--*/ $temp3 = $loc3 /*--}}
							{{--*/ $temp4 = $loc4 /*--}}
							@if(isset($get_det[$j]['nickname']))
								{{--*/ $temp5 = $loc5 /*--}}
								{{--*/ $temp6 = $loc6 /*--}}
							@endif
					@endfor
				@else
					<tr>
						<td class="text-center colred" colspan="8">
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
	</article>
	{{ Form::close() }}
	{{ Form::open(array('name'=>'subhistorydownload', 'id'=>'subhistorydownload', 'url' => 'Transfer/transfersubhistorydownload?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('month', $request->month, array('id' => 'month')) }}
		{{ Form::hidden('year', $request->year, array('id' => 'year')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('salaryflg',$request->salaryflg, array('id' => 'salaryflg')) }}
	    {{ Form::hidden('loan_flg',$request->loan_flg, array('id' => 'loan_flg')) }}
	    {{ Form::hidden('pettyflg',$request->pettyflg, array('id' => 'pettyflg')) }}
	    {{ Form::hidden('delflg',$request->delflg, array('id' => 'delflg')) }}
	    {{ Form::hidden('subject',$request->subject, array('id' => 'subject')) }}
	    {{ Form::hidden('bankName',$request->bankName, array('id' => 'bankName')) }}
	    {{ Form::hidden('backflg',$request->backflg, array('id' => 'backflg')) }}
	    {{ Form::hidden('expdetails',$request->expdetails, array('id' => 'expdetails')) }}
	    {{ Form::hidden('accNo',$request->accNo, array('id' => 'accNo')) }}
	    {{ Form::hidden('active_select', $request->active_select, array('id' => 'active_select')) }}
	    {{ Form::hidden('filter', $request->filter, array('id' => 'filter')) }}
	{{ Form::close() }}
	</div>
@endsection
