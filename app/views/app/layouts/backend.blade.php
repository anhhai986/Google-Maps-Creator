<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ URL::asset('favicon.ico') }}">

    <title>@yield('page_title')</title>

    <link href="{{ URL::asset('assets/vendor/bootswatch-dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/vendor/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/vendor/select2/select2.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/vendor/select2/select2-bootstrap.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/vendor/summernote/dist/summernote.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/vendor/jquery.bootgrid/dist/jquery.bootgrid.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/css/global.css') }}" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
@yield('head')
  </head>

  <body>

    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="{{ url('/dashboard') }}"><i class="fa fa-map-marker"></i> {{ \GMC\Core\Settings::get('app_title', Config::get('system.title')) }}</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li<?php if(Request::url() == url('/dashboard/user/settings')) echo ' class="active"'; ?>><a href="{{ url('/dashboard/user/settings') }}">{{ Auth::user()->name; }}</a></li>
            <li><a href="{{ url('/logout') }}"><i class="fa fa-power-off"></i> {{ trans('global.logout') }}</a></li>
          </ul>
<?php /*
          <form class="navbar-form navbar-right">
            <input type="text" class="form-control" placeholder="{{ trans('global.search') }}">
          </form>
*/ ?>
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li<?php if(Request::url() == url('/dashboard')) echo ' class="active"'; ?>><a href="{{ url('/dashboard') }}">{{ trans('global.categories') }}</a></li>
<?php
foreach($oCats as $cat) { 
?>
            <li<?php if(Request::get('category_id') == $cat->id || (Request::url() == url('/dashboard/category') && Request::get('id') == $cat->id)) echo ' class="active"'; ?>><a href="{{ url('/dashboard/items?category_id=' . $cat->id) }}"><i class="fa fa-map-marker"></i> {{ $cat->name }}</a></li>
<?php
}
?>
            <li<?php if(Request::url() == url('/dashboard/category') && Request::get('id', '') == '') echo ' class="active"'; ?>><a href="{{ url('/dashboard/category') }}" style="font-weight:bold"><i class="fa fa-plus-square"></i> {{ trans('global.new_category') }}</a></li>
          </ul>
          <ul class="nav nav-sidebar">
            <li<?php if(Request::url() == url('/dashboard/user/settings')) echo ' class="active"'; ?>><a href="{{ url('/dashboard/user/settings') }}">{{ trans('global.personal_settings') }}</a></li>
<?php
if(Auth::user()->role == 1 && Config::get('system.user_management'))
{
?>
              <li<?php if(Request::url() == url('/dashboard/settings')) echo ' class="active"'; ?>><a href="{{ url('/dashboard/settings') }}">{{ trans('global.general_settings') }}</a></li>
              <li<?php if(Request::url() == url('/dashboard/users') || Request::url() == url('/dashboard/users/user')) echo ' class="active"'; ?>><a href="{{ url('/dashboard/users') }}">{{ trans('global.user_management') }}</a></li>
<?php
}
?>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
			@yield('content')
        </div>
      </div>
    </div>

    <script src="{{ URL::asset('assets/vendor/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('assets/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('assets/vendor/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js') }}"></script>
    <script src="{{ URL::asset('assets/vendor/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('assets/vendor/summernote/dist/summernote.min.js') }}"></script>
    <script src="{{ URL::asset('assets/vendor/jquery.bootgrid/dist/jquery.bootgrid.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/app.js') }}"></script>
    <script src="{{ URL::asset('assets/js/global.js') }}"></script>
@yield('custom_script')
  </body>
</html>