@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/visarenew.js') }}
{{ HTML::script('resources/assets/js/lib/additional-methods.min.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::script('resources/assets/js/lib/lightbox.js') }}
{{ HTML::style('resources/assets/css/lib/lightbox.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
	$(document).ready(function() {
		setDatePicker("startdate");
	});
</script>
<style type="text/css">
	.vish{visibility: hidden;}
</style>
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="staff" class="DEC_flex_wrapper " data-category="staff staff_sub_6">
	{{ Form::open(array('name'=>'frvisarenewaddedit', 
						'id'=>'frvisarenewaddedit', 
						'url' => 'Visarenew/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('Emp_ID', $request->Emp_ID , array('id' => 'Emp_ID')) }}
		{{ Form::hidden('visanumber', (isset($visaRenewDetails[0]->visaNo)?$visaRenewDetails[0]->visaNo:'') , array('id' => 'visanumber')) }}
		{{ Form::hidden('visaid', (isset($visaRenewDetails[0]->id)?$visaRenewDetails[0]->id:'') , array('id' => 'visaid')) }}
	<!-- Start Heading -->
	<div class="row hline">
	<div class="col-xs-12 pm0 ml5">
			<img class="pull-left box35 mt15" src="{{ URL::asset('resources/assets/images/visarenew.png') }}">
			<h2 class="pull-left pl5 mt15">
				{{ trans('messages.lbl_visarenew') }}
			</h2>
			<h2 class="pull-left mt15">・</h2>
			<h2 class="pull-left mt15 green">{{ trans('messages.lbl_register') }}</h2>
		</div>
	</div>
	<div class="pb10"></div>
	<div class="col-xs-12 pl5 pr5">
		<div class="col-xs-12 pm0 mb10 mt5">
		<fieldset class="pm0">
		<legend align="left" class="ml15" 
        style="width: auto !important; background-color: white; border:none !important;float: left; font-size: 15px; position: relative; margin-top: -10px;">
        <b class="ml5 mr5">{{ trans('messages.lbl_employee_details') }}</b></legend>
		<div class="col-xs-12">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_employeeid') }}<span class="fr ml10 red vish">*</span></label>
			</div>
			<div class="col-xs-8 text-left clr_blue">
				<label class="fwb blue">{{ $visaRenewDetails[0]->Emp_ID }}</label>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_empName') }}<span class="fr ml10 red vish">*</span></label>
			</div>
			<div class="col-xs-8 text-left">
				<span>{{ empnamelength($visaRenewDetails[0]->LastName, $visaRenewDetails[0]->FirstName, 200) }}</span>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_doj') }}<span class="fr ml10 red vish">*</span></label>
			</div>
			<div class="col-xs-8 text-left">
				<span>{{ $visaRenewDetails[0]->DOJ }}</span>
				{{ Form::hidden('doj', $visaRenewDetails[0]->DOJ , array('id' => 'doj')) }}
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_religion') }}<span class="fr ml10 red vish">*</span></label>
			</div>
			<div class="col-xs-8 text-left">
				<span>
					@if($visaRenewDetails[0]->religion!="")
						{{ (isset($visaRenewDetails[0]->religion)?$religiontype[$visaRenewDetails[0]->religion]:'Nill') }}
					@else
						Nill
					@endif
				</span>
				{{ Form::hidden('religion', $visaRenewDetails[0]->religion , array('id' => 'religion')) }}
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_gender') }}<span class="fr ml10 red vish">*</span></label>
			</div>
			<div class="col-xs-8 text-left">
				<span>
					@if($visaRenewDetails[0]->Gender=="1")
                    	{{trans('messages.lbl_male')}}
                  	@elseif($visaRenewDetails[0]->Gender=="2")
                    	{{trans('messages.lbl_female')}}
                  	@else
                    	{{ "Nill" }}
                  	@endif
				</span>
				{{ Form::hidden('sex', $visaRenewDetails[0]->Gender , array('id' => 'sex')) }}
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_maritalstatus') }}<span class="fr ml10 red vish">*</span></label>
			</div>
			<div class="col-xs-8 text-left">
				<span>
					@if($visaRenewDetails[0]->martialStatus=="1")
                    	{{ trans('messages.lbl_single') }}
                  	@elseif($visaRenewDetails[0]->martialStatus=="2")
                    	{{ trans('messages.lbl_married') }}
                   	@else
                    	{{ "Nill" }}    
                  	@endif
				</span>
				{{ Form::hidden('maritalstate', $visaRenewDetails[0]->martialStatus , array('id' => 'maritalstate')) }}
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-4 text-right clr_blue"> 
				<label>{{ trans('messages.lbl_designation') }}<span class="fr ml10 red vish">*</span></label>
			</div>
			<div class="col-xs-8 text-left">
				<span>{{ ($visaRenewDetails[0]->designation!="")?$visaRenewDetails[0]->DesignationNM:'Nill' }}</span>
				{{ Form::hidden('ocupation', $visaRenewDetails[0]->designation , array('id' => 'ocupation')) }}
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_placeofbirth') }}<span class="fr ml10 red vish">*</span></label>
			</div>
			<div class="col-xs-8 text-left">
				<span>{{ ($visaRenewDetails[0]->indiaAddress != "")?$visaRenewDetails[0]->indiaAddress:'Nill' }}</span>
				{{ Form::hidden('placeofBirth', $visaRenewDetails[0]->indiaAddress , array('id' => 'placeofBirth')) }}
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_address') }}<span class="fr ml10 red vish">*</span></label>
			</div>
			<div class="col-xs-8 text-left">
				<span>{{ ($visaRenewDetails[0]->full_address !="")?$visaRenewDetails[0]->full_address: 'Nill' }}</span>
				{{ Form::hidden('address', $visaRenewDetails[0]->Address , array('id' => 'address')) }}
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_mobilenumber') }}<span class="fr ml10 red vish">*</span></label>
			</div>
			<div class="col-xs-8 text-left">
				<span>{{ ($visaRenewDetails[0]->Mobile1 != "")?$visaRenewDetails[0]->Mobile1:'Nill'}}</span>
				{{ Form::hidden('telnumber', $visaRenewDetails[0]->Mobile1 , array('id' => 'telnumber')) }}
			</div>
		</div>
		</fieldset>
		</div>
		<div class="col-xs-12 pm0 mb10">
		<fieldset class="pm0">
		<legend align="left" class="ml15" 
        style="width: auto !important; background-color: white; border:none !important;float: left; font-size: 15px; position: relative; margin-top: -10px;">
        <b class="ml5 mr5"> {{ trans('messages.lbl_passport') }} </b></legend>
		<div class="col-xs-12">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_passportno') }}<span class="fr ml10 red vish">*</span></label>
			</div>
			<div class="col-xs-8 text-left">
				<span>{{ ($visaRenewDetails[0]->passportNo!="")?$visaRenewDetails[0]->passportNo:'Nill' }}</span>
				{{ Form::hidden('passportnumb', $visaRenewDetails[0]->passportNo , array('id' => 'passportnumb')) }}
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_dateofexpiry') }}<span class="fr ml10 red vish">*</span></label>
			</div>
			<div class="col-xs-8 text-left">
				<span>{{ ($visaRenewDetails[0]->passportExpiryDate!="")?$visaRenewDetails[0]->passportExpiryDate:'Nill' }}</span>
				{{ Form::hidden('passportexipry', $visaRenewDetails[0]->passportExpiryDate , array('id' => 'passportexipry')) }}
			</div>
		</div>
		</fieldset>
		</div>
		<div class="col-xs-12 pm0 mb10">
		<fieldset class="pm0">
		<legend align="left" class="ml15" 
        style="width: auto !important; background-color: white; border:none !important;float: left; font-size: 15px; position: relative; margin-top: -10px;">
        <b class="ml5 mr5"> {{ trans('messages.lbl_visa') }} </b></legend>	
		<div class="col-xs-12">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_statusofresid') }}<span class="fr ml10 red vish">*</span></label>
			</div>
			<div class="col-xs-8 text-left">
				<span>{{ $visaRenewDetails[0]->NewVisaStatus }}</span>
				{{ Form::hidden('statusofresid', $visaRenewDetails[0]->visaStatus , array('id' => 'statusofresid')) }}
				{{ Form::hidden('visaposition', $visaRenewDetails[0]->VisaPosNM , array('id' => 'visaposition')) }}
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_periodofstay') }}<span class="fr ml10 red vish">*</span></label>
			</div>
			<div class="col-xs-8 text-left">
					{{ (!empty($visaRenewDetails[0]->visaValidPeriod)?$visaRenewDetails[0]->visaValidPeriod:'') }}
						<span>{{ trans('messages.lbl_year') }}</span>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_Start_date') }}<span class="fr ml10 red vish">*</span></label>
			</div>
			<div class="col-xs-8 text-left">
				{{ (isset($visaRenewDetails[0]->visaStartDate)) ? $visaRenewDetails[0]->visaStartDate:'' }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_enddate') }}<span class="fr ml10 red vish">*</span></label>
			</div>
			<div class="col-xs-8 text-left">
				{{ (isset($visaRenewDetails[0]->visaExpiryDate)) ? $visaRenewDetails[0]->visaExpiryDate:'' }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_residencecardno') }}<span class="fr ml10 red vish">*</span></label>
			</div>
			<div class="col-xs-8 text-left">
				{{ (isset($visaRenewDetails[0]->visaNo)) ? $visaRenewDetails[0]->visaNo:'' }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_extensionlength') }}<span class="fr ml10 red">*</span></label>
			</div>
			<div class="col-xs-8 text-left">
				{{ Form::select('extyear',[null=>''] + $periodofstay,(!empty($visaRenewDetails[0]->visaExtensionPeriod)?$visaRenewDetails[0]->visaExtensionPeriod:''), 
									array('name' => 'extyear',
									'id'=>'extyear',
									'data-label' => trans('messages.lbl_extensionlength'),
									'class'=>'pl5')) }}
					<span>({{ trans('messages.lbl_inyears') }})</span>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_extensionlengthreason') }}<span class="fr ml10 red">*</span></label>
			</div>
			<div class="col-xs-8 text-left">
				{{ Form::textarea('resonforext', ($visaRenewDetails[0]->reasonforExtension)?$visaRenewDetails[0]->reasonforExtension:'Work Extended',array(
											'id'=>'resonforext',
											'name' => 'resonforext',
											'data-label' =>trans('messages.lbl_extensionlengthreason'),
											'class'=>'box50per form-control',
											'size' => '30x4')) }}
					<span style="vertical-align: top !important;">(Default)</span>
			</div>
		</div>
		@if($visaRenewDetails[0]->visaExtensionPeriod != '')
			{{--*/ $row = 'checked' /*--}}
		@else
			{{--*/ $row = '' /*--}}
		@endif
		<div class="col-xs-12">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_crimerecord') }}<span class="fr ml10 red vish">*</span></label>
			</div>
			<div class="col-xs-8 text-left clr_blue">
				<label class="pm0">
					<div class="fll ">
                  	{{ Form::radio('crime', '1', ($visaRenewDetails[0]->crimeRecord =="1") ? $visaRenewDetails[0]->crimeRecord : '', array('id' =>'crime1',
                                                'name' => 'crime',
                                                'data-label' => trans('messages.lbl_crimerecord'),
                                                'onclick' => "fncrimestatus(1)",
                                                'class' => 'amtrup')) }}
                    </div>
                    <div class="fll">
                    	<label class="ml5 mt3" style="font-weight: normal;color: black;" 
                    							for="crime1">{{ trans('messages.lbl_yes') }}</label>
                   	</div>
	            </label>
                <label class="pm0">
					<div class="fll ml5">
                  	{{ Form::radio('crime', '2', ($visaRenewDetails[0]->crimeRecord =="2") ? $visaRenewDetails[0]->crimeRecord : '', array('id' =>'crime2',
                                                'name' => 'crime',
                                                'data-label' => trans('messages.lbl_crimerecord'),
                                                'onclick' => "fncrimestatus(2)",
                                                'class' => 'amtrup')) }}
                    </div>
                    <div class="fll">
                    	<label class="ml5 mt3" style="font-weight: normal;color: black;" 
                    							for="crime2">{{ trans('messages.lbl_no') }}</label>
                   	</div>
                </label>
			</div>
		</div>
		<div class="col-xs-12 mb10" id="crimedetails" style="display: none;">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_crimedetails') }}<span class="fr ml10 red">*</span></label>
			</div>
			<div class="col-xs-8 text-left clr_blue">
				{{ Form::textarea('crimedetail', ($visaRenewDetails[0]->crimeDetails)?$visaRenewDetails[0]->crimeDetails:'',array(
											'id'=>'crimedetail',
											'name' => 'crimedetail',
											'data-label' => trans('messages.lbl_crimedetails'),
											'class'=>'box50per form-control',
											'size' => '30x4')) }}
			</div>
		</div>
		</fieldset>
		</div>
	</div>
	<div class="col-xs-12 pm0">
	<fieldset style="background-color: #DDF1FA;">
		<div class="form-group mt10 ">
			<div align="center" class="mb10">
				<button type="submit" class="btn edit btn-success addeditprocess box100">
					<i class="fa fa-plus" aria-hidden="true"></i>
						{{ trans('messages.lbl_renew') }}
				</button>
				<a onclick="javascript:fngotoindexpage();" class="btn btn-danger ml5 box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				</a>
			</div>
		</div>
	</fieldset>
	</div>
	@if(!empty($visaRenewDetails[0]->crimeRecord))
        <script type="text/javascript">
        	fncrimestatus('{{ $visaRenewDetails[0]->crimeRecord }}')
        </script>
    @endif
	{{ Form::close() }}
	</article>
</div>
{{ Form::open(array('name'=>'frvisarenewindex', 
						'id'=>'frvisarenewindex', 
						'url' => 'Visarenew/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
{{ Form::close() }}
@endsection