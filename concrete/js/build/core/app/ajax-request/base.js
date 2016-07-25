!function(global, $) {
	'use strict';

	function ConcreteAjaxRequest(options) {
		'use strict';
		var my = this;
		options = options || {};
		options = $.extend({
			'dataType': 'json',
			'type': 'post',
			'loader': 'standard',
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

			my.before(my);
			$.ajax(options);
		},

		before: function(my) {
			if (my.options.loader) {
				jQuery.fn.dialog.showLoader();
			}
		},

		error: function(r, my) {
			ConcreteEvent.fire('AjaxRequestError', {
				'response': r
			});
			ConcreteAlert.dialog('Error', r.responseText);
		},

		validateResponse: function(r) {
			if (r.error) {
				ConcreteEvent.fire('AjaxRequestError', {
					'response': r
				});
				ConcreteAlert.dialog('Error', r.errors.join("<br/>"));
				return false;
			}
			return true;
		},

		success: function(r, my, callback) {
			if (my.options.dataType != 'json' || my.validateResponse(r)) {
				if (callback) {
					callback(r);
				}
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