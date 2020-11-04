//init google Map//
var mapSettings = {
    Japan: {
        // center: { lat: -7.8099125, lng: 110.5722973},
        center: { lat: 40, lng: 136},
        zoom: 5,
        maxZoom: null,
        styles: [
            {
                "featureType": "landscape.man_made",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#f7f1df"
                    }
                ]
            },
            {
                "featureType": "landscape.natural",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#d0e3b4"
                    }
                ]
            },
            {
                "featureType": "landscape.natural.terrain",
                "elementType": "geometry",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "labels",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "poi.business",
                "elementType": "all",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "poi.medical",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#fbd3da"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#bde6ab"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "geometry.stroke",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "labels",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "color": "#ffe15f"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry.stroke",
                "stylers": [
                    {
                        "color": "#efd151"
                    }
                ]
            },
            {
                "featureType": "road.arterial",
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "color": "#ffffff"
                    }
                ]
            },
            {
                "featureType": "road.local",
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "color": "black"
                    }
                ]
            },
            {
                "featureType": "transit.station.airport",
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "color": "#cfb2db"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#a2daf2"
                    }
                ]
            }
        ]
    },
    Jakarta: {
        center: { lat: -7.8099125, lng: 110.5722973},
        zoom: 5,
        maxZoom: null,
        styles: [
            {
                "featureType": "landscape.man_made",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#f7f1df"
                    }
                ]
            },
            {
                "featureType": "landscape.natural",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#d0e3b4"
                    }
                ]
            },
            {
                "featureType": "landscape.natural.terrain",
                "elementType": "geometry",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "labels",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "poi.business",
                "elementType": "all",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "poi.medical",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#fbd3da"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#bde6ab"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "geometry.stroke",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "labels",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "color": "#ffe15f"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry.stroke",
                "stylers": [
                    {
                        "color": "#efd151"
                    }
                ]
            },
            {
                "featureType": "road.arterial",
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "color": "#ffffff"
                    }
                ]
            },
            {
                "featureType": "road.local",
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "color": "black"
                    }
                ]
            },
            {
                "featureType": "transit.station.airport",
                "elementType": "geometry.fill",
                "stylers": [
                    {
                        "color": "#cfb2db"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#a2daf2"
                    }
                ]
            }
        ]
    }
};
var map = null;
var markerCluster = null;
const isIOT = document.querySelector('body').dataset.iot;
function initMap () {
    console.log('initMap');
    // resizeMap('load');
}
$(function () {
    // ** element
    var $mapContainer = $('#geochart-colors');
    // ** common
    var isInitMarker = false;
    var topbarHeight = $('.topbar').height();
    var xhrCounter = null;
    var orgID = $('input[name=org_id]').val();
    var resizeCounter = null;
        $(window).on('load', function(e) {
            resizeMap('load');
            // ** get count
            getCount('load');
        });
    //******************************************
    //getCount
    //******************************************
    function getCount (evtType) {
        console.log('getCount is executed.');
        if (xhrCounter) {
            clearTimeout(xhrCounter);
        }
        $.ajax(
            {
                url: '/dashboard/statistics/feeds/',
                method: 'GET',
                dataType: 'json'
            }
        ).done(function (rs) {
            console.log(rs);
            // XXX for test
            // rs.statistics_data = {
            //     total_warnings: 7,
            //     total_devices: 31,
            //     status_offline: 1,
            //     status_stable: 10,
            //     status_unstable: 2,
            //     status_inactive: 13,
            //     status_nouplink: 5,
            //     city_devices:{}
            // };
            if (map && rs.online_devices && rs.online_devices.length > 0) {
                initMarkers(rs.online_devices, evtType);
            } else {
                initMarkers([], 'reload');
            }
            var problematic_devices = 0;
            var online_devices = 0;
            var problemPropAy = ['status_offline', 'status_unstable', 'status_nouplink', 'inactive_devices'];
            var onlinePropAy = ['status_stable', 'status_unstable', 'status_nouplink' ];
            for(var key in rs) {
                console.log(key);
                if ($('#' + key).length > 0) {
                    $('#' + key).text(rs[key]);
                }
                if (onlinePropAy.indexOf(key) > -1) {
                    online_devices += parseInt(rs[key]);
                }
                if (problemPropAy.indexOf(key) > -1) {
                    problematic_devices += parseInt(rs[key]);
                    console.log('problematic_devices:' + problematic_devices);
                }
                console.log(key + ':' + rs[key]);
            }
            $('#problematic_devices').text(problematic_devices);
            $('#status_online').text(online_devices);
            if (xhrCounter === null) {
                $('.counterxhr').counterUp(
                    {
                        delay: 10,
                        time: 1000
                    }
                );
            }
            xhrCounter = setTimeout(function () {
                getCount();
            }, 10000);
        });
    }
    // ***************
    // initMarker
    // ***************
    function initMarkers (locations, evtType) {
        console.log('initMarkers');
        let bounds = new google.maps.LatLngBounds();
        // console.log(bounds);
        isInitMarker = true;
        let markers = locations.map(function(location, i) {
            console.log(location);
            if (location.location_info) {
                bounds.extend(location.location_info);
                return new google.maps.Marker({
                    position: location.location_info,
                    label: location.uid
                });
            }
        });
        // Add a marker clusterer to manage the markers.
        if (markerCluster) {
            console.log('clearMarkers');
            markerCluster.clearMarkers();
        }
        markerCluster = new MarkerClusterer(map, markers,
        {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
        $('#geochartWrapper').attr('data-stat', '');
        if (isIOT) {
            return;
        }
        if (!evtType) {
            return;
        }
        map.fitBounds(bounds);
        if (map.getZoom() > 8) {
            map.setZoom(8);
        }
        console.log(map.getZoom());
    }
    // *********************
    // resizeMap
    // *********************
    function resizeMap(evtType) {
        console.log('=====================resizeMap========================');
        // adjust map
        let winHeight = window.innerHeight;
        let winWidth = window.innerWidth;
        let mapHeight = winHeight - topbarHeight - 80;
        let cardCount = 5;
        let cardMargin = 10;
        let cardPadding = 10;
        let cardBorder= 1;
        let cardHeight = 0;
        console.log('mapHeight:' + mapHeight);
        console.log('winWidth:' + winWidth);
        if ( mapHeight > 400 && winWidth > 414) {
            if (mapHeight > $('#my-map').width()) {
                mapHeight = $('#my-map').width();
            }
            $mapContainer.height(mapHeight);
            // $mapContainer.width(mapHeight);
            // adjust map card
            // count single card hight
            cardHeight = (mapHeight - cardMargin*(cardCount-1))/cardCount -cardPadding*2 - cardBorder*2;
            // apply to all cards
            $('.my-map-card').height(cardHeight);
        } else {
            $mapContainer.height($('#my-map').width());
            // $mapContainer.width($('#my-map').width());
            cardHeight = ($('#my-map').height() - cardMargin*(cardCount-1))/cardCount -cardPadding*2 - cardBorder*2;
            // apply to all cards
            $('.my-map-card').height(cardHeight);
        }
        if (!map) {
            map = new google.maps.Map(document.getElementById('geochart-colors'), isIOT ? mapSettings.Japan : mapSettings.Jakarta);
        }
        // initMarkers();
    }
});
