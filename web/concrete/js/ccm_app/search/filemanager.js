/**
 * block ajax
 */

!function(global, $) {
	'use strict';

	function ConcreteFileManager($element, options) {
		'use strict';
		var my = this;
		options = $.extend({
		}, options);
		my.options = options;

		if (!$('#ccm-file-manager-download-target').length) {
			my.$downloadTarget = $('<iframe />', {'id': 'ccm-file-manager-download-target'}).appendTo(document.body);
		} else {
			my.$downloadTarget = $('#ccm-file-manager-download-target');
		}
		return ConcreteAjaxSearch.call(my, $element, options);
	}

	ConcreteFileManager.prototype = Object.create(ConcreteAjaxSearch.prototype);

	ConcreteFileManager.prototype.handleSelectedBulkAction = function(value, $option, items) {
		var my = this;
		if (value == 'download') {
			my.$downloadTarget.get(0).src = CCM_TOOLS_PATH + '/files/download?' + jQuery.param(items);

		} else {
			ConcreteAjaxSearch.prototype.handleSelectedBulkAction.call(this, value, $option, items);
		}
	}

	// jQuery Plugin
	$.fn.concreteFileManager = function(options) {
		return $.each($(this), function(i, obj) {
			new ConcreteFileManager($(this), options);
		});
	}

	global.ConcreteFileManager = ConcreteFileManager;

}(this, $);