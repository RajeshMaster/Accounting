@extends('layouts.app')
@section('content')
<?php $idcon=""; ?>
{{ HTML::script('resources/assets/js/staffContract.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
	$(document).ready(function() {
		setDatePicker("opd");
	});
$(document).ready(function() {
	 $('salary').blur(function() {
		    $('.salary').formatCurrency();
		});
});
</script>
<script type="text/javascript">
	$(document).ready(function() {
	// initialize tooltipster on text input elements
	// initialize validate plugin on the form
	$('.addeditprocess').click(function () {
		$("#staffContaddedit").validate({
			showErrors: function(errorMap, errorList) {
			// Clean up any tooltips for valid elements
				$.each(this.validElements(), function (index, element) {
						var $element = $(element);
						$element.data("title", "") // Clear the title - there is no error associated anymore
								.removeClass("error")
								.tooltip("destroy");
				});
				// Create new tooltips for invalid elements
				$.each(errorList, function (index, error) {
						var $element = $(error.element);
						$element.tooltip("destroy") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
								.data("title", error.message)
								.addClass("error")
								.tooltip(); // Create a new tooltip based on the error messsage we just set in the title
				});
			},
			rules: {
				StartDate: {required: true, date: true,correctformatdate: true},
				EndDate: {greaterThanStartdate: "#StartDate"},
				Salary: {required: true,number: true},
				Contract_date: {required: true, date: true,correctformatdate: true,lessThanStartdate : "#StartDate"},
			},
			submitHandler: function(form) { // for demo
				if($('#rid').val() == "1") {
					var confirmprocess = confirm(err_confreg);
				} else {
					var confirmprocess = confirm(err_confup);
				}
				 if(confirmprocess) {
					pageload();
					//form.submit();
					return true;
				} else {
					return false;
				}
			}
		});
		$.validator.messages.required = function (param, input) {
            var article = document.getElementById(input.id);
            return article.dataset.label + ' field is required';
        }
        $.validator.messages.minlength = function (param, input) {
          var article = document.getElementById(input.id);
          return "Atleast Enter 6 Numbers";
        }
        $.validator.messages.number = function (param, input) {
          var article = document.getElementById(input.id);
          return "Please Enter Numbers Only";
        }
	});
});

	// function fnContractDate() {
	// var startdate = document.getElementById('StartDate').value;
	// alert(startdate);
	// var enddate = document.getElementById('EndDate').value;
	// alert(enddate);
	// var id = document.getElementById('editid').value;
	// alert(id);
	// var empid = document.getElementById('viewid').value;
	// alert(empid);
	//  $.ajax({
 //      type: 'GET',
 //      dataType: "JSON",
 //      url: 'cdate_ajax',
 //      data: {"startdate": startdate,"enddate": enddate,"id": id,"empid": empid},
 //      success: function(resp) {
 //        alert(resp);
 //      },
 //      error: function(data) {
 //        alert(data.status);
 //        $("#regbutton").attr("data-dismiss","modal");
 //      }
 //    });
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
<article id="staff" class="DEC_flex_wrapper " data-category="staff staff_sub_2">
@if($request->rid == '2')
	{{ Form::model($edit_query, array('name'=>'staffContaddedit', 'id'=>'staffContaddedit', 'files'=>true,'type'=>'file', 'method' => 'POST','class'=>'form-horizontal','url' => 'StaffContr/staffContaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis')) ) }}
	  {{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
@else
	{{ Form::open(array('name'=>'staffContaddedit','id'=>'staffContaddedit', 
			'url' => 'StaffContr/staffContaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
			'files'=>true,'method' => 'POST')) }}
			  {{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
@endif
	{{ Form::hidden('viewid',$request->empnoadd, array('id' => 'viewid')) }} 
	{{ Form::hidden('Name', $request->Name, array('id' => 'Name')) }}
	{{ Form::hidden('rid', $request->rid, array('id' => 'rid')) }}
	{{ Form::hidden('empnoadd', '', array('id' => 'empnoadd')) }} 
	{{ Form::hidden('radio_emp', '', array('id' => 'radio_emp')) }}
	{{ Form::hidden('radio', '', array('id' => 'radio')) }}
	{{ Form::hidden('total', '', array('id' => 'total')) }}
	{{ Form::hidden('empname','', array('id' => 'empname')) }}
	
<!-- Start Heading -->
	<div class="row hline">
	<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/contractImg.png') }}">
			@if($request->rid == 3)
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_contOfEmpl') }}<span>・</span><span class="colbl">{{ trans('messages.lbl_view') }}</span></h2>
			@elseif($request->rid == 2)
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_contOfEmpl') }}<span>・</span><span class="red">{{ trans('messages.lbl_edit') }}</span></h2>
			@else
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_contOfEmpl') }}<span>・</span><span class="green">{{ trans('messages.lbl_register') }}</span></h2>
			@endif


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
			@if($request->rid == 3)
				<div class="pull-left ml5">
					<a href="javascript:godetailspage('{{ $request->mainmenu }}');" class="pageload btn btn-info box80"><span class="fa fa-arrow-left"></span> {{ trans('messages.lbl_back') }}</a>
				</div>
			@else
			@endif	
			<div class="pull-right mr5">
			</div>
			@if($request->rid == 3)
			<div class="pull-right mr10">
				<a href="javascript:contractemployeeedit('{{ $request->empnoadd }}','{{ $request->radio_emp }}');" class="btn btn-warning box80 pull-right pr10"><span class="fa fa-pencil"></span><span class="ml3">{{ trans('messages.lbl_edit') }}</span></a>
			</div>
			@endif
		@if($request->rid == 3)
			<div class="col-xs-12 pl5 pr5">
		@else
			<div class="col-xs-12 pl5 pr5" style="margin-top: -10px;">
		@endif	
	<fieldset class="mt10">
		<div class="box60per CMN_display_block mt5">
			<div class="col-xs-12 mt10">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_periodofWork') }}</label>
					@if($request->rid != 3)
					<span class="fr ml2 red"> * </span>
					@endif
				</div>
				<div>
					@if($request->rid == 3)
					<span class="ml5">
					{{ $edit_query[0]->StartDate }} ~ {{ $edit_query[0]->EndDate }}
					</span>
					@else
					<span class="CMN_display_block box14per ml2">
					{{ Form::text('StartDate',(isset($edit_query[0]->StartDate)?$edit_query[0]->StartDate:""),array('id'=>'StartDate', 'name' => 'StartDate', 'maxlength' => '10','data-label' => trans('messages.lbl_Start_date'),'class'=>'box100per form-control pl5 opd'
					,'onkeypress' => 'fnCancel_check()')) }}
					</span>
					<label class="mt10 ml2 fa fa-calendar fa-lg CMN_display_block pr5" 
					for="StartDate" aria-hidden="true" style="display: inline-block!important;">
					</label>
					<span class="CMN_display_block box14per" 
					style="display: inline-block!important;">
                      {{ Form::text('EndDate',(isset($edit_query[0]->EndDate)?$edit_query[0]->EndDate:""),array('id'=>'EndDate', 'name' => 'EndDate','data-label' => trans('messages.lbl_End_date'),'class'=>'box100per form-control pl5 opd','readonly')) }}
                   </span>
					@endif
				</div>
			</div>
			<div class="col-xs-12 mt5">
				@if($request->rid == 3)
					<div class="col-xs-3 text-right clr_blue box38per">
				@else
					<div class="col-xs-3 text-right clr_blue box40per ml3">
				@endif		
					<!-- <label>{{ trans('messages.lbl_contract_Period') }}</label> -->
					<span class="fr ml3 red" style="visibility: hidden;"> * </span>
				</div>
				@if($request->rid != 3)
				<div class="box55per ml5">
					{{ Form::checkbox('year',null,null, array('id'=>'year',
												'onclick' => 'javascript:test();',
												 'checked' => 'checked')) }}
					<span class="CMN_display_block box10per">
					@if(!empty($edit_query[0]->EndDate)&& ($edit_query[0]->StartDate))
						{{--*/ $contractEnd = explode('-',$edit_query[0]->EndDate);
						 			   $contract_eyr = $contractEnd[0];
						 			   $contractStart =explode('-', $edit_query[0]->StartDate);
						 			   $contract_syr = $contractStart[0];
						 			   $difference = $contract_eyr-$contract_syr;
						 			   $diff =  $difference;/*--}}
						 {{ Form::text('numyear',$diff,array('id'=>'numyear', 'name' => 'numyear','class'=>'box100per form-control pl5',
						 'onkeypress' => 'return isNumberKey(event);',
						 'onkeyup' => "return add_date();",
						 'style' => 'ime-mode:disabled;')) }}
					 @else
						 	{{ Form::text('numyear','',array('id'=>'numyear', 'name' => 'numyear','class'=>'box100per form-control pl5',
						 'onkeypress' => 'return isNumberKey(event);',
						 'onkeyup' => "return add_date();",
						 'style' => 'ime-mode:disabled;')) }}
					 @endif
					</span>
					<span class="fwb"> 年</span>
				</div>
				@else
				<div class="col-xs-3 text-left box50per ml5">
					{{ $numyear }}<span class="fwb"> 年</span>
				</div>
				@endif
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_salary') }}</label>
					@if($request->rid != 3)
					<span class="fr ml2 red"> * </span>
					@endif
				</div>
				<div class="tar box56per">
				@if($request->rid == 3)
					{{ $edit_query[0]->Salary }}
					<span class="fwb"> 円</span>
				@else
					<span class="CMN_display_block box20per mr11">
					{{ Form::text('Salary',(isset($edit_query[0]->Salary)?$edit_query[0]->Salary:""),array('id'=>'Salary', 'name' => 'Salary', null,'class'=>'box100per form-control tar salary','autocomplete' => 'off','onkeypress' => 'return isNumberKey(event);','data-label' => trans('messages.lbl_salary'),
					'onkeyup' => "allowancecal(),fnMoneyFormat(this.name,'jp'), fnCancel_check()",'maxlength' => '7')) }}
					</span>
					<span class="fwb"> 円</span>
				@endif
				</div>
			</div>
			@if($request->rid == 3 && $request->rid == 2)
			{{--*/ $i = 1;
				   $tval = $edit_query[0]->Salary;
				   $row_count = Count($get_tabFld); /*--}}
			@else
			{{--*/ $i = 1;
				   $row_count = Count($get_tabFld); /*--}}
			@endif 
			{{ Form::hidden('allowancecount',$row_count , array('id' => 'allowancecount')) }}
			@if($row_count!="")
			<?php $allcnt=1; ?>
			@for ($cnt = 0; $cnt < $row_count; $cnt++)
			@if($get_tabFld[$cnt]['delflg'] == 0)
			<div class="col-xs-12 box100per mt5" >
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ $get_tabFld[$cnt]['allowance_lan'] }}</label>
				@if($request->rid != 3)	
					<span class="fr ml2 red" style="visibility: hidden;"> * </span>
				@endif
				</div>
				<div class="box56per tar">
				{{--*/ $tempall="Allowance".$i; /*--}}
				@if($request->rid == 3)
					@if(!empty($edit_query[0]->$tempall))
					 {{$tval= $edit_query[0]->$tempall }} 
					<span class="fwb"> 円</span>
					@else
					 {{ 0 }}
					<span class="fwb"> 円</span>
					@endif
				@else
				<span class="CMN_display_block box20per mr11">
					{{ Form::text('allowance_'.$allcnt,(isset($edit_query[0]->$tempall)?$edit_query[0]->$tempall:""),array('id'=>'allowance_'.$allcnt, 'name' => 'allowance_'.$allcnt, null,'class'=>'box200per tar form-control pl5','autocomplete' => 'off','onkeypress' => 'return isNumberKey(event);',
					'onkeyup' => "allowancecal(),fnMoneyFormat(this.name,'jp'), fnCancel_check()",'maxlength' => '7')) }}
					<?php  $allcnt =  $allcnt+1; ?>
				</span><span class="fwb"> 円</span>
				@endif
				</div>
			</div>
			{{--*/ $idcon .= $i."-";  /*--}}
			@endif
			{{--*/ $i++; /*--}}
			@endfor
			@endif
			<div class="col-xs-12 box100per mt5">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_total') }}</label>
					@if($request->rid != 3)	
					<span class="fr ml2 red" style="visibility: hidden;"> * </span>
					@endif
				</div>
				<div class="box56per tar">
				@if($request->rid == 3)
					{{--*/ $Others = number_format(str_replace(',','',$edit_query[0]->Salary)
											+str_replace(',','',$edit_query[0]->Allowance1)
											+str_replace(',','',$edit_query[0]->Allowance2)
											+str_replace(',','',$edit_query[0]->Allowance3)
											+str_replace(',','',$edit_query[0]->Allowance4)
											+str_replace(',','',$edit_query[0]->Allowance5)
											+str_replace(',','',$edit_query[0]->Allowance6)
											+str_replace(',','',$edit_query[0]->Allowance7)
											+str_replace(',','',$edit_query[0]->Allowance8)
											+str_replace(',','',$edit_query[0]->Allowance9)
											+str_replace(',','',$edit_query[0]->Allowance10)); /*--}}
					{{ $Others }}
					<span class="fwb"> 円</span>
				@else
					<span id="allow" class="mr10">
						@if(!empty($totallowance))
							{{$totallowance}}
						@else
							{{ 0 }}
						@endif
					</span>
					<span class="fwb"> 円</span>
				@endif
				</div>
			</div>
			<div class="col-xs-12 box100per mt5">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_contractDate') }}</label>
					@if($request->rid != 3)
					<span class="fr ml2 red"> * </span>
					@endif
				</div>
				@php $date1 = date('Y-m-d') @endphp
				<div  class="box60per">
				@if($request->rid == 3)
					<span class="ml5">
						{{ $edit_query[0]->Contract_date }}
					</span>
				@else
					<span class="CMN_display_block box23per">
                      {{ Form::text('Contract_date',(isset($edit_query[0]->Contract_date)?$edit_query[0]->Contract_date:"$date1"),array('id'=>'Contract_date', 'name' => 'Contract_date','data-label' => trans('messages.lbl_contractDate'),'class'=>'box100per form-control pl5 opd','onblur' => 'return fnCancel_check();'))  }}
                   	</span>
				<!-- <span class="CMN_display_block box21per ml2">
					{{ Form::text('Contract_date',(isset($edit_query[0]->Contract_date)) ? $edit_query[0]->Contract_date : "$date1",array('id'=>'Contract_date', 'name' => 'Contract_date','maxlength' => '10', 'data-label' => trans('messages.lbl_contractDate'),null,'class'=>'box100per pl5 opd',
					'onblur' => 'return fnCancel_check();')) }}
					</span> --> 
					<label class="mt10 ml2 fa fa-calendar fa-lg CMN_display_block pr5" 
					for="Contract_date" aria-hidden="true" style="display: inline-block!important;">
					</label>
				@endif
				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue box40per">
					<label>{{ trans('messages.lbl_remarks') }}</label>
					@if($request->rid != 3)	
					<span class="fr ml2 red" style="visibility: hidden;"> * </span>
					@endif
				</div>
				<div>
				@if($request->rid == 3)
					<span class="ml5">
						{{ ($edit_query[0]->Remarks != "") ? $edit_query[0]->Remarks : 'Nill'}}
					</span>
				@else
				<span class="CMN_display_block box21per ml2">
					{{ Form::textarea('Remarks', (isset($edit_query[0]->Remarks)?$edit_query[0]->Remarks:""),array('id'=>'Remarks', 
											'name' => 'Remarks',
											'data-label' => trans('messages.lbl_remarks'),
											'style' => 'height:60px;width:260px;',
											'onkeyup' => 'fnCancel_check()')) }}
				</span>
				@endif
				</div>
			</div>
		</div>
	{{ Form::hidden('edit_id',  $idcon , array('id' => 'edit_id')) }}
	</fieldset>
	</div>
	@if($request->rid != 3)
	<fieldset style="background-color: #DDF1FA;">
		<div class="form-group mt15">
			<div align="center" class="mt5">
				@if($request->rid == 2)
				 <button type="submit" class="btn edit btn-warning box100 addeditprocess" >
                        <i class="fa fa-edit" aria-hidden="true"></i><span class="ml3"> {{ trans('messages.lbl_update') }}</span>
                </button>
				@else
				<button type="submit" class="btn btn-success add box100 addeditprocess" >
					<i class="fa fa-plus ml2"></i> {{ trans('messages.lbl_register') }}
				</button>
				@endif
				<a onclick="javascript:gotoindexpage('2','{{ $request->mainmenu }}');" class="btn btn-danger box120 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_cancel')}}
				</a>
			</div>
		</div>
		<div class="CMN_display_block pb10"></div>
	</fieldset>
	@endif
</div>
{{ Form::close() }}
{{ Form::open(array('name'=>'frmcontractaddeditcancel', 'id'=>'frmcontractaddeditcancel', 'url' => 'StaffContr/staffContaddeditprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
                {{ Form::hidden('editid', $request->editid, array('id' => 'editid')) }}
				{{ Form::hidden('viewid',$request->empnoadd, array('id' => 'viewid')) }} 
				{{ Form::hidden('Name', $request->Name, array('id' => 'Name')) }}
				{{ Form::hidden('rid', '', array('id' => 'rid')) }}
				{{ Form::hidden('empnoadd', '', array('id' => 'empnoadd')) }} 
				{{ Form::hidden('radio_emp', '', array('id' => 'radio_emp')) }}
				{{ Form::hidden('radio', '', array('id' => 'radio')) }}
				{{ Form::hidden('total', '', array('id' => 'total')) }}
    {{ Form::close() }}
</article>
</div>
<div class="CMN_display_block pb10"></div>
<script type="text/javascript">
	function formatNumber(nStr) {
	      nStr += '';
     x = nStr.split('.');
     x1 = x[0];
     x2 = x.length > 1 ? '.' + x[1] : '';
     var rgx = /(\d+)(\d{3})/;
     var z = 0;
     var len = String(x1).length;
     var num = parseInt((len/2)-1);
 
      while (rgx.test(x1))
      {
        if(z > 0)
        {
          x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        else
        {
          x1 = x1.replace(rgx, '$1' + ',' + '$2');
          rgx = /(\d+)(\d{2})/;
        }
        z++;
        num--;
        if(num == 0)
        {
          break;
        }
      }
     return x1 + x2;
	}
</script>
@endsection