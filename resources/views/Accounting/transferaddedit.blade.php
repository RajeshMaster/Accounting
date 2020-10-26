@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/accounts.js') }}
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
<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_1">
		{{ Form::open(array('name'=>'frmtransferaddedit','id'=>'frmtransferaddedit', 
			'url' => 'Accounting/tranferaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
			'files'=>true,'method' => 'POST')) }}

		{{ Form::hidden('edit_flg', $request->edit_flg, array('id' => 'edit_flg')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('editId', $request->editId, array('id' => 'editId')) }}

		<div class="row hline pm0">
			<div class="col-xs-12">
				<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/expenses_icon.png') }}">
				<h2 class="pull-left pl5 mt10">{{ trans('messages.lbl_transfer') }}</h2>
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
		<div class="col-xs-12 pt10">
			<div class="col-xs-5" style="text-align: left;margin-left: -15px;">
				<button type="button" onclick="javascript:addedit('transferCash','{{ $request->mainmenu }}');" class="btn btn-success box25per pt9 pb8">
					<span class="fa fa-plus"></span>&nbsp;{{ trans('messages.lbl_cash') }}
				</button> 
				<button type="button" id="transferbutton" class="btn btn-success box25per pt9 pb8">
					<span class="fa fa-plus"></span>&nbsp;{{ trans('messages.lbl_transfer') }}
				</button> 
				<button type="button" onclick="javascript:addedit('transferAutoDebit','{{ $request->mainmenu }}');" class="btn btn-success box25per pt9 pb8">
					<span class="fa fa-plus"></span>&nbsp;{{ trans('messages.lbl_autodebit') }}
				</button> 
			</div>
			<div class="col-xs-7 pull-right" style="text-align: right;padding: 0px;">
				@if($request->edit_flg != 1)
				{{ Form::text('accDate',(isset($transferEdit[0]->date)) ? $transferEdit[0]->date : '',
							array('id'=>'accDate', 
								'name' => 'accDate',
								'data-label' => trans('messages.lbl_Date'),
								'autocomplete' =>'off',
								'class'=>' box15per form-control dob')) }}
					
					<label class="mt10 ml2 fa fa-calendar fa-lg" for="accDate" aria-hidden="true">
					</label>
					<a href="javascript:getdate();" class="anchorstyle">
						<img title="Current Date" class="box15" 
							src="{{ URL::asset('resources/assets/images/add_date.png') }}"></a>
				@else
					{{ Form::text('accDate',(isset($transferEdit[0]->date)) ? $transferEdit[0]->date : '',
							array('id'=>'accDate', 
								'name' => 'accDate',
								'readonly' => 'true',
								'data-label' => trans('messages.lbl_Date'),
								'autocomplete' =>'off',
								'class'=>' box15per form-control disabled')) }}
				@endif
				@if($request->edit_flg != 1)
				<button type="button" id="salarybutton" style="background-color: purple; color: #fff;" 
					onclick="return Getsalarypopup('');"  
					class="btn box24per pt9 pb8 ml2">
					{{ trans('messages.lbl_getsalary') }}
				</button> 
				<button type="button" id="loanbutton" style="background-color: purple; color: #fff;" 
					onclick="return Getloanpopup('');"
					class="btn box23per pt9 pb8 ml2">
					{{ trans('messages.lbl_getloan') }}
				</button> 
				<button type="button" id="invoicebutton" style="background-color: purple; color: #fff;" 
					onclick="return GetInvoicepopup('');"
					class="btn box24per pt9 pb8 ml2">
					{{ trans('messages.lbl_getinvoiceDtl') }}
				</button> 
				@endif
			</div>
		</div>
		<div class="col-xs-12 pl5 pr5">
		<fieldset>
		
			<!-- <div class="col-xs-12 mt15">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_Date') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
						{{ Form::text('transferDate',(isset($transferEdit[0]->date)) ? $transferEdit[0]->date : '',
								array('id'=>'transferDate',
									'name' => 'transferDate',
									'autocomplete' => 'off',
									'class'=>'box12per txt_startdate form-control dob',
									'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
									'data-label' => trans('messages.lbl_Date'),
									'maxlength' => '10')) }}
						<label class="fa fa-calendar fa-lg" for="transferDate" aria-hidden="true">
						</label>
						<a href="javascript:getdate('Transfer');" class="anchorstyle">
							<img title="Current Date" class="box15" src="{{ URL::asset('resources/assets/images/add_date.png') }}"></a>
				</div>
			</div> -->
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_bank_name') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					@if(isset($transferEdit[0]->Empname))
						{{ Form::text('transferBankName',$transferEdit[0]->Bank_NickName.'-'.$transferEdit[0]->AccNo,
									array('id'=>'transferBankName', 
										'name' => 'transferBankName',
										'readonly' => 'true',
										'data-label' => trans('messages.lbl_bank'),
										'class'=>'pl5 widthauto disabled')) }}
						{{ Form::hidden('transferBank', $transferEdit[0]->bankIdFrom.'-'.$transferEdit[0]->accountNumberFrom , array('id' =>'transferBank','name' =>'transferBank')) }}
					@else
						{{ Form::select('transferBank',[null=>'']+$bankDetail,(isset($transferEdit[0]->bankIdFrom)) ? $transferEdit[0]->bankIdFrom.'-'.$transferEdit[0]->accountNumberFrom : '',
								array('name' =>'transferBank',
											'id'=>'transferBank',
											'data-label' => trans('messages.lbl_bank'),
											'class'=>'pl5 widthauto'))}}
					@endif
				</div>
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_mainsubject') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::select('transferMainExp',[null=>''] + $mainExpDetail,(isset($transferEdit[0]->subjectId)) ? $transferEdit[0]->subjectId : '',
							array('id'=>'transferMainExp',
									'name' => 'transferMainExp',
									'class'=>'widthauto ime_mode_active',
									'maxlength' => 10,
									'onchange'=>'javascript:fngetsubsubject(this.value);',
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
					{{ Form::hidden('empID',(isset($transferEdit[0]->emp_ID)) ? $transferEdit[0]->emp_ID : '',array('id'=>'empID')) }}
					<!-- {{ Form::hidden('hidempid', '', array('id' => 'hidempid')) }} -->
					{{ Form::hidden('hidemp', '', array('id' => 'hidemp')) }}
					<!-- {{ Form::hidden('hidchkTrans', '', array('id' => 'hidchkTrans')) }} -->
					{{ Form::text('txt_empname',(isset($transferEdit[0]->Empname)) ? $transferEdit[0]->Empname : '',
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

					<button type="button" id="clearSal" class="btn btn-danger box75 pt3 h30 ml5 mb3" 
							style ="color:white;cursor: pointer;display: none;" 
							onclick="return fntransclear();">
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
					{{ Form::text('transferContent',(isset($transferEdit[0]->content)) ? $transferEdit[0]->content : '',
							array('id'=>'transferContent', 
									'name' => 'transferContent',
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
					{{ Form::text('transferAmount',(isset($transferEdit[0]->amount)) ? number_format($transferEdit[0]->amount) : 0,
							array('id'=>'transferAmount',
									'name' => 'transferAmount',
									'style'=>'text-align:right;padding-right:4px;',
									'class'=>'box15per ime_mode_disable',
									'onblur' => 'return fnSetZero11(this.id);',
									'onfocus' => 'return fnRemoveZero(this.id);',
									'onclick' => 'return fnRemoveZero(this.id);',
									'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
									'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
									'data-label' => trans('messages.lbl_amount'))) }}
					<span class=" ml7 black" style=" font-weight: bold;font-size: 17px;"> / </span>
					{{ Form::text('transferFee',(isset($transferEdit[0]->fee)) ? number_format($transferEdit[0]->fee) : 0,
								array('id'=>'transferFee',
									'name' => 'transferFee',
									'style'=>'text-align:right;padding-right:4px;',
									'class'=>'box7per ime_mode_disable ml7',
									'onblur' => 'return fnSetZero11(this.id);',
									'onfocus' => 'return fnRemoveZero(this.id);',
									'onclick' => 'return fnRemoveZero(this.id);',
									'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
									'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
									'data-label' => trans('messages.lbl_fee'))) }}
				</div>
			</div>

			<div class="col-xs-12 mt5"  id="enableamt" style="display: none;">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_amount') }}<span class="fr ml2 red" style="visibility: hidden"> * </span>
					</label>
				</div>
				<div class="col-xs-9 CMN_display_block">
					{{ Form::label('',null,
							array('id'=>'transferAmountsalary',
									'name' => 'transferAmountsalary',
									'style'=>'text-align:left;',
									'class'=>'box15per',
									
									)) }}
				</div>
			</div>

			<div class="col-xs-12 mt5"  id="enablefee" style="display: none;">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_fee') }}
						<span class="fr ml2 red" style="visibility: hidden"> * </span>
					</label>
				</div>
				<div class="col-xs-9 CMN_display_block">
					{{ Form::label('',null,
							array('id'=>'transferFeesalary',
									'name' => 'transferFeesalary',
									'style'=>'text-align:left;',
									'class'=>'box15per',
									)) }}
				</div>
			</div>
			
			<div class="col-xs-12 mt5">
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
						<!-- <a style="text-decoration:none" href="{{ URL::asset('../../../../AccountingUpload/Accounting').'/'.$transferEdit[0]->fileDtl }}" data-lightbox="visa-img"></a> -->
						<img width="20" height="20" name="empimg" id="empimg" 
						class="ml5 box20 viewPic3by2" src="{{ URL::asset('../../../../AccountingUpload/Accounting').'/'.$transferEdit[0]->fileDtl }}">
						{{ Form::hidden('pdffiles', $transferEdit[0]->fileDtl , array('id' => 'pdffiles')) }}
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
					{{ Form::textarea('transFerRemarks',(isset($transferEdit[0]->remarks)) ? $transferEdit[0]->remarks : '',
									array('id'=>'transFerRemarks', 
												'name' => 'transFerRemarks',
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
						<button type="submit" class="btn btn-warning add box100 ml5 tranferaddeditprocess">
							<i class="fa fa-edit" aria-hidden="true"></i> 
							{{ trans('messages.lbl_update') }}
						</button>&nbsp;
					@else
						<button type="submit" class="btn btn-success add box100 ml5 tranferaddeditprocess">
							<i class="fa fa-plus" aria-hidden="true"></i> 
							{{ trans('messages.lbl_register') }}
						</button>&nbsp;
					@endif
					<a href="javascript:gotoindexpage('Transfer','{{ $request->mainmenu }}');" 
						class="btn btn-danger box120 white">
						<i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
					</a>
				</div>
			</div>

		</fieldset>
		</div>

	{{ Form::close() }}

	{{ Form::open(array('name'=>'transferaddeditcancel', 'id'=>'transferaddeditcancel', 
						'url' => 'Accounting/tranferaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
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

	<div id="getsalarypopup" class="modal fade">
		<div id="login-overlay">
			<div class="modal-content">
				<!-- Popup will be loaded here -->
			</div>
		</div>
	</div>
	
	<div id="getloanpopup" class="modal fade">
		<div id="login-overlay">
			<div class="modal-content">
				<!-- Popup will be loaded here -->
			</div>
		</div>
	</div>

	<div id="getinvoicepopup" class="modal fade">
		<div id="login-overlay">
			<div class="modal-content">
				<!-- Popup will be loaded here -->
			</div>
		</div>
	</div>

</article>
</div>
@endsection
