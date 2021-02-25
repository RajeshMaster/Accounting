@extends('layouts.app')
@section('content')

@php use App\Http\Helpers; @endphp

<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
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
	.tablealternate tr:nth-child(even){ 
		background: #FFFFFF !important;
	}
	.tablealternate tr:nth-child(4n+3){
		background: #e5f4f9 !important;
	}
	.tablealternate tr:nth-child(4n+4){
		background:  #e5f4f9 !important;
	}
</style>

{{ HTML::script('resources/assets/js/externalinvoice.js') }}
{{ HTML::style('resources/assets/css/bootstrap.min.css') }}
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::style('resources/assets/css/switch.css') }}

<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="external" class="DEC_flex_wrapper " data-category="external external_sub_2">

	{{ Form::open(array('name'=>'frmextinvoiceview', 
						'id'=>'frmextinvoiceview', 
						'url' => 'ExternalInvoice/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}

		{{ Form::hidden('viewid', $request->viewid, array('id' => 'viewid')) }}
		{{ Form::hidden('editflg', '', array('id' => 'editflg')) }}
	    {{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
		{{ Form::hidden('editid', '', array('id' => 'editid')) }}
		{{ Form::hidden('filter', $request->filter, array('id' => 'filter')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	    {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	    {{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('sortOptn',$request->sortOptn , array('id' => 'sortOptn')) }}
	    {{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
	    {{ Form::hidden('searchmethod', $request->searchmethod, array('id' => 'searchmethod')) }}
	    {{ Form::hidden('msearchusercode', $request->msearchusercode, array('id' => 'msearchusercode')) }}
		{{ Form::hidden('msearchusers', $request->msearchusers, array('id' => 'msearchusers')) }}
		{{ Form::hidden('msearchstdate', $request->msearchstdate, array('id' => 'msearchstdate')) }}
		{{ Form::hidden('msearcheddate', $request->msearcheddate, array('id' => 'msearcheddate')) }}
	    {{ Form::hidden('singlesearch', $request->singlesearch, array('id' => 'singlesearch')) }}
		{{ Form::hidden('ordervalue', $request->ordervalue, array('id' => 'ordervalue')) }}
		{{ Form::hidden('totalrecords', $totalRec, array('id' => 'totalrecords')) }}
		{{ Form::hidden('currentRec', $currentRec, array('id' => 'currentRec')) }}

	<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/invoices-icon-3.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_invoice') }}</h2>
			<h2 class="pull-left mt15">・</h2>
			<h2 class="pull-left mt15">{{ trans('messages.lbl_Details') }}</h2>
		</div>
	</div>
	<!-- End Heading -->

	<div class="col-xs-12 pm0 pull-left mt10">
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

		<div class="col-xs-6 ml10 pm0 pull-left">
			<a href="javascript:fngotoindex();" 
				class="btn btn-info box80">
				<span class="fa fa-arrow-left"></span>
					{{ trans('messages.lbl_back') }}
			</a>
			@if($invoicedata[0]->classification == 0)
				<a href="javascript:gotoinvedit('{{ $request->viewid }}','edit');"  
					class="btn btn-warning box80">
					<span class="fa fa-edit"></span>
					{{ trans('messages.lbl_edit') }}
				</a>
			@endif
			<a href="javascript:gotoinvedit('{{ $request->viewid }}','copy');" 
				class="btn btn-primary box80">
				<span class="fa fa-plus"></span>
					{{ trans('messages.lbl_copy') }}
			</a>
		</div>

		<div class="col-xs-2 text-right ml55">
			{{ Helpers::displayYearMon_view($search_flg,$totalRec,$currentRec,$date_month,$get_view,$curTime,$order,$sort,$invoicedata[0]->id) }}
		</div>

		<!-- SEARCH -->

		<div style="top: 185px;position: fixed;" @if ($request->searchmethod == 1 || $request->searchmethod == 2) 
			class="CMN_fixed pm0" 
		@else 
			class="open CMN_fixed pm0 pr0" 
		@endif 
		id="styleSelector">
			<div class="selector-toggle">
				<a id="sidedesignselector" href="javascript:void(0)"></a>
			</div>
			<div style="background-color:#136E83;color: white;">
				<ul class="ml5">	
					<span>
						<li>
							<label class="mt10">Claim Total Amount</label>
						</li>
						<li class="mb10">
							<label class="pull-right pr10" style="font-size:18px;">
								¥ {{ number_format($grandtotal) }}
							</label>
						</li>
					</span>
					<li class="theme-option ml6">
						<div class="box100per mt10">
						<div>
					</li>
					<li class="theme-option ml6">
						<div class="box100per mt10">
						<div>
					</li>
				</ul>
			</div>

			<?php 
			$path = "../AccountingUpload/ExternalInvoice";
			$files = glob($path . '/' . $invoicedata[0]->invoiceId . '*.pdf');
			if ( $files !== false ) {
				$filecount = count($files);
			}
			$i = 1;
			foreach ($files as $readfile) {
				$setpath[$i] = $readfile;
				$i = $i + 1;
			}
			if($filecount != "") {
				krsort($setpath); ?>
				<div>
					<ul>
					<li>
						<label class="mt10 ml15">PDF Download List</label>
					</li>
					<?php $j = $filecount;
					for ($i = $filecount; $i >= 1; --$i) {
						if ($i == 1){
							$filename = $invoicedata[0]->invoiceId;
						} else {
							$filename = $invoicedata[0]->invoiceId."_".str_pad(($i-1) , 2, '0', STR_PAD_LEFT);
						}
						$filepath = $path."/".$filename.".pdf"; ?>	
						<li class="ml25">
						<i class="fa fa-check-circle-o" aria-hidden="true"></i>
						<a name="estimat" href="javascript:filedownload('<?php echo "../../../".$path; ?>','<?php echo $filename.".pdf"; ?>');" 
							style="font-size:14px;font-weight: bold;"><?php echo $filename; ?></a>&nbsp;&nbsp;&nbsp;&nbsp;
							<?php if($i == $filecount) { ?>
								<img name="newinvoice" class="mt3" src="{{ URL::asset('resources/assets/images/newicon.gif') }}" style="width:34px;height:14px;vertical-align: top;">
							<?php } ?>
						</li>
						<?php $j--;
					} ?>
					</ul>
					<input type = "hidden" id = "estimatepdflinkid" name = "estimatepdflinkid" value = "">
				</div>
			<?php } ?>

		</div>

		<!-- END SEARCH -->

		<div class="mr10 ml10 box100per">
		<div class="minh400">
			<div class="col-xs-12 mt10 pm0">
				<div class="col-xs-3 pm0" style="border :1px solid #136E83">
					<div class="col-xs-12 text-left clr_blue" style="background: #b0e0f2">
						<label class="fwn" style="color: black;">
							{{ trans('messages.lbl_usernamesign') }} :
						</label>
					</div>
					<div class="col-xs-12" style="background: #e5f4f9">
						<div class="col-xs-12" style="background: #e5f4f9">
							@if(isset($getbankdetails[0]->userName))
								<span>
									{{ (isset($getbankdetails[0]->userName)?$getbankdetails[0]->userName:"Nill") }}
									{{ Form::hidden('userName', (isset($getbankdetails[0]->userName)?$getbankdetails[0]->userName:"Nill"), array('id' => 'userName')) }}
								</span>
							@else
							@endif
							<?php echo "<br>"; ?>
							@if(!empty($getbankdetails[0]->address))
								<span>
									@if(isset($getbankdetails[0]->address))
										{!! nl2br(e($getbankdetails[0]->address)) !!}
										{{ (isset($getbankdetails[0]->buildingName)?$getbankdetails[0]->buildingName:"") }}
									@else
									@endif
								</span>
							@endif
							<?php echo "<br>"; ?>
							@if(!empty($getbankdetails[0]->pincode))
								<span>
									@if(isset($getbankdetails[0]->pincode))
										{!! nl2br(e($getbankdetails[0]->pincode)) !!}
									@else
									@endif
								</span>
							@endif
							<?php echo "<br>"; ?>
							@if (!empty($getbankdetails[0]->mobileno)) 
								<span>
									{{ (isset($getbankdetails[0]->mobileno)?$getbankdetails[0]->mobileno:"") }}
								</span>
							@endif
							<?php echo "<br>"; ?>
							<span></span>
						</div>
					</div>
				</div>
				<div class="col-xs-3 pm0"></div>
				<div class="col-xs-3 pm0" style="border :1px solid #136E83" 
					id="certificatesort">
					<div class="col-xs-12 pm0">
						<div class="col-xs-6 text-right clr_blue" style="background: #b0e0f2">
							<label class="fwn" style="color: black;">
								{{ trans('messages.lbl_reginvoice') }}
							</label>
						</div>
						<div class="col-xs-6 brown">
							<lablel style="font-weight: bold;">
								{{ (isset($invoicedata[0]->invoiceId)?$invoicedata[0]->invoiceId:"Nill") }}
							</lablel>
						</div>
					</div>
					<div class="col-xs-12 pm0">
						<div class="col-xs-6 text-right clr_blue" style="background: #b0e0f2">
							<label class="fwn" style="color: black;">
								{{ trans('messages.lbl_invoicenumber') }}
							</label>
						</div>
						<div class="col-xs-6 brown">
							<lablel style="font-weight: bold;">
								{{ (isset($invoicedata[0]->invoiceNumber)?$invoicedata[0]->invoiceNumber:"Nill") }}
							</lablel>
						</div>
					</div>
					<div class="col-xs-12 pm0">
						<div class="col-xs-6 text-right clr_blue" style="background: #b0e0f2">
							<label class="fwn" style="color: black;">
								{{ trans('messages.lbl_invoicedate') }}
							</label>
						</div>
						<div class="col-xs-6">
							{{ (isset($invoicedata[0]->quot_date)?$invoicedata[0]->quot_date:"Nill") }}
						</div>
					</div>
					<div class="col-xs-12 pm0">
						<div class="col-xs-6 text-right clr_blue" style="background: #b0e0f2">
							<label class="fwn" style="color: black;">
								{{ trans('messages.lbl_paymentday') }}
							</label>
						</div>
						<div class="col-xs-6">
							{{ (isset($invoicedata[0]->payment_date)?$invoicedata[0]->payment_date:"Nill") }}
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-12 mb15 mt15" style="padding: 0px;">
				<div class="col-xs-6 pm0">
					<div class="col-xs-2  pm0 text-left clr_blue">
						<label>{{ trans('messages.lbl_projecttitle') }} :</label>
					</div>
					<div class="col-xs-7 ml10 pm0" style="border-bottom: 1px solid #A7D4DD">
						<label>
							{{ (isset($invoicedata[0]->projectName)?$invoicedata[0]->projectName:"Nill") }}
						</label>
					</div>
				</div>
				<div class="col-xs-6">
					<div class="box25per fll text-left clr_blue">
						<label>{{ trans('messages.lbl_projecttype') }} :</label>
					</div>
					<div class="col-xs-9 pm0" style="border-bottom: 1px solid #A7D4DD">
						<label>
							{{ (isset($invoicedata[0]->ProjectTypeName)?$invoicedata[0]->ProjectTypeName:"Nill") }}
						</label>
					</div>
				</div>
			</div>
			<div class="mr10">
				<div class="minh400">
					<table class="tablealternate box100per" style="table-layout: fixed;">
						<colgroup>
							<col width="15%">
							<col width="15%">
							<col width="7%">
							<col width="15%">
							<col width="20%">
							<col width="">
						</colgroup>
						<thead class="CMN_tbltheadcolor">
							<tr class="tableheader fwb tac"> 
								<th colspan="2" class="tac">
									{{ trans('messages.lbl_workspec') }}</th>
								<th class="tac">
									{{ trans('messages.lbl_quantity') }}</th>
								<th class="tac">
									{{ trans('messages.lbl_unitprice') }}</th>
								<th class="tac">
									{{ trans('messages.lbl_amount') }}</th>
								<th class="tac">
									{{ trans('messages.lbl_remarks') }}</th>
							</tr>
						</thead>
						<tbody>
							<?php $k = 0; ?>
							@php
								$workloop = "work_specific"; 
								$quantityloop = "quantity"; 
								$unit_priceloop = "unit_price"; 
								$amountloop = "amount"; 
								$remarksloop = "remarks";
							@endphp
							<?php 
							if($amtcount < 15) {
								$a = 15;
							} else {
								$a= $amtcount;
							} ?>
							@for ($j = 1; $j <= $a; $j++)
								<tr>
									<td colspan="2">
										@if(isset($invoicedata[$k]))
											{{ ($invoicedata[$k]->$workloop) ? $invoicedata[$k]->$workloop : '' }}
										@endif
									</td>
									<td rowspan="2" class="text-center">
										@if(isset($invoicedata[$k]))
											{{ ($invoicedata[$k]->$quantityloop) ? $invoicedata[$k]->$quantityloop : '' }}
										@endif
									</td>
									<td rowspan="2" class="text-right">
										@if(isset($invoicedata[$k]))
											@if($invoicedata[$k]->$unit_priceloop<0)
												<div style= "color: red">
													{{ isset($invoicedata[$k]->$unit_priceloop) ? $invoicedata[$k]->$unit_priceloop : '' }}
												</div>
											@else
												{{ ($invoicedata[$k]->$unit_priceloop) ? $invoicedata[$k]->$unit_priceloop : '' }}
											@endif
										@endif
									</td>
									<td rowspan="2" class="text-right">
										@if(isset($invoicedata[$k]))
											@if($invoicedata[$k]->$amountloop<0)
												<div style= "color: red">
													{{ isset($invoicedata[$k]->$amountloop) ? $invoicedata[$k]->$amountloop : '' }}
												</div>
											@else
												{{ ($invoicedata[$k]->$amountloop) ? $invoicedata[$k]->$amountloop : '' }}
											@endif
										@endif
									</td>
									<td rowspan="2">
										@if(isset($invoicedata[$k]))
											{!! nl2br(e(($invoicedata[$k]->$remarksloop) ? $invoicedata[$k]->$remarksloop : '')) !!}
										@endif
									</td>
								</tr>
								@for ($row = 0; $row < 1; $row++)
									<tr>
										@for ($col = 0; $col < 1; $col++)
											<td colspan='2' style="color: grey;">
											</td>
										@endfor
									</tr>
								@endfor
								<?php $k++; ?>
							@endfor
      						<tr>
								<td class="tar" style="background: #b0e0f2">
									{{ trans('messages.lbl_transferaccount') }}
								</td>
								<td colspan="2" style="background: #e5f4f9">
									{{ (isset($getbankdetails[0]->bankName)?$getbankdetails[0]->bankName:"Nill") }}
								</td>
								<td class="tar"  style="background: #b0e0f2">
									{{ trans('messages.lbl_subtotal') }}
								</td>
								<td class="tar" style="background: #e5f4f9">
									{{ isset($invoicedata[0]->totalval)?$invoicedata[0]->totalval:0 }}
								</td>
								<td style="border:hidden;border-top: 1px solid lightgrey;border-left: 1px solid lightgrey;background: white"></td>
							</tr>
							<tr>
								<td class="tar" style="background: #b0e0f2">
									{{ trans('messages.lbl_accountnumber') }}
								</td>
								<td colspan="2" style="background: #e5f4f9">
									{{ $type }} {{isset($getbankdetails[0]->accountNo)?$getbankdetails[0]->accountNo:"Nill"}}
								</td>
								<td class="tar"  style="background: #b0e0f2">
									{{ trans('messages.lbl_consumptiontax') }}
								</td>
								<td class="tar" style="background: #e5f4f9">
									{{ number_format($dispval) }}
								</td>
								<td style="border:hidden;border-left: 1px solid lightgrey;background: white"></td>
							</tr>
      						<tr>
								<td class="tar" style="background: #b0e0f2">
									{{ trans('messages.lbl_branchname') }}
								</td>
								<td colspan="2" style="background: #e5f4f9">
									{{ isset($getbankdetails[0]->branchName)?$getbankdetails[0]->branchName:"Nill"}}
								</td>
								<td class="tar"  style="border:1px solid lightgrey; background: #b0e0f2">
									{{ trans('messages.lbl_claimtotalamt') }}
								</td>
								<td class="tar" style="border:1px solid lightgrey;background: #e5f4f9"><b>
									{{ number_format($grandtotal) }}</b>
								</td>
								<td style="border:hidden;border-left: 1px solid lightgrey;background: white"></td>
							</tr>
							<tr>
								<td class="tar" style="background: #b0e0f2">
									{{ trans('messages.lbl_accountholder') }}
								</td>
								<td colspan="4" style="background: #e5f4f9">
									{{isset($getbankdetails[0]->bankKanaName)?$getbankdetails[0]->bankKanaName:"Nill" }}
								</td>
      						</tr>
						</tbody>
					</table>
				</div>
				@if(!empty($invoicedata[0]->special_ins1) || !empty($invoicedata[0]->special_ins2) || !empty($invoicedata[0]->special_ins3) || !empty($invoicedata[0]->special_ins4) || !empty($invoicedata[0]->special_ins5))
					<div class="col-xs-4 mt10 inline-block">
						<div class="box11per clr_blue ml100 text-right">
							<label>{{ trans('messages.lbl_notices') }}</label>
						</div>
						<div class="ml110 text-left pm0">
							<table style="border: 0px;" class="pm0">
								@for ($i = 1; $i <= 5; $i++)
									<tr class="pm0">
										@php $special_ins = "special_ins".$i; @endphp
										@if(!empty($invoicedata[0]->$special_ins))
											<td style="border: 0px;height: 15px !important;" class="pm0">
													<span>{{ $i.") " }}{{ !empty($invoicedata[0]->$special_ins)?$invoicedata[0]->$special_ins:"Nill" }}</span>
											</td>
										@endif
									</tr>
								@endfor
							</table>
						</div>
					</div>
				@endif
				<?php if(!empty($invoicedata[0]->memo)){  ?>
					<div class="inline-block col-xs-7 mt10">
						<div class="ml80 box70per clr_blue ">
							<label>{{ trans('messages.lbl_memo') }}</label>
						</div>
						<div class="ml90 text-left">
							@if(isset($invoicedata[0]->memo))
								{!! nl2br(e($invoicedata[0]->memo)) !!}
							@endif
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
		</div>
	</div>

	{{ Form::close() }}

	<div id="coverpopup" class="modal fade">
		<div id="login-overlay">
			<div class="modal-content">
				<!-- Popup will be loaded here -->
			</div>
		</div>
	</div>

</article>

</div>

@endsection