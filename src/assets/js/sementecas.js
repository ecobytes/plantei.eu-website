var layerSementecas;

var map;
var ajaxRequest;
var plotlist;
var plotlayers=[];
var onMapClick = function (e) {
  console.log("You clicked the map at " + e.latlng);
  $.get('/sementecas/new', function(data) {
        $('#sementeca .modal-content').empty();
        $('#sementeca .modal-content').append(data);

        $('#sementeca').modal('show');
      $("#sementeca input[name='lat']").val(e.latlng.lat);
      $("#sementeca input[name='lon']").val(e.latlng.lng);
      });
  /*L.marker(e.latlng)
    .bindPopup('<strong>new marker</strong>').addTo(map);*/
  //map.removeLayer(layerSementecas);
}

function initmap(lat, lon) {
  let zoom = 10;
  if ( ! ( typeof lat == 'number' ) || ( typeof lon == 'number' ) ) {

    lat = typeof lat == 'number' ? lat : 38.89531;
    lon = typeof lon == 'number' ? lon : -8.54806714892635;
    zoom = 7;
  }

  // set up the map
  // Map bounds: Portugal
  var corner1 = L.latLng(44.074958 + 2, -9.997559 - 2),
      corner2 = L.latLng(36.689565 - 2, -6.04248 + 2),
      bounds = L.latLngBounds(corner1, corner2);
  map = new L.Map('map', {
    "center": [lat, lon],
    "zoom": zoom,
    "minZoom": 7,
    "maxZoom": 10,
    "maxBounds": bounds,
  });
  //map = new L.Map('map');

  // create the tile layer with correct attribution
  var osmUrl='http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
  var osmAttrib='Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors';
  var osm = new L.TileLayer(osmUrl, {minZoom: 6, maxZoom: 12, attribution: osmAttrib});

  //map.setView(new L.LatLng(lat, lon),6);
  map.addLayer(osm);

  //hillshading layer
  //layer = new L.TileLayer("https://tiles.wmflabs.org/hillshading/{z}/{x}/{y}.png");
  //map.addLayer(layer);

  // Add marker layer on load
  $.get('/api/sementecasgeo', function(data) {
    var markers = [];
    //var sementecas = data;
    $.each(data, function (i, s) {
      var popup_text = '<a href="mailto:' + s.contact + '"><strong>' + s.name + '</strong></a><br>';
      if ( s.image ) {
        popup_text += '<img src="' + s.image + '"/>';
      }
      popup_text += s.description;

      markers.push(L.marker({ lng: s.lon, lat: s.lat})
      .bindPopup(popup_text));
    });
    layerSementecas = L.layerGroup(markers);
    map.addLayer(layerSementecas);
  });

  /*$('.leaflet-marker-pane img').on('click', function(e) {
    console.log(e);
  });*/
}
