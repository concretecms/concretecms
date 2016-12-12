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

        info: function(defaults) {
            var options = $.extend({
                type: 'info',
                icon: 'question'
            }, defaults);

            return this.notify(options);
        },

        error: function(defaults) {
            var options = $.extend({
                type: 'danger',
                icon: 'times'
            }, defaults);

            return this.notify(options);
        },

        notify: function(defaults) {
            var options = $.extend({
                type: 'success',
                icon: 'check',
                title: false,
                message: false,
                appendTo: false,
                delay: 2000,
                callback: function() {}
            }, defaults);

            var messageText = '',
                $appendTo = (options.appendTo) ? $(options.appendTo) : $(document.body);

            if (options.title) {
                messageText = '<h3>' + options.title + '</h3>' + options.message;
            } else {
                messageText = '<h3>' + options.message + '</h3>';
            }

            var elem = $('<div id="ccm-notification-hud" class="ccm-ui ccm-notification ccm-notification-' + options.type + '"><i class="ccm-notification-icon fa fa-' + options.icon + '"></i><div class="ccm-notification-inner">' + messageText + '</div></div>').
            appendTo($appendTo).delay(5).queue(function() {
                $(this).addClass('animated fadeIn');
                $(this).dequeue();
            });

            var removeElem = _.once(function() {
                    elem.queue(function () {
                        $(this).css('opacity', 1);
                        $(this).dequeue();
                    }).delay(1).queue(function () {
                        $(this).addClass('animated bounceOutRight');
                        $(this).dequeue();
                    }).delay(1000).queue(function () {
                        $(this).remove();
                        $(this).dequeue();
                        options.callback();
                    });
                }),
                timeout = setTimeout(removeElem, options.delay);

            elem.click(function() {
                removeElem();
                clearTimeout(timeout);
            });
        }

    };

    global.ConcreteAlert = ConcreteAlert;

}(this, $);
