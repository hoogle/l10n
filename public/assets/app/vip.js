$(function () {
    const vipBoxTemp = $('#vipBoxTemp').html();
    let curPage = 1;
    let curListLen = 9;
    let qVal = null;
    let invalidLoad = false;
    if ($('#sys-msg').length > 0) {
        $("#sys-msg").click(function() {
            $("#sys-message2").hide();
        });
    }

    if ($('#organization_vip').length > 0) {
        getVIPList({curPage: curPage, curListLen: curListLen, q: qVal});
        $(window).scroll(function() {
            console.log($(window).scrollTop(), 'scrollTop');
            var disHeight = $(document).height() - $(window).height();
            if ((disHeight - $(document).scrollTop()) < 50) {
                // ajax call get data from server and append to the div
                if (invalidLoad) {
                    return;
                }
                curPage +=1;
                getVIPList({curPage: curPage, curListLen: curListLen, q: qVal, scrollLoad: true});
                console.log('need to load:' + curPage);
            }
        });
    }
    if ($('form[name=search]').length > 0) {
        $('#searchButton').prop('disabled', false);
        $('form[name=search]').submit(function(e) {
            // location.href = "/organization/vip?q=" + encodeURIComponent($('#searchText').val());
            e.preventDefault();
            qVal = $('#searchText').val();
            curPage = 1;
            invalidLoad = false;
            getVIPList({curPage: curPage, curListLen: curListLen, q: qVal});
        });
    }

    if ($('#updateMemberForm').length > 0) {

        $('#birthday').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });

        $("#resetButton").bind("click", function() {
            $('#updateMemberForm')[0].reset();
        });

        $("#applyButton").bind("click", function() {
            if ($('#name').val() == "") {
                $('#name').focus();
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
                    var params = {
                        member_id: $('#member_id').val(),
                        name: $('#name').val(),
                        gender: $('input[name=gender]:checked').val(),
                        birthday: $('#birthday').val(),
                        telephone: $('#telephone').val()
                    };
                    $.post("/organization/vip/profile_edit_do", params, function(result) {
                        if (result.errno == 0) {
                            setTimeout(function(){
                                swal({
                                    title: "Good job!",
                                    text: "update is successful member data",
                                    type: "success",
                                },
                                    function (isConfirm) {
                                        location.reload();
                                    });
                            }, 100);
                        } else {
                            setTimeout(function(){ swal(result.errmsg, "", "error"); }, 100);
                        }
                    });
                }
            });
        });

        $('#deleteButton').bind("click", function() {
            swal({
                title: 'Do you confirm to delete the VIP member',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger m-l-10',
                allowOutsideClick: false
            }, function (isConfirm) {
                if (isConfirm) {
                    $.post("/organization/vip/remove", {member_id: $('#member_id').val()}, function(result) {
                        if (result.errno == 0) {
                            setTimeout(function(){
                                swal({
                                    title: "Good job!",
                                    text: "delete is successful vip member",
                                    type: "success",
                                },
                                    function (isConfirm) {
                                        location.href = "/organization/vip";
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
    // **********************
    // getVIPList
    // {Object} param : curPage, curListLen
    // **********************
    function getVIPList(param) {
        console.log('getVIPList implement');
        console.log(param);
        let url = '/organization/vip/page/{curPage}';
        let formData = {
            per_page: param.curListLen,
            q: param.q
        };
        url = $.fn.replaceElString(url, param); 
        function callback (rs) {
            console.log(rs);
            // rs = JSON.parse(rs);
            const $vipSection = $('#vipSection');
            if (!rs || rs.length < 1 || rs.data.length < 1) {
                curPage -=1;
                invalidLoad = true;
                if (curPage === 0) {
                    $vipSection.empty();
                    $('.my-result-title').html(LAN.total_members.replace('{num}', 0));
                }
                return;
            }
            if (!param.scrollLoad) {
                $vipSection.empty();
                $('.my-result-title').html(LAN.total_members.replace('{num}', rs.rows));
            }
            let curStart = ((parseInt(rs.curr_page) - 1) * param.curListLen) + 1;
            $.each(rs.data, function (i, row) {
                row.telephone = row.telephone ? 'tel:' + row.telephone : 'tel:';
                console.log(row);
                var temp = $($.fn.replaceElString(vipBoxTemp, row));
                $vipSection.append(temp);
            });
            console.log('scrollHeight:' + document.documentElement.scrollHeight);
            console.log('clientHeight:' + document.documentElement.clientHeight);
            if (document.documentElement.scrollHeight <= document.documentElement.clientHeight) {
                console.log('Content height is smaller than document. Need to load more');
                curPage +=1;
                getVIPList({curPage: curPage, curListLen: curListLen, scrollLoad: true, q: qVal});
            }
        }
        $.post(url, formData, callback, 'json');
    }
});
