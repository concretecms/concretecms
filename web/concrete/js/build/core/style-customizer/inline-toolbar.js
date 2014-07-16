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
            var newBlock = block.replace(resp.bID, r);
            ConcreteAlert.notify({
                'message': resp.message
            });

            my.refreshStyles(resp);

            editor.destroyInlineEditModeToolbars();

            ConcreteEvent.fire('EditModeExitInlineComplete', {
                block: newBlock
            });
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
        area.getElem().find('div[data-section=area-view]').removeClass();
        if (resp.containerClass) {
            area.getElem().find('div[data-section=area-view]').addClass(resp.containerClass);
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

    global.ConcreteBlockInlineStyleCustomizer = ConcreteBlockInlineStyleCustomizer;
    global.ConcreteAreaInlineStyleCustomizer = ConcreteAreaInlineStyleCustomizer;

})(this, $);
