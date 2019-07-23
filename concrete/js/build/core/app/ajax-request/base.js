/* jshint unused:vars, undef:true, jquery:true */
/* global ccmi18n, ConcreteEvent, ConcreteAlert */

;(function(global, $) {
	'use strict';

	function ConcreteAjaxRequest(options) {
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
			},
			skipResponseValidation: false
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
				$.fn.dialog.showLoader();
			}
		},

		errorResponseToString: function(r) {
			return ConcreteAjaxRequest.renderErrorResponse(r, true);
		},

		error: function(r, my) {
			ConcreteEvent.fire('AjaxRequestError', {
				response: r
			});
			ConcreteAlert.dialog(ccmi18n.error, ConcreteAjaxRequest.renderErrorResponse(r, true));
		},

		validateResponse: function(r, callback) {
			if (r.error) {
				ConcreteEvent.fire('AjaxRequestError', {
					response: r
				});
				ConcreteAlert.dialog(
					ccmi18n.error,
					ConcreteAjaxRequest.renderJsonError(r),
					function() {
						if (callback) {
							callback(false, r);
						}
					}
				);
				return false;
			} else if (callback) {
				callback(true, r);
			}
			return true;
		},

		success: function(r, my, callback) {
			if (my.options.dataType != 'json' || my.options.skipResponseValidation || my.validateResponse(r)) {
				if (callback) {
					callback(r);
				}
			}
		},

		complete: function(my) {
			if (my.options.loader) {
				$.fn.dialog.hideLoader();
			}
		}
	};

	// static methods
	ConcreteAjaxRequest.renderJsonError = function (json, asHtml) {
		if (!json) {
			return '';
		}
		var toHtml = function(text, index) {
			if (typeof index === 'number' && $.isArray(json.htmlErrorIndexes) && $.inArray(index, json.htmlErrorIndexes) >= 0) {
				return text;
			}
			return $('<div />').text(text).html().replace(/\n/g, '<br />');
		};
		var result = '';
		if (typeof json.error === 'object' && $.isArray(json.error.trace)) {
			result = '<p class="text-danger"><strong>' + toHtml(json.error.message) + '</strong></p>';
			result += '<p class="text-muted">' + ccmi18n.errorDetails + '</p>';
			result += '<table class="table"><tbody>';
			for (var i = 0, trace; i < json.error.trace.length; i++) {
				trace = json.error.trace[i];
				result += '<tr><td>' + trace.file + '(' + trace.line + '): ' + trace['class'] + '->' + trace['function'] + '<td></tr>';
			}
			result += '</tbody></table>';
		} else if ($.isArray(json.errors) && json.errors.length > 0 && typeof json.errors[0] === 'string') {
			$.each(json.errors, function (index, text) {
				result += '<p class="text-danger"><strong>' + toHtml(text, index) + '</strong></p>';
			});
		} else if (typeof json.error === 'string' && json.error !== '') {
			result = '<p class="text-danger" style="word-break: break-all"><strong>' + toHtml(json.error) + '</strong></p>';
		}
		return result;
	};
	ConcreteAjaxRequest.renderErrorResponse = function (xhr, asHtml) {
		return ConcreteAjaxRequest.renderJsonError(xhr.responseJSON, asHtml) || xhr.responseText;
	};
	ConcreteAjaxRequest.validateResponse = ConcreteAjaxRequest.prototype.validateResponse;
	ConcreteAjaxRequest.errorResponseToString = ConcreteAjaxRequest.prototype.errorResponseToString;

	// jQuery Plugin
	$.concreteAjax = function(options) {
		new ConcreteAjaxRequest(options);
	};

	global.ConcreteAjaxRequest = ConcreteAjaxRequest;

})(this, jQuery);
