!function(global, $) {
    'use strict';

    function ConcreteStyleCustomizerImageSelector($element, options) {
        'use strict';
        var my = this,
            options = $.extend({
                'inputName': false,
                'value': false
            }, options);

        ConcreteStyleCustomizerPalette.call(my, $element, options);

        my.$widget.find('div[data-style-customizer-field=image]').concreteFileSelector({
            inputName: my.options.inputName
        });
    }

    ConcreteStyleCustomizerImageSelector.prototype = Object.create(ConcreteStyleCustomizerPalette.prototype);

    ConcreteStyleCustomizerImageSelector.prototype.chooseTemplate = '<div data-launch="style-customizer-palette">' +
        '<input type="hidden" name="<%=options.inputName%>[image]" data-style-customizer-input="image" />' +
        '<div class="ccm-style-customizer-image ccm-ui"><i class="glyphicon glyphicon-picture"</div></div>';

    ConcreteStyleCustomizerImageSelector.prototype.selectorWidgetTemplate = '<div class="ccm-ui ccm-style-customizer-palette">' +
        '<% if (options.value) { %><div><label>Current Image</label><div><img src="<%=options.value%>" /></div></div><% } %>' +
        '<div><label>Image</label><div data-style-customizer-field="image" class="ccm-file-selector"></div></div>' +
        '<div class="ccm-style-customizer-palette-actions"><button class="btn btn-primary">Save</button></div>' +
        '</div>';

    ConcreteStyleCustomizerImageSelector.prototype.getPosition = function($element) {
        var my = this;
        var dim = $element.offset();
        dim.left += $element.width() + 10;
        dim.top -= 20;
        return dim;
    }

    ConcreteStyleCustomizerImageSelector.prototype.updateSwatch = function() {
        var my = this,
            $swatch = my.$element.find('div.ccm-style-customizer-size');

        $swatch.html(my.getValue('size') + my.options.unit);
    }

    ConcreteStyleCustomizerImageSelector.prototype.save = function (e) {
        var my = this;
        my.setValue('size', my.$widget.find('div[data-style-customizer-field=size] div.ccm-style-customizer-slider').slider('value'));
        my.updateSwatch();
        my.closeSelector(e);
    }

    // jQuery Plugin
    $.fn.concreteStyleCustomizerImageSelector = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteStyleCustomizerImageSelector($(this), options);
        });
    }

    global.ConcreteStyleCustomizerImageSelector = ConcreteStyleCustomizerImageSelector;

}(this, $);
