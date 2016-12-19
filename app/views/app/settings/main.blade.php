@extends('app.layouts.backend')

@section('page_title')
{{ trans('global.settings') }} - {{ \GMC\Core\Settings::get('app_title', Config::get('system.title')) }}
@endsection

@section('content')
 	<div class="row">
		<div class="col-md-12">

            <h1 class="page-header">{{ trans('global.general_settings') }}</h1>

			<ol class="breadcrumb">
			  <li><a href="{{ url('/dashboard') }}">{{ trans('global.dashboard') }}</a></li>
			  <li class="active">{{ trans('global.general_settings') }}</li>
			</ol>
		</div>
	</div>

<?php
if($message)
{
	echo '<div class="alert alert-success">' . $message . '</div>';
}

echo Former::vertical_open()
	->action(url('api/v1/app/settings'))
	->method('POST');
?>
 
 	<div class="row">
		<div class="col-md-12">

			<ul class="nav nav-tabs" id="tab1">
			  <li class="active"><a href="#application" data-toggle="tab">{{ trans('global.application') }}</a></li>
			  <li><a href="#user_management" data-toggle="tab">{{ trans('global.user_management') }}</a></li>
			</ul>

			<div class="tab-content">
			  <div class="tab-pane fade in active" id="application">

				<div class="panel panel-primary">
					<div class="panel-body">

<?php

echo Former::text()
    ->name('app_title')
    ->value(GMC\Core\Settings::get('app_title', Config::get('system.title')))
	->label(trans('global.title'))
	->help(trans('global.app_title_help'))
    ->required();

echo '<hr>';

echo '<div class="row"><div class="col-md-6">';

echo Former::text()
    ->name('mail_from_name')
    ->value(GMC\Core\Settings::get('mail_from_name', $aMailConfig['name']))
	->label(trans('global.mail_from_name'))
	->help(trans('global.mail_from_name_help'))
    ->required();

echo '</div><div class="col-md-6">';

echo Former::text()
    ->name('mail_from_address')
    ->value(GMC\Core\Settings::get('mail_from_address', $aMailConfig['address']))
	->label(trans('global.mail_from_address'))
	->help(trans('global.mail_from_address_help'))
    ->required();

echo '</div></div>';

?>

					</div>
				</div>

			  </div>

			  <div class="tab-pane fade" id="user_management">
				<div class="panel panel-primary">
					<div class="panel-body">
<?php
echo Former::checkbox()
    ->name('allow_signup')
    ->label(false)
    ->value(1)
	->check($allow_signup)
    ->help(trans('global.allow_signup_help'))
	->text(trans('global.allow_signup'));

?>
		  
				  </div>
				</div>
			  </div>
			</div>


	<button type="reset" class="btn btn-default">{{ trans('global.reset') }}</button>
	<button type="submit" class="btn btn-primary ladda-button">{{ trans('global.save_changes') }}</button>

		</div>	
	</div>
<?php
echo Former::close();
?>

@stop

@section('custom_script')

<script type="text/javascript">

</script>

@stop