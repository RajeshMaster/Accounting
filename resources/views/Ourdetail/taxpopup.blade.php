{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
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
			setDatePicker("txt_startdate");
			$('.taxprocess').click(function () {
		$("#taxform").validate({
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
				txt_tax: {required: true, number: true, greaterThan: true},
				txt_startdate: {required: true, date: true,correctformatdate: true},
			},
			submitHandler: function(form) { // for demo
					var confirmprocess = confirm(err_confreg);
				if(confirmprocess) {
					pageload();
					//form.submit();
					return true;
				} else {
					return false
				}
			}
		});
	    $.validator.addMethod('greaterThan', function (value, el, param) {
		    return value <= 100;
		}, "Tax Rate Is Not More Than 100.");
		$.validator.messages.required = function (param, input) {
			var article = document.getElementById(input.id);
			return article.dataset.label + ' field is required';
		}
	});
		});
</script>
<body>
{{ Form::open(array('name'=>'taxform', 'id'=>'taxform', 
                          'url' => 'Ourdetail/taxprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
                          'method' => 'POST')) }}
      {{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		<div class="modal-content">
			<div class="modal-header ml12" style="padding: 0px;">
				<div class="col-md-2 pull-right mt10">
					<button type="button" data-dismiss="modal" class="close" style="color: red !important;" aria-hidden="true">&#10006;</button>
				</div>
				<div class="col-md-8 mt10" style="padding-left: 4px;">
					<h2 style="font-size:30px;" class="modal-title custom_align">{{ trans('messages.lbl_Tax_added') }}</h2>
				</div>
			</div>
		<div class="modal-body">
		<fieldset class="ml10" style="width:96%;">
			<div class="col-md-12 mt10">
				<div class="col-md-4 text-right">
					<label class="clr_blue mt5">{{ trans('messages.lbl_Tax') }}<span class="fr ml2"> * </span></label>
				</div>
				<div class="col-md-8">
					{{ Form::text('txt_tax','',array(
													'id'=>'txt_tax',
													'name' => 'txt_tax',
													'class'=>'box12per form-control ime_mode_active',
													'maxlength' => 3,
													'data-label' => trans('messages.lbl_Tax'))) }}
					<span>&nbsp; % &nbsp;</span>
				</div>
			</div>
			<div class="col-md-12 mt10 mb10">
				<div class="col-md-4 text-right">
					<label class="clr_blue mt5">{{ trans('messages.lbl_Start_date') }}<span class="fr ml2"> * </span></label>
				</div>
				<div class="col-md-8">
					{{ Form::text('txt_startdate','',array(
														'id'=>'txt_startdate',
														'name' => 'txt_startdate',
														'class'=>'box25per form-control ime_mode_active txt_startdate',
														'maxlength' => 10,
														'data-label' => trans('messages.lbl_Start_date'))) }}
					<label class="mt10 ml2 fa fa-calendar fa-lg" for="txt_startdate" aria-hidden="true"></label>
				</div>
			</div>
		</fieldset>
	<div class="modal-footer bg-info" style="margin-top:-3px; width: 100%;">
		<center>
			<button type="submit" class="btn btn-success add box100 ml5 taxprocess">
				<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_add') }}
			</button>
			<button type="button" data-dismiss="modal" class="btn btn-danger CMN_display_block box100 button" ><i class="fa fa-times" aria-hidden="true"></i>
			 {{ trans('messages.lbl_cancel') }}</button>
		</center>
	</div>
	</div>
	</div>
{{ Form::close() }}
</body>