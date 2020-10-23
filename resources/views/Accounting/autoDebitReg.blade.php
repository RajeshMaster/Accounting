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
		$("#autodebitbutton").attr("disabled", "disabled");
		if (userclassification == 1) {
			accessDate = setNextDay(accessDate);
			setDatePickerAfterAccessDate("dob", accessDate);
		} else {
			setDatePicker("dob");
		}
		/*if ($('#loanName').val() != "") {
			$("#autoDebitContent").attr("disabled", "disabled");
		} else {
			$("#autoDebitContent").removeAttr("disabled");
		}*/
	});
</script>
<style type="text/css">
	.clr_brown{
		 color: #9C0000 ! important;
	}
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
	{{ Form::open(array('name'=>'frmAutoDebitReg', 
						'id'=>'frmAutoDebitReg', 
						'url' => 'Accounting/
						AutoDebitRegprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files' => true,
						'method' => 'POST')) }}
		{{ Form::hidden('mainmenu',$request->mainmenu, array('id' => 'mainmenu')) }}
		{{ Form::hidden('edit_flg', $request->edit_flg, array('id' => 'edit_flg')) }}
		{{ Form::hidden('editId', $request->editId, array('id' => 'editId')) }}
	  
		<div class="row hline pm0">
			<div class="col-xs-12">
				<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/pettycash.jpg') }}">
				<h2 class="pull-left pl5 mt10">
					{{ trans('messages.lbl_autodebit') }}
				</h2>
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
			<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
				<button type="button" onclick="javascript:addedit('autoDebitCash','{{ $request->mainmenu }}');" class="btn btn-success box25per pt9 pb8">
					<span class="fa fa-plus"></span>&nbsp;{{ trans('messages.lbl_cash') }}
				</button> 
				<button type="button" onclick="javascript:addedit('autoDebitTransfer','{{ $request->mainmenu }}');" class="btn btn-success box25per pt9 pb8">
					<span class="fa fa-plus"></span>&nbsp;{{ trans('messages.lbl_transfer') }}
				</button> 
				<button type="button" id="autodebitbutton"  class="btn btn-success box25per pt9 pb8">
					<span class="fa fa-plus"></span>&nbsp;{{ trans('messages.lbl_autodebit') }}
				</button> 
			</div>
			<div class="col-xs-6 pull-right" style="text-align: right;">
				{{ Form::text('accDate',(isset($autodebitEdit[0]->date)) ? $autodebitEdit[0]->date : '',
							array('id'=>'accDate', 
								'name' => 'accDate',
								'data-label' => trans('messages.lbl_Date'),
								'autocomplete' =>'off',
								'class'=>' box20per form-control dob')) }}
					<label class="mt10 ml2 fa fa-calendar fa-lg" for="accDate" aria-hidden="true">
					</label>
					<a href="javascript:getdate();" class="anchorstyle">
						<img title="Current Date" class="box15" 
							src="{{ URL::asset('resources/assets/images/add_date.png') }}"></a>
				@if($request->edit_flg != 1)
				<button type="button" id="salarybutton" style="background-color: purple; color: #fff;" 
					onclick="return Getsalarypopup('');"  
					class="btn box24per pt9 pb8 ml5">
					{{ trans('messages.lbl_getsalary') }}
				</button> 
				<button type="button" id="loanbutton" style="background-color: purple; color: #fff;" 
					onclick="return Getloanpopup('');"
					class="btn box24per pt9 pb8 ml5">
					{{ trans('messages.lbl_getloan') }}
				</button> 
				@endif
			</div>
		</div>

		<div class="col-xs-12 pl5 pr5" ondragstart="return false;" ondrop="return false;">

		<fieldset>
	
			<!-- <div class="col-xs-12 mt10">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_Date') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::text('autoDebitDate',(isset($autodebitEdit[0]->date)) ? $autodebitEdit[0]->date : '',
							array('id'=>'autoDebitDate', 
								'name' => 'autoDebitDate',
								'data-label' => trans('messages.lbl_Date'),
								'autocomplete' =>'off',
								'class'=>'box11per form-control pl5 dob')) }}
					<label class="mt10 ml2 fa fa-calendar fa-lg" for="autoDebitDate" 
						aria-hidden="true">
					</label>
					<a href="javascript:getdate('AutoDebit');" class="anchorstyle">
					<img title="Current Date" class="box15" 
						src="{{ URL::asset('resources/assets/images/add_date.png') }}"></a>
				</div>
			</div> -->

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_bank') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::select('autoDebitBank',[null=>'']+$bankDetail,(isset($autodebitEdit[0]->bankIdFrom)) ? $autodebitEdit[0]->bankIdFrom.'-'.$autodebitEdit[0]->accountNumberFrom : '',
							array('name' =>'autoDebitBank',
										'id'=>'autoDebitBank',
										'data-label' => trans('messages.lbl_bank'),
										'class'=>'pl5 widthauto'))}}
				</div>
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_mainsubject') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::select('autoDebitMainExp',[null=>'']+$mainExpDetail,(isset($autodebitEdit[0]->subjectId)) ? $autodebitEdit[0]->subjectId : '',
							array('name' =>'autoDebitMainExp',
										'id'=>'autoDebitMainExp',
										'data-label' => trans('messages.lbl_mainsubject'),
										'class'=>'pl5 widthauto'))}}
				</div>
			</div>

			<!-- <div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_loanname') }}
						<span class="fr ml2 red" id = "loanrequired"> * </span>
					</label>
				</div>
				<div class="col-xs-9">
					{{ Form::hidden('hidloan', '', array('id' => 'hidloan')) }}
					{{ Form::hidden('hidcheckDeb', '', array('id' => 'hidcheckDeb')) }}
					{{ Form::hidden('hidloanId', '', array('id' => 'hidloanId')) }}
					{{ Form::hidden('hidempId', '', array('id' => 'hidempId')) }}
					{{ Form::text('loanName',(isset($autodebitEdit[0]->loanName)) ? $autodebitEdit[0]->loanName : '',
							array('id'=>'loanName', 
									'name' => 'loanName',
									'readonly' => 'true',
									'data-label' => trans('messages.lbl_loanname'),
									'class'=>'box31per form-control pl5 disabled')) }}
					@if($request->edit_flg != 1)
						<button type="button" id="clearloan" 
								class="btn btn-danger box75 pt3 h30 ml5 mb3" 
								onclick="return fndebitclear();">
									{{ trans('messages.lbl_clear') }}
						</button> 
					@endif
				</div>
			</div> -->

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_content') }}
						<span class="fr ml2 red" id = "debitrequired"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::text('autoDebitContent',(isset($autodebitEdit[0]->content)) ? $autodebitEdit[0]->content : '',
							array('id'=>'autoDebitContent', 
									'name' => 'autoDebitContent',
									'autocomplete' =>'off',
									'onkeyup'=>'disabledloan();',
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
					{{ Form::text('autoDebitAmount',(isset($autodebitEdit[0]->amount)) ? number_format($autodebitEdit[0]->amount) : 0,
							array('id'=>'autoDebitAmount',
									'name' => 'autoDebitAmount',
									'style'=>'text-align:right;padding-right:4px;',
									'autocomplete' =>'off',
									'class'=>'box15per ime_mode_disable',
									'onblur' => 'return fnSetZero11(this.id);',
									'onfocus' => 'return fnRemoveZero(this.id);',
									'onclick' => 'return fnRemoveZero(this.id);',
									'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
									'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
									'data-label' => trans('messages.lbl_amount'))) }}
					<span class=" ml7 black" style=" font-weight: bold;font-size: 17px;"> / </span>
					{{ Form::text('autoDebitFee',(isset($autodebitEdit[0]->fee)) ? number_format($autodebitEdit[0]->fee) : 0,
							array('id'=>'autoDebitFee',
									'name' => 'autoDebitFee',
									'style'=>'text-align:right;padding-right:4px;',
									'autocomplete' =>'off',
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
							array('id'=>'autoDebitAmountloan',
									'name' => 'autoDebitAmountloan',
									'style'=>'text-align:left;',
									'class'=>'box15per',
									
									)) }}
				</div>
			</div>
			<div class="col-xs-12 mt5"  id="enablefee" style="display: none;">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_fee') }}<span class="fr ml2 red" style="visibility: hidden"> * </span>
					</label>
				</div>
				<div class="col-xs-9 CMN_display_block">
					{{ Form::label('',null,
							array('id'=>'autoDebitFeeloan',
									'name' => 'autoDebitFeeloan',
									'style'=>'text-align:left;',
									'class'=>'box15per',
									)) }}
				</div>
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_bill') }}
						<span class="fr ml2 red"> &nbsp;&nbsp; </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::file('autoDebitBill',array('class' => 'pull-left box350',
													'id' => 'autoDebitBill',
													'name' => 'autoDebitBill',
													'style' => 'height:23px;',
													'accept' => 'image/x-png,image/gif,image/jpeg',
													'data-label' => trans('messages.lbl_bill'))) }}
					<span>&nbsp;(Ex: Image File Only)</span>

					@if(isset($autodebitEdit) && $request->edit_flg == 1)
						<?php $file_url = '../AccountingUpload/Accounting/' . $autodebitEdit[0]->fileDtl; ?>
						@if(isset($autodebitEdit[0]->fileDtl) && file_exists($file_url))
							<!-- <a style="text-decoration:none" href="{{ URL::asset('../../../../AccountingUpload/Accounting').'/'.$autodebitEdit[0]->fileDtl }}" data-lightbox="visa-img"></a> -->
							<img width="20" height="20" name="empimg" id="empimg" 
							class="ml5 box20 viewPic3by2" src="{{ URL::asset('../../../../AccountingUpload/Accounting').'/'.$autodebitEdit[0]->fileDtl }}">
							{{ Form::hidden('pdffiles', $autodebitEdit[0]->fileDtl , array('id' => 'pdffiles')) }}
						@endif
					@endif
					
				</div>
			</div>
		
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_remarks') }}<span class="fr ml2 red" style="visibility: hidden;"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::textarea('autoDebitRemarks',(isset($autodebitEdit[0]->remarks)) ? $autodebitEdit[0]->remarks : '', 
									array('name' => 'autoDebitRemarks',
											'id'=>'autoDebitRemarks', 
											'autocomplete' =>'off',
											'class' => 'box40per form-control',
											'size' => '30x4')) }}
				</div>
			</div>
		
		</fieldset>

		<fieldset style="background-color: #DDF1FA;">

			<div class="form-group">
				<div align="center" class="mt5">
				@if($request->edit_flg == 1)
					<button type="submit" class="btn btn-warning add box100 ml5 AutoDebitRegprocess">
						<i class="fa fa-edit" aria-hidden="true"></i> 
						{{ trans('messages.lbl_update') }}
					</button>&nbsp;
				@else
					<button type="submit" class="btn btn-success add box100 ml5 AutoDebitRegprocess">
						<i class="fa fa-plus" aria-hidden="true"></i> 
						{{ trans('messages.lbl_register') }}
					</button>&nbsp;
				@endif
					<a href="javascript:gotoindexpage('AutoDebit','{{ $request->mainmenu }}');" 
						class="btn btn-danger box120 white">
						<i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
					</a>
				</div>
			</div>

		</fieldset>

		</div>

	{{ Form::close() }}

	{{ Form::open(array('name'=>'AutoDebitRegcancel', 'id'=>'AutoDebitRegcancel', 
						'url' => 'Accounting/AutoDebitRegprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,'method' => 'POST')) }}

		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}

	{{ Form::close() }}

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

</article>
</div>

@endsection