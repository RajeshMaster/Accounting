@extends('layouts.app')
@section('content')
@php use App\Http\Helpers; @endphp
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
        document.getElementById(divid).style.height= "240px";
      }else {
        document.getElementById(divid).style.display = 'block';
      }
  }
</script>
<style type="text/css">
  .sort_asc {
    background-image:url({{ URL::asset('resources/assets/images/upArrow.png') }}) !important;
  }
  .sort_desc {
    background-image:url({{ URL::asset('resources/assets/images/downArrow.png') }}) !important;
  }
</style>
{{ HTML::script('resources/assets/js/engineerdetails.js') }}
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::style('resources/assets/css/switch.css') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::script('resources/assets/js/lib/lightbox.js') }}
{{ HTML::style('resources/assets/css/lib/lightbox.css') }}
<div class="CMN_display_block" id="main_contents">
    <!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper " data-category="sales sales_sub_7">
  {{ Form::open(array('name'=>'frmcustomerplusindex', 
            'id'=>'frmcustomerplusindex', 
            'url' => 'Engineerdetails/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
            'files'=>true,
            'method' => 'POST')) }}
      {{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
      {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
      {{ Form::hidden('sorting', $request->sorting, array('id' => 'sorting')) }}
      {{ Form::hidden('searchmethod', $request->searchmethod, array('id' => 'searchmethod')) }}
      {{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
      {{ Form::hidden('ordervalue', $request->ordervalue, array('id' => 'ordervalue')) }}
      {{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
      {{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
      {{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
      {{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
      {{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
      {{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}
      {{ Form::hidden('engineeridClick', '' , array('id' => 'engineeridClick')) }}
 <!-- Start Heading -->   
      <div class="row hline">
        <div class="col-sm-12 mt10">
          <img class="pull-left box40 mt5" src="{{ URL::asset('resources/assets/images/Client.png') }}">
              <h2 class="pl5 pull-left mt10">{{ trans('messages.lbl_engg_details') }}</h2>
        </div>
      </div>
      @if($request->searchmethod =="6" || $request->searchmethod=="")
        <div class="box100per pr10 pl10 mt10">
          <div class="mt10">
            {{ Helpers::displayYear_MonthEst($account_period, $year_month, $db_year_month, $date_month, $dbnext, $dbprevious,$last_year, $current_year, $account_val) }}
          </div>
        </div> 
     
      @endif
<!-- End Heading -->
     @if($request->searchmethod !="3")
     <div class="col-xs-12 pm0 pull-left mb10 pl10 mt10 pr10">
        <div class="form-group pm0 pull-right moveleft nodropdownsymbol" id="moveleft">
          <a href="javascript:clearsearch()" title="Clear Search">
            <img class="pull-left box30 mr5 " src="{{ URL::asset('resources/assets/images/clearsearch.png') }}">
          </a>
          {{ Form::select('engineerdetailssort', [null=>''] + $engineerdetailssortarray, $request->engineerdetailssort,
                              array('class' => 'form-control'.' ' .$request->sortstyle.' '.'CMN_sorting pull-right',
                             'id' => 'engineerdetailssort',
                              'style' => $sortMargin,
                             'name' => 'engineerdetailssort'))
          }}
        </div>
      </div>
      @endif
      @if($request->searchmethod =="3")
      <div class="col-xs-12 pm0 pull-left mb10 pl10 pr10 mt20 fwb">
        {{ trans('messages.lbl_employeeid').':' }}
          <span class="mr40 ml12" style="color:blue;">
            {{ $engineerdet[0]->EMPID }}
          </span>
            {{ trans('messages.lbl_empName').':' }}
          <span style="color:#9C0000;margin-left: 10px">
            {{ $engineerdet[0]->LastName }}
            </span>
      </div>
      @endif
      <div class="minh350 box100per pl10 pr10">
        <table class="tablealternate box100per CMN_tblfixed width98">
          <colgroup>
            @if($request->searchmethod =="3")
            <col width="2%">
            <col width="6%">
            <col width="6%">
            @else
            <col width="4%">
            <col width="9%">
            <col width="9%">
            @endif
            @if($request->searchmethod !="3")
            <col width="10%">
            <col width="12%"> 
            @endif
            @if($request->searchmethod =="3")
            <col width="20%">
            <col width="10%">
            <col width="6%">
            @else
            <col width="20%"> 
            <col width="12%"> 
           	<col width="12%">
            @endif
          </colgroup>
          <thead class="CMN_tbltheadcolor">
            <tr class="tableheader fwb tac"> 
              <th class="tac">{{ trans('messages.lbl_sno') }}</th>
              <th class="tac">{{ trans('messages.lbl_invoiceno') }}</th>
              <th class="tac">{{ trans('messages.lbl_dateofissue') }}</th>
              @if($request->searchmethod !="3")
              <th class="tac">{{ trans('messages.lbl_employeeid') }}</th>
              <th class="tac">{{ trans('messages.lbl_empName') }}</th>
              @endif
              <th class="tac">{{ trans('messages.lbl_companyname') }}</th>            
              <th class="tac">{{ trans('messages.lbl_content') }}</th>
              <th class="tac">{{ trans('messages.lbl_estamount') }}</th>
            </tr>
          </thead>
          <tbody>
            @if($request->searchmethod !="3")
              <tr style="background-color: #DDDDDD;">
                <td colspan="6"></td>
                <td align="right" style="font-weight: 700">{{ trans('messages.lbl_total') }}</td>
                <td align="right" style="color: blue;font-weight: 700;">{{ number_format($grandtotal) }}</td>
              </tr>
            @else
              <tr style="background-color: #DDDDDD;">
                <td colspan="4"></td>
                <td align="right" style="font-weight: 700">{{ trans('messages.lbl_total') }}</td>
                <td align="right" style="color: blue;font-weight: 700;">{{ number_format($grandtotal) }}</td>
              </tr>
            @endif
                  {{ $temp = ""}}
                  {{--*/ $row = '0' /*--}}
                  {{ $tempcomp = ""}}
                  {{--*/ $rowcomp = '0' /*--}}
                  {{ $tempcompany = ""}}
                  {{--*/ $rowcompany = '0' /*--}}
                  {{ $tempemp = ""}}
                  {{--*/ $rowemp = '0' /*--}}
                  {{ $tempname = ""}}
                  {{--*/ $rowname = '0' /*--}}
            <?php $i=0; $style_tr=""; $style_trcomp=""; ?>
             @if(count($engineerdet)!="")
             @foreach ($engineerdet as $key => $value) 
              {{--*/ $loc = $value->company_name /*--}}
              {{--*/ $loccomp = $value->quot_date /*--}}
              {{--*/ $loccompany = $value->company_name /*--}}
              {{--*/ $locempid = $value->EMPID /*--}}
              {{--*/ $locname = $value->LastName /*--}}	
                @if($loc != $temp) 
                  @if($row==1)
                      {{--*/ $style_tr = 'background-color: #E5F4F9;' /*--}}
                      {{--*/ $row = '0' /*--}}
                  @else
                      {{--*/ $style_tr = 'background-color: #FFFFFF;' /*--}}
                      {{--*/ $row = '1' /*--}}
                  @endif
                    {{--*/ $style_td = '' /*--}}
                @else
                  {{--*/ $style_td = 'border-top: hidden;' /*--}}
             @endif
              @if($loccomp != $tempcomp) 
                @if($rowcomp==1)
                  {{--*/ $style_trcomp = 'background-color: #E5F4F9;' /*--}}
                  {{--*/ $rowcomp = '0' /*--}}
                @else
                  {{--*/ $style_trcomp = 'background-color: #FFFFFF;' /*--}}
                  {{--*/ $rowcomp = '1' /*--}}
                @endif
                  {{--*/ $style_tdcomp = '' /*--}}
              @else
                {{--*/ $style_tdcomp = 'border-top:hidden;' /*--}}
              @endif
            @if($loccompany != $tempcompany) 
                @if($rowcompany==1)
                	{{--*/ $style_trcompany = 'background-color: #E5F4F9;' /*--}}
                	{{--*/ $rowcompany = '0' /*--}}
                @else
                	{{--*/ $style_trcomp = 'background-color: #FFFFFF;' /*--}}
                	{{--*/ $rowcompany = '1' /*--}}
                @endif
                	{{--*/ $style_tdcompany = '' /*--}}
            @else
              	{{--*/ $style_tdcompany = 'border-top:hidden;' /*--}}
            @endif
            @if($locempid != $tempemp) 
                @if($rowemp==1)
                	{{--*/ $style_tremp = 'background-color: #E5F4F9;' /*--}}
                	{{--*/ $rowemp = '0' /*--}}
                @else
                	{{--*/ $style_tremp = 'background-color: #FFFFFF;' /*--}}
                	{{--*/ $rowemp = '1' /*--}}
                @endif
                	{{--*/ $style_tdemp = '' /*--}}
            @else
              	{{--*/ $style_tdemp = 'border-top:hidden;' /*--}}
            @endif
            @if($locname != $tempname) 
                @if($rowname==1)
                	{{--*/ $style_trname = 'background-color: #E5F4F9;' /*--}}
                	{{--*/ $rowname = '0' /*--}}
                @else
                	{{--*/ $style_trname = 'background-color: #FFFFFF;' /*--}}
                	{{--*/ $rowname = '1' /*--}}
                @endif
                	{{--*/ $style_tdname = '' /*--}}
            @else
              	{{--*/ $style_tdname = 'border-top:hidden;' /*--}}
            @endif
              <tr style="{{$style_tr}}">
              <td class="text-center">
                {{ $key + 1 }}
              </td>
              <td class="" style="{{$style_td}}" >
                <div class="tac">
                  <label class="pm0 vam" style="color:#136E83;">
                    @if($loc!=$temp)
                      {{ $value->InvoiceNo }}
                    @endif
                  </label>
                </div>
              </td>
                <td class="" style="{{$style_tdcomp}}">
                <div class="tac">
                  @if($loccomp!=$tempcomp)
                    {{ $value->quot_date }} 
                  @endif 
                </div>
                </td>
                  @if($request->searchmethod !="3")
                <td class="" style="{{$style_tdemp}}">
                    <div class="tac">
                      @if($locempid != $tempemp)
                        {{ $value->EMPID }}
                      @endif
                    </div>
                </td>
                <td  style="{{$style_tdname}}" @if(mb_strlen($value->LastName, 'UTF-8') > 8)
                    title ="{{ $value->LastName }}" @endif>
                    <a class="colbl tdn" name="namevalue" href="javascript:engineeridclick(' {{ $value->EMPID }}');" class="blue">
                      @if($locname!=$tempname)	
                      @if(mb_strlen($value->LastName, 'UTF-8') > 8)
                        @php echo mb_substr($value->LastName, 0, 12, 'UTF-8')."..." @endphp
                      @else
                        	{{ $value->LastName }}
                      @endif
                      @endif
                    </a>
                </td>
                      @endif
                  <td class="" style="{{$style_tdcompany}}">
                    <div class="tal">
                    	@if($loccompany!=$tempcompany)
                        	{{ $value->company_name }}
                  		@endif
                    </div>
                  </td>
                    <td class="">
                      <div class="fll">
                            {{ $value->work_spec }}
                      </div>
                    </td>
                    <td class="" align="right" style="padding-right: 5px;">
                            {{ $value->amount }}
                    </td>
                </tr>
                  {{--*/ $temp = $loc /*--}}
                  {{--*/ $tempcomp = $loccomp /*--}}
                  {{--*/ $tempcompany = $loccompany /*--}}
                  {{--*/ $tempemp = $locempid /*--}}
                  {{--*/ $tempname = $locname /*--}}
            @endforeach
            @else
            <tr>
              @if($request->searchmethod =="3")
              <td class="text-center fr" colspan="6">
              @else
              <td class="text-center fr" colspan="8">
              @endif  
              {{ trans('messages.lbl_nodatafound') }}</td>
            </tr>
            @endif  
          </tbody>
        </table>
        
            @if(!empty($engineerdet->total()))
              <div class="text-center">
                <span class="pull-left mt24">
                  {{ $engineerdet->firstItem() }} ~ {{ $engineerdet->lastItem() }} / {{ $engineerdet->total() }}
                </span>
                   {{ $engineerdet->links() }}
                    <div class="CMN_display_block flr">
                      {{ $engineerdet->linkspagelimit() }}
                    </div>
              </div>
            @endif
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
                    {{ trans('messages.lbl_empName') }}
                  </span>
                  <div class="mt5 box88per" style="display: inline-block!important;">
                      {!! Form::text('employeename', $request->employeename,
                           array('','id' => 'employeename','style'=>'height:30px;','class'=>'box93per 
                           ')) !!}
                  </div>
               @if($request->searchmethod =="3")
                  <div>
                       <B>{{ trans('messages.lbl_total') }} &nbsp : &nbsp {{ number_format($grandtotal) }}</B>
                 </div> 
             @else
                <div>
                  <B>{{ trans('messages.lbl_total') }} &nbsp : &nbsp {{ number_format($grandtotal) }}</B>
                </div>
              @endif

                </div>
                  <div class="mt5 mb6">
                     {{ Form::button(
                         '<i class="fa fa-search" aria-hidden="true"></i> '.trans('messages.lbl_search'),
                         array('class'=>'mt10 btn btn-info btn-sm ',
                            'onclick' => 'javascript:return umultiplesearch()',
                                'type'=>'button')) 
                     }}
                  </div>
                  
               </li>
             </ul>
          </div>
        </div>
      {{ Form::close() }}
  	</article>
  </div>
@endsection