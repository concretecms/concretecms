/* jshint unused:vars, undef:true */

/* concrete5 Redactor functionality */
;(function(global) {
    'use strict';

    if (typeof global.RedactorPlugins === 'undefined') global.RedactorPlugins = {};

    global.RedactorPlugins.concrete5lightbox = function() {

        return {
            init: function()
            {
                this.opts.concrete5.lightbox = true;
            }
    
        };
    };

})(this);
