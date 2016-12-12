!function(global, $) {
    'use strict';

    function ConcreteExpressEntryList(options) {
        options = options || {};
        options = $.extend({
            'bID': 0,
        }, options);

        this.options = options;
        this.setupAdvancedSearch();
    }

    ConcreteExpressEntryList.prototype.setupAdvancedSearch = function() {
        $('a[data-express-entry-list-advanced-search]').on('click', function(e) {
            e.preventDefault();
            var bID = $(this).attr('data-express-entry-list-advanced-search');
            var $details = $('div[data-express-entry-list-advanced-search-fields=' + bID + ']');
            if ($details.is(':visible')) {
                $(this).removeClass('ccm-block-express-entry-list-advanced-search-open');
                $details.find('input[name=advancedSearchDisplayed]').val('');
                $details.hide();
            } else {
                $(this).addClass('ccm-block-express-entry-list-advanced-search-open');
                $details.find('input[name=advancedSearchDisplayed]').val(1);
                $details.show();
            }
        });
    }

    // jQuery Plugin
    $.concreteExpressEntryList = function(options) {
        return new ConcreteExpressEntryList(options);
    }

}(this, $);