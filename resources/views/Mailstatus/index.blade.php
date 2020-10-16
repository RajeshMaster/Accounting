@extends('layouts.app')
@section('content')
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
<style type="text/css">
	.alertboxalign {
    	margin-bottom: -60px !important;
	}
	.alert {
		margin-top: 5px;
	    display:inline-block !important;
	    height:30px !important;
	    padding:5px !important;
	}
</style>
{{ HTML::script('resources/assets/js/mailstatus.js') }}
<div class="CMN_display_block box100per" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="mail" class="DEC_flex_wrapper " data-category="mail mail_sub_1">
	{{ Form::open(array('name'=>'frmmailstatusindex', 
						'id'=>'frmmailstatusindex', 
						'url' => 'Estimation/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		
		{{ Form::hidden('hiddenpage', '', array('id' => 'hiddenpage')) }}
		{{ Form::hidden('page', $request->page, array('id' => 'page')) }}
		{{ Form::hidden('hiddenplimit', '', array('id' => 'hiddenplimit')) }}
		{{ Form::hidden('plimit', $request->plimit, array('id' => 'plimit')) }}
		{{ Form::hidden('statusid', $request->statusid, array('id' => 'statusid')) }}
		{{ Form::hidden('sendfilter', $request->sendfilter, array('id' => 'sendfilter')) }}
		{{ Form::hidden('hiddensendfilter', '' , array('id' => 'hiddensendfilter')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
		{{ Form::hidden('customerid', '' , array('id' => 'customerid')) }}
		{{ Form::hidden('customer_name', '' , array('id' => 'customer_name')) }}
	<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt15" src="{{ URL::asset('resources/assets/images/mail.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_mailstatus') }}</h2>
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
		<div class="box65per pm0 CMN_display_block pull-left mt4 mb4">
			<a class="btn btn-link  {{ $disabledsent }}" href="javascript:filtermail('1');"> {{ trans('messages.lbl_sent') }} </a>
			<span>|</span>
			<a class="btn btn-link {{ $disableddraft }}" href="javascript:filtermail('0');"> {{ trans('messages.lbl_draft') }} </a>
		</div>
	</div>
	<div class="mr10 ml10 mt15">
		<div class="minh400">
			<table class="tablealternate col-xs-12" style="table-layout: fixed;">
				<colgroup>
				   <col width="4%">
				   <col width="8%">
				   <col width="35%">
				   <col width="">
				   <col width="12%">
				   <col width="4%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader tac"> 
				  		<th class="tac">{{ trans('messages.lbl_sno') }}</th>
				  		<?php if($request->sendfilter == "0") { ?>
				  		<th class="tac">{{ trans('messages.lbl_draftdate') }}</th>
				  		<?php } else  { ?>
				  		<th class="tac">{{ trans('messages.lbl_senddate') }}</th>
				  		<?php  } ?>
				  		<th class="tac">{{ trans('messages.lbl_companyname') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_recipient') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_subject') }}</th>
				  		<th class="tac"></th>
			   		</tr>
			   	</thead>
			   	<tbody>
			   		{{--*/ $sno = $allmailstatus->firstItem() /*--}}
			   		{{ $temp = ""}}
			   		{{ $tempcomp = ""}}
			   		{{ $row = ""}}
			   		@forelse($allmailstatus as $key => $data)
			   			{{--*/ $loc = substr($data->updatedDate,0,10) /*--}}
			   			{{--*/ $loccomp = $data->customer_name /*--}}
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
                      	@if($loccomp == $tempcomp && $loc == $temp) 
                        	{{--*/ $style_tdcomp = 'border-top: hidden;' /*--}}
                      	@else
                        	{{--*/ $style_tdcomp = '' /*--}}
                      	@endif
		   			<tr style="{{$style_tr}}">
		   				<td class="tac">{{ $sno }}</td>
		   				<td class="tac" style="{{$style_td}}">@if($loc != $temp){{ substr($data->updatedDate,0,10) }}@endif</td>
		   				<td class="tal" style="{{ $style_tdcomp }}">@if($loc == $temp && $loccomp == $tempcomp) @else <a class="anchorstyle" style="cursor: pointer;color: blue;" href="javascript:fnmailhistoryview('{{ $data->companyId }}','{{ $request->mainmenu }}','{{ $data->customer_name }}')"> {{ $data->customer_name }}</a>@endif</td>
		   				<td class="tal">
		   					<div><span class="" style="color: brown;">To :</span> <?php echo preg_replace('/(?<!\d),|,(?!\d{3})/', ', ', $data->toMail); ?></div>
		   					<?php if ($data->cc != "") {?>
		   					<div style="font-size: 12px;">&nbsp;<span style="color: brown;">cc :</span> <?php echo preg_replace('/(?<!\d),|,(?!\d{3})/', ', ', $data->cc); ?></div>
		   					<?php } ?>
		   					<!-- <div class="col-xs-12 pm0">
								<?php $extomail = explode(",", $data->toMail); 
								print_r($data->cc);?>
								<div class="col-xs-9 pm0">
									<?php for ($i=0; $i < 1; $i++) { ?>
									<div class="col-xs-12 pm0">
										{{ $extomail[$i] }}
									</div>
									<?php } ?>
								</div>
		   					</div> -->
		   				</td>
		   				<td class="tal">{{ $data->subject }}</td>
		   				<td class="tac"><a href="javascript:fnstatusView({{ $data->id }});"><img title="{{ trans('messages.lbl_view') }}" class=" box15" src="{{ URL::asset('resources/assets/images/ourdetails.png') }}"></a></td>
		   			</tr>
			   		{{--*/ $temp = $loc /*--}}
			   		{{--*/ $tempcomp = $loccomp /*--}}
			   		{{--*/ $sno = $sno + 1 /*--}}
			   		@empty
						<tr>
							<td class="text-center" colspan="6" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
						</tr>
					@endforelse
			   	</tbody>
			</table>
		</div>
		@if(isset($allmailstatus[0]->id))
		<div class="text-center">
			@if(!empty($allmailstatus->total()))
				<span class="pull-left mt24">
					{{ $allmailstatus->firstItem() }} ~ {{ $allmailstatus->lastItem() }} / {{ $allmailstatus->total() }}
				</span>
			@endif 
			{{ $allmailstatus->links() }}
			<div class="CMN_display_block flr">
				{{ $allmailstatus->linkspagelimit() }}
			</div>
		</div>
		@endif
	</div>
</article>
</div>
@endsection