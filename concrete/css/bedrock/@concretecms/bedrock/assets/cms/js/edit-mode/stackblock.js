/* eslint-disable no-new, no-unused-vars, camelcase */
/* global _, Concrete, ConcretePanelManager, CCM_CID, CCM_DISPATCHER_FILENAME, CCM_SECURITY_TOKEN */

import _ from 'underscore'

;(function(window, $) {
    'use strict'

    /**
     * StackBlock object used only in panels. This allows us to drag blocks out from a stack panel.
     * @type {Function}
     */
    var StackBlock = Concrete.StackBlock = function StackBlock(elem, stack, edit_mode, dragger) {
        this.init.apply(this, _.toArray(arguments))
    }

    StackBlock.prototype = _.extend(Object.create(Concrete.BlockType.prototype), {

        init: function stackBlockInit(elem, stack, edit_mode, dragger, default_area) {
            Concrete.BlockType.prototype.init.call(this, elem, edit_mode, dragger, default_area)
            this.setAttr('stack', stack)
        },

        removeElement: function() {
            $.pep.unbind(this.getPeper())
        },

        addToDragArea: function StackBlockAddToDragArea(drag_area) {
            var my = this; var elem = my.getElem()
            var block_type_id = elem.data('btid')
            var area = drag_area.getArea()
            var area_handle = area.getHandle()
            var dragAreaBlockID = 0
            var dragAreaBlock = drag_area.getBlock()

            if (dragAreaBlock) {
                dragAreaBlockID = dragAreaBlock.getId()
            }

            ConcretePanelManager.exitPanelMode()

            var url = CCM_DISPATCHER_FILENAME + `/ccm/system/block/process/alias/${CCM_CID}/${area_handle}/${elem.data('cID')}/${dragAreaBlockID || '0'}/0`

            $.getJSON(url, { ccm_token: CCM_SECURITY_TOKEN }, function (response) {
                my.handleAddResponse(response, area, dragAreaBlock)
            })
        }

    })
})(window, jQuery); // eslint-disable-line semi
