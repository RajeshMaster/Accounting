<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>{{ Session::get('systemname') }}</title>
		{{ HTML::style('resources/assets/css/menu.css') }}
		{{ HTML::style('resources/assets/css/common.css') }}
		{{ HTML::style('resources/assets/css/font-awesome.min.css') }}
		{{ HTML::style('resources/assets/css/decoration.css') }}
		{{ HTML::style('resources/assets/css/minheight.css') }}
		{{ HTML::style('resources/assets/css/widthbox.css') }}
		{{ HTML::style('resources/assets/css/paddingmargin.css') }}
		{{ HTML::style('resources/assets/css/bootstrap.min.css') }}
		{{ HTML::style('resources/assets/css/AdminLTE.css') }}
		{{ HTML::style('resources/assets/css/_all-skins.min.css') }}

		{{ HTML::script('resources/assets/js/jquery.min.js') }}
		{{ HTML::script('resources/assets/js/bootstrap.min.js') }}
		{{ HTML::script('resources/assets/js/jquery.plugin.js') }}
		{{ HTML::script('resources/assets/js/common.js') }}
		{{ HTML::script('resources/assets/js/jquery.form-validator.min.js') }}
		@if (Session::get('languageval') == 'en')
			{{ HTML::script('resources/assets/js/english.js') }}
		@elseif(empty(Session::get('languageval')))
			{{ HTML::script('resources/assets/js/japanese.js') }}
		@else
			{{ HTML::script('resources/assets/js/japanese.js') }}
		@endif
		{{ HTML::script('resources/assets/js/render.js') }}
</head>
<style type="text/css">
	.se-pre-con {
		position: fixed;
		left: 0px;
		top: 0px;
		width: 100%;
		height: 100%;
		z-index: 9999;
		background: url({{ URL::asset('resources/assets/images/loading.gif') }}) center no-repeat;
		background-size: 10%;
		background-color: rgba(255, 255, 255, .5);
	}
	.shadowforimage {
		box-shadow: 5px 5px 2px #888888;
	}
	html {
		height: 40% !important;
   		display: table;
   		margin: auto;
   		padding: 0px;
	}
	body {
		display: table-cell;
   		margin: auto;
   		overflow-y: scroll !important;
	}
</style>
<body class="check CMN_cursor_wait response" style="width: 1185px;min-height: 200px !important ;border: 1px solid white;max-width: 1350px !important">
	<div id="fixeddiv" class="CMN_menu_stretch" style="background-color: #F0F0F0 !important;">
	<div class="se-pre-con" id="se-pre-con"></div>
		<div class="CMN_header_wrap_wrap mb5">
			<div class="CMN_header_wrap">
				<div id="CMN_logo_area" style="margin-top: 4px;margin-bottom: 10px;margin-left: 2px;">
					<a href="#"><img class="logo logosize shadowforimage" style="height:40px;" src="{{ URL::asset('resources/assets/images/microbit_logo.jpg') }}"></a>
				</div>
				<div id="CMN_user_area">
					<div id="CMN_user_date" style="padding-right:10px;background-color: #F0F0F0 !important;vertical-align: middle;">
							@if(Session::get('Gender') == "2")
								<img class="img25px" src="{{ URL::asset('resources/assets/images/female.png') }}">
							@else
								<img class="img25px" src="{{ URL::asset('resources/assets/images/male.png') }}">
							@endif
							<span class="CMN_user_name">{{ Session::get('LastName')." ".Session::get('FirstName') }}</span>
					</div>
				</div>
				<div id="CMN_btn_area">
						<div class="CMN_div_logout" style="margin-top: 6px;">
							<a href="{{ url('logout') }}" class="btn btn-primary">Sign out</a>
						</div>
				</div>
			</div>
		</div>
		<!-- main_tab -->
		<div class="CMN_header_wrap_wrap" style="background-color: #F0F0F0 !important;">
			<div class="CMN_header_wrap">
				<nav id="CMN_gmenu">
					<ul class="" style="padding: 0px;">
						<li class="home jop_btn">
							<a class="pageload" href="{{ url('Menu/index?mainmenu=home&time='.date('Ymdhis')) }}">
							{{ trans('messages.lbl_home') }}</a>
						</li>
						<li class="btn_sales jop_btn">
							<a class="pageload" href="{{ url('Estimation/index?mainmenu=estimation&time='.date('Ymdhis')) }}">
							{{ trans('messages.lbl_sales') }}</a>
						</li>
						<li class="btn_expenses jop_btn">
							<a class="pageload" href="{{ url('Expenses/index?mainmenu=expenses&time='.date('Ymdhis')) }}">
							{{ trans('messages.lbl_cexpenses') }}</a>
						</li>
						@if(Session::get('userclassification') == 4)
						<li class="btn_staff jop_btn">
							<a class="pageload" href="{{ url('Staff/index?mainmenu=staff&time='.date('Ymdhis')) }}">
							{{ trans('messages.lbl_staff') }}</a>
						</li>
						@endif
						<li class="btn_mail jop_btn">
							<a class="pageload" href="{{ url('Mailstatus/index?mainmenu=mail&time='.date('Ymdhis')) }}">
							{{ trans('messages.lbl_mail') }}</a>
						</li>
						<li class="btn_master jop_btn">
							<a href="{{ url('User/index?mainmenu=user&time='.date('Ymdhis')) }}">
							{{ trans('messages.lbl_master') }}</a>
						</li>
						@if(Session::get('userclassification') == 4)
						<li class="btn_customer jop_btn">
							<a href="{{ url('EmpHistory/index?mainmenu=Employee&time='.date('Ymdhis')) }}">
							{{ trans('messages.lbl_customer') }}</a>
						</li>
						@else
						<li class="btn_customer jop_btn">
							<a href="{{ url('Customer/index?mainmenu=Customer&time='.date('Ymdhis')) }}">
							{{ trans('messages.lbl_customer') }}</a>
						</li>
						@endif
						<!-- @if(Session::get('userclassification') == 4)
						<li class="btn_ourdetails jop_btn">
							<a href="{{ url('Ourdetail/index?mainmenu=Ourdetail&time='.date('Ymdhis')) }}">
							{{ trans('messages.lbl_ourdetails') }}</a>
						</li>
						@endif -->
						<li class="btn_accounting jop_btn">
							<a href="{{ url('Accounting/index?mainmenu=Accounting&time='.date('Ymdhis')) }}">
							{{ trans('messages.lbl_accounting') }}</a>
						</li>

						@if(Session::get('userclassification') == 4 || Session::get('userclassification') == 1)
						<li class="btn_setting jop_btn">
							<a href="{{ url('Setting/index?mainmenu=Setting&time='.date('Ymdhis')) }}">
							{{ trans('messages.lbl_setting') }}</a>
						</li>
						@endif

						
					</ul>
				</nav>
			</div>
		</div>
		<!-- end_main_tab -->
		<!-- sub_tab -->
		<div class="sub_menu_size" style="background-color: #F0F0F0 !important;font-weight: normal;">
				<!-- language_icon -->
			<div class="langIcon" style="">
				{{ Form::hidden('langvalue', Session::get('setlanguageval'), array('id' => 'langvalue')) }}
				@if (Session::get('setlanguageval') == 'en')
					{!! Form::image('resources/assets/images/languageiconen.png', '', 
						 array('class' => 'pull-right search box2per pr5 langimg11', 
							'onclick' => 'javascript:return changelanguage()','style'=>'min-width:35px;cursor:pointer;')) !!}
				@elseif(empty(Session::get('setlanguageval')))
					{!! Form::image('resources/assets/images/languageiconjp.png', '', 
						 array('class' => 'pull-right search box2per pr5 langimg11', 
							'onclick' => 'javascript:return changelanguage()','style'=>'min-width:35px;cursor:pointer;')) !!}
				@else
					{!! Form::image('resources/assets/images/languageiconjp.png', '', 
						 array('class' => 'pull-right search box2per pr5 langimg11', 
							'onclick' => 'javascript:return changelanguage()','style'=>'min-width:35px;cursor:pointer;')) !!}
				@endif
			</div>
			<div id="salesDiv" class="CMN_sub_gmenu">
			@if (isset($request->mainmenu) && ($request->mainmenu == "estimation") || ($request->mainmenu == "invoice")||   ($request->mainmenu == "billing") || ($request->mainmenu == "payment")|| ($request->mainmenu == "salesdetails") ||   ($request->mainmenu == "engineerdetailsplus") ||   ($request->mainmenu == "salesplus")
			|| ($request->mainmenu == "engineerdetails"))
			<!-- Sales sub -->
				<div id="sales_sub_1">
					<a class="pageload" href="{{ url('Estimation/index?mainmenu=estimation&time='.date('Ymdhis')) }}" 
						style="text-decoration:none;color:white;">
					{{ trans('messages.lbl_estimation') }}</a>
				</div>
				<div id="sales_sub_2">
					<a class="pageload" href="{{ url('Invoice/index?mainmenu=invoice&time='.date('Ymdhis')) }}" 
					 style="text-decoration:none;color:white;">
					{{ trans('messages.lbl_invoice') }}</a>
				</div>
				@if(Session::get('userclassification') == 4)
				<!-- <div id="sales_sub_3">
					<a class="pageload" href="{{ url('Invoice/index?mainmenu=invoice&time='.date('Ymdhis')) }}" 
						 style="text-decoration:none;color:white;">{{ trans('messages.lbl_invoice')."+" }}</a>
				</div> -->
				<div id="sales_sub_7">
					<a class="pageload" href="{{ url('Engineerdetails/index?mainmenu=engineerdetails&time='.date('Ymdhis')) }}" 
						 style="text-decoration:none;color:white;">{{ trans('messages.lbl_engg_details') }}</a>
				</div>
				<div id="sales_sub_3">
					<a class="pageload" href="{{ url('Engineerdetailsplus/index?mainmenu=engineerdetailsplus&time='.date('Ymdhis')) }}" 
						 style="text-decoration:none;color:white;">{{ trans('messages.lbl_engg_detailsplus')}}</a>
				</div>
				<div id="sales_sub_6">
					<a class="pageload"  
					href="{{ url('Billing/index?mainmenu=billing&time='.date('Ymdhis')) }}" 
					style="text-decoration:none;color:white;">{{ trans('messages.lbl_billing') }}</a>
				</div>
				@endif
				<div id="sales_sub_4">
					<a class="pageload" href="{{ url('Payment/index?mainmenu=payment&time='.date('Ymdhis')) }}" 
						style="text-decoration:none;color:white;">
					{{ trans('messages.lbl_payment') }}</a>
				</div>
				@if(Session::get('userclassification') == 4)
				<div id="sales_sub_5">
					<a class="pageload" href="{{ url('Salesdetails/index?mainmenu=salesdetails&time='.date('Ymdhis')) }}" 
						style="text-decoration:none;color:white;">
					{{ trans('messages.lbl_salesdetails') }}</a>
				</div>
				@endif
				<div id="sales_sub_9">
					<a class="pageload" href="{{ url('Salesplus/index?mainmenu=salesplus&time='.date('Ymdhis')) }}" 
						style="text-decoration:none;color:white;">
					{{ trans('messages.lbl_sales_dep') }}</a>
				</div>
			<!-- //Sales Sub -->
			@endif
			@if (isset($request->mainmenu) && ($request->mainmenu == "expenses") ||
			 ($request->mainmenu == "company_transfer") || ($request->mainmenu == "pettycash") || 
			 ($request->mainmenu == "company_loan") || ($request->mainmenu == "company_salary") ||
			  ($request->mainmenu == "company_bankdetails") || ($request->mainmenu == "expdetails") || 
			  ($request->mainmenu == "engineerexpdetails") || ($request->mainmenu == "salaryplus")  || ($request->mainmenu == "salarycalc"))
			<!-- Expenses Sub -->
				<div id="expenses_sub_1">
					<a class="pageload" href="{{ url('Expenses/index?mainmenu=expenses&time='.date('Ymdhis')) }}" 
						style="text-decoration:none;color:white;">{{ trans('messages.lbl_expenses') }}</a>
				</div>
				<div id="expenses_sub_2">
					<a class="pageload" href="{{ url('Transfer/index?mainmenu=company_transfer&time='.date('Ymdhis')) }}" style="text-decoration:none;color:white;">{{ trans('messages.lbl_transfer') }}</a>
				</div>
				<div id="expenses_sub_8">
					<a class="pageload" href="{{ url('Expenses/index?mainmenu=pettycash&time='.date('Ymdhis')) }}" style="text-decoration:none;color:white;">{{ trans('messages.lbl_pettycash') }}</a>
				</div>
				@if(Session::get('userclassification') == 4)
				<div id="expenses_sub_5">
					<a class="pageload" href="{{ url('Salary/index?mainmenu=company_salary&time='.date('Ymdhis')) }}" style="text-decoration:none;color:white;">{{ trans('messages.lbl_salary') }}</a>
				</div>
				@endif
				@if(Session::get('userclassification') == 4)
				<div id="expenses_sub_7">
					<a class="pageload" href="{{ url('ExpensesDetails/index?mainmenu=expdetails&time='.date('Ymdhis')) }}" style="text-decoration:none;color:white;">{{ trans('messages.lbl_expdetail') }}</a>
				</div>
				<div id="expenses_sub_4">
					<a class="pageload" href="{{ url('Loandetails/index?mainmenu=company_loan&time='.date('Ymdhis')) }}" style="text-decoration:none;color:white;">
					{{ trans('messages.lbl_loandetail') }}</a>
				</div>
				
				<div id="expenses_sub_6">
					<a class="pageload" href="{{ url('Bankdetails/index?mainmenu=company_bankdetails&time='.date('Ymdhis')) }}" style="text-decoration:none;color:white;">
					{{ trans('messages.lbl_bankdetail') }}</a>
				</div>
				@endif
			<!-- //Expenses sub -->
			@endif
			@if (isset($request->mainmenu) && ($request->mainmenu == "staff"|| $request->mainmenu == "nonstaff"|| $request->mainmenu == "timesheet" || $request->mainmenu == "StaffContr" || $request->mainmenu == "StaffSalary" || $request->mainmenu == "visarenew" ||  $request->mainmenu == "tax")) 
			<!-- Staff Sub -->
				<div id="staff_sub_1">
					<a href="{{ url('Staff/index?mainmenu=staff&time='.date('Ymdhis')) }}" 
					style="text-decoration:none;color:white;">{{ trans('messages.lbl_staff') }}</a>
				</div>
				<div id="staff_sub_8">
					<a href="{{ url('NonStaff/index?mainmenu=nonstaff&time='.date('Ymdhis')) }}" 
					style="text-decoration:none;color:white;">{{ trans('messages.lbl_nonstaff') }}</a>
				</div>
				<div id="staff_sub_6">
					<a href="{{ url('Visarenew/index?mainmenu=visarenew&time='.date('Ymdhis')) }}" style="text-decoration:none;color:white;">{{ trans('messages.lbl_visarenew') }}</a>
				</div>
				<div id="staff_sub_2">
					<a href="{{ url('StaffContr/index?mainmenu=StaffContr&time='.date('Ymdhis')) }}" style="text-decoration:none;color:white;">{{ trans('messages.lbl_staffcontr') }}</a>
				</div>
				<div id="staff_sub_3">
					<a class="pageload"  
					href="{{ url('Timesheet/timesheetindex?mainmenu=timesheet&time='.date('Ymdhis')) }}" 
					style="text-decoration:none;color:white;">
					{{ trans('messages.lbl_timesheet') }}</a>
				</div>
				<div id="staff_sub_5">
					<a href="{{ url('StaffSalary/index?mainmenu=staff&time='.date('Ymdhis')) }}" 
					style="text-decoration:none;color:white;">
					{{ trans('messages.lbl_salary') }}</a>
				</div>
				<div id="staff_sub_7">
					<a href="{{ url('Tax/index?mainmenu=tax&time='.date('Ymdhis')) }}" 
					style="text-decoration:none;color:white;">
					{{ trans('messages.lbl_taxdetails') }}</a>
				</div>
			<!-- //Staff Sub -->
			@endif
			@if (($request->mainmenu == "mail") || ($request->mainmenu == "mailcontent")
			 || ($request->mainmenu == "mailsignature"))
			<!-- Mail Sub -->
				<div id="mail_sub_1">
					<a class="pageload" href="{{ url('Mailstatus/index?mainmenu=mail&time='.date('Ymdhis')) }}" style="text-decoration:none;color:white;">
					{{ trans('messages.lbl_mailstatus') }}</a>
				</div>
				<div id="mail_sub_2">
					<a class="pageload" href="{{ url('Mailcontent/index?mainmenu=mailcontent&time='.date('Ymdhis')) }}" 
						style="text-decoration:none;color:white;">
					{{ trans('messages.lbl_mailcontent') }}</a>
				</div>
				<div id="mail_sub_3">
				@if(Session::get('userclassification') == 4)
					<a class="pageload" href="{{ url('Mailsignature/index?mainmenu=mailsignature&time='.date('Ymdhis')) }}" 
						style="text-decoration:none;color:white;">
					{{ trans('messages.lbl_mailsignature') }}</a>
				@else
					<a class="pageload" href="{{ url('Mailsignature/view?mainmenu=mailsignature&time='.date('Ymdhis')) }}" 
						style="text-decoration:none;color:white;">
					{{ trans('messages.lbl_mailsignature') }}</a>
				@endif	
				</div>
			<!-- //Mail Sub -->
			@endif
			@if (isset($request->mainmenu) && ($request->mainmenu == "user" || $request->mainmenu == "Bank_invoice")) 
			<!-- Master Sub -->
				<div id="master_sub_1">
					<a class="pageload" href="{{ url('User/index?mainmenu=user&time='.date('Ymdhis')) }}" 
						style="text-decoration:none;color:white;">
					{{ trans('messages.lbl_user') }}</a>
				</div>
				@if(Session::get('userclassification') == 4)
				<div id="master_sub_2">
					<a href="{{ url('Bank/index?mainmenu=Bank_invoice&time='.date('Ymdhis')) }}" style="text-decoration:none;color:white;">
					{{ trans('messages.lbl_bank') }}</a>
				</div>
				@endif
			<!-- //Master Sub -->
			@endif
			
			@if (isset($request->mainmenu) && ($request->mainmenu == "Customer" || $request->mainmenu == "Employee" || $request->mainmenu == "MeetingDetails"))
			<!-- Customer Sub -->
				@if(Session::get('userclassification') == 4)
				<div id="customer_sub_1">
					<a class="pageload" href="{{ url('EmpHistory/index?mainmenu=Employee&time='.date('Ymdhis')) }}" style="text-decoration:none;color:white;">{{ trans('messages.lbl_emphistory') }}</a>
				</div>
				@endif
				<div id="customer_sub_2">
					<a class="pageload" href="{{ url('Customer/index?mainmenu=Customer&time='.date('Ymdhis')) }}" style="text-decoration:none;color:white;">{{ trans('messages.lbl_customer') }}</a>
				</div>
				@if(Session::get('userclassification') == 4)
				<div id="customer_sub_3">
					<a class="pageload" href="{{ url('MeetingDetails/index?mainmenu=Customer&time='.date('Ymdhis')) }}" style="text-decoration:none;color:white;">{{ trans('messages.lbl_meetingdet') }}</a>
				</div>
				@endif
			<!-- //Customer sub -->
			@endif
	
			<?php if ($request->mainmenu == "Accounting" || $request->mainmenu == "Accounting") {?>
				<div id="accounting_sub_1">
					<a href="{{ url('Setting/index?mainmenu=Setting&time='.date('Ymdhis')) }}" style="text-decoration:none;color:white;">{{ trans('messages.lbl_invoice') }}</a>
				</div>

				<div id="accounting_sub_2">
					<a href="{{ url('Ourdetail/index?mainmenu=Ourdetail&time='.date('Ymdhis')) }}" style="text-decoration:none;color:white;">{{ trans('messages.lbl_salary_det') }}</a>
				</div>
			<?php } ?>
			<?php if ($request->mainmenu == "Setting" || $request->mainmenu == "Ourdetail") {?>
			<!-- Setting Sub -->
				<div id="setting_sub_1">
					<a href="{{ url('Setting/index?mainmenu=Setting&time='.date('Ymdhis')) }}" style="text-decoration:none;color:white;">{{ trans('messages.lbl_setting') }}</a>
				</div>

				<div id="setting_sub_2">
					<a href="{{ url('Ourdetail/index?mainmenu=Ourdetail&time='.date('Ymdhis')) }}" style="text-decoration:none;color:white;">{{ trans('messages.lbl_ourdetails') }}</a>
				</div>
			<!-- // Setting Sub -->
			<?php } ?>
			</div>
		</div>
	<!-- end_sub_tab -->
	</div>
	<!-- Content Wrapper. Contains page content -->
	<div id="sectiondiv" class="bg_white" style="min-width: 700px;margin-top:0px;">
		@yield('content')
	</div>
</body>
</html>