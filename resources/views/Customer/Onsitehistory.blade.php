@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/customer.js') }}
 <script type="text/javascript">
    var datetime = '<?php echo date('Ymdhis'); ?>';
  </script> 
  <div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="customer" class="DEC_flex_wrapper " data-category="customer customer_sub_2">
{{ Form::open(array('name'=>'emphistoryviewform', 'id'=>'emphistoryviewform','url' => 'Customer/Onsitehistory?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'), 'method' => 'POST')) }}
    {{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
    {{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
    {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
    {{ Form::hidden('filterval', $request->filterval, array('id' => 'filterval')) }}
    {{ Form::hidden('hdnempid', '$request->hdnempid', array('id' => 'hdnempid')) }}
    {{ Form::hidden('hdnempname', '$request->hdnempname', array('id' => 'hdnempname')) }}
    {{ Form::hidden('hdnback', $request->hdnback, array('id' => 'hdnback')) }}
    {{ Form::hidden('id',$request->id,array('id' => 'id')) }}
    {{ Form::hidden('custid',$request->custid,array('id' => 'custid')) }}
    {{ Form::hidden('viewflg', '3', array('id' => 'viewflg')) }}
    {{ Form::hidden('empid', $request->empid ,array('id' => 'empid')) }}
   {{ Form::hidden('empname', $request->empname ,array('id' => 'empname')) }}
  <div class="row hline">
	<div class="row hline">
     	<div class="col-sm-12">
     	 	<img class="pull-left box40 mt10" src="{{ URL::asset('resources/assets/images/Client.png') }}">
        	<h2 class="pl5 pull-left mt15">{{ trans('messages.lbl_onsitehistory') }}</h2>
      	</div>
  	</div>
  	<div class="box100per pr10 pl10 mt10">
        <div class="mt10 ml10">
          	<a href="javascript:gotoBack('{{ date('YmdHis') }}');" class="btn btn-info box70"><span class="fa fa-arrow-left"></span> {{ trans('messages.lbl_back') }}</a>
        </div>
        <div class="fwb mt5 mb5 ml10">
            {{ trans('messages.lbl_employeeid')}}
          	<span class="ml5 colbl fwb">
            	{{ $request->hdnempid }}
          	</span>
            <span class="ml15 fwb">
            	{{ trans('messages.lbl_empName')}}
            </span>  
          	<span class="fwn ml5">
             {{ $request->hdnempname }} 
          	</span>
        </div>
       <div class="mt10 minh300 ml10 box100per">
    	 <table class="tablealternate CMN_tblfixed box99per">
          <colgroup>
            <col width="5%">
            <col width="15%">
            <col width="15%">
            <col width="15%">
            <col width="15%">
            <col width="">
          </colgroup>
          <thead class="CMN_tbltheadcolor">
            <tr>
              <th>{{ trans('messages.lbl_sno') }}</th>
              <th>{{ trans('messages.lbl_Start_date') }}</th>
              <th>{{ trans('messages.lbl_enddate') }}</th>
              <th>{{ trans('messages.lbl_yearmonth') }}</th>
              <th>{{ trans('messages.lbl_status') }}</th>
              <th>{{ trans('messages.lbl_cusname') }}</th>
            </tr>
          </thead>
           <tbody>
          @if(count($customerhistory)!="")
          @for ($i = 0; $i < count($customerhistory); $i++)
            <tr>
                <td class="text-center">
                  {{ $i+1 }}
                </td>
                <td class="text-center">
                @if($customerhistory[$i]['start_date'] == '0000-00-00' )

                @else
                  {{ $customerhistory[$i]['start_date'] }}
                @endif  
                </td>
                <td class="text-center">
                @if($customerhistory[$i]['end_date'] == '0000-00-00' )

                @else
                  {{ $customerhistory[$i]['end_date'] }}
                @endif 	
                </td>
                <td class="text-center">
                {{ $customerhistory[$i]['experience'] }} Yrs
                </td>
                <td class="text-left">
                @if($customerhistory[$i]['status']=='1')
                	{{ "StayIN"}}
                @elseif($customerhistory[$i]['status']=='2')	
                	{{ "Returned"}}
                @elseif($customerhistory[$i]['status']=='3')	
                	{{ "Client Changed"}}
                @elseif($customerhistory[$i]['status']=='4')	
                	{{ "Others"}}
                @else	
                	{{ "Work End"}}			
                @endif	
                </td>
                <td class="text-left">
                	{{ $customerhistory[$i]['customer_name'] }}
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
     @if(!empty($customerhistory))
    <div class="text-center pl13">
      @if(!empty($cushistory->total()))
        <span class="pull-left mt24">
          {{ $cushistory->firstItem() }} ~ {{ $cushistory->lastItem() }} / {{ $cushistory->total() }}
        </span>
      @endif  
      {{ $cushistory->links() }}
       <div class="CMN_display_block flr mr10">
      {{ $cushistory->linkspagelimit() }}
      </div>
       @endif 
    </div>
    {{ Form::close() }}
</article>
</div>
@endsection