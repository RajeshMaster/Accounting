@extends('layouts.layout')

@section('content')
{{ HTML::style('resources/assets/css/login.css') }}
<style type="text/css">
    fieldset {
        border:0px solid #136E83 !important;
        border-radius:4px !important;
        margin-top: 0px !important;
        margin-bottom: 15px !important;
    }
</style>
<script type="text/javascript">
    // Jquery From Validation
    $(document).ready(function() {
        // initialize tooltipster on text input elements
        // initialize validate plugin on the form
        $("#loginForm").validate({
            showErrors: function(errorMap, errorList) {
                // Clean up any tooltips for valid elements
                $.each(this.validElements(), function (index, element) {
                    var $element = $(element);
                    $element.data("title", "") // Clear the title - there is no error associated anymore
                            .removeClass("error")
                            .tooltip("destroy");
                });
                // Create new tooltips for invalid elements
                $.each(errorList, function (index, error) {
                    var $element = $(error.element);
                    $element.tooltip("destroy") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                            .data("title", error.message)
                            .addClass("error")
                            .tooltip(); // Create a new tooltip based on the error messsage we just set in the title
                });
            },
            rules: {
                userid: {required: true},
                password: {required: true},
                domain:{required:true}
            },
            submitHandler: function(form) {
                form.submit();
                return true;
            }
        });
        $.validator.messages.required = function (param, input) {
                var article = document.getElementById(input.id);
                return article.dataset.label + ' field is required';
        }
        $('.a-middle').css('margin-top', function () {
                return ($(window).height() - $(this).height()) / 4
        });
    });
    // To set Default focus
    window.onload = function() {
        if ($("#userid").val() == "") {
            $("#userid").focus();
        } else if ($("#password").val() == "") {
             $("#password").focus();
        }
    };
</script>
<div class="CMN_display_block box100per a-middle">
    <!-- Session msg -->
    <div align="center" class="" style="color: green;height: 50px;">
    @if(Session::has('message'))
        <p class="alert {{ Session::get('alert', Session::get('message') ) }}">
          {{ Session::get('message') }}
        </p>
    @endif
    </div>
    <!-- Session msg -->
    <div id="login" class="">
        <h2><span class="fa fa-sign-in"></span><span class="ml5"> Log In</span></h2>
        <form class="form-horizontal" role="form" name="loginForm" id="loginForm" method="POST" 
                action="{{ url('/login') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <fieldset style="height: 245px;border-radius: 0 0 20px 20px !important;">
                <p><label for="userid">Emp Id</label></p>
                <p>
                <input id="userid" type="text" class="form-control loginusername" name="userid" 
                        value="{{ old('userid') }}" data-label="Employee Id" style="height: 34px;">
               </p>
                <p><label for="password">Password</label></p>
                <p>
                    <input id="password" type="password" class="form-control loginusername" name="password" 
                            data-label="Password" style="height: 34px;">
                </p>
                <p class="mt15">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-btn fa-sign-in"></i> Login
                    </button>
                    <!-- <a class="btn btn-link" href="{{ url('/forgetpassword?time='.date('YmdHis')) }}">Forgot Your Password ?</a> -->
                </p>
            </fieldset>
            <!-- Session msg -->
            <div align="center" class="" style="color: red;height: 50px;">
            @if(Session::has('error'))
                <p class="alert {{ Session::get('alert', Session::get('error') ) }}">
                  {{ Session::get('error') }}
                </p>
            @endif
            </div>
            <!-- Session msg -->
        </form>
    </div>
</div>
<script type = "text/javascript" >
    history.pushState(null, null, '');
    window.addEventListener('popstate', function(event) {
    history.pushState(null, null, '');
    });
    $(window).load(function() {
    // Animate loader off screen
    $(".se-pre-con").fadeOut();
});
</script>
@endsection