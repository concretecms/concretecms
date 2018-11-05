/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global _, Concrete, ConcretePanelManager, CCM_CID, CCM_DISPATCHER_FILENAME, CCM_SECURITY_TOKEN */

;(function(window, $) {
    'use strict';

    /**
     * StackBlock object used only in panels. This allows us to drag blocks out from a stack panel.
     * @type {Function}
     */
    var StackBlock = Concrete.StackBlock = function StackBlock(elem, stack, edit_mode, dragger) {
        this.init.apply(this, _.toArray(arguments));
    };

    StackBlock.prototype = _.extend(Object.create(Concrete.BlockType.prototype), {

        init: function stackBlockInit(elem, stack, edit_mode, dragger, default_area) {
            Concrete.BlockType.prototype.init.call(this, elem, edit_mode, dragger, default_area);
            this.setAttr('stack', stack);
        },

        removeElement: function() {
            $.pep.unbind(this.getPeper());
        },

        addToDragArea: function StackBlockAddToDragArea(drag_area) {
            var my = this, elem = my.getElem(),
                block_type_id = elem.data('btid'),
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
                bID: elem.data('block-id'),
                arHandle: area_handle,
                btID: block_type_id,
                mode: 'edit',
                processBlock: 1,
                add: 1,
                btask: 'alias_existing_block',
                pcID: [elem.data('cID')],
                ccm_token: CCM_SECURITY_TOKEN
            };
            if (dragAreaBlockID) {
                settings.dragAreaBlockID = dragAreaBlockID;
            }
            $.getJSON(CCM_DISPATCHER_FILENAME, settings, function (response) {
                my.handleAddResponse(response, area, dragAreaBlock);
            });
        }

    });

})(window, jQuery);
