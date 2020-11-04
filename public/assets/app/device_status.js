$(function() {
    // ele
    var $navTabs = $(".nav-tabs");
    var $targetTable = {};
    var $wrapper = $('.mainWrapper');
    // common
    var containerID = $('.content-page .container').attr('id');
    var defaultState = $('#' + containerID).data().page;
    var curState = $('#' + containerID).data().page;
    var noHisState = location.pathname.indexOf(curState) < 0; 
    var defaultPathname = location.pathname.replace(curState, '').replace(/\/$/, '') + '/';
    var rowTempConf = $($("#statusRowTemp").html()).find('tr')[0].outerHTML;
    var orgID = $('input[name=org_id]').val();
    // var rowTempConf = {
    //     'offline': $($("#statusRowTemp").html()).find('tr')[0].outerHTML,
    //     'no_uplink': $($("#statusRowTemp").html()).find('tr')[0].outerHTML,
    //     'unstable': $($("#statusRowTemp").html()).find('tr')[0].outerHTML,
    //     'fw_version_incorrect': $($("#statusRowTemp").html()).find('tr')[0].outerHTML,
    //     'ssid_incorrect': $($("#statusRowTemp").html()).find('tr')[0].outerHTML,
    //     'sd_damage': $($("#statusRowTemp").html()).find('tr')[0].outerHTML,
    //     'sd_remove': $($("#statusRowTemp").html()).find('tr')[0].outerHTML
    // };
    var deviceDatasets = null;
    
    //init
    //**php check hash , js not need to do
    $targetTable = $("#datatable-" + curState);
    //**hash change
    window.onpopstate = function(event) {
        console.log('onpopstate');
        var state = history.state;
        if (!state) {
            return;
        }
        curState = state;
        $targetTable = $("#datatable-" + curState);
        activeTabs();
        activeTable();
    };
    //replace Hist
    if (noHisState) {
        history.replaceState(curState, curState, defaultPathname + curState);
    }
    //**active tabs
    activeTabs();
    //**dataTable init
    activeTable();
    //********************************
    //activeTable
    //Init table with dataTable plugin
    //********************************
    function activeTable () {
        $wrapper.attr('data-stat', 'loading');
        if (deviceDatasets !== null) {
            initTable();
            return;
        }
        $.ajax({
            // url: '/dashboard/' + containerID + '/feeds/' + curState,
            url: '/dashboard/device_status/feeds/' + orgID,
            type: 'POST',
            dataType: 'JSON',
            data: {}
        }).done(function(rs) {
            var datasets = rs;
            deviceDatasets = {
                unstable: datasets.unstable,
                no_uplink: datasets.no_uplink,
                offline: datasets.offline,
                inactive: rs.inactive_devices,
                // fw_incorrect: rs.incorrect_fw_ver,
                // ssid_incorrect: rs.incorrect_ssid,
                // sd_damage: rs.sd_card_damage,
                // sd_remove: rs.sd_card_remove
            };
            initTable();
        });
    }
    //********************************
    //activeTabs
    //Set tab ative class and bind click evnet to change hash
    //********************************
    function activeTabs () {
        $navTabs.find("a[href=#status-" + curState + "]").trigger("click").parent().addClass("active");
        $navTabs.find("a").unbind("click");
        $navTabs.find("a").click(function (e) {
            curState = $(this).attr("href").replace("#status-", "");
            if (location.pathname !== defaultPathname + curState) { 
                console.log("pushState");
                history.pushState(curState, curState, defaultPathname + curState);
                console.log('curState:' + curState);
                $targetTable = $("#datatable-" + curState);
            }
            activeTable();
        });
    }
    //********************************
    // initTable
    //********************************
    function initTable () {
        // [] -> empty
        console.log(deviceDatasets);
        d = deviceDatasets;
        if (!deviceDatasets[curState] || Array.isArray(deviceDatasets[curState])) {
            $wrapper.attr('data-stat', '');
            $('#status-' + curState).attr('data-row', 0);
            return;
        }
        //
        //
        //{
        //     - device_status:
        //     - {
        //     - stable: [ ],
        //     - unstable: [ ],
        //     - no_uplink: [ ],
        //     - offline:
        //     - {
        //         - FGSLLBUB6856:
        //         - {
        //             - location_id: "L428",
        //             - customer_id: "C428",
        //             - location_name: "Location 1",
        //             - customer_name: "cc",
        //             - customer_manager: ""
        //             - },
        //         - FFFF7BUFDTSX:
        //         - {},
        //         - }
        //     - },
        //     - inactive_devices: [ ],
        //     - incorrect_fw_ver: [ ],
        //     - incorrect_ssid: [ ],
        //     - sd_card_damage: [ ],
        //     - sd_card_remove: [ ]
        //}
        var len = Object.keys(deviceDatasets[curState]).length;
        var isDataTable = $.fn.DataTable.isDataTable("#" + $targetTable[0].id);
        if (isDataTable) {
            $wrapper.attr('data-stat', '');
            return;
            // var target = $targetTable.DataTable().destroy();
        }
        emptyList();
        $('#status-' + curState).attr("data-row", len);
        if (len < 1) {// Shound not happen
            return;
        }
        $.each(deviceDatasets[curState], function (key, rowData) {
            rowData.uid = key;
            // Can change object here
            // rowData.uid = rowData.device_uid
            console.log(rowData);
            var temp = $($.fn.replaceElString(rowTempConf, rowData));
            $targetTable.append(temp);
        });
        $targetTable.dataTable(
            {
                language: LAN.dataTable,
                order: [[0, 'desc']],
                lengthChange: true,// isOverTenCount ? true: false,
                "drawCallback": function( settings ) {
                    $wrapper.attr('data-stat', '');
                }
            }
        );
    }
    
    //********************************
    //emptyList
    //clear tbody
    //reset data-state
    //********************************
    function emptyList () {
        //console.log("emptyList is executed.");
        $targetTable.find("tbody").empty();
        $targetTable.attr("data-state", "loading");
    }
     
}); 
