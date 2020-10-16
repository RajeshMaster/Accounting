@extends('layouts.app')
@section('content')
@php use App\Http\Helpers; @endphp
{{ HTML::script('resources/assets/js/payment.js') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
	function pageClick(pageval) {
	$('#page').val(pageval);
	var mainmenu= $('#mainmenu').val();
	$('#frmcustomerview').attr('action','customerview'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmcustomerview").submit();
	}
	function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	var mainmenu= $('#mainmenu').val();
	$('#frmcustomerview').attr('action','customerview'+'?mainmenu='+mainmenu+'&time='+datetime); 
	$("#frmcustomerview").submit();
	}
</script>
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper " data-category="sales sales_sub_4">
	{{ Form::open(array('name'=>'frmcustomerview', 
						'id'=>'frmcustomerview', 
						'url' => 'Payment/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	    {{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
	    {{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('topclick', $request->topclick, array('id' => 'topclick')) }}
		{{ Form::hidden('sortOptn', $request->paymentsort , array('id' => 'sortOptn')) }}
	    {{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	    {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	    {{ Form::hidden('companyname',$request->companyname, array('id' => 'companyname')) }} 
	    {{ Form::hidden('invoiceid','', array('id' => 'invoiceid')) }}
	    {{ Form::hidden('backflg','', array('id' => 'backflg')) }}
<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/payment.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_payment') }} {{ trans('messages.lbl_view') }} {{ trans('messages.lbl_Details') }}</h2>
		</div>
	</div>
<!-- End Heading -->
<div class="col-xs-12 mt10">
	<a href="javascript:fnpaymentindex();"
        class="btn btn-info box80"><span class="fa fa-arrow-left"></span>{{ trans('messages.lbl_back') }}</a>
</div>
<div class="col-xs-12 mt10" style="display: inline-block;">
	<div class="box12per text-left clr_blue" style="padding-left: 0px;display: inline-block;">
		<label>{{ trans('messages.lbl_custname') }} :</label>
	</div>
	<div class="box50per text-left" style="display: inline-block;">
		<b>{{ $request->companyname }}</b>
	</div>
</div>
<div class="mr10 ml10">
		<div class="minh400">
			<table class="tablealternate box100per">
				<colgroup>
				   <col width="4%">
				   <col width="7%">
				   <col width="7%">
				   <col width="15%">
				   <col width="14%">
				   <col width="15%">
				   <col width="15%">
				   <col width="8%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader tac"> 
				  		<th class="tac">{{ trans('messages.lbl_sno') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_invoicedate') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_paymentdate') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_invorbank') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_projecttitle') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_invoiceamt') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_payment') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_bankfee') }}</th>
			   		</tr>
			   	</thead>
			   	<tbody>
			   		<?php 
			   			$temp="";
			   			$loc="";
			   			$row="";
			   		if(!empty($inv_query)) {
			   		for($i=$datacount; $i >= 0; $i--) {
						$loc=$userValue[$i]['id'];
							if($loc != $temp){
								if($row==1){
									$style='style="background-color:#dff1f4;text-align:center;height:25px; "';
									$row=0;
								} else {
									$style='style="text-align:center;height:25px;"';
									$row=1;
								}
								$styleTD='style="border:1px dotted black;vertical-align: top;text-align:centerheight:25px;"';
							} else {
								$styleTD='style="border:0px dotted black;vertical-align: top;text-align:centerheight:25px;"';
							}
							if($balance_amount >= 0){
								$balance_style="style='color:red'";
							}else if($balance_amount < 0){
								$balance_style="style='color:blue'";
							}else{
								$balance_style="style='color:green'";
							}
							if(empty($userValue[$i]['user_id'])){
								$user_id_BankName= $userValue[$i]['BankName'];
							}else{
								$user_id_BankName= $userValue[$i]['user_id'];
							}
							if ($i == ($datacount)) {
						?>
			   			<tr height="25px;">
							<td width="4%"></td>
							<td width="9%"></td>
							<td width="10%"></td>
							<td width="15%"></td>
							<td width="20%">
								<span <?php echo $balance_style;?>>Payment Balance</span>
							</td>
							<td width="12%"></td>
							<td align="right" width="10%">
								<span <?php echo $balance_style;?> >
									<?php 
										echo number_format($balance_amount); 
									?></span>
							</td>
							<td width="9%"></td>
						</tr>
							<tr style="background-color:#dff1f4;text-align:center;height:25px;"height="25px">
								<td  width="4%"></td>
							<td width="9%"></td>
							<td width="10%"></td>
							<td width="15%"></td>
							<td width="20%"></td>
							<td width="12%"></td>
							<td align="right" width="10%"></td>
							<td width="9%"></td>
							</tr>
					<?php } ?>
						<tr>
							<td class="tac">
								{{ $i+1 }}
							</td>
							<td style="text-align: center;">
								{{ isset($userValue[$i]['pay_inv_date'])?$userValue[$i]['pay_inv_date']:"" }}
							</td>
							<td style="text-align: center;">
								{{ isset($userValue[$i]['payment_date'])?$userValue[$i]['payment_date']:"" }}
							</td>
							<td>
								@if(empty($userValue[$i]['user_id']))
									{{ $user_id_BankName }}
								@else
									<a class="btn-link" style="color: blue !important;" href="javascript:fngotoinvoiceview('{{ $userValue[$i]['id'] }}');">
									{{ $user_id_BankName }}</a>
								@endif
							</td>
							<td>
								{{ isset($userValue[$i]['project_name'])?$userValue[$i]['project_name']:"" }}
							</td>
								<?php $zeros = 0; ?>
							<td style="text-align: right;">
									@if(isset($userValue[$i]['totalval']))
										@if(empty($userValue[$i]['totalval']))
										@else
											{{ number_format($userValue[$i]['totalval']) }}
										@endif
									@else
									@endif
							</td>
							<td style="text-align: right;">
								{{ isset($userValue[$i]['deposit_amount'])?$userValue[$i]['deposit_amount']:"" }}
							</td>
							<td style="text-align: right;">
								{{ isset($userValue[$i]['bank_charge'])?$userValue[$i]['bank_charge']:"" }}
							</td>
						</tr>
					<?php } 
						} else {?>
						<tr>
							<td class="text-center" colspan="8" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
						</tr>
					<?php } ?>
			   	</tbody>
			</table>
		</div>
		@if(!empty($inv_query))
          <div class="text-center">
            @if(!empty($inv_query->total()))
              <span class="pull-left mt24">
                {{ $inv_query->firstItem() }} ~ {{ $inv_query->lastItem() }} / {{ $inv_query->total() }}
              </span>
            @endif 
            {{ $inv_query->links() }}
            <div class="CMN_display_block flr">
              {{ $inv_query->linkspagelimit() }}
            </div>  
          </div>
          @endif
</div>
	{{ Form::close() }}
</article>
</div>
@endsection