@extends('layouts.app')
@section('content')
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
<style type="text/css">
	.imgheight{
		height: 23px;
		width: 25px;
	}
</style>
{{ HTML::script('resources/assets/js/tax.js') }}
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="master" class="DEC_flex_wrapper " data-category="staff staff_sub_7">
		{{ Form::open(array('name'=>'taxdetailsform',
							'id'=>'taxdetailsform',
							'url'=>'Tax/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
							'files'=>true,
							'method' => 'POST' )) }}
			{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
			{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
			{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
			{{ Form::hidden('empid', '' , array('id' => 'empid')) }}
        	{{ Form::hidden('empname', '' , array('id' => 'empname')) }}
        	{{ Form::hidden('checkflg', '' , array('id' => 'checkflg')) }}
        	{{ Form::hidden('excelflg', '' , array('id' => 'excelflg')) }}
        	{{ Form::hidden('afterexcel', URL::asset('resources/assets/images/excel_ia.png'),
        										array('id' => 'afterexcel')) }}
		<!-- Start Heading -->
		<div class="row hline">
		<div class="col-xs-12 pm0 ml10">
				<img class="pull-left box35 mt15" src="{{ URL::asset('resources/assets/images/taxdetails.png') }}">
				<h2 class="pull-left pl5 mt15">
					{{ trans('messages.lbl_taxdetails') }}
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
		<!-- Start Import Link -->
		<div class="col-xs-12 pm0 mt10 pull-left">
	        <div class="col-xs-12 pm0 mr10">
	        	<a href="javascript:showempselectionpopup();"><img class="box22 ml8 mr7 pull-left mb5" 
	      							src="{{ URL::asset('resources/assets/images/edit.png') }}">
	        	</a><a class="pull-left" 
          				href="javascript:showempselectionpopup();">
          					{{ trans('messages.lbl_cempsel') }}
          		</a>
				<div class="form-group pm0 pull-right moveleft mr10" id="moveleft">
	      			<a href="javascript:fnTaxDetailsImport('{{ $request->mainmenu }}');" 
	      				style="color:blue;" 
	      				class="pb15 box30 anchorstyle">
	      					<img class="box22 mr7 mb5" 
	      							src="{{ URL::asset('resources/assets/images/import.png') }}">{{ trans('messages.lbl_visaimport') }}
	      			</a>
	      		</div>
	      	</div>
	    </div> 
	    <!-- End Import Link -->
	    <div class="mr10 ml10 mt10">
		<div class="minh400">
			<table class="tablealternate box100per">
				<colgroup>
					<col width="4%">
					<col width="7%">
					<col width="">
					<col width="25%">
					<col width="7%">
					<col width="3%">
					<col width="8%">
					<col width="6%">
					<col width="12%">
					<col width="6%">
					<col width="5%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
					<tr class="tableheader fwb tac">
						<th class="tac">{{ trans('messages.lbl_sno') }}</th>
						<th class="tac">{{ trans('messages.lbl_empid') }}</th>
						<th class="tac">{{ trans('messages.lbl_empName') }}</th> 
						<th class="tac">{{ trans('messages.lbl_address') }}</th> 
						<th class="tac">{{ trans('messages.lbl_gender') }}</th> 
						<th colspan="2" rowspan="2" class="tac">{{ trans('messages.lbl_dob') }}</th> 
						<th class="tac">{{ trans('messages.lbl_country') }}</th> 
						<th class="tac">{{ trans('messages.lbl_vstatus') }}</th> 
						<th class="tac">{{ trans('messages.lbl_depends') }}</th> 
						<th class="tac"></th> 
					</tr>
				</thead>
				<tbody>
					@forelse($employeeDetails as $count => $data)
					{{--*/ $row = ($employeeDetails->currentpage()-1) * $employeeDetails->perpage() + $count + 1 /*--}}
						<tr>
							<td class="tac">
								{{ ($employeeDetails->currentpage()-1) * $employeeDetails->perpage() + $count + 1 }}
							</td>
							<td class="tac">
								<!-- fngovisaview('{{ $data->Emp_ID }}') -->
								<a class="anchorstyle" href="javascript:fnViewPage('{{ $data->Emp_ID }}')">{{ ($data->Emp_ID !="")?$data->Emp_ID:$data->empuserid}}</a>
							</td>
							<td>
								{{ $data->FirstName }}
					            {{ $data->LastName }}<br>
					            {{ $data->KanaFirstName }}
					            {{ $data->KanaLastName }}
							</td>
							<td  class="tal breakword" style="vertical-align: middle;">
					              @if ($data->pincode == "" && $data->jpaddress == "" && 
					                    $data->jpbuildingname == "" && $data->roomno == "")
					                  {{ "NIL" }}
					              @else
					                  {{ "〒".$data->pincode }}<br/>
					                  {{ $data->jpaddress }}<br/>
					                  {{ $data->jpbuildingname." - ".$data->roomno }}
					              @endif
					        </td>
					        <td class="tac">
					        	@if($data->Gender == 1)
						        	{{ trans('messages.lbl_male') }}
						        @elseif($data->Gender == 2)
						        	{{ trans('messages.lbl_female') }}
						        @endif
					        </td>
					        <td class="tac">
					        	{{ $get_detail[$count]['DOB'] }}
					        </td>
					        <td class="tac">
					        	{{ $get_detail[$count]['date'] }}
					        </td>
					        <td class="tac">
					        	{{ $get_detail[$count]['citizenShip'] }}
					        </td>
					        <td class="">
					        	{{ $get_detail[$count]['visaStatus'] }}
					        </td>
							<td class="tac">
								@if($get_detail[$count]['dep_cou'] != "")
					               {{ $get_detail[$count]['dep_cou']."/".$get_detail[$count]['Relation_count'] }}
					            @else
					              {{ "-" }}
					            @endif
							</td>
							<td class="tac">
								<a href = "javascript:fnTaxExcelDownload('{{ $data->Emp_ID }}','{{ $data->LastName }}','{{ $row }}','{{ $data->Emp_ID."excelFlg" }}','{{ $data->excelFlg }}');" >
								@if($data->excelFlg==0)
								<img class="box18 mr7 mb2 mt2 csrp imgheight" 
									name="<?php echo $data->Emp_ID."excelFlg"?>" 
                        			id="<?php echo $data->Emp_ID."excelFlg"?>" 
									title="{{ trans('messages.lbl_taxdownload') }}"
									src="{{ URL::asset('resources/assets/images/excel_a.png') }}">
								@else
								<img class="box18 mr7 mb2 mt2 csrp imgheight" name="excelFlg" 
                        			id="excelFlg"
									title="{{ trans('messages.lbl_taxdownload') }}"
									src="{{ URL::asset('resources/assets/images/excel_ia.png') }}">
								@endif
								</a>
							</td>
						</tr>
					@empty
						<tr>
							<td class="text-center colred" colspan="11" >
								{{ trans('messages.lbl_nodatafound') }}
							</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
		</div>
		<!-- For Pagination -->
		<div class="text-center ml8">
			@if(!empty($employeeDetails->total()))
				<span class="pull-left mt24">
					{{ $employeeDetails->firstItem() }} ~ {{ $employeeDetails->lastItem() }} / {{ $employeeDetails->total() }}
				</span>
			@endif 
			{{ $employeeDetails->links() }}
		<div class="CMN_display_block flr mr10">
			{{ $employeeDetails->linkspagelimit() }}
		</div>
		</div>
		<!-- End of PAgination -->
		{{ Form::close() }}
	</article>
</div>
<div id="importpopup" class="modal fade">
    <div id="login-overlay">
        <div class="modal-content">
            <!-- Popup will be loaded here -->
        </div>
    </div>
</div>
<div id="empselectionpopup" class="modal fade">
    <div id="login-overlay">
        <div class="modal-content">
            <!-- Popup will be loaded here -->
        </div>
    </div>
</div>
@endsection
