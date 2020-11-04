$(function () {
    const lengthMenuOpt = '<select name="listLen" class="form-control input-sm"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select>';
    const $listLenMenu = $('#table_length');
    let curListLen = 10;
    let curPage = 1;
    // Init show list len selector
    var lengthMenuLan = LAN.dataTable.hasOwnProperty('lengthMenu') ? LAN.dataTable.lengthMenu : LAN.dataTable.sLengthMenu;
    if ($listLenMenu.length > 0) {
        $listLenMenu.find('label').html(lengthMenuLan.replace('_MENU_', lengthMenuOpt));
    }
    if ($('#sys-msg').length > 0) {
        $("#sys-msg").click(function() {
            $("#sys-message2").hide();
        });
    }

    if ($('#organization_location_detail').length > 0) {
        var hash = document.location.hash;
        ($(hash + "_panel").length > 0) ? $(hash + "_panel").trigger("click") : $("#detail_panel").trigger("click");
    }

    if ($('#locationCreateForm').length > 0) {
        $('#title').focus();
        $("#saveButton").bind("click", function() {
            if ($('#title').val().trim() == '' || $('#ssid').val().trim() == '' || $('#ssid_passwd').val().trim() == '' || $('#address').val().trim() == '' || $('#tz').val().trim() == '') {
                $('#title').focus();
                return swal({
                    title: "Parameter incorrect",
                    text: "please fill out the required field(s)",
                    type: "warning",
                });
            }
            swal({
                title: 'Do you confirm to submit form data',
                showCancelButton: true,
                confirmButtonText: 'Create',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger m-l-10',
                allowOutsideClick: false
            }, function (isConfirm) {
                if (isConfirm) {
                    var params = {
                        customer_id: $('#customer_id').val(),
                        title: $('#title').val().trim(),
                        ssid: $('#ssid').val().trim(),
                        ssid_passwd: $('#ssid_passwd').val().trim(),
                        address: $('#address').val().trim(),
                        phone: $('#phone').val().trim(),
                        memo: $('#memo').val().trim(),
                        tz: $('#tz').val()
                    };
                    $.post("/organization/location/create_do", params, function(result) {
                        if (result.errno == 0) {
                            setTimeout(function(){
                                swal({
                                    title: "Good job!",
                                    text: "create is successful location",
                                    type: "success",
                                },
                                    function (isConfirm) {
                                        location.href = "/organization/location?customer_id=" + $('#customer_id').val();
                                    });
                            }, 100);
                        } else {
                            setTimeout(function(){ swal(result.errmsg, "", "error"); }, 100);
                        }
                    });
                }
            });
        });
    }

    if ($('#locationUpdateForm').length > 0) {
        let $targetForm = $('#locationUpdateForm');
        $targetForm.find('button[type=submit]').prop('disabled', false);
        $targetForm.submit(function (e) {
            e.preventDefault();
            let self = this;
            let isValid = $(this).parsley().validate();
            if (!isValid) {
                console.log('isValid:' + isValid);
                return false;
            }
            $(self).attr('data-stat', 'loading');
            var params = {
                location_id: $('#location_id').val(),
                title: $('#title').val().trim(),
                ssid: $('#ssid').val().trim(),
                ssid_passwd: $('#ssid_passwd').val().trim(),
                address: $('#address').val().trim(),
                phone: $('#phone').val().trim(),
                memo: $('#memo').val().trim(),
                tz: $('#tz').val()
            };
            $.post("/organization/location/update_do", params, function(result) {
                $(self).attr('data-stat', '');
                $(self).find('button[type=submit]').prop('disabled', false);
                if (result.errno == 0) {
                    swal({
                        title: LAN.done,
                        type: "success",
                        showConfirmButton: false,
                        timer: 3000
                    });
                    setTimeout( function() {
                        location.href = PAGE.custDetail.replace('{customer_id}', self[0].customer_id.value);
                    }, 3000);
                } else {
                    setTimeout(function(){ swal(result.errmsg, "", "error"); }, 100);
                }
            });
        });

        $("#deleteButton").bind("click", function() {
            swal({
                title: LAN.are_u_sure,
                text: LAN.cannot_recover_loc, 
                type: "warning",
                showCancelButton: true,
                cancelButtonText: LAN.cancel,
                confirmButtonClass: "btn-warning",
                confirmButtonText: LAN.yes_del_it
            }, function () {
                $.post("/organization/location/remove", {location_id: $('#location_id').val()}, function(result) {
                    if (result.errno == 0) {
                        swal({
                            title: LAN.done,
                            type: "success",
                            showConfirmButton: false,
                            timer: 3000
                        });
                        setTimeout( function() {
                            location.href = PAGE.custDetail.replace('{customer_id}', $(self)[0].customer_id.value);
                        }, 3000);
                    } else {
                        setTimeout(function(){ swal(result.errmsg, "", "error"); }, 100);
                    }
                });
            });
        });
    }

    if ($('#inviteUserModal').length > 0) {
        $("#inviteUserButton").bind("click", function() {
            var params = {}
            if ($('input[name=create_type]:checked').val() == 'new_user') {
                if ($('#account').val().trim() == "" || $('#email').val().trim() == "" ||  $('#name').val().trim() =="") {
                    return swal({
                        title: "Parameter incorrect",
                        text: "please fill out the required field(s)",
                        type: "warning",
                    });
                }
                var params = {
                    create_type: $('input[name=create_type]:checked').val(),
                    location_id: $('#location_id').val(),
                    account: $('#account').val().trim(),
                    email: $('#email').val().trim(),
                    name: $('#name').val().trim()
                };
            } else {
                if ($('#user_id').val() == "") {
                    return swal({
                        title: "Parameter incorrect",
                        text: "please fill out the required field(s)",
                        type: "warning",
                    });
                }
                var params = {
                    create_type: $('input[name=create_type]:checked').val(),
                    location_id: $('#location_id').val(),
                    user_id: $('#user_id').val()
                };
            }
            $.post("/organization/location/invite_user", params, function(result) {
                if (result.errno == 0) {
                    setTimeout(function(){
                        swal({
                            title: "Good job!",
                            text: "invite user is successful",
                            type: "success",
                        },
                            function (isConfirm) {
                                location.href = "/organization/location/detail?customer_id=" + $('#customer_id').val() + "&location_id=" + $('#location_id').val() + "&t=" + Date.now() + "#users";
                            });
                    }, 100);
                } else {
                    setTimeout(function(){ swal(result.errmsg, "", "error"); }, 100);
                }
            });
        });
    }

    if ($('#createDeviceModal').length > 0) {
        $("#createDeviceButton").bind("click", function() {
            var params = {
                location_id: $('#location_id').val(),
                uid: $('#uid').val()
            };
            $.post("/organization/location/create_device", params, function(result) {
                if (result.errno == 0) {
                    setTimeout(function(){
                        swal({
                            title: "Good job!",
                            text: "create is successful device",
                            type: "success",
                        },
                            function (isConfirm) {
                                location.href = "/organization/location/detail?customer_id=" + $('#customer_id').val() + "&location_id=" + $('#location_id').val() + "&t=" + Date.now() + "#device";
                            });
                    }, 100);
                } else {
                    setTimeout(function(){ swal(result.errmsg, "", "error"); }, 100);
                }
            });
        });
    }

    if ($('.removeUser').length > 0) {
        $(".removeUser").bind("click", function() {
            var user_id = $(this).data('user_id');
            swal({
                title: 'Do you confirm to remove the user 「' + $(this).data('account') + '」',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger m-l-10',
                allowOutsideClick: false
            }, function (isConfirm) {
                if (isConfirm) {
                    $.post("/organization/location/remove_user", {location_id: $('#location_id').val(), user_id: user_id}, function(result) {
                        if (result.errno == 0) {
                            setTimeout(function(){
                                swal({
                                    title: "Good job!",
                                    text: "delete is successful user",
                                    type: "success",
                                },
                                    function (isConfirm) {
                                        location.href = "/organization/location/detail?customer_id=" + $('#customer_id').val() + "&location_id=" + $('#location_id').val() + "&t=" + Date.now() + "#users";
                                    });
                            }, 100);
                        } else {
                            setTimeout(function(){ swal(result.errmsg, "", "error"); }, 100);
                        }
                    });
                }
            });
        });
    }

    if ($('.removeDevice').length > 0) {
        $(".removeDevice").bind("click", function() {
            var uid = $(this).data('uid');
            swal({
                title: 'Do you confirm to remove the user 「' + $(this).data('uid') + '」',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger m-l-10',
                allowOutsideClick: false
            }, function (isConfirm) {
                if (isConfirm) {
                    $.post("/organization/location/remove_device", {location_id: $('#location_id').val(), uid: uid}, function(result) {
                        if (result.errno == 0) {
                            setTimeout(function(){
                                swal({
                                    title: "Good job!",
                                    text: "delete is successful",
                                    type: "success",
                                },
                                    function (isConfirm) {
                                        location.href = "/organization/location/detail?customer_id=" + $('#customer_id').val() + "&location_id=" + $('#location_id').val() + "&t=" + Date.now() + "#device";
                                    });
                            }, 100);
                        } else {
                            setTimeout(function(){ swal(result.errmsg, "", "error"); }, 100);
                        }
                    });
                }
            });
        });
    }


    //** init pagination of VIP
    if ($('#vip').length > 0 && !$('body').data().iot) {
        getVIPList({curPage: 1, curListLen: 10});
    }
    //** init pagination of location list 
    if ($('#organization_location').length > 0) {

        $listLenMenu.find('select[name=listLen]').change(function (e) {
            var val = $(this).val();
            getLocationList({ curPage: 1, curListLen: val, destoryPagination: true });
        });
        getLocationList({curPage: 1, curListLen: 10, tableWrapper: '#locationWrapper'});
    }
    // **********************
    // getVIPList
    // {Object} param : curPage, curListLen
    // **********************
    const vipListRowTemp = $('#vipListRowTemp').html();
    function getVIPList(param) {
        console.log('getVIPList implement');
        console.log(param);
        let url = '/organization/location/vip_page/{curPage}';
        let info = LAN.dataTable.info ? LAN.dataTable.info : LAN.dataTable.sInfo;
        info = info.replace('_TOTAL_', '{dataLen}').replace('_START_', '{curStart}').replace('_END_', '{curEnd}');
        let $vipTbody = $('#vip tbody');
        let $vipInfo = $('#vipTable_info');
        let locationID = $('input[name=location_id]').val();
        let customerID = $('input[name=customer_id]').val();
        let formData = {
            per_page: param.curListLen,
            customer_id: customerID,
            location_id: locationID
        };
        url = $.fn.replaceElString(url, param); 
        function callback (rs) {
            console.log(rs);
            // rs = JSON.parse(rs);
            $vipTbody.empty();
            $vipInfo.empty();
            if (param.destoryPagination) {
                $('#pagination').twbsPagination('destroy');
            }
            $('.listWrapper').attr('data-size', rs.data.length);
            if (rs.data.length < 1) {
                return;
            }
            let curStart = ((parseInt(rs.curr_page) - 1) * param.curListLen) + 1;
            $.each(rs.data, function (i, row) {
                var _row = {};
                _row.cv_result = row.cv_result ? 'Member' : 'Unknown';
                if (row.gender !== '') {
                    _row.gender = row.gender ? 'Male' : 'Female';
                } else {
                    _row.gender = row.cv_gender ? 'Male' : 'Female';
                }
                _row.id = i + curStart; 
                _row.name = row.name ? row.name : '';
                _row.birthday = row.birthday ? row.birthday : '';
                _row.visited_at = moment(row.visited_at).valueOf() +  parseInt($('input[name=gmt_offset]').val());
                _row.visited_at = moment(_row.visited_at).format('YYYY-MM-DD HH:mm:ss');
                _row.image_url = row.image_url;
                console.log(row);
                var temp = $($.fn.replaceElString(vipListRowTemp, _row));
                $vipTbody.append(temp);
            });
            let infoDatasets = {
                curStart: curStart,
                curEnd: parseInt(rs.curr_page) * param.curListLen > rs.rows ? rs.rows : parseInt(rs.curr_page) * param.curListLen,
                dataLen: rs.rows
            };
            info = $.fn.replaceElString(info, infoDatasets); 
            $vipInfo.html(info);
            initPagination(rs, $('#pagination'), 'vip');
        }
        $.post(url, formData, callback, 'json');
    }
    // ***************************
    // getLocationList
    // ***************************
    const locationListRowTemp = $('#locationListRowTemp').html();
    function getLocationList(param) {
        console.log('getLocationList implement');
        console.log(param);
        let $tableWrapper = $(param.tableWrapper);
        let url = '/organization/location/list_page/{curPage}';
        let info = LAN.dataTable.info ? LAN.dataTable.info : LAN.dataTable.sInfo;
        info = info.replace('_TOTAL_', '{dataLen}').replace('_START_', '{curStart}').replace('_END_', '{curEnd}');
        let $tBody = $tableWrapper.find('tbody');
        let $info = $tableWrapper.find('#table_info');
        let formData = {
            per_page: param.curListLen,
            customer_id: $('input[name=customer_id]').val()
        };
        url = $.fn.replaceElString(url, param); 
        function callback (rs) {
            console.log(rs);
            // rs = JSON.parse(rs);
            $tBody.empty();
            $info.empty();
            if (param.destoryPagination) {
                $('#pagination').twbsPagination('destroy');
            }
            $('.listWrapper').attr('data-size', rs.data.length);
            if (rs.data.length < 1) {
                return;
            }
            let curStart = ((parseInt(rs.curr_page) - 1) * param.curListLen) + 1;
            $.each(rs.data, function (i, row) {
                console.log(row);
                var temp = $($.fn.replaceElString(locationListRowTemp, row));
                $tBody.append(temp);
            });
            let infoDatasets = {
                curStart: curStart,
                curEnd: parseInt(rs.curr_page) * param.curListLen > rs.rows ? rs.rows : parseInt(rs.curr_page) * param.curListLen,
                dataLen: rs.rows
            };
            info = $.fn.replaceElString(info, infoDatasets); 
            $info.html(info);
            initPagination(rs, $('#pagination'), 'location');
        }
        $.post(url, formData, callback, 'json');
    }
    // **********************
    // initPagination
    // {Object}, vip or location list
    // {Object}, pagination ele
    // {String}, type of table, vip or location
    // **********************
    function initPagination (rs, targetEle, type) {
        var $pagination = targetEle;
        $pagination.twbsPagination({
            first: '',
            last: '',
            prev: LAN.dataTable.paginate ? LAN.dataTable.paginate.previous : LAN.dataTable.oPaginate.sPrevious,
            next: LAN.dataTable.paginate ? LAN.dataTable.paginate.next : LAN.dataTable.oPaginate.sNext,
            totalPages: rs.pages,
            visiblePages: 5,
            initiateStartPageClick: false,
            onPageClick: function (event, page) {
                console.log('page click');
                if (type === 'vip') {
                getVIPList({curPage: page, curListLen: $('select[name=vipTable_length]').val() ? $('select[name=vipTable_length]').val() : 10});
                }
                if (type === 'location') {
                getLocationList({curPage: page, curListLen: $('select[name=listLen]').val() ? $('select[name=listLen]').val() : 10, tableWrapper: '#locationWrapper'});
                }
            }
        });
    }
});
