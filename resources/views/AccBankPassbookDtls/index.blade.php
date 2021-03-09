@extends('layouts.app')
@section('content')
@php use App\Http\Helpers; @endphp
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
	$(document).ready(function() {
		setDatePicker("from_date");
		setDatePicker("to_date");
		if($('#hidAuth').val() == "5" || mainmenu == "AuditingBankPassbook"){
			$(".divdisplay").css("display", "none");
			$('.columnspan').attr('colspan','2');
			$('.columnspannodata').attr('colspan','5');
			$(".Auddivdisplay").css("");
		} else {
			$(".divdisplay").css("");
			$('.columnspan').attr('colspan','2');
			$('.columnspannodata').attr('colspan','6');
			$(".Auddivdisplay").css("display", "none");
		}
	});

</script>
<style type="text/css">
	.alertboxalign {
		margin-bottom: -60px !important;
	}
	.alert {
		margin-top: 10px;
		display:inline-block !important;
		height:30px !important;
		padding:5px !important;
	}

</style>
{{ HTML::script('resources/assets/js/accbankpassbookdtls.js') }}
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::style('resources/assets/css/switch.css') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::script('resources/assets/js/lib/lightbox.js') }}
{{ HTML::style('resources/assets/css/lib/lightbox.css') }}
<div class="CMN_display_block" id="main_contents" style="width: 100%">
<!-- article to select the main&sub menu -->
@if($request->mainmenu =="AuditingBankPassbook")
<article id="auditing" class="DEC_flex_wrapper " data-category="auditing auditing_sub_8">
@else
<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_7">
@endif

	{{ Form::open(array('name'=>'frmAccBankPassbook', 
						'id'=>'frmAccBankPassbook', 
						'url' => 'AccBankPassbookDtls/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}

		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	    {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	    {{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}

		{{ Form::hidden('edit_flg', '', array('id' => 'edit_flg')) }}
		{{ Form::hidden('edit_id', '', array('id' => 'edit_id')) }}
		{{ Form::hidden('bank_id', '', array('id' => 'bank_id')) }}
		{{ Form::hidden('bnk_id', '', array('id' => 'bnk_id')) }}
		{{ Form::hidden('acc_no', '', array('id' => 'acc_no')) }}
		{{ Form::hidden('del_flg', '', array('id' => 'del_flg')) }}
		{{ Form::hidden('searchmethod', '', array('id' => 'searchmethod')) }}

		<!-- Year Bar Start -->
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		<!-- Year Bar End -->


	<!-- Start Heading -->
	<div class="row hline" >
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/expenses.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_passbookdetail') }}</h2>
		</div>
	</div>

	<div class=" pr10 pl10 ">
		<div class="mt10 ">
			@if($request->searchmethod == "6" || $request->searchmethod == "")
				 {{ Helpers::displayYear($prev_yrs,$cur_year,$total_yrs,$curtime) }}
			@endif
		</div>
	</div>

	<div class="col-xs-12 pull-left pl10 pr10">
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
		<div class="col-xs-6  pm0 pull-left mt10 divdisplay">
			<a href="javascript:addedit(1,'','');" class="btn btn-success box100"><span class="fa fa-plus"></span> {{ trans('messages.lbl_register') }}</a>
		</div>
	</div>

	<div class="pt43 minh300 pl10 pr10 " style="padding:3px 3px 20px">
		<table class="tablealternate CMN_tblfixed mt10">
			<colgroup>
				<col width="5%">
				<col width="">
				<col width="10%">
				<col width="25%">
				<col width="10%">
				<col class="divdisplay" width="10%">
			</colgroup>

			<thead class="CMN_tbltheadcolor">
				<tr id="data">
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam">{{ trans('messages.lbl_bank_name') }} / {{ trans('messages.lbl_account_no') }}</th>
					<th class="vam">{{ trans('messages.lbl_pageNo') }}</th>
					<th class="vam">{{ trans('messages.lbl_daterange') }}</th>
					<th class="vam">{{ trans('messages.lbl_file') }}</th>
					<th class="vam divdisplay"></th>
				</tr>
			</thead>
			<tbody>
				@php 
					$i = 0; 
					$lastBankId = "";
				@endphp
				@forelse($accBankPassbook as $key => $data)
					@if($lastBankId != $data['bankId'])
						<tr style="background-color: #f1a2a2">
							<td colspan="6"> 
								<a href="javascript:bankNameclick('{{ $data['bankId'] }}');" 
								class="blue">
									{{ $data['Bank_NickName'] }}
										&nbsp<span class="fwb"> - </span>&nbsp
									{{ $data['AccNo'] }}
								</a>
							</td>
						</tr>
					@endif
					<tr>
						<td>{{ $i+1 }}</td>
						<td>
							{{ $data['Bank_NickName'] }}
							&nbsp<span class="fwb"> - </span>&nbsp
							{{ $data['AccNo'] }}
						</td>
						<td class="vam tac"> 
							{{ $data['pageNoFrom'] }}
							&nbsp<span class="fwb"> - </span>&nbsp
							{{ $data['pageNoTo'] }}
						</td>
						<td class="vam tac">
							{{ $data['dateRangeFrom'] }}
							&nbsp<span class="fwb"> - </span>&nbsp
							{{ $data['dateRangeTo'] }}
						</td>
						<td align="center">
							@if($data['fileDtl'] != "")
								@php $fileName = "AccBankPassbook_".$data['id']; @endphp
								<a onclick="fileImgPopup('{{ $fileName }}','{{ $request->selYear }}','{{ $data['bankId'] }}')" 
									id ="filelink<?php echo $fileName; ?>" class ="csrp">
									<img width="20" height="20" name="empimg" id="empimg"
									class=" box20 viewPic3by2" src= "{{ URL::asset('../../../../AccountingUpload/AccBankPassbook').'/'.$data['fileDtl'] }}">
								</a>
							@endif
						</td>
						<td class="divdisplay" align="center">
							<a href="javascript:addedit(2,'{{ $data['id'] }}','{{ $data['bankId'] }}');">
								{{ trans('messages.lbl_edit') }}
							</a> 
							@if($data['nxtFlg'] == 0)
								&nbsp<span class="fwb"> / </span>&nbsp
								<a href="javascript:addedit(3,'{{ $data['id'] }}','{{ $data['bankId'] }}');">
									{{ trans('messages.lbl_next') }}
								</a>
							@endif
						</td>
					</tr>
					
					@php 
						$i++;
						$lastBankId = $data['bankId'];
					@endphp
				@empty
					<tr>
						<td class="text-center columnspannodata" style="color: red;">
							{{ trans('messages.lbl_nodatafound') }}</td>
					</tr>
				@endforelse

			</tbody>
		</table>

	</div>
	<div class="text-center pl14">
		@if(!empty($bankPassbookindex->total()))
			<span class="pull-left mt24">
				{{ $bankPassbookindex->firstItem() }} ~ {{ $bankPassbookindex->lastItem() }} / {{ $bankPassbookindex->total() }}
			</span>
			{{ $bankPassbookindex->links() }}
			<div class="CMN_display_block flr pr14">
				{{ $bankPassbookindex->linkspagelimit() }}
			</div>
		@endif 
	</div>

</article>
</div>

<div id="imgViewPopup" class="modal fade">
	<div id="login-overlay">
		<div class="modal-content">
			<!-- Popup will be loaded here -->
		</div>
	</div>
</div>

@endsection