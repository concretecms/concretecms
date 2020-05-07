/* eslint-disable no-new, no-unused-vars, camelcase */
/* global _, ConcreteAssetLoader */

;(function(global, $) {
    'use strict'

    function ConcreteAdvancedSearchLauncher($element) {
        var my = this

        my.$element = $element
        my.$element.on('click', function() {
            var url = $(this).data('advanced-search-dialog-url')
            if (my.$element.data('advanced-search-query')) {
                var query = $element.attr('data-advanced-search-query')
                url = url + '?query=' + query;
            }
            jQuery.fn.dialog.open({
                href: url,
                modal: true,
                title: ccmi18n.advancedSearch,
                width: 500,
                height: 500,
                onOpen: function() {
                    $('div[data-component=search-field-selector]').concreteSearchFieldSelector()
                }
            });
        });

    }

    // jQuery Plugin
    $.fn.concreteAdvancedSearchLauncher = function () {
        return $.each($(this), function (i, obj) {
            new ConcreteAdvancedSearchLauncher($(this))
        })
    }

    global.ConcreteAdvancedSearchLauncher = ConcreteAdvancedSearchLauncher
})(window, jQuery); // eslint-disable-line semi
