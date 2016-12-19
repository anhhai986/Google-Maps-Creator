@extends('app.layouts.backend')

@section('page_title')
{{ trans('global.personal_settings') }} - {{ \GMC\Core\Settings::get('app_title', Config::get('system.title')) }}
@endsection

@section('content')
 	<div class="row">
		<div class="col-md-12">

            <h1 class="page-header">{{ trans('global.personal_settings') }}</h1>

			<ol class="breadcrumb">
			  <li><a href="{{ url('/dashboard') }}">{{ trans('global.dashboard') }}</a></li>
			  <li class="active">{{ trans('global.personal_settings') }}</li>
			</ol>
		</div>
	</div>
<?php
echo Former::horizontal_open()
	->action(url('api/v1/auth/update-account'))
	->method('POST');
?>
<?php if(isset($_GET['change_mail'])) { ?>
<div class="alert alert-danger alert-dismissable">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	{{ trans('global.change_mail_msg') }}
</div>
<?php } ?>
<?php

if($error)
{
	echo '<div class="alert alert-danger">' . $error . '</div>';
}

if($message)
{
	echo '<div class="alert alert-success">' . $message . '</div>';
}

echo Former::hidden()
    ->forceValue($oUser->id)
    ->name('id');

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
    ->forceValue($oUser->timezone)
	->options(trans('timezones.timezones'))
	->label(trans('global.timezone'))
    ->name('timezone')
    ->required();

echo '<hr>';
echo '<div class="col-lg-2 col-sm-4"></div><div class="col-lg-10 col-sm-8"><p>' . trans('global.new_password_msg') . '</p></div>';

echo Former::password()
    ->id('pass1')
    ->name('pass1')
	->label(trans('global.new_password'));

echo Former::password()
    ->id('pass2')
    ->name('pass2')
	->label(trans('global.confirm'));

echo '<hr>';

echo '<div class="col-lg-2 col-sm-4"></div><div class="col-lg-10 col-sm-8"><p>' . trans('global.current_password_msg') . '</p></div>';

echo Former::password()
    ->id('password')
    ->name('password')
	->label(trans('global.current_password'))
    ->required();

echo Former::actions()
    ->lg_primary_submit(trans('global.save_changes'));

echo Former::close();

?>
@endsection