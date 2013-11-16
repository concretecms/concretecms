/**
 * Base search class for AJAX forms in the UI
 */

!function(global, $) {
	'use strict';

	function ConcreteAjaxForm($form, options) {
		var my = this;
		options = options || {};
		options = $.extend({
			'dataType': 'json',
			'type': 'post'
		}, options);

		my.$form = $form;
		my.options = options;

		my.$form.ajaxForm({
			type: options.type,
			dataType: options.dataType,
			beforeSubmit: function() {
				my.beforeSubmit(my)
			},
			error: function(r) {
				my.error(r, my);
			},
			success: function(r) {
				my.success(r, my)
			},
			complete: function() {
				my.complete(my);
			}
		});

		return my.$form;
	}

	ConcreteAjaxForm.prototype = {
		
		beforeSubmit: function(my) {
			jQuery.fn.dialog.showLoader();
		},

		error: function(r, my) {
			ccmAlert.notice('Error', '<div class="alert alert-danger">' + r.responseText + '</div>');
		},

		validateResponse: function(r) {
			if (r.error) {
				ccmAlert.notice('Error', '<div class="alert alert-danger">' + r.errors.join("<br>") + '</div>');
				return false;
			}
			return true;
		},

		success: function(r, my) {
			if (my.validateResponse(r)) {
				ccm_event.publish('AjaxFormSubmitSuccess', r, my.$form.get(0));
				if (my.$form.attr('data-dialog-form')) {
					jQuery.fn.dialog.closeTop();
				}
				ccmAlert.showResponseNotification(r.message, 'ok', 'success');
				CCMPanelManager.exitPanelMode();
				if (r.redirectURL) {
					setTimeout(function() {
						window.location.href = r.redirectURL;
					}, 2000);
				}
			}
		},

		complete: function(my) {
			jQuery.fn.dialog.hideLoader();
		}
	}

	// jQuery Plugin
	$.fn.concreteAjaxForm = function(options) {
		return $.each($(this), function(i, obj) {
			new ConcreteAjaxForm($(this), options);
		});
	}

	global.ConcreteAjaxForm = ConcreteAjaxForm;

}(this, $);