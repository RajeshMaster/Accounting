@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/billing.js') }}
<script type="text/javascript">
    var datetime = '<?php echo date('Ymdhis'); ?>';
</script>
<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper " data-category="sales sales_sub_6">
	{{ Form::open(array('name'=>'billhistoryfrm', 
						'id'=>'billhistoryfrm', 
						'url' => 'Billing/billhistory?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
     	{{ Form::hidden('scrname', '' ,array('id' => 'scrname')) }}
     	{{ Form::hidden('mainmenu', $request->mainmenu ,array('id' => 'mainmenu')) }}
     	{{ Form::hidden('empid', $request->empid ,array('id' => 'empid')) }}
     	{{ Form::hidden('empname', $request->empname ,array('id' => 'empname')) }}
     	{{ Form::hidden('pageclick', $request->pageclick, array('id' => 'pageclick')) }}
		{{ Form::hidden('plimit', $request->plimit, array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('hdnback', $request->hdnback, array('id' => 'hdnback')) }}
		{{ Form::hidden('hdnmainmenu', $request->mainmenu, array('id' => 'hdnmainmenu')) }}
	<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt15" 
				src="{{ URL::asset('resources/assets/images/billing.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_billinghistory') }}</h2>
		</div>
	</div>
	<div class="pb10"></div>
	<!-- End Heading -->
	<!-- Session msg -->
	@if(Session::has('success'))
		<div align="center" class="alertboxalign mt5" role="alert">
			<p class="alert {{ Session::get('alert', Session::get('type') ) }}">
            {{ Session::get('success') }}
          	</p>
		</div>
	@endif
	@php Session::forget('success'); @endphp
	<!-- Session msg -->
	<div class="pl5 pr5">
		<div class="pull-left ml10">
			<a href="javascript:goindexpage('{{ $request->mainmenu }}',1);" class="pageload btn btn-info box80"><span class="fa fa-arrow-left"></span> {{ trans('messages.lbl_back') }}</a>
		</div>
	</div>
	<div class="col-xs-12 pm0 pull-left mb10 pl15 mt5 fwb">
		{{ trans('messages.lbl_empid').':' }}
		<span class="mr40 ml12" style="color:blue;">
	       {{ $request->empid }}
	    </span>
	    {{ trans('messages.lbl_empName').':' }}
	    <span style="color:#9C0000;margin-left: 10px">
	      {{ $request->empname }}
	    </span>
	</div>
	<div class="minh400 box100per pl15 pr10">
		<table class="tablealternate box100per CMN_tblfixed">
	        <colgroup>
		        <col width="5%">
		        <col width="7%">
		        <col width="12%">
		        <col width="12%">
		        <col width="10%">
		        <col width="10%">
		        <col width="10%">
	        </colgroup>
	        <thead class="CMN_tbltheadcolor">
	            <tr>
	              <th>{{ trans('messages.lbl_sno') }}</th>
	              <th>{{ trans('messages.lbl_Date') }}</th>
	              <th>{{ trans('messages.lbl_client_name') }}</th>
	              <th>{{ trans('messages.lbl_branch') }}</th>
	              <th>{{ trans('messages.lbl_totalhrs') }}</th>
	              <th>{{ trans('messages.lbl_OTAmt') }}</th>
	              <th>{{ trans('messages.lbl_TotalAmt') }}</th>
	            </tr>
	        </thead>
	        <tbody>
	        @if(count($displayArray) != '0')
	        	<tr class="bg_lightgrey">
	        		<td colspan="5" class="text-right fwb blue">{{ trans('messages.lbl_grandtot') }}</td>
	        		<td class="text-right fwb blue pr10">
	        			@if($grndotamt)
	        				@php echo "¥ ".number_format($grndotamt) @endphp
	        			@else
	        			@endif	
	        		</td>
	        		<td class="text-right fwb blue pr10">
	        			@if($grndtotamt)
	        				@php echo "¥ ".number_format($grndtotamt) @endphp
	        			@else
	        			@endif	
	        	</tr>
	        	<tr class="bg_white">
	        		<td colspan="7"></td>
	        	</tr>
	        	{{--*/ $l = '0' /*--}}
	        	{{--*/ $clienttemp = "" /*--}}
				{{--*/ $branchtemp = "" /*--}}
        		{{ $style_td = "" }}
				@for ($j = 0; $j < count($displayArray); $j++)
					@if($displayArray[$j]['totalhrs'] && $displayArray[$j]['TotalAmount']!="")
						@if($clienttemp != $displayArray[$j]['customer_name'])
							{{--*/ $style_td = '' /*--}}
                			{{--*/ $disp_custname = $displayArray[$j]['customer_name'] /*--}}
                		@else
			                {{--*/ $style_td = '' /*--}}
			                {{--*/ $disp_custname = '' /*--}}
		              	@endif
		              	@if($branchtemp != $displayArray[$j]['branch_name'])
							{{--*/ $style_td = '' /*--}}
                			{{--*/ $disp_branchname = $displayArray[$j]['branch_name'] /*--}}
                		@else
			                {{--*/ $style_td = '' /*--}}
			                {{--*/ $disp_branchname = '' /*--}}
		              	@endif
		        		<tr>
		        		<td class="text-center">{{ $l+1 }}</td>
		        		<td class="text-center">{{ $displayArray[$j]['date'] }}</td>
		        		<td title ="{{ $displayArray[$j]['customer_name'] }}">
							 @if(mb_strlen($displayArray[$j]['customer_name'], 'UTF-8') > 13)
								@php echo mb_substr($displayArray[$j]['customer_name'], 0, 12, 'UTF-8')."..." @endphp
							@else
								{{ $displayArray[$j]['customer_name'] }}
							@endif
		        		</td>
		        		<td>{{ $displayArray[$j]['branch_name'] }}</td>
		        		<td class="text-center">@php echo $displayArray[$j]['totalhrs'] @endphp</td>
		        		<td class="text-right pr10">@php echo $displayArray[$j]['OTAmount'] @endphp</td>
		        		<td class="text-right pr10">@php echo $displayArray[$j]['TotalAmount'] @endphp</td>
		        		</tr>
		        		@php $l++ @endphp
		        	{{--*/ $clienttemp = $displayArray[$j]['customer_name'] /*--}}
                    {{--*/ $branchtemp = $displayArray[$j]['branch_name'] /*--}}
	        		@endif
	        	@endfor
	        @else
	        	<tr>
					<td colspan="7" align="center" class="red">
					{{ trans('messages.lbl_nodatafound') }}</td>
				</tr>
	        @endif
	        </tbody>
       	</table>
	</div>
		@if(count($displayArray) != '0')
		<div class="text-center pl15">
			@if(!empty($g_query->total()))
				<span class="pull-left mt24">
					{{ $g_query->firstItem() }} ~ {{ $g_query->lastItem() }} / {{ $g_query->total() }}
				</span>
			@endif 
			{{ $g_query->links() }}
			<div class="CMN_display_block flr mr10">
			{{ $g_query->linkspagelimit() }}
			</div>
		</div>
		@endif
	{{ Form::close() }}
	</article>
</div>
<script type="text/javascript">
	$('#scrname').val("billinghistory");
</script>
@endsection