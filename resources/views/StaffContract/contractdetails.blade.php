@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/staffContract.js') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
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

	{{ Form::open(array('name'=>'employeefrm','id'=>'employeefrm', 
			'url' => 'StaffContr/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
			'files'=>true,'method' => 'POST')) }}
	{{ Form::hidden('rid', '', array('id' => 'rid')) }}
	{{ Form::hidden('empnoadd', '', array('id' => 'empnoadd')) }}
	{{ Form::hidden('radio_emp', '', array('id' => 'radio_emp')) }}
	{{ Form::hidden('Name', '', array('id' => 'Name')) }}
	{{ Form::hidden('empid', '', array('id' => 'empid')) }}
	{{ Form::hidden('empname', '', array('id' => 'empname')) }}
	{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	{{ Form::hidden('pageclick', $request->pageclick , array('id' => 'pageclick')) }}
	{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="staff" class="DEC_flex_wrapper " data-category="staff staff_sub_2">
	<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-8 pm0">
			<img class="pull-left box35 mt10 ml10" src="{{ URL::asset('resources/assets/images/contractEmp.png') }}">
			<h2 class="pull-left pl5 mt15 CMN_mw150">{{ trans('messages.lbl_conEmployee') }}</h2>
		</div>
	</div>
	<div class="pb10"></div>
	<!-- End Heading -->
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
	<div class="pl5 pr5">
		<div class="pull-left ml5">
			<a href="javascript:goindexpage('{{ $request->mainmenu }}');" class="pageload btn btn-info box80"><span class="fa fa-arrow-left"></span> {{ trans('messages.lbl_back') }}</a>
		</div>
			<div class="pull-right mr5">
				<a href="javascript:contractdownload('{{ $emp_Id }}','{{ $emp_Name }}');" class="btn btn-primary box100 pull-right pr10"><span class="fa fa-download"></span> {{ trans('messages.lbl_download') }}</a>
			</div>
			<div class="pull-right mr10">
				<a href="javascript:contractemployeeadd('{{ $emp_Id }}','{{ $emp_Name }}');" 
				class="btn btn-success box80"><span class="fa fa-plus"></span> {{ trans('messages.lbl_add') }}</a>
			</div>
	<div class="col-xs-12 pl5 pr5">
	<fieldset>
		<div class="box100per mt5" >
			<div class="30per  CMN_display_block ml13">
			 	<label>{{ trans('messages.lbl_employeeid') }}:</label>
			</div>
			<div class="30per  CMN_display_block ml5 fwb blue">
				{{ $emp_Id }}
			</div>
			<div class="60per  CMN_display_block ml50">
			 	<label>{{ trans('messages.lbl_empName') }}:</label>
			</div>
			<div class="30per ml5 CMN_display_block black">
			 {{ $emp_Name }}
			</div>
		</div>
		@if(count($get_det) != 0 )
		<div class="minh340 mr10 ml10">
			<table class="tablealternate box100per">
				<colgroup>
				   <col width="4%">
				   <col width="14%">
				   <col width="10%">
				   <col width="8%">
				   <col width="9%">
				   <col width="9%">
				   <col width="6%">
				   <col width="">
				   <col>
				</colgroup>
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader fwb tac"> 
				  		<th class="tac" rowspan="2">{{ trans('messages.lbl_sno') }}</th>
				  		<th class="tac" colspan="2">{{ trans('messages.lbl_periodofWork') }}</th>
				  		<th class="tac" rowspan="2" title="Period Of Contract">
				  			{{ trans('messages.lbl_contract_Period') }}
				  		</th>
				  		<th class="tac" rowspan="2">{{ trans('messages.lbl_salary') }}</th>
				  		<th class="tac" rowspan="2">{{ trans('messages.lbl_Others') }}</th>
				  		<th class="tac" rowspan="2">{{ trans('messages.lbl_total') }}</th>
				  		<th class="tac" rowspan="2" title="Contract Date">
				  			{{ trans('messages.lbl_cdate') }}
				  		</th>
				  		<th class="tac" rowspan="2">{{ trans('messages.lbl_remarks') }}
				  		</th>
			   		</tr>
			   		<tr class="tableheader fwb tac"> 
			   			<th style="width: 50%;">{{ trans('messages.lbl_Start_date') }}</th> 
			   			<th style="width: 50%;">{{ trans('messages.lbl_enddate') }}</th>
			   		</tr>
			   	</thead>
			   	<tbody>
			   		@forelse($get_det as $count => $data)
			   		<tr>
			   			<td class="tac">
					 		{{ $count+1 }}
						</td>
						<td class="tac" colspan="2">
							{{--*/ $stdate_split = explode('-',$data->StartDate);
							   $endate_split = explode('-',$data->EndDate); 
							   $stdate_yr = $stdate_split[0];
							   $stdate_mon = $stdate_split[1];
							   $stdate_day = $stdate_split[2];
							   $stend_yr = $endate_split[0];
							   $endate_mnth = $endate_split[1];
							   $endate_day = $endate_split[2];
							   $Date = $stdate_yr."年".$stdate_mon."月".$stdate_day."日～"
							   		   .$stend_yr."年".$endate_mnth."月".$endate_day."日";/*--}}

							<a href="javascript:contractemployeeview('{{ $data->Emp_id }}',
																		'{{ $data->Id }}');" 
								class="blue">
							{{ $Date }}
							</a>
						</td>
						<td class="tac">
					 		{{--*/ $contractEnd = explode('-',$data->EndDate);
					 			   $contract_eyr = $contractEnd[0];
					 			   $contractStart =explode('-', $data->StartDate);
					 			   $contract_syr = $contractStart[0];
					 			   $difference = $contract_eyr-$contract_syr;
					 			   $diff =  $difference."年";/*--}}
					 		{{ $diff }}
						</td>
						<td class="tar">
					 		{{ $data->Salary }}
						</td>
						<td class="tar">
					 		{{--*/ $Others = $data->Allowance9+$data->Allowance10
										 +$data->Allowance1+$data->Allowance2
										 +$data->Allowance3+$data->Allowance4
										 +$data->Allowance5+$data->Allowance6
										 +$data->Allowance7+$data->Allowance8; /*--}}
					 		{{ $Others }}
						</td>
						<td class="tar">
							{{ $data->Total }}
						</td>
						<td class="tac">
					 		{{--*/ $stdate_split = explode('-',$data->Contract_date);
							   $stdate_yr = $stdate_split[0];
							   $stdate_mon = $stdate_split[1];
							   $stdate_day = $stdate_split[2];
							   $C_Date = $stdate_yr."年".$stdate_mon."月".$stdate_day."日";/*--}}
							   {{ $C_Date }}
						</td>
						<td class="tal">
					 		{{ $data->Remarks }}
						</td>
			   		</tr>
			   		@empty
					<tr>
						<td class="text-center" colspan="9" style="color: red;"> No Data Found</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>
		@else
		<div class="minh300" style="width: 900px;">
			<table class="box100" style="border:1px solid white;">
				<div class="fwb red mt20" style="text-align: center; margin-left: 200px">
					{{ trans('messages.lbl_nodatafound') }}
				</div>
			</table>
		</div>
		@endif
		<div class="pb10"></div>
	</fieldset>
	</div>
</div>
{{ Form::close() }}
</article>
</div>
<div class="CMN_display_block pb10"></div>
@endsection