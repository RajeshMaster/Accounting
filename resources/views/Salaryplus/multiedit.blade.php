@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/salaryplus.js') }}
{{ HTML::script('resources/assets/js/expenses.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var dates = '<?php echo date('Y-m-d'); ?>';
	$(document).ready(function() {
		$('#date').val(dates);
		$('#date_hdn').val(dates);
		setDatePicker("dob");
	    calculateSum();

	    $(".totalclick").on("keydown keyup", function() {
	    	var suffix = this.id.match(/\d+/); // 123456
	        calculateSum(suffix);
	    });

	});
	function negativeamt(id,amt) {
	var amt = $('#'+id).val();
	amt = $('#'+id).val().replace(/[^0-9]/gi, '');
	//amt = Number(amt.trim().replace(/[, ]+/g, ""));
	if (amt == "") {
		$('#'+id).focus();  
		$('#'+id).val('-');
	} else {
		$('#'+id).focus(); 
		if (amt>0) {
			value1 = amt;
			tot = value1.toLocaleString();
			amount = "-"+tot;
			document.getElementById(""+id).value = amount;
		}
	}
}
	function calculateSum(suffix) {
	    var sum = 0;
	    //iterate through each textboxes and add the values
	    $(".txt"+suffix).each(function() {
	    	var remnum = Number(this.value.trim().replace(/[, ]+/g, ""));
	        //add only if the value is number
	        if (!isNaN(remnum) && this.value.length != 0) {
	            sum += parseFloat(remnum);
	            // $(this).css("background-color", "#FEFFB0");
	        }
	        else if (this.value.length != 0){
	            // $(this).css("background-color", "red");
	        }
	    });
	    var amount = Math.abs(sum.toFixed(0));
		var value1 = amount;
		var tot = value1.toLocaleString();
		$("#totalspan"+suffix).text(tot);
	}
</script>
<style type="text/css">
		.clr_blue1{
		 color: blue ! important;
	}
</style>
<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_10">
		{{ Form::open(array('name'=>'frmmultireg',
							'id'=>'frmmultireg',
							'url'=>'Salaryplus/multiregister?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
							'files'=>true,
							'method' => 'POST' )) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('date_hdn', '', array('id' => 'date_hdn')) }}
		{{ Form::hidden('count', count($get_det), array('id' => 'count')) }}
		<div class="row hline pm0">
			<div class="col-xs-12">
				<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/salary_1.png') }}">
				<h2 class="pull-left pl5 mt10">
					{{ trans('messages.lbl_salaryplus') }} <span>・</span><span class="green">{{ trans('messages.lbl_multireg') }}</span></h2>
			</div>
		</div>
		<div class="col-xs-12">
      		<div class="col-xs-6 pm0 pull-left mb10 pl10 pr10 mt20 fwb">
	        	{{ trans('messages.lbl_year').':' }}
	          	<span class="mr40 ml12" style="color:brown;">
	            {{ $request->selYear }}
	          	</span>
             	{{ trans('messages.lbl_month').':' }}
    			{{ Form::selectRange('month', 1, 12, $request->selMonth, array('id' => 'month', 'onchange' => 'monthchange(this.value)')) }}
      		</div>
		</div>
		<div class="minh400 box100per pl10 pr10 mt10">
			<table class="tablealternate box100per CMN_tblfixed">
				<colgroup>
					<col width="3%">
					<col width="7%">
					<col width="">
					<col width="10%">
					<col width="7%">
					<col width="7%">
					<col width="6%">
					<col width="7%">
					<col width="7%">
					<col width="7%">
					<col width="7%">
					<col width="7%">	
					<col width="8%">	
				</colgroup>
				<thead class="CMN_tbltheadcolor">
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam">{{ trans('messages.lbl_empno') }}</th>
					<th class="vam">{{ trans('messages.lbl_name') }}</th>
					<th class="vam">{{ trans('messages.lbl_basic') }}</th>
					<th class="vam" title="{{ trans('messages.lbl_House_Rent_allowance') }}">{{ trans('messages.lbl_HRA') }}</th>
					<th class="vam" title="{{ trans('messages.lbl_overtime') }}">{{ trans('messages.lbl_OT') }}</th>
					<th class="vam">{{ trans('messages.lbl_leave') }}</th>
					<th class="vam">{{ trans('messages.lbl_bonus') }}</th>
					<th class="vam" title="{{ trans('messages.lbl_esi_f') }}">{{ trans('messages.lbl_esi') }}</th>
					<th class="vam" title="Income Tax">{{ trans('messages.lbl_it') }}</th>
					<th class="vam">{{ trans('messages.lbl_travel') }}</th>
					<th class="vam" title="{{ trans('messages.lbl_monthlytravel') }}">{{ trans('messages.lbl_m_travel') }}</th>
					<th class="vam"></th>
				</thead>
				<tbody>
					@if (count($get_det) > 0)
						@for ($i = 0; $i < count($get_det); $i++)
						<tr id="row<?php echo $i ?>">
							 <td class="text-center">
				             	 {{  $i + 1 }}
				             	 <input type="hidden" name="count" id="count" value="<?php echo count($get_det); ?>">
				             </td>	
				             <td class="">
				             	<div class="tac">
				             	 {{ $get_det[$i]['Emp_ID'] }}
								{{ Form::hidden('Emp_ID'.$i, $get_det[$i]['Emp_ID'], array('id' => 'Emp_ID'.$i)) }}
				                </div>
				             </td>
				             <td>
				             	{{ empnamelength($get_det[$i]['LastName'], $get_det[$i]['FirstName'],20) }}
				             </td>
				             <td class="text-right pr10">
	                    		{{ Form::text('basic'.$i,'',array('id'=>'basic'.$i, 
																			'name' => 'basic'.$i,
																			'maxlength' => '14',
																			'style'=>'text-align:right;padding-right:4px;',
																			'onkeypress' => 'return isNumberKey(event)',
																			'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
																			'class'=>'txt'.$i.' totalclick box99per form-control')) }}
	                    	</td>
	                    	<td class="text-right pr10">
	                    		{{ Form::text('hra'.$i,'',array('id'=>'hra'.$i, 
																			'name' => 'hra'.$i,
																			'maxlength' => '14',
																			'style'=>'text-align:right;padding-right:4px;',
																			'onkeypress' => 'return isNumberKey(event)',
																			'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
																			'class'=>'txt'.$i.' totalclick box99per form-control')) }}
	                    	</td>
	                    	<td class="text-right pr10">
	                    		{{ Form::text('ot'.$i,'',array('id'=>'ot'.$i, 
																			'name' => 'ot'.$i,
																			'maxlength' => '14',
																			'style'=>'text-align:right;padding-right:4px;',
																			'onkeypress' => 'return isNumberKey(event)',
																			'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
																			'class'=>'txt'.$i.' totalclick box99per form-control')) }}
	                    	</td>
	                    	<td class="text-right pr10">
	                    		{{ Form::text('leave'.$i,'',array('id'=>'leave'.$i, 
																			'name' => 'leave'.$i,
																			'maxlength' => '14',
																			'style'=>'text-align:right;padding-right:4px;color:red;',
																			'onkeypress' => 'return isNumberKey(event)',
																			'onkeyup'=>'return fnMoneyFormatNegative(this.id,"jp");negativeamt(this.id,this.value)',
																			'class'=>'txt'.$i.' totalclick box99per form-control')) }}
							</td>
							<td class="text-right pr10">
	                    		{{ Form::text('bonus'.$i,'',array('id'=>'bonus'.$i, 
																			'name' => 'bonus'.$i,
																			'maxlength' => '14',
																			'style'=>'text-align:right;padding-right:4px;',
																			'onkeypress' => 'return isNumberKey(event)',
																			'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
																			'class'=>'txt'.$i.' totalclick box99per form-control')) }}
							</td>
	                    	<td class="text-right pr10">
	                    		{{ Form::text('esi'.$i,'',array('id'=>'esi'.$i, 
																			'name' => 'esi'.$i,
																			'maxlength' => '14',
																			'style'=>'text-align:right;padding-right:4px;color:red;',
																			'onkeypress' => 'return isNumberKey(event)',
																			'onkeyup'=>'return fnMoneyFormatNegative(this.id,"jp");negativeamt(this.id,this.value)',
																			'class'=>'txt'.$i.' totalclick box99per form-control')) }}
	                    	</td>
	                    	<td class="text-right pr10">
	                    		{{ Form::text('it'.$i,'',array('id'=>'it'.$i, 
																			'name' => 'it'.$i,
																			'maxlength' => '14',
																			'style'=>'text-align:right;padding-right:4px;color:red;',
																			'onkeypress' => 'return isNumberKey(event)',
																			'onkeyup'=>'return fnMoneyFormatNegative(this.id,"jp");negativeamt(this.id,this.value)',
																			'class'=>'txt'.$i.' totalclick box99per form-control')) }}
	                    	</td>
	                    	<td class="text-right pr10">
	                    		{{ Form::text('travel'.$i,'',array('id'=>'travel'.$i, 
																			'name' => 'travel'.$i,
																			'maxlength' => '14',
																			'style'=>'text-align:right;padding-right:4px;',
																			'onkeypress' => 'return isNumberKey(event)',
																			'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
																			'class'=>'txt'.$i.' totalclick box99per form-control')) }}
	                    	</td>
	                    	<td class="text-right pr10">
	                    		{{ Form::text('mtravel'.$i,'',array('id'=>'mtravel'.$i, 
																			'name' => 'mtravel'.$i,
																			'maxlength' => '14',
																			'style'=>'text-align:right;padding-right:4px;',
																			'onkeypress' => 'return isNumberKey(event)',
																			'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
																			'class'=>'txt'.$i.' totalclick box99per form-control')) }}
							</td>
							<td class="text-right pr10">
	                    		<span id="totalspan<?php echo $i ?>" class="clr_blue1 totamt" style="font-size:10px;">
								</span>
								<span class="" style="font-size:10px;">円</span>
							</td>
						</tr>
						@endfor
					@else
						<tr>
							<td class="text-center" colspan="13" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
						</tr>
					@endif
				</tbody>	
			</table>
			<fieldset style="background-color: #DDF1FA;">
			<div class="form-group">
			<div align="center" class="mt8">
				<button type="submit" class="btn btn-success add box100 multiplereg ml5">
						<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
				</button>
				<a onclick="javascript:goindex('index','{{$request->mainmenu}}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				</a>
			</div>
			</div>
			</fieldset>
			{{ Form::close() }}

			{{ Form::open(array('name'=>'monthonchangefrm',
							'id'=>'monthonchangefrm',
							'url'=>'Salaryplus/multiregister?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
							'files'=>true,
							'method' => 'POST' )) }}
				{{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
				{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
				{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
			{{ Form::close() }}

	    {{ Form::open(array('name'=>'salaryplusmultieditcancel', 'id'=>'salaryplusmultieditcancel', 'url' => 'Salaryplus/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('date_hdn', '', array('id' => 'date_hdn')) }}
		{{ Form::hidden('count', count($get_det), array('id' => 'count')) }}
		{{ Form::close() }}

	</article>
</div>
@endsection
