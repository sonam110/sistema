<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="_token" content="{{ csrf_token() }}">
<meta name="msapplication-TileColor" content="#ff685c">
<meta name="theme-color" content="#32cafe">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<meta name="HandheldFriendly" content="True">
<meta name="MobileOptimized" content="320">
<link rel="icon" href="{{ env('CDN_URL')}}/img/logo-ds.png" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="{{ env('CDN_URL')}}/img/logo-ds.png" />

<title>{{env('APP_NAME')}}</title>
<link href="https://fonts.googleapis.com/css?family=Comfortaa:300,400,700" rel="stylesheet">
{!! Html::style('assets/fonts/fonts/font-awesome.min.css') !!}
{!! Html::style('assets/css/dashboard.css') !!}
{!! Html::style('assets/plugins/scroll-bar/jquery.mCustomScrollbar.css') !!}
{!! Html::style('assets/plugins/toggle-sidebar/sidemenu.css') !!}
{!! Html::style('assets/plugins/iconfonts/plugin.css') !!}
{!! Html::style('assets/plugins/toastr/toastr.min.css') !!}
{!! Html::style('assets/plugins/datatable/dataTables.bootstrap4.min.css') !!}
{!! Html::style('assets/plugins/select2/select2.min.css') !!}
{!! Html::style('assets/css/custom.css') !!}
