/* eslint-disable no-new, no-unused-vars, camelcase */
/* global _, Concrete, ConcreteEvent, ConcretePanelManager, CCM_SECURITY_TOKEN, CCM_DISPATCHER_FILENAME */

import _ from 'underscore'

;(function(window, $) {
    'use strict'

    /**
     * ClipBoard block used in panels
     * @type {Function}
     */
    var DuplicateBlock = Concrete.DuplicateBlock = function DuplicateBlock(elem, edit_mode, default_area) {
        this.init.apply(this, _.toArray(arguments))
    }

    DuplicateBlock.prototype = _.extend(Object.create(Concrete.BlockType.prototype), {

        init: function duplicateBlockInit(elem, edit_mode, default_area) {
            var my = this
            Concrete.BlockType.prototype.init.call(my, elem, edit_mode, elem.find('.block-content'), default_area)
        },

        handleDefaultArea: function() {
            var my = this
            $.pep.unbind(my.getPeper())
            my.getPeper().click(function (e) {
                my.handleClick()

                return false
            }).css({
                cursor: 'pointer'
            }).children('.block-name').css({
                cursor: 'pointer'
            })
        },

        removeElement: function() {
            this.getElem().remove()
        },

        addToDragArea: function DuplicateBlockAddToDragArea(drag_area) {
            var my = this; var elem = my.getElem()
            var block_type_id = elem.data('btid')
            var area = drag_area.getArea()
            var area_handle = area.getHandle()
            var dragAreaBlockID = 0
            var cID = elem.data('cid')
            var dragAreaBlock = drag_area.getBlock()
            var pcID = elem.data('pcid')

            if (dragAreaBlock) {
                dragAreaBlockID = dragAreaBlock.getId()
            }

            ConcretePanelManager.exitPanelMode()
            $.fn.dialog.closeAll()
            $.fn.dialog.showLoader()

            var url = CCM_DISPATCHER_FILENAME + `/ccm/system/block/process/alias/${cID}/${area_handle}/${pcID}/${dragAreaBlockID || '0'}/0`

            $.getJSON(url, { ccm_token: CCM_SECURITY_TOKEN }, function (response) {
                my.handleAddResponse(response, area, dragAreaBlock, function () {
                    ConcreteEvent.fire('EditModeAddClipboardComplete', {
                        block: my
                    })
                })
            })
        }
    })
})(window, jQuery); // eslint-disable-line semi
