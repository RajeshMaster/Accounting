@extends('layouts.app')
@section('content')

{{ HTML::script('resources/assets/js/expenses.js') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	$(document).ready(function() {
		setDatePicker("date");
	});
</script>
{{ HTML::script('resources/assets/js/expenses.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_1">
	{{ Form::open(array('name'=>'frmmultireg',
						'id'=>'frmmultireg',
						'url' => 'Expenses/multipleregprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	<!-- Start Heading -->
	<div class="row hline pm0">
	<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/expenses.png') }}">
			<h2 class="pull-left pl5 mt10">{{ trans('messages.lbl_expenses') }}<span>・</span><span class="green">{{ trans('messages.lbl_multireg') }}</span></h2>
		</div>
	</div>
	<div class="pb10"></div>
	<!-- End Heading -->
	<div class="col-xs-12 mt5 mb10">
		<div class="col-xs-3 text-right clr_blue">
			<label>{{ trans('messages.lbl_Date') }}<span class="fr ml2 red"> * </span></label>
		</div>
		<div class="col-xs-9">
			{{ Form::text('date',(isset($expcash_sql[0]->date)) ? $expcash_sql[0]->date : '',array('id'=>'date', 
														'name' => 'date',
														'data-label' => trans('messages.lbl_Date'),
														'class'=>'box11per form-control pl5 date')) }}
			<label class="mt10 ml2 fa fa-calendar fa-lg" for="date" aria-hidden="true"></label>
			@if (Session::get('userclassification') == 4)
				&nbsp;&nbsp;{{ Form::checkbox('accessrights', 1,1, ['id' => 'accessrights']) }}
				&nbsp;<label for="accessrights"><span class="grey fb">{{ trans('messages.lbl_accessrights') }}</span></label>
			@endif
		</div>
	</div>
	<div class="mr10 ml10">
		<div class="minh400">
			<table class="tablealternate box100per">
				<colgroup>
				   <col width="3%">
				   <col width="19%">
				   <col width="19%">
				   <col width="12%">
				   <col width="12%">
				   <col width="15%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
			   		<tr> 
				  		<th rowspan="2" class="tac">{{ trans('messages.lbl_sno') }}</th>
				  		<th rowspan="2" class="tac">{{ trans('messages.lbl_mainsubject') }}</th>
				  		<th rowspan="2" class="tac">{{ trans('messages.lbl_subsubject') }}</th>
				  		<th rowspan="1" colspan="2" class="tac">{{ trans('messages.lbl_amount') }}</th>
				  		<th rowspan="2" class="tac">{{ trans('messages.lbl_remarks') }}</th>
			   		</tr>
			   		<tr>
              			<th>{{ trans('messages.lbl_cash') }}</th> 
              			<th>{{ trans('messages.lbl_expenses') }}</th> 
            		</tr>
			   	</thead>
			   	<tbody>
			   		@for ($i = 0; $i < count($getreg_det); $i++)
			   			<tr>
						<td style="text-align: center;">
							{{ $i+1 }}
							<input type="hidden" name="screen_name" id="screen_name" value="expense_multireg">
							<input type="hidden" name="count" id="count" value="<?php echo count($getreg_det); ?>">
							<input type="hidden" name="id_<?php echo$i?>" id="id_<?php echo$i?>" value="<?php echo $getreg_det[$i]['id']; ?>">
							<input type="hidden" name="Bank_NickName_<?php echo$i?>" id="Bank_NickName_<?php echo$i?>" value="<?php echo $getreg_det[$i]['Bank_NickName']; ?>">
							<input type="hidden" name="Bank_No_<?php echo$i?>" id="Bank_No_<?php echo$i?>" value="<?php echo $getreg_det[$i]['Bank_No']; ?>">
							<input type="hidden" name="bankaccno_<?php echo$i?>" id="bankaccno_<?php echo$i?>" value="<?php echo $getreg_det[$i]['bankaccno']; ?>">
							<input type="hidden" name="transaction_<?php echo$i?>" id="transaction_<?php echo$i?>" value="<?php echo $getreg_det[$i]['transaction']; ?>">
							<input type="hidden" name="subjectcode_<?php echo$i?>" id="subjectcode_<?php echo$i?>" value="<?php echo $getreg_det[$i]['subjectcode']; ?>">
							<input type="hidden" name="details_<?php echo$i?>" id="details_<?php echo$i?>" value="<?php echo $getreg_det[$i]['details']; ?>">
						</td>
						<td style="word-wrap: break-word; border-top: 1px dotted black;">
							<?php
							if($getreg_det[$i]['transaction'] != ""){
								if($getreg_det[$i]['Bank_NickName'] != ""){
									if (mb_strlen($getreg_det[$i]['Bank_NickName']."-".$getreg_det[$i]['bankaccno'], 'UTF-8') >= 12) {
										$str = mb_substr(ucwords($getreg_det[$i]['Bank_NickName']."-".$getreg_det[$i]['bankaccno']), 0, 11, 'UTF-8');
										?>
										<span title="<?php echo $getreg_det[$i]['Bank_NickName']."-".$getreg_det[$i]['bankaccno'] ?>"> <?php echo $str."..."; ?> </span>
										<?php
									} else {
										echo $getreg_det[$i]['Bank_NickName']."-".$getreg_det[$i]['bankaccno'];
									}
								} ?>
								<?php
							}else{
								if (mb_strlen($getreg_det[$i]['subject'], 'UTF-8') >= 12) {
										$str = mb_substr(ucwords($getreg_det[$i]['subject']), 0, 11, 'UTF-8');
										?>
										<span title="<?php echo $getreg_det[$i]['subject']; ?>"> <?php echo $str."..."; ?> </span>
										<?php
									} else {
										echo $getreg_det[$i]['subject'];
									}
							?>
							<input type="hidden" name="subject_<?php echo$i?>" id="subject_<?php echo$i?>" value="<?php echo $getreg_det[$i]['subject']; ?>">
							<?php } ?>
						</td>
						<td style="word-wrap: break-word; border-top: 1px dotted black;">
							<?php
							if($getreg_det[$i]['transaction'] != ""){
								if($getreg_det[$i]['transaction'] == 1){
									echo $debit_lab;
								}else{
									echo $credit_lab;
								}?>
							<input type="hidden" name="transaction_<?php echo$i?>" id="transaction_<?php echo$i?>" value="<?php echo $getreg_det[$i]['transaction']; ?>">
							<?php
							}else{
								echo $getreg_det[$i]['subsubject'];
							?>
							<input type="hidden" name="subsubject_<?php echo$i?>" id="subsubject_<?php echo$i?>" value="<?php echo $getreg_det[$i]['subsubject']; ?>">
							<?php } ?>
						</td>
						<td>
							@if($getreg_det[$i]['transaction'] != "")
								{{ Form::text('cash'.$i,(isset($getreg_det[$i]["amount"])) ? $getreg_det[$i]["amount"] : '',array('id'=>'cash'.$i, 
																		'name' => 'cash'.$i,
																		'maxlength' => '14',
																		'style'=>'text-align:right;padding-right:4px;',
																		'onkeypress' => 'return isNumberKey(event)',
																		'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
																		'class'=>'box99per form-control')) }}
							@else
								{{ Form::text('cash'.$i,'',array('id'=>'cash'.$i, 
																		'name' => 'cash'.$i,
																		'disabled' => 'disabled',
																		'onkeypress' => 'return isNumberKey(event)',
																		'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
																		'class'=>'box99per form-control')) }}
							@endif
						</td>
						<td>
							@if($getreg_det[$i]['transaction']=="")
								{{ Form::text('expenses'.$i,(isset($getreg_det[$i]["amount"])) ? $getreg_det[$i]["amount"] : '',array('id'=>'expenses'.$i, 
																		'name' => 'expenses'.$i,
																		'maxlength' => '14',
																		'style'=>'text-align:right;padding-right:4px;',
																		'onkeypress' => 'return isNumberKey(event)',
																		'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
																		'class'=>'box99per form-control')) }}
							@else
								{{ Form::text('expenses'.$i,'',array('id'=>'expenses'.$i, 
																		'name' => 'expenses'.$i,
																		'disabled' => 'disabled',
																		'onkeypress' => 'return isNumberKey(event)',
																		'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
																		'class'=>'box99per form-control')) }}
							@endif
						</td>
						<td>{{ Form::textarea('remarks'.$i,'', 
                        						array('name' => 'remarks'.$i,
                        							  'id' => 'remarks'.$i,
                              						  'class' => 'box99per form-control',
                              						  'size' => '20x1')) }}</td>
						</tr>
						{{ Form::hidden('count', $i, array('id' => 'count')) }}
			   		@endfor
			   	</tbody>
			</table>
		</div>
		<fieldset style="background-color: #DDF1FA;">
		<div class="form-group">
			<div align="center" class="mt5">
				<button type="submit" class="btn btn-success add box100 multiregprocess ml5">
						<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
					</button>
				<a onclick="javascript:gotomultoindex('index','{{$request->mainmenu}}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				</a>
			</div>
		</div>
	</fieldset>
	</div>
	{{ Form::close() }}
</article>
</div>
<div class="CMN_display_block pb10"></div>
{{ Form::open(array('name'=>'multiaddcancel', 'id'=>'multiaddcancel', 'url' => 'Expenses/multipleregprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
@endsection