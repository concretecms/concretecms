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

				// first, lets check to see if json.error is an object. Because if it is we've enabled
				// debug error handling and we have a bunch of interesting information about the error to report.
				if (typeof json.error === 'object' && $.isArray(json.error.trace)) {
					result = '<p class="text-danger" style="word-break: break-all"><strong>' + json.error.message + '</strong></p>';
					result += '<p class="text-muted">' + ccmi18n.errorDetails + '</p>';
					result += '<table class="table"><tbody>';
					for (var i = 0; i < json.error.trace.length; i++) {
						var trace = json.error.trace[i];
						result += '<tr><td style="word-break: break-all">' + trace.file + '(' + trace.line + '): ' + trace.class + '->' + trace.function + '<td></tr>';
					}
					result += '</tbody></table>';
				} else if ($.isArray(json.errors) && json.errors.length > 0 && typeof json.errors[0] === 'string') {
					result = '<p class="text-danger" style="word-break: break-all"><strong>' + json.errors.join('\n') + '</strong></p>';
				} else if (typeof json.error === 'string' && json.error !== '') {
					result = '<p class="text-danger" style="word-break: break-all"><strong>' + json.error + '</strong></p>';
				}
			}

			return result;
		},

		error: function(r, my) {
			ConcreteEvent.fire('AjaxRequestError', {
				'response': r
			});

			ConcreteAlert.dialog(ccmi18n.error, my.errorResponseToString(r));
		},

		validateResponse: function(r) {
			if (r.error) {
				ConcreteEvent.fire('AjaxRequestError', {
					'response': r
				});
				ConcreteAlert.dialog(ccmi18n.error, '<p class="text-danger">' + r.errors.join("<br/>") + '</p>');
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
			if (my.options.loader) {
				jQuery.fn.dialog.hideLoader();
			}
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