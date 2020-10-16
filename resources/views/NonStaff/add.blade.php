@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/nonstaff.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::script('resources/assets/js/lib/additional-methods.min.js') }}
{{ HTML::script('resources/assets/js/lib/lightbox.js') }}
{{ HTML::style('resources/assets/css/lib/lightbox.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
	$(document).ready(function() {
		setDatePicker18yearbefore("dob");
		setDatePicker("opd");

	});
</script>
<style type="text/css">
	.ime_mode_disable {
		ime-mode:disabled;
	}
</style>	
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="staff" class="DEC_flex_wrapper " data-category="staff staff_sub_8">
	{{ Form::open(array('name'=>'frmnonstaffaddedit','id'=>'frmnonstaffaddedit', 'url' => 'NonStaff/nonstaffaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
	{{ Form::hidden('DOB', $dob_year, array('id' => 'DOB')) }}
	{{ Form::hidden('viewid', $request->viewid , array('id' => 'viewid')) }}
	{{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
	@if(isset($staffview[0]->Emp_ID))
		{{ Form::hidden('hdnempid', (isset($staffview[0]->Emp_ID)) ? $staffview[0]->Emp_ID : '', array('id' => 'hdnempid')) }}
	@endif
	{{ Form::hidden('pictureId', (isset($staffview[0]->Picture)) ? $staffview[0]->Picture : '', array('id' => 'pictureId')) }}
	<!-- Start Heading -->
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/staffList.png') }}">
			<h2 class="pull-left pl5 mt10">{{ trans('messages.lbl_nonstafflist') }}</h2>
			<h2 class="pull-left mt10">・</h2>
			<h2 class="pull-left mt10 CMN_mw150">
				@if($request->editflg!="edit")
					<span class="green">
						{{ trans('messages.lbl_register') }}
					</span>
				@else
					<span class="red">
						{{ trans('messages.lbl_edit') }}
					</span>
				@endif
			</h2>
		</div>
	</div>
	<div class="pb10"></div>
	<!-- End Heading -->
	<div class="col-xs-12 pl5 pr5">
	<fieldset>
		<div class="col-xs-12 mt10">
		<div class="col-xs-12">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_opendate') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-8 text-left clr_blue">
				{{ Form::text('OpenDate',(isset($staffview[0]->DOJ)) ? $staffview[0]->DOJ : '',array('id'=>'OpenDate', 
													'name' => 'OpenDate',
													'data-label' => trans('messages.lbl_opendate'),
													'class'=>'box14per form-control opd ime_mode_disable')) }}
				<label class="mt10 ml2 fa fa-calendar fa-lg" for="OpenDate" aria-hidden="true"></label>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_surname') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-8 text-left clr_blue">
				{{ Form::text('Surname',(isset($staffview[0]->FirstName)) ? $staffview[0]->FirstName : '',array('id'=>'Surname', 
													'name' => 'Surname',
													'data-label' => trans('Sur Name'),
													'class'=>'box30per form-control')) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_engjpname') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-8 text-left clr_blue">
				{{ Form::text('Name',(isset($staffview[0]->LastName)) ? $staffview[0]->LastName : '',array('id'=>'Name', 
												'name' => 'Name',
												'data-label' => trans('messages.lbl_name'),
												'class'=>'box30per form-control')) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_nickname') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-8 text-left clr_blue">
				{{ Form::text('NinkName',(isset($staffview[0]->nickname)) ? $staffview[0]->nickname : '',array('id'=>'NinkName', 
													'name' => 'NinkName',
													'data-label' => trans('messages.lbl_ninkname'),
													'class'=>'box30per form-control')) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_kanaFirstName') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-8 text-left clr_blue">
				{{ Form::text('KanaFirstName',(isset($staffview[0]->KanaFirstName)) ? $staffview[0]->KanaFirstName : '',array('id'=>'KanaFirstName', 
													'name' => 'KanaFirstName',
													'data-label' => trans('messages.lbl_kanaFirstName'),
													'class'=>'box30per form-control')) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_kanaLastName') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-8 text-left clr_blue">
				{{ Form::text('KanaLastName',(isset($staffview[0]->KanaLastName)) ? $staffview[0]->KanaLastName : '',array('id'=>'KanaLastName', 
													'name' => 'KanaLastName',
													'data-label' => trans('messages.lbl_kanaLastName'),
													'class'=>'box30per form-control')) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-4 text-right clr_blue mb10">
				<label>{{ trans('messages.lbl_gender') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-md-8 text-left">
			<div class="col-md-6 pm0">
				<label style="font-weight: normal;">
					{{ Form::radio('Gender', '1',(isset($staffview[0]->Gender) && ($staffview[0]->Gender)=="1") ? $staffview[0]->Gender : '',
									array('id' =>'Gender1',
									  'name' => 'Gender',
									  'class' => 'comp',
									  'style' => 'margin:-2px 0 0 !important',
									  'data-label' => trans('messages.lbl_Gender'))) }}
					<span class="vam">{{ trans('messages.lbl_male') }}</span>
				</label>
				<label style="font-weight: normal;">
					{{ Form::radio('Gender', '2',(isset($staffview[0]->Gender) && ($staffview[0]->Gender)=="2") ? $staffview[0]->Gender : '',
									array('id' =>'Gender2',
									  'name' => 'Gender',
									  'class' => 'ntcomp',
									  'style' => 'margin:-2px 0 0 !important',
									  'data-label' => trans('messages.lbl_Gender'))) }}
				<span class="vam">{{ trans('messages.lbl_female') }}</span>
				</label>
			</div>
			</div>
		</div>
		<div class="col-xs-12" style="margin-top: -5px;">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_dob') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-8 text-left clr_blue">
				{{ Form::text('DateofBirth',(isset($staffview[0]->DOB)) ? $staffview[0]->DOB : '',array('id'=>'DateofBirth', 
														'name' => 'DateofBirth',
														'data-label' => trans('messages.lbl_dob'),
														'class'=>'box14per form-control dob ime_mode_disable')) }}
				<label class="mt10 ml4 fa fa-calendar fa-lg" for="DateofBirth" aria-hidden="true"></label>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-4 text-right clr_blue">
				<label class="mr10">{{ trans('messages.lbl_mobilenumber') }}</label>
			</div>
			<div class="col-xs-8 text-left clr_blue">
				{{ Form::text('MobileNo',(isset($staffview[0]->Mobile1)) ? $staffview[0]->Mobile1 : '',array('id'=>'MobileNo', 
													'name' => 'MobileNo',
													'maxlength' => '13',
													'data-label' => trans('messages.lbl_mobilenumber'),
													'class'=>'box17per ntcomp form-control ime_mode_disable',
													'data-label' => trans('messages.lbl_mobilenumber'),
													'onkeypress' => 'return isNumberKeywithminus(event)')) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-4 text-right clr_blue">
				<label class="mr10">{{ trans('messages.lbl_email') }}</label>
			</div>
			<div class="col-xs-8 text-left clr_blue">
				{{ Form::text('Email',(isset($staffview[0]->Emailpersonal)) ? $staffview[0]->Emailpersonal : '',array('id'=>'Email', 
												'name' => 'Email',
												'email' => 'email',
												'data-label' => trans('messages.lbl_email'),
												'class'=>'box32per ntcomp form-control ime_mode_disable')) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-4 text-right clr_blue">
				<label class="mr10">{{ trans('messages.lbl_image') }}</label>
			</div>
			<div class="col-xs-4 text-left">
				{{ Form::file('picture',array('id'=>'picture',
										'name' => 'picture')) }}
			</div>	
			<div class="box3per CMN_display_block" style="display: inline-block!important;">
				@if(isset($staffview[0]->Picture))
					<?php $file_url = '../resources/assets/images/upload/' . $staffview[0]->Picture;?>
					@if($staffview[0]->Picture != "")
						<a style="text-decoration:none" href="{{ $file_url }}" data-lightbox="visa-img">
						<img class="pull-left box50per" src="{{ $file_url }}" width="10" name="empimg" id="empimg" height = "10">
						</img>
						</a>
						{{ Form::hidden('pdffiles',  $staffview[0]->Picture , array('id' => 'pdffiles')) }}
					@endif
				@endif	
			</div>
			<div class="CMN_display_block mb5">{{trans('messages.lbl_fileonly')}}</div>
		</div>
		@if(isset($staffview[0]->Address1))
			<?php
				$address = "";
				if (!empty($staffview[0]->Address1)) {
					if (is_numeric($staffview[0]->Address1)) {
						if(isset($staffview[0]->pincode) || isset($staffview[0]->jpstate) || isset($staffview[0]->jpaddress)) {
							$address = '〒'.$staffview[0]->pincode.$staffview[0]->jpstate.$staffview[0]->jpaddress.$staffview[0]->roomno.'号';
						} else {
							$address = $staffview[0]->Address1;
						}
					} else {
						$address = $staffview[0]->Address1;
					}
				}
			?>
		<div class="col-xs-12 mt10 mb10">
		@else
		<?php $address = ""; ?>
		<div class="col-xs-12 mb10 mt7">
		@endif
			<div class="col-xs-4 text-right clr_blue">
				<label class="mr10">{{ trans('messages.lbl_streetaddress') }}</label>
			</div>
			<div class="col-xs-8 text-left">
				{{ Form::textarea('StreetAddress',$address,array('id'=>'StreetAddress', 
												'name' => 'StreetAddress',
												'data-label' => trans('messages.lbl_streetaddress'),
												'style' => 'height:130px;')) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_bank_name') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-8 text-left clr_blue">
				{{ Form::text('BankName',(isset($staffview[0]->BankName)) ? $staffview[0]->BankName : '',array('id'=>'BankName', 
													'name' => 'BankName',
													'data-label' => trans('messages.lbl_bank_name'),
													'class'=>'box50per form-control')) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_branch_name') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-8 text-left clr_blue">
				{{ Form::text('BranchName',(isset($staffview[0]->BranchName)) ? $staffview[0]->BranchName : '',array('id'=>'BranchName', 
													'name' => 'BranchName',
													'data-label' => trans('messages.lbl_branch_name'),
													'class'=>'box30per form-control')) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_account_no') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-8 text-left clr_blue">
				{{ Form::text('AccountNo',(isset($staffview[0]->AccNo)) ? $staffview[0]->AccNo : '',array('id'=>'AccountNo', 
													'name' => 'AccountNo',
													'data-label' => trans('messages.lbl_account_no'),
													'class'=>'box25per form-control')) }}
			</div>
		</div>
		<div class="col-xs-12 mt5 mb10">
			<div class="col-xs-4 text-right clr_blue">
				<label>{{ trans('messages.lbl_branch_number') }}<span class="fr ml2 red"> * </span></label>
			</div>
			<div class="col-xs-8 text-left clr_blue">
				{{ Form::text('BranchNo',(isset($staffview[0]->BranchNo)) ? $staffview[0]->BranchNo : '',array('id'=>'BranchNo', 
													'name' => 'BranchNo',
													'data-label' => trans('messages.lbl_branch_number'),
													'class'=>'box15per form-control')) }}
			</div>
		</div>
	</fieldset>
	</div>
		<fieldset style="background-color: #DDF1FA;">
		<div class="form-group mt15">
			<div align="center" class="mb15">
				@if($request->editflg =="edit")
					<button type="submit" class="btn edit btn-warning box100 nonstfaddeditprocess" >
						<i class="fa fa-edit" aria-hidden="true"></i> {{ trans('messages.lbl_update') }}
					</button>
					<a onclick="javascript:gotoidxpage('1','{{ $request->mainmenu }}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
					</a>
				@else
					<button type="submit" class="btn btn-success add box100 nonstfaddeditprocess" >
						<i class="fa fa-plus"></i> {{ trans('messages.lbl_register') }}
					</button>
					<a onclick="javascript:gotoidxpage('2','{{ $request->mainmenu }}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
					</a>	
				@endif
			</div>
		</div>
	</fieldset>
	{{ Form::close() }}
	{{ Form::open(array('name'=>'frmnonstaffaddeditcancel', 'id'=>'frmnonstaffaddeditcancel', 'url' => 'NonStaff/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
	{{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
	{{ Form::hidden('viewid', $request->viewid, array('id' => 'viewid')) }}
	{{ Form::close() }}
<div class="CMN_display_block pb10"></div>	
</article>
</div>	
@endsection