/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global CCM_TOOLS_PATH, CCM_SECURITY_TOKEN */

/* Page Reindexing */
;(function(global, $) {
    'use strict';

    global.ccm_doPageReindexing = function() {
        $.get(CCM_TOOLS_PATH + '/reindex_pending_pages?ccm_token=' + CCM_SECURITY_TOKEN);
    };

})(window, jQuery);
