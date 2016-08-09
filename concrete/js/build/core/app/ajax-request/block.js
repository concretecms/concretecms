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
            'dragArea': null,
            'bID': false,
            'loader': true
        }, options);
        my.options = options;

        if (options.dragAreaBlockID) {
            $form.find('input[name=dragAreaBlockID]').val(options.dragAreaBlockID);
        }

        ConcreteAjaxForm.call(my, $form, options);
        return $form;
    }

    ConcreteAjaxBlockForm.prototype = Object.create(ConcreteAjaxForm.prototype);

    ConcreteAjaxBlockForm.prototype.before = function (my) {
        jQuery.fn.dialog.showLoader();
        ConcreteEvent.fire('EditModeBeforeBlockSubmit', {
            'form': my
        });
    };

    ConcreteAjaxBlockForm.prototype.refreshBlock = function (resp) {
        var my = this,
            cID = (resp.cID) ? resp.cID : CCM_CID,
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
            var block, edit_mode = Concrete.getEditMode(), local_area = area.inEditMode(edit_mode);

            ConcreteToolbar.disableDirectExit();
            jQuery.fn.dialog.hideLoader();

            if (my.options.task == 'add') {
                var $area = local_area.getElem(), $elem = $(r);

                if (!$elem.hasClass('ccm-block-edit')) {
                    var found = $elem.find('.ccm-block-edit');
                    if (found.length) {
                        block = new Concrete.Block(found, edit_mode);
                        block.setArea(local_area);
                    }
                }

                if (!block) {
                    block = new Concrete.Block($elem, edit_mode);
                    block.setArea(local_area);
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
                block = local_area.getBlockByID(my.options.bID);
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

    ConcreteAjaxBlockForm.prototype.success = function (resp, my) {
        if (my.options.progressiveOperation) {
            my.handleProgressiveOperation(resp, function(r) {
                my.refreshBlock(r);
            });
        } else if (my.validateResponse(resp)) {
            my.refreshBlock(resp);
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
