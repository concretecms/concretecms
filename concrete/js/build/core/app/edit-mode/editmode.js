/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global Concrete, ConcreteAlert, ConcreteEvent, ConcreteMenuManager, ConcretePanelManager, ConcreteToolbar, ccmi18n, _, CCM_DISPATCHER_FILENAME, CCM_TOOLS_PATH */

;(function(window, $) {
    'use strict';

    var html = $('html');

    /**
     * Edit mode object for managing editing.
     */
    var EditMode = Concrete.EditMode = function(options) {
        this.init.call(this, options);
    };

    EditMode.prototype = {

        init: function editModeInit(options) {
            var my = this;
            options = options || {};
            options = $.extend({
                'notify': false
            }, options);

            Concrete.createGetterSetters.call(my, {
                dragging: false,
                active: true,
                nextBlockArea: null,
                areas: [],
                selectedCache: [],
                selectedThreshold: 5,
                dragAreaBlacklist: []
            });

            my.bindEvent('PanelLoad', function editModePanelOpenEventHandler(event, data) {
                my.panelOpened(data.panel, data.element);
            });
            my.bindEvent('PanelClose', function editModePanelCloseEventHandler(event, data) {
                if (data.panel.getIdentifier() == 'add-block') {
                    my.setNextBlockArea(null);
                }
                html.removeClass('ccm-panel-add-block');
            });

            my.bindEvent('EditModeAddBlockComplete EditModeUpdateBlockComplete', function(e) {
                _.defer(function() {
                    my.scanBlocks();
                });
            });

            my.bindEvent('EditModeBlockSaveInline', function(event, data) {
                $('#ccm-block-form').submit();
                ConcreteEvent.fire('EditModeExitInlineSaved');
                ConcreteEvent.fire('EditModeExitInline', {
                    action: 'save_inline'
                });
            });

            my.bindEvent('EditModeBlockEditInline', function (event, data) {
                var block = data.block,
                    area = block.getArea(),
                    action = (data.action) ? data.action : CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/block/edit',
                    arEnableGridContainer = area.getEnableGridContainer() ? 1 : 0,
                    templates = area.getCustomTemplates();
                    var postData = [
                        {name: 'cID', value: block.getCID()},
                        {name: 'arHandle', value: area.getHandle()},
                        {name: 'arGridMaximumColumns', value: data.arGridMaximumColumns},
                        {name: 'arEnableGridContainer', value: arEnableGridContainer},
                        {name: 'aID', value: area.getId()},
                        {name: 'bID', value: block.getId()}
                    ],
                    $container = block.getElem(),
                    prop;

                if (templates) {
                    for (var k in templates) {
                        postData[postData.length] = {
                            name: 'arCustomTemplates[' + k + ']',
                            value: templates[k]
                        };
                    }
                }

                if (block.getAttr('menu')) {
                    block.getAttr('menu').destroy();
                }
                if (data.postData) {
                    for (prop in data.postData) {
                        if (data.postData.hasOwnProperty(prop)) {
                            postData.push({name: prop, value: data.postData[prop]});
                        }
                    }
                }

                Concrete.event.unbind('EditModeExitInline.editmode');
                my.bindEvent('EditModeExitInline.editmode', function (e, event_data) {
                    Concrete.event.unbind(e);
                    e.stopPropagation();
                    var action = CCM_DISPATCHER_FILENAME + '/ccm/system/block/render',
                        data = {
                            cID: block.getCID(),
                            arEnableGridContainer: arEnableGridContainer,
                            bID: block.getId(),
                            arHandle: area.getHandle()
                        };

                    $.fn.dialog.showLoader();

                    if (!event_data || !event_data.action || event_data.action !== 'save_inline') {
                        $.get(action, data,
                            function (r) {
                                var realBlock = my.getBlockByID(block.getId());
                                if (!realBlock) {
                                    return;
                                }

                                var newBlock = realBlock.replace(r);
                                _.defer(function () {
                                    ConcreteEvent.fire('EditModeExitInlineComplete', {
                                        block: newBlock
                                    });
                                    my.destroyInlineEditModeToolbars();
                                    _.defer(function () {
                                        my.scanBlocks();
                                    });
                                });
                            }
                        );
                    }
                });

                // We can't just wholesale disable the menu manager even though that makes
                // it so that you can't click on blocks while they're disabled, because we
                // need the file manager menu when editing block design.
//              ConcreteMenuManager.disable();
                ConcreteToolbar.disable();
                $('div.ccm-area').addClass('ccm-area-inline-edit-disabled');
                block.getElem().addClass('ccm-block-edit-inline-active');


                $.ajax({
                    type: 'GET',
                    url: action,
                    data: postData,
                    success: function (r) {
                        var elem = $(r);
                        $container.empty()
                            .append(elem)
                            .find('.ccm-block-edit')
                            .addClass('ccm-block-edit-inline-active');
                        my.loadInlineEditModeToolbars($container);
                        $.fn.dialog.hideLoader();
                        Concrete.event.fire('EditModeInlineEditLoaded', {
                            block: block,
                            element: elem
                        });
                    }
                });
            });

            my.bindEvent('EditModeBlockAddInline', function (event, data) {
                var area = data.area,
                    selected = data.selected,
                    btID = data.btID,
                    cID = data.cID,
                    arEnableGridContainer = area.getEnableGridContainer() ? 1 : 0,
                    postData = [
                        {name: 'btask', value: 'edit'},
                        {name: 'cID', value: cID},
                        {name: 'arGridMaximumColumns', value: data.arGridMaximumColumns},
                        {name: 'arEnableGridContainer', value: arEnableGridContainer},
                        {name: 'arHandle', value: area.getHandle()},
                        {name: 'btID', value: btID}
                    ], dragAreaBlock, dragAreaBlockID, after;
                if (selected) {
                    after = selected.getElem();
                    dragAreaBlock = selected.getBlock();
                } else {
                    after = area.getBlockContainer().children().last();
                    dragAreaBlock = data.dragAreaBlock;
                }

                var templates = area.getCustomTemplates();
                if (templates) {
                    for (var k in templates) {
                        postData[postData.length] = {
                            name: 'arCustomTemplate[' + k + ']',
                            value: templates[k]
                        };
                    }
                }

                if (dragAreaBlock) {
                    dragAreaBlockID = dragAreaBlock.getId();
                }

                ConcreteMenuManager.disable();
                ConcreteToolbar.disable();
                $.fn.dialog.closeAll();

                $('div.ccm-area').addClass('ccm-area-inline-edit-disabled');

                $.fn.dialog.showLoader();

                if (area.getAttr('menu')) {
                    area.getAttr('menu').destroy();
                }

                var saved = false;
                my.bindEvent('EditModeExitInlineSaved', function (e) {
                    Concrete.event.unbind(e);
                    saved = true;

                    var panel = ConcretePanelManager.getByIdentifier('add-block');
                    if ( panel && panel.pinned() ) panel.show();
                });
                my.bindEvent('EditModeExitInline', function (e) {
                    Concrete.event.unsubscribe(e);
                    if (saved) {
                        return;
                    }
                    $('#a' + area.getId() + '-bt' + btID).remove();
                    my.destroyInlineEditModeToolbars();
                    ConcreteEvent.fire('EditModeExitInlineComplete');

                    var panel = ConcretePanelManager.getByIdentifier('add-block');
                    if ( panel && panel.pinned() ) panel.show();
                });
                $.ajax({
                    type: 'GET',
                    url: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/add_block',
                    data: postData,
                    success: function (r) {
                        var elem = $(r);
                        var $container = $('<div class="ccm-block-edit-inline-active"></div>');
                        $container.attr('id', 'a' + area.getId() + '-bt' + btID).append(elem);

                        area.getElem().addClass("ccm-area-edit-inline-active");
                        after.after($container);
                        $(function () {
                            $container.find('#ccm-block-form').concreteAjaxBlockForm({
                                'task': 'add',
                                'btSupportsInlineAdd': true,
                                'dragAreaBlockID': dragAreaBlockID,
                                dragArea: selected,
                                placeholder: '#a' + area.getId() + '-bt' + btID
                            });
                        });
                        my.loadInlineEditModeToolbars($container.find('div[data-container=inline-toolbar]'));
                    },
                    complete: function () {
                        $.fn.dialog.hideLoader();
                    }
                });
            });

            Concrete.event.bind('EditModeBlockAddToClipboard', function (event, data) {
                var block = data.block, area = block.getArea(), token = data.token;
                ConcreteToolbar.disableDirectExit();
                // got to grab the message too, eventually
                $.ajax({
                    type: 'POST',
                    url: CCM_TOOLS_PATH + '/pile_manager',
                    data: 'cID=' + block.getCID() + '&bID=' + block.getId() + '&arHandle=' + encodeURIComponent(area.getHandle()) + '&btask=add&scrapbookName=userScrapbook&ccm_token=' + encodeURIComponent(token),
                    success: function (resp) {
                        ConcreteAlert.notify({
                            'message': ccmi18n.copyBlockToScrapbookMsg,
                            'title': ccmi18n.copyBlockToScrapbook
                        });
                    }
                });
            });

            my.bindEvent('EditModeBlockDelete', function (event, data) {
                var block = data.block;
                block.delete(data.message);
            });

            my.bindEvent('EditModeBlockDeleteComplete', function (event, data) {
                var block = data.block;
                block.finishDelete();
                ConcreteEvent.fire('EditModeBlockDeleteAfterComplete', {
                    block: block
                });
            });

            var $document = $(window.document),
                scrolling = false,
                scroll_buffer = 100;

            function scrollLoop(block, element, amount, step, test, scroll_method, axis) {
                if (!my.getDragging()) {
                    return;
                }
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

            my.bindEvent('EditModeBlockDrag', _.throttle(function editModeEditModeBlockDragEventHandler(event, data) {
                if (!my.getDragging()) {
                    return;
                }
                var block = data.block, pep = data.pep,
                    areas = my.getAreas(),
                    contenders;

                if (block instanceof Concrete.Layout) {
                    areas = [_(areas).find(function (a) {
                        return block.getArea() === a;
                    })];
                }

                contenders = _.flatten(_(areas).map(function (area) {
                    var drag_areas = area.contendingDragAreas(pep, block);
                    return drag_areas;
                }), true);

                _.defer(function () {
                    Concrete.event.fire('EditModeContenders', contenders);
                    my.selectContender(pep, block, contenders, data.event);
                });

                if (!scrolling) {
                    // Vertical
                    scrollLoop(block, $document, 2, 10, function () {
                        var pos = block.getDraggerPosition().y - $document.scrollTop();
                        return block.getDragging() && $(window).height() - pos <= scroll_buffer;
                    }, $.fn.scrollTop, 'y');
                    scrollLoop(block, $document, -2, 10, function () {
                        var pos = block.getDraggerPosition().y - $document.scrollTop();
                        return block.getDragging() && pos <= scroll_buffer;
                    }, $.fn.scrollTop, 'y');

                    // Horizontal
                    scrollLoop(block, $document, 2, 10, function () {
                        var pos = block.getDraggerPosition().x - $document.scrollLeft();
                        return block.getDragging() && $(window).width() - pos <= scroll_buffer;
                    }, $.fn.scrollLeft, 'x');
                    scrollLoop(block, $document, -2, 10, function () {
                        var pos = block.getDraggerPosition().x - $document.scrollLeft();
                        return block.getDragging() && pos <= scroll_buffer;
                    }, $.fn.scrollLeft, 'x');
                }

            }, 250, {trailing: false}));

            my.bindEvent('EditModeBlockDragStop', function editModeEditModeBlockDragStopEventHandler(e, data) {
                Concrete.event.fire('EditModeContenders', []);
                Concrete.event.fire('EditModeSelectableContender');
                html.removeClass('ccm-block-dragging');

                if (data.block instanceof Concrete.BlockType) return;
                my.scanBlocks();
            });

            my.bindEvent('EditModeBlockMove', function editModeEditModeBlockMoveEventHandler(e, data) {
                var block = data.block,
                    targetArea = data.targetArea,
                    sourceArea = data.sourceArea,
                    send = {
                        ccm_token: window.CCM_SECURITY_TOKEN,
                        btask: 'ajax_do_arrange',
                        area: targetArea.getId(),
                        sourceArea: sourceArea.getId(),
                        block: block.getId(),
                        blocks: []
                    };

                targetArea = targetArea.inEditMode(targetArea.getEditMode());

                _(targetArea.getBlocks()).each(function (block, key) {
                    send.blocks.push(block.getId());
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
                        ConcreteToolbar.disableDirectExit();
                        $.fn.dialog.hideLoader();
                        clearTimeout(timeout);
                    }
                });

            });

            my.bindEvent('EditModeBlockDragStart', function editModeEditModeBlockDragStartEventHandler() {
                html.addClass('ccm-block-dragging');
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

            ConcreteEvent.fire('EditModeAfterInit', {
                editMode: my
            });
        },

        bindEvent: function editModeBindEvent(event, handler) {
            return Concrete.event.bind(event, handler);
        },

        reset: function () {
            var my = this;

            _(my.getAreas()).each(function (area) {
                area.destroy();
            });

            my.setAttr('areas', []);
        },

        scanBlocks: function editModeScanBlocks() {
            var my = this, area;
            my.reset();

            $('div.ccm-area').each(function () {
                var me = $(this);
                if (me.parent().hasClass('ccm-block-stack')) return;
                area = new Concrete.Area(me, my);
                area.scanBlocks();
                my.addArea(area);
            });

            _.invoke(my.getAreas(), 'bindMenu');
        },

        panelOpened: function editModePanelOpened(panel, element) {
            var my = this, next_area = my.getNextBlockArea();

            if (panel.getIdentifier() !== 'add-block') {
                return null;
            }
            html.addClass('ccm-panel-add-block');

            $(element).find('input[data-input=search-blocks]').liveUpdate('ccm-panel-add-blocktypes-list', 'blocktypes');
            $(element).find('input[data-input=search-blocks]').focus();



            $(element).find('a.ccm-panel-add-block-draggable-block-type').each(function () {
                var block, me = $(this), dragger = $('<a/>').addClass('ccm-panel-add-block-draggable-block-type-dragger').appendTo(me);
                block = new Concrete.BlockType($(this), my, dragger, next_area);

                block.setPeper(dragger);
            });


            $(element).find('div.ccm-panel-add-block-stack-item').each(function () {
                var stack, me = $(this), dragger = me.find('div.stack-name');
                stack = new Concrete.Stack($(this), my, dragger, next_area);

                stack.setPeper(dragger);

                $(this).find('div.block').each(function () {
                    var block, me = $(this), dragger = me.find('div.block-name');
                    block = new Concrete.StackBlock($(this), stack, my, dragger, next_area);

                    block.setPeper(dragger);
                });
            });

            $(element).find('div.ccm-panel-add-clipboard-block-item').each(function () {
                var me = $(this);
                new Concrete.DuplicateBlock(me, my, next_area);
            });

            $(element).find('.ccm-panel-content').mousewheel(function (e) {

                if (!e.deltaY || !e.deltaFactor) {
                    return;
                }

                var change = -1 * e.deltaY * e.deltaFactor;

                var me = $(this),
                    deltaY = change || 0,
                    distance_from_top = me.scrollTop(),
                    distance_from_bottom = (me.get(0).scrollHeight - (me.scrollTop() + me.height()) - me.css('paddingTop').replace('px', ''));

                // If we don't have deltaY, just use default behavior.
                if (!deltaY) {
                    return;
                }

                if ((deltaY < 0 && !distance_from_top) ||
                    (deltaY > 0 && !distance_from_bottom)
                ) {
                    return false;
                }

                me.scrollTop(me.scrollTop() + deltaY);
                return false;
            });

            return panel;
        },

        getAreaByID: function editModeGetAreaByID(arID) {
            var areas = this.getAreas();
            return _.findWhere(areas, {id: parseInt(arID)});
        },

        getBlockByID: function editModeGetBockByID(blockID) {
            var areas = this.getAreas(), match = null;

            _(areas).every(function(area) {
                if (match) {
                    return false;
                }
                _(area.getBlocks()).every(function(block) {
                    if (block.getId() == blockID) {
                        match = block;
                        return false;
                    }

                    return true;
                });

                return true;
            });

            return match;
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

        getBlocks: function editModeGetBlocks() {
            var blocks = [];

            _(this.getAreas()).each(function (area) {
                blocks.push(area.getBlocks());
            });

            return _(blocks).flatten();
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

        loadInlineEditModeToolbars: function ($container, toolbarHTML) {

            $('#ccm-inline-toolbar-container').remove();
            var $holder = $('<div />', {id: 'ccm-inline-toolbar-container'}).appendTo(document.body),
                $toolbar;

            if (toolbarHTML) {
                $holder.append(toolbarHTML);
                $toolbar = $holder.find('.ccm-inline-toolbar');
            } else {
                $toolbar = $container.find('.ccm-inline-toolbar');
                $toolbar.appendTo($holder);
            }

            var $window = $(window),
                pos = $container.offset(),
                l = pos.left;

            var tw = l + parseInt($toolbar.width());
            if ($window.width() > $toolbar.width()) {
                if (tw > $window.width()) {
                    var overage = tw - (l + $container.width());
                    $toolbar.css('left', l - overage);
                } else {
                    $toolbar.css('left', l);
                }
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

})(window, jQuery);
