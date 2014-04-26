/**
 * block ajax
 */

!function(global, $) {
	'use strict';

	function ConcreteFontSelector($element, options) {
		'use strict';
		var my = this,
			options = $.extend({
				'inputName': false,
				'appendTo': document.body
			}, options);

		my.options = options;

		my.$element = $element;
		my.$container = $(my.options.appendTo);
		my._chooseTemplate = _.template(my.chooseTemplate, {'options': my.options});
		my._selectorTemplate = _.template(my.selectorWidgetTemplate, {'options': my.options});

		my.$element.append(my._chooseTemplate);
		my.$container.append(my._selectorTemplate);
		my.$widget = my.$container.find('div.ccm-font-selector-widget');
		my.$fontMenu = my.$widget.find('select[data-font-selector-field=font]');
		my.$sliders = my.$widget.find('div.ccm-font-selector-slider');

		my.$sliders.slider({
			min: 0,
			max: 64,
			value: 0,
			create: function (e, ui) {
				$(this).parent().find('span').html('0px');
			},
			slide: function (e, ui) {
				$(this).parent().find('span').html(ui.value + 'px');
			}
		});
		my.$widget.find('input[data-font-selector-field=color]').spectrum();
		my.$widget.find('div.ccm-font-selector-actions button').on('click.font-selector', function(e) {
			my.save(e);
			return false;
		});
		my.$fontMenu.on('change', function() {
			var font = $(this).val();
			$(this).css('font-family', font);
		});
		$.each(my.fonts, function(i, font) {
			my.$fontMenu.append('<option value="' + font + '">' + font + '</option>');
		});
		my.$element.on('click.font-selector', 'div.ccm-font-selector-select-wrapper', function() {
            var dim = $(this).offset();
			dim.left += $(this).width() + 10;
			dim.top -= 180;
			my.$widget.css({'top': dim.top, 'left': dim.left}).show().on('click.font-selector', function(e) {
				e.stopPropagation();
			});
			$(document).on('click.font-selector', function(e) {
				my.closeSelector(e);
			});
			return false;
		});

	}

	ConcreteFontSelector.prototype = {

        fonts: ['Arial','Helvetica', 'Georgia', 'Verdana', 'Trebuchet MS', 'Book Antiqua', 'Tahoma', 'Times New Roman', 'Courier New', 'Arial Black', 'Comic Sans MS'],

		chooseTemplate: '<div class="ccm-font-selector-select-wrapper"><span class="ccm-font-selector-select">T</span></div>',
		selectorWidgetTemplate: '<div class="ccm-ui ccm-font-selector-widget">' +
		'<div><select data-font-selector-field="font"><option value="">Choose Font</option></select> <input type="text" data-font-selector-field="color"></div>' +  
		'<div class="checkbox"><label><input type="checkbox" class="ccm-flat-checkbox" data-font-selector-field="bold"> Bold</label></div>' +
		'<div class="checkbox"><label><input type="checkbox" class="ccm-flat-checkbox" data-font-selector-field="italic"> Italic</label></div>' +
		'<div class="checkbox"><label><input type="checkbox" class="ccm-flat-checkbox" data-font-selector-field="underline"> Underline</label></div>' +
		'<div class="checkbox"><label><input type="checkbox" class="ccm-flat-checkbox" data-font-selector-field="uppercase"> Uppercase</label></div>' +
		'<div><label>Font Size</label><div data-font-selector-field="size"><div class="ccm-font-selector-slider"></div><span class="ccm-font-selector-slider-value"></span></div></div>' +
		'<div><label>Letter Spacing</label><div data-font-selector-field="letter-spacing"><div class="ccm-font-selector-slider"></div><span class="ccm-font-selector-slider-value"></span></div></div>' +
		'<div><label>Line Height</label><div data-font-selector-field="line-height"><div class="ccm-font-selector-slider"></div><span class="ccm-font-selector-slider-value"></span></div></div>' +
		'<div class="ccm-font-selector-actions"><button class="btn btn-primary">Save</button></div>' + 
		'</div>',

		closeSelector: function(e) {
			var my = this;
			my.$widget.hide();
			$(document).unbind('click.font-selector');
		},

		save: function (e) {
			var my = this;
			my.closeSelector(e);
		}
	}

	// jQuery Plugin
	$.fn.concreteFontSelector = function(options) {
		return $.each($(this), function(i, obj) {
			new ConcreteFontSelector($(this), options);
		});
	}

	global.ConcreteFontSelector = ConcreteFontSelector;

}(this, $);
