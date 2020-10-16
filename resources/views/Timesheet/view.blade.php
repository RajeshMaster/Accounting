@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/timesheet.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
<script type="text/javascript" >
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
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
<script type="text/javascript" >
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
<div class="CMN_display_block" id="main_contents" >
<!-- article to select the main&sub menu -->
<article id="staff" class="DEC_flex_wrapper " data-category="staff staff_sub_3">
<!-- Start Heading -->
{{ Form::open(array('name'=>'timesheetview', 
						'id'=>'timesheetview', 
						'url' => 'Timesheet/timesheetview?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('empinhide', $request->empinhide, array('id' => 'empinhide')) }}
		{{ Form::hidden('selMonth', $request->selMonth , array('id' => 'selMonth')) }}
		{{ Form::hidden('selYear', $request->selYear , array('id' => 'selYear')) }}
		{{ Form::hidden('empid', $Emp_ID , array('id' => 'empid')) }}
		{{ Form::hidden('downloadflg', $request->downloadflg , array('id' => 'downloadflg')) }}
		{{ Form::hidden('flag', $request->flag , array('id' => 'flag')) }}
		<!-- label,button purpose -->
		{{ Form::hidden('flagval', $request->flagval , array('id' => 'flagval')) }}
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt15" 
				src="{{ URL::asset('resources/assets/images/timesheet.jpg') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_timesheetentrylist') }}</h2>
		</div>
	</div>
	<div class="pb10"></div>
	<!-- End Heading -->
	<div class="col-xs-12 mb10">
		<div class="col-xs-3 pull-left pl1">
			<a href="javascript:goindex('{{ $request->flag }}');" class="btn btn-info box80"><span class="fa fa-arrow-left"></span> {{ trans('messages.lbl_back') }}</a>
        </div>
		<div class="col-xs-9 pull-right pr1">
			@if(Session::has('success'))			
			<div align="center" class="col-xs-5 alertboxalign mt5" role="alert">
				<p class="alert {{ Session::get('alert', Session::get('type') ) }}">
	            	{{ Session::get('success') }}
	          	</p>
			</div>
			@endif
			@php Session::forget('success'); @endphp
		<!-- Session msg -->
			@if(!empty($update))
			<button type="button" id="staffpopup" 
				class="btn fa fa-edit btn-warning box90 pull-right mr0"
	                onclick="return addeditupdate('{{ $request->mainmenu }}',
	                '{{ $Emp_ID }}','{{ 3 }}');">
	                {{ trans('messages.lbl_updatetimesheet') }}
	        </button>
	        @else
	        <button type="button" id="staffpopup" 
				class="btn btn-success box80 pull-right mr0"
	                onclick="return addeditupdate('{{ $request->mainmenu }}',
	                '{{ $Emp_ID }}','{{ 1 }}');">
	                <i class="glyphicon glyphicon-add"></i>
	                {{ trans('messages.lbl_register') }}
	        </button>
	        @endif
	        <a href="javascript:timesheetdownload('{{ $request->mainmenu }}',
	        '{{ 3 }}');" class="pull-right pr10 anchorstyle mt8">
	        {{ trans('messages.lbl_download') }}
	        <img class="pull-left box20 mr10 mt1" src="{{ URL::asset('resources/assets/images/excelIcon.png') }}">
				</a>
	        <a href="javascript:uploadpopup('{{ $request->mainmenu}}', '{{ $Emp_ID }}',
	        '{{ $yr }}','{{ $mon }}')" class="pull-right pr10 anchorstyle  mt8"></span>
	        <img class="pull-left box20 mr10 mt1"
				src="{{ URL::asset('resources/assets/images/excelIcon.png') }}">
				{{ trans('messages.lbl_upload') }}</a>
        </div>
	</div>
	<div class="mr10 ml13">
		<center><h2></h2></center>
		<table class="box100per">
			<tr style="background-color:#B0E0F2;font-weight: bold;font-size: 15px">
				<td>
					<center>
						{{ trans('messages.lbl_year') }}
					</center>
				</td>
				<td>
					<center>
						{{ trans('messages.lbl_month') }}
					</center>
				</td>
				<td rowspan="2" colspan="8" style="background-color: #FFFFFF;border-top:hidden;">
					<center>
						{{ trans('messages.lbl_workingtimechart') }}
					</center>
				</td>
				<td>
					<center>
						<span>
								{{ mb_substr(trans('messages.lbl_empid'),0,6,'utf-8') }}
							</span>
					</center>
				</td>
				<td colspan="2">
					<center>
						{{ trans('messages.lbl_name') }}
					</center>
				</td>
				<td>
					<center>
						<span>
							{{ mb_substr(trans('messages.lbl_print'),0,6,'utf-8') }}
						</span>
					</center>
				</td>
			</tr>
			<tr style="font-size: 15px">
				<td> 
					<center>
						<?php echo $yr;?>
					</center> 
				</td>
				<td> 
					<center>
						<?php echo $mon;?>
					</center> 
				</td>
				<td> 
					<center>
						<?php echo $Emp_ID;?>
					</center> 
				</td>
				<td colspan="2"> 
					<center>
						<?php echo $Name;?>
					</center> 
				</td>
				<td></td>
			</tr>
			<tr style="background-color:#B0E0F2;font-weight: bold;font-size: 15px">
				<td rowspan="2">
					<center>
						<span>
							{{ mb_substr(trans('messages.lbl_date'),0,4,'utf-8') }}
						</span>
					</center>
				</td>
				<td rowspan="2">
					<center>
						<span title = "{{ trans('messages.lbl_classification') }}">
							@if(singlefieldlength(trans('messages.lbl_classification'),4))
			            		{{singlefieldlength(trans('messages.lbl_classification'),4)}}
			            	@endif
						</span>
					</center>
				</td>
				<td rowspan="2" colspan="2">
					<center>
						{{ trans('messages.lbl_workplace') }}
					</center>
				</td>
				<td rowspan="1" colspan="2">
					<center>
						{{ trans('messages.lbl_workhrs') }}
					</center>
				</td>
				<td rowspan="1" colspan="2">
					<center>
						<span title = "{{ trans('messages.lbl_nonwrkg') }}">
							@if(singlefieldlength(trans('messages.lbl_nonwrkg'),8))
			            		{{singlefieldlength(trans('messages.lbl_nonwrkg'),8)}}
			            	@endif
						</span>
					</center>
				</td>
				<td rowspan="2">
					<center>
						<span title = "{{ trans('messages.lbl_Actwrkghrs') }}">
							@if(singlefieldlength(trans('messages.lbl_Actwrkghrs'),6))
			            		{{singlefieldlength(trans('messages.lbl_Actwrkghrs'),6)}}
			            	@endif
						</span>
					</center>
				</td>
				<td rowspan="2">
					<center>
						<span title = "{{ trans('messages.lbl_overtime') }}">
							@if(singlefieldlength(trans('messages.lbl_overtime'),4))
			            		{{singlefieldlength(trans('messages.lbl_overtime'),4)}}
			            	@endif
						</span>
					</center>
				</td>
				<td rowspan="2">
					<center>
						<span title = "{{ trans('messages.lbl_latenight') }}">
							@if(singlefieldlength(trans('messages.lbl_latenight'),4))
			            		{{singlefieldlength(trans('messages.lbl_latenight'),4)}}
			            	@endif
						</span>
					</center>
				</td>
				<td rowspan="2">
					<center>
						<span title = "{{ trans('messages.lbl_deduction') }}">
							@if(singlefieldlength(trans('messages.lbl_deduction'),4))
			            		{{singlefieldlength(trans('messages.lbl_deduction'),4)}}
			            	@endif
						</span>
					</center>
				</td>
				<td rowspan="2" colspan="2">
					<center>
						{{ trans('messages.lbl_remarks') }}
					</center>
				</td>
			</tr>
			<tr style="background-color:#B0E0F2;font-weight: bold;font-size: 15px">
				<td>
					<center>
						{{ trans('messages.lbl_start') }}
					</center>
				</td>
				<td>
					<center>
						{{ trans('messages.lbl_end') }}
					</center>
				</td>
				<td>
					<center>
						{{ trans('messages.lbl_start') }}
					</center>
				</td>
				<td>
					<center>
						{{ trans('messages.lbl_end') }}
					</center>
				</td>
			</tr>
			<?php for ($i=1; $i <= count($displayArray); $i++) { 
			 
				if ($displayArray[$i]["leaveDisplay"] == 1) { ?>
				<tr style="background-color:#e5f4f9;">
				<?php } else {?>	
				<tr>
				<?php }?>
				<td class="tac" style="font-size: 13px">
					<center>
					<?php echo $i.$youbi[date('N', $displayArray[$i]['timestamp'])];?>
					</center>
				</td>
				<td class="" align="center" style="font-size: 15px">
					<?php $select=array('','○','△','×','▲','□','●','◎','☆','★');
						echo $select[$displayArray[$i]['section']]; ?>					
					<?php $sectioncount[$displayArray[$i]['section']]++;?>	
				</td>
				<td colspan="2" style="font-size: 13px">
					<span><?php echo $displayArray[$i]['workingplace'];?></span>
				</td>
				<td class="tac" style="font-size: 13px">
					<span>
					<?php echo $displayArray[$i]['starttime']=="00:00:00"?'':substr($displayArray[$i]['starttime'],0,5);?></span>		
				</td>
					<?php  $workend=$displayArray[$i]['starttime'];?>
				<td class="tac" style="font-size: 13px">
					<span>
					<?php echo $displayArray[$i]['endtime']=="00:00:00"?'':substr($displayArray[$i]['endtime'],0,5);?></span>		
				</td>
					<?php  $workend=$displayArray[$i]['endtime'];?>
				<td class="tac" style="font-size: 13px">
					<span>	
						<?php echo $displayArray[$i]['non_work_starttime']=="00:00:00"?'':substr($displayArray[$i]['non_work_starttime'],0,5);?>
					</span>							
				</td>	
					<?php $nonstart=$displayArray[$i]['non_work_starttime'];?>
				<td class="tac" style="font-size: 13px">
					<span>
					<?php echo $displayArray[$i]['non_work_endtime']=="00:00:00"?'':substr($displayArray[$i]['non_work_endtime'],0,5);?>
					</span>					
				</td>
				<td class="content_td tac" style="font-size: 13px">
					<?php echo $displayArray[$i]["arr"][0]=="00:00"?'': 
					$displayArray[$i]["arr"][0] ?>
				</td>
				<input type="hidden" id="<?php echo "hiddentd1".$i;?>" 
					value="<?php echo $displayArray[$i]["arr"][0]?>">
				<td class="content_td tac" id="<?php echo "td2".$i;?>" style="font-size: 13px">
				<?php if (!empty($displayArray[$i]["arr"][1])) {
					echo $displayArray[$i]["arr"][1]=="00:00"?'': 
					$displayArray[$i]["arr"][1] ;
				} ?>

				</td>
				<input type="hidden" id="<?php echo "hiddentd2".$i;?>" 
					value="<?php echo $displayArray[$i]["arr"][1]?>">
				<td class="content_td tac" id="<?php echo "td3".$i;?>" style="font-size: 13px">
					<?php echo $displayArray[$i]["arr"][2]=="00:00"?'': 
						$displayArray[$i]["arr"][2] ?>
				</td>
				<input type="hidden" id="<?php echo "hiddentd3".$i;?>" 
					value="<?php echo $displayArray[$i]["arr"][2]?>">

				<td class="content_td tac" id="<?php echo "td4".$i;?>" style="font-size: 13px">
					<?php echo $displayArray[$i]["arr"][3]=="00:00"|| 
						$displayArray[$i]["arr"][3] == ":"?'': $displayArray[$i]["arr"][3] ?>
				</td>
			
				<input type="hidden" id="<?php echo "hiddentd4".$i;?>" 
				value="<?php echo $displayArray[$i]["arr"][3]==":"?'00:00':$arr[3]?>">
				<td colspan="2" style="font-size: 13px">
					<span>
						{!! nl2br(e($displayArray[$i]['remark'])) !!}
					</span>
				</td>
			</tr>
			<?php }  ?>
				<td colspan="8" style="background-color:#B0E0F2;font-size: 15px;">
					<center><b>{{ trans('messages.lbl_totalhrs') }}</b></center>
				</td>
				<td class="" style="font-size: 15px;">
					<center><b><?php echo $actual; ?></b></center>
					{{ Form::hidden('actualTotal', $actual, array('id' => 'actualTotal')) }}
				</td>
				<td class="" style="font-size: 15px;">
					<center><b><?php echo $over; ?></b></center>
					{{ Form::hidden('overTotal', $over, array('id' => 'actualTotal')) }}

				</td>
				<td class="" style="font-size: 15px;">
					<center><b><?php echo $la; ?></b></center>
					{{ Form::hidden('laTotal', $la, array('id' => 'actualTotal')) }}

				</td>
				<td class="" style="font-size: 15px;">
					<center><b><?php echo $dut; ?></b></center>
					{{ Form::hidden('dutTotal', $dut, array('id' => 'actualTotal')) }}

				</td>
				<td colspan="2" style="border-bottom:hidden;">
					<center><b></b></center>
				</td>
			</tr>
		</table>
		<table class="tablealternate box100per" style="border-top:hidden;font-size: 15px;">
			<tr class="">
				<td width="70per">
					<span>
						<span title = "">
							@if(singlefieldlength(trans('messages.lbl_leave'),6))
			            		{{singlefieldlength(trans('messages.lbl_leave'),6)}}
			            	@endif
						</span>
					</span>
					<td width="40per">&nbsp;○
					</td>
				</td>
				<td width="70per" style="font-size: 15px;">
					<center><b>
					<span>
						<?php echo $sectioncount[1]; ?>
					</span>
					&nbsp;<span title="Times">
						<?php echo fnDisplyCharacter("Tim..", 0, 1); ?>
					</span>
					</b></center>
				</td>
				<td width="70per">
					<span>
						<span title = "{{ trans('messages.lbl_comholydy') }}">
							@if(singlefieldlength(trans('messages.lbl_comholydy'),4))
			            		{{singlefieldlength(trans('messages.lbl_comholydy'),4)}}
			            	@endif
						</span>
					</span>
					<td width="40per">&nbsp;●</td> 
				</td>
				<td width="70per">
					<center><b>
					<span>
						<?php echo $sectioncount[6];?>
					</span>&nbsp;<span title="Times">
						<?php echo fnDisplyCharacter("Tim..", 0, 1); ?>
					</span>
					</b></center>
				</td>
				<td rowspan="5" style="border-top:hidden;font-size: 15px;">
					<ol style="float:leftfont-family: ＭＳ ゴシック;font-size: 15px;">
						<li> {{ trans('messages.ts_explanation_1') }} </li><br/>
						<li> {{ trans('messages.ts_explanation_2') }} </li><br/>
						<li> {{ trans('messages.ts_explanation_3') }} </li>
					</ol>			
				</td>
			</tr>
			<tr class="">
				<td class="">
					<span title = "{{ trans('messages.lbl_halfholdy') }}">
						@if(singlefieldlength(trans('messages.lbl_halfholdy'),4))
			            	{{singlefieldlength(trans('messages.lbl_halfholdy'),4)}}
			            @endif
					</span>
					<td class="" style="width:2%;align:right;">&nbsp;△
				</td></td>
				<td class="content_td box4per"><center><b><span id="leave2">
				<?php echo $sectioncount[2];?></span>&nbsp;
				<span title="Times">
					<?php echo fnDisplyCharacter("Tim..", 0, 1); ?></span>
					</b></center></td>
				<td class="content_td border_right_hidden box6per">
					<span title = "{{ trans('messages.lbl_trholydy') }}">
						@if(singlefieldlength(trans('messages.lbl_trholydy'),4))
			            	{{singlefieldlength(trans('messages.lbl_trholydy'),4)}}
			            @endif
					</span>
				<td class="content_td" style="width:2%;align:right;">&nbsp;◎</td></td>
				<td class="content_td box4per"><center><b><span id="leave7"><?php echo $sectioncount[7];?></span>&nbsp;<span title="Times">
				<?php echo fnDisplyCharacter("Tim..", 0, 1); ?></span></b></center>
				</td>
			</tr>
			<tr class="height25 CMN_bg_white">
				<td class="content_td border_right_hidden box6per">
					<span title = "{{ trans('messages.lbl_absence') }}">
						@if(singlefieldlength(trans('messages.lbl_absence'),4))
			            	{{singlefieldlength(trans('messages.lbl_absence'),4)}}
			            @endif
					</span>
				<td class="content_td box2per" style="align:right;">&nbsp;×</td></td>
				<td class="content_td box4per"><center><b><span id="leave3">
				<?php echo $sectioncount[3];?></span>&nbsp;<span title="Times">
				<?php echo fnDisplyCharacter("Tim..", 0, 1); ?>
				</span></b></center></td>
				<td class="content_td border_right_hidden box6per">
					<span title = "{{ trans('messages.lbl_splholydy') }}">
						@if(singlefieldlength(trans('messages.lbl_splholydy'),4))
			            	{{singlefieldlength(trans('messages.lbl_splholydy'),4)}}
			            @endif
					</span>
				<td class="content_td" style="width:2%;align:right;">&nbsp;☆</td></td>
				<td class="content_td box4per"><center><b><span id="leave8"><?php echo $sectioncount[8];?></span>&nbsp;
				<span title="Times">
				<?php echo fnDisplyCharacter("Tim..", 0, 1); ?></span></b></center>
				</td>
			</tr>
			<tr class="height25 CMN_bg_white">
				<td class="content_td border_right_hidden box6per">
					<span title = "{{ trans('messages.lbl_lateleave') }}">
						@if(singlefieldlength(trans('messages.lbl_lateleave'),4))
			            	{{singlefieldlength(trans('messages.lbl_lateleave'),4)}}
			            @endif
					</span>
				<td class="content_td" style="width:2%;align:right;">&nbsp;▲</td></td>
				<td class="content_td box4per"><center><b><span id="leave4"><?php echo $sectioncount[4];?></span>&nbsp;<span title="Times">
				<?php echo fnDisplyCharacter("Tim..", 0, 1); ?></span></b></center>
				</td>
				<td class="content_td border_right_hidden box6per">
					<span title = "{{ trans('messages.lbl_publcholydy') }}">
						@if(singlefieldlength(trans('messages.lbl_publcholydy'),4))
			            	{{singlefieldlength(trans('messages.lbl_publcholydy'),4)}}
			            @endif
					</span>
				<td class="content_td" style="width:2%;align:right;">&nbsp;★</td></td>
				<td class="content_td box4per"><center><b><span id="leave9">
				<?php echo $sectioncount[9];?></span>&nbsp;
				<span title="Times">
				<?php echo fnDisplyCharacter("Tim..", 0, 1); ?></span></b></center>
				</td>
			</tr>
			<tr class="height25 CMN_bg_white">
				<td class="content_td border_right_hidden box6per">
				{{ trans('messages.lbl_Others') }}<td class="content_td" style="width:2%;align:right;">&nbsp;□</td></td>
				<td class="content_td box4per"><center><b><span id="leave5">
				<?php echo $sectioncount[5];?></span>&nbsp;
				<span title="Times">
				<?php echo fnDisplyCharacter("Tim..", 0, 1); ?></span></b></center>
				</td>
				<td colspan="2" class="box6per">&nbsp;
				<a href="javascript:void(0);" class="btn btn-primary box70 ml8" style="padding:1px;"><span class=""></span> Check </a>
				<td class="content_td box4per tac" style="font-size:24;"><b>○</b></td>
			</tr>
		</table>
		<div class="box100per mt10 cursor">
			<div style="float:left;cursor:default;float:leftfont-family: ＭＳ ゴシック;font-size: 12;">
			<?php echo date('Y/m/d');?>{{ trans('messages.lbl_edition') }}</div>
		
		<div class="col-xs-3 pull-right mb10 pr1 mr5">	
			<table class="ml10 box100per">
				<tr style="background-color:#B0E0F2;font-weight: bold;font-size: 15px;">
					<td class="pagelast_td" colspan="6"><center>
					<span title = "">
					@if(singlefieldlength(trans('messages.lbl_aprltmestmp'),14))
			            {{singlefieldlength(trans('messages.lbl_aprltmestmp'),14)}}
			        @endif
					</span></center></td>
				</tr>
				<tr style="background-color:#B0E0F2;font-weight: bold;font-size: 15px;">	
					<td class="pagelast_td" colspan="3"><center>
					<span title = "{{ trans('messages.lbl_directors') }}">
					@if(singlefieldlength(trans('messages.lbl_directors'),14))
			            {{singlefieldlength(trans('messages.lbl_directors'),14)}}
			        @endif
					</span></center>
					</td>
					<td class="pagelast_td" colspan="3"><center>
					<span title = "">
					@if(singlefieldlength(trans('messages.lbl_pl'),14))
			            {{singlefieldlength(trans('messages.lbl_pl'),14)}}
			        @endif
					</span></center></td>
				</tr>
				<tr>
					<td class="content_td" style="height:100" colspan="3"></td>
					<td class="content_td" colspan="3"></td>
				</tr>	
			</table>
		</div>
		</div>
	</div>
	{{ Form::close() }}
</article>
</div>
<div id="timesheetdetails" class="modal fade">
    <div id="login-overlay">
        <div class="modal-content">
            <!-- Popup will be loaded here -->
        </div>
    </div>
</div>
@endsection
@php
	function fnDisplyCharacter($string, $start, $length) {

		if (Session::get('languageval') == "en") {
			$string = mb_substr($string, $start, $length) . "..";
		} 
		return $string;
	}
@endphp
