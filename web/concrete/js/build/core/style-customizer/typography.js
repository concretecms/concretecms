/**
 * block ajax
 */

/* jshint unused:vars, undef:true, browser:true */
/* global $, _, ConcreteEvent, ConcreteStyleCustomizerPalette */

(function(global, $) {
    'use strict';

    function ConcreteFontFamily(display, css) {
        if (this instanceof ConcreteFontFamily === false) {
            return new ConcreteFontFamily(display, css);
        }
        this.display = display;
        this.css = css;
    }
    ConcreteFontFamily.prototype.toString = function() {
        return this.display;
    };

    function ConcreteTypographySelector($element, options) {
        var my = this, $field, $slider;
        options = $.extend({
            'inputName': false,
            'fontFamily': -1,
            'color': -1,
            'italic': -1,
            'underline': -1,
            'uppercase': -1,
            'fontSizeValue': -1,
            'fontSizeUnit': 'px',
            'fontWeight': -1,
            'letterSpacingValue': -1,
            'letterSpacingUnit': 'px',
            'lineHeightValue': -1,
            'lineHeightUnit': 'px'
        }, options);
        ConcreteStyleCustomizerPalette.call(my, $element, options);

        my.$fontMenu = my.$widget.find('select[data-style-customizer-field=font]');
        my.$sliders = my.$widget.find('div.ccm-style-customizer-slider');

        my.$sliders.slider({
            min: 0,
            max: 64,
            value: 0,
            create: function (e, ui) {
                $(this).parent().find('span.ccm-style-customizer-slider-value span.ccm-style-customizer-number').html('0');
            },
            slide: function (e, ui) {
                $(this).parent().find('span.ccm-style-customizer-slider-value span.ccm-style-customizer-number').html(ui.value);
            }
        });

        my.$colorpicker = my.$widget.find('input[data-style-customizer-field=color]');
        my.$colorpicker.spectrum({
            'preferredFormat': 'rgb',
            'showAlpha': true,
            'className': 'ccm-widget-colorpicker',
            'showInitial': true,
            'showInput': true
        });

        my.$fontMenu.on('change', function() {
            var font = my.fonts[$(this).val()];
            $(this).css('font-family', font);
        });

        $.each(my.fonts, function(i, font) {
            my.$fontMenu.append('<option value="' + font + '">' + font + '</option>');
        });

        // set defaults
        if (my.options.fontFamily != -1) {
            var fontFamilyName = my.options.fontFamily.split(',')[0].replace("'", '').replace("'", '');
            if (typeof my.fonts[fontFamilyName] === 'undefined') {
                my.fonts[fontFamilyName] = new ConcreteFontFamily(
                    fontFamilyName,
                    my.options.fontFamily
                );
                my.$fontMenu.append($('<option>', {
                    'value': fontFamilyName,
                    'text': fontFamilyName
                }));
            }

            my.setValue('font-family', my.fonts[fontFamilyName].css);
            my.$fontMenu.val(fontFamilyName);
            my.$fontMenu.css('font-family', my.fonts[fontFamilyName].css);
        } else {
            my.$widget.find('[data-wrapper=fontFamily]').remove();
            my.$element.find('[data-wrapper=fontFamily]').remove();
        }

        if (my.options.color != -1) {
            my.$colorpicker.spectrum('set', my.options.color);
            my.setValue('color', my.options.color);
        } else {
            my.$widget.find('[data-wrapper=color]').remove();
            my.$element.find('[data-wrapper=color]').remove();
        }

        if (my.options.underline != -1) {
            my.$widget.find('input[data-style-customizer-field=underline]').prop('checked', my.options.underline);
            my.setValue('underline', my.options.underline ? 1 : 0);
        } else {
            my.$widget.find('[data-wrapper=underline]').remove();
            my.$element.find('[data-wrapper=underline]').remove();
        }

        if (my.options.uppercase != -1) {
            my.$widget.find('input[data-style-customizer-field=uppercase]').prop('checked', my.options.uppercase);
            my.setValue('uppercase',  my.options.uppercase ? 1 : 0);
        } else {
            my.$widget.find('[data-wrapper=uppercase]').remove();
            my.$element.find('[data-wrapper=uppercase]').remove();
        }

        if (my.options.italic != -1) {
            my.$widget.find('input[data-style-customizer-field=italic]').prop('checked', my.options.italic);
            my.setValue('italic',  my.options.italic ? 1 : 0);
        } else {
            my.$widget.find('[data-wrapper=italic]').remove();
            my.$element.find('[data-wrapper=italic]').remove();
        }

        if (my.options.fontSizeValue != -1) {
            $field = my.$widget.find('div[data-style-customizer-field=font-size]');
            $slider = $field.find('div.ccm-style-customizer-slider');
            $slider.slider('value', my.options.fontSizeValue);
            if (my.options.fontSizeUnit == 'em') {
                $slider.slider('option', 'step', 0.1);
                $slider.slider('option', 'max', 10);
            }
            $field.find('span.ccm-style-customizer-slider-value span.ccm-style-customizer-number').html(my.options.fontSizeValue);
            $field.find('span.ccm-style-customizer-slider-value span.ccm-style-customizer-unit').html(my.options.fontSizeUnit);
            my.setValue('font-size', my.options.fontSizeValue);
        } else {
            my.$widget.find('[data-wrapper=fontSize]').remove();
            my.$element.find('[data-wrapper=fontSize]').remove();
        }

        if (my.options.fontWeight != -1) {
            $field = my.$widget.find('div[data-style-customizer-field=font-weight]');
            $slider = $field.find('div.ccm-style-customizer-slider');
            $slider.slider('option', 'step', 100);
            $slider.slider('option', 'max', 900);
            $slider.slider('option', 'min', 100);
            $slider.slider('value', my.options.fontWeight);
            $field.find('span.ccm-style-customizer-slider-value span.ccm-style-customizer-number').html(my.options.fontWeight);
            my.setValue('font-weight', my.options.fontWeight);
        } else {
            my.$widget.find('[data-wrapper=fontWeight]').remove();
            my.$element.find('[data-wrapper=fontWeight]').remove();
        }

        if (my.options.letterSpacingValue != -1) {
            $field = my.$widget.find('div[data-style-customizer-field=letter-spacing]');
            $slider = $field.find('div.ccm-style-customizer-slider');
            $slider.slider('value', my.options.letterSpacingValue);
            if (my.options.letterSpacingUnit == 'em') {
                $slider.slider('option', 'step', 0.1);
                $slider.slider('option', 'max', 10);
            }
            $field.find('span.ccm-style-customizer-slider-value span.ccm-style-customizer-number').html(my.options.letterSpacingValue);
            $field.find('span.ccm-style-customizer-slider-value span.ccm-style-customizer-unit').html(my.options.letterSpacingUnit);
            my.setValue('letter-spacing', my.options.letterSpacingValue);
        } else {
            my.$widget.find('[data-wrapper=letterSpacing]').remove();
            my.$element.find('[data-wrapper=letterSpacing]').remove();
        }

        if (my.options.lineHeightValue != -1) {
            $field = my.$widget.find('div[data-style-customizer-field=line-height]');
            $slider = $field.find('div.ccm-style-customizer-slider');
            $slider.slider('value', my.options.lineHeightValue);
            if (my.options.lineHeightUnit == 'em') {
                $slider.slider('option', 'step', 0.1);
                $slider.slider('option', 'max', 10);
            }
            $field.find('span.ccm-style-customizer-slider-value span.ccm-style-customizer-number').html(my.options.lineHeightValue);
            $field.find('span.ccm-style-customizer-slider-value span.ccm-style-customizer-unit').html(my.options.lineHeightUnit);
            my.setValue('line-height', my.options.lineHeightValue);
        } else {
            my.$widget.find('[data-wrapper=lineHeight]').remove();
            my.$element.find('[data-wrapper=lineHeight]').remove();
        }

        my.updateSwatch();

    }

    ConcreteTypographySelector.prototype = Object.create(ConcreteStyleCustomizerPalette.prototype);

    ConcreteTypographySelector.prototype.fonts = {
        'Arial': new ConcreteFontFamily('Arial', 'Arial, sans-serif'),
        'Helvetica': new ConcreteFontFamily('Helvetica', 'Helvetica, sans-serif'),
        'Georgia': new ConcreteFontFamily('Georgia', 'Georgia, serif'),
        'Verdana': new ConcreteFontFamily('Verdana', 'Verdana, sans-serif'),
        'Trebuchet MS': new ConcreteFontFamily('Trebuchet MS', 'Trebuchet MS, sans-serif'),
        'Book Antiqua': new ConcreteFontFamily('Book Antiqua', 'Book Antiqua, serif'),
        'Tahoma': new ConcreteFontFamily('Tahoma', 'Tahoma, sans-serif'),
        'Times New Roman': new ConcreteFontFamily('Times New Roman', 'Times New Roman, serif'),
        'Courier New': new ConcreteFontFamily('Courier New', 'Courier New, monospace'),
        'Arial Black': new ConcreteFontFamily('Arial Black', 'Arial Black, sans-serif'),
        'Comic Sans MS': new ConcreteFontFamily('Comic Sans MS', 'Comic Sans MS, sans-serif')
    };
    ConcreteTypographySelector.prototype.chooseTemplate = '<span class="ccm-style-customizer-display-swatch" data-launch="style-customizer-palette">' +
        '<div data-wrapper="fontFamily"><input type="hidden" name="<%=options.inputName%>[font-family]" data-style-customizer-input="font-family" /></div>' +
        '<div data-wrapper="color"><input type="hidden" name="<%=options.inputName%>[color]" data-style-customizer-input="color" /></div>' +
        '<div data-wrapper="italic"><input type="hidden" name="<%=options.inputName%>[italic]" data-style-customizer-input="italic" /></div>' +
        '<div data-wrapper="underline"><input type="hidden" name="<%=options.inputName%>[underline]" data-style-customizer-input="underline" /></div>' +
        '<div data-wrapper="uppercase"><input type="hidden" name="<%=options.inputName%>[uppercase]" data-style-customizer-input="uppercase" /></div>' +
        '<div data-wrapper="fontWeight"><input type="hidden" name="<%=options.inputName%>[font-weight]" data-style-customizer-input="font-weight" /></div>' +
        '<div data-wrapper="fontSize"><input type="hidden" name="<%=options.inputName%>[font-size][size]" data-style-customizer-input="font-size" />' +
        '<input type="hidden" name="<%=options.inputName%>[font-size][unit]" value="<%=options.fontSizeUnit%>" /></div>' +
        '<div data-wrapper="letterSpacing"><input type="hidden" name="<%=options.inputName%>[letter-spacing][size]" data-style-customizer-input="letter-spacing" />' +
        '<input type="hidden" name="<%=options.inputName%>[letter-spacing][unit]" value="<%=options.letterSpacingUnit%>" /></div>' +
        '<div data-wrapper="lineHeight"><input type="hidden" name="<%=options.inputName%>[line-height][size]" data-style-customizer-input="line-height" />' +
        '<input type="hidden" name="<%=options.inputName%>[line-height][unit]" value="<%=options.lineHeightUnit%>" /></div>' +
        '<span>T</span></span>';

    ConcreteTypographySelector.prototype.selectorWidgetTemplate = '<div class="ccm-ui ccm-style-customizer-palette">' +
        '<div><select data-style-customizer-field="font" data-wrapper="fontFamily"><option value=""><%=i18n.chooseFont%></option></select> <span data-wrapper="color"><input type="text" data-style-customizer-field="color"></span></div>' +
        '<div data-wrapper="italic" class="checkbox"><label><input type="checkbox" class="ccm-flat-checkbox" data-style-customizer-field="italic"> <%=i18n.italic%></label></div>' +
        '<div data-wrapper="underline" class="checkbox"><label><input type="checkbox" class="ccm-flat-checkbox" data-style-customizer-field="underline"> <%=i18n.underline%></label></div>' +
        '<div data-wrapper="uppercase" class="checkbox"><label><input type="checkbox" class="ccm-flat-checkbox" data-style-customizer-field="uppercase"> <%=i18n.uppercase%></label></div>' +
        '<div data-wrapper="fontSize"><label><%=i18n.fontSize%></label><div data-style-customizer-field="font-size"><div class="ccm-style-customizer-slider"></div><span class="ccm-style-customizer-slider-value"><span class="ccm-style-customizer-number"></span><span class="ccm-style-customizer-unit">px</span></span></div></div>' +
        '<div data-wrapper="fontWeight"><label><%=i18n.fontWeight%> <i class="fa fa-question-circle launch-tooltip" title="400 = Normal, 700 = Bold"></i></label><div data-style-customizer-field="font-weight"><div class="ccm-style-customizer-slider"></div><span class="ccm-style-customizer-slider-value"><span class="ccm-style-customizer-number"></span></span></div></div>' +
        '<div data-wrapper="letterSpacing"><label><%=i18n.letterSpacing%></label><div data-style-customizer-field="letter-spacing"><div class="ccm-style-customizer-slider"></div><span class="ccm-style-customizer-slider-value"><span class="ccm-style-customizer-number"></span><span class="ccm-style-customizer-unit">px</span></span></div></div>' +
        '<div data-wrapper="lineHeight"><label><%=i18n.lineHeight%></label><div data-style-customizer-field="line-height"><div class="ccm-style-customizer-slider"></div><span class="ccm-style-customizer-slider-value"><span class="ccm-style-customizer-number"></span><span class="ccm-style-customizer-unit">px</span></span></div></div>' +
        '<div class="ccm-style-customizer-palette-actions"><button class="btn btn-primary"><%=i18n.save%></button></div>' +
        '</div>';

    ConcreteTypographySelector.prototype.updateSwatch = function() {
        var my = this,
            $swatch = my.$element.find('span.ccm-style-customizer-display-swatch');

        if (my.getValue('font-family')) {
            $swatch.css('font-family', my.getValue('font-family'));
        }
        if (my.getValue('color')) {
            $swatch.css('color', my.getValue('color'));
        }

        $swatch.css('font-weight', 'inherit');
        $swatch.css('font-style', 'inherit');
        $swatch.css('text-decoration', 'inherit');
        $swatch.css('text-transform', 'inherit');


        if (my.getValue('italic') === '1') {
            $swatch.css('font-style', 'italic');
        }
        if (my.getValue('underline') === '1') {
            $swatch.css('text-decoration', 'underline');
        }
        $swatch.css('font-weight', my.getValue('font-weight'));
        if (my.getValue('uppercase') === '1') {
            $swatch.css('text-transform', 'uppercase');
        }
        $swatch.css('font-size', '14px');

    };

    ConcreteTypographySelector.prototype.save = function (e) {
        var my = this;
        my.setValue('font-family', my.fonts[my.$fontMenu.val()].css);
        my.setValue('color', my.$widget.find('input[data-style-customizer-field=color]').spectrum('get'));
        my.setValue('italic', my.$widget.find('input[data-style-customizer-field=italic]').is(':checked') ? '1' : 0);
        my.setValue('underline', my.$widget.find('input[data-style-customizer-field=underline]').is(':checked') ? '1' : 0);
        my.setValue('uppercase', my.$widget.find('input[data-style-customizer-field=uppercase]').is(':checked') ? '1' : 0);
        my.setValue('font-size', my.$widget.find('div[data-style-customizer-field=font-size] div.ccm-style-customizer-slider').slider('value'));
        my.setValue('font-weight', my.$widget.find('div[data-style-customizer-field=font-weight] div.ccm-style-customizer-slider').slider('value'));
        my.setValue('letter-spacing', my.$widget.find('div[data-style-customizer-field=letter-spacing] div.ccm-style-customizer-slider').slider('value'));
        my.setValue('line-height', my.$widget.find('div[data-style-customizer-field=line-height] div.ccm-style-customizer-slider').slider('value'));
        my.updateSwatch();
        ConcreteEvent.publish('StyleCustomizerControlUpdate');
        my.closeSelector(e);
    };

    // jQuery Plugin
    $.fn.concreteTypographySelector = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteTypographySelector($(this), options);
        });
    };

    global.ConcreteTypographySelector = ConcreteTypographySelector;

})(this, $);
