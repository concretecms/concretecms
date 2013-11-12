/**
 * Base search class for AJAX searching
 */

!function(window, $) {
	'use strict';

	function ConcreteAjaxSearch($element, options) {
		options = options || {};
		this.$element = $element;
		this.$results = $element.find('div[data-search-results]');
		this.setupCheckboxes();
		this.setupSort();
		this.setupSearch();
		this.setupPagination();
	}

	ConcreteAjaxSearch.prototype.ajaxUpdate = function(url, data) {
		data = data || [];
		var cs = this;
		jQuery.fn.dialog.showLoader();
		$.ajax({
			type: 'post', 
			data: data,
			url: url,
			complete: function() {
				jQuery.fn.dialog.hideLoader();
			},
			error: function(r) {
				ccmAlert.notice(r);
			},
			success: function(r) {
				cs.$results.html(r);
			}
		});
	}

	ConcreteAjaxSearch.prototype.setupSort = function() {
		var cs = this;
		this.$element.on('click', 'thead th a', function() {
			cs.ajaxUpdate($(this).attr('href'));
			return false;
		});
	}

	ConcreteAjaxSearch.prototype.setupSearch = function() {
		var cs = this;
		this.$element.on('submit', 'form[data-search-form]', function() {
			var data = $(this).serializeArray();
			data.push({'name': 'submitSearch', 'value': '1'});
			cs.ajaxUpdate($(this).attr('action'), data);
			return false;
		});
	}

	ConcreteAjaxSearch.prototype.setupPagination = function() {
		var cs = this;
		this.$element.on('click', 'ul.pagination a', function() {
			cs.ajaxUpdate($(this).attr('href'));
			return false;
		});
	}

	ConcreteAjaxSearch.prototype.setupCheckboxes = function() {
		var cs = this;
		this.$element.on('click', 'input[data-search-checkbox=select-all]', function() {
			cs.$element.find('input[data-search-checkbox=individual]').prop('checked', $(this).is(':checked'));
		});
	}

	// jQuery Plugin
	$.fn.concreteAjaxSearch = function(options) {
		return new ConcreteAjaxSearch(this, options);
	}

}(window, $);