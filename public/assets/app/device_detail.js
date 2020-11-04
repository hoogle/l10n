$(function () {
    // init history
    history.replaceState('', 'device_detail_query', '/system/devicedetail/query')
    $('button[type=submit]').prop('disabled', false);
});
