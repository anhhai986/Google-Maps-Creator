@extends('app.layouts.frontend')

@section('page_title')
{{ trans('global.login') }} - {{ \GMC\Core\Settings::get('app_title', Config::get('system.title')) }}
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-offset-4 col-md-4">
			<ul class="nav nav-tabs nav-justified">
				<li class="active"><a href="#" data-toggle="tab">{{ trans('global.login') }}</a></li>
<?php
if($allow_signup)
{
?>
				<li><a href="{{ url('/signup') }}">{{ trans('global.sign_up') }}</a></li>
<?php
}
?>
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

if(\File::isDirectory(base_path() . '/../reset-demo'))
{
?>
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        The demo is reset every hour. If you can't login, please check back later.
                    </div>
<?php
}

$error = Session::get('error', false);

if($error)
{
	echo '<div class="alert alert-danger">' . $error . '</div>';
}

echo Former::vertical_open()
	->class('form-signin')
	->action(url('api/v1/auth/login'))
	->method('POST');

$val = \File::isDirectory(base_path() . '/../reset-demo') ? 'info@example.com': Input::old('email');

echo Former::text()
    ->name('email')
	->label(trans('global.email'))
    ->forceValue($val)
    ->autofocus()
    ->tabindex(1)
    ->required();

$val = \File::isDirectory(base_path() . '/../reset-demo') ? 'welcome': '';

echo Former::password()
    ->name('password')
	->label(trans('global.password') . ' | <a href="' . url('/reminder') . '">' . trans('global.forgot_password') . '</a>')
    ->tabindex(2)
    ->forceValue($val)
    ->required();

echo Former::checkbox()
    ->name('remember')
    ->label('')
    ->value(1)
	->check((bool) Input::old('remember', false))
    ->tabindex(3)
	->text(trans('global.remember_me'));

echo Former::actions();
echo Former::submit(trans('global.login'))->class('btn-lg form-control btn-primary btn')->tabindex(4);

echo Former::close();
?>
                </div>
            </div>
        </div>
    </div>
</div>
@stop