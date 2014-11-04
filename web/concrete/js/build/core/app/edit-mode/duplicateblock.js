(function (window, $, _, Concrete) {
    'use strict';


    var DuplicateBlock = Concrete.DuplicateBlock = function DuplicateBlock(elem, edit_mode) {
        this.init.apply(this, _.toArray(arguments));
    };

    DuplicateBlock.prototype = _.extend(Object.create(Concrete.Block.prototype), {

        init: function duplicateBlockInit(elem, edit_mode) {
            var my = this;
            Concrete.Block.prototype.init.call(my, elem, edit_mode, elem.find('.block-content'));
        },

        pepStart: function duplicateBlockPepStart(context, event, pep) {
            var my = this, panel;
            Concrete.Block.prototype.pepStart.call(this, context, event, pep);

            my.setAttr('closedPanel', _(ConcretePanelManager.getPanels()).find(function (panel) {
                return panel.isOpen;
            }));

            if ((panel = my.getAttr('closedPanel'))) {
                panel.hide();
            }
        },

        pepStop: function duplicateBlockPepStop(context, event, pep) {
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

        addToDragArea: function DuplicateBlockAddToDragArea(drag_area) {
            var my = this, elem = my.getElem(),
                block_type_id = elem.data('btid'),
                area = drag_area.getArea(),
                area_handle = area.getHandle(),
                dragAreaBlockID = 0,
                cID = elem.data('cid'),
                dragAreaBlock = drag_area.getBlock(),
                pcID = elem.data('pcid');

            if (dragAreaBlock) {
                dragAreaBlockID = dragAreaBlock.getId();
            }

            ConcretePanelManager.exitPanelMode();
            jQuery.fn.dialog.closeAll();
            jQuery.fn.dialog.showLoader();

            var settings = {
                cID: cID,
                arHandle: area_handle,
                btID: block_type_id,
                mode: 'edit',
                processBlock: 1,
                add: 1,
                btask: 'alias_existing_block',
                pcID: [pcID],
                ccm_token: CCM_SECURITY_TOKEN
            };
            if (dragAreaBlockID) {
                settings.dragAreaBlockID = dragAreaBlockID;
            }
            $.getJSON(CCM_DISPATCHER_FILENAME, settings, function (response) {
                my.handleAddResponse(response, area, dragAreaBlock, function () {
                    ConcreteEvent.fire('EditModeAddClipboardComplete', {
                        block: my
                    });
                });
            });
        }
    });

}(window, jQuery, _, Concrete));
