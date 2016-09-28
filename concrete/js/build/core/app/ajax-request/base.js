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
			};

			my.before(my);
			$.ajax(options);
		},

		before: function(my) {
			if (my.options.loader) {
				jQuery.fn.dialog.showLoader();
			}
		},

		errorResponseToString: function(r) {
			var result = r.responseText;
			if (r.responseJSON) {
				var json = r.responseJSON;
				if ($.isArray(json.errors) && json.errors.length > 0 && typeof json.errors[0] === 'string') {
					result = json.errors.join('\n');
				} else if (typeof json.error === 'string' && json.error !== '') {
					result = json.error;
				}
			}

			return result;
		},

		error: function(r, my) {
			ConcreteEvent.fire('AjaxRequestError', {
				'response': r
			});
			ConcreteAlert.dialog('Error', my.errorResponseToString(r));
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
	};

	// static method
	ConcreteAjaxRequest.validateResponse = ConcreteAjaxRequest.prototype.validateResponse;
	ConcreteAjaxRequest.errorResponseToString = ConcreteAjaxRequest.prototype.errorResponseToString;

	// jQuery Plugin
	$.concreteAjax = function(options) {
		new ConcreteAjaxRequest(options);
	};

	global.ConcreteAjaxRequest = ConcreteAjaxRequest;

}(this, $);