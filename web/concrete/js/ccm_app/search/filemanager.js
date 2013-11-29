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

		ConcreteAjaxSearch.call(my, $element, options);


		if (!$('#ccm-file-manager-download-target').length) {
			my.$downloadTarget = $('<iframe />', {'id': 'ccm-file-manager-download-target'}).appendTo(document.body);
		} else {
			my.$downloadTarget = $('#ccm-file-manager-download-target');
		}

		my.setupStarredResults();
	}

	ConcreteFileManager.prototype = Object.create(ConcreteAjaxSearch.prototype);

	ConcreteFileManager.prototype.setupStarredResults = function() {
		var my = this;
		my.$element.on('click', 'a[data-search-toggle=star]', function() {
			var $link = $(this);
			var data = {'fID': $(this).attr('data-search-toggle-file-id')};
			my.ajaxUpdate($link.attr('data-search-toggle-url'), data, function(r) {
				if (r.star) {
					$link.parent().addClass('ccm-file-manager-search-results-star-active');
				} else {
					$link.parent().removeClass('ccm-file-manager-search-results-star-active');	
				}
			});
			return false;
		});
	}

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