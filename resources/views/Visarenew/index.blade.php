@extends('layouts.app')
@section('content')
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
{{ HTML::script('resources/assets/js/visarenew.js') }}
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="staff" class="DEC_flex_wrapper " data-category="staff staff_sub_6">
	{{ Form::open(array('name'=>'frvisarenewindex', 
						'id'=>'frvisarenewindex', 
						'url' => 'Visarenew/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('Emp_ID', '' , array('id' => 'Emp_ID')) }}
		{{ Form::hidden('visanumb', '' , array('id' => 'visanumb')) }}
		{{ Form::hidden('empname', '' , array('id' => 'empname')) }}
	<!-- Start Heading -->
	<div class="row hline">
	<div class="col-xs-12 pm0 ml10">
			<img class="pull-left box35 mt15" src="{{ URL::asset('resources/assets/images/visarenew.png') }}">
			<h2 class="pull-left pl5 mt15">
				{{ trans('messages.lbl_visarenewlist') }}
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
			<div class="form-group pm0 pull-right moveleft mr10" id="moveleft">
      			<a href="javascript:importvisapopupenable('{{ $request->mainmenu }}');" 
      				style="color:blue;" 
      				class="pb15 box30 anchorstyle">
      					<img class="box22 mr7 mb5" 
      							src="{{ URL::asset('resources/assets/images/import.png') }}">{{ trans('messages.lbl_visaimport') }}
      			</a>
      		</div>
      	</div>
    </div>
    <!-- End Import Link -->
	<!-- Start Page Body -->
	<div class="mr10 ml10 mt10">
		<div class="minh400">
			<table class="tablealternate box100per">
				<colgroup>
					<col width="4%">
					<col width="7%">
					<col width="">
					<col width="13%">
					<col width="4%">
					<col width="8%">
					<col width="8%">
					<col width="11%">
					<col width="12%">
					<col width="7%">
					<col width="7%">
				</colgroup>
				<thead class="CMN_tbltheadcolor">
					<tr class="tableheader fwb tac"> 
						<th class="tac">{{ trans('messages.lbl_sno') }}</th>
						<th class="tac">{{ trans('messages.lbl_empid') }}</th>
						<th class="tac">{{ trans('messages.lbl_empName') }}</th> 
						<th class="tac">{{ trans('messages.lbl_visacardno') }}</th>
						<th class="tac">{{ trans('messages.lbl_year') }}</th>
						<th class="tac">{{ trans('messages.lbl_Start_date') }}</th>
						<th class="tac">{{ trans('messages.lbl_enddate') }}</th> 
						<th class="tac">{{ trans('messages.lbl_visastatus') }}</th>
						<th class="tac">{{ trans('messages.lbl_visaposition') }}</th>
						<th class="tac">{{ trans('messages.lbl_validitystr') }}</th>
						<th class="tac"></th>
					</tr>
				</thead>
				<tbody>
					@forelse($visaRenewDetails as $count => $data)
						<tr>
							<td class="tac">
								{{ ($visaRenewDetails->currentpage()-1) * $visaRenewDetails->perpage() + $count + 1 }}
							</td>
							<td class="tac">
								<!-- fngovisaview('{{ $data->Emp_ID }}') -->
								<a class="anchorstyle" href="javascript:underconstruction()">{{ ($data->Emp_ID !="")?$data->Emp_ID:$data->empuserid}}</a>
							</td>
							<td title="{{empnameontitle($data->LastName,$data->FirstName, 25) }}">
								{{ empnamelength($data->LastName, $data->FirstName, 20) }}
							</td>
							<td>
								{{ $data->visaNo }}
							</td>
							<td class="tac">
								{{ $data->visaValidPeriod }}
							</td>
							<td class="tac">
								{{ $data->visaStartDate }}
							</td>
							<td class="tac">
								{{ $data->visaExpiryDate }}
							</td>
							<td title="{{ $data->NewVisaStatus }}">
								{{ singlefieldlength($data->NewVisaStatus,12) }}
							</td>
							<td title="{{ $data->NewVisaPosition }}">
								{{ singlefieldlength($data->NewVisaPosition,14) }}
							</td>
							<td class="tac"
				               @if ($data->days_diff == '')
				                  @if ($data->Validity  < 90)
				                     style="color:red;font-weight:bold;"
				                  @endif>
				                     @if ($data->Validity  < 0)
				                        -
				                     @else
				                        {{ $data->Validity }}
				                     @endif
				               @else
				                  @if ($data->Validity  < 90)
				                     style="color:blue;font-weight:bold;"
				                  @endif>
				                      @if ($data->Validity  < 0)
				                        {{ $data->days_diff+1 }}
				                     @else
				                        {{ $data->days_diff+1 }}
				                     @endif
				               @endif
				            </td>
				            <td class="tac"
				               @if ($data->days_diff == '')
				                  @if ($data->Validity  < 90)
				                     style="color:red;font-weight:bold;"
				                  @endif>
				                  	@if($data->visaNo != "")
				                     @if ($data->Validity  < 0 || $data->Validity  < 90)
											<img class="box18 mr7 mb5 mt5 csrp"
												onclick="javascript:fngotoaddedit('{{$data->empuserid}}','{{$data->visaNo}}')" 
												title="{{ trans('messages.lbl_visarenew') }}" 
      											src="{{ URL::asset('resources/assets/images/renew.png') }}">
				                     @else
				                     	@if($data->visaExtensionPeriod != "")
											<img class="box18 mr7 mb5 mt5 csrp" 
												onclick="javascript:fngotoaddedit('{{$data->empuserid}}','{{$data->visaNo}}')" 
												title="{{ trans('messages.lbl_visarenew') }}" 
      											src="{{ URL::asset('resources/assets/images/renew.png') }}">
				                     	@else
											<img class="box18 mr7 mb5 mt5 csrp"
												onclick="javascript:underconstruction()" 
												title="{{ trans('messages.lbl_view') }}"
      											src="{{ URL::asset('resources/assets/images/visaview.png') }}">
										@endif
				                     @endif
				                    @endif
				               @else
				                  @if ($data->Validity  < 90)
				                     style="color:blue;font-weight:bold;"
				                  @endif>
				                      @if ($data->Validity  < 0)
											<img class="box18 mr7 mb5 mt5 csrp" 
												onclick="javascript:fngotoaddedit('{{$data->empuserid}}','{{$data->visaNo}}')" 
												title="{{ trans('messages.lbl_visarenew') }}"
      											src="{{ URL::asset('resources/assets/images/renew.png') }}">
				                     @endif
				               @endif
				               	@if($data->delFlg == 1)
				               		<label class="fa fa-arrow-down ml5" 
				               				onclick="javascript:fnvisaextensionform('{{$data->empuserid}}','{{$data->visaNo}}','{{ $data->LastName }}')" 
				               				style="color: green;cursor:pointer;"></label>
				               	@elseif($data->delFlg == 0)
				               		<span class="fa fa-arrow-down ml5" style="visibility: hidden;cursor: default;"></span>
				               	@endif
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
			@if(!empty($visaRenewDetails->total()))
				<span class="pull-left mt24">
					{{ $visaRenewDetails->firstItem() }} ~ {{ $visaRenewDetails->lastItem() }} / {{ $visaRenewDetails->total() }}
				</span>
			@endif 
			{{ $visaRenewDetails->links() }}
		<div class="CMN_display_block flr mr10">
			{{ $visaRenewDetails->linkspagelimit() }}
		</div>
		</div>
	<!-- End of PAgination -->
	<!-- End Page Body -->
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
@endsection