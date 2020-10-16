@extends('layouts.app')
@section('content')
@php use App\Http\Helpers; @endphp
{{ HTML::script('resources/assets/js/engineerdetails.js') }}
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
<div class="CMN_display_block" id="main_contents">
    <!-- article to select the main&sub menu -->
<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_9">
  {{ Form::open(array('name'=>'enggexpenseindex', 
            'id'=>'enggexpenseindex', 
            'url' => 'Engineerdetails/expenseindex?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
            'files'=>true,
            'method' => 'POST')) }}
	{{ Form::hidden('empid', $request->empid , array('id' => 'empid')) }}
	{{ Form::hidden('historypage', $request->historypage , array('id' => 'historypage')) }}
	{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
	{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
    {{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
    {{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}
    {{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
    {{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
    {{ Form::hidden('sorting', $request->sorting, array('id' => 'sorting')) }}
    {{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
	{{ Form::hidden('ordervalue', $request->ordervalue, array('id' => 'ordervalue')) }}
	<div class="row hline">
        <div class="col-sm-12 mt10">
          <img class="pull-left box40 mt5" src="{{ URL::asset('resources/assets/images/Client.png') }}">
              <h2 class="pl5 pull-left mt10">{{ trans('messages.lbl_enggexp_details') }}</h2>
        </div>
    </div>
    @if($request->historypage != 1)
    <div class="box100per pr10 pl10 ">
		<div class="mt10 mb10">
			{{ Helpers::displayYear_MonthEst($account_period, $year_month, $db_year_month, $date_month, $dbnext, $dbprevious, $last_year, $current_year, $account_val) }}
		</div>
	</div>
	<div class="col-xs-12 pm0 pull-left mb10 pl10 mt10 pr10">
        <div class="form-group pm0 pull-right moveleft nodropdownsymbol" id="moveleft">
          <a href="javascript:clearsearchexp()" title="Clear Search">
            <img class="pull-left box30 mr5 " src="{{ URL::asset('resources/assets/images/clearsearch.png') }}">
          </a>
          {{ Form::select('engineerdetailssort', [null=>''] + $engineerdetailssortarray, $request->engineerdetailssort,
                              array('class' => 'form-control'.' ' .$request->sortstyle.' '.'CMN_sorting pull-right',
                             'id' => 'engineerdetailssort',
                              'style' => '',
                             'name' => 'engineerdetailssort'))
          }}
        </div>
      </div>
	@endif
      @if($request->historypage =="1")
      <div class="col-xs-12 pm0 pull-left mb10 pl10 pr10 mt20 fwb">
        {{ trans('messages.lbl_employeeid').':' }}
          <span class="mr40 ml12" style="color:blue;">
            {{ $g_query[0]->emp_ID }}
          </span>
            {{ trans('messages.lbl_empName').':' }}
          <span style="color:#9C0000;margin-left: 10px">
            {{ $g_query[0]->LastName }}.{{ substr($g_query[0]->LastName,0,1) }}
            </span>
      </div>
      @endif
    <div class="mr10 ml10 mt20">
		<div class="minh200">
			<table class="tablealternate box100per">
				<colgroup>
				   <col width="3%">
				   <col width="8%">
				   @if($request->historypage !="1")
				   <col width="">
				   @else

				   @endif
				   <col width="15%">
				   <col width="15%">
				   <col width="15%">
				   <col width="15%">
				   <col width="5%">
				 
				</colgroup>
				<thead class="CMN_tbltheadcolor">
			   		<tr> 
				  		<th rowspan="2" class="tac">{{ trans('messages.lbl_sno') }}</th>
				  		<th rowspan="2" class="tac">{{ trans('messages.lbl_Date') }}</th>
				  		@if($request->historypage != "1")
				  		<th rowspan="2" class="tac">{{ trans('messages.lbl_empName') }}</th>
				  		@endif
				  		<th rowspan="2" class="tac">{{ trans('messages.lbl_mainsubject') }}</th>
				  		<th rowspan="2" class="tac">{{ trans('messages.lbl_subsubject') }}</th>
				  		<th rowspan="2" class="tac">{{ trans('messages.lbl_amount') }}</th>
				  		<th rowspan="2" class="tac">{{ trans('messages.lbl_remarks') }}</th>
				  		<th rowspan="2" class="tac">{{ trans('messages.lbl_bill') }}</th>
				  	</tr>
				  	<!-- <tr>
              			<th>{{ trans('messages.lbl_cash') }}</th> 
              			<th>{{ trans('messages.lbl_expenses') }}</th> 
            		</tr> -->
				</thead>
				<tbody>
					{{ $temp = ""}}
					{{--*/ $row = '0' /*--}}
					{{ $tempsub = ""}}
					{{--*/ $rowsub = '0' /*--}}
					{{ $tempdet = ""}}
					{{--*/ $rowdet = '0' /*--}}
					<?php $style_tr=""; ?>
					@if(count($g_query)!="")
					@foreach($g_query1 as $key => $value)
					{{--*/ $loc = $value->emp_ID /*--}}
					@if(Session::get('languageval') == "en")
						{{--*/ $locsub = $value->subname /*--}}
					@else
						{{--*/ $locsub = $value->subjp /*--}}
					@endif
					@if(Session::get('languageval') == "en")
						{{--*/ $locdet = $value->sub_eng /*--}}
					@else
						{{--*/ $locdet = $value->sub_jap /*--}}
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
					@if($locsub != $tempsub) 
						@if($rowsub==1)
							{{--*/ $style_tr = 'background-color: #E5F4F9;' /*--}}
							{{--*/ $rowsub = '0' /*--}}
						@else
							{{--*/ $style_tr = 'background-color: #FFFFFF;' /*--}}
							{{--*/ $rowsub = '1' /*--}}
						@endif
						{{--*/ $style_tdsub = '' /*--}}
					@else
						{{--*/ $style_tdsub = 'border-top: hidden;' /*--}}
					@endif
					@if($locdet != $tempdet) 
						@if($rowdet==1)
							{{--*/ $style_tr = 'background-color: #E5F4F9;' /*--}}
							{{--*/ $rowdet = '0' /*--}}
						@else
							{{--*/ $style_tr = 'background-color: #FFFFFF;' /*--}}
							{{--*/ $rowdet = '1' /*--}}
						@endif
						{{--*/ $style_tddet = '' /*--}}
					@else
						{{--*/ $style_tddet = 'border-top: hidden;' /*--}}
					@endif
						<tr style="{{$style_tr}}">
							<td class="tac">
								{{ ($g_query1->currentpage()-1) * $g_query1->perpage() + $key + 1 }}
							</td>
							<td class="tac">
								{{ $value->date }}
							</td>
							@if($request->historypage !="1")
							<td style="{{$style_td}}" title="{{ empnameontitle($value->LastName,$value->Firstname,50) }}">
								@if($loc!=$temp)
									@if(isset($value->LastName))
										<a href="javascript:fngetEmpDet('{{ $value->emp_ID }}')"  class="anchorstyle">{{ empnamelength($value->LastName, $value->Firstname,25) }}</a>
									@endif
								@endif
							</td>
								@endif
							<td class="tal" style="{{$style_tdsub}}">
								@if($locsub!=$tempsub)
									@if(Session::get('languageval') == "en")
										{{ $value->subname }}
									@else
										{{ $value->subjp }}
									@endif
								@endif
							</td>
							<td class="tal" style="{{$style_tddet}}">
								@if($locdet!=$tempdet)
									@if(Session::get('languageval') == "en")
										{{ $value->sub_eng }}
									@else
										{{ $value->sub_jap }}
									@endif
								@endif
							</td>
							<td class="tar">
								{{ number_format($value->amount) }}
							</td>
							<td>
								{{ $value->remark_dtl }}
							</td>
							<td style="text-align: center;">
							@if($value->file_dtl != "")
								<?php
									$file_url = '../InvoiceUpload/Expenses/' . $value->file_dtl;
								 ?>
								@if(isset($value->file_dtl) && file_exists($file_url))
									<a href="javascript:filedownload('../../../../InvoiceUpload/Expenses','{{$value->file_dtl}}');" title="Download"><i class="" aria-hidden="true"></i><img src="{{ URL::asset('resources/assets/images/download.png') }}" width="20px;" height="20px;" title="Download Bank Tansfer Image"></img></a>
								@else
								@endif
							@endif
						</td>
						</tr>
						{{--*/ $temp = $loc /*--}}
						{{--*/ $tempsub = $locsub /*--}}
						{{--*/ $tempdet = $locdet /*--}}
		            @endforeach
		            @else
		            	<tr>
							<td class="text-center fr" colspan="8">
								{{ trans('messages.lbl_nodatafound') }}
							</td>
						</tr>
		            @endif
				</tbody>  	
			</table>
			<table width="100%" cellspacing="0" cellpadding="0" class="mtneg5 mt5">
				<tr>
					<td width="3%" class="CMN_bdrnone"></td>
					<td width="8%" class="CMN_bdrnone"></td>
					@if($request->historypage != "1")
					<td width="" class="CMN_bdrnone"></td>
					@endif
					<td width="15%" class="CMN_bdrnone"></td>
					<td align="right" width="15%" class="CMN_tbltheadcolor fwb">{{ trans('messages.lbl_total') }}</td>
					<td align="right" width="15%" style="font-weight: bold;">{{ number_format($total) }}</td>
					<td width="15%" class="CMN_bdrnone"></td>
					<td width="5%" class="CMN_bdrnone"></td>
				</tr>
			</table>
		</div>
		@if(!empty($g_query1->total()))
			<div class="text-center">
				<span class="pull-left mt24">
					{{ $g_query1->firstItem() }} ~ {{ $g_query1->lastItem() }} / {{ $g_query1->total() }}
				</span>
				{{ $g_query1->links() }}
				<div class="CMN_display_block flr">
					{{ $g_query1->linkspagelimit() }}
				</div>
			</div>
		@endif
	</div>
</article>
</div>
@endsection