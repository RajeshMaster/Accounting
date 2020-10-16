@extends('layouts.app')
@section('content')
{{ HTML::style('resources/assets/css/common.css') }}
{{ HTML::style('resources/assets/css/widthbox.css') }}
{{ HTML::script('resources/assets/css/bootstrap.min.css') }}
{{ HTML::script('resources/assets/js/salary.js') }}
{{ HTML::style('resources/assets/css/sidebar-bootstrap.min.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
</script>
<style type="text/css">
	.sort_asc {
		background-image:url({{ URL::asset('resources/assets/images/upArrow.png') }}) !important;
	}
	.sort_desc {
		background-image:url({{ URL::asset('resources/assets/images/downArroW.png') }}) !important;
	}
</style>
	<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_5">
	{{ Form::open(array('name'=>'salaryviewlist', 'id'=>'salaryviewlist', 'url' => 'Salary/Viewlist?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('sortOptn',$request->salaryviewsort , array('id' => 'sortOptn')) }}
	    {{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
	    {{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
		{{ Form::hidden('id', $request->id , array('id' => 'id')) }}
		{{ Form::hidden('ids', $request->ids , array('id' => 'ids')) }}
	    {{ Form::hidden('empname', $request->empname , array('id' => 'empname')) }}
	    {{ Form::hidden('bankid', $request->bankid , array('id' => 'bankid')) }}
		{{ Form::hidden('editflg', '' , array('id' => 'editflg')) }}
		{{ Form::hidden('gobackflg', '' , array('id' => 'gobackflg')) }}
	<!-- Start Heading -->
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/salary_1.png') }}">
			<h2 class="pull-left pl5 mt10">{{ trans('messages.lbl_salhistory') }}</h2>
		</div>
	</div>
	<div class="col-xs-12 mt10">
			<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
				<a href="javascript:goviewtoindex('{{ $request->mainmenu }}');" class="btn btn-info box80"><span class="fa fa-arrow-left"></span> {{ trans('messages.lbl_back') }}</a>
				<a href="javascript:gotoaddsalary('{{ $request->mainmenu }}',1,'{{ $request->ids }}');" class="btn btn-success box100">
					<span class="fa fa-plus"></span>
					{{ trans('messages.lbl_add') }}
				</a>
			</div>
			<div class="form-group pm0 pull-right moveleft nodropdownsymbol" id="moveleft">
				{{ Form::select('salaryviewsort', [null=>''] + $salaryviewlistarray, $request->salaryviewsort,
									array('class' => 'form-control'.' ' .$request->sortstyle.' '.'CMN_sorting pull-right',
									'id' => 'salaryviewsort',
									'name' => 'salaryviewsort'))
				}}
			</div>
	</div>
	<div class="col-xs-12 mt5">
		<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
			<label class="clr_blue">{{ trans('messages.lbl_empid').':' }}</label>
			<span class="mr40" style="color:blue">
				<b>{{ $request->id }}</b> 
			</span>
			<label class="clr_blue">{{ trans('messages.lbl_empName').':' }}</label>
			<span style="color:#9C0000">
				<b>{{ $request->empname }}</b>
			</span>
		</div>
	</div>
	<div class="pt43 minh400 pl15 pr15">
		<table class="tablealternate CMN_tblfixed">
			<colgroup>
				<col width="4%">
				<col width="10%">
				<col width="10%">
				<col>
				<col width="11%">
				<col width="11%">
				<col width="5%">
			</colgroup>
			<thead class="CMN_tbltheadcolor">
				<tr>
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam">{{ trans('messages.lbl_saldate') }}</th>
					<th class="vam">{{ trans('messages.lbl_month') }}</th>
					<th class="vam">{{ trans('messages.lbl_bank_name') }}</th>
					<th class="vam">{{ trans('messages.lbl_salary') }}</th>
					<th class="vam">{{ trans('messages.lbl_charge') }}</th>
					<th class="vam"></th>
				</tr>
				@if(!empty($disp))
				<tr style="background-color:#DDDDDD;" class="boxhei25">
					<td class="tax_data_name"></td>
					<td class="tax_data_name"></td>
					<td class="tax_data_name"></td>
					<td class="tax_data_name"></td>
					<td class="tax_data_name tar blue CMN_boldText pr5">
						<?php if ( $saltotal != "" ) {
							echo number_format($saltotal);
						}?>
					</td>
					<td class="tax_data_name tar blue CMN_boldText pr5">
						<?php if ($chartotal != "") {
							echo number_format($chartotal);
						}?>
					</td>
					<td class="tax_data_name"></td>
				</tr>
				@endif
			</thead>
			<tbody>
				@if(!empty($salaryviewlist))
					@for($i=0; $i<$disp;$i++)
					<tr>
						<td class="tac">
							{{ $i +1 }}
						</td>
						<td class="tac">
							@if(isset($salaryviewlist[$i]['salaryDate']))
								{{ $salaryviewlist[$i]['salaryDate'] }}
							@endif
						</td>
						<td class="tac">
							@if(isset($salaryviewlist[$i]['salaryMonth']))
								{{ $salaryviewlist[$i]['salaryMonth'] }}
							@endif
						</td>
						<td class="tal pl10">
							@if($salaryviewlist[$i]['bankId'] != "999")
								{{ $salaryviewlist[$i]['bankname'] }}-{{ $salaryviewlist[$i]['accountNo'] }}
							@else
							    Cash
							@endif
						</td>
						<td class="tar pr5">
							<a href="javascript:gotosingviewpage('{{ $salaryviewlist[$i]['id'] }}','{{ $request->mainmenu }}','{{ $request->empname }}','1');" class="anchorstyle">
								{{ $salaryviewlist[$i]['salary'] }}
							</a>
						</td>
						<td class="tar pr5">
							@if(isset($salaryviewlist[$i]['charge']))
								{{ $salaryviewlist[$i]['charge'] }}
							@endif
						</td>
						<td class="tac" title="Copy">
							<a href="javascript:gotocopysingles('{{ $salaryviewlist[$i]['id'] }}','{{ $request->empname }}','{{ $salaryviewlist[$i]['salary'] }}','{{ $request->mainmenu }}','3','{{ $salaryviewlist[$i]['bankId'] }}');" class="anchorstyle">
								<img class="box19" src="{{ URL::asset('resources/assets/images/copy.png') }}">
							</a>
						</td>
					</tr>
					@endfor
				@else 
					<tr>
						<td class="text-center colred" colspan="7">
							{{ trans('messages.lbl_nodatafound') }}
						</td>
					</tr>
				@endif
			</tbody>
		</table>
	</div>
	<div class="text-center pl13">
		@if(!empty($index->total()))
			<span class="pull-left mt24">
				{{ $index->firstItem() }} ~ {{ $index->lastItem() }} / {{ $index->total() }}
			</span>
		@endif 
		{{ $index->links() }}
		<div class="CMN_display_block flr mr18">
			{{ $index->linkspagelimit() }}
		</div>
	</div>
	</article>
	{{ Form::close() }}
	</div>
@endsection
