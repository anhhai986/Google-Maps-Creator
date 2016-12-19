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
			  <li><a href="{{ url('/dashboard/items?category_id=' . $category_id) }}">{{ $oCat->name }}</a></li>
			  <li class="active">{{ $crumb_title }}</li>
			</ol>
		</div>
	</div>

 	<div class="row">
		<div class="col-md-12">
            <div class="list-actions">
			  <a href="#" onclick="history.go(-1); return false;" class="btn btn-default"><i class="fa fa-arrow-left"></i> {{ trans('global.back') }}</a>
			  <a href="{{ \GMC\Core\CategoryHelpers::getLink($category_id) }}" class="btn btn-info" target="_blank"><i class="fa fa-external-link-square"></i> {{ trans('global.view_category') }}</a>
			  <button type="button" class="btn btn-danger" rel="tooltip" title="{{ trans('global.embed_map') }}" data-toggle="popover" data-container="body" data-content="<?php echo \GMC\Core\CategoryHelpers::getEmbed($category_id) ?>"><i class="fa fa-code"></i></button>
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
	->action(url('api/v1/item/save'))
	->method('POST');

echo Former::hidden()
    ->value($oItem->id)
    ->name('id');

echo Former::hidden()
    ->value($oItem->category_id)
    ->name('category_id');

echo Former::text()
    ->name('name')
    ->forceValue($oItem->name)
	->label(trans('global.name'))
    ->autofocus($geoError == false)
    ->required();

$address = Former::text()
    ->name('address')
    ->forceValue($oItem->address)
	->label(trans('global.address'))
    ->required();

if($geoError)
{
	$address->state('has-error');
	$address->autofocus();
	$address->help($geoError);
}

echo $address;

echo Former::text()
    ->name('options')
    ->forceValue($oItem->options)
	->label(trans('global.options'))
	->help(trans('global.help_options'));

echo Former::text()
    ->name('phone')
    ->forceValue($oItem->phone)
	->label(trans('global.phone'));

echo Former::text()
    ->name('email')
    ->forceValue($oItem->email)
	->label(trans('global.email'));

echo Former::text()
    ->name('website')
    ->forceValue($oItem->website)
	->label(trans('global.website'));

echo Former::textarea()
    ->name('description')
    ->class('simple-wysiwyg')
    ->forceValue($oItem->description)
	->label(trans('global.description'));

$marker = ($oItem->marker != '') ? \GMC\Core\CategoryHelpers::getMarker($oItem->marker, true, $oItem->category_id) : '';

echo Former::select('marker')->options(GMC\Core\CategoryHelpers::getMarkers(false))
    ->forceValue($marker)
  	->help(trans('global.marker_item_help'));

echo '<hr>';

echo Former::checkbox()
    ->name('active')
    ->label(' ')
	->check($oItem->active)
	->text(trans('global.active'));

echo Former::actions()
    ->lg_primary_submit(trans('global.save_changes'))
    ->lg_default_link(trans('global.cancel'), url('/dashboard/items?category_id=' . $category_id));

echo Former::close();
?>
	</div>
</div>
@endsection

@section('custom_script')
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo \Config::get('google.api_key') ?>&v=3.exp&sensor=false&libraries=places"></script>
<script>


function format_marker(marker) {
    if(!marker.id) return marker.text; // optgroup
    return "<img class='flag' src='{{ url('/assets/img/markers/') }}/" + marker.id + "'/> " + marker.text;
}

$(function() {
	$('.simple-wysiwyg').summernote({
		height: 180,
		toolbar: [
			['style', ['bold', 'italic', 'underline', 'clear']],
			['fontsize', ['fontsize']],
			['color', ['color']],
			['para', ['ul', 'ol', 'paragraph']],
			['insert', ['picture', 'link']],
			['misc', ['codeview']]
		]
	});

    $('#options').select2({
        tags: [{{ $tags }}],
        formatNoMatches: function () { return "{{ trans('global.no_options_found') }}"; },
    });
	$("#marker").select2({
		formatResult: format_marker,
		formatSelection: format_marker,
		placeholder: "",
		allowClear: true,
		escapeMarkup: function(m) { return m; }
	});
});

function initialize()
{
	var input = (document.getElementById('address'));
	var autocomplete = new google.maps.places.Autocomplete(input);
}

google.maps.event.addDomListener(window, 'load', initialize);

</script>
@endsection