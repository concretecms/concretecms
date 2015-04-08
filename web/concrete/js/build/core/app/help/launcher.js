!function (global, $, _) {
    'use strict';

    function ConcreteHelpLauncher($element, options) {
        var my = this;
        options = options || {};
        options = $.extend({}, options);

        my.$element = $element;
        my.options = options;

        var $notification = my.options.element ? my.options.element :
            $('div[data-help-notification=\'' + my.$element.attr('data-help-notification-toggle') + '\']');

		$('a[data-launch-guide]').on('click.concreteHelp', function(e) {
			e.preventDefault();
			var tour = ConcreteHelpGuideManager.getGuide($(this).attr('data-launch-guide'));
			tour.start();
		});

        my.$element.on('click', function() {
            $(this).addClass('animated fadeOut');

            $notification.queue(function() {
                $(this).addClass('animated fadeIn');
                $(this).show();
                $(this).dequeue();
            });
        });
    }

    // jQuery Plugin
    $.fn.concreteHelpLauncher = function (options) {
        return $.each($(this), function (i, obj) {
            new ConcreteHelpLauncher($(this), options);
        });
    };

    global.ConcreteHelpLauncher = ConcreteMenuManager;

}(this, $, _);
