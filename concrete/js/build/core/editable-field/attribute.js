/* jshint unused:vars, undef:true, browser:true, jquery:true */

/* concrete5 Attribute type for X-Editable */
;(function(global, $) {
	'use strict';

	function ConcreteEditableAttribute(options) {
		var $scope = $(options.scope),
			akID = $scope.attr('data-key-id');

		options.tpl = $('[data-editable-attribute-key-id=' + akID + ']');
		this.init('concreteeditableattribute', options, ConcreteEditableAttribute.defaults);
	}

    $.fn.editableutils.inherit(ConcreteEditableAttribute, $.fn.editabletypes.abstractinput);

    $.extend(ConcreteEditableAttribute.prototype, {

    	input2value: function() {
    		var values = this.$input.closest('.editableform').serializeArray();
    		return values;
    	},

        value2html: function(value, element) {
        	$(element).html(value);
        },

    });

    ConcreteEditableAttribute.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults);

    $.fn.editabletypes.concreteattribute = ConcreteEditableAttribute;

})(this, jQuery);
