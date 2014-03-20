/**
 * Base search class for AJAX forms in the UI
 */

!function(global, $, _) {
	'use strict';

	function ConcreteSitemap($element, options) {
		var my = this;
		options = options || {};
		options = $.extend({
			displayNodePagination: false,
			cParentID: 0,
			displaySingleLevel: false,
		}, options);
		my.options = options;
		my.$element = $element;
		my._sitemapMenuTemplate = _.template(ConcreteSitemap.getMenu());
		my.setupTree();
		my.setupTreeEvents();
		return my.$element;
	}

	ConcreteSitemap.prototype = {

		setupTree: function() {
			var minExpandLevel, 
				my = this,
				doPersist = true;

			if (my.options.displaySingleLevel) {
				if (my.options.cParentID == 1) {
					minExpandLevel = 2;
				} else {
					minExpandLevel = 3;
				}
				doPersist = false;
			} else {
				minExpandLevel = 1;
			}
    		$(my.$element).addClass('ccm-tree-sitemap');
    		$(my.$element).dynatree({
				autoFocus: false,
				cookieId: 'ConcreteSitemap',
				cookie: {
					path: CCM_REL + '/'
				},
				persist: doPersist,
				initAjax: {
					url: CCM_TOOLS_PATH + '/dashboard/sitemap_data',
					data: {
						'displayNodePagination': my.options.displayNodePagination ? 1 : 0,
						'cParentID': my.options.cParentID,
						'displaySingleLevel': my.options.displaySingleLevel ? 1 : 0
					}, 

				},
				onPostInit: function() {
					if (my.options.displayNodePagination) {
						my.setupNodePagination(my.$element, my.options.cParentID);
					}
				},
				selectMode: 1,
				minExpandLevel:  minExpandLevel,
				clickFolderMode: 2,
				onLazyRead: function(node) {
					if (my.options.displaySingleLevel) {
						my.displaySingleLevel(node);
					} else {
						my.reloadNode(node);
					}
				}, 
				onExpand: function(expand, node) {
					if (expand && my.options.displaySingleLevel) {
						my.displaySingleLevel(node);
					}
				},
				onClick: function(node, e) {
					if (node.getEventTargetType(e) == "title" && node.data.cID) {
						if (my.options.onSelectNode) {
							my.options.onSelectNode(node);
						
						/*} else if (methods.private.eventListenerExists(my.options.requestID, 'onSelectNode')) {
							methods.private.triggerEvent(my.options.requestID, 'onSelectNode', [node]); */

						} else {
							var $menu = my._sitemapMenuTemplate({options: my.options, data: node.data});
							if ($menu) {
								var menu = new ConcreteMenu($(node.span), {
									menu: $menu,
									handle: 'none'
								});
								menu.show(e);
							}
						}
					} else if (node.data.href) {
						window.location.href = node.data.href;
					}
				},
				fx: {height: 'toggle', duration: 200},
				dnd: {
					onDragStart: function(node) {
						if (node.data.cID) {
							return true;
						}
						return false;
					},
					onDragStop: function(node) {

					},
					autoExpandMS: 1000,
					preventVoidMoves: true,
					onDragEnter: function(node, sourceNode) {
						return true;
					},
					onDragOver: function(node, sourceNode, hitMode) {
						if (!node.parent.data.cID) {
							return false;
						}

						if (!node.data.cID && hitMode == 'after') {
							return false;
						}

				        // Prevent dropping a parent below it's own child
				        if(node.isDescendantOf(sourceNode)){
				          return false;
				        }
				        return true;

					},
					onDrop: function(node, sourceNode, hitMode, ui, draggable) {
						if (node.parent.data.cID == sourceNode.parent.data.cID && hitMode != 'over') {
							// we are reordering
				        	sourceNode.move(node, hitMode);
							my.rescanDisplayOrder(sourceNode.parent);
						} else {
							// we are dragging either onto a node or into another part of the site
							my.selectMoveCopyTarget(sourceNode, node, hitMode);
						}
					}
				}
			});
		},

		setupTreeEvents: function() {
			var my = this;
			ConcreteEvent.subscribe('SitemapDeleteRequestComplete', function(e) {
	 			var node = my.$element.dynatree('getActiveNode');
				var parent = node.parent;
				my.reloadNode(parent);
			});
		},

    	rescanDisplayOrder: function(node) {
			var childNodes = node.getChildren(),
				params = [],
				i;

			node.setLazyNodeStatus(DTNodeStatus_Loading);	
			for (i = 0; i < childNodes.length; i++) {
				var childNode = childNodes[i];
				params.push({'name': 'cID[]', 'value': childNode.data.cID});
			}
			$.concreteAjax({
				dataType: 'json',
				type: 'POST',
				data: params,
				url: CCM_TOOLS_PATH + '/dashboard/sitemap_update',
				success: function(r) {
					node.setLazyNodeStatus(DTNodeStatus_Ok);
					ConcreteAlert.hud(r.message, 'success');
				}
			});
    	},


    	selectMoveCopyTarget: function(node, destNode, dragMode) {
    		var my = this;
			var dialog_title = ccmi18n_sitemap.moveCopyPage;
			if (!dragMode) {
				var dragMode = '';
			}
			var dialog_url = CCM_TOOLS_PATH + '/dashboard/sitemap_drag_request?origCID=' + node.data.cID + '&destCID=' + destNode.data.cID + '&dragMode=' + dragMode;
			var dialog_height = 350;
			var dialog_width = 350;
			
			$.fn.dialog.open({
				title: dialog_title,
				href: dialog_url,
				width: dialog_width,
				modal: false,
				height: dialog_height
			});

			ConcreteEvent.subscribe('SitemapDragRequestComplete', function(e) {
				var reloadNode = destNode.parent;
				if (dragMode == 'over') {
					reloadNode = destNode;
				}
				reloadNode.removeChildren();
				my.reloadNode(reloadNode, function() {
					if (!destNode.bExpanded) {
						destNode.expand(true);
					}
				});
			});

    	},

    
    	setupNodePagination: function($tree, nodeKey) {
    		//var tree = $tree.dynatree('getTree');
    		var pg = $tree.find('span.ccm-sitemap-explore-paging');
    		$tree.find('div.ccm-pagination-bound').remove();
    		if (pg.length) {
    			pg.find('a').on('click', function() {
    				// load under node
    				var href = $(this).attr('href');
    				$tree.dynatree('option', 'initAjax', {
    					url: href
    				});
    				$tree.dynatree('getTree').reload();
    				return false;
    			});
	    		pg.find('div.ccm-pagination').addClass('ccm-pagination-bound').appendTo($tree);
	    		var node = $.ui.dynatree.getNode(pg);
	    		node.remove();

				$tree.dynatree('option', 'onActivate', function(node) {
					if ($(node.span).hasClass('ccm-sitemap-explore-paging')) {
						node.deactivate();
					}
				});
	    	}
    	},

    	displaySingleLevel: function(node) {
    		var my = this,
    			options = my.options,
    			minExpandLevel = (node.data.cID == 1) ? 2 : 3;

    		var root = my.$element.dynatree('getRoot');
			$(node.li).closest('[data-sitemap=container]').dynatree('option', 'minExpandLevel', minExpandLevel);
			root.removeChildren();
			root.appendAjax({
				url: CCM_TOOLS_PATH + '/dashboard/sitemap_data',
				data: {
					'displayNodePagination': options.displayNodePagination ? 1 : 0,
					'cParentID': node.data.cID,
					'displaySingleLevel': true
				},

				success: function() {
					my.setupNodePagination(root.tree.$tree, node.data.key);
				}
			});

    	},

    	reloadNode: function(node, onComplete) {
    		var my = this,
    			options = my.options,
    			params = {
					url: CCM_TOOLS_PATH + '/dashboard/sitemap_data',
					data: {
						cParentID: node.data.cID,
						'displayNodePagination': options.displayNodePagination ? 1 : 0
					},
					success: function() {
						if (onComplete) {
							onComplete();
						}
					}
				};
				
			node.appendAjax(params);
    	}

	}

	/** 
	 * Static methods
	 */

	ConcreteSitemap.visit = function(cID) {
		window.location.href = CCM_DISPATCHER_FILENAME + '?cID=' + cID;
	}

    ConcreteSitemap.exitEditMode = function(cID) {
		$.get(CCM_TOOLS_PATH + "/dashboard/sitemap_check_in?cID=" + cID  + "&ccm_token=" + CCM_SECURITY_TOKEN);
    }

	ConcreteSitemap.getMenu = function() {
		return '<div class="ccm-popover-page-menu popover fade" data-search-page-menu="<%=data.cID%>" data-search-menu="<%=data.cID%>">' +
			'<div class="arrow"></div><div class="popover-inner"><ul class="dropdown-menu">' + 
			'<% if (data.isTrash && data.numSubpages) { %>' + 
				'<li><a onclick="ConcreteSitemap.deleteForever(<%=data.cID%>)" href="javascript:void(0)">' + ccmi18n_sitemap.emptyTrash + '</a></li>' + 
			'<% } else if (data.isInTrash) { %>' + 
				'<li><a onclick="ccm_previewInternalTheme(<%=data.cID%>, false, \'' + ccmi18n_sitemap.previewPage + '\')" href="javascript:void(0)">' + ccmi18n_sitemap.previewPage + '</a></li>' +
				'<li class="divider"><\/li>' + 
				'<li><a onclick="ConcreteSitemap.deleteForever(<%=data.cID%>)" href="javascript:void(0)">' + ccmi18n_sitemap.deletePageForever + '</a></li>' +
			'<% } else if (data.cAlias == \'LINK\' || data.cAlias == \'POINTER\') { %>' +
				'<li><a onclick="window.location.href=\'' + CCM_DISPATCHER_FILENAME + '?cID=<%=data.cID%>\'" href="javascript:void(0)">' + ccmi18n_sitemap.visitExternalLink + '</a></li>' +
				'<% if (data.cAlias == \'LINK\' && data.canEditProperties) { %>' +
					'<li><a dialog-width="350" dialog-height="170" dialog-title="' + ccmi18n_sitemap.editExternalLink + '" dialog-modal="false" dialog-append-buttons="true" href="' + CCM_TOOLS_PATH + '/edit_collection_popup?rel=SITEMAP&cID=<%=data.cID%>&ctask=edit_external">' + ccmi18n_sitemap.editExternalLink + '</a></li>' +
				'<% } %>' +
				'<% if (data.canDelete) { %>' +
					'<li><a dialog-width="360" dialog-height="150" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.deleteExternalLink + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup?rel=SITEMAP&cID=<%=data.cID%>&ctask=delete_external">' + ccmi18n_sitemap.deleteExternalLink + '</a></li>' +
				'<% } %>' +
			'<% } else { %>' + 
				'<li><a href="#" onclick="ConcreteSitemap.visit(<%=data.cID%>)">' + ccmi18n_sitemap.visitPage + '</a></li>' +
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

	ConcreteSitemap.refreshCopyOperations = function() {
		ccm_triggerProgressiveOperation(CCM_TOOLS_PATH + '/dashboard/sitemap_copy_all', [],	ccmi18n_sitemap.copyProgressTitle, function() {
			$('.ui-dialog-content').dialog('close');
			window.location.reload();
		});
	}

	ConcreteSitemap.submitDragRequest = function() {
	
		var origCID = $('#origCID').val();
		var destParentID = $('#destParentID').val();
		var destCID = $('#destCID').val();
		var dragMode = $('#dragMode').val();
		var destSibling = $('#destSibling').val();
		var ctask = $("input[name=ctask]:checked").val();
		var copyAll = $("input[name=copyAll]:checked").val();
		var saveOldPagePath = $("input[name=saveOldPagePath]:checked").val();
		var params = {
		
			'origCID': origCID,
			'destCID': destCID,
			'ctask': ctask,
			'ccm_token': CCM_SECURITY_TOKEN,
			'copyAll': copyAll,
			'destSibling': destSibling,
			'dragMode': dragMode,
			'saveOldPagePath': saveOldPagePath
		};


		if (copyAll == 1) {

			var dialogTitle = ccmi18n_sitemap.copyProgressTitle;
			ccm_triggerProgressiveOperation(
				CCM_TOOLS_PATH + '/dashboard/sitemap_copy_all', 
				[{'name': 'origCID', 'value': origCID}, {'name': 'destCID', 'value': destCID}],
				dialogTitle, function() {
					$('.ui-dialog-content').dialog('close');
					ConcreteEvent.publish('SitemapDragRequestComplete', {'task': ctask});
				}
			);

		} else {

			jQuery.fn.dialog.showLoader();

			$.getJSON(CCM_TOOLS_PATH + '/dashboard/sitemap_drag_request', params, function(resp) {
				// parse response
				ccm_parseJSON(resp, function() {
					jQuery.fn.dialog.closeAll();
					jQuery.fn.dialog.hideLoader();
		 			ConcreteAlert.hud(resp.message, 2000);
					ConcreteEvent.publish('SitemapDragRequestComplete', {'task': ctask});
					jQuery.fn.dialog.closeTop();
					jQuery.fn.dialog.closeTop();
				});
			});
		}
	}

	// jQuery Plugin
	$.fn.concreteSitemap = function(options) {
		return $.each($(this), function(i, obj) {
			new ConcreteSitemap($(this), options);
		});
	}

	global.ConcreteSitemap = ConcreteSitemap;

}(this, $, _);