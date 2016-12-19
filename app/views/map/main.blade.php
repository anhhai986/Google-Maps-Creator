@extends('map.layouts.master')
<?php
$show_cluster = true;
$limit_sidebar = 1000;
$unit = "K"; // K = KM, M = Miles

switch($unit)
{
	case 'K': $unit_label = "km"; break;
	case 'M': $unit_label = "mi"; break;
}

use Ivory\GoogleMap\Map;
use Ivory\GoogleMap\Base\Coordinate;

use Ivory\GoogleMap\Helper\Builder\ApiHelperBuilder;
use Ivory\GoogleMap\Helper\Builder\MapHelperBuilder;

use Ivory\GoogleMap\Overlay\Marker;
use Ivory\GoogleMap\Overlay\MarkerCluster;
use Ivory\GoogleMap\Overlay\Animation;
use Ivory\GoogleMap\Overlay\InfoWindow;
use Ivory\GoogleMap\Overlay\Icon;
use Ivory\GoogleMap\Overlay\MarkerClusterType;

use Ivory\GoogleMap\Events\Event;

use Ivory\GoogleMap\Helper\MapHelper;

use Ivory\GoogleMap\Control\ControlPosition;
use Ivory\GoogleMap\Control\MapTypeControl;
use Ivory\GoogleMap\Control\MapTypeControlStyle;
use Ivory\GoogleMap\MapTypeId;
use Ivory\GoogleMap\Control\StreetViewControl;
use Ivory\GoogleMap\Control\OverviewMapControl;
use Ivory\GoogleMap\Control\ZoomControl;
use Ivory\GoogleMap\Control\ZoomControlStyle;

use Ivory\GoogleMap\Helper\Builder\PlaceAutocompleteHelperBuilder;

use Ivory\GoogleMap\Place\Autocomplete;
use Ivory\GoogleMap\Place\AutocompleteComponentRestriction;
use Ivory\GoogleMap\Place\AutocompleteType;
use Ivory\GoogleMap\Helper\Place\AutocompleteHelper;

$cat = \GMC\Core\CategoryHelpers::parseLink(Request::get('m'));
$q = Request::get('q', '');

$oMap = \GMC\Model\Category::find($cat['id']);
$oItems = $oMap->items()->where('active', '=', '1')->get();
$oOptions = $oMap->options()->where('active', '=', '1')->get();

if($oMap->map_style_id > 1) $oStyle = \GMC\Model\MapStyle::find($oMap->map_style_id);

if($q == '')
{
	// IP to address
	$geocode = \GMC\Core\GeoHelpers::ip2address($_SERVER['REMOTE_ADDR']);
}
else
{
	$geocode = \GMC\Core\GeoHelpers::ip2address($q);
}

// Is location found?
$location_found = true;

if($geocode['error'])
{
	$location_found = false;
	$geocode = \GMC\Core\GeoHelpers::ip2address($_SERVER['REMOTE_ADDR']);
}

if(isset($geocode['city']) && $geocode['city'] != NULL)
{
	$starting_point = $geocode['city'];
	$starting_point_full = (isset($geocode['country'])) ? $geocode['city'] . ', ' . $geocode['country'] : $geocode['city'];
}

if(! isset($starting_point) && isset($geocode['country']))
{
	$starting_point = $geocode['country'];
    $starting_point_full = $starting_point;
}

if(! isset($starting_point))
{
	$starting_point = $q;
    $starting_point_full = $starting_point;
}
?>

@section('page_title')
{{ $oMap->name }} - {{ \GMC\Core\Settings::get('app_title', Config::get('system.title')) }}
@endsection

@section('content')
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#map-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>
	<div class="collapse navbar-collapse" id="map-collapse">
      <ul class="nav navbar-nav navbar-right">
        <li class="hidden-xs" data-toggle="tooltip" data-placement="bottom" title="{{ trans('map.directions') }}"><a href="#" id="show-directions"><i class="fa fa-car hidden-xs"></i><span class="visible-xs"><i class="fa fa-car"></i> {{ trans('map.directions') }}</span></a></li>
<?php
if(count($oOptions) > 0)
{
?>
        <li class="dropdown" data-toggle="tooltip" data-placement="bottom" title="{{ trans('map.filter') }}">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-filter hidden-xs"></i><span class="visible-xs"><i class="fa fa-filter"></i> {{ trans('map.filter') }}</span></a>
          <ul class="dropdown-menu prevent-close" id="optionFilter">
			<li>
				<div class="dropdown-box prevent-close">
				<div class="checkbox dropdown-header"><h5>{{ trans('map.filter') }}</h5></div>
<?php
echo '<div class="checkbox dropdown-header"><label><input type="checkbox" value="0" checked="checked"> ' . trans('map.no_label') . '</label></div>';
foreach($oOptions as $option)
{
	echo '<div class="checkbox dropdown-header"><label><input type="checkbox" value="' . $option->id . '" checked="checked"> ' . $option->name . '</label></div>';
}
?>
				</div>
			</li>
          </ul>
        </li>
<?php
}
?>
		<li class="hidden-xs">
			<a onClick="return false;">
			<div class="btn-group" data-toggle="buttons">
			  <label class="btn btn-default btn-xs<?php if($unit == 'K') echo ' active'; ?>">
				<input type="radio" name="unit" id="unit-km" value="km"> km
			  </label>
			  <label class="btn btn-default btn-xs<?php if($unit == 'M') echo ' active'; ?>">
				<input type="radio" name="unit" id="unit-mi" value="mi"> mi
			  </label>
			</div>
			</a>
		</li>
<?php /*
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-link hidden-xs"></i> <span class="visible-xs"><i class="fa fa-link"></i> {{ trans('map.link') }}</span></a>
          <ul class="dropdown-menu">
			<li role="presentation" class="dropdown-header">{{ trans('map.link') }}</li>
            <li>
				<div class="dropdown-box prevent-close">
					<input type="text" id="map-link">
				</div>
			</li>
          </ul>
        </li>
*/ ?>
      </ul>
<?php
$autocomplete = new Autocomplete();

//$autocomplete->setPrefixJavascriptVariable('place_autocomplete_');
$autocomplete->setInputId('place_input');

$autocomplete->setInputAttributes(
	array(
		'name' => 'q',
		'value' => $q,
		'class' => 'form-control',
		'placeholder' => $starting_point_full
	));

//$autocomplete->setValue($geocode['city'] . ', ' . $geocode['country']);

$autocomplete->setTypes(array(AutocompleteType::ESTABLISHMENT));
$autocompleteHelper = PlaceAutocompleteHelperBuilder::create()->build();
//$apiHelper = Ivory\GoogleMap\Helper\Builder\ApiHelperBuilder::create()->build();


?>
<div class="row" id="search-container">
	<div class="col col-xs-12 col-sm-8">
      <form class="navbar-form" role="search" method="get">
	  	<input type="hidden" name="m" value="{{ Request::get('m') }}">
        <div class="form-group">
			<div class="input-group{{ (! $location_found) ? ' has-error' : ''; }}">
<?php
echo $autocompleteHelper->render($autocomplete);

?>
			<span class="input-group-btn">
				<button type="submit" class="btn btn-primary map-search"><i class="fa fa-search"></i></button>
			</span>
        	</div>
        </div>
      </form>
	  </div>
	  </div>
   </div>
  </div>
</nav>
<div class="container-fluid full-height" id="map-container">
<div class="row full-height">
	<div class="col col-sm-8 full-height">
<?php
$map = new Map();

$map->setHtmlId('map');
$map->setVariable('map');

$map->setAutoZoom(false);

$map->setCenter(new Coordinate($geocode['latitude'], $geocode['longitude']), true);
$map->setMapOption('zoom', 10);
$map->setMapOption('async', true);
$map->setAutoZoom(true);

$map->setMapOption('mapTypeId', 'roadmap');

$map->setMapOptions(array(
    'disableDefaultUI'       => true,
    'disableDoubleClickZoom' => true,
));

$map->setStylesheetOptions(array(
    'width'  => '100%',
    'height' => '100%',
));

// Markers
$aMarkers = array();

if(count($oItems) > 0)
{
	foreach($oItems as $item)
	{
		$marker = new Marker(new Coordinate($item->lat, $item->lng));
		$marker->setVariable('marker' . $item->id . '');
		//$marker->setPosition($item->lat, $item->lng, true);
		$marker->setAnimation(Animation::DROP);

		$marker->setIcon(new Icon(GMC\Core\CategoryHelpers::getMarker($item->marker, false, $item->category_id)));

		$clickable = true;
		$info = '<div class="info-container">';

		$info .= '<h5>' . $item->name . '</h5>';

		$info .= ($item->description != '') ? '<div class="info-row">' . $item->description . '</div>' : '';
		$info .= ($item->address != '') ? '<div class="info-row"><i class="fa fa-external-link-square"></i> ' . $item->address . '</div>' : '';
		$info .= ($item->phone != '') ? '<div class="info-row"><i class="fa fa-phone-square"></i> ' . $item->phone . '</div>' : '';
		$info .= ($item->email != '') ? '<div class="info-row"><i class="fa fa-envelope-square"></i> <a href="mailto:' . $item->email . '">' . $item->email . '</a></div>' : '';

		if($item->website != '')
		{
			$website = $item->website;
			if(0 !== strpos($website, 'http://') && 0 !== strpos($website, 'https://')) {
			   $website = 'http://' . $website;
			}

			$info .= '<div class="info-row" style="margin-top:10px">';
			$info .= '<a href="' . $website . '" class="btn btn-primary btn-block" target="_blank">' . trans('map.visit_website') . '</a>';
			$info .= '</div>';
		}

		$info .= '<div class="info-actions">';
		$info .= '<a href="#" class="zoom-to" data-lat="' . $item->lat . '" data-lng="' . $item->lng . '">' . trans('map.zoom') . '</a>';
		$info .= ' | ';
		$info .= '<a href="#" class="route-to" data-lat="' . $item->lat . '" data-lng="' . $item->lng . '" data-address="' . str_replace('"', '&quot;', $item->address) . '">' . trans('map.directions') . '</a>';
		$info .= '</div>';

		$info .= '</div>';

		$infoWindow = new InfoWindow($info);
		$infoWindow->setVariable('infoWindow' . $item->id);
    
		$marker->setInfoWindow($infoWindow);

		$options_numeric = implode(',', $item->optionsList(false));
		if($options_numeric == null) $options_numeric = '';

		$marker->setOptions(array(
			'clickable' => $clickable,
			'flat'      => true,
			'label'      => (string) $options_numeric
		));

    $map->getOverlayManager()->addMarker($marker);

		$distance_km = \GMC\Core\GeoHelpers::distance($geocode['latitude'], $geocode['longitude'], $item->lat, $item->lng, 'K');
		$distance_km = round($distance_km, 1);

		$distance_mi = \GMC\Core\GeoHelpers::distance($geocode['latitude'], $geocode['longitude'], $item->lat, $item->lng, 'M');
		$distance_mi = round($distance_mi, 1);

		$options = implode(', ', $item->optionsList(true));

		$aMarkers[] = array(
			'id' => $item->id,
			'name' => $item->name,
			'address' => $item->address,
			'lat' => $item->lat,
			'lng' => $item->lng,
			'distance_km' => $distance_km,
			'distance_mi' => $distance_mi,
			'options' => $options,
			'options_numeric' => $options_numeric
		);
	}
}

// Sort markers
$aMarkers = array_values(array_sort($aMarkers, function($value)
{
    return $value['distance_km'];
}));

// Controls
$mapTypeControl = new MapTypeControl(
    [MapTypeId::ROADMAP, MapTypeId::SATELLITE],
    ControlPosition::TOP_RIGHT,
    MapTypeControlStyle::DEFAULT_);

$map->getControlManager()->setMapTypeControl($mapTypeControl);


// Overview map control
/*
$overviewMapControl = new OverviewMapControl();
$overviewMapControl->setOpened(true);
$map->setOverviewMapControl($overviewMapControl);
*/

// Streetview
$streetViewControl = new StreetViewControl(ControlPosition::TOP_LEFT);
$map->getControlManager()->setStreetViewControl($streetViewControl);

// Zoom control
$zoomControl = new ZoomControl(
    ControlPosition::TOP_LEFT,
    ZoomControlStyle::DEFAULT_
);

$map->getControlManager()->setZoomControl($zoomControl);

// Cluster
if($show_cluster)
{
	//$markerCluster = $map->getMarkerCluster();
	//$markerCluster->setVariable('markerCluster');
  $map->getOverlayManager()->getMarkerCluster()->setType(MarkerClusterType::MARKER_CLUSTERER);
  $map->getOverlayManager()->getMarkerCluster()->setOption('imagePath', '//raw.githubusercontent.com/googlemaps/v3-utility-library/master/markerclustererplus/images/m');
}

// Helper
$mapHelperBuilder = MapHelperBuilder::create(); 
$mapHelper = $mapHelperBuilder->build();

//$apiHelperBuilder = ApiHelperBuilder::create();
//$apiHelperBuilder->setLanguage(App::getLocale());
//$apiHelperBuilder->setKey(\Config::get('google.api_key'));
//$apiHelper = $apiHelperBuilder->build();
//echo $apiHelper->render([$mapHelper]);

echo $mapHelper->renderHtml($map);
echo $mapHelper->renderJavascript($map);

$apiHelperBuilder = Ivory\GoogleMap\Helper\Builder\ApiHelperBuilder::create();
$apiHelperBuilder->setLanguage(App::getLocale());
$apiHelperBuilder->setKey(\Config::get('google.api_key'));

$apiHelper = $apiHelperBuilder->build();
echo $apiHelper->render([$autocomplete, $map]);
?>
	</div>
	<div class="col col-sm-4" id="sidebar">
	<div id="directions">
		<button type="button" class="close" aria-hidden="true" onClick="$('#directions').slideUp(200, function(){ $('#sidebar').getNiceScroll().resize(); })" style="margin-top:-5px">&times;</button>
		<form role="form">
			<div class="form-group">
				<label>{{ trans('map.directions') }}</label>
				<input type="text" class="form-control" id="routeStart" value="{{ $starting_point_full }}">
			</div>
			<div class="form-group">
				<div class="input-group">
					<input type="text" class="form-control" id="routeEnd">
					<span class="input-group-btn">
						<button class="btn btn-primary" type="button" id="get-directions" title="{{ trans('map.get_directions') }}"><i class="fa fa-car"></i></button>
					</span>
				</div>
			</div>
		</form>
		<div id="directions-panel"></div>
	</div>
	<a href="#" data-id="0" data-lng="{{ $geocode['longitude'] }}" data-lat="{{ $geocode['latitude'] }}" class="list-group-item" id="pan-here">
		<h4 class="list-group-item-heading">
			<div class="pull-right controls-here hidden-xs">
				<div id="zoom-here-in"><i class="fa fa-search-plus"></i></div>
				<div id="zoom-here-out"><i class="fa fa-search-minus"></i></div>
			</div>
			<span class="icon-holder hidden-xs"><i class="fa fa-crosshairs"></i></span> {{ $starting_point }}
		</h4>
	</a>
<?php
if(count($oItems) > 0)
{
	$i = 0;
	echo '<div class="list-group">';
	foreach($aMarkers as $marker)
	{
		if($i >= $limit_sidebar)break;
?>
	<a href="#" id="marker{{ $marker['id'] }}" data-id="{{ $marker['id'] }}" data-address="<?php echo str_replace('"', '&quot;', $marker['address']) ?>" data-lng="{{ $marker['lng'] }}" data-lat="{{ $marker['lat'] }}" class="list-group-item">
		<span class="badge pull-right hidden-xs"><span class="distance_km" style="<?php echo ($unit != 'K') ? 'display:none' : 'display:block'; ?>">{{ $marker['distance_km'] }} km</span>
        <span class="distance_mi" style="<?php echo ($unit != 'M') ? 'display:none' : 'display:block'; ?>">{{ $marker['distance_mi'] }} mi</span></span>
		<h4 class="list-group-item-heading"><span class="icon-holder hidden-xs"><i class="fa fa-map-marker"></i></span> {{ $marker['name'] }}</h4>
<?php if($marker['options'] != '') { ?>
		<p class="list-group-item-text hidden-xs">{{ $marker['options'] }}</p>
<?php } ?>
	</a>
<?php
		$i++;
	}
	echo '</div>';
}
?>
	</div>
</div>
@stop

@section('custom_script')
<script type="text/javascript">
var show_cluster = <?php echo ($show_cluster) ? 'true' : 'false'; ?>;
var directionsDisplay, directionsService;

function gMapsAutoCenter() {
	var bounds = new google.maps.LatLngBounds();
/*
	bounds.extend(new google.maps.LatLng({{ $geocode['latitude'] }}, {{ $geocode['longitude'] }}));
	bounds.extend(new google.maps.LatLng({{ (isset($aMarkers[0]['lat'])) ? $aMarkers[0]['lat'] : $geocode['latitude']; }}, {{ (isset($aMarkers[0]['lng'])) ? $aMarkers[0]['lng'] : $geocode['longitude']; }}));
*/
  for (marker in map_container.overlays.markers) {
    bounds.extend(map_container.overlays.markers[marker].position);
  }
	map.fitBounds(bounds);
}

function mapIsLoaded() {

  if (typeof map.setOptions === 'undefined') {
    setTimeout(function() {
      mapIsLoaded();
    }, 100);
  } else {
<?php if($oMap->map_style_id > 1) { ?>
    map.setOptions({styles: {{ $oStyle->style }}});
<?php } ?>
    gMapsAutoCenter();

    directionsDisplay = new google.maps.DirectionsRenderer();
    directionsService = new google.maps.DirectionsService();

    google.maps.event.addListener(directionsDisplay, 'directions_changed', function() {
      setTimeout(function(){
        $('#sidebar').getNiceScroll().resize();
      }, 500);
    });

  }
}

mapIsLoaded()
</script>

@stop