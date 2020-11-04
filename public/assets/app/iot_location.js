$(function () {
    const $devicesList = $($('#devicesListTemp').html());
    const deviceRowTemp = $devicesList.find('tbody tr')[0].outerHTML;
    let $devicesWrapper = $('.locWrapper .tab-pane.active .devicesWrapper');
    let curLocID = $('#locNavPills').length > 0 ? $('#locNavPills li.active').data().loc : null;
    // **init
    getDevicebyLoc(curLocID);
    // **NavPills bind click
    $('#locNavPills li').click(function (e) {
        curLocID = e.currentTarget.dataset.loc;
        setTimeout(function () {
            $devicesWrapper = $('.locWrapper .tab-pane.active .devicesWrapper');
            getDevicebyLoc(curLocID);
        }, 200);
    });
    // *********************************
    // getDevicebyLoc 
    // {String}, location id
    // *********************************
    function getDevicebyLoc (id) {
        if (!id) {
            $('.locWrapper').attr('data-stat', '');
            return;
        }
        $('.locWrapper').attr('data-stat', 'loading');
        // var id = '9d1d1ce6-be35-404a-a181-4ffcbd5afce2';
        var params = { location_id : id };
        $.post("/organization/location/get_device", params, function (rs) {
            $('.locWrapper').attr('data-stat', '');
            if (rs && rs.length > 0) {
                console.log('==>show device list');
                showDeviceList(rs);
                return;
            }
            $devicesWrapper.empty(); 
        });
    }
    // ******************************
    // showDeviceList
    // {Array} devices datasets of one location
    // ******************************
    function showDeviceList (datasets) {
        let $targetTbody = $devicesList.find('tbody').empty();
        $devicesWrapper.append($devicesList);
        for (var i in datasets) {
            var agent_ip = JSON.parse(datasets[i].agent_ip);
            $.extend(datasets[i], {
                order_id : parseInt(i) + 1,
                stat: datasets[i].online === '1' ? 'online' : 'offline',
                stat_icon: datasets[i].online === '1' ? '<i class="icomoon icomoon-online m-r-5" style="color: #81c868"></i>' : '<i class="icomoon icomoon-warning m-r-5" style="color: #f05050"></i>',
                ip : agent_ip.wan_ip ? agent_ip.wan_ip : agent_ip.lan_ip 
            });
            var temp = $($.fn.replaceElString(deviceRowTemp, datasets[i]));
            $targetTbody.append(temp);
        }
    }
});
