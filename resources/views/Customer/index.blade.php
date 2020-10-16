@extends('layouts.app')
@section('content')
<?php use App\Http\Helpers; ?>
{{ HTML::script('resources/assets/js/customer.js') }}
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/multisearchvalidation.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::style('resources/assets/css/switch.css') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
 <script type="text/javascript">
    var datetime = '<?php echo date('Ymdhis'); ?>';
    $(document).ready(function() {
    setDatePicker("datarange");
  });
  </script>
  <style type="text/css">
  .fb{
    color: gray !important;
  }
  	.sort_asc {
		background-image:url({{ URL::asset('resources/assets/images/upArrow.png') }}) !important;
	}
	.sort_desc {
		background-image:url({{ URL::asset('resources/assets/images/downArrow.png') }}) !important;
	}
  #styleSelector {
    border: 1px;
    background: #FFF;
    position: absolute;
    margin: 0;
    padding: 0;
    width: 270px;
    height: auto;       /*by anto */
    top: 5px;
    right: -270px;
    z-index: 9999;
    height: auto;
    -webkit-transition: 0.5s;
    transition: 0.5s;
}
  </style> 
  <div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="customer" class="DEC_flex_wrapper " data-category="customer customer_sub_2">
{{ Form::open(array('name'=>'customerindexform', 'id'=>'customerindexform','url' => 'Customer/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'), 'method' => 'POST')) }}
    {{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
    {{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
    {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
    {{ Form::hidden('filterval', $request->filterval, array('id' => 'filterval')) }}
    {{ Form::hidden('hdnempid', '', array('id' => 'hdnempid')) }}
    {{ Form::hidden('hdnempname', '', array('id' => 'hdnempname')) }}
	  {{ Form::hidden('sorting', '' , array('id' => 'sorting')) }}
	  {{ Form::hidden('viewid', '' , array('id' => 'viewid')) }}
	  {{ Form::hidden('searchmethod', $request->searchmethod, array('id' => 'searchmethod')) }}
	  {{ Form::hidden('sortOptn',$request->cussort , array('id' => 'sortOptn')) }}
    {{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
    {{ Form::hidden('useval','',array('id' => 'useval')) }}
    {{ Form::hidden('id','',array('id' => 'id')) }}
    {{ Form::hidden('custid','',array('id' => 'custid')) }}
    {{ Form::hidden('viewflg', '2', array('id' => 'viewflg')) }}
    {{ Form::hidden('hid_branch_id','', array('id' => 'hid_branch_id')) }}
    {{ Form::hidden('ordervalue', $request->ordervalue, array('id' => 'ordervalue')) }}
    {{ Form::hidden('oldfilter', $request->filterval, array('id' => 'oldfilter')) }}

    <div class="row hline">
     	<div class="col-sm-12 mt10">
     	 	<img class="pull-left box40 mt5" src="{{ URL::asset('resources/assets/images/Client.png') }}">
        	<h2 class="pl5 pull-left mt10">{{ trans('messages.lbl_customer_list') }}</h2>
      	</div>
  	</div>
  	<div class="col-xs-12 pm0 pull-left">
		<div class="col-xs-6 ml10 pm0 pull-left mt8">
			<a href="javascript:customerreg({{ date('YmdHis') }});" class="pageload btn btn-success box100"><span class="fa fa-plus"></span> {{ trans('messages.lbl_register') }}</a>
		</div><div class="col-xs-12 pm0 pull-left">
      <div class="col-xs-7 pm0 CMN_display_block pull-left">
        <a class="btn btn-link {{ $disabledactive }}" href="javascript:filter('1');"> {{ trans('messages.lbl_active') }} </a>
        <span>|</span>
        <a class="btn btn-link {{ $disabledinactive }}" href="javascript:filter('2');"> {{ trans('messages.lbl_inactive') }} </a>
        <span>|</span>
        <a class="btn btn-link {{ $disabledusenotuse }}" href="javascript:filter('3');"> {{ trans('messages.lbl_notuse') }} </a>
      </div>
      <div class="col-xs-5 pm0 pr12">
        <div class="form-group pm0 pull-right moveleft nodropdownsymbol" id="moveleft">
          <a href="javascript:clearsearch()" title="Clear Search">
                  <img class="pull-left box30 mr5 pageload" src="{{ URL::asset('resources/assets/images/clearsearch.png') }}">
          </a>        
            {{ Form::select('cussort', $customersortarray, $request->cussort,
                                array('class' => 'form-control'.' ' .$request->sortstyle.' '.'CMN_sorting pull-right',
                               'id' => 'cussort',
                               'style' => $sortMargin,
                               'name' => 'cussort'))
                    }}
        </div>
      </div>
  </div>
	</div>
  	<div class="mt10 minh340 ml10 box100per">
    <table class="tablealternate CMN_tblfixed width98">
      <colgroup>
          <col width="5%">
          <col width="8%">
          <col width="20%">
          <col width="23%">
          <col width="">
          <col>
      </colgroup>
      <thead class="CMN_tbltheadcolor">
        <tr>
          <th>{{ trans('messages.lbl_use') }}</th>
          <th>{{ trans('messages.lbl_customerno') }}</th>
          <th>{{ trans('messages.lbl_name') }}</th>
          <th>{{ trans('messages.lbl_address') }}</th>
          <th>{{ trans('messages.lbl_Details') }}</th>
          <th>{{ trans('messages.lbl_branch_name') }}</th>
        </tr>
      </thead>
    <tbody>
    @if(count($cstviews)!="")
      @for ($i = 0; $i < count($cstviews); $i++)
    <tr>
        <td class="text-center">
          @if($cstviews[$i]['delflg']==0)
          {{ ($detailview->currentpage()-1) * $detailview->perpage() + $i + 1 }}<br>
          <a title="Not Use" class="fr" href="javascript:ChangecutomerUse('1',{{ $cstviews[$i]['id'] }});">x</a>
          @else
           {{ ($detailview->currentpage()-1) * $detailview->perpage() + $i + 1 }}<br>
           <a title="Use" href="javascript:ChangecutomerUse('0',{{ $cstviews[$i]['id'] }});">○</a>
           @endif
        </td>
        <td class="text-center">
          @if($cstviews[$i]['customer_id'])
            {!! nl2br(e($cstviews[$i]['customer_id'])) !!}
          @else
            {{ "NILL"}}
          @endif
        </td>
        <td> 
          <a class="colbl fwb" href="javascript:custview('{{ date('YmdHis') }}','{{ $cstviews[$i]['id'] }}','{{ $cstviews[$i]['customer_id'] }}');">
          @if($cstviews[$i]['customer_name'])
            {{ $cstviews[$i]['customer_name'] }}</a>
          @else
            {{ "NILL"}}
          @endif    
          <br>
            @if($cstviews[$i]['romaji'])
              {{ $cstviews[$i]['romaji'] }}
            @else
            @endif 
          <br>
            @if($cstviews[$i]['contract']=="0000-00-00")
            @else
              {{ $cstviews[$i]['contract'] }}
            @endif
         </td>
        <td> 
          @if($cstviews[$i]['customer_address'])
            {!! nl2br(e($cstviews[$i]['customer_address'])) !!}
          @else
          @endif
        </td>
        <td>
          <span class="clr_blue">{{ trans('messages.lbl_mobileno') }}</span> <span>:</span><span class="ml5">
          @if($cstviews[$i]['customer_contact_no'])
              {{ $cstviews[$i]['customer_contact_no'] }}
          @else
            {{ "NILL"}}
          @endif
          </span><br>
          <span class="clr_blue">{{ trans('messages.lbl_fax') }}</span><span class="ml40">:</span><span class="ml5">
           @if($cstviews[$i]['customer_fax_no'])
            {{ $cstviews[$i]['customer_fax_no'] }}
          @else
            {{ "NILL"}}
          @endif
          </span><br>
          <span class="clr_blue">{{ trans('messages.lbl_url') }}</span><span class="ml39">:</span>
           @if($cstviews[$i]['customer_website'])
            <span class="ml5 colbl">
            <a class="colbl" href="http://{{ $cstviews[$i]['customer_website'] }}" target="_blank">{{ $cstviews[$i]['customer_website'] }}</a>
          </span>
          @else
            <span class="ml5">
            {{ "NILL"}}
          </span>
          @endif
        </td>
        <td>
        @if(isset($cstviews[$i]['BranchName']))
        @for ($k = 0; $k < count($cstviews[$i]['BranchName']); $k++)
          @if(isset($cstviews[$i]['BranchName'][$k]))
            {{ $cstviews[$i]['BranchName'][$k] }}<br/>
          @else
            {{ "NILL" }}<br/>
          @endif
        @endfor 
        @else
          {{ "NILL"}}
        @endif 
        </td>
    </tr>
    @endfor
          @else
            <tr>
              <td class="text-center fr" colspan="6">
                {{ trans('messages.lbl_nodatafound') }}
              </td>
            </tr>
          @endif
     </tbody>
    </table>
    </div>
	<!-- SEARCH -->
        <div style="top: 180px;position: fixed;" @if ($request->searchmethod == 1 || $request->searchmethod == 2) 
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
              <a href="#demo" onclick="clearAll('1');" class="" style="font-family: arial, verdana;" data-toggle="collapse">
              	  {{ trans('messages.lbl_multi_search') }}
              </a>
            </div>
            <div  id="multisearch">
             <ul id="demo" @if ($request->searchmethod == 2) class="collapse in ml5 pull-left" 
                          @else class="collapse ml5 pull-left"  @endif>
                 <li class="theme-option" onKeyPress="return checkSubmitmulti(event)">
                 	<div class="mt5">
                 		<span class="pt3" style="font-family: arial, verdana;">
                 			{{ trans('messages.lbl_name') }}
                 		</span>
                 		<div class="mt5 box88per" style="display: inline-block!important;">
                 			{!! Form::text('name', $request->name,
	                         array('','id' => 'name','style'=>'height:30px;','class'=>'box93per 
	                         ')) !!}
                 		</div>
                 	</div>
                 	<div class="mt5">
                 		<span class="pt3" style="font-family: arial, verdana;">
                 			{{ trans('messages.lbl_daterange') }}
                 		</span>
                 		<div class="mt5 box88per" style="display: inline-block!important;">
                 			<span class="CMN_display_block box33per " style="display: inline-block!important;">
                         	{{ Form::text('startdate','',array('id'=>'startdate', 'name' => 'startdate','data-label' => trans('messages.lbl_dob'),'class'=>'box100per datarange','onkeypress' => 'return isNumberKey(event)')) }}
                         	</span>
							<label class="mt10 ml2 fa fa-calendar fa-lg CMN_display_block pr5" 
									for="startdate" aria-hidden="true" style="display: inline-block!important;">
							</label>
							<span class="CMN_display_block box33per " style="display: inline-block!important;">
                         	{{ Form::text('enddate','',array('id'=>'enddate', 'name' => 'enddate','data-label' => trans('messages.lbl_dob'),'class'=>'box100per datarange','onkeypress' => 'return isNumberKey(event)')) }}
                         	</span>
							<label class="mt10 ml2 fa fa-calendar fa-lg CMN_display_block" 
									for="enddate" aria-hidden="true" style="display: inline-block!important;">
							</label>
                 		</div>
                 	</div>
                 	<div class="mt5">
                 		<span class="pt3" style="font-family: arial, verdana;">
                 			{{ trans('messages.lbl_address') }}
                 		</span>
                 		<div class="mt5 box88per" style="display: inline-block!important;">
                 			{!! Form::text('address', $request->address,
	                         array('','id' => 'address','style'=>'height:30px;','class'=>'box93per 
	                         ')) !!}
                 		</div>
                 	</div>
                 	<div class="mt5 mb6">
		                 {{ Form::button(
		                     '<i class="fa fa-search" aria-hidden="true"></i> '.trans('messages.lbl_search'),
		                     array('class'=>'mt10 btn btn-info btn-sm ',
		                     		'onclick' => 'javascript:return umultiplesearch()',
		                           	'type'=>'button')) 
		                 }}
		                <div class="ml10 CMN_display_block vab">
                    <a href = "javascript:void(0);" onclick="clearAll('2');" style="font-family: arial, verdana;" class="tab">{{ trans('messages.lbl_cancel') }}</a>
				            </a>
			            </div>
		            </div>
                 </li>
             </ul>
            </div> 
         </div>
         @if(!empty($cstviews))
          <div class="text-center pl13">
            @if(!empty($detailview->total()))
              <span class="pull-left mt24">
                {{ $detailview->firstItem() }} ~ {{ $detailview->lastItem() }} / {{ $detailview->total() }}
              </span>
            @endif 
            {{ $detailview->links() }}
            <div class="CMN_display_block flr mr10">
              {{ $detailview->linkspagelimit() }}
            </div>  
          </div>
          @endif
  	{{ Form::close() }}
  	</article>
  	</div>
@endsection