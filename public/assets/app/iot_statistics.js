//init google Map//
var mapSettings = {
    hokkaido: {
        center: { lat: 43.0588758, lng: 141.3087261},
        zoom: 6 
    },
    tokyo: {
        center: { lat: 35.652832, lng: 139.839478 },
        zoom: 10
    }
};
var visualMap;
//**XXX OSM 
$(function () {
    //element
    var $mapWrapper = $('.mapWrapper');
    var $searchLocForm = $('form[name=searchLoc]');
    var $svgWrapper = $('#my-map');
    var $regionInfo = $('#regionInfo');
    var $svgContainer = $('#geochart-colors');
    //common
    var isLocaleJP = LAN.lang === 'ja-JP';
    var resizeCounter = null;
    var subdivisionInfo = {
        'Hokkaido': ['Hokkaido', 'JP-01'],
        'Aomori' : ['Aomori', 'JP-02'],
        'Iwate' : ['Iwate', 'JP-03'],
        'Miyagi' : ['Miyagi', 'JP-04'],
        'Akita' : ['Akita', 'JP-05'],
        'Yamagata': ['Yamagata', 'JP-06'],
        'Fukushima': ['Fukushima', 'JP-07'],
        'Ibaraki' : ['Ibaraki', 'JP-08'],
        'Tochigi' : ['Tochigi', 'JP-09'],
        'Gunma' : ['Gunma', 'JP-10'],
        'Saitama' : ['Saitama', 'JP-11'],
        'Chiba' : ['Chiba', 'JP-12'],
        'Tokyo' : ['Tokyo', 'JP-13'],
        'Kanagawa' : ['Kanagawa', 'JP-14'],
        'Niigata' : ['Niigata', 'JP-15'],
        'Toyama' : ['Toyama', 'JP-16'],
        'Ishikawa' : ['Ishikawa', 'JP-17'],
        'Fukui' : ['Fukui', 'JP-18'],
        'Yamanashi': ['Yamanashi', 'JP-19'],
        'Nagano' : ['Nagano', 'JP-20'],
        'Gifu' : ['Gifu', 'JP-21'],
        'Shizuoka' : ['Shizuoka', 'JP-22'],
        'Aichi' : ['Aichi', 'JP-23'],
        'Mie' : ['Mie', 'JP-24'],
        'Shiga' : ['Shiga', 'JP-25'],
        'Kyoto' : ['Kyoto', 'JP-26'],
        'Osaka' : ['Osaka', 'JP-27'],
        'Hyogo' : ['Hyogo', 'JP-28'],
        'Nara' : ['Nara', 'JP-29'],
        'Wakayama' : ['Wakayama', 'JP-30'],
        'Tottori' : ['Tottori', 'JP-31'],
        'Shimane' : ['Shimane', 'JP-32'],
        'Okayama' : ['Okayama', 'JP-33'],
        'Hiroshima' : ['Hiroshima', 'JP-34'],
        'Yamaguchi' : ['Yamaguchi', 'JP-35'],
        'Tokushima' : ['Tokushima', 'JP-36'],
        'Kagawa' : ['Kagawa', 'JP-37'],
        'Ehime' : ['Ehime', 'JP-38'],
        'Kochi' : ['Kochi', 'JP-39'],
        'Fukuoka' : ['Fukuoka', 'JP-40'],
        'Saga' : ['Saga', 'JP-41'],
        'Nagasaki' : ['Nagasaki', 'JP-42'],
        'Kumamoto' : ['Kumamoto', 'JP-43'],
        'Oita' : ['Oita', 'JP-44'],
        'Miyazaki' : ['Miyazaki', 'JP-45'],
        'Kagoshima' : ['Kagoshima', 'JP-46'],
        'Okinawa' : ['Okinawa', 'JP-47']
    };
    var cityDevices = null;
    var conditionColors = [[0, 50, '#d6e0fd'], [50, 100, '#acbff9'], [100, 150, '#5c84fb'], [150, 200, '#335bd3'], [200, null, '#022691']];
    var topbarHeight = $('.topbar').height();
    var subdivisionKey = Object.keys(subdivisionInfo);
    var chart = null;
    var validRegion=['tokyo', 'hokkaido'];
    var geochartCounter = null;
    var xhrCounter = null;
    var deviceRowTemp = $("#deviceRowTemp").html();
    var tooltipTemp = '{region}: {count}';
    var statConf = {
        1: 'online',
        0: 'offline'
    };
    var statStyle = {
        1: 'success',
        0: 'danger'
    };
    var markerSettings = {
        minSlice: 10,
        perSlice: 10,
        sliceLen: 4,
        speed: 50, 
    };
    var locOnMap_arr = [];
    var geochartStatus = 'online';
    var subdivisionLang = null;
    const onlineColor = '92, 132, 251';
    const geochartColors = {
        online: ['#e5ecfe', '#9eb7fa', '#5c84fb'],
        // stable: ['#e5ecfe', '#9eb7fa', '#5c84fb'],
        // unstable: ['#bbe0ae', '#fc0']
    };
    //** init sub
    if (isLocaleJP) {
        $.get('/assets/json/Japan_administrator_name.json', function (rs) {
            subdivisionLang = rs;
            console.log(subdivisionLang);
        }, 'json');
    }
    
    //** init geo chart
    // if (!location.hash) {
    var svgPath = 'https://s3-us-west-2.amazonaws.com/static-us-west-2.astra.cloud/msp/dist/images/japanHigh.svg';
    $.get(svgPath, function (rs) {
        // console.log(rs.documentElement);
        var svg = rs.documentElement;
        $svgContainer.html(rs.documentElement.outerHTML);
        $('#geochartWrapper').attr('data-stat', '');
        resizeMap('load');
        // ** get count
        getCount('load');
    });

    $(window).on('resize', function(e) {
        if (resizeCounter) {
            clearTimeout(resizeCounter);
        }
        resizeCounter = setTimeout(function () { 
           resizeMap();
        }, 200);
    });
        // setTimeout(function () {
        //     $(window).resize(function(){
        //         if (geochartCounter) {
        //             clearTimeout(geochartCounter);
        //         }
        //         geochartCounter = setTimeout(function () {
        //             drawSVG();
        //         }, 500);
        //     });
        // }, 500);
    // } else {
    //     google.charts.load('current', {
    //         'packages':['geochart'],
    //         // Note: you will need to get a mapsApiKey for your project.
    //         // See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
    //         'mapsApiKey': 'AIzaSyAO6LD0z4OotZO0t1GXJz5QwQK0O8-ESWY'
    //     });
    //     google.charts.setOnLoadCallback(drawRegionsMap);

    //     setTimeout(function () {
    //         $(window).resize(function(){
    //             if (geochartCounter) {
    //                 clearTimeout(geochartCounter);
    //             }
    //             geochartCounter = setTimeout(function () {
    //                 drawRegionsMap();
    //             }, 500);
    //         });
    //     }, 500);
    // }
    //** bind event to map refreshing
    $('.statusNav .wrapper').click(function () {
        var geochartStatus = $(this).find('.my-num').attr('id').replace('status_');
    });
    //
    //*****************************************
    //searchLoc
    //*****************************************
    $searchLocForm.submit(function (e) {
        e.preventDefault();
        var loc = $(this)[0].key.value;
        var targetMarker = $('.leaflet-marker-icon.' + loc);
        if (targetMarker.length < 1) {
            return;
        }
        var index = targetMarker.find('.devicesCount').data('m');
        var markers = targetMap.getMarker();
        var _map = targetMap.getMap();

        //_map.setView(markers[index].getLatLng(), _map.getZoom(), {animation: true});
        // console.log(markers[index].getLatLng());
        targetMarker.trigger('click');
    });
    $searchLocForm.find('input[name=key]').change(function (e) {
        e.stopPropagation();
        $searchLocForm.submit();
        var _this = $(this);
        setTimeout(function () {
            _this.val('');
        }, 500);
    });
    //******************************************
    //init counter 
    //XXX
    //******************************************
    // $('.counter').counterUp({
    //     delay: 10,
    //     time: 1000
    // });

    //******************************************
    //XXX init map
    //******************************************
    // var targetMap = new OSM({
    //     centerLat: 35.709,
    //     centerLng: 139.732,
    //     zoom: 10,
    //     minZoom: 1.25 
    // });
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
            if (rs.city_devices instanceof Array) {
                console.log('no city_devices or invalid format');
                xhrCounter = setTimeout(function () {
                    getCount();
                }, 10000);
                return;
            }
            cityDevices = rs.city_devices ? rs.city_devices : null;
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
                $('.counterxhr').each(function () {
                    // Start the counting from a specified number - in this case, 0!
                    $(this).prop('Counter',0).animate({
                        Counter: $(this).text()
                    }, {
                        // Speed of counter in ms, default animation style
                        duration: 2000,
                        easing: 'swing',
                        step: function (now) {
                            // Round up the number
                            $(this).text(Math.ceil(now));
                        }
                    });
                });
            }
            updateSVGMap(evtType);
            xhrCounter = setTimeout(function () {
                getCount();
            }, 10000);
        });
    }
    //****************
    // drawSVG
    //****************
    function drawSVG () {
        console.log('drawSVG');
        $regionInfo.html('').removeClass('active');
        if ($svgWrapper.find('svg').length < 1) {
            return;
        }
        var svg = $svgWrapper.find('svg')[0];
        var svgInnerW = 380;
        var svgInnerH = 410;
        // var svgW = 350;
        var svgW = $svgContainer.width();
        var svgH = $svgContainer.height();
        var svgWrapperW = $svgWrapper.width();
        var scaleX =  svgWrapperW > svgW ? (svgWrapperW / svgW).toFixed(1) : 1;
        var translateX = 0;
        svg.setAttribute('transform-origin', 'top left');
        // svg.setAttribute('transform', 'scale(1.2, 1.2) rotate(17) translate(30,50)');
        // console.log('scaleX:' + scaleX);
        console.log('svgW:' + svgW);
        console.log('svgH:' + svgH);
        scaleX = scaleX > 1.2 ? 1.2: scaleX;
        // scaleX = 1;
        svg.setAttribute('width', svgW);
        svg.setAttribute('height', svgH);
        svg.setAttribute('viewBox', [100, 0, svgInnerW, svgInnerH].join(' '));
        svg.setAttribute('preserveAspectRatio', 'xMinYMin slice');
        $svgContainer.css('transform', 'scaleX(' + scaleX + ')');
        // svg.setAttribute('style', 'transform: scaleX(' + scaleX +');');
        //svg.setAttribute('viewBox', [100, 0, 350, 350].join(' '));
    }
    //*************************
    // updateSVGMap
    // update color of region by XHR
    //*************************
    function updateSVGMap (bind) {
        console.log('updateSVGMap');
        // $.ajax(
        //     {
        //         url: '/dashboard/statistics/get_info/' + orgID,
        //         method: 'POST',
        //         dataType: 'JSON'
        //     }
        // ).done(function (rs) {
            // var _data = rs.city_devices;
            var _data = cityDevices;
            // Must follow the order bellow
            // var _data = [
            // ['Hokkaido', 1], ['Aomori', 5],
            // ['Iwate', parseInt(Math.random()*30)], ['Miyagi', 3],
            // ['Akita', 150], ['Aomori', 51],
            // ['Aichi', 10], ['Yamagata', 101],
            // ['Fukushima', null], ['Ibaraki', 1],
            // ['Tochigi', 2], ['Gunma', 210],
            // ['Saitama', null], ['Saitama', 150],
            // ['Tokyo', 151], ['Kanagawa', 5],
            // ['Niigata', 7], ['Toyama', 4],
            // ['Ishikawa', 2], ['Fukui', 200]
            // ];
            if (!_data) {
                return;
            }
            $.each(_data, function (i, row) {
                    // var regionNum = i + 1;
                    // regionNum = regionNum <  10 ? '0' + regionNum : regionNum;
                    var regionNum = subdivisionInfo[i] ? subdivisionInfo[i][1] : null; 
                    if (!regionNum) {
                        return;
                    }
                    var targetPath = $('#' + regionNum);
                    var count = row; 
                    var color = null;
                    console.log(regionNum);
                    console.log(targetPath.length);
                    $.each(conditionColors, function (i, condition) {
                        console.log('count: ' + count);
                        console.log('condition: ' + condition.join(','));
                        if (condition[1] && count > condition[0] && count <= condition[1]) {
                            console.log('less than: ' + condition[1]);
                            color = condition[2];
                            console.log('check1');
                            return false; 
                        }
                        if (!condition[1] && count >= condition[0]) {
                            console.log('equal and larger than: ' + condition[1]);
                            color = condition[2];
                            console.log('check2');
                            return false;
                        }
                    });
                    console.log('color??' + color);
                    color = color ? color: '#ccc';
                    targetPath.css(
                        {
                            fill: color
                        }
                    ).data('devices', count).attr('data-hasval', 1);
                    if (bind || !targetPath.data('bindhover')) {
                        bindRegionEvt(targetPath);
                    }
                });
            // setTimeout(function () {
            //     updateSVGMap();
            // }, 10000);
        // });
    }
    //********************************
    // bindRegionEvt
    //********************************
    function bindRegionEvt (opt) {
        var $path = opt;
        $path.unbind('hover');
        $path.data('bindhover', 1);
        $path.hover(function () {
            console.log($path.offset().top - $svgWrapper.offset().top);
            console.log($path.offset().left - $svgWrapper.offset().left);
            var region = subdivisionKey[parseInt($path.attr('id').replace('JP-', '')) - 1];
            region = isLocaleJP ? subdivisionLang[region] : region;
            var count = $path.data().devices;
            $svgWrapper.find('g').append($path);
            // console.log($path.attr('id'));
            var w = $path[0].getBoundingClientRect().width;
            var msg = $.fn.replaceElString(tooltipTemp, {region: region, count: count}); 
            $regionInfo.html(msg).addClass('active');
            $regionInfo.css({
                top: Math.abs($path.offset().top - $svgWrapper.offset().top),
                left: $path.offset().left - $svgWrapper.offset().left + w/2
            });
        });
    }
    //********************************
    // bindRegionEvt
    //********************************
    function resizeMap(evtType) {
        console.log('=====================resizeMap========================');
        // adjust map
        var winHeight = window.innerHeight;
        var winWidth = window.innerWidth;
        var mapHeight = winHeight - topbarHeight - 80;
        var cardCount = 5;
        var cardMargin = 10;
        var cardPadding = 10;
        var cardBorder= 1;
        var cardHeight = 0;
        console.log('mapHeight:' + mapHeight);
        console.log('winWidth:' + winWidth);
        if ( mapHeight > 400 && winWidth > 414) {
            if (mapHeight > $('#my-map').width()) {
                mapHeight = $('#my-map').width();
            }
            $svgContainer.height(mapHeight);
            $svgContainer.width(mapHeight);
            // adjust map card
            // count single card hight
            cardHeight = (mapHeight - cardMargin*(cardCount-1))/cardCount -cardPadding*2 - cardBorder*2;
            // apply to all cards
            $('.my-map-card').height(cardHeight);
        } else {
            $svgContainer.height($('#my-map').width());
            $svgContainer.width($('#my-map').width());
            cardHeight = ($('#my-map').height() - cardMargin*(cardCount-1))/cardCount -cardPadding*2 - cardBorder*2;
            // apply to all cards
            $('.my-map-card').height(cardHeight);
        }
        drawSVG();
    }
});
