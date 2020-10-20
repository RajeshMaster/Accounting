@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/accounts.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::script('resources/assets/js/lib/additional-methods.min.js') }}
@php use App\Http\Helpers; @endphp
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
	.clr_brown{
		 color: #9C0000 ! important;
	}
	.ime_mode_disable {
		ime-mode:disabled;
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
	  
		<div class="row hline pm0">
				<div class="col-xs-12">
					<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/pettycash.jpg') }}">
					<h2 class="pull-left pl5 mt10">
						{{ trans('messages.lbl_autodebit') }}
					</h2>
					<h2 class="pull-left mt10">・</h2>
					<h2 class="pull-left mt10">
						<span class="green">
							{{ trans('messages.lbl_register') }}
						</span>
					</h2>
				</div>
		</div>

		<div class="col-xs-12 pt10">
			<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
				<a href="javascript:addedit('autoDebitCash','{{ $request->mainmenu }}');" 
					class="btn btn-success box20per"><span class="fa fa-plus"></span> 
					<label class="ml5">{{ trans('messages.lbl_cash') }}</label></a>
				<a href="javascript:addedit('autoDebitTransfer','{{ $request->mainmenu }}');" 
					class="btn btn-success box25per"><span class="fa fa-plus"></span>
					<label class="ml5">{{ trans('messages.lbl_transfer') }}</label></a>
				<a href="#" 
					class="btn btn-success box25per disabled"><span class="fa fa-plus"></span>
					<label class="ml5">{{ trans('messages.lbl_autodebit') }}</label></a>
			</div>
		</div>

		<div class="col-xs-12 pl5 pr5" ondragstart="return false;" ondrop="return false;">

		<fieldset>
	
			<div class="col-xs-12 mt10">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_Date') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::text('autoDebitDate',(isset($expcash_sql[0]->date)) ? $expcash_sql[0]->date : '',
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
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_bank') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::select('autoDebitBank',[null=>'']+$bankDetail,(isset($expcash_sql[0]->bankname)) ?$expcash_sql[0]->bankname.'-'.$expcash_sql[0]->bankaccno : '',
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
					{{ Form::select('autoDebitMainExp',[null=>'']+$mainExpDetail,(isset($expcash_sql[0]->autoDebitMainExp)) ?$expcash_sql[0]->autoDebitMainExp: '',
							array('name' =>'autoDebitMainExp',
										'id'=>'autoDebitMainExp',
										'data-label' => trans('messages.lbl_mainsubject'),
										'class'=>'pl5 widthauto'))}}
				</div>
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_loanname') }}
						<span class="fr ml2 red" style="visibility: hidden"> * </span>
					</label>
				</div>
				<div class="col-xs-9">
					{{ Form::hidden('hidloan', '', array('id' => 'hidloan')) }}
					{{ Form::hidden('hidcheckDeb', '', array('id' => 'hidcheckDeb')) }}
					{{ Form::text('loanName',null,
							array('id'=>'loanName', 
									'name' => 'loanName',
									'readonly',
									'data-label' => trans('messages.lbl_loanname'),
									'class'=>'box31per form-control pl5')) }}
					<button type="button" id="clear" class="btn btn-danger box75 pt3 h30 ml5 mb3" 
							style ="color:white;cursor: pointer;" 
							onclick="return fndebitclear();">
								{{ trans('messages.lbl_clear') }}
					</button> 
				</div>
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_content') }}<span class="fr ml2 red" style="visibility: hidden"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::text('autoDebitContent',(isset($expcash_sql[0]->autoDebitContent)) ? $expcash_sql[0]->autoDebitContent : '',
							array('id'=>'autoDebitContent', 
									'name' => 'autoDebitContent',
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
					{{ Form::text('autoDebitAmount',(isset($expcash_sql[0]->amount)) ? number_format($expcash_sql[0]->amount) : "",
							array('id'=>'autoDebitAmount',
									'name' => 'autoDebitAmount',
									'style'=>'text-align:right;padding-right:4px;',
									'class'=>'box15per ime_mode_disable',
									'onblur' => 'return fnSetZero11(this.id);',
									'onfocus' => 'return fnRemoveZero(this.id);',
									'onclick' => 'return fnRemoveZero(this.id);',
									'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
									'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
									'data-label' => trans('messages.lbl_amount'))) }}
					<span class=" ml7 black" style=" font-weight: bold;font-size: 17px;"> / </span>
					{{ Form::text('autoDebitFee',(isset($expcash_sql[0]->autoDebitFee)) ? number_format($expcash_sql[0]->autoDebitFee) : "",
							array('id'=>'autoDebitFee',
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
				</div>
			</div>
		
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_remarks') }}<span class="fr ml2 red" style="visibility: hidden;"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::textarea('autoDebitRemarks',(isset($expcash_sql[0]->remark_dtl)) ? $expcash_sql[0]->remark_dtl : '', 
									array('name' => 'autoDebitRemarks',
											'id'=>'autoDebitRemarks', 
											'class' => 'box40per form-control',
											'size' => '30x4')) }}
				</div>
			</div>
		
		</fieldset>

		<fieldset style="background-color: #DDF1FA;">

			<div class="form-group">
				<div align="center" class="mt5">
					<button type="button" id="loanbutton" class=" btn btn add box160" 
						onclick="return Getloanpopup();" 
						style="margin-left: -10%!important;background-color: purple; color: #fff;">
						{{ trans('messages.lbl_getloan') }}
					</button>&nbsp;&nbsp;&nbsp;

					<button type="submit" class="btn btn-success add box100 AutoDebitRegprocess ml5">
						<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
					</button>&nbsp;
				
					<a href = "javascript:gotoindexpage('AutoDebit','{{ $request->mainmenu }}');" 
						class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
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