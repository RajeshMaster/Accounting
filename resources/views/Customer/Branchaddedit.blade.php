@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/customer.js') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
	<div class="CMN_display_block"  id="main_contents">
	<article id="customer" class="DEC_flex_wrapper " data-category="customer customer_sub_2">
          @if(!empty($bdetails))
        {{ Form::model($bdetails,array('name'=>'frmbranchaddedit','method' => 'POST',
                                         'class'=>'form-horizontal',
                                         'id'=>'frmbranchaddedit', 
                                         'url' => 'Customer/Branchaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'))) }}
            {{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
            {{ Form::hidden('flg', $request->flg , array('id' => 'flg')) }}
            {{ Form::hidden('id', $request->id , array('id' => 'id')) }}
            {{ Form::hidden('custid',$request->custid,array('id' => 'custid')) }}
    @else
        {{ Form::open(array('name'=>'frmbranchaddedit', 'id'=>'frmbranchaddedit', 
                            'class' => 'form-horizontal',
                            'files'=>true,
                            'url' => 'Customer/Branchaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'), 
                            'method' => 'POST')) }}
                {{ Form::hidden('custid',$request->custid, array('id' => 'custid')) }}
                {{ Form::hidden('id',$request->id, array('id' => 'id')) }} 
                {{ Form::hidden('flg', $request->flg , array('id' => 'flg')) }} 
                {{ Form::hidden('editid', '', array('id' => 'editid')) }} 
    @endif                       
	<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-8 pl10 mt10">
			<img class="pull-left box35 mt5" src="{{ URL::asset('resources/assets/images/Client.png') }}">
			<h2 class="pull-left pl5 mt10">{{ trans('messages.lbl_branch') }}</h2>
            <h2 class="pull-left mt10">・</h2>
           <h2 class="pull-left mt10 green">@if($request->flg!=1){{ trans('messages.lbl_register') }}</h2>@else<h2 class="pull-left mt10 red">{{ trans('messages.lbl_edit') }}@endif</h2>
		</div>
	</div>
	<!-- End Heading -->
    <div class="pl5 pr5" style="margin-top: -10px;">
    <fieldset class="col-xs-12">
    <div class="col-xs-12 mt10">
         @if(isset($bdetails))
        <div class="col-xs-3 text-right clr_blue ml6">
             <label>
                {{ trans('messages.lbl_branchid') }}<span class="fr ml2 red" style="visibility: hidden;"> * </span></label>
        </div>
        <div>
        <label name="maxid" id="maxid">
                {{$bdetails[0]->branch_id}}
                {{ Form::hidden('branid',$bdetails[0]->branch_id , array('id' => 'branid')) }} 
        </label>
        </div>
        @else
        @endif
    </div>
     <div class="col-xs-12 mt5">
        <div class="col-xs-3 text-right clr_blue ml6">
             <label>
                {{ trans('messages.lbl_branch_name') }}<span class="fr ml2 red"> * </span></label>
        </div>
        <div>
                {{ Form::text('txt_branch_name',(isset($bdetails[0]->branch_name)) ? $bdetails[0]->branch_name : '',array(
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
                {{ Form::text('txt_mobilenumber',(isset($bdetails)) ? $bdetails[0]->txt_mobilenumber : '',array(
                                    'id'=>'txt_mobilenumber',
                                    'name' => 'txt_mobilenumber',
                                    'class'=>'box12per form-control',
                                    'style'=>'ime-mode: disabled;',
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
                {{ Form::text('txt_fax',(isset($bdetails)) ? $bdetails[0]->txt_fax : '',array(
                                    'id'=>'txt_fax',
                                    'name' => 'txt_fax',
                                    'class'=>'box12per form-control',
                                    'maxlength' => 13,
                                    'style'=>'ime-mode: disabled;',
                                    'data-label' => trans('messages.lbl_fax'),
                                    'onkeypress' => 'return isNumberKeywithminus(event)')) }}
        </div>
    </div>
    <div class="col-xs-12 mt5 pb15">
        <div class="col-xs-3 text-right clr_blue ml6">
             <label>
                {{ trans('messages.lbl_address') }}<span class="fr ml2 red"> * </span></label>
        </div>
        <div>
                {{ Form::textarea('txt_address',(isset($bdetails)) ? $bdetails[0]->txt_address : '',array(
                                    'id'=>'txt_address',
                                    'name' => 'txt_address',
                                    'class'=>'box25per',
                                    'style' => 'height :70px;',
                                    'name' => 'txt_address',
                                    'data-label' => trans('messages.lbl_address'))) }}
        </div>
    </div>
        <div class="CMN_display_block pb15"></div>
        </fieldset>
         <fieldset style="background-color: #DDF1FA;" >
            <div class="form-group">
                <div align="center" class="mt5">
                   @if($request->flg ==1)
                    <button type="submit" class="btn edit btn-warning box100 Branchaddeditprocess" >
                        <i class="fa fa-edit" aria-hidden="true"></i> {{ trans('messages.lbl_update') }}
                    </button>
                    <a onclick="javascript:gotoinpage('{{ $request->mainmenu }}',{{ date('YmdHis') }});" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{ trans('messages.lbl_cancel') }} 
                        </a>
                    @else
                        <button type="submit" class="btn btn-success add box100 Branchaddeditprocess ml5">
                            <i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
                        </button>
                        <a onclick="javascript:gotoinpage('{{ $request->mainmenu }}',{{ date('YmdHis') }});" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{ trans('messages.lbl_cancel') }} 
                        </a>
                    @endif  
                </div>
            </div>
        </fieldset>
        </div>
    </article>
    </div>
    {{ Form::close() }}
    {{ Form::open(array('name'=>'frmbranchaddeditcancel', 'id'=>'frmbranchaddeditcancel', 'url' => 'Customer/Branchaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
                {{ Form::hidden('custid',$request->custid, array('id' => 'custid')) }}
                {{ Form::hidden('id',$request->id, array('id' => 'id')) }} 
                {{ Form::hidden('flg', $request->flg , array('id' => 'flg')) }} 
                 {{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
    {{ Form::close() }}
    @endsection