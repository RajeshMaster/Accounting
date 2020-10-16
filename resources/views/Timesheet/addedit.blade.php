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
	.fb{
		color: gray !important;
	}
	.trhide { display: none ; }
  	.trshow { display: block ; }
</style>
<div class="CMN_display_block" id="main_contents" >
<!-- article to select the main&sub menu -->
<article id="staff" class="DEC_flex_wrapper " data-category="staff staff_sub_3">
	@if(isset($value))
	{{ Form::model($value, array('name'=>'timesheetadd', 
						'id'=>'timesheetadd', 
						'url' => 'Timesheet/addedit?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	@else
	{{ Form::open(array('name'=>'timesheetadd', 
						'id'=>'timesheetadd', 
						'url' => 'Timesheet/addedit?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	@endif
	<!-- specification label purpose -->
	<!-- Reg Update purpose -->
	{{ Form::hidden('flag', $request->flag, array('id' => 'flag')) }}
	<!-- Reg Update label button purpose -->
	{{ Form::hidden('empid', $Emp_ID , array('id' => 'empid')) }}
	{{ Form::hidden('selMonth', $request->selMonth , array('id' => 'selMonth')) }}
	{{ Form::hidden('selYear', $request->selYear , array('id' => 'selYear')) }}
	{{ Form::hidden('flagvalue', $request->flagval , array('id' => 'flagvalue')) }}
	{{ Form::hidden('filtervalue', $request->filtervalue, array('id' => 'filtervalue')) }}
	<div class="row hline">
		<div class="col-xs-12 pl5">
			<div class="col-xs-6 pull-left">
				<img class="pull-left box30 mt10" src="{{ URL::asset('resources/assets/images/timesheet.jpg') }}">
				<h2 class="pull-left pl5 mt10">{{ trans('messages.lbl_timesheet') }}</h2>
				<h2 class="pull-left mt15">・</h2>
				@if($request->flagval == "1")
					<h2 class="pull-left mt10 green">{{ trans('messages.lbl_register') }}</h2>
				@else 
					<h2 class="pull-left mt10 red">{{ trans('messages.lbl_edit') }}</h2>
				@endif
			</div>
		</div>
	</div>
	<div class="pb5"></div>
	<!-- End Heading -->
	<div class="col-xs-6 pull-right mb10 pr1">
		<a href="javascript:specfy('{{ 3 }}');" class="pull-right pr10 pm0 btn btn-link {{ $disabledsymbol }}">{{ trans('messages.lbl_symbol') }}</a>
		<span class="pull-right pr10 pm0">|</span>
		<a href="javascript:specfy('{{ 2 }}');" class="pull-right pr10 pm0 btn btn-link {{ $disabledjp }}">{{ trans('messages.lbl_jp') }}</a>
		<span class="pull-right pr10 pm0">|</span>
		<a href="javascript:specfy('{{ 1 }}');" 
    		class="pull-right pr10 btn btn-link {{ $disabledeng }} pm0">
    	{{ trans('messages.lbl_en') }}</a>
		<span class="pull-right mr10">{{ trans('messages.lbl_specification') }}</span>
	</div>
	<div class="mr10 ml13">
		<center><h2></h2></center>
		<table class="box100per" style="table-layout: fixed;">
			<tr style="background-color:#B0E0F2;font-weight: bold;font-size: 15px" >
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
			<tr style="font-size: 15px;">
				<td> 
					<center>
						{{ $yr }}
					</center> 
				</td>
				<td> 
					<center>
						{{ $mon }}
					</center> 
				</td>
				<td> 
					<center>
						{{ $Emp_ID }}
					</center> 
				</td>
				<td colspan="2"> 
					<center>
						{{ $Name }}
					</center> 
				</td>
				<td></td>
			</tr>
            
			<tr style="background-color:#B0E0F2;font-weight: bold;font-size: 15px;">
				<td rowspan="2">
					<center>
						<span>
							{{ mb_substr(trans('messages.lbl_date'),0,6,'utf-8') }}
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
				<td rowspan="2" colspan="3">
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
				<td rowspan="2" colspan="4">
					<center>
						{{ trans('messages.lbl_remarks') }}
					</center>
				</td>
				<td rowspan="2">
					<center>
						{{ trans('messages.lbl_process') }}
					</center>
				</td>
			</tr>
			<tr style="background-color:#B0E0F2;font-weight: bold;font-size: 15px;">
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
			@php
	            if($request->flagval == "1") {
	              $disp = "trshow";
	            } else {
	              $disp = "trhide";
	            }
	          @endphp
			<?php for ($i=1; $i <= $number; $i++) {
					if ($displayArray[$i]["leaveDisplay"] == 1) {?>
				<tr style="background-color:#E5F4F9;height: 45px !important;">
					<?php } else {?>
				<tr style="height: 45px !important;">
				<?php }?>
					<td class="tac">
						<?php echo $i.$youbi[date('N', $displayArray[$i]['timestamp'])];?>
					</td>
					<td>
					{{ Form::select('classification.$i',[null=>''] + $sprow , 
					(!empty($value['classification'.$i])) ?  $value['classification'.$i] : '',
										array( 'id'=>'classification'.$i,
										'name' => 'classification'.$i,
										'class' => 'width-auto box100per',
										'onchange' => 'javascript:fndisablechargefield();',
										'data-label' => trans('messages.lbl_bank'))) }}
					</td>
					<td colspan="3">
						<span id="@php echo "labelworktxt".$i; @endphp" name="@php echo "labelworktxt".$i; @endphp">{{ (!empty($value['worktxt'.$i])) ?  $value['worktxt'.$i] : '' }}</span>
						{{ Form::text('worktxt'.$i,old('worktxt'),
								array('class' => 'field box100per '.$disp, 
										'id'=>'worktxt'.$i)) }}
					</td>
					<td class="tac">
						<span id="@php echo "labelstart1".$i; @endphp" name="@php echo "labelstart1".$i; @endphp">
							{{ (!empty($value['start1'.$i])) ?  $value['start1'.$i] : '' }}
						</span>
						{{ Form::text('start1'.$i, old('start1'),
								array('class' => 'field text-center box100per '.$disp,
										'id'=>'start1'.$i,
										'maxlength' => '5',
										'onkeypress'=> "return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57",
										'onkeyup' => 'return isTimeColon(this.id,event);') ) }}
					</td>
					<td class="tac">
						<span id="@php echo "labelend1".$i; @endphp" name="@php echo "labelend1".$i; @endphp">
							{{ (!empty($value['end1'.$i])) ?  $value['end1'.$i] : '' }}
						</span>
						{{ Form::text('end1'.$i,old('end1'),
								array('class' => 'field text-center box100per '.$disp,
											'id'=>'end1'.$i, 
											'maxlength' => '5',
											'onkeypress'=> "return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57",
											'onkeyup' => 'return isTimeColon(this.id,event);') ) }}
					</td>
					<td class="tac">
						<span id="@php echo "labelstart2".$i; @endphp" name="@php echo "labelstart2".$i; @endphp"></span>
						{{ Form::text('start2'.$i,old('start2'),
								array('class' => 'field text-center box100per '.$disp,
										'id'=>'start2'.$i, 
										'maxlength' => '5',
										'disabled' => 'disabled',
										'onkeypress'=> "return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57",
										'onkeyup' => 'return isTimeColon(this.id,event);') ) }}
					</td>
					<td class="tac">
						<span id="@php echo "labelend2".$i; @endphp" 
						name="@php echo "labelend2".$i; @endphp"></span>
						{{ Form::text('end2'.$i,old('end2'),
								array('class' => 'field text-center box100per '.$disp,
										'id'=>'end2'.$i, 
										'maxlength' => '5',
										'disabled' => 'disabled',
										'onkeypress'=> "return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57",
										'onkeyup' => 'return isTimeColon(this.id,event);') ) }}
					</td>
					<td colspan="4">
						<span id="@php echo "labelremarks".$i; @endphp" 
						name="@php echo "labelremarks".$i; @endphp">
							{{ (!empty($value['remarks'.$i])) ?  $value['remarks'.$i] : '' }}
						</span>
						{{ Form::textarea('remarks'.$i,old('remarks'),
								array('class' => 'field input-sm box100per '.$disp,
										'style'=> 'height:30px;',
										'id'=>'remarks'.$i,
										'size' => '6x1')) }}
					</td>
					<td style="font-size: 13px">
						@if($flagvalue == "1")
						<img class="pull-left box12 ml3 mr10 mt3" style="cursor: pointer;" id= "<?php echo "add".$i;?>" 
						name="<?php echo "add".$i;?>" src="{{ URL::asset('resources/assets/images/plus3.png') }}" onclick="add_timesheet('{{ $Emp_ID }}','{{ $i }}',
						'{{ 1 }}');">
						<input type="hidden" name="enable{{ $i }}" value="a" id="enable{{ $i }}">
						@else 
						<img class="pull-left box17 ml3 mr5 mt2" style="cursor: pointer;" id= "<?php echo "update".$i;?>" 
						name="<?php echo "update".$i;?>" src="{{ URL::asset('resources/assets/images/edit.png') }}" onclick="add_timesheet('{{ $Emp_ID }}','{{ $i }}','{{ 2 }}');">
						<input type="hidden" name="enable{{ $i }}" value="" id="enable{{ $i }}">
						@endif
						<input type="checkbox" class="vam mt1 mr5" name="paste" 
							id="<?php echo "paste".$i; ?>" <?php if(isset($value['classification'.$i])){ ?>disabled <?php }?> 
						onclick="checkboxpaste('<?php echo $i?>');">
						<a href="javascript:copy('{{ $i }}');" id="timesheetcopy{{ $i }}"	
							class="pt8 mr5 btn-link" style="color: blue;">{{ "C" }}</a>
						<a href="javascript:paste('{{ $i }}');" id="timesheetcopy{{ $i }}" 
						class="pt8 btn-link" style="color: blue;"">{{ "P" }}</a>
					</td>
			</tr>
			<?php } ?>
		</table>
	</div>
	<div class="form-group mt15">
			<div align="center" class="mt5">

			@if($request->flagval == "1")
					<button type="button" 
						onclick="addeditall('{{ $Emp_ID }}','{{ 1 }}','{{ $number }}');"  
						class="btn btn-success add box100" >
					<i class="glyphicon glyphicon-plus"></i> {{ trans('messages.lbl_register') }}
				</button>
				<a onclick="javascript:fncancelgoview('{{ $request->mainmenu }}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i>    {{trans('messages.lbl_cancel')}}
				</a>
			@elseif($request->flagval == "2")
					<button type="button" 
						onclick="addeditall('{{ $Emp_ID }}','{{ 2 }}','{{ $number }}');" 
						class="btn btn-warning btn fa fa-edit box100 addcopyprocess">
                        {{ trans('messages.lbl_update') }}
               		</button>
                	<a onclick="javascript:fncancel('{{ $request->mainmenu }}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
					</a>
			@else 
					<button type="button" onclick="addeditall('{{ $Emp_ID }}','{{ 2 }}',
					'{{ $number }}');" 
						class="btn btn-warning btn box100 fa fa-edit addcopyprocess"> 
                        {{ trans('messages.lbl_update') }}
               		</button>
					<a onclick="javascript:fncancelgoview('{{ $request->mainmenu }}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
					</a>
			@endif
				
				
			</div>
		</div>
{{ Form::close() }}
</article>
</div>
{{ Form::open(array('name'=>'timesheetsingleadd', 
						'id'=>'timesheetsingleadd', 
						'url' => 'Timesheet/addedit?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
	{{ Form::hidden('empid', $Emp_ID , array('id' => 'empid')) }}
	{{ Form::hidden('selMonth', $request->selMonth , array('id' => 'selMonth')) }}
	{{ Form::hidden('selYear', $request->selYear , array('id' => 'selYear')) }}
	{{ Form::hidden('seldate', $request->seldate , array('id' => 'seldate')) }}
	{{ Form::hidden('sectionhdn', $request->section , array('id' => 'sectionhdn')) }}
	{{ Form::hidden('worktxthdn', $request->worktxt , array('id' => 'worktxthdn')) }}
	{{ Form::hidden('start1hdn',$request->start1, array('id' => 'start1hdn')) }}
	{{ Form::hidden('end1hdn', $request->end1 , array('id' => 'end1hdn')) }}
	{{ Form::hidden('start2hdn', $request->start2 , array('id' => 'start2hdn')) }}
	{{ Form::hidden('end2hdn', $request->end2 , array('id' => 'end2hdn')) }}
	{{ Form::hidden('remarkshdn', $request->remarks , array('id' => 'remarkshdn')) }}
{{ Form::close() }}
@endsection