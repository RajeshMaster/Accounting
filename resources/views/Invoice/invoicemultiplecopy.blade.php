@extends('layouts.app')
@section('content')
<?php use App\Model\Invoice; ?>
<script type="text/javascript">
    var datetime = '<?php echo date('Ymdhis'); ?>';
    var mainmenu = '<?php echo $request->mainmenu; ?>';
    var accessDate = '<?php echo Auth::user()->accessDate; ?>'; 
    var userclassification = '<?php echo Auth::user()->userclassification; ?>';
    var lastMtnLastDay = '<?php echo $lastMtnLastDay; ?>';
    $(document).ready(function() {
        if (userclassification == 1) {
            accessDate = setNextDay(accessDate);
            setDatePickerAfterAccessDate("quot_date", accessDate);
            setDatePickerAfterAccessDate("payment_date", accessDate);
        } else {
            setDatePicker("quot_date");
            setDatePicker("payment_date");
        }
        $('input[name=lstmnthdate]').change(function(){
            if($('input[name=lstmnthdate]').is(':checked')){
                $('#quot_date').val(lastMtnLastDay);
            } else {
                $('#quot_date').val('');
            }
        });
    });

    
</script>

<style type="text/css">
    /*.highlight1 { background-color: #f2eab0 !important; }*/
</style>
{{ HTML::script('resources/assets/js/common.js') }}
{{ HTML::script('resources/assets/js/invoice.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper " data-category="sales sales_sub_2">
    {{ Form::open(array('name'=>'frminvoicemulticopy', 
                        'id'=>'frminvoicemulticopy', 
                        'url' => 'Invoice/invoicecopyprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
                        'files'=>true,
                        'method' => 'POST')) }}
    {{ Form::hidden('table_id','',array('id'=>'table_id','name' => 'table_id')) }}
    {{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
    {{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
    {{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
    {{ Form::hidden('tablecount', $request->tablecount, array('id' => 'tablecount')) }}
    @if (Auth::user()->userclassification == 1) 
        {{ Form::hidden('accessdate',Auth::user()->accessDate, array('id' => 'accessdate')) }}
    @else
        {{ Form::hidden('accessdate','0001-01-01', array('id' => 'accessdate')) }}
    @endif
    <!-- Start Heading --> 
        <div class="row hline">
        <div class="col-xs-12">
            <img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/invoices-icon-3.png') }}">
            <h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_invoice') }}</h2>
            <h2 class="pull-left mt15">・</h2>
            <h2 class="pull-left mt15 blue">{{ trans('messages.lbl_multible') }}</h2>
            <h2 class="pull-left mt15">・</h2>
            <h2 class="pull-left mt15 green">{{ trans('messages.lbl_register') }}</h2>
        </div>
        </div>
        <div class="col-xs-12 pm0 pull-left mb10 pl10 pr10 mt20 fwb">
            {{ trans('messages.lbl_copiedfrom').':' }}
            <span class="mr40 ml12" style="color:brown;">
            {{ $request->selYear }} 年 {{ $request->selMonth }} 月
            </span>
            {{ trans('messages.lbl_invoicedate').':' }}
            <span style="margin-left: 10px">
                {{ Form::text('quot_date', '',array(
                                                'id'=>'quot_date',
                                                'name' => 'quot_date',
                                                'class'=>'box9per form-control quot_date',
                                                'data-label' => trans('messages.lbl_invoicedate'),
                                                'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
                                                'maxlength' => '10')) }}
                <label class="mt10 ml2 fa fa-calendar fa-lg" for="quot_date" aria-hidden="true"></label>
            </span>
            {{ trans('messages.lbl_paymentdate').':' }}
            <span style="margin-left: 10px">
                {{ Form::text('payment_date', '',array(
                                                'id'=>'payment_date',
                                                'name' => 'payment_date',
                                                'class'=>'box9per form-control payment_date',
                                                'data-label' => trans('messages.lbl_paymentdate'),
                                                'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
                                                'maxlength' => '10')) }}
                <label class="mt10 ml2 fa fa-calendar fa-lg" for="payment_date" aria-hidden="true"></label>
            </span>
      </div>
      <div class="col-xs-12 mt5">
        <table class="box99per CMN_tblfixed tablealternate" id = "workspectable">
            <colgroup>
                <col class="tdhead box3per"></col>
                <col class="tdhead box5per"></col>
                <col class="tdhead box10per"></col>
                <col class="tdhead box18per"></col>
                <col class="tdhead box7per"></col>
                <col class="tdhead box7per"></col>
                <col class="tdhead box8per"></col>
            </colgroup>
            <thead class="CMN_tbltheadcolor">
                <tr class="">
                    <th class="">{{ trans('messages.lbl_sno') }}</th>
                    <th class="">{{ trans('messages.lbl_invoiceno') }}</th>
                    <th class="">{{ trans('messages.lbl_companyname') }}</th>
                    <th class="">{{ trans('messages.lbl_workspec') }}</th>
                    <th class="">{{ trans('messages.lbl_quantity') }}</th>
                    <th class="">{{ trans('messages.lbl_unitprice') }}</th>
                    <th class="">{{ trans('messages.lbl_amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @if (count($assignemp) > 0)
                    {{ $temp = ""}}
                    {{--*/ $row = '0' /*--}}
                    {{ $tempuser = ""}}
                    {{--*/ $rowuser = '0' /*--}}
                    {{--*/ $setId = '1' /*--}}
                    {{--*/ $j = '1' /*--}}
                    {{--*/ $srlcntt = '0' /*--}}
                    {{--*/ $cnt = '0' /*--}}
                    {{--*/ $cunt = '0' /*--}}
                    {{--*/ $k = '1' /*--}}
                    {{--*/ $cntt = '1' /*--}}

                    @foreach($assignemp as $key => $value)
                            {{--*/ $loc = $value->company_name /*--}}
                            {{--*/ $locuser = $value->user_id /*--}}
                            @if($loc != $temp && $locuser != $tempuser) 
                            {{--*/ $cntt = '1' /*--}}
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
                            @if($locuser != $tempuser) 
                                @if($rowuser==1)
                                    {{--*/ $style_truser = 'background-color: #E5F4F9;' /*--}}
                                    {{--*/ $rowuser = '0' /*--}}
                                @else
                                    {{--*/ $style_truser = 'background-color: #FFFFFF;' /*--}}
                                    {{--*/ $rowuser = '1' /*--}}
                                @endif
                                {{--*/ $style_tduser = '' /*--}}
                            @else
                                {{--*/ $style_tduser = 'border-top: hidden;' /*--}}
                            @endif

                            <?php $emp_ID = "emp_id"; ?>
                            <?php $workloop = "work_specific"; ?>
                            <?php $quantityloop = "quantity"; ?>
                            <?php $unit_priceloop = "unit_price"; ?>
                            <?php $amountloop = "amount"; ?>
                            @if($value->$workloop != '' || $value->$quantityloop != '' || $value->$unit_priceloop != '' || $value->$amountloop != '')
                                <?php /*$ids = '_'.$setId;*/ ?>
                                <?php //$ids = '_'.$value->id.'_'.$i; 
                                $ids = '_'.$value->id.'_'.$cntt;?>
                            <tr id="<?php echo $ids; ?>" style="{{ $style_tr }}">
                                {{ Form::hidden('id'.$ids,isset($value->id)?$value->id:'',
                                                            array('id'=>'id'.$ids,
                                                                    'name' => 'id'.$ids)) }}
                                <td class="tac" style="{{ $style_tduser }}">
                                    @if($locuser!=$tempuser)
                                    {{--*/ $srlcntt += 1; /*--}}
                                        {{ $srlcntt }}
                                    @endif
                                </td>
                                <td class="tac" style="{{ $style_tduser }}">
                                    @if($locuser!=$tempuser)
                                    {{--*/ $cnt += '1' /*--}}

                                        {{ Form::hidden('invid'.$cnt,isset($value->id)?$value->id:'',
                                                            array('id'=>'invid'.$cnt,
                                                                    'name' => 'invid'.$cnt)) }}
                                        <label>                            
                                        <input type = "checkbox" id = "removecheck<?php echo $key; ?>" name = "addcheck[<?php echo $value->id ?>]" value = "1" onclick = "return fnCheckboxVal1(<?php echo $k; ?>);" checked="checked">
                                        <input type = "hidden" id = "hidid<?php echo $cnt; ?>" name = "hidid<?php echo $cnt; ?>" value = "">

                                        <span class="pm0 vam" style="color:#136E83;">
                                            {{ Form::hidden('user_id'.$cnt,isset($value->user_id)?$value->user_id:'',
                                                            array('id'=>'user_id'.$cnt,
                                                                    'name' => 'user_id'.$cnt)) }}
                                            {{ $value->user_id }}
                                        </span>
                                    </label>
                                    @endif
                                </td>
                                <td style="{{ $style_td }}">
                                    @if($loc!=$temp)
                                        {{ $value->company_name }}
                                    @endif
                                    <input type="hidden" name="count" id="count" value="<?php echo count($assignemp); ?>">
                                </td>
                                <td>
                                    <?php
                                        $getEmpData=Invoice::getemp_details($value->id,$value->emp_id);
                                    ?>
                                    <div class="">
                                        <div style="">
                                            {{ Form::text('work_specific'.$ids, ($value->$workloop) ? $value->$workloop : '',array('id'=>'work_specific'.$ids,
                                                            'name' => 'work_specific'.$ids,
                                                            'maxlength' => 20,
                                                            'data-label' => trans('messages.lbl_UserID'),
                                                            'onchange' => 'this.value=this.value.trim()',
                                                            'onfocusout'=>'fnWorkspecificEmpidDisable(this.id);',
                                                            'class'=>'box99per form-control pl5 mt3')) }}
                                        </div>
                                        @php $cls = "" @endphp
                                        @if($getEmpData[0]->$emp_ID != "")
                                            @php $cls='display: inline' @endphp
                                        @else
                                            @php $cls='display: none' @endphp
                                        @endif
                                        <div id="divid<?php echo $ids ?>" style = "<?php echo $cls; ?>">
                                                
                                                {{ Form::hidden('emp_ID'.$ids,isset($getEmpData[0]->$emp_ID)?$getEmpData[0]->$emp_ID:$request->$emp_ID,
                                                                    array('id'=>'emp_ID'.$ids,
                                                                            'name' => 'emp_ID'.$ids)) }}
                                                <label id="empKanaNames<?php echo $ids ?>" name="empKanaNames<?php echo $ids ?>" style="padding-left: 2px;font-weight: 100 !important;color: black !important; font-size: 80%;">
                                                    @if( isset($getEmpData[0]->KanaName) && $getEmpData[0]->KanaName != '　' )
                                                    {{ $getEmpData[0]->KanaName }}

                                                    @else
                                                            @if(isset($getEmpData[0]->EnglishName))
                                                                {{ $getEmpData[0]->EnglishName }}
                                                            @endif
                                                    @endif
                                                </label>
                                                <a id="crossid<?php echo $ids ?>" onclick="fngetEmpty('<?php echo $ids ?>',);" style="float: right;cursor: pointer !important;display: none;">
                                                    <i class="fa fa-close" aria-hidden="true"></i>
                                                </a>
                                                <script type="text/javascript">
                                                    $(document).ready(function() {
                                                        var empid = $('#emp_ID'+'<?php echo $ids; ?>').val();
                                                        var i = '<?php echo $ids; ?>';
                                                        if (empid != "") {
                                                            $('#crossid'+i).css('display','inline');
                                                        } else {
                                                            // $('#divid'+i).attr('display','none');
                                                        }
                                                    });
                                                </script>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="">
                                        <a onclick="return popupenableempname('{{ $request->mainmenu }}','{{ $ids }}');" 
                                            class="btn btn-success box35 white" style="line-height: 1 !important;">
                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                        </a>
                                            {{ Form::text('quantity'.$ids,($value->$quantityloop) ? $value->$quantityloop : '',array('id'=>'quantity'.$ids,
                                                                'name' => 'quantity'.$ids,
                                                                'style'=>'text-align:right;',
                                                                'maxlength' => 7,
                                                                'data-label' => trans('messages.lbl_UserID'),
                                                                'onkeypress' => 'return isDotNumberKey(event,this.value,1)',
                                                                'ondragstart'=>'return false',
                                                                'ondrop'=>'return false',
                                                                "onkeyup" => "return fnCalculateAmount('$ids', '', '')",
                                                                'class'=>'box65per form-control pl5 mt3')) }}
                                    </div>
                                </td>
                                <td>

                                    <div class="">
                                            <?php 
                                                $color = "";
                                                $bordercolor = "";
                                                if (isset($value->$unit_priceloop)) {
                                                    if ($value->$unit_priceloop < 0) {
                                                        $color = 'color:red;';
                                                        $bordercolor = 'border-color:red;';
                                                    }
                                                } 
                                            ?>
                                            {{ Form::text('unit_price'.$ids,($value->$unit_priceloop) ? $value->$unit_priceloop : '',array('id'=>'unit_price'.$ids,
                                                                'name' => 'unit_price'.$ids,
                                                                'maxlength' => 11,
                                                                'style' => $color.$bordercolor,
                                                                'data-label' => trans('messages.lbl_UserID'),
                                                                'onkeypress' => 'return isNumberKeywithminus(event)',
                                                                'ondragstart'=>'return false',
                                                                'ondrop'=>'return false',
                                                                "onkeyup" => "return fnCalculateAmount('$ids', this.name, this.value)",
                                                                'class'=>'box99per form-control pl5 mt3 tar')) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="">
                                            <?php 
                                                $color = "";
                                                $bordercolor = "";
                                                if (isset($value->$amountloop)) {
                                                    if ($value->$amountloop < 0) {
                                                        $color = 'color:red;';
                                                        $bordercolor = 'border-color:red;';
                                                    }
                                                } 
                                            ?>
                                            {{ Form::text('amount'.$ids,($value->$amountloop) ? $value->$amountloop : '',array('id'=>'amount'.$ids,
                                                                'name' => 'amount'.$ids,
                                                                'style' => $color.$bordercolor,
                                                                'onkeypress' => 'return isNumberKeywithminus(event)',
                                                                'ondragstart'=>'return false',
                                                                'ondrop'=>'return false',
                                                                'data-label' => trans('messages.lbl_UserID'),
                                                                'class'=>'box99per form-control pl5 mt3 tar')) }}
                                    </div>
                                </td>
                                @if($locuser!=$tempuser)
                                    {{--*/ $cunt += '1' /*--}}
                                    {{ Form::hidden('invcount', $cunt,array('id'=>'invcount',
                                                                    'name' => 'invcount')) }}
                                @endif
                            </tr>
                            {{--*/ $temp = $loc /*--}}
                            {{--*/ $tempuser = $locuser /*--}}
                            {{--*/ $setId++ /*--}}
                            {{--*/ $j++ /*--}}
                            {{--*/ $cntt++ /*--}}
                            @endif
                    {{ Form::hidden('count1',$j,array('id' => 'count1')) }}
                        {{--*/ $k++ /*--}}
                    @endforeach
                @else
                    <tr>
                        <td class="text-center" colspan="7" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
        @if (count($assignemp) > 0)
       <?php $style=""; 
        $colour="btn edit btn-success";?>
        @else
        <?php
        $style="disabled"; 
         $colour="btn btn-gray"; ?>
        @endif
        <fieldset style="background-color: #DDF1FA;">
            <div class="form-group">
                <div align="center" class="mt5">
                    <button type="submit"  class="{{$colour}} invmulticopyprocess box100 "{{$style}}>
                            <i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
                    </button>
                    <a onclick="javascript:gotoindexinv('{{$request->mainmenu}}');" 
                            class="btn btn-danger box120 white {{$style}}">
                                    <i class="fa fa-times" aria-hidden="true"></i> 
                                        {{trans('messages.lbl_cancel')}}
                    </a>
                </div>
            </div>
        </fieldset>
    </div>

    {{ Form::close() }}
    {{ Form::open(array('name'=>'involdeditcancel', 'id'=>'involdeditcancel', 'url' => 'Invoice/editempassignprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
    {{ Form::close() }}
</article>
<div id="empnamepopup" class="modal fade">
    <div id="login-overlay">
        <div class="modal-content">
            <!-- Popup will be loaded here -->
        </div>
    </div>
</div>
</div>
@endsection