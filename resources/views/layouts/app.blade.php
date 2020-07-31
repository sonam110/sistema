<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    @include('includes.head')
    <style>
  .chat {
    list-style: none;
    margin: 0;
    padding: 0;
  }

  .chat li {
    margin-bottom: 10px;
    padding-bottom: 5px;
    border-bottom: 1px dotted #B3A9A9;
  }

  .chat li .chat-body p {
    margin: 0;
    color: #777777;
  }

  .panel-body {
    overflow-y: scroll;
    height: 350px;
  }

  ::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
    background-color: #F5F5F5;
  }

  ::-webkit-scrollbar {
    width: 12px;
    background-color: #F5F5F5;
  }

  ::-webkit-scrollbar-thumb {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
    background-color: #555;
  }
</style>
    <script type="text/javascript">
      var appurl = '{{url("/")}}/';
    </script>
</head>
<body class="login-img">
    <div class="page">
        <div class="page-single">
            @yield('content')
        </div>
    </div>
{!! Html::script('assets/js/vendors/jquery-3.2.1.min.js') !!}
{!! Html::script('assets/js/vendors/bootstrap.bundle.min.js') !!}
{!! Html::script('assets/js/vendors/jquery.sparkline.min.js') !!}
{!! Html::script('assets/plugins/scroll-bar/jquery.mCustomScrollbar.concat.min.js') !!}
@yield('extrajs')
</body>
</html>
