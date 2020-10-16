@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/expenses.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::script('resources/assets/js/lib/lightbox.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::style('resources/assets/css/lib/lightbox.css') }}
{{ HTML::script('resources/assets/js/lib/additional-methods.min.js') }}
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
{{ Form::open(array('name'=>'frmexpenseaddedit', 
						'id'=>'frmexpenseaddedit', 
						'url' => 'Expenses/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files' => true,
						'method' => 'POST')) }}
	    {{ Form::hidden('registration',$request->registration, array('id' => 'registration')) }}
	    {{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	    {{ Form::hidden('mainmenu',$request->mainmenu, array('id' => 'mainmenu')) }}
	    {{ Form::hidden('dateflg',$request->dateflg, array('id' => 'dateflg')) }}
	    {{ Form::hidden('edit_flg',$edit_flg, array('id' => 'edit_flg')) }}
	    {{ Form::hidden('expcopyflg',$request->expcopyflg , array('id' => 'expcopyflg')) }}
	    {{ Form::hidden('lang',Session::get('languageval') , array('id' => 'lang')) }}
	    {{ Form::hidden('id',$request->id, array('id' => 'id')) }}
	    @if (Auth::user()->userclassification == 1) 
	    	{{ Form::hidden('accessdate',Auth::user()->accessDate, array('id' => 'accessdate')) }}
	    @else
	    	{{ Form::hidden('accessdate','0001-01-01', array('id' => 'accessdate')) }}
	    @endif
<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/expenses.png') }}">
			<h2 class="pull-left pl5 mt10">
				@if($request->mainmenu == "pettycash")
					{{ trans('messages.lbl_pettyexpenses') }}
				@elseif($request->mainmenu == "company_transfer")
					{{ trans('messages.lbl_transfer') }}
				@else
					{{ trans('messages.lbl_expenses') }}
				@endif
			</h2>
			<h2 class="pull-left mt10">・</h2>
			<h2 class="pull-left mt10">
				@if($edit_flg == "2")
					<span class="green">
						{{ trans('messages.lbl_register') }}
					</span>
				@elseif($edit_flg == "3")
					<span class="blue">
						{{ trans('messages.lbl_copy') }}
					</span>
				@else
					<span class="red">
						{{ trans('messages.lbl_edit') }}
					</span>
				@endif
			</h2>
		</div>
</div>
	@if($edit_flg != 1 && $edit_flg != 3)
		<div class="col-xs-12 pt10">
				<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
				<a href="javascript:gotocashadd('{{ $request->mainmenu }}',1);" class="btn btn-success box17per"><span class="fa fa-plus"></span> {{ trans('messages.lbl_cash') }}</a>
			</div>
		</div>
	@endif
<div class="col-xs-12 pl5 pr5" ondragstart="return false;" ondrop="return false;">
	<fieldset>
		@if($edit_flg == 1)
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
		@if($edit_flg == 1)
			<div class="col-xs-12 mt5">
		@else
			<div class="col-xs-12 mt10">
		@endif
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_Date') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					@if($edit_flg == 3)
						{{ Form::text('date',(isset($request->dateflg)) ? $request->dateflg : '',array('id'=>'date', 
																'name' => 'date',
																'data-label' => trans('messages.lbl_Date'),
																'class'=>'box11per form-control pl5 dob')) }}
						<label class="mt10 ml2 fa fa-calendar fa-lg" for="date" aria-hidden="true"></label>
					@else
						{{ Form::text('date',(isset($expcash_sql[0]->date)) ? $expcash_sql[0]->date : '',array('id'=>'date', 
																'name' => 'date',
																'data-label' => trans('messages.lbl_Date'),
																'class'=>'box11per form-control pl5 dob')) }}
						<label class="mt10 ml2 fa fa-calendar fa-lg" for="date" aria-hidden="true"></label>
					@endif
					@if (Auth::user()->userclassification == 4)
						@if($edit_flg == "3")
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
				<label>{{ trans('messages.lbl_mainsubject') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
			@if($request->mainmenu == "pettycash")
				{{ Form::select('mainsubject',[null=>'']+ $getsubject,(isset($expcash_sql[0]->main_subject)) ? $expcash_sql[0]->main_subject : '', 													array('name' =>'mainsubject',
																	'id'=>'mainsubject',
																	'data-label' => trans('messages.lbl_mainsubject'),
																	'onchange'=>'javascript:fngetsubsubject(this.value);',
																	'class'=>'pl5 widthauto'))}}
			@else
				{{ Form::select('mainsubject',[null=>'']+ $getsubject,(isset($expcash_sql[0]->subject)) ? $expcash_sql[0]->subject : '', 													array('name' =>'mainsubject',
																	'id'=>'mainsubject',
																	'data-label' => trans('messages.lbl_mainsubject'),
																	'onchange'=>'javascript:fngetsubsubject(this.value);',
																	'class'=>'pl5 widthauto'))}}
			@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_subsubject') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::select('subsubject',[null=>''],'', array('name' =>'subsubject',
																	'id'=>'subsubject',
																	'style'=>'min-width:100px;',
																	'data-label' => trans('messages.lbl_subsubject'),
																	'class'=>'pl5 box35per widthauto'))}}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_amount') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::text('amount',(isset($expcash_sql[0]->amount)) ? number_format($expcash_sql[0]->amount) : 0,array('id'=>'amount', 
														'name' => 'amount',
														'style'=>'text-align:right;',
														'maxlength' => 10,
														'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
														'onchange'=>'return fnCancel_check();',
														'onblur' => 'return fnSetZero11(this.id);',
														'onfocus' => 'return fnRemoveZero(this.id);',
														'onclick' => 'return fnRemoveZero(this.id);',
														'onkeyup'=>'return fnMoneyFormat(this.id,"jp");',
														'data-label' => trans('messages.lbl_amount'),
														'class'=>'box15per form-control pl5 ime_mode_disable')) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_bill') }}<span class="fr ml2 red" style="visibility: hidden;"> * </span></label>
			</div>
			<div class="col-xs-9">
				{{ Form::file('file1',array(
										'class' => 'pull-left box350',
										'id' => 'file1',
										'name' => 'file1',
										'style' => 'height:23px;',
										'data-label' => trans('messages.lbl_bill'))) }}
				<span>&nbsp;(Ex: Image File Only)</span>
				@if(isset($expcash_sql) && $edit_flg == 1 && $expcash_sql[0]->file_dtl != "")
				<?php
					$file_url = '../InvoiceUpload/Expenses/' . $expcash_sql[0]->file_dtl;
				?>
					@if(isset($expcash_sql[0]->file_dtl) && file_exists($file_url))
						<a style="text-decoration:none" href="{{ URL::asset('../../../../InvoiceUpload/Expenses/'.$expcash_sql[0]->file_dtl) }}" data-lightbox="visa-img">
						<img width="20" height="20" name="empimg" id="empimg" 
						class="ml5 box20 viewPic3by2" src="{{ URL::asset('../../../../InvoiceUpload/Expenses').'/'.$expcash_sql[0]->file_dtl }}"></a>
						{{ Form::hidden('pdffiles', $expcash_sql[0]->file_dtl , array('id' => 'pdffiles')) }}
					@else
					@endif
				@endif
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
		@if($edit_flg == "1")
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
			@if($edit_flg == "2" || $edit_flg == "3")
				<button type="submit" class="btn btn-success add box100 addeditprocess ml5">
					<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
				</button>
			@else
				<button type="submit" class="btn edit btn-warning box100 addeditprocess">
					<i class="fa fa-edit" aria-hidden="true"></i> {{ trans('messages.lbl_update') }}
				</button>
			@endif
			@if($request->mainmenu == "company_transfer")
				<a onclick="javascript:gotoindextransfer('index','{{$request->mainmenu}}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				</a>
			@else
				<a onclick="javascript:gotoindex('index','{{$request->mainmenu}}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				</a>
    		@endif
			</div>
		</div>
	</fieldset>
</div>
@if(isset($expcash_sql))
	@if($request->mainmenu == "pettycash")
		<script type="text/javascript">
            fngetsubsubject('{{ $expcash_sql[0]->main_subject }}','{{ $expcash_sql[0]->sub_subject }}')
        </script>
	@else
        <script type="text/javascript">
            fngetsubsubject('{{ $expcash_sql[0]->subject }}','{{ $expcash_sql[0]->details }}')
        </script>
    @endif
@endif
{{ Form::close() }}
{{ Form::open(array('name'=>'frmgotocashadd', 
						'id'=>'frmgotocashadd', 
						'url' => 'Expenses/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files' => true,
						'method' => 'POST')) }}
	    {{ Form::hidden('registration',$request->registration, array('id' => 'registration')) }}
	    {{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	    {{ Form::hidden('mainmenu',$request->mainmenu, array('id' => 'mainmenu')) }}
	    {{ Form::hidden('dateflg',$request->dateflg, array('id' => 'dateflg')) }}
	    {{ Form::hidden('edit_flg',$edit_flg, array('id' => 'edit_flg')) }}
	    {{ Form::hidden('cashflg', '' , array('id' => 'cashflg')) }}
	    {{ Form::hidden('expcopyflg',$request->expcopyflg , array('id' => 'expcopyflg')) }}
	    {{ Form::hidden('lang',Session::get('languageval') , array('id' => 'lang')) }}
	    {{ Form::hidden('id',$request->id, array('id' => 'id')) }}
	    @if (Auth::user()->userclassification == 1 && Auth::user()->accessDate != "") 
	    	{{ Form::hidden('accessdate',Auth::user()->accessDate, array('id' => 'accessdate')) }}
	    @else
	    	{{ Form::hidden('accessdate','0001-01-01', array('id' => 'accessdate')) }}
	    @endif
{{ Form::close() }}
{{ Form::open(array('name'=>'expensesaddeditcancel', 'id'=>'expensesaddeditcancel', 'url' => 'Expenses/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	    {{ Form::hidden('edit_flg',$edit_flg, array('id' => 'edit_flg')) }}
	    {{ Form::hidden('expcopyflg',$request->expcopyflg , array('id' => 'expcopyflg')) }}
	    {{ Form::hidden('registration',$request->registration, array('id' => 'registration')) }}
	    {{ Form::hidden('mainmenu',$request->mainmenu, array('id' => 'mainmenu')) }}
	    {{ Form::hidden('lang',Session::get('languageval') , array('id' => 'lang')) }}
	    @if($request->registration==1)
	    @else
	    {{ Form::hidden('id',$request->id, array('id' => 'id')) }}
	    {{ Form::hidden('dateflg',$request->dateflg, array('id' => 'dateflg')) }}
	    @endif
{{ Form::close() }}
</article>
</div>
@endsection