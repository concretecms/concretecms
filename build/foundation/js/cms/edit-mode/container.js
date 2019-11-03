/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global _, Concrete, ConcreteAlert, ccmi18n, CCM_SECURITY_TOKEN, CCM_DISPATCHER_FILENAME */

import * as _ from 'underscore';

(function (window, $) {
    'use strict';

    /**
     * Container object used in the add panel. This is a BlockType subclass.
     * @type {Function}
     */
    var Container = Concrete.Container = function Container(elem, edit_mode, dragger) {
        this.init.apply(this, _(arguments).toArray());
    };

    Container.prototype = _.extend(Object.create(Concrete.BlockType.prototype), {

        removeElement: function() {
            this.getElem().remove();
        },

        addToDragArea: function ContainerAddToDragArea(drag_area) {
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
                containerID: elem.data('container-id'),
                ccm_token: CCM_SECURITY_TOKEN
            };

            if (dragAreaBlockID) {
                settings.dragAreaBlockID = dragAreaBlockID;
            }

            $.fn.dialog.showLoader();
            $.getJSON(CCM_DISPATCHER_FILENAME + '/ccm/system/page/add_container', settings, function (response) {
                my.handleAddResponse(response, area, dragAreaBlock);
            });
        },

        showSuccessfulAdd: function containerShowSuccessfulAdd() {
            ConcreteAlert.notify({
                'message': ccmi18n.addBlockContainerMsg,
                'title': ccmi18n.addBlockContainer
            });
        }

    });

})(window, jQuery);
