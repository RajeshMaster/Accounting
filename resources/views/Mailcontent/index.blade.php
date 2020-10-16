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
	    display:inline-block !important;
	    height:30px !important;
	    padding:5px !important;
	}
</style>
{{ HTML::script('resources/assets/js/mailcontent.js') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="mail" class="DEC_flex_wrapper " data-category="mail mail_sub_2">
	{{ Form::open(array('name'=>'frmcontentmindex', 
						'id'=>'frmcontentmindex', 
						'url' => 'Mailcontent/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	    {{ Form::hidden('eid','', array('id' => 'eid')) }}
	    {{ Form::hidden('emailid','', array('id' => 'emailid')) }}
	    {{ Form::hidden('editflg','', array('id' => 'editflg')) }}
	    {{ Form::hidden('page', $request->page, array('id' => 'page')) }}
		{{ Form::hidden('plimit', $request->plimit, array('id' => 'plimit')) }}
<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/mailcontent.png') }}">
			<h2 class="pull-left pl5 mt10">{{ trans('messages.lbl_mailcontent') }}</h2>
		</div>
	</div>
<!-- End Heading -->
	<div class="col-xs-12 pm0 mt10 mb10 pull-left">
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
			<a href="javascript:fngotoregister('{{ $request->mainmenu }}');"  class="btn btn-success box125"><span class="fa fa-plus"></span> {{ trans('messages.lbl_register') }}</a>
		</div>
	</div>
	<div class="mr10 ml10 mt10">
		<div class="minh400">
			<table class="tablealternate box100per">
				<colgroup>
				   <col width="4%">
				   <col width="8%">
				   <col width="22%">
				   <col width="22%">
				   <col width="">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader fwb tac"> 
				  		<th class="tac">{{ trans('messages.lbl_sno') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_mailid') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_mailname') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_mailtype') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_subject') }}</th>
			   		</tr>
			   	</thead>
			   	<tbody>
			   		@forelse($getmaildetails as $key => $data)
			   			<tr>
			   				<td class="text-center">
			   					{{ ($getmaildetails->currentpage()-1) * $getmaildetails->perpage()+$key+1 }}
			   				</td>
			   				<td class="text-center">
			   					<a class="anchorstyle" href="javascript:gotomailview('{{ $data->id }}');">{{ $data->mailId }}</a>
			   				</td>
			   				<td>
			   					{{ $data->mailName }}
			   				</td>
			   				<td class="">
			   					{{ $data->typeName }}
			   				</td>
			   				<td class="">
			   					{{ $data->subject }}
			   				</td>
			   			</tr>
			   		@empty
						<tr>
							<td class="text-center" colspan="3" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
						</tr>
					@endforelse
			   	</tbody>
			</table>
		</div>
	</div>
	<div class="text-center ml10">
		@if(!empty($getmaildetails->total()))
			<span class="pull-left mt24">
				{{ $getmaildetails->firstItem() }} ~ {{ $getmaildetails->lastItem() }} / {{ $getmaildetails->total() }}
			</span>
		@endif 
		{{ $getmaildetails->links() }}
		<div class="CMN_display_block flr pr10">
			{{ $getmaildetails->linkspagelimit() }}
		</div>
	</div>
	{{ Form::close() }}
</article>
</div>
@endsection