<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ trans('messages.lbl_login') }}</title>
    <script type="text/javascript" src="{{ URL::asset('resources/assets/js/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('resources/assets/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('resources/assets/js/jquery.plugin.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('resources/assets/js/jquery.form-validator.min.js') }}"></script>
    <link rel="stylesheet" href="{{ URL::asset('resources/assets/css/font-awesome.min.css') }}" />
    <link rel="stylesheet" href="{{ URL::asset('resources/assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ URL::asset('resources/assets/css/widthbox.css') }}" />
    <link rel="stylesheet" href="{{ URL::asset('resources/assets/css/common.css') }}" />
    <link rel="stylesheet" href="{{ URL::asset('resources/assets/css/decoration.css') }}" />
    <style>
        body {
            font-family: 'Lato';
        }
        .fa-btn {
            margin-right: 6px;
        }
    </style>
    <SCRIPT LANGUAGE="javascript">
    </SCRIPT>
</head>
<body id="app-layout">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="col-xs-12" style="background-color: #3C8DBC;">
            <div class="navbar-header">
                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar" style="height:55px;">
                    <li class="">
                        <div class="" style="margin-top: 5.5px;"><img style="height:40px;" src="{{ URL::asset('resources/assets/images/microbit_logo.jpg') }}"></div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @yield('content')
</body>
</html>
