/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global ConcreteEvent */

;(function(global, $) {
    'use strict';

    function ConcreteStyleCustomizerColorPicker($element, options) {
        var my = this;
        my.$element = $element;
        my.options = $.extend(true, {
            initialColor: '',
            i18n: {
                cancel: 'Cancel',
                choose: 'Choose',
                clear: 'Clean'
            }
        }, options || {});
        $element.spectrum({
            showInput: true,
            showInitial: true,
            preferredFormat: 'rgb',
            allowEmpty: true,
            className: 'ccm-widget-colorpicker',
            showAlpha: true,
            value: my.options.initialColor,
            cancelText: my.options.i18n.cancel,
            chooseText: my.options.i18n.choose,
            clearText: my.options.i18n.clear,
            change: function() {
                ConcreteEvent.publish('StyleCustomizerControlUpdate');
            }
        });
        $element.addClass('ccm-style-customizer-importexport').data('ccm-style-customizer-importexport', this);
    }
    
    ConcreteStyleCustomizerColorPicker.prototype = {
        exportStyle: function (data, cb) {
            var my = this,
                varName = my.$element.attr('name') || '',
                match = varName.match(/^(.+)\[color\]$/);
            if (!match) {
                cb();
                return;
            }
            var value = my.$element.spectrum('get');
            if (!(match[1] in data)) {
                data[match[1]] = {};
            }
            data[match[1]].color = value ? value.toHex8String() : '';
            cb();
        },
        importStyle: function (data, cb) {
            var my = this,
                varName = my.$element.attr('name') || '',
                match = varName.match(/^(.+)\[color\]$/);
            if (!match) {
                cb();
                return;
            }
            if (data[match[1]] && typeof data[match[1]].color === 'string') {
                my.$element.spectrum('set', data[match[1]].color);
            }
            cb();
        }
    };

    // jQuery Plugin
    $.fn.concreteStyleCustomizerColorPicker = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteStyleCustomizerColorPicker($(this), options);
        });
    };

    global.ConcreteStyleCustomizerColorPicker = ConcreteStyleCustomizerColorPicker;

})(this, jQuery);
