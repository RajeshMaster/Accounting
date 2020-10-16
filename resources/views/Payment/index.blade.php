@extends('layouts.app')
@section('content')
@php use App\Http\Helpers; @endphp
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
<style type="text/css">
	.sort_asc {
		background-image:url({{ URL::asset('resources/assets/images/upArrow.png') }}) !important;
	}
	.sort_desc {
		background-image:url({{ URL::asset('resources/assets/images/downArrow.png') }}) !important;
	}
</style>
{{ HTML::script('resources/assets/js/payment.js') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper " data-category="sales sales_sub_4">
	{{ Form::open(array('name'=>'frmpaymentindex', 
						'id'=>'frmpaymentindex', 
						'url' => 'Payment/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	    {{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
	    {{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('topclick', $request->topclick, array('id' => 'topclick')) }}
		{{ Form::hidden('sortOptn', $request->paymentsort , array('id' => 'sortOptn')) }}
	    {{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	    {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	    {{ Form::hidden('estimate_id', $request->estimate_id , array('id' => 'estimate_id')) }}
	    {{ Form::hidden('companyname', '' , array('id' => 'companyname')) }}
	    {{ Form::hidden('payid', '' , array('id' => 'payid')) }}
	    {{ Form::hidden('invoiceid', '' , array('id' => 'invoiceid')) }}
	    {{ Form::hidden('frompayindex', '1' , array('id' => 'frompayindex')) }}
		{{ Form::hidden('quot_date', '', array('id' => 'quot_date')) }}
<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/payment.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_payment') }}</h2>
		</div>
	</div>
	<div class="box100per pr10 pl10 mt10">
		<div class="mt10">
			{{ Helpers::displayYear_Monthpayment($account_period, $year_month, $db_year_month, $date_month, $dbnext, $dbprevious, $last_year, $current_year, $account_val) }}
		</div>
	</div>
<!-- End Heading -->
	<div class="col-xs-12 pm0 pull-left mb10">
			<div class="mt13 pm0 pr12">
				<div class="form-group pm0 pull-right moveleft nodropdownsymbol" id="moveleft">
					{{ Form::select('paymentsort', [null=>'']+$paymentsortarray, $request->paymentsort,
	                            array('class' => 'form-control'.' ' .$request->sortstyle.' '.'CMN_sorting 						pull-right',
	                           'id' => 'paymentsort',
	                           'name' => 'paymentsort'))
	                }}
	            </div>
			</div>
	</div>
	<div class="mr10 ml10">
		<div class="minh400">
			<table class="tablealternate box100per">
				<colgroup>
				   <col width="4%">
				   <col width="9%">
				   <col width="8%">
				   <col width="">
				   <col width="9%">
				@if($debit_total != 0)
				   <col width="15%">
				@endif
				   <col width="15%">
				   <col width="15%">
				   <col width="2%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader tac"> 
				  		<th class="tac">{{ trans('messages.lbl_sno') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_paymentdate') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_invoiceno') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_customername') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_bankcharge') }}</th>
				  		@if($debit_total != 0)
				  		<th class="tac">{{ trans('messages.lbl_debit') }}</th>
				  		@endif
				  		<th class="tac">{{ trans('messages.lbl_credit') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_oldbalance') }}</th>
				  		<th class="tac"></th>
			   		</tr>
			   	</thead>
			   	<tbody>
			   			{{ $temp = ""}}
                  		{{--*/ $row = '0' /*--}}
                  		{{ $tempcomp = ""}}
                  		{{--*/ $rowcomp = '0' /*--}}
                  		{{ $tempbtn = ""}}
                  		{{--*/ $rowbtn = '0' /*--}}
		   			<?php $newbal=0;$debitbal=0;$style_tr="";$style_tdold=""; ?>
			   			<?php $last_key = (count($get_det)-1); ?>
			   		@forelse($get_det as $key => $data)
			   			{{--*/ $loc = $data['invpaymentdate'] /*--}}
			   			{{--*/ $loccomp = $data['clientnumber'] /*--}}
			   			{{--*/ $locold = $data['oldbalance'][0]->Ctotal /*--}}
			   			{{--*/ $locedit = $data['paid_status'] /*--}}
			   			{{--*/ $locbtn = $data['payidtotal'] /*--}}
			   			@if($locbtn != $tempbtn)
                        	@if($rowbtn==1)
                          		{{--*/ $style_trbtn = 'background-color: #E5F4F9;' /*--}}
                          		{{--*/ $rowbtn = '0' /*--}}
                        	@else
                          		{{--*/ $style_trbtn = 'background-color: #FFFFFF;' /*--}}
                          		{{--*/ $rowbtn = '1' /*--}}
                        	@endif
                        	{{--*/ $style_tdbtn = '' /*--}}
                      	@else
                        	{{--*/ $style_tdbtn = 'border-top: hidden;' /*--}}
                      	@endif
                      	@if($loc != $temp)
                        	@if($row==1)
                          		{{--*/ $style_tr = 'background-color: #E5F4F9;' /*--}}
                          		{{--*/ $row = '0' /*--}}
                        	@else
                          		{{--*/ $style_tr = 'background-color: #FFFFFF;' /*--}}
                          		{{--*/ $row = '1' /*--}}
                        	@endif
                        	{{--*/ $style_td = '' /*--}}
                      	@else
                        	{{--*/ $style_td = 'border-top: hidden;' /*--}}
                      	@endif
                      	@if($loccomp != $tempcomp) 
                        @if($rowcomp==1)
                          {{--*/ $style_trcomp = 'background-color: #E5F4F9;' /*--}}
                          {{--*/ $rowcomp = '0' /*--}}
                        @else
                          {{--*/ $style_trcomp = 'background-color: #FFFFFF;' /*--}}
                          {{--*/ $rowcomp = '1' /*--}}
                        @endif
                        {{--*/ $style_tdcomp = '' /*--}}
                      @else
                        {{--*/ $style_tdcomp = 'border-top:hidden;' /*--}}
                      @endif
		   			<?php $singlecomptot = $allTotal[0]->Ctotal; ?> 
			   			<tr style="{{$style_trcomp}}">
			   				<td style="text-align: center;">
			   				{{ $key + 1 }}
			   				</td>
			   				<td style="{{$style_td}}" class="tac">
			   					@if($loc != $temp)
			   						{{ $data['invpaymentdate'] }}
			   					@endif
			   				</td>
			   				<td style="text-align: center;">
			   					<div class="pl10 tac fll">
			   					<?php if($data['deposit_amount']!="") { ?>
			   					<a class="btn-link" style="color: blue !important;" href ="javascript:fngotospecification('{{ $data['id'] }}','{{ $data['invid'] }}');">
			   					{{ $data['invoiceno'] }}</a>
			   					<?php } else { ?>
			   						{{ $data['invoiceno'] }}
			   					<?php } ?>
			   					</div>
			   					<div class="tac">
			   					<?php if($data['deposit_amount']!="") { ?>
			   					<!-- <a onclick="return fnPaymentEdit('edit','{{ $data['id'] }}');" class="csrp"><i class="fa fa-edit" aria-hidden="true"></i></a> -->
			   					<?php } ?>
			   					</div>
			   				</td>
			   				<td style="{{$style_tdcomp}}">
			   					@if($loccomp != $tempcomp)
				   					<a class="btn-link" style="color: blue !important;" href="javascript:fncustomerview('{{ $data['clientName'] }}');">
			   						<b>{{ $data['clientName'] }}</b></a>
			   					@endif
			   				</td>
			   				<td class="tar">
			   					{{ ($data['bank_charge'] == "0")?"":$data['bank_charge'] }}
			   				</td>
			   				@if($debit_total != 0)
			   				<td @if($data['previousamountstyle'] != "")
			   						title="Skipped Value For Total"
			   					@else
			   						title=""
			   					@endif class="tar" <?php echo $data['previousamountstyle']; ?>>
			   					@if($data['Debitval']!=0)
		   							{{ $data['Debitval'] }}
			   					@endif
			   				</td>
			   				@endif
			   				<td class="tar" <?php echo $data['excessamountstyle']; ?>>
			   					@if($data['Creditval']!=0)
		   							{{ $data['Creditval'] }}
			   					@endif
			   				</td>
			   				<td style="{{$style_tdcomp}}" class="tar">
			   					@if($loccomp != $tempcomp)
			   						{{ ($data['oldbalance'][0]->Ctotal == 0)?"":$data['oldbalance'][0]->Ctotal }}
			   					@endif
			   				</td>
			   				<td style="{{$style_tdbtn}}">
			   					<div class="tac" style="vertical-align: middle;">
			   					@if($data['paid_status']==1 && ($data['payinvid']!=""))
			   					<a onclick="return fnPaymentEdit('edit','{{ $data['id'] }}');" class="csrp"><i class="fa fa-edit" aria-hidden="true"></i></a>
			   					@endif
			   					@if($data['paid_status']==0 || $data['paid_status']=="")
			   						<a onclick="return fnpaymentadd('{{ $data['invid'] }}','{{ $data['quot_date'] }}');" class="csrp"><i class="fa fa-plus" aria-hidden="true"></i></a>
			   					@endif
			   					</div>
			   				</td>
			   			</tr>
			   				{{--*/ $temp = $loc /*--}}
			   				{{--*/ $tempcomp = $loccomp /*--}}
			   				{{--*/ $tempbtn = $locbtn /*--}}
			   		@empty
						<tr>
							<td class="text-center" colspan="8" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
						</tr>
					@endforelse
			   		@if(isset($get_det[0]["invoiceno"]))
						<tr>
			   				<td style="border: none;background: white;"></td>
			   				<td style="border: none;background: white;"></td>
			   				<td style="border: none;background: white;"></td>
			   				<td class="tar fwb" style="background: #b0e0f2">{{ trans('messages.lbl_total') }}</td>
			   				<td class="fwb" style="background: #e5f4f9;text-align: right;">
			   					<!-- {{ ($allTotal[0]->BCtotal=="0")?"":$allTotal[0]->BCtotal }} -->
			   					{{ ($bankcharge_total==0)?"":number_format($bankcharge_total) }}
			   				</td>
				  		@if($debit_total != 0)
			   				<td class="fwb" style="background: #e5f4f9;color: red; text-align: right;">
			   					<!-- {{ ($allTotal[0]->Dtotal=="0")?"":$allTotal[0]->Dtotal }} -->
			   					{{ ($debit_total==0)?"":number_format($debit_total) }}
			   				</td>
			   			@endif
			   				<td class="fwb" style="background: #e5f4f9;color: green; text-align: right;">
			   					<!-- {{ ($allTotal[0]->Ctotal=="0")?"":$allTotal[0]->Ctotal }} -->
			   					{{ ($credit_total==0)?"":number_format($credit_total) }}
			   				</td>
			   			</tr>
				  			@if($debit_total != 0)
				  				{{--*/ $coltemp = '3' /*--}}
				  			@else
				  				{{--*/ $coltemp = '2' /*--}}
				  			@endif
			   			<tr>
			   				<td style="border: none;background: white;"></td>
			   				<td style="border: none;background: white;"></td>
			   				<td style="border: none;background: white;"></td>
			   				<td class="tar fwb" style="background: #b0e0f2">{{ trans('messages.lbl_grandtot') }}</td>
			   				<td colspan="<?php echo $coltemp; ?>" class="fwb" style="background: #e5f4f9;text-align: center;">
			   					<!-- {{ $allTotal[0]->Gtotal }} -->
			   					{{ ($grand_total==0)?"":number_format($grand_total) }}
			   				</td>
			   			</tr>
			   		@endif
			   	</tbody>
			</table>
		</div>
	</div>
	{{ Form::close() }}
</article>
</div>
@endsection