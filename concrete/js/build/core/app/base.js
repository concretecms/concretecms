/* jshint unused:vars, undef:true, browser:true, jquery:true */

/* Basic functions */
;(function(global, $) {
    'use strict';

    /**
     * JavaScript localization. Provide a key and then reference that key in PHP somewhere (where it will be translated)
     */
    global.ccm_t = function(key) {
        return $("input[name=ccm-string-" + key + "]").val();
    };
    
    
    /**
     * Basic JSON parsing used in block and page editing
     */
    global.ccm_parseJSON = function(resp, onNoError) {
        if (resp.error) {
            global.alert(resp.message);
        } else {
            onNoError();
        }
    };
    
    //legacy
    global.ccm_isBlockError = false;
    global.ccm_blockError = false;
    
    global.ccm_addError = function(err) {
        if (!global.ccm_isBlockError) {
            global.ccm_blockError = '';
            global.ccm_blockError += '<ul>';
        }
    
        global.ccm_isBlockError = true;
        global.ccm_blockError += "<li>" + err + "</li>";
    };
    
    global.ccm_resetBlockErrors = function() {
        global.ccm_isBlockError = false;
        global.ccm_blockError = "";
    };

})(window, jQuery);
