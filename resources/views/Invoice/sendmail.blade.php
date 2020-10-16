@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/invoice.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
<?php use App\Http\Helpers; ?>
<div class="CMN_display_block col-xs-12" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="sales" class="DEC_flex_wrapper " data-category="sales sales_sub_2">
	{{ Form::open(array('name'=>'frmsendmail','id'=>'frmsendmail', 'url' => 'Invoice/sendmailprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
	{{ Form::hidden('sendmailfrom', 'Invoice', array('id' => 'sendmailfrom')) }}
	{{ Form::hidden('invoice_id', $request->invoice_id, array('id' => 'invoice_id')) }}
	{{ Form::hidden('cust_id', $request->cust_id, array('id' => 'cust_id')) }}
	{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
	{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	{{ Form::hidden('filepath', 'resources/assets/uploadandtemplates/upload/Invoice/', array('id' => 'filepath')) }}
	<!-- Start Heading -->
	<div class="row hline">
	<div class="col-xs-12 pm0">
			<img class="pull-left box35 mt15" src="{{ URL::asset('resources/assets/images/mail.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_sendmail') }}</h2>
		</div>
	</div>
	<div class="pb10"></div>
	<!-- End Heading -->
	<div class="col-xs-12 pl5 pr5">
	<fieldset class="pb10">
		<div class="col-xs-12 mt15">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_companyname') }}<span class="ml2 white"> * </span></label>
			</div>
			<div>
				<label class="brown">{{ isset($CompanyName[0]->company_name) ? $CompanyName[0]->company_name : ''}}</label>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_to') }}<span class="ml2 red"> * </span></label>
			</div>
			<div>
				{{ Form::text('tomail',(isset($CustomerDetails[0]->customer_email_id)) ? $CustomerDetails[0]->customer_email_id : '',array('id'=>'tomail',
														'name' => 'tomail',
														'data-label' => trans('messages.lbl_to'),
														'class'=>'box25per form-control pl5')) }}
				<a href="javascript:underconstruction();" class="btn btn-success box100 p4">{{ trans('messages.lbl_Browse') }}</a>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_CC') }}<span class="ml2 white"> * </span></label>
			</div>
			<div>
				{{ Form::text('ccname',(isset($CompanyName[0]->CC)) ? $CompanyName[0]->CC : '',array('id'=>'ccname',
														'name' => 'ccname',
														'data-label' => trans('messages.lbl_CC'),
														'class'=>'box25per form-control pl5')) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_subject') }}<span class="ml2 white"> * </span></label>
			</div>
			<div>
				{{ Form::text('subject',(isset($CompanyName[0]->subject)) ? $CompanyName[0]->subject : '',array('id'=>'subject',
														'name' => 'subject',
														'data-label' => trans('messages.lbl_subject'),
														'class'=>'box25per form-control pl5')) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_content') }}<span class="ml2 red"> * </span></label>
			</div>
			<div>
				{{ Form::textarea('content',(isset($CompanyName[0]->content)) ? $CompanyName[0]->content : '',array('id'=>'content',
														'name' => 'content',
														'data-label' => trans('messages.lbl_content'),
														'class'=>'box50per form-control pl5')) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_pdfpassword') }}<span class="ml2 red"> * </span></label>
			</div>
			<div>
				{{ Form::text('pdfpassword',(isset($CompanyName[0]->pdfpassword)) ? $CompanyName[0]->pdfpassword : '',array('id'=>'pdfpassword',
														'name' => 'pdfpassword',
														'data-label' => trans('messages.lbl_subject'),
														'class'=>'box25per form-control pl5')) }}
			</div>
		</div>
		<?php $totfile=count($getpdf); ?>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_Attachmentscnt') }}<span class="ml2 white"> * </span></label>
			</div>
			<div>
				<label id="filecnttxt">{{ $totfile }}</label>
				{{ Form::hidden('pdfcnt', $totfile, array('id' => 'pdfcnt')) }}
				{{ Form::hidden('pdftotcnt', $totfile, array('id' => 'pdftotcnt')) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_Attachments') }}<span class="ml2 white"> * </span></label>
			</div>
			<div class="col-xs-9 pm0">
			<?php
				$i="1";
				foreach ($getpdf as $key => $pdfvalue) { ?>
				<div class="col-xs-12 pm0">
				<div class="box22per yespdf fll">
				<img class="pull-left box30" id="imgtick{{$i}}" src="{{ URL::asset('resources/assets/images/pdf.png') }}">
				<input type="checkbox" name="fileCounter[]" checked="" id="tick{{$i}}" onclick="javascript:fnpdfremove(this.id,'{{$pdfvalue->user_id}}');" class="pull-left mt10">
				<!-- <img class="pull-left box28" style="visibility: visible;" src="{{ URL::asset('resources/assets/images/nopdf.png') }}">
				<a onclick="javascript:fnpdfremove('1');" class="csrp"><img class="pull-left box15 mt10" style="visibility: visible;" src="{{ URL::asset('resources/assets/images/tick.png') }}"></a> -->
				<span class="CMN_display_block mt5 ml3">{{ "( ".$pdfvalue->user_id.".pdf )" }}</span>
				</div>
				<div class="box75per">
				<label class="clr_blue ml25">{{ trans('messages.lbl_PDFname') }}<span class="ml2 white"> * </span></label>
				{{ Form::hidden('pdfid'.$i, (isset($pdfvalue->id)) ? $pdfvalue->id : '', array('id' => 'pdfid'.$i)) }}
				{{ Form::hidden('estimatename'.$i, (isset($pdfvalue->user_id)) ? $pdfvalue->user_id : '', array('id' => 'estimatename'.$i)) }}
				{{ Form::hidden($pdfvalue->user_id, '1', array('id' => $pdfvalue->user_id)) }}
				{{ Form::text('pdfname'.$i,'',array('id'=>'pdfname'.$i,
														'name' => 'pdfname'.$i,
														'data-label' => trans('messages.lbl_subject'),
														'class'=>'box35per form-control pl5')) }}
				</div>
				</div>
			<?php $i=$i+1; } ?>
			</div>
		</div>
	</fieldset>
	<fieldset style="background-color: #DDF1FA;">
		<div class="form-group">
			<div align="center" class="mt5">
			<?php $cansend=Session::get('sessionfrommail');
				$ext=explode("@", $cansend);
				if($ext[1]=="microbit.co.jp") {
					$cansend="1";
				} else  {
					$cansend="0";
				}
			?>
			<a onclick="javascript:validationmail({{$cansend}});" class="btn btn-success box80 white">
					<i class="fa fa-send" aria-hidden="true"></i>
					{{ trans('messages.lbl_send') }}
			</a>
				<a onclick="javascript:mailbacktoindex();" class="btn btn-danger box155 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_draftscancel')}}
				</a>
			</div>
		</div>
	</fieldset>
	</div>
	{{ Form::close() }}
	{{ Form::open(array('name'=>'frmsendmailcancel', 'id'=>'frmsendmailcancel', 'url' => 'Invoice/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
	{{ Form::hidden('sample', $request->sample, array('id' => 'sample')) }}
	{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
	{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	{{ Form::close() }}
</article>
</div>
<div class="CMN_display_block pb10"></div>
@endsection