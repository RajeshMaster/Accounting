@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/customer.js') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
	<div class="CMN_display_block"  id="main_contents">
	<article id="customer" class="DEC_flex_wrapper " data-category="customer customer_sub_2">
	 @if(!empty($indetails))
        {{ Form::model($indetails,array('name'=>'frminchargeaddedit','method' => 'POST',
                                         'class'=>'form-horizontal',
                                         'id'=>'frminchargeaddedit', 
                                         'url' => 'Customer/Inchargeaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'))) }}
            {{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
            {{ Form::hidden('flg', $request->flg , array('id' => 'flg')) }}
            {{ Form::hidden('id', $request->id , array('id' => 'id')) }}
            {{ Form::hidden('custid',$request->custid,array('id' => 'custid')) }}
            {{ Form::hidden('inchargeid', '', array('id' => 'inchargeid')) }} 
    @else
        {{ Form::open(array('name'=>'frminchargeaddedit', 'id'=>'frminchargeaddedit', 
                            'class' => 'form-horizontal',
                            'files'=>true,
                            'url' => 'Customer/Inchargeaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'), 
                            'method' => 'POST')) }}
                {{ Form::hidden('custid',$request->custid, array('id' => 'custid')) }}
                {{ Form::hidden('id',$request->id, array('id' => 'id')) }} 
                {{ Form::hidden('flg','' , array('id' => 'flg')) }} 
                {{ Form::hidden('editid', '', array('id' => 'editid')) }}
                {{ Form::hidden('inchargeid', '', array('id' => 'inchargeid')) }}  
    @endif            
	<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-8 pl10 mt10">
			<img class="pull-left box35 mt5" src="{{ URL::asset('resources/assets/images/Client.png') }}">
			<h2 class="pull-left pl5 mt10">{{ trans('messages.lbl_incharge') }}</h2>
            <h2 class="pull-left mt10">・</h2>
           <h2 class="pull-left mt10 green">@if($request->flg!=1){{ trans('messages.lbl_register') }}</h2>@else<h2 class="pull-left mt10 red">{{ trans('messages.lbl_edit') }}@endif</h2>
		</div>
	</div>
	<div class="pb10"></div>
	<!-- End Heading -->
    <div class="pl5 pr5">
    <fieldset class="col-xs-12">
    <div class="col-xs-12 mt15">
        <div class="col-xs-3 text-right clr_blue ml6">
             <label>
                {{ trans('messages.lbl_inchargename') }}<span class="fr ml2 red"> * </span></label>
        </div>
        <div>
                {{ Form::text('txt_incharge_name',(isset($indetails[0]->txt_incharge_name)) ? $indetails[0]->txt_incharge_name : '',array(
                                    'id'=>'txt_incharge_name',
                                    'name' => 'txt_incharge_name',
                                    'class'=>'box25per form-control',
                                    'data-label' => trans('messages.lbl_inchargename'))) }}
        </div>
    </div>
     <div class="col-xs-12 mt5">
        <div class="col-xs-3 text-right clr_blue ml6">
             <label>
                {{ trans('messages.lbl_inchargenamekana') }}<span class="fr ml2 red"> * </span></label>
        </div>
        <div>
                {{ Form::text('txt_incharge_namekana',(isset($indetails[0]->txt_incharge_namekana)) ? $indetails[0]->txt_incharge_namekana : '',array(
                                    'id'=>'txt_incharge_namekana',
                                    'name' => 'txt_incharge_namekana',
                                    'class'=>'box25per form-control',
                                    'data-label' => trans('messages.lbl_inchargenamekana'))) }}
        </div>
    </div>
	   <div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue ml6">
					<label>{{ trans('messages.lbl_designation') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div>
					@if($request->flg!=1)
						{{ Form::select('designation',[null=>'Please select'] + $getdesname,'',array('class' => 'box12per','id' =>'designation','data-label' => trans('messages.lbl_designation'),'name' => 'designation')) }}
					@else
						{{ Form::select('designation',$getdesname,(isset($indetails[0]->designation)) ? $indetails[0]->designation : '',array('class' => 'box12per','id' =>'designation','data-label' => trans('messages.lbl_designation'),'name' => 'designation')) }}
					@endif
				</div>
			</div>
	     <div class="col-xs-12 mt5">
	        <div class="col-xs-3 text-right clr_blue ml6">
	             <label>
	                {{ trans('messages.lbl_mobilenumber') }}<span class="fr ml2 red" style="visibility: hidden;"> * </span></label>
	        </div>
	        <div>
	                {{ Form::text('txt_mobilenumber',(isset($indetails[0]->txt_mobilenumber)) ? $indetails[0]->txt_mobilenumber : '',array(
	                                    'id'=>'txt_mobilenumber',
	                                    'name' => 'txt_mobilenumber',
	                                    'class'=>'box10per form-control',
	                                    'maxlength' => 11,
                                        'style'=>'ime-mode: disabled;',
	                                    'data-label' => trans('messages.lbl_mobilenumber'),
	                                    'onkeypress' => 'return isNumberKey(event)')) }}
	        </div>
	    </div>
	     <div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue ml6">
					<label>{{ trans('messages.lbl_branch_name') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div>
					@if($request->flg!=1)
						{{ Form::select('bname',[null=>'Please select'] + $getbname,' ', ['class' => 'box12per','id' =>'bname','data-label' => trans('messages.lbl_branch_name'),'name' => 'bname']) }}
					@else
						{{ Form::select('bname',$getbname,(isset($indetails[0]->bname)) ? '本社' : '',array('class' => 'box12per','id' =>'bname','data-label' => trans('messages.lbl_branch_name'),'name' => 'bname')) }}
					@endif
					
				</div>
			</div>
	    <div class="col-xs-12 mt5 pb15">
	        <div class="col-xs-3 text-right clr_blue ml6">
	             <label>
	                {{ trans('messages.lbl_mailid') }}<span class="fr ml2 red" style="visibility: hidden;"> * </span></label>
	        </div>
	        <div>
	                {{ Form::text('txt_mailid',(isset($indetails[0]->txt_mailid)) ? $indetails[0]->txt_mailid : '',array(
	                                    'id'=>'txt_mailid',
	                                    'name' => 'txt_mailid',
	                                    'class'=>'box25per',
                                        'style'=>'ime-mode: disabled;',
	                                    'data-label' => trans('messages.lbl_mailid'))) }}
	        </div>
	    </div>
        <div class="CMN_display_block pb15"></div>
        </fieldset>
         <fieldset style="background-color: #DDF1FA;" >
            <div class="form-group">
                <div align="center" class="mt5">
                   @if($request->flg ==1)
                    <button type="submit" class="btn edit btn-warning box100 Inchargeaddeditprocess" >
                        <i class="fa fa-edit" aria-hidden="true"></i> {{ trans('messages.lbl_update') }}
                    </button>
                    <a onclick="javascript:gotoviewpage('{{ $request->mainmenu }}',{{ date('YmdHis') }});" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{ trans('messages.lbl_cancel') }} 
                        </a>
                    @else
                        <button type="submit" class="btn btn-success add box100 Inchargeaddeditprocess ml5">
                            <i class="fa fa-plus" aria-hidden="true"></i> {{ trans('messages.lbl_register') }}
                        </button>
                        <a onclick="javascript:gotoviewpage('{{ $request->mainmenu }}',{{ date('YmdHis') }});" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{ trans('messages.lbl_cancel') }} 
                        </a>
                    @endif  
                </div>
            </div>
        </fieldset>
        </div>
    </article>
    </div>
    {{ Form::close() }}
    {{ Form::open(array('name'=>'frminchargeaddeditcancel', 'id'=>'frminchargeaddeditcancel', 
                            'class' => 'form-horizontal',
                            'files'=>true,
                            'url' => 'Customer/Inchargeaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'), 
                            'method' => 'POST')) }}
                {{ Form::hidden('custid',$request->custid, array('id' => 'custid')) }}
                {{ Form::hidden('id',$request->id, array('id' => 'id')) }} 
                {{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
            	{{ Form::hidden('flg', $request->flg , array('id' => 'flg')) }}
                {{ Form::hidden('inchargeid', '', array('id' => 'inchargeid')) }}
    @endsection