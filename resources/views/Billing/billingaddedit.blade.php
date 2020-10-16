@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/billing.js') }}
<style type="text/css">
	.tabellabel {
		color: #136E83;
		font-weight: bold;
		text-align: right;
		padding-right: 10px;
	}
	.editdisable {
	height: 19px;
	width: 18px;
	border: none;
	cursor: pointer;
	background-position: -173px -107px;
}
</style>
<script type="text/javascript">
    var datetime = '<?php echo date('Ymdhis'); ?>';
</script>
<div class="CMN_display_block" id="main_contents">
	<!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper " data-category="sales sales_sub_6">
	@if(isset($sqlquery))
	    	{{ Form::model($sqlquery, array('name'=>'billaddeditfrm','id'=>'billaddeditfrm', 
	    				'class'=>'form-horizontal',
	    				'url' => 'Billing/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
	    				'files'=>true,
	    				'method' => 'POST')) }}
	{{ Form::hidden('confirmid','2', array('id' => 'confirmid')) }}
	{{ Form::hidden('hdn_lblBillingAmt', '' ,array('id' => 'hdn_lblBillingAmt')) }}
	@else
	{{ Form::open(array('name'=>'billaddeditfrm', 
						'id'=>'billaddeditfrm', 
						'url' => 'Billing/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	{{ Form::hidden('confirmid','1', array('id' => 'confirmid')) }}
	@endif
	{{ Form::hidden('id',old('id'), array('id' => 'id')) }}
	{{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
    {{ Form::hidden('scrname', '' ,array('id' => 'scrname')) }}
    {{ Form::hidden('mainmenu', $request->mainmenu ,array('id' => 'mainmenu')) }}
	{{ Form::hidden('emp_id', $request->hdnempid , array('id' => 'emp_id')) }}
	{{ Form::hidden('emp_name', $request->hdnnickname , array('id' => 'emp_name')) }}
	{{ Form::hidden('last_name', $request->hdnlastname , array('id' => 'last_name')) }}
	{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
	{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
    {{ Form::hidden('hdnempid', '' ,array('id' => 'hdnempid')) }}
    {{ Form::hidden('startdate', '' ,array('id' => 'startdate')) }}
    {{ Form::hidden('lblmMinAmt', '' ,array('id' => 'lblmMinAmt')) }}
    {{ Form::hidden('lblmMaxAmt', '' ,array('id' => 'lblmMaxAmt')) }}
    {{ Form::hidden('hdn_lblBillingAmt', '' ,array('id' => 'hdn_lblBillingAmt')) }}
    {{ Form::hidden('hdn_otamount', '' ,array('id' => 'hdn_otamount')) }}
    <!-- Start Heading -->
		<div class="row hline">
			<div class="col-xs-12">
				<img class="pull-left box35 mt15" 
					src="{{ URL::asset('resources/assets/images/billing.png') }}">
				<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_billing') }}・
				<span class="green mt15">
				@if($request->scrname=="register"){{ trans('messages.lbl_register') }}</span>
				<span class="blue fwb">
					@if(Session::get('languageval') == "en")
						{{ $request->selYear.'&nbsp;/&nbsp;'.$request->selMonth }}
					@else
						{{ $request->selYear.'年'.$request->selMonth.'月' }}
					@endif
				</span>
				@elseif($request->scrname=="copy"){{ trans('messages.lbl_copy') }}</span>
				@else<span class="mt15 red">{{ trans('messages.lbl_update') }}@endif</span>&nbsp;
				</h2>
			</div>
		</div>
		<div class="pb10"></div>
		<!-- End Heading -->
		<div class="minh400 pl15 pr10 mt3">
			<table class="box100per">
				<colgroup>
			        <col width="5%">
			        <col width="5%">
			        <col width="6%">
			        <col width="20%">
			        <col width="6%">
			        <col width="6%">
			        <col width="6%">
	        	</colgroup>
				<tr>
					<td class="CMN_bdrnone tabellabel pr20">{{ trans('messages.lbl_empid') }}</td>
					<td class="CMN_bdrnone">{{ $request->hdnempid }}</td>
					<td class="CMN_bdrnone tabellabel pr20">{{ trans('messages.lbl_name') }}</td>
					<td class="CMN_bdrnone">
					<label>
						@if(isset($sqlquery['LastName']))
							{{ $sqlquery['LastName'] }}
						@else

						@endif
						@if(($request->scrname =="edit") || ($request->scrname =="copy"))
							@if(isset($sqlquery['nickname']))
								( {{ $sqlquery['nickname'] }} )
							@else
							
							@endif
						@else
							( {{$request->hdnlastname}} )
						@endif
					</label>		
					</td>
					<td class="CMN_bdrnone"></td>
					<td class="CMN_bdrnone"></td>
					<td class="CMN_bdrnone pull-right" style="border: 1px solid red;">
						<input type="checkbox" name="caldone" id="caldone"
						@if(!empty($sqlquery['tcheckcalc'])) 
						@if($sqlquery['tcheckcalc'] == 1) checked @endif @endif>&nbsp;
						<label  for='caldone' class="font_size_12 color_black">
						{{ trans('messages.lbl_Caldone') }}</label>
						
					</td>
				</tr>
				<tr>
					<td class="CMN_bdrnone tabellabel">
					{{ trans('messages.lbl_client_name') }}<span class="fr red"> * </span></td>
					<td class="CMN_bdrnone">
					{{ Form::select('clientname', [null=>'']+$cust_id,old('clientname'),array('name' => 'clientname','id'=>'clientname','style' => 'min-width:80px;max-width:150px;','data-label' => trans('messages.lbl_client_name'),'class'=>'pl5',
					'onchange'=>'javascript:fnGetcustomerDetail(1);'))}}
					</td>
					<td class="CMN_bdrnone tabellabel">
					{{ trans('messages.lbl_branch') }}<span class="fr red"> * </span></td>
					<td class="CMN_bdrnone">
					{{ Form::select('branchname',[null=>'']+$getbranchname,old('branchname'),array('name' => 'branchname','id'=>'branchname','style' => 'min-width:80px;','data-label' => trans('messages.lbl_branch'),'class'=>'pl5'))}}
					</td>
					<td class="CMN_bdrnone tabellabel">{{ trans('messages.lbl_Start_date') }}</td>
					<td class="CMN_bdrnone">
					@if(!empty($getdata))
					{{ $getdata[0]->start_date }}
					@else {{ "Nill" }} @endif</td>
					<td class="CMN_bdrnone pull-right"><a href="javascript:goindexpage('{{ $request->mainmenu }}');" class="pageload btn btn-info box80"><span class="fa fa-arrow-left"></span> {{ trans('messages.lbl_back') }}</a></td>
				</tr>
	        </table>
	        <table class="box100per" style="margin-top: -5px;">
	        	<tr>
					<td colspan="7" class="CMN_bdrnone">
						<span class="fwb pl5">
							{{ trans('messages.lbl_billingdetails').'&nbsp;:' }}
						</span>
				        <fieldset class="mt5">
				        	<div>
								<div class="mt10">
									<div class="box250 tabellabel CMN_display_block">
									{{ trans('messages.lbl_amount') }}<span class="fr ml2 red">*</span>
									</div>	
									<div class="box100 CMN_display_block">
									{{ Form::text('amount',old('amount'),array('id'=>'amount', 'name' => 'amount','class'=>'tar box100per form-control','data-label' => trans('messages.lbl_amount'),'onDrag' => 'return false' ,'onPaste'=> 'return false',
									'onkeypress' => 'return isNumberKey(event)','onkeyup' => "chgamountBD();return fnMoneyFormat(this.name,'jp');")) }}
									</div>
								</div>
								<div class="mt5">
									<div class="box250 tabellabel CMN_display_block">
									{{ trans('messages.lbl_timerange') }}<span class="fr ml2 red">*</span>
									</div>
									<div class="tar box100 CMN_display_block">
									{{ Form::text('time_start',old('time_start'),array('id'=>'time_start', 'name' => 'time_start','class'=>'tar box100per form-control','data-label' => trans('messages.lbl_timerange'),'onDrag' => 'return false' ,'onPaste'=> 'return false',
										'onkeypress' => 'return isNumberKey(event)')) }}
									</div>&nbsp;&nbsp;~&nbsp;&nbsp;
									<div class="CMN_display_block">
									{{ Form::text('time_end',old('time_end'),array('id'=>'time_end', 'name' => 'time_end','class'=>'tar box60per form-control','data-label' => trans('messages.lbl_timerange'),'onDrag' => 'return false' ,'onPaste'=> 'return false',
													'onkeypress' => 'return isNumberKey(event)')) }}
									</div>
								</div>
								<div class="mt5 mb12">
									<div class="box250 tabellabel CMN_display_block">
									{{ trans('messages.lbl_OTAmount') }}<span class="fr ml2 red" id="mandatory_hidden" style="visibility: visible;">*</span>
									</div>	
									<div class="box100 CMN_display_block">
										{{ Form::text('ot_start',old('ot_start'),array('id'=>'ot_start', 'name' => 'ot_start','class'=>'tar box100per form-control','data-label' => trans('messages.lbl_OTAmount'),'onDrag' => 'return false' ,'onPaste'=> 'return false',
										'onkeypress' => 'return isNumberKey(event)')) }}
									</div>&nbsp;&nbsp;~&nbsp;&nbsp;
									<div class="CMN_display_block">
										{{ Form::text('ot_end',old('ot_end'),array('id'=>'ot_end', 'name' => 'ot_end','class'=>'tar box60per form-control','data-label' => trans('messages.lbl_OTAmount'),'onDrag' => 'return false' ,'onPaste'=> 'return false',
										'onkeypress' => 'return isNumberKey(event)')) }}
									</div>
									<div class="box225 CMN_display_block">
									@if($request->scrname == 'edit') @php $page = 'edit' @endphp
									@else @php $page = 'register' @endphp
									@endif
										<input type="checkbox" name="chkval" id="chkval" 
									onclick="return fnCheckboxVal('{{$page}}');"
											@if(!empty($sqlquery['checkboxval'])) 
											@if($sqlquery['checkboxval'] == 1) checked @endif @endif>
										<label for='chkval' class="vat CMN_display_block mr10 fwn">
											{{ trans('messages.lbl_autocal') }}
										</label>
									</div>
								</div>
							</div>
				        </fieldset>
		        	</td>
		        </tr>
	        </table>
	        <table class="box100per" style="margin-top: -15px;">
	        	<tr>
					<td colspan="7" class="CMN_bdrnone">
						<span class="fwb pl5">
							{{ trans('messages.lbl_monthlybilling').
							'&nbsp;'.trans('messages.lbl_calculation').'&nbsp;:' }}
						</span>
				        <fieldset>
				        	<div>
				        		@if($request->scrname == 'edit')
				        		<div class="mt10">
									<div class="box250 tabellabel CMN_display_block">
									{{ trans('messages.lbl_yearmonth') }}<span class="fr ml2 red">*</span>
									</div>	
									<div class="box150 CMN_display_block">
										{{ Form::select('selYear',[null=>'']+$getyearname,old('selYear'),array('name' => 'selYear','id'=>'selYear','data-label' => trans('messages.lbl_yearmonth'),'class'=>'box38per'))}}
									<span>-</span>		
										{{ Form::select('selMonth', [null=>'']+$getmonthname,old('selMonth'),array('name' => 'selMonth','id'=>'selMonth','data-label' => trans('messages.lbl_yearmonth'),'class'=>'box27per'))}}	
									</div>
								</div>
								@elseif($request->scrname == 'copy')
								<div class="mt10">
									<div class="box250 tabellabel CMN_display_block">
									{{ trans('messages.lbl_yearmonth') }}<span class="fr ml2 red">*</span>
									</div>	
									<div class="box150 CMN_display_block">
										{{ Form::select('',[null=>''] + $getyearname,'',array('name' => 'selYear','id'=>'selYear','data-label' => trans('messages.lbl_yearmonth'),'class'=>'box38per'))}}
									<span>-</span>		
										{{ Form::select('', [null=>''] + $getmonthname,'',array('name' => 'selMonth','id'=>'selMonth','data-label' => trans('messages.lbl_yearmonth'),'class'=>'box27per'))}}	
									</div>
								</div>
								@endif
								<div class="mt10">
									<div class="box250 tabellabel CMN_display_block">
									{{ trans('messages.lbl_timerange') }}<span class="fr ml2 red">*</span>
									</div>
									@if($request->scrname == 'edit')	
									<div class="box100 CMN_display_block">
										{{ Form::text('timerange',old('timerange'),array('id'=>'timerange', 'name' => 'timerange','class'=>'tar box110','data-label' => trans('messages.lbl_timerange'),'onDrag' => 'return false' ,'onPaste'=> 'return false',
													'onkeypress' => 'return isNumberKey(event)','onkeyup' => "chgtimerange();")) }}
									</div>
									@else
									<div class="box100 CMN_display_block">
										{{ Form::text('timerange','',array('id'=>'timerange', 'name' => 'timerange','class'=>'tar box110','data-label' => trans('messages.lbl_timerange'),'onDrag' => 'return false' ,'onPaste'=> 'return false',
													'onkeypress' => 'return isNumberKey(event)','onkeyup' => "chgtimerange();")) }}
									</div>
									@endif
									<div class="box18per CMN_display_block">
									@if($request->scrname == 'edit')	
										<label class="CMN_display_block box50per fll tar">
										<input type="checkbox" name="chkvalTS" id="chkvalTS" 
											onclick="return fngetworkinghrs('{{ $request->selMonth}}', '{{ $request->selYear}}',
											'{{$request->hdnempid}}');"
											@if(!empty($sqlquery['chkvalTS'])) 
											@if($sqlquery['chkvalTS'] == 1 ) checked @endif @endif></label>
									@else
										<div class="CMN_display_block box50per fll tar">
											<input type="checkbox" name="chkvalTS" id="chkvalTS" 
												onclick="return fngetworkinghrs('{{ $request->selMonth}}', '{{ $request->selYear}}',
												'{{$request->hdnempid}}');">
										</div>
									@endif		
										<label for="chkvalTS" class="vat CMN_display_block ml5 tal fwn">
											{{ trans('messages.lbl_getworkinghrs') }}
										</label>
									</div>
								</div>
								<div class="mt5">
									<div class="box250 tabellabel CMN_display_block">
									{{ trans('messages.lbl_OTAmount') }}<span class="fr ml2 red">*</span>
									</div>
									@if($request->scrname == 'edit')	
									<div class="box100 CMN_display_block" id="overtmamt">
										{{ Form::text('otamount',old('otamount'),array('id'=>'otamount', 'name' => 'otamount','class'=>'tar box110','data-label' => trans('messages.lbl_OTAmount'),'onDrag' => 'return false' ,'onPaste'=> 'return false',
													'onkeypress' => 'return isNumberKey(event)','onkeyup' => "calcBillAmount();return fnMoneyFormat(this.name, 'jp');")) }}
									</div>
									@else
									<div class="box100 CMN_display_block" id="overtmamt">
										{{ Form::text('otamount','',array('id'=>'otamount', 'name' => 'otamount','class'=>'tar box110','data-label' => trans('messages.lbl_OTAmount'),'onDrag' => 'return false' ,'onPaste'=> 'return false',
													'onkeypress' => 'return isNumberKey(event)','onkeyup' => "calcBillAmount();return fnMoneyFormat(this.name, 'jp');")) }}
									</div>
									@endif
									<div class="box18per CMN_display_block">
									@if($request->scrname == 'edit')	
										<div class="CMN_display_block box50per fll tar">
										<input type="checkbox" name="chkvalMB" id="chkvalMB" 
											onclick="return fnTotalCalc();"
											@if(!empty($sqlquery['TotalCalc'])) 
											@if($sqlquery['TotalCalc'] == 1) checked @endif @endif></div>
									@else
										<div class="CMN_display_block box50per fll tar">
										<input type="checkbox" name="chkvalMB" id="chkvalMB" 
											onclick="return fnTotalCalc();"></div>
									@endif		
										<label for="chkvalMB" class="vat CMN_display_block ml5 tal fwn">
											{{ trans('messages.lbl_autocal') }}
										</label>
									</div>
								</div>
								<div class="mt5 mb12 h25">
									<div class="box250 tabellabel CMN_display_block pr20">
										{{ trans('messages.lbl_billingAmt') }}
									</div>
									@if($request->scrname == 'edit')	
										<div class="tar box110 CMN_display_block">
										<label name="lblBillingAmt" id="lblBillingAmt">
											@if(!empty($sqlquery['totalamount']))
											{{$sqlquery['totalamount']}}
											@else 
											@endif
										</label>
										</div>
									@else
										<div class="tar box110 CMN_display_block">
										<label name="lblBillingAmt" id="lblBillingAmt">
										</label>
										</div>
									@endif	
								</div>
							</div>
				        </fieldset>
		        	</td>
		        </tr>
	        </table>
	        <div style="margin-top: -17px;">
	        <fieldset style="background-color: #DDF1FA;">
				<div class="form-group mt15">
					<div align="center" class="mt9">
					@if($request->scrname =="edit")
						<button type="submit" class="btn edit btn-warning box100 addeditprocess">
							<i class="fa fa-edit" aria-hidden="true"></i> {{ trans('messages.lbl_update') }}
						</button>
						<a onclick="javascript:gotoindexpage('1','{{ $request->mainmenu }}',{{ date('YmdHis') }});" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
						</a>
						@else
						<button type="submit" class="btn btn-success add box100 addeditprocess" >
							<i class="glyphicon glyphicon-plus"></i> {{ trans('messages.lbl_register') }}
						</button>
							@if($request->scrname =="copy")
							<a onclick="javascript:gotoindexpage('1','{{ $request->mainmenu }}',{{ date('YmdHis') }});" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
							@else
							<a onclick="javascript:gotoindexpage('2','{{ $request->mainmenu }}',{{ date('YmdHis') }});" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
							@endif
						@endif
						</a>
					</div>
				</div>
				<div class="CMN_display_block pb10"></div>
			</fieldset>
			</div>
		</div>
	{{ Form::close() }}
	{{ Form::open(array('name'=>'frmbillingaddeditcancel', 'id'=>'frmbillingaddeditcancel', 'url' => 'Billing/addeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
	{{ Form::hidden('id',old('id'), array('id' => 'id')) }}
	{{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
    {{ Form::hidden('scrname', '' ,array('id' => 'scrname')) }}
    {{ Form::hidden('mainmenu', $request->mainmenu ,array('id' => 'mainmenu')) }}
	{{ Form::hidden('emp_id', $request->hdnempid , array('id' => 'emp_id')) }}
	{{ Form::hidden('emp_name', $request->hdnnickname , array('id' => 'emp_name')) }}
	{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
	{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
    {{ Form::hidden('hdnempid', '' ,array('id' => 'hdnempid')) }}
    {{ Form::hidden('startdate', '' ,array('id' => 'startdate')) }}
    {{ Form::hidden('lblmMinAmt', '' ,array('id' => 'lblmMinAmt')) }}
    {{ Form::hidden('lblmMaxAmt', '' ,array('id' => 'lblmMaxAmt')) }}
    {{ Form::hidden('hdn_lblBillingAmt', '' ,array('id' => 'hdn_lblBillingAmt')) }}
    {{ Form::hidden('hdn_otamount', '' ,array('id' => 'hdn_otamount')) }}
	{{ Form::close() }}
	</article>
</div>
@endsection