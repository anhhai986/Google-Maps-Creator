@extends('app.layouts.frontend')

@section('page_title')
{{ trans('global.reset_password') }} - {{ \GMC\Core\Settings::get('app_title', Config::get('system.title')) }}
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-offset-4 col-md-4">

			<ol class="breadcrumb">
			  <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
			  <li><a href="{{ url('/login') }}">{{ trans('global.login') }}</a></li>
			  <li class="active">{{ trans('global.reset_password') }}</li>
			</ol>

            <div class="panel panel-default">
                <div class="panel-body">
<?php
if($valid_token)
{
	$error = Session::get('error', false);

	if($error)
	{
		echo '<div class="alert alert-danger">' . $error . '</div>';
	}
}

// Valid token
if($valid_token)
{
	echo Former::vertical_open()
		->class('form-signin')
		->action(url('api/v1/auth/reset'))
		->method('POST');

	echo Former::hidden()
		->name('token')
		->value($token)
		->required();

	echo Former::password()
		->name('pass1')
		->forceValue(Input::old('pass1'))
		->autofocus()
		->label(trans('global.new_password'))
		->required();

	echo Former::password()
		->name('pass2')
		->forceValue(Input::old('pass2'))
		->label(trans('global.confirm_password'))
		->required();

    echo Former::actions();
	echo Former::submit(trans('global.reset_password'))->class('btn-lg form-control btn-primary btn');

    echo Former::close();
}
else
{
	echo '<div class="alert alert-warning">';
	echo trans('global.reset_password_token_not_found');
	echo '</div>';
}
?>
                </div>
            </div>
        </div>
    </div>
</div>
@stop