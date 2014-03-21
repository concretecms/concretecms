!function(global, $, _) {
	'use strict';

	function ConcretePageMenu($element, options) {
		var my = this, 
			options = options || {};

		options = $.extend({
			'sitemap': false,
			'data': {},
			'menuOptions': {}
		}, options);

		ConcreteMenu.call(my, $element, options);
		my.$menu = $(_.template(my.getMenu(), {options: options.menuOptions, data: options.data}));
	}

	ConcretePageMenu.prototype = Object.create(ConcreteMenu.prototype);

	ConcretePageMenu.prototype.getMenu = function() {
		return '<div class="ccm-popover-page-menu popover fade" data-search-page-menu="<%=data.cID%>" data-search-menu="<%=data.cID%>">' +
			'<div class="arrow"></div><div class="popover-inner"><ul class="dropdown-menu">' + 
			'<% if (data.isTrash) { %>' + 
				'<li><a data-action="empty-trash" href="javascript:void(0)">' + ccmi18n_sitemap.emptyTrash + '</a></li>' + 
			'<% } else if (data.isInTrash) { %>' + 
				'<li><a onclick="ccm_previewInternalTheme(<%=data.cID%>, false, \'' + ccmi18n_sitemap.previewPage + '\')" href="javascript:void(0)">' + ccmi18n_sitemap.previewPage + '</a></li>' +
				'<li class="divider"><\/li>' + 
				'<li><a data-action="delete-forever" href="javascript:void(0)">' + ccmi18n_sitemap.deletePageForever + '</a></li>' +
			'<% } else if (data.cAlias == \'LINK\' || data.cAlias == \'POINTER\') { %>' +
				'<li><a onclick="window.location.href=\'' + CCM_DISPATCHER_FILENAME + '?cID=<%=data.cID%>\'" href="javascript:void(0)">' + ccmi18n_sitemap.visitExternalLink + '</a></li>' +
				'<% if (data.cAlias == \'LINK\' && data.canEditProperties) { %>' +
					'<li><a dialog-width="350" dialog-height="170" dialog-title="' + ccmi18n_sitemap.editExternalLink + '" dialog-modal="false" dialog-append-buttons="true" href="' + CCM_TOOLS_PATH + '/edit_collection_popup?rel=SITEMAP&cID=<%=data.cID%>&ctask=edit_external">' + ccmi18n_sitemap.editExternalLink + '</a></li>' +
				'<% } %>' +
				'<% if (data.canDelete) { %>' +
					'<li><a dialog-width="360" dialog-height="150" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.deleteExternalLink + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup?rel=SITEMAP&cID=<%=data.cID%>&ctask=delete_external">' + ccmi18n_sitemap.deleteExternalLink + '</a></li>' +
				'<% } %>' +
			'<% } else { %>' + 
				'<li><a href="#" data-action="visit">' + ccmi18n_sitemap.visitPage + '</a></li>' +
				'<% if (data.canEditPageProperties || data.canEditPageSpeedSettings || data.canEditPagePermissions || data.canEditPageDesign || data.canViewPageVersions || data.canDeletePage) { %>' + 
					'<li class="divider"></li>' + 
				'<% } %>' +
				'<% if (data.canEditPageProperties) { %>' + 
					'<li><a class="dialog-launch" dialog-on-close="ConcreteSitemap.exitEditMode(<%=data.cID%>)" dialog-width="850" dialog-height="360" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.pagePropertiesTitle + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup?rel=SITEMAP&cID=<%=data.cID%>&ctask=edit_metadata">' + ccmi18n_sitemap.pageProperties + '</a></li>' + 
				'<% } %>' +
				'<% if (data.canEditPageSpeedSettings) { %>' + 
					'<li><a class="dialog-launch" dialog-on-close="ConcreteSitemap.exitEditMode(<%=data.cID%>)" dialog-width="550" dialog-height="280" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.speedSettingsTitle + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup?rel=SITEMAP&cID=<%=data.cID%>&ctask=edit_speed_settings">' + ccmi18n_sitemap.speedSettings + '</a></li>' + 
				'<% } %>' +
				'<% if (data.canEditPagePermissions) { %>' + 
					'<li><a class="dialog-launch" dialog-on-close="ConcreteSitemap.exitEditMode(<%=data.cID%>)" dialog-width="420" dialog-height="630" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.setPagePermissions + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup?rel=SITEMAP&cID=<%=data.cID%>&ctask=edit_permissions">' + ccmi18n_sitemap.setPagePermissions + '</a></li>' + 
				'<% } %>' +
				'<% if (data.canEditPageDesign) { %>' + 
					'<li><a class="dialog-launch" dialog-on-close="ConcreteSitemap.exitEditMode(<%=data.cID%>)" dialog-width="610" dialog-height="405" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.pageDesign + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup?rel=SITEMAP&cID=<%=data.cID%>&ctask=set_theme">' + ccmi18n_sitemap.pageDesign + '</a></li>' + 
				'<% } %>' +
				'<% if (data.canViewPageVersions) { %>' + 
					'<li><a class="dialog-launch" dialog-on-close="ConcreteSitemap.exitEditMode(<%=data.cID%>)" dialog-width="640" dialog-height="340" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.pageVersions + '" href="' + CCM_TOOLS_PATH + '/versions?cID=<%=data.cID%>">' + ccmi18n_sitemap.pageVersions + '</a></li>' + 
				'<% } %>' +
				'<% if (data.canDeletePage) { %>' + 
					'<li><a class="dialog-launch" dialog-on-close="ConcreteSitemap.exitEditMode(<%=data.cID%>)" dialog-width="360" dialog-height="150" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.deletePage + '" href="' + CCM_DISPATCHER_FILENAME + '/system/dialogs/page/delete?cID=<%=data.cID%>">' + ccmi18n_sitemap.deletePage + '</a></li>' + 
				'<% } %>' +
				'<% if (options.displaySingleLevel) { %>' + 
					'<li class="divider"></li>' + 
					'<li><a class="dialog-launch" dialog-width="90%" dialog-height="70%" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.moveCopyPage + '" href="' + CCM_TOOLS_PATH + '/sitemap_search_selector?sitemap_select_mode=move_copy_delete&cID=<%=data.cID%>>' + ccmi18n_sitemap.moveCopyPage + '</a></li>' +
					'<li><a href="' + CCM_DISPATCHER_FILENAME + '/dashboard/sitemap/explore?cNodeID=<%=data.cID%>&task=send_to_top">' + ccmi18n_sitemap.sendToTop + '</a></li>' +
					'<li><a href="' + CCM_DISPATCHER_FILENAME + '/dashboard/sitemap/explore?cNodeID=<%=data.cID%>&task=send_to_bottom">' + ccmi18n_sitemap.sendToBottom + '</a></li>' +
				'<% } %>' +
				'<% if (data.numSubpages > 0) { %>' + 
					'<li class="divider"></li>' + 
					'<li><a href="' + CCM_DISPATCHER_FILENAME + '/dashboard/sitemap/search/?selectedSearchField[]=parent&cParentAll=1&cParentIDSearchField=<%=data.cID%>">' + ccmi18n_sitemap.searchPages + '</a></li>' +
					'<% if (!options.displaySingleLevel) { %>' +
						'<li><a href="' + CCM_DISPATCHER_FILENAME + '/dashboard/sitemap/explore/-/<%=data.cID%>">' + ccmi18n_sitemap.explorePages + '</a></li>' +
					'<% } %>' +
				'<% } %>' +
				'<% if (data.canAddExternalLinks) { %>' + 
					'<li class="divider"></li>' + 
					'<li><a class="dialog-launch" dialog-width="350" dialog-modal="false" dialog-height="170" dialog-title="' + ccmi18n_sitemap.addExternalLink + '" dialog-modal="false" href="' + CCM_TOOLS_PATH + '/edit_collection_popup?rel=SITEMAP&cID=<%=data.cID%>&ctask=add_external">' + ccmi18n_sitemap.addExternalLink + '</a></li>' +
				'<% } %>' +
			'<% } %>' +
		'</ul></div></div>';		
	}

	ConcretePageMenu.prototype.setupMenuOptions = function($menu) {
		var my = this, 
			parent = ConcreteMenu.prototype,
			cID = $menu.attr('data-search-page-menu'),
			container = my.options.container;

		parent.setupMenuOptions($menu);
		$menu.find('a[data-action=visit]').on('click', function() {
			window.location.href = CCM_DISPATCHER_FILENAME + '?cID=' + cID;
		});
		$menu.find('a[data-action=delete-forever]').on('click', function() {
			ccm_triggerProgressiveOperation(
				CCM_TOOLS_PATH + '/dashboard/sitemap_delete_forever', 
				[{'name': 'cID', 'value': cID}],
				ccmi18n_sitemap.deletePages,
				function() {
					if (my.options.sitemap) {
						var tree = my.options.sitemap.getTree(),
							node = tree.getNodeByKey(cID);

						node.remove();
					}
					ConcreteAlert.hud(ccmi18n_sitemap.deletePageSuccessMsg, 2000);
				}
			);
			return false;
		});
		$menu.find('a[data-action=empty-trash]').on('click', function() {
			ccm_triggerProgressiveOperation(
				CCM_TOOLS_PATH + '/dashboard/sitemap_delete_forever', 
				[{'name': 'cID', 'value': cID}],
				ccmi18n_sitemap.deletePages,
				function() {
					if (my.options.sitemap) {
						var tree = my.options.sitemap.getTree(),
							node = tree.getNodeByKey(cID);

						node.removeChildren();
					}
				}
			);
			return false;
		});
	}

	// jQuery Plugin
	$.fn.concretePageMenu = function(options) {
		return $.each($(this), function(i, obj) {
			new ConcretePageMenu($(this), options);
		});
	}

	global.ConcretePageMenu = ConcretePageMenu;

}(this, $, _);