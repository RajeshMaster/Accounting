<?php
    session_start();
    $_SESSION['previous_location'] = 'homepage';
    $actionName = Route::getCurrentRoute()->getActionName();
    $split_action_name = explode('@', $actionName);
    $action = $split_action_name['1'];
?>
<html>
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" type="text/css" href="{{ URL::asset('resources/assets/css/bootstrap.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ URL::asset('resources/assets/css/widthbox.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ URL::asset('resources/assets/css/common.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ URL::asset('resources/assets/css/paddingmargin.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ URL::asset('resources/assets/css/font-awesome.min.css') }}">
  <title></title>
  <script type="text/javascript">
    var datetime = '<?php echo date('Ymdhis'); ?>';
  </script>
  <script type="text/javascript">
  function previouspage(path) {
    $('#mysqlerrorform').action(path)
        $('#mysqlerrorform').submit();
    }
    </script>
</head>
<body>
{{ Form::open(array( 'id'=>'mysqlerrorform','name'=>'mysqlerrorform', 
'files'=>true,'method' => 'POST')) }}
<div class="box100per pr10 pl10" style="min-height: 150px">
        <table class="tablealternate box100per mt30">
          <colgroup>
          <col width="7%">
            <col width="10%">
            <col width="">
            <col width="8%">
            <col width="50%">
          </colgroup>
          <thead class="CMN_tbltheadcolor">
            <tr class="tableheader fwb tac">
              <th class="fwb">{{ trans('messages.lbl_sno') }}</th>
              <th class="fwb">{{ trans('messages.lbl_date') }}</th>
              <th class="fwb">{{ trans('messages.lbl_fileapath') }}</th>
              <th class="fwb">{{ trans('messages.lbl_line') }}</th>
              <th class="fwb">{{ trans('messages.lbl_errorinformation') }}</th>
            </tr>
          </thead>
      <tbody>
      <tr>
        <td class="text-center">{{ "1" }}</td>
        <td class="text-center">{{ date('Y-m-d') }}</td>
        <td style="word-wrap:break-word;">
          @php
           $trace = $e->getTrace();
          @endphp
          {{ $trace[4]['file'] }}
        </td>
        <td class="text-center">
          {{ $trace[4]['line'] }}
        </td>
        <td>
          {{ $e->getMessage() }}
        </td>
      </tr>
        @php 
          $date=date('Ymd');
          $path="storage/error_".$date."/";
          $ldate="Date : ".date('Y-m-d H:i:s')."\n";
          $lpath="Path : ".$trace[4]['file']."\n";
          $lline="Line : ".$trace[4]['line']."\n";
          $lmsg="Message : ".$e->getMessage()."\n\n";
          $contents=$ldate.$lpath.$lline.$lmsg;
          $file=$path.$date.".php";

          if(is_dir($path)) {
            File::append($file, $contents);
          } else {
            File::makeDirectory($path);
            File::put($file, $contents);
          }
        @endphp
      </table>
      </div>
    <br>
      <fieldset class="bg-info ml10 mr10">
        <div class="form-group">
            <div align="center" class="mt5">
              <a href="{{ URL::previous() }}" class="box7per fwb mt5 btn btn-info box100">
                  <i class="fa fa-arrow-left"></i>{{trans('messages.lbl_back')}}
              </a>
            </div>
        </div>
    </fieldset>
        {{ Form::close() }}
  </body>
</html>