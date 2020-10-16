@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/bank.js') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
	<div class="CMN_display_block"  id="main_contents">
	<article id="master" class="DEC_flex_wrapper " data-category="master master_sub_2">
    @if(!empty($getdetails))
        {{ Form::model($getdetails,array('name'=>'frmbankaddedit','method' => 'POST',
                                         'class'=>'form-horizontal',
                                         'id'=>'frmbankaddedit', 
                                         'url' => 'Bank/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'))) }}
            {{ Form::hidden('id', $request->id , array('id' => 'id')) }}
            {{ Form::hidden('branid', '' , array('id' => 'branchid')) }}
            {{ Form::hidden('bankuid', $getdetails->mstbankname , array('id' => 'bankuid')) }}
            {{ Form::hidden('branchuid', $getdetails->mstbranchname , array('id' => 'branchuid')) }}
            {{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
            {{ Form::hidden('viewid', $request->editid, array('id' => 'viewid')) }}
            {{ Form::hidden('filterval', $request->filterval, array('id' => 'filterval')) }}
            {{ Form::hidden('flg', $request->flg , array('id' => 'flg')) }}
            {{ Form::hidden('id', $getdetails->id , array('id' => 'id')) }}
			{{ Form::hidden('loc', $getdetails->Location , array('id' => 'loc')) }}
    @else
        {{ Form::open(array('name'=>'frmbankaddedit', 'id'=>'frmbankaddedit', 
                            'class' => 'form-horizontal',
                            'files'=>true,
                            'url' => 'Bank/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'), 
                            'method' => 'POST')) }}
        {{ Form::hidden('flg', $request->flg , array('id' => 'flg')) }}
        {{ Form::hidden('filterval', $request->filterval, array('id' => 'filterval')) }}                    
        {{ Form::hidden('bankuid', $request->bankuid , array('id' => 'bankuid')) }}                    
        {{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
        {{ Form::hidden('loc', $request->Location , array('id' => 'loc')) }}
        {{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
        {{ Form::hidden('viewid', $request->editid, array('id' => 'viewid')) }}
        {{ Form::hidden('id', $request->id , array('id' => 'id')) }}
    @endif    
        {{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
        {{ Form::hidden('filterval', $request->filterval, array('id' => 'filterval')) }}
        {{ Form::hidden('viewid', $request->editid, array('id' => 'viewid')) }}

	<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-8 pl10">
			<img class="pull-left box35 mt15" src="{{ URL::asset('resources/assets/images/bank.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_bank') }}</h2>
            <h2 class="pull-left mt15">・</h2>
           <h2 class="pull-left mt15 green">@if($request->flg!=1){{ trans('messages.lbl_register') }}</h2>@else<h2 class="pull-left mt15 red">{{ trans('messages.lbl_edit') }}@endif</h2>
		</div>
	</div>
	<div class="pb10"></div>
	<!-- End Heading -->
	<div class="col-xs-12 pl5 pr5">
	<fieldset>
		<div class="col-xs-12 ml7 mt15">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_location') }}<span class="fr ml2 red"> * </span></label>
			</div>
				@if(!empty($getdetails))
	                {{ Form::hidden('location', $getdetails->Location , array('id' => 'location')) }}
	        	@else
	                {{ Form::hidden('location','', array('id' => 'location')) }}
	        	@endif
			<div class="text-left">
	              <span>
	               		<label style="font-weight: normal;">
		                    <input type="radio" name="nation" class="nation mb6" id="india" value="1" data-label = 
		                    {{ trans('messages.lbl_location') }}
		                          onclick="getNamesbyajax('1')" @if(isset($request->flg))
		                                                       @if($getdetails->Location==1) 
		                                                            checked="checked"  
		                                                        @endif  
		                                                  @endif> &nbsp{{ trans('messages.lbl_india') }} 
                		</label>
		                <label style="font-weight: normal;">
		                    &nbsp
		                    <input type="radio" name="nation" class="nation mb6" id="japan" onclick="getNamesbyajax('2')" value="2"
		                    @if(isset($request->flg)) @if($getdetails->Location==2) checked="checked" @endif @endif> &nbsp{{ trans('messages.lbl_japan') }}
		                </label>
                			<div class="nation_err CMN_display_inline"></div>
	              </span>
	        </div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue ml7">
				 <label for="kananame" id="kananame">
	              	{{ trans('messages.lbl_kananame') }}<span class="fr ml2 red"> * </span></label>
	              <label for="name" id="name" style="display: none">
	            	{{ trans('messages.lbl_name') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
					{{ Form::text('txt_kananame',old('txt_kananame'),array(
										'id'=>'txt_kananame',
										'name' => 'txt_kananame',
										'class'=>'box25per form-control',
										'data-label' => trans('messages.lbl_kananame'))) }}
				<span id="exampleName" class="labelLeft CMN_color_black fwn">{{ trans('messages.lbl_ltdcontent') }}</span>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue ml7">
				<label>{{ trans('messages.lbl_accnumber') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
					{{ Form::text('txt_accnumber',old('txt_accnumber'),array(
										'id'=>'txt_accnumber',
										'name' => 'txt_accnumber',
										'class'=>'box13per form-control',
										'data-label' => trans('messages.lbl_accnumber'),
										'maxlength' => 15
										,'onkeypress' => 'return isNumberKey(event)')) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue ml7">
				<label>{{ trans('messages.lbl_accounttype') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				@if($request->flg!="")
				{{ Form::select('type',($getdetails->Location==1) ? $indiaaccounttype : $jpnaccounttype,(isset($getdetails->type)) ? $getdetails->type : '',array('name' => 'type','id'=>'type','data-label' => trans('messages.lbl_accounttype'),'class'=>'pl5'))}}
				@else
				{{ Form::select('type', [null=>''] +$indiaaccounttype , '',array('name' => 'type','id'=>'type','data-label' => trans('messages.lbl_accounttype'),'class'=>'pl5'))}}
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt7">
			<div class="col-xs-3 text-right clr_blue ml7">
				<label>{{ trans('messages.lbl_bank_name') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="box25per fll">
				@if($request->bnkname || $request->flg!="")
                    {{ Form::text('txt_bankname',(isset($getdetails)) ? $getdetails->bankname : $request->bnkname,array('id'=>'txt_bankname','name' => 'txt_bankname','class'=>'form-control','readonly','readonly','data-label' => trans('messages.lbl_bank_name'))) }}
                @else
                {{ Form::hidden('bankid', '' , array('id' => 'bankid')) }}
                {{ Form::hidden('branchid', '' , array('id' => 'branchid')) }}
                    {{ Form::text('txt_bankname',null,array('id'=>'txt_bankname', 'name' => 'txt_bankname',
                                                        'class'=>'form-control',
                                                        'readonly','readonly','data-label' => trans('messages.lbl_bank_name'))) }}
                @endif 
			</div>
			<div class="col-xs-3 mr25">
				@if($request->flg!="")
                    <button type="button" id="bnkpopup" class="btn btn-success box75 pt3 h30"  style ="color:white;background-color: hsl(120, 39%, 54%);cursor: default;" onclick="return popupenable('{{ $request->mainmenu }}');">{{ trans('messages.lbl_Browse') }}</button>
                @else
                    <button type="button" id="bnkpopup" class="btn btn-success box75 pt3 h30" disabled="disabled" style ="color:white;background-color: grey;cursor: default;" onclick="return popupenable('{{ $request->mainmenu }}');">{{ trans('messages.lbl_Browse') }}</button> 
                @endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue ml7">
				<label>{{ trans('messages.lbl_nickname') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
					{{ Form::text('txt_nickname',old('txt_nickname'),array('id'=>'txt_nickname', 'name' => 'txt_nickname','class'=>'box25per form-control','data-label' => trans('messages.lbl_nickname'))) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue ml7">
				<label>{{ trans('messages.lbl_branch_name') }}<span class="fr ml2 red"> * </span></label>
			</div>
			
				@if($request->bnkname || $request->branch || $request->flg!="")
				<div class="box25per fll">
                {{ Form::text('txt_branchname',(isset($getdetails)) ? $getdetails->branchname : $request->branch,array('id'=>'txt_branchname', 'name' => 'txt_branchname',
                                                                  'class'=>'form-control',
                                                                  'maxlength' => 12,'readonly','readonly','data-label' => trans('messages.lbl_branch_name'))) }}
                </div>                                                  
                <div class="col-xs-3 val mr20">                                                  
                    <button type="button" id="brchpopup" class="btn btn-success box75 pt3 h30"
                             style ="color:white;background-color: hsl(120, 39%, 54%);cursor: default;" onclick="return branchpopupenable('{{ $request->mainmenu }}');">{{ trans('messages.lbl_Browse') }}
                    </button>
                </div>
                @else
                <div class="box25per fll">
                    {{ Form::text('txt_branchname',null,array('id'=>'txt_branchname', 'name' => 'txt_branchname','class'=>'form-control','readonly','readonly','data-label' => trans('messages.lbl_branch_name'))) }}
                    </div>
                <div class="col-xs-3 val">    
                    <button type="button" id="brchpopup" class="btn btn-success box75 pt3 h30"  disabled="disabled" style ="color:white;background-color: grey;cursor: default;" onclick="return branchpopupenable('{{ $request->mainmenu }}');">
                        {{ trans('messages.lbl_Browse') }}
                    </button>
                </div>    
                @endif
			</div>
		<div class="col-xs-12 mt5 pb15">
			<div class="col-xs-3 text-right clr_blue ml7">
				<label>{{ trans('messages.lbl_branch_number') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div>
				@if($request->bnkname || $request->bno || $request->flg!="")
                {{ Form::text('txt_branchnumber',(isset($getdetails)) ? $getdetails->branchno : $request->bno,array('id'=>'txt_branchnumber', 'name' => 'txt_branchnumber',
                              'class'=>'bno box12per form-control',
                              'maxlength' => 12,'readonly','readonly','data-label' => trans('messages.lbl_branch_number'),'onkeypress' => 'return isNumberKey(event)')) }}
                @else
                {{ Form::text('txt_branchnumber',null,array('id'=>'txt_branchnumber', 'name' => 'txt_branchnumber',
                                                    'class'=>'bno box12per form-control',
                                                    'maxlength' => 12,'readonly','readonly','data-label' => trans('messages.lbl_branch_number'),'onkeypress' => 'return isNumberKey(event)')) }}
                @endif
			</div>
		</div>
		<div class="CMN_display_block pb15"></div>
	</fieldset>
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
	{{ Form::close() }}
	{{ Form::open(array('name'=>'frmbankaddeditcancel', 'id'=>'frmbankaddeditcancel', 'url' => 'Bank/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
	{{ Form::hidden('filterval', $request->filterval, array('id' => 'filterval')) }}
    {{ Form::hidden('flg', $request->flg , array('id' => 'flg')) }}
    {{ Form::hidden('id', $request->id , array('id' => 'id')) }}
	{{ Form::hidden('loc', $request->Location , array('id' => 'loc')) }}
	{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
	{{ Form::hidden('viewid', $request->editid, array('id' => 'viewid')) }}
	{{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
	{{ Form::close() }}
	 </div>
	 </article>
	<div class="CMN_display_block pb10"></div>
	 <div id="banknamepopup" class="modal fade">
        <div id="login-overlay">
            <div class="modal-content">
                <!-- Popup will be loaded here -->
            </div>
        </div>
    </div>
     <div id="branchnamepopup" class="modal fade">
        <div id="login-overlay">
            <div class="modal-content">
                <!-- Popup will be loaded here -->
            </div>
        </div>
    </div>
@endsection