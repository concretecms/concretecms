/**
 * block ajax
 */

!function(global, $) {
    'use strict';

    function ConcreteStyleCustomizerPalette($element, options) {
        'use strict';
        var my = this,
            options = $.extend({
                'inputName': false,
                'unit': 'px',
                'appendTo': document.body
            }, options);

        my.options = options;
        my.opened = false;

        my.$element = $element;
        my.$container = $(my.options.appendTo);

        my._chooseTemplate = _.template(my.chooseTemplate, {'options': my.options});
        my._selectorTemplate = _.template(my.selectorWidgetTemplate, {'options': my.options});

        my.$element.append(my._chooseTemplate);
        my.$widget = $(my._selectorTemplate);
        my.$container.append(my.$widget);

        my.$widget.find('div.ccm-style-customizer-palette-actions button').on('click.style-customizer-palette', function(e) {
            my.save(e);
            return false;
        });

        my.$element.on('click.style-customizer-palette', 'div[data-launch=style-customizer-palette]', function(e) {
            if (my.opened) {
                my.closeSelector(e);
            } else {
                var dim = my.getPosition($(this));
                my.$widget.css({'top': dim.top, 'left': dim.left}).show().on('click.style-customizer-palette', function(e) {
                    e.stopPropagation();
                });
                $(document).on('click.style-customizer-palette', function(e) {
                    my.closeSelector(e);
                });
                my.opened = true;
            }
            return false;
        });
    }

    ConcreteStyleCustomizerPalette.prototype = {

        setValue: function(field, value) {
            var my = this;
            my.$element.find('input[data-style-customizer-input=' + field + ']').val(value);
        },

        getPosition: function($element) {
            var my = this;
            var dim = $element.offset();
            return dim;
        },

        getValue: function(field) {
            var my = this;
            return my.$element.find('input[data-style-customizer-input=' + field + ']').val();
        },

        closeSelector: function(e) {
            var my = this;
            my.$widget.hide();
            my.opened = false;
            $(document).unbind('click.style-customizer-palette');
        },

        updateSwatch: function() {
            alert('You must implement this method updateSwatch.');
        },

        save: function (e) {
            var my = this;
            my.updateSwatch();
            my.closeSelector(e);
            ConcreteEvent.publish('StyleCustomizerSave');
        }
    }

    global.ConcreteStyleCustomizerPalette = ConcreteStyleCustomizerPalette;

}(this, $);
