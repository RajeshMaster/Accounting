@extends('layouts.app')
@section('content')
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
{{ HTML::script('resources/assets/js/mail.js') }}
<div class="CMN_display_block box100per" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper " data-category="sales sales_sub_6">
	{{ Form::open(array('name'=>'frmestimationindex', 
						'id'=>'frmestimationindex', 
						'url' => 'Estimation/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		
		{{ Form::hidden('sample', '', array('id' => 'sample')) }}
	<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt15" src="{{ URL::asset('resources/assets/images/mail.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_maillist') }}</h2>
		</div>
	</div>
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
	<div class="CMN_display_block pl500">
		<h2>Under Construction...!!!</h4>
	</div>
	
</article>
</div>
@endsection