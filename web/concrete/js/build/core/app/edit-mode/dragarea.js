(function (window, $, _, Concrete) {
    'use strict';

    /**
     * Drag Area that we create for dropping the blocks into
     * @param {jQuery}   elem  The drag area html element
     * @param {Area} area  The area it belongs to
     * @param {Block} block The block that this drag_area is above, this may be null.
     */
    var DragArea = Concrete.DragArea = function DragArea(elem, area, block) {
        this.init.apply(this, _.toArray(arguments));
    };

    DragArea.prototype = {

        init: function dragAreaInit(elem, area, block) {
            var my = this;

            Concrete.createGetterSetters.call(my, {
                block: block,
                active: true,
                elem: elem,
                area: area,
                isContender: false,
                isSelectable: false,
                animationLength: 500
            });

            my.bindEvent('EditModeContenders', function (e, data) {
                my.setIsContender(_.contains(data, my));
            });

            my.bindEvent('EditModeSelectableContender', function (e, data) {
                my.setIsSelectable(data === my);
            });
        },

        destroy: function dragAreaDestroy() {
            var my = this;

            my.getElem().remove();
        },

        bindEvent: function dragAreaBindEvent(event, handler) {
            return Concrete.EditMode.prototype.bindEvent.apply(this, _(arguments).toArray());
        },

        getActive: function dragAreaGetActive() {
            // If Area is inactive, this has to be inactive.
            return this.getArea().getActive() && this.getAttr('active');
        },

        /**
         * Is DragArea selectable
         * @param  {Pep}       pep   The active Pep
         * @param  {Block}     block The dragging Block
         * @param  {Event}     event The relevant event
         * @return {Boolean}         Is the dragarea selectable
         */
        isSelectable: function dragAreaIsSelectable(pep, block) {
            return pep.isOverlapping(block.getDragger(), this.getElem());
        },

        /**
         * Handle setting the DragArea to selectable, this is generally a visual change.
         * @param  {Boolean} is_selectable true/false
         * @return {Boolean}               Success, always true.
         */
        setIsSelectable: function dragAreaSetIsSelectable(is_selectable) {
            var my = this;

            if (is_selectable && !my.getIsSelectable()) {
                my.getElem().addClass('ccm-area-drag-area-selectable');
            } else if (!is_selectable && my.getIsSelectable()) {
                my.getElem().removeClass('ccm-area-drag-area-selectable');
            }
            my.setAttr('isSelectable', is_selectable);
            return true;
        },

        /**
         * Is this DragArea a contender
         * @param  {Pep}     pep   The relevant Pep object
         * @param  {Block}   block The dragging Block
         * @return {Boolean}       true/false
         */
        isContender: function dragAreaIsContender(pep, block) {
            var my = this;
            _.identity(pep); // This does nothing but quiet the lint

            return (my.getBlock() !== block);
        },

        /**
         * Handle setting as contender
         * @param  {Boolean} is_contender Is this a contender
         * @return {Boolean}              Success, always true.
         */
        setIsContender: function dragAreaSetIsContender(is_contender) {
            var my = this;
            if (is_contender && !my.getIsContender()) {
                _.defer(function () {
                    my.getElem().addClass('ccm-area-drag-area-contender');
                });
            } else if (!is_contender && my.getIsContender()) {
                _.defer(function () {
                    my.getElem().removeClass('ccm-area-drag-area-contender');
                });
            }
            my.setAttr('isContender', is_contender);
            return true;
        },

        /**
         * Get the distance from the center of the DragArea to the center of a block.
         * @param  {Block}  block The block to measure
         * @return {double}       The distance from center to center
         */
        centerDistanceToBlock: function (block) {
            var my = this;

            var block_elem = block.getDragger(),
                block_center = {
                    x: block_elem.offset().left + block_elem.width() / 2,
                    y: block_elem.offset().top + block_elem.height() / 2
                },
                my_elem = my.getElem(),
                my_center = {
                    x: my_elem.offset().left + my_elem.width() / 2,
                    y: my_elem.offset().top + my_elem.height() / 2
                };

            return Math.sqrt(Math.pow(Math.abs(block_center.x - my_center.x), 2) + Math.pow(Math.abs(block_center.y - my_center.y), 2));
        }

    };



}(window, jQuery, _, Concrete));
