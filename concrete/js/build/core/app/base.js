/**
 * Basic functions
 */

/**
 * JavaScript localization. Provide a key and then reference that key in PHP somewhere (where it will be translated)
 */
ccm_t = function(key) {
    return $("input[name=ccm-string-" + key + "]").val();
}


/**
 * Basic JSON parsing used in block and page editing
 */
ccm_parseJSON = function(resp, onNoError) {
    if (resp.error) {
        alert(resp.message);
    } else {
        onNoError();
    }
}

//legacy
var ccm_isBlockError = false;
var ccm_blockError = false;

ccm_addError = function(err) {
    if (!ccm_isBlockError) {
        ccm_blockError = '';
        ccm_blockError += '<ul>';
    }

    ccm_isBlockError = true;
    ccm_blockError += "<li>" + err + "</li>";;
}

ccm_resetBlockErrors = function() {
    ccm_isBlockError = false;
    ccm_blockError = "";
}