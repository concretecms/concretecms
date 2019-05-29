/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global CCM_DISPATCHER_FILENAME */

;(function(global, $) {
'use strict';

var lastUserInteraction = new Date();
var lastHeartbeat = new Date();
$(window).on('mousemove keydown keyup', function() {
    lastUserInteraction = new Date();
});
setInterval(
    function() {
        if(lastUserInteraction.getTime() > lastHeartbeat.getTime()) {
            lastHeartbeat = new Date();
            $.ajax({
                async: true,
                cache: false,
                dataType: 'json',
                type: 'GET',
                url: CCM_DISPATCHER_FILENAME + '/ccm/system/heartbeat',
            });
        }
    },
    30 * 1000
);

})(this, jQuery);
