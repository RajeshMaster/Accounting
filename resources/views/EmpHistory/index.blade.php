@extends('layouts.app')
@section('content')
<?php use App\Http\Helpers; ?>
{{ HTML::script('resources/assets/js/customer.js') }}
 <script type="text/javascript">
    var datetime = '<?php echo date('Ymdhis'); ?>';
  </script> 
  <div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="customer" class="DEC_flex_wrapper " data-category="customer customer_sub_1">
{{ Form::open(array('name'=>'emphistoryform', 'id'=>'emphistoryform','url' => 'EmpHistory/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'), 'method' => 'POST')) }}
    {{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
    {{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
    {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
    {{ Form::hidden('filterval', $request->filterval, array('id' => 'filterval')) }}
    {{ Form::hidden('hdnempid', '', array('id' => 'hdnempid')) }}
    {{ Form::hidden('hdnempname', '', array('id' => 'hdnempname')) }}
    {{ Form::hidden('viewflg', '1', array('id' => 'viewflg')) }}
    {{ Form::hidden('id','',array('id' => 'id')) }}
    {{ Form::hidden('custid','',array('id' => 'custid')) }}
    {{ Form::hidden('empid','',array('id' => 'empid')) }}
    {{ Form::hidden('hdnback', '1', array('id' => 'hdnback')) }}
    {{ Form::hidden('pageflg', '1', array('id' => 'pageflg')) }}
  	<div class="row hline">
     	<div class="col-sm-8">
          <img class="pull-left box40 mt10" src="{{ URL::asset('resources/assets/images/employee.png') }}">
        	<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_emphistory') }}</h2>
      	</div>
  	</div>
    <div class="mt10 minh340 ml10 box100per">
    <table class="tablealternate CMN_tblfixed width98">
      <colgroup>
         <col width="4%">
          <col width="7%">
          <col width="">
          <col width="10%">
          <col width="10%">
          <col width="20%">
          <col width="10%">
          <col width="10%">
      </colgroup>
      <thead class="CMN_tbltheadcolor">
        <tr>
          <th>{{ trans('messages.lbl_sno') }}</th>
          <th>{{ trans('messages.lbl_empno') }}</th>
          <th>{{ trans('messages.lbl_name') }}</th>
          <th>{{ trans('messages.lbl_Start_date') }}</th>
          <th>{{ trans('messages.lbl_NoofYears') }}</th>
          <th>{{ trans('messages.lbl_client_name') }}</th>
          <th>{{ trans('messages.lbl_branch') }}</th>
          <th> </th>
        </tr>
      </thead>
    <tbody>
          @if(count($empdetails)!="")
          @for ($i = 0; $i < count($empdetails); $i++)
           
            <tr>
                <td class="text-center">
                  {{ ($emphistory->currentpage()-1) * $emphistory->perpage() + $i + 1 }}
                </td>
                <td class="text-center">
                  {{ $empdetails[$i]['Emp_ID'] }}
                </td>
                <td >
                  {{ empnamelength($empdetails[$i]['LastName'], $empdetails[$i]['FirstName'], 50) }}
                </td>
                <td class="text-center">{{ $empdetails[$i]['StartDate'] }}</td>
                <td class="text-center">
                 @if($empdetails[$i]['experience']== '-')
                 @else 
                  {{ $empdetails[$i]['experience'] }} Yrs
                 @endif
                </td>
                <td class="text-left"><a class="colbl" href="javascript:customerview('{{ date('YmdHis') }}','{{ $empdetails[$i]['id'] }}','{{ $empdetails[$i]['custid'] }}');">
                      {{ $empdetails[$i]['CustomerName'] }}</a></td>
                <td class="text-left">{{ $empdetails[$i]['BranchName'] }}</td>
                <td class="text-center"><a class="colbl"
                      href="javascript:getdetails('{{ $empdetails[$i]['Emp_ID'] }}','{{ $empdetails[$i]['LastName'] }}','{{ date('YmdHis') }}','1');">{{ "Details" }}
                  </a>    
                </td>
            </tr>
          @endfor
          @else
            <tr>
              <td class="text-center fr" colspan="8">
                {{ trans('messages.lbl_nodatafound') }}
              </td>
            </tr>
          @endif
          </tbody>
    </table>
      <div class="text-center">
          @if(!empty($emphistory->total()))
            <span class="pull-left mt24">
              {{ $emphistory->firstItem() }} ~ {{ $emphistory->lastItem() }} / {{ $emphistory->total() }}
            </span>
          {{ $emphistory->links() }}
        <div class="CMN_display_block flr mr18">
          {{ $emphistory->linkspagelimit() }}
        </div>
         @endif
      </div>
  </div>
{{ Form::close() }}
</article>
</div>
@endsection