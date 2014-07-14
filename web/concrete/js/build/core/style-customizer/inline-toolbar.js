(function(global, $) {
    'use strict';

    function ConcreteInlineStyleCustomizer($element, options) {
        var my = this;
        options = $.extend({
        }, options);

        my.options = options;
        my.$element = $element;
        my.$toolbar = my.$element.find('>ul');

        my.setupForm();
        my.setupButtons();
    }

    ConcreteInlineStyleCustomizer.prototype = {

        setupForm: function() {
            var my = this;
            my.$element.concreteAjaxForm({
                success: function(r) {
                    ConcreteEvent.fire('EditModeExitInline');
                },
                error: function(r) {
                    ConcreteAlert.dialog('Error', r.responseText);
                    my.$toolbar.prependTo('#ccm-inline-toolbar-container').show();
                }
            });
        },

        setupButtons: function() {
            var my = this;
            my.$toolbar.on('click.inlineStyleCustomizer', 'button[data-action=cancel-design]', function() {
                my.$element.hide();
                ConcreteEvent.fire('EditModeExitInline');
                return false;
            });

            my.$toolbar.on('click.inlineStyleCustomizer', 'button[data-action=save-design]', function() {
                // move the toolbar back into the form so it submits. so great.
                my.$toolbar.hide().prependTo(my.$element);
                my.$element.submit();
                ConcreteEvent.unsubscribe('EditModeExitInlineComplete');
                return false;
            });
        }

    }

    // jQuery Plugin
    $.fn.concreteInlineStyleCustomizer = function (options) {
        return $.each($(this), function (i, obj) {
            new ConcreteInlineStyleCustomizer($(this), options);
        });
    }

    global.ConcreteInlineStyleCustomizer = ConcreteInlineStyleCustomizer;

})(this, $);
