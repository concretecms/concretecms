/* jshint unused:vars, undef:true */

;(function(global) {
    'use strict';

    if (global.RedactorPlugins === 'undefined') global.RedactorPlugins = {};
    
    global.RedactorPlugins.underline = function() {
    	return {
    		init: function()
    		{
    			this.button.addAfter('italic', 'underline', this.lang.get('underline'));
    			/* concrete5 */
    			var $btn = this.button.get('underline');
    			this.button.addCallback($btn, this.underline.format);
    			this.button.setAwesome('underline', 'fa fa-underline');
    			/* end concrete5 */
    		},
    		format: function(s)
    		{
    			var $button = this.button.get('underline');
    			if ($button.hasClass('redactor-act')) {
    				this.inline.removeFormat('u');
    			} else {
    				this.inline.format('u');
    			}
    		}
    	};
    };

})(this);
