(function (window, $, _, Concrete) {
    'use strict';

    /**
     * Stack object used in the stack panel. This is a BlockType subclass.
     * @type {Function}
     */
    var Stack = Concrete.Stack = function Stack(elem, edit_mode, dragger) {
        this.init.apply(this, _(arguments).toArray());
    };

    Stack.prototype = _.extend(Object.create(Concrete.BlockType.prototype), {

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
