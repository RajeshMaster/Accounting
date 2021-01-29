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
		setDatePicker("dob");
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
		{{ Form::open(array('name'=>'creditdetailedit','id'=>'creditdetailedit', 
			'url' => 'CreditCardPay/detailseditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
			'enctype' => 'multipart/form-data',
			'files'=>true,'method' => 'POST')) }}

		{{ Form::hidden('edit_flg', $request->edit_flg, array('id' => 'edit_flg')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('id', $request->id, array('id' => 'id')) }}

		<div class="row hline pm0">
			<div class="col-xs-12">
				<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/expenses_icon.png') }}">
				<h2 class="pull-left pl5 mt10">{{ trans('messages.lbl_creditCardPay') }}</h2>
				<h2 class="pull-left mt10">・</h2>
				<h2 class="pull-left mt10 red">{{ trans('messages.lbl_edit') }}</h2>
			</div>
		</div>
		
		<div class="col-xs-12 pl5 pr5">
		<fieldset>
		
			<div class="col-xs-12 mt15">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_Date') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ $creditcardDetails[0]->mainDate }}
						{{ Form::hidden('mainDate',(isset($creditcardDetails[0]->mainDate)) ? $creditcardDetails[0]->mainDate : '',
								array('id'=>'mainDate',
									'name' => 'mainDate',
									'autocomplete' => 'off',
									'readonly' => 'true',
									'class'=>'box12per txt_startdate form-control dob',
									'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
									'data-label' => trans('messages.lbl_Date'),
									'maxlength' => '10')) }}
						<!-- <label class="fa fa-calendar fa-lg" for="mainDate" aria-hidden="true">
						</label>
						<a href="javascript:getdate('Transfer');" class="anchorstyle">
							<img title="Current Date" class="box15" src="{{ URL::asset('resources/assets/images/add_date.png') }}"></a> -->
				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_creditCardName') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ $creditcardDetails[0]->creditCardName }}

					{{ Form::hidden('creditCard', (isset($creditcardDetails[0]->creditCardId)) ? 
														$creditcardDetails[0]->creditCardId : '' , array('id' => 'creditCard')) }}

				</div>
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_CreditDate') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ $creditcardDetails[0]->creditCardDate }}
					{{ Form::hidden('creditCardDate',(isset($creditcardDetails[0]->creditCardDate)) ? $creditcardDetails[0]->creditCardDate : '',
								array('id'=>'creditCardDate',
									'name' => 'creditCardDate',
									'autocomplete' => 'off',
									'readonly' => 'true',
									'class'=>'box12per txt_startdate form-control dob',
									'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
									'data-label' => trans('messages.lbl_Date'),
									'maxlength' => '10')) }}
				</div>
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_categories') }}<span class="fr ml2 red" style="visibility: hidden;"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::select('categories',[null=>'']+$categoryName,(isset($creditcardDetails[0]->categoryId)) ? 
												$creditcardDetails[0]->categoryId : '',
															array('name' =>'categories',
															'id'=>'categories',
															'data-label' => trans('messages.lbl_creditCard'),
															'class'=>'pl5 widthauto'))}}
				</div>
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_content') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
					{{ $creditcardDetails[0]->creditCardContent }}

					{{ Form::hidden('content',(isset($creditcardDetails[0]->creditCardContent)) ? $creditcardDetails[0]->creditCardContent : '',
										array('id'=>'content', 
																'name' => 'content',
																'autocomplete' =>'off',
																'readonly' => 'true',
																'data-label' => trans('messages.lbl_content'),
																'class'=>'box31per form-control pl5')) }}
				</div>
			</div>

			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_amount') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">

					{{ number_format($creditcardDetails[0]->creditCardAmount) }}

					{{ Form::hidden('amount',(isset($creditcardDetails[0]->creditCardAmount)) ? number_format($creditcardDetails[0]->creditCardAmount) : 0,array('id'=>'amount', 
															'name' => 'amount',
															'style'=>'text-align:right;',
															'maxlength' => 10,
															'autocomplete' =>'off',
															'readonly' => 'true',
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
					<label>{{ trans('messages.lbl_bill') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div class="col-xs-9">
						@if(isset($creditcardDetails[0]->rdoBill) && $creditcardDetails[0]->rdoBill == 1)
							<?php $rdoBillVal = 1; ?>
						@else
							<?php $rdoBillVal = 0; ?>
						@endif
					<label style="font-weight: normal;display: inline-block;">
								{{ Form::radio('rdoBill', '1',$rdoBillVal, 
											array('id' =>'Bill1',
												  'name' => 'rdoBill',
												  'class' => 'Bill1',
												  'style' => 'margin:-2px 0 0 !important',
												  'data-label' => trans('messages.lbl_bill'))) }}
						<span class="vam">&nbsp; 有 &nbsp;</span>
					</label>
						@if(isset($creditcardDetails[0]->rdoBill) && $creditcardDetails[0]->rdoBill == 2)
							@php $rdoBillVal = 1; @endphp
						@else
							@php $rdoBillVal = 0; @endphp
						@endif
					<label style="font-weight: normal;display: inline-block;">
						{{ Form::radio('rdoBill', '2',$rdoBillVal,
									array('id' =>'Bill2',
										  'name' => 'rdoBill',
										  'class' => 'Bill2',
										  'style' => 'margin:-2px 0 0 !important',
										  'data-label' => trans('messages.lbl_bill'))) }}
						<span class="vam">&nbsp; 無 &nbsp;</span>
					</label>
				</div>
			</div>


			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_image') }}<span class="fr ml2 red"> &nbsp;&nbsp; </span></label>
				</div>
				<div class="col-xs-9">
					{{ Form::file('transferBill',array(
												'class' => 'pull-left box350',
												'id' => 'transferBill',
												'name' => 'transferBill',
												'style' => 'height:23px;',
												'accept' => 'image/*',
												'data-label' => trans('messages.lbl_bill'))) }}
						<span>&nbsp;(Ex: Image File Only)</span>
						@if(isset($creditcardDetails))
						<?php $file_url = '../AccountingUpload/CreditCard/' . $creditcardDetails[0]->file; ?>
						@if(isset($creditcardDetails[0]->file) && file_exists($file_url))
							<!-- <a style="text-decoration:none" href="{{ URL::asset('../../../../AccountingUpload/Accounting').'/'.$creditcardDetails[0]->file }}" data-lightbox="visa-img"></a> -->
							<img width="20" height="20" name="empimg" id="empimg" 
							class="ml5 box20 viewPic3by2" src="{{ URL::asset('../../../../AccountingUpload/CreditCard').'/'.$creditcardDetails[0]->file }}">
							{{ Form::hidden('imgfiles', $creditcardDetails[0]->file , array('id' => 'imgfiles')) }}
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
					{{ Form::textarea('remarks',(isset($creditcardDetails[0]->remarks)) ? $creditcardDetails[0]->remarks : '',
									array('id'=>'remarks', 
												'name' => 'remarks',
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
					<button type="submit" class="btn btn-warning add box100 ml5 detailedit">
						<i class="fa fa-edit" aria-hidden="true"></i> 
						{{ trans('messages.lbl_update') }}
					</button>&nbsp;
					<a href="javascript:gotoindexpage('addeditDetail');" 
						class="btn btn-danger box120 white">
						<i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
					</a>
				</div>
			</div>

		</fieldset>
		</div>

	{{ Form::close() }}

	{{ Form::open(array('name'=>'detailsaddeditcancel', 'id'=>'detailsaddeditcancel', 
						'url' => 'CreditCardPay/detailseditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
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
