@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/customer.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
    $(document).ready(function() {
    setDatePicker("txt_custagreement");
  });
</script>
	<div class="CMN_display_block"  id="main_contents">
	<article id="customer" class="DEC_flex_wrapper " data-category="customer customer_sub_2">
         @if(!empty($getdetails))
        {{ Form::model($getdetails,array('name'=>'frmcustaddedit','method' => 'POST',
                                         'class'=>'form-horizontal',
                                         'id'=>'frmcustaddedit', 
                                         'url' => 'Customer/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'))) }}
            {{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
            {{ Form::hidden('viewid', $request->editid, array('id' => 'viewid')) }}
            {{ Form::hidden('filterval', $request->filterval, array('id' => 'filterval')) }}
            {{ Form::hidden('flg', $request->flg , array('id' => 'flg')) }}
            {{ Form::hidden('id', $request->id , array('id' => 'id')) }}
            {{ Form::hidden('custid',$request->custid,array('id' => 'custid')) }}
            {{ Form::hidden('hid_branch_id','', array('id' => 'hid_branch_id')) }}
             {{ Form::hidden('filterval', $request->filterval, array('id' => 'filterval')) }}
    @else
        {{ Form::open(array('name'=>'frmcustaddedit', 'id'=>'frmcustaddedit', 
                            'class' => 'form-horizontal',
                            'files'=>true,
                            'url' => 'Customer/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'), 
                            'method' => 'POST')) }}
        {{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
        {{ Form::hidden('filterval', $request->filterval, array('id' => 'filterval')) }}
        {{ Form::hidden('editid','', array('id' => 'editid')) }}
        {{ Form::hidden('flg', $request->flg , array('id' => 'flg')) }}
         {{ Form::hidden('id', $request->id , array('id' => 'id')) }}
            {{ Form::hidden('custid',$request->custid,array('id' => 'custid')) }}
         {{ Form::hidden('hid_branch_id','', array('id' => 'hid_branch_id')) }}

    @endif     
	<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-8 pl10 mt10">
			<img class="pull-left box35 mt5" src="{{ URL::asset('resources/assets/images/Client.png') }}">
			<h2 class="pull-left pl5 mt10">{{ trans('messages.lbl_customer') }}</h2>
            <h2 class="pull-left mt10">・</h2>
           <h2 class="pull-left mt10 green">@if($request->flg!=1){{ trans('messages.lbl_register') }}</h2>@else<h2 class="pull-left mt10 red">{{ trans('messages.lbl_edit') }}@endif</h2>
		</div>
	</div>
	<!-- End Heading -->
    <div class="pl5 pr5" style="margin-top: -10px;">
        <fieldset class="col-xs-12">
            <div class="col-xs-12 mt15">
         @if(isset($getdetails))
            <div class="col-xs-3 text-right clr_blue mr3">
             <label>
                {{ trans('messages.lbl_CustId') }}<span class="fr ml2 red" style="visibility: hidden;">  </span>
             </label>
            </div>
            <div>
            <label name="maxid" id="maxid">
                {{$request->custid}} 
            </label>
            </div>
        @else

        @endif  
     </div>
    <div class="col-xs-12 mt5">
            <div class="col-xs-3 text-right clr_blue ml6">
                  <label>
                    {{ trans('messages.lbl_custname(JP & Eng)') }}<span class="fr ml2 red"> * </span></label>
            </div>
            <div>
                    {{ Form::text('txt_custnamejp',(isset($getdetails)) ? $getdetails[0]->txt_custnamejp : '',array(
                                        'id'=>'txt_custnamejp',
                                        'name' => 'txt_custnamejp',
                                        'class'=>'box25per form-control',
                                        'data-label' => trans('messages.lbl_custname(JP & Eng)'))) }}
            </div>
        </div>
        <div class="col-xs-12 mt5">
            <div class="col-xs-3 text-right clr_blue ml6">
                 <label>
                    {{ trans('messages.lbl_custname(kana)') }}<span class="fr ml2 red"> * </span></label>
            </div>
            <div>
                    {{ Form::text('txt_kananame',(isset($getdetails)) ? $getdetails[0]->txt_kananame : '',array(
                                        'id'=>'txt_kananame',
                                        'name' => 'txt_kananame',
                                        'class'=>'box25per form-control',
                                        'data-label' => trans('messages.lbl_kananame'))) }}
            </div>
        </div>
        <div class="col-xs-12 mt5">
            <div class="col-xs-3 text-right clr_blue ml6">
                 <label>
                    {{ trans('messages.lbl_repname') }}<span class="fr ml2 red"> * </span></label>
            </div>
            <div>
                    {{ Form::text('txt_repname',(isset($getdetails)) ? $getdetails[0]->txt_repname : '',array(
                                        'id'=>'txt_repname',
                                        'name' => 'txt_repname',
                                        'class'=>'box25per form-control',
                                        'name' => 'txt_repname',
                                        'data-label' => trans('messages.lbl_repname'))) }}
            </div>
        </div>
        <div class="col-xs-12 mt5">
            <div class="col-xs-3 text-right clr_blue ml6">
                 <label>
                    {{ trans('messages.lbl_custagreement') }}<span class="fr ml2 red"> * </span></label>
            </div>
            <div>
                    {{ Form::text('txt_custagreement',(isset($getdetails)) ? $getdetails[0]->txt_custagreement : '',array(
                                        'id'=>'txt_custagreement',
                                        'name' => 'txt_custagreement',
                                        'class'=>'box9per form-control txt_custagreement',
                                        'data-label' => trans('messages.lbl_custagreement'),
                                         'onkeypress'=>'return event.charCode >=6 && event.charCode <=58',
                                        'maxlength' => '10')) }}
                    <label class="mt10 ml2 fa fa-calendar fa-lg" 
                                    for="txt_custagreement" aria-hidden="true" style="display: inline-block!important;">
                    </label>                    
            </div>
        </div>
         <div class="col-xs-12 mt5">
            <div class="col-xs-3 text-right clr_blue ml6">
                 <label>
                    {{ trans('messages.lbl_branch_name') }}<span class="fr ml2 red"> * </span></label>
            </div>
            <div>
                    {{ Form::text('txt_branch_name',(isset($getbranchdetails[0]->branch_name)) ? $getbranchdetails[0]->branch_name : '',array(
                                        'id'=>'txt_branch_name',
                                        'name' => 'txt_branch_name',
                                        'class'=>'box25per form-control',
                                        'data-label' => trans('messages.lbl_branch_name'))) }}
            </div>
        </div>
         <div class="col-xs-12 mt5">
            <div class="col-xs-3 text-right clr_blue ml6">
                 <label>
                    {{ trans('messages.lbl_mobilenumber') }}<span class="fr ml2 red"> * </span></label>
            </div>
            <div>
                    {{ Form::text('txt_mobilenumber',(isset($getdetails)) ? $getdetails[0]->txt_mobilenumber : '',array(
                                        'id'=>'txt_mobilenumber',
                                        'name' => 'txt_mobilenumber',
                                        'class'=>'box12per form-control',
                                        'maxlength' => 13,
                                        'data-label' => trans('messages.lbl_mobilenumber'),
                                        'onkeypress' => 'return isNumberKeywithminus(event)')) }}
            </div>
        </div>
         <div class="col-xs-12 mt5">
            <div class="col-xs-3 text-right clr_blue ml6">
                 <label>
                    {{ trans('messages.lbl_fax') }}<span class="fr ml2 red"> * </span></label>
            </div>
            <div>
                    {{ Form::text('txt_fax',(isset($getdetails)) ? $getdetails[0]->txt_fax : '',array(
                                        'id'=>'txt_fax',
                                        'name' => 'txt_fax',
                                        'class'=>'box12per form-control',
                                        'maxlength' => 13,
                                        'data-label' => trans('messages.lbl_fax'),
                                        'onkeypress' => 'return isNumberKeywithminus(event)')) }}
            </div>
        </div>
         <div class="col-xs-12 mt5">
            <div class="col-xs-3 text-right clr_blue ml6">
                 <label>
                    {{ trans('messages.lbl_url') }}<span class="fr ml2 red"> * </span></label>
            </div>
            <div>
                    {{ Form::text('txt_url',(isset($getdetails)) ? $getdetails[0]->txt_url : '',array(
                                        'id'=>'txt_url',
                                        'name' => 'txt_url',
                                        'class'=>'box25per form-control',
                                        'data-label' => trans('messages.lbl_url'))) }}
            </div>
        </div>
        <div class="col-xs-12 mt5 pb15">
            <div class="col-xs-3 text-right clr_blue ml6">
                 <label>
                    {{ trans('messages.lbl_address') }}<span class="fr ml2 red"> * </span></label>
            </div>
            <div>
                    {{ Form::textarea('txt_address',(isset($getdetails)) ? $getdetails[0]->txt_address : '',array(
                                        'id'=>'txt_address',
                                        'name' => 'txt_address',
                                        'class'=>'box25per',
                                        'style' =>'height:70px;',
                                        'data-label' => trans('messages.lbl_address'))) }}
            </div>
        </div>
        <div class="CMN_display_block pb10"></div>
        </fieldset>
        <div style="margin-top: -5px;">
         <fieldset style="background-color: #DDF1FA;" >
            <div class="form-group">
                <div align="center" class="mt5">
                    @if(isset($request->flg))
                    <button type="submit" class="btn edit btn-warning box100 addeditprocess" >
                        <i class="fa fa-edit" aria-hidden="true"></i> {{ trans('messages.lbl_update') }}
                    </button>
                    <a onclick="javascript:gotoindexpage('1','{{ $request->mainmenu }}',{{ date('YmdHis') }});" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{ trans('messages.lbl_cancel') }} 
                        </a>
                    @else
                        <button type="submit" class="btn btn-success add box100 addeditprocess ml5">
                            <i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
                        </button>
                        <a onclick="javascript:gotoindexpage('2','{{ $request->mainmenu }}',{{ date('YmdHis') }});" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{ trans('messages.lbl_cancel') }} 
                        </a>
                    @endif  
                </div>
            </div>
        </fieldset>
        </div>
        </div>
    </article>
    </div>
    {{ Form::close() }}
     {{ Form::open(array('name'=>'frmcustaddeditcancel', 'id'=>'frmcustaddeditcancel', 'url' => 'Customer/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
        {{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
        {{ Form::hidden('filterval', $request->filterval, array('id' => 'filterval')) }}
        {{ Form::hidden('editid','', array('id' => 'editid')) }}
        {{ Form::hidden('flg', $request->flg , array('id' => 'flg')) }}
        {{ Form::hidden('id', $request->id , array('id' => 'id')) }}
        {{ Form::hidden('custid',$request->custid,array('id' => 'custid')) }}
         {{ Form::hidden('hid_branch_id','', array('id' => 'hid_branch_id')) }}
    {{ Form::close() }}
    @endsection