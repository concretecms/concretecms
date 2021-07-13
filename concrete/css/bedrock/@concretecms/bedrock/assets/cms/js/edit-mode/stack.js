/* eslint-disable no-new, no-unused-vars, camelcase */
/* global _, Concrete, ConcreteAlert, ccmi18n, CCM_SECURITY_TOKEN, CCM_DISPATCHER_FILENAME */

import _ from 'underscore'

(function (window, $) {
    'use strict'

    /**
     * Stack object used in the stack panel. This is a BlockType subclass.
     * @type {Function}
     */
    var Stack = Concrete.Stack = function Stack(elem, edit_mode, dragger) {
        this.init.apply(this, _(arguments).toArray())
    }

    Stack.prototype = _.extend(Object.create(Concrete.BlockType.prototype), {

        removeElement: function() {
            this.getElem().remove()
        },

        addToDragArea: function StackAddToDragArea(drag_area) {
            var my = this; var elem = my.getElem()
            var area = drag_area.getArea()
            var area_handle = area.getHandle()
            var dragAreaBlockID = 0
            var dragAreaBlock = drag_area.getBlock()

            if (dragAreaBlock) {
                dragAreaBlockID = dragAreaBlock.getId()
            }

            $.fn.dialog.closeAll()

            var settings = {
                cID: elem.data('cid'),
                arHandle: area_handle,
                stID: elem.data('sid'),
                ccm_token: CCM_SECURITY_TOKEN
            }

            if (dragAreaBlockID) {
                settings.dragAreaBlockID = dragAreaBlockID
            }

            $.fn.dialog.showLoader()
            $.getJSON(CCM_DISPATCHER_FILENAME + '/ccm/system/page/add_stack', settings, function (response) {
                my.handleAddResponse(response, area, dragAreaBlock)
            })
        },

        showSuccessfulAdd: function stackShowSuccessfulAdd() {
            ConcreteAlert.notify({
                message: ccmi18n.addBlockStackMsg,
                title: ccmi18n.addBlockStack
            })
        }

    })
})(window, jQuery); // eslint-disable-line semi
