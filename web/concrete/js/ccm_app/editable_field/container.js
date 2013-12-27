/**
 * Base search class for AJAX forms in the UI
 */

!function(global, $) {
	'use strict';

	function ConcreteEditableFieldContainer($element, options) {
		var my = this;
		options = options || {};
		options = $.extend({
			url: false,
			data: {}
		}, options);
		my.$element = $element;
		my.options = options;
		my.initializeEditableFields();
		my.initializeClearCommands();
		return my.$element;
	}

	ConcreteEditableFieldContainer.prototype = {

		/** 
		 * returns either the options.url or a url from the field. This is the default behavior for x editable so we don't have
		 * to do this there
		 */
		getAjaxURL: function($field) {
			var my = this, url = my.options.url;
			if ($field.attr('data-url')) {
				url = $field.attr('data-url');
			}
			return url;
		},

		setupXeditableField: function($field) {
			var my = this;
			$field.editable({
				showbuttons: false,
				params: my.options.data,
				url: my.options.url,
				pk: '_x' // we have to include this otherwise xeditable doesn't work.
			});
		},

		setupImageField: function($field) {
			var my = this;
			// automatically set the width and height of the proxy field
			var $thumbnail = $field.find('.editable-image-wrapper img');
			$field.find('.editable-image-wrapper input').css('width', $thumbnail.width()).css('height', $thumbnail.height());

		    $field.fileupload({
		    	url: my.getAjaxURL($field),
		        dataType: 'json',
		        start: function() {
		        	ConcreteAlert.showLoader();
		        },
		        success: function(r) {
		        	my.updateImageField(r, $field);
		        },
		        error: function(r) {
					ConcreteAlert.notice('Error', '<div class="alert alert-danger">' + r.responseText + '</div>');
		        },
		        complete: function(r) {
		        	ConcreteAlert.hideLoader()
		        }
		    });
		},

		updateImageField: function(r, $field) {
			var my = this;
        	if (ConcreteAjaxRequest.validateResponse(r)) {
	        	$field.find('.editable-image-display').html(r.imageHTML);
	        	my.setupImageField($field);
				ConcreteAlert.showResponseNotification(r.message, 'ok', 'success');
        	}
		},

		initializeEditableFields: function() {
			var my = this;
			my.$element.find('[data-editable-field-type]').each(function() {
				var $field = $(this);
				var method = 'setup'  + $field.attr('data-editable-field-type').charAt(0).toUpperCase() + $field.attr('data-editable-field-type').slice(1) + 'Field';
				my[method]($field);
			});
		},

		initializeClearCommands: function() {
			var my = this;
			my.$element.on('click', '[data-editable-field-command=clear]', function() {
				var $icon = $(this), 
					$field = $icon.closest('[data-editable-field-type]'),
					method = 'update'  + $field.attr('data-editable-field-type').charAt(0).toUpperCase() + $field.attr('data-editable-field-type').slice(1) + 'Field';

				var url = my.getAjaxURL($field);
				return new ConcreteAjaxRequest({
					url: url,
					data: {'task': 'clear'},
					success: function(r) {
						my[method](r, $field);

					}
				})
				return false;
			});
		}

	}

	// jQuery Plugin
	$.fn.concreteEditableFieldContainer = function(options) {
		return $.each($(this), function(i, obj) {
			new ConcreteEditableFieldContainer($(this), options);
		});
	}

	global.ConcreteEditableFieldContainer = ConcreteEditableFieldContainer;

}(this, $);