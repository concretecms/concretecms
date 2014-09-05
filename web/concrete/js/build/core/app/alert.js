/**
 * Simple alert using dialog class.
 */


!function(global, $) {
    'use strict';

    var ConcreteAlert = {

        dialog : function(title, message, onCloseFn) {
            $('<div id="ccm-popup-alert" class="ccm-ui"><div id="ccm-popup-alert-message" class="alert alert-danger">' + message + '</div></div>').dialog({
                title: title,
                modal: true,
                onDestroy: onCloseFn
            });
        },

        notify: function(defaults) {
            var options = $.extend({
                'type': 'success',
                'icon': 'check',
                'title': false,
                'message': false,
                'appendTo': false,
            }, defaults);

            var messageText = '',
                $appendTo = (options.appendTo) ? $(options.appendTo) : $(document.body);

            if (options.title) {
                messageText = '<h3>' + options.title + '</h3>' + options.message;
            } else {
                messageText = '<h3>' + options.message + '</h3>';
            }


            $('<div id="ccm-notification-hud" class="ccm-ui ccm-notification ccm-notification-' + options.type + '"><i class="ccm-notification-icon fa fa-' + options.icon + '"></i><div class="ccm-notification-inner">' + messageText + '</div></div>').
            appendTo($appendTo).delay(5).queue(function() {
                $(this).addClass('animated fadeIn');
                $(this).dequeue();
            }).delay(2000).queue(function() {
                $(this).css('opacity', 1);
                $(this).dequeue();
            }).delay(1).queue(function() {
                $(this).addClass('animated bounceOutRight');
                $(this).dequeue();
            }).delay(1000).queue(function() {
                $(this).remove();
                $(this).dequeue();
            });
        }

    }

    global.ConcreteAlert = ConcreteAlert;

}(this, $);