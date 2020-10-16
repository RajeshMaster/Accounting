@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/expenses.js') }}
{{ HTML::script('resources/assets/js/Setting.js') }}
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::style('resources/assets/css/switch.css') }}
@php use App\Http\Helpers; @endphp
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
</script>
<style type="text/css">
	.scrollbar {
		float: left;
		max-height: 485px;
		width: 270px;
		overflow-x: hidden !important;
		overflow-y: scroll !important;
		/*margin-bottom: 5px;*/
	}
	.CMN_btn_lightgray {
		border: 2px solid #CCCCCC; /* lightgray */
		border-radius: 2px;
		background-color: #CCCCCC;
		color: #ffffff;
		height: 40%;
	}
	.alertboxalign {
		margin-bottom: -50px !important;
	}
	.alert {
		display:inline-block !important;
		height:30px !important;
		padding:5px !important;
	}
	.btn-gray {
		background-color: gray;
		border-color: white;
	}
	.btn-red {
		background-color: red;
		border-color: white;
		color: white;
	}
	.bg_lightgrey {
		background-color:#D3D3D3    ! important;
	}
	li.dropdown ul {
		display : none;
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
	.mte5 {
		margin-top: -5px !important;
	}
</style>
<div class="CMN_display_block box100per" id="main_contents">
<!-- article to select the main&sub menu -->
@if($request->mainmenu == "pettycash")
<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_8">
@else
<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_1">
@endif
	{{ Form::open(array('name'=>'frmexpenseindex', 
						'id'=>'frmexpenseindex', 
						'url' => 'Expenses/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('month', '', array('id' => 'month')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('year', '', array('id' => 'year')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('hiddenplimit', '' , array('id' => 'hiddenplimit')) }}
	    {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	    {{ Form::hidden('hiddenpage', '' , array('id' => 'hiddenpage')) }}
	    {{ Form::hidden('submitflg','', array('id' => 'submitflg')) }}
	    {{ Form::hidden('id','', array('id' => 'id')) }}
	    {{ Form::hidden('expcopyflg','', array('id' => 'expcopyflg')) }}
	    {{ Form::hidden('cashflg', '' , array('id' => 'cashflg')) }}
	    {{ Form::hidden('mulid','', array('id' => 'mulid')) }}
	    {{ Form::hidden('registration','', array('id' => 'registration')) }}
	    {{ Form::hidden('whichprocess','', array('id' => 'whichprocess')) }}
	    {{ Form::hidden('salaryflg','', array('id' => 'salaryflg')) }}
	    {{ Form::hidden('loan_flg','', array('id' => 'loan_flg')) }}
	    {{ Form::hidden('pettyflg','', array('id' => 'pettyflg')) }}
	    {{ Form::hidden('delflg','', array('id' => 'delflg')) }}
	    {{ Form::hidden('subject','', array('id' => 'subject')) }}
	    {{ Form::hidden('bankName','', array('id' => 'bankName')) }}
	    {{ Form::hidden('bname','', array('id' => 'bname')) }}
	    {{ Form::hidden('accNo','', array('id' => 'accNo')) }}
	    {{ Form::hidden('trans_flg','', array('id' => 'trans_flg')) }}
	    {{ Form::hidden('subject_type','', array('id' => 'subject_type')) }}
	    {{ Form::hidden('detail','', array('id' => 'detail')) }}
	    {{ Form::hidden('empid','', array('id' => 'empid')) }}
	    {{ Form::hidden('empname','', array('id' => 'empname')) }}
	    {{ Form::hidden('type','', array('id' => 'type')) }}
	    {{ Form::hidden('exptype1','', array('id' => 'exptype1')) }}
	    {{ Form::hidden('dateflg','', array('id' => 'dateflg')) }}
	    {{ Form::hidden('flgs','', array('id' => 'flgs')) }}
	    @php $count=count($get_det);  @endphp
	<!-- Start Heading -->
	<div class="row hline pm0">
		<div class="col-xs-12">
			@if($request->mainmenu == "pettycash")
				<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/pettycash.jpg') }}">
				<h2 class="pull-left pl5 mt10 CMN_mw150">{{ trans('messages.lbl_pettycash') }}</h2>
			@else
				<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/expenses.png') }}">
				<h2 class="pull-left pl5 mt10 CMN_mw150">{{ trans('messages.lbl_expenses') }}</h2>
			@endif
		</div>
	</div>
	<div class="box100per pr10 pl10 ">
		<div class="mt10 mb10">
			{{ Helpers::displayYear_MonthEst($account_period, $year_month, $db_year_month, $date_month, $dbnext, $dbprevious, $last_year, $current_year, $account_val) }}
		</div>
	</div>
	<div class="col-xs-12 mb5 mte5">
	<!-- Session msg -->
		@if(Session::has('success'))
			<div align="center" class="alertboxalign" role="alert">
				<p class="alert {{ Session::get('alert', Session::get('type') ) }}">
	            	{{ Session::get('success') }}
	          	</p>
			</div>
		@endif
		@php Session::forget('success'); @endphp
		@php $count=count($get_det);  @endphp
		<!-- Session msg -->
		<div class="col-xs-6" style="text-align: left;margin-left: -20px;">
			@if($request->mainmenu == "pettycash")
				<a href="javascript:fnregister('{{ $request->mainmenu }}','1');" class="btn btn-success box100"><span class="fa fa-plus"></span> {{ trans('messages.lbl_register') }}</a>
			@else
				<a href="javascript:fnregister('{{ $request->mainmenu }}','1');" class="btn btn-success box100"><span class="fa fa-plus"></span> {{ trans('messages.lbl_register') }}</a>
				@if($disabl=="disabled")    
					<a  style="color: white;" class="btn btn-gray {{ $disabl }}" 
						href="javascript:expensesexceldownload('{{ $request->mainmenu }}','{{ $date_month }}');" class="btn btn-primary box125">
						<span class="fa fa-download" ></span> {{ trans('messages.lbl_download') }}
					</a>
				@else
					<a  style="color: white;" class="btn btn-primary box125 {{ $disabl }}" 
						href="javascript:expensesexceldownload('{{ $request->mainmenu }}','{{ $date_month }}');" class="btn btn-primary box125">
						<span class="fa fa-download" ></span> {{ trans('messages.lbl_download') }}
					</a>
				@endif
			@endif
		</div>
		<div class="form-group pm0 pull-right moveleft nodropdownsymbol" id="moveleft">
			<div style="display: inline-block;" class="pull-right">
				@if(isset($get_det[0]['submit_flg']) && $get_det[0]['submit_flg'] == 1 && Session::get('userclassification') != 4)
					<a title="Edit" class="btn btn-gray" style="color: white;">
						<i class="fa fa-pencil-square-o" aria-hidden="true" style="color: white;"></i>
						{{ trans('messages.lbl_edit') }}
					</a>
				@else
					<a href="javascript:edit_view('{{ $count }}')" class="btn btn-warning" title="Edit">
						<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
						{{ trans('messages.lbl_edit') }}
					</a>
				@endif
			</div>
			@if($request->mainmenu == "expenses")
				<div style="display: inline-block;" class="mr10 pull-right">
					@if(isset($get_det[0]['submit_flg']) && $get_det[0]['submit_flg'] == 1 && Session::get('userclassification') != 4)
						<a class="btn btn-gray" title="Multiple Register" style="color: white;">
							<i class="fa fa-plus" aria-hidden="true" style="color: white;"></i>
								{{ trans('messages.lbl_multi_register') }}
						</a>
					@else
						<a href="javascript:multi_view('{{ $count }}', '{{ $transcount }}', 1)" 
							class="btn btn-success" title="Multiple Register">
							<i class="fa fa-plus" aria-hidden="true"></i>
								{{ trans('messages.lbl_multi_register') }}
						</a>
					@endif
				</div>
			@endif
			@if($request->mainmenu == "expenses")
				<div style="display: inline-block;" class="mr10 pull-right">
					@if(isset($get_det[0]['submit_flg']) && $get_det[0]['submit_flg'] == 1 && Session::get('userclassification') != 4)
						<a class="btn btn-gray" title="Total" style="color: white;">
							<i class="fa fa-plus" aria-hidden="true" style="color: white;"></i>
								{{ trans('messages.lbl_total') }}
						</a>
					@else
						<a href="javascript:multi_view('{{ $count }}', '{{ $transcount }}', 2)" class="btn btn-info" title="Total">
							<i class="fa fa-plus" aria-hidden="true"></i>
								{{ trans('messages.lbl_total') }}
						</a>
					@endif
				</div>
			@endif
		</div>
	</div>
	<!-- SEARCH -->
	<div style="top: 134px;position: fixed;" 
		@if ($request->searchmethod == 1 || $request->searchmethod == 2) 
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
							<label class="mt10">{{ Helpers::ordinalize($account_val) }} {{ trans('messages.lbl_period') }}</label>
						</li>
						<li>
							<span>
								@if($request->mainmenu == "pettycash")
									Petty Cash total amount
								@else
									{{ trans('messages.lbl_exptotamt') }}
								@endif
							</span>
						</li>
						<li class="mb10">
							<label class="pull-right pr10" style="font-size:18px;">
							@if($count>0)
								@if($request->mainmenu == "pettycash")
									@if(isset($exp_rsTotalAmount1))
										¥{{number_format($exp_rsTotalAmount1)}}
									@else
										¥ 0
									@endif
								@else
									@if(isset($totalexptra))
										¥{{number_format($totalexptra)}}
									@else
										¥ 0
									@endif
								@endif
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
						<div class="box100per mt5"></div>
					</li>
				</ul>
				<ul></ul>
			</div>
			<div class="hline">
			<ul>
				<li class="mt5 ml5">
					@if($request->mainmenu == "pettycash")
						@if($count>0)
							<a style="margin: -19px !important;padding-left: 18px;" 
								href="javascript:pettycash_download('{{ $request->mainmenu }}');">
								<i class="fa fa-arrow-down" aria-hidden="true"></i>Petty Cash Download</a>
						@else
							<a href="javascript:pettycash_nodownload();">
								<i class="fa fa-arrow-down" aria-hidden="true"></i>Petty Cash Download
							</a>
						@endif
					@else
						<a href="javascript:expenses_download('{{ $request->mainmenu }}');">
							<i class="fa fa-arrow-down" aria-hidden="true"></i>
							{{ trans('messages.lbl_expdownload') }}
						</a>
					@endif
				</li>
			</ul>
			</div>
			<div class="hline">
			<ul>
				<li class="mt10 ml5">
					<label><i class="fa fa-cogs" aria-hidden="true"></i> {{ trans('messages.lbl_setting') }}</label>
				</li>
				<li class="ml35">
					@if($request->mainmenu == "pettycash")
						@php
							$tbl_name = 'inv_set_transfermain'
						@endphp
					@else
						@php
							$tbl_name = 'dev_expensesetting'
						@endphp
					@endif
					<a class="mt20 btn-link" style="color:blue;" 
						href="javascript:settingpopupsinglefield('twotextpopup','{{ $tbl_name}}')" 
						onclick="fnselecttoggleclose();">
						{{ trans('messages.lbl_mainsubject') }}
					</a>
				</li>
				<li class="ml35">
					@if($request->mainmenu == "pettycash")
						{{--*/ $tbl_name = 'inv_set_transfersub' /*--}}
						{{--*/ $tbl_select = 'inv_set_transfermain' /*--}}
						<a class="mt20 btn-link" style="color:blue;" 
							href="javascript:settingpopupsinglefield('selectthreefieldDatas','{{ $tbl_name }}','{{ 2 }}','{{ $tbl_select }}','{{ 3 }}');" onclick="fnselecttoggleclose();">
							{{ trans('messages.lbl_subsubject') }}
						</a>
					@else
						{{--*/ $tbl_name = 'inv_set_expensesub' /*--}}
						{{--*/ $tbl_select = 'dev_expensesetting' /*--}}
						<a class="mt20 btn-link" style="color:blue;" 
							href="javascript:settingpopupsinglefield('selectthreefieldDatas','{{ $tbl_name }}','{{ 2 }}','{{ $tbl_select }}','{{ 2 }}');" onclick="fnselecttoggleclose();">
							{{ trans('messages.lbl_subsubject') }}
						</a>
					@endif
				</li>
			</ul>
			<div id="showpopup" class="modal fade">
				<div id="login-overlay">
					<div class="modal-content"></div>
				</div>
			</div>
			</div>
			<div>
				<ul class="mt15 ml5">
					<?php for ($mn = 0; $mn <count($mainCatDetails); $mn++) { ?>
						<li class="dropdown"><i class="fa fa-plus" aria-hidden="true"></i><span class="ml5">
							<a href = "javascript:gotomainexpenseshistory('{{ $mainCatDetails[$mn]['id'] }}','{{ $mainCatDetails[$mn]['mainCat'] }}','{{ $request->mainmenu }}','{{$request->selMonth}}','{{$request->selYear}}');" @if(strlen($mainCatDetails[$mn]['mainCat']) > 15) 
										title="{{ $mainCatDetails[$mn]['mainCat'] }}"
												@endif>@if(singlefieldlength($mainCatDetails[$mn]['mainCat'],15)){{singlefieldlength($mainCatDetails[$mn]['mainCat'],15)}}@else{{$mainCatDetails[$mn]['mainCat']}}@endif</a></span>
								<?php $subcount = 0;
							if (isset($subCatDetails[$mainCatDetails[$mn]['mainCat']])) {
							 	$subcount = count($subCatDetails[$mainCatDetails[$mn]['mainCat']]);
							}
							for ($sb = 0; $sb <$subcount; $sb++) { ?> 
								<ul class="ml25">
									<li><i class="fa fa-minus" aria-hidden="true"></i>
										<a href = "javascript:gotosubexphistory('{{ $mainCatDetails[$mn]['id'] }}','{{ $subCatDetails[$mainCatDetails[$mn]['mainCat']][$sb]['subId'] }}','{{ $subCatDetails[$mainCatDetails[$mn]['mainCat']][$sb]['subCat'] }}','{{ $mainCatDetails[$mn]['mainCat'] }}','{{ $request->mainmenu }}','{{$request->selMonth}}','{{$request->selYear}}');">{{$subCatDetails[$mainCatDetails[$mn]['mainCat']][$sb]['subCat']}}</a>
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
	<div class="mr10 ml10 mt10">
		<div class="minh200">
			<table class="tablealternate box100per">
				<colgroup>
				   <col width="3%">
				   <col width="4%">
				   <col width="11%">
				   <col width="11%">
				   @if($request->mainmenu == "pettycash")
				   <col width="11%">
				   <col width="8%">
				   <col width="8%">
				   <col width="10%">
				   @else
				   <col width="15%">
				   <col width="8%">
				   <col width="15%">
				   @endif
				   <col width="3%">
				   <col width="5%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
			   		<tr> 
				  		<th rowspan="2" class="tac">{{ trans('messages.lbl_sno') }}</th>
				  		<th rowspan="2" class="tac">{{ trans('messages.lbl_Date') }}</th>
				  		<th rowspan="2" class="tac">{{ trans('messages.lbl_mainsubject') }}</th>
				  		<th rowspan="2" class="tac">{{ trans('messages.lbl_subsubject') }}</th>
				  		<th rowspan="2" class="tac">{{ trans('messages.lbl_empName') }}</th>
				  		@if($request->mainmenu == "pettycash")
				  			<th rowspan="1" colspan="2" class="tac">{{ trans('messages.lbl_amount') }}</th>
				  		@else
				  			<th rowspan="2" class="tac">{{ trans('messages.lbl_expenses') }}</th>
            			@endif
				  		<th rowspan="2" class="tac">{{ trans('messages.lbl_remarks') }}</th>
				  		<th rowspan="2" class="tac">{{ trans('messages.lbl_bill') }}</th>
				  		<th rowspan="2" class="tac">
				  			<span id="edit_header">{{ trans('messages.lbl_edit') }}</span>
				  			<input type="checkbox" class="vam pb2 Multi_reg_checkbox" id="Chk_all" style="text-decoration:none; display:none;" title="Check All" onchange="checkAll(this)" name="Chk_all" >
				  		</th>
			   		</tr>
			   		@if($request->mainmenu == "pettycash")
				   		<tr>
	              			<th>{{ trans('messages.lbl_cash') }}</th> 
	              			<th>{{ trans('messages.lbl_expenses') }}</th> 
	            		</tr>
            		@endif
			   	</thead>
			   	<tbody>
			   	@if($count>0)
			   	{{--*/ $row = '0' /*--}}
			   	<?php
					$j=1;
					$temp="";
					$today_date = date('Y-m-d');
					$yenTotalAmount = 0;
					 ?>
			   	{{--*/ $transdate=array(); /*--}}
				@for ($i = 0; $i < count($get_det); $i++)
						@if($get_det[$i]['transaction_flg'] == 3)
							{{--*/ $transcount += 1; /*--}}
						@endif
						@if($request->mainmenu == "expenses")
							@if($get_det[$i]['carryForwardFlg'] != 1)
								{{--*/ $transcount += 1; /*--}}
							@endif
							@if($get_det[$i]['salaryFlg'] == 1)
								{{--*/ $transcount += 1; /*--}}
							@endif
						@endif
							{{--*/ $db_inserted_date = $get_det[$i]['insert_date']; /*--}} 
							{{--*/ $registered_date = strtotime($get_det[$i]['insert_date']); /*--}} 
							{{--*/ $future_date = strtotime($get_det[$i]['date']); /*--}} 
							{{--*/ $updated_date = strtotime($get_det[$i]['update_date']); /*--}} 
						@if($get_det[$i]['date'] != "")
								{{--*/ $loc = $get_det[$i]['date']; /*--}} 
						@endif
				{{--*/ $loc = $get_det[$i]['date'] /*--}}
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
							{{--*/ $incr = $incr + 1; /*--}} 
							{{--*/ $serialcolor = $PAGING+$incr; /*--}} 
							@if($serialcolor != $tempvar)
								<?php	if($rowclr==1){
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
					<tr <?php echo $style;?> >
						<td style="text-align: center;">{{($g_query->currentpage()-1) * $g_query->perpage() + $i + 1}}</td>
						<td style="{{ $style_td }}" align="center">
									@if($loc != $temp)
			   							{{--*/ $transferdate=$get_det[$i]['datedetail']; /*--}}
			   							{{--*/ $transdate=explode("-" , $transferdate); /*--}}
										@if($transdate[2] < 10)
											{{ $transferdate[9] }}日 
										@else
							 				{{ $transferdate[8].$transferdate[9] }}日
										@endif
									@endif
						</td>
							@if($request->mainmenu == "pettycash")
								@if(Session::get('languageval') == "en")
									@if($get_det[$i]['bank']!="Cash")
									<td>
										<a href="javascript:gotopettyhistory('{{$get_det[$i]['subject']}}','{{$get_det[$i]['bank']}}','{{$request->mainmenu}}','{{$request->selMonth}}','{{$request->selYear}}');" style="color: blue;">
										<?php 
												if (mb_strlen($get_det[$i]['bank'], 'UTF-8') >= 16) {
													$str = mb_substr($get_det[$i]['bank'], 0, 15, 'UTF-8');
													echo "<span title = '".$get_det[$i]['bank']."'>".$str."...</span>"; 
												} else {
													echo $get_det[$i]['bank'];
												} ?>
										</a>
									</td>
									@else
									<td>
										<a href="javascript:gotonamepettycash('{{$get_det[$i]['banknamevalue']}}','{{$get_det[$i]['bankaccno']}}','{{$get_det[$i]['bankname']}}','{{$get_det[$i]['del_flg']}}','{{$request->mainmenu}}','{{$request->selMonth}}','{{$request->selYear}}');" style="color: blue;">
											<?php 
												if (mb_strlen($get_det[$i]['banknickname']."-".$get_det[$i]['bankaccno'], 'UTF-8') >= 9) {
														echo $get_det[$i]['banknickname']."-".$get_det[$i]['bankaccno'] ;
													} else {
														echo $get_det[$i]['banknickname']."-".$get_det[$i]['bankaccno'] ;
												} ?>
										</a>
									</td>
									@endif
								@else
									@if($get_det[$i]['bank']!="Cash")
									<td>
										<a href="javascript:gotopettyhistory('{{$get_det[$i]['subject']}}','{{$get_det[$i]['bank']}}','{{$request->mainmenu}}','{{$request->selMonth}}','{{$request->selYear}}');" style="color: blue;">
										<?php 
												if (mb_strlen($get_det[$i]['bank'], 'UTF-8') >= 16) {
													echo $get_det[$i]['bank'];
												} else {
													echo $get_det[$i]['bank'];
												} ?>
										</a>
									</td>
									@else
									<td>
										<a href="javascript:gotonamepettycash('{{$get_det[$i]['banknamevalue']}}','{{$get_det[$i]['bankaccno']}}','{{$get_det[$i]['bankname']}}','{{$get_det[$i]['del_flg']}}','{{$request->mainmenu}}','{{$request->selMonth}}','{{$request->selYear}}');" style="color: blue;">
											<?php 
												if (mb_strlen($get_det[$i]['banknickname']."-".$get_det[$i]['bankaccno'], 'UTF-8') >= 9) {
														echo $get_det[$i]['banknickname']."-".$get_det[$i]['bankaccno'] ;
													} else {
														echo $get_det[$i]['banknickname']."-".$get_det[$i]['bankaccno'] ;
												} ?>
										</a>
									</td>
									@endif
								@endif
							@else
								@if($get_det[$i]['carryForwardFlg']!=1)
									@if($get_det[$i]['pettyFlg']!=1)
										@if($get_det[$i]['bank']!="Cash")
											@if($get_det[$i]['salaryFlg']==1)
											<td>
												<a href="javascript:gotoempnamehistory('{{ $get_det[$i]['empNo'] }}','{{ $get_det[$i]['EmpName'] }}','{{ $get_det[$i]['bankId'] }}','{{ $get_det[$i]['bankaccno'] }}','{{$request->mainmenu}}','{{$request->selMonth}}','{{$request->selYear}}');" style="color: blue;"
														@if(strlen($get_det[$i]['EmpName']) > 15) 
			    												title="{{ $get_det[$i]['EmpName'] }}"
					    										@endif>
					    								@if(singlefieldlength($get_det[$i]['EmpName'],15))
					    									{{singlefieldlength($get_det[$i]['EmpName'],15)}}
														@else
															{{$get_det[$i]['EmpName']}}
														@endif
												</a>
											</td>
											@else
											<td>
												<a class="btn-link" href="javascript:gotoexpensestransferhistory('{{$get_det[$i]['subject']}}','{{$get_det[$i]['salaryFlg']}}','{{$request->mainmenu}}','{{$request->selMonth}}','{{$request->selYear}}');" style="color: blue;"
													@if(strlen($get_det[$i]['bank']) > 14) 
		    												title="{{ $get_det[$i]['bank'] }}"
				    										@endif>
				    								@if(singlefieldlength($get_det[$i]['bank'],14))
				    									{{singlefieldlength($get_det[$i]['bank'],14)}}
													@else
														{{$get_det[$i]['bank']}}
													@endif
												</a>
											</td>
											@endif
										@else
											<?php 
												if(isset($get_det[$i]['banknickname'])) {
													$str = $get_det[$i]['banknickname'];
												} else {
													$str = "";
												}
											?>
											<td>
											<a href="javascript:gotoexpenses_history('{{ $get_det[$i]['bankname'] }}','{{ $get_det[$i]['bankaccno'] }}','{{ $str }}','{{ $request->mainmenu }}','{{$request->selMonth}}','{{$request->selYear}}');" style="color: blue;">
												<!-- @if(isset($get_det[$i]['banknickname']))
													{{ $get_det[$i]['banknickname'] }} - {{ $get_det[$i]['bankaccno'] }}
												@else
													{{ $str }} - {{ $get_det[$i]['bankaccno'] }}
												@endif -->
											</a>
											</td>
										@endif
									@else
										<td>
											<a href="javascript:gotopettycash('{{ $get_det[$i]['subject'] }}','{{ $get_det[$i]['pettyFlg'] }}','{{ $request->mainmenu }}','{{$request->selMonth}}','{{$request->selYear}}');" style="color: blue;">
													@if(isset($get_det[$i]['bank']) && mb_strlen($get_det[$i]['bank'], 'UTF-8') >= 16)
														{{ $get_det[$i]['bank'] }}
													@else
														{{ $get_det[$i]['bank'] }}
													@endif
											</a>
										</td>
									@endif
								@endif
							@endif
						<?php if (Session::get('languageval') == "en") { ?>
								<td style="width: 15%; word-wrap: break-word; border-top: 1px dotted black;"
									align="left">
									<?php 
									if($request->mainmenu == "pettycash") {
										if ($get_det[$i]['bank']!="Cash")  { ?>
											<a href="javascript:gotopettycashsubhistory1('{{ $get_det[$i]['subject'] }}','{{ $get_det[$i]['details'] }}','{{ $get_det[$i]['detail'] }}','{{ $get_det[$i]['bank'] }}','{{ $request->mainmenu }}','{{$request->selMonth}}','{{$request->selYear}}');">
											<font color="blue"><?php echo $get_det[$i]['detail'];
													if (isset($get_det[$i]['sub_eng']) && mb_strlen($get_det[$i]['sub_eng'], 'UTF-8') >= 15) {
																$str = mb_substr(ucwords($get_det[$i]['sub_eng']), 0, 14, 'UTF-8');
																echo "<span title = '".ucwords($get_det[$i]['sub_eng'])."'>".$str."...</span>"; 
															} else {
																if(isset($get_det[$i]['sub_eng'])) {
																	echo ucwords(strtolower($get_det[$i]['sub_eng'])) ;
																}
														}
											} else { ?>
												<a href="javascript:gotopettycashsubhistory('{{ $get_det[$i]['subject'] }}','{{ $get_det[$i]['bankname'] }}','{{ $get_det[$i]['bankaccno'] }}','{{ $get_det[$i]['transaction_flg'] }}','{{ $get_det[$i]['banknamevalue'] }}','{{ $get_det[$i]['del_flg'] }}','{{ $request->mainmenu }}','{{$request->selMonth}}','{{$request->selYear}}');">
											<font color="blue"><?php if ($get_det[$i]['transaction_flg']==1){
													if (isset($get_det[$i]['sub_eng']) &&mb_strlen($get_det[$i]['sub_eng'], 'UTF-8') >= 15) {
																$str = mb_substr(ucwords($get_det[$i]['sub_eng']), 0, 14, 'UTF-8');
																echo "<span title = '".ucwords($get_det[$i]['sub_eng'])."'>".$str."...</span>"; 
															} else {
																if(isset($get_det[$i]['sub_eng'])) {
																	echo ucwords(strtolower($get_det[$i]['sub_eng'])) ;
																}
														} 
													
													echo "Debit";	
												} else {
													echo "Credit";	
												}
										} ?>
									<?php
									}else {
										if ($get_det[$i]['pettyFlg']!=1)  {
										if ($get_det[$i]['bank']!="Cash") {
											if ($get_det[$i]['salaryFlg']==1) {
												$name ="transfer_history";
											}else {
												$name ="expenses_history";
											}?>
											<a href="javascript:gotosubhistory('{{ $name }}','{{ $get_det[$i]['subject'] }}','{{ $get_det[$i]['details'] }}','{{ $get_det[$i]['detail'] }}','{{ $get_det[$i]['salaryFlg'] }}','{{ $request->mainmenu }}','sub','{{$request->selMonth}}','{{$request->selYear}}');">
											<font color="blue"><?php echo $get_det[$i]['detail'];
													if ($get_det[$i]['salaryFlg'] == 1) {
														echo "Salary";
													} else {
													if (isset($get_det[$i]['sub_eng']) && mb_strlen($get_det[$i]['sub_eng'], 'UTF-8') >= 15) {
																$str = mb_substr(ucwords($get_det[$i]['sub_eng']), 0, 14, 'UTF-8');
																echo "<span title = '".ucwords($get_det[$i]['sub_eng'])."'>".$str."...</span>"; 
															} else {
																if(isset($get_det[$i]['sub_eng'])) {
																	echo ucwords(strtolower($get_det[$i]['sub_eng'])) ;
																}
														}
													}
											} else { ?>
											<?php if ($get_det[$i]['carryForwardFlg']!=1) {?>
												<a href="javascript:gotoexpenses1_history('{{ $get_det[$i]['bankname'] }}','{{ $get_det[$i]['bankaccno'] }}','{{ $get_det[$i]['banknickname'] }}','{{ $get_det[$i]['transaction_flg'] }}','{{ $request->mainmenu }}','{{$request->selMonth}}','{{$request->selYear}}');">
											<font color="blue"><?php if ($get_det[$i]['transaction_flg']==1){
													if (isset($get_det[$i]['sub_eng']) && mb_strlen($get_det[$i]['sub_eng'], 'UTF-8') >= 15) {
																$str = mb_substr(ucwords($get_det[$i]['sub_eng']), 0, 14, 'UTF-8');
																echo "<span title = '".ucwords($get_det[$i]['sub_eng'])."'>".$str."...</span>"; 
															} else {
																if(isset($get_det[$i]['sub_eng'])) {
																	echo ucwords(strtolower($get_det[$i]['sub_eng'])) ;
																}
														} 
													
													// echo "Debit";	
												} else if ($get_det[$i]['transaction_flg']==3){
													if($get_det[$i]['transfer_flg']==1){
														// echo "Transfer to"." ・"."Debit";
													} else {
														// echo "Transfer to"." ・"."Credit";	
													}
												} else {
													// echo "Credit";	
												}
												} else {
													if ($get_det[$i]['transaction_flg']==1){
													if (isset($get_det[$i]['sub_eng']) && mb_strlen($get_det[$i]['sub_eng'], 'UTF-8') >= 15) {
																$str = mb_substr(ucwords($get_det[$i]['sub_eng']), 0, 14, 'UTF-8');
																echo "<span title = '".ucwords($get_det[$i]['sub_eng'])."'>".$str."...</span>"; 
															} else {
																if(isset($get_det[$i]['sub_eng'])) {
																	echo ucwords(strtolower($get_det[$i]['sub_eng'])) ;
																}
														} 
													
													echo "Debit";	
												} else {
													echo "Credit";	
												}
												}
											} 
										} else {
												if( $get_det[$i]['del_flg'] != 1 ){ ?>
															<a href="javascript:gotopettycashsubhistoryexpenses('{{ $get_det[$i]['subject'] }}','{{ $get_det[$i]['pettyFlg'] }}','{{ $get_det[$i]['del_flg'] }}','{{ $request->mainmenu }}','{{$request->selMonth}}','{{$request->selYear}}');">
															<font color="blue">
											     	<?php	echo "Cash"; ?></font></a>
											   		<?php   } else { ?>
											  				 <a href="javascript:gotopettycashsubhistoryexpenses('{{ $get_det[$i]['subject'] }}','{{ $get_det[$i]['pettyFlg'] }}','{{ $get_det[$i]['del_flg'] }}','{{ $request->mainmenu }}','{{$request->selMonth}}','{{$request->selYear}}');">
															<font color="blue">
											     	<?php echo $get_det[$i]['detail']; ?></font></a>
											<?php     }
										} 
									} 
									?>
								</td>
								<?php } else { ?>
								<td style="width: 15%; word-wrap: break-word; border-top: 1px dotted black;"
									align="left">
									<?php 
									if($request->mainmenu == "pettycash") {
										if ($get_det[$i]['bank']!="Cash")  { ?>
											<a href="javascript:gotopettycashsubhistory1('{{ $get_det[$i]['subject'] }}','{{ $get_det[$i]['details'] }}','{{ $get_det[$i]['detail'] }}','{{ $get_det[$i]['bank'] }}','{{ $request->mainmenu }}','{{$request->selMonth}}','{{$request->selYear}}');">
											<font color="blue"><?php echo $get_det[$i]['detail'];
													if (isset($get_det[$i]['sub_jap']) && mb_strlen($get_det[$i]['sub_jap'], 'UTF-8') >= 15) {
																$str = mb_substr(ucwords($get_det[$i]['sub_jap']), 0, 14, 'UTF-8');
																echo "<span title = '".ucwords($get_det[$i]['sub_jap'])."'>".$str."...</span>"; 
															} else {
																if(isset($get_det[$i]['sub_jap'])) {
																	echo ucwords(strtolower($get_det[$i]['sub_jap'])) ;
																}
														}
											} else { ?>
												<a href="javascript:gotopettycashsubhistory('{{ $get_det[$i]['subject'] }}','{{ $get_det[$i]['bankname'] }}','{{ $get_det[$i]['bankaccno'] }}','{{ $get_det[$i]['transaction_flg'] }}','{{ $get_det[$i]['banknamevalue'] }}','{{ $get_det[$i]['del_flg'] }}','{{ $request->mainmenu }}','{{$request->selMonth}}','{{$request->selYear}}');">
											<font color="blue"><?php if ($get_det[$i]['transaction_flg']==1){
													if (isset($get_det[$i]['sub_jap']) &&mb_strlen($get_det[$i]['sub_jap'], 'UTF-8') >= 15) {
																$str = mb_substr(ucwords($get_det[$i]['sub_jap']), 0, 14, 'UTF-8');
																echo "<span title = '".ucwords($get_det[$i]['sub_jap'])."'>".$str."...</span>"; 
															} else {
																if(isset($get_det[$i]['sub_jap'])) {
																	echo ucwords(strtolower($get_det[$i]['sub_jap'])) ;
																}
														} 
													
													// echo "引出";	
												} else {
													// echo "入金";	
												}
										} ?>
									<?php
									} else {
										if ($get_det[$i]['pettyFlg']!=1)  {
										if ($get_det[$i]['bank']!="Cash") {
											if ($get_det[$i]['salaryFlg']==1) {
												$name ="transfer_history";
											}else {
												$name ="expenses_history";
												}?>
											<a href="javascript:gotosubhistory('{{ $name }}','{{ $get_det[$i]['subject'] }}','{{ $get_det[$i]['details'] }}','{{ $get_det[$i]['detail'] }}','{{ $get_det[$i]['salaryFlg'] }}','{{ $request->mainmenu }}','sub','{{$request->selMonth}}','{{$request->selYear}}');">
											<font color="blue"><?php echo $get_det[$i]['detail'];
													if ($get_det[$i]['salaryFlg'] == 1) {
														echo "給料";
													} else {
													if (isset($get_det[$i]['sub_eng']) && mb_strlen($get_det[$i]['sub_eng'], 'UTF-8') >= 15) {
																$str = mb_substr(ucwords($get_det[$i]['sub_eng']), 0, 14, 'UTF-8');
																echo "<span title = '".ucwords($get_det[$i]['sub_eng'])."'>".$str."...</span>"; 
															} else {
																if(isset($get_det[$i]['sub_eng'])) {
																	echo ucwords(strtolower($get_det[$i]['sub_eng'])) ;
																}
														}
													}
											} else { ?>
											<?php if ($get_det[$i]['carryForwardFlg']!=1) {?>
												<a href="javascript:gotoexpenses1_history('{{ $get_det[$i]['bankname'] }}','{{ $get_det[$i]['bankaccno'] }}','{{ $get_det[$i]['banknickname'] }}','{{ $get_det[$i]['transaction_flg'] }}','{{ $request->mainmenu }}','{{$request->selMonth}}','{{$request->selYear}}');">
											<font color="blue"><?php if ($get_det[$i]['transaction_flg']==1){
													if (isset($get_det[$i]['sub_eng']) && mb_strlen($get_det[$i]['sub_eng'], 'UTF-8') >= 15) {
																$str = mb_substr(ucwords($get_det[$i]['sub_eng']), 0, 14, 'UTF-8');
																echo "<span title = '".ucwords($get_det[$i]['sub_eng'])."'>".$str."...</span>"; 
															} else {
																if(isset($get_det[$i]['sub_eng'])) {
																	echo ucwords(strtolower($get_det[$i]['sub_eng'])) ;
																}
														} 
													// echo "引出";	
												} else if ($get_det[$i]['transaction_flg']==3){
													if($get_det[$i]['transfer_flg']==1){
														// echo "送金"." ・ "."引出";
													} else {
														// echo "送金"." ・ "."入金";	
													}
												} else {
													// echo "入金";	
													}
												} else {
													if ($get_det[$i]['transaction_flg']==1){
													if (isset($get_det[$i]['sub_eng']) && mb_strlen($get_det[$i]['sub_eng'], 'UTF-8') >= 15) {
																$str = mb_substr(ucwords($get_det[$i]['sub_eng']), 0, 14, 'UTF-8');
																echo "<span title = '".ucwords($get_det[$i]['sub_eng'])."'>".$str."...</span>"; 
															} else {
																if(isset($get_det[$i]['sub_eng'])) {
																	echo ucwords(strtolower($get_det[$i]['sub_eng'])) ;
																}
														} 
													
													echo "引出";	
												} else {
													echo "入金";	
												}
												}
											} 
										} else {
											if( $get_det[$i]['del_flg'] != 1 ){ ?>
															<a href="javascript:gotopettycashsubhistoryexpenses('{{ $get_det[$i]['subject'] }}','{{ $get_det[$i]['pettyFlg'] }}','{{ $get_det[$i]['del_flg'] }}','{{ $request->mainmenu }}','{{$request->selMonth}}','{{$request->selYear}}');">
															<font color="blue">
											     	<?php	echo "現金"; ?></font></a>
											   		<?php   } else { ?>
											  				 <a href="javascript:gotopettycashsubhistoryexpenses('{{ $get_det[$i]['subject'] }}','{{ $get_det[$i]['pettyFlg'] }}','{{ $get_det[$i]['del_flg'] }}','{{ $request->mainmenu }}','{{$request->selMonth}}','{{$request->selYear}}');">
															<font color="blue">
											     	<?php echo $get_det[$i]['detail']; ?></font></a>
											<?php   } 
										}
									}
										?></font>
								</td>
								<?php } ?>
						<td title="{{ empnameontitle($get_det[$i]['LastNames'],$get_det[$i]['FirstNames'],50) }}">	@if(isset($get_det[$i]['LastNames'])){{ empnamelength($get_det[$i]['LastNames'], $get_det[$i]['FirstNames'],25) }}@endif</td>
						@if($request->mainmenu == "pettycash")
						<td style="text-align: right;padding: 3px;padding-right: 5px !important;"><?php echo $get_det[$i]['cash']; ?></td>
						@endif
						<td style="text-align: right;padding: 3px;padding-right: 5px !important;"><?php echo $get_det[$i]['expenses']; ?></td>
						<td>{!! nl2br(e($get_det[$i]['remark_dtl'])) !!}</td>
						<td style="text-align: center;">
							@if($get_det[$i]['file_dtl'] != "")
								<?php
									$file_url = '../InvoiceUpload/Expenses/' . $get_det[$i]['file_dtl'];
								 ?>
								@if(isset($get_det[$i]['file_dtl']) && file_exists($file_url))
									<a href="javascript:filedownload('../../../../InvoiceUpload/Expenses','{{$get_det[$i]['file_dtl']}}');" title="Download"><i class="" aria-hidden="true"></i><img src="{{ URL::asset('resources/assets/images/download.png') }}" width="20px;" height="20px;" title="Download Bank Tansfer Image"></img></a>
								@else
								@endif
							@endif
						</td>
						<?php 
							if($request->mainmenu == "pettycash") {
								$copy_month_flg = $get_det[$i]['copy_month_flg'];
							} else {
								if($get_det[$i]['carryForward'] != 1){
									$currmon = "";											
									$month = "";											
									$cur_month = "";
									$yearval = "";
								   $month = $cur_month;  
								   $Year = $current_year; 
								   $monyear = $Year."-".$month;

								    // next month count

								   $orderdateval = explode('-', $get_det[$i]['datedetail']);
								   $yearval = $orderdateval[0];
								   $monthval  = $orderdateval[1];
								   $dateval  = $orderdateval[2];
								   //split the date
								   $monyear =  $yearval . '-' . $monthval;

								   $futuremonth = date ('n', strtotime ( '+1 month' , strtotime ( $monyear."-01" )));
								   $futureyear = date ('Y', strtotime ( '+1 month' , strtotime (  $monyear."-01" )));

								   $copycur_month = $get_det[$i]['datedetail'];
								   // get the db date

						 		   $nextCheck = date("Y-m", mktime(0, 0, 0, $monthval+1, 1, $yearval));

						 		   $orderdate = explode('-',$nextCheck);
								   $year = $orderdate[0];
						           $month   = $orderdate[1];	
						 			
				           		   $day_nextmonth =cal_days_in_month(CAL_GREGORIAN,$month, $year);
				           		  // print_r($dateval);print_r("<br/>");
				           		  // print_r($day_nextmonth);print_r("<br/>");
				           		   //count with all months date 
								    if($dateval <= $day_nextmonth) {
			            	    		$monthlyDate = strtotime("+1 month".$get_det[$i]['datedetail']);
	                    				$next_date = date("Y-m-d", $monthlyDate);
			           				 } else {
										$d = new DateTime($get_det[$i]['datedetail']);
										$d->modify( 'last day of next month' );
										$lastDateOfNxtMonth = $d->format( "Y-m-d" );
                    					$next_date = $lastDateOfNxtMonth;
			           				 }
			           				  $copy_month_flg = $get_det[$i]['copy_month_flg'];

								  } 
								} ?>

						<!-- <td style="width: 10%; word-wrap: break-word; border-top: 1px dotted black;"  class="tdcontenthistory" align="center">

								{{--*/ $id=$get_det[$i]['id']; /*--}}	
							 <a href="javascript:underconstruction();">{{ $futuremonth }}月</a>
							<a id="<?php echo 'edt'.$i ?>" style="cursor: default; display: none;" title="Edit" onclick="javascript:EditRecord('{{ $id }}','{{ $request->mainmenu }}');"><i class="fa fa-pencil" aria-hidden="true" style="color: black;"></i></a>
							<a id="<?php echo 'mul'.$i ?>" style="text-decoration: none;display: none;" title="Multi Register">
								<input type="checkbox" class="vam Multi_reg_checkbox" id="multi_reg" name="multi_reg[]" onchange="uncheckheader(this,<?php echo $get_det[$i]['id']; ?>)" value="<?php echo $get_det[$i]['id']; ?>" >
							</a>
							@if($get_det[$i]['detail'] != "")
										<a id="<?php echo 'tot'.$i ?>" style="text-decoration: none;display: none;" title="Total">
									<input type="checkbox" class="vam Multi_reg_checkbox" id="total" name="total[]" onclick="checkfunction(this.value,this)" value="<?php echo $get_det[$i]['expenses']; ?>" >
								</a>
							@endif
							</td> -->
							<td class="tac">
							@if($request->mainmenu == "pettycash")
								@if($get_det[$i]['currency_type'] !== "" && $get_det[$i]['del_flg'] == 1)
									@if($get_det[$i]['copy_month_flg'] != 0 || $get_det[$i]['copy'] != 1)
										<a style="text-decoration: none;" style="color: blue;"  href="javascript:gotoexpensescopy('{{ $get_det[$i]['id'] }}','{{ $request->mainmenu }}','','')" title="Copy">
												<img class="vam" src="{{ URL::asset('resources/assets/images/copy.png') }}" width="20" height="20"></a>
									@else
										@if($get_det[$i]['bank'] =="Cash")
										@else
										@endif
									@endif
									<a id="<?php echo 'edt'.$i ?>" style="text-decoration:none;display: none;" title="Edit" href="javascript:EditRecord('{{ $get_det[$i]['id'] }}','{{ $request->mainmenu }}');"><img class="vam" src="{{ URL::asset('resources/assets/images/edit.png') }}" width="20" height="20"></a>
								@else
									@if($get_det[$i]['copy_month_flg'] != "0" || $get_det[$i]['copy'] != "1")
										<a style="text-decoration: none;" style="color: blue;"  href="javascript:copyCashRecord('<?php echo $get_det[$i]['id'];?>','{{ $request->mainmenu }}')" title="Copy">
												<img class="vam" src="{{ URL::asset('resources/assets/images/copy.png') }}" width="20" height="20"></a>
									@else
									@endif
									<a id="<?php echo 'edt'.$i ?>" style="text-decoration:none;display: none;" title="Edit" href="javascript:EditCashRecord('<?php echo $get_det[$i]['id'];?>','{{ $request->mainmenu }}');"><img class="vam" src="{{ URL::asset('resources/assets/images/edit.png') }}" width="20" height="20"></a>
								@endif
							@else
								@if($get_det[$i]['subject'] != "" && $get_det[$i]['carryForwardFlg'] != 1   && $get_det[$i]['transfer_flg'] != 2)
									@if($get_det[$i]['currency_type'] !== "" && $get_det[$i]['del_flg'] == 1)
										@if($get_det[$i]['copy_month_flg'] != 0 || $get_det[$i]['copy'] != 1)

											<a style="text-decoration: none;" style="color: blue;"  href="javascript:gotoexpensescopy('{{ $get_det[$i]['id'] }}','{{ $request->mainmenu }}','','')" title="Copy">
												<img class="vam" src="{{ URL::asset('resources/assets/images/copy.png') }}" width="20" height="20"></a>

										@else
											@if($get_det[$i]['bank'] =="Cash")
												<a href="javascript:copyCashRecord('<?php echo $get_det[$i]['id'];?>','{{ $request->mainmenu }}');" style="color: blue;">{{ $futuremonth }}月</a>
											@else
												<a href="javascript:gotoexpensescopy('{{ $get_det[$i]['id'] }}','{{ $request->mainmenu }}',3,'{{ $next_date }}');" style="color: blue;">{{ $futuremonth }}月</a>
											@endif
										@endif
											<a id="<?php echo 'edt'.$i ?>" style="text-decoration:none; display: none;" title="Edit" href="javascript:EditRecord('{{ $get_det[$i]['id'] }}','{{ $request->mainmenu }}');"><img class="vam" src="{{ URL::asset('resources/assets/images/edit.png') }}" width="20" height="20"></a>
											<a id="<?php echo 'mul'.$i ?>" 
												style="text-decoration: none;display: none;" title="Multi Register">
												<input type="checkbox" class="vam Multi_reg_checkbox" 
													id="multi_reg" name="multi_reg[]" 
													style="margin: 0px;"
													onclick="checkfunction(this.value,this,'<?php echo $get_det[$i]['expenses'] ?>')"
													onchange="uncheckheader(this,<?php echo $get_det[$i]['id']; ?>)" value="<?php echo $get_det[$i]['id']; ?>" >
											</a>
									@else
										@if($get_det[$i]['copy_month_flg'] != "0" || $get_det[$i]['copy'] != "1")
											<a style="text-decoration: none;" style="color: blue;"  href="javascript:copyCashRecord('<?php echo $get_det[$i]['id'];?>','{{ $request->mainmenu }}')" title="Copy">
												<img class="vam" src="{{ URL::asset('resources/assets/images/copy.png') }}" width="20" height="20"></a>
										@else
											@if($get_det[$i]['bank'] =="Cash")
											  	@if($get_det[$i]['transfer_flg'] != '2')
											 		<a href="javascript:copyCashRecord('<?php echo $get_det[$i]['id'];?>','{{ $request->mainmenu }}','{{ $next_date }}');" style="color: blue;">{{ $futuremonth }}月</a>
											 	@endif
										 	@else
												<a href="javascript:gotoexpensescopy('{{ $get_det[$i]['id'] }}','{{ $request->mainmenu }}',3,'{{ $next_date }}');" style="color: blue;">{{ $futuremonth }}月</a>
											@endif
										@endif
												 <!-- <a href="javascript:underconstruction();">{{ $futuremonth }}月</a> -->
											<a id="<?php echo 'edt'.$i ?>" style="text-decoration:none; display:none;"  href="javascript:EditCashRecord('<?php echo $get_det[$i]['id'];?>','{{ $request->mainmenu }}');" title="Edit"><img class="vam" src="{{ URL::asset('resources/assets/images/edit.png') }}" width="20" height="20"></a>
											<?php
												$disablechk = "";
												$typechk = "checkbox"; 
												if($get_det[$i]['transaction_flg'] == "3" || 
														$get_det[$i]['detail'] == "") { 
													$disablechk = "disabled='disabled'"; 
													$typechk = "hidden"; 
												}
												?>
											<a id="<?php echo 'mul'.$i ?>" style="text-decoration: none;display: none;" title="Multi Register">
												<input type="<?php echo $typechk;?>"  <?php echo $disablechk;?> class="vam Multi_reg_checkbox" id="multi_reg" name="multi_reg[]" 
												onclick="checkfunction(this.value,this)"
												onchange="uncheckheader(this,<?php echo $get_det[$i]['id']; ?>)" 
												value="<?php echo $get_det[$i]['id']; ?>" >
											</a>
									@endif
								@else
									<a id="<?php echo 'edt'.$i ?>" style="text-decoration:none; display:none;"  href="javascript:EditCashRecord('<?php echo $get_det[$i]['id'];?>');" title="Edit"></a>
									<a id="<?php echo 'mul'.$i ?>" style="text-decoration:none; display:none;"  href="javascript:EditCashRecord('<?php echo $get_det[$i]['id'];?>');" title="Multi Register"></a>
								@endif
							@endif
							</td>
					</tr>
					{{--*/ $temp = $loc /*--}}
					{{--*/ $tempvar = $serialcolor /*--}}
              	@endfor
              	@else
              		<tr>
              			@if($request->mainmenu == "pettycash")
	              			<td class="text-center colred" colspan="10">
								{{ trans('messages.lbl_nodatafound') }}
							</td>
	              		@else
	              			<td class="text-center colred" colspan="9">
								{{ trans('messages.lbl_nodatafound') }}
							</td>
              			@endif
					</tr>
              	@endif
				</tbody>
			</table>
			@if($count>0)
			<table class="mt10 box100per" border="0" cellspacing="0" cellpadding="3" 
			style="border: none;">
					<?php
						if(isset($get_det[0]['totalamount']) && $get_det[0]['totalamount'] < 0) {
							$totalYenColor="color:blue";
						} else {
							$totalYenColor="color:blue";
						}
						if ($exp_rsTotalAmount1 < 0) {
							$totalrsColor="color:red";
						} else {
							$totalrsColor="color:blue";
						}
					?>
			<tr style="font-weight:bold;">
				<td width="3%" style="border: none;"></td>
				<td width="4%" style="border: none;"></td>
				<td width="15%" style="border: none;"></td>
				<td width="16%" style="border: none;padding-right: 28px;" align="right"><label>{{ trans('messages.lbl_totamt') }}</label></td>
				<td width="7.5%" align="right" style="padding-right: 5px !important;padding-bottom: 0px;"><label style="<?php echo $totalYenColor; ?>" >¥ @if($request->mainmenu == "expenses"){{ number_format((isset($exp_rsTotalAmount1)? $exp_rsTotalAmount1 : 0)) }}@else{{ number_format((isset($rsTotalAmount1)? $rsTotalAmount1 : 0)) }}@endif</label></td>
				@if($request->mainmenu == "expenses")
				<td width="2.5%" style="border: none;"></td>
				@else
				<td width="7.3%" align="right" style="padding-right: 5px !important;padding-bottom: 0px;"><label style="<?php echo $totalrsColor; ?>">¥ @if($request->mainmenu == "expenses"){{ number_format((isset($exp_rsTotalAmount1)? $exp_rsTotalAmount1 : 0)) }}@else{{ number_format((isset($exp_rsTotalAmount1)? $exp_rsTotalAmount1 : 0))}}@endif</label></td>
				@endif
				<td width="9.2%" style="border: none;"></td>
				<td width="3%" style="border: none;"></td>
				<td width="5%" style="border: none;" align="right">
					<a href="javascript:expensesaddreg('{{$request->mainmenu}}');" id="add" class="btns btn-success box70 h20 pull-right" style="text-decoration: none;display: none;"> 
						<span class="fa fa-plus"></span>
						 	{{ trans('messages.lbl_add') }}
					</a>
					<span id="viewtotal" style="display: block;">
					</span>
				</td>
			</tr>
			</table>
			<table class="mt10 box100per" border="0" cellspacing="0" cellpadding="3" style="border: none;">
			@if($request->mainmenu == "expenses")
			<!-- <tr style="font-weight:bold;">
				<td width="23.5%" style="border: none;padding-right: 6px;" align="right"><label>{{ trans('messages.lbl_thismonth') }}</label></td>
				@php
					$months_s = 0; 
					$months_s = $rsTotalAmount1 - $gett; 
				@endphp
				<td width="5.5%" align="right" style="padding: 3px;border: none;padding-right: 5px !important;"><label>¥ @if($request->mainmenu == "expenses"){{ number_format($months_s) }}@else{{ number_format($months_s) }}@endif</label></td>
				<td width="4.75%" align="right" style="padding: 3px;border: none;"><label></label></td>
				<td width="11%" style="border: none;" align="right">
						
				</td>
			</tr> -->
			@endif
					<?php 
						if($balan1 == 0) {
							$balance_color="color:blue";
						} else if($balan1 < 0) {
							$balance_color="color:red";
						} else if($balan1 > 10){
							$balance_color="color:green";
						}
					?>
			@if($request->mainmenu != "expenses")
			<tr style="font-weight:bold;">
				<td width="27%" style="border: none;padding-right: 6px;" align="right"><label>{{ trans('messages.lbl_balance') }}</label></td>
				<td width="6%" align="right" style="padding: 3px;border: none;padding-right: 5px;"><label style="<?php echo $balance_color; ?>">¥ @if($request->mainmenu == "expenses"){{ number_format($balan1) }}@else{{ number_format($balan1) }}@endif</label></td>
				<td width="4.75%" align="right" style="padding: 3px;border: none;"><label></label></td>
				<td width="13.75%" style="border: none;" align="right"></td>
			</tr>
			@endif
			</table>
			@if($request->mainmenu == "expenses")
			<table  class="mt10 box55per" border="0" cellspacing="0" cellpadding="3" style="border: none;">
			@php $curdate = date('Y-m'); @endphp
				@if($date_month < $curdate)
					@if($get_det[0]['submit_flg'] == "0")
						<tr class="mt20" style="border: none;">
							<td class="mt20" style="border: none;">
								<a href="javascript:funsubmit('1');" class="btn btn-success box100 pull-right">
									<span class="fa fa-plus"></span> {{ trans('messages.lbl_submit') }}</a>
							</td>
						</tr>
					@elseif($get_det[0]['submit_flg'] == "1" && Session::get('userclassification') != 4)
						<tr class="mt20" style="border: none;">
							<td style="border: none;">
								<a class="box100 pull-right btn btn-gray" style="color: white;cursor:default;font-weight:bold;"><span class="fa fa-plus"></span> {{ trans('messages.lbl_submit') }}</a>
							</td>
						</tr>
					@elseif($get_det[0]['submit_flg'] == "1" && Session::get('userclassification') == 4)
						<tr class="mt20" style="border: none;">
							<td style="border: none;">
								<a href="javascript:fnrevert('2');" class="btn btn-red box100 pull-right" style="color: white;">
								<span class="fa fa-plus"></span> 
								{{ trans('messages.lbl_revert') }}</a>
							</td>
						</tr>
					@endif
				@endif
			</table>
			<table  class="mt10 box100per" border="0" cellspacing="0" cellpadding="3" style="border: none;">
				@if($date_month <  $curdate && $get_det[0]['submit_flg'] == "1")
					<tr>
						<div class="mt10">
						<b><span style="color:#8B0000;font-size: 14px;" class="ml10">Note:</span></b>
						<b><span style="color:black;font-size: 14px;" class="ml10">The Record Was Submitted in The Period
							</span></b>
						<i><b><span style="color: #8B0000;">({{ $yearval }}  年 {{ $monthval }}  月分)</span></b></i>
						</div>
					</tr>
				@elseif($date_month<$curdate)
					<tr>
						<div class="mt10">
						<b><span style="color:#8B0000;font-size: 14px;" class="ml10">Note:</span></b>
						<b><span style="color:black;font-size: 14px;" class="ml10">After the final submit, The User cannot able to do Register, Edit And Copy Operation to the period
							</span></b>
						<i><b><span style="color: #8B0000;">({{ $yearval }}  年 {{ $monthval }}  月分)</span></b></i>
						</div>
					</tr>
				@endif
			</table>
			@endif
			@if($request->mainmenu == "pettycash")
				<table  class="mt10 box100per" border="0" cellspacing="0" cellpadding="3" style="border: none;">
				@php 
					$current_year_month = date('Y-m');
					$year = explode("-",$date_month); 
				@endphp
				@if( $date_month <  $current_year_month && $get_det[0]['submit_flg'] == "1")
					<tr>
						<div class="mt10">
						<b><span style="color:#8B0000;font-size: 14px;" class="ml10">Note:</span></b>
						<b><span style="color:black;font-size: 14px;" class="ml10">The Record Was Submitted in The Period
							</span></b>
						<i><b><span style="color: #8B0000;">({{ $year[0] }}  年 {{ $year[1] }}  月分)</span></b></i>
						</div>
					</tr>
				@endif
				</table>
			@endif
	</div>
		</div>
	</div>
	<div class="text-center pl13">
		@if(!empty($g_query->total()))
			<span class="pull-left mt24">
				{{ $g_query->firstItem() }} ~ {{ $g_query->lastItem() }} / {{ $g_query->total() }}
			</span>
		@endif 
		{{ $g_query->links() }}
		<div class="CMN_display_block flr mr8">
			{{ $g_query->linkspagelimit() }}
		</div>
	</div>
	@endif
	{{ Form::close() }}
<script type="text/javascript">
	$('li.dropdown').click(function() {
   		$(this).children('ul').toggle();
	});
</script>
</article>
</div>
{{ Form::open(array('name'=>'frmexpensedownloadindex', 
						'id'=>'frmexpensedownloadindex', 
						'url' => 'Expenses/pettycashdownload?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	{{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
{{ Form::close() }}
{{ Form::open(array('name'=>'frmexpensesdownloadindex', 
						'id'=>'frmexpensesdownloadindex', 
						'url' => 'Expenses/download?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	{{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
{{ Form::close() }}
{{ Form::open(array('name'=>'frmexpensesexceldownload', 
						'id'=>'frmexpensesexceldownload', 
						'url' => 'Expenses/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
{{ Form::hidden('actionName', '', array('id' => 'actionName')) }}
{{ Form::hidden('selYearMonth', '', array('id' => 'selYearMonth')) }}
{{ Form::close() }}
@endsection

