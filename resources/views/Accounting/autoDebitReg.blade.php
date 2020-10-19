@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/accounts.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
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
					class="btn btn-success box20per"><span class="ml5 fa fa-plus"></span> 
					{{ trans('messages.lbl_cash') }}</a>
				<a href="javascript:addedit('autoDebitTransfer','{{ $request->mainmenu }}');" 
					class="btn btn-success box25per"><span class="ml5 fa fa-plus"></span>
					{{ trans('messages.lbl_transfer') }}</a>
				<a href="#" 
					class="btn btn-success box25per disabled"><span class="ml5 fa fa-plus"></span>
					{{ trans('messages.lbl_autodebit') }}</a>
			</div>
		</div>

		<div class="col-xs-12 pl5 pr5" ondragstart="return false;" ondrop="return false;">
		<fieldset>
	
			<div class="col-xs-12 mt10">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_Date') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::text('date',(isset($expcash_sql[0]->date)) ? $expcash_sql[0]->date : '',
							array('id'=>'date', 
								'name' => 'date',
								'data-label' => trans('messages.lbl_Date'),
								'autocomplete' =>'off',
								'class'=>'box11per form-control pl5 dob')) }}
					<label class="mt10 ml2 fa fa-calendar fa-lg" for="date" aria-hidden="true"></label>
					<a href="javascript:getdate();" class="anchorstyle">
					<img title="Current Date" class="box15" src="{{ URL::asset('resources/assets/images/add_date.png') }}"></a>
				</div>
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_bank') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::select('bank',[null=>'']+$bankDetail,(isset($expcash_sql[0]->bankname)) ?$expcash_sql[0]->bankname.'-'.$expcash_sql[0]->bankaccno : '',
							array('name' =>'bank',
										'id'=>'bank',
										'data-label' => trans('messages.lbl_bank'),
										'class'=>'pl5 widthauto'))}}
				</div>
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_mainsubject') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::select('mainsubject',[null=>'']+$mainExpDetail,(isset($expcash_sql[0]->mainsubject)) ?$expcash_sql[0]->mainsubject: '',
							array('name' =>'mainsubject',
										'id'=>'mainsubject',
										'data-label' => trans('messages.lbl_mainsubject'),
										'class'=>'pl5 widthauto'))}}
				</div>
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_content') }}<span class="fr ml2 red" style="visibility: hidden"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::text('content',(isset($expcash_sql[0]->content)) ? $expcash_sql[0]->content : '',
							array('id'=>'content', 
									'name' => 'content',
									'data-label' => trans('messages.lbl_content'),
									'class'=>'box31per form-control pl5')) }}
				</div>
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_amount') }}
						<span class="black ml2">&#47;</span>
						{{ trans('messages.lbl_charge') }}
						<span class="fr ml2 red"> * </span>
					</label>
				</div>
				<div class="col-xs-9 CMN_display_block">
						{{ Form::text('amount_1',(isset($expcash_sql[0]->amount)) ? number_format($expcash_sql[0]->amount) : 0,array(
																'id'=>'amount_1',
																'name' => 'amount_1',
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
						{{ Form::text('charge_1',(isset($expcash_sql[0]->charge_1)) ? number_format($expcash_sql[0]->charge_1) : 0,array(
																'id'=>'charge_1',
																'name' => 'charge_1',
																'maxlength' => '14',
																'style'=>'text-align:right;padding-right:4px;',
																'class'=>'box12per ime_mode_disable ml10',
																'onblur' => 'return fnSetZero11(this.id);',
																'onfocus' => 'return fnRemoveZero(this.id);',
																'onclick' => 'return fnRemoveZero(this.id);',
																'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
																'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
																'data-label' => trans('messages.lbl_charge'))) }}
				</div>
			</div>
		
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_bill') }}<span class="fr ml2 red"> &nbsp;&nbsp; </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::file('file1',array(
											'class' => 'pull-left box350',
											'id' => 'file1',
											'name' => 'file1',
											'style' => 'height:23px;',
											'data-label' => trans('messages.lbl_payup'))) }}
					<span>&nbsp;(Ex: Image File Only)</span>
				</div>
			</div>
		
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_remarks') }}<span class="fr ml2 red" style="visibility: hidden;"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::textarea('remarks',(isset($expcash_sql[0]->remark_dtl)) ? $expcash_sql[0]->remark_dtl : '', 
						array('name' => 'remarks',
								'class' => 'box40per form-control',
								'size' => '30x4')) }}
				</div>
			</div>
		
		</fieldset>

		<fieldset style="background-color: #DDF1FA;">

			<div class="form-group">
				<div align="center" class="mt5">
					<button type="submit" class="btn btn-success add box100 AutoDebitRegprocess ml5">
						<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
					</button>
				
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

</article>
</div>

@endsection