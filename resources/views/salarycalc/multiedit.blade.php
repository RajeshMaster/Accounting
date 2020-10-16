@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/salaryplus.js') }}
{{ HTML::script('resources/assets/js/salarycalc.js') }}
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
	    	// var suffix = this.id.match(/\d+/); // 123456
	    	var suffix = $(this).attr('data-id'); // 123456
			// alert($(this).attr('data-id'));
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

	$(document).ready(function() {
  		var ht;
  		var lastWindowHeight = $(window).height();
        ht= $(window).height();
        if(ht!=lastWindowHeight) {
        	$("#sidebar").height(ht);
        }
	});
	$(window).bind('resize', function () { 
  		var ht;
  		var lastWindowHeight = $(window).height();
        ht= $(window).height();
        if(ht!=lastWindowHeight) {
        	$("#sidebar").height(ht);
        }
	});
</script>
<style type="text/css">
		.clr_blue1{
		 color: blue ! important;
	}
</style>
<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_11">
		@if(isset($detedit))
		    {{ Form::model($detedit, ['name'=>'frmmultireg', 
									'id'=>'frmmultireg', 
									'url' => 'salarycalc/multiregister?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
									'files' => true,
									'method' => 'POST']) }}
		@else
			{{ Form::open(array('name'=>'frmmultireg',
								'id'=>'frmmultireg',
								'url'=>'salarycalc/multiregister?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
								'files'=>true,
								'method' => 'POST' )) }}
		@endif
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('date_hdn', '', array('id' => 'date_hdn')) }}
		{{ Form::hidden('count', count($get_det), array('id' => 'count')) }}
		<div class="row hline pm0">
			<div class="col-xs-12">
				<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/salary_1.png') }}">
				<h2 class="pull-left pl5 mt10">
					{{ trans('messages.lbl_salary_calc') }} <span>・</span><span class="green">{{ trans('messages.lbl_multireg') }}</span></h2>
			</div>
		</div>
		<div class="col-xs-12">
      		<div class="col-xs-6 pm0 pull-left mb10 pl10 pr10 mt20 fwb">
	        	{{ trans('messages.lbl_year').':' }}
	          	<span class="mr40 ml12" style="color:brown;">
	            {{ $request->selYear }}
	          	</span>
             	{{ trans('messages.lbl_month').':' }}
             	@if($request->salflg == 1)
	             	<span class="mr40 ml12" style="color:brown;">
		            	{{ $request->selMonth }}
		          	</span>
             	@else
    				{{ Form::selectRange('month', 1, 12, $request->selMonth, array('id' => 'month', 'onchange' => 'monthchangecalc(this.value)')) }}
    			@endif
      		</div>
		</div>
		<div class="minh400 box100per pl10 pr10 mt10">
			<div style="border: 1px solid white;overflow-x: auto;" id="sidebar">
				@php $count = 43; @endphp
				@if(count($salary_det)!="")
	 				@for ($i = 0; $i < count($salary_det); $i++)
	 					@php $count += 10; @endphp
	 				@endfor
 				@endif
 				@if(count($salary_ded)!="")
	 				@for ($j = 0; $j < count($salary_ded); $j++)
	 					@php $count += 10; @endphp
	 				@endfor
 				@endif
 				@if($count<100)
 					@php $count = 100; @endphp
 				@endif
			<table class="tablealternate CMN_tblfixed" style="width: <?php echo $count; ?>% !important;">
				<colgroup>
					<col width="3%">
					<col width="7%">
					<col width="18%">
					@if(count($salary_det)!="")
		 				@for ($i = 0; $i < count($salary_det); $i++)
		 					<col width="10%">
		 				@endfor
	 				@endif
	 				@if(count($salary_ded)!="")
		 				@for ($j = 0; $j < count($salary_ded); $j++)
		 					<col width="10%">
		 				@endfor
	 				@endif
					<col width="7%">	
					<col width="8%">	
				</colgroup>
				<thead class="CMN_tbltheadcolor">
					<tr>
						<th rowspan="2" class="vam">{{ trans('messages.lbl_sno') }}</th>
						<th rowspan="2" class="vam">{{ trans('messages.lbl_empno') }}</th>
						<th rowspan="2" class="vam">{{ trans('messages.lbl_name') }}</th>
	 					<th rowspan="1" colspan="<?php echo count($salary_det); ?>" class="vam">{{ trans('messages.lbl_salary_det') }}</th>
	 					<th rowspan="1" colspan="<?php echo count($salary_ded); ?>" class="vam">{{ trans('messages.lbl_salary_ded') }}</th>
						<th rowspan="2" class="vam"></th>
						<th rowspan="2" class="vam">{{ trans('messages.lbl_transferred') }}</th>
					</tr>
					<tr>
						@if(count($salary_det)!="")
			 				@for ($i = 0; $i < count($salary_det); $i++)
			 					<th class="vam">{{ $salary_det[$i]->Name }}</th>
			 				@endfor
		 				@endif
		 				@if(count($salary_ded)!="")
			 				@for ($j = 0; $j < count($salary_ded); $j++)
			 					<th class="vam">{{ $salary_ded[$j]->Name }}</th>
			 				@endfor
		 				@endif
					</tr>
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
	                    	@if(count($salary_det)!="")
				 				@for ($m = 0; $m < count($salary_det); $m++)
				 					<td class="text-right pr10">
			                    		{{ Form::text('salary_'.$get_det[$i]['Emp_ID'].'_'.$salary_det[$m]->Salarayid,null,array('id'=>'salary_'.$get_det[$i]['Emp_ID'].'_'.$salary_det[$m]->Salarayid, 'name' => 'salary_'.$get_det[$i]['Emp_ID'].'_'.$salary_det[$m]->Salarayid,'maxlength' => '14','style'=>'text-align:right;padding-right:4px;','onkeypress' => 'return isNumberKey(event)','onkeyup'=>'return fnMoneyFormat(this.id,"jp")','data-id'=>$i,'class'=>'txt'.$i.' totalclick box99per form-control')) }}
									</td>
				 				@endfor
				 			@else
				 				<td></td>
			 				@endif
			 				@if(count($salary_ded)!="")
				 				@for ($n = 0; $n < count($salary_ded); $n++)
				 					<td class="text-right pr10">
			                    		{{ Form::text('Deduction_'.$get_det[$i]['Emp_ID'].'_'.$salary_ded[$n]->Salarayid,null,array('id'=>'Deduction_'.$get_det[$i]['Emp_ID'].'_'.$salary_ded[$n]->Salarayid,'name' => 'Deduction_'.$get_det[$i]['Emp_ID'].'_'.$salary_ded[$n]->Salarayid,'maxlength' => '14','style'=>'text-align:right;padding-right:4px;color:red;','onkeypress' => 'return isNumberKey(event)','onkeyup'=>'return fnMoneyFormatNegative(this.id,"jp");negativeamt(this.id,this.value)','data-id'=>$i,'class'=>'txt'.$i.' totalclick box99per form-control')) }}
									</td>
				 				@endfor
				 			@else
				 				<td></td>
			 				@endif
							<td class="text-right pr10">
	                    		<span id="totalspan<?php echo $i ?>" class="clr_blue1 totamt" style="font-size:10px;">
								</span>
								<span class="" style="font-size:10px;">円</span>
							</td>
							<td class="text-right pr10">
			                    		{{ Form::text('transferred_'.$get_det[$i]['Emp_ID'],'',array('id'=>'transferred_'.$get_det[$i]['Emp_ID'], 
																					'name' => 'transferred_'.$get_det[$i]['Emp_ID'],
																					'maxlength' => '14',
																					'style'=>'text-align:right;padding:0px !important;width:110%;',
																					'onkeypress' => 'return isNumberKey(event)',
																					'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
																					'class'=>'totalclick  form-control')) }}
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
			</div>
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

		@if(isset($detedit))
			@for ($i=0; $i < count($detedit); $i++)
			    <script type="text/javascript">
		            calculateSum('{{ $i }}')
		        </script>
			@endfor
		@endif

	</article>
</div>
@endsection
