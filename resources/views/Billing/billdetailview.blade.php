@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/billing.js') }}
<style type="text/css">
	.tabellabel {
		color: #136E83;
		font-weight: bold;
		text-align: right;
		padding-right: 10px;
	}
	.editdisable {
	height: 19px;
	width: 18px;
	border: none;
	cursor: pointer;
	background-position: -173px -107px;
}
.alertboxalign {
    	margin-bottom: -50px !important;
	}
	.alert {
	    display:inline-block !important;
	    height:30px !important;
	    padding:5px !important;
	}
	.fb{
		color: gray !important;
	}
</style>
<script type="text/javascript">
    var datetime = '<?php echo date('Ymdhis'); ?>';
</script>
<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper " data-category="sales sales_sub_6">
	{{ Form::open(array('name'=>'billdetailfrm', 
						'id'=>'billdetailfrm', 
						'url' => 'Billing/billdetailview?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
     	{{ Form::hidden('scrname', '' ,array('id' => 'scrname')) }}
     	{{ Form::hidden('upcheckval', '' ,array('id' => 'upcheckval')) }}
     	{{ Form::hidden('hdnempidchk', '' ,array('id' => 'hdnempidchk')) }}
     	{{ Form::hidden('hdnempid', '' ,array('id' => 'hdnempid')) }}
	 	{{ Form::hidden('hdnnickname', '' , array('id' => 'hdnnickname')) }}
     	{{ Form::hidden('copybillregflg', '' ,array('id' => 'copybillregflg')) }}
     	{{ Form::hidden('editbillregflg', '' ,array('id' => 'editbillregflg')) }}
     	{{ Form::hidden('editbillregidchk', '' ,array('id' => 'editbillregidchk')) }}
     	{{ Form::hidden('mainmenu', $request->mainmenu ,array('id' => 'mainmenu')) }}
     	{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
    	{{ Form::hidden('startdate', '' ,array('id' => 'startdate')) }}
     	<!-- Start Heading -->
		<div class="row hline">
			<div class="col-xs-12">
				<img class="pull-left box35 mt15" 
					src="{{ URL::asset('resources/assets/images/billing.png') }}">
				<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_billingdetails') }}&nbsp;&nbsp;
				<span class="blue fwb">
					@if(Session::get('languageval') == "en")
						{{ $request->selYear.'&nbsp;/&nbsp;'.$request->selMonth }}
					@else
						{{ $request->selYear.'年'.$request->selMonth.'月' }}
					@endif
				</span></h2>
			</div>

		</div>
		<div class="pb10">
		</div>
		@if(Session::has('success'))
				<div align="center" class="alertboxalign" role="alert">
					<p class="alert {{ Session::get('alert', Session::get('type') ) }}">
		            {{ Session::get('success') }}
		          	</p>
				</div>
					@endif
					@php Session::forget('success'); @endphp
		<!-- End Heading -->
		<div class="pl5 pr5">
			<div class="pull-left ml10">
				<a href="javascript:goindexpage('{{ $request->mainmenu }}');" class="pageload btn btn-info box80"><span class="fa fa-arrow-left"></span> {{ trans('messages.lbl_back') }}</a>
			</div>
			<div class="pull-right pr10 mt10">
				<input type="checkbox" name="chkval" id="chkval" 
				onclick="return fnCalcDone(this,
										'{{ $result_query[0]->id }}',
										'{{ $result_query[0]->Empno }}',
										'{{ $result_query[0]->branch_id }}',
										'{{ $result_query[0]->date }}');"
							@if ($result_query[0]->tcheckcalc == 1) {{ "checked" }} @endif >
						&nbsp;
						<label class="font_size_12 color_black" for="chkval">
						{{ trans('messages.lbl_Caldone') }}</label>
			</div>
			<div class="pull-right pr10">
				<a href="javascript:javascript:Editbillingdetails('{{ $result_query[0]->id }}',
																	'copy',
																	'{{ $result_query[0]->nickname }}',
																	'{{ $result_query[0]->Empno }}',
																	'{{$result_query[0]->start_date}}');" class="btn btn-success box80 pull-right pr10">
				<span class="fa fa-copy mr5"></span>{{ trans('messages.lbl_copy') }}</a>
			</div>
			<div class="pull-right mr10">
				@if($result_query[0]->tcheckcalc != 1)
					<a href="javascript:javascript:Editbillingdetails('{{ $result_query[0]->id }}',
																	'edit',
																	'{{ $result_query[0]->nickname }}',
																	'{{ $result_query[0]->Empno }}',
																	'{{$result_query[0]->start_date}}');" 
					class="btn btn-warning box80 pull-right pr10">
					<span class="fa fa-pencil mr5"></span>{{ trans('messages.lbl_edit') }}</a>
				@else
					<button id="edit" data-dismiss="modal" 
				            class="btn CMN_display_block box80 flr" disabled="disabled" 
				            style="background-color: #bbb5b5;">
				      <i class="fa fa-pencil"></i>
				      {{ trans('messages.lbl_edit') }}
				    </button>
				@endif
			</div>
		</div>
		<div class="minh400 mt50 pl15 pr10">
			<table class="box100per">
				<colgroup>
			        <col width="5%">
			        <col width="5%">
			        <col width="6%">
			        <col width="20%">
			        <col width="6%">
			        <col width="6%">
			        <col width="6%">
	        	</colgroup>
				<tr>
					<td class="CMN_bdrnone tabellabel">{{ trans('messages.lbl_empid') }}</td>
					<td class="CMN_bdrnone">{{$result_query[0]->Empno}}</td>
					<td class="CMN_bdrnone tabellabel">{{ trans('messages.lbl_name') }}</td>
					<td class="CMN_bdrnone">
						@if(isset($result_query[0]->LastName))
							{{$result_query[0]->LastName}}
						@else

						@endif
						@if(!empty($result_query[0]->nickname)) ( {{$result_query[0]->nickname}} )
						@else @endif
					</td>
					<td class="CMN_bdrnone"></td>
					<td class="CMN_bdrnone"></td>
					<td class="CMN_bdrnone"></td>
				</tr>
				<tr>
					<td class="CMN_bdrnone tabellabel">{{ trans('messages.lbl_client_name') }}</td>
					<td class="CMN_bdrnone">
					@if(!empty($result_query[0]->customer_name))
					{{$result_query[0]->customer_name}}
					@else {{ "Nill" }}
					@endif</td>
					<td class="CMN_bdrnone tabellabel">{{ trans('messages.lbl_branch') }}</td>
					<td class="CMN_bdrnone">
					@if(!empty($result_query['branch_name']))
					{{$result_query['branch_name']}}
					@else {{ "Nill" }}
					@endif</td>
					<td class="CMN_bdrnone tabellabel">{{ trans('messages.lbl_Start_date') }}</td>
					<td class="CMN_bdrnone">{{$result_query[0]->start_date}}</td>
					<td class="CMN_bdrnone"></td>
				</tr>
	        </table>
	        <table class="box100per">
	        	<tr>
					<td colspan="7" class="CMN_bdrnone">
						<span class="fwb pl5">
							{{ trans('messages.lbl_billingdetails').'&nbsp;:' }}
						</span>
				        <fieldset class="mt10">
				        	<div>
								<div class="mt10">
									<div class="box250 tabellabel CMN_display_block">
										{{ trans('messages.lbl_amount') }}
									</div>	
									<div class="tar box100 CMN_display_block">
										{{$result_query[0]->Amount}}
									</div>	
								</div>
								<div class="mt10">
									<div class="box250 tabellabel CMN_display_block">
										{{ trans('messages.lbl_timerange') }}
									</div>	
									<div class="tar box100 CMN_display_block">
										{{$result_query[0]->minhrs}}
									</div>&nbsp;&nbsp;~&nbsp;&nbsp;
									<div class="tar CMN_display_block">
										{{$result_query[0]->maxhrs}}
									</div>
								</div>
								<div class="mt10 mb12">
									<div class="box250 tabellabel CMN_display_block">
										{{ trans('messages.lbl_OTAmount') }}
									</div>	
									<div class="tar box100 CMN_display_block">
										{{$result_query[0]->maxamt}}
									</div>&nbsp;&nbsp;~&nbsp;&nbsp;
									<div class="tar CMN_display_block">
										{{$result_query[0]->minamt}}
									</div>
									<div class="tar box150 CMN_display_block">
										@if($result_query[0]->bdcheckcalc == 1)
										<div class="black_box CMN_display_block mt2"></div>
										<div class="vat CMN_display_block mr23">
											{{ trans('messages.lbl_autocal') }}
										</div>
										@elseif($result_query[0]->bdcheckcalc == 2)
										<div class="white_box CMN_display_block mt2"></div>
										<div class="vat CMN_display_block mr23">
											{{ trans('messages.lbl_autocal') }}
										</div>
										@endif
									</div>
								</div>
							</div>
				        </fieldset>
		        	</td>
		        </tr>
	        </table>
	        <table class="box100per">
	        	<tr>
					<td colspan="7" class="CMN_bdrnone">
						<span class="fwb pl5">
							{{ trans('messages.lbl_monthlybilling').
							'&nbsp;'.trans('messages.lbl_calculation').'&nbsp;:' }}
						</span>
				        <fieldset>
				        	<div>
								<div class="mt10">
									<div class="box250 tabellabel CMN_display_block">
										{{ trans('messages.lbl_timerange') }}
									</div>	
									<div class="tar box100 CMN_display_block">
										@if(!empty($result_query[0]->timerange))
										{{$result_query[0]->timerange}}
										@else {{ '0' }}
										@endif
									</div>
									<div class="box18per CMN_display_block">
										@if($result_query[0]->wknghrschk == 1)
										<div class="CMN_display_block mt2 fll tar box50per">
											<div class="black_box tar CMN_display_block"></div>
										</div>
										<div class="vat CMN_display_block ml5 tal">
											{{ trans('messages.lbl_getworkinghrs') }}
										</div>
										@elseif($result_query[0]->wknghrschk == 0)
										<div class="CMN_display_block mt2  fll tar box50per">
											<div class="white_box tar CMN_display_block"></div>
										</div>
										<div class="vat CMN_display_block ml5 tal">
											{{ trans('messages.lbl_getworkinghrs') }}
										</div>
										@endif
									</div>
								</div>
								<div class="mt10">
									<div class="box250 tabellabel CMN_display_block">
										{{ trans('messages.lbl_OTAmount') }}
									</div>	
									<div class="tar box100 CMN_display_block">
										@if(!empty($result_query[0]->OTAmount))
										{{$result_query[0]->OTAmount}}
										@else {{ '0' }}
										@endif
									</div>
									<div class="box18per CMN_display_block">
										@if($result_query[0]->mbcheckcalc == 1)
										<div class="CMN_display_block mt2 fll tar box50per">
											<div class="black_box tar CMN_display_block"></div>
										</div>
										<div class="vat CMN_display_block ml5 tal">
											{{ trans('messages.lbl_autocal') }}
										</div>
										@elseif($result_query[0]->mbcheckcalc == 2)
										<div class="CMN_display_block mt2 fll tar box50per">
											<div class="white_box tar CMN_display_block"></div>
										</div>
										<div class="vat CMN_display_block ml5 tal">
											{{ trans('messages.lbl_autocal') }}
										</div>
										@endif
									</div>
								</div>
								<div class="mt10 mb12">
									<div class="box250 tabellabel CMN_display_block">
										{{ trans('messages.lbl_billingAmt') }}
									</div>	
									<div class="tar box100 CMN_display_block">
										@if(!empty($result_query[0]->TotalAmount))
										{{$result_query[0]->TotalAmount}}
										@else {{ '0' }}
										@endif
									</div>
								</div>
							</div>
				        </fieldset>
		        	</td>
		        </tr>
	        </table>
		</div>
	{{ Form::close() }}
	</article>
</div>
<script type="text/javascript">
	$('#scrname').val("detailview");
</script>
@endsection