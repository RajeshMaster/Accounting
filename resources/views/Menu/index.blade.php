@extends('layouts.menulayout')
@section('content')
@if (Session::get('setlanguageval') == 'en')
	{{ HTML::script('resources/assets/js/english.js') }}
@elseif(empty(Session::get('setlanguageval')))
	{{ HTML::script('resources/assets/js/japanese.js') }}
@else
	{{ HTML::script('resources/assets/js/japanese.js') }}
@endif
<script type="text/javascript">
	function changelanguage() {
		// var err_changelanguage = "言語を変更しますか？。";
		if ($('#langvalue').val() == "jp") {
			var err_lang = "Do You Want To Change The Language?.";
		} else {
			var err_lang = "言語を変更しますか？。";
		}
		// var confm = confirm("Do You Want To Change The Language?.");
		if (!confirm(err_lang)) {
			return false;
		}
		$.ajax({
			type:'GET',
			url:'changelanguage',
			data: {
				langvalue: $('#langvalue').val()
			},
			success:function(data){
				location.reload(true);
			},
			error: function (data) {
				// alert(data.status);
			}
		});
	}
</script>
<style type="text/css">
.image_b {
    width: 150px;
    height: 40px;
    background-color: #0b93b3;
    background-repeat: no-repeat;
    color: #ffffff;
    padding-top: 10px;
    font-size: 15px;
    text-align: center;

}
.div_inline {
    display: inline-block;
}
.alinkEng {
    background: url(../resources/assets/images/resultset_next.png) no-repeat left center;
    height: 16px;
    vertical-align: text-bottom;
    font-size: 12px;
    padding-left: 16px;
}
#for_stretch_align {
   	display: table;
   	margin: auto;
   	width: 1185px !important;
}
</style>
{{ Form::hidden('langvalue', Session::get('setlanguageval'), array('id' => 'langvalue')) }}
<div class="" id="for_stretch_align">
<!-- article to select the main&sub menu -->
		<div class="col-xs-12">
			@if(Auth::user()->userclassification == 4)
			<div class="col-xs-3">
			@else
			<div class="col-xs-4" style="padding-left: 100px;">
			@endif
				<div class="image_b div_inline mb15 mr95 ml110">
					{{ trans('messages.lbl_sales') }}</div>
				<div class="alinkEng ml130 tal">
					<a class="pageload csrp btn-link" href="{{ url('Estimation/index?mainmenu=estimation&time='.date('Ymdhis')) }}" 
					style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_estimation') }}
					</a>
				</div>
				<div class="alinkEng ml130 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('Invoice/index?mainmenu=invoice&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
					{{ trans('messages.lbl_invoice') }}
					</a>
				</div>
				@if(Auth::user()->userclassification == 4)
				<!-- <div class="alinkEng ml130 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('Invoice/index?mainmenu=invoice&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
					{{ trans('messages.lbl_invoice')."+"  }}
					</a>
				</div> -->
				<div class="alinkEng ml130 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('Engineerdetails/index?mainmenu=engineerdetails&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
					{{ trans('messages.lbl_engg_details')  }}
					</a>
				</div>
				<div class="alinkEng ml130 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('Engineerdetailsplus/index?mainmenu=engineerdetailsplus&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
					{{ trans('messages.lbl_engg_detailsplus')  }}
					</a>
				</div>
				<div class="alinkEng ml130 mt5">
					<a class="pageload csrp btn-link"  
					href="{{ url('Billing/index?mainmenu=billing&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_billing') }}
					</a>
				</div>
				@endif
				<div class="alinkEng ml130 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('Payment/index?mainmenu=payment&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
					{{ trans('messages.lbl_payment') }}
					</a>
				</div>
				@if(Auth::user()->userclassification == 4)
				<div class="alinkEng ml130 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('Salesdetails/index?mainmenu=salesdetails&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
					{{ trans('messages.lbl_salesdetails') }}
					</a>
				</div>
				@endif
				<div class="alinkEng ml130 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('Salesplus/index?mainmenu=salesplus&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
					{{ trans('messages.lbl_sales_dep') }}
					</a>
				</div>
			</div>
			@if(Auth::user()->userclassification == 4)
			<div class="col-xs-3" style="padding-left: 88px;">
			@else
			<div class="col-xs-4" style="padding-left: 117px;">
			@endif
				<div class="image_b div_inline mb15 mr120">{{ trans('messages.lbl_cexpenses') }}</div>
				<div class="alinkEng ml20 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('Expenses/index?mainmenu=expenses&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_expenses') }}
					</a>
				</div>
				<div class="alinkEng ml20 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('Transfer/index?mainmenu=company_transfer&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_transfer') }}
					</a>
				</div>
				<div class="alinkEng ml20 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('Expenses/index?mainmenu=pettycash&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_pettycash') }}
					</a>
				</div>
				@if(Auth::user()->userclassification == 4)
				<div class="alinkEng ml20 mt5">
					<a class="pageload csrp btn-link" 
					href="{{ url('Salaryplus/index?mainmenu=salaryplus&time='.date('Ymdhis')) }}" 
					style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_salaryplus') }}
					</a>
				</div>
				<div class="alinkEng ml20 mt5">
					<a class="pageload csrp btn-link" 
					href="{{ url('salarycalc/index?mainmenu=salaryplus&time='.date('Ymdhis')) }}" 
					style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_salary_calc') }}
					</a>
				</div>
				 <div class="alinkEng ml20 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('Salary/index?mainmenu=company_salary&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_salary') }}
					</a>
				</div>
				<div class="alinkEng ml20 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('Engineerdetails/expenseindex?mainmenu=engineerexpdetails&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_enggexp_details') }}
					</a>
				</div>
				<div class="alinkEng ml20 mt5 tal">
					<a class="csrp btn-link" href="{{ url('ExpensesDetails/index?mainmenu=expdetails&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_expdetail') }}
					</a>
				</div>
				<div class="alinkEng ml20 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('Loandetails/index?mainmenu=company_loan&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
					{{ trans('messages.lbl_loandetail') }}
					</a>
				</div>
				<div class="alinkEng ml20 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('Bankdetails/index?mainmenu=company_bankdetails&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
					{{ trans('messages.lbl_bankdetail') }}
					</a>
				</div>
				@endif
			</div>
			@if(Auth::user()->userclassification == 4)
			<div class="col-xs-3" style="padding-left: 53px;">
				<div class="image_b div_inline mb15">{{ trans('messages.lbl_staff') }}</div>
				<div class="alinkEng ml20 mt5">
					<a class="pageload csrp btn-link" href="{{ url('Staff/index?mainmenu=staff&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_staff') }}
					</a>
				</div>
				<div class="alinkEng ml20 mt5">
					<a class="pageload csrp btn-link" href="{{ url('NonStaff/index?mainmenu=nonstaff&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_nonstaff') }}
					</a>
				</div>
				<div class="alinkEng ml20 mt5">
					<a class="pageload csrp btn-link" href="{{ url('Visarenew/index?mainmenu=visarenew&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_visarenew') }}
				</div>
				<div class="alinkEng ml20 mt5">
					<a class="pageload csrp btn-link" href="{{ url('StaffContr/index?mainmenu=staff&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_staffcontr') }}
					</a>
				</div>
				
				<div class="alinkEng ml20 mt5">
					<a class="pageload csrp btn-link"  
					href="{{ url('Timesheet/timesheetindex?mainmenu=timesheet&time='.date('Ymdhis')) }}"  style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_timesheet') }}
					</a>
				</div>
				
				<div class="alinkEng ml20 mt5">
					<a class="pageload csrp btn-link" 
					href="{{ url('StaffSalary/index?mainmenu=staff&time='.date('Ymdhis')) }}" 
					style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_salary') }}
					</a>
				</div>

			<!-- 	<div class="alinkEng ml20 mt5">
					<a class="pageload csrp btn-link" 
					href="{{ url('Salaryplus/index?mainmenu=salaryplus&time='.date('Ymdhis')) }}" 
					style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_salaryplus') }}
					</a>
				</div> -->
				<!-- Tax Details Created by kumaran.L 2018-03-27 -->
				<div class="alinkEng ml20 mt5">
					<a class="pageload csrp btn-link" 
					href="{{ url('Tax/index?mainmenu=tax&time='.date('Ymdhis')) }}" 
					style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_taxdetails') }}
					</a>
				</div>
			</div>
			@endif
			@if(Auth::user()->userclassification == 4)
			<div class="col-xs-3">
			@else
			<div class="col-xs-4" style="padding-left: 23px;">
			@endif
				<div class="image_b div_inline mb15 mr90">
					{{ trans('messages.lbl_mail') }}</div>
				<div class="alinkEng ml20 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('Mailstatus/index?mainmenu=mail&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
					{{ trans('messages.lbl_mailstatus') }}
					</a>
				</div>
				<div class="alinkEng ml20 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('Mailcontent/index?mainmenu=mailcontent&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
					{{ trans('messages.lbl_mailcontent') }}
					</a>
				</div>
				@if(Session::get('userclassification') == 4)
				<div class="alinkEng ml20 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('Mailsignature/index?mainmenu=mailsignature&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
					{{ trans('messages.lbl_mailsignature') }}
					</a>
				</div>
				@else
				<div class="alinkEng ml20 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('Mailsignature/view?mainmenu=mailsignature&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
					{{ trans('messages.lbl_mailsignature') }}
					</a>
				</div>
				@endif
			</div>
		</div>
		@if(Auth::user()->userclassification == 4)
		<div class="col-xs-12 mt40 ml0">
		@else
		<div class="col-xs-12 mt40" style="padding-left: 100px;">
		@endif
			<div class="col-xs-3">
				<div class="image_b div_inline mb15 mr95 ml110">{{ trans('messages.lbl_master') }}</div>
				<div class="alinkEng ml130 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('User/index?mainmenu=user&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_user') }}
					</a>
				</div>
				@if(Auth::user()->userclassification == 4)
				<div class="alinkEng ml130 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('Bank/index?mainmenu=Bank_invoice&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_bank') }}
					</a>
				</div>
				@endif
			</div>
				@if(Auth::user()->userclassification == 4)
			<div class="col-xs-3" style="padding-left: 88px;">
			@else
			<div class="col-xs-3" style="padding-left: 150px;">
			@endif
				<div class="image_b div_inline mb15 mr120">
					{{ trans('messages.lbl_customer') }}</div>
				@if(Auth::user()->userclassification == 4)
				<div class="alinkEng ml20 mt5 tal">
					<a class="pageload csrp btn-link csrp" href="{{ url('EmpHistory/index?mainmenu=Employee&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_emphistory') }}
					</a>
				</div>
				@endif
				<div class="alinkEng ml20 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('Customer/index?mainmenu=Customer&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_customer') }}
					</a>
				</div>
				@if(Auth::user()->userclassification == 4)
				<div class="alinkEng ml20 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('MeetingDetails/index?mainmenu=Customer&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_meetingdet') }}
					</a>
				</div>
				@endif
			</div>
			@if(Auth::user()->userclassification == 4)
			<div class="col-xs-3" style="padding-left: 53px;">
				<div class="image_b div_inline mb15 mr90">{{ trans('messages.lbl_ourdetails') }}</div>
				<div class="alinkEng ml20 mt5 tal">
					<a class="pageload csrp btn-link" href="{{ url('Ourdetail/index?mainmenu=Ourdetail&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
						{{ trans('messages.lbl_ourdetails') }}
					</a>
				</div>
			</div>
			@endif
			@if(Auth::user()->userclassification == 4)
			<div class="col-xs-3">
			@else
			<div class="col-xs-3" style="padding-left: 172px;">
			@endif
				<div class="image_b div_inline mb15">{{ trans('messages.lbl_setting') }}</div>
					<div class="alinkEng ml20 mt5">
						<a class="pageload csrp btn-link" href="{{ url('Setting/index?mainmenu=Setting&time='.date('Ymdhis')) }}" style="color:blue;font-size: 13px;">
							{{ trans('messages.lbl_setting') }}
						</a>
					</div>
				<div class="alinkEng ml20 mt5">
					{{ Form::hidden('langvalue', Session::get('setlanguageval'), array('id' => 'langvalue')) }}
				@if (Session::get('setlanguageval') == 'en')
					<a class="csrp btn-link" href="javascript:;" onclick="javascript:return changelanguage();" style="color:blue;font-size: 13px;">
					{{ trans('messages.lbl_english') }}
					</a>
				@elseif(empty(Session::get('setlanguageval')))
					<a class="csrp btn-link" href="javascript:;" onclick="javascript:return changelanguage();" style="color:blue;font-size: 13px;">
					{{ trans('messages.lbl_japanese') }}
					</a>
				@else
					<a class="csrp btn-link" href="javascript:;" 
					onclick="javascript:return changelanguage();" style="color:blue;font-size: 13px;">
					{{ trans('messages.lbl_japanese') }}
					</a>
				@endif
					</a>
				</div>
			</div>
			<div class="col-xs-3">
				
			</div>
		</div>
</div>
@endsection