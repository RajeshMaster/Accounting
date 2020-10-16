@extends('layouts.app')
@section('content')
@php use App\Http\Helpers @endphp
{{ HTML::script('resources/assets/js/salaryplus.js') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
</script>
<style type="text/css">
	.alertboxalign {
		margin-bottom: -35px !important;
	}
	.alert {
		display:inline-block !important;
		height:30px !important;
		padding:5px !important;
	}
	.btn-gray {
 		 background-color: gray;
  		 border-color: white;
	}
	.btn-red {
	background-color: red;
  	border-color: white;
  	color: white;
	}
	.bg_lightgrey {
	    background-color:#D3D3D3    ! important;
	}
</style>
<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_10">
		{{ Form::open(array('name'=>'salaryplusindex',
							'id'=>'salaryplusindex',
							'url'=>'Salaryplus/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
							'files'=>true,
							'method' => 'POST' )) }}
			{{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
			{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
			{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
			{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
			{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
			{{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}
			{{ Form::hidden('previou_next_year', $request->previou_next_year, 
								array('id' => 'previou_next_year')) }}
			{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
			{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
			{{ Form::hidden('id','' , array('id' => 'id')) }}
			{{ Form::hidden('Emp_ID','' , array('id' => 'Emp_ID')) }}
			{{ Form::hidden('editcheck','' , array('id' => 'editcheck')) }}
			{{ Form::hidden('firstname','' , array('id' => 'firstname')) }}
			{{ Form::hidden('lastname','' , array('id' => 'lastname')) }}
			{{ Form::hidden('mutlireg','' , array('id' => 'mutlireg')) }}
	    	{{ Form::hidden('empname', '' , array('id' => 'empname')) }}
	    	{{ Form::hidden('total', '' , array('id' => 'total')) }}
			{{ Form::hidden('multiflg','' , array('id' => 'multiflg')) }}
			{{ Form::hidden('editflg', '' , array('id' => 'editflg')) }}

		<!-- Start Heading -->
		<div class="row hline pm0">
				<div class="col-xs-12">
					<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/salary_1.png') }}">
					<h2 class="pull-left pl5 mt10">
						{{ trans('messages.lbl_salaryplus') }}
					</h2>
				</div>
			</div>
		<!-- End Heading -->	
		<div class="box100per pr10 pl10 ">
			<div class="mt10 mb10">
				{{ Helpers::displayYear_MonthEst($account_period, $year_month, $db_year_month, $date_month, $dbnext, $dbprevious, $last_year, $current_year, $account_val) }}
			</div>
		</div>
		<div class="col-xs-12 mt5 pm0 pull-left pl10">
			<!-- Session msg -->
			@if(Session::has('success'))
				<div align="center" class="alertboxalign" role="alert">
					<p class="alert {{ Session::get('alert', Session::get('type') ) }}">
		            	{{ Session::get('success') }}
		          	</p>
				</div>
			@endif
			<!-- Session msg -->
			<a class="pull-left" href="javascript:salaryselectpopup();">
	          	<img class="box19" src="{{ URL::asset('resources/assets/images/edit.png') }}"></a>
				<a href="javascript:salaryselectpopup();" class="pull-left pr10 ml5 anchorstyle" title="{{ trans('messages.lbl_cempsel') }}">
				{{ trans('messages.lbl_cempsel') }}
			</a>
			
			<div style="display: inline-block;" class="mr10 mb10 pull-right">
				<a href="javascript:pay_reg(1);" class="btn btn-primary" title="Multiple Register" style="color: white;">
					{{ trans('messages.lbl_multipay') }}
				</a>
			</div>
			
			<div style="display: inline-block;" class="mr10 mb10 pull-right">
				<a href="javascript:multi_reg();" class="btn btn-success" title="Multiple Register" style="color: white;">
					{{ trans('messages.lbl_multi_register') }}
				</a>
			</div>
		</div>
		<div class="minh400 box100per pl10 pr10 mt10">
			<table class="tablealternate box100per CMN_tblfixed">
				<colgroup>
					<col width="4%">
					<col width="7%">
					<col width="">
					<col width="13%">
					<col width="8%">
					<col width="8%">
					<col width="7%">
					<col width="7%">
					<col width="8%">
					<col width="8%">
					<col width="13%">
					<col width="3%">
					<col width="3%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam">{{ trans('messages.lbl_empno') }}</th>
					<th class="vam">{{ trans('messages.lbl_name') }}</th>
					<th class="vam">{{ trans('messages.lbl_basic') }}</th>
					<th class="vam" title="{{ trans('messages.lbl_House_Rent_allowance') }}">{{ trans('messages.lbl_HRA') }}</th>
					<th class="vam" title="{{ trans('messages.lbl_overtime') }}">{{ trans('messages.lbl_OT') }}</th>
					<th class="vam" title="{{ trans('messages.lbl_esi_f') }}">{{ trans('messages.lbl_esi') }}</th>
					<th class="vam" title="Income Tax">{{ trans('messages.lbl_it') }}</th>
					<th class="vam">{{ trans('messages.lbl_travel') }}</th>
					<th class="vam" title="{{ trans('messages.lbl_monthlytravel') }}">{{ trans('messages.lbl_m_travel') }}</th>
					<th class="vam" colspan="3">{{ trans('messages.lbl_totamt') }}</th>
				</thead>
				<tbody>
					@if(count($get_det)!="")
						<tr>
							<td colspan="0" bgcolor="lightgrey" class="tar fwb" style="color:blue;">
							</td>
							<td bgcolor="lightgrey" class="tar fwb" style="color:blue;">
							</td>
							<td bgcolor="lightgrey" class="tar fwb" style="color:black;">
								{{ trans('messages.lbl_grandtot') }}
							</td>
							<td bgcolor="lightgrey" class="fwb" align="right"  style="color:blue;">
								@if($g_query_tot['BasicTotal'] != "")
									{{ $g_query_tot['BasicTotal'] }}
								@endif
							</td>
							<td bgcolor="lightgrey" class="tar fwb" style="color:blue;">
								@if($g_query_tot['HrAllowanceTotal'] != "")
									{{ $g_query_tot['HrAllowanceTotal'] }}
								@endif
							</td>
							<td bgcolor="lightgrey" class="fwb" align="right" style="color:blue;">
								@if($g_query_tot['OTTotal'] != "")
									{{ $g_query_tot['OTTotal'] }}
								@endif
							</td>
							<td bgcolor="lightgrey" class="fwb red" align="right" style="color:blue;">
								@if($g_query_tot['ESITotal'] == '-0')
									0
								@else
									@if($g_query_tot['ESITotal'] != "")
										-{{ $g_query_tot['ESITotal'] }}
									@endif
								@endif
							</td>
							<td bgcolor="lightgrey" class="fwb red" align="right" style="color:blue;">
								@if($g_query_tot['ITTotal'] == '-0')
									0
								@else
									@if($g_query_tot['ITTotal'] != "")
										-{{ $g_query_tot['ITTotal'] }}
									@endif
								@endif
							</td>
							<td bgcolor="lightgrey" class="fwb" align="right" style="color:blue;">
								@if($g_query_tot['TravelTotal'] != "")
									{{ $g_query_tot['TravelTotal'] }}
								@endif
							</td>
							<td bgcolor="lightgrey" class="fwb" align="right" style="color:blue;">
								@if($g_query_tot['MonthlyTravelTotal'] != "")
									{{ $g_query_tot['MonthlyTravelTotal'] }}
								@endif
							</td>
							<td colspan="3" bgcolor="lightgrey" class="fwb pr10" align="right" style="color:blue;">
								@if($g_query_totall['Total'] != "")
									{{ $g_query_totall['Total'] }}
								@endif
							</td>
						</tr>
			   		 @for ($i = 0; $i < count($get_det); $i++)
						<tr>
	                    	<td class="text-center">
	                    		{{ ($g_query->currentpage()-1) * $g_query->perpage() + $i + 1 }}
	                    	</td>
	                    	<td class="tac">
	                    		<a class="colbl fwb anchorstyle" href="javascript:underconstruction();">
	                    			{{ $get_det[$i]['Emp_ID'] }}
	                    		</a>
				   			</td>
	                    	<td>
	                    		<a class="colbl anchorstyle" href="javascript:fngotoadd('{{ $get_det[$i]['id'] }}','{{ $get_det[$i]['Emp_ID'] }}','{{ $get_det[$i]['editcheck'] }}','{{ $request->mainmenu }}','{{ $get_det[$i]['FirstName'] }}','{{ $get_det[$i]['LastName'] }}');" title="{{ empnameontitle($get_det[$i]['LastName'], $get_det[$i]['FirstName'],50) }}">
	                    			{{ empnamelength($get_det[$i]['LastName'], $get_det[$i]['FirstName'],12) }}
	                    		</a>
	                    	</td>
	                    	<td class="text-right pr10">
	                    		@if(isset($get_det[$i]['Basic']) && $get_det[$i]['HrAllowance'] != "")
	                    			{{ number_format($get_det[$i]['Basic']) }}
	                    		@endif
	                    	</td>
	                    	<td class="text-right pr10">
	                    		@if(isset($get_det[$i]['HrAllowance']) && $get_det[$i]['HrAllowance'] != "")
	                    			{{ number_format($get_det[$i]['HrAllowance']) }}
	                    		@endif
	                    	</td>
	                    	<td class="text-right pr10">
	                    		@if(isset($get_det[$i]['OT']) && $get_det[$i]['OT'] != "")
	                    			{{ number_format($get_det[$i]['OT']) }}
	                    		@endif
	                    	</td>
	                    	<td class="text-right pr10 red">
	                    		@if(isset($get_det[$i]['ESI']) && $get_det[$i]['ESI'] != "")
	                    			{{ number_format($get_det[$i]['ESI']) }}
	                    		@endif
	                    	</td>
	                    	<td class="text-right pr10 red">
	                    		@if(isset($get_det[$i]['IT']) && $get_det[$i]['IT'] != "")
	                    			{{ number_format($get_det[$i]['IT']) }}
	                    		@endif
	                    	</td>
	                    	<td class="text-right pr10">
	                    		@if(isset($get_det[$i]['Travel']) && $get_det[$i]['Travel'] != "")
	                    			{{ number_format($get_det[$i]['Travel']) }}
	                    		@endif
	                    	</td>
	                    	<td class="text-right pr10">
	                    		@if(isset($get_det[$i]['MonthlyTravel']) && $get_det[$i]['MonthlyTravel'] != "")
									{{ number_format($get_det[$i]['MonthlyTravel']) }}	
	                    		@endif
							</td>
							<td class="text-right pr10">
								@if(isset($get_det[$i]['Total']) && $get_det[$i]['Total'] != "")
									{{ $get_det[$i]['Total'] }}
	                    		@endif
							</td>
							<td class="text-center">
								@if($get_det[$i]['status'] == 1)
									<img class="box19" title="{{ trans('messages.lbl_paid') }}" src="{{ URL::asset('resources/assets/images/tick_1.png') }}">
								@elseif($get_det[$i]['status'] === 0)
									<img class="box19" title="{{ trans('messages.lbl_notpaid') }}" src="{{ URL::asset('resources/assets/images/close.png') }}">
								@endif
							</td>
							<td class="text-center">
								@if($get_det[$i]['status'] == 1)
									@if($get_det[$i]['TotalAmount'] > $get_det[$i]['salarychk'])
									<a title="Add" href="javascript:gotoadd('{{ $get_det[$i]['Emp_ID'] }}','{{ $get_det[$i]['EmpName'] }}','{{ str_replace(",","",$get_det[$i]['Total']) }}','{{ $request->mainmenu }}',1);" class="anchorstyle">
										<img class="box19" src="{{ URL::asset('resources/assets/images/addicon.png') }}">
									</a>
									@endif
								@elseif($get_det[$i]['status'] === 0)
								@endif
							</td>
                    	</tr>
                    	@endfor
                    	@else
							<tr>
								<td class="text-center fr" colspan="11">
								{{ trans('messages.lbl_nodatafound') }}</td>
							</tr>
						@endif
				</tbody>
		</table>
		@if(count($get_det)!="")
		<!-- <table width="94.8%" cellspacing="0" cellpadding="0" class="mtneg5">
			<tr>
				<td width="51.8%" class="CMN_bdrnone"></td>
				<td width="45%" class="CMN_bdrnone">
					<table width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td align="right" width="16.2%" class="CMN_tbltheadcolor fwb">
								{{ trans('messages.lbl_total') }}
							</td>
							<td align="right" width="10%" class="pr10">
								
							</td>
							<td align="right" width="10%" class="pr10">
								
							</td>
							<td align="right" width="10%" class="pr10">
								
							</td>
						</tr>
						<tr>
							<td align="right" class="CMN_tbltheadcolor fwb">{{ trans('messages.lbl_Tax') }}</td>
							<td align="right" class="pr10">
								
							</td>
							<td></td>
							<td align="right" class="pr10">
								
							</td>
						</tr>
						<tr>
							<td align="right" class="CMN_tbltheadcolor fwb">
								{{ trans('messages.lbl_grandtot') }}
							</td>
							<td align="right" class="word_wrap dotted_border pr10">
								
							</td>
							<td class="word_wrap dotted_border"></td>
							<td align="right" class="word_wrap dotted_border pr10">
								
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table> -->
		@endif	
		@if(count($get_det)!="")
			<div class="text-center">
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
	<div id="salarypluspopup" class="modal fade">
		<div id="login-overlay">
			<div class="modal-content">
				<!-- Popup will be loaded here -->
			</div>
		</div>
	</div>
	</article>
</div>
@endsection