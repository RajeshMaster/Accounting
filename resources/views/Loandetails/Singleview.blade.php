@extends('layouts.app')
@section('content')
{{ HTML::style('resources/assets/css/common.css') }}
{{ HTML::style('resources/assets/css/widthbox.css') }}
{{ HTML::script('resources/assets/css/bootstrap.min.css') }}
{{ HTML::script('resources/assets/js/loan.js') }}
{{ HTML::style('resources/assets/css/sidebar-bootstrap.min.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
</script>
<style type="text/css">
	.alertboxalign {
    	margin-bottom: -50px !important;
	}
	.alert {
	    display:inline-block !important;
	    height:30px !important;
	    padding:5px !important;
	}
</style>
	<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_4">
	{{ Form::open(array('name'=>'loansingleview', 'id'=>'loansingleview', 'url' => 'Loandetails/Loanconfirm?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('id', $request->id , array('id' => 'id')) }}
		{{ Form::hidden('editflg', '' , array('id' => 'editflg')) }}
		{{ Form::hidden('loan_confirm', '' , array('id' => 'loan_confirm')) }}
		{{ Form::hidden('loandetail', '' , array('id' => 'loandetail')) }}
	<!-- Start Heading -->
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/loan.jpg') }}">
			<h2 class="pull-left pl5 mt10">
					{{ trans('messages.lbl_loandetail') }}<span>・</span><span class="colbl">{{ trans('messages.lbl_view') }}</span>
			</h2>
		</div>
	</div>
	<div class="col-xs-12 pt10">
	<!-- Session msg -->
	@if(Session::has('success'))
		<div align="center" class="alertboxalign" role="alert">
			<p class="alert {{ Session::get('alert', Session::get('type') ) }}">
            {{ Session::get('success') }}
          	</p>
		</div>
	@endif
	@php Session::forget('success'); @endphp
	<!-- Session msg -->
			<div class="col-xs-8" style="text-align: left;margin-left: -15px;">
				<a href="javascript:goindexloanpage('{{ $request->mainmenu }}');" class="pageload btn btn-info box80"><span class="fa fa-arrow-left"></span> {{ trans('messages.lbl_back') }}</a>
				@if($view[0]->editFlg != "1")
					<a href="javascript:goToedit('{{ $request->id }}','{{ $request->mainmenu }}',2);" class="btn btn-warning box100"><span class="fa fa-pencil"></span> {{ trans('messages.lbl_edit') }}</a>
				@endif
				<a style="width: 130px;" href="javascript:gototransferpage();" class="btn btn-success box100"><span class="fa fa-plus"></span> {{ trans('messages.lbl_loan') }}&nbsp;{{ trans('messages.lbl_register') }}</a>
			</div>
			<div class="col-xs-4 pull-right pt10" style="text-align: right;margin-right: -15px;">
			<?php $edit = ""; if (isset($view[0]->editFlg)) {
				$edit = $view[0]->editFlg;
			} ?>
				<input type="checkbox" id="loanconfirm" name="loanconfirm" 
				    	<?php if ($edit == 1) {?> 
									checked="checked"
									<?php	} ?> 
				    	onclick="return fnloanconfirm(this.id,
				    				'<?php echo $edit;?>','<?php echo $request->mainmenu;?>');">
							<label for="loanconfirm" class="pl2">{{ trans('messages.lbl_loanconfirm') }}
							</label>
			</div>
		</div>
	<div class="col-xs-12 pl15 pr15">
	<fieldset>
		<div class="col-xs-12 mt15">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_loanno') }}</label>
			</div>
			<div>
				<b>
					<span style="color:blue;">
              			{{ $request->id }}
              		</span>
              	</b>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_loantype') }}</label>
			</div>
			<div>
				@if(isset($view[0]))
					@if(Session::get('languageval') == "en")
						{{ $view[0]->loanEng }}
					@else
						{{ $view[0]->loanJap }}
					@endif
				@else
					NIL
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_loanname') }}</label>
			</div>
			<div>
				@if(isset($view[0]->loanName))
					{{ $view[0]->loanName }}
				@else
					NIL
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_recdate') }}</label>
			</div>
			<div>
				@if(isset($view[0]->receivedDate))
					{{ $view[0]->receivedDate }}
				@else
					NIL
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_enddate') }}</label>
			</div>
			<div>
				@if(isset($view[0]->endDate))
					{{ $view[0]->endDate }}
				@else
					NIL
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_bank_name') }}</label>
			</div>
			<div>
				@if(isset($view[0]->BankName))
					{{ $view[0]->BankName }}
				@else
					NIL
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_branch_name') }}</label>
			</div>
			<div>
				@if(isset($view[0]->BranchName))
					{{ $view[0]->BranchName }}
				@else
					NIL
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_accnumber') }}</label>
			</div>
			<div>
				@if(isset($view[0]->AccNo))
					{{ $view[0]->AccNo }}
				@else
					NIL
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_amount') }}</label>
			</div>
			<div>
				@if(isset($view[0]->currentBalance))
					{{ $view[0]->currentBalance }}
				@else
					NIL
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_period') }}</label>
			</div>
			<div>
				@if(isset($view[0]->period))
					{{ $view[0]->period }}
				@else
					NIL
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_interest') }}</label>
			</div>
			<div>
				@if(isset($view[0]->interest))
					{{ $view[0]->interest }} %
				@else
					NIL
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_paymentday') }}</label>
			</div>
			<div>
				@if(isset($view[0]->repaymentDate))
					{{ $view[0]->repaymentDate }}
				@else
					NIL
				@endif	
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_remainmonths') }}</label>
			</div>
			<div>
				@if(isset($view[0]->remainingMonths))
					{{ $view[0]->remainingMonths }}
				@else
					NIL
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_currbal') }}</label>
			</div>
			<div>
				@if(isset($view[0]->currentBalance))
					{{ $view[0]->currentBalance }} @if($view[0]->checkFlg == "1") <div class="black_box CMN_display_block"></div> @else <div class="white_box CMN_display_block"></div>@endif
				@else
					NIL
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_pdffile') }}</label>
			</div>
			<div>
			<?php
					$file_url = 'resources/assets/uploadandtemplates/upload/Loandetails/' . $view[0]->pdfFile;
				 ?>
				@if($view[0]->pdfFile != "" && file_exists($file_url))
					<a class="tac" href="javascript:download('{{ $view[0]->pdfFile }}','../../../resources/assets/uploadandtemplates/upload/Loandetails');" class="tal" style='color:blue;'>
						{{ $view[0]->pdfFile }}
					</a>
				@else
					NIL
				@endif
			</div>
		</div>
		<div class="col-xs-12 mb15">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_remarks') }}</label>
			</div>
			<div>
				@if($view[0]->remarks != "")
					{{ $view[0]->remarks }}
				@else
					NIL
				@endif
			</div>
		</div>
	</fieldset>
	</div>
	</article>
	</div>
	{{ Form::close() }}
@endsection
