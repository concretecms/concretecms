!function(global, $, _) {
	'use strict';

	function ConcreteCalendarEventMenu($element, options) {
		var my = this,
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
		var my = this,
			parent = ConcreteMenu.prototype,
			container = my.options.container;

		parent.setupMenuOptions($menu);
		$menu.find('a[data-calendar-event-action=duplicate]').on('click', function() {
			var eventID = $(this).attr('data-calendar-event-id'),
				token = $(this).attr('data-calendar-event-token');
			$.concreteAjax({
				url: CCM_DISPATCHER_FILENAME + '/ccm/calendar/event/duplicate',
				data: {
					eventID: eventID,
					ccm_token: token
				},
				success: function(r) {
					window.location.reload();
				}
			});
			return false;
		});
	}

	// jQuery Plugin
	$.fn.concreteCalendarEventMenu = function(options) {
		return $.each($(this), function(i, obj) {
			new ConcreteCalendarEventMenu($(this), options);
		});
	}

	global.ConcreteCalendarEventMenu = ConcreteCalendarEventMenu;

}(this, $, _);