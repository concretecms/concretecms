/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global ConcreteEvent, ConcreteStyleCustomizerPalette */

;(function(global, $) {
    'use strict';

    function ConcreteStyleCustomizerImageSelector($element, options) {
        var my = this;
        options = $.extend({
            'inputName': false,
            'value': false
        }, options);

        ConcreteStyleCustomizerPalette.call(my, $element, options);

        my.$widget.find('div[data-style-customizer-field=image]').concreteFileSelector({
            inputName: my.options.inputName
        });

        $element.addClass('ccm-style-customizer-importexport').data('ccm-style-customizer-importexport', this);
    }

    ConcreteStyleCustomizerImageSelector.prototype = Object.create(ConcreteStyleCustomizerPalette.prototype);

    ConcreteStyleCustomizerImageSelector.prototype.chooseTemplate = '<span data-launch="style-customizer-palette" class="ccm-style-customizer-display-swatch">' +
        '<input type="hidden" value="<%=options.value%>" name="<%=options.inputName%>[image]" data-style-customizer-input="image" />' +
        '<span class="ccm-ui"><i class="fa fa-picture-o"></i></span></span>';

    ConcreteStyleCustomizerImageSelector.prototype.selectorWidgetTemplate = '<div class="ccm-ui ccm-style-customizer-palette">' +
        '<% if (options.value) { %><div><label><%=i18n.currentImage%></label><div><img style="width: 100%" src="<%=options.value%>" /></div></div><% } %>' +
        '<div><label><%=i18n.image%></label><div data-style-customizer-field="image" class="ccm-file-selector"></div></div>' +
        '<div class="ccm-style-customizer-palette-actions"><button class="btn btn-primary"><%=i18n.save%></button></div>' +
        '</div>';

    ConcreteStyleCustomizerImageSelector.prototype.save = function (e) {
        var my = this,
            image;

        var $selector = my.$widget.find('div.ccm-file-selector-file-selected');
        if ($selector.length) {
            image = $selector.find('input[type=hidden]').val();
        }
        my.setValue('image', image);
        ConcreteEvent.publish('StyleCustomizerControlUpdate');
        my.closeSelector(e);
    };

    ConcreteStyleCustomizerImageSelector.prototype.exportStyle = function (data, cb) {
        var my = this;
        if (!my.options.inputName) {
            cb();
            return;
        }
        var $i = my.$element.find('input[data-style-customizer-input="image"]');
        if ($i.length !== 1) {
            cb();
            return;
        }
        if (!(my.options.inputName in data)) {
            data[my.options.inputName] = {};
        }
        data[my.options.inputName].image = $i.val();
        cb();
    };

    ConcreteStyleCustomizerImageSelector.prototype.importStyle = function (data, cb) {
        var my = this;
        if (my.options.inputName && data[my.options.inputName] && typeof data[my.options.inputName].image === 'string') {
            my.$element.find('input[data-style-customizer-input="image"]').val(data[my.options.inputName].image);
        }
        cb();
    };

    // jQuery Plugin
    $.fn.concreteStyleCustomizerImageSelector = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteStyleCustomizerImageSelector($(this), options);
        });
    };

    global.ConcreteStyleCustomizerImageSelector = ConcreteStyleCustomizerImageSelector;

})(this, jQuery);
