@extends('layouts.app')
@section('content')
<?php use App\Model\Invoice; ?>
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
<style type="text/css">
	/*.highlight1 { background-color: #f2eab0 !important; }*/
</style>
{{ HTML::script('resources/assets/js/common.js') }}
{{ HTML::script('resources/assets/js/invoice.js') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper " data-category="sales sales_sub_2">
	{{ Form::open(array('name'=>'frminvoiceassignemp', 
						'id'=>'frminvoiceassignemp', 
						'url' => 'Invoice/editempassignprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	{{ Form::hidden('table_id','',array('id'=>'table_id','name' => 'table_id')) }}
	{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
	{{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
	{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	<!-- Start Heading --> 
		<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/invoices-icon-3.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_assignemployee') }}</h2>
			<h2 class="pull-left mt15">・</h2>
			<h2 class="pull-left mt15 red">{{ trans('messages.lbl_edit') }}</h2>
		</div>
		</div>
		<div class="col-xs-12 pm0 pull-left mb10 pl10 pr10 mt20 fwb">
        	{{ trans('messages.lbl_year').':' }}
          	<span class="mr40 ml12" style="color:brown;">
            {{ $request->selYear }}
          	</span>
            {{ trans('messages.lbl_month').':' }}
          	<span style="color:brown;margin-left: 10px">
            {{ $request->selMonth }}
            </span>
      </div>
      <div class="col-xs-12 mt5">
		<table class="box99per CMN_tblfixed tablealternate" id = "workspectable">
			<colgroup>
				<col class="tdhead box5per"></col>
				<col class="tdhead box10per"></col>
				<col class="tdhead box18per"></col>
				<col class="tdhead box6per"></col>
				<col class="tdhead box7per"></col>
				<col class="tdhead box8per"></col>
			</colgroup>
			<thead class="CMN_tbltheadcolor">
				<tr class="">
					<th class="">{{ trans('messages.lbl_invoiceno') }}</th>
					<th class="">{{ trans('messages.lbl_companyname') }}</th>
					<th class="">{{ trans('messages.lbl_workspec') }}</th>
					<th class="">{{ trans('messages.lbl_quantity') }}</th>
					<th class="">{{ trans('messages.lbl_unitprice') }}</th>
					<th class="">{{ trans('messages.lbl_amount') }}</th>
				</tr>
			</thead>
			<tbody>
				@if (count($assignemp) > 0)
				{{ $temp = ""}}
				{{--*/ $row = '0' /*--}}
				{{ $tempuser = ""}}
				{{--*/ $rowuser = '0' /*--}}
				{{--*/ $setId = '1' /*--}}
				{{--*/ $j = '1' /*--}}
				{{--*/ $i = '1' /*--}}
				@foreach($assignemp as $key => $value)
					
						{{--*/ $loc = $value->company_name /*--}}
						{{--*/ $locuser = $value->user_id /*--}}
						@if($loc != $temp && $locuser != $tempuser) 
							{{--*/ $i = '1' /*--}}
							@if($row==1)
								{{--*/ $style_tr = 'background-color: #E5F4F9;' /*--}}
								{{--*/ $row = '0' /*--}}
							@else
								{{--*/ $style_tr = 'background-color: #FFFFFF;' /*--}}
								{{--*/ $row = '1' /*--}}
							@endif
							{{--*/ $style_td = '' /*--}}
						@else
							{{--*/ $style_td = 'border-top: hidden;' /*--}}
						@endif
						@if($locuser != $tempuser) 
						{{--*/ $i = '1' /*--}}
							@if($rowuser==1)
								{{--*/ $style_truser = 'background-color: #E5F4F9;' /*--}}
								{{--*/ $rowuser = '0' /*--}}
							@else
								{{--*/ $style_truser = 'background-color: #FFFFFF;' /*--}}
								{{--*/ $rowuser = '1' /*--}}
							@endif
							{{--*/ $style_tduser = '' /*--}}
						@else
							{{--*/ $style_tduser = 'border-top: hidden;' /*--}}
						@endif
						<?php $emp_ID = "emp_id"; ?>
						<?php $workloop = "work_specific"; ?>
						<?php $quantityloop = "quantity"; ?>
						<?php $unit_priceloop = "unit_price"; ?>
						<?php $amountloop = "amount"; ?>
						@if($value->$amountloop != '' || $value->$emp_ID != '')
							<?php /*$ids = '_'.$setId;*/ ?>
							<?php $ids = '_'.$value->id.'_'.$i; ?>
						<tr id="<?php echo $ids; ?>" style="{{ $style_tr }}">
							{{ Form::hidden('id'.$ids,isset($value->id)?$value->id:'',
														array('id'=>'id'.$ids,
																'name' => 'id'.$ids)) }}
							
							<td class="tac" style="{{ $style_tduser }}">
								@if($locuser!=$tempuser)
								{{ Form::hidden('invid'.$j,isset($value->id)?$value->id:'',
														array('id'=>'invid'.$j,
																'name' => 'invid'.$j)) }}
									<label class="pm0 vam" style="color:#136E83;">
										{{ $value->user_id }}
									</label>
								@endif
							</td>
							<td style="{{ $style_td }}">
								@if($loc!=$temp)
									{{ $value->company_name }}
								@endif
							</td>
							<td>
								
								<?php
                                        $getEmpData=Invoice::getemp_details($value->id,$value->emp_id);
                                    ?>
								<div class="">
									<div style="">
										{{ Form::text('work_specific'.$ids, ($value->$workloop) ? $value->$workloop : '',array('id'=>'work_specific'.$ids,
														'name' => 'work_specific'.$ids,
														'maxlength' => 20,
											  			'disabled' => 'true',
														'data-label' => trans('messages.lbl_UserID'),
										  				'onchange' => 'this.value=this.value.trim()',
														'class'=>'box99per form-control pl5 mt3')) }}
									</div>
									@php $cls = "" @endphp
									@if($getEmpData[0]->$emp_ID != "")
										@php $cls='display: inline' @endphp
									@else
										@php $cls='display: none' @endphp
									@endif
									<div id="divid<?php echo $ids ?>" style = "<?php echo $cls; ?>">
											
											{{ Form::hidden('emp_ID'.$ids,isset($getEmpData[0]->$emp_ID)?$getEmpData[0]->$emp_ID:$request->$emp_ID,
																array('id'=>'emp_ID'.$ids,
																		'name' => 'emp_ID'.$ids)) }}
											<label id="empKanaNames<?php echo $ids ?>" name="empKanaNames<?php echo $ids ?>" style="padding-left: 2px;font-weight: 100 !important;color: black !important; font-size: 80%;">
												@if( isset($getEmpData[0]->KanaName) && $getEmpData[0]->KanaName != '　' )
                                                    {{ $getEmpData[0]->KanaName }}

                                                    @else
                                                            @if(isset($getEmpData[0]->EnglishName))
                                                                {{ $getEmpData[0]->EnglishName }}
                                                            @endif
                                                    @endif
											</label>
											<a id="crossid<?php echo $ids ?>" onclick="fngetEmpty('<?php echo $ids ?>',);" style="float: right;cursor: pointer !important;display: none;">
												<i class="fa fa-close" aria-hidden="true"></i>
											</a>
											<script type="text/javascript">
												$(document).ready(function() {
													var empid = $('#emp_ID'+'<?php echo $ids; ?>').val();
													var i = '<?php echo $ids; ?>';
													if (empid != "") {
														$('#crossid'+i).css('display','inline');
													} else {
														// $('#divid'+i).attr('display','none');
													}
												});
											</script>
									</div>
								</div>
							</td>
							<td>
								<div class="">
									<a onclick="return popupenableempname('{{ $request->mainmenu }}','{{ $ids }}');" 
										class="btn btn-success box35 white" style="line-height: 1 !important;">
										<i class="fa fa-plus" aria-hidden="true"></i>
									</a>
										{{ Form::text('quantity'.$ids,($value->$quantityloop) ? $value->$quantityloop : '',array('id'=>'quantity'.$ids,
															'name' => 'quantity'.$ids,
															'style'=>'text-align:right;',
															'maxlength' => 7,
															'data-label' => trans('messages.lbl_UserID'),
											  				'onkeypress' => 'return isDotNumberKey(event,this.value,1)',
											  				'ondragstart'=>'return false',
											  				'disabled' => 'true',
											  				'ondrop'=>'return false',
											  				"onkeyup" => "return fnCalculateAmount('$ids', '', '')",
															'class'=>'box65per form-control pl5 mt3')) }}
								</div>
							</td>
							<td>
								<div class="">
										<?php 
											$color = "";
											$bordercolor = "";
											if (isset($value->$unit_priceloop)) {
												if ($value->$unit_priceloop < 0) {
													$color = 'color:red;';
													$bordercolor = 'border-color:red;';
												}
											} 
										?>
										{{ Form::text('unit_price'.$ids,($value->$unit_priceloop) ? $value->$unit_priceloop : '',array('id'=>'unit_price'.$ids,
															'name' => 'unit_price'.$ids,
															'maxlength' => 11,
															'style' => $color.$bordercolor,
															'data-label' => trans('messages.lbl_UserID'),
											  				'onkeypress' => 'return isNumberKeywithminus(event)',
											  				'ondragstart'=>'return false',
											  				'ondrop'=>'return false',
											  				'disabled' => 'true',
											  				"onkeyup" => "return fnCalculateAmount('$ids', this.name, this.value)",
															'class'=>'box99per form-control pl5 mt3 tar')) }}
								</div>
							</td>
							<td>
								<div class="">
										<?php 
											$color = "";
											$bordercolor = "";
											if (isset($value->$amountloop)) {
												if ($value->$amountloop < 0) {
													$color = 'color:red;';
													$bordercolor = 'border-color:red;';
												}
											} 
										?>
										{{ Form::text('amount'.$ids,($value->$amountloop) ? $value->$amountloop : '',array('id'=>'amount'.$ids,
															'name' => 'amount'.$ids,
															'disabled' => 'true',
															'style' => $color.$bordercolor,
															'data-label' => trans('messages.lbl_UserID'),
															'class'=>'box99per form-control pl5 mt3 tar')) }}
								</div>
							</td>
							{{ Form::hidden('invcount', $j,array('id'=>'invcount',
															'name' => 'invcount')) }}
						</tr>
						{{--*/ $temp = $loc /*--}}
						{{--*/ $tempuser = $locuser /*--}}
						{{--*/ $setId++ /*--}}
						{{--*/ $j++ /*--}} 
						{{--*/ $i++ /*--}}
						@endif
				@endforeach
				@else
                    <tr>
                        <td class="text-center" colspan="6" style="color: red;">{{ trans('messages.lbl_nodatafound') }}</td>
                    </tr>
                @endif
			</tbody>
		</table>
		@if (count($assignemp) > 0)
       <?php $style="";
       $colour="btn edit btn-warning"; ?>
        @else
        <?php
        $style="disabled";
        $colour="btn btn-gray"; ?>
        @endif
		<fieldset style="background-color: #DDF1FA;">
			<div class="form-group">
				<div align="center" class="mt5">
					<button type="submit" class="{{$colour}} editinvprocess box100" {{$style}}>
							<i class="fa fa-edit" aria-hidden="true"></i> {{ trans('messages.lbl_update') }}
					</button>
					<a onclick="javascript:gotoindexinv('{{$request->mainmenu}}');" 
							class="btn btn-danger box120 white {{$style}}">
									<i class="fa fa-times" aria-hidden="true"></i> 
										{{trans('messages.lbl_cancel')}}
					</a>
				</div>
			</div>
		</fieldset>
	</div>

	{{ Form::close() }}
	{{ Form::open(array('name'=>'involdeditcancel', 'id'=>'involdeditcancel', 'url' => 'Invoice/editempassignprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
	{{ Form::close() }}
</article>
<div id="empnamepopup" class="modal fade">
    <div id="login-overlay">
        <div class="modal-content">
            <!-- Popup will be loaded here -->
        </div>
    </div>
</div>
</div>
@endsection