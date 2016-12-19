@extends('app.layouts.frontend')

@section('page_title')
{{ trans('global.activate_account') }} - {{ \GMC\Core\Settings::get('app_title', Config::get('system.title')) }}
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-offset-3 col-md-6">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h2>{{ trans('global.activate_account') }}</h2>
<?php
// Valid token
if($valid_token)
{
	echo '<div class="alert alert-success">';
	echo trans('global.account_activated');
	echo ' <a href="' . url('/login') . '">' . trans('global.click_to_login') . '</a>';
	echo '</div>';
}
else
{
	echo '<div class="alert alert-warning">';
	echo trans('global.activate_token_not_found');
	echo '</div>';
}
?>
                </div>
                <div class="panel-footer text-right">
                    <a href="{{ url('/login') }}" class="btn btn-xs btn-info"><i class="fa fa-angle-right"></i> {{ trans('global.login') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop