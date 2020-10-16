{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::style('resources/assets/css/switch.css') }}
<style>
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
			$('#contentsel').val();
			$('.importprocess').click(function () {
		$("#importform").validate({
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
				contentsel: {required: true},
			},
			submitHandler: function(form) { // for demo
				var confirmprocess = confirm("Do You Want to Use This Database?.");
				if(confirmprocess) {
					pageload();
					if ($('#mainmenu').val() != "staff") {
						$('#importform').attr('action', '../Timesheet/importprocess?mainmenu');
					}
					form.submit();
					return true;
				} else {
					return false
				}
			}
		});
	});
		});
	function dblclick() {
		$('.importprocess').click();
		//submitHandler();
	}
</script>
<body>
{{ Form::open(array('name'=>'importform', 'id'=>'importform', 
                      'url' => 'Staff/importprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
                      'method' => 'POST')) }}
     {{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		<div class="modal-content">
			<div class="modal-header" style="padding: 0px !important;">
				<div class="col-md-2 pull-right mt15">
					<button type="button" data-dismiss="modal" class="close" style="color: red !important;" aria-hidden="true">&#10006;</button>
				</div>
				<div class="col-md-8" style="padding-left: 4px;">
					<h2 style="font-size:30px;" class="modal-title custom_align">{{ trans('messages.lbl_dbselection') }}</h2>
				</div>
			</div>
		<div class="modal-body">
		<div class="box98per" style="padding-left: 30px;margin-top: -5px">
		<fieldset style="width:98%;">
			<div class = "box50per ml150  mt15">
				<select id = "contentsel" name = "contentsel" size="30" style="height: 175px;margin-bottom: 13px;" ondblclick="return dblclick();" 
						class="combosize box250 importprocess">
					<?php
						foreach ($getOldDbDetails as $key => $value) {
							?>
							<option value = "<?=$key?>"><?php print_r($value); ?></option>
						<?php
						}
					?>
				</select>
			</div>
		</fieldset>
		</div>
	<div class="modal-footer bg-info" style="margin-top:-3px; width: 100%;">
		<center>
			<button class="btn btn-success add box100 ml5 importprocess pageload">
				<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_add') }}
			</button>
			<button type="button" data-dismiss="modal" class="btn btn-danger CMN_display_block box100 button" >
				<i class="fa fa-times" aria-hidden="true"></i> {{ trans('messages.lbl_cancel') }}</button>
		</center>
	</div>
	</div>
	</div>
{{ Form::close() }}
</body>
