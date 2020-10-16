@extends('layouts.app')
@section('content')
<?php use App\Http\Helpers; ?>
{{ HTML::script('resources/assets/js/timesheet.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
<script type="text/javascript" >
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
function pageClick(pageval) {
    $('#page').val(pageval);
    $("#timesheetfrm").submit();
}
function pageLimitClick(pagelimitval) {
	$('#page').val('');
	$('#plimit').val(pagelimitval);
	$("#timesheetfrm").submit();
}
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
<div class="CMN_display_block" id="main_contents" >
<!-- article to select the main&sub menu -->
<article id="staff" class="DEC_flex_wrapper " data-category="staff staff_sub_3">
{{ Form::open(array('name'=>'timesheetfrm', 
						'id'=>'timesheetfrm', 
						'url' => 'Timesheet/timesheetindex?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('plimit', $request->plimit, array('id' => 'plimit')) }}
		{{ Form::hidden('pageclick', $request->pageclick, array('id' => 'pageclick')) }}
		{{ Form::hidden('pagenxt', $request->pagenxt , array('id' => 'pagenxt')) }}
		{{ Form::hidden('plimitnxt', $request->plimitnxt, array('id' => 'plimitnxt')) }}
		{{ Form::hidden('pagecnt', $request->pagecnt, array('id' => 'pagecnt')) }}
		{{ Form::hidden('lastsortvalue', $request->lastsortvalue, array('id' => 'lastsortvalue')) }}
		{{ Form::hidden('lastordervalue', $request->lastordervalue, array('id' => 'lastordervalue')) }}
		{{ Form::hidden('ordervalue', $request->ordervalue, array('id' => 'ordervalue')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
	 	{{ Form::hidden('empid', '' , array('id' => 'empid')) }}
		{{ Form::hidden('flag', $request->flag, array('id' => 'flag')) }}
		{{ Form::hidden('hdnback', '1' , array('id' => 'hdnback')) }}
	<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" 
				src="{{ URL::asset('resources/assets/images/timesheet.jpg') }}">
			<h2 class="pull-left pl5 mt10">{{ trans('messages.lbl_timesheetentrylist') }}</h2>
		</div>
	</div>
	<div class="pb10"></div>
	<!-- End Heading -->
	
	<div class="box100per pr10 pl10">
		<div class="">
			{{ Helpers::displayYear_Monthtimesheet($account_period, $year_month, $db_year_month, $date_month, $dbnext, $dbprevious, $last_year, $current_year, $account_val) }}
		</div>
	</div>
	
	<!-- End Heading -->
	<div class="col-xs-12 pm0 pull-left">
			<!-- Session msg -->
		@if(Session::has('success'))
			<div align="center" class="alertboxalign mt5" role="alert">
				<p class="alert {{ Session::get('alert', Session::get('type') ) }}">
	            {{ Session::get('success') }}
	          	</p>
			</div>
		@endif
		@php Session::forget('success'); @endphp
		<!-- Session msg -->
		<div class="col-xs-12 pm0 pull-left mb10 pl10">
            <a href="javascript:staffpopup('{{ $request->mainmenu }}');" class="pull-right pr10 mt10 anchorstyle"></span>{{ trans('messages.lbl_import') }}</a>
		</div>
	</div>
	<div>
		<div class="box100per pl10 pr10" style="min-height: 333px;">
			<table class="tablealternate box100per" style="table-layout: fixed;">
				<colgroup>
					<col width="4%">
					<col width="8%">
					<col width="">
					<col width="">
					<col width="6%">
					<?php
					foreach ($titleArray as $key => $vu) { 
					if ($titleArray[$key] != "") { ?>
					<col width="4%">
					<?php } } ?>
					<col width="5%">
					<col width="5%">
					<col width="8%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
					<tr>
						<th class="vam">{{ trans('messages.lbl_sno') }}</th>
						<th class="vam">{{ trans('messages.lbl_Date') }}</th>
						<th class="vam">{{ trans('messages.lbl_name') }}</th>
						<th class="vam">{{ trans('messages.lbl_companyname') }}</th>
						<th class="vam">
							<span title = "{{ trans('messages.lbl_workhrs') }}">
 							{{ mb_substr(trans('messages.lbl_workhrs'),0,2,'utf-8') }}
						 	</span>
						</th>
						<?php
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
						<?php } } ?>
						<th class="vam">
							<span title = "{{ trans('messages.lbl_extradays') }}">
								{{ mb_substr(trans('messages.lbl_extradays'),0,2,'utf-8') }}
							</span>
						</th>
						<th class="vam" title="">{{ trans('messages.lbl_file') }}</th>
						<th class="vam">{{ trans('messages.lbl_submit') }}</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($displayArray)) { ?>
						<tr>
							<td colspan=8 class="tac red">{{ trans('messages.lbl_nodatafound') }}</td>
						</tr>
					<?php }?>
					<?php
						for ($j = 0; $j < count($displayArray); $j++) {
							if ($j%2 != 0) {
								$style = "background-color:#dff1f4;";
							} else {
								$style = "background-color:#FFFFFF;";
							}
					?>
	              	<tr>
	                   	<td class="tac">
	                   		{{ ($res->currentpage()-1) * $res->perpage() + $j + 1 }}
	                   	</td>
	                   	<td class="tac">
	                   		<?php  if ($displayArray[$j]['Emp_ID'] != "") { ?>
	                   		<a href="javascript:viewTS_entry('<?php echo $displayArray[$j]['Emp_ID'];?>',{{1}})" id="linkcolor" class="btn-link" style="color:blue;">
	                   		<?php if ($displayArray[$j]['CREATEDDATE'] !='') {
	                   			 echo substr($displayArray[$j]['CREATEDDATE'],5,2)."月".substr($displayArray[$j]['CREATEDDATE'],8,2)."日"; ?>
	                   	   	 <?php } else {
	                   	   	   echo "-" ;
	                   	   	} ?>
	                   	   	</a>
	                   	   	<?php } else { 
								echo "-";
							}  ?>
	                   	</td>
	                   <td @if(strlen($displayArray[$j]['LastName']) > 16))
			              title="{{ empnamelength($displayArray[$j]['LastName'], $displayArray[$j]['FirstName'], 120) }}" @endif>
			              <a href="javascript:Byidview('<?php echo $displayArray[$j]['Emp_ID'];?>');">{{ empnamelength($displayArray[$j]['LastName'], $displayArray[$j]['FirstName'], 18) }}</a>
	                   	</td>
	                   	<td>
	                   		@if ($displayArray[$j]['workplace'] !='')
	                   			<span class="tal"><?php echo $displayArray[$j]['workplace']; ?>
	                   			</span>
	                   		@endif
	                   	</td>
	                   	<td class="tac">
	                   		<?php if ($displayArray[$j]['workinghours'] !='0:00')  { 
	                   			$timeSplit = explode(":", $displayArray[$j]['workinghours']);
	                   			if ( ($timeSplit[0] > 199 && ($timeSplit[1] > 0 || $timeSplit[1] == 0)) && $displayArray[$j]['workinghours'] != "200:00") { ?>
	                   				<span style="color:brown;font-weight:bold;font-size:11.5px;">
										<?php echo $displayArray[$j]['workinghours'];?>
									</span>
	                   		<?php } else { 
	                   			echo $displayArray[$j]['workinghours'];
	                   		} } else { 
	                   			echo "-";
	                   		} ?>
	                   	</td>
	                   	<?php foreach ($titleArray as $key => $vu) { 
	                   			if ($titleArray[$key] != "") { ?> 
								<td class="tac"><?php if ($displayArray[$j]['section'][$key] != '0') { 
										echo $displayArray[$j]['section'][$key];
									} else {
										echo "-";
									} ?>
								</td>
						<?php } } ?>

	                    <td class="tac">
	                    	<?php 
								if ($displayArray[$j]['extradays'] != '0') { 
									echo $displayArray[$j]['extradays'];
								} else {
									echo "-";
								}
							?>
	                   	</td>
	                   	<td class="tac box35">
	                   		<?php 
								if ($displayArray[$j]['upload_path'] != "") {
									echo "<a href='javascript:void(0);'> <img src='../resources/assets/images/xls.png' class = 'editImg box20'>
									</img> </a>";
								} else {
									echo "-";
								}
							?>
	                   	</td>
	                   	<td class="tac">
	                   		<?php 
								if ($displayArray[$j]['submit'] != '' && $displayArray[$j]["submit"] != "0000-00-00") { ?>
									<a href="javascript:void(0);" style="text-decoration: none;color:GREEN;"?><b>
										<?php echo $displayArray[$j]['submit'];?></b>
									</a>
								<?php } else {
									 if ($displayArray[$j]['workinghours'] == '0:00') {  ?>
									<span style="color:gray;font-weight:normal"><b>SUBMIT</b></span>
								<?php } else { ?>
									<span>
									<a href="javascript:void(0);" class="btn btn-primary box70" style="padding:1px;"><span class=""></span> {{ trans('messages.lbl_submit') }}</a>
									</span>
								<?php } } ?>
	                   	</td>
	                </tr>
					<?php  } ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="text-center pl13">
		@if(!empty($res->total()))
			<span class="pull-left mt24">
				{{ $res->firstItem() }} ~ {{ $res->lastItem() }} / {{ $res->total() }}
			</span>
		@endif 
		{{ $res->links() }}
        <div class="CMN_display_block flr mr10">
		{{ $res->linkspagelimit() }}
        </div>
	</div>
	{{ form::close() }}
</article>
</div>
<div id="importstaffpopup" class="modal fade">
    <div id="login-overlay">
        <div class="modal-content">
            <!-- Popup will be loaded here -->
        </div>
    </div>
</div>
@endsection