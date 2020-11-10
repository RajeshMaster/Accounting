@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/creditcardpay.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::script('resources/assets/js/lib/additional-methods.min.js') }}
{{ HTML::script('resources/assets/js/lib/lightbox.js') }}
{{ HTML::style('resources/assets/css/lib/lightbox.css') }}
@php use App\Http\Helpers; @endphp
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var dates = '<?php echo date('Y-m-d'); ?>';
	var accessDate = '<?php echo Auth::user()->accessDate; ?>';
	var userclassification = '<?php echo Auth::user()->userclassification; ?>';
	$(document).ready(function() {
		$("#transferbutton").attr("disabled", "disabled");
		if ($('#transferContent').val() != "") {
			$("#browseEmp").attr("disabled", "disabled");
			$("#clearEmp").attr("disabled", "disabled");
		} else {
			$("#browseEmp").removeAttr("disabled");
			$("#clearEmp").removeAttr("disabled");
		}
		if ($('#txt_empname').val() != "") {
			$("#transferContent").attr("disabled", "disabled");
		} else {
			$("#transferContent").removeAttr("disabled");
		}
		if (userclassification == 1) {
			accessDate = setNextDay(accessDate);
			setDatePickerAfterAccessDate("dob", accessDate);
		} else {
			setDatePicker("dob");
		}
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
<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_2">
		{{ Form::open(array('name'=>'creditCardPayaddedit','id'=>'creditCardPayaddedit', 
			'url' => 'CreditCardPay/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
			'enctype' => 'multipart/form-data',
			'files'=>true,'method' => 'POST')) }}

		{{ Form::hidden('edit_flg', $request->edit_flg, array('id' => 'edit_flg')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('editId', $request->editId, array('id' => 'editId')) }}
		{{ Form::hidden('selectedMonth', '', array('id' => 'selectedMonth')) }}

		<div class="row hline pm0">
			<div class="col-xs-12">
				<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/expenses_icon.png') }}">
				<h2 class="pull-left pl5 mt10">{{ trans('messages.lbl_creditCardPay') }}</h2>
				<h2 class="pull-left mt10">・</h2>
				@if($request->edit_flg == 1)
					<h2 class="pull-left mt10 red">{{ trans('messages.lbl_edit') }}</h2>
				@else
					<h2 class="pull-left mt10 green">{{ trans('messages.lbl_register') }}</h2>
				@endif
			</div>
		</div>
		
		<div class="col-xs-12 pl5 pr5">
		<fieldset>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_creditCardName') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::select('creditCard',[null=>'']+$creditcard,(isset($editData[0]->bankIdFrom)) ? 
														$editData[0]->bankIdFrom.'-'.$editData[0]->accountNumberFrom : '',						array('name' =>'creditCard',
																	'id'=>'creditCard',
																	'onchange'=>'fnGetInsertedValue();',
																	'data-label' => trans('messages.lbl_creditCard'),
																	'class'=>'pl5 widthauto'))}}
				</div>
			</div>

			<div class="col-xs-12 mt10">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_Date') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
						{{ Form::text('mainDate',(isset($transferEdit[0]->date)) ? $transferEdit[0]->date : '',
								array('id'=>'mainDate',
									'name' => 'mainDate',
									'autocomplete' => 'off',
									'class'=>'box12per txt_startdate form-control dob',
									'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
									'data-label' => trans('messages.lbl_Date'),
									'maxlength' => '10')) }}
						<label class="fa fa-calendar fa-lg" for="mainDate" aria-hidden="true">
						</label>
						<a href="javascript:getdate('Transfer');" class="anchorstyle">
							<img title="Current Date" class="box15" src="{{ URL::asset('resources/assets/images/add_date.png') }}"></a>
				</div>
			</div>
			
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_expmonth') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::select('mainYear',[null=>'']+$yearArr,(isset($year)) ? $year : '',						
																	array('name' =>'mainYear',
																	'id'=>'mainYear',
																	'onchange'=>'fnGetInsertedValue();',
																	'data-label' => trans('messages.lbl_creditCard'),
																	'class'=>'pl5 widthauto'))}}
					@for($i = 1; $i <= 12; $i++)
						{{ Form::checkbox('month',$i,'',['id' => 'month'.$i,
													'class' => 'checkboxid',
													'style' => 'display:inline-block']) }}
						{{ $i }}月							
					@endfor
				</div>
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>Csv File<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
				<input type="file" name="fileToUpload" accept=".csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" id="fileToUpload">
					<span>&nbsp;(Ex: Csv File Only)</span>
		
				</div>
			</div>


			<!-- <div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_bill') }}<span class="fr ml2 red"> &nbsp;&nbsp; </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::file('transferBill',array(
											'class' => 'pull-left box350',
											'id' => 'transferBill',
											'name' => 'transferBill',
											'style' => 'height:23px;',
											'accept' => 'image/x-png,image/gif,image/jpeg',
											'data-label' => trans('messages.lbl_bill'))) }}
					<span>&nbsp;(Ex: Image File Only)</span>
					@if(isset($transferEdit) && $request->edit_flg == 1)
					<?php $file_url = '../AccountingUpload/Accounting/' . $transferEdit[0]->fileDtl; ?>
					@if(isset($transferEdit[0]->fileDtl) && file_exists($file_url))
						<img width="20" height="20" name="empimg" id="empimg" 
						class="ml5 box20 viewPic3by2" src="{{ URL::asset('../../../../AccountingUpload/Accounting').'/'.$transferEdit[0]->fileDtl }}">
						{{ Form::hidden('pdffiles', $transferEdit[0]->fileDtl , array('id' => 'pdffiles')) }}
					@else
					@endif
				@endif
				</div>
			</div> -->
		</fieldset>
		<fieldset style="background-color: #DDF1FA;">

			<div class="form-group">
				<div align="center" class="mt5">
					@if($request->edit_flg == 1)
						<button type="submit" class="btn btn-warning add box100 ml5 creditCardAddedit">
							<i class="fa fa-edit" aria-hidden="true"></i> 
							{{ trans('messages.lbl_update') }}
						</button>&nbsp;
					@else
						<button type="submit" class="btn btn-success add box100 ml5 creditCardAddedit">
							<i class="fa fa-plus" aria-hidden="true"></i> 
							{{ trans('messages.lbl_register') }}
						</button>&nbsp;
					@endif
					<a href="javascript:gotoindexpage('addedit');" 
						class="btn btn-danger box120 white">
						<i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
					</a>
				</div>
			</div>

		</fieldset>
		</div>

	{{ Form::close() }}

	{{ Form::open(array('name'=>'creditaddeditcancel', 'id'=>'creditaddeditcancel', 
						'url' => 'CreditCardPay/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}

	{{ Form::close() }}

	<div id="detailPopup" class="modal fade">
		<div id="login-overlay">
			<div class="modal-content">
				<!-- Popup will be loaded here -->
			</div>
		</div>
	</div>

</article>
</div>
@endsection
