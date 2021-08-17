/* eslint-disable no-new, no-unused-vars, camelcase, eqeqeq */
/* global _, ccmi18n_sitemap, CCM_DISPATCHER_FILENAME, CCM_SECURITY_TOKEN, CCM_REL, Concrete, ConcreteAlert, ConcretePageMenu, ccm_parseJSON, ConcreteProgressiveOperation, ConcreteEvent */

/* Base search class for AJAX forms in the UI */
;(function(global, $) {
    'use strict'

    function ConcreteSitemap($element, options) {
        var my = this
        options = options || {}
        options.sitemapIndex = Math.max(0, parseInt(options.sitemapIndex, 10) || 0)
        options = $.extend({
            isSitemapOverlay: false,
            displayNodePagination: false,
            cParentID: 0,
            siteTreeID: 0,
            cookieId: 'ConcreteSitemap' + (options.sitemapIndex > 0 ? '-' + options.sitemapIndex : ''),
            includeSystemPages: false,
            displaySingleLevel: false,
            persist: true,
            minExpandLevel: false,
            dataSource: CCM_DISPATCHER_FILENAME + '/ccm/system/page/sitemap_data',
            ajaxData: {},
            selectMode: false, // 1 - single, 2 = multiple , 3 = hierarchical-multiple - has NOTHING to do with clicks. If you enable select mode you CANNOT use a click handler.
            onClickNode: false, // This handles clicking on the title.
            onSelectNode: false, // this handles when a radio or checkbox in the tree is checked
            init: false
        }, options)
        if (options.sitemapIndex > 0) {
            options.ajaxData.sitemapIndex = options.sitemapIndex
        }
        my.options = options
        my.$element = $element
        my.$sitemap = null
        my.homeCID = null
        my.setupTree()
        my.setupTreeEvents()
        Concrete.event.publish('ConcreteSitemap', this)

        return my.$element
    }

    ConcreteSitemap.prototype = {

        sitemapTemplate: '<div class="ccm-sitemap-wrapper"><div class="ccm-sitemap-tree-selector-wrapper"></div><div class="ccm-sitemap-tree"></div></div>',
        localesWrapperTemplate: '<select class="form-select form-control" data-select="site-trees"></select>',

        getTree: function() {
            var my = this
            return my.$sitemap.fancytree('getTree')
        },

        setupSiteTreeSelector: function(tree) {
            var my = this
            if (!tree) {
                return false
            }
            if (tree.displayMenu && my.options.siteTreeID < 1) {
                if (!my.$element.find('div.ccm-sitemap-tree-selector-wrapper select').length) {
                    my.$element.find('div.ccm-sitemap-tree-selector-wrapper').append($(my.localesWrapperTemplate))
                    var $menu = my.$element.find('div.ccm-sitemap-tree-selector-wrapper select')

                    if (tree.entryGroups && tree.entryGroups.length) {
                        $.each(tree.entryGroups, function (gi, group) {
                            var $optgroup = $('<optgroup label="' + group.label + '">')

                            $.each(tree.entries, function (ti, entry) {
                                if (entry.class == group.value) {
                                    var $option = '<option value="' + entry.siteTreeID + '" data-content=\'<div class="option">' + entry.element + '</div>\''

                                    if (entry.isSelected) {
                                        $option += ' selected'
                                    }

                                    $option += '>' + entry.title + '</option>'
                                    $optgroup.append($option)
                                }
                            })

                            $menu.append($optgroup)
                        })
                    } else {
                        $.each(tree.entries, function (ti, entry) {
                            var $option = '<option value="' + entry.siteTreeID + '" data-content=\'<div class="option">' + entry.element + '</div>\''

                            if (entry.isSelected) {
                                $option += ' selected'
                            }

                            $option += '>' + entry.title + '</option>'
                            $menu.append($option)
                        })
                    }

                    $menu.selectpicker({
                        liveSearch: true,
                        maxOptions: 1
                    })

                    $menu.on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
                        var treeID = $(this).selectpicker('val')
                        if (treeID != previousValue) {
                            var source = my.getTree().options.source
                            my.options.siteTreeID = treeID
                            source.data.siteTreeID = treeID
                            my.getTree().reload(source)
                        }
                    })
                }
            }
        },

        setupTree: function() {
            var minExpandLevel
            var my = this
            var doPersist = true
            var treeSelectMode = 1
            var checkbox = false
            var classNames = false
            var dndPerformed = false

            if (my.options.selectMode == 'single') {
                checkbox = true
                classNames = { checkbox: 'fancytree-radio' }
            } else if (my.options.selectMode == 'multiple') {
                treeSelectMode = 2
                checkbox = true
            } else if (my.options.selectMode == 'hierarchical-multiple') {
                treeSelectMode = 3
                checkbox = true
            }

            if (checkbox) {
                doPersist = false
            }

            if (my.options.minExpandLevel !== false) {
                minExpandLevel = my.options.minExpandLevel
            } else {
                if (my.options.displaySingleLevel) {
                    if (my.options.cParentID) {
                        minExpandLevel = 3
                    } else {
                        minExpandLevel = 2
                    }
                    doPersist = false
                } else {
                    if (my.options.selectMode) {
                        minExpandLevel = 2
                    } else {
                        minExpandLevel = 1
                    }
                }
            }

            if (!my.options.persist) {
                doPersist = false
            }

            var ajaxData = $.extend({
                isSitemapOverlay: my.options.isSitemapOverlay ? 1 : 0,
                displayNodePagination: my.options.displayNodePagination ? 1 : 0,
                cParentID: my.options.cParentID,
                siteTreeID: my.options.siteTreeID,
                displaySingleLevel: my.options.displaySingleLevel ? 1 : 0,
                includeSystemPages: my.options.includeSystemPages ? 1 : 0
            }, my.options.ajaxData)

            var extensions = ['glyph', 'dnd']
            if (doPersist) {
                extensions.push('persist')
            }

            var _sitemap = _.template(my.sitemapTemplate)

            my.$element.append(_sitemap)
            my.$sitemap = my.$element.find('div.ccm-sitemap-tree')
            my.$sitemap.fancytree({
                tabindex: null,
                titlesTabbable: false,
                extensions: extensions,
                glyph: {
                    preset: 'awesome5'
                },
                persist: {
                    // Available options with their default:
                    cookieDelimiter: '~', // character used to join key strings
                    cookiePrefix: my.options.cookieId,
                    cookie: { // settings passed to jquery.cookie plugin
                        path: CCM_REL + '/'
                    }
                },
                autoFocus: false,
                classNames: classNames,
                source: {
                    url: my.options.dataSource,
                    data: ajaxData
                },
                init: function() {
                    if (my.options.init) {
                        my.options.init.call()
                    }
                    if (my.options.displayNodePagination) {
                        my.setupNodePagination(my.$sitemap, my.options.cParentID)
                    }
                    var treeData = my.getTree().data
                    my.homeCID = 'homeCID' in treeData ? treeData.homeCID : null
                    my.setupSiteTreeSelector(treeData.trees)
                },

                selectMode: treeSelectMode,
                checkbox: checkbox,
                minExpandLevel: minExpandLevel,
                clickFolderMode: 2,
                lazyLoad: function(event, data) {
                    if (!my.options.displaySingleLevel) {
                        data.result = my.getLoadNodePromise(data.node)
                    } else {
                        return false
                    }
                },

                click: function(event, data) {
                    var node = data.node
                    if (data.targetType == 'title' && node.data.cID) {
                        // I have a select mode, so clicking on the title does nothing.
                        if (my.options.selectMode) {
                            return false
                        }

                        // I have a special on click handler, so we run that. It CAN return
                        // false to disable the on click, but it probably won't.
                        if (my.options.onClickNode) {
                            event.preventDefault()
                            event.stopPropagation()
                            return my.options.onClickNode.call(my, node)
                        }

                        var menu = new ConcretePageMenu($(node.li), {
                            menuOptions: my.options,
                            data: node.data,
                            sitemap: my,
                            onHide: function(menu) {
                                menu.$launcher.each(function() {
                                    $(this).unbind('mousemove.concreteMenu')
                                })
                            }
                        })
                        menu.show(event)
                    } else if (node.data.href) {
                        window.location.href = node.data.href
                    } else if (my.options.displaySingleLevel) {
                        my.displaySingleLevel(node)
                        return false
                    }
                },
                select: function(event, data, flag) {
                    if (my.options.onSelectNode) {
                        my.options.onSelectNode.call(my, data.node, data.node.isSelected())
                    }
                },

                dnd: {
                    // https://github.com/mar10/fancytree/wiki/ExtDnd
                    focusOnClick: true, // Focus although draggable cancels mousedown event?
                    preventRecursiveMoves: false, // Prevent dropping nodes on own descendants?
                    preventVoidMoves: false, // Prevent dropping nodes 'before self', etc.
                    dragStart: function(sourceNode, data) { // return true to enable dnd
                        if (my.options.selectMode) {
                            return false
                        }
                        if (!sourceNode.data.cID) {
                            return false
                        }
                        dndPerformed = true
                        my.$sitemap.addClass('ccm-sitemap-dnd')
                        return true
                    },
                    dragEnter: function(targetNode, data) { // return false: disable drag, true: enable drag, string (or string[]) to limit operations ('over', 'before', 'after')
                        if (!data.otherNode) {
                            // data.otherNode may be null for non-fancytree droppables
                            return false
                        }
                        var hoverCID = parseInt(targetNode.data.cID)
                        var draggingCID = parseInt(data.otherNode.data.cID)
                        var hoveringHome = !(targetNode.parent && targetNode.parent.data.cID)

                        if (!hoverCID || !draggingCID) {
                            // something strange occurred
                            return false
                        }
                        if (targetNode.data.cAlias) {
                            // destination is an alias
                            return ['before', 'after']
                        }
                        if (hoverCID === draggingCID) {
                            // we can only copy node under itself
                            return 'over'
                        }
                        if (hoveringHome) {
                            // home gets no siblings
                            return 'over'
                        }
                        return true
                    },
                    dragDrop: function(targetNode, data) {
                        if (targetNode.parent.data.cID == data.otherNode.parent.data.cID && data.hitMode != 'over') {
                            // we are reordering
                            data.otherNode.moveTo(targetNode, data.hitMode)
                            my.rescanDisplayOrder(data.otherNode.parent)
                        } else {
                            // we are dragging either onto a node or into another part of the site
                            my.selectMoveCopyTarget(data.otherNode, targetNode, data.hitMode)
                        }
                    },
                    dragStop: function() {
                        my.$sitemap.removeClass('ccm-sitemap-dnd')
                        setTimeout(function() {
                            dndPerformed = false
                        }, 0)
                    }
                }
            })
            ConcreteEvent.subscribe('ConcreteMenuShow', function(e, data) {
                if (dndPerformed) {
                    data.menu.hide()
                }
            })
        },

        /**
 * These are events that are useful when the sitemap is in the Dashboard, but
 * they should NOT be listened to when the sitemap is in select Mode.
 */
        setupTreeEvents: function() {
            var my = this
            if (my.options.selectMode || my.options.onClickNode) {
                return false
            }
            ConcreteEvent.unsubscribe('SitemapDeleteRequestComplete.sitemap')
            ConcreteEvent.subscribe('SitemapDeleteRequestComplete.sitemap', function(e) {
                var node = my.$sitemap.fancytree('getActiveNode')
                var parent = node.parent
                my.reloadNode(parent)
                $(my.$sitemap).fancytree('getTree').visit(function(node) {
                    // update the trash node when a page is deleted
                    if (node.data.isTrash) {
                        var isTrashNodeExpanded = node.expanded
                        my.getLoadNodePromise(node).done(function(data) {
                            node.removeChildren()
                            node.addChildren(data)
                            if (isTrashNodeExpanded) {
                                node.setExpanded(true, { noAnimation: true })
                            }
                        })
                        return false
                    }
                })
            })
            ConcreteEvent.unsubscribe('SitemapAddPageRequestComplete.sitemap')
            ConcreteEvent.subscribe('SitemapAddPageRequestComplete.sitemap', function(e, data) {
                var node = my.getTree().getNodeByKey(String(data.cParentID))
                if (node) {
                    my.reloadNode(node)
                }
                jQuery.fn.dialog.closeAll()
            })
            ConcreteEvent.subscribe('SitemapUpdatePageRequestComplete.sitemap', function(e, data) {
                try {
                    var node = my.getTree().getNodeByKey(String(data.cID))
                    var parent = node.parent
                    if (parent) {
                        my.reloadNode(parent)
                    }
                } catch (ex) {}
            })
            ConcreteEvent.unsubscribe('PageVersionChanged.deleted')
            ConcreteEvent.unsubscribe('PageVersionChanged.duplicated')
            Concrete.event.subscribe(['PageVersionChanged.deleted', 'PageVersionChanged.duplicated'], function(e, data) {
                my.reloadSelfNodeByCID(data.cID)
            })
        },

        rescanDisplayOrder: function(node) {
            var childNodes = node.getChildren()
            var params = []
            var i

            node.setStatus('loading')
            for (i = 0; i < childNodes.length; i++) {
                var childNode = childNodes[i]
                params.push({ name: 'cID[]', value: childNode.data.cID })
            }
            $.concreteAjax({
                dataType: 'json',
                type: 'POST',
                data: params,
                url: CCM_DISPATCHER_FILENAME + '/ccm/system/backend/dashboard/sitemap_update',
                success: function(r) {
                    node.setStatus('ok')
                    ConcreteAlert.notify({
                        message: r.message
                    })
                }
            })
        },

        selectMoveCopyTarget: function(node, destNode, dragMode) {
            var my = this
            var dialog_title = ccmi18n_sitemap.moveCopyPage
            if (!dragMode) {
                dragMode = ''
            }
            var dialog_url = CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/drag_request?origCID=' + node.data.cID + '&destCID=' + destNode.data.cID + '&dragMode=' + dragMode
            var dialog_height = 'auto'
            var dialog_width = 520

            $.fn.dialog.open({
                title: dialog_title,
                href: dialog_url,
                width: dialog_width,
                modal: false,
                height: dialog_height
            })

            ConcreteEvent.unsubscribe('SitemapDragRequestComplete.sitemap')
            ConcreteEvent.subscribe('SitemapDragRequestComplete.sitemap', function(e, data) {
                switch (data.task) {
                case 'COPY_VERSION':
                    my.reloadSelfNode(destNode)
                    break
                default:
                    var reloadNode = destNode.parent
                    if (dragMode == 'over') {
                        reloadNode = destNode
                    }
                    if (data.task == 'MOVE') {
                        node.remove()
                    }
                    reloadNode.removeChildren()

                    my.reloadNode(reloadNode, function() {
                        if (!destNode.bExpanded) {
                            destNode.setExpanded(true, { noAnimation: true })
                        }
                    })
                }
            })
        },

        setupNodePagination: function($tree) {
            $tree.find('.ccm-pagination-bound').remove()
            var pg = $tree.find('div.ccm-pagination-wrapper')
            var my = this
            if (pg.length) {
                pg.find('a:not([disabled])').unbind('click').on('click', function() {
                    var href = $(this).attr('href')
                    var root = my.$sitemap.fancytree('getRootNode')
                    jQuery.fn.dialog.showLoader()
                    $.ajax({
                        dataType: 'json',
                        url: href,
                        success: function(data) {
                            jQuery.fn.dialog.hideLoader()
                            root.removeChildren()
                            root.addChildren(data)
                            my.setupNodePagination(my.$sitemap)
                        }
                    })
                    return false
                })

                pg.addClass('ccm-pagination-bound').appendTo($tree)
            }
        },

        displaySingleLevel: function(node) {
            var my = this
            /* minExpandLevel = parseInt(node.data.cID) === my.homeCID ? 2 : 3, */
            var options = my.options;

            (my.options.onDisplaySingleLevel || $.noop).call(this, node)

            var root = my.$sitemap.fancytree('getRootNode')
            // my.$sitemap.fancytree('option', 'minExpandLevel', minExpandLevel);
            var ajaxData = $.extend({
                dataType: 'json',
                isSitemapOverlay: options.isSitemapOverlay ? 1 : 0,
                displayNodePagination: options.displayNodePagination ? 1 : 0,
                siteTreeID: options.siteTreeID,
                cParentID: node.data.cID,
                displaySingleLevel: true,
                includeSystemPages: options.includeSystemPages ? 1 : 0
            }, options.ajaxData)

            jQuery.fn.dialog.showLoader()
            return $.ajax({
                dataType: 'json',
                url: options.dataSource,
                data: ajaxData,
                success: function(data) {
                    jQuery.fn.dialog.hideLoader()
                    root.removeChildren()
                    root.addChildren(data)
                    my.setupNodePagination(my.$sitemap, node.data.key)
                }
            })
        },

        getLoadNodePromise: function(node) {
            var my = this
            var options = my.options
            var ajaxData = $.extend({
                cParentID: node.data.cID ? node.data.cID : 0,
                siteTreeID: options.siteTreeID,
                reloadNode: 1,
                includeSystemPages: options.includeSystemPages ? 1 : 0,
                isSitemapOverlay: options.isSitemapOverlay ? 1 : 0,
                displayNodePagination: options.displayNodePagination ? 1 : 0
            }, options.ajaxData)
            var params = {
                dataType: 'json',
                url: options.dataSource,
                data: ajaxData
            }

            return $.ajax(params)
        },

        reloadNode: function(node, onComplete) {
            this.getLoadNodePromise(node).done(function(data) {
                node.removeChildren()
                node.addChildren(data)
                node.setExpanded(true, { noAnimation: true })
                if (onComplete) {
                    onComplete()
                }
            })
        },

        getLoadSelfNodePromise: function(node) {
            return $.ajax({
                dataType: 'json',
                url: this.options.dataSource,
                data: $.extend({
                    cID: node.data.cID,
                    reloadNode: 1,
                    reloadSelfNode: 1
                }, this.options.ajaxData)
            })
        },

        reloadSelfNode: function(node, onComplete) {
            this.getLoadSelfNodePromise(node).done(function(data) {
                var nodeData = data[0]
                node.setTitle(nodeData.title)
                if (onComplete) {
                    onComplete()
                }
            })
        },

        reloadSelfNodeByCID: function(cID, onComplete) {
            var node = cID ? this.getTree().getNodeByKey(String(cID)) : null
            if (node) {
                this.reloadSelfNode(node, onComplete)
            }
        }
    }

    /**
 * Static methods
 */

    ConcreteSitemap.exitEditMode = function(cID) {
        $.get(CCM_DISPATCHER_FILENAME + '/ccm/system/backend/dashboard/sitemap_check_in?cID=' + cID + '&ccm_token=' + CCM_SECURITY_TOKEN)
    }

    ConcreteSitemap.submitDragRequest = function($form) {
        var params = {
            ccm_token: $form.find('input[name="validationToken"]').val(),
            dragMode: $form.find('input[name="dragMode"]').val(),
            destCID: $form.find('input[name="destCID"]').val(),
            destSibling: $form.find('input[name="destSibling"]').val() || '',
            origCID: $form.find('input[name="origCID"]').val(),
            ctask: $('input[name=ctask]:checked').val()
        }
        switch (params.ctask) {
        case 'MOVE':
            params.saveOldPagePath = $form.find('input[name="saveOldPagePath"]').is(':checked') ? 1 : 0
            break
        case 'a-copy-operation':
            params.ctask = $('input[name="dtask"]:checked').val()
            break
        }
        var paramsArray = []
        $.each(params, function (name, value) {
            paramsArray.push({ name: name, value: value })
        })
        if (params.ctask === 'COPY_ALL') {
            /* eslint-disable no-new */
            new ConcreteProgressiveOperation({
                url: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/drag_request/copy_all',
                data: paramsArray,
                title: ccmi18n_sitemap.copyProgressTitle,
                onComplete: function() {
                    $('.ui-dialog-content').dialog('close')
                    ConcreteEvent.publish('SitemapDragRequestComplete', { task: params.ctask })
                }
            })
        } else {
            jQuery.fn.dialog.showLoader()
            $.getJSON(
                CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/drag_request/submit',
                params,
                function(resp) {
                    jQuery.fn.dialog.closeAll()
                    jQuery.fn.dialog.hideLoader()
                    ConcreteAlert.notify({ message: resp.message })
                    ConcreteEvent.publish('SitemapDragRequestComplete', { task: params.ctask })
                    jQuery.fn.dialog.closeTop()
                    jQuery.fn.dialog.closeTop()
                }
            ).fail(function(xhr, status, error) {
                jQuery.fn.dialog.hideLoader()
                var msg = error; var json = xhr ? xhr.responseJSON : null
                if (json && json.error) {
                    msg = json.errors instanceof Array ? json.errors.join('\n') : json.error
                }
                window.alert(msg)
            })
        }
    }

    // jQuery Plugin
    $.fn.concreteSitemap = function(options) {
        return $.each($(this), function(i, obj) {
            /* eslint-disable no-new */
            new ConcreteSitemap($(this), options)
        })
    }

    global.ConcreteSitemap = ConcreteSitemap
})(global, jQuery)
