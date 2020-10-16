@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/meeting.js') }}
<script type="text/javascript">
	var datetime = '@php echo date('Ymdhis') @endphp';
	var mainmenu = '@php echo $request->mainmenu @endphp';
$(document).ready(function() {
	 $('salary').blur(function() {
	 	$('.salary').formatCurrency();
	 });
});
</script>
<div class="CMN_display_block box100per" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper " data-category="customer customer_sub_3">
	{{ Form::open(array('name'=>'meetingdetailsviewfrm', 
					'id'=>'meetingdetailsviewfrm', 
					'url' => 'MeetingDetails/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
					'files'=>true,
					'method' => 'POST')) }}
	{{ Form::hidden('editflg', '', array('id' => 'editflg')) }}
	{{ Form::hidden('editid', $viewdt[0]->id , array('id' => 'editid')) }}
	{{ Form::hidden('viewid', $request->request , array('id' => 'viewid')) }}
	{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
	{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
		<img class="pull-left box35 mt15" src="{{ URL::asset('resources/assets/images/meetingdet.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_meetingdet') }}<span>・</span><span class="colbl">{{ trans('messages.lbl_view') }}</span></h2>
		</div>
	</div>
	<div class="pb10"></div>
	<!-- End Heading -->
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
<div class="pl5 pr5" >
		<div class="pull-left ml5">
			<a href="javascript:goindexpage('{{ $request->mainmenu }}');" class="pageload btn btn-info box80"><span class="fa fa-arrow-left"></span> {{ trans('messages.lbl_back') }}</a>
		</div>
			<div class="pull-right pr10">
				<a href="javascript:javascript:contractemployeeedit('copy','{{ $viewdt[0]->id }}');" class="btn btn-success box80 pull-right pr10">
				<span class="fa fa-copy mr5"></span>{{ trans('messages.lbl_copy') }}</a>
			</div>
			<div class="pull-right pr10">
				<a href="javascript:contractemployeeedit('edit','{{ $viewdt[0]->id }}');" class="btn btn-warning box80"><span class="fa fa-pencil"></span> {{ trans('messages.lbl_edit') }}</a>
			</div>
	<div class="col-xs-12 pl5 pr5">
	<fieldset>
		<div class="box60per CMN_display_block">
			<div class="col-xs-12 mt10">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_Date') }}</label>
				</div>
				<div class="col-xs-7 text-left">
					{{ $viewdt[0]->date }}
				</div>
			</div>
			<div class="col-xs-12 mt7">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_startTime') }} ~ {{ trans('messages.lbl_endTime') }}</label>
				</div>
				<div class="col-xs-7 text-left">
					{{ $viewdt[0]->startTime }} ~ {{ $viewdt[0]->endTime }}
				</div>
			</div>
			<div class="col-xs-12 mt7">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_cusname') }}</label>
				</div>
				<div class="col-xs-7 text-left">
					{{ $viewdt[0]->customer_name }}
				</div>
			</div>
			<div class="col-xs-12 mt7">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_branchname') }}</label>
				</div>
				<div class="col-xs-7 text-left">
					{{ (!empty($viewdt[0]->branch_name) ?  $viewdt[0]->branch_name : "Nill")  }}
				</div>
			</div>
			<div class="col-xs-12 mt7">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_personName') }}</label>
				</div>
				<div class="col-xs-7 text-left">
					{{ $viewdt[0]->personName }}
				</div>
			</div>
			<div class="col-xs-12 mt7">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_remarks') }}</label>
				</div>
				<div class="col-xs-7 text-left">
					{!! (!empty($viewdt[0]->Remarks) ?  nl2br(e($viewdt[0]->Remarks)) : "Nill") !!}
				</div>
			</div>
		</div>
	</fieldset>
	</div>
</div>
{{ Form::close() }}
</article>
</div>
<div class="CMN_display_block pb10"></div>
@endsection