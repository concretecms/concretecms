!function(global, $) {
	'use strict';

	function ConcreteAjaxRequest(options) {
		'use strict';
		var my = this;
		options = options || {};
		options = $.extend({
			'dataType': 'json',
			'type': 'post',
			'error': function(r) {
				my.error(r, my);
			},
			'complete': function() {
				my.complete(my);
			}
		}, options);
		my.options = options;
		my.execute();
	}

	ConcreteAjaxRequest.prototype = {

		execute: function() {
			var my = this,
				options = my.options,
				successCallback = options.success;

			options.success = function(r) {
				my.success(r, my, successCallback);
			}

			my.before();
			$.ajax(options);
		},

		before: function(my) {
			jQuery.fn.dialog.showLoader();
		},

		error: function(r, my) {
			ConcreteAlert.dialog('Error', r.responseText);
		},

		validateResponse: function(r) {
			if (r.error) {
				ConcreteAlert.dialog('Error', r.errors.join("<br/>"));
				return false;
			}
			return true;
		},

		success: function(r, my, callback) {
			if (my.validateResponse(r)) {
				callback(r);
			}
		},

		complete: function(my) {
			jQuery.fn.dialog.hideLoader();
		}
	}

	// static method
	ConcreteAjaxRequest.validateResponse = ConcreteAjaxRequest.prototype.validateResponse;

	// jQuery Plugin
	$.concreteAjax = function(options) {
		new ConcreteAjaxRequest(options);
	}

	global.ConcreteAjaxRequest = ConcreteAjaxRequest;

}(this, $);