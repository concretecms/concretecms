/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global _, ccmi18n, ccmi18n_filemanager, ccm_triggerProgressiveOperation, ConcreteAlert, ConcreteAssetLoader, ConcreteEvent, ConcreteMenu */

/* Base search class for AJAX searching */
;(function(global, $) {
	'use strict';

	function ConcreteAjaxSearch($element, options) {
		options = options || {};
		options = $.extend({
			'result': {},
			'onLoad': false,
			'onUpdateResults': false,
            'bulkParameterName': 'item',
			'selectMode': false,
			'appendToOuterDialog': false,
			'searchMethod': 'get'
		}, options);
		this.$element = $element;
		this.$results = $element.find('div[data-search-element=results]');
		this.$resultsTableBody = this.$results.find('tbody');
		this.$resultsTableHead = this.$results.find('thead');
		this.$resultsPagination = this.$results.find('div.ccm-search-results-pagination');
		this.$menuTemplate = $element.find('script[data-template=search-results-menu]');
		this.$searchFieldRowTemplate = $element.find('script[data-template=search-field-row]');

		this.$headerSearch = $element.find('div[data-header]');
		this.$headerSearchInput = $element.find('div[data-header] input');
		this.$advancedSearchButton = $element.find('a[data-launch-dialog=advanced-search]');
		this.$resetSearchButton = $element.find('a[data-button-action=clear-search]');

		this.options = options;

		if ($element.find('script[data-template=search-form]').length) {
			this._templateSearchForm = _.template($element.find('script[data-template=search-form]').html());
		}
		if ($element.find('script[data-template=search-results-table-head]').length) {
			this._templateSearchResultsTableHead = _.template($element.find('script[data-template=search-results-table-head]').html());
		}
		if ($element.find('script[data-template=search-results-table-body]').length) {
			this._templateSearchResultsTableBody = _.template($element.find('script[data-template=search-results-table-body]').html());
		}
		if ($element.find('script[data-template=search-results-pagination]').length) {
			this._templateSearchResultsPagination = _.template($element.find('script[data-template=search-results-pagination]').html());
		}
		if (this.$menuTemplate.length) {
			this._templateSearchResultsMenu = _.template(this.$menuTemplate.html());
		}
		if (this.$searchFieldRowTemplate.length) {
			this._templateAdvancedSearchFieldRow = _.template(this.$searchFieldRowTemplate.html());
		}

		this.setupSearch();
		this.setupCheckboxes();
		this.setupSort();
		this.setupPagination();
        this.setupSelectize();
		this.setupAdvancedSearch();
		this.setupCustomizeColumns();
		this.updateResults(options.result);

		if (options.onLoad) {
			options.onLoad(this);
		}
	}

	ConcreteAjaxSearch.prototype.setupResetButton = function (result) {
		var my = this,
			advancedSearchText;

		if (result.query || (result.folder && result.folder.treeNodeTypeHandle === 'search_preset')) {
			advancedSearchText = ccmi18n_filemanager.edit;
		} else {
			advancedSearchText = ccmi18n.advanced;
		}

		my.$advancedSearchButton.html(advancedSearchText);

		if (result.query && result.folder && result.folder.treeNodeTypeHandle !== 'search_preset') {
			my.$headerSearch.find('div.btn-group').hide(); // hide any fancy button groups we've added here.
			my.$headerSearchInput.prop('disabled', true);
			my.$headerSearchInput.attr('placeholder', '');
			my.$resetSearchButton.show();
		}
	};

	ConcreteAjaxSearch.prototype.ajaxUpdate = function(url, data, callback) {
		var cs = this;
		$.concreteAjax({
			url: url,
			data: data,
			method: cs.options.searchMethod,
			success: function(r) {
				cs.scrollToTop();
				if (!callback) {
					cs.updateResults(r);
				} else {
					callback(r);
				}
			}
		});
	};

	ConcreteAjaxSearch.prototype.scrollToTop = function() {
		var cs = this,
			$dialog = cs.$element.closest(".ui-dialog-content");

		if ($dialog.length) {
			$dialog.scrollTop(0);
		} else {
			window.scrollTo(0, 0);
		}
	};

	ConcreteAjaxSearch.prototype.getSearchData = function() {
		var cs = this;
		var $form = cs.$element.find('form[data-search-form]');
		var data = $form.serializeArray();
		return data;
	};

	ConcreteAjaxSearch.prototype.setupSelectize = function() {
        var selects = this.$element.find('.selectize-select');
        if (selects.length) {
        	selects.selectize({
        		plugins: ['remove_button']
        	});
        }
    };



	/**
	 * The legacy create menu function for simple list items without multiple selection
	 * @param $selector
     */
    ConcreteAjaxSearch.prototype.createMenu = function($selector) {
		$selector.concreteMenu({
			'menu': $('[data-search-menu=' + $selector.attr('data-launch-search-menu') + ']')
		});
	};

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
	};

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
	};

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
	};

	ConcreteAjaxSearch.prototype.showMenu = function($element, $menu, event) {
		var concreteMenu = new ConcreteMenu($element, {
			menu: $menu,
			handle: 'none'
		});
		concreteMenu.show(event);
	};

	ConcreteAjaxSearch.prototype.handleSelectClick = function(event, $row) {
		var my = this;
		event.preventDefault();
		$row.removeClass('ccm-search-select-hover');
		var $selected = my.$element.find('.ccm-search-select-selected');
		if (event.shiftKey) {
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
				my.handleMenuClick(event, $row);
			} else {
				if (!event.metaKey) {
					$selected.removeClass('ccm-search-select-selected');
				}
				if (!$row.hasClass('ccm-search-select-selected')) {
					// Select the row
					$row.addClass('ccm-search-select-selected');
				} else {
					// Unselect the row
					$row.removeClass('ccm-search-select-selected');
				}
			}

			ConcreteEvent.publish('SearchSelectItems', {
				'results': my.getSelectedResults()
			}, my.$element);

		}
	};

    ConcreteAjaxSearch.prototype.handleMenuClick = function(event, $row) {
        // right click
        // If the current item is not selected, we deselect everything and select it
        if (!$row.hasClass('ccm-search-select-selected')) {
            this.$element.find('.ccm-search-select-selected').removeClass();
            $row.addClass('ccm-search-select-selected');
        }

        var results = this.getSelectedResults();
        var $menu = this.getResultMenu(results);
        if ($menu) {
            this.showMenu($row, $menu, event);
        }
	};

	ConcreteAjaxSearch.prototype.getResult = function() {
		return this.result;
	};

	ConcreteAjaxSearch.prototype.updateResults = function(result) {
		var cs = this,
			options = cs.options,
			touchTimer = null,
			touchEvent;

		cs.result = result;

		if (result) {
			if (cs.$resultsTableHead.length) {
				cs.$resultsTableHead.html(cs._templateSearchResultsTableHead({'columns': result.columns}));
			}
			if (cs.$resultsTableBody.length) {
				cs.$resultsTableBody.html(cs._templateSearchResultsTableBody({'items': result.items}));
			}
			if (cs.$resultsPagination.length) {
				cs.$resultsPagination.html(cs._templateSearchResultsPagination({'paginationTemplate': result.paginationTemplate}));
			}
			if (cs.$advancedFields) {
				cs.$advancedFields.html('');
				if (cs.$advancedFields.length) {
					$.each(result.fields, function(i, field) {
						cs.$advancedFields.append(cs._templateAdvancedSearchFieldRow({'field': field}));
					});
				}
			}
	
			cs.setupResetButton(result);
		}

		if (options.selectMode == 'multiple') {
			// We enable item selection, click to select single, command click for
			// multiple, shift click for range
			cs.$element.find('tbody tr').on('contextmenu touchstart touchend' +
				'', function(e) {
				e.preventDefault();
				return false;
			}).on('mouseover.concreteSearchResultItem', function() {
				if (cs.hoverIsEnabled($(this))) {
					$(this).addClass('ccm-search-select-hover');
				}
			}).on('mouseout.concreteSearchResultItem', function() {
				if (cs.hoverIsEnabled($(this))) {
					$(this).removeClass('ccm-search-select-hover');
				}
			}).on('mousedown.concreteSearchResultItem', function(e) {
				cs.handleSelectClick(e, $(this));
			}).on('mouseup.concreteSearchResultItem', function(e) {
				if (!e.metaKey && !e.ctrlKey && !e.shiftKey) {
					cs.$element.find('.ccm-search-select-selected').not($(this)).removeClass();
				}
			}).on('touchstart.concreteSearchResultItem', function(e) {
				var me = $(this);
				touchEvent = e;
				touchTimer = setTimeout(function() {
					cs.handleSelectClick(e, me);
					touchTimer = null;
				}, 1000);
			}).on('touchend.concreteSearchResultItem', function(e) {
				if (touchTimer) {
					clearTimeout(touchTimer);
					touchTimer = null;
                    cs.handleMenuClick(touchEvent, $(this));
				}
				touchEvent = null;
			});

		} else {
			cs.setupMenus(result);
		}
		cs.setupBulkActions();
		if (options.onUpdateResults) {
			options.onUpdateResults(this);
		}
	};

	ConcreteAjaxSearch.prototype.hoverIsEnabled = function($element) {
		return true;
	};

	ConcreteAjaxSearch.prototype.setupAdvancedSearch = function() {
		var cs = this;
		// OLD SEARCH

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
						ConcreteAssetLoader.loadCSS(css);
					});
					_.each(r.assets.javascript, function(javascript) {
						ConcreteAssetLoader.loadJavaScript(javascript);
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

		// NEW SEARCH
		cs.$advancedSearchButton.on('click', function() {

			// remove previous save-search-preset dialog
			$('div[data-dialog=save-search-preset]').remove();
			var url = $(this).attr('href');
			$.fn.dialog.open({
				width: 620,
				height: 500,
				href: url,
				modal: true,
				title: ccmi18n.search,
				onOpen: function() {
					cs.setupAdvancedSearchDialog();
				}
			});
			return false;
		});

	};

	ConcreteAjaxSearch.prototype.setupAdvancedSearchDialog = function() {
		var my = this;
		var $container = $('div[data-container=search-fields]');
		var renderFieldRowTemplate = _.template(
			$('script[data-template=search-field-row]').html()
		);
		var defaultQuery = $('script[data-template=default-query]').html();
		if (defaultQuery) {
			defaultQuery = JSON.parse(defaultQuery);
		}
		$('button[data-button-action=add-field]').on('click', function() {
			$container.append(
				renderFieldRowTemplate()
			);
		});

		if (my.result && my.result.query) {
			$.each(my.result.query.fields, function(i, field) {
				$container.append(
					renderFieldRowTemplate({'field': field})
				);
			});
		} else if (defaultQuery) {
			$.each(defaultQuery.fields, function(i, field) {
				$container.append(
					renderFieldRowTemplate({'field': field})
				);
			});
		}

		var selects = $container.find('select.selectize-select');
		if (selects.length) {
			selects.selectize({
				plugins: ['remove_button']
	    	});
		}
		$container.on('change', 'select.ccm-search-choose-field', function() {
			var key = $(this).val();
			var $content = $(this).parent().find('div.form-group');
			if (key) {
				$.concreteAjax({
					url: $(this).attr('data-action'),
					data: {
						'field': key
					},
					success: function(r) {
						_.each(r.assets.css, function(css) {
							ConcreteAssetLoader.loadCSS(css);
						});
						_.each(r.assets.javascript, function(javascript) {
							ConcreteAssetLoader.loadJavaScript(javascript);
						});
						$content.html(r.element);
						var selects = $content.find('select.selectize-select');
						if (selects.length) {
							selects.selectize({
								plugins: ['remove_button']
							});
						}
					}
				});
			}
		});
		$container.on('click', 'a[data-search-remove=search-field]', function(e) {
			e.preventDefault();
			var $row = $(this).parent();
			$row.remove();
		});

		$('[data-search-preset-id]').on('click', function(e) {
			e.preventDefault();
			if (!$(e.target).is('button') && $(this).data('action')) {
				$.fn.dialog.closeAll();
				my.ajaxUpdate($(this).data('action'));
				my.$resetSearchButton.show();
				my.$headerSearch.find('div.btn-group').hide();
				my.$headerSearchInput.prop('disabled', true).val('');
				my.$headerSearchInput.attr('placeholder', '');
			}
		});

		$('.ccm-search-presets-table tbody tr').on('mouseover', function() {
			$(this).addClass('ccm-search-select-hover');
		}).on('mouseout', function() {
			$(this).removeClass('ccm-search-select-hover');
		});

		$('button[data-button-action=save-search-preset]').on('click.saveSearchPreset', function() {
			$.fn.dialog.open({
				element: 'div[data-dialog=save-search-preset]:first',
				modal: true,
				width: 320,
				title: 'Save Preset',
				height: 'auto'
			});
		});

		var $presetForm = $('form[data-form=save-preset]');
		var $form = $('form[data-form=advanced-search]');
		$('button[data-button-action=save-search-preset-submit]').on('click.saveSearchPresetSubmit', function() {
			var $presetForm = $('form[data-form=save-preset]');
			$presetForm.trigger('submit');
		});

		$presetForm.on('submit', function() {
			var formData = $form.serializeArray();
			formData = formData.concat($presetForm.serializeArray());
			$.concreteAjax({
				data: formData,
				url: $presetForm.attr('action'),
				success: function(r) {
					$.fn.dialog.closeAll();
					ConcreteEvent.publish('SavedSearchCreated', {search: r});
				}
			});
			return false;
		});

		$('button[data-button-action=edit-search-preset], button[data-button-action=delete-search-preset]').on('click', function(e) {
			e.preventDefault();
			var url = $(this).attr('data-tree-action-url'),
				title = $(this).attr('dialog-title');

			$.fn.dialog.open({
				title: title,
				href: url,
				width: 550,
				modal: true,
				height: 'auto'
			});
		});

		ConcreteEvent.unsubscribe('SavedSearchDeleted');
		ConcreteEvent.subscribe('SavedSearchDeleted', function() {
			$.fn.dialog.closeAll();
			my.ajaxUpdate(my.$resetSearchButton.data('button-action-url'));
		});

		ConcreteEvent.unsubscribe('SavedSearchUpdated');
		ConcreteEvent.subscribe('SavedSearchUpdated', function(e, data) {
			$.fn.dialog.closeAll();
			if (data.preset && data.preset.actionURL) {
				my.ajaxUpdate(data.preset.actionURL);
			}
		});

		my.setupSearch();
	};

	ConcreteAjaxSearch.prototype.setupSort = function() {
		var cs = this;
		this.$element.on('click', 'thead th > a', function() {
			cs.ajaxUpdate($(this).attr('href'));
			return false;
		});
	};

	ConcreteAjaxSearch.prototype.refreshResults = function() {
		var cs = this;
		cs.$element.find('form[data-search-form]').trigger('submit');
	};

	ConcreteAjaxSearch.prototype.setupSearch = function() {
		// OLD SEARCH
		var cs = this;
		if (cs._templateSearchForm) {
			cs.$element.find('[data-search-element=wrapper]').html(cs._templateSearchForm());
		}
		cs.$element.on('submit', 'form[data-search-form]', function() {
			var data = $(this).serializeArray();
			data.push({'name': 'submitSearch', 'value': '1'});
			cs.ajaxUpdate($(this).attr('action'), data);
			return false;
		});

		// NEW SEARCH
		cs.$element.find('div[data-header] form').on('submit', function() {
			var data = $(this).serializeArray();
			data.push({'name': 'submitSearch', 'value': '1'});
			cs.ajaxUpdate($(this).attr('action'), data);
			cs.$advancedSearchButton.hide();
			cs.$resetSearchButton.addClass('ccm-header-reset-search-right').show();

			return false;
		});

		// If we're calling this from a dialog, we move it out to the top of the dialog so it can display properly
		if (cs.options.appendToOuterDialog) {
			var $container = cs.$element.closest('div.ui-dialog');
			if ($container.length) {
				cs.$element.find('div[data-header]').insertBefore($container.find('.ui-dialog-content'));
			}
		}

		$('form[data-form=advanced-search]').concreteAjaxForm({
			'success': function(r) {
				cs.updateResults(r);
				$.fn.dialog.closeTop();
				cs.$advancedSearchButton.html(ccmi18n_filemanager.edit);
				cs.$resetSearchButton.show();
				cs.$headerSearch.find('div.btn-group').hide(); // hide any fancy button groups we've added here.
				cs.$headerSearchInput.prop('disabled', true).val('');
				cs.$headerSearchInput.attr('placeholder', '');
			}
		});
		cs.$resetSearchButton.on('click', function(e) {
			cs.$element.find('div[data-header] input').val('');
			e.preventDefault();
			$.concreteAjax({
				url: $(this).attr('data-button-action-url'),
				success: function(r) {
					cs.updateResults(r);
					cs.$headerSearch.find('div.btn-group').show();
					cs.$headerSearchInput.prop('disabled', false);
					cs.$headerSearchInput.attr('placeholder', ccmi18n.search);
					cs.$advancedSearchButton.html(ccmi18n.advanced).show();
					cs.$resetSearchButton.removeClass('ccm-header-reset-search-right').hide();
				}
			});
		});
	};

	ConcreteAjaxSearch.prototype.handleSelectedBulkAction = function(value, type, $option, $items) {
		var cs = this,
			itemIDs = [];

		if ($items instanceof $) {
			$.each($items, function(i, checkbox) {
				itemIDs.push({'name': cs.options.bulkParameterName + '[]', 'value': $(checkbox).val()});
			});
		} else {
			$.each($items, function(i, id) {
				itemIDs.push({'name': cs.options.bulkParameterName + '[]', 'value': id});
			});
		}

		if (type == 'dialog') {
			$.fn.dialog.open({
				width: $option.attr('data-bulk-action-dialog-width'),
				height: $option.attr('data-bulk-action-dialog-height'),
				modal: true,
				href: $option.attr('data-bulk-action-url') + '?' + $.param(itemIDs),
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

		if (type == 'progressive') {
			ccm_triggerProgressiveOperation($option.attr('data-bulk-action-url'), itemIDs,	$option.attr('data-bulk-action-title'), function() {
				cs.refreshResults();
			});
		}
		cs.publish('SearchBulkActionSelect', {value: value, option: $option, items: $items});
	};

	ConcreteAjaxSearch.prototype.publish = function(eventName, data) {
		var cs = this;
		ConcreteEvent.publish(eventName, data, cs);
	};

	ConcreteAjaxSearch.prototype.subscribe = function(eventName, callback) {
		var cs = this;
		ConcreteEvent.subscribe(eventName, callback, cs);
	};

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
	};

	ConcreteAjaxSearch.prototype.setupPagination = function() {
		var cs = this;
		this.$element.on('click', 'div.ccm-search-results-pagination a:not([disabled])', function() {
			cs.ajaxUpdate($(this).attr('href'));
			return false;
		});
	};

	ConcreteAjaxSearch.prototype.getResultMenu = function(results) {
		var cs = this, menu;
		if (results.length > 1 && cs.options.result.bulkMenus) {
			var propertyName = cs.options.result.bulkMenus.propertyName,
				type;
			menu = cs.options.result.bulkMenus.menu;
			$.each(results, function(i, result) {
				var propertyValue = result[propertyName];
				if (i == 0) {
					type = propertyValue;
				} else if (type != propertyValue) {
					type = null;
				}
			});
			if (type && type == cs.options.result.bulkMenus.propertyValue) {
				return $(menu);
			}
		} else if (results.length == 1) {
			menu = results[0].treeNodeMenu;
			return $(menu);
		}
		return false;
	};

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


	};

	// jQuery Plugin
	$.fn.concreteAjaxSearch = function(options) {
		return new ConcreteAjaxSearch(this, options);
	};

	global.ConcreteAjaxSearch = ConcreteAjaxSearch;

})(window, jQuery);
