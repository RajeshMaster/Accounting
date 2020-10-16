@extends('layouts.app')
@section('content')
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
{{ HTML::script('resources/assets/js/staffsalary.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
<div class="CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="staff" class="DEC_flex_wrapper " data-category="staff staff_sub_5">
		{{ Form::open(array('name'=>'staffslyviewfrm', 
						'id'=>'staffslyviewfrm', 
						'url' => 'Staffdetail/viewsalary?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	{{ Form::hidden('empid', $request->empid, array('id' => 'empid')) }}
	{{ Form::hidden('empname', $request->empname, array('id' => 'empname')) }}
	{{ Form::hidden('doj', $request->DOJ, array('id' => 'doj')) }}
	{{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
	{{ Form::hidden('mainCount', count($settingDetails), array('id' => 'mainCount')) }}
	{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
	{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	{{ Form::hidden('prevcnt', $request->prevcnt, array('id' => 'prevcnt')) }}
	{{ Form::hidden('nextcnt', $request->nextcnt, array('id' => 'nextcnt')) }}
	{{ Form::hidden('account_val', $request->account_val, array('id' => 'account_val')) }}
	{{ Form::hidden('previou_next_year', $request->previou_next_year, 
								array('id' => 'previou_next_year')) }}
	{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	{{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	{{ Form::hidden('hdnback', $request->hdnback, array('id' => 'hdnback')) }}
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/salary_1.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_salarylist') }}</h2>
		</div>
	</div>
	<div class="pb10"></div>
	<div class="col-xs-12 pm0 pull-left">
		<div class="col-xs-6 ml10 pm0 pull-left">
			<a href="javascript:goindexpage('{{ $request->mainmenu }}');" class="pageload btn btn-info box80"><span class="fa fa-arrow-left"></span> {{ trans('messages.lbl_back') }}</a>
		</div>
	</div>
	<div class="ml10 fwb mt5">
		<div class="CMN_display_block box5per mt5">
			<label>{{ trans('messages.lbl_empno') }}</label>
		</div>
		<div class="CMN_display_block box15per blue">
			{{  $request->empid }}
		</div>
		<div class="CMN_display_block box4per">
			<label>{{ trans('messages.lbl_name') }}</label>
		</div>
		<div class="CMN_display_block box25per fwn">
			{{  $request->empname }}
		</div>
		<div class="CMN_display_block box6per">
			<label>{{ trans('messages.lbl_Start_date') }}</label>
		</div>
		<div class="CMN_display_block box8per fwn">
			{{  $request->DOJ }}
		</div>
	</div>
	<div class="mr20">
		<table id="clearsearch" class="tablealternate box100per ml10 pr10">
			<colgroup>
				   <col width="5%">
				   <col width="10%">
				   <col width="">
				   <col width="15%">
				   <col width="15%">
				   <col width="15%">
			</colgroup>
				<thead class="CMN_tbltheadcolor">
			   		<tr class="tableheader fwb tac">
				  		<th class="tac">{{ trans('messages.lbl_sno') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_month') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_custname') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_billing') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_salary') }}</th>
				  		<th class="tac">{{ trans('messages.lbl_difference') }}</th>
			   		</tr>
			   	</thead>
			<tbody>
				<tr>
			   		<td colspan="3" bgcolor="lightgrey" class="tar fwb" style="color:blue";>
			   			 {{ trans('messages.lbl_total') }}
			   		</td>
			   		<td bgcolor="lightgrey" class="tar fwb" style="color:blue";>
			   			{{ number_format($total_bill) }}
			   		</td>
			   		<td bgcolor="lightgrey" class="tar fwb" style="color:blue">
			   			{{ number_format($total_sal) }}
			   		</td>
			   		<td bgcolor="lightgrey" class="tar fwb" style="color:blue">
			   			{{ number_format($total_bill-$total_sal) }}
			   		</td>
		   		</tr>
				@foreach($getdata as $key => $row)
			<tr>
				<td class="tac">
					{{ $key+1 }}
				</td>
				<td class="tac">
					{{ $row['date'] }}
				</td>
				<td class="tal">
					{{ isset($row['cname'])?$row['cname']:"" }}
				</td>
				<td class="tar">
					{{ isset($row['billing'])?number_format($row['billing']):"" }}
				</td>
				<td class="tar">
					{{ isset($row['salary'])?number_format($row['salary']):"" }}
				</td>
				<td class="tar">
					@if(isset($row['billing']) || isset($row['salary']) )
						@php $diff = 0;
						 	$diff= number_format($row['billing']-$row['salary']);
						@endphp
						@if($diff < 0)
						<a href="javascript:fnviewbyajax('{{ $row['date'] }}','{{ $request->empid }}');" style="color: red;">
							{{ $diff }}
						</a>	
						@elseif($diff > 0)
						<a href="javascript:fnviewbyajax('{{ $row['date'] }}','{{ $request->empid }}');" style="color: blue;">
							{{ $diff }}
						</a>
						@else
							<span style="color: black;">{{ $diff }}</span>	
						@endif
					@endif
				</td>
			</tr>
			@endforeach
			</tbody>
		</table>
	</div>
	<div id="styleSelector" style="display: none;">
		<div class="selector-toggle">
		<a id="sidedesignselector" href="javascript:void(0)"></a>
		</div>
		<div>
		<ul class="">	
              			<span>
                  			<li class="" style="background-color:#136E83;color: white;">
                  				<div class="mt8" style="display: inline-block;">
                      			<label class="mt0" id="empdate"></label>
                  				</div>
                  				<div style="display: inline-block;">
                  				</div>
                  			</li>
                  			<li class="">
                  				<div style="display: inline-block;">
                      				<span class="mt10 green fwb">{{ trans('messages.lbl_salary') }}  </span>
                  				</div>
                  				<div style="display: inline-block;">
                  				</div>
                  			</li>
                  		<div>
   							<li class="">
   									<?php $mainArray = array('MainTotal1','MainTotal2','MainTotal3','MainTotal4','MainTotal5','MainTotal6','MainTotal7','MainTotal8','MainTotal9','MainTotal10'); ?>	
                  					@for ($x=0; $x < count($settingDetails); $x++) 
										<div class="col-xs-12">
											<div class="col-xs-5 tar">
											{{ $settingDetails[$x]['mainField'] }}
											</div>
											<div class="col-xs-6 pull-right"> 
   											<span class="pull-right" 
   											id="{{ (isset($mainArray[$x])?$mainArray[$x]:"") }}"></span>
   											</div>
   										</div>
									@endfor
   							</li>
            			</div>
                  			<li class="">
                  				<div class="col-xs-12 mt5">
                  				<div class="col-xs-5 tar">
                      				<span class="mt10 green fwb">{{ trans('messages.lbl_total') }}</span>
                      			</div>
                  				<div class="col-xs-6 pull-right">
                  					<label class="pull-right green" id="total"> </label>
                  				</div>
                  				</div>
                  			</li>
                  			<li class="">
                  				<div class="col-xs-12 row hline">
                  				</div>
                  			</li>
                  			<li>
                  				<div style="display: inline-block;margin-top: 10px;">
                  					<span class="mt10 blue fwb">{{ trans('messages.lbl_billing') }}</span>
                  				</div>
                  				<div style="display: inline-block;">
                  				</div>
                  			</li>
                  			<li>
                  				<div class="col-xs-12">
                  					<div class="col-xs-5 tar">
                  					<span>{{ trans('messages.lbl_amount') }} : </span>
                  					</div>
                  					<div class="col-xs-6 pull-right">
                  					<label class="pull-right fwn" id="empamt"> </label>
                  					</div>
                  				</div>
                  			</li>
                  			<li>
                  				<div class="col-xs-12">
                  					<div class="col-xs-6 pm0">
                  					<span>{{ trans('messages.lbl_OTAmount') }} : </span>
                  					</div>
                  					<div class="col-xs-6 pull-right">
                  					<label class="pull-right fwn" id="empotamt"> </label>
                  					</div>
                  				</div>
                  			</li>
                  			<li class="">
                  				<div class="col-xs-12 mb5">
                  					<div class="col-xs-5 tar">
                      				<span class="mt10 blue fwb">{{ trans('messages.lbl_total') }}</span>
	                  				</div>
	                  				<div class="col-xs-6 pull-right">
	                  					<label class="pull-right green " id="totalamt"></label>
	                  				</div>
                  				</div>
                  			</li>
              			</span>
            		</ul>
	</div>
	</div>
		{{ Form::close() }}
</article>
 </div>
 <script type="text/javascript">
 	$('#styleSelector').css({
	    'top': '134px',
	    'position': 'fixed',
	});
 </script>
@endsection