<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="{{ URL::asset('favicon.ico') }}">

    <title>@yield('page_title')</title>

    <link href="{{ URL::asset('assets/vendor/bootswatch-dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/vendor/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/css/front.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/css/global.css') }}" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
@yield('head')
  </head>

  <body>
	<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
	  <div class="container-fluid">
		<div class="navbar-header">
		  <a class="navbar-brand" href="{{ url('/') }}"><i class="fa fa-map-marker"></i> {{ \GMC\Core\Settings::get('app_title', Config::get('system.title')) }}</a>
		</div>
	  </div>
	</nav>

@yield('content')

    <script src="{{ URL::asset('assets/vendor/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('assets/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/jquery.shards.js') }}"></script>
    <script src="{{ URL::asset('assets/js/global.js') }}"></script>
@yield('custom_script')
  </body>
</html>