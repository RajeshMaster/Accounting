@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/accbankpassbookdtls.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::script('resources/assets/js/lib/additional-methods.min.js') }}
{{ HTML::script('resources/assets/js/lib/lightbox.js') }}
{{ HTML::style('resources/assets/css/lib/lightbox.css') }}
@php use App\Http\Helpers; @endphp
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
	var dates = '<?php echo date('Y-m-d'); ?>';
	var accessDate = '<?php echo Auth::user()->accessDate; ?>';
	var userclassification = '<?php echo Auth::user()->userclassification; ?>';
	$(document).ready(function() {
		setDatePicker("from_date");
		setDatePicker("to_date");
	});
</script>

<style type="text/css">
	.ime_mode_disable {
		ime-mode:disabled;
	}
	.disabled{
		cursor:not-allowed !important;
	}
</style>

<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_7">
		{{ Form::open(array('name'=>'frmBankPassportaddEdit','id'=>'frmBankPassportaddEdit', 
			'url' => 'AccBankPassbookDtls/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
			'files'=>true,'method' => 'POST')) }}

		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('edit_flg', $request->edit_flg, array('id' => 'edit_flg')) }}
		{{ Form::hidden('edit_id', $request->edit_id, array('id' => 'edit_id')) }}

		<div class="row hline pm0">
			<div class="col-xs-12">
				<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/expenses.png') }}">
				<h2 class="pull-left pl5 mt10">{{ trans('messages.lbl_passbookdetail') }}</h2>
				<h2 class="pull-left mt10">・</h2>
				@if($request->edit_flg == 2)
					<h2 class="pull-left mt10 red">{{ trans('messages.lbl_edit') }}</h2>
				@elseif($request->edit_flg == 3)
					<h2 class="pull-left mt10 blue">{{ trans('messages.lbl_copy') }}</h2>
				@else
					<h2 class="pull-left mt10 green">{{ trans('messages.lbl_register') }}</h2>
				@endif
			</div>
		</div>
		<div class="col-xs-12 pl5 pr5">
		<fieldset>
			<div id="errorSectiondisplay" align="center" class="box100per"></div>
			<div class="col-xs-12 mt15">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_bank_name') }}
						<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::select('bankId',[null=>''] + $bankDetail, (isset($accBankPassbook[0]->bankId)) ? $accBankPassbook[0]->bankId : '', array('name' =>'bankId',
										'id'=>'bankId',
										'data-label' => trans('messages.lbl_bank'),
										'class'=>'pl5 widthauto'))}}
				</div>
			</div>

			<div class="col-xs-12 mt10">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_pageNo') }}
						<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					@if(isset($accBankPassbook[0]->pageNo))
						{{--*/ $pageNo = explode('-',$accBankPassbook[0]->pageNo);/*--}}
						@if(!isset($pageNo[0]))  {{ $pageNo[0]= "" }} @endif
						@if(!isset($pageNo[1]))  {{ $pageNo[1]= "" }} @endif
					@else
						{{ $pageNo[0]= "" }}
						{{ $pageNo[1]= "" }}
					@endif
					{{ Form::text('pageNoFrom', $pageNo[0],
							array('id'=>'pageNoFrom', 
									'name' => 'pageNoFrom',
									'maxlength' => '2',
									'autocomplete' => 'off',
									'data-label' => trans('messages.lbl_pageNo'),
									'class'=>'box5per form-control pl5',
									'onkeydown' => 'return nextfield("pageNoFrom","pageNoTo","2",event)',
									'onkeypress' => 'return isNumberKey(event)')) }}
					<span class="ml5 mr5"> - </span>
					{{ Form::text('pageNoTo', $pageNo[1],
							array('id'=>'pageNoTo', 
									'name' => 'pageNoTo',
									'maxlength' => '2',
									'autocomplete' => 'off',
									'data-label' => trans('messages.lbl_pageNo'),
									'class'=>'box5per form-control pl5',
									'onkeypress' => 'return isNumberKey(event)')) }}
				</div>
			</div>

			<div class="col-xs-12 mt10">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_daterange') }}
						<span class="fr ml2 red"> * </span>
					</label>
				</div>
				<div class="col-xs-9">
					{{ Form::text('dateRangeFrom',(isset($accBankPassbook[0]->dateRangeFrom)) ? $accBankPassbook[0]->dateRangeFrom : '',
							array('id'=>'dateRangeFrom', 
									'name' => 'dateRangeFrom',
									'autocomplete' => 'off',
									'data-label' => trans('messages.lbl_daterange'),
									'class'=>'box11per form-control pl5 from_date')) }}
					<label class="mt10 ml2 fa fa-calendar fa-lg" for="dateRangeFrom" 
							aria-hidden="true">
					</label>
					<a href="javascript:getdate(1);" class="anchorstyle">
						<img title="Current Date" class="box15" 
						src="{{ URL::asset('resources/assets/images/add_date.png') }}">
					</a>
					<span class="ml5 mr5"> - </span></label>
					{{ Form::text('dateRangeTo',(isset($accBankPassbook[0]->dateRangeTo)) ? $accBankPassbook[0]->dateRangeTo : '',
							array('id'=>'dateRangeTo', 
									'name' => 'dateRangeTo',
									'autocomplete' => 'off',
									'data-label' => trans('messages.lbl_daterange'),
									'class'=>'box11per form-control pl5 to_date')) }}
					<label class="mt10 ml2 fa fa-calendar fa-lg" for="dateRangeTo" 
							aria-hidden="true">
					</label>
					<a href="javascript:getdate(2);" class="anchorstyle">
						<img title="Current Date" class="box15" 
						src="{{ URL::asset('resources/assets/images/add_date.png') }}">
					</a>
				</div>
			</div>

			<div class="col-xs-12 mt10 mb15">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_pgupload') }}
						<span class="fr ml2 red"> &nbsp;&nbsp; </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::file('bankPassbook',array(
											'class' => 'pull-left box350',
											'id' => 'bankPassbook',
											'name' => 'bankPassbook',
											'style' => 'height:23px;',
											'accept' => 'image/x-png,image/gif,image/jpeg',
											'data-label' => trans('messages.lbl_pgupload'))) }}
					<span>&nbsp;(Ex: Image File Only)</span>
					@if(isset($accBankPassbook[0]) && $request->edit_flg == 2)
						<?php $file_url = '../AccountingUpload/AccBankPassbook/' . $accBankPassbook[0]->fileDtl; ?>
						@if(isset($accBankPassbook[0]->fileDtl) && file_exists($file_url))
							<img width="20" height="20" name="empimg" id="empimg" 
							class="ml5 box20 viewPic3by2" src="{{ URL::asset('../../../../AccountingUpload/AccBankPassbook').'/'.$accBankPassbook[0]->fileDtl }}">
							{{ Form::hidden('pdffiles', $accBankPassbook[0]->fileDtl , array('id' => 'pdffiles')) }}
						@else
						@endif
					@endif
				</div>
			</div>

		</fieldset>
		<fieldset style="background-color: #DDF1FA;">

			<div class="form-group">
				<div align="center" class="mt5">
					@if($request->edit_flg == 2)
						<button type="submit" class="btn btn-warning add box100 ml5 addeditprocess">
							<i class="fa fa-edit" aria-hidden="true"></i> 
							{{ trans('messages.lbl_update') }}
						</button>&nbsp;
					@else
						<button type="submit" class="btn btn-success add box100 ml5 addeditprocess">
							<i class="fa fa-plus" aria-hidden="true"></i> 
							{{ trans('messages.lbl_register') }}
						</button>&nbsp;
					@endif
					<a href="javascript:gotoindexpage();" 
						class="btn btn-danger box120 white">
						<i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
					</a>
				</div>
			</div>

		</fieldset>
		</div>

	{{ Form::close() }}

	{{ Form::open(array('name'=>'frmBankPassportaaddeditcancel',
						'id'=>'frmBankPassportaaddeditcancel', 
						'url' => 'AccBankPassbookDtls/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,'method' => 'POST')) }}

		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}

	{{ Form::close() }}

	<div id="empnamepopup" class="modal fade">
		<div id="login-overlay">
			<div class="modal-content">
				<!-- Popup will be loaded here -->
			</div>
		</div>
	</div>

</article>
</div>
@endsection
