@extends('app.layouts.backend')

@section('page_title')
{{ $page_title }} - {{ \GMC\Core\Settings::get('app_title', Config::get('system.title')) }}
@endsection

@section('content')
 	<div class="row">
		<div class="col-md-12">

			<h1 class="page-header">{{ $page_title }}</h1>

			<ol class="breadcrumb">
			  <li><a href="{{ url('/dashboard') }}">{{ trans('global.categories') }}</a></li>
			  <li><a href="{{ url('/dashboard/items?category_id=' . $category_id) }}">{{ $oCheck->name }}</a></li>
			  <li><a href="{{ url('/dashboard/options?category_id=' . $category_id) }}">{{ trans('global.options') }}</a></li>
			  <li class="active">{{ $page_title }}</li>
			</ol>
		</div>
	</div>
 	<div class="row">
		<div class="col-md-12">
            <div class="list-actions">
			  <a href="#" onclick="history.go(-1); return false;" class="btn btn-default"><i class="fa fa-arrow-left"></i> {{ trans('global.back') }}</a>
			</div>
<?php

if($error)
{
	echo '<div class="alert alert-danger">' . $error . '</div>';
}

if($message)
{
	echo '<div class="alert alert-success">' . $message . '</div>';
}

echo Former::horizontal_open()
	->action(url('api/v1/option/save'))
	->method('POST');

echo Former::hidden()
    ->value($oOption->id)
    ->name('id');

echo Former::hidden()
    ->value($category_id)
    ->name('category_id');

echo Former::text()
    ->name('name')
    ->forceValue($oOption->name)
	->label(trans('global.name'))
    ->autofocus()
    ->required();

echo '<hr>';

echo Former::checkbox()
    ->name('active')
    ->label(' ')
	->check($oOption->active)
	->text(trans('global.active'));

echo Former::actions()
    ->lg_primary_submit(trans('global.save_changes'))
    ->lg_default_link(trans('global.cancel'), url('/dashboard/options?category_id=' . $category_id));

echo Former::close();
?>
	</div>
</div>
@endsection