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
{{ HTML::script('resources/assets/js/invoice.js') }}
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::style('resources/assets/css/switch.css') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper " data-category="sales sales_sub_2">
	{{ Form::open(array('name'=>'frminvoiceindex', 
						'id'=>'frminvoiceindex', 
						'url' => 'Invoice/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('filter', $request->filter, array('id' => 'filter')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	    {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	    {{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
	    {{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('topclick', $request->topclick, array('id' => 'topclick')) }}
		{{ Form::hidden('sortOptn',$request->invoicesort , array('id' => 'sortOptn')) }}
	    {{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
		{{ Form::hidden('ordervalue', $request->ordervalue, array('id' => 'ordervalue')) }}
		{{ Form::hidden('year_month', $date_month, array('id' => 'year_month')) }}
		{{ Form::hidden('searchmethod', $request->searchmethod, array('id' => 'searchmethod')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
		{{ Form::hidden('invoice_id', '', array('id' => 'invoice_id')) }}
		{{ Form::hidden('userid', '', array('id' => 'userid')) }}
		{{ Form::hidden('editflg', $request->editflg, array('id' => 'editflg')) }}
		{{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
		{{ Form::hidden('invoiceid', '', array('id' => 'invoiceid')) }}
		{{ Form::hidden('cust_id', $request->cust_id, array('id' => 'cust_id')) }}
		{{ Form::hidden('sendmailfrom', 'Invoice', array('id' => 'sendmailfrom')) }}
		{{ Form::hidden('estimate_id', '', array('id' => 'estimate_id')) }}
		{{ Form::hidden('currentRec', '', array('id' => 'currentRec')) }}
		{{ Form::hidden('invoicestatus', '', array('id' => 'invoicestatus')) }}
		{{ Form::hidden('invoicestatusid', '', array('id' => 'invoicestatusid')) }}
		{{ Form::hidden('companynameClick', $request->companynameClick, array('id' => 'companynameClick')) }}
		{{ Form::hidden('estid', '', array('id' => 'estid')) }}
		{{ Form::hidden('checkdefault', '', array('id' => 'checkdefault')) }}
		{{ Form::hidden('identEdit', 0, array('id' => 'identEdit')) }}

<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/invoices-icon-3.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_invoice') }}</h2>
		</div>
	</div>
	@if($request->searchmethod=="6" || $request->searchmethod=="")
	<div class="box100per pr10 pl10 mt10">
		<div class="mt10">
			{{ Helpers::displayYear_MonthEst($account_period, $year_month, $db_year_month, $date_month, $dbnext, $dbprevious,$last_year, $current_year, $account_val) }}
		</div>
	</div>
	@endif
<!-- End Heading -->
	<div class="col-xs-12 pm0 pull-left">
		
		<div class="col-xs-8 ml10 pm0 pull-left mt10">
			<a href="javascript:addedit('add','','{{ $request->mainmenu }}');"  class="btn btn-success box100"><span class="fa fa-plus"></span> {{ trans('messages.lbl_estregister') }}</a>
			<a href="javascript:invoiceexceldownload('{{$request->mainmenu}}', '{{ $date_month }}');"  class="btn btn-primary box125"><span class="fa fa-download"></span> {{ trans('messages.lbl_download') }}</a>
			<a href="javascript:fnassignemployee('{{ $date_month }}', '{{ $date_month }}');"  class="btn btn-warning box145"><span class="fa fa-plus"></span> {{ trans('messages.lbl_assignemployee') }}</a>
			<a href="javascript:fninvoicecopy('{{ $date_month }}');"  class="btn btn-primary box145"><span class="fa fa-plus"></span> {{ trans('messages.lbl_multiple') }}{{ trans('messages.lbl_copy') }}</a>
			<a href="javascript:allinvoiceexceldownload('{{$request->mainmenu}}');"  class="btn btn-primary box145"><span class="fa fa-download"></span> {{ trans('messages.lbl_invoice') }} {{ trans('messages.lbl_download') }}</a>
		</div>
		<!-- Session msg -->
			@if(Session::has('success'))
				<div class="alertboxalign" role="alert">
					<p class="alert {{ Session::get('alert', Session::get('type') ) }}">
		            {{ Session::get('success') }}
		          	</p>
				</div>
			@endif
			@php Session::forget('success'); @endphp
		<!-- Session msg -->
		<div class="col-xs-12 pm0 pull-left">
			<div class="box55per pm0 CMN_display_block pull-left">
				<a class="btn btn-link {{ $disabledall }}" href="javascript:filter('1');"> {{ trans('messages.lbl_all') }} </a>
				<span>|</span>
				<a class="btn btn-link {{ $disabledcreating }}" href="javascript:filter('2');"> {{ trans('messages.lbl_creating') }} </a>
				<span>|</span>
				<a class="btn btn-link {{ $disabledapproved }}" href="javascript:filter('3');"> {{ trans('messages.lbl_approved') }} </a>
				<span>|</span>
				<a class="btn btn-link {{ $disabledunused }}" href="javascript:filter('4');"> {{ trans('messages.lbl_unused') }} </a>
				<span>|</span>
				<a class="btn btn-link {{ $disabledsend }}" href="javascript:filter('5');"> {{ trans('messages.lbl_sent') }} </a>
			</div>
			<div class=" pm0 pr12">
				<div class="form-group pm0 pull-right moveleft nodropdownsymbol" id="moveleft">
				<a href="javascript:clearsearch()" title="Clear Search">
            		<img class="pull-left box30 mr5 " src="{{ URL::asset('resources/assets/images/clearsearch.png') }}">
          		</a>
					{{ Form::select('invoicesort', [null=>''] + $invoicesortarray, $request->invoicesort,
	                            array('class' => 'form-control'.' ' .$request->sortstyle.' '.'CMN_sorting pull-right',
	                           'id' => 'invoicesort',
	                           'style' => $sortMargin,
	                           'name' => 'invoicesort'))
	                }}
	            </div>
			</div>
		</div>
	</div>
		<div class="mr10 ml10">
		<div class="minh300">
			<table class="tablealternate box100per">
				<colgroup>
					<col width="5%">
					<col width="10%">
					<col width="">
					<col width="15%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader fwb tac"> 
			   			<th class="tac">{{ trans('messages.lbl_sno') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_invoiceno') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_Details') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_estamount') }}</th>
			   		</tr>
			   	</thead>
			   	<tbody>
			   		<?php $i=0; ?>
			   		@forelse($TotEstquery as $key => $data)
			   		{{--*/ $invoice_balance[$key] = Helpers::fnfetchinvoicebalance($data->id); /*--}}
			   			<tr>
							<td class="text-center">
								{{ ($TotEstquery->currentpage()-1) * $TotEstquery->perpage() + $i + 1 }}
								</br>
			 						{{ Form::checkbox($data->user_id, $data->id.'#'.$data->estimate_id,'',['id' => $data->user_id,'class' => 'checkboxid','style' => 'display:inline-block']) }}
							</td>
							<td class="tal pr10 vat pt5">
								<div class="">
									<label class="pm0 vam" style="color:#136E83;">
										{{ $data->user_id }}
									</label>
								</div>
								<div>
									<span class="estStatusDIV_New_1">
										
										@if($data->classification==0 && $data->del_flg==0)
											<span class="blue">{{ trans('messages.lbl_creating') }}</span>
										@elseif($data->classification==1 && $data->del_flg==0)
											<span class="orange">{{ trans('messages.lbl_approved') }}</span>
										@elseif($data->classification==2 && $data->del_flg==0)
											<span class="green">{{ trans('messages.lbl_sent') }}</span>
											
										@else
											<span class="red">{{ trans('messages.lbl_unused') }}</span>

										@endif
									</span>
								</div>
								<div>
									@if($copyFlag == 1)
										@if($data->copyFlg == 1)
											<span class="estStatusDIV_New_1">
													<span class="brown">({{ trans('messages.lbl_copied') }})</span>
											</span>
										@endif
									@endif
								</div>
							</td>
							<td>
						<div class="ml5 pt5">
							<div class="mb2">
								<a href="javascript:customernameclick('{{ $data->company_name }}');" class="blue">
									<b class="blue">{{$data->company_name}}</b>
								</a>
							</div>
							<div class="f12 vam label_gray boxhei24">
								<span class="f12"> 
									{{ trans('messages.lbl_dateofissue') }} :
								</span>
								<span class="f12">
									{{$data->quot_date}}
								</span>
								<span class="f12 ml20">
									{{ trans('messages.lbl_projecttitle') }} :
								</span>
								<span class="f12">
									{{$data->project_name}}
								</span>
								<span class="f12 ml20">
									{{ trans('messages.lbl_projecttype') }} :
								</span>
								<span class="f12">
									{{$data->ProjectType}}
								</span>
							</div>
							<div class="f12 vam label_gray boxhei24">
								<span class="f12"> 
									{{ trans('messages.lbl_paymentday') }} :
								</span>
								<span class="f12">
									{{$data->payment_date}}
								</span>
								<span class="f12 ml20">
									{{ trans('messages.lbl_Creater') }} :
								</span>
								<span class="f12">
									{{$data->created_by}}
								</span>
							</div>
						</div>
						<div class="ml5 mb2 smallBlue CMN_display_block">
							<div class="CMN_display_block">
								<a href="javascript:underconstruction();" class="anchorstyle">{{ trans('messages.lbl_estimation') }}</a>&nbsp;<span class="ml3">|</span>
							</div>
							<div class="CMN_display_block">
								<a href="javascript:underconstruction();" class="anchorstyle">{{ trans('messages.lbl_purchaseorder') }}</a>&nbsp;<span class="ml3">|</span>
							</div>
							<div class="CMN_display_block ml3">
								@if($data->pdf_flg==0)
            					<img class="pull-left box15 mt5" id="{{ $data->id}}pdfimg" src="{{ URL::asset('resources/assets/images/nopdf.png') }}">
								@else
            					<img class="pull-left box15 mt5" id="{{ $data->id}}pdfimg" src="{{ URL::asset('resources/assets/images/pdf.png') }}">
								@endif
								{{ Form::hidden('pdfflag', '', array('id' => 'pdfflag')) }}
								<a href="javascript:newpdf('{{ $data->id }}','{{ $data->user_id }}','{{ $data->pdf_flg }}','{{ $data->id}}pdfimg','{{ $request->mainmenu }}','{{ $data->trading_destination_selection }}');"  class="anchorstyle ml3">{{ trans('messages.lbl_invoice') }}</a>&nbsp;<span class="ml3">|</span>
							</div>
							<div class="CMN_display_block ml3">
								{{ trans('messages.lbl_packingslip') }}&nbsp;<span class="ml3">|</span>
							</div>
							<div class="CMN_display_block ml3">
								{{ trans('messages.lbl_receipt') }}&nbsp;<span class="ml3">|</span>
							</div>
							<div class="CMN_display_block ml3">
								{{ trans('messages.lbl_Others') }}&nbsp;
							</div>
						</div>
						<div class="ml5 mb2 smallBlue">
							<div class="CMN_display_block">
							@if($data->paid_status != 1 && $data->classification==0)
								<a href="javascript:gotoinvoiceedit('{{ $data->id }}','{{ $request->mainmenu }}','{{ $key+1 }}');" class="anchorstyle">{{ trans('messages.lbl_edit') }}</a>&nbsp;<span class="ml3">|</span>
							@else
								<span>{{ trans('messages.lbl_edit') }}</span>&nbsp;<span class="ml3">|</span>
							@endif
							</div>
							<div class="CMN_display_block">
								<a href="javascript:gotoinvoicedetails('{{ $data->id }}','{{ $request->mainmenu }}','{{ $key+1 }}');" class="anchorstyle">{{ trans('messages.lbl_Details') }}</a>&nbsp;<span class="ml3">|</span>
							</div>
							<div class="CMN_display_block">
								@if($data->mailFlg=="0")
            					<img class="pull-left box15 mt2" id="{{ $data->id}}pdfimg" src="{{ URL::asset('resources/assets/images/nosendmail.png') }}">
								@else
            					<img class="pull-left box18 mt1" id="{{ $data->id}}pdfimg" src="{{ URL::asset('resources/assets/images/sendmail.png') }}">
								@endif
								<a <?php if($data->pdf_flg==1) { ?> href="javascript:sendmail('{{ $data->id }}','{{ $data->trading_destination_selection}}','{{ $data->user_id}}');" class="anchorstyle ml3 csrp" <?php } else { ?>  class="black disabled tdn pl3 cur_default" <?php } ?> id="sendemail{{ $data->id }}">{{ trans('messages.lbl_email') }}</a>&nbsp;<span class="ml3">|</span>
							</div>
							<div class="CMN_display_block ml3">
								<a href="javascript:fnpaymentaddedit('{{ $data->id }}');" class="anchorstyle ml3">{{ trans('messages.lbl_payment') }}</a>&nbsp;<span class="ml3">|</span>
							</div>
							<div class="CMN_display_block ml3">
								{{ trans('messages.lbl_information') }}&nbsp;<span class="ml3">|</span>
							</div>
							<div class="CMN_display_block ml3 dropdown">
								<a href="#" style="text-decoration: none !important;" class="anchorstyle">{{ trans('messages.lbl_Others') }}</a>
								<div class="CMN_display_block" >
									<img class="pull-left box12 CMN_display_block" id="{{ $data->id}}pdfimg" src="{{ URL::asset('resources/assets/images/downarrowothers.png') }}">
								</div>
								<div class="dropdown-content ml10" style="border: 1px solid grey;">
									<?php for ($ot=0; $ot < count($othersArray); $ot++) { ?>
										<?php if ($ot!=$data->classification) {?>
											<a href="javascript:invoicestatus('{{ $data->id }}', '{{ $ot }}');" style="text-decoration: none;border-bottom: 1px solid grey;font-size: 12px;">{{ $othersArray[$ot] }}</a>
										<?php } ?>
									<?php } ?>
							  	</div>
							</div>
						</div>
					</td>
					<td class="" align="right" style="padding-right: 5px;">
								{{ $data->totalval }}
			   					<?php  $totalval += preg_replace('/,/', '', $data->totalval); ?>
			   					{{--*/ $getTaxquery = Helpers::fnGetTaxDetails($data->quot_date); /*--}}
							<?php 
									if(!empty($data->totalval)) {
										if($data->tax != 2) {
			   								$totroundval = preg_replace("/,/", "", $data->totalval);
			   								$dispval = (($totroundval * intval((isset($getTaxquery[0]->Tax)?$getTaxquery[0]->Tax:0)))/100);
			   								$dispval1 = number_format($dispval);
			   								$grandtotal = $totroundval + $dispval;
			   							} else {
			   								$totroundval = preg_replace("/,/", "", $data->totalval);
											$dispval = 0;
											$grandtotal = $totroundval + $dispval;
											$dispval1 = $dispval;
										}
									}
			   						$grand_total = number_format($grandtotal);
									$divtotal += str_replace(",", "",$grand_total);

									if ($data->paid_status != 1) {
										$grand_style = "style='font-weight:bold;color:red;'";
										$balance += $grandtotal;
									} else {
										$grand_style = "style='font-weight:bold;color:green;'";
										$paid_amo += $grandtotal;
									}
									if($data->paid_status == 1) {
										$pay_balance = str_replace(",", "",(isset($invoice_balance[$key][0]->totalval)?$invoice_balance[$key][0]->totalval:0));
										$gr_total = number_format($grandtotal);
										$grand_tot = str_replace(",", "",$gr_total);
										$paid_amount += (isset($invoice_balance[$key][0]->deposit_amount)?$invoice_balance[$key][0]->deposit_amount:0);
										$bal_amount = $divtotal-$paid_amount;
									}
									if($data->paid_status != 1) {
										$gr_total = number_format($grandtotal);
										$grand_tot = str_replace(",", "",$gr_total);
										$bal_amount = $divtotal-$paid_amount;
									}
			   						if(isset($invbal[$key])) {
			   							if($invbal[$key]['bal_amount'] > 0) {
			   								$balance_style = "style='font-weight:bold;color:red;'";
			   							} else {
			   								$balance_style = "style='font-weight:bold;color:green;'";
			   							}
			   						}
			   				?>
			   					@if(!empty($data->totalval))
			   					<div class="ml5 mb2 smallBlue">
			   					<?php echo "<span style='background-color:#136E83;color:white;'>"; ?> {{trans('messages.lbl_tax')}}<?php echo"</span>&nbsp;" . $dispval1;$dispval1 = ''; ?>
			   					</div>
			   					@endif
			   					<div class="ml5 mb2 smallBlue" <?php echo $grand_style; ?>>
			   						{{ number_format($grandtotal) }}
			   						@php $grandtotal=0; @endphp
			   					</div>
			   					<div class="ml5 mb2 smallBlue" <?php echo $balance_style; ?>>
			   								@if(isset($invbal[$key]))
			   									@if($invbal[$key]['bal_amount'] > 0)
			   										@if($invbal[$key]['bal_amount']==0)
			   										@else
			   										<span class="vat font-s15">△</span>
			   										{{ number_format($invbal[$key]['bal_amount']) }}
			   										@endif
			   									@else
			   										@if($invbal[$key]['bal_amount']==0)
			   										@else
			   										<span class="font-s20">●</span>
			   										{{ number_format($invbal[$key]['bal_amount']) }}
			   										@endif
			   									@endif
			   								@endif
			   					</div>
					</td>
						</tr>
						<?php $i=$i+1; ?>
			   		@empty
						<tr>
							<td class="text-center" colspan="4" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
						</tr>
					@endforelse
			   	</tbody>
			</table>
		</div>
		<div class="text-center">
			@if(!empty($TotEstquery->total()))
				<span class="pull-left mt24">
					{{ $TotEstquery->firstItem() }} ~ {{ $TotEstquery->lastItem() }} / {{ $TotEstquery->total() }}
				</span>
			@endif 
			{{ $TotEstquery->links() }}
			<div class="CMN_display_block flr mr0">
          		{{ $TotEstquery->linkspagelimit() }}
        	</div>
		</div>
		</div>
		<!-- SEARCH -->
        <div style="top:134px;position: fixed;" 
        			@if ($request->searchmethod == 1 || $request->searchmethod == 2) 
                    	class="open CMN_fixed pm0" 
                   	@else 
                    	class="CMN_fixed pm0 pr0" 
                   	@endif 
                    	id="styleSelector">
             <div class="selector-toggle">
              	<a id="sidedesignselector" href="javascript:void(0)"></a>
          	</div>
          	<div class="scrollbar">
          	<div style="background-color:#136E83;color: white;">
            		<ul class="ml5">	
              			<span>
                  			<li class="">
                  				<div class="mt8" style="display: inline-block;">
                      			<span class="mt0">{{ trans('messages.lbl_targetdate') }} :</span>
                  				</div>
                  				<div style="display: inline-block;">
                  					<span>{{ $date_month }}</span>
                  				</div>
                  			</li>
                  			<li class="">
                  				<div style="display: inline-block;">
                      				<span class="mt10">{{ trans('messages.lbl_claimtotal') }} : </span>
                  				</div>
                  				<div style="display: inline-block;">
                  					<label>{{ number_format($divtotal) }}</label>
                  				</div>
                  			</li>
                  			<li>
                  			<div class="box235 border_btm_solid_line"></div>
                  			</li>
                  			<li>
                  				<div style="display: inline-block;">
                  					<span>Paid Amount : </span>
                  				</div>
                  				<div style="display: inline-block;">
                  					<label>{{ number_format($paid_amount) }}</label>
                  				</div>
                  			</li>
                  			<li>
                  				<div style="display: inline-block;">
                  					<span>Balance Amount : </span>
                  				</div>
                  				<div style="display: inline-block;">
                  				@if($bal_amount==0)
                  					<label style="color: white;">{{ number_format($bal_amount) }}</label>
                  				@else
                  					<label style="color: pink;">{{ number_format($bal_amount) }}</label>
                  				@endif
                  				</div>
                  			</li>
              			</span>
                			<li class="theme-option ml6">
                  				<div class="box100per mt5">
                  				<div>
                			</li>
            		</ul>
            		<ul>
            		</ul>
            		</div>
            <ul>
              <span>
                  <li style="">
                      <p class="selector-title">{{ trans('messages.lbl_search') }}</p>
                  </li>
              </span>
                <li class="theme-option ml6">
                  <div class="box100per mt5" onKeyPress="return checkSubmitsingle(event)">
                    {!! Form::text('singlesearch', $request->singlesearch,
                          array('','class'=>' form-control box80per pull-left','style'=>'height:30px;','id'=>'singlesearch')) !!}

                    {{ Form::button('<i class="fa fa-search" aria-hidden="true"></i>', 
                        array('class'=>'ml5 mt2 pull-left search box15per btn btn-info btn-sm', 
                              'type'=>'submit',
                              'name' => 'advsearch',
                              'onclick' => 'javascript:return usinglesearch()',
                              'style'=>'border: none;' 
                              )) }}
                  <div>
                </li>
            </ul>
            <div class="mt5 ml10 pull-left mb5">
              <a href="#demo" onclick="mulclick('demo');" style="font-family: arial, verdana;">{{ trans('messages.lbl_multi_search') }}
              </a>
            </div>
            <ul id="demo" @if ($request->searchmethod == 2) class="collapse in ml5 pull-left" 
                          @else class="collapse ml5 pull-left" @endif>
                <li class="theme-option" onKeyPress="return checkSubmitmulti(event)">
                  <span class="mt2" style="font-family: arial, verdana;">{{ trans('messages.lbl_invoiceno') }}</span>
                  <div class="mt5 box88per">
                      {!! Form::text('msearchusercode', $request->msearchusercode,
                        				array('','id' => 'msearchusercode',
                        				'style'=>'height:30px;', 
                        				'class'=>'form-control box93per')) !!}
                  </div>
                  <div class="mt5">
                    <span class="pt3" style="font-family: arial, verdana;">{{ trans('messages.lbl_customer') }}</span>
                   	<div class="mt5 box88per">
                      {!! Form::text('msearchcustomer', $request->msearchcustomer,
                        				array('','id' => 'msearchcustomer',
                        				'style'=>'height:30px;',
                        				'class'=>'form-control box93per')) !!}
                  	</div>
                  </div>
                  <div class="mt5">
                    <span class="pt3" style="font-family: arial, verdana;">{{ trans('messages.lbl_daterange') }}</span>
                   <div class="mt5 box100per">
                   		{!! Form::text('msearchstdate', $request->msearchstdate,
                                array('',
                                    'id'=>'msearchstdate',
                                    'style="font-size:13px;"', 'data-placement'=>'left', 
                                    'onKeyPress'=>'return event.charCode >= 48 && event.charCode <= 57',
                                    'class'=>'form-control box40per pull-left from_date'
                                    )) !!}
                  <label class="ml1 mt8 fa fa-calendar fa-lg pull-left" for="msearchstdate" aria-hidden="true"></label>
                  		{!! Form::text('msearcheddate', $request->msearcheddate,
                                 array('',
                                 'style="font-size:13px;"', 'data-placement'=>'left', 
                                 'onKeyPress'=>'return event.charCode >= 48 && event.charCode <= 57',
                                 'class'=>'form-control box40per pull-left to_date',
                                 'id'=>'msearcheddate')) !!}
                  <label class="ml1 mt8 fa fa-calendar fa-lg" for="msearcheddate" aria-hidden="true"></label>
                    </div>
                </div>
                <div class="mt6">
                   <span class="pt3" style="font-family: arial, verdana;">{{ trans('messages.lbl_projecttype') }}</span>
                   <div class="mt5 box100per" style="display: inline-block;">
                   		{{ Form::select('protype1',[null=>'']+ [1=>'ALL'] + $selectboxtext,null, 
									array('name' => 'protype1',
										  'id'=>'protype1',
										  'onchange' => 'mainchange(this.value);',
										  'style'=>'max-width: 45%;background-color:white;',
										  'class'=>'pl5'))}}
                  		{{ Form::select('protype2',[null=>'']+[999=>'Not Included'],'', 
									array('name' => 'protype2',
										  'id'=>'protype2',
										  'style'=>'max-width: 45%;background-color:white;',
										  'class'=>'pl5'))}}
                    </div>
                </div>
            <div class="mt5 mb6">
                 {{ Form::button(
                     '<i class="fa fa-search" aria-hidden="true"></i> '.trans('messages.lbl_search'),
                     array('class'=>'mt10 btn btn-info btn-sm',
                     		'onclick' => 'javascript:return umultiplesearch()',
                           	'type'=>'button')) 
                 }}
            </div>
                </li>
            </ul>
        </div>
        </div>
		{{ Form::hidden('totalrecords', $TotEstquery->total(), array('id' => 'totalrecords')) }}
        <!-- END SEARCH -->
	{{ Form::close() }}
	{{ Form::open(array('name'=>'frminvoiceexceldownload', 
						'id'=>'frminvoiceexceldownload', 
						'url' => 'Invoice/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	{{ Form::hidden('selYearMonth', '', array('id' => 'selYearMonth')) }}
	{{ Form::close() }}

	{{ Form::open(array('name'=>'frmallinvoiceexceldownload', 
						'id'=>'frmallinvoiceexceldownload', 
						'url' => 'Invoice/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('hdn_invoice_arr', '', array('id' => 'hdn_invoice_arr')) }}
	{{ Form::close() }}
</article>
</div>
<script type="text/javascript">
	var recordTotal = '<?php echo $TotEstquery->total(); ?>';
	$('#totalrecords').val(recordTotal);
</script>
@endsection