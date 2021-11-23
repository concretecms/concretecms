;(function(global, $) {
    'use strict'

    function ConcreteSearchResultsTable($element, options) {
        var my = this
        options = options || {}
        options = $.extend({
            bulkParameterName: 'item',
        }, options)

        my.$element = $element
        my.$selectAll = my.$element.find('input[data-search-checkbox=select-all]')
        my.$selectAllDropdownButton = my.$element.find('[data-search-checkbox-button=dropdown]');
        my.$selectAllDropdownMenu = my.$element.find('.dropdown-menu');
        my.$searchResultMenu = my.$element.find('[data-menu=search-result]');
        my.checkedCheckboxes = '[data-search-checkbox=individual]:checked';
        my.options = options

        my.activateSelectAllCheckbox();
        my.activateIndividualCheckboxes();
        my.activateSearchResultMenus();
    }

    ConcreteSearchResultsTable.prototype.activateSelectAllCheckbox = function() {
        var my = this
        my.$selectAll.on('click', function(e) {
            my.$element.find('input[data-search-checkbox=individual]').
            prop('checked', $(this).is(':checked')).trigger('change')
        })
    }

    ConcreteSearchResultsTable.prototype.activateIndividualCheckboxes = function() {
        var my = this
        my.$element.on('change', 'input[data-search-checkbox=individual]', function() {
            if (my.$element.find(my.checkedCheckboxes).length) {
                my.$selectAllDropdownButton.prop('disabled', false)
            } else {
                my.$selectAllDropdownButton.prop('disabled', true)
            }
        });
    }

    ConcreteSearchResultsTable.prototype.getSelectedResultIDs = function() {
        var my = this,
            ids = [],
            $checked = my.$element.find(my.checkedCheckboxes)

        $checked.each(function() {
            ids.push($(this).data('item-id'));
        });
        return ids;
    }

    ConcreteSearchResultsTable.prototype.setupBulkActions = function() {
        var my = this

        my.$selectAllDropdownMenu.find('a').on('click.concreteSearchResultBulkAction', function (e) {
            var value = $(this).attr('data-bulk-action')
            var type = $(this).attr('data-bulk-action-type')
            my.handleSelectedBulkAction(value, type, $(this), my.getSelectedResultIDs())
        })
    }

    ConcreteSearchResultsTable.prototype.activateSearchResultMenus = function() {
        return;
    }

    ConcreteSearchResultsTable.prototype.handleSelectedBulkAction = function(value, type, $option, $items) {
        var my = this
        var itemIDs = []

        if ($items instanceof $) {
            $.each($items, function(i, checkbox) {
                itemIDs.push({ name: my.options.bulkParameterName + '[]', value: $(checkbox).val() })
            })
        } else {
            $.each($items, function(i, id) {
                itemIDs.push({ name: my.options.bulkParameterName + '[]', value: id })
            })
        }

        if (type == 'dialog') {
            $.fn.dialog.open({
                width: $option.attr('data-bulk-action-dialog-width'),
                height: $option.attr('data-bulk-action-dialog-height'),
                modal: true,
                href: $option.attr('data-bulk-action-url') + '?' + $.param(itemIDs),
                title: $option.attr('data-bulk-action-title')
            })
        }

        if (type == 'ajax') {
            $.concreteAjax({
                url: $option.attr('data-bulk-action-url'),
                data: itemIDs,
                success: function(r) {
                    if (r.message) {
                        ConcreteAlert.notify({
                            message: r.message,
                            title: r.title
                        })
                    }
                }
            })
        }

        if (type == 'progressive') {
            new ConcreteProgressiveOperation({
                url: $option.attr('data-bulk-action-url'),
                data: itemIDs,
                title: $option.attr('data-bulk-action-title'),
                onComplete: function() {

                }
            })
        }
    }


    // jQuery Plugin
    $.fn.concreteSearchResultsTable = function (options) {
        return $.each($(this), function (i, obj) {
            new ConcreteSearchResultsTable($(this), options)
        })
    }

    global.ConcreteSearchResultsTable = ConcreteSearchResultsTable
})(window, jQuery);
