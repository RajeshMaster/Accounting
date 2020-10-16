@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/nonstaff.js') }}
{{ HTML::script('resources/assets/js/lib/lightbox.js') }}
{{ HTML::style('resources/assets/css/lib/lightbox.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
<style type="text/css">
	.alertboxalign {
    	margin-bottom: -50px !important;
	}
	.alert {
	    display:inline-block !important;
	    height:30px !important;
	    padding:5px !important;
	}
</style>
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="staff" class="DEC_flex_wrapper " data-category="staff staff_sub_8">
{{ Form::open(array('name'=>'frmnonstaffview',
						'id'=>'frmnonstaffview',
						'url' => 'NonStaff/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	{{ Form::hidden('viewid', $request->viewid , array('id' => 'viewid')) }}	
	{{ Form::hidden('editid', $request->editid , array('id' => 'editid')) }}
	{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	{{ Form::hidden('editflg', '', array('id' => 'editflg')) }}
	<div class="row hline pm0">
	<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/employee.png') }}">
			<h2 class="pull-left pl5 mt10 CMN_mw150">{{ trans('messages.lbl_nonstafflist') }}<span>・</span><span class="colbl">{{ trans('messages.lbl_view') }}</span></h2>
		</div>
	</div>
	<div class="pb10"></div>
	<!-- End Heading -->
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
	<div class="pl5 pr5">
		<div class="pull-left ml5">
			<a href="javascript:goindexpage('{{ $request->mainmenu }}');" class="pageload btn btn-info box80"><span class="fa fa-arrow-left"></span> {{ trans('messages.lbl_back') }}</a>
		</div>
			@if($request->resignid == "" || $request->resignid == "0")
				<div class="pull-right mr5">
					<a href="javascript:fnunderconstr();" class="btn btn-primary box100 pull-right pr10"><span class="fa fa-close"></span> {{ trans('messages.lbl_resign') }}</a>
				</div>
				<div class="pull-right mr10">
				<a href="javascript:editview('edit','{{ $request->viewid }}');" class="btn btn-warning box80 pull-right pr10"><span class="fa fa-pencil"></span> {{ trans('messages.lbl_edit') }} </a>
			</div>
			@else
				<a href="javascript:fnunderconstr();" class="btn btn-primary box100 pull-right pr10"><span class="fa fa-close"></span> {{ trans('messages.lbl_rejoin') }}</a>
			@endif
	<div class="col-xs-12 pl5 pr5">
	<fieldset>
		<div class="box60per CMN_display_block">
			<div class="col-xs-12 mt10">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_employeeid') }}</label>
				</div>
				<div>
					{{ ($staffdetail[0]->Emp_ID != "") ? $staffdetail[0]->Emp_ID : 'Nill'}}
				</div>
			</div>
			<div class="col-xs-12">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_opendate') }}</label>
				</div>
				<div>
					{{ ($staffdetail[0]->DOJ != "") ? $staffdetail[0]->DOJ : 'Nill'}}
				</div>
			</div>
			<div class="col-xs-12">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_staffusersurname') }}</label>
				</div>
				<div>
					{{ ($staffdetail[0]->FirstName != "") ? $staffdetail[0]->FirstName : 'Nill'}}
				</div>
			</div>
			<div class="col-xs-12">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_staffusername') }}</label>
				</div>
				<div>
					{{ ($staffdetail[0]->LastName != "") ? $staffdetail[0]->LastName : 'Nill'}}
				</div>
			</div>
			<div class="col-xs-12">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_nickname') }}</label>
				</div>
				<div>
					{{ ($staffdetail[0]->nickname != "") ? $staffdetail[0]->nickname : 'Nill'}}
				</div>
			</div>
			<div class="col-xs-12">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_kanaFirstName') }}</label>
				</div>
				<div>
					{{ ($staffdetail[0]->KanaFirstName != "") ? $staffdetail[0]->KanaFirstName : 'Nill'}}
				</div>
			</div>
			<div class="col-xs-12">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_kanaLastName') }}</label>
				</div>
				<div>
					{{ ($staffdetail[0]->KanaLastName != "") ? $staffdetail[0]->KanaLastName : 'Nill'}}
				</div>
			</div>
			<div class="col-xs-12">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_gender') }}</label>
				</div>
				<div>
					{{ ($staffdetail[0]->Gender == "1") ? trans('messages.lbl_male') : trans('messages.lbl_female')}}
				</div>
			</div>
			<div class="col-xs-12">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_dob') }}</label>
				</div>
				<div>
				{{ ($staffdetail[0]->DOB != "") ? $staffdetail[0]->DOB : 'Nill'}}
				</div>
			</div>
			<div class="col-xs-12">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_mobileno') }}</label>
				</div>
				<div>
					{{ ($staffdetail[0]->Mobile1 != "") ? $staffdetail[0]->Mobile1 : 'Nill'}}
				</div>
			</div>
			<div class="col-xs-12">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_mailid') }}</label>
				</div>
				<div>
					{{ ($staffdetail[0]->Emailpersonal != "") ? $staffdetail[0]->Emailpersonal : 'Nill'}}
				</div>
			</div>
			<div class="col-xs-12">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_streetaddress') }}</label>
				</div>
				<div class="col-xs-9 box60per pm0">
					@if(!empty($staffdetail[0]->Address1))
						@if(is_numeric($staffdetail[0]->Address1))
							@if(isset($staffdetail[0]->pincode) || isset($staffdetail[0]->jpstate) || isset($staffdetail[0]->jpaddress))
                  				〒{{ $staffdetail[0]->pincode }} {{ $staffdetail[0]->jpstate }}{{ $staffdetail[0]->jpaddress }} {{ $staffdetail[0]->roomno }}号
		                	@else
		                		{{ $staffdetail[0]->Address1 }}
		                	@endif
		                @else
		                  {!! nl2br(e($staffdetail[0]->Address1)) !!}
		                @endif
					@else
						NIL
					@endif
				</div>
			</div>
			<div class="col-xs-12">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_resigneddate') }}</label>
				</div>
				<div>
					{{ ($staffdetail[0]->resignedDate != "") ? $staffdetail[0]->resignedDate : 'Nill'}}
				</div>
			</div>
			<div class="col-xs-12">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_bank_name') }}</label>
				</div>
				<div>
					{{ ($staffdetail[0]->BankName != "") ? $staffdetail[0]->BankName : 'Nill'}}
				</div>
			</div>
			<div class="col-xs-12">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_branch_name') }}</label>
				</div>
				<div>
					{{ ($staffdetail[0]->BranchName != "") ? $staffdetail[0]->BranchName : 'Nill'}}
				</div>
			</div>
						<div class="col-xs-12">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_account_no') }}</label>
				</div>
				<div>
					{{ ($staffdetail[0]->AccNo != "") ? $staffdetail[0]->AccNo : 'Nill'}}
				</div>
			</div>
			<div class="col-xs-12">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_branch_number') }}</label>
				</div>
				<div>
					{{ ($staffdetail[0]->BranchNo != "") ? $staffdetail[0]->BranchNo : 'Nill'}}
				</div>
			</div>
		</div>
		<div class="box30per CMN_display_block vat mt52 ml110">
			@if(isset($staffdetail) && $staffdetail[0]->Picture != "")
				<?php $file_url = 'resources/assets/images/upload/' . $staffdetail[0]->Picture; ?>
					@if(isset($staffdetail[0]->Picture) && file_exists($file_url))
						<a style="text-decoration:none" href="{{ URL::asset('resources/assets/images/upload/'.$staffdetail[0]->Picture) }}"  data-lightbox="visa-img">
						<img class="pull-left box170 mr5  ml20" name="empimg" id="empimg"  src="{{ URL::asset('resources/assets/images/upload').'/'.$staffdetail[0]->Picture }}" width="180" height="180"></a>
						</img>
					@else
					@endif
				@endif
			</div>
	</fieldset>
	</div>
	{{ Form::close() }}
	<div id="resign" class="modal fade">
		<div id="login-overlay">
			<div class="modal-content">
			<!-- Popup will be loaded here -->
			</div>
		</div>
	</div>
</div>
</article>
</div>
<div class="CMN_display_block pb10"></div>
@endsection