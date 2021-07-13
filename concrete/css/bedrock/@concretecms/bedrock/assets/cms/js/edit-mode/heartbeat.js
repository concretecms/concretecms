/* eslint-disable no-new, no-unused-vars, camelcase */
/* global CCM_DISPATCHER_FILENAME */

var lastUserInteraction = new Date()
var lastHeartbeat = new Date()
var MIN_HEARTHBEAT_INTERVAL = 30 * 1000

$(window).on('mousemove keydown keyup', function() {
    lastUserInteraction = new Date()
    if (lastUserInteraction.getTime() - lastHeartbeat.getTime() < MIN_HEARTHBEAT_INTERVAL) {
        return
    }
    lastHeartbeat = new Date()
    $.ajax({
        async: true,
        cache: false,
        dataType: 'json',
        type: 'GET',
        url: CCM_DISPATCHER_FILENAME + '/ccm/system/heartbeat'
    })
})
