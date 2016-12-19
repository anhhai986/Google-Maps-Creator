$(function() {
  $('#optionFilter input').change(filterMarkers);

  // Get navbar height and set page padding
  $('body').css({'padding-top' : parseInt($('nav.navbar').outerHeight()) + 'px'});

  $('[data-toggle=tooltip]').tooltip();

  // Nicescroll
  var nice = $('#sidebar').niceScroll({
    cursorwidth: '8px',
    cursorborder: '1px solid rgba(255,255,255,0.8)',
    cursorborderradius: '5px',
    autohidemode: false
  });

  $('#zoom-here-in').on('click', function() {
    var zoom = map.getZoom();
    zoom++;
    map.setZoom(zoom);
  });

  $('#zoom-here-out').on('click', function() {
    var zoom = map.getZoom();
    zoom--;
    map.setZoom(zoom);
  });

  $('#show-directions').on('click', function() {
    $('#sidebar').scrollTop(0);
    $('#directions').slideToggle(200, function(){ 
       $('#sidebar').getNiceScroll().resize();
     });
  });


  $('#directions-panel').on('click', '#close-directions', function() {
    $('#directions-panel').html('');
    $('#sidebar').getNiceScroll().resize();
  });

  $('#map').on('click', '.zoom-to', function() {
    var lat_to = $(this).attr('data-lat');
    var lng_to = $(this).attr('data-lng');
    var latLng_to = new google.maps.LatLng(lat_to, lng_to);

    map.panTo(latLng_to);
    map.setZoom(15);

    return false;
  });

  $('#map').on('click', '.route-to', function() {
    var latLng_end = new google.maps.LatLng($(this).attr('data-lat'), $(this).attr('data-lng'));

    $('#routeEnd').val($(this).attr('data-address'));

    showDirections($('#routeStart').val(), latLng_end, google.maps.TravelMode.DRIVING);
    return false;
  });

  $('#get-directions').on('click', function() {
    showDirections($('#routeStart').val(), $('#routeEnd').val(), google.maps.TravelMode.DRIVING);
  });

  $('#routeStart,#routeEnd').keypress(function (e) {
    if (e.which == 13) {
      showDirections($('#routeStart').val(), $('#routeEnd').val(), google.maps.TravelMode.DRIVING);
    }
  });

  function showDirections(origin, dest, mode)
  {
    var request = {
      origin: origin,
      destination: dest,
      travelMode: mode,
      provideRouteAlternatives: true
    };

    directionsService.route(request, function(response, status) {
      if (status == google.maps.DirectionsStatus.OK) {
        directionsDisplay.setDirections(response);
      }
    });

    directionsDisplay.setMap(map);

    $('#directions-panel').html('<button type="button" class="btn btn-default btn-xs" id="close-directions" aria-hidden="true">&times;</button>');
    $('#directions').slideDown();
    $('#sidebar').scrollTop(0);

    directionsDisplay.setPanel(document.getElementById('directions-panel'));
    $('#sidebar').getNiceScroll().resize();
  }


  $('#sidebar a').on('click', function() {
    $('#sidebar a').removeClass('active');
    $(this).addClass('active');

    var id = $(this).attr('data-id');
    var address = $(this).attr('data-address');
    var lat = $(this).attr('data-lat');
    var lng = $(this).attr('data-lng');
    var latLng = new google.maps.LatLng(lat, lng);
    map.panTo(latLng);

    // Close infoWindows
    for (infoWindow in map_container.overlays.info_windows) {
      map_container.overlays.info_windows[infoWindow].close();
    }

    // Open infoWindow
    if(id > 0)
    {
      //map.setZoom(8);
      $('#routeEnd').val(address);
      google.maps.event.trigger(map_container.overlays.markers['marker' + id], 'click');
    }

    //return false;
  });

  var url = (window.location != window.parent.location) ? window.parent.location : window.location;

  $('#map-link').val(url);
  $('#map-link').on('click', function() {
    $(this).select();
  });

  $('.prevent-close').click(function(e) {
    e.stopPropagation();
  });

  $('input[type=radio][name=unit]').on('change', function(){
    $('.distance_km,.distance_mi').hide();
    $('.distance_' + $(this).val()).show();
    $.cookie('distance_unit', $(this).val());
  });

  // Init unit
  if(typeof $.cookie('distance_unit') !== 'undefined')
  {
    $('#unit-' + $.cookie('distance_unit')).trigger('click');
  }
});

function filterMarkers(){
  // Hide all markers
  for(marker in map_container.markers) {
    var my_marker = map_container.markers[marker];
    my_marker.setMap(null);
    $('#' + marker).hide();
  }

  if(show_cluster) markerCluster.clearMarkers();

  for(marker in map_container.markers) {
    var my_marker = map_container.markers[marker];
    var labels = my_marker.label; 
    var show_marker = false;

    $('#optionFilter :checked').each(function() {
      var label_val = $(this).val();
      if(labels == 0)
      {
        if(labels == label_val) show_marker = true;
      }
      else
      {
        if(labels.indexOf(',') === -1)
        {
          if(labels == label_val) show_marker = true;
        }
        else
        {
          var aLabels = labels.split(',');
          $.each(aLabels, function (index, label) {
            if(label == label_val) show_marker = true;
          });
        }
      }

      if(show_marker)
      {
        if(show_cluster) markerCluster.addMarker(my_marker);
        my_marker.setMap(map);
        $('#' + marker).show();
      }
    });
  }
  if(show_cluster) markerCluster.redraw();
}