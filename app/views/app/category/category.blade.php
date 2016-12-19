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
<?php if($category_id > 0) { ?>
			  <li><a href="{{ url('/dashboard/items?category_id=' . $category_id) }}">{{ $page_title }}</a></li>
<?php } else { ?>
			  <li class="active">{{ $page_title }}</li>
<?php } ?>
			</ol>
		</div>
	</div>

 	<div class="row">
		<div class="col-md-12">
            <div class="list-actions">
			  <a href="#" onclick="history.go(-1); return false;" class="btn btn-default"><i class="fa fa-arrow-left"></i> {{ trans('global.back') }}</a>
<?php
if($category_id > 0)
{
?>
			  <button type="button" class="btn btn-danger" rel="tooltip" title="{{ trans('global.embed_map') }}" data-toggle="popover" data-container="body" data-content="<?php echo \GMC\Core\CategoryHelpers::getEmbed($category_id) ?>"><i class="fa fa-code"></i></button>
			  <a href="{{ \GMC\Core\CategoryHelpers::getLink($category_id) }}" class="btn btn-info" target="_blank" data-toggle="tooltip" title="{{ trans('global.view_category') }}"><i class="fa fa-external-link-square"></i></a>
<?php
}
?>
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
	->action(url('api/v1/category/save'))
	->method('POST');

echo Former::hidden()
    ->value($oCat->id)
    ->name('id');

echo Former::text()
    ->name('name')
    ->forceValue($oCat->name)
	->label(trans('global.name'))
    ->autofocus()
    ->required();

echo Former::select('language')->options(GMC\Core\CategoryHelpers::getLanguages())
  ->forceValue(GMC\Core\CategoryHelpers::getLanguage($language));

$map_styles = \GMC\Model\MapStyle::all()->lists('thumb', 'id');

echo Former::select('map_style_id')->options($map_styles)
	->label(trans('global.style'))
	->forceValue($map_style_id);

echo Former::select('theme')->options(GMC\Core\CategoryHelpers::getThemes())
  ->forceValue(GMC\Core\CategoryHelpers::getTheme($theme, true));

echo Former::select('marker')->options(GMC\Core\CategoryHelpers::getMarkers())
  ->forceValue(GMC\Core\CategoryHelpers::getMarker($oCat->marker, true))
  ->help(trans('global.marker_category_help'));

echo '<hr>';

echo Former::checkbox()
    ->name('active')
    ->label(' ')
	->check($oCat->active)
	->text(trans('global.active'));

echo Former::actions()
    ->lg_primary_submit(trans('global.save_changes'))
    ->lg_default_link(trans('global.cancel'), url('/dashboard'));

echo Former::close();
?>
	</div>
</div>
@endsection

@section('custom_script')

<script type="text/javascript">

function format_marker(marker) {
    if(!marker.id) return marker.text; // optgroup
    return "<img src='{{ url('/assets/img/markers/') }}/" + marker.id + "'/> " + marker.text;
}

function format_theme(theme) {
    if(!theme.id) return theme.text; // optgroup
    return "<img src='{{ url('/assets/vendor/bootswatch/') }}/" + theme.id.toLowerCase() + "/thumbnail.png' style='width:160px'/> ";
}

function format_map_style(style) {
    if(!style.id) return style.text; // optgroup
    return "<img src='{{ url('/assets/img/map-styles/') }}/" + style.text + "' style='width:160px'/> ";
}

$(function() {
	$("#marker").select2({
		formatResult: format_marker,
		formatSelection: format_marker,
		escapeMarkup: function(m) { return m; }
	});
	$("#theme").select2({
		formatResult: format_theme,
		formatSelection: format_theme,
		escapeMarkup: function(m) { return m; }
	});
	$("#map_style_id").select2({
		formatResult: format_map_style,
		formatSelection: format_map_style,
		escapeMarkup: function(m) { return m; }
	});
});

</script>

@stop