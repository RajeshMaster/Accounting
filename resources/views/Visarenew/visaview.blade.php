@extends('layouts.app')
@section('content')
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
{{ HTML::script('resources/assets/js/visarenew.js') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="visarenew" class="DEC_flex_wrapper " data-category="visarenew visarenew_sub_1">
	{{ Form::open(array('name'=>'frmvisaview', 
						'id'=>'frmvisaview', 
						'files'=>true,
						'method' => 'POST')) }}
	<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/visarenew.png') }}">
			<h2 class="pull-left pl5 mt15">Visa Renew</h2>
			<h2 class="pull-left mt15">・</h2>
			<h2 class="pull-left mt15">{{ trans('messages.lbl_Details') }}</h2>
		</div>
	</div>
	<!-- End Heading -->
	<div class="col-xs-12 pm0 pull-left mt10">
	<!-- Session msg -->
		@if(Session::has('success'))
			<div align="center" class="alertboxalign" role="alert">
				<p class="alert {{ Session::get('alert', Session::get('type') ) }}">
	            {{ Session::get('success') }}
	          	</p>
			</div>
		@endif
		@php Session::forget('success'); @endphp
	<!-- Session msg -->
	<div class="col-xs-6 ml10 pm0 pull-left">
		<a href="javascript:fngotovisaindex();" 
			class="btn btn-info box80">
    		<span class="fa fa-arrow-left"></span>
    			{{ trans('messages.lbl_back') }}
    	</a>
    </div>
	</div>
    <div class="mr10 ml10 box100per">
			<div class="col-xs-12 mt10 pm0">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_employeeid') }}</label>
				</div>
				<div class="col-xs-6">
					<label style="color: blue;">
					{{($visaRenewDetails[0]->Emp_ID != "")?$visaRenewDetails[0]->Emp_ID:'Nill'}}
					</label>
				</div>
			</div>
			<div class="col-xs-12 mt10 pm0">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_empName') }}</label>
				</div>
				<div class="col-xs-9">
					<label class="fwn" style="color: black;">
					{{ empnamelength($visaRenewDetails[0]->LastName, $visaRenewDetails[0]->FirstName, 25) }}
					</label>
				</div>
			</div>
			<div class="col-xs-12 mt10 pm0">
				<div class="col-xs-3 text-right clr_blue">
					<label>Passport Number</label>
				</div>
				<div class="col-xs-9">
					<label class="fwn" style="color: black;">
					{{($visaRenewDetails[0]->PassportNo != "")?$visaRenewDetails[0]->PassportNo:'Nill'}}
					</label>
				</div>
			</div>
			<div class="col-xs-12 mt10 pm0">
				<div class="col-xs-3 text-right clr_blue">
					<label>Passport Expiry</label>
				</div>
				<div class="col-xs-9">
					<label class="fwn" style="color: black;">
					{{($visaRenewDetails[0]->PassportExpiryDate != "")?$visaRenewDetails[0]->PassportExpiryDate:'Nill'}}
					</label>
				</div>
			</div>
			<div class="col-xs-12 mt10 pm0">
				<div class="col-xs-3 text-right clr_blue">
					<label>Card number</label>
				</div>
				<div class="col-xs-9">
					<label class="fwn" style="color: black;">
					{{($visaRenewDetails[0]->VisaNo != "")?$visaRenewDetails[0]->VisaNo:'Nill'}}
					</label>
				</div>
			</div>
			<div class="col-xs-12 mt10 pm0">
				<div class="col-xs-3 text-right clr_blue">
					<label>Years</label>
				</div>
				<div class="col-xs-9">
					<label class="fwn" style="color: black;">
					{{($visaRenewDetails[0]->VisaValidPeriod != "")?$visaRenewDetails[0]->VisaValidPeriod:'Nill'}}
					</label>
				</div>
			</div>
			<div class="col-xs-12 mt10 pm0">
				<div class="col-xs-3 text-right clr_blue">
					<label>Start Date</label>
				</div>
				<div class="col-xs-9">
					<label class="fwn" style="color: black;">
					{{($visaRenewDetails[0]->VisaStartDate != "")?$visaRenewDetails[0]->VisaStartDate:'Nill'}}
					</label>
				</div>
			</div>
			<div class="col-xs-12 mt10 pm0">
				<div class="col-xs-3 text-right clr_blue">
					<label>End Date</label>
				</div>
				<div class="col-xs-9">
					<label class="fwn" style="color: black;">
					{{($visaRenewDetails[0]->VisaExpiryDate != "")?$visaRenewDetails[0]->VisaExpiryDate:'Nill'}}
					</label>
				</div>
			</div>
			<div class="col-xs-12 mt10 pm0">
				<div class="col-xs-3 text-right clr_blue">
					<label>Visa Extension Period</label>
				</div>
				<div class="col-xs-9">
					<label class="fwn" style="color: black;">
					{{($visaRenewDetails[0]->VisaExtensionPeriod != "")?$visaRenewDetails[0]->VisaExtensionPeriod:'Nill'}}<span class="ml5">Yr</span>
					</label>
				</div>
			</div>
			<div class="col-xs-12 mt10 pm0">
				<div class="col-xs-3 text-right clr_blue">
					<label>Reason for Extension</label>
				</div>
				<div class="col-xs-9">
					<label class="fwn" style="color: black;">
					{{($visaRenewDetails[0]->ReasonforExtension != "")?$visaRenewDetails[0]->ReasonforExtension:'Nill'}}
					</label>
				</div>
			</div>
			<div class="col-xs-12 mt10 pm0">
				<div class="col-xs-3 text-right clr_blue">
					<label>Crime Record</label>
				</div>
				<div class="col-xs-9">
					<label class="fwn" style="color: black;">
						@if($visaRenewDetails[0]->CrimeRecord != "")
							@if($visaRenewDetails[0]->CrimeRecord == 2)
								No
							@elseif($visaRenewDetails[0]->CrimeRecord == 1)
								Yes
							@endif
						@else
							Nill
						@endif
					</label>
				</div>
			</div>
			<div class="col-xs-12 mt10 pm0">
				<div class="col-xs-3 text-right clr_blue">
					<label>Crime Details</label>
				</div>
				<div class="col-xs-9">
					<label class="fwn" style="color: black;">
					{{($visaRenewDetails[0]->CrimeDetails != "")?$visaRenewDetails[0]->CrimeDetails:'Nill'}}
					</label>
				</div>
			</div>
	</div>
	{{ Form::close() }}
</article>
</div>
@endsection