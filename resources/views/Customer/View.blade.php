@extends('layouts.app')
@section('content')
@php use App\Http\Helpers; @endphp
{{ HTML::script('resources/assets/js/customer.js') }}
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/multisearchvalidation.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::style('resources/assets/css/switch.css') }}
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
	#styleSelector {
    border: 1px;
    background: #FFF;
    position: fixed;
    margin: 0;
    padding: 0;
    /*width: 230px;*/
    width: 160px;
    height: auto;       /*by anto */
    top: 5px;
    /*right: -230px;*/
    right: -160px;
    z-index: 9999;
    height: auto;
    -webkit-transition: 0.5s;
    transition: 0.5s;
    border: 1px solid #A9A9A9;
}
.text {
  width: 100px;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}
</style>
<script type="text/javascript">
	function enableselect(bid) {    
      $('#hid_empid').val(bid);
      $("#select" ).css( "background-color", "orange" );
      $("#select" ).removeAttr("disabled");
}
$(document).ready(function() {
        $('#swaptable tr').click(function(event) {
              if (event.target.type !== 'radio') {
                if (event.target.nodeName != "SPAN") {
                  $(':radio', this).trigger('click');
                }
             }
        });
      });
</script>
<div class="CMN_display_block box100per" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="customer" class="DEC_flex_wrapper " data-category="customer customer_sub_2">
	{{ Form::open(array('name'=>'customerviewform', 'id'=>'customerviewform','url' => 'Customer/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'), 'method' => 'POST')) }}
	 {{ Form::hidden('id',$request->id,array('id' => 'id')) }}
	 {{ Form::hidden('custid',$request->custid,array('id' => 'custid')) }}
	 {{ Form::hidden('editid','',array('id' => 'editid')) }}
	 {{ Form::hidden('hid_branch_id','', array('id' => 'hid_branch_id')) }}
	 {{ Form::hidden('hid_custid','', array('id' => 'hid_custid')) }}
	 {{ Form::hidden('emp_id', $request->emp_id , array('id' => 'emp_id')) }}
	 {{ Form::hidden('branchid',$request->branchid, array('id' => 'branchid')) }}
	 {{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
     {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
     {{ Form::hidden('filterval', $request->filterval, array('id' => 'filterval')) }}
     {{ Form::hidden('flg', $request->flg , array('id' => 'flg')) }}
  	 {{ Form::hidden('empid', $request->empid , array('id' => 'empid')) }}
     {{ Form::hidden('selectionid', '1' , array('id' => 'selectionid')) }}
     {{ Form::hidden('hdnempid', '', array('id' => 'hdnempid')) }}
	 {{ Form::hidden('hdnempname', '', array('id' => 'hdnempname')) }}
	 {{ Form::hidden('hdnback', '3', array('id' => 'hdnback')) }}
	 {{ Form::hidden('hdncancel', '1', array('id' => 'hdncancel')) }}
	 <div class="row hline">
	     	<div class="col-sm-12 mt10">
	     	 	<img class="pull-left box40 mt5" src="{{ URL::asset('resources/assets/images/Client.png') }}">
	        	<h2 class="pl5 pull-left mt10">{{ trans('messages.lbl_customer') }}<span class="ml5"></span><span class="colbl ml5">{{ trans('messages.lbl_view') }}</span></h2>
	      	</div>
	  	</div>
	  	<div class="mt10 ml15">
	  		@if(Session::has('success'))
			<div align="center" class="alertboxalign" role="alert">
				<p class="alert {{ Session::get('alert', Session::get('type') ) }}">
	            {{ Session::get('success') }}
	          	</p>
			</div>
		@endif
		@php Session::forget('success'); @endphp
				@if(!empty($request->empid))
					<a href="javascript:goempindexpage('Employee',{{ date('YmdHis') }});" class="pageload btn btn-info box80"><span class="fa fa-arrow-left"></span> {{ trans('messages.lbl_back') }}</a>
				@else
					<a href="javascript:goindexpage('Customer',{{ date('YmdHis') }});" class="pageload btn btn-info box80"><span class="fa fa-arrow-left"></span> {{ trans('messages.lbl_back') }}</a>
				@endif
				<a href="javascript:edit('{{ date('YmdHis') }}','{{ $getdetails[0]->id}}','{{ $getdetails[0]->custid}}');" class="pageload btn btn-warning box100"><span class="fa fa-pencil"></span> {{ trans('messages.lbl_edit') }}</a>
		</div>
			<div class="col-xs-12">
				<fieldset>
						<div class="col-xs-6" style="display:inline-block;">
							<div class="mt15">
					        <div class="col-xs-4 text-right clr_blue ml2">
					           <label>
					                {{ trans('messages.lbl_CustId') }}<span class="fr ml2 red">  </span>
					            </label>
					        </div>
					        <div>
				                <label class="colbl">
				                   @if(isset($getdetails[0]->custid))
				                   	{{ $getdetails[0]->custid}}
				                   @else
				                   	{{ "NILL"}}
				                   @endif		  
				                </label>
					        </div>
					    </div>
					    <div class="mt5">
				        <div class="col-xs-4 text-right clr_blue ml2">
				             <label>
				                {{ trans('messages.lbl_custname(JP & Eng)') }}<span class="fr ml2 red">  </span></label>
				        </div>
				        <div>
				                <label class="green">
				                   @if(isset($getdetails[0]->txt_custnamejp))
			                   	{{ $getdetails[0]->txt_custnamejp}}
			                   @else
			                   	{{ "NILL"}}
			                   @endif	  
				                </label>
				        </div>
				    </div>
				    <div class="mt5">
				        <div class="col-xs-4 text-right clr_blue ml6">
				             <label>
				                {{ trans('messages.lbl_custname(kana)') }}<span class="fr ml2 red">  </span></label>
				        </div>
				        <div>
				                <label class="fwn">
				                    @if(isset($getdetails[0]->txt_kananame))
			                   	{{ $getdetails[0]->txt_kananame}}
			                   @else
			                   	{{ "NILL"}}
			                   @endif	 
				                </label>
				        </div>
				    </div>
				     <div class="mt5">
				        <div class="col-xs-4 text-right clr_blue ml6">
				             <label>
				                {{ trans('messages.lbl_repname') }}<span class="fr ml2 red">  </span></label>
				        </div>
				        <div>
				                <label class="fwn">
				                     @if(isset($getdetails[0]->txt_repname))
			                   	{{ $getdetails[0]->txt_repname}}
			                   @else
			                   	{{ "NILL"}}
			                   @endif	
				                </label>
				        </div>
				    </div>
				    <div class="mt5">
				        <div class="col-xs-4 text-right clr_blue ml6">
				             <label>
				                {{ trans('messages.lbl_branch_name') }}<span class="fr ml2 red">  </span></label>
				        </div>
				        <div>
				                <label class="fwn">
				                    @if(isset($getbranchdetails[0]->branch_name))
			                   	{{ $getbranchdetails[0]->branch_name}}
			                   @else
			                   	{{ "NILL"}}
			                   @endif	 
				                </label>
				        </div>
				    </div>
				    <div class="mt5">
				        <div class="col-xs-4 text-right clr_blue ml6">
				             <label>
				                {{ trans('messages.lbl_custagreement') }}<span class="fr ml2 red">  </span></label>
				        </div>
				        <div>
				                <label class="fwn">
				                    @if(isset($getdetails[0]->txt_custagreement))
			                   	{{ $getdetails[0]->txt_custagreement}}
			                   @else
			                   	{{ "NILL"}}
			                   @endif	 
				                </label>
				        </div>
				    </div>
				    <div class="mt5">
				        <div class="col-xs-4 text-right clr_blue ml6">
				             <label>
				                {{ trans('messages.lbl_mobilenumber') }}<span class="fr ml2 red">  </span></label>
				        </div>
				        <div>
			                <label class="fwn">
	                     	@if(isset($getdetails[0]->txt_mobilenumber))
	                     		{{ Helpers::checkTELFAX($getdetails[0]->txt_mobilenumber) }}
		                   @else
		                   		{{ "NILL"}}
		                   @endif	
			                </label>
				        </div>
				    </div>
				    <div class="mt5">
				        <div class="col-xs-4 text-right clr_blue ml6">
				             <label>
				                {{ trans('messages.lbl_fax') }}<span class="fr ml2 red">  </span></label>
				        </div>
				        <div>
			                <label class="fwn">
	                    	 @if(isset($getdetails[0]->txt_fax))
	                     		{{ Helpers::checkTELFAX($getdetails[0]->txt_fax) }}
		                   	@else
		                   		{{ "NILL"}}
		                    @endif	
			                </label>
				        </div>
				    </div>
				    <div class="mt5">
				        <div class="col-xs-4 text-right clr_blue ml6">
				             <label>
				                {{ trans('messages.lbl_url') }}<span class="fr ml2 red">  </span></label>
				        </div>
				        <div>
				                <label class="fwn">
				                     @if(isset($getdetails[0]->txt_url))
			                   	{{ $getdetails[0]->txt_url}}
			                   @else
			                   	{{ "NILL"}}
			                   @endif	
				                </label>
				        </div>
				    </div>
				    <div class="mt5">
				        <div class="col-xs-4 text-right clr_blue ml6">
				             <label>
				                {{ trans('messages.lbl_address') }}<span class="fr ml2 red">  </span></label>
				        </div>
				        <div>
			                <label class="fwn" style="word-wrap: break-word;width:250px">
			                     @if(isset($getdetails[0]->txt_address))
		                   	{!! nl2br(e($getdetails[0]->txt_address)) !!}
		                   @else
		                   	{{ "NILL"}}
		                   @endif	
			                </label>
				        </div>
				    </div>
				 </div>
					@if(count($inchargeview)!="")
					<div class="col-xs-6" style="display:inline-block;">
      					@for ($i = 0; $i < count($inchargeview); $i++)
						<div class="mt15">
					        <div class="col-xs-4 text-right clr_blue ml6">
					             <label>
					                {{ trans('messages.lbl_inchargename') }}<span class="fr ml2 red">  </span></label>
					        </div>
					        <div>
				                <label class="fwb word_break_all box40per">
				                     @if(isset($inchargeview[$i]['incharge_name']))
				                     	<a class="colbl" href="javascript:inchargeedit('{{ date('YmdHis') }}','{{ $inchargeview[$i]['id'] }}');">
				                   		{{ $inchargeview[$i]['incharge_name'] }} ({{ $inchargeview[$i]['incharge_name_romaji'] }})</a>
				                   @else
				                   		{{ "NILL"}}
				                   @endif	
				                </label>
					        </div>
					    </div>
					    <div class="mt5">
					        <div class="col-xs-4 text-right clr_blue ml6">
					             <label>
					                {{ trans('messages.lbl_mobilenumber') }}<span class="fr ml2 red">  </span></label>
					        </div>
					        <div>
				                <label class="fwn word_break_all box40per">
					               @if(isset($inchargeview[$i]['incharge_contact_no']))
					               {{ Helpers::checkTELFAX($inchargeview[$i]['incharge_contact_no']) }}	
				                   @else
				                   	{{ "NILL"}}
				                   @endif	
				                </label>
					        </div>
					    </div>
					    <div class="mt5">
					        <div class="col-xs-4 text-right clr_blue ml6">
					             <label>
					                {{ trans('messages.lbl_mail') }}<span class="fr ml2 red">  </span></label>
					        </div>
					        <div>
				                <label class="fwn word_break_all box40per">
				                  @if(isset($inchargeview[$i]['incharge_email_id']))
				                  	@if(!empty($inchargeview[$i]['incharge_email_id']))
				                   		{{ $inchargeview[$i]['incharge_email_id'] }}
				                   	@else
					                   	{{ 'NILL' }}
				                   	@endif
				                   @else
				                   	{{ 'NILL' }}
				                   @endif
				                </label>
					        </div>
					    </div>
					    <div class="mt5">
					        <div class="col-xs-4 text-right clr_blue ml6">
					             <label>
					                {{ trans('messages.lbl_designation') }}<span class="fr ml2 red">  </span></label>
					        </div>
					        <div>
				                <label class="fwn word_break_all box40per">
				              	   @if(isset($inchargeview[$i]['DesignationNM']))
				                   	{{ $inchargeview[$i]['DesignationNM'] }}
				                   @else
				                   	{{ "NILL"}}
				                   @endif	
				                </label>
					        </div>
					    </div>
					    @endfor
						</div>
					    @else
					    @endif
				</fieldset>	
			</div>
					@if(count($branchview)!="")
					<div style="margin-top: -15px;" class="col-xs-12">
						<fieldset class="col-xs-12">
							<div class="row hline">
						     	<div class="col-sm-12">
						        	<h2 class="pl5 pull-left mt15">{{ trans('messages.lbl_branch') }}</h2>
						      	</div>
					  		</div>
							<div class="col-xs-6" style="display:inline-block;">
      						@for ($i = 0; $i < count($branchview); $i++)
							<div class="mt15">
						        <div class="col-xs-4 text-right clr_blue ml2">
						             <label>
						                {{ trans('messages.lbl_branchid') }}<span class="fr ml2 red">  </span></label>
						        </div>
						        <div>
					                <label>
					                   @if(isset($branchview[$i]['id']))
					                   	<a href="javascript:branchedit('{{ date('YmdHis') }}','{{ $branchview[$i]['id'] }}');" class="colbl">{{ $branchview[$i]['id'] }}</a>
					                   @else
					                   	{{ "NILL"}}
					                   @endif		  
					                </label>
						        </div>
						    </div>
						    <div class="mt5">
						        <div class="col-xs-4 text-right clr_blue ml2">
						             <label>
						                {{ trans('messages.lbl_branch_name') }}<span class="fr ml2 red">  </span></label>
						        </div>
						        <div>
					                <label class="fwb">
					                   @if(isset($branchview[$i]['branch_name']))
					                   	{{ $branchview[$i]['branch_name'] }}
					                   @else
					                   	{{ "NILL"}}
					                   @endif	  
					                </label>
						        </div>
						    </div>
						    <div class="mt5">
						        <div class="col-xs-4 text-right clr_blue ml2">
						             <label>
						                {{ trans('messages.lbl_mobilenumber') }}<span class="fr ml2 red">  </span></label>
						        </div>
						        <div>
					                <label class="fwn">
					                   @if(isset($branchview[$i]['branch_contact_no']))
					                   	{{ $branchview[$i]['branch_contact_no'] }}
					                   @else
					                   	{{ "NILL"}}
					                   @endif		  
					                </label>
						        </div>
						    </div>
						    <div class="mt5">
						        <div class="col-xs-4 text-right clr_blue ml2">
						             <label>
						                {{ trans('messages.lbl_fax') }}<span class="fr ml2 red">  </span></label>
						        </div>
						        <div>
					                <label class="fwn">
					                  @if(isset($branchview[$i]['branch_fax_no']))
					                   	{{ $branchview[$i]['branch_fax_no'] }}
					                   @else
					                   	{{ "NILL"}}
					                   @endif		  
					                </label>
						        </div>
						    </div>
						    <div class="mt5">
						        <div class="col-xs-4 text-right clr_blue ml2">
						             <label>
						                {{ trans('messages.lbl_address') }}<span class="fr ml2 red">  </span></label>
						        </div>
						        <div>
					                <label class="fwn" style="word-wrap: break-word;width:250px">
					                   @if(isset($branchview[$i]['branch_address']))
					                   {!! nl2br(e( $branchview[$i]['branch_address'])) !!}
					                   @else
					                   	{{ "NILL"}}
					                   @endif		  
					                </label>
						        </div>
						    </div>
						    @endfor
						    @endif
							</div>
						</fieldset>
					</div>
					@if(count($currentview)!="")
					<div class="col-sm-12 fwb">
			        	{{ trans('messages.lbl_currentemployees') }} : <button id="select" type="button" data-dismiss="modal" onclick="popupopen('{{ date('YmdHis') }}','{{ $request->custid}}','{{ $request->id}}');" class="btn CMN_display_block box80 flr white bg_grey" disabled="disabled" style="margin-top: -10px;cursor: pointer"><span class="fa fa-pencil"></span>  Edit </button>
	      			<div class="pb8"></div>
	      			</div>
	      			<div class="ml15 box100per">
				    <table id="swaptable" class="tablealternate CMN_tblfixed box98per">
				      <colgroup>
				         <col width="4%">
				         <col width="5%">
				         <col width="7%">
			          	 <col>
			             <col width="9%">
			             <col width="9%">
				         <col width="9%">
				         <col width="9%">
				         <col width="22%">
				      </colgroup>
				      <thead class="CMN_tbltheadcolor">
				        <tr>
				          <th></th>
				          <th>{{ trans('messages.lbl_sno') }}</th>
				           <th>{{ trans('messages.lbl_empid') }}</th>
				          <th>{{ trans('messages.lbl_name') }}</th>
				          <th>{{ trans('messages.lbl_Start_date') }}</th>
				          <th>{{ trans('messages.lbl_enddate') }}</th>
				          <th>{{ trans('messages.lbl_yearmonth') }}</th>
				          <th>{{ trans('messages.lbl_status') }}</th>
				          <th>{{ trans('messages.lbl_updated_by') }}</th>
				        </tr>
				      </thead>
				      <tbody>
      					@for ($i = 0; $i < count($currentview); $i++)
				      	<tr>
				      		<td class="text-center">
				      					{{--*/ $emp_id=$currentview[$i]['emp_id']; /*--}}
				      			{{ Form::radio('selectradio','', false, array('id'=>'selectradio','class' => 'ml5 mb3' , "onchange" => "javascript:disablededittrue('$emp_id');" )) }}
                                          
                            </td>
				      		<td class="text-center">{{$i+1}}</td>
				      		<td class="text-center colbl fwb"> 
				      			@if($currentview[$i]['emp_id'])
						            {{ $currentview[$i]['emp_id'] }}
						          @else
						            {{ "-"}}
						          @endif
						    </td>
				      		<td class="text">
				      			@if(!empty($currentview[$i]['LastName']))
						             {{ empnamelength($currentview[$i]['LastName'], $currentview[$i]['FirstName'], 50) }}
						          @else
						            {{ " "}}
						          @endif
				      		</td>
				      		<td class="text-center">
				      			@if($currentview[$i]['start_date'])
						            {{ $currentview[$i]['start_date'] }}
						          @else
						            {{ "-"}}
						          @endif
				      		</td>
				      		<td class="text-center">
				      			@if($currentview[$i]['end_date']!="0000-00-00")
						            {{ $currentview[$i]['end_date'] }}
						          @else
						            {{ ""}}
						          @endif
				      		</td>
				      		<td class="text-center">
				      			@if($currentview[$i]['experience'] != "-")
						            {{ $currentview[$i]['experience'] }} Yrs
						          @else
						            {{ "0.0 Yr"}}
						          @endif
				      		</td>
				      		<td>
				      			@if($currentview[$i]['status']=="1")
						            {{ "StayIN" }}
						          @else
						            {{ "-"}}
						          @endif
				      		</td>
				      		<td>
				      			@if(!empty($currentview[$i]['update_by']))
						            {{ $currentview[$i]['update_by'] }}
						          @else
						            {{ "-"}}
						          @endif
				      		</td>
				      	</tr>
				      	@endfor
				          @else
				            <!-- <tr>
				              <td class="text-center fr" colspan="9">
				                {{ trans('messages.lbl_nodatafound') }}
				              </td>
				            </tr> -->
				          @endif
				      </tbody>
					</table>
					</div>
					 @if(count($currentempview)!="")
					<div class="col-sm-12 fwb mt10">
			        	{{ trans('messages.lbl_changeemployees') }} :
	      			<div class="pb10"></div>
	      			</div>
	      			<div class="mt15 ml15 box100per">
				    <table class="tablealternate CMN_tblfixed box98per">
				      <colgroup>
				         <col width="5%">
				         <col width="7%">
			          	 <col>
			             <col width="9%">
			             <col width="9%">
				         <col width="9%">
				         <col width="15%">
				         <col width="22%">
				      </colgroup>
				      <thead class="CMN_tbltheadcolor">
				        <tr>
				          <th>{{ trans('messages.lbl_sno') }}</th>
				           <th>{{ trans('messages.lbl_empid') }}</th>
				          <th>{{ trans('messages.lbl_name') }}</th>
				          <th>{{ trans('messages.lbl_Start_date') }}</th>
				          <th>{{ trans('messages.lbl_enddate') }}</th>
				          <th>{{ trans('messages.lbl_yearmonth') }}</th>
				          <th>{{ trans('messages.lbl_status') }}</th>
				          <th>{{ trans('messages.lbl_newemployees') }}</th>
				        </tr>
				      </thead>
				      <tbody>
      					@for ($i = 0; $i < count($currentempview); $i++)
				      	<tr>
				      		<td class="text-center">{{$i+1}}</td>
				      		<td class="text-center"> 
			      				<a class="colbl fwb" href="javascript:getchangeempdetails('{{ date('YmdHis') }}','{{ $currentempview[$i]['emp_id'] }}','{{$currentempview[$i]['LastName']}}');">
			      				@if($currentempview[$i]['emp_id'])
			      				{{ $currentempview[$i]['emp_id'] }}
              					</a>    
					          @else
					            {{ "-"}}
					          @endif
						    </td>
				      		<td class="text">
				      			@if(!empty($currentempview[$i]['LastName']))
						             {{ empnamelength($currentempview[$i]['LastName'], $currentempview[$i]['FirstName'], 50) }}
						          @else
						            {{ "-"}}
						          @endif
				      		</td>
				      		<td class="text-center">
				      			@if($currentempview[$i]['start_date'])
						            {{ $currentempview[$i]['start_date'] }}
						          @else
						            {{ "-"}}
						          @endif
				      		</td>
				      		<td class="text-center">
				      			@if($currentempview[$i]['end_date']!="0000-00-00")
						            {{ $currentempview[$i]['end_date'] }}
						          @else
						            {{ ""}}
						          @endif
				      		</td>
				      		<td class="text-center">
				      			@if($currentempview[$i]['experience']!="-")
						            {{ $currentempview[$i]['experience'] }} Yrs
						          @else
						            {{ "0.0 Yr"}}
						          @endif
				      		</td>
				      		<td>
				                @if($currentempview[$i]['status']=='2')	
				                	{{ "Returned"}}
				                @elseif($currentempview[$i]['status']=='3')	
				                	{{ "Client Changed"}}
				                @elseif($currentempview[$i]['status']=='4')	
				                	{{ "Others"}}
				                @else	
				                	{{ "Work End"}}			
				                @endif
				      		</td>
				      		<td>
				      			@if($currentempview[$i]['status']=='2')	
				                	{{ "Microbit"}}
				                @elseif($currentempview[$i]['status']=='3')
				                	@if(isset($currentempview[$i]['customername']))
				                		{{ $currentempview[$i]['customername'] }}
				                	@else
				                		
				                	@endif		
				                @else	
				                	{{ "-"}}			
				                @endif
				      		</td>
				      	</tr>
				      	@endfor
				          @else
				            <!-- <tr>
				              <td class="text-center fr" colspan="8">
				                {{ trans('messages.lbl_nodatafound') }}
				              </td>
				            </tr> -->
				          @endif
				      </tbody>
					</table>
					</div>
					<div class="pb15"></div>				    
        	<div style="top: 145px;position: fixed;" @if ($request->searchmethod == 1 || $request->searchmethod == 2) 
                     class="CMN_fixed pm0" 
                   @else 
                     class="open CMN_fixed pm0 pr0" 
                   @endif 
                    id="styleSelector">
             <div class="selector-toggle">
              <a id="sidedesignselector" href="javascript:void(0)" style="border: 1px solid #A9A9A9;"></a>
          </div>
            <ul>
                <li style="cursor: hand;">
                  <div class="mt10 fll">
              		 <a href="#demo" onclick="branchadd('{{ date('YmdHis') }}');" class="" style="font-family: arial, verdana;" data-toggle="collapse">
		              	  <span class="fa fa-plus csrp"></span><span class="ml5 csrp">{{ trans('messages.lbl_branchadd') }}</span>
		              </a>	
                  </div>
                  <div class="mt10 fll">
                  		 <a href="#demo" onclick="inchargeadd('{{ date('YmdHis') }}');" class="" style="font-family: arial, verdana;" data-toggle="collapse">
		              	  <span class="fa fa-plus csrp"></span><span class="ml5 csrp">{{ trans('messages.lbl_inchargeadd') }}</span>
		              </a>
                  </div>
                  <div class="mt10 fll">
                  		 <a href="#demo" onclick="gotoestimation('{{ date('YmdHis') }}');" class="" style="font-family: arial, verdana;" data-toggle="collapse">
		              	  <span class="fa fa-plus csrp"></span><span class="ml5 csrp">{{trans('messages.lbl_createestimation')}}</span>
		              </a>
                  </div>
                  <div class="mt10 fll">
                  		 <a href="javascript:empselectionpopupadd('{{ date('YmdHis') }}','{{ $request->custid}}','{{ $request->id}}');" class="">
		              	  <span class="fa fa-plus csrp"></span><span class="ml5 csrp">{{trans('messages.lbl_employeeselection')}}</span>
		              	</a>
                  <div class="pb10"></div>
                  </div>
                   <div class="row hline mt10" style="width: 150px;">
            	   </div>
            	   <!-- <div class="mt10 fll">
            	   		<a href="#demo" onclick="UnderConstruction();" class="" style="font-family: arial, verdana;" data-toggle="collapse"><img class="pull-left box15" src="{{ URL::asset('resources/assets/images/coverletter.png') }}">
		              	 <span class="ml5">{{ "Covering Letter" }}</span>
		              	</a>
		              	 <div class="pb10"></div>
                  </div> -->
                <div class="mt10 fll">
                    	<a href="javascript:coverletter('{{ date('YmdHis') }}','{{ $request->custid}}','{{ $request->id}}');" class="box80" data-dismiss="modal">
                    		<img class="pull-left box15" src="{{ URL::asset('resources/assets/images/coverletter.png') }}">
                    		<span class="fa ml5"></span>{{trans('messages.lbl_coveringletter')}}</a>
                  </div>
                  <div class="mt5 fll ml10">
                  		<?php if($getdetails[0]->coverletter){
                  		$myArray=array();
                 		$myString =$getdetails[0]->coverletter; 
						$myArray = explode('.', $myString);
						$myArray1=$myArray[0]="Cover letter";
						}	else{

						}?>
						@if(isset($getdetails[0]->coverletter))
                    		<a href="javascript:coverdownload('{{$getdetails[0]->coverletter}}','../../../resources/assets/uploadandtemplates/upload/Coverletter');" class="box80" data-dismiss="modal">
                    		<img class="pull-left box15" src="{{ URL::asset('resources/assets/images/coverletter.png') }}">
                    		<span class="fa ml5"></span>{{ $myArray1 }}.{{$myArray[1]}}</a>
                    	@else @endif	
                  </div>
                </li>
            </ul>
         </div>
		{{ Form::close() }}
		</div>
	  	</article>
	  </div>
	  <div id="empnamepopup" class="modal fade">
        <div id="login-overlay">
            <div class="modal-content">
                <!-- Popup will be loaded here -->
            </div>
        </div>
    </div>
    <div id="coverletterpopup" class="modal fade">
        <div id="login-overlay">
            <div class="modal-content">
                <!-- Popup will be loaded here -->
            </div>
        </div>
    </div>
@endsection