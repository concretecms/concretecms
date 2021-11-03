
;(function(global, $) {
    'use strict'

    function ConcretePageSearchTable($element, options) {
        var my = this
        options = options || {}
        options = $.extend({
        }, options)

        my.$element = $element
        my.options = options

        ConcreteSearchResultsTable.call(my, $element, options)
    }

    ConcretePageSearchTable.prototype = Object.create(ConcreteSearchResultsTable.prototype)

    ConcretePageSearchTable.prototype.handleSelectedBulkAction = function(value, type, $option, ids) {
        if (value == 'move-copy') {
            var my = this
            var itemIDs = my.getSelectedResultIDs()

            window.ConcretePageAjaxSearch.launchDialog(
                function(data) {
                    var url = CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/drag_request?origCID=' + itemIDs.join(',') + '&destCID=' + data.cID
                    $.fn.dialog.open({
                        width: 520,
                        height: 'auto',
                        href: url,
                        title: ccmi18n_sitemap.moveCopyPage,
                    })
                },
                {
                    askIncludeSystemPages: true,
                }
            )
        } else {
            ConcreteSearchResultsTable.prototype.handleSelectedBulkAction.call(this, value, type, $option, ids)
        }
    }

    // jQuery Plugin
    $.fn.concretePageSearchTable = function (options) {
        return $.each($(this), function (i, obj) {
            new ConcretePageSearchTable($(this), options)
        })
    }

    global.ConcretePageSearchTable = ConcretePageSearchTable
})(window, jQuery);
