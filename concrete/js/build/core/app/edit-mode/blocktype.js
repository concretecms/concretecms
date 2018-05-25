/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global _, Concrete, ConcreteEvent, ConcretePanelManager, ConcreteToolbar, CCM_SECURITY_TOKEN, CCM_DISPATCHER_FILENAME */

;(function(window, $) {
    'use strict';

    var BlockType = Concrete.BlockType = function BlockType(elem, edit_mode, dragger, default_area) {
        this.init.apply(this, _(arguments).toArray());
    };

    BlockType.prototype = _.extend(Object.create(Concrete.Block.prototype), {

        init: function(elem, edit_mode, dragger, default_area) {
            var my = this;
            Concrete.Block.prototype.init.apply(my, _(arguments).toArray());
            my.setAttr('defaultArea', default_area || null);

            if (default_area) {
                if (default_area.acceptsBlockType(my.getHandle())) {
                    my.handleDefaultArea();
                } else {
                    my.removeElement();
                }
            }
        },

        handleDefaultArea: function() {
            var my = this;
            $.pep.unbind(my.getPeper());
            my.getPeper().click(function (e) {
                my.handleClick();

                return false;
            }).css({
                cursor: 'pointer'
            });
        },

        removeElement: function() {
            var panel = this.getPeper().closest('.ccm-panel-content-inner');
            this.getPeper().closest('li').remove();

            panel.children('.ccm-panel-add-block-set').each(function() {
                var ul = $(this).children('ul');
                if (!ul.children().length) {
                    $(this).remove();
                }
            });
        },

        handleClick: function() {
            var my = this, default_area = my.getAttr('defaultArea');

            ConcretePanelManager.exitPanelMode(function() {
                _.defer(function() {
                    my.addToDragArea(_.last(default_area.getDragAreas()));
                });
            });
        },

        pepStart: function blockTypePepStart(context, event, pep) {
            var my = this, panel;
            Concrete.Block.prototype.pepStart.call(this, context, event, pep);

            my.setAttr('closedPanel', _(ConcretePanelManager.getPanels()).find(function (panel) {
                return panel.isOpen;
            }));

            if ((panel = my.getAttr('closedPanel'))) {
                panel.hide();
            }
        },

        pepStop: function blockTypePepStop(context, event, pep) {
            var my = this, drag_area, panel;

            if ((drag_area = my.getSelected())) {
                my.addToDragArea(drag_area);
            } else {
                if ((panel = my.getAttr('closedPanel'))) {
                    panel.show();
                }
            }

            _.defer(function () {
                Concrete.event.fire('EditModeBlockDragStop', { block: my, pep: pep, event: event });
            });

            my.getDragger().remove();
            my.setAttr('dragger', null);
        },

        addToDragArea: function blockTypeAddToDragArea(drag_area) {
            var my = this, elem = my.getElem(),
                block_type_id = elem.data('btid'),
                cID = elem.data('cid'),
                area = drag_area.getArea(),
                area_handle = area.getHandle(),
                arEnableGridContainer = area.getEnableGridContainer() ? 1 : 0,
                dragAreaBlockID = 0,
                dragAreaBlock = drag_area.getBlock(),
                is_inline = !!elem.data('supports-inline-add'),
                has_add = !!elem.data('has-add-template'),
                templates,
                k;

            if (dragAreaBlock) {
                dragAreaBlockID = dragAreaBlock.getId();
            }

            $.fn.dialog.closeAll();

            if (!has_add) {

                var postData = {
                    cID: cID,
                    arHandle: area_handle,
                    btID: block_type_id,
                    mode: 'edit',
                    processBlock: 1,
                    add: 1,
                    ccm_token: CCM_SECURITY_TOKEN,
                    arCustomTemplates: area.getCustomTemplates(),
                    dragAreaBlockID: dragAreaBlockID
                };

                templates = area.getCustomTemplates();
                if (templates) {
                    for (k in templates) {
                        postData[postData.length] = {
                            name: 'arCustomTemplates[' + k + ']',
                            value: templates[k]
                        };
                    }
                }

                $.getJSON(CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/add_block/submit', postData, function (response) {
                    $.fn.dialog.showLoader();
                    ConcreteToolbar.disableDirectExit();
                    $.get(CCM_DISPATCHER_FILENAME + '/ccm/system/block/render',
                        {
                            arHandle: area.getHandle(),
                            cID: cID,
                            bID: response.bID,
                            arEnableGridContainer: arEnableGridContainer
                        }, function (html) {
                            if (dragAreaBlock) {
                                dragAreaBlock.getContainer().after(html);
                            } else {
                                area.getBlockContainer().prepend(html);
                            }
                            $.fn.dialog.hideLoader();
                            _.defer(function () {
                                my.getEditMode().scanBlocks();
                            });

                            var panel = ConcretePanelManager.getByIdentifier('add-block');
                            if ( panel && panel.pinned() ) panel.show();
                        });
                });
            } else if (is_inline) {
                ConcreteEvent.fire('EditModeBlockAddInline', {
                    'selected': drag_area,
                    'area': drag_area.getArea(),
                    'cID': cID,
                    'arEnableGridContainer': arEnableGridContainer,
                    'btID': block_type_id,
                    'dragAreaBlockID': dragAreaBlockID
                });
            } else {
                var customTemplateString = '';
                templates = area.getCustomTemplates();
                    

                if (templates) {
                    for (k in templates) {
                        customTemplateString += '&arCustomTemplates[' + k + ']=' + templates[k];
                    }
                }

                $.fn.dialog.open({

                    onOpen: function () {
                        $(function () {
                            var placeholder = $('<div style="display:none" />');
                            drag_area.getElem().after(placeholder);
                            $('#ccm-block-form').concreteAjaxBlockForm({
                                'task': 'add',
                                'dragAreaBlockID': dragAreaBlockID,
                                dragArea: drag_area,
                                placeholder: placeholder
                            });
                        });
                    },
                    onDestroy: function() {
                        var panel = ConcretePanelManager.getByIdentifier('add-block');
                        if ( panel && panel.pinned() ) panel.show();
                    },
                    width: parseInt(elem.data('dialog-width'), 10),
                    height: parseInt(elem.data('dialog-height'), 10) + 20,
                    title: elem.data('dialog-title'),
                    href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/add_block?cID=' + cID + '&btID=' + block_type_id + '&arHandle=' + encodeURIComponent(area_handle) + customTemplateString
                });
            }
        }
    });


})(window, jQuery);
