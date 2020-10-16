<style>
	.scrollbar {
		overflow-y: auto;
		padding: 5px;
		height: 100%;
		width:100%;
		max-width: 100%;
		margin-bottom: 0px;
	}
	.table_Scroll_limit_set{
		overflow-y: scroll;
		overflow-x: hidden;
		height: 225px;
		background-color: #FFFFFF;
	}
	.modal {
      position: fixed;
      top: 50% !important;
      left: 50%;
      transform: translate(-50%, -50%);
   }
</style>
<script type="text/javascript">
		var datetime = '<?php echo date('Ymdhis'); ?>';
		$( document ).ready(function() {
			$('.balsheetprocess').click(function () {
		$("#balform").validate({
			showErrors: function(errorMap, errorList) {
			// Clean up any tooltips for valid elements
				$.each(this.validElements(), function (index, element) {
						var $element = $(element);
						$element.data("title", "") // Clear the title - there is no error associated anymore
								.removeClass("error")
								.tooltip("destroy");
				});
				// Create new tooltips for invalid elements
				$.each(errorList, function (index, error) {
						var $element = $(error.element);
						$element.tooltip("destroy") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
								.data("title", error.message)
								.addClass("error")
								.tooltip(); // Create a new tooltip based on the error messsage we just set in the title
				});
			},
			rules: {
				period: {required: true, number: true},
				startyear: {required: true, number: true},
				startmonth: {required: true, number: true},
				endyear: {required: true, number: true},
				endmonth: {required: true, number: true},
			},
			submitHandler: function(form) { // for demo
				if($('#balid').val() != "") {
					var confirmprocess = confirm(err_confup);
				} else {
					var confirmprocess = confirm(err_confreg);
				}
				if(confirmprocess) {
					pageload();
					//form.submit();
					return true;
				} else {
					return false
				}
			}
		});
		$.validator.messages.required = function (param, input) {
			var article = document.getElementById(input.id);
			return article.dataset.label + ' field is required';
		}
	});
		});
</script>
<body>
{{ Form::open(array('name'=>'balform', 'id'=>'balform', 
                          'url' => 'Ourdetail/balsheetprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
                          'method' => 'POST')) }}
      {{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
      {{ Form::hidden('balid', $request->balid , array('id' => 'balid')) }}
		<div class="modal-content">
			<div class="modal-header" style="padding: 0px;">
				<div class="col-md-2 pull-right mt10">
					<button type="button" data-dismiss="modal" class="close" style="color: red !important;" aria-hidden="true">&#10006;</button>
				</div>
				<div class="box90per mt15 h37" style="padding-left: 4px;">
					<h2 style="font-size:30px;" class="modal-title custom_align">{{ trans('messages.lbl_balance_sheet_details') }}<span class="">・</span>@if ($request->balid!="")<span style="color:red">{{ trans('messages.lbl_edit') }}</span>@else<span style="color:green">{{ trans('messages.lbl_register') }}</span>@endif</h2>
				</div>
			</div>
		<div class="modal-body">
		<div class="box100per">
		<fieldset style="width:100%;">
			<div class="mt10">
				<div class="col-md-4 text-right">
					<label class="clr_blue mt5">{{ trans('messages.lbl_balance_sheet_period') }}<span class="fr ml2"> * </span></label>
				</div>
				<div class="">
				@if ($request->balid!="")
					{{ Form::text('period',$edit[0]->Accountperiod,array(
														'id'=>'period',
														'name' => 'period',
														'class'=>'box14per form-control ime_mode_active',
														'maxlength' => 3,
														'data-label' => trans('messages.lbl_balance_sheet_period'))) }}
				@else
					{{ Form::text('period','',array(
														'id'=>'period',
														'name' => 'period',
														'class'=>'box14per form-control ime_mode_active',
														'maxlength' => 3,
														'data-label' => trans('messages.lbl_balance_sheet_period'))) }}
				@endif
				</div>
			</div>
			<div class="mt10 mb10">
				<div class="col-md-4 text-right">
					<label class="clr_blue mt5">{{ trans('messages.lbl_balance_sheet_start') }}<span class="fr ml2"> * </span></label>
				</div>
				<div class="">
					@if ($request->balid!="")
						{{ Form::select('startyear',[null=>''] + $gradyear,$edit[0]->Startingyear,array(
															'id'=>'startyear',
															'name' => 'startyear',
															'class'=>'form-control widthauto ime_mode_active',
															'maxlength' => 10,
															'data-label' => trans('messages.lbl_startyr'))) }}
						<span>&nbsp; Yr &nbsp;</span>
						{{ Form::select('startmonth',[null=>''] + range(1,12),$edit[0]->Startingmonth,array(
															'id'=>'startmonth',
															'name' => 'startmonth',
															'class'=>'form-control widthauto ime_mode_active',
															'maxlength' => 10,
															'data-label' => trans('messages.lbl_startmn'))) }}
						<span>&nbsp; Mn  ~&nbsp;</span>
						{{ Form::select('endyear',[null=>''] + $gradyear,$edit[0]->Closingyear,array(
															'id'=>'endyear',
															'name' => 'endyear',
															'class'=>'form-control widthauto ime_mode_active',
															'maxlength' => 10,
															'data-label' => trans('messages.lbl_endyr'))) }}
						<span>&nbsp; Yr &nbsp;</span>
						{{ Form::select('endmonth',[null=>''] + range(1,12),$edit[0]->Closingmonth,array(
															'id'=>'endmonth',
															'name' => 'endmonth',
															'class'=>'form-control widthauto ime_mode_active',
															'maxlength' => 10,
															'data-label' => trans('messages.lbl_endmn'))) }}
						<span>&nbsp; Mn &nbsp;</span>
					@else
						{{ Form::select('startyear',[null=>''] + $gradyear,'',array(
															'id'=>'startyear',
															'name' => 'startyear',
															'class'=>'form-control widthauto ime_mode_active',
															'maxlength' => 10,
															'data-label' => trans('messages.lbl_startyr'))) }}
						<span>&nbsp; Yr &nbsp;</span>
						{{ Form::select('startmonth',[null=>''] + range(1,12),'',array(
															'id'=>'startmonth',
															'name' => 'startmonth',
															'class'=>'form-control widthauto ime_mode_active',
															'maxlength' => 10,
															'data-label' => trans('messages.lbl_startmn'))) }}
						<span>&nbsp; Mn  ~&nbsp;</span>
						{{ Form::select('endyear',[null=>''] + $gradyear,'',array(
															'id'=>'endyear',
															'name' => 'endyear',
															'class'=>'form-control widthauto ime_mode_active',
															'maxlength' => 10,
															'data-label' => trans('messages.lbl_endyr'))) }}
						<span>&nbsp; Yr &nbsp;</span>
						{{ Form::select('endmonth',[null=>''] + range(1,12),'',array(
															'id'=>'endmonth',
															'name' => 'endmonth',
															'class'=>'form-control widthauto ime_mode_active',
															'maxlength' => 10,
															'data-label' => trans('messages.lbl_endmn'))) }}
						<span>&nbsp; Mn &nbsp;</span>
					@endif
				</div>
			</div>
		</fieldset>
	</div>
	<div class="modal-footer bg-info" style="width: 100%;">
		<center>
			@if ($request->balid!="")
				<button type="submit" class="btn edit btn-warning box100 ml5 balsheetprocess">
					<i class="fa fa-edit" aria-hidden="true"></i> {{ trans('messages.lbl_update') }}
				</button>
				<button type="button" data-dismiss="modal" class="btn btn-danger CMN_display_block box100 button" ><i class="fa fa-times" aria-hidden="true"></i> {{ trans('messages.lbl_cancel') }} </button>
			@else
				<button type="submit" class="btn btn-success add box100 ml5 balsheetprocess">
					<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_add') }}
				</button>
				<button type="button" data-dismiss="modal" class="btn btn-danger CMN_display_block box100 button" ><i class="fa fa-times" aria-hidden="true"></i>  {{ trans('messages.lbl_cancel') }} </button>
			@endif
		</center>
	</div>
	</div>
	</div>
{{ Form::close() }}
</body>