@extends('layouts.app')
@section('content')
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	 $(document).ready(function() {
    	setDatePicker("dob");
  });
	function mulclick(divid){

	     if($('#'+divid).css('display') == 'block'){
	      document.getElementById(divid).style.display = 'none';
	      document.getElementById(divid).style.height= "240px";
	    }else {
	      document.getElementById(divid).style.display = 'block';
	    }
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
	.fb{
		color: gray !important;
	}
	.sort_asc {
		background-image:url({{ URL::asset('resources/assets/images/upArrow.png') }}) !important;
	}
	.sort_desc {
		background-image:url({{ URL::asset('resources/assets/images/downArrow.png') }}) !important;
	}
</style>
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::script('resources/assets/js/nonstaff.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::script('resources/assets/js/lib/lightbox.js') }}
{{ HTML::style('resources/assets/css/lib/lightbox.css') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="staff" class="DEC_flex_wrapper " data-category="staff staff_sub_8">
	{{ Form::open(array('name'=>'nonemployeefrm', 
						'id'=>'nonemployeefrm', 
						'url' => 'NonStaff/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	 {{ Form::hidden('resignid', '' , array('id' => 'resignid')) }}
	 {{ Form::hidden('sorting', '' , array('id' => 'sorting')) }}
	 {{ Form::hidden('searchmethod', $request->searchmethod, array('id' => 'searchmethod')) }}
	 {{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	 {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	 {{ Form::hidden('sortOptn',$request->nonstaffsort , array('id' => 'sortOptn')) }}
	 {{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
	 {{ Form::hidden('empid', $request->empid ,array('id' => 'empid')) }}
     {{ Form::hidden('empname', $request->empname ,array('id' => 'empname')) }}
     {{ Form::hidden('DOJ', $request->DOJ ,array('id' => 'DOJ')) }}
     {{ Form::hidden('hdnback', '', array('id' => 'hdnback')) }}
     {{ Form::hidden('viewid', '', array('id' => 'viewid')) }}
     {{ Form::hidden('ordervalue', $request->ordervalue, array('id' => 'ordervalue')) }}
     {{ Form::hidden('hdnempid', '$request->hdnempid', array('id' => 'hdnempid')) }}
      {{ Form::hidden('hdnempname', '$request->hdnempname', array('id' => 'hdnempname')) }}
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/staffList.png') }}">
			<h2 class="pull-left pl5 mt10 CMN_mw150">{{ trans('messages.lbl_nonstafflist') }} <span style="font-weight:normal;font-size:16px;"><span style="color:#136E83;font-weight:normal;font-size:16px;"></span>
			</span></h2>
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
	<div class="col-xs-12 pm0 pull-left mb10">
		<div class="col-xs-6 ml10 pm0 pull-left">
			<a href="javascript:nonstaff();" class="btn btn-success box100"><span class="fa fa-plus"></span> {{ trans('messages.lbl_register') }}</a>
		</div>
		<div class="col-xs-12 pm0 pull-left">
			<div class="col-xs-7 pm0 CMN_display_block pull-left vam pt5 box50per mt5">
				@if($resignid == 0)
					<a href="javascript:selectActive(1);" style="color:blue;" class="pl10 pb5">
						{{ trans('messages.lbl_resigned') }}
					</a>
				@else
					<a href="javascript:selectActive(0);" style="color:blue;" class="pl10 pb5">
						{{ trans('messages.lbl_employee') }}
					</a>
				@endif
            </div>
            <div class="col-xs-5 pm0 pr12 box50per">
				<div class="form-group pm0 pull-right moveleft nodropdownsymbol" id="moveleft">
				<a href="javascript:clearsearch()" title="Clear Search">
            		<img class="box30 mr5 " src="{{ URL::asset('resources/assets/images/clearsearch.png') }}">
          		</a>
				{{ Form::select('nonstaffsort', $array, $request->nonstaffsort,
	                            array('class' => 'form-control'.' ' .$request->sortstyle.' '.'CMN_sorting pull-right',
	                           'id' => 'nonstaffsort',
	                           'style' => $sortMargin,
	                           'name' => 'nonstaffsort'))
	                }}
	            </div>
			</div>
		</div>
	</div>
	<div>
	<div class="mr10 ml10">
		<div class="minh400">
			<table class="tablealternate box100per">
				<colgroup>
				   <col width="10%">
				   <col width="">
				   <col width="10%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader fwb tac">
				  		<th class="tac">{{ trans('messages.lbl_empid') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_empdetails') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_doj') }}</th>
			   		</tr>
			   	</thead>
			   	<tbody>
			   	@if(count($empdetailsdet)!="")
			   	@for ($i = 0; $i < count($empdetailsdet); $i++)
				<tr>
					<td class="">
						<div class="tac">
							<label class="box70per mb5">
								<a href="javascript:staffview('{{ $empdetailsdet[$i]['Emp_ID'] }}');" style="color:blue;" class="vam">
									{{ $empdetailsdet[$i]['Emp_ID'] }}
								</a>
							</label>
							<br>
							{{--*/ $file_exist = $disPath . $empdetailsdet[$i]['Picture']; /*--}}
							{{--*/ $file_uploadcheck = $uploadcheck . $empdetailsdet[$i]['Picture']; /*--}}
								@if (!file_exists($file_exist))
									{{--*/ $empdetailsdet[$i]['Picture'] = ""; /*--}}
								@endif
								@if(file_exists($file_exist) && $empdetailsdet[$i]['Picture'] != "")
									{{--*/ $src = $file . $empdetailsdet[$i]['Picture']; /*--}}
								@elseif(file_exists($file_uploadcheck) && $empdetailsdet[$i]['Picture'] != "")
									{{--*/ $src = $uploadcheck . $empdetailsdet[$i]['Picture']; /*--}}
								@else
									@if($empdetailsdet[$i]['Gender'] == 1)
										{{--*/ $src = $noimage . '/no-prof-male.JPG'; /*--}}
									@else
										{{--*/ $src = $noimage . '/no-prof-female.jpg'; /*--}}
									@endif
								@endif
							<img class="pull-left box70 mr5  ml20" src="{{ $src }}" 
								 width="90" height = "70">
							</img>
						</div>
					</td>
					
					<td>
						<div class="ml5">
							<div>　
								<span class="fll">
								{{ $empdetailsdet[$i]['FirstName'] }}
								</span>
								<span class="fwb" style="margin-left: -10px">
								{{ $empdetailsdet[$i]['LastName'] }}
								</span>
								<span class="">
									@if($empdetailsdet[$i]['nickname'] != "" )
									({{ $empdetailsdet[$i]['nickname'] }} )
								@endif	
								</span>
							</div>
							@if($empdetailsdet[$i]['KanaFirstName'] != "" && $empdetailsdet[$i]['KanaLastName'] != "")
							<div>　
								<span class="fll">
								{{ $empdetailsdet[$i]['KanaFirstName'] }}
								</span>
								<span class="fwb" style="margin-left: -10px">
								{{ $empdetailsdet[$i]['KanaLastName'] }}
								</span>
							</div>
							@endif
							<div>
								<span class="f12 clr_blue"> 
								{{ trans('messages.lbl_dob') }} :
								</span>
								<span class="f12"> 
								{{ $empdetailsdet[$i]['DOB'] }} <b>@if(!empty($empdetailsdet[$i]['DOB']))({{ birthday($empdetailsdet[$i]['DOB']) }})@endif</b>
								</span>
								<span class="f12 clr_blue">
								{{ trans('messages.lbl_mobilenumber') }} :
								</span>
								<span class="f12"> 
								{{ (!empty($empdetailsdet[$i]['Mobile1']) ?  $empdetailsdet[$i]['Mobile1'] : "Nill")  }}
								</span>
								<span class="f12 ml20 clr_blue">
								{{ trans('messages.lbl_email') }} :
								</span>
								<span class="f12"> 
								{{ (!empty($empdetailsdet[$i]['Emailpersonal']) ?  $empdetailsdet[$i]['Emailpersonal'] : "Nill") }}
								</span>
							</div>
						<div>
							<span class="clr_blue">{{ trans('messages.lbl_streetaddress') }}</span> :
							<span class="f12"> 
								{{ (!empty($empdetailsdet[$i]['Address1']) ?  $empdetailsdet[$i]['Address1'] : "Nill")  }}
							</span>
						</div>
						<div>
							<span class="clr_blue">{{ trans('messages.lbl_customer') }}</span> :
							<span class="f12"> 
							{{ (!empty($empdetailsdet[$i]['customer_name']) ?  $empdetailsdet[$i]['customer_name'] : "Nill")  }}
							</span>
						</div>
						<div class="mb4 CMN_display_block mt4">
							<div class="CMN_display_block">
								<a style="color:blue;" href="javascript:fnunderconstr();">{{ trans('messages.lbl_billinglist') }}</a>&nbsp;|
							</div>
							<div class="CMN_display_block">
								<a style="color:blue;" href="javascript:fnunderconstr();">
								{{ trans('messages.lbl_timesheetdets') }}</a>&nbsp;|
							</div>
							<div class="CMN_display_block">
								<a style="color:blue;" href="javascript:fnunderconstr();">{{ trans('messages.lbl_salarylist') }}</a> |
							</div>
							<div class="CMN_display_block">
								<a style="color:blue;" href="javascript:fnunderconstr();">{{ trans('messages.lbl_customer') }}</a>&nbsp;
							</div>
						</div>
					</div>
					</td>
					<td class="tac">
						<div class="45px">
							<span>{{ $empdetailsdet[$i]['DOJ'] }}</span>
						</div>
						<div class="mt55">
							<span class="clr_blue">
								@if($empdetailsdet[$i]['experience'] > 1 )
									{{ $empdetailsdet[$i]['experience'] }} Yrs
								@elseif($empdetailsdet[$i]['experience'] <= 1 )
									{{ $empdetailsdet[$i]['experience'] }} Yr
								@else
									{{ 0 }} Yr
								@endif		
							</span>
						</div>
					</td>
				</tr>
				@endfor
				@else
				<tr>
					<td class="text-center" colspan="3" style="color: red;"> No Data Found</td>
				</tr>
				@endif
			</tbody>
			</table>
		</div>
		<div class="text-center">
			@if(!empty($getNSTdetails->total()))
				<span class="pull-left mt24">
					{{ $getNSTdetails->firstItem() }} ~ {{ $getNSTdetails->lastItem() }} / {{ $getNSTdetails->total() }}
				</span>
			@endif 
			{{ $getNSTdetails->links() }}
			<div class="CMN_display_block flr">
          		{{ $getNSTdetails->linkspagelimit() }}
        	</div>
		</div>
		</div></div>
			<!-- SEARCH -->
			<div style="top: 136px!important;position: fixed;" 
        	@if ($request->searchmethod == 1 || $request->searchmethod == 2) 
                    	class="open CMN_fixed pm0" 
                   	@else 
                    	class="CMN_fixed pm0 pr0" 
                   	@endif 
                    	id="styleSelector">
        	<div class="selector-toggle">
              	<a id="sidedesignselector" href="javascript:void(0)"></a>
          	</div>
          	 <ul>
              <span>
                  <li style="">
                      <p class="selector-title">{{ trans('messages.lbl_search') }}</p>
                  </li>
              </span>
                <li class="theme-option ml6">
                  <div class="box100per mt5"  onKeyPress="return checkSubmitsingle(event)">
                    {!! Form::text('singlesearch', $request->singlesearch,
                          array('','class'=>' form-control box80per pull-left','style'=>'height:30px;','id'=>'singlesearch')) !!}

                    {{ Form::button('<i class="fa fa-search" aria-hidden="true"></i>', 
                        array('class'=>'ml5 mt2 pull-left search box15per btn btn-info btn-sm', 
                              'type'=>'button',
                              'name' => 'advsearch',
                              'onclick' => 'javascript:usinglesearch();',
                              'style'=>'border: none;' 
                              )) }}
                  <div>
                </li>
            </ul>
				<div class="mt5 ml10 pull-left mb5">
                <a onclick="mulclick('demo');" class="" style="font-family: arial, verdana;cursor: pointer;">
              	  {{ trans('messages.lbl_multi_search') }}
              </a>
            </div>
             <div>
             <ul id="demo" @if ($request->searchmethod == 2) class="collapse in ml5 pull-left" 
                          @else class="collapse ml5 pull-left"  @endif>
                 <li class="theme-option"  onKeyPress="return checkSubmitmulti(event)">
                 	<div class="mt5">
                 		<span class="pt3" style="font-family: arial, verdana;">
                 			{{ trans('messages.lbl_employeeid') }}
                 		</span>
                 		<div class="mt5 box88per" style="display: inline-block!important;">
                 			{!! Form::text('employeeno', $request->employeeno,
	                         array('','id' => 'employeeno','style'=>'height:30px;','class'=>'box93per 
	                         ')) !!}
                 		</div>
                 	</div>
                 	<div class="mt5">
                 		<span class="pt3" style="font-family: arial, verdana;">
                 			{{ trans('messages.lbl_doj') }}
                 		</span>
                 		<div class="mt5 box88per" style="display: inline-block!important;">
                 			<span class="CMN_display_block box33per " style="display: inline-block!important;">
                         	{!! Form::text('startdate', '',
                                array('',
                                    'id'=>'startdate','onKeyPress'=>'return event.charCode >= 48 && event.charCode <= 57',
                                    'class'=>'form-control box100per dob pm0'
                                    )) !!}
                         	</span>
							<label class="mt10 ml2 fa fa-calendar fa-lg CMN_display_block pr5" 
									for="startdate" aria-hidden="true" style="display: inline-block!important;">
							</label>
							<span class="CMN_display_block box33per " style="display: inline-block!important;">
								{!! Form::text('enddate', '',
	                                 array('', 'data-placement'=>'left', 
	                                 'onKeyPress'=>'return event.charCode >= 48 && event.charCode <= 57',
	                                 'class'=>'form-control box100per dob pm0',
	                                 'id'=>'enddate')) !!}
                         	</span>
							<label class="mt10 ml2 fa fa-calendar fa-lg CMN_display_block" 
									for="enddate" aria-hidden="true" style="display: inline-block!important;">
							</label>
                 		</div>
                 	</div>
                 	<div class="mt5 mb6">
		                 {{ Form::button(
		                     '<i class="fa fa-search" aria-hidden="true"></i> '.trans('messages.lbl_search'),
		                     array('class'=>'mt10 btn btn-info btn-sm ',
		                     		'onclick' => 'javascript: amultiplesearch()',
		                           	'type'=>'button')) 
		                 }}
		            </div>
                 </li>
             </ul>
            </div>
        </div>
			</div>
</div>
{{ Form::close() }}
<div id="importpopup" class="modal fade">
    <div id="login-overlay">
        <div class="modal-content">
            <!-- Popup will be loaded here -->
        </div>
    </div>
</div>
	@endsection