@extends('layouts.app')
@section('content')
@php use App\Http\Helpers @endphp
{{ HTML::script('resources/assets/js/billing.js') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
<style type="text/css">
	.sort_asc {
		background-image:url({{ URL::asset('resources/assets/images/upArrow.png') }}) !important;
	}
	.sort_desc {
		background-image:url({{ URL::asset('resources/assets/images/downArrow.png') }}) !important;
	}
	.green_box {
		background-color: green;
		width:10px;
		height:10px;
		border:1px solid green;
	}
	.red_box {
		background-color: red;
		width:10px;
		height:10px;
		border:1px solid red;
	}
	.black_box {
		background-color: grey;
		width:10px;
		height:10px;
		border:1px solid grey;
	}
	.alertboxalign {
		margin-bottom: -10px !important;
	}
	.alert {
		display:inline-block !important;
		height:30px !important;
		padding:5px !important;
	}
</style>
<script type="text/javascript">
    var datetime = '<?php echo date('Ymdhis'); ?>';
</script>
<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="sales" class="DEC_flex_wrapper " data-category="sales sales_sub_6">
	{{ Form::open(array('name'=>'billingfrm', 
						'id'=>'billingfrm', 
						'url' => 'Billing/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
     	{{ Form::hidden('scrname', $request->scrname ,array('id' => 'scrname')) }}
     	{{ Form::hidden('mainmenu', $request->mainmenu ,array('id' => 'mainmenu')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('pageclick', $request->pageclick, array('id' => 'pageclick')) }}
		{{ Form::hidden('plimit', $request->plimit, array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('sortOptn',$request->billsort, array('id' => 'sortOptn')) }}
	 	{{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
	 	{{ Form::hidden('ordervalue', $request->ordervalue, array('id' => 'ordervalue')) }}
	 	<!-- History -->
	 	{{ Form::hidden('empid', '' , array('id' => 'empid')) }}
	 	{{ Form::hidden('empname', '' , array('id' => 'empname')) }}
	 	<!-- Details & Register -->
	 	{{ Form::hidden('newrec', '' , array('id' => 'newrec')) }}
	 	{{ Form::hidden('addbillregflg', '' , array('id' => 'addbillregflg')) }}
	 	{{ Form::hidden('hdnbranchname', '' , array('id' => 'hdnbranchname')) }}
	 	{{ Form::hidden('hdnempid', '' , array('id' => 'hdnempid')) }}
	 	{{ Form::hidden('hdnnickname', '' , array('id' => 'hdnnickname')) }}
	 	{{ Form::hidden('hdncustname', '' , array('id' => 'hdncustname')) }}
	 	{{ Form::hidden('hdnstartdate', '' , array('id' => 'hdnstartdate')) }}
	 	{{ Form::hidden('hdncusid', '' , array('id' => 'hdncusid')) }}
	 	{{ Form::hidden('sorting', '' , array('id' => 'sorting')) }}
	 	{{ Form::hidden('hdnlastname', '' , array('id' => 'hdnlastname')) }}
	 	{{ Form::hidden('hdnback', 1, array('id' => 'hdnback')) }}

		<!-- Start Heading -->
		<div class="row hline">
			<div class="col-xs-12">
				<img class="pull-left box35 mt15" 
					src="{{ URL::asset('resources/assets/images/billing.png') }}">
				<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_billinglist') }}</h2>
			</div>
		</div>
		<div class="pb10"></div>
		<!-- End Heading -->
		<div class="box100per pr10 pl10 mt10">
		<div class="mt10">
		</div>
		</div>
		<div class="col-xs-12 pm0 pull-left">
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
		<div class="box100per pr10 pl10">
			<div style="margin-top: -10px;">
				{{ Helpers::displayYear_Monthtimesheet($account_period, $year_month, $db_year_month, $date_month, $dbnext, $dbprevious, $last_year, $current_year, $account_val) }}
			</div>
		</div>
			<div class="col-xs-12 pm0 pull-left mb10 pl10">
			<a class="pull-left mb5" href="javascript:staffselectpopup();">
          	<img class="box19 mt22" src="{{ URL::asset('resources/assets/images/edit.png') }}"></a>
			<a href="javascript:staffselectpopup();" class="pull-left pr10 mt22 ml5 anchorstyle" title="{{ trans('messages.lbl_cempsel') }}">
			{{ trans('messages.lbl_cempsel') }}
		</a>
			@if($get_pre_value != '1')
			<a class="pull-left mb5" href="javascript:selectpreviousdetails();">
          	<img class="box19 mt22" src="{{ URL::asset('resources/assets/images/edit.png') }}"></a>
			<a href="javascript:selectpreviousdetails();" class="pull-left pr10 mt22 ml5 anchorstyle" title="{{ trans('messages.lbl_getprevdetail') }}">
			{{ trans('messages.lbl_getprevdetail') }}</a>
			@endif
			{{ Form::select('billsort', $array, $request->billsort,
	                            array('class' => 'form-control'.' ' .$request->sortstyle.' '.'CMN_sorting pull-right mt10 mr10',
	                           'id' => 'billsort',
	                           'style' => $sortMargin,
	                           'name' => 'billsort'))
	                }}
			</div>
		</div>
		<div class="minh400 box100per pl10 pr10">
			<table class="tablealternate box100per CMN_tblfixed">
				<colgroup>
					<col width="4%">
					<col width="8%">
					<col width="15%">
					<col width="10%">
					<col width="12%">
					<col width="8%">
					<col width="5%">
					<col width="8%">
					<col width="8%">
					<col width="8%">
					<col width="5%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam">{{ trans('messages.lbl_empno') }}</th>
					<th class="vam">{{ trans('messages.lbl_client_name') }}</th>
					<th class="vam">{{ trans('messages.lbl_branch') }}</th>
					<th class="vam">{{ trans('messages.lbl_name') }}</th>
					<th class="vam">{{ trans('messages.lbl_totalhrs') }}</th>
					<th class="vam">{{ trans('messages.lbl_OT') }}</th>
					<th class="vam">{{ trans('messages.lbl_amount') }}</th>
					<th class="vam">{{ trans('messages.lbl_OTAmt') }}</th>
					<th class="vam">{{ trans('messages.lbl_TotalAmt') }}</th>
					<th class="vam">{{ trans('messages.lbl_Caldone') }}</th>
				</thead>
				<tbody>
						{{ $temp = ""}}
                  		{{--*/ $row = '0' /*--}}
                  		{{ $tempcomp = ""}}
                  		{{ $tempcomp1 = ""}}
                  		{{--*/ $rowcomp = '0' /*--}}
                  		{{--*/ $rowcomp1 = '0' /*--}}
                  		{{ $tempold = ""}}
                  		{{--*/ $rowold = '0' /*--}}
		   			<?php $style_tr="";$style_tdold=""; ?>
		   			 @if(count($displayArray)!="")
			   		 @for ($i = 0; $i < count($displayArray); $i++)
			   			{{--*/ $loc = $displayArray[$i]['Emp_ID'] /*--}}
			   			{{--*/ $loccomp = $displayArray[$i]['customer_name'] /*--}}
			   			{{--*/ $loccomp1 =$displayArray[$i]['branch_name'] /*--}}
			   			{{--*/ $locold = $totamt_sql[0]->TotalAmount /*--}}
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
                      @if($loccomp1 != $tempcomp1) 
                        @if($rowcomp1==1)
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
                       @if($totamt_sql[0]->TotalAmount)
                      @if($locold != $tempold) 
                        @if($rowold==1)
                          {{--*/ $style_trold = 'background-color: #E5F4F9;' /*--}}
                          {{--*/ $rowold = '0' /*--}}
                        @else
                          {{--*/ $style_trold = 'background-color: #FFFFFF;' /*--}}
                          {{--*/ $rowold = '1' /*--}}
                        @endif
                        {{--*/ $style_tdold = 'border-bottom:none;' /*--}}
                      @else
                        {{--*/ $style_tdold = 'border-top:none;' /*--}}
                      @endif
                      @else
                      	{{--*/ $style_tdold = '' /*--}}
                      @endif
						<tr style="{{$style_tr}}">
                    	<td class="text-center">
                    		{{ ($g_query->currentpage()-1) * $g_query->perpage() + $i + 1 }}
                    	</td>
                    	<td style="{{$style_td}}" class="tac" title="{{ $displayArray[$i]['Emp_ID'] }}">
                    		<a class="colbl fwb" style="text-decoration: none;"  
                    			href="javascript:billHistory('{{ $displayArray[$i]['Emp_ID'] }}',
                    			'{{ $displayArray[$i]['nickname'] }}');">
                    			@if($loc!=$temp)
			   						{{ $displayArray[$i]['Emp_ID'] }}
			   					@endif
                    		</a>
			   			</td>
                    	<td style="{{$style_td}}" title="{{ $displayArray[$i]['customer_name'] }}">
                    			@if($loccomp != $tempcomp)
			   						{{ $displayArray[$i]['customer_name'] }}
			   					@endif
                    	</td>
                    	<td style="{{$style_td}}" title="{{ $displayArray[$i]['branch_name'] }}">
                    			@if($loccomp1 != $tempcomp1)
			   						{{ $displayArray[$i]['branch_name'] }}
			   					@endif
                    	</td>
                    	<td title ="{{ $displayArray[$i]['nickname'] }}" @if(mb_strlen($displayArray[$i]['nickname'], 'UTF-8') > 13)
							 title ="{{ $displayArray[$i]['nickname'] }}" @endif>
							<a class="colbl" style="text-decoration: none;" name="namevalue" 
							href="javascript:Addnewbillingdetails('{{ $displayArray[$i]['Emp_ID'] }}',
                    										'{{ $displayArray[$i]['customer_name'] }}',
                    										'{{ $displayArray[$i]['TotalHrs'] }}',
                    										'{{ $displayArray[$i]['nickname'] }}',
                    										'{{ $displayArray[$i]['start_date'] }}',
                    										'{{ $displayArray[$i]['customer_id'] }}',
                    										'{{ $displayArray[$i]['branch_name'] }}',
                    										'{{ $displayArray[$i]['branch_id'] }}',
                    										'{{ $displayArray[$i]['branch_name'] }}',
                    										'{{ $request->sorting }}',
                    										'{{ $displayArray[$i]['Amount'] }}','');">
                    		@if(mb_strlen($displayArray[$i]['nickname'], 'UTF-8') > 13)
								@php echo mb_substr($displayArray[$i]['nickname'], 0, 12, 'UTF-8')."..." @endphp
							@else
								{{ $displayArray[$i]['nickname'] }}
							@endif
							</a>
                    	</td>
                    	<td class="text-right">
                    		@if(!empty($displayArray[$i]['date']))
								@php echo floatval($displayArray[$i]['TotalHrs']) @endphp
							@endif
                    	</td>
                    	<td class="text-right pr10">
                    		@if(!empty($displayArray[$i]['date']))
                    			@if($displayArray[$i]['TotalHrs'] != "" 
                    				&& $displayArray[$i]['TotalHrs'] != 0)
									@if($displayArray[$i]['TotalHrs'] < $displayArray[$i]['MinHrs'])
										@php $OT = $displayArray[$i]['TotalHrs'] - $displayArray[$i]['MinHrs'] @endphp
													{{ floatval($OT) }}
									@elseif($displayArray[$i]['TotalHrs'] > $displayArray[$i]['MaxHrs'])
										@php $OT = $displayArray[$i]['TotalHrs'] - $displayArray[$i]['MaxHrs'] @endphp
													{{ floatval($OT) }}
									@else
									{{ "" }}
									@endif
								@endif
							@else
								{{ "" }}
							@endif
                    	</td>
                    	<td class="text-right pr10">
                    		@if(!empty($displayArray[$i]['date']))
								{{$displayArray[$i]['Amount']}}
							@endif
                    	</td>
                    	<td class="text-right pr15">
                    		@if(!empty($displayArray[$i]['date']))
								@php $dispOTAmount = preg_replace('/,/', '', $displayArray[$i]['OTAmount']) @endphp
									@if(substr($displayArray[$i]['OTAmount'], 0, 1) === '-')
										<span class="red pr3">
											@if(!empty($dispOTAmount))
												@php echo number_format($dispOTAmount) @endphp
											@else
												{{ '0' }}
											@endif
										</span>
									@else
											@if(!empty($dispOTAmount))
												@php echo number_format($dispOTAmount) @endphp
											@else
												{{ '0' }}
											@endif
									@endif
							@endif
                    	</td>
                    	<td class="text-right pr10">
                    		@if(!empty($displayArray[$i]['date']))
								{{$displayArray[$i]['TotalAmount']}}
							@endif
                    	</td>
                    	<td align="center">
        					@if(($fut_1month_link == $date_month 
							|| $fut_2month_link == $date_month || $cur_year_month == $date_month ) && $get_pre_value == 1 && $displayArray[$i]['tcheckcalc'] == 0)
								<div class="CMN_div_inblock boxhei12 box40per">
								<a class="color_blue" style="text-decoration: none;" name="namevalue" 
								href="javascript:Addnewbillingdetails('{{ $displayArray[$i]['Emp_ID'] }}',
                    										'{{ $displayArray[$i]['customer_name'] }}',
                    										'{{ $displayArray[$i]['TotalHrs'] }}',
                    										'{{ $displayArray[$i]['nickname'] }}',
                    										'{{ $displayArray[$i]['start_date'] }}',
                    										'{{ $displayArray[$i]['customer_id'] }}',
                    										'{{ $displayArray[$i]['branch_name'] }}',
                    										'{{ $displayArray[$i]['branch_id'] }}',
                    										'{{ $displayArray[$i]['branch_name'] }}',
                    										'{{ $request->sorting }}',
                    										'{{ $displayArray[$i]['Amount'] }}','1')">
									<img class="box19" src="{{ URL::asset('resources/assets/images/edit.png') }}">
									 </a>
								</div>
                    		@else
								@if(!empty($displayArray[$i]['date']))
									@if($displayArray[$i]['tcheckcalc'] == 1)
									<div class="green_box"></div>
									@elseif($displayArray[$i]['tcheckcalc'] == 2)
									<div class="red_box"></div>
									@endif
								@endif
								@if($displayArray[$i]['Amount'] != "" && 
										 $displayArray[$i]['TotalHrs'] == "" && 
										 $displayArray[$i]['OTAmount'] == "" && 
										 $displayArray[$i]['tcheckcalc'] == 0)
										<div class="black_box"></div>
								@endif
							@endif
							</td>
                    	</td>
                    	</tr>
                    {{--*/ $temp = $loc /*--}}
                    {{--*/ $tempcomp = $loccomp /*--}}
                    {{--*/ $tempcomp1 = $loccomp1 /*--}}
                    @endfor
					@else
						<tr>
							<td class="text-center" colspan="11" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
						</tr>
					@endif	
				</tbody>
		</table>
	@if(count($displayArray) != '0')
		<table width="94.8%" cellspacing="0" cellpadding="0" style="margin-top: -5px;">
			<tr>
				<td width="51.8%" class="CMN_bdrnone"></td>
				<td width="40%" class="CMN_bdrnone">
					<table width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td align="right" width="16.2%" class="CMN_tbltheadcolor fwb">
								{{ trans('messages.lbl_total') }}
							</td>
							<td align="right" width="10.1%" style="padding-right:10px !important;">
								@if(number_format($totamt_sql[0]->Amount) != 0)
									@php echo number_format($totamt_sql[0]->Amount) @endphp
								@endif
							</td>
							<td align="right" width="10%" style="padding-right:10px !important;">
								@if(number_format($totamt_sql[0]->OTAmount) != 0)
									@php echo number_format($totamt_sql[0]->OTAmount) @endphp
								@endif
							</td>
							<td align="right" width="10%" style="padding-right:10px !important;">
								@if(number_format($totamt_sql[0]->TotalAmount) != 0)
									@php echo number_format($totamt_sql[0]->TotalAmount) @endphp
								@endif
							</td>
						</tr>
						<tr>
							<td align="right" class="CMN_tbltheadcolor fwb">{{ trans('messages.lbl_Tax') }}</td>
							<td align="right" style="padding-right:10px !important;">
								@php 

									$tax = (($totamt_sql[0]->Amount * intval($tax_sal[0]->Tax))/100);
									echo $taxDetail = number_format($tax);
								@endphp
							</td>
							<td></td>
							<td align="right" style="padding-right:10px !important;">
								@php
									$totaltax = (($totamt_sql[0]->TotalAmount * intval($tax_sal[0]->Tax))/100);
									echo $taxDetail = number_format($totaltax);
								@endphp
							</td>
						</tr>
						<tr>
							<td align="right" class="CMN_tbltheadcolor fwb">
								{{ trans('messages.lbl_grandtot') }}
							</td>
							<td align="right" class="word_wrap dotted_border" style="padding-right:10px !important;">
								@php echo $gtotal = number_format($totamt_sql[0]->Amount + $tax) @endphp
							</td>
							<td class="word_wrap dotted_border"></td>
							<td align="right" class="word_wrap dotted_border" style="padding-right:10px !important;">
								@php echo $gtotal = number_format($totamt_sql[0]->TotalAmount + $totaltax)
								@endphp
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	<div class="text-center pl12">
		@if(!empty($g_query->total()))
			<span class="pull-left mt24">
				{{ $g_query->firstItem() }} ~ {{ $g_query->lastItem() }} / {{ $g_query->total() }}
			</span>
		@endif 
		{{ $g_query->links() }}
		<div class="CMN_display_block flr">
			{{ $g_query->linkspagelimit() }}
		</div>
	</div>
	@endif
	{{ Form::close() }}
	</article>
</div>
<div id="staffselectpopup" class="modal fade">
    <div id="login-overlay">
        <div class="modal-content">
            <!-- Popup will be loaded here -->
        </div>
    </div>
</div>
<script type="text/javascript">
	$('#scrname').val("billingindex");
</script>
@endsection