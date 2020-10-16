@extends('layouts.layout')
@section('content')
  <script type="text/javascript" src="{{ URL::asset('resources/assets/js/forgetpassword.js') }}"></script>
  <link rel="stylesheet" href="{{ URL::asset('resources/assets/css/common.css') }}" />
  <link rel="stylesheet" href="{{ URL::asset('resources/assets/css/paddingmargin.css') }}" />
  {{ HTML::script('resources/assets/js/common.js') }}
  <script type="text/javascript">
    var datetime = '<?php echo date('Ymdhis'); ?>';
    function passwordcancel() {
      window.history.back();
    }
  </script>
  <style type="text/css">
    .alertboxalign {
      margin-bottom: -60px !important;
    }
  </style>
<div class="container">
  <div class="row">
    <div class="col-xs-8 col-xs-offset-2">
        <!-- Session msg -->
          @if(Session::has('message'))
            <div  align="center" class="mb35" style="height: 20px;">
              <p class="alert {{ Session::get('alert', Session::get('type') ) }}">
                {{ Session::get('message') }}
              </p>
            </div>
          @endif
          <!-- Session msg -->
        <div class="panel panel-default mt10" align="center">
            <div class="panel-heading" style="height: 49px;">
              <div class="pull-left">
                <span style="font-size: 20px;">
                <i class="fa fa-key" aria-hidden="true"> </i>
                  <span class="ml5">{{ trans('messages.lbl_passwordchange') }}</span>
                </span>
              </div>
            </div>
            {{ Form::open(array('name'=>'forgetpassfrm', 'id'=>'forgetpassfrm', 
                        'class' => 'form-horizontal',
                        'files'=>true,
                        'url' => 'addeditprocess', 
                        'method' => 'POST')) }}
              <div class="panel-body">
                  <div class="col-xs-12">
                      <label class="col-xs-4  control-label clr_blue text-right">
                        {{ trans('messages.lbl_usercodeemailid') }}
                        <span class="red"> * </span></label>
                      <div class="col-xs-7">
                          {{ Form::text('email',null,array('id'=>'email','style'=>'height:34px;',
                                      'class'=>'pull-left email form-control')) }}
                      </div>
                      <div class="email_err ml130" style="">&nbsp;</div>
                  </div>
              </div>
              <div class="" align="center" style="margin-bottom: 15px;">
                  <button type="submit" id="register" class="pageload add btn btn-success box100 addeditprocess">
                  <i class="fa fa-key"></i> {{ trans('Reset') }} 
                  </button>
                  <a href="javascript:passwordcancel();" class="btn btn-danger "><span class="fa fa-times"></span> {{ trans('messages.lbl_cancel') }} </a>
              </div>
            {{ Form::close() }}
            </div>
          </div>
      </div>
  </div>
</div>
@endsection