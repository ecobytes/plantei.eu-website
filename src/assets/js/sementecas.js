var sementecas = [];
/*
  { url: 'http://localhost/blabla.html',
    image: 'http://localhost/images/ptcture.jpg',
    text: 'a número 1',
    name: 'uma sementeca',
    latLon: [38.89531, -8.3990],
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
*/
var layerSementecas;

var map;
var ajaxRequest;
var plotlist;
var plotlayers=[];
var onMapClick = function (e) {
  alert("You clicked the map at " + e.latlng);
  L.marker(e.latlng)
    .bindPopup('<strong>new marker</strong>').addTo(map);
  map.removeLayer(layerSementecas);
}

function initmap(lat, lon) {
  lat = typeof lat == 'number' ? lat : 38.89531;
  lon = typeof lon == 'number' ? lon : -8.39905;
  // set up the map
  map = new L.Map('map');

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

  var markers = [];
  $.each(sementecas, function (i, s) {
    //L.marker(s.latLon).addTo(map)
    markers.push(L.marker(s.latLon)
    .bindPopup('<a href="' + s.url + '"><strong>' + s.name + '</strong></a><br><img src="' + s.image + '"/>' + s.text));
      //.openPopup();
  });

  layerSementecas = L.layerGroup(markers);
  map.addLayer(layerSementecas);

  $('.leaflet-marker-pane img').on('click', function(e) {
    console.log(e);
  });
}
