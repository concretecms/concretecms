/**
 * Base search class for AJAX forms in the UI
 */

!function(global, $) {
	'use strict';

	function ConcreteMenu($element, options) {
		var my = this;
		options = options || {};
		options = $.extend({

		}, options);

		my.$element = $element;
		my.options = options;
		return my.$element;
	}

	ConcreteMenu.prototype = {
		

	}

	// jQuery Plugin
	$.fn.concreteMenu = function(options) {
		return $.each($(this), function(i, obj) {
			new ConcreteMenu($(this), options);
		});
	}

	global.ConcreteMenu = ConcreteMenu;

}(this, $);