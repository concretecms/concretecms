/**
 * block ajax
 */

/* jshint unused:vars, undef:true, browser:true */
/* global $, _, alert, ConcreteEvent, ccmi18n */

(function(global, $) {
    'use strict';

    function ConcreteStyleCustomizerPalette($element, options) {
        var my = this;
        options = $.extend({
            'inputName': false,
            'unit': 'px',
            'appendTo': document.body
        }, options);

        my.options = options;
        my.opened = false;

        my.$element = $element;
        my.$container = $(my.options.appendTo);

        my._chooseTemplate = _.template(my.chooseTemplate, {'options': my.options, i18n: ccmi18n});
        my._selectorTemplate = _.template(my.selectorWidgetTemplate, {'options': my.options, i18n: ccmi18n});

        my.$element.append(my._chooseTemplate);
        my.$widget = $(my._selectorTemplate);
        my.$container.append(my.$widget);

        my.$widget.find('.launch-tooltip').tooltip();
        my.$widget.find('div.ccm-style-customizer-palette-actions button').on('click.style-customizer-palette', function(e) {
            my.save(e);
            return false;
        });

        my.$element.on('click.style-customizer-palette', '[data-launch=style-customizer-palette]', function(e) {
            if (my.opened) {
                my.closeSelector(e);
            } else {
                var dim = my.getPosition();
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

        getPosition: function() {
            var my = this;
            var offset = my.getOffset(my.$widget, my.$element);
            return offset;
        },

        getOffset: function(picker, input) {
            var extraY = -5;
            var dpWidth = picker.outerWidth();
            var dpHeight = picker.outerHeight();
            var inputHeight = input.outerHeight();
            var doc = picker[0].ownerDocument;
            var docElem = doc.documentElement;
            var viewWidth = docElem.clientWidth + $(doc).scrollLeft();
            var viewHeight = docElem.clientHeight + $(doc).scrollTop();
            var offset = input.offset();
            offset.top += inputHeight;

            offset.left -=
                Math.min(offset.left, (offset.left + dpWidth > viewWidth && viewWidth > dpWidth) ?
                Math.abs(offset.left + dpWidth - viewWidth) : 0);

            offset.top -=
                Math.min(offset.top, ((offset.top + dpHeight > viewHeight && viewHeight > dpHeight) ?
                Math.abs(dpHeight + inputHeight - extraY) : extraY));

            return offset;
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
            ConcreteEvent.publish('StyleCustomizerControlUpdate');
        }
    };

    global.ConcreteStyleCustomizerPalette = ConcreteStyleCustomizerPalette;

})(this, $);
