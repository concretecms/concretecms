(function(global, $) {
    'use strict';


    function ConcreteInlineStyleCustomizer($element, options) {
        var my = this;
        options = $.extend({
        }, options);

        my.options = options;
        my.$element = $element;
        my.$toolbar = my.$element.find('>ul');
        my.$toolbar.find('div.dropdown-menu').on('click', function(e) {
            if ($(e.target).is('button')) {
                return true;
            } else {
                e.stopPropagation(); // stop the menu from closing
            }
        });

        my.setupForm();
        my.setupButtons();
        my.setupSliders();
    }

    ConcreteInlineStyleCustomizer.prototype = {

        refreshStyles: function(resp) {
            if (resp.oldIssID) {
                $('head').find('style[data-style-set=' + resp.oldIssID +']').remove();
            }
            if (resp.issID) {
                $('head').append($('<style />', {'data-style-set': resp.issID, 'text': resp.css}));
            }
        },

        setupForm: function() {
            var my = this;
            my.$element.concreteAjaxForm({
                success: function(resp) {
                    my.handleResponse(resp);
                },
                error: function(r) {
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

            my.$toolbar.on('click.inlineStyleCustomizer', 'button[data-action=reset-design]', function() {
                $.concreteAjax({
                    url: $(this).attr('data-reset-action'),
                    success: function(resp) {
                        my.handleResponse(resp);
                    }
                });
                return false;
            });

            my.$toolbar.on('click.inlineStyleCustomizer', 'button[data-action=save-design]', function() {
                // move the toolbar back into the form so it submits. so great.
                my.$toolbar.hide().prependTo(my.$element);
                my.$element.submit();
                ConcreteEvent.unsubscribe('EditModeExitInlineComplete');
                return false;
            });
        },

        setupSliders: function(){
            var my = this;
            my.$toolbar.find('.ccm-inline-style-sliders').each(function(){
                var targetInput = $(this).children('.ccm-inline-style-slider-value');
                var min = parseInt($(this).attr('data-style-slider-min'));
                var max = parseInt($(this).attr('data-style-slider-max'));
                var currentValue = parseInt($(this).children('.ccm-inline-style-slider-value').val().replace(/\D/g,''));
                if(isNaN(currentValue)) {
                    currentValue = parseInt($(this).attr('data-style-slider-default-setting'));
                }
                $(this).slider({
                    min: min,
                    max: max,
                    value: currentValue,
                    slide: function( event, ui ) {
                        targetInput.prop('disabled', false);
                        var targetInputFormat = targetInput.attr('data-value-format');
                        targetInput.val( ui.value + targetInputFormat );
                        $(this).next('.ccm-inline-style-slider-display-value').text(ui.value + targetInputFormat);
                    }
                });
            });
        }

    }

    function ConcreteBlockInlineStyleCustomizer($element, options) {
        var my = this;
        ConcreteInlineStyleCustomizer.call(my, $element, options);
    }

    ConcreteBlockInlineStyleCustomizer.prototype = Object.create(ConcreteInlineStyleCustomizer.prototype);

    ConcreteBlockInlineStyleCustomizer.prototype.handleResponse = function(resp) {
        var my = this;
        var editor = new Concrete.getEditMode(),
            area = editor.getAreaByID(resp.aID),
            block = area.getBlockByID(parseInt(resp.originalBlockID)),
            arEnableGridContainer = area.getEnableGridContainer() ? 1 : 0,
            action = CCM_DISPATCHER_FILENAME + '/ccm/system/block/render';

        $.get(action, {
            arHandle: area.getHandle(),
            cID: resp.cID,
            bID: resp.bID,
            arEnableGridContainer: arEnableGridContainer
        }, function (r) {
            ConcreteToolbar.disableDirectExit();
            var newBlock = block.replace(r);
            ConcreteAlert.notify({
                'message': resp.message
            });

            my.refreshStyles(resp);
            ConcreteEvent.fire('EditModeExitInline', {
                action: 'save_inline',
                block: newBlock
            });
            ConcreteEvent.fire('EditModeExitInlineComplete', {
                block: newBlock
            });
            $.fn.dialog.hideLoader();
            editor.destroyInlineEditModeToolbars();
            editor.scanBlocks();

        });
    }

    function ConcreteAreaInlineStyleCustomizer($element, options) {
        var my = this;
        ConcreteInlineStyleCustomizer.call(my, $element, options);
    }

    ConcreteAreaInlineStyleCustomizer.prototype = Object.create(ConcreteInlineStyleCustomizer.prototype);

    ConcreteAreaInlineStyleCustomizer.prototype.handleResponse = function(resp) {
        var my = this,
            editor = new Concrete.getEditMode(),
            area = editor.getAreaByID(resp.aID);
        my.refreshStyles(resp);
        area.getElem().removeClassExcept('ccm-area ccm-global-area');
        if (resp.containerClass) {
            area.getElem().addClass(resp.containerClass);
        }
        editor.destroyInlineEditModeToolbars();
    }

    // jQuery Plugin
    $.fn.concreteBlockInlineStyleCustomizer = function (options) {
        return $.each($(this), function (i, obj) {
            new ConcreteBlockInlineStyleCustomizer($(this), options);
        });
    }

    $.fn.concreteAreaInlineStyleCustomizer = function (options) {
        return $.each($(this), function (i, obj) {
            new ConcreteAreaInlineStyleCustomizer($(this), options);
        });
    }

    $.fn.removeClassExcept = function (val) {
        return this.each(function (index, el) {
            var keep = val.split(" "),  // list we'd like to keep
                reAdd = [],          // ones that should be re-added if found
                $el = $(el);       // element we're working on

            // look for which we re-add (based on them already existing)
            for (var i = 0; i < keep.length; i++){
                if ($el.hasClass(keep[i])) reAdd.push(keep[i]);
            }

            // drop all, and only add those confirmed as existing
            $el
                .removeClass()               // remove existing classes
                .addClass(reAdd.join(' '));  // re-add the confirmed ones
        });
    };

    global.ConcreteBlockInlineStyleCustomizer = ConcreteBlockInlineStyleCustomizer;
    global.ConcreteAreaInlineStyleCustomizer = ConcreteAreaInlineStyleCustomizer;

})(this, $);
