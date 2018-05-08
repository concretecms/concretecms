/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global ConcreteMenu */

;(function(global, $) {
	'use strict';

	function ConcreteCalendarEventMenu($element, options) {
		var my = this;
		options = options || {};

		options = $.extend({
			'container': false,
		}, options);

		my.options = options;

		if ($element) {

			ConcreteMenu.call(my, $element, options);

		}
	}

	ConcreteCalendarEventMenu.prototype = Object.create(ConcreteMenu.prototype);

	ConcreteCalendarEventMenu.prototype.setupMenuOptions = function($menu) {
		var parent = ConcreteMenu.prototype;

		parent.setupMenuOptions($menu);


		// We don't need this class any longer, but let's keep it around in case.

	};

	// jQuery Plugin
	$.fn.concreteCalendarEventMenu = function(options) {
		return $.each($(this), function(i, obj) {
			new ConcreteCalendarEventMenu($(this), options);
		});
	};

	global.ConcreteCalendarEventMenu = ConcreteCalendarEventMenu;

})(this, jQuery);
