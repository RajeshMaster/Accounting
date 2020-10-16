@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/staff.js') }}
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
/*.growIconImg {
	display: block;
	max-width:18px;
	max-height:25px;
	width: auto;
	height: auto;
	transition: all .2s ease-in-out; 
}
.growIconImg:hover { 
	display: block;
	width: auto;
	height: auto;
	transform: scale(22.1); 
}*/
</style>
<?php use App\Http\Helpers; ?>
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="staff" class="DEC_flex_wrapper " data-category="staff staff_sub_1">
	{{ Form::open(array('name'=>'frmstaffaddedit','id'=>'frmstaffaddedit', 'url' => 'Staff/staffaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
	{{ Form::hidden('viewid', $request->viewid , array('id' => 'viewid')) }}
	{{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
	{{ Form::hidden('hdnempid', (isset($staffview[0]->Emp_ID)) ? $staffview[0]->Emp_ID : '', array('id' => 'hdnempid')) }}
	{{ Form::hidden('pictureId', (isset($staffview[0]->Picture)) ? $staffview[0]->Picture : '', array('id' => 'pictureId')) }}
	{{ Form::hidden('DOB', $dob_year, array('id' => 'DOB')) }}
	<!-- Start Heading -->
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/staffList.png') }}">
			<h2 class="pull-left pl5 mt10">{{ trans('messages.lbl_staff') }}</h2>
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
			<div class="col-xs-4 text-right clr_blue mb10">
				@if(isset($staffview[0]->Emp_ID))
					<label>{{ trans('messages.lbl_employeeid') }}<span class="fr ml10 red"></span></label>
				@else
					<label>{{ trans('messages.lbl_employeeid') }}<span class="fr ml2 red"> * </span></label>
				@endif
			</div>
			<div class="col-xs-8 text-left ">
			@if($request->editflg!="edit")
			<label style="font-weight: normal;">
				{{ Form::text('EmployeeId',(isset($staffview[0]->Emp_ID)) ? $staffview[0]->Emp_ID : '',array('id'=>'EmployeeId', 
													'name' => 'EmployeeId',
													'data-label' => trans('messages.lbl_employeeid'),
													'class'=>'box45per form-control ime_mode_disable')) }}
			</label>
			@else
			<label class="fwb blue">{{ $request->viewid }}</label>
			@endif
			</div>
		</div>
		@if(isset($staffview[0]->DOJ))
		<div class="col-xs-12" style="margin-top: -5px;">
		@else
		<div class="col-xs-12">
		@endif
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
			<div class="col-xs-4 text-right clr_blue mb10">
				<label>{{ trans('messages.lbl_gender') }}<span class="fr ml2 red"> * </span></label>
			</div>
            <div class="col-md-8 text-left">
            	@if(isset($staffview[0]->Gender) == 1)
            		{{--*/ $checkM = "true";
            			   $checkF = ""; /*--}}
            	@else(isset($staffview[0]->Gender) == 2)
            		{{--*/ $checkF = "true";
            			   $checkM = ""; /*--}}
            	@endif
            <div class="col-md-6 pm0">
				<label style="font-weight: normal;">
					{{ Form::radio('Gender', '1',(isset($staffview[0]->Gender) && ($staffview[0]->Gender)=="1") ? $staffview[0]->Gender : '', 
								array('id' =>'Gender1',
									  'name' => 'Gender',
									  'class' => 'comp',
									  'style' => 'margin:-2px 0 0 !important',
									  'data-label' => trans('messages.lbl_Gender'))) }}
					<span class="vam">&nbsp;{{ trans('messages.lbl_male') }}&nbsp;</span>
				</label>
				<label style="font-weight: normal;">
					{{ Form::radio('Gender', '2',(isset($staffview[0]->Gender) && ($staffview[0]->Gender)=="2") ? $staffview[0]->Gender : '', 
								array('id' =>'Gender2',
									  'name' => 'Gender',
									  'class' => 'ntcomp',
									  'style' => 'margin:-2px 0 0 !important',
									  'data-label' => trans('messages.lbl_Gender'))) }}
				<span class="vam">&nbsp;{{ trans('messages.lbl_female') }}&nbsp;</span>
				</label>
			</div>
               <!--  <label style="font-weight: normal;">
                {{ Form::radio('Gender', '1', $checkM, array('id' =>'Gender1',
                									'name' => 'Gender')) }}
                <span class="vam">&nbsp;{{ trans('messages.lbl_male') }}&nbsp;</span>
            	</label>
            	<label style="font-weight: normal;">
                {{ Form::radio('Gender', '2', $checkF, array('id'=>'Gender2',
                											'name' => 'Gender')) }}
                <span class="vam">&nbsp;{{ trans('messages.lbl_female') }}&nbsp;</span>
            	</label> -->
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
		<!-- <div class="col-xs-12">
			<div class="col-xs-4 text-right clr_blue">
				<label class="mr10">{{ trans('messages.lbl_image') }}</label>
			</div>
			<div class="col-xs-4 text-left clr_blue"  style="border: 1px solid red;">
				@if(isset($staffview[0]->Picture))
				<div class="box45per CMN_display_block" style="display: inline-block!important;height:15px; ">
				{{ Form::file('picture',array('id'=>'picture', 
												'name' => 'picture',
												'data-label' => $staffview[0]->Picture)) }}
				</div>
				<div class="box10per CMN_display_block ml100" style="display: inline-block!important;border: 1px solid red;">
					{{--*/ $src = $filepath . $staffview[0]->Picture; /*--}}
					<img class="pull-left box50per  growIconImg" src="{{ $src }}" width="10" height = "10">
					</img>
				</div>
				@else
				{{ Form::file('picture',(isset($staffview[0]->Picture)) ? $staffview[0]->Picture : '',array('id'=>'picture', 
												'name' => 'picture',
												'data-label' => trans('messages.lbl_image'),
												'class'=>'box30per ntcomp form-control')) }}
				@endif
			</div>
			<div class="col-xs-3 tal">
				<span>(Ex: Image File Only）</span>
			</div>
		</div> -->
		<div class="col-xs-12 mt5">
			<div class="col-xs-4 text-right clr_blue">
				<label class="mr10">{{ trans('messages.lbl_image') }}</label>
			</div>
			<div class="col-xs-4 text-left">
				@if(isset($staffview[0]->Picture))
					{{ Form::file('picture',array('id'=>'picture', 
												'name' => 'picture',
												'data-label' => $staffview[0]->Picture)) }}
					
				@else
				{{ Form::file('picture',(isset($staffview[0]->Picture)) ? $staffview[0]->Picture : '',array('id'=>'picture', 
												'name' => 'picture',
												'data-label' => trans('messages.lbl_image'))) }}
				@endif
			</div>	
			<div class="box3per CMN_display_block" style="display: inline-block!important;">
				@if(isset($staffview[0]->Picture))
					{{--*/ $src = $filepath . $staffview[0]->Picture; /*--}}
					<a style="text-decoration:none" href="{{ $src }}" data-lightbox="visa-img">
					<img class="pull-left box50per" src="{{ $src }}" width="10" name="empimg" id="empimg" height = "10">
					</img>
					</a>
				@else
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
					<button type="submit" class="btn edit btn-warning box100 addeditprocess">
						<i class="fa fa-edit" aria-hidden="true"></i> {{ trans('messages.lbl_update') }}
					</button>
					<a onclick="javascript:gotoindexpage('1','{{ $request->mainmenu }}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
					</a>
				@else
					<button type="submit" class="btn btn-success add box100 addeditprocess" >
						<i class="fa fa-plus"></i> {{ trans('messages.lbl_register') }}
					</button>
					<a onclick="javascript:gotoindexpage('2','{{ $request->mainmenu }}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				@endif
				</a>
			</div>
		</div>
	</fieldset>
</div>
	{{ Form::close() }}
	{{ Form::open(array('name'=>'frmstaffaddeditcancel', 'id'=>'frmstaffaddeditcancel', 'url' => 'Staff/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
	{{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
	{{ Form::hidden('viewid', $request->viewid, array('id' => 'viewid')) }}
	{{ Form::close() }}
</div>
<div class="CMN_display_block pb10"></div>
</article>
</div>
@endsection