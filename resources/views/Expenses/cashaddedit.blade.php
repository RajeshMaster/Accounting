@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/expenses.js') }}
{{ HTML::script('resources/assets/js/accounting.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
@php use App\Http\Helpers; @endphp
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
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
.btn-gray {
  background-color: gray;
  border-color: white;
}
.ime_mode_disable {
	ime-mode:disabled;
}
</style>
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
@if($request->mainmenu == "pettycash")
<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_8">
@else
<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_1">
@endif
{{ Form::open(array('name'=>'frmcashaddedit', 
						'id'=>'frmcashaddedit', 
						'url' => 'Expenses/cashaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files' => true,
						'type' => 'file',
						'method' => 'POST')) }}
		{{ Form::hidden('registration',$request->registration, array('id' => 'registration')) }}
	    {{ Form::hidden('cashflg', $request->cashflg , array('id' => 'cashflg')) }}
	    {{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('dateflg', $request->dateflg, array('id' => 'dateflg')) }}
		{{ Form::hidden('id', $request->id, array('id' => 'id')) }}
	    {{ Form::hidden('cashflggg',$request->cashflggg, array('id' => 'cashflggg')) }}
		@if (Auth::user()->userclassification == 1) 
	    	{{ Form::hidden('accessdate',Auth::user()->accessDate, array('id' => 'accessdate')) }}
	    @else
	    	{{ Form::hidden('accessdate','0001-01-01', array('id' => 'accessdate')) }}
	    @endif
<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/pettycash.jpg') }}">
			<h2 class="pull-left pl5 mt10">
				@if($request->mainmenu == "pettycash")
					{{ trans('messages.lbl_pettycash') }}
				@else
					{{ trans('messages.lbl_cash') }}
				@endif
			</h2>
			<h2 class="pull-left mt10">・</h2>
			<h2 class="pull-left mt10">
			@if($request->cashflg==2)
				<span class="red">
					{{ trans('messages.lbl_edit') }}
				</span>
			@elseif($request->cashflg==3)
				<span class="blue">
					{{ trans('messages.lbl_copy') }}
				</span>
			@elseif($request->cashflg==3 && $request->mainmenu == "company_transfer")
				<span class="blue">
					{{ trans('messages.lbl_copy') }}
				</span>
			@else
				<span class="green">
					{{ trans('messages.lbl_register') }}
				</span>
			@endif
			</h2>
		</div>
</div>
@if($request->cashflg==2)
	{{ Form::hidden('id',$expcash_sql[0]->id, array('id' => 'id')) }}
@endif
	@if($request->cashflg==1)
		<div class="col-xs-12 pt10">
				<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
				<a href="javascript:gotoexpensesadd('{{ $request->mainmenu }}');" class="btn btn-success box19per"><span class="fa fa-plus"></span> {{ trans('messages.lbl_expenses') }}</a>
			</div>
		</div>
	@endif
<div class="col-xs-12 pl5 pr5" ondragstart="return false;" ondrop="return false;">
	<fieldset>
		@if($request->cashflg==2)
			<div class="col-xs-12 mt10">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_billnumb') }}<span class="fr ml2 red" style="visibility: hidden;"> * </span></label>
				</div>
				<div class="col-xs-9"><label class="clr_brown">
					{{ $expcash_sql[0]->billno }}
				    {{ Form::hidden('billno',$expcash_sql[0]->billno, array('id' => 'billno')) }}
					</label>
				</div>
			</div>
		@endif
		@if($request->cashflg!=2)
			<div class="col-xs-12 mt5">
		@else
			<div class="col-xs-12 mt10">
		@endif
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_Date') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				@if($request->cashflg==3)
					{{ Form::text('date',(isset($request->dateflg)) ? $request->dateflg : '',array('id'=>'date', 
														'name' => 'date',
														'data-label' => trans('messages.lbl_Date'),
														'class'=>'box11per form-control pl5 dob')) }}
				@else
					{{ Form::text('date',(isset($expcash_sql[0]->date)) ? $expcash_sql[0]->date : '',array('id'=>'date', 
														'name' => 'date',
														'data-label' => trans('messages.lbl_Date'),
														'class'=>'box11per form-control pl5 dob')) }}
				@endif
				<label class="mt10 ml2 fa fa-calendar fa-lg" for="date" aria-hidden="true"></label>
				@if (Auth::user()->userclassification == 4)
					@if($request->cashflg==3)
						&nbsp;&nbsp;{{ Form::checkbox('accessrights', 1, 1, ['id' => 'accessrights']) }}
						&nbsp;<label for="accessrights"><span class="grey fb">{{ trans('messages.lbl_accessrights') }}</span></label>
					@else
						&nbsp;&nbsp;{{ Form::checkbox('accessrights', 1, (isset($expcash_sql[0]->accessFlg)) ? $expcash_sql[0]->accessFlg : 1, ['id' => 'accessrights']) }}
						&nbsp;<label for="accessrights"><span class="grey fb">{{ trans('messages.lbl_accessrights') }}</span></label>
					@endif
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_bank') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::select('bank',[null=>'']+$sql,(isset($expcash_sql[0]->bankname)) ? 
														$expcash_sql[0]->bankname.'-'.$expcash_sql[0]->bankaccno : '',						array('name' =>'bank',
																	'id'=>'bank',
																	'data-label' => trans('messages.lbl_bank'),
																	'onchange'=>'javascript:getselectedTexts(this.value);',
																	'class'=>'pl5 widthauto'))}}
				{{ Form::select('transfer',[null=>'']+$sql,'', 													array('name' =>'transfer',
																	'id'=>'transfer',
																	'data-label' =>  trans('messages.lbl_bank'),
																	'style' => 'display:none;',
																	'class'=>'pl5 widthauto'))}}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_transaction') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9 mt2">
				<label style="font-weight: normal;">
				<?php $disableRadio = "";
								if ($request->cashflg == 2 && $expcash_sql[0]->transaction_flg == 3) {
									// $disableRadio = "disabled='disabled'"; 
								}?>
                  {{ Form::radio('transtype', '1', (isset($expcash_sql[0]->transaction_flg) && ($expcash_sql[0]->transaction_flg)=="1") ? $expcash_sql[0]->transaction_flg : '', array('id' =>'transtype',
                                                                'name' => 'transtype',
                                                                $disableRadio,
                                                                'onkeypress'=>'return numberonly(event)',
                                                                'style' => 'margin-bottom:5px;',
																'data-label' => trans('messages.lbl_transaction'),
                                                                'onchange' => 'debitAmount()',
                                                                'class' => '')) }}
                  &nbsp {{ trans('messages.lbl_debit') }} &nbsp
                </label>
                <label style="font-weight: normal;">
                  {{ Form::radio('transtype', '2', (isset($expcash_sql[0]->transaction_flg) && ($expcash_sql[0]->transaction_flg)=="2") ? $expcash_sql[0]->transaction_flg : '', array('id' =>'transtype1',
                                                                'name' => 'transtype',
                                                                $disableRadio,
                                                                'style' => 'margin-bottom:5px;',
																'data-label' => trans('messages.lbl_transaction'),
                                                                'onchange' => 'creditAmount()',
                                                                'class' => 'transtype1')) }}
                  &nbsp {{ trans('messages.lbl_credit') }} &nbsp
                </label>
                @if($request->mainmenu != "pettycash")
	                <label style="font-weight: normal;">
	                  {{ Form::radio('transtype', '3', (isset($expcash_sql[0]->transaction_flg) && ($expcash_sql[0]->transaction_flg)=="3") ? $expcash_sql[0]->transaction_flg : '', array('id' =>'transtype2',
	                                                                'name' => 'transtype',
	                                                                $disableRadio,
	                                                                'style' => 'margin-bottom:5px;',
																	'data-label' => trans('messages.lbl_transaction'),
	                                                                'onchange' => 'banktransferselect()',
	                                                                'class' => '')) }}
	                  &nbsp {{ trans('messages.lbl_transfer') }} &nbsp
	                </label>
                @endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_amount') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::text('amount',(isset($expcash_sql[0]->amount)) ? number_format($expcash_sql[0]->amount) : '0',array('id'=>'amount', 
														'name' => 'amount',
														'style'=>'text-align:right;',
														'maxlength' => 14,
														'onblur' => 'return fnSetZero11(this.id);',
														'onfocus' => 'return fnRemoveZero(this.id);',
														'onclick' => 'return fnRemoveZero(this.id);',
														'data-label' => trans('messages.lbl_amount'),
														'onkeyup'=>'javascript:fnMoneyFormat(this.id,"jp");javascript:fnMoneyFormatcashadd(this.name, this.value);',
														'onkeypress'=>'return isNumberFormat(event);',
														'class'=>'box15per form-control pl5 ime_mode_disable')) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_remarks') }}<span class="fr ml2 red" style="visibility: hidden;"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::textarea('remarks',(isset($expcash_sql[0]->remark_dtl)) ? $expcash_sql[0]->remark_dtl : '', 
                        array('name' => 'remarks',
                              'class' => 'box40per form-control','size' => '30x4')) }}
			</div>
		</div>
		@if($request->cashflg == "2")
			@if(isset($expcash_sql[0]->Ins_DT))
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_created_date') }}<span class="fr ml2 red"> &nbsp; </span></label>
				</div>
				<div class="col-xs-9">
					<span  style="color: black;">
						<b>
							{{ $expcash_sql[0]->Ins_DT }}
						</b>
					</span>
				</div>
			</div>
			@endif
			@if(isset($expcash_sql[0]->Up_DT))
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_updated_date') }}<span class="fr ml2 red"> &nbsp; </span></label>
				</div>
				<div class="col-xs-9">
					<span  style="color: black;">
						<b>
							{{ $expcash_sql[0]->Up_DT }}
						</b>
					</span>
				</div>
			</div>
			@endif
		@endif
		<div class="col-xs-12 mt10"></div>
	</fieldset>
	<fieldset style="background-color: #DDF1FA;">
		<div class="form-group">
			<div align="center" class="mt5">
			
			@if($request->cashflg == 2)
				<button type="submit" class="btn edit btn-warning box100 cashaddeditprocess">
					<i class="fa fa-edit" aria-hidden="true"></i> {{ trans('messages.lbl_update') }}
				</button>
			@else
				<button type="submit" class="btn btn-success add box100 cashaddeditprocess ml5">
					<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
				</button>
			@endif
			@if($request->mainmenu == "company_transfer")
				<a onclick="javascript:gotocashindextransfer('index','{{$request->mainmenu}}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				</a>
			@else
				<a onclick="javascript:gotocashindex('index','{{$request->mainmenu}}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				</a>
			@endif
			</div>
		</div>
	</fieldset>
</div>
@if($request->mainmenu == "expenses" || $request->mainmenu == "company_transfer")
	@if(isset($expcash_sql))
		<script type="text/javascript">
			getselectedTexts('{{ $expcash_sql[0]->bankname }}','{{ $expcash_sql[0]->banknameTransfer."-".$expcash_sql[0]->bankaccnoTransfer }}','{{ $expcash_sql[0]->banknameTransfer."-".$expcash_sql[0]->bankaccnoTransfer }}','edit')
		</script>
	@endif
@endif
{{ Form::close() }}
{{ Form::open(array('name'=>'frgotoexpensesadd', 
						'id'=>'frgotoexpensesadd', 
						'url' => 'Expenses/cashaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files' => true,
						'type' => 'file',
						'method' => 'POST')) }}
		{{ Form::hidden('registration',$request->registration, array('id' => 'registration')) }}
	    {{ Form::hidden('cashflg', $request->cashflg , array('id' => 'cashflg')) }}
	    {{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('dateflg', $request->dateflg, array('id' => 'dateflg')) }}
		{{ Form::hidden('id', $request->id, array('id' => 'id')) }}
		@if (Auth::user()->userclassification == 1 && Auth::user()->accessDate != "") 
	    	{{ Form::hidden('accessdate',Auth::user()->accessDate, array('id' => 'accessdate')) }}
	    @else
	    	{{ Form::hidden('accessdate','0001-01-01', array('id' => 'accessdate')) }}
	    @endif
{{ Form::close() }}
{{ Form::open(array('name'=>'cashaddeditcancel', 'id'=>'cashaddeditcancel', 'url' => 'Expenses/cashaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('registration',$request->registration, array('id' => 'registration')) }}
	    {{ Form::hidden('cashflg', $request->cashflg , array('id' => 'cashflg')) }}
	    {{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
{{ Form::close() }}
</article>
</div>
@endsection