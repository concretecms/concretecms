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

        getCollectionStyles: function() {
            var stylesheets = document.styleSheets;

            for (var i = 0; i < stylesheets.length; i++) {
                // is this the one we want?
                if (stylesheets[i].ownerNode.id === 'collection-styles') {
                    return stylesheets[i];
                }
            }

            return null;
        },

        refreshStyles: function(resp) {
            var stylesheet = this.getCollectionStyles(),
                ruleCount = 0;

            if (resp.oldIssID && stylesheet !== null) {
                // grab the rules and remove the ones we don't want anymore
                var rules = stylesheet.cssRules;
                var removedCount = 0;
                var regexp = new RegExp("\\." + resp.class.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&') + "($|\\D)");
                for (var ri = 0; ri < rules.length; ri++) {
                    if (regexp.test(rules[ri].selectorText)) {
                        // remove this style
                        stylesheet.deleteRule(ri - removedCount++);
                    }
                }
                ruleCount = rules.length - removedCount;
            }

            if (resp.issID) {
                // now we need to add new styles
                if (stylesheet === null) {
                    var newStylesheet = $("<style id='collection-styles' />");
                    $('head').append(newStylesheet);
                    stylesheet = this.getCollectionStyles();
                }
                var newRules = resp.css.split("}");
                for (var nri = 0; nri < newRules.length; nri++) {
                    var newRule = newRules[nri];
                    if (newRule.length > 0) {
                        stylesheet.insertRule(newRule + "}", ruleCount++);
                    }
                }
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
                var targetInput = $(this).next().children('.ccm-inline-style-slider-value');
                var targetInputFormat = targetInput.attr('data-value-format');
                var sliderElement = $(this);
                var min = parseInt($(this).attr('data-style-slider-min'));
                var max = parseInt($(this).attr('data-style-slider-max'));
                var defaultValue = $(this).attr('data-style-slider-default-setting');
                var currentValue = function () {
                    return parseInt(targetInput.val().replace(/\D\-/g,''));
                };
                var disableCheck = function () {
                    if (parseInt(defaultValue) === currentValue() || isNaN(currentValue())) {
                        targetInput.prop('disabled', true).val(defaultValue + targetInputFormat);
                    }
                };

                sliderElement.slider({
                    min: min,
                    max: max,
                    value: currentValue(),
                    slide: function( event, ui ) {
                        targetInput.prop('disabled', false);
                        targetInput.val(ui.value + targetInputFormat);
                        disableCheck();
                    }
                });

                targetInput.change(function () {
                    var value = currentValue();

                    if (value > max) { 
                        value = max;
                    } else if (value < min) {
                        value = min;
                    } else if (isNaN(value)) {
                        value = defaultValue;
                    }
                    
                    $(this).val(value + targetInputFormat);
                    sliderElement.slider("value", value);
                    disableCheck();
                }).blur(function () {
                    disableCheck();
                }).parent().click(function () {
                    if (targetInput.prop('disabled')) {
                        targetInput.prop('disabled', false).select();
                    }
                });

                disableCheck();
            });
        }

    };

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
