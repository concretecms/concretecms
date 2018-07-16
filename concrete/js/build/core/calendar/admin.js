/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global CCM_DISPATCHER_FILENAME, ConcreteAlert */

;(function(global, $) {
	'use strict';

	function ConcreteCalendarAdmin($element) {

		// List View
		$element.find('table[data-table=event-list] tbody tr').each(function() {
			$(this).concreteCalendarEventMenu({
				menu: $(this).find('div[data-event-occurrence]')
			});
		});

		$element.find('table.ccm-dashboard-calendar div.ccm-dashboard-calendar-date-event > a').each(function() {
			$(this).concreteCalendarEventMenu({
				menu: $(this).parent().find('div[data-event-occurrence]')
			});
		});
	}

	ConcreteCalendarAdmin.setupVersionsTable = function($table) {
		$table.on('click', 'input[name=eventVersionID]', function () {
			var eventVersionID = $(this).val();
			if (eventVersionID == -1) {
				$.concreteAjax({
					url: CCM_DISPATCHER_FILENAME + '/ccm/calendar/event/version/unapprove_all',
					data: {'eventID': $(this).data('event-id'), ccm_token: $(this).data('token')},
					success: function (r) {
						ConcreteAlert.notify({
							'message': r.message
						});

						$('#ccm-calendar-event-version-reload').show();
						$table.find('tr[class=success]').removeClass();
						$table.find('a[data-action=delete-version]').show();
					}
				});

			} else {
				$.concreteAjax({
					url: CCM_DISPATCHER_FILENAME + '/ccm/calendar/event/version/approve',
					data: {'eventVersionID': eventVersionID, ccm_token: $(this).data('token')},
					success: function (r) {
						ConcreteAlert.notify({
							'message': r.message
						});

						$('#ccm-calendar-event-version-reload').show();
						$table.find('tr[class=success]').removeClass();
						$table.find('a[data-action=delete-version]').show();
						$table.find('tr[data-calendar-event-version-id=' + eventVersionID + ']').addClass('success');
						$table.find('tr[data-calendar-event-version-id=' + eventVersionID + '] a[data-action=delete-version]').hide();
					}
				});

			}
		});
		$table.on('click', 'a[data-action=delete-version]', function () {
			var eventVersionID = $(this).attr('data-calendar-event-version-id');
			$.concreteAjax({
				url: CCM_DISPATCHER_FILENAME + '/ccm/calendar/event/version/delete',
				data: {'eventVersionID': eventVersionID, ccm_token: $(this).data('token')},
				success: function (r) {
					ConcreteAlert.notify({
						'message': r.message
					});

					$('#ccm-calendar-event-version-reload').show();
					var $row = $table.find('tr[data-calendar-event-version-id=' + eventVersionID + ']');
					$row.queue(function () {
						$(this).addClass('animated fadeOutDown');
						$(this).dequeue();
					}).delay(500).queue(function () {
						$(this).remove();
						$(this).dequeue();
					});
				}
			});
		});
	};


	global.ConcreteCalendarAdmin = ConcreteCalendarAdmin;

})(this, jQuery);
