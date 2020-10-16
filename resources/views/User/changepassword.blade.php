@extends('layouts.app')
@section('content')
{{ HTML::script('resources/assets/js/user.js') }}
<script type="text/javascript">
	var datetime = '<?php echo date('Ymdhis'); ?>';
	var mainmenu = '<?php echo $request->mainmenu; ?>';
</script>
<div class="box100per CMN_display_block" id="main_contents">
<!-- article to select the main&sub menu -->
<article id="master" class="DEC_flex_wrapper " data-category="master master_sub_1">
	{{ Form::open(array('name'=>'frmpasswordchange','id'=>'frmpasswordchange', 'url' => 'User/passwordchangeprocess?mainmenu='.$request->mainmenu.'&time='.date('YmdHis'),'files'=>true,'method' => 'POST')) }}
		{{ Form::hidden('mainmenu', $request->mainmenu , array('id' => 'mainmenu')) }}
		{{ Form::hidden('id', $request->id , array('id' => 'id')) }}
		{{ Form::hidden('viewid', $request->id , array('id' => 'viewid')) }}
		{{ Form::hidden('editid', $request->id , array('id' => 'editid')) }}
	<!-- Start Heading -->
	<div class="row hline">
		<div class="col-xs-12 pl5">
			<img class="pull-left box45 mt12" src="{{ URL::asset('resources/assets/images/passwordchange.png') }}">
			<h2 class="pull-left pl5 mt15">{{ trans('messages.lbl_passwordchange') }}</h2>
		</div>
	</div>
	<div class="pb10"></div>
	<!-- End Heading -->
	<div class="col-xs-12 pl10 pr10 minheight250">
		<fieldset>
			<div class="col-xs-12 mt15">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_usercode') }}<span class="fr ml2 white"> * </span></label>
				</div>
				<div>
					<label class="">{{ $view[0]->usercode }}</label>
				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_password') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div>
					{{ Form::password('password',array('id'=>'password',
														'name' => 'password',
														'data-label' => trans('messages.lbl_password'),
														'class'=>'box25per form-control pl5')) 
					}}
				</div>
			</div>
			<div class="col-xs-12 mt5">
				<div class="col-xs-3 text-right clr_blue">
					<label>{{ trans('messages.lbl_conpassword') }}<span class="fr ml2 red"> * </span></label>
				</div>
				<div>
					{{ Form::password('confirmpassword',array('id'=>'confirmpassword',
																'name' => 'confirmpassword',
																'data-label' => trans('messages.lbl_conpassword'),
																'class'=>'mb20 box25per form-control pl5')) 
					}}
				</div>
			</div>
		</fieldset>
		<fieldset class="bg_footer_clr">
			<div class="form-group">
				<div align="center" class="mt7">
					<button type="submit" class="btn btn-success add box100 frmpasswordchange" >
						<i class="fa fa-key"></i>{{ trans('messages.lbl_register') }}
					</button>
					<a onclick="javascript:cancelpassword('view','{{ $request->mainmenu }}');" class="pageload btn btn-danger white"><span class="fa fa-times"></span> {{trans('messages.lbl_cancel')}}
					</a>
				</div>
			</div>
		</fieldset>
	</div>
	{{ Form::close() }}
</article>
</div>
@endsection