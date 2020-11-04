if ($('#sys-msg').length > 0) {
    $("#sys-msg").click(function() {
        $("#sys-message2").hide();
    });
}

if ($('#orgCreateForm').length > 0){
    $('#account').focus();
    $("#saveButton").bind("click", function() {
        if ($('#account').val().trim() == '' || $('#name').val().trim() == '' || $('#memo').val().trim() == '') {
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
                var features = []
                $("input[name='feature[]']:checked").each(function (){
                    features.push($(this).val());
                });
                var params = {
                    account: $('#account').val().trim(),
                    name: $('#name').val().trim(),
                    customer_support: $('input[name=customer_support]:checked').val(),
                    memo: $('#memo').val().trim(),
                    model: $('#model').val().trim(),
                    feature: features.join(","),
                    tz: $('#tz').val()
                };
                $.post("/root/organization/create_do", params, function(result) {
                    if (result.errno == 0) {
                        setTimeout(function(){
                            swal({
                                title: "Good job!",
                                text: "create is successful organization",
                                type: "success",
                            },
                            function (isConfirm) {
                                location.href = "/root/organization/";
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

if ($('#orgUpdateForm').length > 0){
    $("#saveButton").bind("click", function() {
        if ($('#name').val().trim() == '' || $('#memo').val().trim() == '') {
            return swal({
                title: "Parameter incorrect",
                text: "please fill out the required field(s)",
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
                var features = []
                $("input[name='feature[]']:checked").each(function (){
                    features.push($(this).val());
                });
                var params = {
                    org_id: $('#org_id').val(),
                    name: $('#name').val().trim(),
                    memo: $('#memo').val().trim(),
                    feature: features.join(","),
                    tz: $('#tz').val()
                };
                $.post("/root/organization/update_do", params, function(result) {
                    if (result.errno == 0) {
                        setTimeout(function(){
                            swal("Good job!", "update is successful organization", "success");
                        }, 100);
                    } else {
                        setTimeout(function(){ swal(result.errmsg, "", "error"); }, 100);
                    }
                });
            }
        });
    });

    $("#deleteButton").bind("click", function() {
        swal({
            title: 'Do you confirm to delete the Organization',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            confirmButtonClass: 'btn btn-success',
            cancelButtonClass: 'btn btn-danger m-l-10',
            allowOutsideClick: false
        }, function (isConfirm) {
            if (isConfirm) {
                $.post("/root/organization/remove", {org_id: $('#org_id').val()}, function(result) {
                    if (result.errno == 0) {
                        setTimeout(function(){
                            swal({
                                title: "Good job!",
                                text: "delete is successful Organization",
                                type: "success",
                            },
                            function (isConfirm) {
                                location.href = "/root/organization/";
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
