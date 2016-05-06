/**
 * Base search class for AJAX searching
 */

!function(global, $) {
	'use strict';

	function ConcreteAjaxSearch($element, options) {
		options = options || {};
		options = $.extend({
			'result': {},
			'onLoad': false,
			'onUpdateResults': false,
            'bulkParameterName': 'item',
			'selectMode': false,
			'searchMethod': 'post'
		}, options);
		this.$element = $element;
		this.$results = $element.find('div[data-search-element=results]');
		this.$resultsTableBody = this.$results.find('tbody');
		this.$resultsTableHead = this.$results.find('thead');
		this.$resultsPagination = this.$results.find('div.ccm-search-results-pagination');
		this.$menuTemplate = $element.find('script[data-template=search-results-menu]');
		this.$searchFieldRowTemplate = $element.find('script[data-template=search-field-row]');

		this.options = options;

		if ($element.find('script[data-template=search-form]').length) {
			this._templateSearchForm = _.template($element.find('script[data-template=search-form]').html());
		}
		this._templateSearchResultsTableHead = _.template($element.find('script[data-template=search-results-table-head]').html());
		this._templateSearchResultsTableBody = _.template($element.find('script[data-template=search-results-table-body]').html());
		this._templateSearchResultsPagination = _.template($element.find('script[data-template=search-results-pagination]').html());
		if (this.$menuTemplate.length) {
			this._templateSearchResultsMenu = _.template(this.$menuTemplate.html());
		}
		if (this.$searchFieldRowTemplate.length) {
			this._templateAdvancedSearchFieldRow = _.template(this.$searchFieldRowTemplate.html());
		}

		this.setupSearch();
		this.setupCheckboxes();
		this.setupBulkActions();
		this.setupSort();
		this.setupPagination();
        this.setupSelect2();
		this.setupAdvancedSearch();
		this.setupCustomizeColumns();
		this.updateResults(options.result);

		if (options.onLoad) {
			options.onLoad(this);
		}
	}

	ConcreteAjaxSearch.prototype.ajaxUpdate = function(url, data, callback) {
		var cs = this;
		$.concreteAjax({
			url: url,
			data: data,
			method: cs.options.searchMethod,
			success: function(r) {
				if (!callback) {
					cs.updateResults(r);
				} else {
					callback(r);
				}
			}
		})
	}

	ConcreteAjaxSearch.prototype.getSearchData = function() {
		var cs = this;
		var $form = cs.$element.find('form[data-search-form]');
		var data = $form.serializeArray();
		return data;
	}

	ConcreteAjaxSearch.prototype.setupSelect2 = function() {
        var selects = this.$element.find('.select2-select');
        if (selects.length) {
            selects.select2();
        }
    }



	/**
	 * The legacy create menu function for simple list items without multiple selection
	 * @param $selector
     */
    ConcreteAjaxSearch.prototype.createMenu = function($selector) {
		$selector.concreteMenu({
			'menu': $('[data-search-menu=' + $selector.attr('data-launch-search-menu') + ']')
		});
	}

	/**
	 * The legacy setup menus function for simple list items without multiple selection
	 * @param result
     */
	ConcreteAjaxSearch.prototype.setupMenus = function(result) {
		var cs = this;
		if (cs._templateSearchResultsMenu) {
			cs.$element.find('[data-search-menu]').remove();

			// loop through all results,
			// create nodes for them.
			$.each(result.items, function(i, item) {
				cs.$results.append(cs._templateSearchResultsMenu({'item': item}));
			});

			cs.$element.find('tbody tr').each(function() {
				cs.createMenu($(this));
			});
		}
	}

	ConcreteAjaxSearch.prototype.setupCustomizeColumns = function() {
		var cs = this;
		cs.$element.on('click', 'a[data-search-toggle=customize]', function() {
			var url = $(this).attr('data-search-column-customize-url');
			$.fn.dialog.open({
				width: 480,
				height: 400,
				href: url,
				modal: true,
				title: ccmi18n.customizeSearch,
				onOpen: function() {
					ConcreteEvent.subscribe('AjaxFormSubmitSuccess', function(e, data) {
						cs.updateResults(data.response.result);
					});
				}
			});
			return false;
		});
	}

	/*
	 * Returns an array of selected result objects. These are not DOM objects, they are objects
	 * passed in through the options.result object.
	 */
	ConcreteAjaxSearch.prototype.getSelectedResults = function() {
		var my = this,
			$total = my.$element.find('tbody tr'),
			$selected = my.$element.find('.ccm-search-select-selected'),
			results = [];

		$selected.each(function() {
			var index = $total.index($(this));
			if (index > -1) {
				results.push(my.getResult().items[index]);
			}
		});

		return results;
	}

	ConcreteAjaxSearch.prototype.showMenu = function($element, $menu, event) {
		var concreteMenu = new ConcreteMenu($element, {
			menu: $menu,
			handle: 'none'
		});
		concreteMenu.show(event);
	}

	ConcreteAjaxSearch.prototype.handleSelectClick = function(event, $row) {
		var my = this;
		event.preventDefault();
		$row.removeClass('ccm-search-select-hover');
		if (event.shiftKey) {
			var $selected = my.$element.find('.ccm-search-select-selected');
			var index = my.$element.find('tbody tr').index($row);
			if (!$selected.length) {
				// If nothing is selected, we select everything from the beginning up to row.
				my.$element.find('tbody tr').slice(0, index + 1).removeClass().addClass('ccm-search-select-selected');
			} else {
				var selectedIndex = my.$element.find('tbody tr').index($selected.eq(0));
				if (selectedIndex > -1) {
					if (selectedIndex > index) {
						// we select from $row up to index.
						my.$element.find('tbody tr').slice(index, selectedIndex + 1).removeClass().addClass('ccm-search-select-selected');
					} else {
						// we select from selectedIndex up to row
						my.$element.find('tbody tr').slice(selectedIndex, index + 1).removeClass().addClass('ccm-search-select-selected');
					}
				}
			}
			ConcreteEvent.publish('SearchSelectItems', {
				'results': my.getSelectedResults()
			}, my.$element);

		} else {
			if (event.which == 3) {
				// right click
				// If the current item is not selected, we deselect everything and select it
				if (!$row.hasClass('ccm-search-select-selected')) {
					my.$element.find('.ccm-search-select-selected').removeClass();
					$row.addClass('ccm-search-select-selected');
				}

				var results = my.getSelectedResults();
				var $menu = my.getResultMenu(results);
				if ($menu) {
					my.showMenu($row, $menu, event);
				}

			} else {
				if ($row.hasClass('ccm-search-select-selected')) {
					$row.removeClass('ccm-search-select-selected');
				} else {
					$row.addClass('ccm-search-select-selected');
				}
				if (!event.metaKey) {
					my.$element.find('.ccm-search-select-selected').not($row).removeClass();
				}

			}
			ConcreteEvent.publish('SearchSelectItems', {
				'results': my.getSelectedResults()
			}, my.$element);

		}
	}

	ConcreteAjaxSearch.prototype.getResult = function() {
		return this.result;
	}

	ConcreteAjaxSearch.prototype.updateResults = function(result) {
		var cs = this,
			options = cs.options;

		cs.result = result;

		cs.$resultsTableHead.html(cs._templateSearchResultsTableHead({'columns': result.columns}));
		cs.$resultsTableBody.html(cs._templateSearchResultsTableBody({'items': result.items}));
		cs.$resultsPagination.html(cs._templateSearchResultsPagination({'paginationTemplate': result.paginationTemplate}));
		if (cs.$advancedFields) {
			cs.$advancedFields.html('');
			if (cs.$advancedFields.length) {
				$.each(result.fields, function(i, field) {
					cs.$advancedFields.append(cs._templateAdvancedSearchFieldRow({'field': field}));
				});
			}
		}
		if (options.selectMode == 'multiple') {
			// We enable item selection, click to select single, command click for
			// multiple, shift click for range
			cs.$element.find('tbody tr').on('contextmenu' +
				'', function(e) {
				e.preventDefault();
				return false;
			}).on('mouseover.concreteSearchResultItem', function() {
				$(this).addClass('ccm-search-select-hover');
			}).on('mouseout.concreteSearchResultItem', function() {
				$(this).removeClass('ccm-search-select-hover');
			}).on('mousedown.concreteSearchResultItem', function(e) {
				cs.handleSelectClick(e, $(this));
			});
		} else {
			cs.setupMenus(result);
		}
		if (options.onUpdateResults) {
			options.onUpdateResults(this);
		}
	}

	ConcreteAjaxSearch.prototype.setupAdvancedSearch = function() {
		var cs = this;
		cs.$advancedFields = cs.$element.find('div.ccm-search-fields-advanced');

		cs.$element.on('click', 'a[data-search-toggle=advanced]', function() {
			cs.$advancedFields.append(cs._templateAdvancedSearchFieldRow());
			return false;
		});
		cs.$element.on('change', 'select[data-search-field]', function() {
			var $content = $(this).parent().find('.ccm-search-field-content');
			$content.html('');
			var field = $(this).find(':selected').attr('data-search-field-url');
			if (field) {
				cs.ajaxUpdate(field, false, function(r) {
					_.each(r.assets.css, function(css) {
						ccm_addHeaderItem(css, 'CSS');
					});
					_.each(r.assets.javascript, function(javascript) {
						ccm_addHeaderItem(javascript, 'JAVASCRIPT');
					});
					$content.html(r.html);
				});
			}
		});
		cs.$element.on('click', 'a[data-search-remove=search-field]', function() {
			var $row = $(this).parent();
			$row.remove();
			return false;
		});


	}

	ConcreteAjaxSearch.prototype.setupSort = function() {
		var cs = this;
		this.$element.on('click', 'thead th > a', function() {
			cs.ajaxUpdate($(this).attr('href'));
			return false;
		});
	}

	ConcreteAjaxSearch.prototype.refreshResults = function() {
		var cs = this;
		cs.$element.find('form[data-search-form]').trigger('submit');
	}

	ConcreteAjaxSearch.prototype.setupSearch = function() {
		var cs = this;
		if (cs._templateSearchForm) {
			cs.$element.find('[data-search-element=wrapper]').html(cs._templateSearchForm());
		}
		$('form[data-search-form]').on('submit', function() {
			var data = $(this).serializeArray();
			data.push({'name': 'submitSearch', 'value': '1'});
			cs.ajaxUpdate($(this).attr('action'), data);
			return false;
		});
	}

	ConcreteAjaxSearch.prototype.handleSelectedBulkAction = function(value, type, $option, $items) {
		var cs = this,
			itemIDs = [];

		if ($items instanceof jQuery) {
			$.each($items, function(i, checkbox) {
				itemIDs.push({'name': cs.options.bulkParameterName + '[]', 'value': $(checkbox).val()});
			});
		} else {
			$.each($items, function(i, id) {
				itemIDs.push({'name': cs.options.bulkParameterName + '[]', 'value': id});
			});
		}

		if (type == 'dialog') {
			jQuery.fn.dialog.open({
				width: $option.attr('data-bulk-action-dialog-width'),
				height: $option.attr('data-bulk-action-dialog-height'),
				modal: true,
				href: $option.attr('data-bulk-action-url') + '?' + jQuery.param(itemIDs),
				title: $option.attr('data-bulk-action-title')
			});
		}

        if (type == 'ajax') {
            $.concreteAjax({
                url: $option.attr('data-bulk-action-url'),
                data: itemIDs,
                success: function(r) {
                    if (r.message) {
                        ConcreteAlert.notify({
                            'message': r.message,
                            'title': r.title
                        });
                    }
                }
            });
        }
		cs.publish('SearchBulkActionSelect', {value: value, option: $option, items: $items});
	}

	ConcreteAjaxSearch.prototype.publish = function(eventName, data) {
		var cs = this;
		ConcreteEvent.publish(eventName, data, cs);
	}

	ConcreteAjaxSearch.prototype.subscribe = function(eventName, callback) {
		var cs = this;
		ConcreteEvent.subscribe(eventName, callback, cs);
	}

	ConcreteAjaxSearch.prototype.setupBulkActions = function() {
		var cs = this;

		cs.$bulkActions = cs.$element.find('select[data-bulk-action]');
		// legacy bulk actions
		cs.$element.on('change', 'select[data-bulk-action]', function() {
			var $option = $(this).find('option:selected'),
				value = $option.val(),
				type = $option.attr('data-bulk-action-type');

			cs.handleSelectedBulkAction(value, type, $option, cs.$element.find('input[data-search-checkbox=individual]:checked'));
			cs.$element.find('option').eq(0).prop('selected', true);
		});
	}

	ConcreteAjaxSearch.prototype.setupPagination = function() {
		var cs = this;
		this.$element.on('click', 'ul.pagination a', function() {
			cs.ajaxUpdate($(this).attr('href'));
			return false;
		});
	}

	ConcreteAjaxSearch.prototype.getResultMenu = function(results) {
		var cs = this;
		if (results.length > 1 && cs.options.result.bulkMenus) {
			var propertyName = cs.options.result.bulkMenus.propertyName,
				menu = cs.options.result.bulkMenus.menu,
				type,
				currentType;

			$.each(results, function(i, result) {
				var propertyValue = result[propertyName];
				if (i == 0) {
					type = propertyValue;
				} else if (type != propertyValue) {
					type = null;
				}
			});
			if (type) {
				return $(menu);
			}
		} else if (results.length == 1) {
			var menu = results[0].treeNodeMenu;
			return $(menu);
		}
		return false;
	}

	ConcreteAjaxSearch.prototype.setupCheckboxes = function() {
		var cs = this;
		cs.$element.on('click', 'input[data-search-checkbox=select-all]', function() {
			cs.$element.find('input[data-search-checkbox=individual]').prop('checked', $(this).is(':checked')).trigger('change');
		});
		cs.$element.on('change', 'input[data-search-checkbox=individual]', function() {
			if (cs.$element.find('input[data-search-checkbox=individual]:checked').length) {
				cs.$bulkActions.prop('disabled', false);
			} else {
				cs.$bulkActions.prop('disabled', true);
			}
		});

		ConcreteEvent.subscribe('SearchSelectItems', function(e, data) {
			var $menu = cs.getResultMenu(data.results);
			if ($menu) {
				cs.$element.find('button.btn-menu-launcher').prop('disabled', false);
			} else {
				cs.$element.find('button.btn-menu-launcher').prop('disabled', true);
			}
		}, cs.$element);


	}

	// jQuery Plugin
	$.fn.concreteAjaxSearch = function(options) {
		return new ConcreteAjaxSearch(this, options);
	}

	global.ConcreteAjaxSearch = ConcreteAjaxSearch;

}(this, $);
