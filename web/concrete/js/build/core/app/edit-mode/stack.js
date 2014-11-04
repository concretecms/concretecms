(function (window, $, _, Concrete) {
    'use strict';

    var Stack = Concrete.Stack = function Stack(elem, edit_mode, dragger) {
        this.init.apply(this, _(arguments).toArray());
    };

    Stack.prototype = _.extend(Object.create(Concrete.Block.prototype), {

        pepStart: function stackPepStart(context, event, pep) {
            var my = this, panel;
            Concrete.Block.prototype.pepStart.call(this, context, event, pep);

            my.setAttr('closedPanel', _(ConcretePanelManager.getPanels()).find(function (panel) {
                return panel.isOpen;
            }));

            if ((panel = my.getAttr('closedPanel'))) {
                panel.hide();
            }
        },

        pepStop: function stackPepStop(context, event, pep) {
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

        addToDragArea: function StackAddToDragArea(drag_area) {
            var my = this, elem = my.getElem(),
                area = drag_area.getArea(),
                area_handle = area.getHandle(),
                dragAreaBlockID = 0,
                dragAreaBlock = drag_area.getBlock();

            if (dragAreaBlock) {
                dragAreaBlockID = dragAreaBlock.getId();
            }

            ConcretePanelManager.exitPanelMode();

            var settings = {
                cID: CCM_CID,
                arHandle: area_handle,
                stID: elem.data('cid'),
                atask: 'add_stack',
                ccm_token: CCM_SECURITY_TOKEN
            };

            if (dragAreaBlockID) {
                settings.dragAreaBlockID = dragAreaBlockID;
            }

            $.fn.dialog.showLoader();
            $.getJSON(CCM_DISPATCHER_FILENAME, settings, function (response) {
                my.handleAddResponse(response, area, dragAreaBlock);
            });
        },

        showSuccessfulAdd: function stackShowSuccessfulAdd() {
            ConcreteAlert.notify({
                'message': ccmi18n.addBlockStackMsg,
                'title': ccmi18n.addBlockStack
            });
        }

    });


}(window, jQuery, _, Concrete));
