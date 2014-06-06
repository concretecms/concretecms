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
				ajaxOptions: {
					dataType: 'json'
				},
				emptytext: ccmi18n.none,
				showbuttons: true,
				params: my.options.data,
				url: my.options.url,
				success: function(r, newValue) {
		        	if (ConcreteAjaxRequest.validateResponse(r)) {
		        		return {'newValue': newValue};
		        	} else {
		        		return '';
		        	}
				},
				pk: '_x' // we have to include this otherwise xeditable doesn't work.
			});
		},

		setupXeditableAttributeField: function($field) {
			var my = this;
			$field.editable({
				ajaxOptions: {
					dataType: 'json'
				},
				emptytext: ccmi18n.none,
				showbuttons: true,
				savenochange: true,
				autotext: 'never',
				url: my.options.url,
				params: function(args) {
					var newParams = [];
					newParams.push({name: 'name', 'value': args.name});
					newParams.push({name: 'pk', 'value': args.pk});
					_.each(my.options.data, function(value, key) {
						if (typeof(value) == 'object') {
							newParams.push({name: value.name, 'value': value.value});
						} else {
							newParams.push({name: key, 'value': value});
						}
					});
					_.each(args.value, function(value) {
						newParams.push({name: value.name, 'value': value.value});
					});
					return newParams;
				},
				success: function(r, newValue) {
		        	if (ConcreteAjaxRequest.validateResponse(r)) {
		        		return {'newValue': r.value};
		        	} else {
		        		return '';
		        	}
				},
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
		        formData: my.options.data,
		        start: function() {
		        	jQuery.fn.dialog.showLoader();
		        	//ConcreteAlert.showLoader();
		        },
		        success: function(r) {
		        	my.updateImageField(r, $field);
		        },
		        error: function(r) {
					ConcreteAlert.dialog('Error', r.responseText);
		        },
		        complete: function(r) {
		        	jQuery.fn.dialog.hideLoader();
		        	//ConcreteAlert.hideLoader()
		        }
		    });
		},

		updateImageField: function(r, $field) {
			var my = this;
        	if (ConcreteAjaxRequest.validateResponse(r)) {
	        	$field.find('.editable-image-display').html(r.imageHTML);
	        	my.setupImageField($field);
				ConcreteAlert.notify({
				'message': r.message
				});
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
					method = 'update'  + $field.attr('data-editable-field-type').charAt(0).toUpperCase() + $field.attr('data-editable-field-type').slice(1) + 'Field',
					data = my.options.data;

				data.task = 'clear';

				var url = my.getAjaxURL($field);
				return new ConcreteAjaxRequest({
					url: url,
					data: data,
					success: function(r) {
						my[method](r, $field);

					}
				})
				return false;
			});
			my.$element.on('click', '[data-editable-field-command=clear_attribute]', function() {
				var data = my.options.data,
					url = my.getAjaxURL($(this)),
					akID = $(this).attr('data-key-id'),
					ajaxData = [];

				_.each(data, function(value, key) {
					if (typeof(value) == 'object') {
						ajaxData.push({name: value.name, 'value': value.value});
					} else {
						ajaxData.push({name: key, 'value': value});
					}
				});

				ajaxData.push({'name': 'akID', 'value': akID});

				return new ConcreteAjaxRequest({
					url: url,
					data: ajaxData,
					success: function(r) {
     					$('[data-key-id=' + akID + '][data-editable-field-type=xeditableAttribute]').editable('setValue', '');
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