;(function(global, $) {
    'use strict'

    function ConcreteSearchResultsTable($element, options) {
        var my = this
        options = options || {}
        options = $.extend({}, options)

        my.$element = $element
        my.options = options

        my.$element.on('click', 'input[data-search-checkbox=select-all]', function() {
            my.$element.find('input[data-search-checkbox=individual]').
            prop('checked', $(this).is(':checked')).trigger('change')
        })
        my.$element.on('change', 'input[data-search-checkbox=individual]', function() {
            if (my.$element.find('input[data-search-checkbox=individual]:checked').length) {
                //cs.$bulkActions.prop('disabled', false);
            } else {
                //cs.$bulkActions.prop('disabled', true);
            }
        })

        /*
        ConcreteEvent.subscribe('SearchSelectItems', function(e, data) {
            var $menu = cs.getResultMenu(data.results);
            if ($menu) {
                cs.$element.find('button.btn-menu-launcher').prop('disabled', false);
            } else {
                cs.$element.find('button.btn-menu-launcher').prop('disabled', true);
            }
        }, cs.$element);*/

    }

    // jQuery Plugin
    $.fn.concreteSearchResultsTable = function (options) {
        return $.each($(this), function (i, obj) {
            new ConcreteSearchResultsTable($(this), options)
        })
    }

    global.ConcreteSearchResultsTable = ConcreteSearchResultsTable
})(window, jQuery);
