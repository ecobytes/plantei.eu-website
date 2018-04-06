var layerSementecas;

var map, markers;
var ajaxRequest;
var plotlist;
var plotlayers=[];
var onMapClick = function (e) {
  console.log("You clicked the map at " + e.latlng);
  $("form input[name='lat']").val(e.latlng.lat);
  $("form input[name='lon']").val(e.latlng.lng);
  $('#modal').modal('show');
  $('#sementeca-preview').hide();
  $('form').show();
  $('form').submit(function(e){
    e.preventDefault();
    $.ajax({
        url: $('form').attr('action'),
        type:'post',
        data:$('form').serialize(),
        success:function(data){
          console.log(this);
          $('#modal').modal('hide');
          $('#sementeca-preview').show();
          $('form').hide();
          getSementecas();

        },
        error:function(data) {
          if ( data.status == 422 ) {
            console.log(data.responseJSON);
            formErrors(data.responseJSON, $('form'))


          }
          return false;

        }
    });
  });
}

var getSementecas = function () {
  $.get('/api/sementecasgeo', function(data) {
    if (map.hasLayer(markers)) {
      map.removeLayer(markers);
    }
    markers = [];
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
  getSementecas();

}
