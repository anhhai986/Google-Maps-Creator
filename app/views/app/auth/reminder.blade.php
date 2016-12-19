@extends('app.layouts.frontend')

@section('page_title')
{{ trans('global.forgot_password') }} - {{ \GMC\Core\Settings::get('app_title', Config::get('system.title')) }}
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
if(isset($_GET['reset']))
{
?>
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {{ trans('global.password_changed_success') }}
                    </div>
<?php
}

$message = Session::get('message', false);

if($message)
{
	echo '<div class="alert alert-info">' . $message . '</div>';
}

echo Former::vertical_open()
	->class('form-signin')
	->action(url('api/v1/auth/remind'))
	->method('POST');

echo Former::text()
    ->name('email')
    ->forceValue(Input::old('email'))
    ->autofocus()
	->label(trans('global.email'))
    ->help(trans('global.forgot_password_help'))
    ->required();

echo Former::actions();
echo Former::submit(trans('global.reset_password'))->class('btn-lg form-control btn-primary btn');

echo Former::close();
?>
                </div>
            </div>
        </div>
    </div>
</div>
@stop