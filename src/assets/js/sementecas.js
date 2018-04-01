//var sementecas = [];
///*
var sementecas = [
  { url: 'http://localhost/blabla.html',
    image: 'http://localhost/images/ptcture.jpg',
    text: 'a número 1',
    name: 'uma sementeca',
    latLon: [47.37180823552663, 8.54806714892635],
    },
  { url: 'http://localhost/blabla.html',
    image: 'http://localhost/images/ptcture.jpg',
    text: 'a nº 2',
    name: 'duas sementecas',
    latLon: [38.89531, -6.3990],
    },
  { url: 'http://localhost/blabla.html',
    image: 'http://localhost/images/ptcture.jpg',
    text: 'a nº 3',
    name: 'três sementecas',
    latLon: [38.89531, -7.3990],
    },
];
//*/

var layerSementecas;

var map;
var ajaxRequest;
var plotlist;
var plotlayers=[];
var onMapClick = function (e) {
  //alert("You clicked the map at " + e.latlng);
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
  lat = typeof lat == 'number' ? lat : 38.89531;
  lon = typeof lon == 'number' ? lon : -8.39905;
  // set up the map
  map = new L.Map('map', {
    "center": [
      8.54806714892635,
      47.37180823552663
    ],
    "zoom": 12.241790506353492,
  });

  // create the tile layer with correct attribution
  var osmUrl='http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
  var osmAttrib='Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors';
  var osm = new L.TileLayer(osmUrl, {minZoom: 6, maxZoom: 12, attribution: osmAttrib});

  // start the map in South-East England
  map.setView(new L.LatLng(lat, lon),6);
  map.addLayer(osm);
  /*function onMapClick(e) {
      alert("You clicked the map at " + e.latlng);
      }

  map.on('click', onMapClick);*/

  $.get('/sementecas/get?all', function(data) {
    var markers = [];
    //var sementecas = data;
    $.each(sementecas, function (i, s) {
      /*if (! ((s.lat) && (s.lon)) ) {
        return;
      }*/
     console.log(s);
      var popup_text = '<a href="' + s.url + '"><strong>' + s.name + '</strong></a><br>';
      if ( s.image ) {
        popup_text += '<img src="' + s.image + '"/>';
      }
      popup_text += s.description;
      //L.marker(s.latLon).addTo(map)
      //markers.push(L.marker({ lng: s.lon, lat: s.lat})
      markers.push(L.marker(s.latLon)
      .bindPopup(popup_text));
        //.openPopup();
    });

  layerSementecas = L.layerGroup(markers);
  map.addLayer(layerSementecas);
  });

  $('.leaflet-marker-pane img').on('click', function(e) {
    console.log(e);
  });
}

function initmap2(lat, lon) {
  lat = typeof lat == 'number' ? lat : 38.89531;
  lon = typeof lon == 'number' ? lon : -8.39905;

  // set up the map
  //map = new L.Map('map', mapconfig);
  map = new L.Map('map', {
    "center": [
      38.89,
      -8.54806714892635    ],
    "zoom": 12,
  });
  //map = new L.Map('map');

  // create the tile layer with correct attribution
  var osmUrl='http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
  var osmAttrib='Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors';
  var osm = new L.TileLayer(osmUrl, {minZoom: 6, maxZoom: 12, attribution: osmAttrib});

  // start the map in South-East England
  //map.setView(new L.LatLng(lat, lon),6);
  map.addLayer(osm);

  layer = new L.TileLayer("https://tiles.wmflabs.org/hillshading/{z}/{x}/{y}.png");
  map.addLayer(layer);
  //                       https://tiles.wmflabs.org/hillshading/12/2196/1342.png
  //https://tiles.wmflabs.org/hillshading/12/2583/1950.png
}
