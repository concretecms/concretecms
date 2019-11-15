/* Toggle Password Visibility */
;(function(global, $) {
    "use strict";
    $('.show-password').on('click', function() {
        $(this).toggleClass('fa-eye-slash').siblings('input').focus().attr('type', function(index, attrVal){
            return attrVal == 'password' ? 'text' : 'password';
        });
    });        
})(this, jQuery);
