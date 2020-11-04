$(function () {
    const checkTokenUrl = '/service/activate/account';
    const checkResetPwdTokenUrl = '/service/password/link_verify/account';
    const $msg = $('#msg');
    // **admin/account/verify
    if ($('#admin_account_verify').length > 0) {
        $.get(checkTokenUrl + location.search, function (rs) {
            var toHome = false;
            if (!rs) {
                toHome = true;
            }
            switch (rs.status) {
                case 'data_error' :
                case 'expired' :
                    $msg.html(LAN.account_activate_invalid_expired);
                    toHome = true;
                    break;
                case 'already_activated' :
                    $msg.html(LAN.account_already_activated);
                    toHome = true;
                    break;
                case 'activate_done' :
                    $msg.html(LAN.account_activate_done);
                    toHome = true;
                    break;
            }
            if (rs.status === 'set_pwd') {
                let search = location.search;
                location.href = '/admin/account/resetpwd' + search;
                return;
            }
            if (toHome) {
                $('.tockenSection').attr('data-stat', '');
                setTimeout(function () {
                    location.href = '/'; 
                }, 5000);
                return;
            }
        }, 'json');
    }
    // **admin/account/resetpwd
    if ($('#admin_account_resetpwd').length > 0) {
        let $targetForm = $('form');
        $.get(checkResetPwdTokenUrl + location.search, function (rs) {
            console.log('check tocken');
            rr = rs;
            if (!rs) {
                return;
            }
            if (rs.status !== 'ok') {
                $('#msg').html(LAN.this_link_is_invalid);
                $targetForm.remove();
                $('.tockenSection').attr('data-stat', '');
                setTimeout(function () {
                    location.href='/';
                }, 3000);
                return;
            }
            $targetForm.show();
            $('.tockenSection').remove();
        }, 'json');
        $targetForm.submit(function (e) {
            $(this).attr('data-stat', 'loading');
            e.preventDefault();
            $(this).find('button[type=submit]').prop('disabled', true);
            $.post('/service/password/reset/account' + location.search, {passwd: $(this)[0].passwd.value}, function (rs) {
                $(this).attr('data-stat', '');
                if ( ! rs ) {
                    return;
                }
                if (rs.status === 'data_error') {
                    swal(LAN.error, '', 'warning');
                }
                if (rs.status === 'link_is_invalid' || rs.status === 'expired') {
                    swal(LAN.error, LAN.this_link_is_invalid, 'warning');
                }
                if (rs.status === 'ok') {
                    swal({
                        title: LAN.done,
                        type: "success",
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
                setTimeout( function() {
                    location.href = "/staff/logout";
                }, 3000);
            }, 'json');
        });
    }
});
