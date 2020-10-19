@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/accounts.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::script('resources/assets/js/lib/additional-methods.min.js') }}

<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var dates = '<?php echo date('Y-m-d'); ?>';
	var accessDate = '<?php echo Auth::user()->accessDate; ?>';
	var userclassification = '<?php echo Auth::user()->userclassification; ?>';
	$(document).ready(function() {
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
</style>

<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_1">
		{{ Form::open(array('name'=>'frmtransferaddedit','id'=>'frmtransferaddedit', 
			'url' => 'Accounting/tranferaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
			'files'=>true,'method' => 'POST')) }}

			{{ Form::hidden('editflg', $request->editflg, array('id' => 'editflg')) }}
			{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}

		<div class="row hline pm0">
			<div class="col-xs-12">
				<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/expenses_icon.png') }}">
				<h2 class="pull-left pl5 mt10">
						{{ trans('messages.lbl_transfer') }}<span class="">・</span><span style="color:green"> 
						{{ trans('messages.lbl_register') }}</span>
				</h2>
			</div>
		</div>
		<div class="col-xs-12 pt10">
			<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
				<a href="javascript:addedit('transferCash','{{ $request->mainmenu }}');" 
					class="btn btn-success box20per"><span class="fa fa-plus"></span> 
					<label class="ml5">{{ trans('messages.lbl_cash') }}</label></a>
				<a href="#" class="btn btn-success box25per disabled">
					<span class="fa fa-plus"></span>
					<label class="ml5">{{ trans('messages.lbl_transfer') }}</label></a>
				<a href="javascript:addedit('transferAutoDebit','{{ $request->mainmenu }}');" 
					class="btn btn-success box25per"><span class="fa fa-plus"></span>
					<label class="ml5">{{ trans('messages.lbl_autodebit') }}</label></a>
			</div>
		</div>
		<div class="col-xs-12 pl5 pr5">
		<fieldset>
		
			<div class="col-xs-12 mt15">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_Date') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
						{{ Form::text('transferDate',null,
								array('id'=>'transferDate',
									'name' => 'transferDate',
									'autocomplete' => 'off',
									'class'=>'box12per txt_startdate form-control dob',
									'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
									'data-label' => trans('messages.lbl_Date'),
									'maxlength' => '10')) }}
						<label class="fa fa-calendar fa-lg" for="transferDate" aria-hidden="true">
						</label>
						<a href="javascript:getdate();" class="anchorstyle">
							<img title="Current Date" class="box15" src="{{ URL::asset('resources/assets/images/add_date.png') }}"></a>
				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_bank_name') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::select('transferBank',[null=>'']+$bankDetail,(isset($expcash_sql[0]->bankname)) ? $expcash_sql[0]->bankname.'-'.$expcash_sql[0]->bankaccno : '',
								array('name' =>'transferBank',
										'id'=>'transferBank',
										'data-label' => trans('messages.lbl_bank'),
										'class'=>'pl5 widthauto'))}}
				</div>
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_mainsubject') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::select('transferMainExp',[null=>''] + $mainExpDetail,'',
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
					<label>{{ trans('messages.lbl_empName') }}<span class="fr ml2 red" 
						style="visibility: hidden"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::hidden('empid', '', array('id' => 'empid')) }}
					{{ Form::text('txt_empname',null,
							array('id'=>'txt_empname', 
									'name' => 'txt_empname',
									'class'=>'form-control box25per',
									'readonly','readonly',
									'data-label' => trans('messages.lbl_empName'))) }}

					<button type="button" id="bnkpopup" class="btn btn-success box75 pt3 h30" 
							style ="color:white;background-color: green;cursor: pointer;" 
							onclick="return popupenable();">
								{{ trans('messages.lbl_Browse') }}
					</button> 
				</div>
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_content') }}
						<span class="fr ml2 red" style="visibility: hidden"> * </span>
					</label>
				</div>
				<div class="col-xs-9">
					{{ Form::text('transferContent',null,
							array('id'=>'transferContent', 
									'name' => 'transferContent',
									'data-label' => trans('messages.lbl_content'),
									'class'=>'box31per form-control pl5')) }}
				</div>
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_amount') }}
						<span class="black ml2">&#47;</span>
						{{ trans('messages.lbl_fee') }}
						<span class="fr ml2 red"> * </span>
					</label>
				</div>
				<div class="col-xs-9 CMN_display_block">
					{{ Form::text('transferAmount',(isset($expcash_sql[0]->amount)) ? number_format($expcash_sql[0]->amount) : 0,
							array('id'=>'transferAmount',
									'name' => 'transferAmount',
									'maxlength' => '14',
									'style'=>'text-align:right;padding-right:4px;',
									'class'=>'box15per ime_mode_disable',
									'onblur' => 'return fnSetZero11(this.id);',
									'onfocus' => 'return fnRemoveZero(this.id);',
									'onclick' => 'return fnRemoveZero(this.id);',
									'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
									'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
									'data-label' => trans('messages.lbl_amount'))) }}
					<span class=" ml7 black" style=" font-weight: bold;font-size: 17px;"> / </span>
					{{ Form::text('transferFee',(isset($expcash_sql[0]->transferFee)) ? number_format($expcash_sql[0]->fee) : 0,
								array('id'=>'transferFee',
									'name' => 'transferFee',
									'maxlength' => '14',
									'style'=>'text-align:right;padding-right:4px;',
									'class'=>'box12per ime_mode_disable ml10',
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
					{{ Form::file('transferBill',array(
											'class' => 'pull-left box350',
											'id' => 'transferBill',
											'name' => 'transferBill',
											'style' => 'height:23px;',
											'data-label' => trans('messages.lbl_bill'))) }}
					<span>&nbsp;(Ex: Image File Only)</span>
				</div>
			</div>

			<div class="col-xs-12 mt5 mb10">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_remarks') }}<span class="fr ml2 red"> &nbsp;&nbsp; </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::textarea('transFerRemarks',null,
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
					<button type="button" class=" btn btn add box160" 
						onclick="return Getsalarypopup();" 
						style="margin-left: -10%!important;background-color: purple; color: #fff;">
						{{ trans('messages.lbl_getsalary') }}
					</button>&nbsp;&nbsp;&nbsp;

					<button type="submit" class="btn btn-success add box100 ml5 tranferaddeditprocess">
						<i class="fa fa-plus" aria-hidden="true"></i> 
						{{ trans('messages.lbl_register') }}
					</button>&nbsp;

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
		
</article>
</div>
@endsection
