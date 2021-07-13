/* eslint-disable no-new, no-unused-vars, camelcase, eqeqeq */

var DIALOGS = {
    externalLink: {
        width: 350,
        height: 270
    },
    alias: {
        width: 350,
        height: 270
    }
}

function ConcretePageAjaxSearch($element, options) {
    var my = this
    options = $.extend({
        mode: 'menu',
        searchMethod: 'get'
    }, options)

    my.options = options

    my._templateSearchResultsMenu = _.template(ConcretePageAjaxSearchMenu.get())
    ConcreteAjaxSearch.call(my, $element, options)

    my.setupEvents()
}

ConcretePageAjaxSearch.prototype = Object.create(ConcreteAjaxSearch.prototype)

ConcretePageAjaxSearch.prototype.setupEvents = function () {
    var my = this
    ConcreteEvent.subscribe('SitemapDeleteRequestComplete', function (e) {
        my.refreshResults()
    })

    ConcreteEvent.fire('ConcreteSitemapPageSearch', my)
}

ConcretePageAjaxSearch.prototype.updateResults = function (result) {
    var my = this; var $e = my.$element
    ConcreteAjaxSearch.prototype.updateResults.call(my, result)
    if (my.options.mode == 'choose') {
        // hide the checkbox since they're pointless here.
        $e.find('.ccm-search-results-checkbox').parent().remove()
        // hide the bulk item selector.
        $e.find('select[data-bulk-action]').parent().remove()

        $e.unbind('.concretePageSearchHoverPage')
        $e.on('mouseover.concretePageSearchHoverPage', 'tr[data-launch-search-menu]', function () {
            $(this).addClass('ccm-search-select-hover')
        })
        $e.on('mouseout.concretePageSearchHoverPage', 'tr[data-launch-search-menu]', function () {
            $(this).removeClass('ccm-search-select-hover')
        })
        $e.unbind('.concretePageSearchChoosePage').on('click.concretePageSearchChoosePage', 'tr[data-launch-search-menu]', function () {
            ConcreteEvent.publish('SitemapSelectPage', {
                instance: my,
                cID: $(this).attr('data-page-id'),
                title: $(this).attr('data-page-name')
            })
            return false
        })
    }
}

ConcretePageAjaxSearch.prototype.handleSelectedBulkAction = function (value, type, $option, $items) {
    if (value == 'movecopy' || value == 'Move/Copy') {
        var url; var itemIDs = []
        $.each($items, function (i, checkbox) {
            itemIDs.push($(checkbox).val())
        })

        ConcreteEvent.unsubscribe('SitemapSelectPage.search')

        var subscription = function (e, data) {
            Concrete.event.unsubscribe(e)
            url = CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/drag_request?origCID=' + itemIDs.join(',') + '&destCID=' + data.cID
            $.fn.dialog.open({
                width: 520,
                height: 'auto',
                href: url,
                title: ccmi18n_sitemap.moveCopyPage,
                onDirectClose: function() {
                    ConcreteEvent.subscribe('SitemapSelectPage.search', subscription)
                }
            })
        }
        ConcreteEvent.subscribe('SitemapSelectPage.search', subscription)
    }
    ConcreteAjaxSearch.prototype.handleSelectedBulkAction.call(this, value, type, $option, $items)
}

ConcreteAjaxSearch.prototype.createMenu = function ($selector) {
    var my = this
    $selector.concretePageMenu({
        container: my,
        menu: $('[data-search-menu=' + $selector.attr('data-launch-search-menu') + ']')
    })
}

/**
 * Static Methods
 */
ConcretePageAjaxSearch.launchDialog = function(callback, options) {
    options = $.extend({
        includeSystemPages: false,
        askIncludeSystemPages: false
    }, options || {})
    var url = CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/sitemap_selector?' + [
        'includeSystemPages=' + (options.includeSystemPages ? 1 : 0),
        'askIncludeSystemPages=' + (options.askIncludeSystemPages ? 1 : 0)
    ].join('&')
    $.fn.dialog.open({
        width: '90%',
        height: '75%',
        href: url,
        modal: true,
        title: ccmi18n_sitemap.pageLocationTitle,
        onClose: function() {
            ConcreteEvent.fire('PageSelectorClose')
        },
        onOpen: function() {
            ConcreteEvent.unsubscribe('SitemapSelectPage')
            ConcreteEvent.subscribe('SitemapSelectPage', function(e, data) {
                jQuery.fn.dialog.closeTop()
                callback(data)
            })
        }
    })
}

ConcretePageAjaxSearch.getPageDetails = function(cID, callback) {
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: CCM_DISPATCHER_FILENAME + '/ccm/system/page/get_json',
        data: { cID: cID },
        error: function(r) {
            ConcreteAlert.dialog(ccmi18n.error, r.responseText)
        },
        success: function(r) {
            callback(r)
        }
    })
}
var ConcretePageAjaxSearchMenu = {

    get: function () {
        return ['',
            '<div class="popover fade" data-search-page-menu="<%=item.cID%>" data-search-menu="<%=item.cID%>">',
            '<div class="arrow"></div>',
            '<div class="popover-inner">',
            '<div class="dropdown-menu">',
            '<% if (item.isTrash) { %>',
            '<a class="dropdown-item" data-action="empty-trash" href="javascript:void(0)">' + ccmi18n_sitemap.emptyTrash + '</a></li>',
            '<% } else if (item.isInTrash) { %>',
            '<a class="dropdown-item" data-action="delete-forever" href="javascript:void(0)">' + ccmi18n_sitemap.deletePageForever + '</a></li>',
            '<% } else if (item.cAlias == \'LINK\' || item.cAlias == \'POINTER\') { %>',
            '<a class="dropdown-item" href="<%- item.link %>">' + ccmi18n_sitemap.visitExternalLink + '</a></li>',
            '<% if (item.cAlias == \'POINTER\' && item.canEditPageProperties) { %>',
            '<a class="dropdown-item dialog-launch" dialog-width="' + DIALOGS.alias.width + '" dialog-height="' + DIALOGS.alias.height + '" dialog-title="' + ccmi18n_sitemap.editAlias + '" dialog-modal="false" dialog-append-buttons="true" href="' + CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/edit_alias?cID=<%=item.cID%>">' + ccmi18n_sitemap.editAlias + '</a></li>',
            '<% } %>',
            '<% if (item.cAlias == \'LINK\' && item.canEditPageProperties) { %>',
            '<a class="dropdown-item dialog-launch" dialog-width="' + DIALOGS.externalLink.width + '" dialog-height="' + DIALOGS.externalLink.height + '" dialog-title="' + ccmi18n_sitemap.editExternalLink + '" dialog-modal="false" dialog-append-buttons="true" href="' + CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/edit_external?cID=<%=item.cID%>">' + ccmi18n_sitemap.editExternalLink + '</a></li>',
            '<a class="dropdown-item dialog-launch" dialog-on-close="ConcreteSitemap.exitEditMode(<%=item.cID%>)" dialog-width="90%" dialog-height="70%" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.pageAttributesTitle + '" href="' + CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/attributes?cID=<%=item.cID%>">' + ccmi18n_sitemap.pageAttributes + '</a></li>',
            '<a class="dropdown-item dialog-launch" dialog-on-close="ConcreteSitemap.exitEditMode(<%=item.cID%>)" dialog-width="500" dialog-height="630" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.setPagePermissions + '" href="' + CCM_DISPATCHER_FILENAME + '/ccm/system/panels/details/page/permissions?cID=<%=item.cID%>">' + ccmi18n_sitemap.setPagePermissions + '</a></li>',
            '<% } %>',
            '<% if (item.canDeletePage) { %>',
            '<a class="dropdown-item dialog-launch" dialog-width="360" dialog-height="150" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.deleteExternalLink + '" href="' + CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/delete_alias?cID=<%=item.cID%>">' + ccmi18n_sitemap.deleteExternalLink + '</a></li>',
            '<% } %>',
            '<% } else { %>',
            '<a class="dropdown-item " href="<%- item.link %>">' + ccmi18n_sitemap.visitPage + '</a></li>',
            '<% if (item.canEditPageProperties || item.canEditPageSpeedSettings || item.canEditPagePermissions || item.canEditPageDesign || item.canViewPageVersions || item.canDeletePage) { %>',
            '<div class="dropdown-divider"></div>',
            '<% } %>',
            '<% if (item.canEditPageProperties) { %>',
            '<a class="dropdown-item dialog-launch" dialog-on-close="ConcreteSitemap.exitEditMode(<%=item.cID%>)" dialog-width="640" dialog-height="360" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.seo + '" href="' + CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/seo?cID=<%=item.cID%>">' + ccmi18n_sitemap.seo + '</a></li>',
            '<a class="dropdown-item dialog-launch" dialog-on-close="ConcreteSitemap.exitEditMode(<%=item.cID%>)" dialog-width="500" dialog-height="500" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.pageLocationTitle + '" href="' + CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/location?cID=<%=item.cID%>">' + ccmi18n_sitemap.pageLocation + '</a></li>',
            '<div class="dropdown-divider"></div>',
            '<a class="dropdown-item dialog-launch" dialog-on-close="ConcreteSitemap.exitEditMode(<%=item.cID%>)" dialog-width="90%" dialog-height="70%" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.pageAttributesTitle + '" href="' + CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/attributes?cID=<%=item.cID%>">' + ccmi18n_sitemap.pageAttributes + '</a></li>',
            '<% } %>',
            '<% if (item.canEditPageSpeedSettings) { %>',
            '<a class="dropdown-item dialog-launch" dialog-on-close="ConcreteSitemap.exitEditMode(<%=item.cID%>)" dialog-width="550" dialog-height="280" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.speedSettingsTitle + '" href="' + CCM_DISPATCHER_FILENAME + '/ccm/system/panels/details/page/caching?cID=<%=item.cID%>">' + ccmi18n_sitemap.speedSettings + '</a></li>',
            '<% } %>',
            '<% if (item.canEditPagePermissions) { %>',
            '<a class="dropdown-item dialog-launch" dialog-on-close="ConcreteSitemap.exitEditMode(<%=item.cID%>)" dialog-width="500" dialog-height="630" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.setPagePermissions + '" href="' + CCM_DISPATCHER_FILENAME + '/ccm/system/panels/details/page/permissions?cID=<%=item.cID%>">' + ccmi18n_sitemap.setPagePermissions + '</a></li>',
            '<% } %>',
            '<% if (item.canEditPageDesign || item.canEditPageType) { %>',
            '<a class="dropdown-item dialog-launch" dialog-on-close="ConcreteSitemap.exitEditMode(<%=item.cID%>)" dialog-width="350" dialog-height="500" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.pageDesign + '" href="' + CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/design?cID=<%=item.cID%>">' + ccmi18n_sitemap.pageDesign + '</a></li>',
            '<% } %>',
            '<% if (item.canViewPageVersions) { %>',
            '<a class="dropdown-item dialog-launch" dialog-on-close="ConcreteSitemap.exitEditMode(<%=item.cID%>)" dialog-width="640" dialog-height="340" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.pageVersions + '" href="' + CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/versions?cID=<%=item.cID%>">' + ccmi18n_sitemap.pageVersions + '</a></li>',
            '<% } %>',
            '<% if (item.canDeletePage) { %>',
            '<a class="dropdown-item dialog-launch" dialog-on-close="ConcreteSitemap.exitEditMode(<%=item.cID%>)" dialog-width="360" dialog-height="250" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.deletePage + '" href="' + CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/delete_from_sitemap?cID=<%=item.cID%>">' + ccmi18n_sitemap.deletePage + '</a></li>',
            '<% } %>',
            '<div class="dropdown-divider" data-sitemap-mode="explore"></div>',
            '<a data-sitemap-mode="explore" class="dropdown-item dialog-launch" dialog-width="90%" dialog-height="70%" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.moveCopyPage + '" href="' + CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/sitemap_selector?sitemap_select_mode=move_copy_delete&cID=<%=item.cID%>">' + ccmi18n_sitemap.moveCopyPage + '</a></li>',
            '<a data-sitemap-mode="explore" class="dropdown-item" href="' + CCM_DISPATCHER_FILENAME + '/dashboard/sitemap/explore?cNodeID=<%=item.cID%>&task=send_to_top">' + ccmi18n_sitemap.sendToTop + '</a></li>',
            '<a data-sitemap-mode="explore" class="dropdown-item" href="' + CCM_DISPATCHER_FILENAME + '/dashboard/sitemap/explore?cNodeID=<%=item.cID%>&task=send_to_bottom">' + ccmi18n_sitemap.sendToBottom + '</a></li>',
            '<% if (item.numSubpages > 0) { %>',
            '<li class="dropdown-divider"></li>',
            '<a class="dropdown-item" href="' + CCM_DISPATCHER_FILENAME + '/dashboard/sitemap/search/?submitSearch=1&field[]=parent_page&cParentAll=1&cParentIDSearchField=<%=item.cID%>">' + ccmi18n_sitemap.searchPages + '</a></li>',
            '<a class="dropdown-item" href="' + CCM_DISPATCHER_FILENAME + '/dashboard/sitemap/explore/-/<%=item.cID%>">' + ccmi18n_sitemap.explorePages + '</a></li>',
            '<% } %>',
            '<% if (item.canAddExternalLinks || item.canAddSubpages) { %>',
            '<div class="dropdown-divider"></div>',
            '<% if (item.canAddSubpages > 0) { %>',
            '<a class="dropdown-item dialog-launch" dialog-width="350" dialog-modal="false" dialog-height="350" dialog-title="' + ccmi18n_sitemap.addPage + '" dialog-modal="false" href="' + CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/add?cID=<%=item.cID%>">' + ccmi18n_sitemap.addPage + '</a></li>',
            '<% } %>',
            '<% if (item.canAddExternalLinks > 0) { %>',
            '<a class="dropdown-item dialog-launch" dialog-width="' + DIALOGS.externalLink.width + '" dialog-modal="false" dialog-height="' + DIALOGS.externalLink.height + '" dialog-title="' + ccmi18n_sitemap.addExternalLink + '" dialog-modal="false" href="' + CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/add_external?cID=<%=item.cID%>">' + ccmi18n_sitemap.addExternalLink + '</a></li>',
            '<% } %>',
            '<% } %>',
            '<% } %>',
            '</div>',
            '</div>',
            '</div>',
            ''].join('')
    }
}

// jQuery Plugin
$.fn.concretePageAjaxSearch = function (options) {
    return $.each($(this), function (i, obj) {
        new ConcretePageAjaxSearch($(this), options)
    })
}

global.ConcretePageAjaxSearch = ConcretePageAjaxSearch
global.ConcretePageAjaxSearchMenu = ConcretePageAjaxSearchMenu
