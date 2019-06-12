/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global CCM_DISPATCHER_FILENAME */

;(function(global, $) {
    'use strict';

    var lastUserInteraction = new Date(),
        lastHeartbeat = new Date(),
        MIN_HEARTHBEAT_INTERVAL = 30 * 1000;

    $(window).on('mousemove keydown keyup', function() {
        lastUserInteraction = new Date();
        if (lastUserInteraction.getTime() - lastHeartbeat.getTime() < MIN_HEARTHBEAT_INTERVAL) {
            return;
        }
        lastHeartbeat = new Date();
        $.ajax({
            async: true,
            cache: false,
            dataType: 'json',
            type: 'GET',
            url: CCM_DISPATCHER_FILENAME + '/ccm/system/heartbeat',
        });
    });

})(this, jQuery);
