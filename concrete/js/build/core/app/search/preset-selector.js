/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global ConcreteEvent */

;(function(global, $) {
    'use strict';

    function ConcreteAdvancedSearchPresetSelector($element, options) {
        var my = this;
        options = options || {};
        options = $.extend({}, options);

        my.$element = $element;
        my.options = options;

        $('[data-search-preset-id]').on('click', function(e) {
            e.preventDefault();
            if (!$(e.target).is('button') && $(this).data('action')) {
                $.fn.dialog.closeTop();
                ConcreteEvent.publish('SavedPresetSubmit',$(this).data('action'));
            }
        });

        $('.ccm-search-presets-table tbody tr').on('mouseover', function() {
            $(this).addClass('ccm-search-select-hover');
        }).on('mouseout', function() {
            $(this).removeClass('ccm-search-select-hover');
        });

        $('button[data-button-action=save-search-preset]').on('click.saveSearchPreset', function() {
            $.fn.dialog.open({
                element: 'div[data-dialog=save-search-preset]:first',
                modal: true,
                width: 320,
                title: 'Save Preset',
                height: 'auto'
            });
        });

        var $presetForm = $('form[data-form=save-preset]');
        var $form = $('form[data-form=advanced-search]');
        $('button[data-button-action=save-search-preset-submit]').on('click.saveSearchPresetSubmit', function() {
            var $presetForm = $('form[data-form=save-preset]');
            $presetForm.trigger('submit');
        });

        $presetForm.on('submit', function() {
            var formData = $form.serializeArray();
            formData = formData.concat($presetForm.serializeArray());
            $.concreteAjax({
                data: formData,
                url: $presetForm.attr('action'),
                success: function(r) {
                    $.fn.dialog.closeAll();
                    ConcreteEvent.publish('SavedSearchCreated', r);
                }
            });
            return false;
        });

        $('button[data-button-action=edit-search-preset], button[data-button-action=delete-search-preset]').on('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('data-tree-action-url'),
                title = $(this).attr('dialog-title');

            $.fn.dialog.open({
                title: title,
                href: url,
                width: 550,
                modal: true,
                height: 'auto'
            });
        });


    }


    // jQuery Plugin
    $.fn.concreteAdvancedSearchPresetSelector = function (options) {
        return $.each($(this), function (i, obj) {
            new ConcreteAdvancedSearchPresetSelector($(this), options);
        });
    };

    global.ConcreteAdvancedSearchPresetSelector = ConcreteAdvancedSearchPresetSelector;

})(window, jQuery);
