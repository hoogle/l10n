if ($('#gen-psk').length > 0){
    $("#gen-psk").bind("click", function() {
        if ($('#ssid').val().length > 0) {
            $('#full_ssid').val($('#ssid_prefix').val() + $('#ssid').val());
            $.post("/system/psk/gen_psk", {full_ssid: $('#full_ssid').val()}, function(result) {
                if (result.errno == 0) {
                    $('#input-psk').val(result.passwd);
                } else {
                    setTimeout(function(){ swal(result.errmsg, "", "error"); }, 100);
                }
            });
        }
    });
}

if ($('#copy-psk-btn').length > 0) {
    $('#copy-psk-btn').click(function () {
        swal({
            title: "Copy PSK",
            text: "PSK has copied to clipboard!",
            timer: 2000,
            showConfirmButton: false,
            type: "success"
        }, "success");
    });

    new Clipboard($('#copy-psk-btn')[0] , {
        text: function(argument) {
            return $('#input-psk').val();
        }
    });
}
