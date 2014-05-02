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

    ConcreteStyleCustomizerImageSelector.prototype.chooseTemplate = '<span data-launch="style-customizer-palette" class="ccm-style-customizer-display-swatch">' +
        '<input type="hidden" name="<%=options.inputName%>[fID]" data-style-customizer-input="fID" />' +
        '<span class="ccm-ui"><i class="glyphicon glyphicon-picture"></i></span></span>';

    ConcreteStyleCustomizerImageSelector.prototype.selectorWidgetTemplate = '<div class="ccm-ui ccm-style-customizer-palette">' +
        '<% if (options.value) { %><div><label>Current Image</label><div><img src="<%=options.value%>" /></div></div><% } %>' +
        '<div><label>Image</label><div data-style-customizer-field="image" class="ccm-file-selector"></div></div>' +
        '<div class="ccm-style-customizer-palette-actions"><button class="btn btn-primary">Save</button></div>' +
        '</div>';

    ConcreteStyleCustomizerImageSelector.prototype.save = function (e) {
        var my = this,
            fID = 0;

        var $selector = my.$widget.find('div.ccm-file-selector-file-selected');
        if ($selector.length) {
            fID = $selector.find('input[type=hidden]').val();
        }
        my.setValue('fID', fID);
        ConcreteEvent.publish('StyleCustomizerControlUpdate');
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
