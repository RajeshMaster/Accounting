@extends('layouts.app')
@section('content')
<?php use App\Http\Helpers; ?>
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
	  $(document).ready(function() {
    	setDatePicker("startdate");
		setDatePicker("enddate");
  });
	function mulclick(divid){
	    if($('#'+divid).css('display') == 'block'){
	      document.getElementById(divid).style.display = 'none';
	      document.getElementById(divid).style.height= "278px";
	    }else {
	      document.getElementById(divid).style.display = 'block';
	    }
  }
</script>
<style type="text/css">
	.alertboxalign {
    	margin-bottom: -60px !important;
	}
	.alert {
		margin-top: 5px;
	    display:inline-block !important;
	    height:30px !important;
	    padding:5px !important;
	}
	.fb{
		color: gray !important;
	}
	.sort_asc {
		background-image:url({{ URL::asset('resources/assets/images/upArrow.png') }}) !important;
	}
	.sort_desc {
		background-image:url({{ URL::asset('resources/assets/images/downArrow.png') }}) !important;
	}
	/* Dropdown Button */
	.dropbtn {
	    background-color: #4CAF50;
	    color: white;
	    padding: 16px;
	    font-size: 16px;
	    border: none;
	    cursor: pointer;
	}

	/* The container <div> - needed to position the dropdown content */
	.dropdown {
	    position: relative;
	    display: inline-block;
	}

	/* Dropdown Content (Hidden by Default) */
	.dropdown-content {
	    display: none;
	    position: absolute;
	    background-color: #f9f9f9;
	    min-width: 160px;
	    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
	    z-index: 1;
	}

	/* Links inside the dropdown */
	.dropdown-content a {
	    color: black;
	    padding: 5px 7px;
	    text-decoration: none;
	    display: block;
	}

	/* Change color of dropdown links on hover */
	.dropdown-content a:hover {background-color: #e5f4f9}

	/* Show the dropdown menu on hover */
	.dropdown:hover .dropdown-content {
	    display: block;
	}

	/* Change the background color of the dropdown button when the dropdown content is shown */
	.dropdown:hover .dropbtn {
	    background-color: #3e8e41;
	}
</style>
{{ HTML::script('resources/assets/js/estimation.js') }}
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/multisearchvalidation.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::style('resources/assets/css/switch.css') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper " data-category="sales sales_sub_1">
	{{ Form::open(array('name'=>'frmestimationindex', 
						'id'=>'frmestimationindex', 
						'url' => 'Estimation/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('filter', $request->filter, array('id' => 'filter')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	    {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('topclick', $request->topclick, array('id' => 'topclick')) }}
		{{ Form::hidden('sorting', $request->sorting, array('id' => 'sorting')) }}
		{{ Form::hidden('lastsortvalue', $request->lastsortvalue, array('id' => 'lastsortvalue')) }}
		{{ Form::hidden('lastordervalue', $request->lastordervalue, array('id' => 'lastordervalue')) }}
		{{ Form::hidden('ordervalue', $request->ordervalue, array('id' => 'ordervalue')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
		{{ Form::hidden('editflg', $request->editflg, array('id' => 'editflg')) }}
		{{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
		{{ Form::hidden('estimate_id', $request->estimate_id, array('id' => 'estimate_id')) }}
		{{ Form::hidden('cust_id', $request->cust_id, array('id' => 'cust_id')) }}
		{{ Form::hidden('currentRec', '', array('id' => 'currentRec')) }}
		<!-- For invoice -->
		{{ Form::hidden('invoiceid', '', array('id' => 'invoiceid')) }}
		{{ Form::hidden('estflg', 1, array('id' => 'estflg')) }}
		{{ Form::hidden('sendmailfrom', 'Estimation', array('id' => 'sendmailfrom')) }}
		{{ Form::hidden('companynameClick', $request->companynameClick, array('id' => 'companynameClick')) }}
		{{ Form::hidden('estimatestatus', '', array('id' => 'estimatestatus')) }}
		{{ Form::hidden('estimatestatusid', '', array('id' => 'estimatestatusid')) }}
		{{ Form::hidden('estid', '', array('id' => 'estid')) }}
		{{ Form::hidden('checkdefault', '', array('id' => 'checkdefault')) }}
		<input type="hidden" name="searchmethod" id="searchmethod" value="<?php echo $searchmethod; ?>">
	<!-- Start Heading -->
	<div class="row hline">
	<div class="col-xs-12">
			<img class="pull-left box25 mt10" src="{{ URL::asset('resources/assets/images/estimate.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_estimationlist') }}</h2>
		</div>
	</div>
	<div class="box100per pr10 pl10 mt10">
		<div class="mt10 mb10">
		<?php if($hideyearbar!="1") { ?>
			{{ Helpers::displayYear_MonthEst($account_period, $year_month, $db_year_month, $date_month, $dbnext, $dbprevious, $last_year, $current_year, $account_val) }}
		<?php } ?>
		</div>
	</div>
	<!-- End Heading -->
	<div class="col-xs-12 pm0 pull-left">
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
		<div class="col-xs-6 ml10 pm0 pull-left">
			<a href="javascript:addedit('add','');" class="btn btn-success box100"><span class="fa fa-plus"></span> {{ trans('messages.lbl_estregister') }}</a>
		</div>
		<div class="col-xs-12 pm0 pull-left">
			<div class="box55per pm0 CMN_display_block pull-left">
				<a class="btn btn-link {{ $disabledall }}" href="javascript:filter('1');"> {{ trans('messages.lbl_all') }} </a>
				<span>|</span>
				<a class="btn btn-link {{ $disabledestimates }}" href="javascript:filter('2');"> {{ trans('messages.lbl_estimates') }} </a>
				<span>|</span>
				<a class="btn btn-link {{ $disabledapproved }}" href="javascript:filter('3');"> {{ trans('messages.lbl_approved') }} </a>
				<span>|</span>
				<a class="btn btn-link {{ $disabledunused }}" href="javascript:filter('4');"> {{ trans('messages.lbl_unused') }} </a>
				<span>|</span>
				<a class="btn btn-link {{ $disabledsend }}" href="javascript:filter('5');"> {{ trans('messages.lbl_sent') }} </a>
			</div>
			<div class=" pm0 pr12">
				<div class="form-group pm0 pull-right moveleft nodropdownsymbol" id="moveleft">
				<a href="javascript:clearsearch()" title="Clear Search">
            		<img class="pull-left box30 mr5 " src="{{ URL::asset('resources/assets/images/clearsearch.png') }}">
          		</a>
					{{ Form::select('estimationsort', [null=>''] + $estimationsortarray, $request->sorting,
	                            array('class' => 'form-control'.' ' .$request->sortstyle.' '.'CMN_sorting pull-right',
	                           'id' => 'estimationsort',
	                           'style' => $sortMargin,
	                           'name' => 'estimationsort'))
	                }}
	            </div>
			</div>
		</div>
		 <!-- SEARCH -->
        <div style="top: 134px;position: fixed;" @if ($hideyearbar == 1 && $request->companynameClick == "") 
                     class="open CMN_fixed pm0" 
                   @else 
                     class="CMN_fixed pm0 pr0" 
                   @endif 
                    id="styleSelector">
             <div class="selector-toggle">
              <a id="sidedesignselector" href="javascript:void(0)"></a>
          </div>
			<div style="background-color:#136E83;color: white;">
				<ul class="ml5">	
					<span>
						<li class="pt5">
			  				<span class="">{{ trans('messages.lbl_targetdate') }}</span>
			  				<span class="">{{ " : " }}</span>
			  				<span class=" ml5">{{ str_replace('-',"/", $date_month) }}</span>
						</li>
						<li>
							<span>{{ trans('messages.lbl_totamt') }}</span>
						</li>
						<li>
							<label class="pull-right pr10" style="font-size:18px;">¥ {{ number_format($totval) }} 
								</label>
						</li>
						<li>
							<label class="pull-right pr10">   </label>
						</li>
					</span>
					<li class="theme-option ml6">
							<div class="box100per mt10">
							<div>
					</li>
				</ul>
			</div>
            <ul>
              <span>
                  <li style="">
                      <p class="selector-title">{{ trans('messages.lbl_search') }}</p>
                  </li>
              </span>
                <li class="theme-option ml6">
                  <div class="box100per mt5" onKeyPress="return checkSubmitsingle(event)">
                    {!! Form::text('singlesearchtxt', $request->singlesearchtxt,
                          array('','class'=>' form-control box80per pull-left','style'=>'height:30px;','id'=>'singlesearchtxt')) !!}

                    {{ Form::button('<i class="fa fa-search" aria-hidden="true"></i>', 
                        array('class'=>'ml5 mt2 pull-left search box15per btn btn-info btn-sm', 
                              'type'=>'button',
                              'name' => 'advsearch',
                              'onclick' => 'javascript:return fnSingleSearch()',
                              'style'=>'border: none;' 
                              )) }}
                  <div>
                </li>
            </ul>
            <div class="mt5 ml10 pull-left mb5">
              <a href="#demo" onclick="mulclick('demo');" class="" style="font-family: arial, verdana;">{{ trans('messages.lbl_multi_search') }}
              </a>
            </div>
            <ul id="demo" @if ($hideyearbar == 1 && $request->searchmethod == 2)  class="collapse in ml5 pull-left" 
                          @else class="collapse ml5 pull-left"  @endif>
                <li class="theme-option" onKeyPress="return checkSubmitmulti(event)">
                  <span class="mt2" style="font-family: arial, verdana;">{{ trans('messages.lbl_estimateno') }}</span>
                  <div class="mt5 box100per">
                      {!! Form::text('estimateno', $request->estimateno,
                         array('','id' => 'estimateno','style'=>'height:30px;','class'=>'form-control box93per')) !!}
                  </div>
                  <span class="mt2" style="font-family: arial, verdana;">{{ trans('messages.lbl_customer') }}</span>
                  <div class="mt5 box100per">
                      {!! Form::text('companyname', $request->companyname,
                         array('','id' => 'companyname','style'=>'height:30px;','class'=>'form-control box93per')) !!}
                  </div>
                  <span class="mt2" style="font-family: arial, verdana;">{{ trans('messages.lbl_daterange') }}</span>
                  <div class="mt5 box100per fll">
                     	{!! Form::text('startdate', $request->startdate,
                                array('',
                                    'id'=>'startdate',
                                    'style="font-size:13px;"', 'data-placement'=>'left', 
                                    'onKeyPress'=>'return event.charCode >= 48 && event.charCode <= 57',
                                    'class'=>'form-control box40per pull-left startdate'
                                    )) !!}
	                    <label class="ml1 mt8 fa fa-calendar fa-lg pull-left" for="startdate" aria-hidden="true"></label>
	                    {!! Form::text('enddate', $request->enddate,
	                                 array('',
	                                 'style="font-size:13px;"', 'data-placement'=>'left', 
	                                 'onKeyPress'=>'return event.charCode >= 48 && event.charCode <= 57',
	                                 'class'=>'form-control box40per pull-left enddate',
	                                 'id'=>'enddate')) !!}
	                    <label class="ml1 mt8 fa fa-calendar fa-lg" for="enddate" aria-hidden="true"></label>
                  </div>
                  <div class="mt2 box100per" style="font-family: arial, verdana;">{{ trans('messages.lbl_projecttype') }}</div>
                  <div class="mt5 box100per">
						{{ Form::select('projecttype',[null=>'']+["b"=>'ALL']+ $prjtypequery,'', 
										array('name' => 'projecttype',
											  'id'=>'projecttype',
											  'style="min-width:40%;width : auto;background : white"',
											  'class'=>'pl5 box10per'))}}
						{{ Form::select('taxSearch',[null=>'']+ $taxarray,'', 
										array('name' => 'taxSearch',
											  'id'=>'taxSearch',
											  'style="min-width:40%;width : auto;background : white"',
											  'class'=>'pl5 box10per'))}}
                  </div>
            <div class="mt5 mb6">
                 {{ Form::button(
                     '<i class="fa fa-search" aria-hidden="true"></i> '.trans('messages.lbl_search'),
                     array('class'=>'mt10 btn btn-info btn-sm',
                     		'onclick' => 'javascript:return fnMultiSearch()',
                           	'type'=>'button')) 
                 }}
            </div>
                </li>
            </ul>
        <!-- END SEARCH -->
	</div>
	<div class="mr10 ml10">
		<div class="minh300">
			<table class="tablealternate box100per">
				<colgroup>
					<col width="5%">
					<col width="10%">
					<col width="">
					<col width="13%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader fwb tac"> 
			   			<th class="tac">{{ trans('messages.lbl_sno') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_estimateno') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_Details') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_estamount') }}</th>
			   		</tr>
			   	</thead>
			   	<tbody>
			   	<?php $totval=0; $i=0; ?>
				@forelse($Estquery as $key => $data)
			   	<?php //print_r($data); print_r("<br>"); print_r("<br>"); ?>
				<tr>
					<td class="text-center">
						{{ ($Estquery->currentpage()-1) * $Estquery->perpage() + $i + 1 }}
					</td>
					<td class="tal pr10 vat pt5">
						<div class="">
							<label class="pm0 vam" style="color:#136E83;">
								{{ $data->user_id }}
							</label>
						</div>
						<div>
							<span class="estStatusDIV_New_1">
								@if($data->classification==0 && $data->del_flg==0)
									<span class="blue">{{ trans('messages.lbl_estimates') }}</span>
								@elseif($data->classification==1 && $data->del_flg==0)
									<span class="orange">{{ trans('messages.lbl_approved') }}</span>
								@elseif($data->classification==2 && $data->del_flg==0)
									<span class="green">{{ trans('messages.lbl_sent') }}</span>
								@else
									<span class="red">{{ trans('messages.lbl_unused') }}</span>
								@endif
							</span>
						</div>
					</td>
					<td style="font-size: 13px !important;">
						<div class="ml5 pt5">
							<div class="mb2">
								<a href="javascript:customernameclick('{{ $data->company_name }}');">
								<b class="blue">{{$data->company_name}}</b></a>
							</div>
							<div class="f12 vam label_gray boxhei24">
								<span class="f12"> 
									{{ trans('messages.lbl_dateofissue') }} :
								</span>
								<span class="f12">
									{{$data->quot_date}}
								</span>
								<span class="f12 ml20">
									{{ trans('messages.lbl_Creater') }} :
								</span>
								<span class="f12">
									{{$data->created_by}}
								</span>
							</div>
						</div>
						<div class="ml5 mb2 smallBlue CMN_display_block">
							<div class="CMN_display_block">
								@if($data->classification==0)
									<a href="javascript:addedit('edit','{{ $data->id }}');" class="anchorstyle">{{ trans('messages.lbl_edit') }}</a>&nbsp;<span class="ml3">|</span>
								@else
									<span>{{ trans('messages.lbl_edit') }}</span>&nbsp;<span class="ml3">|</span>
								@endif
							</div>
							<!-- <div class="CMN_display_block">
								<a href="javascript:addedit('copy','{{$data->id}}');" class="anchorstyle">{{ trans('messages.lbl_copy') }}</a>&nbsp;<span class="ml3">|</span>
							</div> -->
							<div class="CMN_display_block">
								<a href="javascript:fnview('{{$data->id}}','{{ $key+1 }}');" class="anchorstyle">{{ trans('messages.lbl_Details') }}</a>&nbsp;<span class="ml3">|</span>
							</div>
							<div class="CMN_display_block ml3">
								@if($data->pdf_flg==0)
            					<img class="pull-left box13 mt2" id="{{ $data->id}}pdfimg" src="{{ URL::asset('resources/assets/images/nopdf.png') }}">
								@else
            					<img class="pull-left box15 mt1" id="{{ $data->id}}pdfimg" src="{{ URL::asset('resources/assets/images/pdf.png') }}">
								@endif
								<a href="javascript:newpdf('{{ $data->id }}','{{ $data->user_id }}','{{ $data->pdf_flg }}','{{ $data->id}}pdfimg','{{ $data->trading_destination_selection}}');" class="anchorstyle ml3">{{ trans('messages.lbl_estimation') }}</a>&nbsp;<span class="ml3">|</span>
							</div>
							<div class="CMN_display_block ml3">
								@if($data->mailFlg==0)
            					<img class="pull-left box15 mt2" id="{{ $data->id}}pdfimg" src="{{ URL::asset('resources/assets/images/nosendmail.png') }}">
								@else
            					<img class="pull-left box18 mt1" id="{{ $data->id}}pdfimg" src="{{ URL::asset('resources/assets/images/sendmail.png') }}">
								@endif
								<a <?php if($data->pdf_flg==1) { ?> onclick="javascript:sendmail('{{ $data->id }}','{{ $data->trading_destination_selection}}','{{ $data->user_id}}');" class="anchorstyle ml3 csrp" <?php } else { ?>  class="black disabled tdn pl3 cur_default" <?php } ?> id="sendemail{{ $data->id }}">{{ trans('messages.lbl_email') }}</a>&nbsp;<span class="ml3">|</span>
							</div>
							<div class="CMN_display_block ml3">
								{{ trans('messages.lbl_order') }}&nbsp;<span class="ml3">|</span>
							</div>
							<div class="CMN_display_block ml3">
								<a href="javascript:gotoinvoicecreate('{{ $data->id }}','invoice');" class="anchorstyle">{{ trans('messages.lbl_CreateInvoice') }}</a>&nbsp;<span class="ml3">|</span>
							</div>
							<div class="CMN_display_block ml3 dropdown">
								<a href="#" style="text-decoration: none !important;" class="anchorstyle">{{ trans('messages.lbl_Others') }}</a>
								<div class="CMN_display_block" >
									<img class="pull-left box12 CMN_display_block" id="{{ $data->id}}pdfimg" src="{{ URL::asset('resources/assets/images/downarrowothers.png') }}">
								</div>
								<div class="dropdown-content ml10" style="border: 1px solid grey;">
									<?php for ($ot=0; $ot < count($othersArray); $ot++) { ?>
										<?php if ($ot!=$data->classification) {?>
											<a href="javascript:estimatestatus('{{ $data->id }}', '{{ $ot }}');" style="text-decoration: none;border-bottom: 1px solid grey;font-size: 12px;">{{ $othersArray[$ot] }}</a>
										<?php } ?>
									<?php } ?>
							  	</div>
							</div>
						</div>
					</td>
					<td class="tar pr10 vat pt5 pb5">
						{{ $data->totalval }}
						<?php  $totval += preg_replace('/,/', '', $data->totalval); $i=$i+1; ?>
					</td>
				</tr>
				@empty
				<tr>
					<td class="text-center" colspan="4" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
				</tr>
				@endforelse
			</tbody>
			</table>
			{{ Form::hidden('totalrecords', $TotEstquery->total(), array('id' => 'totalrecords')) }}
		</div>
		<div class="text-center">
			@if(!empty($Estquery->total()))
				<span class="pull-left mt24">
					{{ $Estquery->firstItem() }} ~ {{ $Estquery->lastItem() }} / {{ $Estquery->total() }}
				</span>
			@endif 
			{{ $Estquery->links() }}
			<div class="CMN_display_block flr">
				{{ $Estquery->linkspagelimit() }}
			</div>
		</div>
	{{ Form::close() }}
</article>
</div>
<script type="text/javascript">
	var recordTotal = '<?php echo $TotEstquery->total(); ?>';
	$('#totalrecords').val(recordTotal);
</script>
@endsection