(function (window, $, _, Concrete) {
    'use strict';

    /**
     * Area object, used for managing areas
     * @param {jQuery}   elem      The area's HTML element
     * @param {EditMode} edit_mode The EditMode instance
     */
    var Area = Concrete.Area = function Area(elem, edit_mode) {
        this.init.apply(this, _(arguments).toArray());
    };

    Area.prototype = {

        init: function areaInit(elem, edit_mode) {
            var my = this;
            elem.data('Concrete.area', my);

            Concrete.createGetterSetters.call(my, {
                id: elem.data('area-id'),
                active: true,
                blockTemplate: _(elem.children('script[role=area-block-wrapper]').html()).template(),
                elem: elem,
                totalBlocks: 0,
                enableGridContainer: elem.data('area-enable-grid-container'),
                customTemplates: elem.data('area-custom-templates'),
                handle: elem.data('area-handle'),
                dragAreas: [],
                blocks: [],
                editMode: edit_mode,
                maximumBlocks: parseInt(elem.data('maximumBlocks'), 10),
                blockTypes: elem.data('accepts-block-types').toLowerCase().split(' '),
                blockContainer: elem.children('.ccm-area-block-list')
            });
            my.id = my.getId();
            my.setTotalBlocks(0); // we also need to update the DOM which this does.

            my.bindEvent('EditModeBlockAddInline.area', function(e, data) {
                if (data.area === my) {
                    my.setTotalBlocks(my.getTotalBlocks() + 1);
                }
            });
            my.bindEvent('EditModeAddBlocksToArea.area', function(e, data) {
                if (data.area === my) {
                    my.getEditMode().setNextBlockArea(my);
                    var panelButton = $('[data-launch-panel="add-block"]');
                    panelButton.click();
                }
            });
        },

        /**
         * Get this area in the passed edit mode context
         * @param Concrete.EditMode edit_mode
         * @returns Concrete.Area|null
         */
        inEditMode: function areaInEditMode(edit_mode) {
            return edit_mode.getAreaByID(this.getId());
        },

        /**
         * Handle unbinding.
         */
        destroy: function areaDestroy() {
            var my = this;
            if (my.getAttr('menu')) {
                my.getAttr('menu').destroy();
            }

            Concrete.event.unbind(".ccm-area-a" + this.getId());

            my.reset();
        },

        reset: function areaReset() {
            var my = this;
            _(my.getDragAreas()).each(function (drag_area) {
                drag_area.destroy();
            });

            _(my.getBlocks()).each(function (block) {
                block.destroy();
            });

            my.setBlocks([]);
            my.setDragAreas([]);

            my.setTotalBlocks(0);
        },

        bindEvent: function areaBindEvent(event, handler) {
            return Concrete.EditMode.prototype.bindEvent.call(this, event + ".ccm-area-a" + this.getId(), handler);
        },

        scanBlocks: function areaScanBlocks() {
            var my = this, type, block;

            my.reset();
            my.addDragArea(null);

            $('div.ccm-block-edit[data-area-id=' + my.getId() + ']', this.getElem()).each(function () {
                var me = $(this), handle = me.data('block-type-handle');

                if (handle === 'core_area_layout') {
                    type = Concrete.Layout;
                } else if (handle === 'core_stack_display') {
                    type = Concrete.StackDisplay;
                } else {
                    type = Concrete.Block;
                }

                block = new type(me, my.getEditMode());
                block.setArea(my);

                my.addBlock(block);
            });
        },

        getBlockByID: function areaGetBlockByID(bID) {
            var my = this;
            return _.findWhere(my.getBlocks(), {id: bID});
        },

        getMenuElem: function areaGetMenuElem() {
            var my = this;
            return $('[data-area-menu=area-menu-a' + my.getId() + ']');
        },

        bindMenu: function areaBindMenu() {
            var my = this,
                elem = my.getElem(),
                totalBlocks = my.getTotalBlocks(),
                $menuElem = my.getMenuElem(),
                menuHandle;

            if (totalBlocks > 0) {
                menuHandle = '#area-menu-footer-' + my.getId();
            } else {
                menuHandle = 'div[data-area-menu-handle=' + my.getId() + ']';
            }
            if (my.getAttr('menu')) {
                my.getAttr('menu').destroy();
            }

            var menu_config = {
                'handle': menuHandle,
                'highlightClassName': 'ccm-area-highlight',
                'menuActiveClass': 'ccm-area-highlight',
                'menu': $('[data-area-menu=' + elem.attr('data-launch-area-menu') + ']')
            };

            if (my.getElem().hasClass('ccm-global-area')) {
                menu_config.menuActiveClass += " ccm-global-area-highlight";
                menu_config.highlightClassName += " ccm-global-area-highlight";
            }

            my.setAttr('menu', new ConcreteMenu(elem, menu_config));

            $menuElem.find('a[data-menu-action=add-inline]')
                .off('click.edit-mode')
                .on('click.edit-mode', function (e) {
                    // we are going to place this at the END of the list.
                    var dragAreaLastBlock = false;
                    _.each(my.getBlocks(), function (block) {
                        dragAreaLastBlock = block;
                    });
                    Concrete.event.fire('EditModeBlockAddInline', {
                        area: my,
                        cID: CCM_CID,
                        btID: $(this).data('block-type-id'),
                        arGridMaximumColumns: $(this).attr('data-area-grid-maximum-columns'),
                        event: e,
                        dragAreaBlock: dragAreaLastBlock,
                        btHandle: $(this).data('block-type-handle')
                    });
                    return false;
                });

            $menuElem.find('a[data-menu-action=edit-container-layout]')
                .off('click.edit-mode')
                .on('click.edit-mode', function (e) {
                    // we are going to place this at the END of the list.
                    var $link = $(this);
                    var bID = parseInt($(this).attr('data-container-layout-block-id'));
                    var editor = Concrete.getEditMode();
                    var block = _.findWhere(editor.getBlocks(), {id: bID});
                    Concrete.event.fire('EditModeBlockEditInline', {
                        block: block,
                        arGridMaximumColumns: $link.attr('data-area-grid-maximum-columns'),
                        event: e
                    });
                    return false;
                });

            $menuElem.find('a[data-menu-action=edit-container-layout-style]')
                .off('click.edit-mode')
                .on('click.edit-mode', function (e) {
                    e.preventDefault();
                    // we are going to place this at the END of the list.
                    var $link = $(this);
                    var bID = parseInt($(this).attr('data-container-layout-block-id'));
                    var editor = Concrete.getEditMode();
                    var block = _.findWhere(editor.getBlocks(), {id: bID});
                    Concrete.event.fire('EditModeBlockEditInline', {
                        block: block, event: e, action: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/block/design'
                    });
                });

            $menuElem.find('a[data-menu-action=area-add-block]')
                .off('click.edit-mode')
                .on('click.edit-mode', function(e) {
                    var max = my.getMaximumBlocks();
                    if (max < 0 || max > my.getTotalBlocks()) {
                        Concrete.event.fire('EditModeAddBlocksToArea', {
                            area: my
                        });
                    } else {
                        ConcreteAlert.error({'message' : ccmi18n.fullArea});
                    }
                    return false;
                });

            my.bindEvent('ConcreteMenuShow', function(e, data) {
                if (data.menu == my.getAttr('menu')) {
                    var max = my.getMaximumBlocks(),
                        list_item = data.menu.$menuPointer.find('a[data-menu-action=area-add-block]').parent();

                    if (max < 0 || max > my.getTotalBlocks()) {
                        list_item.show();
                    } else {
                        list_item.hide();
                    }
                }
            });


            $menuElem.find('a[data-menu-action=edit-area-design]')
                .off('click.edit-mode')
                .on('click.edit-mode', function (e) {
                    e.preventDefault();
                    ConcreteToolbar.disable();
                    my.getElem().addClass('ccm-area-inline-edit-disabled');
                    var postData = {
                        'arHandle': my.getHandle(),
                        'cID': CCM_CID
                    };

                    my.bindEvent('EditModeExitInline', function (e) {
                        Concrete.event.unsubscribe(e);
                        my.getEditMode().destroyInlineEditModeToolbars();
                    });

                    $.ajax({
                        type: 'GET',
                        url: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/area/design',
                        data: postData,
                        success: function (r) {
                            var $container = my.getElem();
                            my.getEditMode().loadInlineEditModeToolbars($container, r);
                            $.fn.dialog.hideLoader();
                        }
                    });


                });
        },

        /**
         * Does this area accept a specific block type handle
         * @param type_handle the block type handle
         * @returns {bool}
         */
        acceptsBlockType: function areaAcceptsBlockType(type_handle) {
            return _(this.getBlockTypes()).contains(type_handle.toLowerCase());
        },

        /**
         * Add block to area
         * @param  {Block}   block     block to add
         * @param  {Block}   sub_block The block that should be above the added block
         * @return {Boolean}           Success, always true
         */
        addBlock: function areaAddBlock(block, sub_block) {
            var my = this;
            if (sub_block) {
                return this.addBlockToIndex(block, _(my.getBlocks()).indexOf(sub_block) + 1);
            }
            return this.addBlockToIndex(block, my.getBlocks().length);
        },

        setTotalBlocks: function (totalBlocks) {
            this.setAttr('totalBlocks', totalBlocks);
            this.getElem().attr('data-total-blocks', totalBlocks);
        },

        /**
         * Add to specific index, pipes to addBlock
         * @param  {Block}   block Block to add
         * @param  {int}     index Index to add to
         * @return {Boolean}       Success, always true
         */
        addBlockToIndex: function areaAddBlockToIndex(block, index) {
            var totalBlocks = this.getTotalBlocks(),
                blocks = this.getBlocks(),
                totalHigherBlocks = totalBlocks - index;

            block.setArea(this);
            this.setTotalBlocks(totalBlocks + 1);

            // any blocks with indexes higher than this one need to have them incremented
            if (totalHigherBlocks > 0) {
                var updateBlocksArray = [];
                for (var i = 0; i < blocks.length; i++) {
                    if (i >= index) {
                        updateBlocksArray[i + 1] = blocks[i];
                    } else {
                        updateBlocksArray[i] = blocks[i];
                    }
                }
                updateBlocksArray[index] = block;
                this.setBlocks(updateBlocksArray);
            } else {
                this.getBlocks()[index] = block;
            }

            this.addDragArea(block);

            // ensure that the DOM attributes are correct
            block.getElem().attr("data-area-id", this.getId());

            return true;
        },

        /**
         * Remove block from area
         * @param  {Block}   block The block to remove.
         * @return {Boolean}       Success, always true.
         */
        removeBlock: function areaRemoveBlock(block) {
            var my = this, totalBlocks = my.getTotalBlocks();

            my.getElem().removeClass('ccm-parent-menu-item-active');

            block.getContainer().remove();
            my.setBlocks(_(my.getBlocks()).without(block));

            my.setTotalBlocks(totalBlocks - 1);

            var drag_area = _.first(_(my.getDragAreas()).filter(function (drag_area) {
                return drag_area.getBlock() === block;
            }));
            if (drag_area) {
                drag_area.getElem().remove();
                my.setDragAreas(_(my.getDragAreas()).without(drag_area));
            }

            if (!my.getTotalBlocks()) {
                // we have to destroy the old menu and create it anew
                my.bindMenu();
            }

            return true;
        },

        /**
         * Add a drag area
         * @param  {Block}    block The block to add this area below.
         * @return {DragArea}       The added DragArea
         */
        addDragArea: function areaAddDragArea(block) {
            var my = this, elem, drag_area;

            if (!block) {
                if (my.getDragAreas().length) {
                    throw new Error('No block supplied');
                }
                elem = $('<div class="ccm-area-drag-area"/>');
                drag_area = new Concrete.DragArea(elem, my, block);
                my.getBlockContainer().prepend(elem);
            } else {
                elem = $('<div class="ccm-area-drag-area"/>');
                drag_area = new Concrete.DragArea(elem, my, block);
                block.getContainer().after(elem);
            }

            if (!my.getElem().parent().is('#ccm-stack-container')) {
                elem.text(_(ccmi18n.emptyArea).template({
                    area_handle: my.getElem().data('area-display-name')
                }));
            }
            my.getDragAreas().push(drag_area);
            return drag_area;
        },

        /**
         * Find the contending DragArea's
         * @param  {Pep}      pep   The Pep object from the event.
         * @param  {Block|Stack}    block The Block object from the event.
         * @return {Array}          Array of all drag areas that are capable of accepting the block.
         */
        contendingDragAreas: function areaContendingDragAreas(pep, block) {
            var my = this, max_blocks = my.getMaximumBlocks();

            if (block instanceof Concrete.Stack || block.getHandle() === 'core_stack_display') {
                return _(my.getDragAreas()).filter(function (drag_area) {
                    return drag_area.isContender(pep, block);
                });
            } else if ((max_blocks > 0 && my.getBlocks().length >= max_blocks) || !_(my.getBlockTypes()).contains(block.getHandle())) {
                return [];
            }
            return _(my.getDragAreas()).filter(function (drag_area) {
                return drag_area.isContender(pep, block);
            });
        }
    };

}(window, jQuery, _, Concrete));
