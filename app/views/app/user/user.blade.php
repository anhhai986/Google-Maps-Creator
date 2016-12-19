@extends('app.layouts.backend')

@section('page_title')
{{ $page_title }} - {{ \GMC\Core\Settings::get('app_title', Config::get('system.title')) }}
@endsection

@section('content')
 	<div class="row">
		<div class="col-md-12">

			<ol class="breadcrumb">
			  <li><a href="{{ url('/dashboard') }}">{{ trans('global.dashboard') }}</a></li>
			  <li><a href="{{ url('/dashboard/users') }}">{{ trans('global.user_management') }}</a></li>
			  <li class="active">{{ $page_title }}</li>
			</ol>

			<h1 class="page-header">{{ $page_title }}</h1>
		</div>
	</div>
<?php

$error = Session::get('error', false);

if($error)
{
	echo '<div class="alert alert-danger">' . $error . '</div>';
}

$message = Session::get('message', false);

if($message)
{
	echo '<div class="alert alert-success">' . $message . '</div>';
}

echo Former::horizontal_open()
	->action(url('api/v1/auth/account'))
	->method('POST');

echo Former::hidden()
    ->value($oUser->id)
    ->name('id');

$el = Former::select('role')
    ->name('role')
    ->forceValue($oUser->role)
	->options(trans('global.roles'))
	->label(trans('global.role'))
    ->required();

if($user_id == 1) $el->disabled(true);

echo $el;

echo '<hr>';

echo Former::text()
    ->name('name')
    ->forceValue($oUser->name)
	->label(trans('global.name'))
    ->required();

echo Former::text()
    ->name('email')
    ->forceValue($oUser->email)
	->label(trans('global.email'))
    ->required();

echo Former::select('timezone')
    ->name('timezone')
    ->forceValue($oUser->timezone)
	->options(trans('timezones.timezones'))
	->label(trans('global.timezone'))
    ->required();

echo '<hr>';

if($user_id > 0) 
{
    echo '<div class="col-lg-2 col-sm-4"></div><div class="col-lg-10 col-sm-8"><p>' . trans('global.new_password_msg') . '</p></div>';
}

$el = Former::password()
    ->name('pass1')
	->label(trans('global.password'));

if($user_id == 0) $el->required();

echo $el;

$el = Former::password()
    ->name('pass2')
	->label(trans('global.confirm'));

if($user_id == 0) $el->required();

echo $el;

echo '<hr>';

echo Former::checkbox()
    ->name('active')
    ->label(' ')
	->check($oUser->active)
    ->help(trans('global.active_info'))
	->text(trans('global.active'));

echo Former::actions()
    ->lg_primary_submit(trans('global.save_changes'));

echo Former::close();
?>
@endsection