if ($('#sys-msg').length > 0) {
    $("#sys-msg").click(function() {
        $("#sys-message2").hide();
    });
}

if ($('#staffCreateForm').length > 0){
    $('#email').focus();
    $("#saveButton").bind("click", function() {
        if ($('#email').val() == '' || $('#passwd').val() == '' || $('#name').val() == '' || $("#org_id").val() == '' || $("#locale").val() == '') {
            return swal({
                title: "Parameter incorrect",
                text: "please fill out the required field(s)",
                type: "warning",
            });
        }
        if ($('#passwd').val() != $('#re-passwd').val()) {
            return swal({
                title: "Password incorrect",
                text: "confirm password not match",
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
                    email: $('#email').val(),
                    passwd: $('#passwd').val(),
                    name: $('#name').val(),
                    org_id: $('#org_id').val(),
                    locale: $('#locale').val()
                };
                $.post("/root/staff/create_do", params, function(result) {
                    if (result.errno == 0) {
                        setTimeout(function(){
                            swal({
                                title: "Good job!",
                                text: "create is successful admin staff",
                                type: "success",
                            },
                            function (isConfirm) {
                                location.href = "/root/staff/";
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

if ($('#staffUpdateForm').length > 0){
    $("#saveButton").bind("click", function() {
        if ($("#name").val() == '' || $("#locale").val() == '') {
            return swal({
                title: "Parameter incorrect",
                text: "please fill out the required field(s)",
                type: "warning",
            });
        }
        if ($('#passwd').val() != '' && ($('#passwd').val() != $('#re-passwd').val())) {
            return swal({
                title: "Password incorrect",
                text: "confirm password not match",
                type: "warning",
            });
        }
        swal({
            title: 'Do you confirm to submit form data',
            showCancelButton: true,
            confirmButtonText: 'Update',
            confirmButtonClass: 'btn btn-success',
            cancelButtonClass: 'btn btn-danger m-l-10',
            allowOutsideClick: false
        }, function (isConfirm) {
            if (isConfirm) {
                var params = {
                    staff_id: $('#staff_id').val(),
                    passwd: $('#passwd').val(),
                    name: $("#name").val(),
                    locale: $('#locale').val()
                };
                $.post("/root/staff/update_do", params, function(result) {
                    if (result.errno == 0) {
                        setTimeout(function(){
                            swal("Good job!", "update is successful admin staff", "success");
                        }, 100);
                    } else {
                        setTimeout(function(){ swal(result.errmsg, "", "error"); }, 100);
                    }
                });
            }
        });
    });

    if ($("#resentButton").length > 0) {
        $("#resentButton").bind("click", function() {
            swal({
                title: 'Do you confirm to resend the email',
                showCancelButton: true,
                confirmButtonText: 'Send',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger m-l-10',
                allowOutsideClick: false
            }, function (isConfirm) {
                if (isConfirm) {
                    $.post("/root/staff/send_activate_email", {staff_id: $('#staff_id').val()}, function(result) {
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

    if ($("#deleteButton").length > 0) {
        $("#deleteButton").bind("click", function() {
            swal({
                title: 'Do you confirm to delete the staff',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger m-l-10',
                allowOutsideClick: false
            }, function (isConfirm) {
                if (isConfirm) {
                    $.post("/root/staff/remove", {staff_id: $('#staff_id').val()}, function(result) {
                        if (result.errno == 0) {
                            setTimeout(function(){
                                swal({
                                    title: "Good job!",
                                    text: "delete is successful Staff",
                                    type: "success",
                                },
                                function (isConfirm) {
                                    location.href = "/root/staff/";
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
}

if ($('#passwdUpdateForm').length > 0) {
    const $targetForm = $('#passwdUpdateForm');
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
            staff_id: $('#staff_id').val(),
            passwd: $('#passwd').val()
        };
        $.post("/staff/passwd/update", params, function(result) {
            if (result.errno == 0) {
                swal({
                    title: LAN.done,
                    type: "success",
                    showConfirmButton: false,
                    timer: 3000
                });
                setTimeout( function() {
                    location.href = "/staff/logout";
                }, 3000);
            } else {
                setTimeout(function(){ swal(result.errmsg, "", "error"); }, 100);
            }
        });
    });
}
