$(function () {
    if ($('#sys-msg').length > 0) {
        $("#sys-msg").click(function() {
            $("#sys-message2").hide();
        });
    }
    if ($('#organization_customer').length > 0) {
        const $pagination = $('#pagination');
        const $listLenMenu = $('#custTable_length');
        const lengthMenuOpt = '<select name="listLen" class="form-control input-sm"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select>';
        const $searchForm = $('form[name=searchCustomer]');
        const $keyNameInput = $searchForm.find('input[type=search]');
        let curListLen = 10;
        let curPage = 1;
        $listLenMenu.find('select[name=listLen]').val(curListLen);

        // Init show list len selector
        let lengthMenuLan = LAN.dataTable.hasOwnProperty('lengthMenu') ? LAN.dataTable.lengthMenu : LAN.dataTable.sLengthMenu;
        $listLenMenu.find('label').html(lengthMenuLan.replace('_MENU_', lengthMenuOpt));
        $listLenMenu.find('select[name=listLen]').change(function (e) {
            var val = $(this).val();
            curListLen = val;
            getCustList({ curPage: 1, curListLen: val, destoryPagination: true, key: $keyNameInput.val() });
        });
        getCustList({curPage: 1, curListLen: curListLen, key: $keyNameInput.val()});
        // Bind search form submit
        $searchForm.submit(function (e) {
            e.preventDefault();
            getCustList({curPage: 1, curListLen: curListLen,  destoryPagination: true, key: $keyNameInput.val()});
        });
    }
    if ($('#customerCreateForm').length > 0) {
        $('#name').focus();
        $("#saveButton").bind("click", function() {
            if ($('#org_id').length > 0) {
                if ($('#org_id').val() == 0) {
                    return swal({
                        title: "Parameter incorrect",
                        text: "please fill out the required field(s)",
                        type: "warning",
                    });
                }
            }
            if ($('#name').val().trim() == '') {
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
                    var org_id = ($('#org_id').length > 0) ? $('#org_id').val().trim() : '';
                    var params = {
                        org_id: org_id,
                        name: $('#name').val().trim(),
                        timezone: $('#tz').val(),
                        memo: $('#memo').val().trim()
                    };
                    $.post("/organization/customer/create_do", params, function(result) {
                        if (result.errno == 0) {
                            setTimeout(function(){
                                swal({
                                    title: "Good job!",
                                    text: "create is successful customer",
                                    type: "success",
                                },
                                    function (isConfirm) {
                                        location.href = "/organization/customer";
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

    if ($('#customerUpdateForm').length > 0) {
        const $targetForm = $('#customerUpdateForm');
        $targetForm.find('button[type=submit]').prop('disabled', false);
        $targetForm.submit(function (e) {
            e.preventDefault();
            let self = $(this);
            var isValid = self.parsley().validate();
            if (!isValid) {
                console.log('isValid:' + isValid);
                return false;
            }
            var params = {
                customer_id: $('#customer_id').val().trim(),
                name: $('#name').val().trim(),
                timezone: $('#tz').val(),
                memo: $('#memo').val().trim()
            };
            self.attr('data-stat', 'loading');
            $.post("/organization/customer/update_do", params, function(result) {
                self.attr('data-stat', '');
                if (result.errno === 0) {
                    swal({
                        title: LAN.done,
                        type: "success",
                        showConfirmButton: false,
                        timer: 3000
                    });
                    setTimeout( function() {
                        location.href = PAGE.custManage;
                    }, 3000);
                } else {
                    setTimeout(function(){ swal(result.errmsg, "", "error"); }, 100);
                }
            });
        });
    }
    // **********************
    // getCustList
    // {Object} param : curPage, curListLen
    // **********************
    const custListRowTemp = $('#custListRowTemp').html();
    function getCustList(param) {
        console.log('getCustList implement');
        console.log(param);
        let url = '/organization/customer/page/{curPage}';
        // let info = 'Showing {curStart} to {curEnd} of {dataLen} entries';
        let info = LAN.dataTable.info ? LAN.dataTable.info : LAN.dataTable.sInfo;
        info = info.replace('_TOTAL_', '{dataLen}').replace('_START_', '{curStart}').replace('_END_', '{curEnd}');
        let $custTbody = $('#custList tbody');
        let $custInfo = $('#custTable_info');
        let $targetList = $('.listWrapper');
        let formData = {
            per_page: param.curListLen,
            key: param.key
        };
        url = $.fn.replaceElString(url, param);
        function callback (rs) {
            console.log(rs);
            // rs = JSON.parse(rs);
            $custTbody.empty();
            $custInfo.empty();
            let isTableRefresh = $('.listWrapper')[0].hasAttribute('data-size');
            if (param.destoryPagination) {
                $('#pagination').twbsPagination('destroy');
            }
            if ( ! isTableRefresh ) {
                $('.listWrapper').attr('data-size', rs.data.length);
                if (rs.data.length < 1) {
                    return;
                }
            }
            if (rs.data.length < 1 && isTableRefresh) {
                let msg = LAN.dataTable.zeroRecords ? LAN.dataTable.zeroRecords : LAN.dataTable.sZeroRecords;
                let temp = ['<tr><td colspan="7">', msg, '</td></tr>'];
                $custTbody.append(temp.join(''));
            }
            let curStart = ((parseInt(rs.curr_page) - 1) * param.curListLen) + 1;
            $.each(rs.data, function (i, row) {
                var _row = {};
                _row.id = i + curStart;
                _row.name = row.name ? row.name : '';
                _row.updated_at = row.updated_at ? row.updated_at : '';
                _row.created_at = row.created_at ? row.created_at : '';
                _row.timezone = row.timezone ? row.timezone : '';
                _row.feature = row.feature ? row.feature : '';
                _row.customer_name = row.customer_name ? row.customer_name : '';
                _row.customer_id = row.customer_id ? row.customer_id : '';
                _row.total_devices = row.total_devices ? row.total_devices : 0;
                console.log(row);
                var temp = $($.fn.replaceElString(custListRowTemp, _row));
                $custTbody.append(temp);
            });
            let infoDatasets = {
                curStart: curStart,
                curEnd: parseInt(rs.curr_page) * param.curListLen > rs.rows ? rs.rows : parseInt(rs.curr_page) * param.curListLen,
                dataLen: rs.rows
            };
            info = $.fn.replaceElString(info, infoDatasets);
            $custInfo.html(info);
            initPagination(rs, $('#pagination'));
        }
        $.post(url, formData, callback, 'json');
    }
    // **********************
    // initPagination
    // **********************
    function initPagination (rs, targetEle) {
        if (rs.data.length < 1) {
            return;
        }
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
                getCustList({curPage: page, curListLen: $('select[name=custTable_length]').val() ? $('select[name=custTable_length]').val() : 10, key: $('input[type=search]').val()});
            }
        });
    }

    $("#deleteButton").bind("click", function() {
        swal({
            title: 'Do you confirm to delete the Customer',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            confirmButtonClass: 'btn btn-success',
            cancelButtonClass: 'btn btn-danger m-l-10',
            allowOutsideClick: false
        }, function (isConfirm) {
            if (isConfirm) {
                $.post("/organization/customer/remove", {customer_id: $('#customer_id').val()}, function(result) {
                    if (result.errno == 0) {
                        setTimeout(function(){
                            swal({
                                title: "Good job!",
                                text: "delete is successful Customer",
                                type: "success",
                            },
                            function (isConfirm) {
                                location.href = "/organization/customer";
                            });
                        }, 100);
                    } else {
                        setTimeout(function(){ swal(result.errmsg, "", "error"); }, 100);
                    }
                });
            }
        });
    });
});
