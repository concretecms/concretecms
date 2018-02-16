/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global _, ccmi18n, ccmi18n_express, ConcreteExpressEntryAjaxSearch */

;(function(global, $) {
    'use strict';

    function ConcreteExpressEntrySelector($element, options) {
        var my = this;
        options = $.extend({
            'chooseText': ccmi18n_express.chooseEntry,
            'loadingText': ccmi18n.loadingText,
            'inputName': 'entryID',
            'entityID': false,
            'entryID': 0
        }, options);

        my.$element = $element;
        my.options = options;
        my._chooseTemplate = _.template(my.chooseTemplate, {'options': my.options});
        my._loadingTemplate = _.template(my.loadingTemplate);
        my._entryLoadedTemplate = _.template(my.entryLoadedTemplate);

        my.$element.append(my._chooseTemplate);
        my.$element.on('click', 'a[data-express-entry-selector-link=choose]', function(e) {
            e.preventDefault();
            ConcreteExpressEntryAjaxSearch.launchDialog(options.entityID, function(data) {
                my.loadEntry(data.exEntryID);
            });
        });


        if (my.options.exEntryID) {
            my.loadEntry(my.options.exEntryID);
        }
    }

    ConcreteExpressEntrySelector.prototype = {


        chooseTemplate: '<div class="ccm-item-selector">' +
            '<input type="hidden" name="<%=options.inputName%>" value="0" /><a href="#" data-express-entry-selector-link="choose"><%=options.chooseText%></a></div>',
        loadingTemplate: '<div class="ccm-item-selector"><div class="ccm-item-selector-choose"><input type="hidden" name="<%=options.inputName%>" value="<%=exEntryID%>"><i class="fa fa-spin fa-spinner"></i> <%=options.loadingText%></div></div>',
        entryLoadedTemplate: '<div class="ccm-item-selector"><div class="ccm-item-selector-item-selected">' +
            '<input type="hidden" name="<%=inputName%>" value="<%=entry.exEntryID%>" />' +
            '<a data-express-entry-selector-action="clear" href="#" class="ccm-item-selector-clear"><i class="fa fa-close"></i></a>' +
            '<div class="ccm-item-selector-item-selected-title"><%=entry.label%></div>' +
            '</div></div>',

        loadEntry: function(exEntryID) {
            var my = this;
            my.$element.html(my._loadingTemplate({'options': my.options, 'exEntryID': exEntryID}));
            ConcreteExpressEntryAjaxSearch.getEntryDetails(exEntryID, function(r) {
                var entry = r.entries[0];
                my.$element.html(my._entryLoadedTemplate({'inputName': my.options.inputName, 'entry': entry}));
                my.$element.on('click', 'a[data-express-entry-selector-action=clear]', function(e) {
                    e.preventDefault();
                    my.$element.html(my._chooseTemplate);
                });
            });
        }
    };

    // jQuery Plugin
    $.fn.concreteExpressEntrySelector = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteExpressEntrySelector($(this), options);
        });
    };

    global.ConcreteExpressEntrySelector = ConcreteExpressEntrySelector;

})(this, jQuery);
