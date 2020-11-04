$(function () {
    if ($('#sys-msg').length > 0) {
        $("#sys-msg").click(function() {
            $("#sys-message2").hide();
        });
    }

    if ($('#admin_account').length > 0) {
        const lengthMenuOpt = '<select name="listLen" class="form-control input-sm"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select>';
        const $listLenMenu = $('#table_length');
        let curListLen = 10;
        let curPage = 1;
        let lengthMenuLan = LAN.dataTable.hasOwnProperty('lengthMenu') ? LAN.dataTable.lengthMenu : LAN.dataTable.sLengthMenu;
        if ($listLenMenu.length > 0) {
            $listLenMenu.find('label').html(lengthMenuLan.replace('_MENU_', lengthMenuOpt));
        }
        // ** init acc list
        $listLenMenu.find('select[name=listLen]').change(function (e) {
            var val = $(this).val();
            getAccList({ curPage: 1, curListLen: val, destoryPagination: true });
        });
        getAccList({curPage: 1, curListLen: 10});
    }

    if ($('#createAccountForm').length > 0){
        let $targetForm = $('#createAccountForm');
        $targetForm.find('button[type=submit]').prop('disabled', false);
        $('#email').focus();
        $targetForm.submit(function (e) {
            e.preventDefault();
            let isValid = $(this).parsley().validate();
            if (!isValid) {
                console.log('isValid:' + isValid);
                return false;
            }
            $(this).attr('data-stat', 'loading');
            updateAccount('create', this);
        });
    }

    if ($('#updateAccountForm').length > 0){
        let $targetForm = $('#updateAccountForm');
        $targetForm.find('button[type=submit]').prop('disabled', false);
        $targetForm.submit(function (e) {
            e.preventDefault();
            let isValid = $(this).parsley().validate();
            if (!isValid) {
                console.log('isValid:' + isValid);
                return false;
            }
            $(this).attr('data-stat', 'loading');
            updateAccount('modify', this);
        });
    }

    if ($("#sendEmailButton").length > 0) {
        $("#sendEmailButton").bind("click", function() {

            swal({
                title: 'Do you confirm to resend the email',
                showCancelButton: true,
                confirmButtonText: 'Send',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger m-l-10',
                allowOutsideClick: false
            }, function (isConfirm) {
                if (isConfirm) {
                    $.post("/admin/account/send_activate_email", {staff_id: $('#staff_id').val()}, function(result) {
                        if (result.errno == 0) {
                            setTimeout(function(){
                                swal("Good job!", "resend a email is successful admin staff", "success");
                                $('#resentButton').unbind("click").addClass('disabled');
                            }, 100);
                        } else {
                            setTimeout(function(){ swal(result.errmsg, "", "error"); }, 100);
                        }
                    });
                }
            });
        });
    }

    $('#deleteButton').bind("click", function() {
        swal({
            title: LAN.are_u_sure,
            text: LAN.not_recover_user, 
            type: "warning",
            showCancelButton: true,
            cancelButtonText: LAN.cancel,
            confirmButtonClass: "btn-warning",
            confirmButtonText: LAN.yes_del_it
        }, function (isConfirm) {
            if (isConfirm) {
                $.post("/admin/account/remove", {staff_id: $('#staff_id').val()}, function(result) {
                    if (result.errno == 0) {
                        swal({
                            title: LAN.done,
                            type: "success",
                            showConfirmButton: false,
                            timer: 3000
                        });
                        setTimeout(function(){
                            location.href = PAGE.adminAccount;
                        }, 3000);
                    } else {
                        setTimeout(function(){ swal(result.errmsg, "", "error"); }, 100);
                    }
                });
            }
        });
    });
    // *************************
    // bindResetReactivate
    //
    // *************************
    function bindResetReactivate (datasets, ele) {
        ele.find('.reActivateBtn, .resetPwdBtn').click(function (e) {
            e.preventDefault();
            let staffID = $(this).attr('data-sid');
            let url = ! datasets.is_activated ? '/admin/account/send_activate_email' : '/admin/account/send_resetpwd_email';
            swal({
                title: LAN.are_u_sure,
                text: datasets.is_activated ? LAN.sure_reset_msp_usr_pwd : LAN.sure_reactivate_msp_usr,
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: LAN.cancel,
                confirmButtonClass: 'btn-warning',
                confirmButtonText: datasets.is_activated ? LAN.reset : LAN.yes,
            }, function (isConfirm) {
                $(this).prop('disabled', true);
                if ( ! isConfirm || ! staffID) return;
                $.post(url, {staff_id : staffID}, function (rs) {
                    console.log(rs);
                    $(this).prop('disabled', false);
                    if (!rs.errno) {
                        swal({
                            title: LAN.done,
                            type: "success",
                            showConfirmButton: false,
                            timer: 3000
                        });
                        return;
                    }
                    swal(LAN.error, '', 'warning');
                });
            });
        });
    }
    // *************************
    // updateAccount
    // {String}, action type, create & modify
    // {Object}, target form element
    // account creating and modifying
    // *************************
    function updateAccount (type, targetEle) {
        var params = {
            staff_id: type === 'modify' ? $('#staff_id').val() : null,
            email: $('#email').val(),
            passwd: $('#passwd').val(),
            name: $('#name').val(),
            locale: $('#locale').val(),
            description: $('#description').val()
        };
        var url = {
            create: "/admin/account/create_do",
            modify: "/admin/account/update_do"
        }
        $.post(url[type], params, function(result) {
            $(targetEle).attr('data-stat', '');
            if (result.errno == 0) {
                swal({
                    title: LAN.done,
                    type: "success",
                    showConfirmButton: false,
                    timer: 3000
                });
                setTimeout(function () {
                    location.href = PAGE.adminAccount;
                }, 3000);
            } else {
                swal(result.errmsg, "", "error");
            }
        });
    }
    // **********************
    // getAccList
    // {Object} param : curPage, curListLen
    // **********************
    const listRowTemp = $('#listRowTemp').html();
    function getAccList(param) {
        console.log('getAccList implement');
        console.log(param);
        let url = '/admin/account/page/{curPage}';
        let info = LAN.dataTable.info ? LAN.dataTable.info : LAN.dataTable.sInfo;
        info = info.replace('_TOTAL_', '{dataLen}').replace('_START_', '{curStart}').replace('_END_', '{curEnd}');
        let $accTbody = $('tbody');
        let $accInfo = $('#table_info');
        let locationID = $('input[name=location_id]').val();
        let customerID = $('input[name=customer_id]').val();
        let formData = {
            per_page: param.curListLen
        }
        url = $.fn.replaceElString(url, param); 
        function callback (rs) {
            console.log(rs);
            // rs = JSON.parse(rs);
            $accTbody.empty();
            $accInfo.empty();
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
                row.is_activated = row.activate === '1'
                row.activate = row.is_activated ? '<div class="my-font-color-green">' + LAN.activated + '</div>' : '<div class="my-font-color-red">' + LAN.non_activated + '</div>';
                row.btnString = row.is_activated ? LAN.reset_pwd : LAN.re_activate;
                row.btnClass = row.is_activated ? 'resetPwdBtn' : 'reActivateBtn';
                var temp = $($.fn.replaceElString(listRowTemp, row));
                bindResetReactivate(row, temp);
                $accTbody.append(temp);
            });
            let infoDatasets = {
                curStart: curStart,
                curEnd: parseInt(rs.curr_page) * param.curListLen > rs.rows ? rs.rows : parseInt(rs.curr_page) * param.curListLen,
                dataLen: rs.rows
            };
            info = $.fn.replaceElString(info, infoDatasets); 
            $accInfo.html(info);
            initPagination(rs, $('#pagination'));
        }
        $.post(url, formData, callback, 'json');
    }
    // **********************
    // initPagination
    // {Object}, acc or location list
    // {Object}, pagination ele
    // **********************
    function initPagination (rs, targetEle) {
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
                getAccList({curPage: page, curListLen: $('select[name=listLen]').val() ? $('select[name=listLen]').val() : 10, tableWrapper: '.tableWrapper'});
            }
        });
    }
});
