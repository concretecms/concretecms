/* eslint-disable no-new, no-unused-vars, camelcase, eqeqeq */
/* global _, ccmi18n_sitemap, CCM_DISPATCHER_FILENAME, ConcreteProgressiveOperation, ConcreteAlert, ConcretePageAjaxSearchMenu, ConcreteMenu */

function ConcretePageMenu($element, options) {
    var my = this
    options = options || {}

    options = $.extend({
        sitemap: false,
        data: {},
        menuOptions: {}
    }, options)

    ConcreteMenu.call(my, $element, options)
    if (options.sitemap != false) {
        var template = _.template(ConcretePageAjaxSearchMenu.get())
        var content = template({ item: options.data })
        my.$menu = $(content)
    }
}

ConcretePageMenu.prototype = Object.create(ConcreteMenu.prototype)

ConcretePageMenu.prototype.setupMenuOptions = function($menu) {
    var my = this
    var parent = ConcreteMenu.prototype
    var cID = $menu.attr('data-search-page-menu')

    parent.setupMenuOptions($menu)
    if (!my.options.sitemap || my.options.sitemap.options.displaySingleLevel == false) {
        $menu.find('[data-sitemap-mode=explore]').remove()
    }
    $menu.find('a[data-action=delete-forever]').on('click', function() {
        new ConcreteProgressiveOperation({
            url: CCM_DISPATCHER_FILENAME + '/ccm/system/page/sitemap_delete_forever',
            data: [{ name: 'cID', value: cID }],
            title: ccmi18n_sitemap.deletePages,
            onComplete: function() {
                if (my.options.sitemap) {
                    var tree = my.options.sitemap.getTree()
                    var node = tree.getNodeByKey(String(cID))

                    node.remove()
                }
                ConcreteAlert.notify({
                    message: ccmi18n_sitemap.deletePageSuccessMsg
                })
            }
        })
        return false
    })
    $menu.find('a[data-action=empty-trash]').on('click', function() {
        new ConcreteProgressiveOperation({
            url: CCM_DISPATCHER_FILENAME + '/ccm/system/page/sitemap_delete_forever',
            data: [{ name: 'cID', value: cID }],
            title: ccmi18n_sitemap.deletePages,
            onComplete: function() {
                if (my.options.sitemap) {
                    var tree = my.options.sitemap.getTree()
                    var node = tree.getNodeByKey(String(cID))

                    node.removeChildren()
                }
            }
        })
        return false
    })
}

// jQuery Plugin
$.fn.concretePageMenu = function(options) {
    return $.each($(this), function(i, obj) {
        new ConcretePageMenu($(this), options)
    })
}

global.ConcretePageMenu = ConcretePageMenu
