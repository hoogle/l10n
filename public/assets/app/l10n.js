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
        if (item === null) {
            return;
        }
        var reg = new RegExp("{" + i + "}", "g");
        //console.log(reg.exec(target));
        //console.log("replace param:" + reg + ", value:" + item);

        target = target.replace(reg, escapeHtml(item));
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
    const $signInForm = $('form[name=signIn]');
    const $searchForm = $('form[name=searchTranslate');
    const $chgPwdBtn = $('#chgPwdBtn');
    const $logoutBtn = $('#logoutBtn');
    const $translateList = $('#translateList');
    const $pagination = $('#pagination');
    const $copyURL = $('#copy-url');
    const $s3URL = $('#s3-url');
    const transListRowTemp = $('#transListRowTemp').html();
    const baseURL = '/index';
    const myURL = new URL(window.location.href);
    const platform = myURL.searchParams.get('p') ? myURL.searchParams.get('p') : $('#platform').html();
    let keyValue = '';
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
                console.log('page click');
                fetchTranslate(page);
            }
        });
    }

    //
    //**/index/page/1?platform=genesis_msp_php
    //**id, en, ja
    const updateLan = (formData, eleBtn) => {
        eleBtn.prop('disabled', true);
        let url = '/index/update';
        formData.map( item => (Object.assign(item, { value: item.value.replace(/'/g, "’") })))
        $.post(url, formData, function (rs) {
            if (rs.status === 'ok') {
                eleBtn.removeClass('btn-warning').prop('disabled', false);
                return;
            }
        }, 'json');
    }
    //
    const fetchTranslate = (curpage, curkey, doDestroyPagi) => {
        const page = curpage !== undefined ? curpage : 1; 
        const key = curkey !== undefined ? curkey : keyValue;
        const destroyPagi = doDestroyPagi !== undefined ? doDestroyPagi : false;
        let url = '/index/page/' + page;
        const formData = {
            p: platform,
            key: key,
            per_page: 25
        };
        $translateList.find('button[type=submit]').prop('disabled', true);
        $.get(url, formData, function (rs) {
            console.log(rs, 'fetchTranslate');
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
            $.each(rs.data, function (i, row) {
                const temp = $($.fn.replaceElString(transListRowTemp, row));
                const form = temp.find('form');
                const targetInput = temp.find('textarea');
                const targetCol = temp.find('.translateCol');
                const submitBtn = temp.find('button[type=submit]');
                let mobile = false;
                $translateList.append(temp);
                targetInput.on('focus touchstart', function (e) {
                    const name = $(this)[0].name;
                    console.log(e,':click event');
                    console.log(name + ':click');
                    if (e.type === 'touchstart') {
                        mobile = true;
                        targetCol.parent().find('textarea:not([readOnly])').blur();
                    }
                    if (name === 'id') {
                        return;
                    }
                    $(this).parent().css('flex', '0 0 50%');
                    if (name === 'keyword') {
                        copyToClipboard($(this)[0]);
                        return;
                    }
                    $(this).prop('readonly', false);
                });
                targetInput.on('change keyup paste', function (e) {
                    console.log(e, ':change');
                    console.log($(this).val());
                    console.log($(this).attr('title'));
                    submitBtn.addClass('btn-warning');
                });
                targetInput.on('focusout', function (e) {
                    console.log(e, ':focus out');
                    $(this).prop('readonly', true);
                    $(this).parent().css('flex', 'auto');
                });
                //
                form.submit(function (e) {
                    e.preventDefault();
                    updateLan($(this).serializeArray(), submitBtn);
                });
            });
            //init pagination
            if (destroyPagi) {
                initPagination(rs, $('#pagination'));
            }

        }, 'json');
    }
    $searchForm.submit(function (e) {
        e.preventDefault();
        fetchTranslate(1, $(this)[0].key.value, true);
    });
    //init
    if ($signInForm.length > 0) {
        $signInForm.find('button[type=submit]').prop('disabled', false);
    }

    $chgPwdBtn.click(function (e) {
        location.href = "/user/password";
        return;
    });

    $copyURL.click(function () {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($s3URL.text()).select();
        document.execCommand("copy");
        $temp.remove();
        alert("Copy S3 url to clipboard");
    });

    $logoutBtn.click(function (e) {
        e.preventDefault();
        $.get( baseURL + '/logout', null, function (rs) {
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
            console.log('ooo');
                location.href= baseURL + window.location.search;
                return;
            }
        }, 'json');
    });

    if( $translateList.length > 0) {
        fetchTranslate(1, keyValue, true);
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
                                    $.getJSON(baseURL + "/logout").done(function (rs) {
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
        $.post('/index/add', self.serializeArray(), function(rs) {
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
});
