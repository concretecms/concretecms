!function(global, $, _) {
	'use strict';

	function ConcreteFileMenu($element, options) {
		var my = this, 
			options = options || {};

		options = $.extend({
			'container': false,
		}, options);

		ConcreteMenu.call(my, $element, options);
	}

	ConcreteFileMenu.prototype = Object.create(ConcreteMenu.prototype);

	ConcreteFileMenu.prototype.setupMenuOptions = function($menu) {
		var my = this, 
			parent = ConcreteMenu.prototype,
			container = my.options.container;

		parent.setupMenuOptions($menu);
		$menu.find('a[data-file-manager-action=clear]').on('click', function() {
			var menu = ConcreteMenuManager.getActiveMenu();
			if (menu) {
				menu.hide();
			}
			_.defer(function() { container.$element.html(container._chooseTemplate); });
			return false;
		});
		$menu.find('a[data-file-manager-action=duplicate]').on('click', function() {
			
			return false;
		});
	}

	// jQuery Plugin
	$.fn.concreteFileMenu = function(options) {
		return $.each($(this), function(i, obj) {
			new ConcreteFileMenu($(this), options);
		});
	}

	global.ConcreteFileMenu = ConcreteFileMenu;

}(this, $, _);