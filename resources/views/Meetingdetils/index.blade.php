@extends('layouts.app')
@section('content')
@php use App\Http\Helpers @endphp
<script type="text/javascript">
	var datetime = '@php echo date('Ymdhis') @endphp';
	var mainmenu = '@php echo $request->mainmenu @endphp';
</script>
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/multisearchvalidation.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::script('resources/assets/js/meeting.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::style('resources/assets/css/switch.css') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper " data-category="customer customer_sub_3">
	{{ Form::open(array('name'=>'meetingindex', 
						'id'=>'meetingindex', 
						'url' => 'MeetingDetails/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
	{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
	{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
	{{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}
	{{ Form::hidden('topclick', $request->topclick, array('id' => 'topclick')) }}
	{{ Form::hidden('sorting', $request->sorting, array('id' => 'sorting')) }}
	{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	{{ Form::hidden('viewid', '' , array('id' => 'viewid')) }} 
	{{ Form::hidden('editflg', '' , array('id' => 'editflg')) }} 
	{{ Form::hidden('customer_name', '' , array('id' => 'customer_name')) }} 
	<!-- Start Heading -->
	<div class="row hline">
	<div class="col-xs-12">
		<img class="pull-left box35 mt15" src="{{ URL::asset('resources/assets/images/meetingdet.png') }}">
		<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_meetingdet') }}</h2>
	</div>
	</div>
	<div class="box100per pr10 pl10 mt10">
		<div class="mt10 mb10">
			{{ Helpers::displayYear_MonthEst($account_period, $year_month, $db_year_month, $date_month, $dbnext, $dbprevious, $last_year, $current_year, $account_val) }}
		</div>
	</div>
	<!-- End Heading -->
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
		<div class="col-xs-6 ml10 pm0 pull-left mb10">
			<a href="javascript:getmeetingReg('add');" class="btn btn-success box100"><span class="fa fa-plus"></span> {{ trans('messages.lbl_register') }}</a>
		</div>
	</div>
	<div class="mr10 ml10">
		<div class="minh400">
			<table class="tablealternate box100per">
				<colgroup>
				   <col width="3%">
				   <col width="6%">
				   <col width="11%">
				   <col width="15%">
				   <col width="15%">
				   <col width="15%">
				   <col width="12%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader fwb tac"> 
				  		<th class="tac">{{ trans('messages.lbl_sno') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_Date') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_startTime') }} 
				  		~ {{ trans('messages.lbl_endTime') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_cusname') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_branchname') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_personName') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_remarks') }}</th>
			   		</tr>
			   	</thead>
			   	<tbody>
					@forelse($details as $count => $data)
					<tr>
						<td class="tac">
						{{ ($details->currentpage()-1) * $details->perpage() + $count + 1 }}
						</td>
						<td class="tac">
	             			 <a href="javascript:getmeetingView('{{ $data->id }}');" 
	             			 	class="blue">
								{{ $data->date }}
							</a>
						</td>
						<td class="tac">
						{{ $data->startTime }} ~ {{ $data->endTime }}
						</td>
						<td>
						<a href="javascript:getmeetingHistory('{{ $data->customer_name }}');" 
							class="blue">
							{{ $data->customer_name }} 
             			 </a> 
						</td>
						<td>
						{{ ($data->branch_name != "") ? $data->branch_name : ''}}
						</td>
						<td>
						@if (mb_strlen($data->personName, 'UTF-8') > 30) 
							{{ mb_substr($data->personName, 0, 29, 'UTF-8')."..." }} 
						@else 
							{{ $data->personName }}  
						@endif
						</td>
						<td>
							{!! nl2br(e($data->Remarks)) !!}
						</td>
					</tr>
					@empty
					<tr>
						<td class="text-center colred" colspan="7">
						{{ trans('messages.lbl_nodatafound') }}</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>
		<div class="text-center">
			@if(!empty($details->total()))
				<span class="pull-left mt24">
				{{ $details->firstItem() }} ~ {{ $details->lastItem() }} / {{ $details->total() }}
				</span>
			{{ $details->links() }}
			<div class="CMN_display_block flr">
			{{ $details->linkspagelimit() }}
			</div>
			@endif 
		</div>
	{{ Form::close() }}
</article>
</div>
@endsection