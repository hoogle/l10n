//********************************
//$.fn.replaceElString
//The method searches a string for a specified value
//{object}, target element,  row
//{object}, target data,  row data
//********************************
$.fn.replaceElString = function (target, value) {
    //console.log("replaceElStrig is executed.");
    function escapeHtml(text) {
        var map = {
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#039;'
        };
        
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
      }
    $.each(value, function ( i , item) {
        const nullVal = !item;
        var reg = new RegExp("{" + i + "}", "g");
        // console.log(reg.exec(target));
        // console.log(target);
        // console.log("replace param:" + reg + ", value:" + item);

        target = nullVal ? target.replace(reg, '') : target.replace(reg, escapeHtml(item));
    });
        //console.log("replace finished target:" + target);
        return target;
};

function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    console.log($(element).text());
    document.execCommand("copy");
    $temp.remove();
}
  
$(function () {
    const $colGroup = $('#mainColGroup');
    const $signInForm = $('form[name=signIn]');
    const $searchForm = $('form[name=searchTranslate');
    const $addkeyForm = $('form[name=addKey');
    const $chgPwdBtn = $('#chgPwdBtn');
    const $logoutBtn = $('#logoutBtn');
    const $userBtn = $('#userBtn');
    const $translateList = $('#translateList');
    const $pagination = $('#pagination');
    const $copyURL = $('#copy-url');
    const $s3URL = $('#s3-url');
    const $newkeyBtn = $('#NewKeyBtn');
    const $downloadBtn = $('#downloadBtn');
    const $emailEditBtn = $('.emailEditBtn');
    const $emailPreviewBtn = $('.emailPreviewBtn');
    const transListRowTemp = $('#transListRowTemp').html();
    const baseURL = '/welcome';
    const myURL = new URL(window.location.href);
    const platform = myURL.searchParams.get('p') ? myURL.searchParams.get('p') : $('input[name=platform]').val();
    const $showKeyBtn = $('#showKeyBtn');
    const $hideColBtn = $('.hideColBtn');
    const $showColBtn = $('.showColBtn');
    const $usingLanForm = $('#usingLanForm');
    const $emailLanForm = $('form[name=emailLanForm]');
    const canModifyKeyAcc = {
        'goface_Android': ['ray@astra.cloud', 'hoogle@astra.cloud'],
        'goface_iOS': ['timmy@astra.cloud', 'hoogle@astra.cloud'],
        'goface_portal': ['mei@astra.cloud', 'hoogle@astra.cloud'],
        'goface_gf': ['max@astra.cloud', 'milo@astra.cloud'],
        'goface_email': ['hoogle@astra.cloud', 'milo@astra.cloud']
    };
    const canModifyKey = canModifyKeyAcc[platform] !== undefined && canModifyKeyAcc[platform].indexOf($('input[name=email]').val()) > -1;
    const $topbar = $('.topbar');
    let curOrder = null;
    let rowUrl = null;
    let keyValue = '';
    const autoXHR = {};
    let showLanCount = 0;
    const hideCol = {
        'en-US': null,
        'ja-JP': null,
        'zh-TW': null,
        'id-ID': null,
        'ms-MY': null,
        'ui-key':null 
    };
    const lanPropsMapping = {
        'enus': 'en-US',
        'jajp': 'ja-JP',
        'idid': 'id-ID',
        'msmy': 'ms-MY',
        'zhtw': 'zh-TW',
        'uikey': 'ui-key'
    };
    const pageUrl = {
        'relation': '/relation/page/'
    };
    const curOrdersets = {
        'relation': 'ui_key'
    };

    const rowUrlsets = {
        // 'relation': [location.origin, '/relation/?production=', $('input[name=production]').val(), '&ui_key='].join('')
        'relation': '' 
    }

    const platformIconsets = {
        'iOS' : 'apple',
        'Android' : 'android'
    };

    // **********************
    // init default param
    // **********************
    Object.keys(curOrdersets).map(function (c) {
        curOrder = location.pathname.indexOf(c) > -1 ? curOrdersets[c] : 'updated_at';
    });
    Object.keys(rowUrlsets).map(function (c) {
        rowUrl = location.pathname.indexOf(c) > -1 ? rowUrlsets[c] : [location.origin,'/trans?p=',platform,'&id='].join('');
    });

    // **********************
    // initPagination
    // **********************
    function initPagination (rs, targetEle) {
        if (rs.data.length < 1) {
            return;
        }
        var $pagination = targetEle;
        $pagination.twbsPagination({
            // first: '',
            // last: '',
            // prev: '',
            // next: '',
            totalPages: rs.pages,
            visiblePages: 6,
            initiateStartPageClick: false,
            onPageClick: function (event, page) {
                // console.log('page click');
                fetchTranslate(page, keyValue);
            }
        });
    }

    $hideColBtn.click(function (e) {
        if ($('.widescreen').hasClass('fixed-left')) {
            $('.button-menu-mobile').trigger('click');
        }
        e.stopPropagation();
        const hideID = $(this).parent()[0].id.replace('col_', '');
        hideCol[hideID] = true;
        $(this).parent().addClass('flex-small');
        $('.edit_' + hideID).addClass('flex-small');
        showLanCount--;
        $colGroup.attr('data-showCol', showLanCount);
    });

    $showColBtn.click(function (e) {
        e.stopPropagation();
        const showID = $(this).parent()[0].id.replace('col_', '');
        hideCol[showID] = false;
        $(this).parent().removeClass('flex-small');
        $('.edit_' + showID).removeClass('flex-small');
        showLanCount++;
        $colGroup.attr('data-showCol', showLanCount);
    });

    $('#mainColGroup div').click(function (e) {
        // e.stopPropagation();
        if ($(this).hasClass('flex-small')) {
            return;
        }
        const order = e.currentTarget.id.replace('col_', '');
        curOrder = order;
        // $searchForm.find('input').val('');
        // keyValue = '';
        fetchTranslate(1, keyValue, true);
    });
    //
    const autoUpdateLan = (formData, orRow ) => {
        const url = '/trans/update';
        $topbar.addClass('actionBar');
        $.post(url, formData, function (rs) {
            $topbar.removeClass('actionBar');
            if (rs.status === 'ok') {
                Object.assign(orRow, formData);
                // console.log(orRow, ':orRow');
                autoXHR[formData.id] = null;
                // eleBtn.removeClass('btn-warning').prop('disabled', false);
                return;
            }
        }, 'json');
        
    }
    //**/index/page/1?platform=genesis_msp_php
    //**id, en, ja
    const updateLan = (formData, eleBtn, orRow) => {
        eleBtn.prop('disabled', true);
        // console.log(orRow, ':orRow');
        let url = '/trans/update';
        const myformData = {};
        let needToUpdate = false;
        formData.map( item => {
            // console.log(item.name, ':row modify name');
            // console.log(orRow, ':row origin name');
            if (item.name !== 'id' && item.name !== 'platform' && orRow[item.name] === item.value ) {
                return;
            }
            needToUpdate = item.name !== 'id' && item.name !== 'platform';
            myformData[item.name] = item.value.replace(/'/g, "???");
            //return (Object.assign(item, { value: item.value.replace(/'/g, "???") }))
        })

        if (! needToUpdate ) {
            eleBtn.removeClass('btn-warning').prop('disabled', false);
            return;
        }
        
        $.post(url, myformData, function (rs) {
            if (rs.status === 'ok') {
                Object.assign(orRow, myformData);
                eleBtn.removeClass('btn-warning').prop('disabled', false);
                return;
            }
        }, 'json');
    }
    //
    const fetchTranslate = (curpage, curkey, doDestroyPagi, id) => {
        const page = curpage !== undefined ? curpage : 1; 
        const key = curkey !== undefined ? curkey : keyValue;
        const destroyPagi = doDestroyPagi !== undefined ? doDestroyPagi : false;
        const by = ( curOrder !== 'updated_at' && ! $('#col_'+ curOrder).data('by') ) || ($('#col_'+ curOrder).data('by') && $('#col_'+ curOrder).data('by') === 'DESC') ? 'ASC' : 'DESC';
        let url = '';
        Object.keys(pageUrl).map(function(p) {
            url = location.pathname.indexOf(p) > -1 ? pageUrl[p] + page : '/trans/page/' + page;
        });
        let formData = {};
        if (id) {
            formData = {
                order: curOrder,
                p: platform,
                id: id,
                per_page: 20,
                by: by 
            };
        } else {
            formData = {
                order: curOrder,
                p: platform,
                key: key,
                per_page: 20,
                by: by 
            };
        }
        $translateList.find('button[type=submit]').prop('disabled', true);
        $.get(url, formData, function (rs) {
            // console.log(rs, 'fetchTranslate');
            $('#col_'+ curOrder).data('by',by);
            if (rs.data.length < 1) {
                $translateList.find('button[type=submit]').prop('disabled', false);
                alert('Key not found');
                $searchForm[0].key.value= keyValue;
                // location.reload();
                return;
            }
            keyValue = key; // Sign key until has key data
            if (destroyPagi && $pagination.twbsPagination) {
                $pagination.twbsPagination('destroy');
            }
            $translateList.empty();
            $('#mainColGroup').attr('data-sort', curOrder);
            function copyUrl(value) {
                var tempInput = document.createElement("input");
                tempInput.style = "position: absolute; left: -1000px; top: -1000px";
                tempInput.value = value;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand("copy");
                alert("Copied the text: " + tempInput.value);
                document.body.removeChild(tempInput);
            }
            let uk = '';
            $.each(rs.data, function (i, row) {
                row.url = rowUrl ? rowUrl + row.id : rowUrl;
                row.platformIcon = platformIconsets[row.platform];
                row.uiKeyGroup = 'none';
                if (curOrder === 'ui_key') {
                    row.uiKeyGroup = row.ui_key !== uk ? 'group': '';
                }
                uk = row.ui_key;
                // console.log(row, ':row');
                const temp = $($.fn.replaceElString(transListRowTemp, row));
                const form = temp.find('form');
                const targetInput = temp.find('textarea');
                const targetCol = temp.find('.translateCol');
                const submitBtn = temp.find('button[type=submit]');
                const idCol = temp.find('.idCol');
                const urlCol = temp.find('.urlCol');
                if (row.dot && row.dot === '1') {
                    idCol.append('<span style="position: relative; top: -9px; right: -2px; display: inline-block; width: 5px; height: 5px; border-radius: 100%; background-color: #ff5757;"></span>');
                }
                let mobile = false;
                // console.log(temp[0]);
                    Object.keys(hideCol).map((lan) => {
                        if (i==0 && hideCol[lan] === null) {
                            hideCol[lan] = !temp.find('.edit_' + lan)[0];
                            $colGroup.attr('data-showCol', showLanCount);
                            if (!hideCol[lan]) {
                                showLanCount++;
                            }
                        }
                        if (!hideCol[lan]) {
                            temp.find('.edit_' + lan).removeClass('flex-small');
                        } else {
                            temp.find('.edit_' + lan).addClass('flex-small');
                        }
                    })
                $translateList.append(temp);
                targetInput.on('focus touchstart', function (e) {
                    const name = $(this)[0].name;
                    console.log(e,':click event');
                    console.log(name + ':click');
                    if (e.type === 'touchstart') {
                        mobile = true;
                        const prevEl=$translateList.find('.mobileEditStatus');
                        prevEl.css('flex', '1').removeClass('mobileEditStatus');
                        prevEl.find('textarea').prop('readonly', true);
                    }
                    if (name === 'id') {
                        return;
                    }
                    $(this).parent().css('flex', '1 1 auto');
                    if (mobile) {
                        $(this).parent().addClass('mobileEditStatus');
                    }
                    if (name === 'keyword') {

                        console.log(canModifyKey, ':canModifyKey');
                        if (! canModifyKey) {
                            copyToClipboard($(this)[0]);
                            return;
                        }
                    }
                    $(this).prop('readonly', false);
                    $('#mainColGroup').attr('data-edit', name);
                });

                targetInput.on('change keyup paste', function (e) {
                    // console.log(e, ':change');
                    // console.log(row, ':row change');
                    // console.log($(this).val());
                    // console.log($(this).attr('title'));
                    submitBtn.addClass('btn-warning');
                    const val = $(this).val();
                    const name = e.currentTarget.name;
                    /*
                    console.log(name, ':row name');
                    console.log(row, ':row');
                    */
                    if (row[name] === undefined && row[lanPropsMapping[name]] === val) {
                        console.log('The value is the same. Do not update.')
                        return;
                    }

                    if ( row[name] === val ) {
                        console.log('The value is the same. Do not update.')
                        return;
                    }
                    if (autoXHR[row.id]) { clearTimeout(autoXHR[row.id]) }
                    autoXHR[row.id] = setTimeout(function () {
                       autoUpdateLan( {id: row.id, [name]: val === '' ? ' ' : val, platform: row.platform, production: row.production }, row ) ;
                    }, 500);
                });
                
                targetInput.on('focus', function (e) {
                    if ($('.widescreen').hasClass('fixed-left')) {
                        $('.button-menu-mobile').trigger('click');
                    }
                });
                targetInput.on('focusout', function (e) {
                    // console.log(e, ':focus out');
                    $(this).prop('readonly', true);
                    $(this).parent().css('flex', '1');
                    $(this).parent().removeClass('mobileEditStatus');
                    $('#mainColGroup').attr('data-edit', '');
                });
                //
                form.submit(function (e) {
                    e.preventDefault();
                    Object.keys(row).map(function(key, index) {
                        const shortcutKey = key.replace(/-/g, '').toLowerCase();
                        if (row.hasOwnProperty([shortcutKey])) {
                            return;
                        }
                        row[shortcutKey] = row[key];
                    });
                    updateLan($(this).serializeArray(), submitBtn, row);
                });
                //
                urlCol.on("click", function (e) {
                   copyUrl($(this).find('input').val());
                })
            });
            //init pagination
            if (destroyPagi) {
                initPagination(rs, $('#pagination'));
            }

        }, 'json');
    }
    $searchForm.submit(function (e) {
        e.preventDefault();
        keyValue = $(this)[0].key.value;
        fetchTranslate(1, keyValue, true);
    });
    //init
    if ($signInForm.length > 0) {
        $signInForm.find('button[type=submit]').prop('disabled', false);
    }

    $chgPwdBtn.click(function (e) {
        location.href = "/user/password";
        return;
    });

    $newkeyBtn.click(function (e) {
        $.get('/trans/get_last_id', function(data) {
            $addkeyForm[0].keyword.value= data;
        });
    });

    $downloadBtn.click(function (e) {
        var url = new URLSearchParams(window.location.search);
        var link = document.createElement("a");
        link.href = '/tool/download/' + url.get('p');
        link.click();
    });

    $emailEditBtn.click(function (e) {
        var url = new URLSearchParams(window.location.search);
        var link = document.createElement("a");
        link.href = '/email?p=' + url.get('p') + '&item=' + $(this).data("id");
        link.click();
    });

    $emailPreviewBtn.click(function (e) {
        var url = new URLSearchParams(window.location.search);
        var link = document.createElement("a");
        link.href = '/email/preview?p=' + url.get('p') + '&item=' + $(this).data("id");
        link.click();
    });

    $copyURL.click(function () {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($s3URL.text()).select();
        document.execCommand("copy");
        $temp.remove();
        alert("Copy S3 url to clipboard");
    });

    $userBtn.click(function (e) {
        var link = document.createElement("a");
        link.href = '/user';
        link.click();
    });

    $logoutBtn.click(function (e) {
        e.preventDefault();
        $.get('/logout', null, function (rs) {
            if (rs.status === 'ok') {
                location.href= baseURL;
                return;
            }
        }, 'json');
    });


    $signInForm.submit(function (e) {
        e.preventDefault();
        const formData = {
            email: $(this)[0].email.value,
            passwd: $(this)[0].passwd.value
        };
        $.post('/index/login', formData, function(rs) {
            if (rs.status === 'ok') {
                location.href= baseURL + window.location.search;
                return;
            }
        }, 'json');
    });

    if( $translateList.length > 0) {
        if (location.search.slice(1).split("&")[1] && location.search.slice(1).split("&")[1].split("id=")[1]) {
            const id = location.search.slice(1).split("&")[1].split("id=")[1];
            fetchTranslate(1, '', true, id);
        } else {
            fetchTranslate(1, keyValue, true);
        }
    }

    if ($('#passwdUpdateForm').length > 0) {
        $("#saveButton").bind("click", function(e) {
            e.preventDefault();
            if ($('#passwd').val() != '' && ($('#passwd').val() != $('#re-passwd').val())) {
                return swal({
                    title: "Password incorrect",
                    text: "confirm password not match",
                    type: "warning",
                });
            }
            swal({
                title: 'Do you confirm to submit form data.',
                showCancelButton: true,
                confirmButtonText: 'Update',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger m-l-10',
                allowOutsideClick: false
            }, function (isConfirm) {
                if (isConfirm) {
                    var params = {
                        email: $('#email').val(),
                        passwd: $('#passwd').val()
                    };
                    $.post("/user/update_pwd", params, function(result) {
                        if (result.errno == 0) {
                            setTimeout(function(){
                                swal({
                                    title: "Good job!",
                                    text: "User's password has been updated.",
                                    type: "success",
                                },
                                function (isConfirm) {
                                    $.getJSON("/logout").done(function (rs) {
                                        if (rs.status === 'ok') {
                                            location.href = baseURL;
                                        }
                                    });
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

    $('form[name=addKey]').submit(function (e) {
        e.preventDefault();
        const self = $(this);
        self.find('button[type=submit]').prop('disabled', true);
        $.post('/trans/add', self.serializeArray(), function(rs) {
            $('#myAdd').modal('hide');
            self[0].reset();
            self.find('button[type=submit]').prop('disabled', false);
            if (rs.status === 'ok') {
                fetchTranslate(1, '', true);
                swal({
                    title: "Done",
                    type: "success",
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            } else {
                swal({
                    title: "Error",
                    type: "error",
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
        }, 'json');
    });

    $('form[name=exportIt]').submit(function (e) {
        e.preventDefault();
        const self = $(this);
        self.find('button[type=submit]').prop('disabled', true);
        const exportPara = {
            p: platform
        }
        $.get('/tool/export', exportPara, function(rs) {
            $('#myExport').modal('hide');
            self[0].reset();
            self.find('button[type=submit]').prop('disabled', false);
            if (rs.status === 'ok') {
                fetchTranslate(1, '', true);
                swal({
                    title: "Done",
                    type: "success",
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            } else {
                swal({
                    title: "Error",
                    type: "error",
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
        }, 'json');
    });

    $showKeyBtn.click( function (e) {
        const $targetTable = $('#mainTable');
        const val = $targetTable.attr('data-key') === '1' ? '0' : '1';
        $('#mainTable').attr('data-key', val);
    });

    $usingLanForm.change( function (e) {
        e.preventDefault();
        const url = e.currentTarget.action;
        $.post(url, $(this).serializeArray(), function (rs) {
            if (rs.status === 'ok') {
                return;
            } else {
                alert(rs.message);
                location.reload();
            }
        }, 'json')
    });

    async function autoUpdateAction (formTarget, formData) {
        console.log(formTarget.id.value, ':formTarget id');
        if (!formTarget.id.value) {
            alert('Need id to identify XHR');
            return false;
        }
        const xhrID = formTarget.id.value;
        if (autoXHR[xhrID]) {
            clearTimeout(autoXHR[xhrID]);
        }
        autoXHR[xhrID] = setTimeout(function () {
                const url = formTarget.action;
                $topbar.addClass('actionBar');
                async function doAjax() {
                    try {
                        let rs = await $.post(url, formData, function (rs) {
                            const url = formTarget.action;
                            $topbar.removeClass('actionBar');
                            if (rs.status === 'ok') {
                                // console.log(orRow, ':orRow');
                                autoXHR[xhrID] = null;
                                // eleBtn.removeClass('btn-warning').prop('disabled', false);
                                return formData;
                            }
                            return false
                        }, 'json');
                        return rs;
                    } catch (error) {
                        console.error(error);
                    }
                }
                return doAjax();
            }, 500)
    }
                $emailLanForm.find('textarea').on('change keyup paste', function (e) {
                    // console.log(row, ':row change');
                    // console.log($(this).val());
                    // console.log($(this).attr('title'));
                    const val = $(this).val();
                    const name = e.currentTarget.name;
                    const targetForm = $(this).parent()[0];
                    const orVal = targetForm.orVal.value;
                    console.log(val, ':val');
                    console.log(orVal, ':orVal');
                    if (orVal === val) {
                        console.log('Value not changed');
                        return;
                    }
                    function handler () {
                        console.log(val, ':handle autoUpdateAction success');
                        targetForm.orVal.value = val;
                    }
                    autoUpdateAction($(this).parent()[0], $(this).parent().serializeArray()).then( (data) => { handler(); console.log(data, "autoUpdateAction Done");} )
                });







                   

                /*
    $emailLanForm.change(function (e) {
        e.preventDefault();
        const url = e.currentTarget.action;
        $topbar.addClass('actionBar');
        $.post(url, $(this).serializeArray(), function (rs) {
            $topbar.removeClass('actionBar');
            if (rs.status === 'ok') {
                return;
            } else {
                alert(rs.message);
            }
        }, 'json');
        console.log(e.target.value, ':email lan form')
    });
    */
});
