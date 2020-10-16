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
    var selectjsonArray = '<?php echo $selectjsonArray; ?>';
</script>
{{ HTML::script('resources/assets/js/common.js') }}
{{ HTML::script('resources/assets/js/invoice.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper " data-category="sales sales_sub_2">
    {{ Form::open(array('name'=>'frminvoiceaddedit', 
                        'id'=>'frminvoiceaddedit', 
                        'url' => 'Invoice/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
                        'files'=>true,
                        'method' => 'POST')) }}
        {{ Form::hidden('editflg', $request->editflg, array('id' => 'editflg')) }}
        {{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
        {{ Form::hidden('estimateid', $request->invoiceid, array('id' => 'estimateid')) }}
        {{ Form::hidden('invoiceid', $request->invoiceid, array('id' => 'invoiceid')) }}
        {{ Form::hidden('regflg', $regflag, array('id' => 'regflg')) }}
        {{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
        {{ Form::hidden('filter', $request->filter, array('id' => 'filter')) }}
        {{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
        {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
        {{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
        {{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
        {{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
        {{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
        {{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
        {{ Form::hidden('topclick', $request->topclick, array('id' => 'topclick')) }}
        {{ Form::hidden('sortOptn',$request->invoicesort , array('id' => 'sortOptn')) }}
        {{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
        {{ Form::hidden('ordervalue', $request->ordervalue, array('id' => 'ordervalue')) }}
        {{ Form::hidden('totalRec', $request->totalrecords, array('id' => 'totalRec')) }}
        {{ Form::hidden('currentRec', $request->currentRec, array('id' => 'currentRec')) }}
        {{ Form::hidden('rowCount','',array('id' => 'rowCount')) }}
        {{ Form::hidden('branch_selection', $invoicedata[0]->branch_selection, array('id' => 'branch_selection')) }}
        @if (Auth::user()->userclassification == 1) 
            {{ Form::hidden('accessdate',Auth::user()->accessDate, array('id' => 'accessdate')) }}
        @else
            {{ Form::hidden('accessdate','0001-01-01', array('id' => 'accessdate')) }}
        @endif
        {{ Form::hidden('table_id','',array('id'=>'table_id','name' => 'table_id')) }}
        {{ Form::hidden('tablecount', $amtcount, array('id' => 'tablecount')) }}

    <!-- Start Heading --> 
        <div class="row hline">
        <div class="col-xs-8">
            <img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/invoices-icon-3.png') }}">
            <h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_invoice') }}</h2>
            <h2 class="pull-left mt15">・</h2>
                    @if($regflag==1)
                            <h2 class="pull-left mt15 green">{{ trans('messages.lbl_register') }}</h2>
                    @elseif ($regflag==2)
                            <h2 class="pull-left mt15 blue">{{ trans('messages.lbl_multiple') }}</h2>
                    @else
                            <h2 class="pull-left mt15 red">{{ trans('messages.lbl_edit') }}</h2>
                            {{ Form::hidden('invid', $invoicedata[0]->id, array('id' => 'invid')) }}
                    @endif
        </div>
    </div>
    <div class="pb10"></div>
    <fieldset class="ml10">
    <div class="col-xs-12  mt15">
        <div class="col-xs-6">
        <fieldset class="ml10 mr10" style="border: 1px solid #79d7ec !important;">
            <div class="col-xs-12 mt15">
                <div class="col-xs-4 text-right clr_blue">
                    <label>{{ trans('messages.lbl_customer') }}</label>
                </div>
                <div class="col-xs-8 pm0">
                    @if($regflag==1 || $regflag==2)
                        {{ $invoicedata[0]->company_name }}
                        {{ Form::hidden('company_name', $invoicedata[0]->company_name, array('id' => 'company_name')) }}
                        {{ Form::hidden('tradename', $invoicedata[0]->trading_destination_selection, array('id' => 'tradename')) }}
                    @else
                    {{ Form::select('trading_destination_sel',array(
                null=>'','Recent Customer' => $recentcustomer,'Existing Customer' => $existingcustomer),(isset($invoicedata[0]->customer_id)?$invoicedata[0]->customer_id:''), 
                                    array('name' => 'trading_destination_sel',
                                          'id'=>'trading_destination_sel',
                                          'style'=>'min-width: 30%;',
                                          'data-label' => trans('messages.lbl_customer'),
                                          'onchange'=>'javascript:fngetsubsubject(this.value);',
                                          'class'=>'pl5')) }}
                    {{ Form::hidden('companynames', '', array('id' => 'companynames')) }}
                    {{ Form::hidden('companyid', '', array('id' => 'companyid')) }}
                    @endif
                    {{ Form::hidden('customer_id', $invoicedata[0]->customer_id, array('id' => 'customer_id')) }}
                </div>
            </div>
            <div class="col-xs-12 mt5">
                <div class="col-xs-4 text-right clr_blue">
                    <label>{{ trans('messages.lbl_branch') }}</label>
                </div>
                <div class="col-xs-8 pm0">
                    @if($regflag==1)
                    {{ Form::hidden('brasel', (isset($invoicedata[0]->branch_selection)?$invoicedata[0]->branch_selection:''), array('id' => 'brasel')) }}
                        
                    @endif
                    {{ Form::select('branchname_sel',[null=>''],'', 
                                array('name' => 'branchname_sel',
                                      'id'=>'branchname_sel',
                                      'data-label' => trans('messages.lbl_branchname'),
                                      'onchange' => 'javascript:fncleardataset();',
                                      'style'=>'min-width: 30%;',
                                      'class'=>'pl5'))}}
                </div>
            </div>
            <div class="col-xs-12 mt5">
                <div class="col-xs-4 text-right clr_blue">
                    <label>{{ trans('messages.lbl_incharge') }}</label>
                </div>
                <div class="col-xs-8 pm0">
                    {{ Form::text('projectpersonal',(isset($invoicedata[0]->project_personal)) ? $invoicedata[0]->project_personal : '',array('id'=>'projectpersonal',
                                                        'name' => 'projectpersonal',
                                                        'data-label' => trans('messages.lbl_incharge'),
                                                        'class'=>'form-control pl5 box50per')) }}
                </div>
            </div>
            <div class="col-xs-12 mt5">
                <div class="col-xs-4 text-right clr_blue">
                    <label>{{ trans('messages.lbl_projecttitle') }}</label> 
                </div>
                <div class="col-xs-8 pm0">
                    {{ Form::text('project_name', (isset($invoicedata[0]->project_name)) ? $invoicedata[0]->project_name : '',array('id'=>'project_name',
                                                        'name' => 'project_name',
                                                        'data-label' => trans('messages.lbl_projecttitle'),
                                                        'class'=>'form-control pl5 box50per')) }}
                </div>
            </div>
            <div class="col-xs-12 mt5">
                <div class="col-xs-4 text-right clr_blue">
                    <label>{{ trans('messages.lbl_projecttype') }}</label>
                </div>
                <div class="col-xs-8 pm0">
                    {{ Form::select('projecttype_sel',[null=>'']+$prjtypequery,(isset($invoicedata[0]->project_type_selection)) ? $invoicedata[0]->project_type_selection : '',
                                    array('name' => 'projecttype_sel',
                                          'id'=>'projecttype_sel',
                                          'data-label' => trans('messages.lbl_projectype'),
                                          'style'=>'min-width: 30%;',
                                          'class'=>'pl5'))}}
                    {{ Form::hidden('protype', $invoicedata[0]->project_type_selection, array('id' => 'protype')) }}
                </div>
            </div>
            <div class="col-xs-12 mt5">
                <div class="col-xs-4 text-right clr_blue"> 
                    <label>{{ trans('messages.lbl_bank_name') }}</label>
                </div>
                <div class="col-xs-8 pm0">
                    {{ Form::select('bankname_sel',[null=>'']+$get_bank_query, $selectval,
                                    array('name' => 'bankname_sel',
                                          'id'=>'bankname_sel',
                                          'data-label' => trans('messages.lbl_bank_name'),
                                          'onchange'=>'javascript:fnbankaccountdetail(this.value);',
                                          'style'=>'min-width: 30%;',
                                          'class'=>'pl5'))}}
                    {{ Form::hidden('bank_id', (isset($invoicedata[0]->bankid)?$invoicedata[0]->bankid:""), array('id' => 'bank_id')) }}
                </div>
            </div>
            <div class="col-xs-12 mt5">
                <div class="col-xs-4 text-right clr_blue">
                    <label>{{ trans('messages.lbl_branch_name') }}</label>
                </div>
                <div class="col-xs-8 pm0">
                    <label id="invbranchname"> </label>
                    {{ Form::hidden('invoicebranchname', '', array('id' => 'invoicebranchname')) }}
                </div>
            </div>
            <div class="col-xs-12 mt5">
                <div class="col-xs-4 text-right clr_blue">
                    <label>{{ trans('messages.lbl_account') }}</label>
                </div>
                <div class="col-xs-1 pm0">
                    <label id="invacttype"> </label>
                </div>
                <div class="col-xs-4 pm0">
                    <label><span id="invaccount">   </span></label>
                    {{ Form::hidden('invoiceacctnumb', '', array('id' => 'invoiceacctnumb')) }}
                </div>
            </div>
            <div class="col-xs-12 mt5 mb10">
                <div class="col-xs-4 text-right clr_blue">
                    <label>{{ trans('messages.lbl_accountholder') }}</label>
                </div>
                <div class="col-xs-8 pm0">
                    <label id="invactholder"> </label>
                    {{ Form::hidden('invoiceaccthold', '', array('id' => 'invoiceaccthold')) }}
                </div>
            </div>
        </fieldset>
        </div>
        <div class="col-xs-6">
            @if (Session::get('userclassification') == 4)
            <div class="col-xs-12 mt15 pm0">
                <div class="col-xs-4 pm0"></div>
                <div class="col-xs-8 pm0">
                @if($regflag==2)
                    &nbsp;&nbsp;{{ Form::checkbox('accessrights', 1, 1, 
                            ['id' => 'accessrights']) }}
                            &nbsp;<label for="accessrights"><span class="grey fb">{{ trans('messages.lbl_accessrights') }}</span></label>
                @else
                    &nbsp;&nbsp;{{ Form::checkbox('accessrights', 1, (isset($invoicedata[0]->accessFlg)?$invoicedata[0]->accessFlg:1), 
                            ['id' => 'accessrights']) }}
                            &nbsp;<label for="accessrights"><span class="grey fb">{{ trans('messages.lbl_accessrights') }}</span></label>
                @endif
                </div>
            </div>
            @else
            <div class="col-xs-12  mt5">
                <div class="col-xs-4 text-right clr_blue">
                    <label class="mt2">{{ trans('messages.lbl_invoice') }} {{ trans('messages.lbl_Date') }}<span style="visibility: hidden;"> * </span></label>
                </div>
                <div class="col-xs-8 pm0">
                    @if($regflag==1 || $regflag==2)
                    {{ Form::text('quot_date', '',array(
                                            'id'=>'quot_date',
                                            'name' => 'quot_date',
                                            'autocomplete'=>'off',
                                            'class'=>'box30per form-control quot_date',
                                            'data-label' => trans('messages.lbl_Date'),
                                            'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
                                            'maxlength' => '10')) }}
                    @else
                    {{ Form::text('quot_date', (isset($invoicedata[0]->quot_date)?$invoicedata[0]->quot_date:''),array(
                                            'id'=>'quot_date',
                                            'name' => 'quot_date',
                                            'autocomplete'=>'off',
                                            'class'=>'box30per form-control quot_date',
                                            'data-label' => trans('messages.lbl_Date'),
                                            'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
                                            'maxlength' => '10')) }}
                    @endif
                    <label class="mt10 ml2 fa fa-calendar fa-lg" for="quot_date" aria-hidden="true"></label>
                </div>
            </div>
            @endif
            @if (Session::get('userclassification') == 4)
            <div class="col-xs-12  mt5">
                <div class="col-xs-4 text-right clr_blue">
                    <label class="mt2">{{ trans('messages.lbl_invoice') }} {{ trans('messages.lbl_Date') }}<span style="visibility: hidden;"> * </span></label>
                </div>
                <div class="col-xs-8 pm0">
                    @if($regflag==1 || $regflag==2)
                    {{ Form::text('quot_date', '',array(
                                            'id'=>'quot_date',
                                            'name' => 'quot_date',
                                            'autocomplete'=>'off',
                                            'class'=>'box30per form-control quot_date',
                                            'data-label' => trans('messages.lbl_Date'),
                                            'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
                                            'maxlength' => '10')) }}
                    @else
                    {{ Form::text('quot_date', (isset($invoicedata[0]->quot_date)?$invoicedata[0]->quot_date:''),array(
                                            'id'=>'quot_date',
                                            'name' => 'quot_date',
                                            'autocomplete'=>'off',
                                            'class'=>'box30per form-control quot_date',
                                            'data-label' => trans('messages.lbl_Date'),
                                            'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
                                            'maxlength' => '10')) }}
                    @endif
                    <label class="mt10 ml2 fa fa-calendar fa-lg" for="quot_date" aria-hidden="true"></label>
                    @if($regflag==1 || $regflag==2)
                        {{ Form::checkbox('lstmnthdate', 1, null, 
                            ['id' => 'lstmnthdate','name' => 'lstmnthdate']) }}
                            &nbsp;<label for="lstmnthdate"><span class="grey fb">{{ trans('messages.lbl_lastmonthdate') }}
                    @endif
                </div>
            </div>
            @endif
            @if (Session::get('userclassification') == 4)
            <div class="col-xs-12 mt5"></div>
            @else
            <div class="col-xs-12 mt55"></div>
            @endif
            <fieldset class="ml10 mr10" style="border: 1px solid #79d7ec !important;">
            <div class="col-xs-12 mt5">
                <div class="col-xs-4 text-right clr_blue">
                    <label>{{ trans('messages.lbl_consumptiontax') }}<span style="visibility: hidden;"> * </span> </label>
                </div>
                <div class="col-xs-8 pm0">
                    <label>
                        @if($invoicedata[0]->tax == 1)
                            {{trans('messages.lbl_withoutax')}}
                        @else
                            {{trans('messages.lbl_withtax')}}
                        @endif
                    </label>
                    {{ Form::hidden('tax', $invoicedata[0]->tax, array('id' => 'tax')) }}
                </div>
            </div>
            <div class="col-xs-12 mt5">
                <div class="col-xs-4 text-right clr_blue">
                    <label>{{ trans('messages.lbl_paymentday') }}<span class="fr ml2 red" style="visibility: hidden;"> * </span></label>
                </div>
                <div class="col-xs-8 pm0" style="display: inline-block;">
                @if($regflag==1 || $regflag==2)
                    {{ Form::text('payment_date', "",array(
                                            'id'=>'payment_date',
                                            'autocomplete'=>'off',
                                            'name' => 'payment_date',
                                            'class'=>'box30per form-control payment_date',
                                            'data-label' => trans('messages.lbl_paymentdate'),
                                            'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
                                            'maxlength' => '10')) }}
                @else
                    {{ Form::text('payment_date', (isset($invoicedata[0]->payment_date)?$invoicedata[0]->payment_date:""),array(
                                            'id'=>'payment_date',
                                            'autocomplete'=>'off',
                                            'name' => 'payment_date',
                                            'class'=>'box30per form-control payment_date',
                                            'data-label' => trans('messages.lbl_paymentdate'),
                                            'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
                                            'maxlength' => '10')) }}
                @endif
                    <label class="mt10 ml2 fa fa-calendar fa-lg" for="payment_date" aria-hidden="true"></label>
                </div>
            </div>
            <div class="col-xs-12 mt5">
                <div class="col-xs-4 text-right clr_blue"> 
                    <label>{{ trans('messages.lbl_companymark') }}<span class="fr ml2 red" style="visibility: hidden;"> * </span></label>
                </div>
                <div class="col-xs-8 pm0" style="display: inline-block;">
                    <label>
                        <div class="fll">
                        {{ Form::radio('mark', '1', (isset($invoicedata[0]->company_sign) && ($invoicedata[0]->company_sign)=="1") ? $invoicedata[0]->company_sign : '', array('id' =>'mark1',
                                                                    'name' => 'mark',
                                                                    'data-label' => trans('messages.lbl_tax'),
                                                                    'class' => 'amtrup')) }}
                        </div>
                        <div class="fll">
                            <label class="ml5 mt3 fwn" for="mark1">{{ trans('messages.lbl_need') }}</label>
                        </div>
                    </label>
                    <label>
                        <div class="fll">
                        {{ Form::radio('mark', '2', (isset($invoicedata[0]->company_sign) && ($invoicedata[0]->company_sign)=="2") ? $invoicedata[0]->company_sign : '', array('id' =>'mark2',
                                                                    'name' => 'mark',
                                                                    'data-label' => trans('messages.lbl_tax'),
                                                                    'class' => 'amtrup')) }}
                        </div>
                        <div class="fll">
                            <label class="ml5 mt3" style="font-weight: normal;" for="mark2">{{ trans('messages.lbl_unnecessary') }}</label>
                        </div>
                    </label>
                </div>
            </div>
            <div class="col-xs-12 mt5">
                <div class="col-xs-4 text-right clr_blue">
                    <label class="pm0">{{ trans('messages.lbl_imprints') }}<span class="fr ml2 red" style="visibility: hidden;"> * </span></label>
                </div>
                <div class="col-xs-8" style="display: inline-block;padding: 0px;">
                    <label>
                        <div class="fll">
                        {{ Form::radio('impre', '1', (isset($invoicedata[0]->imprint) && ($invoicedata[0]->imprint)=="1") ? $invoicedata[0]->imprint : '', array('id' =>'impre1',
                                                                    'name' => 'impre',
                                                                    'data-label' => trans('messages.lbl_tax'),
                                                                    'class' => 'amtrup')) }}
                        </div>
                        <div class="fll">
                            <label class="ml5 mt3" style="font-weight: normal;" for="impre1">{{ trans('messages.lbl_need') }}</label>
                        </div>
                    </label>
                    <label>
                        <div class="fll">
                        {{ Form::radio('impre', '2', (isset($invoicedata[0]->imprint) && ($invoicedata[0]->imprint)=="2") ? $invoicedata[0]->imprint : '', array('id' =>'impre2',
                                                                    'name' => 'impre',
                                                                    'data-label' => trans('messages.lbl_tax'),
                                                                    'class' => 'amtrup')) }}
                        </div>
                        <div class="fll">
                            <label class="ml5 mt3" style="font-weight: normal;" for="impre2">{{ trans('messages.lbl_unnecessary') }}</label>
                        </div>
                    </label>
                </div>
            </div>
            <div class="col-xs-12 mt5">
                <div class="col-xs-4 text-right clr_blue">
                    <label>{{ trans('messages.lbl_personalmark') }}<span class="fr ml2 red" style="visibility: hidden;"> * </span></label>
                </div>
                <div class="col-xs-8 pm0" style="display: inline-block;">
                    {{ Form::select('projecttype_sel',[null=>''],'',
                                    array('name' => 'projecttype_sel',
                                          'id'=>'projecttype_sel',
                                          'data-label' => trans('messages.lbl_projectype'),
                                          'style'=>'min-width: 30%;background-color:#EEEEEE;',
                                          'disabled' => 'disabled',
                                          'class'=>'pl5'))}}
                </div>
            </div>
            <div class="col-xs-12 mt5 mb10">
                <div class="col-xs-4 text-right clr_blue">
                    <label>{{ trans('messages.lbl_approvermark') }}<span class="fr ml2 red" style="visibility: hidden;"> * </span></label>
                </div>
                <div class="col-xs-8 pm0" style="display: inline-block;">
                    {{ Form::select('projecttype_sel',[null=>''], '',
                                    array('name' => 'projecttype_sel',
                                          'id'=>'projecttype_sel',
                                          'data-label' => trans('messages.lbl_projectype'),
                                          'style'=>'min-width: 30%;background-color:#EEEEEE;',
                                          'disabled' => 'disabled',
                                          'class'=>'pl5'))}}
                </div>
            </div>
            </fieldset>
        </div>
    </div>
        <div class="col-xs-12">
            <div class="col-xs-6">
            </div>
            <div class="col-xs-6" style="padding: 1px;">
                <a href="javascript:;" onclick="javascript:fncleardataset();" id="undo" class="btn btn-disabled pull-right box75 disabled">{{ trans('messages.lbl_undo') }}</a>
                <a href="javascript:fngetbillingdetails();" id="getdetails" class="btn btn-success pull-right box105 mr10">{{ trans('messages.lbl_getdetails') }}</a>
            </div>
        </div>
        <div class="col-xs-12 mt5">
            <table class="box99per CMN_tblfixed tablealternate ml10" id = "workspectable">
                <colgroup>
                    <col class="tdhead box13per"></col>
                    <col class="tdhead box7per"></col>
                    <col class="tdhead box7per"></col>
                    <col class="tdhead box9per"></col>
                    <col class="tdhead box10per"></col>
                    <col class="tdhead box3per"></col>
                    <col class="tdhead box2per"></col>
                </colgroup>
                <thead class="CMN_tbltheadcolor">
                    <tr class="">
                        <th class="">{{ trans('messages.lbl_workspec') }}</th>
                        <th class="">{{ trans('messages.lbl_quantity') }}</th>
                        <th class="">{{ trans('messages.lbl_unitprice') }}</th>
                        <th class="">{{ trans('messages.lbl_amount') }}</th>
                        <th class="">{{ trans('messages.lbl_remarks') }}</th>
                        <th class="" colspan="2" onclick="javascript:cloneaddblade();"><a id="add_row" 
                        class=" pull-center ml10 imgtableheight  box3per csrp"
                        style="cursor: pointer; color: white;" 
                        >{{ trans('messages.lbl_add') }}</a></th>
                    </tr>
                </thead>
                <tbody id="forccappend">
                    <?php 
                        if($amtcount<15) {
                            $a=15;
                        } else {
                            $a= $amtcount;
                        }
                        ?>
                @for ($i = 1; $i <= $a; $i++)
                    <?php $workloop = "work_specific"; ?>
                    <?php $quantityloop = "quantity"; ?>
                    <?php $unit_priceloop = "unit_price"; ?>
                    <?php $amountloop = "amount"; ?>
                    <?php $remarksloop = "remarks"; ?>
                    <?php $empid = "emp_id"; ?>
                    <tr id="othercc_<?php echo $i ?>">
                        <td>
                            <div class="">
                                <div style="">
                                    {{ Form::text('work_specific'.$i, isset($invoicedata[$i]->$workloop) ? ($invoicedata[$i]->$workloop) : '',array('id'=>'work_specific'.$i,
                                                        'name' => 'work_specific'.$i,
                                                        'maxlength' => 20,
                                                        'autocomplete'=>'off',
                                                        'data-label' => trans('messages.lbl_UserID'),
                                                        'onchange' => 'this.value=this.value.trim()',
                                                        'class'=>'input_text box99per form-control pl5 mt3')) }}
                                    {{ Form::hidden('work_specific_hdn'.$i, isset($invoicedata[$i]->$workloop) ? ($invoicedata[$i]->$workloop) : '',array('id'=>'work_specific_hdn'.$i,
                                                        'name' => 'work_specific_hdn'.$i)) }}
                                    {{ Form::hidden('fordisable_hdn'.$i, 0,
                                                        array('id'=>'fordisable_hdn'.$i,
                                                                'name' => 'fordisable_hdn'.$i)) }}
                            </div>
                            <?php
                            $getEmpData = array();
                            if (isset($invoicedata[$i]->$empid)) {
                                $getEmpData=Invoice::getemp_details($invoicedata[0]->id,$invoicedata[$i]->$empid);
                            }
                            ?>
                            @php $cls = "" @endphp
                            @if(isset($getEmpData[0]->emp_id) && $getEmpData[0]->emp_id != "")
                                @php $cls='display: inline' @endphp
                            @else
                                @php $cls='display: none' @endphp
                            @endif
                            <div id="divid<?php echo $i ?>" style = "<?php echo $cls; ?>">
                                    {{ Form::hidden('emp_ID'.$i,isset($getEmpData[0]->emp_id)?$getEmpData[0]->emp_id:$request->emp_id,
                                                        array('id'=>'emp_ID'.$i,
                                                                'name' => 'emp_ID'.$i)) }}
                                    <label id="empKanaNames<?php echo $i ?>" 
                                            name="empKanaNames<?php echo $i ?>" 
                                            style="padding-left: 2px;font-weight: 100 !important;color: grey !important;">
                                        @if( isset($getEmpData[0]->KanaName) && $getEmpData[0]->KanaName != '　' )
                                            {{ $getEmpData[0]->KanaName }}
                                        @else
                                            @if(isset($getEmpData[0]->EnglishName))
                                                {{ $getEmpData[0]->EnglishName }}
                                            @endif
                                        @endif
                                    </label>
                                    <a id="crossid<?php echo $i ?>" onclick="fngetEmpty('<?php echo $i ?>',);" style="float: right;cursor: pointer !important;display: none;">
                                        <i class="fa fa-close" aria-hidden="true"></i>
                                    </a>
                                    <script type="text/javascript">
                                        $(document).ready(function() {
                                            var empid = $('#emp_ID'+'<?php echo $i; ?>').val();
                                            var i = '<?php echo $i; ?>';
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
                                <a  id="emp" onclick="return popupenableempname('{{ $request->mainmenu }}','{{ $i }}');" 
                                    class="btn btn-success box36 white" style="line-height: 1 !important;">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                </a>
                                    {{ Form::text('quantity'.$i,isset($invoicedata[$i]->$quantityloop) ? ($invoicedata[$i]->$quantityloop ): '',array('id'=>'quantity'.$i,
                                                        'name' => 'quantity'.$i,

                                                        'style'=>'text-align:right;',
                                                        'maxlength' => 7,
                                                        'data-label' => trans('messages.lbl_UserID'),
                                                        'onkeypress' => 'return isDotNumberKey(event,this.value,1)',
                                                        'ondragstart'=>'return false',
                                                        'ondrop'=>'return false',
                                                        'autocomplete'=>'off',
                                                        "onkeyup" => "return fnCalculateAmount('$i', '', '',$a)",
                                                        'class'=>'box72per form-control pl5 mt3')) }}
                                    {{ Form::hidden('quantity_hdn'.$i,isset($invoicedata[$i]->$quantityloop) ? ($invoicedata[$i]->$quantityloop) : '',array('id'=>'quantity_hdn'.$i,
                                                        'name' => 'quantity_hdn'.$i)) }}
                            </div>
                        </td>
                        <td>
                            <div class="">
                                    <?php 
                                        $color = "";
                                        $bordercolor = "";
                                        if (isset($invoicedata[$i]->$unit_priceloop)) {
                                            if ($invoicedata[$i]->$unit_priceloop < 0) {
                                                $color = 'color:red;';
                                                $bordercolor = 'border-color:red;';
                                            }
                                        } 
                                    ?>
                                    {{ Form::text('unit_price'.$i,isset($invoicedata[$i]->$unit_priceloop) ? ($invoicedata[$i]->$unit_priceloop) : '',array('id'=>'unit_price'.$i,
                                                        'name' => 'unit_price'.$i,
                                                        'autocomplete'=>'off',
                                                        'maxlength' => 11,
                                                        'style' => $color.$bordercolor,
                                                        'data-label' => trans('messages.lbl_UserID'),
                                                        'onkeypress' => 'return isNumberKeywithminus(event)',
                                                        'ondragstart'=>'return false',
                                                        'ondrop'=>'return false',
                                                        "onkeyup" => "return fnCalculateAmount('$i', this.name, this.value,$a)",
                                                        'class'=>'box99per form-control pl5 mt3 tar')) }}
                                    {{ Form::hidden('unit_price_hdn'.$i,isset($invoicedata[$i]->$unit_priceloop) ? ($invoicedata[$i]->$unit_priceloop) : '',array('id'=>'unit_price_hdn'.$i,
                                                        'name' => 'unit_price_hdn'.$i)) }}
                            </div>
                        </td>
                        <td>
                            <div class="">
                                    <?php 
                                        $color = "";
                                        $bordercolor = "";
                                        if (isset($invoicedata[$i]->$amountloop)) {
                                            if ($invoicedata[$i]->$amountloop < 0) {

                                                $color = 'color:red;';
                                                $bordercolor = 'border-color:red;';
                                            }
                                        } 
                                    ?>
                                    {{ Form::text('amount'.$i,isset($invoicedata[$i]->$amountloop) ? ($invoicedata[$i]->$amountloop) : '',array('id'=>'amount'.$i,
                                                        'name' => 'amount'.$i,

                                                        'disabled' => 'true',
                                                        'style' => $color.$bordercolor,
                                                        'data-label' => trans('messages.lbl_UserID'),
                                                        'class'=>'box99per form-control pl5 mt3 tar')) }}
                                    {{ Form::hidden('amount_hdn'.$i,isset($invoicedata[$i]->$amountloop) ? ($invoicedata[$i]->$amountloop) : '',array('id'=>'amount_hdn'.$i,
                                                        'name' => 'amount_hdn'.$i)) }}
                                    {{ Form::hidden('amountfif'.$i, isset($invoicedata[$i]->$amountloop) ? ($invoicedata[$i]->$amountloop) : '', array('id' => 'amountfif'.$i)) }}
                            </div>
                        </td>
                        <td style="padding: 0px;">
                            <div class="" style="padding: 0px;padding-left: 5px;">
                                {{ Form::text('remarks'.$i, isset($invoicedata[$i]->$remarksloop) ? ($invoicedata[$i]->$remarksloop) : '',array('id'=>'remarks'.$i,
                                                        'name' => 'remarks'.$i,
                                                        'autocomplete'=>'off',
                                                        'maxlength' => 10,
                                                        'data-label' => trans('messages.lbl_UserID'),
                                                        'onchange' => 'this.value=this.value.trim()',
                                                        'class'=>'box99per form-control pl5 mt3')) }}
                                {{ Form::hidden('remarks_hdn'.$i, isset($invoicedata[$i]->$remarksloop) ? ($invoicedata[$i]->$remarksloop) : '',array('id'=>'remarks_hdn'.$i,
                                                        'name' => 'remarks_hdn'.$i)) }}
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <div style="display: inline-block;">
                                <a onclick="return fnAddTR('<?php echo $i; ?>', 1);" id="addrow{{ $i }}" name = "addrow"  class="csrp"><i class="fa fa-plus" aria-hidden="true"></i></a>
                            </div>
                            <div class="ml10" style="display: inline-block;">
                                <a onclick="return fnRemoveTR('<?php echo $i; ?>', 1);"  id="removerow{{ $i }}" name = "removerow" class="csrp"><i class="fa fa-minus" aria-hidden="true"></i></a>
                            </div>
                            <td style="padding: 0px;" style="text-align: center;">
                            <div style="display: inline-block;">
                        <?php
                        if($i==1){
                            $style="cursor: pointer; display: none;";}      
                        else{
                            $style="cursor: pointer;";}
                        ?>
                        <a class="pull-center  ml5 imgtableheight dispnone"
                        id="removeiconid_{{ $i }}"
                        onclick="javascript:cloneremoveabove(this);"
                        style="{{$style}}" 
                        ><img class="pull-center box30 ml5" style="max-height: 19px; max-width: 19px;"src="{{ URL::asset('resources/assets/images/close.png') }}"></a>
                    </div>
                </td>
                </td>
                </tr>
                @endfor
                </tbody>
            </table>
        </div>
        <script>
            fnAddTR('<?php echo $i; ?>', 1);
        </script>
        {{ Form::hidden('tableamountcount', $i-1, array('id' => 'tableamountcount')) }}
        <div>
            <div class="box96per mt5 ml20" style="display: inline-block;">
                <div class="box53per text-right" style="display: inline-block;">
                    <label class="clr_blue mt8 mr5">
                        Total Amount
                    </label>
                </div>
                <div class="box46per pull-right" style="display: inline-block;">
                    {{ Form::text('totval',isset($invoicedata[0]->totalval) ? ($invoicedata[0]->totalval) : 0,array('id'=>'totval',
                                                        'name' => 'totval',
                                                        'disabled' => 'true',
                                                        'style'=>'text-align:right;',
                                                        'class'=>'box37per form-control pl5 mt3')) }}
            {{ Form::hidden('tabamount', isset($invoicedata[0]->totalval) ? ($invoicedata[0]->totalval) : "", array('id' => 'tabamount')) }}
                </div>
            </div>
        </div>
        <div class="mt20 mb10">
            <div class="col-xs-6 mt3 mb20">
        @for($i=1;$i<=5;$i++)
            <?php $noteloop = "special_ins".$i; ?>
            <?php $g_id = substr(("0" . $i), -2); ?>
                <div class="col-xs-2 text-right" style="width: 108px !important">
                    <label class="clr_blue">
                        {{ trans('messages.lbl_notices') }}{{ $i }}
                    </label>
                </div>
                <div>
                    {{ Form::text('note'.$i,isset($invoicedata[0]->$noteloop)?($invoicedata[0]->$noteloop):'',                                              array('id'=>'note'.$i,
                                                        'name' => 'note'.$i,
                                                        'class'=>'box60per form-control pl5 mt3')) }}
                    {{ Form::hidden('noticesel'.$i, '', array('id' => 'noticesel'.$i)) }}
                    <a onclick="return popupenable('{{ $request->mainmenu }}','{{ $i }}');" 
                        class="btn btn-success box100 white">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        {{trans('messages.lbl_browser')}}
                    </a>
                </div>
        @endfor
            </div>
            <div class="col-xs-6 mt3 mb20">
                <div class="text-left">
                    <label class="clr_blue">
                        {{ trans('messages.lbl_memo') }}
                    </label>
                </div>
                <div>
                    {{ Form::textarea('memo',(isset($invoicedata[0]->memo)) ? $invoicedata[0]->memo : '',array(
                                            'id'=>'memo',
                                            'name' => 'memo',
                                            'class'=>'box100per form-control',
                                            'data-label' => trans('messages.lbl_payday'),
                                            'style' => 'height: 150px !important;',
                                            'size' => '30x9')) }}
                </div>
            </div>
        </div>
        {{ Form::hidden('tablespecialcount', $i-1, array('id' => 'tablespecialcount')) }}
    </fieldset>
    <fieldset style="background-color: #DDF1FA;">
        <div class="form-group">
            <div align="center" class="mt5">
            @if($regflag==1 || $regflag==2)
                    <button type="submit" class="btn btn-success add box100 addeditprocess ml5">
                    <i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
                    </button>
            @else
            {{ Form::hidden('usersid', $invoicedata[0]->user_id, array('id' => 'usersid')) }}
                    <button type="submit" class="btn edit btn-warning addeditprocess box100">
                            <i class="fa fa-edit" aria-hidden="true"></i>{{ trans('messages.lbl_update') }}
                    </button>
            @endif
            @if($request->frmestview ==1)
                <a onclick="javascript:gotoindex('../Estimation/view','{{$request->mainmenu}}');" 
                        class="btn btn-danger box120 white">
                                <i class="fa fa-times" aria-hidden="true"></i> 
                                    {{trans('messages.lbl_cancel')}}
                </a>
            @elseif($request->frminvview ==1)
                <a onclick="javascript:gotoindex('../Invoice/specification','{{$request->mainmenu}}');" 
                        class="btn btn-danger box120 white">
                                <i class="fa fa-times" aria-hidden="true"></i> 
                                    {{trans('messages.lbl_cancel')}}
                </a>
            @else
                <a onclick="javascript:gotoindex('../Estimation/index','{{$request->mainmenu}}');" 
                        class="btn btn-danger box120 white">
                                <i class="fa fa-times" aria-hidden="true"></i> 
                                    {{trans('messages.lbl_cancel')}}
                </a>
            @endif
            </div>
        </div>
    </fieldset>
<!-- End Heading -->
    @if(!empty($selectval))
        <script type="text/javascript">
            fnbankaccountdetail('{{ $selectval }}')
        </script>
    @endif
    @if(isset($invoicedata))
    <script type="text/javascript">
            fngetsubsubject('{{ $invoicedata[0]->customer_id }}','{{ $invoicedata[0]->branch_selection }}')
        </script>
    @endif
    {{ Form::close() }}
    <div id="noticepopup" class="modal fade">
        <div id="login-overlay">
            <div class="modal-content">
                <!-- Popup will be loaded here -->
            </div>
        </div>
    </div>
    <div id="empnamepopup" class="modal fade">
        <div id="login-overlay">
            <div class="modal-content">
                <!-- Popup will be loaded here -->
            </div>
        </div>
    </div>
</article>
</div>
@endsection