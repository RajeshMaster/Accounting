@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/expensesData.js') }}
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
		if ($('#expensesDataContent').val() != "") {
			$("#browseEmp").attr("disabled", "disabled");
			$("#clearEmp").attr("disabled", "disabled");
		} else {
			$("#browseEmp").removeAttr("disabled");
			$("#clearEmp").removeAttr("disabled");
		}
		if ($('#txt_empname').val() != "") {
			$("#expensesDataContent").attr("disabled", "disabled");
		} else {
			$("#expensesDataContent").removeAttr("disabled");
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
<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_6">
		{{ Form::open(array('name'=>'frmaddEdit','id'=>'frmaddEdit', 
			'url' => 'ExpensesData/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
			'files'=>true,'method' => 'POST')) }}

		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('edit_flg', $request->edit_flg, array('id' => 'edit_flg')) }}
		{{ Form::hidden('editId', $request->editId, array('id' => 'editId')) }}

		<div class="row hline pm0">
			<div class="col-xs-12">
				<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/expenses.png') }}">
				<h2 class="pull-left pl5 mt10">{{ trans('messages.lbl_expensesData') }}</h2>
				<h2 class="pull-left mt10">・</h2>
				@if($request->edit_flg == 1)
					<h2 class="pull-left mt10 red">{{ trans('messages.lbl_edit') }}</h2>
				@elseif($request->edit_flg == 2)
					<h2 class="pull-left mt10 blue">{{ trans('messages.lbl_copy') }}</h2>
				@else
					<h2 class="pull-left mt10 green">{{ trans('messages.lbl_register') }}</h2>
				@endif
			</div>
		</div>
		<div class="col-xs-12 pl5 pr5">
		<fieldset>
			<div id="errorSectiondisplay" align="center" class="box100per"></div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_bank_name') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::select('bankIdAccNo',[null=>'']+$bankDetail,(isset($expensesDataEdit[0]->bankIdFrom)) ? $expensesDataEdit[0]->bankIdFrom.'-'.$expensesDataEdit[0]->accountNumberFrom : '',
							array('name' =>'bankIdAccNo',
										'id'=>'bankIdAccNo',
										'data-label' => trans('messages.lbl_bank'),
										'class'=>'pl5 widthauto'))}}
				</div>
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_mainsubject') }}<span class="fr ml2 red" style="visibility: hidden"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::select('subjectId',[null=>''] + $mainSubDetail,(isset($expensesDataEdit[0]->subjectId)) ? $expensesDataEdit[0]->subjectId : '',
							array('id'=>'subjectId',
									'name' => 'subjectId',
									'class'=>'widthauto ime_mode_active',
									'maxlength' => 10,
									'data-label' => trans('messages.lbl_mainsubject'))) }}
				</div>
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_empName') }}
						<span class="fr ml2 red" id="emprequired"> * </span>
					</label>
				</div>
				<div class="col-xs-9">
					{{ Form::hidden('empid', '', array('id' => 'empid')) }}
					{{ Form::hidden('empID',(isset($expensesDataEdit[0]->empId)) ? $expensesDataEdit[0]->empId : '',array('id'=>'empID')) }}
					{{ Form::text('txt_empname',(isset($expensesDataEdit[0]->Empname)) ? $expensesDataEdit[0]->Empname : '',
							array('id'=>'txt_empname', 
									'name' => 'txt_empname',
									'class'=>'form-control box37per disabled',
									'readonly' => 'true',
									'data-label' => trans('messages.lbl_empName'))) }}

					<button type="button" id="browseEmp" class="btn btn-success box75 pt3 h30 ml3 mb3" 
							onclick="return popupenable();">
								{{ trans('messages.lbl_Browse') }}
					</button> 
					
					<button type="button" id="clearEmp" class="btn btn-danger box75 pt3 h30 ml5 mb3" 
							onclick="return fnclear();">
								{{ trans('messages.lbl_clear') }}
					</button> 

				</div>
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_content') }}
						<span class="fr ml2 red" id = "contentrequired"> * </span>
					</label>
				</div>
				<div class="col-xs-9">
					{{ Form::text('expensesDataContent',(isset($expensesDataEdit[0]->content)) ? $expensesDataEdit[0]->content : '',
							array('id'=>'expensesDataContent', 
									'name' => 'expensesDataContent',
									'onkeyup'=>'disabledemp();',
									'data-label' => trans('messages.lbl_content'),
									'class'=>'box31per form-control pl5')) }}
				</div>
			</div>

			<div class="col-xs-12 mt5" id="hidamtfee">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_amount') }}
						<span class="black ml2">&#47;</span>
						{{ trans('messages.lbl_fee') }}
						<span class="fr ml2 red"> * </span>
					</label>
				</div>
				<div class="col-xs-9 CMN_display_block">
					{{ Form::text('expensesDataAmount',(isset($expensesDataEdit[0]->amount)) ? number_format($expensesDataEdit[0]->amount) : 0,
							array('id'=>'expensesDataAmount',
									'name' => 'expensesDataAmount',
									'style'=>'text-align:right;padding-right:4px;',
									'class'=>'box15per ime_mode_disable numonly',
									'onblur' => 'return fnSetZero11(this.id);',
									'onfocus' => 'return fnRemoveZero(this.id);',
									'onclick' => 'return fnRemoveZero(this.id);',
									'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
									'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
									'data-label' => trans('messages.lbl_amount'))) }}
					<span class=" ml7 black" style=" font-weight: bold;font-size: 17px;"> / </span>
					{{ Form::text('expensesDataFee',(isset($expensesDataEdit[0]->fee)) ? number_format($expensesDataEdit[0]->fee) : 0,
								array('id'=>'expensesDataFee',
									'name' => 'expensesDataFee',
									'style'=>'text-align:right;padding-right:4px;',
									'class'=>'box7per ime_mode_disable ml7 numonly',
									'onblur' => 'return fnSetZero11(this.id);',
									'onfocus' => 'return fnRemoveZero(this.id);',
									'onclick' => 'return fnRemoveZero(this.id);',
									'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
									'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
									'data-label' => trans('messages.lbl_fee'))) }}
				</div>
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_bill') }}<span class="fr ml2 red"> &nbsp;&nbsp; </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::file('expensesDataBill',array(
											'class' => 'pull-left box350',
											'id' => 'expensesDataBill',
											'name' => 'expensesDataBill',
											'style' => 'height:23px;',
											'accept' => 'image/x-png,image/gif,image/jpeg',
											'data-label' => trans('messages.lbl_bill'))) }}
					<span>&nbsp;(Ex: Image File Only)</span>
					@if(isset($expensesDataEdit[0]) && $request->edit_flg == 1)
					<?php $file_url = '../AccountingUpload/ExpensesData/' . $expensesDataEdit[0]->fileDtl; ?>
					@if(isset($expensesDataEdit[0]->fileDtl) && file_exists($file_url))
						<img width="20" height="20" name="empimg" id="empimg" 
						class="ml5 box20 viewPic3by2" src="{{ URL::asset('../../../../AccountingUpload/ExpensesData').'/'.$expensesDataEdit[0]->fileDtl }}">
						{{ Form::hidden('pdffiles', $expensesDataEdit[0]->fileDtl , array('id' => 'pdffiles')) }}
					@else
					@endif
				@endif
				</div>
			</div>

			<div class="col-xs-12 mt5 mb10">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_remarks') }}<span class="fr ml2 red"> &nbsp;&nbsp; </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::textarea('expensesDataRemarks',(isset($expensesDataEdit[0]->remarks)) ? $expensesDataEdit[0]->remarks : '',
									array('id'=>'expensesDataRemarks', 
												'name' => 'expensesDataRemarks',
												'class' => 'box45per',
												'style'=>'text-align:left;padding-left:4px;',
												'size' => '60x5',
												'data-label' => trans('messages.lbl_remarks'))) }}
				</div>
			</div>

		</fieldset>
		<fieldset style="background-color: #DDF1FA;">

			<div class="form-group">
				<div align="center" class="mt5">
					@if($request->edit_flg == 1)
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
					<a href="javascript:gotoindexpage('{{ $request->mainmenu }}');" 
						class="btn btn-danger box120 white">
						<i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
					</a>
				</div>
			</div>

		</fieldset>
		</div>

	{{ Form::close() }}

	{{ Form::open(array('name'=>'addeditcancel', 'id'=>'addeditcancel', 
						'url' => 'ExpensesData/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
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
