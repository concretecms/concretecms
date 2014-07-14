(function(global, $) {
    'use strict';

    function ConcreteInlineStyleCustomizer($element, options) {
        var my = this;
        options = $.extend({
        }, options);

        my.options = options;
        my.$element = $element;

        my.$element.on('click.inlineStyleCustomizer', 'button[data-action=cancel-design]', function() {
            my.$element.hide();
            ConcreteEvent.fire('EditModeExitInline');
            return false;
        });
    }

    // jQuery Plugin
    $.fn.concreteInlineStyleCustomizer = function (options) {
        return $.each($(this), function (i, obj) {
            new ConcreteInlineStyleCustomizer($(this), options);
        });
    }

    global.ConcreteInlineStyleCustomizer = ConcreteInlineStyleCustomizer;

})(this, $);
