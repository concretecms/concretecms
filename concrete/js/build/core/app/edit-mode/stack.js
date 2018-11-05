/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global _, Concrete, ConcreteAlert, ccmi18n, CCM_SECURITY_TOKEN, CCM_DISPATCHER_FILENAME */ 

(function (window, $) {
    'use strict';

    /**
     * Stack object used in the stack panel. This is a BlockType subclass.
     * @type {Function}
     */
    var Stack = Concrete.Stack = function Stack(elem, edit_mode, dragger) {
        this.init.apply(this, _(arguments).toArray());
    };

    Stack.prototype = _.extend(Object.create(Concrete.BlockType.prototype), {

        removeElement: function() {
            this.getElem().remove();
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

            $.fn.dialog.closeAll();

            var settings = {
                cID: elem.data('cid'),
                arHandle: area_handle,
                stID: elem.data('sid'),
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

})(window, jQuery);
