$(function () {
    // lan
    const lan = {
        'are_u_unbind' : LAN.are_u_sure_rm_device,
        'are_u_sure' : LAN.are_u_sure,
        'btn_remove' : LAN.remove
    };
    // element
    const $page = $('#device_setting');
    const $devicePanel = $('.devicePanel');
    const $locPanel = $('.locPanel');
    // common
    const tempConf = {
        deviceInfo: $('#deviceInfoTemp').html(),
        locInfo: $('#locInfoTemp').html(),
        // apList: $('#apListRow').html(),
        curSSID: $('#curSSIDTemp').html(),
        pairedDeviceListRow: $('#pairedDeviceListRow').html()
    };
    const connIcon = {
        'stable': '<i class="icomoon icomoon-online m-r-5" style="color: #81c868"></i>',
        'offline': '<i class="icomoon icomoon-offline m-r-5" style="color: #ccc"></i>',
    };
    const deviceLongPullTime = 10000; //10000
    let uid = $('input[name=uid]').val();
    let api = '';
    let deviceData = {
        conn_icon: '',
        nickname: '',
        ssid: '',
        model: '',
        hw_version: '',
        fw_version: '',
        mac: '',
        uid: uid,
        did: '',
        ip: '' 
    };
    let locData = { 
        name: '',
        ssid: '',
        customer_id: '',
        people_counting: '',
        location_id: '',
        phone: '',
        fw_upgrade_time: '' 
    };
    let deviceOnline = "1";
    // init
    //**set loading style
    $page.attr('data-stat', 'loading');
    //**get device Info 
    getDeviceInfo(false);
    //
    //***************************
    // getDeviceInfo
    //***************************
    function getDeviceInfo (async) {
        $.ajax({
            url: '/system/device/feed/',
            metho: 'GET',
            dataType: 'JSON',
            async: async !== undefined ? async : true,
            data: {
                uid: uid
            },
            timeout: '10000'
        }).done(function (rs) {
            // rs.online_status = 1;
            if (!rs.did) {
                showErr('No Data Found');
                // let opener = window.opener;
                // opener.location.href = opener.location.href + '&error=No data found';
                // window.close();
                return;
            }
            if (async !== undefined && !async) {
                console.log('init device info at first time');
                initLocInfo(rs);
                // initSettings(rs.setting); 
                // initSchedule(rs.scheduling);
                // apiUnbind = apiUnbind.replace('{did}', rs.did).replace('{customer_id}', rs.location_info.customer_id).replace('{location_id}', rs.location_info.location_id);
            }
            // api = apiTemp.replace('{did}', rs.did);

            setDeviceInfo(rs);
            setPairedDeviceInfo(rs);

            //rs.online_status = "0";
            var _deviceOnline = rs.online;
            //
            if (_deviceOnline !== deviceOnline) {
                deviceOnlineChange(_deviceOnline);
            }
        }).always(function (rs) {
            if (!rs.did) {
                return;
            }
            setTimeout(function () {
                // TODO query device Info again
                getDeviceInfo();
            }, deviceLongPullTime);
        });
    } 
    //
    //***************************
    // setDeviceInfo
    // {Object}, device data
    //***************************
    function setDeviceInfo (rs) {
        console.log('setDeviceInfo');
        // var isTheSameSSID = rs.ssid === rs.location_info.ssid;
        var isTheSameSSID = 1;
        var _ssid = rs.ssid ? rs.ssid : '-';
        var agentIP = JSON.parse(rs.agent_ip);
        deviceData = $.extend(deviceData, {
            conn_icon: rs.online === '1' ? connIcon.stable : connIcon.offline,
            nickname: rs.nickname,
            ssid: isTheSameSSID || _ssid === '-' ? _ssid : '<span class="text-danger">' + _ssid  +'</span>',
            model: rs.model,
            hw_version: rs.hw_version,
            fw_version: rs.fw_version,
            mac: rs.mac,
            did: rs.did,
            // uid: uid,
            ip: agentIP.wan_ip ? agentIP.wan_ip : agentIP.lan_ip
        });
        var temp = $.fn.replaceElString(tempConf.deviceInfo, deviceData);
        $devicePanel.find('.panel-body').html(temp);
        //$("#tab-wifi").find('.ssidStatus').html($devicePanel.find('.ssidStatus').html());
    }
    //
    //***************************
    // setPairedDeviceInfo
    // {Object}, device data
    //***************************
    function setPairedDeviceInfo (rs) {
        console.log('setPairedDeviceInfo implement');
        let datasets = rs.paired_device;
        let $tbody = $('#tab-pairedDevice tbody');
        $tbody.empty();
        if (!datasets || datasets.length < 1) {

            $page.attr('data-stat', '');
            $('#tab-pairedDevice').attr('data-stat', 'empty');
            return;
        }
        console.log(datasets);
        for (var d in datasets) {
            var temp = $.fn.replaceElString(tempConf.pairedDeviceListRow, datasets[d]);
            $tbody.append(temp);
        }
        $page.attr('data-stat', '');
    }
    //***************************
    // initLocInfo
    // {Object}, device data
    //***************************
    function initLocInfo (rs) {
        //TODO
        if (!rs.location) {
            $locPanel.hide();
            return;
        }
        var data = {
            name: rs.location.title,
            ssid: rs.location.ssid,
            customer_id: rs.location.customer_id,
            location_id: rs.location.location_id,
            phone: rs.location.phone
        };
        var temp = $($.fn.replaceElString(tempConf.locInfo, data));
        locData = data;
        $locPanel.find('.panel-body').empty().append(temp);
        $locPanel.show();
    }
    //**************************
    // deviceOnloneChange
    // {boolean}, 1 or 0
    //**************************
    function deviceOnlineChange (stat) {
        deviceOnline = stat;
        $page.attr('data-online', deviceOnline);
        if (stat === '1') {
            $('.tab').removeClass('disabled');
            $('#tab-operation').find('button').prop('disabled', false);
            return;
        }
        // offline
        $('.tab').not('.operation').addClass('disabled');
    }
    //********************************
    //Factory reset button click event
    // TODO lan key
    //********************************
    $("#reboot, #factoryReset").click(function (e) {
        let act = $(this).attr('name');
        let url = '/system/device/control';
        let text = {
            reboot: LAN.sure_reboot_device,
            factoryReset: LAN.sure_reset_device
        };
        let done_text = {
            reboot: LAN.done_reboot,
            factoryReset: LAN.done_reset 
        };
        let change_set = {
            reboot: 'reboot',
            factoryReset: 'factory_reset' 
        };
        e.preventDefault();
        swal({
            title: lan.are_u_sure, //"Are you sure?",
            text: text[act],
            type: "warning",
            showCancelButton: true,
            cancelButtonText: LAN.cancel,
            confirmButtonClass: "btn-danger",
            confirmButtonText: LAN.yes
        },
        function(){
            var formData = {
                act: act,
                uid: uid
            };
            var xhr = $.ajax({
                url: url, 
                method: 'POST',
                data: formData
            }).done(function (rs) {
                if (rs.errmsg) {
                    swal(
                    {
                        title: LAN.error,
                        text: rs.errmsg,
                        type: "warning",
                    });
                    return;
                }
                swal(
                    {
                        title: LAN.done,
                        text: done_text[act],
                        type: 'success'
                    }
                );
            });
        });
    });
    // ********************
    // showErr
    // ********************
    function showErr(msgTxt) {
        // let urlParams = new URLSearchParams(window.location.search);
        // if (!urlParams.has('error')) {
        //     return;
        // }
        let $targetEle = $('#sys-message');
        setTimeout(function () {
            $targetEle.find('.sys-text').html(msgTxt);
            $targetEle.addClass('action');
        }, 100);
        $('body').click(function (e) {
            e.stopPropagation();
            window.close();
        });
    }
});
