
;(function(global, $) {
    'use strict'

    function ConcreteGroupManagerTable($element, options) {
        var my = this
        options = options || {}
        options = $.extend({
            bulkParameterName: 'item',
            folderID: 0,
            highlightFolders: []
        }, options)

        my.$element = $element
        my.options = options

        ConcreteSearchResultsTable.call(my, $element, options)

        my.setupBulkActions()
        my.disableSelectAllOnInvalidNodeTypeSelection()
        my.setupFolderActions()
        my.subscribeFolderEvents()
    }

    ConcreteGroupManagerTable.prototype = Object.create(ConcreteSearchResultsTable.prototype)

    ConcreteGroupManagerTable.prototype.subscribeFolderEvents = function() {
        ConcreteEvent.subscribe('ConcreteTreeUpdateTreeNode', function(e, r) {
            window.location.reload()
        });

        ConcreteEvent.subscribe('ConcreteTreeAddTreeNode', function(e, r) {
            window.location.reload()
        });
    }

    ConcreteGroupManagerTable.prototype.disableSelectAllOnInvalidNodeTypeSelection = function() {
        var my = this
        my.$element.on('change', 'input[data-search-checkbox=individual]', function() {
            var deactivate = false,
                $checked = my.$element.find(my.checkedCheckboxes)
            if ($checked.length) {
                $checked.each(function () {
                    if ($(this).data('node-type') != 'group') {
                        deactivate = true;
                    }
                });
                if (deactivate) {
                    my.$selectAllDropdownButton.prop('disabled', true)
                } else {
                    my.$selectAllDropdownButton.prop('disabled', false)
                }
            }
        })
    }

    ConcreteGroupManagerTable.prototype.setupFolderActions = function() {
        var my = this
        ConcreteEvent.subscribe('GroupManagerJumpToFolder.concreteGroupManagerTable', function(e, r) {
            var url = CCM_DISPATCHER_FILENAME + '/dashboard/users/groups/folder/' + r.folderID
            window.location.href = url
        });

    };

    ConcreteGroupManagerTable.prototype.activateSearchResultMenus = function() {
        ConcreteTree.activateMenu(this.$searchResultMenu);
    }

    ConcreteGroupManagerTable.prototype.handleSelectedBulkAction = function(value, type, $option, ids) {
        ConcreteSearchResultsTable.prototype.handleSelectedBulkAction.call(this, value, type, $option, ids)
    }

    // jQuery Plugin
    $.fn.concreteGroupManagerTable = function (options) {
        return $.each($(this), function (i, obj) {
            new ConcreteGroupManagerTable($(this), options)
        })
    }

    global.ConcreteSearchResultsTable = ConcreteSearchResultsTable
})(window, jQuery);
