<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>{{ Session::get('systemname') }}</title>
		{{ HTML::style('resources/assets/css/menu.css') }}
		{{ HTML::style('resources/assets/css/common.css') }}
		{{ HTML::style('resources/assets/css/font-awesome.min.css') }}
		{{ HTML::style('resources/assets/css/decoration.css') }}
		{{ HTML::style('resources/assets/css/minheight.css') }}
		{{ HTML::style('resources/assets/css/widthbox.css') }}
		{{ HTML::style('resources/assets/css/paddingmargin.css') }}
		{{ HTML::style('resources/assets/css/bootstrap.min.css') }}
		{{ HTML::style('resources/assets/css/AdminLTE.css') }}
		{{ HTML::style('resources/assets/css/_all-skins.min.css') }}

		{{ HTML::script('resources/assets/js/jquery.min.js') }}
		{{ HTML::script('resources/assets/js/bootstrap.min.js') }}
		{{ HTML::script('resources/assets/js/jquery.plugin.js') }}
		{{ HTML::script('resources/assets/js/common.js') }}
		{{ HTML::script('resources/assets/js/jquery.form-validator.min.js') }}
		@if (Session::get('setlanguageval') == 'en')
			{{ HTML::script('resources/assets/js/english.js') }}
		@elseif(empty(Session::get('setlanguageval')))
			{{ HTML::script('resources/assets/js/japanese.js') }}
		@else
			{{ HTML::script('resources/assets/js/japanese.js') }}
		@endif
</head>
<style type="text/css">
	.se-pre-con {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
	background: url({{ URL::asset('resources/assets/images/loading.gif') }}) center no-repeat;
    background-size: 10%;
    background-color: rgba(255, 255, 255, .5);
}
.todo-list .primary {
  border-left-color: #ffffff!important;
}
html {
		height: 40% !important;
   		display: table;
   		margin: auto;
   		padding: 0px;
	}
	body {
		display: table-cell;
   		vertical-align: middle;
   		margin: auto;
	}
</style>
<body class="check CMN_cursor_wait response" style="min-width: 1185px;min-height: 200px !important ;border: 1px solid white;">
	<div id="fixeddiv" class="stretch_view" style="background-color: #3C8DBC !important;">
	<div class="se-pre-con" id="se-pre-con"></div>
		<div class="CMN_header_wrap_wrap mb5">
			<div class="CMN_header_wrap" 
				style="background-color: #3C8DBC !important;height:60px;">
				<div id="CMN_logo_area" style="margin-top: 4.8px;margin-left: 16px;">
					<a href="#"><img class="logo logosize" style="height:40px;" src="{{ URL::asset('resources/assets/images/microbit_logo.jpg') }}"></a>
				</div>
				<div id="CMN_user_area">
					<div id="CMN_user_date" style="padding-right:10px;background-color: #3C8DBC !important;margin-top: 6px">
							@if(Session::get('Gender') == "2")
								<img class="img25px" src="{{ URL::asset('resources/assets/images/female.png') }}">
							@else
								<img class="img25px" src="{{ URL::asset('resources/assets/images/male.png') }}">
							@endif
							<span class="CMN_user_name mt20" style="color: #ffffff">{{ Session::get('LastName')." ".Session::get('FirstName') }}</span>
					</div>
				</div>
				<div id="CMN_btn_area">
						<div class="CMN_div_logout mt3">
							<a href="{{ url('logout') }}" class="btn btn-primary">Sign out</a>
						</div>
				</div>
			</div>
		</div>
		
	<!-- end_sub_tab -->
	</div>
	<!-- Content Wrapper. Contains page content -->
	<div id="sectiondiv" class="bg_white" style="min-width: 700px;margin-top:60px;border-radius: 5px;">
		@yield('content')
	</div>
</body>
</html>