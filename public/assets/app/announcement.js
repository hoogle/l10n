$(function () {
    //lang
    let lan = {
        cancel: LAN.cancel,
        sorry: LAN.sorry,
        ann_time_limit: LAN.ann_time_limit,
        are_u_sure : LAN.are_u_sure,
        del_success_ann: LAN.del_success_ann,
        not_recovered : LAN.can_not_recovered,
        del_alert : LAN.delete,
        yes_del_it : LAN.yes_del_it
    };
    const $form = $("form[name=annForm]");
    const $publishTime = $("#publishTime");
    const $expiredTime = $("#expiredTime");
    const $listLenMenu = $('#annTable_length');
    const lengthMenuOpt = '<select name="listLen" class="form-control input-sm"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select>';

    const $pagination = $('#pagination');
    const $dateRange = $("#date-range");
    const $searchForm = $('form[name=searchForm]');
    const isHistory = $searchForm.length > 0;
    let annListRowTemp = $('#annListRowTemp').html();
    //common
    let parsleyInstance = null;
    let statDatasets = {
        'draft' : '0',
        'publish' : '1'
    };
    let statLAN = {
        '0': LAN.draft,
        '1': LAN.to_be_published
    };
    // **init
    // Init show list len selector
    var lengthMenuLan = LAN.dataTable.hasOwnProperty('lengthMenu') ? LAN.dataTable.lengthMenu : LAN.dataTable.sLengthMenu;
    $listLenMenu.find('label').html(lengthMenuLan.replace('_MENU_', lengthMenuOpt));
    const $listLenSt = $('select[name=listLen]');
    if ($form.length > 0) {
        parsleyInstance = $form.parsley();
    }
    if ($('#admin_announcement').length > 0) {
        getAnnList({curPage: 1, curListLen: 10});
    }
    if ($dateRange.length > 0) {
        //**datepicker init
        $dateRange.datepicker(
            {
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                startDate: '-6m', 
                endDate: '0d'
            }
        );
    }
    //**bind submit ent on search history
    $searchForm.submit(function (e) {
        console.log('search submit');
        e.preventDefault();
        getAnnList({history: 1, curPage: 1, curListLen: $listLenSt.val() ? $listLenSt.val() : 10, startDate: $(this)[0].start_date.value, endDate: $(this)[0].end_date.value, destoryPagination: true});
    });
    //**bind change on select of ann list length
    $listLenSt.change(function (e) {
        console.log('per_page changed');
        var val = $(this).val();
        if (isHistory) {
            getAnnList({history: 1, curPage: 1, curListLen: parseInt(val), startDate: $searchForm[0].start_date.value, endDate: $searchForm[0].end_date.value, destoryPagination: true});
        } else {
            getAnnList({curPage: 1, curListLen: parseInt(val), destoryPagination: true});
        }
    });
    //**draft button bind click evt
    $('.draftBtn').click(function (e) {
        e.preventDefault();
        $(this).prop('disabled', true);
        $form.find("input[name=title]").prop("required");
        if (parsleyInstance) {
            parsleyInstance.destroy();
            parsleyInstance.reset();
        }
        parsleyInstance = $form.parsley();
        parsleyInstance.validate();
        if (parsleyInstance.isValid()) {
            $form[0].status.value=statDatasets.draft;
            updateAnn($form.serializeArray());
        }
    });
    //**delet button bind click evt
    // TODO
    $('.delBtn').click(function (e) {
        swal({
            title: lan.are_u_sure, 
            text: lan.not_recovered,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-warning",
            cancelButtonText: lan.cancel,
            confirmButtonText: lan.yes_del_it,
            closeOnConfirm: false
        },
            function(){
                var ann_id = $form.find('input[name=ann_id]').val();
                $.ajax(
                    {
                        url: PAGE.annDelete.replace('{ann_id}', ann_id),
                        method: 'GET',
                        dataType: 'json',
                        statusCode: {
                            400: function (rs) {
                                swal(LAN.error, '', 'warning');
                            }
                        }
                    }).done(function (rs) {
                        if (rs.status === "OK") {
                            swal({
                                title: LAN.done,
                                type: "success",
                                showConfirmButton: false,
                                timer: 3000
                            });
                            setTimeout( function() {
                                location.href = PAGE.annMainList;
                            }, 3000);
                            return;
                        }
                        swal({
                            title: lan.sorry,
                            text: rs.message,
                            type: 'warning',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    });
            }
        );
    });
    //**customerdata table init
    $form.submit(function (e) {
        e.preventDefault();
        e.stopPropagation();
        var form = this;
        var $subBtn = $(this).find('button[type=submit]');
        $subBtn.prop('disabled', true);
        if (parsleyInstance) {
            parsleyInstance.destroy();
            parsleyInstance.reset();
        }
        parsleyInstance = $(this).parsley();
        $(this).find("input:visible:not([type=search], [id=checkbox-all], [name=must_read]), select:visible, textarea:visible").prop("required", true);
        parsleyInstance.validate();
        if (parsleyInstance.isValid()) {
            $(this)[0].status.value = statDatasets.publish;
            updateAnn($(this).serializeArray());
            return;
        }
        $subBtn.prop('disabled', false);
        console.log($(this).serializeArray());
    });
    //**package date picker
    $('#publishDate, #expiredDate').datepicker(
        {
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            startDate: '0d'
        }
    );
    //**time picker
    // var minTime = moment().add(10, 'minute');
    // minTime = minTime.format('HH:mm');
    $('#publishTime, #expiredTime').timepicker({
        // defaultTime: minTime.substring(0, minTime.length-1) + '0', --> not use
        timeFormat: 'HH:mm',
        showMeridian:false,
        snapToStep: true,
        minuteStep: 10
    }).on('hide.timepicker', function(e) {
        var HH = e.time.hours;
        var mm = e.time.minutes;
        if(HH < 10) {
            HH = '0' + HH;
        }
        if(mm < 10) {
            mm = '0' + mm;
        }
        $(this).val(HH + ":" + mm);
    });

    //** enable form
    $form.find('button').prop('disabled', false);
    //******************************
    //updateAnn
    //{Object} form data
    //******************************
    function updateAnn (data) {
        var formData = {} ;
        var $subBtn = $form.find('button[type=submit]');
        console.log(data);
        $.each(data, function (i, _data) {
            formData[_data.name] = _data.value;
        });
        $.each([$publishTime, $expiredTime], function (i, targetEle) {
            var targetPicker = targetEle.data('timepicker');
            var HH = targetPicker.hour;
            var mm = targetPicker.minute;
            var type = targetEle.attr('id').replace('Time', '');
            if (HH < 10) {
                HH = "0" + targetPicker.hour;
            }
            if (mm < 10) {
                mm = "0" + targetPicker.minute;
            }
            formData[type] = [formData[type + 'Date'], HH + ":" + mm].join(' ');
        });
        if(formData.status !== statDatasets.draft) {
            var pubTime = formData.publish.replace(/-/g, '').replace(':', '').replace(' ', '');
            var exTime = formData.expired.replace(/-/g, '').replace(':', '').replace(' ', '');
            var now  = moment().format('YYYYMMDDHHmm');
            console.log(pubTime);
            console.log(now);
            if (pubTime < now || exTime < now) {
                swal({
                    title: lan.sorry,
                    text: lan.ann_time_limit,
                    type: 'warning',
                    closeOnConfirm: true 
                },
                    function(isConfirm) {
                        setTimeout(function () {
                            window.scrollTo( 0, 300 );
                            $form.find('input[name=time]').triggerHandler( "focus" );
                        }, 500);
                    }
                );
                $subBtn.prop('disabled', false);
                return;
            }
        }
        console.log(formData);
        var url = formData.ann_id ? PAGE.annUpdate: PAGE.annCreate;
        delete formData.publishDate;
        delete formData.publishTime;
        delete formData.expiredDate;
        delete formData.expiredTime;
        console.log(formData);
        $.ajax(
            {
                url: url,
                method: 'POST',
                dataType: 'json',
                data: formData
            }
        ).done(function (rs) {
            // status = 200
            console.log(rs);
            $subBtn.prop('disabled', false);
            $('.draftBtn').prop('disabled', false);
            //success
            if (!rs.errmsg) {
                location.href=PAGE.annMainList;
                return;
            }
            //fail
            if (rs.errmsg) {
                $form[0].reset();
                if (parsleyInstance) {
                    parsleyInstance.destroy();
                }
                //TODO
                swal({
                    title: LAN.error,
                    type: 'warning',
                    showConfirmButton: false,
                    timer: 3000
                });
                $subBtn.prop('disabled', false);
                $('.draftBtn').prop('disabled', false);
            }
        });
    }
    // **********************
    // getAnnList
    // {Object} param : curPage, curListLen
    // **********************
    function getAnnList(param) {
        console.log('getAnnList implement');
        let url = !param.history ? '/admin/announcement/page/{curPage}?per_page={curListLen}' : '/admin/announcement/page/{curPage}?per_page={curListLen}&start_date={startDate}&end_date={endDate}&history=1';
        let info = LAN.dataTable.info ? LAN.dataTable.info : LAN.dataTable.sInfo;
        info = info.replace('_TOTAL_', '{dataLen}').replace('_START_', '{curStart}').replace('_END_', '{curEnd}');

        const $annTbody = $('#annTable tbody');
        const $annInfo = $('#annTable_info');
        url = $.fn.replaceElString(url, param); 
        function callback (rs) {
            console.log(rs);
            rs = JSON.parse(rs);
            $annTbody.empty();
            $annInfo.empty();
            if (param.destoryPagination) {
                $('#pagination').twbsPagination('destroy');
            }
            $('.listWrapper').attr('data-size', rs.data.length);
            if (rs.data.length < 1) {
                return;
            }
            $.each(rs.data, function (i, row) {
                row.status = statLAN[row.status];
                row.publish_at = row.publish_at.substring(0, 16); 
                var temp = $($.fn.replaceElString(annListRowTemp, row));
                $annTbody.append(temp);
            });
            let infoDatasets = {
                curStart: ((parseInt(rs.curr_page) - 1) * param.curListLen) + 1,
                curEnd: parseInt(rs.curr_page) * param.curListLen > rs.rows ? rs.rows : parseInt(rs.curr_page) * param.curListLen,
                dataLen: rs.rows
            };
            info = $.fn.replaceElString(info, infoDatasets); 
            $annInfo.html(info);
            initPagination(rs);
        }
        $.get(url, callback);
    }
    // **********************
    // initPagination
    // **********************
    function initPagination (rs) {
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
                if (isHistory) {
                    getAnnList({history: 1, curPage: page, curListLen: $listLenSt.val() ? $listLenSt.val() : 10, startDate: $searchForm[0].start_date.value, endDate: $searchForm[0].end_date.value});
                } else {
                    getAnnList({curPage: page, curListLen: $listLenSt.val() ? $listLenSt.val() : 10});
                }
            }
        });
    }

});
