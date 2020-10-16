<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>INVOICE</title>
        <link rel="stylesheet" href="{{ URL::asset('resources/assets/css/common.css') }}" />
        <link rel="stylesheet" href="{{ URL::asset('resources/assets/css/font-awesome.min.css') }}" />
        <link rel="stylesheet" href="{{ URL::asset('resources/assets/css/decoration.css') }}" />
        <link rel="stylesheet" href="{{ URL::asset('resources/assets/css/minheight.css') }}" />
        <link rel="stylesheet" href="{{ URL::asset('resources/assets/css/widthbox.css') }}" />
        <link rel="stylesheet" href="{{ URL::asset('resources/assets/css/paddingmargin.css') }}" />
        <link rel="stylesheet" href="{{ URL::asset('resources/assets/css/bootstrap.min.css') }}" />
        <link rel="stylesheet" href="{{ URL::asset('resources/assets/css/AdminLTE.css') }}" />
        <link rel="stylesheet" href="{{ URL::asset('resources/assets/css/_all-skins.min.css') }}" />

        <script type="text/javascript" src="{{ URL::asset('resources/assets/js/jquery.min.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('resources/assets/js/bootstrap.min.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('resources/assets/js/app.min.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('resources/assets/js/jquery.plugin.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('resources/assets/js/pageload.js') }}"></script>
        <script type="text/javascript" src="{{ URL::asset('resources/assets/js/jquery.form-validator.min.js') }}"></script>
        @if (Session::get('setlanguageval') == 'en')
            <script type="text/javascript" src="{{ URL::asset('resources/assets/js/japanese.js') }}"></script>
        @elseif(empty(Session::get('setlanguageval')))
            <script type="text/javascript" src="{{ URL::asset('resources/assets/js/japanese.js') }}"></script>
        @else
            <script type="text/javascript" src="{{ URL::asset('resources/assets/js/english.js') }}"></script>
        @endif
</head>
<body class="hold-transition skin-blue sidebar-mini sidebar-collapse">
<div class="se-pre-con" id="se-pre-con"></div>
<div class="wrapper">
    <header class="main-header">
        <!-- Logo -->
        <span class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <img class="logo-mini logosize" src="{{ URL::asset('resources/assets/images/mb.png') }}">
            <!-- logo for regular state and mobile devices -->
            <img class="logo-lg logosize" align="center" src="{{ URL::asset('resources/assets/images/MB_logo.png') }}">
        </span>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            @if(Session::get('Picture') != "")
                                <img class="user-image" src="{{ URL::asset('../../uploads/profile/thumbnail/'.Session::get('Picture')) }}">
                            @else
                                @if(Session::get('Gender') == "2")
                                    <img class="user-image" src="{{ URL::asset('resources/assets/images/female.png') }}">
                                @else
                                    <img class="user-image" src="{{ URL::asset('resources/assets/images/male.png') }}">
                                @endif
                            @endif
                            <span class="hidden-xs csrp">{{ Session::get('LastName').".".substr(Session::get('FirstName'),0,1) }}
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                @if(Session::get('Picture') != "")
                                    <img class="img-circle" src="{{ URL::asset('../../uploads/profile/thumbnail/'.Session::get('Picture')) }}">
                                @else
                                    @if(Session::get('Gender') == "2")
                                        <img class="img-circle" src="{{ URL::asset('resources/assets/images/female.png') }}">
                                    @else
                                        <img class="img-circle" src="{{ URL::asset('resources/assets/images/male.png') }}">
                                    @endif
                                @endif
                                <p>{{ Session::get('LastName').".".substr(Session::get('FirstName'),0,1) }}</p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-right">
                                    <a href="{{ url('login/logout') }}" class="btn btn-default btn-flat">Sign out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown tasks-menu">
                        <div class="pt8 pr10 pl10 flgclick">
                            @if(Session::get('lang'))
                                @if(Session::get('lang')=="jap")
                                    <img alt="CakePHP" src="{{ URL::asset('resources/assets/images/languageiconen.png') }}" escape="false">
                                @else
                                    <img alt="CakePHP" src="{{ URL::asset('resources/assets/images/languageiconjp.png') }}" escape="false">
                                @endif
                            @else
                                <img alt="CakePHP" src="{{ URL::asset('resources/assets/images/languageiconen.png') }}" escape="false">
                            @endif
                        </div>
                    </li>
                </ul>
            </div>

        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image mt5 mb2">
                    @if(Session::get('Picture') !="")
                        <img class="img-circle circlepic" src="{{ URL::asset('../../uploads/profile/thumbnail/'.Session::get('Picture')) }}">
                    @else
                        @if(Session::get('Gender') =="2")
                            <img class="img-circle circlepic" src="{{ URL::asset('resources/assets/images/female.png') }}">
                        @else
                            <img class="img-circle circlepic" src="{{ URL::asset('resources/assets/images/male.png') }}">
                        @endif
                    @endif
                </div>
                <div class="pull-left info mt15" style="padding:0px !important;">
                    <p>{{ Session::get('LastName').".".substr(Session::get('FirstName'),0,1) }}</p>
                </div>
            </div>
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">
                <li @if (isset($request->mainmenu) && $request->mainmenu == "demo")  class="active" @endif >
                    <a class="pageload" escape="false" href="{{ url('Ourdetail/index?mainmenu=Ourdetail&time='.date('Ymdhis')) }}">
                        <i class="fa fa-tasks"></i><span class="menu-text">demo</span>
                        <span class="selected"></span>
                    </a>
                </li>
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper bg_white">
       @yield('content')
    </div>
</body>
</html>