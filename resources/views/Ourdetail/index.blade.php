@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/ourdetail.js') }}
{{ HTML::script('resources/assets/js/common.js') }}
{{ HTML::style('resources/assets/css/common.css') }}
{{ HTML::style('resources/assets/css/widthbox.css') }}
{{ HTML::script('resources/assets/css/bootstrap.min.css') }}
<?php use App\Http\Helpers; ?>
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
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
<article id="ourdetails" class="DEC_flex_wrapper " data-category="ourdetails our_details_sub_1">
	{{ Form::open(array('name'=>'frmourdetailindex', 'id'=>'frmourdetailindex', 'url' => 'Ourdetail/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('editflg', '', array('id' => 'editflg')) }}
		{{ Form::hidden('id', '', array('id' => 'id')) }}
		{{ Form::hidden('balid', '', array('id' => 'balid')) }}
		{{ Form::hidden('editid', '' , array('id' => 'editid')) }}
	<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-8 pl5">
			<img class="pull-left box35 mt15" src="{{ URL::asset('resources/assets/images/ourdetails.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_ourdetails') }}</h2>
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
		<!-- javascript:edit('{{ $result[0]->id }}'); -->
		@if($result != "")
			<a href="javascript:edit('{{$result[0]->id}}','{{$request->mainmenu}}');" class="btn btn-warning box100"><span class="fa fa-pencil"></span> {{ trans('messages.lbl_edit') }}</a>
		@else
			<a href="javascript:register();" class="btn btn-success box100"><span class="glyphicon glyphicon-plus"></span> {{ trans('messages.lbl_register') }}</a>
		@endif
		@if(!empty($kessan) && isset($kessan))
			<a href="javascript:balsheetpopupenable('{{$kessan[0]->id}}','{{ $request->mainmenu }}');" class="pull-right pr10 mt15 anchorstyle"><span class="fa fa-balance-scale"></span>{{ trans('messages.lbl_balance_sheet_setting') }}</a>
		@else
			<a href="javascript:balsheetpopupenable('','{{ $request->mainmenu }}');" class="pull-right pr10 mt15 anchorstyle"><span class="fa fa-balance-scale"></span>{{ trans('messages.lbl_balance_sheet_setting') }}</a>
		@endif
		<a href="javascript:taxpopupenable('{{ $request->mainmenu }}');" id="" class="pull-right pr10 mt15 anchorstyle"><span class="fa fa-percent"></span>{{ trans('messages.lbl_tax_setting') }}</a>
	</div>
	<div class="col-xs-12 pl5 pr5">
	<fieldset>
		<div class="col-xs-12 mt15">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_companyname') }}</label>
			</div>
			<div>
				{{ $result[0]->CompanyName }}
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_companynamekana') }}</label>
			</div>
			<div>
				{{ $result[0]->CompanyNamekana }}
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_postalservice') }}</label>
			</div>
			<div>
				{{ $result[0]->pincode }}
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_perfecturename') }}</label>
			</div>
			<div>
				{{ $result[0]->Prefecturename }}
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_address') }}</label>
			</div>
			<div>
				{{ $result[0]->Streetaddress }}
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_buildingname') }}</label>
			</div>
			<div>
				{{ $result[0]->BuildingName }}
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_tel') }}</label>
			</div>
			<div>
				{{ $result[0]->TEL }}
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_fax') }}</label>
			</div>
			<div>
				{{ $result[0]->FAX }}
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_commonmail') }}</label>
			</div>
			<div>
				{{ $result[0]->Commonmail }}
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_url') }}</label>
			</div>
			<div>
				{{ $result[0]->URL }}
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_establisheddate') }}</label>
			</div>
			<div>
				{{ $result[0]->Establisheddate }}
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_closingdate') }}</label>
			</div>
			<div>
				{{ $result[0]->Closingmonth }} Mn
				{{ $result[0]->Closingdate }} Day
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_accountperiod') }}</label>
			</div>
			<div>
				@if(isset($kessan[0]->Accountperiod))
					{{ $kessan[0]->Accountperiod }} Day
				@else
					NIL
				@endif
			</div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_period') }}</label>
			</div>
			<div>
				@if(isset($kessan[0]->Startingyear))
					{{ $kessan[0]->Startingyear }} .
					{{ $kessan[0]->Startingmonth }}&nbsp;&nbsp;~&nbsp;&nbsp;
					{{ $kessan[0]->Closingyear }} .
					{{ $kessan[0]->Closingmonth }}
				@else
					NIL
				@endif
			</div>
		</div>
		<div class="col-xs-12 mb15">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_systemname') }}</label>
			</div>
			<div>
				{{ $result[0]->systemname }}
			</div>
		</div>
	</fieldset>
	</div>
	<div class="row hline">
		<div class="col-xs-8 pl5">
			<h2 class="pull-left pl5 mt1">{{ trans('messages.lbl_Tax_added') }}</h2>
		</div>
	</div>
	<div class="pb20"></div>
	<div class="box70per">
		<table class="col-xs-6 CMN_tblfixed tablealternate width52">
		<colgroup>
			<col width="5%"/>
			<col width="8%"/>
			<col width="10%"/>
			<col width=""/>
			<col width="10%"/>
		</colgroup>
			<tr>
				<th class="tac">{{ trans('messages.lbl_sno') }}</th>
				<th class="tac">{{ trans('messages.lbl_Tax_per') }}</th>
				<th class="tac">{{ trans('messages.lbl_Start_date') }}</th>
				<th class="tac">{{ trans('messages.lbl_Created_By') }}</th>
				<th class="tac">{{ trans('messages.lbl_Date') }}</th>
			</tr>
		</table>
	</div>
	<div class="pull-left" style="width: 71.45%">
		<div class="scrolldesign width140" >
		<table class="btnone CMN_tblfixed tablealternate" width="630px">
			<colgroup>
				<col width="5%"/>
				<col width="8%"/>
				<col width="10%"/>
				<col width=""/>
				<col width="10%"/>
			</colgroup>
			@php $i=1; @endphp
			@forelse($viewtaxdetails as $key=>$user)
				<tr>
					<td class="tac">{{ $i++ }}</td>
					<td class="tac">{{ $user->Tax }}</td>
					<td class="tac">{{ $user->Startdate }}</td>
					<td class="tal">{{ $user->CreatedBy }}</td>
					<td class="tac">{{ $user->Ins_DT }}</td>
				</tr>
			@empty
				<tr>
					<td colspan="5">
						{{ trans('messages.lbl_nodatafound') }}
					</td>
				</tr>
			@endforelse
		</table>
		</div>
	</div>
	{{ Form::close() }}
	<div id="taxpopup" class="modal fade">
        <div id="login-overlay">
            <div class="modal-content">
                <!-- Popup will be loaded here -->
            </div>
        </div>
    </div>
    <div id="balancesheetpopup" class="modal fade" style="width: 750px;">
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