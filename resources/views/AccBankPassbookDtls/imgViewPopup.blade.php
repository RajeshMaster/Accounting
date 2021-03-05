{{ HTML::script(asset('public/js/accbankpassbookdtls.js')) }}
<script type="text/javascript">
	function divpopupclose() {
		$("body div" ).removeClass( "modalOverlay" );
		$('#imgViewPopup').empty();
		$('#imgViewPopup').modal('toggle');
		
	}
</script>
<style type="text/css">
	.modal {
		width: 85%;
		position: absolute;
		top: 15%;
		left: 10%;
	}
	.passbookImage {
		width:950px;
		height:550px;
	}
	
</style>
{{ Form::open(array('name'=>'imgViewPopup', 'id'=>'imgViewPopup',
							'files'=>true, 
							'class' => 'form-horizontal',
							'url' => '', 
							'method' => 'POST')) }}
	{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
	{{ Form::hidden('selYear', $request->selYear , array('id' => 'selYear')) }}
	<div class="modal-content">
		<div class="modal-header popH_color" style="height: 60px;">
			<button type="button"  data-dismiss="modal" class="close mt7" 
				style="color: red !important;" aria-hidden="true">&#10006;</button>
			<span class="modal-title custom_align">
				<h3 class="pull-left pl5 mt5">
					{{ trans('messages.lbl_image') }}
				</h3>
			</span>
		</div>
		<div class="form-group mt10 mb10" style="min-height: 100px;">
			<div class="mt20 mb20 tac">
				@foreach($passbookImgdetails as $key => $image)
					<?php
						$passBookPath = '../../../../AccountingUpload/AccBankPassbook'."/".$image->fileDtl;
						$dwnldPath = '../../../../AccountingUpload/AccBankPassbook'."/";
						$noPath = 'resources/assets/images/norecord.png';
					?>
					@php 
						$ImageName = "AccBankPassbook_".$image->id;
						$imageId = $image->id;
					@endphp
					@if($passbookPrevId == $imageId) 
						{{--*/ $previmg = "/previousdisab.png" /*--}}
						{{--*/ $disableprev = "disabled" /*--}}
						{{--*/ $cursorprev = "cursor:default;vertical-align:middle;" /*--}}
					@else
						{{--*/ $previmg = "/previousenab.png" /*--}}
						{{--*/ $disableprev = "" /*--}}
						{{--*/ $cursorprev = "cursor:pointer;" /*--}}
					@endif
					@if($passbookNextId == $imageId)
						{{--*/ $nextimg = "/nextdisab.png" /*--}}
						{{--*/ $disablenext = "disabled" /*--}}
						{{--*/ $cursornext = "cursor:default;vertical-align:middle;" /*--}}
					@else
						{{--*/ $nextimg = "/nextenab.png" /*--}}
						{{--*/ $disablenext = "" /*--}}
						{{--*/ $cursornext = "cursor:pointer;" /*--}}
					@endif
					@if($ImageName == $request->fileImage)
						<div id="image<?php echo $ImageName; ?>">
					@else
						<div id="image<?php echo $ImageName; ?>" style="display: none;">
					@endif
						<div class="mt10">
							<img width='17' height='17' class="vam" 
							src="{{ URL::asset('resources/assets/images'.$previmg)}}"
								id = "prev<?php echo $ImageName; ?>"
								@if ($passbookPrevId != $imageId)  
									onclick = "fnprevnext('{{ $ImageName }}','{{ $image->id }}',1);"
								@endif {{ $disableprev }} style="{{ $cursorprev }}">
							<span class="ml10 mr10">
								@if($image->fileDtl != "")
									{{ $image->fileDtl }}
								@else
									AccBankPassbook_{{ $image->id }}.png
								@endif
							</span>
							<img width='17' height='17' class="vam" 
								src="{{ URL::asset('resources/assets/images'.$nextimg)}}"
								id = "next<?php echo $ImageName; ?>"
								@if ($passbookNextId != $imageId)
									onclick = "fnprevnext('{{ $ImageName }}','{{ $image->id }}',2);" 
								@endif {{ $disablenext }} style="{{ $cursornext }}">
						</div>
						<div class="mt10">
							@if($image->fileDtl != "")
								<img src="{{ URL::asset($passBookPath) }}" 
										class="box200 mt10 mb10 passbookImage">
							@else
								<img src="{{ URL::asset($noPath) }}" 
									class="box200 mt10 mb10 passbookImage">
							@endif
						</div>
					</div>
					@if($ImageName == $request->fileImage)
						<div id="dwnld<?php echo $ImageName; ?>" class="mt20">
					@else
						<div id="dwnld<?php echo $ImageName; ?>" class="mt20" 
							style="display: none;">
					@endif
						@if($image->fileDtl != "")
							<a class="fa fa-download" 
								style="text-decoration:none !important;"
								href="javascript:download('{{ $image->fileDtl }}','{{ $dwnldPath }}');">
							</a>
						@endif
					</div>
				@endforeach
			</div>
		</div>
	</div>
{{ Form::close() }}