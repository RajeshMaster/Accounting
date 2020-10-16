@extends('layouts.app')
@section('content')
{{ HTML::style('resources/assets/css/common.css') }}
{{ HTML::style('resources/assets/css/widthbox.css') }}
{{ HTML::script('resources/assets/css/bootstrap.min.css') }}
{{ HTML::script('resources/assets/js/timesheet.js') }}
{{ HTML::style('resources/assets/css/sidebar-bootstrap.min.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
<style type="text/css">
	.sort_asc {
		background-image:url({{ URL::asset('resources/assets/images/upArrow.png') }}) !important;
	}
	.sort_desc {
		background-image:url({{ URL::asset('resources/assets/images/downArroW.png') }}) !important;
	}
</style>
<div class="CMN_display_block" id="main_contents" >
<!-- article to select the main&sub menu -->
<article id="staff" class="DEC_flex_wrapper " data-category="staff staff_sub_3">
{{ Form::open(array('name'=>'timesheetdetails', 
						'id'=>'timesheetdetails', 
						'url' => 'Timesheet/timeSheetHistorydetails?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('plimit', $request->plimit, array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('empid', $request->empid , array('id' => 'viewid')) }}
		{{ Form::hidden('selMonth', $request->selMonth , array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear , array('id' => 'selYear')) }}
		{{ Form::hidden('flagval', $request->flagval , array('id' => 'flagval')) }}
		{{ Form::hidden('pagenxt', $request->pagenxt , array('id' => 'pagenxt')) }}
		{{ Form::hidden('plimitnxt', $request->plimitnxt, array('id' => 'plimitnxt')) }}
		{{ Form::hidden('hdnback', $request->hdnback , array('id' => 'hdnback')) }}
		{{ Form::hidden('sortOptn',$request->salaryviewsort , array('id' => 'sortOptn')) }}
	    {{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}

	<!-- Start Heading -->
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt15" 
				src="{{ URL::asset('resources/assets/images/timesheet.jpg') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_timesheethistory') }}</h2>
		</div>
	</div>
	<div class="col-xs-12 pm0 pull-left pl10" style="padding-top: 10px !important;">
		<div class="pull-left ml2 mb10 pr10 ml10">
				<a href="javascript:goindexpage();" class="btn btn-info box80"><span class="fa fa-arrow-left"></span> {{ trans('messages.lbl_back') }}</a>
		@if(!empty($count))
			<button type="button" id="staffpopup"
					class="fa fa-edit pull-right pageload btn btn-warning box90 ml10"  
                    onclick="return gotoview('{{ $request->mainmenu }}',
                    						'{{ $request->empid }}' ,'{{ 2 }}');">
                    {{ trans('messages.lbl_updatetimesheet') }}
            </button>
         @else
         	<button type="button" id="staffpopup" 
					class="pull-right pageload btn btn-success box86 ml10"  
				 	style ="color:white;background-color: hsl(120, 39%, 54%)" 
                    onclick="return gotoview('{{ $request->mainmenu }}',
                    						'{{ $request->empid }}' ,'{{ 1 }}');">
                    <span class="fa fa-plus"></span>
                    {{ trans('messages.lbl_register') }}
             </button>
        @endif
		</div>
	</div>
	<div class="col-xs-12 pull-left">
		@if($disp!=0)
		<div class="col-xs-8 mt2 pm0">
			<div class="30per  CMN_display_block">
			 	<label>{{ trans('messages.lbl_employeeid') }}:</label>
			</div>
			<div class="30per  CMN_display_block ml5 fwb blue">
				{{ $value[0][0] }}
			</div>
			<div class="60per  CMN_display_block ml50">
			 	<label>{{ trans('messages.lbl_empName') }}:</label>
			</div>
			@php 
				$name = $empdet[0]->LastName.".".ucwords(mb_substr($empdet[0]->FirstName,0,1,'UTF-8'));
			@endphp
			<div class="30per ml5 CMN_display_block fwb black">
			 {{ $name }}
			</div>
		</div>
		<div class="pull-right moveleft nodropdownsymbol pb5" id="moveleft">
				{{ Form::select('timesheetviewsort', [null=>'']+$timesheetviewlistarray, $request->timesheetviewsort,
									array('class' => 'form-control'.' ' .$request->sortstyle.' '.'CMN_sorting pull-right',
									'id' => 'timesheetviewsort',
									'name' => 'timesheetviewsort'))
				}}
			</div>
		@endif
	</div>
	<div>
		<div class="minh400 box100per pl10 pr10">
			<table class="tablealternate box100per" style="table-layout: fixed;">
				<colgroup>
					<col width="4%">
					<col width="8%">
					<col width="">
					<col width="10%">
					<?php if (isset($titleArray)) {
							foreach ($titleArray as $key => $vu) { 
								if ($titleArray[$key] != "") {  ?>
					<col width="4%">
					<?php } } } ?>
					<col width="8%">
					<col width="8%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
					<tr>
						<th class="vam">{{ trans('messages.lbl_sno') }}</th>
						<th class="vam">{{ trans('messages.lbl_Date') }}</th>
						<th class="vam">{{ trans('messages.lbl_companyname') }}</th>
						<th class="vam">
							<span title = "{{ trans('messages.lbl_workhrs') }}">
						 	{{ mb_substr(trans('messages.lbl_workhrs'),0,2,'utf-8') }}
						 	</span>
						</th>
						<?php  if (isset($titleArray)) {
							foreach ($titleArray as $key => $vu) { 
								if ($titleArray[$key] != "") { ?>
									<th class="vam">
										<span title = "<?php echo $titleArray[$key]; ?>">
											<?php if (mb_strlen($titleArray[$key], 'UTF-8') > 3) { 
												$str = mb_substr($titleArray[$key], 0, 3, 'UTF-8');
												echo $str.".."; 
											} else {
												echo $titleArray[$key];
											} ?>
										</span>
									</th>
						<?php } } } ?>
						<th class="vam">
							<span title = "{{ trans('messages.lbl_extradays') }}">
								{{ mb_substr(trans('messages.lbl_extradays'),0,2,'utf-8') }}
							</span>
						</th>
						<th class="vam">{{ trans('messages.lbl_edit') }}</th>
					</tr>
				</thead>
				<tbody>
				@if($disp!=0)
				<?php for ($i=0; $i <$fncount ; $i++) { ?>
	              	<tr>
	                   	<td class="tac">
	                   		{{ $i+1 }}
	                   	</td>
	                   	<td class="tac" style="color:#0000FF">
	                   	 <a href="javascript:gototimesheetview('{{ $value[$i][0] }}','{{ $value[$i]['date'] }}','{{ $value[$i]['workyear'] }}','{{ $value[$i]['workmonth'] }}','{{ $request->mainmenu }}')" 
	                   	 style="color:blue;" class="btn-link">
	                   	 	{{ $value[$i][1] }}
	                   	 </a>

	                   	</td>
	                   	<td class="tal">
	                   	@if(isset($value[$i][2]))
	                   	{{ $value[$i][2] }}
	                   	@else 
	                   	{{ "-" }}
	                   	@endif
	                   	</td>
	                   	<td class="tac">
	                   	{{ $value[$i][3] }}

	                   	</td>
	                   	<?php if (isset($titleArray)) {
	                   			foreach ($titleArray as $key => $vu) { 
	                   				if ($titleArray[$key] != "") { ?> 
										<td class="tac">
											<?php 
												if($array[$key]!='0'){ 
													echo $array[$key];
												} else {
													echo "-";
												}?>
						<?php } } } ?>

	                    <td class="tac">
	                    	<?php if($value[$i][7]!='0'){ 
	                    			echo $value[$i][7];
	                    		} else {
	                    			echo "-";
	                    		}?>
	                   	</td>
	                   	<td class="tac box35">
	                   		<a href="#"><img src='../resources/assets/images/edit.png' 
	                   		class = 'editImg box20 btn-link' 
	                   		onclick="return addeditreg('{{ $request->mainmenu }}',
                    						'{{ $request->empid }}' ,'{{ 2 }}');"> </img></a>
							
	                   	</td>
	                </tr>
	            <?php } ?>
	            @else
	            <tr>
					<td class="text-center colred"  colspan="6">
							{{ trans('messages.lbl_nodatafound') }}
					</td>
				</tr>
				@endif
				</tbody>
			</table>
		</div>
	</div>
	@if($disp!=0)
	<div class="text-center pl13">
		@if(!empty($gettimesheetdetails->total()))
			<span class="pull-left mt24">
				{{ $gettimesheetdetails->firstItem() }} ~ {{ $gettimesheetdetails->lastItem() }} / {{ $gettimesheetdetails->total() }}
			</span>
		@endif 
		{{ $gettimesheetdetails->links() }}
        <div class="CMN_display_block flr mr10">
		{{ $gettimesheetdetails->linkspagelimit() }}
		</div>
	</div>
	@endif
	{{form::close() }}
</article>
</div>
@endsection