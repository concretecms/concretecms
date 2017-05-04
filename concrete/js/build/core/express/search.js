/**
 * block ajax
 */

!function(global, $) {
    'use strict';

    function ConcreteExpressEntryAjaxSearch($element, options) {
        'use strict';
        var my = this;
        options = $.extend({

        }, options);

        ConcreteAjaxSearch.call(my, $element, options);

    }

    ConcreteExpressEntryAjaxSearch.prototype = Object.create(ConcreteAjaxSearch.prototype);


    /**
     * Static Methods
     */
    ConcreteExpressEntryAjaxSearch.launchDialog = function(entityID, callback) {
        var w = $(window).width() - 53;

        $.fn.dialog.open({
            width: w,
            height: '100%',
            href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/express/entry/search?exEntityID=' + entityID,
            modal: true,
            title: ccmi18n_express.entriesTitle,
            onClose: function() {
                ConcreteEvent.fire('ExpressEntrySelectorClose');
            },
            onOpen: function() {
                ConcreteEvent.unsubscribe('SelectExpressEntry');
                ConcreteEvent.subscribe('SelectExpressEntry', function(e, data) {
                    jQuery.fn.dialog.closeTop();
                    callback(data);
                });
            }
        });
    };

    ConcreteExpressEntryAjaxSearch.getEntryDetails = function(exEntryID, callback) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: CCM_DISPATCHER_FILENAME + '/ccm/system/express/entry/get_json',
            data: {'exEntryID': exEntryID},
            error: function(r) {
                ConcreteAlert.dialog(ccmi18n.error, r.responseText);
            },
            success: function(r) {
                callback(r);
            }
        });
    };

    // jQuery Plugin
    $.fn.concreteExpressPageAjaxSearch = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteExpressEntryAjaxSearch($(this), options);
        });
    };

    global.ConcreteExpressEntryAjaxSearch = ConcreteExpressEntryAjaxSearch;

}(window, $);
