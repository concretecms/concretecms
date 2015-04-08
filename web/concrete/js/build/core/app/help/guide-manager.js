!function (global, $) {
    'use strict';

    var ConcreteHelpGuideManager = {

        guides: {},

        register: function (key, guide) {
            this.guides[key] = guide;
        },

        getGuide: function (key) {
            return this.guides[key];
        },

        enterToolbarGuideMode: function() {
            // if help notification is active, hide it
            ConcreteHelpLauncher.close();

            // if the help dialog is active, hide it
            $('.ccm-dialog-help-wrapper').hide();

            // if the widget overlay doesn't exist, show it
            if ($('.ui-widget-overlay').length < 1) {
                var $overlay = $('<div class="ui-widget-overlay"></div>').hide().appendTo('body');
            }
            $('.ui-widget-overlay').addClass('animated fadeIn').show();

            // move the toolbar to above the widget overlay
            $('#ccm-toolbar').addClass('ccm-toolbar-tour-guide');
        },

        exitToolbarGuideMode: function() {
            // if the help dialog is active, show it
            if ($('.ccm-dialog-help-wrapper').length) {
                $('.ccm-dialog-help-wrapper').show();
            } else {
                $('.ui-widget-overlay').addClass('animated fadeOut');
                $('.ui-widget-overlay').delay(250).queue(function() {
                    $(this).remove();
                    $(this).dequeue();
                });
            }

            // move the toolbar back
            $('#ccm-toolbar').removeClass('ccm-toolbar-tour-guide');
        }


    };


    global.ConcreteHelpGuideManager = ConcreteHelpGuideManager;

}(this, $);