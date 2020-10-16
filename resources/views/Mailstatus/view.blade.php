@extends('layouts.app')
@section('content')
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
{{ HTML::script('resources/assets/js/mailstatus.js') }}
<div class="CMN_display_block box100per" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="mail" class="DEC_flex_wrapper " data-category="mail mail_sub_1">
	{{ Form::open(array('name'=>'frmmailstatusview', 
						'id'=>'frmmailstatusview', 
						'url' => 'Estimation/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),
						'files'=>true,
						'method' => 'POST')) }}
		{{ Form::hidden('page', $request->page, array('id' => 'page')) }}
		{{ Form::hidden('plimit', $request->plimit, array('id' => 'plimit')) }}
		{{ Form::hidden('statusid', $request->statusid, array('id' => 'statusid')) }}
		{{ Form::hidden('sendfilter', $request->sendfilter, array('id' => 'sendfilter')) }}
		{{ Form::hidden('historyfilter', $request->historyfilter, array('id' => 'historyfilter')) }}
		{{ Form::hidden('hiddensendfilter', $request->hiddensendfilter, array('id' => 'hiddensendfilter')) }}
		{{ Form::hidden('mailstatusid', (isset($singlemailstatus[0]->id)) ? $singlemailstatus[0]->id : '', array('id' => 'mailstatusid')) }}
		{{ Form::hidden('customerid', $request->customerid , array('id' => 'customerid')) }}
		{{ Form::hidden('customer_name', $request->customer_name , array('id' => 'customer_name')) }}
		{{ Form::hidden('backflg', $request->backflg , array('id' => 'backflg')) }}
		<?php 
			if(!isset($singlemailstatus[0]->attachments)) { $singlemailstatus[0]->attachments =""; }
			if(substr($singlemailstatus[0]->attachments, 0,3)=="INV") { $sendmailfrom = "Invoice"; } else { $sendmailfrom = "Estimation"; } 
		?>
		{{ Form::hidden('sendmailfrom', $sendmailfrom, array('id' => 'sendmailfrom')) }}
	<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12">
			<img class="pull-left box35 mt15" src="{{ URL::asset('resources/assets/images/mail.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_mailstatus') }}</h2>
			<h2 class="pull-left mt15">・</h2>
			<h2 class="pull-left mt15 blue">{{ trans('messages.lbl_view') }}</h2>
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
	<div class="col-xs-6 ml10 pm0 pull-left mt10">
		<a href="javascript:fngotoindex('{{ $request->backflg }}');" 
			class="btn btn-info box80">
    		<span class="fa fa-arrow-left"></span>
    			{{ trans('messages.lbl_back') }}
    	</a>
    	<?php if($request->sendfilter == 0 && $request->historyfilter == 0) { ?>
		<a href="javascript:fnresend();" 
			class="btn btn-success box100">
    		<span class="fa fa-share-square-o"></span>
    			{{ trans('messages.lbl_resend') }}
    	</a>
    	<?php } ?>
	</div>
	<fieldset class="col-xs-12 box98per mr10 ml10">
	<div class="col-xs-12 mr10 ml10 mt10">
		<div class="minh400">
			<div class="col-xs-12 mt15">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_companyname') }}<span class="ml2 white"> * </span></label>
				</div>
				<div class="fwb">
					{{ $singlemailstatus[0]->customer_name}}
				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_to') }}<span class="ml2 white"> * </span></label>
				</div>
				<div class="col-xs-8 pm0">
					@if($singlemailstatus[0]->toMail !="")
					<div class="col-xs-12 pm0">
					<?php $extomail = explode(",", $singlemailstatus[0]->toMail); ?>
								<div class="col-xs-12 pm0">
									<?php for ($i=0; $i < count($extomail); $i++) { ?>
									<div class="col-xs-12 pm0">
										{{ $extomail[$i] }}<?php if(count($extomail)-1 != $i) { echo ","; } ?>
									</div>
									<?php } ?>
								</div>
					</div>
					@else
						{{ "NILL" }}
					@endif
				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_CC') }}<span class="ml2 white"> * </span></label>
				</div>
				<div class="col-xs-8 pm0">
					@if($singlemailstatus[0]->cc !="")
					<div class="col-xs-12 pm0">
					<?php $excc = explode(",", $singlemailstatus[0]->cc); ?>
								<div class="col-xs-12 pm0">
									<?php for ($i=0; $i < count($excc); $i++) { ?>
									<div class="col-xs-12 pm0">
										{{ $excc[$i] }}<?php if(count($excc)-1 != $i) { echo ","; } ?>
									</div>
									<?php } ?>
								</div>
					</div>
					@else
						{{ "NILL" }}
					@endif
				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_subject') }}<span class="ml2 white"> * </span></label>
				</div>
				<div>
					@if($singlemailstatus[0]->subject !="")
						{{ $singlemailstatus[0]->subject }}
					@else
						{{ "NILL" }}
					@endif
				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_content') }}<span class="ml2 white"> * </span></label>
				</div>
				<div class="col-xs-8 pm0">
					@if($singlemailstatus[0]->content !="")
	            		{!! nl2br(e($singlemailstatus[0]->content)) !!}
					@else
						{{ "NILL" }}
					@endif
				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_pdfpassword') }}<span class="ml2 white"> * </span></label>
				</div>
				<div>
					@if($singlemailstatus[0]->pdfPassword !="")
						{{ $singlemailstatus[0]->pdfPassword }}
					@else
						{{ "NILL" }}
					@endif
				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_Attachmentscnt') }}<span class="ml2 white"> * </span></label>
				</div>
				<div>
					@if($singlemailstatus[0]->attachCount !="")
						{{ $singlemailstatus[0]->attachCount }}
					@else
						{{ "NILL" }}
					@endif
				</div>
			</div>
			<?php if($singlemailstatus[0]->pdfNames !="") { ?>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_Attachments') }}<span class="ml2 white"> * </span></label>
				</div>
				<div class="col-xs-9 pm0">
					<?php $pdfviewfile = explode(",", $singlemailstatus[0]->pdfNames);
						$findpath=substr($singlemailstatus[0]->attachments, 0,3);
						if($findpath=="INV") {
							$modulepath ="Invoice";
						} else {
							$modulepath ="Estimation";
						}
						for ($i=0; $i < count($pdfviewfile) ; $i++) { 
							if (substr($pdfviewfile[$i], -3) == "xls" || substr($pdfviewfile[$i], -3) == "pdf" || substr($pdfviewfile[$i], -3) == "doc" || substr($pdfviewfile[$i], -4) == "docx" || substr($pdfviewfile[$i], -4) == "xlsx") { ?>
								<a name="estimat" href="javascript:filedownload('<?php echo "../../../resources/assets/uploadandtemplates/upload/".$modulepath."/Protected_files/mailStatus".$singlemailstatus[0]->id; ?>','<?php echo $pdfviewfile[$i]; ?>');" style="font-size:12px;" class="anchorstyle"><?php echo $pdfviewfile[$i]; ?></a>
							<?php } else { ?>
								<a name="estimat" href="javascript:filedownload('<?php echo "../../../resources/assets/uploadandtemplates/upload/".$modulepath."/Protected_files/mailStatus".$singlemailstatus[0]->id; ?>','<?php echo $pdfviewfile[$i].".pdf"; ?>');" style="font-size:12px;" class="anchorstyle"><?php echo $pdfviewfile[$i]; ?></a>
							<?php } ?>
							<?php if ($i!=count($pdfviewfile)-1) { echo ","; } ?>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
	</fieldset>
</article>
</div>
@endsection