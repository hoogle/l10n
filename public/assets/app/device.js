$(function () {
    // init history
    history.replaceState('', 'device_query', '/system/device/query')
    $('button[type=submit]').prop('disabled', false);
});
