@extends('layouts.app')
@section('content')
@inject('eradate', 'App\Http\eradate')
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
{{ HTML::script('resources/assets/js/tax.js') }}
<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="master" class="DEC_flex_wrapper " data-category="staff staff_sub_7">
		{{ Form::open(array('name'=>'taxviewform',
							'id'=>'taxviewform',
							'url'=>'Tax/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
							'files'=>true,
							'method' => 'POST' )) }}
			{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
			{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
			{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
			{{ Form::hidden('empid', $tax_result[0]->Emp_ID , array('id' => 'empid')) }}
		    {{ Form::hidden('removed', '' , array('id' => 'removed')) }}
		    {{ Form::hidden('selected', '' , array('id' => 'selected')) }}
		    {{ Form::hidden('hdnflg', '' , array('id' => 'hdnflg')) }}
	<!-- Start Heading -->
	<div class="row hline">
	<div class="col-xs-12 pm0 ml10">
			<img class="pull-left box35 mt15" src="{{ URL::asset('resources/assets/images/taxdetails.png') }}">
			<h2 class="pull-left pl5 mt15">
				{{ trans('messages.lbl_taxview') }}
			</h2>
		</div>
	</div>
	<div class="pb0"></div>
	<!-- End Heading -->
	<!-- Session msg -->
	@if(Session::has('success'))
		<div align="center" class="alertboxalign mt5" role="alert">
			<p class="alert {{ Session::get('alert', Session::get('type') ) }}">
            {{ Session::get('success') }}
          	</p>
		</div>
	@endif
	@php Session::forget('success'); @endphp
	<!-- Session msg -->
	<div class="pl5 mt10 pr5">
		<div class="pull-left ml10">
			<a href="javascript:goindexpage('{{ $request->mainmenu }}');" class="pageload btn btn-info box80"><span class="fa fa-arrow-left"></span> {{ trans('messages.lbl_back') }}</a>
		</div>
	</div>
	<div class="col-xs-12 pl5 pr5 ml10">
	<fieldset>
		<div class="col-xs-12 mt15">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_empid') }}</label>
			</div>
			<div>
				{{ $tax_result[0]->Emp_ID }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_empName') }}</label>
			</div>
			<div>
				{{ strtoupper($tax_result[0]->FirstName." ".$tax_result[0]->LastName) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_kananame') }}</label>
			</div>
			<div>
				@if($tax_result[0]->KanaFirstName !="" && $tax_result[0]->KanaLastName !="")
					{{ $tax_result[0]->KanaFirstName." ".$tax_result[0]->KanaLastName }}
				@else
                  	{{ "NIL" }}
				@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_address') }}</label>
			</div>
			<div class="col-xs-9 pm0">
				@if($tax_result[0]->pincode != "" || $tax_result[0]->jpaddress != "" ||  $tax_result[0]->jpbuildingname != ""
                 || $tax_result[0]->roomno != "")
	                <span>{{ ucwords("〒".$tax_result[0]->pincode) }}</span><br/>
	                <span>{{ ucwords($tax_result[0]->jpaddress) }}</span><br/>
	                <span>{{ ucwords($tax_result[0]->jpbuildingname." - ".$tax_result[0]->roomno) }}</span>
                @else
                  	{{ "NIL" }}
                @endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_gender') }}</label>
			</div>
			<div>
				@if($tax_result[0]->Gender == 1)
                	{{ trans('messages.lbl_male') }}
              	@else
                	{{ trans('messages.lbl_female') }}
              	@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_dob') }}</label>
			</div>
			<div>
				{{ $tax_result[0]->DOB }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_country') }}</label>
			</div>
			<div>
				@if($tax_result[0]->citizenShip == 1)
                	{{ strtoupper(trans('messages.lbl_india')) }}
              	@else
                	{{ strtoupper(trans('messages.lbl_japan')) }}
              	@endif
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_vstatus') }}</label>
			</div>
			<div>
				@if($tax_result[0]->visaStatus != "")
                	{{ $tax_result[0]->JapNM }}
              	@else
                	{{ "NIL" }}
              	@endif
			</div>
		</div>
	</fieldset>
	</div>
	@if($family_list != 0 && count($family_list) > 0)
	<div class="mr10 ml15 mt10">
		<div class="minh400">
			<table class="tablealternate box100per">
				<colgroup>
					<col width="4%">
			        <col width="10%">
			        <col>
			        <col>
			        <col width="4%">
			        <col width="7%">
			        <col width="4%">
			        <col width="10%">
			        <col width="15%">
			        <col width="3%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
					<tr class="tableheader fwb tac">
						<th class="tac">{{ trans('messages.lbl_sno') }}</th>
						<th class="tac">{{ "健康国民保険" }}</th>
						<th class="tac">{{ trans('messages.lbl_name') }}</th> 
						<th class="tac">{{ trans('messages.lbl_kananame') }}</th> 
						<th colspan="3" class="tac">{{ trans('messages.lbl_dob') }}</th> 
						<th class="tac">{{ trans('messages.lbl_relationship') }}</th> 
						<th class="tac">{{ trans('messages.lbl_remarks') }}</th> 
						<th style="vertical-align: middle;" class="">{{ Form::checkbox('checkedAll',null, 0, 
                        ['class' => 'field source checkedAll', 'id' => 'checkedAll']) }}</th> 
					</tr>
				</thead>
				<tbody>
		      @if(count($family_list) > 0)
		        @for ($i=0; $i < count($family_list); $i++)
		          <tr class="fromHere">
		            <td class="text-center" style="vertical-align: middle;">
		              {{ $i + 1 }}
		            </td>
		            <td class="text-center">
		            </td>
		            <td style="vertical-align: middle;"  @if(strlen($family_list[$i][0]) > 30))
		              title="{{ singlefieldlength($family_list[$i][0], 120) }}" @endif>
		              {{ singlefieldlength($family_list[$i][0],30) }}
		            </td>
		            <td style="vertical-align: middle;">
		              {{ $family_list[$i][3] }}
		            </td>
		            <td class="text-center" style="vertical-align: middle;">
		              @if(isset($family_list[$i][2]) && $family_list[$i][2] != "0000-00-00")
		                @php 
		                  $empjapancalender = $eradate->geteradate($family_list[$i][2], 6);
		                  $empdobdate = explode('/', $empjapancalender);
		                @endphp
		                {{ $empdobdate[4] }}
		              @else
		                -
		              @endif
		            </td>
		            <td class="text-center" style="vertical-align: middle;">
		              @if($family_list[$i][2] != "0000-00-00" && isset($family_list[$i][2]))
		                {{ substr($empdobdate[0], 1,2)."-".$empdobdate[1]."-".$empdobdate[2] }}
		              @else
		                {{ "-" }}
		              @endif
		            </td>
		            <td class="text-center" style="vertical-align: middle;">
		              @if(isset($family_list[$i][2]) && $family_list[$i][2] != "0000-00-00")
		                @php
		                  $end = Carbon\Carbon::parse($family_list[$i][2]);
		                  $now = Carbon\Carbon::now();
		                  $length = $end->diffInYears($now);
		                @endphp
		                {{ $length }}
		              @else
		                -
		              @endif
		            </td>
		            <td style="vertical-align: middle;">
		              @if(Session::get('languageval') == "en")
		                {{ $family_list[$i][4] }}
		              @else
		                {{ $Relation_kanji[$family_list[$i][4]] }}
		              @endif
		            </td>
		            <td>
		            </td>
		            <td style="vertical-align: middle;">
		              {{--*/ $includeflg = $family_list[$i][6];  /*--}}
		              @if($family_list[$i][5] == 1)
		                    {{ Form::checkbox($includeflg,$includeflg, 1, 
		                        ['class' => 'field ml5 source checkSingle', 'id' => $includeflg]) }}
		              @else
		                    {{ Form::checkbox($includeflg,$includeflg, 0, 
		                        ['class' => 'field ml5 source checkSingle', 'id' => $includeflg]) }}
		              @endif
		            </td>
		          </tr>
		          @endfor
		        @else
		          <tr>
		            <td class="text-center fr" colspan="9">
		              {{ trans('messages.lbl_nodatafound') }}
		            </td>
		        </tr>
		        @endif
		      </tbody>
			</table>
			<a href="javascript:fngetid('$request->mainmenu');" id="family_check" class="btn btn-success pull-right mr10 mt10 mb20"><span class="glyphicon glyphicon-plus"></span> {{ trans('messages.lbl_family_register') }}</a>
		</div>
	</div>
	@endif
{{ Form::close() }}
	</article>
</div>
@endsection