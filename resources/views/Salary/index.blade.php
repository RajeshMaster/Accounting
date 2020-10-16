@extends('layouts.app')
@section('content')
@php use App\Http\Helpers; @endphp
{{ HTML::script('resources/assets/js/salary.js') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
</script>
<?php $request->selMonth = date('m');
	  $request->selYear = date('Y'); ?>
<style type="text/css">
	.alertboxalign {
    	margin-bottom: -50px !important;
	}
	.alert {
	    display:inline-block !important;
	    height:30px !important;
	    padding:5px !important;
	}
	.sort_asc {
		background-image:url({{ URL::asset('resources/assets/images/upArrow.png') }}) !important;
	}
	.sort_desc {
		background-image:url({{ URL::asset('resources/assets/images/downArrow.png') }}) !important;
	}
</style>
	<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
	<article id="expenses" class="DEC_flex_wrapper " data-category="expenses expenses_sub_5">
	{{ Form::open(array('name'=>'salaryindex', 'id'=>'salaryindex', 'url' => 'Salary/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,
		  'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('datemonth', '' , array('id' => 'datemonth')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
		{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
		{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
		{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
		{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
		{{ Form::hidden('account_val', $account_val, array('id' => 'account_val')) }}
		{{ Form::hidden('previou_next_year', $request->previou_next_year, array('id' => 'previou_next_year')) }}
		{{ Form::hidden('sortOptn',$request->salarysort , array('id' => 'sortOptn')) }}
	    {{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
	    {{ Form::hidden('ids', '' , array('id' => 'ids')) }}
	    {{ Form::hidden('id', '' , array('id' => 'id')) }}
	    {{ Form::hidden('empname', '' , array('id' => 'empname')) }}
	    {{ Form::hidden('salary', '' , array('id' => 'salary')) }}
	    {{ Form::hidden('bankid', '' , array('id' => 'bankid')) }}
		{{ Form::hidden('editflg', '' , array('id' => 'editflg')) }}
		{{ Form::hidden('multiflg', '' , array('id' => 'multiflg')) }}
	<!-- Start Heading -->
	<div class="row hline pm0">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/salary_1.png') }}">
			<h2 class="pull-left pl5 mt10 CMN_mw150">{{ trans('messages.lbl_salary') }}</h2>
		</div>
	</div>
	<!-- End Heading -->
	<div class="box100per pl15 pr15 mt10">
		<div class="mt10 mb10">
			{{ Helpers::displayYear_MonthEst($account_period, $year_month, $db_year_month, $date_month, $dbnext, $dbprevious, $last_year, $current_year, $account_val) }}
		</div>
	</div>
	<div class="col-xs-12 mb5">
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
		@if($reg == 1)
			<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
				<a href="javascript:gotomultiadd('{{ $request->mainmenu }}',1);" class="btn btn-success box100">
					<span class="fa fa-plus"></span>
					{{ trans('messages.lbl_multiadd') }}
				</a>
			</div>
		@else
			<div class="col-xs-6" style="text-align: left;margin-left: -15px;">
				<a href="javascript:filledcondition();" class="btn btn-success box100">
					<span class="fa fa-plus"></span>
					{{ trans('messages.lbl_multiadd') }}
				</a>
			</div>
		@endif
	</div>
	<div class="col-xs-12" style="margin-top: -5px;">
		<div class="col-xs-6 mt10" style="text-align: left;margin-left: -15px;">
			<a class="pull-left" href="javascript:empselecttpopupenable('{{ $date_month }}','{{ $request->mainmenu }}');">
                <img class="pull-left box19 mt2" src="{{ URL::asset('resources/assets/images/edit.png') }}" href="javascript:empselecttpopupenable('{{ $date_month }}','{{ $request->mainmenu }}');"></a>
                <a class="pull-left ml6" href="javascript:empselecttpopupenable('{{ $date_month }}','{{ $request->mainmenu }}');">
                    {{ trans('messages.lbl_cempsel') }}
                </a>
		</div>
		<div class="col-xs-6 pm0 pull-right moveleft nodropdownsymbol mb10" id="moveleft">
			@if(!empty($fileCnt))
			<span class="mr5 ml413">
				<a href="javascript:clearsearch()" title="Clear Search">
	            		<img class="box30" src="{{ URL::asset('resources/assets/images/clearsearch.png') }}">
	         	</a>
	        </span>
				{{ Form::select('salarysort', [null=>''] + $salarysortarray, $request->salarysort,
                            array('class' => 'form-control'.' ' .$request->sortstyle.' '.'CMN_sorting pull-right',
                           'id' => 'salarysort',
                           'name' => 'salarysort'))
                }}
            @endif
		</div>
	</div>
	<div class="pt43 minh200 pl15 pr15">
		<table class="tablealternate CMN_tblfixed">
			<colgroup>
				<col width="4%">
				<col width="8%">
				<col>
				<col width="8%">
				<col width="8%">
				<col width="11%">
				<col width="11%">
				<col width="18%">
				<col width="7%">
			</colgroup>
			<thead class="CMN_tbltheadcolor">
				<tr>
					<th class="vam">{{ trans('messages.lbl_sno') }}</th>
					<th class="vam">{{ trans('messages.lbl_empid') }}</th>
					<th class="vam">{{ trans('messages.lbl_empName') }}</th>
					<th class="vam">{{ trans('messages.lbl_Date') }}</th>
					<th class="vam">{{ trans('messages.lbl_month') }}</th>
					<th class="vam">{{ trans('messages.lbl_salary') }}</th>
					<th class="vam">{{ trans('messages.lbl_charge') }}</th>
					<th class="vam">{{ trans('messages.lbl_bank_name') }}</th>
					<th class="vam"></th>
				</tr>
				@if(!empty($fileCnt))
				<tr style="background-color:#DDDDDD;" class="boxhei25">
					<td class="tax_data_name"></td>
					<td class="tax_data_name"></td>
					<td class="tax_data_name"></td>
					<td class="tax_data_name"></td>
					<td class="tax_data_name"></td>
					<td class="tax_data_name tar blue CMN_boldText pr5">
						<?php if ( $saltotal != "" ) {
							echo number_format($saltotal);
						}?>
					</td>
					<td class="tax_data_name tar blue CMN_boldText pr5">
						<?php if ($chartotal != "") {
							echo number_format($chartotal);
						}?>
					</td>
					<td class="tax_data_name"></td>
					<td class="tax_data_name"></td>
				</tr>
				@endif
			</thead>
			<tbody>
				{{ $temp = ""}}
				{{--*/ $row = '0' /*--}}
				@if(!empty($fileCnt))
					@for($cnt=0; $cnt<$fileCnt;$cnt++)
						{{--*/ $loc = $get_det[$cnt]['empNo'] /*--}}
						@if($loc != $temp) 
							@if($row==1)
								{{--*/ $style_tr = 'background-color: #b2d5ed;' /*--}}
								{{--*/ $row = '0' /*--}}
							@else
							{{--*/ $style_tr = 'background-color: #FFFFFF;' /*--}}
							{{--*/ $row = '1' /*--}}
						@endif
							{{--*/ $style_td = 'border-bottom: 1 px dotted black;;' /*--}}
						@else
							{{--*/ $style_td = 'border-top: none;' /*--}}
						@endif
						<tr style="{{$style_tr}}">
							<td class="tac"  style="{{$style_td}}">
								{{ ($index->currentpage()-1) * $index->perpage() + $cnt + 1 }}
							</td>
							<td class="tac"  style="{{$style_td}}">
								@if($loc != $temp)
									{{ $get_det[$cnt]['empNo'] }}
								@endif
							</td>
							<td class="text-left"  style="{{$style_td}}">
								@if($loc != $temp)
									<a href="javascript:gotoViewlist('{{ $get_det[$cnt]['id'] }}','{{ $get_det[$cnt]['empNo'] }}','{{ $get_det[$cnt]['EmpName'] }}','{{ $request->mainmenu }}');" class="anchorstyle">
										{{ $get_det[$cnt]['EmpName'] }}
									</a>
								@endif
							</td>
							<td class="tac pr5">
								{{ $get_det[$cnt]['salaryDate'] }}
							</td>
							<td class="tac pr5">
								{{ $get_det[$cnt]['salaryMonth'] }}
							</td>
							<td class="text-right pr5">
								<a href="javascript:gotoSingleview('{{ $get_det[$cnt]['id'] }}','{{ $get_det[$cnt]['empNo'] }}','{{ $get_det[$cnt]['EmpName'] }}','{{ $get_det[$cnt]['salary'] }}','{{ $request->mainmenu }}','{{ $get_det[$cnt]['bankId'] }}');" class="anchorstyle">
									{{ $get_det[$cnt]['salary'] }}
								</a>
							</td>
							<td class="text-right pr5">
									{{ $get_det[$cnt]['charge'] }}
							</td>
							<td class="text-left pl5">
								@if($get_det[$cnt]['BankName'] != "" && $get_det[$cnt]['bankId'] != "999")
									{{ $get_det[$cnt]['BankName'] }} - {{ $get_det[$cnt]['accountNo'] }}
								@elseif($get_det[$cnt]['bankId'] == "999")
									Cash
								@else
								@endif
							</td>
							<td class="tac pl5" style="vertical-align: middle;">
								@if($get_det[$cnt]['salary'] == "")
									<a title="Add" href="javascript:gotosingleadd('{{ $get_det[$cnt]['empNo'] }}','{{ $get_det[$cnt]['EmpName'] }}','{{ $request->mainmenu }}',1);" class="anchorstyle">
										<img class="box19" src="{{ URL::asset('resources/assets/images/addicon.png') }}">
									</a>
								@else
									<a title="Copy" href="javascript:gotocopysingless('{{ $get_det[$cnt]['id'] }}','{{ $get_det[$cnt]['EmpName'] }}','{{ $get_det[$cnt]['salary'] }}','{{ $request->mainmenu }}','3','{{ $get_det[$cnt]['empNo'] }}','{{ $get_det[$cnt]['bankId'] }}');" class="anchorstyle">
										<img class="box19" src="{{ URL::asset('resources/assets/images/copy.png') }}">
									</a>
								@endif
							</td>
						</tr>
						{{--*/ $temp = $loc /*--}}
					@endfor
				@else 
						<tr>
							<td class="text-center colred" colspan="9">
								{{ trans('messages.lbl_nodatafound') }}
							</td>
						</tr>
				@endif
			</tbody>
		</table>
	</div>
	@if(!empty($fileCnt))
	<div class="text-center pl13">
		@if(!empty($index->total()))
			<span class="pull-left mt24">
				{{ $index->firstItem() }} ~ {{ $index->lastItem() }} / {{ $index->total() }}
			</span>
		@endif 
		{{ $index->links() }}
		<div class="CMN_display_block flr mr18">
			{{ $index->linkspagelimit() }}
		</div>
	</div>
	@endif
	{{ Form::close() }}
	<div id="empselectionpopup" class="modal fade">
        <div id="login-overlay">
            <div class="modal-content">
                <!-- Popup will be loaded here -->
            </div>
        </div>
    </div>
	</article>
	</div>
@endsection