/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global grecaptcha */

;(function(global, $) {
'use strict';

function render(element) {
    var $element = $(element),
        clientId = grecaptcha.render(
            $element.attr('id'),
            {
                sitekey: $element.data('sitekey'),
                badge: $element.data('badge'),
                theme: $element.data('theme'),
                size: 'invisible'
            }
        );
    grecaptcha.ready(function () {
        grecaptcha.execute(
            clientId,
            {
                action: 'submit'
            }
        );
    });
}

global.RecaptchaV3 = function() {
    $('.recaptcha-v3').each(function () {
        render(this);
    });
};

})(window, jQuery);
