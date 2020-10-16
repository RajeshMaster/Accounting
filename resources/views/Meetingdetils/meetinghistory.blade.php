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
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::style('resources/assets/css/switch.css') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper " data-category="customer customer_sub_3">
{{ Form::open(array('name'=>'meetinghistoryfrm', 
						'id'=>'meetinghistoryfrm', 
						'url' => 'MeetingDetails/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
	{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	{{ Form::hidden('customer_name', $request->customer_name , array('id' => 'customer_name')) }}
	{{ Form::hidden('hisFlg','1', array('id' => 'hisFlg')) }}
	<!-- Start Heading -->
	<div class="row hline">
	<div class="col-xs-12">
			<img class="pull-left box35 mt15" src="{{ URL::asset('resources/assets/images/meetingdet.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_meetingdet') }}</h2>
		</div>
	</div>
	<!-- End Heading -->
	<div class="col-xs-12 pm0 pull-left mt10">
		<div class="box100per mb5">
			<div class="pull-left ml10 mb5">
				<a href="javascript:goindexpages('{{ $request->mainmenu }}');" class="pageload btn btn-info box80"><span class="fa fa-arrow-left"></span> 
				{{ trans('messages.lbl_back') }}</a>
			</div>
		</div>
	</div>
		<div class="CMN_display_block ml10">
		 	<label>{{ trans('messages.lbl_cusname') }}:</label>
		</div>
		<div class="25per ml5 CMN_display_block fwb black">
		 	{{ $customerName }}
		</div>
	<div class="mr10 ml10">
		<div class="minh400">
			<table class="tablealternate box100per">
				<colgroup>
				   <col width="2%">
				   <col width="4%">
				   <col width="8%">
				   <col width="14%">
				   <col width="14%">
				   <col width="14%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader fwb tac">
				  		<th class="tac">{{ trans('messages.lbl_sno') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_Date') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_startTime') }} ~ {{ trans('messages.lbl_endTime') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_branchname') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_personName') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_remarks') }}</th>
			   		</tr>
			   	</thead>
			   	<tbody>
					@forelse($historydetails as $count => $data)
					<tr>
						<td class="tac">
						{{ ($historydetails->currentpage()-1) * $historydetails->perpage() + $count + 1 }}
						</td>
						<td class="tac">
							{{ $data->date }}
						</td>
						<td class="tac">
							{{ $data->startTime }} ~ {{ $data->endTime }}
						</td>
						<td>
							{{ $data->branch_name }} 
						</td>
						<td>
							{{ $data->personName }}  
						</td>
						<td>
							{!! nl2br(e($data->Remarks)) !!}
						</td>
					</tr>
					@empty
					<tr>
						<td class="text-center" colspan="6" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>
		<div class="text-center">
			@if(!empty($historydetails->total()))
			<span class="pull-left mt24">
			{{ $historydetails->firstItem() }} ~ {{ $historydetails->lastItem() }} / {{ $historydetails->total() }}
			</span>
			{{ $historydetails->links() }}
			<div class="CMN_display_block flr">
			{{ $historydetails->linkspagelimit() }}
			</div>
			@endif 
		</div>
{{ Form::close() }}
</article>
</div>
@endsection