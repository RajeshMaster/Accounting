@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/estimation.js') }}
{{ HTML::script('resources/assets/js/lib/bootstrap-datetimepicker.js') }}
{{ HTML::style('resources/assets/css/lib/bootstrap-datetimepicker.min.css') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
<?php use App\Http\Helpers; ?>
<div class="CMN_display_block col-xs-12" id="main_contents">
<!-- article to select the main&sub menu -->
<?php if($request->sendmailfrom=="Invoice") { ?>
<article id="sales" class="DEC_flex_wrapper " data-category="sales sales_sub_2">
<?php } else { ?>
<article id="sales" class="DEC_flex_wrapper " data-category="sales sales_sub_1">
<?php } ?>
	{{ Form::open(array('name'=>'frmsendmail','id'=>'frmsendmail', 'url' => 'Estimation/sendmailprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
	{{ Form::hidden('sendmailfrom', $request->sendmailfrom, array('id' => 'sendmailfrom')) }}
	{{ Form::hidden('estimate_id', $request->estimate_id, array('id' => 'estimate_id')) }}
	{{ Form::hidden('cust_id', $request->cust_id, array('id' => 'cust_id')) }}
	{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
	{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	{{ Form::hidden('allhidden', $request->allhidden, array('id' => 'allhidden')) }}
	{{ Form::hidden('CCallhidden', $request->CCallhidden, array('id' => 'CCallhidden')) }}
	{{ Form::hidden('fordraft', 0, array('id' => 'fordraft')) }}
	{{ Form::hidden('sendfilter', $request->sendfilter, array('id' => 'sendfilter')) }}
	{{ Form::hidden('mailstatusid', $request->mailstatusid, array('id' => 'mailstatusid')) }}
	{{ Form::hidden('sendmailfrom', $request->sendmailfrom, array('id' => 'sendmailfrom')) }}
	{{ Form::hidden('sendmailccName', '', array('id' => 'sendmailccName')) }}
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
				{{ Form::text('tomail',(isset($prevsendmail[0]->toMail)) ? $prevsendmail[0]->toMail : '',array('id'=>'tomail',
														'name' => 'tomail',
														'readonly' => 'readonly',
														'data-label' => trans('messages.lbl_to'),
														'class'=>'box25per form-control pl5')) }}
				<a class="btn btn-success box100 p4" <?php if(1!=0) { ?> href="javascript:fnbrowsepopup('to','{{ $request->cust_id }}');" <?php } else { ?> style="background: grey;cursor: default;border: 1px solid grey" <?php } ?>>{{ trans('messages.lbl_browser') }}</a>
				<a class="btn btn-danger box70 p4" href="javascript:fntoclear();" >{{ trans('messages.lbl_clear') }}</a>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_CC') }}<span class="ml2 white"> * </span></label>
			</div>
			<div>
				{{ Form::text('ccname',(isset($prevsendmail[0]->cc)) ? $prevsendmail[0]->cc : '',array('id'=>'ccname',
														'name' => 'ccname',
														'readonly' => 'readonly',
														'data-label' => trans('messages.lbl_CC'),
														'class'=>'box25per form-control pl5')) }}
				<a class="btn btn-success box100 p4" <?php if(1!=0) { ?> href="javascript:fnbrowsepopup('cc','{{ $request->cust_id }}');"<?php } else { ?> style="background: grey;cursor: default;border: 1px solid grey" <?php } ?>>{{ trans('messages.lbl_browser') }}</a>
				<a class="btn btn-danger box70 p4" href="javascript:fnccclear();" >{{ trans('messages.lbl_clear') }}</a>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_subject') }}<span class="ml2 white"> * </span></label>
			</div>
			<div>
				{{ Form::text('subject',(isset($maildata[0]->subject)) ? $maildata[0]->subject : '',array('id'=>'subject',
														'name' => 'subject',
														'data-label' => trans('messages.lbl_subject'),
														'class'=>'box25per form-control pl5')) }}
			</div>
		</div>
		<?php
			$comname = "";
			$signaturecontent = "";
			$content = "";
			$overallcontent = "";
			$comname = $CompanyName[0]->company_name;
			if (isset($signature[0]->content)) {
				$signaturecontent = $signature[0]->content;
			}
			$content = $maildata[0]->content;
			$overallcontent = $comname. PHP_EOL . $content. PHP_EOL . $signaturecontent;
		?>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_content') }}<span class="ml2 red"> * </span></label>
			</div>
			<div>
				{{ Form::textarea('content',$overallcontent,array('id'=>'content',
														'name' => 'content',
														'data-label' => trans('messages.lbl_content'),
														'class'=>'box50per form-control pl5')) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_pdfpassword') }}<span class="ml2 white"> * </span></label>
			</div>
			<div>
				<label class="brown" id="pdf_password">{{ $pdfpassword }}</label>
				{{ Form::hidden('pdfpassword', $pdfpassword, array('id' => 'pdfpassword')) }}
				{{ Form::hidden('pwdcontent', (isset($pwddata[0]->content)) ? $pwddata[0]->content : '', array('id' => 'pwdcontent')) }}
				{{ Form::hidden('pwdsubject', (isset($pwddata[0]->subject)) ? $pwddata[0]->subject : '', array('id' => 'pwdsubject')) }}
				&nbsp;&nbsp;{{ Form::checkbox('nopassword', 1, '', ['id' => 'nopassword']) }}
							&nbsp;<label for="nopassword" id="no_password"><span class="grey fb">{{ trans('messages.lbl_nopdfpassword') }}</span></label>
			</div>
		</div>
		<?php $totfile=count($getpdf); ?>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_Attachmentscnt') }}<span class="ml2 white"> * </span></label>
			</div>
			<div>
				<label id="filecnttxt">{{ $atcnt }}</label>
				{{ Form::hidden('pdfcnt', 1, array('id' => 'pdfcnt')) }}
				{{ Form::hidden('pdftotcnt', $totfile, array('id' => 'pdftotcnt')) }}
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_Attachments') }}<span class="ml2 white"> * </span></label>
			</div>
			<div class="col-xs-9 pm0">
			<?php
			if($request->mailstatusid !="") { ?>
				{{( isset($prevsendmail[0]->pdfNames)) ? $prevsendmail[0]->pdfNames : '' }}

				{{ Form::hidden('draftattachments', (isset($prevsendmail[0]->attachments)) ? $prevsendmail[0]->attachments : '', array('id' => 'draftattachments')) }}
				{{ Form::hidden('draftpdf', (isset($prevsendmail[0]->pdfNames)) ? $prevsendmail[0]->pdfNames : '', array('id' => 'draftpdf')) }}
			<?php } else {
				$i="1";
				foreach ($getpdf as $key => $pdfvalue) { ?>
				<?php 
					if($request->estid==$pdfvalue->user_id) { 
						if($totfile==1) {
							$pdfnameval=$subject;
							$pdfnamechck=$subject;
						} else {
							$pdfnameval=$subject.'1-1';
							$pdfnamechck=$subject.'1-1';
						}
						$pdfimg="pdf.png";
						$pdfval="1";
					} else {
						$pdfnameval="";
						$pdfimg="nopdf.png";
						$pdfnamechck= "( ".$pdfvalue->user_id." )";
						$pdfval="0";
					}
				?>
				<div class="col-xs-12 pm0">
				<div class="box22per yespdf fll">
				<img class="pull-left box30" id="imgtick{{$i}}" src="{{ URL::asset('resources/assets/images/'.$pdfimg) }}">
				<input type="checkbox" <?php if($request->estid==$pdfvalue->user_id) { ?> checked="" <?php } ?> id="tick{{$i}}" name="fileCounter[]" onclick="javascript:fnpdfremove(this.id,'{{$pdfvalue->user_id}}','{{$subject}}');" class="pull-left mt10 csrp">
				<!-- <img class="pull-left box28" style="visibility: visible;" src="{{ URL::asset('resources/assets/images/nopdf.png') }}">
				<a onclick="javascript:fnpdfremove('1');" class="csrp"><img class="pull-left box15 mt10" style="visibility: visible;" src="{{ URL::asset('resources/assets/images/tick.png') }}"></a> -->
				<label class="CMN_display_block mt5 ml5 fwn csrp" id="filenametxt{{$i}}" for="tick{{$i}}">{{ $pdfnamechck }}</label>
				</div>
				<div class="box75per">
				<label class="clr_blue ml25">{{ trans('messages.lbl_PDFname') }}<span class="ml2 white"> * </span></label>
				{{ Form::hidden('pdfid'.$i, (isset($pdfvalue->id)) ? $pdfvalue->id : '', array('id' => 'pdfid'.$i)) }}
				{{ Form::hidden('estimatename'.$i, (isset($pdfvalue->user_id)) ? $pdfvalue->user_id : '', array('id' => 'estimatename'.$i)) }}
				{{ Form::hidden($pdfvalue->user_id, $pdfval, array('id' => $pdfvalue->user_id)) }}
				{{ Form::text('pdfname'.$i,$pdfnameval,array('id'=>'pdfname'.$i,
														'name' => 'pdfname'.$i,
														'data-label' => trans('messages.lbl_subject'),
														'class'=>'box35per form-control pl5')) }}
				</div>
				</div>
			<?php $i=$i+1; } } ?>
			</div>
		</div>
		<div class="col-xs-12 mt5">
			<div class="col-xs-3 text-right clr_blue">
				<label>{{ trans('messages.lbl_Attachfile') }}<span class="ml2 white"> * </span></label>
			</div>
			<div>
				{{ Form::file('file1',array(
										'class' => 'pull-left box350',
										'id' => 'file1',
										'name' => 'file1',
										'style' => 'height:23px;')) }}
				<span>&nbsp;(Ex: Excel,Pdf & Word)</span>
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
			<?php if($request->mailstatusid =="") { ?>
				<a onclick="javascript:mailbacktoindex('{{ $request->sendmailfrom }}');" class="btn btn-danger box155 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_draftscancel')}}
				</a>
			<?php } else { ?>
				<a onclick="javascript:mailbacktomailview();" class="btn btn-danger box155 white"><i class="fa fa-times" aria-hidden="true"></i> {{trans('messages.lbl_draftscancel')}}
				</a>
			<?php } ?>
			</div>
		</div>
	</fieldset>
	</div>
	{{ Form::close() }}
	{{ Form::open(array('name'=>'frmsendmailcancel', 'id'=>'frmsendmailcancel', 'url' => 'Estimation/index?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
	{{ Form::hidden('selMonth', $request->selMonth, array('id' => 'selMonth')) }}
	{{ Form::hidden('selYear', $request->selYear, array('id' => 'selYear')) }}
	{{ Form::hidden('sendfilter', $request->sendfilter, array('id' => 'sendfilter')) }}
	{{ Form::close() }}
</article>
<div id="browsepopup" class="modal fade">
		<div id="login-overlay">
			<div class="modal-content">
				<!-- Popup will be loaded here -->
			</div>
		</div>
	</div>
	<div id="CCbrowsepopup" class="modal fade">
		<div id="login-overlay">
			<div class="modal-content">
				<!-- Popup will be loaded here -->
			</div>
		</div>
	</div>
</div>
<div class="CMN_display_block pb10"></div>
@endsection