@extends('layouts.app')
@section('content')
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
	$(document).ready(function() {
		setDatePicker("txt_startdate");
	});
	$(document).ready(function(){
		$("#bank option").each(function()
		{
			if ($(this).val() == "999") {
				$(this).css('font-weight','bold');
				$(this).css('color','brown');
			}
		});
	});
</script>
<style type="text/css">
	.ime_mode_disable {
		ime-mode:disabled;
	}
</style>
{{ HTML::script('resources/assets/js/salaryplus.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_10">
	{{ Form::open(array('name'=>'salarypayment', 'id'=>'salarypayment', 'url' => 'Salaryplus/multiaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('datemonth', '' , array('id' => 'datemonth')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
		{{ Form::hidden('multiflg',$request->multiflg , array('id' => 'multiflg')) }}
		{{ Form::hidden('fileCnt',$fileCnt , array('id' => 'fileCnt')) }}
	<!-- Start Heading -->
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/salary_1.png') }}">
			<h2 class="pull-left pl5 mt10">
					{{ trans('messages.lbl_salary') }}<span class="">・</span><span style="color:green">{{ trans('messages.lbl_payment') }} {{ trans('messages.lbl_register') }}</span>
			</h2>
		</div>
	</div>
	<div class="pb10"></div>
	<fieldset class="ml10 mr10">
		<div class="box100per  mt15" align="center">
					<label class="clr_blue">{{ trans('messages.lbl_Date') }}<span class="fr ml2 red"> * </span></label>
					{{ Form::text('txt_startdate','',array(
										'id'=>'txt_startdate',
										'name' => 'txt_startdate',
										'class'=>'box9per txt_startdate form-control ime_mode_disable',
										'style'=>'margin-left:12px;',
										'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
										'data-label' => trans('messages.lbl_saldate'),
										'maxlength' => '10')) }}
					<label class="mt10 ml2 fa fa-calendar fa-lg" for="txt_startdate" aria-hidden="true"></label>
					<label class="clr_blue ml25">{{ trans('messages.lbl_month') }}<span class="fr ml2 red"> * </span></label>
					
					{{ Form::selectRange('salarymonth', 1, 12, $request->selMonth, array('id' => 'salarymonth', 
																						'onchange' => 'paymentonchange(this.value)',
																						'class'=>'widthauto ime_mode_active',
																						'data-label' => trans('messages.lbl_salmonth'))) }}

					<label class="clr_blue ml25">{{ trans('messages.lbl_bank_name') }}<span class="fr ml2 red"> * </span></label>
					{{ Form::select('bank',[null=>''] + $bankname+['999'=>'Cash'],'',array(
										'id'=>'bank',
										'name' => 'bank',
										'class'=>'widthauto ime_mode_active',
										'style'=>'margin-left:12px;',
										'onchange' => 'javascript:fndisablechargefield();',
										'maxlength' => 10,
										'data-label' => trans('messages.lbl_bank'))) }}
		</div>
		<div class="col-xs-12 mt15">
			<table class="box98per CMN_tblfixed tablealternate ml10 mb10" id = "workspectable">
				<colgroup>
					<col width="4%">
					<col width="8%">
					<col>
					<col width="13%">
					<col width="11%">
					<!-- <col width="5%"> -->
				</colgroup>
				<thead class="CMN_tbltheadcolor">
					<tr class="">
						<th class="vam">{{ trans('messages.lbl_sno') }}</th>
						<th class="vam">{{ trans('messages.lbl_empid') }}</th>
						<th class="vam">{{ trans('messages.lbl_empName') }}</th>
						<th class="vam">{{ trans('messages.lbl_salary') }}</th>
						<th class="vam">{{ trans('messages.lbl_charge') }}</th>
						<!-- <th class="vam">{{ Form::checkbox('select_all',null,null, array('id'=>'select_all',
																				'class'=>'checkAll')) }}</th> -->
					</tr>
				</thead>
				<tbody>
				@if(!empty($fileCnt))
				@for ($i=0; $i<$fileCnt;$i++)
					<?php $workloop = "work_specific".$i; ?>
					<?php $quantityloop = "quantity".$i; ?>
					<?php $unit_priceloop = "unit_price".$i; ?>
					<?php $amountloop = "amount".$i; ?>
					<?php $remarksloop = "remarks".$i; ?>
					<tr>
						<td class="tac">
							{{ $i + 1 }}
						</td>
						<td class="tac">
							<a  style="text-decoration:none;color:blue;">
								{{ $get_multisalaryId[$i]['empNo'] }}</a>
							{{ Form::hidden('empNo_'.$i, $get_multisalaryId[$i]['empNo'] , array('id' => 'empNo_'.$i)) }}
						</td>
						<td>
							<a  style="text-decoration:none;color: black">
								{{ $get_multisalaryId[$i]['EmpName'] }}</a>
						</td>
						<td>
									{{ Form::text('salary'.$i,(isset($get_multisalaryId[$i]['Total'])) ? $get_multisalaryId[$i]['Total'] : '',array('id'=>'salary'.$i,
														'name' => 'salary'.$i,
														'data-label' => trans('messages.lbl_salary'),
										  				'style'=>'text-align:right;padding-right:4px;height:25px;',
														'onkeypress' => 'return isNumberKey(event)',
														'maxlength' => '15',
														'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
														'class'=>'box99per form-control pl5 mt3 ime_mode_disable')) }}
						</td>
						<td style="text-align: center;">
							{{ Form::text('charge'.$i,(isset($estimate[0]->$remarksloop)) ? $estimate[0]->$remarksloop : '',array('id'=>'charge'.$i,
														'name' => 'charge'.$i,
														'data-label' => trans('messages.lbl_charge'),
										  				'style'=>'text-align:right;padding-right:4px;height:25px;',
														'maxlength' => '15',
														'onkeypress' => 'return isNumberKey(event)',
														'onkeyup'=>'return fnMoneyFormat(this.id,"jp")',
														'class'=>'box99per form-control pl5 mt3 ime_mode_disable')) }}
						</td>
						<!-- <td class="tac">
							{{ Form::checkbox('check[]',null,null, array('id'=>'chk'.$i, 'class'=>'checkpayment')) }}
						</td> -->
					</tr>
				@endfor	
				@else 
					<tr>
						<td class="text-center colred" colspan="5">
							{{ trans('messages.lbl_nodatafound') }}
						</td>
					</tr>
				@endif
				</tbody>
			</table>
		</div>
		<div class="CMN_display_block pb10"></div>
	</fieldset>
	<fieldset class="ml10 mr10" style="background-color: #DDF1FA;">
		<div class="form-group">
			<div align="center" class="mt5">
				@if(!empty($fileCnt))
					<button type="submit" class="btn btn-success add box100 ml5 multiaddeditprocess">
						<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
					</button>
				@else
					<button type="button" class="btn btn-disable add box100 ml5">
						<i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
					</button>
				@endif
				<a onclick="javascript:gotoindexpage('index','{{ $request->mainmenu }}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				</a>
			</div>
		</div>
	</fieldset>
	<!-- End Heading -->
	</article>
	{{ Form::close() }}
</div>
{{ Form::open(array('name'=>'monthonchangefrm',
							'id'=>'monthonchangefrm',
							'url'=>'Salaryplus/multipaymentscreen?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
							'files'=>true,
							'method' => 'POST' )) }}
	{{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
	{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
	{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	{{ Form::hidden('multiflg',$request->multiflg , array('id' => 'multiflg')) }}
{{ Form::close() }}

 {{ Form::open(array('name'=>'salaryplusmulticancel', 'id'=>'salaryplusmulticancel', 'url' => 'Salaryplus/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
	{{ Form::hidden('datemonth', '' , array('id' => 'datemonth')) }}
	{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
	{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
	{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
	{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
	{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
	{{ Form::hidden('multiflg',$request->multiflg , array('id' => 'multiflg')) }}
	{{ Form::hidden('fileCnt',$fileCnt , array('id' => 'fileCnt')) }}
{{ Form::close() }}
@endsection