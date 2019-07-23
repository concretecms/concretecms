/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global ConcreteHelpGuideManager */

;(function(global, $) {
    'use strict';

    function ConcreteHelpLauncher($element, options) {
        var my = this;
        options = options || {};
        options = $.extend({}, options);

        my.$element = $element;
        my.options = options;

        var $notification = my.options.element ? my.options.element :
            $('div[data-help-notification=\'' + my.$element.attr('data-help-notification-toggle') + '\']');

        $notification.find('a[data-launch-guide]').on('click.concreteHelp', function(e) {
			e.preventDefault();
			var tour = ConcreteHelpGuideManager.getGuide($(this).attr('data-launch-guide'));
			tour.start();
		});

        my.$element.on('click', function(e) {
            e.preventDefault();
            $(this).addClass('animated fadeOut');
            $notification.addClass('animated fadeIn').show();

            $notification.on('click', 'a[data-dismiss=help-single]', function(e) {
                e.preventDefault();
                ConcreteHelpLauncher.close($notification);
            });
        });
    }

    ConcreteHelpLauncher.close = function($notification) {
        if (!$notification) {
            $notification = $('div[data-help-notification]');
        }

        if (!$notification || !$notification.is(':visible')) {
            return false;
        }
        var $element = $('[data-help-notification-toggle=\'' + $notification.attr('data-help-notification') + '\']');

        $notification.addClass('animated fadeOut');
        $element.removeClass('fadeOut').addClass('fadeIn');
        $notification.clearQueue().delay(250).queue(function() {
            $(this).hide();
            $(this).removeClass('animated fadeOut');
            $(this).dequeue();
        });
    };
    // jQuery Plugin
    $.fn.concreteHelpLauncher = function (options) {
        return $.each($(this), function (i, obj) {
            new ConcreteHelpLauncher($(this), options);
        });
    };

    global.ConcreteHelpLauncher = ConcreteHelpLauncher;

})(window, jQuery);
