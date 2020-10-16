@extends('layouts.app')
@section('content')
{{ HTML::style('resources/assets/css/common.css') }}
{{ HTML::style('resources/assets/css/widthbox.css') }}
{{ HTML::script('resources/assets/css/bootstrap.min.css') }}
{{ HTML::script('resources/assets/js/loan.js') }}
{{ HTML::style('resources/assets/css/sidebar-bootstrap.min.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
</script>
	<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_4">
	{{ Form::open(array('name'=>'loanview', 'id'=>'loanview', 'url' => 'Loandetails/Viewlist?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('id', $request->id , array('id' => 'id')) }}
		{{ Form::hidden('head', $request->head , array('id' => 'head')) }}
	<!-- Start Heading -->
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/loan.jpg') }}">
			<h2 class="pull-left pl5 mt10">
					{{ $request->head }}
			</h2>
		</div>
	</div>
	<div class="col-xs-12 pt5">
			<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
				<a href="javascript:gobacktoindex('{{ $request->mainmenu }}');" class="btn btn-info box80">
					<span class="fa fa-arrow-left"></span>
					{{ trans('messages.lbl_back') }}
				</a>
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
				<col width="11%">
				<col width="11%">
				<col width="18%">
			</colgroup>
			<thead class="CMN_tbltheadcolor">
				<tr>
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam">{{ trans('messages.lbl_Date') }}</th>
					<th class="vam">{{ trans('messages.lbl_content') }}</th>
					<th class="vam">{{ trans('messages.lbl_monleft') }}</th>
					<th class="vam">{{ trans('messages.lbl_amount') }}</th>
					<th class="vam">{{ trans('messages.lbl_interest') }}</th>
					<th class="vam">{{ trans('messages.lbl_balance') }}</th>
					<th class="vam">{{ trans('messages.lbl_remarks') }}</th>
				</tr>
					@if(!empty($loanview))
							<tr height="25px;">
								<td  colspan="4" style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;text-align:right;padding-right: 5px;color:blue;font-weight:bold;">
									<?php echo "Grant Total"; ?> 
								</td>
								<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;padding-right:5px;color:blue;font-weight:bold;">
									¥ {{ number_format($amountval[0]->amount) }} 
								</td>
								<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-right:1px dotted #136E83;border-bottom:1px dotted #136E83;padding-right:5px;color:blue;font-weight:bold;">
									 ¥ {{ number_format($amountval[0]->fee) }}
								</td>
								<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-bottom:1px dotted #136E83;font-weight:bold;"> </td>
								<td align="right" style="background-color:lightgrey;color:black;vertical-align:middle;border-bottom:1px dotted #136E83;font-weight:bold;"> </td>
							</tr>
					@endif
			</thead>
			<tbody>
				@if(!empty($loanview))
					@for($cn=count($loanview); $cn>0;$cn--)
					@php $cnt = ""; $loc = ""; $temp = ""; $row = ""; $balance=0; @endphp
					<? $cnt = $cn-1;
								$loc = $cnt + 1;
								if($loc != $temp){
								if($row == 1){
									$style='style = "background-color:#E2F0F3;"';
									$row=0;
									
								} else {
									$style='style = ""';
									$row=1;
									}$styleTD = 'style = "border-top:1px dotted #000000;vertical-align: top;"';
								} else {
									 $styleTD = 'style = "border-top:0px dotted #000000;vertical-align: top;"';
								}
								 $i = $cn-1; 
										   if($i == 0){
										  	 echo	$i = "";
										  	 	$style='style = "background-color:#acf5e2;"';
										   }
							 ?>
						<tr>
						<?php  $i = $cn-1; 
										   if($i == 0){
										  	 echo	$i = "";
										  	        $style='style = "background-color:#acf5e2;"';
										   }else{
										  	      $style='';

										   }
						?>
							<td class="tac" <?php echo $style; ?>>
									<div style = "cursor: text; " id = "orderno_<?php echo $cnt;?>">
										{{$i}}
									</div>
							</td>
							<td class="tac" <?php echo $style; ?>>
								@if(isset($loanview[$i]['bankdate']))
									{{ $loanview[$i]['bankdate'] }}
								@endif
							</td>
							<td class="tal" <?php echo $style; ?>>
								{{ trans('messages.lbl_loanpay') }}
							</td>
							<td class="tac pr5" <?php echo $style; ?>>
								@if(isset($loanview[$i]['monthsleft']))
									{{ $loanview[$i]['monthsleft'] }}
								@endif
							</td>
							<td class="text-right pr5" <?php echo $style; ?>>
								@if(isset($loanview[$i]['paymentamount']))
									{{ $loanview[$i]['paymentamount'] }}
								@endif
							</td>
							<td class="text-right pr5" <?php echo $style; ?>>
								@if(isset($loanview[$i]['interest']))
									{{ $loanview[$i]['interest'] }}
								@endif
							</td>
							<td class="text-right pr5" <?php echo $style; ?>>
								@if(isset($loanview[$i]['balance']))
									{{ number_format($loanview[$i]['balance']) }}
								@elseif($i == "")
									{{ $loanview[0]['balance'] }}
								@endif
							</td>
							<td class="text-left pl5" <?php echo $style; ?>>
								@if(isset($loanview[$i]['remark_dtl']))
									{{ $loanview[$i]['remark_dtl'] }}
								@endif
							</td>
						</tr>
								<?php $temp = $loc; ?>
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
	@if(!empty($loanview))
	<div class="text-center pl16">
		@if(!empty($viewlist->total()))
			<span class="pull-left mt24">
				{{ $viewlist->firstItem() }} ~ {{ $viewlist->lastItem() }} / {{ $viewlist->total() }}
			</span>
		@endif 
		{{ $viewlist->links() }}
		<div class="CMN_display_block flr mr18">
			{{ $viewlist->linkspagelimit() }}
		</div>
	</div>
	@endif
	</article>
	{{ Form::close() }}
	</div>
@endsection
