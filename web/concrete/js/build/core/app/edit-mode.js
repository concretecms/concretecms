/* jshint browser: true, unused:vars, undef:true */
/* global escape, jQuery, _, Concrete, CCM_CID, CCM_TOOLS_PATH, ConcreteEvent, ConcreteMenuManager, ConcreteToolbar,
 CCM_DISPATCHER_FILENAME, ConcreteAlert, ccmi18n, ConcreteMenu, CCM_SECURITY_TOKEN, ConcretePanelManager,
 CCM_DISPATCHER_FILENAME, CCMEditMode */
/**
 * concrete5 in context editing
 */
(function (window, $, _, Concrete) {
    'use strict';

    /**
     * Edit mode object for managing editing.
     */
    var EditMode = Concrete.EditMode = function EditMode(options) {
        var my = this;
        options = options || {};
        options = $.extend({
            'notify': true
        }, options);

        Concrete.createGetterSetters.call(my, {
            dragging: false,
            areas: [],
            blocks: [],
            selectedCache: [],
            selectedThreshold: 5,
            dragAreaBlacklist: []
        });

        Concrete.event.bind('PanelLoad', function editModePanelOpenEventHandler(event, data) {
            my.panelOpened(data.panel, data.element);
        });

        Concrete.event.bind('EditModeBlockEditInline', function (event, data) {
            var block = data.block,
                area = block.getArea(),
                postData = [
                    {name: 'btask', value: 'edit'},
                    {name: 'cID', value: block.getCID()},
                    {name: 'arHandle', value: area.getHandle()},
                    {name: 'arGridColumnSpan', value: data.arGridColumnSpan},
                    {name: 'aID', value: area.getId()},
                    {name: 'bID', value: block.getId()}
                ],
                bID = block.getId(),
                $container = block.getElem(),
                prop;

            if (block.menu) {
                block.menu.destroy();
            }
            if (data.postData) {
                for (prop in data.postData) {
                    if (data.postData.hasOwnProperty(prop)) {
                        postData.push({name: prop, value: data.postData[prop]});
                    }
                }
            }

            Concrete.event.bind('EditModeExitInline', function (e) {
                Concrete.event.unbind(e);
                e.stopPropagation();
                var action = CCM_TOOLS_PATH + '/edit_block_popup?cID=' + block.getCID() + '&bID=' + block.getId() + '&arHandle=' + escape(area.getHandle()) + '&btask=view_edit_mode';
                $.fn.dialog.showLoader();
                $.get(action,
                    function (r) {
                        var block = area.getBlockByID(bID);
                        var newBlock = block.replace(bID, r);
                        _.defer(function () {
                            ConcreteEvent.fire('EditModeExitInlineComplete', {
                                block: newBlock
                            });
                            my.destroyInlineEditModeToolbars();
                        });
                    }
                );
            });

            ConcreteMenuManager.disable();
            ConcreteToolbar.disable();
            area.getElem().addClass('ccm-area-inline-edit-disabled');
            $container.addClass('ccm-block-edit-inline-active');

            $.ajax({
                type: 'GET',
                url: CCM_TOOLS_PATH + '/edit_block_popup',
                data: postData,
                success: function (r) {
                    $container.html(r);
                    my.loadInlineEditModeToolbars($container);
                    $.fn.dialog.hideLoader();
                }
            });
        });

        Concrete.event.bind('EditModeBlockAddInline', function (event, data) {
            var area = data.area,
                selected = data.selected,
                btID = data.btID,
                cID = data.cID,
                postData = [
                    {name: 'btask', value: 'edit'},
                    {name: 'cID', value: cID},
                    {name: 'arGridColumnSpan', value: data.arGridColumnSpan},
                    {name: 'arHandle', value: area.getHandle()},
                    {name: 'btID', value: btID}
                ], dragAreaBlock, dragAreaBlockID, elem;
            if (selected) {
                elem = selected.getElem();
                dragAreaBlock = selected.getBlock();
            } else {
                elem = area.getElem();
                dragAreaBlock = data.dragAreaBlock;
            }

            if (dragAreaBlock) {
                dragAreaBlockID = dragAreaBlock.getId();
            }

            ConcreteMenuManager.disable();
            ConcreteToolbar.disable();
            jQuery.fn.dialog.closeAll();

            $('div.ccm-area').addClass('ccm-area-inline-edit-disabled');

            $.fn.dialog.showLoader();

            if (area.menu) {
                area.menu.destroy();
            }

            Concrete.event.unsubscribe('EditModeExitInline');
            Concrete.event.bind('EditModeExitInline', function () {
                $('#a' + area.getId() + '-bt' + btID).remove();
                my.destroyInlineEditModeToolbars();
            });
            $.ajax({
                type: 'GET',
                url: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/add_block',
                data: postData,
                success: function (r) {
                    var $container = $('<div id="a' + area.getId() + '-bt' + btID + '" class="ccm-block-edit-inline-active">' + r + '</div>');
                    elem.addClass("ccm-area-edit-inline-active");
                    elem.append($container);
                    $(function () {
                        elem.find('#ccm-block-form').concreteAjaxBlockForm({
                            'task': 'add',
                            'btSupportsInlineAdd': true,
                            'dragAreaBlockID': dragAreaBlockID
                        });
                    });
                    my.loadInlineEditModeToolbars($container);
                },
                complete: function () {
                    $.fn.dialog.hideLoader();
                }
            });
        });

        Concrete.event.bind('EditModeBlockAddToClipboard', function (event, data) {
            var block = data.block, area = block.getArea();
            ConcreteToolbar.disableDirectExit();
            // got to grab the message too, eventually
            $.ajax({
                type: 'POST',
                url: CCM_TOOLS_PATH + '/pile_manager',
                data: 'cID=' + block.getCID() + '&bID=' + block.getId() + '&arHandle=' + encodeURIComponent(area.getHandle()) + '&btask=add&scrapbookName=userScrapbook',
                success: function (resp) {
                    ConcreteAlert.notify({
                        'message': ccmi18n.copyBlockToScrapbookMsg,
                        'title': ccmi18n.copyBlockToScrapbook
                    });
                }});
        });

        Concrete.event.bind('EditModeBlockDelete', function (event, data) {
            var block = data.block;
            block.delete(data.message);
            ConcreteEvent.fire('EditModeBlockDeleteComplete', {
                block: block
            });
        });


        var $body = $(window.document.body),
            scrolling = false,
            scroll_buffer = 100;

        function scrollLoop(block, element, amount, step, test, scroll_method, axis) {
            if (test.call()) {
                scrolling = true;
                var pos_start = scroll_method.call(element),
                    pos_new = pos_start + amount,
                    args = _.toArray(arguments),
                    pos = block.getDraggerPosition();

                scroll_method.call(element, pos_new);

                pos[axis] -= pos_start - scroll_method.call(element);
                block.renderPosition();

                _.defer(function () {
                    scrollLoop.apply(this, args);
                }, step);
            } else {
                scrolling = false;
            }
        }

        Concrete.event.bind('EditModeBlockDrag', _.throttle(function editModeEditModeBlockDragEventHandler(event, data) {
            if (!my.getDragging()) {
                return;
            }
            var block = data.block, pep = data.pep,
                contenders = _.flatten(_(my.getAreas()).map(function (area) {
                    var drag_areas = area.contendingDragAreas(pep, block);
                    return drag_areas;
                }), true);

            _.defer(function () {
                Concrete.event.fire('EditModeContenders', contenders);
                my.selectContender(pep, block, contenders, data.event);
            });

            if (!scrolling) {
                // Vertical
                scrollLoop(block, $body, 2, 10, function () {
                    var pos = block.getDraggerPosition().y - $body.scrollTop();
                    return block.getDragging() && $(window).height() - pos <= scroll_buffer;
                }, $.fn.scrollTop, 'y');
                scrollLoop(block, $body, -2, 10, function () {
                    var pos = block.getDraggerPosition().y - $body.scrollTop();
                    return block.getDragging() && pos <= scroll_buffer;
                }, $.fn.scrollTop, 'y');

                // Horizontal
                scrollLoop(block, $body, 2, 10, function () {
                    var pos = block.getDraggerPosition().x - $body.scrollLeft();
                    return block.getDragging() && $(window).width() - pos <= scroll_buffer;
                }, $.fn.scrollLeft, 'x');
                scrollLoop(block, $body, -2, 10, function () {
                    var pos = block.getDraggerPosition().x - $body.scrollLeft();
                    return block.getDragging() && pos <= scroll_buffer;
                }, $.fn.scrollLeft, 'x');
            }

        }, 250, {trailing: false}));

        Concrete.event.bind('EditModeBlockDragStop', function editModeEditModeBlockDragStopEventHandler() {
            Concrete.event.fire('EditModeContenders', []);
            Concrete.event.fire('EditModeSelectableContender');
            my.setDragging(false);
        });

        Concrete.event.bind('EditModeBlockMove', function editModeEditModeBlockMoveEventHandler(e, data) {
            var block = data.block,
                targetArea = data.targetArea,
                sourceArea = data.sourceArea,
                send = {
                    ccm_token: window.CCM_SECURITY_TOKEN,
                    btask: 'ajax_do_arrange',
                    area: targetArea.getId(),
                    sourceArea: sourceArea.getId(),
                    block: block.getId(),
                    blocks: {}
                };

            _(targetArea.getBlocks()).each(function (block, key) {
                send.blocks[key] = block.getId();
            });
            block.bindMenu();
            var loading = false, timeout = setTimeout(function () {
                loading = true;
                $.fn.dialog.showLoader();
            }, 150);

            $.concreteAjax({
                url: CCM_DISPATCHER_FILENAME + '/ccm/system/page/arrange_blocks?cID=' + block.getCID(),
                data: send,
                success: function (r) {
                    $.fn.dialog.hideLoader();
                    clearTimeout(timeout);
                }
            });

        });

        Concrete.event.bind('EditModeBlockDragStart', function editModeEditModeBlockDragStartEventHandler() {
            my.setDragging(true);
        });

        my.scanBlocks();

        Concrete.getEditMode = function () {
            return my;
        };

        if (options.notify) {
            ConcreteAlert.notify({
                'message': ccmi18n.editModeMsg,
                'title': ccmi18n.editMode
            });
        }
    };

    /**
     * Area object, used for managing areas
     * @param {jQuery}   elem      The area's HTML element
     * @param {EditMode} edit_mode The EditMode instance
     */
    var Area = Concrete.Area = function Area(elem, edit_mode) {
        var my = this;
        elem.data('Concrete.area', my);

        Concrete.createGetterSetters.call(my, {
            id: elem.data('area-id'),
            elem: elem,
            totalBlocks: 0,
            handle: elem.data('area-handle'),
            dragAreas: [],
            blocks: [],
            editMode: edit_mode,
            maximumBlocks: parseInt(elem.data('maximumBlocks'), 10),
            blockTypes: elem.data('accepts-block-types').split(' ')
        });

        my.id = my.getId();
        my.setTotalBlocks(0); // we also need to update the DOM which this does.
        my.addDragArea();

    };

    /**
     * Block's element
     * @param {jQuery}   elem      The blocks HTML element
     * @param {EditMode} edit_mode The EditMode instance
     */
    var Block = Concrete.Block = function Block(elem, edit_mode, peper) {
        var my = this;
        elem.data('Concrete.block', my);
        Concrete.createGetterSetters.call(my, {
            id: elem.data('block-id'),
            handle: elem.data('block-type-handle'),
            areaId: elem.data('area-id'),
            cID: elem.data('cid'),
            area: null,
            elem: elem,
            dragger: null,
            draggerOffset: {x: 0, y: 0},
            draggerPosition: {x: 0, y: 0},
            dragging: false,
            rotationDeg: 0,
            editMode: edit_mode,
            selected: null,
            stepIndex: 0,
            peper: peper || elem.find('a[data-inline-command="move-block"]'),
            pepSettings: {}
        });

        my.id = my.getId();

        _(my.getPepSettings()).extend({
            deferPlacement: true,
            moveTo: function () {
                my.dragPosition(this);
            },
            initiate: function blockDragInitiate(event, pep) {
                my.pepInitiate.call(my, this, event, pep);
            },
            drag: function blockDrag(event, pep) {
                my.pepDrag.call(my, this, event, pep);
            },
            start: function blockDragStart(event, pep) {
                my.pepStart.call(my, this, event, pep);
            },
            stop: function blockDragStop(event, pep) {
                my.pepStop.call(my, this, event, pep);
            },
            place: false
        });

        my.bindMenu();

        Concrete.event.bind('EditModeSelectableContender', function (e, data) {
            if (my.getDragging() && data instanceof DragArea) {
                my.setSelected(data);
            } else {
                if (my.getDragging()) {
                    my.setSelected(null);
                }
            }
        });

        my.getPeper().click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }).pep(my.getPepSettings());
    };

    var BlockType = Concrete.BlockType = function BlockType(elem, edit_mode, dragger) {
        var my = this;
        Block.call(my, elem, edit_mode, dragger);
    };

    var Stack = Concrete.Stack = function Stack(elem, edit_mode, dragger) {
        var my = this;
        Block.call(my, elem, edit_mode, dragger);
    };

    var DuplicateBlock = Concrete.DuplicateBlock = function DuplicateBlock(elem, edit_mode, dragger) {
        var my = this;
        Block.call(my, elem, edit_mode, dragger);
    };

    var StackBlock = Concrete.StackBlock = function StackBlock(elem, stack, edit_mode, dragger) {
        var my = this;
        Block.call(my, elem, edit_mode, dragger);
        my.setAttr('stack', stack);
    };

    /**
     * Drag Area that we create for dropping the blocks into
     * @param {jQuery}   elem  The drag area html element
     * @param {Area} area  The area it belongs to
     * @param {Block} block The block that this drag_area is above, this may be null.
     */
    var DragArea = Concrete.DragArea = function DragArea(elem, area, block) {
        var my = this;

        Concrete.createGetterSetters.call(my, {
            block: block,
            elem: elem,
            area: area,
            isContender: false,
            isSelectable: false,
            animationLength: 500
        });

        Concrete.event.bind('EditModeContenders', function (e, data) {
            var drag_areas = data;
            my.setIsContender(_.contains(drag_areas, my));
        });
        Concrete.event.bind('EditModeSelectableContender', function (e, data) {
            my.setIsSelectable(data === my);
        });
    };

    EditMode.prototype = {

        reset: function () {
            var my = this;
            my.setAttr('areas', []);
            $('.ccm-area-drag-area').remove();
        },

        scanBlocks: function editModeScanBlocks() {
            var my = this, area, block;
            my.reset();

            $('div.ccm-area').each(function () {
                area = new Area($(this), my);
                my.addArea(area);
            });
            $('div.ccm-block-edit').each(function () {
                my.addBlock(block = new Block($(this), my));
                _(my.getAreas()).findWhere({id: block.getAreaId()}).addBlock(block);
            });
            _.invoke(my.getAreas(), 'bindMenu');
        },

        panelOpened: function editModePanelOpened(panel, element) {
            var my = this;

            if (panel.getIdentifier() !== 'add-block') {
                return null;
            }

            $(element).find('a.ccm-panel-add-block-draggable-block-type').each(function () {
                var block, me = $(this), dragger = $('<a/>').addClass('ccm-panel-add-block-draggable-block-type-dragger').appendTo(me);
                my.addBlock(block = new BlockType($(this), my, dragger));

                block.setPeper(dragger);
            });

            $(element).find('div.ccm-panel-add-block-stack-item').each(function () {
                var stack, block, me = $(this), dragger = me.find('div.stack-name');
                my.addBlock(stack = new Stack($(this), my, dragger));

                stack.setPeper(dragger);

                $(this).find('div.block').each(function () {
                    var block, me = $(this), dragger = me.find('div.block-name');
                    my.addBlock(block = new StackBlock($(this), stack, my, dragger));

                    block.setPeper(dragger);
                });
            });

            $(element).find('div.ccm-panel-add-clipboard-block-item').each(function () {
                var block, me = $(this), dragger = me;
                my.addBlock(block = new DuplicateBlock($(this), my, dragger));

                block.setPeper(dragger);
            });

            return panel;
        },

        getAreaByID: function areaGetByID(arID) {
            var areas = this.getAreas();
            return _.findWhere(areas, {id: parseInt(arID)});
        },

        /**
         * Select the correct contender
         * @param  {Pep}      pep        The relevant pep object
         * @param  {Block}    block      The Block
         * @param  {Array}    contenders The possible contenders
         * @param  {Event}    event      The triggering event
         * @return {DragArea}            The selected contender
         */
        selectContender: function editModeSelectContender(pep, block, contenders, event) {
            var my = this;

            // First, remove those that aren't selectable
            contenders = _(contenders).filter(function (drag_area) {
                return drag_area.isSelectable(pep, block, event);
            });
            if (contenders.length < 2) {
                return Concrete.event.fire('EditModeSelectableContender', _(contenders).first());
            }

            var selectedCache = my.getSelectedCache(), blacklist = my.getDragAreaBlacklist();
            if (my.getSelectedThreshold() === selectedCache.length && !_(selectedCache).without(_(selectedCache).last()).length) {
                blacklist.push(_(selectedCache).last());
                my.setDragAreaBlacklist(blacklist);

                _.delay(function (drag_area) {
                    var blacklist = my.getDragAreaBlacklist();
                    my.setDragAreaBlacklist(_(blacklist).without(drag_area));
                }, 5000, _(selectedCache).last());

            }
            contenders = _(contenders).difference(blacklist);

            // Determine the closest area to center because why not
            var selected = _(contenders).min(function (drag_area) {
                var res = drag_area.centerDistanceToBlock(this);
                return res;
            }, block);

            selectedCache.push(selected);
            my.setSelectedCache(_(selectedCache).last(my.getSelectedThreshold()));

            Concrete.event.fire('EditModeSelectableContender', selected);
            return selected;
        },

        /**
         * Add an area to the areas
         * @param {Area} area Area to add
         */
        addArea: function editModeAddArea(area) {
            var my = this;

            my.getAreas().push(area);
        },

        /**
         * Add block to the blocks
         * @param block
         */
        addBlock: function editModeAddBlock(block) {
            var my = this;

            my.getBlocks().push(block);
        },

        destroyInlineEditModeToolbars: function () {
            ConcreteMenuManager.enable();
            $('div.ccm-area-edit-inline-active').removeClass('ccm-area-edit-inline-active');
            $('div.ccm-block-edit-inline-active').remove();
            $('div.ccm-area').removeClass('ccm-area-inline-edit-disabled');
            $('#ccm-toolbar').css('opacity', 1);
            $('#ccm-inline-toolbar-container').remove();

            $(window).unbind('scroll.inline-toolbar');
            ConcreteToolbar.enable();
            $.fn.dialog.hideLoader();
        },

        loadInlineEditModeToolbars: function ($container) {
            $('#ccm-inline-toolbar-container').remove();

            var $toolbar = $container.find('.ccm-inline-toolbar'),
                $holder = $('<div />', {id: 'ccm-inline-toolbar-container'}).appendTo(document.body),
                $window = $(window),
                pos = $container.offset(),
                l = pos.left;

            $toolbar.appendTo($holder);
            var tw = l + parseInt($toolbar.width());
            if (tw > $window.width()) {
                var overage = tw - (l + $container.width());
                $toolbar.css('left', l - overage);
            } else {
                $toolbar.css('left', l);
            }
            $toolbar.css('opacity', 1);
            $toolbar.find('.dialog-launch').dialog();
            var t = pos.top - $holder.outerHeight() - 5;
            $holder.css('top', t).css('opacity', 1);

            if ($window.scrollTop() > t) {
                $('#ccm-toolbar-disabled,#ccm-toolbar').css('opacity', 0);
                $holder.addClass('ccm-inline-toolbar-affixed');
            }

            $window.on('scroll.inline-toolbar', function () {
                $holder.toggleClass('ccm-inline-toolbar-affixed', $window.scrollTop() > t);
                if ($window.scrollTop() > t) {
                    $('#ccm-toolbar-disabled,#ccm-toolbar').css('opacity', 0);
                } else {
                    $('#ccm-toolbar-disabled,#ccm-toolbar').css('opacity', 1);
                }
            });
        }
    };

    Area.prototype = {

        getBlockByID: function blockGetByID(bID) {
            var my = this;
            return _.findWhere(my.getBlocks(), {id: bID});
        },

        getMenuElem: function () {
            var my = this;
            return $('[data-area-menu=area-menu-a' + my.getId() + ']');
        },

        bindMenu: function () {
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
            if (my.menu) {
                my.menu.destroy();
            }
            my.menu = new ConcreteMenu(elem, {
                'handle': menuHandle,
                'highlightClassName': 'ccm-area-highlight',
                'menuActiveClass': 'ccm-area-highlight',
                'menu': $('[data-area-menu=' + elem.attr('data-launch-area-menu') + ']')
            });

            $menuElem.find('a[data-menu-action=add-inline]').on('click', function (e) {
                // we are going to place this at the END of the list.
                var dragAreaLastBlock = false;
                _.each(my.getBlocks(), function (block) {
                    dragAreaLastBlock = block;
                });
                Concrete.event.fire('EditModeBlockAddInline', {
                    area: my,
                    cID: CCM_CID,
                    btID: $(this).attr('data-block-type-id'),
                    arGridColumnSpan: $(this).attr('data-area-grid-column-span'),
                    event: e,
                    dragAreaBlock: dragAreaLastBlock
                });
                return false;
            });

            $menuElem.find('a[data-menu-action=edit-container-layout]').on('click', function (e) {
                // we are going to place this at the END of the list.
                var $link = $(this);
                var dragAreaLastBlock = false;
                _.each(my.getBlocks(), function (block) {
                    dragAreaLastBlock = block;
                });
                var bID = parseInt($(this).attr('data-container-layout-block-id'));
                var editor = Concrete.getEditMode();
                var block = _.findWhere(editor.getBlocks(), {id: bID});
                Concrete.event.fire('EditModeBlockEditInline', {
                    block: block,
                    arGridColumnSpan: $link.attr('data-area-grid-column-span'),
                    event: e
                });
                return false;
            });

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

            block.getElem().remove();
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
                drag_area = new DragArea(elem, my, block);
                my.getElem().prepend(elem);
            } else {
                elem = $('<div class="ccm-area-drag-area"/>');
                drag_area = new DragArea(elem, my, block);
                block.getElem().after(elem);
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

            if (block instanceof Stack || block.getHandle() === 'core_stack_display') {
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

    Block.prototype = {

        addToDragArea: function blockAddToDragArea(drag_area) {
            var my = this,
                sourceArea = my.getArea(),
                targetArea = drag_area.getArea(),
                selected_block;

            sourceArea.removeBlock(my);
            drag_area.getElem().after(my.getElem());
            selected_block = drag_area.getBlock();
            if (selected_block) {
                drag_area.getArea().addBlock(my, selected_block);
            } else {
                drag_area.getArea().addBlockToIndex(my, 0);
            }
            my.getPeper().pep(my.getPepSettings());
            if (targetArea.getTotalBlocks() === 1) {
                // we have to destroy the old menu and create it anew
                targetArea.bindMenu();
            }
            Concrete.event.fire('EditModeBlockMove', {
                block: my,
                sourceArea: sourceArea,
                targetArea: targetArea
            });
        },

        handleAddResponse: function blockHandleAddResponse(response, area, after_block, onComplete) {
            var my = this;

            if (response.error) {
                return;
            }
            $.get(CCM_TOOLS_PATH + '/edit_block_popup',
                {
                    arHandle: response.arHandle,
                    cID: response.cID,
                    bID: response.bID,
                    btask: 'view_edit_mode'
                }, function (html) {
                    if (after_block) {
                        after_block.getElem().after(html);
                    } else {
                        area.getElem().prepend(html);
                    }
                    $.fn.dialog.hideLoader();
                    _.defer(function () {
                        my.getEditMode().scanBlocks();
                        my.showSuccessfulAdd();
                        Concrete.forceRefresh();

                        if (onComplete) {
                            onComplete();
                        }
                    });
                });
            return true;
        },

        showSuccessfulAdd: function blockShowSuccessfulAdd() {
            ConcreteAlert.notify({
                'message': ccmi18n.addBlockMsg,
                'title': ccmi18n.addBlock
            });
        },

        delete: function (msg) {
            var my = this, bID = my.getId(),
                area = my.getArea(),
                block = area.getBlockByID(bID),
                cID = my.getCID(),
                arHandle = area.getHandle();

            ConcreteToolbar.disableDirectExit();
            area.removeBlock(block);
            ConcreteAlert.notify({
                'message': ccmi18n.deleteBlockMsg,
                'title': ccmi18n.deleteBlock
            });

            $.ajax({
                type: 'POST',
                url: CCM_DISPATCHER_FILENAME,
                data: 'cID=' + cID + '&ccm_token=' + CCM_SECURITY_TOKEN + '&isAjax=true&btask=remove&bID=' + bID + '&arHandle=' + encodeURIComponent(arHandle)
            });
        },

        /**
         * replaces a block in an area with a new block by ID and content
         */
        replace: function (bID, content) {
            var my = this, editor = Concrete.getEditMode(), oldBID = my.getId(), area = my.getArea(), totalBlocks = area.getTotalBlocks(), i, b;

            my.getElem().next('.ccm-area-drag-area').remove();
            my.getElem().data('block-id', bID); // it's super lame that i have to do this.
            my.getElem().attr('data-block-id', bID);

            if (content) {
                my.getElem().before(content).remove();
            }

            var newBlock = new Concrete.Block($('[data-block-id=' + bID + ']'), editor);

            area.setTotalBlocks(totalBlocks - 1); // it will get incremented by addBlock below

            // now we go through all the block registries and replace the old one with this one.
            var editorBlocks = editor.getBlocks();
            for (i = 0; i < editorBlocks.length; i++) {
                b = editorBlocks[i];
                if (b.getId() === oldBID) {
                    editorBlocks[i] = newBlock;
                }
            }
            editor.setBlocks(editorBlocks);

            // area specific
            var areaBlocks = area.getBlocks(), total = areaBlocks.length;
            for (i = 0; i < total; i++) {
                b = areaBlocks[i];
                if (b.getId() === oldBID) {
                    area.addBlockToIndex(newBlock, i);
                }
            }


            return newBlock;
        },

        getMenuElem: function () {
            var my = this;
            return $('[data-block-menu=block-menu-b' + my.getId() + '-' + my.getAreaId() + ']');
        },

        bindMenu: function () {
            var my = this,
                elem = my.getElem(),
                menuHandle = elem.attr('data-block-menu-handle'),
                $menuElem = my.getMenuElem();

            if (menuHandle !== 'none') {

                my.menu = new ConcreteMenu(elem, {
                    'handle': 'this',
                    'highlightClassName': 'ccm-block-highlight',
                    'menuActiveClass': 'ccm-block-highlight',
                    'menu': $('[data-block-menu=' + elem.attr('data-launch-block-menu') + ']')
                });

                $menuElem.find('a[data-menu-action=edit_inline]').unbind().on('click', function () {
                    Concrete.event.fire('EditModeBlockEditInline', {block: my, event: event});
                });

                $menuElem.find('a[data-menu-action=block_scrapbook]').unbind().on('click', function () {
                    Concrete.event.fire('EditModeBlockAddToClipboard', {block: my, event: event});
                });

                $menuElem.find('a[data-menu-action=delete_block]').unbind().on('click', function () {
                    Concrete.event.fire('EditModeBlockDelete', {message: $(this).attr('data-menu-delete-message'), block: my, event: event});
                });
            }
        },

        setArea: function blockSetArea(area) {
            this.setAttr('area', area);

            var my = this;
            my.getElem().find('a[data-menu-action=block_dialog]').each(function () {
                var href = $(this).data('menu-href');
                href += (href.indexOf('?') !== -1) ? '&cID=' + my.getCID() : '?cID=' + my.getCID();
                href += '&arHandle=' + encodeURIComponent(area.getHandle()) + '&bID=' + my.getId();
                $(this).attr('href', href).dialog();
            });
        },

        /**
         * Custom dragger getter, create dragger if it doesn't exist
         * @return {jQuery} dragger
         */
        getDragger: function blockgetDragger() {
            var my = this;

            if (!my.getAttr('dragger')) {
                var dragger = $('<a />')
                        .html(my.getElem().data('dragging-avatar') || ('<p><img src="/concrete/blocks/content/icon.png"><span>' + ccmi18n.content + '</span></p>'))
                        .addClass('ccm-block-edit-drag ccm-panel-add-block-draggable-block-type')
                    ;
                my.setAttr('dragger', dragger.css({
                    width: my.getElem().width(),
                    height: my.getElem().height()
                }));
            }
            return my.getAttr('dragger');
        },

        /**
         * Apply cross-browser compatible transformation
         * @param  {[String]} transformation String containing the css matrix
         * @return {Boolean}                 Success, always true
         */
        transform: function blockTransform(transformation, matrix) {
            var my = this;

            var element = my.getDragger().css({
                '-webkit-transform': transformation,
                '-moz-transform': transformation,
                '-ms-transform': transformation,
                '-o-transform': transformation,
                'transform': transformation
            }).get(0);

            // Modified transformie polyfill
            if (element.filters) {
                if (!element.filters['DXImageTransform.Microsoft.Matrix']) {
                    element.style.filter = (element.style.filter ? '' : ' ' ) + 'progid:DXImageTransform.Microsoft.Matrix(sizingMethod=\'auto expand\')';
                }

                element.filters['DXImageTransform.Microsoft.Matrix'].M11 = matrix.elements[0][0];
                element.filters['DXImageTransform.Microsoft.Matrix'].M12 = matrix.elements[0][1];
                element.filters['DXImageTransform.Microsoft.Matrix'].M21 = matrix.elements[1][0];
                element.filters['DXImageTransform.Microsoft.Matrix'].M22 = matrix.elements[1][1];
                element.style.left = -(element.offsetWidth / 2) + (element.clientWidth / 2) + 'px';
                element.style.top = -(element.offsetHeight / 2) + (element.clientHeight / 2) + 'px';
            }

            return true;
        },

        resetTransform: function blockResetTransform() {
            var transformation = '';
            var element = this.getDragger().css({
                top: 0,
                left: 0,
                '-webkit-transform': transformation,
                '-moz-transform': transformation,
                '-ms-transform': transformation,
                '-o-transform': transformation,
                'transform': transformation
            }).get(0);

            if (element.filters) {
                element.filters = [];
            }

            this.setDraggerPosition({ x: 0, y: 0 });
            return this.renderPosition();
        },

        /**
         * Quick method to multiplty matrices, modified from a version on RosettaCode
         * @param  {Array}  matrix1 Array containing a matrix
         * @param  {Array}  matrix2 Array containing a matrix
         * @return {Array}          Array containing a matrix
         */
        multiplyMatrices: function blockMultiplyMatrices(matrix1, matrix2) {
            var result = [];
            for (var i = 0; i < matrix1.length; i++) {
                result[i] = [];
                for (var j = 0; j < matrix1[0].length; j++) {
                    var sum = 0;
                    for (var k = 0; k < matrix1[0].length; k++) {
                        sum += matrix1[i][k] * matrix2[k][j];
                    }
                    result[i][j] = sum;
                }
            }
            return result;
        },

        /**
         * Convert matrix to CSS value
         * @param  {Array}  matrix Array containing a matrix
         * @return {String}        CSS string
         */
        matrixToCss: function blockMatrixToCss(matrix) {
            var css_arr = [matrix[0][0], matrix[0][1], matrix[1][0], matrix[1][1], matrix[0][2], matrix[1][2]];
            return 'matrix(' + css_arr.join(',') + ')';
        },

        /**
         * Method to run after dragging stops for 50ms
         * @return {Boolean} Success, always true.
         */
        endRotation: function blockEndRotation() {
            var my = this;
            var start_rotation = my.getRotationDeg();
            my.getDragger().animate({rotation: 0}, {duration: 1, step: function () {
            }});
            var step_index = my.setStepIndex(my.getStepIndex() + 1);
            my.getDragger().animate({rotation: my.getRotationDeg()}, {queue: false, duration: 150, step: function (now) {
                if (my.getStepIndex() !== step_index) {
                    return;
                }
                my.setRotationDeg(start_rotation - now);
                my.renderPosition();
            }}, 'easeOutElastic');
            return true;
        },

        /**
         * Render the dragger in the correct position.
         * @return {Boolean} Success, always true.
         */
        renderPosition: function blockRenderPosition() {
            var my = this;

            var x = my.getDraggerPosition().x, y = my.getDraggerPosition().y, a = my.getRotationDeg() * (Math.PI / 180);

            var cos = _.bind(Math.cos, Math),
                sin = _.bind(Math.sin, Math);
            var position_matrix = [
                [ 1, 0, x ],
                [ 0, 1, y ],
                [ 0, 0, 1 ]
            ], rotation_matrix, final_matrix;
            if (a) {
                rotation_matrix = [
                    [ cos(a), sin(a), 0 ],
                    [ -sin(a), cos(a), 0 ],
                    [ 0 , 0 , 1 ]
                ];
                final_matrix = my.multiplyMatrices(position_matrix, rotation_matrix);
            } else {
                final_matrix = position_matrix;
            }
            return this.transform(my.matrixToCss(final_matrix), final_matrix);
        },

        /**
         * Position the dragger
         * @param  {Event}   event The triggering event
         * @param  {Pep}     pep   The pep instance
         * @return {Boolean}       Success, always true
         */
        dragPosition: function blockDragPosition(pep) {
            var my = this;

            my.setRotationDeg(Math.max(-15, Math.min(15, pep.velocity().x / 15)));
            my.endRotation();
            var position = _.last(pep.velocityQueue), offset = my.getDraggerOffset();
            if (!position) {
                position = {x: my.getDragger().offset().left, y: my.getDragger().offset().top};
            }
            var x = position.x - offset.x, y = position.y - offset.y;
            my.setDraggerPosition({ x: x, y: y });
            my.renderPosition();

            return true;
        },

        pepInitiate: function blockPepInitiate(context, event, pep) {
            var my = this;
            my.resetTransform();
            my.setDragging(true);
            my.getDragger().hide().appendTo(window.document.body).css(my.getElem().offset());
            my.setDraggerOffset({x: event.clientX - my.getElem().offset().left + window.document.body.scrollLeft, y: event.clientY - my.getElem().offset().top + window.document.body.scrollTop});
            my.getDragger().fadeIn(250);

            _.defer(function () {
                Concrete.event.fire('EditModeBlockDragInitialization', {block: my, pep: pep, event: event});
            });
        },
        pepDrag: function blockPepDrag(context, event, pep) {
            var my = this;
            _.defer(function () {
                Concrete.event.fire('EditModeBlockDrag', {block: my, pep: pep, event: event});
            });
        },
        pepStart: function blockPepStart(context, event, pep) {
            var my = this;
            my.resetTransform();

            var elem = my.getElem(),
                mouse_position = { x: event.pageX, y: event.pageY },
                elem_position = {
                    x: elem.offset().left,
                    y: elem.offset().top
                },
                mouse_percentage = {
                    x: (elem_position.x - mouse_position.x) / elem.width(),
                    y: (elem_position.y - mouse_position.y) / elem.height()
                };

            my.setDraggerPosition({ x: elem_position.x, y: elem_position.y });
            my.renderPosition();

            my.setDraggerOffset({
                x: -1 * (mouse_percentage.x * elem.width()),
                y: -1 * (mouse_percentage.y * elem.height())
            });

            my.getDragger().animate({
                width: 90,
                height: 90
            }, {
                duration: 250,
                step: function (now, fx) {
                    my.setDraggerOffset({
                        x: -1 * (mouse_percentage.x * $(this).width()),
                        y: -1 * (mouse_percentage.y * $(this).height())
                    });
                    my.dragPosition(pep);
                }
            });

            _.defer(function () {
                Concrete.event.fire('EditModeBlockDragStart', {block: my, pep: pep, event: event});
            });
        },

        pepStop: function blockPepStop(context, event, pep) {
            var my = this, drag_area;
            my.getDragger().stop(1);
            my.getDragger().css({top: 0, left: 0});
            my.dragPosition(pep);

            if ((drag_area = my.getSelected())) {
                my.addToDragArea(drag_area);
            }

            my.animateToElem();

            _.defer(function () {
                Concrete.event.fire('EditModeBlockDragStop', {block: my, pep: pep, event: event});
            });
        },

        animateToElem: function blockAnimateToElem(element) {
            var my = this, elem = element || my.getElem(), dragger_start = {
                x: my.getDraggerPosition().x,
                y: my.getDragger().offset().top,
                width: my.getDragger().width(),
                height: my.getDragger().height()
            };
            my.setDragging(false);
            my.getDragger().animate({ccm_perc: 0}, {duration: 0, step: function () {
            }}).animate({
                ccm_perc: 1,
                opacity: 0
            }, {
                duration: 500,
                step: function (now, fx) {
                    if (fx.prop === 'ccm_perc') {
                        var end_pos = {
                            x: elem.offset().left,
                            y: elem.offset().top,
                            width: elem.width(),
                            height: elem.height()
                        }, change = {
                            x: (end_pos.x - dragger_start.x) * now,
                            y: (end_pos.y - dragger_start.y) * now,
                            width: (end_pos.width - dragger_start.width) * now,
                            height: (end_pos.height - dragger_start.height) * now
                        };

                        my.setDraggerPosition({
                            x: dragger_start.x + change.x,
                            y: dragger_start.y + change.y
                        });
                        my.renderPosition();

                        my.getDragger().css({
                            width: dragger_start.width + change.width,
                            height: dragger_start.height + change.height
                        });
                    } else {
                        my.getDragger().css({
                            opacity: now
                        });
                    }
                },
                complete: function () {
                    my.getDragger().remove();
                    my.setAttr('dragger', null);
                }
            });
        }
    };

    BlockType.prototype = _({

        pepStop: function blockTypePepStop(context, event, pep) {
            var my = this, drag_area;

            if ((drag_area = my.getSelected())) {
                my.addToDragArea(drag_area);
            }

            _.defer(function () {
                Concrete.event.fire('EditModeBlockDragStop', {block: my, pep: pep, event: event});
            });

            my.getDragger().remove();
            my.setAttr('dragger', null);
        },

        addToDragArea: function blockTypeAddToDragArea(drag_area) {
            var my = this, elem = my.getElem(),
                block_type_id = elem.data('btid'),
                cID = elem.data('cid'),
                area = drag_area.getArea(),
                area_handle = area.getHandle(),
                dragAreaBlockID = 0,
                dragAreaBlock = drag_area.getBlock(),
                is_inline = !!elem.data('supports-inline-add'),
                has_add = !!elem.data('has-add-template');

            if (dragAreaBlock) {
                dragAreaBlockID = dragAreaBlock.getId();
            }

            ConcretePanelManager.exitPanelMode();

            if (!has_add) {
                $.get(CCM_DISPATCHER_FILENAME, {
                    cID: cID,
                    arHandle: area_handle,
                    btID: block_type_id,
                    mode: 'edit',
                    processBlock: 1,
                    add: 1,
                    ccm_token: CCM_SECURITY_TOKEN,
                    dragAreaBlockID: dragAreaBlockID
                }, function (response) {
                    $.fn.dialog.showLoader();
                    $.get(CCM_TOOLS_PATH + '/edit_block_popup',
                        {
                            arHandle: response.arHandle,
                            cID: response.cID,
                            bID: response.bID,
                            btask: 'view_edit_mode'
                        }, function (html) {
                            if (dragAreaBlock) {
                                dragAreaBlock.getElem().after(html);
                            } else {
                                area.getElem().append(html);
                            }
                            $.fn.dialog.hideLoader();
                            _.defer(function () {
                                my.getEditMode().scanBlocks();
                            });
                        });
                });
            } else if (is_inline) {
                ConcreteEvent.fire('EditModeBlockAddInline', {
                    'selected': drag_area,
                    'area': drag_area.getArea(),
                    'cID': cID,
                    'btID': block_type_id,
                    'dragAreaBlockID': dragAreaBlockID
                });
            } else {
                $.fn.dialog.open({

                    onOpen: function () {
                        $(function () {
                            $('#ccm-block-form').concreteAjaxBlockForm({
                                'task': 'add',
                                'dragAreaBlockID': dragAreaBlockID
                            });
                        });
                    },
                    width: parseInt(elem.data('dialog-width'), 10),
                    height: parseInt(elem.data('dialog-height'), 10) + 20,
                    title: elem.data('dialog-title'),
                    href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/add_block?cID=' + cID + '&btID=' + block_type_id + '&arHandle=' + encodeURIComponent(area_handle)
                });
            }
        }
    }).defaults(Block.prototype);

    Stack.prototype = _({
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
    }).defaults(BlockType.prototype);

    DuplicateBlock.prototype = _({
        addToDragArea: function DuplicateBlockAddToDragArea(drag_area) {
            var my = this, elem = my.getElem(),
                block_type_id = elem.data('btid'),
                area = drag_area.getArea(),
                area_handle = area.getHandle(),
                dragAreaBlockID = 0,
                cID = elem.data('cid'),
                dragAreaBlock = drag_area.getBlock(),
                pcID = elem.data('pcid');

            if (dragAreaBlock) {
                dragAreaBlockID = dragAreaBlock.getId();
            }

            ConcretePanelManager.exitPanelMode();
            jQuery.fn.dialog.closeAll();
            jQuery.fn.dialog.showLoader();

            var settings = {
                cID: cID,
                arHandle: area_handle,
                btID: block_type_id,
                mode: 'edit',
                processBlock: 1,
                add: 1,
                btask: 'alias_existing_block',
                pcID: [ pcID ],
                ccm_token: CCM_SECURITY_TOKEN
            };
            if (dragAreaBlockID) {
                settings.dragAreaBlockID = dragAreaBlockID;
            }
            $.getJSON(CCM_DISPATCHER_FILENAME, settings, function (response) {
                my.handleAddResponse(response, area, dragAreaBlock, function () {
                    ConcreteEvent.fire('EditModeAddClipboardComplete', {
                        block: my
                    });
                });
            });
        }
    }).defaults(BlockType.prototype);

    StackBlock.prototype = _({
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
                pcID: [ elem.data('cID') ],
                ccm_token: CCM_SECURITY_TOKEN
            };
            if (dragAreaBlockID) {
                settings.dragAreaBlockID = dragAreaBlockID;
            }
            $.getJSON(CCM_DISPATCHER_FILENAME, settings, function (response) {
                my.handleAddResponse(response, area, dragAreaBlock);
            });
        }
    }).defaults(BlockType.prototype);

    DragArea.prototype = {

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
