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
		width: 65%;
		position: absolute;
		top: 5%;
		left: 17%;
	}
	.passbookImage {
		width: 90%;
		height: 700px;
	}
	a:hover {
	  text-decoration: none;
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
			<div class="mt10 mb10 tac">
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
						{{--*/ $cursorprev = "cursor:default;color: black;" /*--}}
						{{--*/ $classprev = "btn btn-default" /*--}}
					@else
						{{--*/ $previmg = "/previousenab.png" /*--}}
						{{--*/ $disableprev = "" /*--}}
						{{--*/ $cursorprev = "cursor:pointer;" /*--}}
						{{--*/ $classprev = "btn btn-info" /*--}}
					@endif
					@if($passbookNextId == $imageId)
						{{--*/ $nextimg = "/nextdisab.png" /*--}}
						{{--*/ $disablenext = "disabled" /*--}}
						{{--*/ $cursornext = "cursor:default;color: black;" /*--}}
						{{--*/ $classnext = "btn btn-default" /*--}}
					@else
						{{--*/ $nextimg = "/nextenab.png" /*--}}
						{{--*/ $disablenext = "" /*--}}
						{{--*/ $cursornext = "cursor:pointer;" /*--}}
						{{--*/ $classnext = "btn btn-info" /*--}}
					@endif
					@if($ImageName == $request->fileImage)
						<div id="image<?php echo $ImageName; ?>">
					@else
						<div id="image<?php echo $ImageName; ?>" style="display: none;">
					@endif
						<div class="mt5 mb5">
							<div class="pull-left ml30" style="display:inline-block;">
							<a id = "prev<?php echo $ImageName; ?>"
								class = "{{ $classprev }}"
								@if ($passbookPrevId != $imageId)  
									onclick = "fnprevnext('{{ $ImageName }}','{{ $image->id }}',1);"
								@endif {{ $disableprev }} style="{{ $cursorprev }}">
								<span class="fa fa-backward"></span>&nbsp
								{{ trans('messages.lbl_prev') }}
							</a>
							<span class="ml10 mr10">
								@if($image->fileDtl != "")
									{{ $image->fileDtl }}
								@else
									AccBankPassbook_{{ $image->pageNoFrom }}_{{ $image->pageNoTo }}
								@endif
							</span>
							<a id = "next<?php echo $ImageName; ?>"
								class = "{{ $classnext }}"
								@if ($passbookNextId != $imageId)
									onclick = "fnprevnext('{{ $ImageName }}','{{ $image->id }}',2);" 
								@endif {{ $disablenext }} style="{{ $cursornext }}">
								{{ trans('messages.lbl_next') }}&nbsp
								<span class="fa fa-forward"></span>
							</a>
							</div>
							<div class="pull-right mr30" style="display:inline-block;">
								@if($image->fileDtl != "")
									<a id="dwnld<?php echo $ImageName; ?>" 
										class = "btn btn-info fa fa-download" 
										href="javascript:download('{{ $image->fileDtl }}','{{ $dwnldPath }}');">&nbsp
										{{ trans('messages.lbl_download') }}
									</a>
								@endif
							</div>
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
				@endforeach
			</div>
		</div>
	</div>
{{ Form::close() }}