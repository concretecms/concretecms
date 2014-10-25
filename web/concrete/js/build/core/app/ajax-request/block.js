/**
 * block ajax
 */

!function (global, $) {
    'use strict';

    function ConcreteAjaxBlockForm($form, options) {
        'use strict';
        var my = this;
        options = $.extend({
            'iframe': true,
            'task': false,
            'dragAreaBlockID': false,
            dragArea: null,
            'bID': false
        }, options);
        my.options = options;

        if (options.dragAreaBlockID) {
            $form.find('input[name=dragAreaBlockID]').val(options.dragAreaBlockID);
        }
        return ConcreteAjaxForm.call(my, $form, options);
    }

    ConcreteAjaxBlockForm.prototype = Object.create(ConcreteAjaxForm.prototype);

    ConcreteAjaxBlockForm.prototype.beforeSubmit = function (my) {
        ConcreteAjaxForm.prototype.beforeSubmit.call(this, my);
    };

    ConcreteAjaxBlockForm.prototype.success = function (resp, my) {
        if (my.validateResponse(resp)) {
            var cID = (resp.cID) ? resp.cID : CCM_CID,
                editor = new Concrete.getEditMode(),
                area = editor.getAreaByID(resp.aID),
                arEnableGridContainer = area.getEnableGridContainer() ? 1 : 0,
                action = CCM_DISPATCHER_FILENAME + '/ccm/system/block/render';

            jQuery.fn.dialog.closeTop();

            $.get(action, {
                arHandle: area.getHandle(),
                cID: cID,
                bID: resp.bID,
                arEnableGridContainer: arEnableGridContainer,
                placeholder: ''
            }, function (r) {

                var block;

                ConcreteToolbar.disableDirectExit();
                jQuery.fn.dialog.hideLoader();

                if (my.options.task == 'add') {
                    var $area = area.getElem(), $elem = $(r);


                    if (!$elem.hasClass('ccm-block-edit')) {
                        var found = $elem.find('.ccm-block-edit');
                        if (found.length) {
                            block = new Concrete.Block(found, Concrete.getEditMode());
                        }
                    }

                    if (!block) {
                        block = new Concrete.Block($elem, Concrete.getEditMode());
                    }

                    if (my.options.btHandle === 'core_area_layout') {
                        $area.children('.ccm-area-block-list').append($elem);
                    } else {
                        var placeholder = $(my.options.placeholder);
                        if (placeholder.length) {
                            placeholder.replaceWith($elem);
                        } else {
                            $area.children('.ccm-area-block-list').prepend($elem);
                        }
                    }

                    ConcreteAlert.notify({
                        'message': ccmi18n.addBlockMsg,
                        'title': ccmi18n.addBlock
                    });
                    jQuery.fn.dialog.closeAll();

                    if (my.options.btSupportsInlineAdd) {
                        editor.destroyInlineEditModeToolbars();
                        ConcreteEvent.fire('EditModeExitInlineComplete', {
                            block: block
                        });
                    }

                    ConcreteEvent.fire('EditModeAddBlockComplete', {
                        block: block
                    });

                } else {
                    // remove old block from area
                    block = area.getBlockByID(my.options.bID);
                    var newBlock = block.replace(r);
                    ConcreteAlert.notify({
                        'message': ccmi18n.updateBlockMsg,
                        'title': ccmi18n.updateBlock
                    });

                    if (my.options.btSupportsInlineEdit) {
                        editor.destroyInlineEditModeToolbars();
                        ConcreteEvent.fire('EditModeExitInlineComplete', {
                            block: newBlock
                        });
                    }

                    ConcreteEvent.fire('EditModeUpdateBlockComplete', {
                        block: newBlock
                    });
                }


            });
        }
    };

    // jQuery Plugin
    $.fn.concreteAjaxBlockForm = function (options) {
        return $.each($(this), function (i, obj) {
            new ConcreteAjaxBlockForm($(this), options);
        });
    };

    global.ConcreteAjaxBlockForm = ConcreteAjaxBlockForm;

}(this, $);
