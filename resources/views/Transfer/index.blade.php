@extends('layouts.app')
@section('content')
@php use App\Http\Helpers; @endphp
{{ HTML::script('resources/assets/js/transfer.js') }}
{{ HTML::script('resources/assets/js/Setting.js') }}
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::style('resources/assets/css/switch.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
</script>
<?php $request->selMonth = date('m');
	  $request->selYear = date('Y'); ?>
<style type="text/css">
  .scrollbar
  {
    float: left;
    max-height: 485px;
    width: 270px;
    overflow-x: hidden !important;
    overflow-y: scroll !important;
    /*margin-bottom: 5px;*/
  }
	li.dropdown ul {
		display : none;
	}
	.alertboxalign {
		margin-bottom: -50px !important;
	}
	.alert {
		display:inline-block !important;
		height:30px !important;
		padding:5px !important;
	}
	.btns {
	    display: inline-block;
	    padding: 0px 12px;
	    height: 25px;
	    padding-top: 2px;
	    margin-bottom: 0;
	    font-size: 14px;
	    font-weight: 400;
	    line-height: 1.42857143;
	    text-align: center;
	    white-space: nowrap;
	    vertical-align: middle;
	    -ms-touch-action: manipulation;
	    touch-action: manipulation;
	    cursor: pointer;
	    -webkit-user-select: none;
	    -moz-user-select: none;
	    -ms-user-select: none;
	    user-select: none;
	    background-image: none;
	    border: 1px solid transparent;
	    border-radius: 4px;
	}
</style>
	<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	{{ Form::open(array('name'=>'transferindex', 'id'=>'transferindex', 'url' => 'Transfer/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('hiddenplimit', '' , array('id' => 'hiddenplimit')) }}
	    {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	    {{ Form::hidden('hiddenpage', '' , array('id' => 'hiddenpage')) }}
	    {{ Form::hidden('year', '' , array('id' => 'year')) }}
	    {{ Form::hidden('month', '' , array('id' => 'month')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('editflg', $request->editflg , array('id' => 'editflg')) }}
		{{ Form::hidden('id','', array('id' => 'id')) }}
	    {{ Form::hidden('mulid','', array('id' => 'mulid')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
	    {{ Form::hidden('subject','', array('id' => 'subject')) }}
	    {{ Form::hidden('bname','', array('id' => 'bname')) }}
	    {{ Form::hidden('accNo','', array('id' => 'accNo')) }}
	    {{ Form::hidden('empid','', array('id' => 'empid')) }}
	    {{ Form::hidden('empname','', array('id' => 'empname')) }}
	    {{ Form::hidden('loan_flg','', array('id' => 'loan_flg')) }}
	    {{ Form::hidden('salaryflg','', array('id' => 'salaryflg')) }}
	    {{ Form::hidden('flgs','', array('id' => 'flgs')) }}
	    {{ Form::hidden('exptype1','', array('id' => 'exptype1')) }}
		{{ Form::hidden('dateflg', $request->dateflg, array('id' => 'dateflg')) }}
	    {{ Form::hidden('backflg','', array('id' => 'backflg')) }}
	    {{ Form::hidden('subcat','', array('id' => 'subcat')) }}
	    {{ Form::hidden('expcopyflg','', array('id' => 'expcopyflg')) }}
	    {{ Form::hidden('bankNamen','', array('id' => 'bankNamen')) }}
	    {{ Form::hidden('sub_type','', array('id' => 'sub_type')) }}
	    {{ Form::hidden('trans_flg','', array('id' => 'trans_flg')) }}
	    {{ Form::hidden('tra_flg','', array('id' => 'tra_flg')) }}
	    {{ Form::hidden('delflg','', array('id' => 'delflg')) }}
	    {{ Form::hidden('cashflg', '' , array('id' => 'cashflg')) }}
     	{{ Form::hidden('registration','', array('id' => 'registration')) }}
     	{{ Form::hidden('viewid', '', array('id' => 'viewid')) }}
		{{ Form::hidden('editid', $request->editid , array('id' => 'editid')) }}
	<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_2">
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/expenses_icon.png') }}">
			<h2 class="pull-left pl5 mt10 CMN_mw150">
					{{ trans('messages.lbl_transfer') }}
			</h2>
		</div>
	</div>
	<div class="box100per pl15 pr15">
		<div class="mt10 mb10">
			{{ Helpers::displayYear_MonthEst($account_period, $year_month, $db_year_month, $date_month, $dbnext, $dbprevious, $last_year, $current_year, $account_val) }}
		</div>
	</div>
	<div class="col-xs-12">
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
			<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
				<a href="javascript:gotoadd('{{ $request->mainmenu }}',1);" class="btn btn-success box20per">
					<span class="fa fa-plus"></span>
					{{ trans('messages.lbl_register') }}
				</a>
			</div>
			@if($disp > 0)
			<div class="form-group pm0 pull-right moveleft nodropdownsymbol" id="moveleft">
				<div style="display: inline-block;" class="pull-right">
					@if($getbktr_det[0]['submit_flg'] == 1 && Session::get('userclassification') != 4)
						<a class="btn btn-gray" title="Multiple Register" style="color: white;">
							<i class="fa fa-plus" aria-hidden="true" style="color: white;"></i>
							{{ trans('messages.lbl_multi_register') }}
						</a>
					@else
						<a href="javascript:multi_view('{{ $disp }}')" class="btn btn-success" title="Multiple Register">
							<i class="fa fa-plus" aria-hidden="true"></i>
								{{ trans('messages.lbl_multi_register') }}
						</a>
					@endif
				</div>
				@if($getbktr_det[0]['submit_flg'] == 1 && Session::get('userclassification') != 4)
					<div style="display: inline-block;" class="mr10 pull-right">
						<a title="Edit" class="btn btn-gray" style="color: white;">
							<i class="fa fa-pencil-square-o" aria-hidden="true" style="color: white;"></i>
							{{ trans('messages.lbl_edit') }}
						</a>
					</div>
				@else
					<div style="display: inline-block;" class="mr10 pull-right">
						<a href="javascript:edit_view('{{ $disp }}');" class="btn btn-warning box100per">
							<span class="fa fa-pencil"></span>
								{{ trans('messages.lbl_edit') }}
						</a>
					</div>
					<div style="display: inline-block;" class="mr10 pull-right">
						<a href="javascript:transferexceldownload('{{ $request->mainmenu }}', '{{ $date_month }}');"  class="btn btn-primary box125"><span class="fa fa-download"></span> {{ trans('messages.lbl_download') }}</a>
					</div>
				@endif
			</div>
			@endif
	</div>
	<!-- SEARCH -->
             <div style="top: 134px;position: fixed;" @if ($request->searchmethod == 1 || $request->searchmethod == 2) 
                     class="open CMN_fixed pm0" 
                   @else 
                     class="CMN_fixed pm0 pr0" 
                   @endif 
                    id="styleSelector">
             <div class="selector-toggle">
              <a id="sidedesignselector" href="javascript:void(0)"></a>
          </div>
          		<div class="scrollbar">
            		<div style="background-color:#136E83;color: white;">
            		<ul class="ml5">	
              			<span>
                  			<li class="">
                      			<label class="mt10">{{ Helpers::ordinalize($account_val) }} Period</label>
                  			</li>
                  			<li>
                  				<span>Expenses Total Amount</span>
                  			</li>
                  			<li class="mb10">
                  				<label class="pull-right pr10" style="font-size:18px;">
                  					@if($expamt[0] > 0)
                  						¥{{number_format($expamt[0])}}
                  					@else
                  						¥ 0
                  					@endif
                  				</label>
                  			</li>
                  			<li>
                  				<label class="pull-right pr10">   </label>
                  			</li>
              			</span>
                			<li class="theme-option ml6">
                  				<div class="box100per mt5">
                  				<div>
                			</li>
            		</ul>
            		<ul>
            		</ul>
            		</div>
            		<div class="hline">
            			<ul>
            				<li class="mt5 ml5">
            					<a href="javascript:transfer_download('{{ $request->mainmenu }}');"><i class="fa fa-arrow-down" aria-hidden="true"></i>Transfer Download</a>
            				</li>
            			</ul>
            		</div>
            		<div class="hline">
            			<ul>
            				<li class="mt10 ml5">
            					<label><i class="fa fa-cogs" aria-hidden="true"></i> Settings</label>
            				</li>
            				<li class="ml35">
            						@php
			        				$tbl_name = 'dev_expensesetting'
			        				@endphp
            					<a class="mt20 btn-link" style="color:blue;" 
	        						href="javascript:settingpopupsinglefield('twotextpopup',
	        						'{{ $tbl_name}}')" onclick="fnselecttoggleclose();">
	        						{{ trans('messages.lbl_mainsubject') }}
	        					</a>
            				</li>
            				<li class="ml35">
            					{{--*/ $tbl_name = 'inv_set_expensesub' /*--}}
								{{--*/ $tbl_select = 'dev_expensesetting' /*--}}
	        					<a class="mt20 btn-link" style="color:blue;" 
	        						href="javascript:settingpopupsinglefield('selectthreefieldDatas',
	        						'{{ $tbl_name }}','{{ 2 }}','{{ $tbl_select }}','{{ 2 }}');" onclick="fnselecttoggleclose();">
	        						{{ trans('messages.lbl_subsubject') }}
	        					</a>
            				</li>
            			</ul>
            			<div id="showpopup" class="modal fade">
						    <div id="login-overlay">
						        <div class="modal-content">
						            <!-- Popup will be loaded here -->
						        </div>
						    </div>
						</div>
            		</div>
            	<div>
            			<ul class="mt15 ml5">
            				<?php for ($mn = 0; $mn <count($mainCatDetails); $mn++) { 
            						if($request->bname == $mainCatDetails[$mn]['mainCat']) {
								 		 $fntColor = "style='color:green; font-weight: bold;'";
								 	} else {
			    					 	 $fntColor = "style='color:blue;'";
									}
            					?>
   							<li class="dropdown"><i class="fa fa-plus" aria-hidden="true"></i>
   									<span class="ml5"> <a href = "javascript:gotomainexpenseshistory('{{ $mainCatDetails[$mn]['id'] }}','{{ $mainCatDetails[$mn]['mainCat'] }}','{{ $request->mainmenu }}',4);" @if(strlen($mainCatDetails[$mn]['mainCat']) > 15) 
	    										title="{{ $mainCatDetails[$mn]['mainCat'] }}"
			    										@endif <?php echo $fntColor;?>>
   									@if(singlefieldlength($mainCatDetails[$mn]['mainCat'],15))
			            				{{singlefieldlength($mainCatDetails[$mn]['mainCat'],15)}}
			            			@else
			            				{{$mainCatDetails[$mn]['mainCat'] }}
			            			@endif
   									</a> </span>
      							<?php $subcount = 0;
								if (isset($subCatDetails[$mainCatDetails[$mn]['mainCat']])) {
								 	$subcount = count($subCatDetails[$mainCatDetails[$mn]['mainCat']]);
								 }
								for ($sb = 0; $sb <$subcount; $sb++) { 
									if($request->subCat == $subCatDetails[$mainCatDetails[$mn]['mainCat']][$sb]['subCat']) {
								 		 $fntColor = "style='color:green; font-weight: bold;'";
								 	} else {
			    					 	 $fntColor = "style='color:blue;";
									}
									?> 
      							<ul class="ml25">
						           <li><i class="fa fa-minus" aria-hidden="true"></i>
						          		 <a href = "javascript:gotosubexphistory('{{ $mainCatDetails[$mn]['id'] }}','{{ $subCatDetails[$mainCatDetails[$mn]['mainCat']][$sb]['subId'] }}','{{ $subCatDetails[$mainCatDetails[$mn]['mainCat']][$sb]['subCat'] }}','{{ $mainCatDetails[$mn]['mainCat'] }}','{{ $request->mainmenu }}');">{{$subCatDetails[$mainCatDetails[$mn]['mainCat']][$sb]['subCat']}}</a>
						           </li>
      							</ul>
   							<?php } ?>
   							</li>
   							<?php } ?>
						</ul>
            		</div>
            	</div>
            </div>
        <!-- END SEARCH -->
	<div class="minh200 pl15 pr15 mt50">
		<table class="tablealternate CMN_tblfixed">
			<colgroup>
				<col width="4%">
				<col width="5%">
				<col width="12%">
				<col width="12%">
				<col width="12%">
				<!-- <col width="12%"> -->
				<col width="8%">
				<col width="8%">
				<col width="8%">
				<col width="8%">
				<col width="8%">
				<col width="8%">
				<col width="5%">
				<col width="5%">
			</colgroup>
			<thead class="CMN_tbltheadcolor">
				<tr>
					<th rowspan="2" style="border: 1px solid black" class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th rowspan="2" style="border: 1px solid black" class="vam">{{ trans('messages.lbl_Date') }}</th>
					<th rowspan="2" style="border: 1px solid black" class="vam">{{ trans('messages.lbl_mainsubject') }}</th>
					<th rowspan="2" style="border: 1px solid black" class="vam">{{ trans('messages.lbl_subsubject') }}</th>
					<th rowspan="2" style="border: 1px solid black" class="vam">{{ trans('messages.lbl_empName') }}</th>
					<!-- <th rowspan="2" class="vam">{{ trans('messages.lbl_amount') }}</th> -->
					<th rowspan="1" style="border-top: 1px solid black;border-right: 1px solid black;" colspan="3" class="vam">{{ trans('messages.lbl_transfer') }}</th>
					<th rowspan="1" style="border-top: 1px solid black;border-right: 1px solid black;" colspan="2" class="vam">{{ trans('messages.lbl_cash') }}</th>
					<th rowspan="2" style="border: 1px solid black" class="vam">{{ trans('messages.lbl_remarks') }}</th>
					<th rowspan="2" style="border: 1px solid black" class="vam">{{ trans('messages.lbl_bill') }}</th>
					<th class="tac" rowspan="2" style="border: 1px solid black">
						<span id="edit_header">{{ trans('messages.lbl_edit') }}</span>
							<input type="checkbox" class="vam pb2 Multi_reg_checkbox" id="Chk_all" style="text-decoration:none;display:none;" title="Check All" onchange="checkAll(this)" name="Chk_all" >
				 	</th>
				</tr>
				<tr>
					<th style=";border-bottom: 1px solid black;">{{ trans('messages.lbl_loan') }}</th> 
          			<th style="border-bottom: 1px solid black;">{{ trans('messages.lbl_expenses') }}</th> 
          			<th style="border-right: 1px solid black;border-bottom: 1px solid black;">
          			{{ trans('messages.lbl_withdraw') }}</th> 
          			<th style="border-bottom: 1px solid black;">{{ trans('messages.lbl_deposit') }}</th> 
          			<th style="border-right: 1px solid black;border-bottom: 1px solid black;" >
          			{{ trans('messages.lbl_sales') }}</th> 
				</tr>
			</thead>
			<tbody>
				@if($disp > 0)
				{{--*/ $row = '0' /*--}}
					<?php $loc = "";
						$i = 0;
						$loc1 = "";
						$loc2 = "";
						$incr = 0;
						$rowclr = 0;
						$tempvar = "";
						$today_date = date('Y-m-d'); 
						$yenTotalAmount = 0;
						$rowclr=0;
						$bank_total = 0;
						$bank_charge = 0;
						$Amtdisplay = 0;
						$loan = 0;
						$expen = 0;
						$withdraw = 0;
						$deposit = 0;
						$sales = 0;
						$result = 0;
					?>
					@for($j=0;$j<$disp;$j++)
					{{--*/ $loc = $getbktr_det[$j]['bankdatedetais'] /*--}}
						@if($loc != $temp) 
							@if($row==1)
								{{--*/ $style_tr = 'background-color: #A7CEC9;' /*--}}
								{{--*/ $row = '0' /*--}}
							@else
								{{--*/ $style_tr = 'background-color: #FFFFFF;' /*--}}
								{{--*/ $row = '1' /*--}}
							@endif
							{{--*/ $style_td = 'border-bottom: 1 px dotted black;' /*--}}
						@else
							{{--*/ $style_td = 'border-top:hidden;' /*--}}
						@endif
							{{--*/ $db_inserted_date = $getbktr_det[$j]['Ins_DT']; /*--}} 
							{{--*/ $db_updated_date = $getbktr_det[$j]['Up_DT']; /*--}} 
							{{--*/ $registered_date = strtotime($getbktr_det[$j]['Ins_DT']); /*--}} 
							{{--*/ $future_date = strtotime($getbktr_det[$j]['bankdate']); /*--}} 
							{{--*/ $updated_date = strtotime($getbktr_det[$j]['Up_DT']); /*--}} 
						<?php 
								// $db_inserted_date = $getbktr_det[$j]['Ins_DT'];
								// $db_updated_date = $getbktr_det[$j]['Up_DT'];
								// $registered_date = strtotime($getbktr_det[$j]['Ins_DT']);
								// $future_date = strtotime($getbktr_det[$j]['bankdate']);
								// $updated_date = strtotime($getbktr_det[$j]['Up_DT']);
								$Amtdisplay = str_replace("-","",$getbktr_det[$j]['Amountdisplay']);
							  $loc = $getbktr_det[$j]['loc'];
							  if (isset($getbktr_det[$j]['bankaccno'])) {
							  	$bankacc = $getbktr_det[$j]['bankaccno'];
							  } else {
							  	$bankacc = "";
							  }
							  $loc1 = ((is_numeric($getbktr_det[$j]['bankname']))?$getbktr_det[$j]['BankName']:$getbktr_det[$j]['bankname'])."-".$bankacc;
							  $loc2 = $bankacc;
							
							if( $loc1 != $temp1){
								$styleTD1 = 'style="border-top:1px dotted black;"'; 
							} else {
								$styleTD1 = 'style="border-top:1px dotted black;vertical-align: top;"';
							} ?>							
							{{--*/ $incr = $incr + 1; /*--}} 
							{{--*/ $serialcolor = $PAGING+$incr; /*--}} 
							@if($serialcolor != $tempvar)
								<?php if($rowclr==1){
										if (($future_date > $registered_date && $updated_date == "") 
											|| ($future_date > $registered_date && $updated_date == "0000-00-00")) {
											$style='style="background-color:#FCE1F0;"';
										} else if ($db_inserted_date == $today_date) {
											$style='style="background-color:#FAFAC0;"';
										} else if ($future_date > $registered_date && $db_updated_date == $today_date) {
											$style='style="background-color:#FAFAC0;"';
										} else if ($db_updated_date == $today_date) {
											$style='style="background-color:#FAFAC0;"';
										} else {
											$style='style="background-color:#dff1f4;"';
										}
										$rowclr=0;
									} else {
										if (($future_date > $registered_date && $updated_date == "") 
											|| ($future_date > $registered_date && $updated_date == "0000-00-00")) {
											$style='style="background-color:#FCE1F0;"';
										} else if ($db_inserted_date == $today_date) {
											$style='style="background-color:#FAFAC0;"';
										} else if ($future_date > $registered_date && $db_updated_date == $today_date) {
											$style='style="background-color:#FAFAC0;"';
										} else if ($db_updated_date == $today_date) {
											$style='style="background-color:#FAFAC0;"';
										} else {
											$style='style=""';
										}
										$rowclr=1;
									}
									$styleTD = 'style="border-top:1px solid red dotted black;"'; ?>
							@else
								<?php 	if($rowclr==1){
										if (($future_date > $registered_date && $updated_date == "") 
											|| ($future_date > $registered_date && $updated_date == "0000-00-00")) {
											$style='style="background-color:#FCE1F0;"';
										} else if ($db_inserted_date == $today_date) {
											$style='style="background-color:#FAFAC0;"';
										} else if ($future_date > $registered_date && $db_updated_date == $today_date) {
											$style='style="background-color:#FAFAC0;"';
										} else if ($db_updated_date == $today_date) {
											$style='style="background-color:#FAFAC0;"';
										} else {
											$style='';
										}
										$rowclr=0;
									} else {
										if (($future_date > $registered_date && $updated_date == "") 
											|| ($future_date > $registered_date && $updated_date == "0000-00-00")) {
											$style='style="background-color:#FCE1F0;"';
										} else if ($db_inserted_date == $today_date) {
											$style='style="background-color:#FAFAC0;"';
										} else if ($future_date > $registered_date && $db_updated_date == $today_date) {
											$style='style="background-color:#FAFAC0;"';
										} else if ($db_updated_date == $today_date) {
											$style='style="background-color:#FAFAC0;"';
										} else {
											$style='';
										}
										$rowclr=1;
									}
									$styleTD = 'style="border-top:0px;vertical-align: top;"'; ?>
							@endif
							
							<?php if($loc1 != $temp1 || $loc2 != $temp2){ ?>
								<?php if($j != 0){?>
								<tr style="background-color:white;border-top: 1.1px dotted black;font-weight:bold;border-left: 1px solid black;border-right: 1px solid black;">
									<td width="3%" style="border: none;"></td>
									<td width="4%" style="border: none;"></td>
									<td width="19%" style="border: none;"></td>
									<td width="19%" style="border: none;"></td>
									<td width="11%" style="border: none;padding-right: 5px;" align="right"></td>
									<td width="16%" align="right" style="padding: 5px;padding-bottom: 0px;color: black;">
										<?php
										if($loan == 0){
											echo "";
											$loanamt=0;
											$loan = 0;
										}else {
										  	echo number_format($loan);
										  	$loanamt=$loan;
											$loan = 0;
										}
										?>
									</td>
									<td width="11%" align="right" style="padding: 5px;padding-bottom: 0px;color: black;">
										<?php
										if($expen == 0){
											echo "";
											$expenamt=0;
											$expen = 0;
										}else {
										  	echo number_format($expen);
										  	$expenamt=$expen;
											$expen = 0;
										}
										?>
									</td>
									<td width="6%" align="right" style="padding: 5px;padding-bottom: 0px;color: black;">
										<?php
										if($withdraw == 0){
											echo "";
											$withdrawamt=0;
											$withdraw = 0;
										} else {
										  	echo number_format($withdraw);
										  	$withdrawamt=$withdraw;
											$withdraw = 0;
										}
										?>
									</td>
									<td width="7%" align="right" style="padding: 5px;padding-bottom: 0px;color: black;">
										<?php
										if($deposit == 0){
											echo "";
											$depositamt=0;
											$deposit = 0;
										} else {
										  	echo number_format($deposit);
										  	$depositamt=$deposit;
											$deposit = 0;
										}
										?>
									</td>
									<td width="7%" align="right" style="padding: 5px;padding-bottom: 0px;color: black;">
										<?php
										if($sales == 0){
											echo "";
											$salesamt=0;
											$sales = 0;
										} else {
										  	echo number_format($sales);
										  	$salesamt=$sales;
											$sales = 0;
										}
										?>
									</td>
									<td width="7%" style="border: none;"></td>
									<td width="5%" style="border: none;"></td>
									<td width="5%" style="border-right: 1px solid black;border-left: 0px solid black;"></td>
								</tr>
								<tr style="font-weight:bold;background-color: #f1a2a2;border-left: 1px solid black;border-right: 1px solid black;">
									<td width="3%" style="border: none;"></td>
									<td width="4%" style="border: none;"></td>
									<td width="19%" style="border: none;"></td>
									<td width="19%" style="border: none;"></td>
									<!-- <td width="19%" style="border: none;padding-right: 5px;" align="right"></td> -->
									<td width="11%" style="padding-right: 5px;border: 1px black;" align="right">
										{{ trans('messages.lbl_grandtot') }}
									</td>
									<td width="16%" colspan="3" align="right" style="padding: 5px;padding-bottom: 0px;border: 1px black;color: blue;">
										<?php
											$result=$loanamt+$expenamt+$withdrawamt;
											if ($result == 0) {
												echo "";
											} else{
												echo number_format($result);
											}
										?>
									</td>
									<td width="7%" align="right" style="padding: 5px;padding-bottom: 0px;border: 1px black;color: blue;">
										<?php
											if($depositamt == 0){
												echo "";
											} else {
											  	echo number_format($depositamt);
											}
										?>
									</td> 
									<td width="7%" align="right" style="padding: 5px;padding-bottom: 0px;border: 1px black;color: blue;">
										<?php
											if($salesamt == 0){
												echo "";
											} else {
											  	echo number_format($salesamt);
											}
										?>
									</td>
									<td width="7%" style="border: 1px black;"></td>
									<td width="5%" style="border: 1px black;"></td>
									<td width="5%" style="border-right: 1px solid black;border-left: 0px black;"></td>
								</tr>
								<!-- <tr style="background-color:white;font-weight:bold;">
									<td width="3%" style="border: none;"></td>
									<td width="4%" style="border: none;"></td>
									<td width="19%" style="border: none;"></td>
									<td width="19%" style="border: none;padding-right: 5px;" align="right">	</td>
									<td width="19%" style="border: none;padding-right: 5px;" align="right">
										Total
									</td>
									<td width="11%"  align="right" style="padding: 5px;padding-bottom: 0px;color: blue;">
										<?php 
											echo number_format($bank_total);
											$bank_total = 0;
										?>
									</td>
									<!-- <td width="11%" align="right" style="padding: 5px;padding-bottom: 0px;color: blue;">
										<?php
											echo number_format($bank_charge);
											$bank_charge = 0;
										?>  -->
									<!-- </td>  -->
									<!-- <td width="16%" style="border: none;"></td>
									<td width="11%" style="border: none;"></td>
									<td width="6%" style="border: none;"></td>
								</tr> --> 
								<?php }  ?>	
								<tr class="box100per boxhei25">
									<td  align="left" colspan="14"  class="box100per" style="border-left: 1px solid black;border-right: 1px solid black;background-color:lightgrey;font-weight:bold;" >
										<?php
										if ($getbktr_det[$j]['del_flg'] == 2) {
											echo $getbktr_det[$j]['BankName']."-".$bankacc."";
										} else {
											echo ((is_numeric($getbktr_det[$j]['bankname']))?$getbktr_det[$j]['BankName']:$getbktr_det[$j]['bankname'])."-".$bankacc;
										}
										?>
									</td>
								</tr>
							<?php }  ?>
						<tr <?php echo $style;?> id="<?php echo $j;?>">
							<td class="tac" style="border-left: 1px solid black;border-right: 1px solid black;">
								{{ ($index->currentpage()-1) * $index->perpage() + $j + 1 }}
							</td>
							<td class="tac" style="{{ $style_td }}border-left: 1px solid black;border-right: 1px solid black;">
								@if($loc != $temp || $loc2 != $temp2)
									{{--*/ $transferdate=$getbktr_det[$j]['bankdatedetais']; /*--}}
			   						{{--*/ $transdate=explode("-" , $transferdate);  /*--}}
									@if($transdate[2]<"10")
										{{ $transferdate[9] }}日
									@else
										{{ $transferdate[8] }}{{ $transferdate[9] }}日
									@endif
								@endif
							</td>	
							@if($getbktr_det[$j]['del_flg'] == 2)
								<td style="border-left: 1px solid black;border-right: 1px solid black;">
									{{ trans('messages.lbl_cash') }}
								</td>
							@elseif($getbktr_det[$j]['fee'] == "" && $getbktr_det[$j]['salaryFlg'] == 1)
								<td style="border-left: 1px solid black;border-right: 1px solid black;">
									{{ trans('messages.lbl_salary') }}
								</td>
							@elseif($getbktr_det[$j]['fee'] != "" && $getbktr_det[$j]['others'] == 1)
								<td style="border-left: 1px solid black;border-right: 1px solid black;">
									{{ trans('messages.lbl_Others') }}
								</td>
							@elseif($getbktr_det[$j]['fee'] == "" && $getbktr_det[$j]['loan_flg'] == 1)
								<td style="border-left: 1px solid black;border-right: 1px solid black;">
									{{ trans('messages.lbl_loan') }}
								</td>
							@elseif($getbktr_det[$j]['fee'] == "" && $getbktr_det[$j]['salaryFlg'] != 1 && 	$getbktr_det[$j]['empNo'] !="")
								<td style="border-left: 1px solid black;border-right: 1px solid black;">
									{{ trans('messages.lbl_transfer') }}
								</td>
							@elseif($getbktr_det[$j]['loan_flg'] != 1 )
								@if(Session::get('languageval') == "en")
									<td style="border-left: 1px solid black;border-right: 1px solid black;">
										<a href="javascript:gotoexp_history('{{ $getbktr_det[$j]['subject'] }}','{{ $getbktr_det[$j]['salaryFlg'] }}','{{ $getbktr_det[$j]['mainbankid'] }}','{{ $getbktr_det[$j]['bankaccno'] }}','{{$request->mainmenu}}',4)" style="color:blue;">
											@if(isset($getbktr_det[$j]['subjectbank']) && mb_strlen($getbktr_det[$j]['subjectbank'], 'UTF-8') >= 16)

												<?php $str = mb_substr(ucwords($getbktr_det[$j]['subjectbank']), 0, 15, 'UTF-8');
														echo "<span title = '".ucwords($getbktr_det[$j]['subjectbank'])."'>".$str."...</span>"; ?>
											@else
												@if($getbktr_det[$j]['salaryFlg'] != 1)
												
											
													@if(isset($getbktr_det[$j]['subjectbank']))
														{{ ucwords(strtolower($getbktr_det[$j]['subjectbank'])) }}
													@endif
												@else
													<a href="javascript:transferhistory('{{ $getbktr_det[$j]['empNo'] }}','{{ $getbktr_det[$j]['EmpName'] }}','{{ $getbktr_det[$j]['mainbankid'] }}','{{ $getbktr_det[$j]['bankaccno'] }}','{{$request->mainmenu}}',4,'{{$request->selMonth}}','{{$request->selYear}}')" style="color:blue;">
													@if(mb_strlen($getbktr_det[$j]['EmpName'], 'UTF-8') >= 16)
														<?php $str = mb_substr($getbktr_det[$j]['EmpName'], 0, 15, 'UTF-8');
																echo "<span title = '".$getbktr_det[$j]['EmpName']."'>".$str."...</span>";  ?>
													@else
														{{ $getbktr_det[$j]['EmpName'] }}
													@endif
												@endif
											@endif
									</td>
								@else
									<td style="border-left: 1px solid black;border-right: 1px solid black;">
										<a href="javascript:gotoexp_history('{{ $getbktr_det[$j]['subject'] }}','{{ $getbktr_det[$j]['salaryFlg'] }}','{{ $getbktr_det[$j]['mainbankid'] }}','{{ $getbktr_det[$j]['bankaccno'] }}','{{$request->mainmenu}}',4)" style="color:blue;">
											@if(isset($getbktr_det[$j]['subjectbank']) && mb_strlen($getbktr_det[$j]['subjectbank'], 'UTF-8') >= 16)
												<?php $str = mb_substr(ucwords($getbktr_det[$j]['subjectbank']), 0, 15, 'UTF-8');
														echo "<span title = '".ucwords($getbktr_det[$j]['subjectbank'])."'>".$str."...</span>"; ?>
											@else
												@if($getbktr_det[$j]['salaryFlg'] != 1)
													{{ ucwords(strtolower($getbktr_det[$j]['subjectbank'])) }}
												@else
													<a href="javascript:transferhistory('{{ $getbktr_det[$j]['empNo'] }}','{{ $getbktr_det[$j]['EmpName'] }}','{{ $getbktr_det[$j]['mainbankid'] }}','{{ $getbktr_det[$j]['bankaccno'] }}','{{$request->mainmenu}}',4,'{{$request->selMonth}}','{{$request->selYear}}')" style="color:blue;">
													@if(mb_strlen($getbktr_det[$j]['EmpName'], 'UTF-8') >= 16)
														<?php $str = mb_substr($getbktr_det[$j]['EmpName'], 0, 15, 'UTF-8');
																echo "<span title = '".$getbktr_det[$j]['EmpName']."'>".$str."...</span>";  ?>
													@else
														{{ $getbktr_det[$j]['EmpName'] }}
													@endif
												@endif
											@endif
									</td>
								@endif
							@else
								<td style="border-left: 1px solid black;border-right: 1px solid black;">
									<a  href="javascript:gototransferhistory('{{$getbktr_det[$j]['bankname_id']}}','{{$getbktr_det[$j]['loan_flg']}}','{{$request->mainmenu}}',4,'{{$request->selMonth}}','{{$request->selYear}}');" style="color:blue;">
										@if(!is_numeric($getbktr_det[$j]['bankname']))
											{{ $getbktr_det[$j]['bankname'] }}
										@endif
									</a>
								</td>
							@endif
							@if($getbktr_det[$j]['loan_flg'] != 1 && $getbktr_det[$j]['others'] !=1)
								@if(Session::get('languageval') == "en")
									<td style="border-left: 1px solid black;border-right: 1px solid black;">
											@if(isset($getbktr_det[$j]['detail']))
												@php $name = $getbktr_det[$j]['detail']; @endphp
											@else
												@php $name = ""; @endphp
											@endif
										@if($getbktr_det[$j]['fee'] == "" && $getbktr_det[$j]['del_flg'] != 2)
									{{ trans('messages.lbl_charge') }}

								@else
										<a href="javascript:gototransfersubhistory('{{$getbktr_det[$j]['details']}}','{{$getbktr_det[$j]['subject']}}','{{$name}}','{{$getbktr_det[$j]['salaryFlg']}}','{{$getbktr_det[$j]['mainbankid']}}','{{$getbktr_det[$j]['bankaccno']}}','{{$request->mainmenu}}',4,'{{$getbktr_det[$j]['transaction_flg']}}')" style="color:blue;">

											<?php if (isset($getbktr_det[$j]['detail'])) {
												echo $getbktr_det[$j]['detail'];
											} 
											if(Session::get('languageval') == "en") {
												if (isset($getbktr_det[$j]['sub_eng']) && mb_strlen($getbktr_det[$j]['sub_eng'], 'UTF-8') >= 15) {
														$str = mb_substr(ucwords($getbktr_det[$j]['sub_eng']), 0, 14, 'UTF-8');
														echo "<span title = '".ucwords($getbktr_det[$j]['sub_eng'])."'>".$str."...</span>"; 
													} else {
														if($getbktr_det[$j]['salaryFlg'] != 1){
														} else {
															echo "Salary";
														}
														
												}
											} else {
												if (isset($getbktr_det[$j]['sub_jap']) && mb_strlen($getbktr_det[$j]['sub_jap'], 'UTF-8') >= 15) {
														$str = mb_substr(ucwords($getbktr_det[$j]['sub_jap']), 0, 14, 'UTF-8');
														echo "<span title = '".ucwords($getbktr_det[$j]['sub_jap'])."'>".$str."...</span>"; 
													} else {
														if($getbktr_det[$j]['salaryFlg'] != 1){
														} else {
															echo "Salary";
														}
												}
												}?>
												<a href="javascript:gotoCash('{{ $getbktr_det[$j]['BankName'] }}','{{ $getbktr_det[$j]['bankaccno'] }}','{{ $getbktr_det[$j]['Bank_NickName'] }}','{{ $getbktr_det[$j]['transaction_flg'] }}','{{ $request->mainmenu }}','{{$request->selMonth}}','{{$request->selYear}}');">
												<font color="blue"><?php if ($getbktr_det[$j]['transaction_flg']==1){

													if (isset($getbktr_det[$j]['sub_eng']) && mb_strlen($getbktr_det[$j]['sub_eng'], 'UTF-8') >= 15) {
																$str = mb_substr(ucwords($getbktr_det[$j]['sub_eng']), 0, 14, 'UTF-8');
																echo "<span title = '".ucwords($getbktr_det[$j]['sub_eng'])."'>".$str."...</span>"; 
															} else {
																if(isset($getbktr_det[$j]['sub_eng'])) {
																	echo ucwords(strtolower($getbktr_det[$j]['sub_eng'])) ;
																}
														} 
													echo "Debit";	
												} else if ($getbktr_det[$j]['transaction_flg']==3){

													if($getbktr_det[$j]['transfer_flg']==1 ){
														echo "Transfer to"." ・"."Debit";
													} else {
														echo "Transfer to"." ・"."Credit";	
													}
														} elseif ($getbktr_det[$j]['transaction_flg']==2){
															echo "Credit";	
														}
												 ?>				
										</a>
									</td>
									@endif
								@else
									<td style="border-left: 1px solid black;border-right: 1px solid black;">
											@if(isset($getbktr_det[$j]['detail']))
												@php $name = $getbktr_det[$j]['detail']; @endphp
											@else
												@php $name = ""; @endphp
											@endif
											@if($getbktr_det[$j]['fee'] == "" && $getbktr_det[$j]['del_flg'] != 2)
												{{ trans('messages.lbl_charge') }}
										@else
										<a href="javascript:gototransfersubhistory('{{$getbktr_det[$j]['details']}}','{{$getbktr_det[$j]['subject']}}','{{$name}}','{{$getbktr_det[$j]['salaryFlg']}}','{{$getbktr_det[$j]['mainbankid']}}','{{$getbktr_det[$j]['bankaccno']}}','{{$request->mainmenu}}',4,'{{$getbktr_det[$j]['transaction_flg']}}')" style="color:blue;">
											<?php  
											if (isset($getbktr_det[$j]['detail'])) {
												echo $getbktr_det[$j]['detail'];
											} 
												if(Session::get('languageval') == "en") {
												if (isset($getbktr_det[$j]['sub_eng']) && mb_strlen($getbktr_det[$j]['sub_eng'], 'UTF-8') >= 15) {
														$str = mb_substr(ucwords($getbktr_det[$j]['sub_eng']), 0, 14, 'UTF-8');
														echo "<span title = '".ucwords($getbktr_det[$j]['sub_eng'])."'>".$str."...</span>"; 
													} else {
														if($getbktr_det[$j]['salaryFlg'] != 1){
														} else {
															echo "給料";
														}
												}
											} else {
												if (isset($getbktr_det[$j]['sub_jap']) && mb_strlen($getbktr_det[$j]['sub_jap'], 'UTF-8') >= 15) {
														$str = mb_substr(ucwords($getbktr_det[$j]['sub_jap']), 0, 14, 'UTF-8');
														echo "<span title = '".ucwords($getbktr_det[$j]['sub_jap'])."'>".$str."...</span>"; 
													} else {
														if($getbktr_det[$j]['salaryFlg'] != 1){
														} else {
															echo "給料";
														}
												}
												}?>
													<font color="blue"><?php if ($getbktr_det[$j]['transaction_flg']==1){
													if (isset($getbktr_det[$j]['sub_eng']) && mb_strlen($getbktr_det[$j]['sub_eng'], 'UTF-8') >= 15) {
																$str = mb_substr(ucwords($getbktr_det[$j]['sub_eng']), 0, 14, 'UTF-8');
																echo "<span title = '".ucwords($getbktr_det[$j]['sub_eng'])."'>".$str."...</span>"; 
															} else {
																if(isset($getbktr_det[$j]['sub_eng'])) {
																	echo ucwords(strtolower($getbktr_det[$j]['sub_eng'])) ;
																}
														} 
													echo "引出";	
												} else if ($getbktr_det[$j]['transaction_flg']==3){
													if($getbktr_det[$j]['transfer_flg']==1){
														echo "送金"." ・ "."引出";
													} else {
														echo "送金"." ・ "."入金";	
													}
												} 	else if ($getbktr_det[$j]['transaction_flg']==2) {
													echo "入金";	
													} 
												?>
										</a>
									</td>
								@endif
								@endif
							@else
								<td style="border-left: 1px solid black;border-right: 1px solid black;">
									@if($getbktr_det[$j]['fee'] == "" && $getbktr_det[$j]['loan_flg'] == 1)
										{{ trans('messages.lbl_charge') }}
									@elseif( $getbktr_det[$j]['others'] == 1)
										{{ trans('messages.lbl_Others') }}
									@else
										{{ trans('messages.lbl_loanpay') }}
									@endif
								</td>
								
							@endif
							<td style="border-left: 1px solid black;border-right: 1px solid black;" title="{{ empnameontitle($getbktr_det[$j]['LastNames'],$getbktr_det[$j]['FirstNames'],50) }}">	@if(isset($getbktr_det[$j]['LastNames'])){{ empnamelength($getbktr_det[$j]['LastNames'], $getbktr_det[$j]['FirstNames'],25) }}@endif</td>
							<!-- <td class="tar pr5">
								<?php $Amtdisplay = str_replace("-","",$getbktr_det[$j]['Amountdisplay']);
									echo $Amtdisplay;
								?>
								<?php
									$bank_total += str_replace(",","",$Amtdisplay) ;
								?>
							</td> -->
							<td class="tar pr5">
								<?php
									if($getbktr_det[$j]['loan_flg'] ==1 || $getbktr_det[$j]['others'] ==1){
										$loan += str_replace(",","",$Amtdisplay);
										echo $Amtdisplay;									
									}
								?>
							</td>
							<td class="tar pr5">
								<?php
									if($getbktr_det[$j]['salaryFlg']==1 || $getbktr_det[$j]['transaction_flg'] =="" && $getbktr_det[$j]['loan_flg'] !=1 && $getbktr_det[$j]['others'] !=1){
										$expen += str_replace(",","",$Amtdisplay);
										echo $Amtdisplay;
									}
								?>
							</td>
							<td class="tar pr5"  style="border-left: 1px solid black;border-right: 1px solid black;">
								<?php
									if($getbktr_det[$j]['transaction_flg']==1 || $getbktr_det[$j]['transfer_flg']==1){
										$withdraw += str_replace(",","",$Amtdisplay);
										echo $Amtdisplay;
									}
								?>
							</td>
							<td class="tar pr5">
								<?php
									if($getbktr_det[$j]['transfer_flg']==2){
							  			$deposit += str_replace(",","",$Amtdisplay);
									   	echo $Amtdisplay;
									}
								?>
							</td>
							<td class="tar pr5"  style="border-left: 1px solid black;border-right: 1px solid black;">
								<?php
									if($getbktr_det[$j]['transaction_flg']==2){
										$sales += str_replace(",","",$Amtdisplay);
										echo $Amtdisplay;
									}
								?>
							</td>
							<td class="tal" style="border-left: 1px solid black;border-right: 1px solid black;">
								@if(isset($getbktr_det[$j]['remark_dtl']))
									{!! nl2br(e($getbktr_det[$j]['remark_dtl'])) !!}
								@endif
							</td>
							<td align="center" style="border-left: 1px solid black;border-right: 1px solid black;">
								@if($getbktr_det[$j]['file_dtl'] != "")
									<?php
										$file_url = '../InvoiceUpload/Expenses/' . $getbktr_det[$j]['file_dtl'];
									 ?>
									@if(isset($getbktr_det[$j]['file_dtl']) && file_exists($file_url))
										<a href="javascript:filedownload('../../../../InvoiceUpload/Expenses','{{$getbktr_det[$j]['file_dtl']}}');" title="Download"><img src="{{ URL::asset('resources/assets/images/download.png') }}" width="20px;" height="20px;" title="Download Bank Tansfer Image"></img></a>
									@else
									@endif
								@endif
							</td>
							<?php   
								$copy_month_flg = $getbktr_det[$j]['copy_month_flg'];

								$currmon = "";											
								$month = "";											
								$cur_month = "";											
								$month = $cur_month;  
								$Year = $current_year; 
								$monyear = $Year."-".$month;
								$currmon = $getbktr_det[$j]['bankdate'];

								$orderdateval = explode('-', $getbktr_det[$j]['bankdate']);
								$yearval = $orderdateval[0];
								$monthval  = $orderdateval[1];
								$dateval  = $orderdateval[2];

								//split the date
								$monyear =  $yearval . '-' . $monthval;	

								$futuremonth = date ('n', strtotime ( '+1 month' , strtotime ( $monyear."-01" )));
								$futureyear = date ('Y', strtotime ( '+1 month' , strtotime ( $monyear."-01" )));

								$orderdateval = explode('-', $getbktr_det[$j]['bankdate']);
								$yearval = $orderdateval[0];
								$monthval  = $orderdateval[1];
								$dateval  = $orderdateval[2];

								$nextCheck = date("Y-m", mktime(0, 0, 0, $monthval+1, 1, $yearval));
								$orderdate = explode('-',$nextCheck);
								$year = $orderdate[0];
								$month   = $orderdate[1];	

								$day_nextmonth =cal_days_in_month(CAL_GREGORIAN,$month, $year);
								if($dateval <= $day_nextmonth){
								$monthlyDate = strtotime("+1 month".$getbktr_det[$j]['bankdate']);
								$nextdate = date("Y-m-d", $monthlyDate);
								} else{
								/*$monthlyDate = strtotime("+1 month last day".$getbktr_det[$i]['bankdate']);
								$nextdate = date("Y-m-d", $monthlyDate);*/
								$d = new DateTime($getbktr_det[$j]['bankdate']);
								$d->modify( 'last day of next month' );
								$lastDateOfNxtMonth = $d->format( "Y-m-d" );
								$nextdate = $lastDateOfNxtMonth;
								} 
							?>
							<?php 
							if($copy_month_flg != "1" && isset($getbktr_det[$j]['copy']) && $getbktr_det[$j]['copy'] == "1") {       					            
								?><td style=" border-left: 1px solid black;border-right: 1px solid black;word-wrap: break-word;" class="tdcontenthistory" align="center">
							    <?php if ($getbktr_det[$j]['salaryFlg'] != "1" && $getbktr_det[$j]['transfer_flg']!=2) { ?>
							    	<?php if ($getbktr_det[$j]['del_flg']==2 && $getbktr_det[$j]['transfer_flg']!=2 &&  $getbktr_det[$j]['transaction_flg']==3 
							    	|| $getbktr_det[$j]['transaction_flg']==1 ||$getbktr_det[$j]['transaction_flg']==2 ) { ?>
							    		<a href="javascript:copyCashRecord('{{ $getbktr_det[$j]['id'] }}','{{ $request->mainmenu }}','{{ $nextdate }}');" title="Copy" style="color: blue;">
							    	<?php }elseif($getbktr_det[$j]['others']==1) { ?> 
							    		<a style="color: blue;"  href="javascript:CopyothersRecord('{{$getbktr_det[$j]['id'] }}','{{ $request->mainmenu }}','{{ $nextdate }}',3)" title="Copy">	
							    	<?php }else { ?> 
										<a style="color: blue;"  href="javascript:CopybkrsRecord('{{$getbktr_det[$j]['id'] }}','{{ $getbktr_det[$j]['loan_flg'] }}','{{ $request->mainmenu }}',3,'{{ $nextdate }}')" title="Copy">
									<?php  } ?>
		 						<font color="blue"> <?php
								$mon = '月';
								echo $futuremonth;echo $mon;
								} }else { ?></font></a>
									
								<td style="border-left: 1px solid black;border-right: 1px solid black; word-wrap: break-word;" class="tdcontenthistory" align="center">
							    <?php if ($getbktr_det[$j]['salaryFlg'] != "1") { ?>
									<?php if($getbktr_det[$j]['del_flg']==2 && $getbktr_det[$j]['transfer_flg']!=2 
									&&  $getbktr_det[$j]['transaction_flg']==3 || $getbktr_det[$j]['transaction_flg']==1 
									||$getbktr_det[$j]['transaction_flg']==2) { ?>
									<a style="text-decoration: none;" style="color: blue;"  href="javascript:copyCashRecord('<?php echo $getbktr_det[$j]['id'];?>','{{ $request->mainmenu }}')" title="Copy">
						<img class="vam" src="{{ URL::asset('resources/assets/images/copy.png') }}" width="20" height="20"></a>
							    	<?php } else if($getbktr_det[$j]['others'] != 1 && $getbktr_det[$j]['transfer_flg'] != 2 || $getbktr_det[$j]['copy_month_flg'] != 0 && $getbktr_det[$j]['del_flg'] != 2 ){?>
							    		<a href="javascript:CopybkrsRecord('{{ $getbktr_det[$j]['id'] }}','{{ $getbktr_det[$j]['loan_flg'] }}','{{ $request->mainmenu }}',3,'');" title="Copy" style="color: blue;">
							    			<img class="vam" src="{{ URL::asset('resources/assets/images/copy.png') }}" width="20" height="20">
							    	<?php } else if($getbktr_det[$j]['others'] == 1){?>
							    		<a href="javascript:CopyothersRecord('{{$getbktr_det[$j]['id'] }}','{{ $request->mainmenu }}')" title="Copy" style="color: blue;">
							    			<img class="vam" src="{{ URL::asset('resources/assets/images/copy.png') }}" width="20" height="20">
							    	<?php }?>
							    	<?php } ?>

								<?php } ?>
								<input type="hidden" id="salary_<?php echo$j?>" name="salary_<?php echo$j?>"
									value="<?php echo $getbktr_det[$j]['salaryFlg'];?>">
									<?php if ($getbktr_det[$j]['salaryFlg'] != "1" && $getbktr_det[$j]['transaction_flg'] == "" && $getbktr_det[$j]['others'] != 1) { ?>
									<a id="<?php echo 'mul'.$j ?>" style="display:none;color: blue;"  href="javascript:CopybkrsRecord('{{$getbktr_det[$j]['id'] }}','{{ $getbktr_det[$j]['loan_flg'] }}','{{ $request->mainmenu }}',3,'')" title="Multi Register">
									<input type="checkbox" class="Multi_reg_checkbox vam m0" id="multi_reg"  onchange="uncheckheader(this,<?php echo $getbktr_det[$j]['id']; ?>)"  name="multi_reg[]" value="<?php echo  $getbktr_det[$j]['id'];?>">
									</a>
									<?php } ?>

								<input type="hidden" id="transfer_<?php echo$j?>" name="transfer_<?php echo$j?>"
								value="<?php echo $getbktr_det[$j]['transfer_flg'],$getbktr_det[$j]['transaction_flg'];?>">
									<?php  
										 if($getbktr_det[$j]['del_flg']==2 && $getbktr_det[$j]['transfer_flg']!=2 &&  $getbktr_det[$j]['transaction_flg']==3 || $getbktr_det[$j]['transaction_flg']==1 ||$getbktr_det[$j]['transaction_flg']==2 ) { ?>
										 	<a id="<?php echo 'edt'.$j ?>" style="cursor: default;display: none;" title="Edit" href="javascript:EditCashRecord('{{ $getbktr_det[$j]['id'] }}','{{ $request->mainmenu }}','');"><img class="vam" src="{{ URL::asset('resources/assets/images/edit.png') }}" width="20" height="20"></a>
									 	
								<?php } elseif($getbktr_det[$j]['others']==1) { ?>
									<a id="<?php echo 'edt'.$j ?>" style="cursor: default; display: none;" title="Edit" href="javascript:EditothersRecord('edit','{{ $getbktr_det[$j]['id'] }}','{{ $request->mainmenu }}');"><img class="vam" src="{{ URL::asset('resources/assets/images/edit.png') }}" width="20" height="20"></a>
								<?php } else { ?>
									<a id="<?php echo 'edt'.$j ?>" style="cursor: default; display: none;" title="Edit" href="javascript:EditbkrsRecord('{{ $getbktr_det[$j]['id'] }}','{{ $getbktr_det[$j]['loan_flg'] }}','{{ $request->mainmenu }}',2);"><img class="vam" src="{{ URL::asset('resources/assets/images/edit.png') }}" width="20" height="20"></a>
								<?php } ?>
								</td>
						</tr>

						<?php if(count($getbktr_det) == ($j+1)){?>
							<tr style="background-color:white;font-weight:bold;border-left: 1px solid black;">
								<td width="3%" style="border: none;"></td>
								<td width="4%" style="border: none;"></td>
								<td width="19%" style="border: none;"></td>
								<td width="19%" style="border: none;"></td>
								<!-- <td width="19%" style="border: none;padding-right: 5px;" align="right"></td> -->
								<td width="11%" style="border: none;padding-right: 5px;" align="right"></td>
								<td width="16%" align="right" style="padding: 5px;padding-bottom: 0px;color: black;">
									<?php
									if($loan == 0){
										echo "";
										$endloanamt=0;
										$loan = 0;
									} else {	
									  	echo number_format($loan);
									  	$endloanamt=$loan;
										$loan = 0;
									}
									?>
								</td>
								<td width="11%" align="right" style="padding: 5px;padding-bottom: 0px;color: black;">
									<?php
									if($expen == 0){
										echo "";
										$endexpenamt=0;
										$expen = 0;
									} else {
									  	echo number_format($expen);
									  	$endexpenamt=$expen;
										$expen = 0;
									}
									?>
								</td>
								<td width="6%" align="right" style="padding: 5px;padding-bottom: 0px;color: black;">
									<?php
									if($withdraw == 0){
										echo "";
										$endwithdrawamt=0;
										$withdraw = 0;
									} else {
									  	echo number_format($withdraw);
									  	$endwithdrawamt=$withdraw;
										$withdraw = 0;
									}
									?>
								</td>
								<td width="7%" align="right" style="padding: 5px;padding-bottom: 0px;color: black;">
									<?php
									if($deposit == 0){
										echo "";
										$enddepositamt=0;
										$deposit = 0;
									} else {
									 	echo number_format($deposit);
								 		$enddepositamt=$deposit;
										$deposit = 0;
									}
									?>
								</td>
								<td width="7%" align="right" style="padding: 5px;padding-bottom: 0px;color: black;">
									<?php
									if($sales == 0){
										echo "";
										$endsalesamt=0;
										$sales = 0;
									} else {
									 	echo number_format($sales);
									 	$endsalesamt=$sales;
										$sales = 0;
									}
									?>
								</td>
								<td width="7%" style="border: none;"></td>
								<td width="5%" style="border: none;"></td>
								<td width="5%" style="border-right: 1px solid black;border-left: 0px solid black;"></td>
							</tr>
							<tr style="background-color: #f1a2a2;font-weight:bold;border-left: 1px solid black;border-bottom: 1px solid black;">
									<td width="3%" style="border: none;"></td>
									<td width="4%" style="border: none;"></td>
									<td width="19%" style="border: none;"></td>
									<td width="19%" style="border: none;"></td>
									<!-- <td width="19%" style="border: none;padding-right: 5px;" align="right"></td> -->
									<td width="11%" style="border: none;padding-right: 0px;" align="right" align="right">
										{{ trans('messages.lbl_grandtot') }}
									</td>
									<td width="16%" colspan="3" align="right" style="padding: 5px;padding-bottom: 0px;border: 1px black;color: blue;">
										<?php
											$endresult=$endloanamt+$endexpenamt+$endwithdrawamt;
											if ($endresult == 0) {
												echo "";
											} else{
												echo number_format($endresult);
											}
										?>
									</td>
									<td width="7%" align="right" style="padding: 5px;padding-bottom: 0px;border: 1px black;color: blue;">
										<?php
											if($enddepositamt == 0){
												echo "";
											} else {
											  	echo number_format($enddepositamt);
											}
										?>
									</td>
									<td width="7%" align="right" style="padding: 5px;padding-bottom: 0px;border: 1px black;color: blue;">
										<?php
											if($endsalesamt == 0){
												echo "";
											} else {
											  	echo number_format($endsalesamt);
											}
										?>
									</td>
									<td width="7%" style="border: none;"></td>
									<td width="5%" style="border: none;"></td>
									<td width="5%" style="border-right: 1px solid black;border-left: 0px black;border-bottom: 1px solid black; "></td>
							</tr>
						<!-- <tr style="background-color:white;font-weight:bold;">
								<td width="3%" style="border: none;"></td>
								<td width="4%" style="border: none;"></td>
								<td width="11%" style="border: none;"></td>
								<td width="11%" style="border: none;padding-right: 5px;" align="right"></td>
								<td width="11%" style="border: none;padding-right: 5px;" align="right">
									Total
								</td>
								<td width="9%" align="right" style="padding: 5px;padding-bottom: 0px;color: blue;">
									<?php 
										echo number_format($bank_total);
										$bank_total = 0;
									?>
								</td>
								 <td width="11%" align="right" style="padding: 5px;padding-bottom: 0px;color: blue;">
									 <?php
										echo number_format($bank_charge);
										$bank_charge = 0;
									?> 
								</td> -->
								<!-- <td width="15.4%" style="border: none;"></td>
								<td width="10%" style="border: none;"></td>
								<td width="5%" style="border: none;"></td>
						</tr> --> 
						<?php }  ?>	
							 {{--*/ $temp = $loc /*--}}
							 {{--*/ $temp1 = $loc1 /*--}}
							 {{--*/ $temp2 = $loc2 /*--}}
							 {{--*/ $tempvar = $serialcolor /*--}}
					@endfor
				@else
					<tr>
						<td class="text-center colred" colspan="13">
							{{ trans('messages.lbl_nodatafound') }}
						</td>
					</tr>
				@endif
			</tbody>
		</table>
		@if($disp > 0)
		<table class="mt10 box100per" border="0" cellspacing="0" cellpadding="3" style="border: none;">
					<?php 
						if(isset($rsTotalAmount)) { 
							if($rsTotalAmount < 0) {
								$totalYenColor="color:red";
							} else {
								$totalYenColor="color:blue";
							}
						}
						if ($fee_rsTotalAmount < 0) {
							$totalrsColor="color:red";
						} else {
							$totalrsColor="color:blue";
						}
					?>
			<tr >
				<!-- <td width="3%" style="border: none;"></td>
				<td width="4%" style="border: none;"></td>
				<td width="3%" style="border: none;"></td> -->
				<!-- <td width="5%" style="border: none;"></td> -->
				<!-- <td width="5%"  style="border: none;padding-right: 5px;" align="right">
					<label>
						{{ trans('messages.lbl_totamt') }}
					</label>
				</td>
				<td width="10.5%" colspan="2" align="right" style="padding: 5px;padding-bottom: 0px;">
					<label style="<?php echo $totalYenColor; ?>" >
						¥ {{ number_format((isset($rsTotalAmount)? $rsTotalAmount: 0)) }}
					</label>
				</td>
				<td width="11%" align="right" style="padding: 5px;padding-bottom: 0px;">
					<label style="<?php echo $totalrsColor; ?>">
						@if(!empty($fee_rsTotalAmount))
							¥ {{ number_format($fee_rsTotalAmount)}}
						@endif
					</label>
				</td> -->
				<!-- <td width="15.4%" style="border: none;"></td> -->
				<!-- <td width="10%" style="border: none;"></td> -->
				<td   width="5%" style="border: none;text-align: right;">
					<a class="btns btn-success box70 h20 pull-right" href="javascript:transfer_addreg('{{$request->mainmenu}}');" id="add" style="text-decoration: none;display: none;"><span class="fa fa-plus"></span> {{ trans('messages.lbl_add') }}</a>
				</td>
				
			</tr>
			@php 
				$curdate = date('Y-m');
			@endphp
			@if($date_month <  $curdate && $getbktr_det[0]['submit_flg'] == "1")
				<tr>
					<td width="100%" colspan="8" style="border: none;">
					<b><span style="color:#8B0000;font-size: 14px;" class="ml10">Note:</span></b>
					<b><span style="color:black;font-size: 14px;" class="ml10">The Record Was Submitted in The Period
						</span></b>
					<i><b><span style="color: #8B0000;">({{ $yearval }}  年 {{ $monthval }}  月分)</span>.</b></i>
					</td>
				</tr>
			@endif

			</table>
	</div>
	@endif
	@if(!empty($index->total()))
	<div class="text-center pl13">
			<span class="pull-left mt24">
				{{ $index->firstItem() }} ~ {{ $index->lastItem() }} / {{ $index->total() }}
			</span>
		{{ $index->links() }}
		<div class="CMN_display_block flr mr18">
			{{ $index->linkspagelimit() }}
		</div>
	</div>
	@endif
	</article>
	<!-- End Heading -->
	</div>
{{ Form::close() }}
<script type="text/javascript">
    $('.scrollbar').height($(window).height()-188);
    $('li.dropdown').click(function() {
   		$(this).children('ul').toggle();
	});
 </script>
 {{ Form::open(array('name'=>'transferdownload', 'id'=>'transferdownload', 'url' => 'Transfer/download?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
{{ Form::close() }}
{{ Form::open(array('name'=>'frmtransferexceldownload', 
						'id'=>'frmtransferexceldownload', 
						'url' => 'Transfer/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
{{ Form::hidden('actionName', '', array('id' => 'actionName')) }}
{{ Form::hidden('selYearMonth', '', array('id' => 'selYearMonth')) }}
{{ Form::close() }}
@endsection
