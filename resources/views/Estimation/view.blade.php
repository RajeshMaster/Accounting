@extends('layouts.app')
@section('content')
@php use App\Http\Common; @endphp
@php use App\Http\Helpers; @endphp
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
<style type="text/css">
	.alertboxalign {
    	margin-bottom: -60px !important;
	}
	.alert {
		margin-top: 10px;
	    display:inline-block !important;
	    height:30px !important;
	    padding:5px !important;
	}
</style>
{{ HTML::script('resources/assets/js/estimation.js') }}
{{ HTML::style('resources/assets/css/bootstrap.min.css') }}
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::style('resources/assets/css/switch.css') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper " data-category="sales sales_sub_1">
	{{ Form::open(array('name'=>'frmEstimationView', 
						'id'=>'frmEstimationView', 
						'url' => 'Invoice/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('invoiceid', '', array('id' => 'invoiceid')) }}
		{{ Form::hidden('estimateid', $get_data[0]->user_id, array('id' => 'estimateid')) }}
		{{ Form::hidden('copyflg', '', array('id' => 'copyflg')) }}
		{{ Form::hidden('estimate_id', '', array('id' => 'estimate_id')) }}
		{{ Form::hidden('editflg', '', array('id' => 'editflg')) }}
		{{ Form::hidden('editid', '', array('id' => 'editid')) }}
		{{ Form::hidden('filter', $request->filter, array('id' => 'filter')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	    {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	    {{ Form::hidden('sorting', $request->sorting, array('id' => 'sorting')) }}
		{{ Form::hidden('lastsortvalue', $request->lastsortvalue, array('id' => 'lastsortvalue')) }}
		{{ Form::hidden('lastordervalue', $request->lastordervalue, array('id' => 'lastordervalue')) }}
		{{ Form::hidden('ordervalue', $request->ordervalue, array('id' => 'ordervalue')) }}
		{{ Form::hidden('totalrecords', $totalRec, array('id' => 'totalrecords')) }}
		{{ Form::hidden('currentRec', $currentRec, array('id' => 'currentRec')) }}
		{{ Form::hidden('frmestview', 1, array('id' => 'frmestview')) }}
		{{ Form::hidden('custid',(isset($get_data[0]->trading_destination_selection)) ? $get_data[0]->trading_destination_selection : '', array('id' => 'custid')) }}
		{{ Form::hidden('checkdefault', $request->checkdefault, array('id' => 'checkdefault')) }}
		{{ Form::hidden('backflgforinvoice', $request->backflgforinvoice, array('id' => 'backflgforinvoice')) }}
		{{ Form::hidden('sortOptn',$request->sortOptn , array('id' => 'sortOptn')) }}
		{{ Form::hidden('singlesearchtxt', $request->singlesearchtxt, array('id' => 'singlesearchtxt')) }}
	    {{ Form::hidden('estimateno', $request->estimateno, array('id' => 'estimateno')) }}
		{{ Form::hidden('companyname', $request->companyname, array('id' => 'companyname')) }}
		{{ Form::hidden('startdate', $request->startdate, array('id' => 'startdate')) }}
		{{ Form::hidden('enddate', $request->enddate, array('id' => 'enddate')) }}
		{{ Form::hidden('projecttype', $request->projecttype, array('id' => 'projecttype')) }}
		{{ Form::hidden('taxSearch', $request->taxSearch, array('id' => 'taxSearch')) }}
	    {{ Form::hidden('singlesearch', $request->singlesearch, array('id' => 'singlesearch')) }}
	<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box25 mt10" src="{{ URL::asset('resources/assets/images/estimate.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_estimation') }}</h2>
			<h2 class="pull-left mt15">・</h2>
			<h2 class="pull-left mt15">{{ trans('messages.lbl_Details') }}</h2>
		</div>
	</div>
	<!-- End Heading -->
	<div class="col-xs-12 pm0 pull-left mt10">
		<!-- Session msg -->
			@if(Session::has('success'))
				<div align="left" class="alertboxalign ml450" role="alert">
					<p class="alert {{ Session::get('alert', Session::get('type') ) }}">
		            {{ Session::get('success') }}
		          	</p>
				</div>
			@endif
			@php Session::forget('success'); @endphp
		<!-- Session msg -->
		<div class="col-xs-6 ml10 pm0 pull-left">
			<a href="javascript:fngotoindex('index','{{ $request->mainmenu }}');" 
				class="btn btn-info box80">
        		<span class="fa fa-arrow-left"></span>
        			{{ trans('messages.lbl_back') }}
        	</a>

			<a href="javascript:addedit('viewedit','{{$get_data[0]->id}}');"  class="btn btn-warning box80">
				<span class="fa fa-edit"></span>
					{{ trans('messages.lbl_edit') }}
			</a>

			<a href="javascript:addedit('copy','{{$get_data[0]->id}}');" class="btn btn-primary box80">
				<span class="fa fa-plus"></span>
					{{ trans('messages.lbl_copy') }}
			</a>
		</div>
		<div class="col-xs-2 text-right ml55">
				{{ Helpers::displayYearMon_view($search_flg,$totalRec,$currentRec,$date_month,$get_view,$curTime,$order,$sort,$get_data[0]->id) }}
		</div>
		<!-- SEARCH -->
        <div style="top: 185px;position: fixed;" @if ($request->searchmethod == 1 || $request->searchmethod == 2) 
                     class="CMN_fixed pm0" 
                   @else 
                     class="open CMN_fixed pm0 pr0" 
                   @endif 
                    id="styleSelector">
             <div class="selector-toggle">
              <a id="sidedesignselector" href="javascript:void(0)"></a>
          </div>
			<div style="background-color:#136E83;color: white;">
				<ul class="ml5">	
					<span>
						<li>
							<label class="mt10">{{ trans('messages.lbl_totamt') }}</label>
						</li>
						<li class="mb10">
							<label class="pull-right pr10" style="font-size:18px;">¥ {{ number_format($grandtotal) }}
								</label>
						</li>
					</span>
					<li class="theme-option ml6">
							<div class="box100per mt10">
							<div>
					</li>
					<li class="theme-option ml6">
							<div class="box100per mt10">
							<div>
					</li>
				</ul>
			</div>
                <ul>
                	<li>
                		<div class="box100per mt10">
							<div class="mt5 ml10 fa fa-plus">
								<a href="javascript:gotoinvoicecreatefrmview('{{ $get_data[0]->id }}','invoice');"><b>{{ trans('messages.lbl_reginvoice') }}</b></a>
							</div>
						</div>
                	</li>
                	<li>
                		<div class="box100per">
							<div class="mt5 ml10 fa fa-arrow-down">
								<a href="javascript:fnexceldownload( '{{ $get_data[0]->id }}', '{{ $request->mainmenu }}' );"><b>{{ trans('messages.lbl_createexcelest') }}</b></a>
							</div>
						</div>
                	</li>
                	<li>
                		<div class="box100per">
							<div class="mt5 ml10 fa fa-arrow-down">
								<a href="javascript:fnexceldownloadnew( '{{ $get_data[0]->id }}', '{{ $request->mainmenu }}' );"><b>{{ trans('messages.lbl_createexcelest+') }}</b></a>
							</div>
						</div>
                	</li>
                	<li>
                		<div class="box100per">
							<div class="mt5 ml10 fa fa-arrow-down">
								<a href="javascript:fncoverpopup('{{ $get_data[0]->trading_destination_selection }}');"><b>{{ trans('messages.lbl_coverletter') }}</b></a>
							</div>
						</div>
                	</li>
                </ul>
				<?php 
				$path= "resources/assets/uploadandtemplates/upload/Estimation";
				$files = glob($path . '/' . $get_data[0]->user_id . '*.pdf');
				if ( $files !== false )
				{
					$filecount = count( $files );
				}
				$i=1;
				foreach ($files as $readfile) {
					$setpath[$i]=$readfile;
					$i=$i+1;
				}
				if($filecount != ""){
					krsort($setpath);
				?>
				<div>
					<ul>
						<li>
							<label class="mt10">{{ trans('messages.lbl_pdfdownlist') }}</label>
						</li>
					<?php 
					$j=$filecount;
					for ($i = $filecount; $i >= 1; --$i) {
						if ($i == 1){
							$filename=$get_data[0]->user_id;
						} else {
							$filename=$get_data[0]->user_id."_".str_pad(($i-1) , 2, '0', STR_PAD_LEFT);
						}
						$filepath=$path."/".$filename.".pdf";
						?>	
						<li class="ml25">
						<i class="fa fa-check-circle-o" aria-hidden="true"></i>
						<a name="estimat" href="javascript:filedownload('<?php echo "../../../".$path; ?>','<?php echo $filename.".pdf"; ?>');" 

							style="font-size:12px;"> <?php echo $filename; ?></a>&nbsp;&nbsp;&nbsp;&nbsp;
							<?php if($i == $filecount) { ?>
								<img name="newinvoice" class="mt3" src="{{ URL::asset('resources/assets/images/newicon.gif') }}" style="width:34px;height:14px;vertical-align: top;">
							<?php }?>
						</li>
							<?php 
						$j--;
						} 
						?>
					</ul>
					<input type = "hidden" id = "estimatepdflinkid" name = "estimatepdflinkid" value = "">
				</div>
				<?php } ?>
				@if(isset($get_customer_data[0]->cover_letter) && $get_customer_data[0]->cover_letter!="")
				<div>
					<ul>
						<li>
						<?php $filePath=''; ?>
						<img name="newinvoice" class="ml5 vam box20 boxhei20 csrp" src="{{ URL::asset('resources/assets/images/coverletterdown.png') }}" style="width:25px;height:20px;vertical-align: top;" onclick="return downloadcoverletter('../../../resources/assets/uploadandtemplates/upload/Coverletter/','{{ $get_customer_data[0]->cover_letter }}');">
							<a class="csrp" href="javascript:downloadcoverletter('../../../resources/assets/uploadandtemplates/upload/Coverletter/','{{ $get_customer_data[0]->cover_letter }}');"><span class="mt5 csrp">{{ trans('messages.lbl_coverletter') }}</span></a>
						</li>
					</ul>
				</div>
				@endif
        <!-- END SEARCH -->
	</div>
	<div class="mr10 ml10 box100per">
		<div class="minh400">
			<div class="col-xs-12 mt10 pm0">
				<div class="col-xs-3 pm0" style="border :1px solid #136E83">
					<div class="col-xs-12 text-left clr_blue" style="background: #b0e0f2">
						<label class="black">{{ trans('messages.lbl_cusname') }}</label>
					</div>
					<?php if(isset($get_customer_data[0]->customer_name) && $get_customer_data[0]->customer_name!="") { ?>
					<div class="col-xs-12" style="background: #e5f4f9">
						<label class="fwn">{{ $get_customer_data[0]->customer_name }}</label>
					</div>
					<?php } ?>
					<?php if(isset($get_customer_data[0]->customer_address) && $get_customer_data[0]->customer_address!="") { ?>
					<div class="col-xs-12 pt10" style="background: #e5f4f9">
						<span class="fwn">{!! nl2br(e(($get_customer_data[0]->customer_address) ? $get_customer_data[0]->customer_address : '')) !!}</span>
						<?php if(isset($get_customer_data[0]->customer_contact_no) && $get_customer_data[0]->customer_contact_no!="") { ?>
						<?php echo "<br>"; ?>
						<span class="fwn">{{ $get_customer_data[0]->customer_contact_no }}</span>
						<?php } ?>
					</div>
					<?php } ?>
				</div>
				<div class="col-xs-3 pm0"></div>
				<div class="col-xs-4 pm0" style="border :1px solid #136E83;width: 27%">
					<div class="col-xs-12 pm0">
						<div class="col-xs-5 text-right clr_blue" style="background: #b0e0f2">
							<label class="fwn black">{{ trans('messages.lbl_estimateno') }}</label>
						</div>
						<div class="col-xs-7">
							<label class="brown">{{ $get_data[0]->user_id }}</label>
						</div>
					</div>
					<div class="col-xs-12 pm0">
						<div class="col-xs-5 text-right clr_blue" style="background: #b0e0f2">
							<label class="fwn black">{{ trans('messages.lbl_estdate') }}</label>
						</div>
						<div class="col-xs-7">
							{{ $get_data[0]->quot_date }}
						</div>
					</div>
					<div class="col-xs-12 pm0">
						<div class="col-xs-5 text-right clr_blue" style="background: #b0e0f2">
							<label class="fwn black">{{ trans('messages.lbl_cutoffmonth') }}</label>
						</div>
						<div class="col-xs-7">
							<?php
							if (session::get('setlanguageval')=="jp") {
								echo Common::fnGetDispMonthRecordeng($get_data[0]->tighten_month_selection); 
								$day =" Day";
								$lastday =" Last Day";
							} else {
								echo Common::fnGetDispMonthRecordjap($get_data[0]->tighten_month_selection); 	
								$day =" 日";
								$lastday =" 末日";	
							}
							
							if ($get_data[0]->cutoff_date_selection == 0) {
								echo $lastday;
							} else {
								echo " ".$get_data[0]->cutoff_date_selection . $day;
							}
							?>
						</div>
					</div>
					<div class="col-xs-12 pm0">
						<div class="col-xs-5 text-right clr_blue" style="background: #b0e0f2">
							<label class="fwn black">{{ trans('messages.lbl_billingmonth') }}</label>
						</div>
						<div class="col-xs-7">
							<?php
							if (session::get('setlanguageval')=="jp") {
								echo Common::fnGetDispMonthRecordeng($get_data[0]->billing_month_selection); 
								$day =" Day";
								$lastday =" Last Day";
							} else {
								echo Common::fnGetDispMonthRecordjap($get_data[0]->billing_month_selection); 	
								$day =" 日";
								$lastday =" 末日";	
							}
							
							if ($get_data[0]->billing_date_selection == 0) {
								echo $lastday;
							} else {
								echo " ".$get_data[0]->billing_date_selection . $day;
							}
							?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-12 mb15 mt15 pm0">
			<div class="col-xs-6 pm0">
				<div class="col-xs-2 pm0 text-left clr_blue">
					<label>{{ trans('messages.lbl_projecttitle') }} :</label>
				</div>
				<div class="col-xs-9 pm0" style="border-bottom: 1px solid #A7D4DD">
					<label>{{ $get_data[0]->project_name }}</label>
				</div>
			</div>
			<div class="col-xs-6">
				<div class="box22per fll text-left clr_blue">
					<label>{{ trans('messages.lbl_projecttype') }} :</label>
				</div>
				<div class="col-xs-9 pm0" style="border-bottom: 1px solid #A7D4DD">
					<label>{{ isset($get_estimate_project_type[0]->ProjectType) ? $get_estimate_project_type[0]->ProjectType : "" }}</label>
				</div>
			</div>
			</div>

			<div class="mr10">
		<div class="minh400">
			<table class="tablealternate box100per">
				<colgroup>
				   <col width="25%">
				   <col width="6%">
				   <col width="10%">
				   <col width="12%">
				   <col width="30%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader fwb tac"> 
				  		<th class="tac">{{ trans('messages.lbl_workspec') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_quantity') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_unitprice') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_amount') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_remarks') }}</th>
			   		</tr>
			   	</thead>
          		<tbody>
      			@foreach($get_data as $key => $value)
      				@php
	      				$rowcount = $key+1;
	      				$workloop = "work_specific"; 
	      				$quantityloop = "quantity"; 
						$unit_priceloop = "unit_price"; 
						$amountloop = "amount"; 
						$remarksloop = "remarks";
      				@endphp
	          		@if(empty($get_data->amount) && empty($get_data->work_specific) && empty($get_data->remarks))
	      				@php  $data = 1; @endphp
	      			@else 
	      				@php $data = count($value->$amountloop); @endphp
		      		@endif
          			@for($i=1;$i<=$data;$i++)
          				<tr>
          					<td>
          						{{ ($value->$workloop) ? $value->$workloop : '' }}
          					</td>
          					<td class="text-center">
          						<?php 
          						if($value->$quantityloop!="") {
	          						$dotOccur = strpos($value->$quantityloop, "."); 
	          						if ($dotOccur) {
	          							echo $value->$quantityloop;
	          						} else {
	          							echo $value->$quantityloop.".0";
	          						}
	          					}
          						?>
          					</td>
          					<td class="text-right">
          						@if($value->$unit_priceloop<0)
          							<div style= "color: red">{{ isset($value->$unit_priceloop) ? $value->$unit_priceloop : '' }}</div>
          						@else
          							{{ ($value->$unit_priceloop) ? $value->$unit_priceloop : '' }}
          						@endif
          					</td>
          					<td class="text-right">
          						@if($value->$amountloop<0)
          						<div style= "color: red">{{ isset($value->$amountloop) ? $value->$amountloop : '' }}</div>
          						@else
          						{{ ($value->$amountloop) ? $value->$amountloop : '' }}
          						@endif
          					</td>
          					<td>
          						{!! nl2br(e(($value->$remarksloop) ? $value->$remarksloop : '')) !!}
          					</td>
          				</tr>
	          			@if($rowcount == $amtcount || empty($rowcount))
	          				@php 
	          					$data = 15 - ($amtcount-1);
		          				$value->$amountloop = "";
		          				$value->$remarksloop = "";
		          				$value->$unit_priceloop = "";
		          				$value->$quantityloop = "";
		          				$value->$workloop = "";
	          				@endphp
	          			@endif
          			@endfor
      			@endforeach
          				<tr>
          					<td style="border:hidden;border-top: 1px solid lightgrey;background: white"></td>
          					<td style="border:hidden;border-top: 1px solid lightgrey;background: white"></td>
          					<td class="tar"  style="background: #b0e0f2">{{ trans('messages.lbl_subtotal') }}</td>
          					<td class="tar" style="background: #e5f4f9">
          						{{ isset($get_data[0]->totalval)?$get_data[0]->totalval:0 }}</td>
          					<td style="border:hidden;border-top: 1px solid lightgrey;border-left: 1px solid lightgrey;background: white"></td>
          				</tr>
          				<tr>
          					<td style="border:hidden;background: white"></td>
          					<td style="border:hidden;background: white"></td>
          					<td class="tar"  style="background: #b0e0f2">{{ trans('messages.lbl_consumptiontax') }}</td>
          					<td class="tar" style="background: #e5f4f9">{{ number_format($dispval) }}</td>
          					<td style="border:hidden;border-left: 1px solid lightgrey;background: white"></td>
          				</tr>
          				<tr>
          					<td style="border:hidden;background: white"></td>
          					<td style="border:hidden;background: white"></td>
          					<td class="tar"  style="background: #b0e0f2">{{ trans('messages.lbl_esttotamt') }}</td>
          					<td class="tar fwb" style="background: #e5f4f9">{{ number_format($grandtotal) }}</td>
          					<td style="border:hidden;border-left: 1px solid lightgrey;background: white"></td>
          				</tr>
				</tbody>
			</table>
		</div>
		<?php $noticecnt=0; ?>
		<?php  for($i = 1; $i <= 5; $i++) {
			$special_ins = "special_ins".$i; 
			if($get_data[0]->$special_ins!="") {
 				$noticecnt=1;
			}
				
		} ?>
		<?php if($noticecnt=="1") { ?>
		<div class="inline-block col-xs-4">
			<div class="box11per ml100 clr_blue text-right">
				<label>{{ trans('messages.lbl_notices') }}</label>
			</div>
			<div class="ml110 text-left">
				@for ($i = 1; $i <= 5; $i++)
				<div>
					@php
						$special_ins = "special_ins".$i;
					@endphp
					@if(!empty($get_data[0]->$special_ins))
						<span>{{ $i.") " }}{{ isset($get_data[0]->$special_ins) && $get_data[0]->$special_ins !="" ? $get_data[0]->$special_ins:"NILL" }}</span>
					@endif
				</div>
				@endfor
			</div>
		</div>
		<?php } ?>
		<?php if(!empty($get_data[0]->memo)){  ?>
		<div class="inline-block col-xs-7">
			<div class=" clr_blue ml80 box70per">
				<label>{{ trans('messages.lbl_memo') }}</label>
			</div>
			<div class="ml90 text-left">
				@if(isset($get_data[0]->memo))
						{!! nl2br(e($get_data[0]->memo)) !!}
				@endif
			</div>
		</div>
		<?php } ?>
		</div>
	</div>
	{{ Form::close() }}
	<div id="coverpopup" class="modal fade">
		<div id="login-overlay">
			<div class="modal-content">
				<!-- Popup will be loaded here -->
			</div>
		</div>
	</div>
</article>
</div>
@endsection