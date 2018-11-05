/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global _, ccmi18n, CCM_DISPATCHER_FILENAME, ConcreteAlert, ConcreteEvent */

;(function(global, $) {
    'use strict';

    function ConcreteCalendarEventSelector($element, options) {
        var my = this;
        options = $.extend({
            'chooseText': 'Choose Event',
            'loadingText': ccmi18n.loadingText,
            'inputName': 'eventID',
            'calendarID': 0,
            'closeOnComplete': true,
            'eventID': 0
        }, options);

        my.$element = $element;
        my.options = options;
        my._chooseTemplate = _.template(my.chooseTemplate, {'options': my.options});
        my._loadingTemplate = _.template(my.loadingTemplate);
        my._eventLoadedTemplate = _.template(my.eventLoadedTemplate);

        my.$element.append(my._chooseTemplate);
        my.$element.unbind('.calendarEventSelector').on('click.calendarEventSelector', 'a[data-calendar-event-selector-link=choose]', function(e) {
            e.preventDefault();
            $.fn.dialog.open({
                title: options.chooseText,
                href: CCM_DISPATCHER_FILENAME + '/ccm/calendar/dialogs/choose_event?caID=' + options.calendarID,
                width: '90%',
                modal: true,
                height: '70%'
            });
        });

        if (my.options.eventID) {
            my.loadEvent(my.options.eventID);
        }

        ConcreteEvent.unsubscribe('CalendarEventSearchDialogSelectEvent');
        ConcreteEvent.subscribe('CalendarEventSearchDialogSelectEvent', function(e, data) {
            if (my.options.closeOnComplete) {
                $.fn.dialog.closeTop();
            }
            my.loadEvent(data.id);
        });

    }

    ConcreteCalendarEventSelector.prototype = {


        chooseTemplate: '<div class="ccm-item-selector">' +
        '<input type="hidden" name="<%=options.inputName%>" value="0" /><a href="#" data-calendar-event-selector-link="choose"><%=options.chooseText%></a></div>',
        loadingTemplate: '<div class="ccm-item-selector"><div class="ccm-item-selector-choose"><input type="hidden" name="<%=options.inputName%>" value="<%=eventID%>"><i class="fa fa-spin fa-spinner"></i> <%=options.loadingText%></div></div>',
        eventLoadedTemplate: '<div class="ccm-item-selector"><div class="ccm-item-selector-item-selected">' +
        '<input type="hidden" name="<%=inputName%>" value="<%=event.id%>" />' +
        '<a data-calendar-event-selector-action="clear" href="#" class="ccm-item-selector-clear"><i class="fa fa-close"></i></a>' +
        '<div class="ccm-item-selector-item-selected-title"><%=event.title%></div>' +
        '</div></div>',


        loadEvent: function(eventID) {
            var my = this;
            my.$element.html(my._loadingTemplate({'options': my.options, 'eventID': eventID}));

            $.ajax({
                type: 'post',
                dataType: 'json',
                url: CCM_DISPATCHER_FILENAME + '/ccm/calendar/event/get_json',
                data: {'eventID': eventID},
                error: function(r) {
                    ConcreteAlert.dialog('Error', r.responseText);
                },
                success: function(r) {
                    my.$element.html(my._eventLoadedTemplate({'inputName': my.options.inputName, 'event': r}));
                    my.$element.on('click.calendarEventSelector', 'a[data-calendar-event-selector-action=clear]', function(e) {
                        e.preventDefault();
                        my.$element.html(my._chooseTemplate);
                    });
                }
            });
        }
    };

    // jQuery Plugin
    $.fn.concreteCalendarEventSelector = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteCalendarEventSelector($(this), options);
        });
    };

    global.ConcreteCalendarEventSelector = ConcreteCalendarEventSelector;

})(this, jQuery);
