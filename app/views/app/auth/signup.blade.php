@extends('app.layouts.frontend')

@section('page_title')
{{ trans('global.sign_up_for_account') }} - {{ \GMC\Core\Settings::get('app_title', Config::get('system.title')) }}
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-offset-4 col-md-4">
			<ul class="nav nav-tabs nav-justified">
				<li><a href="{{ url('/login') }}">{{ trans('global.login') }}</a></li>
				<li class="active"><a href="{{ url('/signup') }}">{{ trans('global.sign_up') }}</a></li>
			</ul>
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
	echo '<div class="alert alert-success">' . $message . '</div>';
}

echo Former::vertical_open()
	->class('form-signin')
	->action(url('api/v1/auth/signup'))
	->method('POST');

echo Former::text()
    ->name('name')
    ->forceValue(Input::old('name'))
    ->autofocus()
	->label(trans('global.name'))
    ->required();

echo Former::text()
    ->name('email')
    ->forceValue(Input::old('email'))
	->label(trans('global.email'))
    ->required();

echo Former::password()
    ->name('password')
    ->forceValue(Input::old('password'))
	->label(trans('global.password'))
    ->required();

echo Former::password()
    ->name('confirm_password')
    ->forceValue(Input::old('confirm_password'))
	->label(trans('global.confirm_password'))
    ->required();

echo Former::checkbox()
    ->name('disclaimer')
    ->label('')
    ->value(1)
	->check((bool) Input::old('disclaimer', false))
	->text(trans('global.agree_toc'));

echo Former::actions();
echo Former::submit(trans('global.sign_up'))->class('btn-lg form-control btn-primary btn');

echo Former::close();
?>
                </div>
            </div>
        </div>
    </div>
</div>
@stop