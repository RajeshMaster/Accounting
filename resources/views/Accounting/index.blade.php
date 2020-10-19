@extends('layouts.app')
@section('content')
@php use App\Http\Helpers; @endphp
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
	$(document).ready(function() {
		setDatePicker("from_date");
		setDatePicker("to_date");
	});
		function mulclick(divid){
	    if($('#'+divid).css('display') == 'block'){
	      document.getElementById(divid).style.display = 'none';
	    }else {
	      document.getElementById(divid).style.display = 'block';
	    }
  }
</script>
<style type="text/css">
	.alertboxalign {
    	margin-bottom: -60px !important;
	}
	.alert {
		margin-top: 10px;
	    display:inline-block !important;
	    height:30px !important;
	    padding:5px !important;
	}
	.fb{
		color: gray !important;
	}
	.sort_asc {
		background-image:url({{ URL::asset('resources/assets/images/upArrow.png') }}) !important;
	}
	.sort_desc {
		background-image:url({{ URL::asset('resources/assets/images/downArrow.png') }}) !important;
	}
	.scrollbar
  	{
    float: left;
    max-height: 485px;
    width: 270px;
    overflow-x: hidden !important;
    overflow-y: scroll !important;
    margin-bottom: 5px;
  	}
	/* Dropdown Button */
	.dropbtn {
	    background-color: #4CAF50;
	    color: white;
	    padding: 16px;
	    font-size: 16px;
	    border: none;
	    cursor: pointer;
	}

	/* The container <div> - needed to position the dropdown content */
	.dropdown {
	    position: relative;
	    display: inline-block;
	}

	/* Dropdown Content (Hidden by Default) */
	.dropdown-content {
	    display: none;
	    position: absolute;
	    background-color: #f9f9f9;
	    min-width: 160px;
	    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
	    z-index: 1;
	}

	/* Links inside the dropdown */
	.dropdown-content a {
	    color: black;
	    padding: 5px 7px;
	    text-decoration: none;
	    display: block;
	}

	/* Change color of dropdown links on hover */
	.dropdown-content a:hover {background-color: #e5f4f9}

	/* Show the dropdown menu on hover */
	.dropdown:hover .dropdown-content {
	    display: block;
	}

	/* Change the background color of the dropdown button when the dropdown content is shown */
	.dropdown:hover .dropbtn {
	    background-color: #3e8e41;
	}
	.border_btm_solid_line{
	border-bottom:1px solid #A7D4DD;
	}
	/*.collapse {
    display: none ;
	}
	.collapse.in {
    display: block ;
	}*/
</style>
{{ HTML::script('resources/assets/js/invoice.js') }}
{{ HTML::script('resources/assets/js/switch.js') }}
{{ HTML::script('resources/assets/js/hoe.js') }}
{{ HTML::style('resources/assets/css/extra.css') }}
{{ HTML::style('resources/assets/css/hoe.css') }}
{{ HTML::style('resources/assets/css/switch.css') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
<div class="CMN_display_block" id="main_contents" style="width: 100%">
<!-- article to select the main&sub menu -->
<article id="accounting" class="DEC_flex_wrapper " data-category="accounting accounting_sub_1">
	{{ Form::open(array('name'=>'frminvoiceindex', 
						'id'=>'frminvoiceindex', 
						'url' => 'Invoice/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('filter', $request->filter, array('id' => 'filter')) }}
		{{ Form::hidden('plimit', $request->plimit , array('id' => 'plimit')) }}
	    {{ Form::hidden('page', $request->page , array('id' => 'page')) }}
	    {{ Form::hidden('mainmenu', $request->mainmenu, array('id' => 'mainmenu')) }}
		{{ Form::hidden('sortOptn',$request->invoicesort , array('id' => 'sortOptn')) }}
	    {{ Form::hidden('sortOrder', $request->sortOrder , array('id' => 'sortOrder')) }}
		{{ Form::hidden('searchmethod', $request->searchmethod, array('id' => 'searchmethod')) }}

<!-- Start Heading -->
	<div class="row hline" >
		<div class="col-xs-12">
			<img class="pull-left box35 mt10" src="{{ URL::asset('resources/assets/images/invoices-icon-3.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_accounting') }}</h2>
		</div>
	</div>
	

	
</article>
</div>

@endsection