var tr_activeNode = false;
//var tr_doAnim = false; // we initial set it to false, but once we're done loading the initial state we can make it true
if (typeof(tr_doAnim) == 'undefined') {
	var tr_doAnim = false; // we initial set it to false, but once we're done loading the initial state we can make it true
}
var tr_parseSubnodes = true;
var tr_reorderMode = false;
var	tr_moveCopyMode = false;

showPageMenu = function(obj, e) {
	ccm_hideMenus();
	e.stopPropagation();
	/* now, check to see if this menu has been made */
	var bobj = $("#ccm-page-menu" + obj.cID);
	
	if (!bobj.get(0)) {
		
		// create the 1st instance of the menu
		el = document.createElement("DIV");
		el.id = "ccm-page-menu" + obj.cID;
		el.className = "ccm-menu ccm-ui";
		el.style.display = "block";
		el.style.visibility = "hidden";
		document.body.appendChild(el);
		
		bobj = $("#ccm-page-menu" + obj.cID);
		bobj.css("position", "absolute");
		
		/* contents  of menu */
		var html = '<div class="popover"><div class="arrow"></div><div class="inner"><div class="content">';
		html += "<ul>";
		
		if (obj.isTrash) {

			html += '<li><a class="ccm-menu-icon ccm-icon-delete-menu" onclick="ccm_sitemapEmptyTrash(' + obj.instance_id + ',' + obj.cID + ')" href="javascript:void(0)">' + ccmi18n_sitemap.emptyTrash + '<\/a><\/li>';
		
		} else if (obj.inTrash) {

			html += '<li><a class="ccm-menu-icon ccm-icon-search-pages" onclick="ccm_previewInternalTheme(' + obj.cID + ', false, \'' + ccmi18n_sitemap.previewPage + '\')" href="javascript:void(0)">' + ccmi18n_sitemap.previewPage + '<\/a><\/li>';
			html += '<li class=\"ccm-menu-separator\"><\/li>';
			html += '<li><a class="ccm-menu-icon ccm-icon-delete-menu" onclick="ccm_sitemapDeleteForever(' + obj.instance_id + ',' + obj.cID + ')" href="javascript:void(0)">' + ccmi18n_sitemap.deletePageForever + '<\/a><\/li>';
		
		} else if (obj.cAlias == 'LINK' || obj.cAlias == 'POINTER') {
		
			html += '<li><a class="ccm-menu-icon ccm-icon-visit" id="menuVisit' + obj.cID + '" href="javascript:void(0)" onclick="window.location.href=\'' + CCM_DISPATCHER_FILENAME + '?cID=' + obj.cID + '\'">' + ccmi18n_sitemap.visitExternalLink + '<\/a><\/li>';
			if (obj.cAlias == 'LINK' && obj.canEditProperties) {
				html += '<li><a class="ccm-menu-icon ccm-icon-edit-external-link" dialog-width="350" dialog-height="170" dialog-title="' + ccmi18n_sitemap.editExternalLink + '" dialog-modal="false" dialog-append-buttons="true" id="menuLink' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=edit_external">' + ccmi18n_sitemap.editExternalLink + '<\/a><\/li>';
			}

			if (obj.canDelete) { 
				html += '<li><a class="ccm-menu-icon ccm-icon-delete-menu" dialog-append-buttons="true" id="menuDelete' + obj.cID + '" dialog-width="360" dialog-height="150" dialog-modal="false" dialog-append-buttons="true" dialog-title="' + ccmi18n_sitemap.deleteExternalLink + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&display_mode=' + obj.display_mode + '&instance_id=' + obj.instance_id + '&select_mode=' + obj.select_mode + '&ctask=delete_external">' + ccmi18n_sitemap.deleteExternalLink + '<\/a><\/li>';
			}
		
		} else {
		
			html += '<li><a class="ccm-menu-icon ccm-icon-visit" id="menuVisit' + obj.cID + '" href="' + CCM_DISPATCHER_FILENAME + '?cID=' + obj.cID + '">' + ccmi18n_sitemap.visitPage + '<\/a><\/li>';
			if (obj.canCompose) {
				html += '<li><a class="ccm-menu-icon ccm-icon-edit-in-composer-menu" id="menuComposer' + obj.cID + '" href="' + CCM_DISPATCHER_FILENAME + '/dashboard/composer/write/-/edit/' + obj.cID + '">' + ccmi18n_sitemap.editInComposer + '<\/a><\/li>';
			}
			if (obj.canEditProperties || obj.canEditSpeedSettings || obj.canEditPermissions || obj.canEditDesign || obj.canViewVersions || obj.canDelete) { 
				html += '<li class=\"ccm-menu-separator\"><\/li>';
			}
			if (obj.canEditProperties) { 
				html += '<li><a class="ccm-menu-icon ccm-icon-properties-menu" dialog-on-close="ccm_sitemapExitEditMode(' + obj.cID + ')" dialog-width="670" dialog-height="360" dialog-append-buttons="true" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.pagePropertiesTitle + '" id="menuProperties' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=edit_metadata">' + ccmi18n_sitemap.pageProperties + '<\/a><\/li>';
			}
			if (obj.canEditSpeedSettings) { 
				html += '<li><a class="ccm-menu-icon ccm-icon-speed-settings-menu" dialog-on-close="ccm_sitemapExitEditMode(' + obj.cID + ')" dialog-width="550" dialog-height="280" dialog-append-buttons="true" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.speedSettingsTitle + '" id="menuSpeedSettings' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=edit_speed_settings">' + ccmi18n_sitemap.speedSettings + '<\/a><\/li>';
			}
			if (obj.canEditPermissions) { 
				html += '<li><a class="ccm-menu-icon ccm-icon-permissions-menu" dialog-on-close="ccm_sitemapExitEditMode(' + obj.cID + ')" dialog-width="420" dialog-height="630" dialog-append-buttons="true" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.setPagePermissions + '" id="menuPermissions' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=edit_permissions">' + ccmi18n_sitemap.setPagePermissions + '<\/a><\/li>';
			}
			if (obj.canEditDesign) { 
				html += '<li><a class="ccm-menu-icon ccm-icon-design-menu" dialog-on-close="ccm_sitemapExitEditMode(' + obj.cID + ')" dialog-width="610" dialog-append-buttons="true" dialog-height="405" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.pageDesign + '" id="menuDesign' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=set_theme">' + ccmi18n_sitemap.pageDesign + '<\/a><\/li>';
			}
			if (obj.canViewVersions) {
				html += '<li><a class="ccm-menu-icon ccm-icon-versions-menu" dialog-on-close="ccm_sitemapExitEditMode(' + obj.cID + ')" dialog-width="640" dialog-height="340" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.pageVersions + '" id="menuVersions' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/versions.php?rel=SITEMAP&cID=' + obj.cID + '">' + ccmi18n_sitemap.pageVersions + '<\/a><\/li>';
			}
			if (obj.canDelete) { 
				html += '<li><a class="ccm-menu-icon ccm-icon-delete-menu" dialog-on-close="ccm_sitemapExitEditMode(' + obj.cID + ')" dialog-append-buttons="true" id="menuDelete' + obj.cID + '" dialog-width="360" dialog-height="150" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.deletePage + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&display_mode=' + obj.display_mode + '&instance_id=' + obj.instance_id + '&select_mode=' + obj.select_mode + '&ctask=delete">' + ccmi18n_sitemap.deletePage + '<\/a><\/li>';
			}
			if (obj.display_mode == 'explore' || obj.display_mode == 'search') {
				html += '<li class=\"ccm-menu-separator\"><\/li>';
				html += '<li><a class="ccm-menu-icon ccm-icon-move-copy-menu" dialog-width="90%" dialog-height="70%" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.moveCopyPage + '" id="menuMoveCopy' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/sitemap_search_selector?sitemap_select_mode=move_copy_delete&cID=' + obj.cID + '" id="menuMoveCopy' + obj.cID + '">' + ccmi18n_sitemap.moveCopyPage + '<\/a><\/li>';
				if (obj.display_mode == 'explore') {
					html += '<li><a class="ccm-menu-icon ccm-icon-move-up" id="menuSendToStop' + obj.cID + '" href="' + CCM_DISPATCHER_FILENAME + '/dashboard/sitemap/explore?cNodeID=' + obj.cID + '&task=send_to_top">' + ccmi18n_sitemap.sendToTop + '<\/a><\/li>';
					html += '<li><a class="ccm-menu-icon ccm-icon-move-down" id="menuSendToBottom' + obj.cID + '" href="' + CCM_DISPATCHER_FILENAME + '/dashboard/sitemap/explore?cNodeID=' + obj.cID + '&task=send_to_bottom">' + ccmi18n_sitemap.sendToBottom + '<\/a><\/li>';
				}
			}
			if (obj.cNumChildren > 0) {
				html += '<li class=\"ccm-menu-separator\"><\/li>';

				//var searchURL = (obj.display_mode == 'explore') ? CCM_REL + CCM_DISPATCHER_FILENAME + '/dashboard/sitemap/search/?selectedSearchField[]=parent&cParentAll=1&cParentIDSearchField=' + obj.cID : 'javascript:searchSubPages(' + obj.cID + ')';
				var searchURL = CCM_DISPATCHER_FILENAME + '/dashboard/sitemap/search/?selectedSearchField[]=parent&cParentAll=1&cParentIDSearchField=' + obj.cID;
				
				if (obj.display_mode == 'full' || obj.display_mode == '' || obj.display_mode == 'explore') {
					html += '<li><a class="ccm-menu-icon ccm-icon-search-pages" id="menuSearch' + obj.cID + '" href="' + searchURL + '">' + ccmi18n_sitemap.searchPages + '<\/a><\/li>';
				}
				if (obj.display_mode != 'explore') {
					html += '<li><a class="ccm-menu-icon ccm-icon-flat-view" id="menuExplore' + obj.cID + '" href="' + CCM_DISPATCHER_FILENAME + '/dashboard/sitemap/explore/-/' + obj.cID + '">' + ccmi18n_sitemap.explorePages + '<\/a><\/li>';
				}
				
			}
			if (obj.canAddSubpages || obj.canAddExternalLinks) { 
				html += '<li class=\"ccm-menu-separator\"><\/li>';
			}
			if (obj.canAddSubpages) {
				html += '<li><a class="ccm-menu-icon ccm-icon-add-page-menu" dialog-append-buttons="true" dialog-width="645" dialog-modal="false" dialog-height="345" dialog-title="' + ccmi18n_sitemap.addPage + '" id="menuSubPage' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&mode=' + obj.display_mode + '&cID=' + obj.cID + '&ctask=add">' + ccmi18n_sitemap.addPage + '<\/a><\/li>';
			}
			if (obj.display_mode != 'search' && obj.canAddExternalLinks) {
				html += '<li><a class="ccm-menu-icon ccm-icon-add-external-link-menu" dialog-width="350" dialog-modal="false" dialog-height="170" dialog-title="' + ccmi18n_sitemap.addExternalLink + '" dialog-modal="false" dialog-append-buttons="true" id="menuLink' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=add_external">' + ccmi18n_sitemap.addExternalLink + '<\/a><\/li>';
			}
		} 
		
		html += '<\/ul>';

		html += '</div></div></div>';

		bobj.append(html);

		$(bobj).find('a').bind('click.hide-menu', function(e) {
			ccm_hideMenus();
		});

		$("#menuProperties" + obj.cID).dialog();
		$("#menuSpeedSettings" + obj.cID).dialog();
		$("#menuSubPage" + obj.cID).dialog();
		$("#menuDesign" + obj.cID).dialog();
		$("#menuLink" + obj.cID).dialog();
		$("#menuVersions" + obj.cID).dialog();
		$("#menuPermissions" + obj.cID).dialog();
		$("#menuMoveCopy" + obj.cID).dialog();
		$("#menuDelete" + obj.cID).dialog();

	} else {
		bobj = $("#ccm-page-menu" + obj.cID);
	}
	
	ccm_fadeInMenu(bobj, e);
	
}

hideBranch = function(nodeID) {
	// hides branch and its drop zone
	$("#tree-node" + nodeID).hide();
	$("#tree-dz" + nodeID).hide();
}

cancelReorder = function() {
	if (tr_reorderMode) {
		//$('img.handle').removeClass('moveable');
		tr_reorderMode = false;
		$('li.tree-node[draggable=true]').draggable('destroy');
		if (!tr_moveCopyMode) {
			hideSitemapMessage();
		}
	}
}

ccm_sitemapExitEditMode = function(cID) {
	$.get(CCM_TOOLS_PATH + "/dashboard/sitemap_check_in?cID=" + cID  + "&ccm_token=" + CCM_SECURITY_TOKEN);
}

searchSubPages = function(cID) {
	$("#ccm-tree-search-trigger" + cID).hide();
	if (ccm_animEffects) {
		$("#ccm-tree-search" + cID).fadeIn(200, function() {
			$("#ccm-tree-search" + cID + " input").get(0).focus();
		});
	} else {
		$("#ccm-tree-search" + cID).show();
		$("#ccm-tree-search" + cID + " input").get(0).focus();
	}
}

activateReorder = function() {
	tr_reorderMode = true;
	
	/*
	
	$('div.tree-label').droppable({
		accept: '.tree-node',
		hoverClass: 'on-drop',
		drop: function(e, ui) {
			var orig = ui.draggable;
			var destCID = $(this).attr('id').substring(10);
			var origCID = $(orig).attr('id').substring(9);
			if(destCID==origCID) return false;
			var dialog_url=CCM_TOOLS_PATH + '/dashboard/sitemap_drag_request.php?origCID=' + origCID + '&destCID=' + destCID;
			//prevent window from opening twice
			if(SITEMAP_LAST_DIALOGUE_URL==dialog_url) return false;
			else SITEMAP_LAST_DIALOGUE_URL=dialog_url;
			$.fn.dialog.open({
				title: ccmi18n_sitemap.moveCopyPage,
				href: dialog_url,
				width: 350,
				modal: false,
				height: 350, 
				onClose: function() {
					showBranch(origCID);
				}
			});
			hideBranch(origCID);
		}
	}); 
	*/
	
	$('li.tree-node[draggable=true]').draggable({
		handle: 'img.handle',
		opacity: 0.5,
		revert: false,
		helper: 'clone',
		start: function() {
			$(document.body).css('overflowX', 'hidden');
		},
		stop: function() {
			$(document.body).css('overflowX', 'auto');
		}
	});
	fixResortingDroppables();
	//showSitemapMessage(ccmi18n_sitemap.reorderPageMessage);
}

deleteBranchFade = function(nodeID) {
	// hides branch and its drop zone
	if (ccm_animEffects) {
		$("#tree-node" + nodeID).fadeOut(300, function() {
			$("#tree-node" + nodeID).remove();
		});
		$("#tree-dz" + nodeID).fadeOut(300, function() {
			$("#tree-dz" + nodeID).remove();
		});
	} else {
		deleteBranchDirect(nodeID);
	}	
}

deleteBranchDirect = function(nodeID) {
	// hides branch and its drop zone
	$("#tree-node" + nodeID).remove();
	$("#tree-dz" + nodeID).remove();
}

showBranch = function(nodeID) {
	var orig = $("#tree-node" + nodeID);
	$("#tree-node" + nodeID).show();
	$("#tree-dz" + nodeID).show();
}

rescanDisplayOrder = function(nodeID) {
	setLoading(nodeID);
	var queryString = "?foo=1";
	var nodes = $('#tree-root' + nodeID).children('li.tree-node');
	for (i = 0; i < nodes.length; i++) {
		if( $(nodes[i]).hasClass('ui-draggable-dragging') ) continue;
		queryString += "&cID[]=" + $(nodes[i]).attr('id').substring(9);
	}
	$.getJSON(CCM_TOOLS_PATH + '/dashboard/sitemap_update.php', queryString, function(resp) {
		ccm_parseJSON(resp, function() {});
		removeLoading(nodeID);	
	});
}

var SITEMAP_LAST_DIALOGUE_URL='';
var ccm_sitemap_html = '';

parseSitemapResponse = function(instanceID, display_mode, select_mode, nodeID, resp) { 
	var container = $("ul[tree-root-node-id=" + nodeID + "][sitemap-instance-id=" + instanceID + "]");
	container.html(resp);
	container.slideDown(150, 'easeOutExpo');
}

selectMoveCopyTarget = function(instanceID, display_mode, select_mode, destCID, origCID) {
	if (!origCID) {
		var origCID = CCM_CID;
	}
	var dialog_title = ccmi18n_sitemap.moveCopyPage;
	var dialog_url = CCM_TOOLS_PATH + '/dashboard/sitemap_drag_request.php?instance_id=' + instanceID + '&display_mode=' + display_mode + '&select_mode=' + select_mode + '&origCID=' + origCID + '&destCID=' + destCID;
	var dialog_height = 350;
	var dialog_width = 350;
	
	try {
		if (CCM_NODE_ACTION == '<none>') {
			if (CCM_TARGET_ID != '') {
				$('#'+CCM_TARGET_ID).val(destCID);
			}
			$.fn.dialog.closeTop();
			return;
		}
	
		if (CCM_NODE_ACTION != '')
			dialog_url = CCM_NODE_ACTION+'?destCID='+destCID;
		if (CCM_DIALOG_TITLE != '')
			dialog_title = CCM_DIALOG_TITLE;
		if (CCM_DIALOG_HEIGHT != '')
			dialog_height = CCM_DIALOG_HEIGHT;
		if (CCM_DIALOG_WIDTH != '')
			dialog_width = CCM_DIALOG_WIDTH;
	} catch(e) {
	
	}
	
	$.fn.dialog.open({
		title: dialog_title,
		href: dialog_url,
		width: dialog_width,
		appendButtons: true,
		modal: false,
		height: dialog_height,
		onClose: function() {
			//$("#tree").fadeIn(200);
			if (typeof(CCM_TARGET_ID) != "undefined" && CCM_TARGET_ID != '') {
				$('#'+CCM_TARGET_ID).val(destCID);
			}
			if (tr_moveCopyMode == true) {
				deactivateMoveCopy();
			}
		}

	});
}

selectLabel = function(e, node) {
	var cNumChildren = node.attr('tree-node-children');
	if (node.attr('sitemap-select-mode') == "move_copy_delete" || tr_moveCopyMode == true) {
		var destCID = node.attr('id').substring(10);
		var origCID = node.attr('selected-page-id');
		selectMoveCopyTarget(node.attr('sitemap-instance-id'), node.attr('sitemap-display-mode'), node.attr('sitemap-select-mode'), destCID, origCID);
	} else if (node.attr('sitemap-select-mode') == 'select_page') {
		var callback = node.parents('[sitemap-wrapper=1]').attr('sitemap-select-callback');
		if (callback == null || callback == '' || typeof(callback) == 'undefined') {
			callback = 'ccm_selectSitemapNode';
		}
		eval(callback + '(node.attr(\'id\').substring(10), unescape(node.attr(\'tree-node-title\')));');
		jQuery.fn.dialog.closeTop();
	} else {
		node.addClass('tree-label-selected');
		if (tr_activeNode != false) {
			if (tr_activeNode.attr('id') != node.attr('id')) {
				tr_activeNode.removeClass('tree-label-selected');
			}
		}
		params = {
			'cID': node.attr('id').substring(10), 
			'display_mode': node.attr('sitemap-display-mode'), 
			'isTrash': node.attr('tree-node-istrash'), 
			'inTrash': node.attr('tree-node-intrash'), 
			'select_mode': node.attr('sitemap-select-mode'), 
			'instance_id': node.attr('sitemap-instance-id'), 
			'canCompose': node.attr('tree-node-cancompose'), 
			'canEditProperties': node.attr('tree-node-can-edit-properties'), 
			'canEditSpeedSettings': node.attr('tree-node-can-edit-speed-settings'), 
			'canEditPermissions': node.attr('tree-node-can-edit-permissions'), 
			'canEditDesign': node.attr('tree-node-can-edit-design'), 
			'canViewVersions': node.attr('tree-node-can-view-versions'), 
			'canDelete': node.attr('tree-node-can-delete'), 
			'canAddSubpages': node.attr('tree-node-can-add-subpages'), 
			'canAddExternalLinks': node.attr('tree-node-can-add-external-links'), 
			'cNumChildren': node.attr('tree-node-children'), 
			'cAlias': node.attr('tree-node-alias')
		};
		
		showPageMenu(params, e);
		tr_activeNode = node;
	}
}

ccmSitemapHighlightPageLabel = function(cID, name) {
	var sp = $("#tree-label" + cID + " > span");

	if (sp.length == 0) {
		var sp = $("tr.ccm-list-record[cID=" + cID + "]");
		if (sp.length > 0) {
			$("#ccm-page-advanced-search").submit();
			
		}
	} else {
		if (name != null) {
			sp.html(name);
		}
	}
	
	sp.show('highlight');

}

activateLabels = function(instance_id, display_mode, select_mode) {
	var smwrapper = $("ul[sitemap-instance-id=" + instance_id + "]");
	smwrapper.find('div.tree-label span').unbind();
	smwrapper.find('div.tree-label span').click(function(e) {
		selectLabel(e, $(this).parent())
	}); 
	
	// now we make sure that all the items that are open are registered as open
	//if ($(this).parent().attr('sitemap-display-mode') != 'explore') {
	smwrapper.find("ul[tree-root-state=closed]").each(function() {
		var container = $(this);
		var nodeID = $(this).attr('tree-root-node-id');
		if ($(this).find('li').length > 0) {
			container.attr('tree-root-state', 'open');
			$("#tree-collapse" + nodeID).attr('src', CCM_IMAGE_PATH + '/dashboard/minus.jpg');
		}
	});

	//}
	
	if (select_mode == 'select_page' || select_mode == 'move_copy_delete') {
		smwrapper.find("li.ccm-sitemap-explore-paging a").each(function() {
			$(this).click(function() {
				var treeRootNode = $(this).parentsUntil('ul').parent().parentsUntil('ul').parent().attr('tree-root-node-id');
				jQuery.fn.dialog.showLoader();
				$.get($(this).attr('href'), function(r) {
					parseSitemapResponse(instance_id, display_mode, select_mode, treeRootNode, r);
					activateLabels(instance_id, display_mode, select_mode);
					jQuery.fn.dialog.hideLoader();
				});			
				
				return false;
			});
		});
	}
	if ((display_mode == 'explore' || display_mode == 'full') && (!select_mode)) {
		smwrapper.find('img.handle').addClass('moveable');
	}
	
	if (display_mode == 'full' && (!select_mode)) {
	
		//drop onto a page
		smwrapper.find('div.tree-label').droppable({
			accept: '.tree-node',
			hoverClass: 'on-drop',
			drop: function(e, ui) {
				var orig = ui.draggable;
				var destCID = $(this).attr('id').substring(10);
				var origCID = $(orig).attr('id').substring(9);
				if(destCID==origCID) return false;
				var dialog_url=CCM_TOOLS_PATH + '/dashboard/sitemap_drag_request.php?instance_id=' + instance_id + '&origCID=' + origCID + '&destCID=' + destCID;
				//prevent window from opening twice
				if(SITEMAP_LAST_DIALOGUE_URL==dialog_url) return false;
				else SITEMAP_LAST_DIALOGUE_URL=dialog_url;
				$.fn.dialog.open({
					title: ccmi18n_sitemap.moveCopyPage,
					href: dialog_url,
					width: 350,
					modal: false,
					height: 350, 
					appendButtons: true,
					onClose: function() {
						showBranch(origCID);
					}
				});
				//hideBranch(origCID);
			}
		}); 
		
		//addResortDroppable(nodeID);		

		smwrapper.find('li.tree-node[draggable=true]').draggable({
			handle: 'img.handle',
			opacity: 0.5,
			revert: false,
			helper: 'clone',
			start: function() {
				$(document.body).css('overflowX', 'hidden');
			},
			stop: function() {
				$(document.body).css('overflowX', 'auto');
			}
		});
	}
}

moveCopyAliasNode = function(reloadPage) {
	
	var origCID = $('#origCID').val();
	var destParentID = $('#destParentID').val();
	var destCID = $('#destCID').val();
	var ctask = $("input[name=ctask]:checked").val();
	var instance_id = $("input[name=instance_id]").val();
	var display_mode = $("input[name=display_mode]").val();
	var select_mode = $("input[name=select_mode]").val();
	var copyAll = $("input[name=copyAll]:checked").val();
	var saveOldPagePath = $("input[name=saveOldPagePath]:checked").val();
	// DO THE DEED

	params = {
	
		'origCID': origCID,
		'destCID': destCID,
		'ctask': ctask,
		'ccm_token': CCM_SECURITY_TOKEN,
		'copyAll': copyAll,
		'saveOldPagePath': saveOldPagePath
	};

	jQuery.fn.dialog.showLoader();

	$.getJSON(CCM_TOOLS_PATH + '/dashboard/sitemap_drag_request.php', params, function(resp) {
		// parse response
		ccm_parseJSON(resp, function() {
			jQuery.fn.dialog.closeAll();
			jQuery.fn.dialog.hideLoader();
 			ccmAlert.hud(resp.message, 2000);
			if (reloadPage == true) {
				if (typeof(CCM_LAUNCHER_SITEMAP) != 'undefined') {
					if (CCM_LAUNCHER_SITEMAP == 'explore') {
						// we are in the dashboard and we need to actually go to the explore node
						window.location.href = CCM_DISPATCHER_FILENAME + "/dashboard/sitemap/explore/-/" + destCID;
						return false;
					}
					if (CCM_LAUNCHER_SITEMAP == 'search') {
						ccm_deactivateSearchResults(CCM_SEARCH_INSTANCE_ID);
						$("#ccm-" + CCM_SEARCH_INSTANCE_ID + "-advanced-search").ajaxSubmit(function(resp) {
							ccm_parseAdvancedSearchResponse(resp, CCM_SEARCH_INSTANCE_ID);
						});
					}
				} else {
					setTimeout(function() {
						window.location.href = CCM_DISPATCHER_FILENAME + "?cID=" + resp.cID;
					}, 2000);
					return false;
				}
			}
			
			switch(ctask) {
				case "COPY":
				case "ALIAS":
					// since we're copying we show the original again
					showBranch(origCID);
					break;
				case "MOVE":
					deleteBranchDirect(origCID);
					break;
			}
			
			openSub(instance_id, destParentID, display_mode, select_mode, function() {openSub(instance_id, destCID, display_mode, select_mode)});
			jQuery.fn.dialog.closeTop();
			jQuery.fn.dialog.closeTop();
		});
	});	
}

/*
searchSitemapNode = function(cID) {
	var q = $('form#ccm-tree-search' + cID + ' input').val();
	openSubSearch(cID, q);
	return false;
}
*/

toggleSub = function(instanceID, nodeID, display_mode, select_mode) {
	ccm_hideMenus();
	var container = $("ul[tree-root-node-id=" + nodeID + "][sitemap-instance-id=" + instanceID + "]");
	if (container.attr('tree-root-state') == 'closed') {
		openSub(instanceID, nodeID, display_mode, select_mode);
	} else {
		closeSub(instanceID, nodeID, display_mode, select_mode);
	}
}

ccm_sitemapEmptyTrash = function(instance_id, nodeID) {
	setLoading(nodeID);
	$.getJSON(CCM_TOOLS_PATH + '/dashboard/sitemap_empty_trash.php', function(resp) {
		// parse response
		ccm_parseJSON(resp, function() {
			closeSub(instance_id, nodeID, 'full', '');
			removeLoading(nodeID);
			var container = $("ul[tree-root-node-id=" + nodeID + "][sitemap-instance-id=" + instance_id + "]").parent();
			container.find('img.tree-plus').remove();
			container.find('span.ccm-sitemap-num-subpages').remove();
			ccmAlert.hud(resp.message, 2000);
		});
	});
}

ccm_sitemapDeleteForever = function(instance_id, nodeID) {
	setLoading(nodeID);
	params = {'cID': nodeID};
	$.getJSON(CCM_TOOLS_PATH + '/dashboard/sitemap_delete_forever.php', params, function(resp) {
		// parse response
		ccm_parseJSON(resp, function() {
			deleteBranchFade(nodeID);
			ccmAlert.hud(resp.message, 2000);
		});
	});
}

setLoading = function(nodeID) {
	var listNode = $("#tree-node" + nodeID);
	listNode.removeClass('tree-node-' + listNode.attr('tree-node-type'));
	listNode.addClass('tree-node-loading');
}

removeLoading = function(nodeID) {
	var listNode = $("#tree-node" + nodeID);
	listNode.removeClass('tree-node-loading');
	listNode.addClass('tree-node-' + listNode.attr('tree-node-type'));
}

openSub = function(instanceID, nodeID, display_mode, select_mode, onComplete) {
	setLoading(nodeID);
	var container = $("#tree-root" + nodeID);
	cancelReorder();
	ccm_sitemap_html = '';
	$.get(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php?instance_id=" + instanceID + "&node=" + nodeID + "&display_mode=" + display_mode + "&select_mode=" + select_mode + "&selectedPageID=" + container.attr('selected-page-id'), function(resp) {
		parseSitemapResponse(instanceID, 'full', select_mode, nodeID, resp);
		activateLabels(instanceID, 'full', select_mode);
		if (select_mode != 'move_copy_delete' && select_mode != 'select_page') {
			activateReorder();
		}

		setTimeout(function() {
			removeLoading(nodeID);
			if (onComplete != null) {
				onComplete();
			}			
		}, 200);
	});	
}

/*
openSubSearch = function(nodeID, query, onComplete) {
	setLoading(nodeID);
	var container = $("#tree-root" + nodeID);
	ccm_sitemap_html = '';
	container.html('');
	container.addClass('ccm-sitemap-search-results');
	cancelReorder();
	$.get(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php?node=" + nodeID, {'keywords': query, 'mode': 'full'}, function(resp) {
		parseSitemapResponse('full', nodeID, resp);	
		activateLabels('full');
		setTimeout(function() {
			removeLoading(nodeID);
			if (onComplete != null) {
				onComplete();
			}			
		}, 200);
	});	
}
*/

closeSub = function(instanceID, nodeID, display_mode, select_mode) {
	var container = $("ul[tree-root-node-id=" + nodeID + "][sitemap-instance-id=" + instanceID + "]");	
	if (tr_doAnim) {
		setLoading(nodeID);
		container.slideUp(150, 'easeOutExpo', function() {
			removeLoading(nodeID);
			container.attr('tree-root-state', 'closed');
			container.html('');
			$("#ccm-tree-search" + nodeID).hide();
			$("#tree-collapse" + nodeID).attr('src', CCM_IMAGE_PATH + '/dashboard/plus.jpg');
			container.removeClass('ccm-sitemap-search-results');
		});
	} else {	
		container.hide();
		container.attr('tree-root-state', 'closed');
		container.removeClass('ccm-sitemap-search-results');
		$("#ccm-tree-search" + nodeID).hide();
		$("#tree-collapse" + nodeID).attr('src', CCM_IMAGE_PATH + '/dashboard/plus.jpg');
	}

	if (tr_moveCopyMode == true) {
		$("#ccm-tree-search-trigger" + cID).show();
	}
	
	$.get(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php?instance_id=" + instanceID + "&select_mode=" + select_mode + "&display_mode=" + display_mode + "&node=" + nodeID +'&display_mode=full&ctask=close-node');
}

toggleMove = function() {
	if ($("#copyThisPage").get(0)) {
		$("#copyThisPage").get(0).disabled = true;
		$("#copyChildren").get(0).disabled = true;
		$("#saveOldPagePath").attr('disabled', false);
	}
}

toggleAlias = function() {
	if ($("#copyThisPage").get(0)) {
		$("#copyThisPage").get(0).disabled = true;
		$("#copyChildren").get(0).disabled = true;
		$("#saveOldPagePath").attr('checked', false);
		$("#saveOldPagePath").attr('disabled', 'disabled');
	}
}

toggleCopy = function() {
	if ($("#copyThisPage").get(0)) {
		$("#copyThisPage").get(0).disabled = false;
		$("#copyThisPage").get(0).checked = true;
		$("#copyChildren").get(0).disabled = false;
		$("#saveOldPagePath").attr('checked', false);
		$("#saveOldPagePath").attr('disabled', 'disabled');
	}
}

showSitemapMessage = function(msg) {
	$("#ccm-sitemap-message").addClass('message');
	$("#ccm-sitemap-message").html(msg);
	$("#ccm-sitemap-message").fadeIn(200);
}

hideSitemapMessage = function() {
	$("#ccm-sitemap-message").hide();
}

function fixResortingDroppables(){
	if (tr_reorderMode == false) {
		return false;
	}
	
	var DZs=$('.dropzone'); 
	for(var i=0;i<DZs.length;i++){ 
		var nodeID = $(DZs[i]).attr('id').substr(7); 
		if( nodeID.indexOf('-sub') > 0) {
			nodeID=nodeID.substr(0,(nodeID.length-4));
		}
		addResortDroppable(nodeID);
	}
}
//drop onto a dropzone - used only for reordering pages 
function addResortDroppable(nodeID){
		//ignore levels with only one branch
		if( $('.tree-branch' + nodeID).length<=1 ) return;
		//add reordering droppable targets
		$('div.tree-dz' + nodeID).droppable({
			accept: '.tree-branch' + nodeID,
			activeClass: 'dropzone-ready',
			hoverClass: 'dropzone-active', 
			drop: function(e, ui) {
				var node = ui.draggable;
				$(node).insertAfter(this);
				var dzNode = $(node).attr('id').substring(9);
				$("#tree-dz" + dzNode).insertAfter($(node));
				rescanDisplayOrder($(this).attr('tree-parent'));			
			}
		});
}

ccmSitemapExploreNode = function(instance_id, display_mode, select_mode, cID, selectedPageID) {
	jQuery.fn.dialog.showLoader();
	$.get(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php", {'instance_id': instance_id, 'display_mode': display_mode, 'select_mode' : select_mode, 'node': cID, 'selectedPageID': selectedPageID}, function(resp) {  
		parseSitemapResponse(instance_id, 'explore', select_mode, 0, resp);
		activateLabels(instance_id, 'explore', select_mode);
		jQuery.fn.dialog.hideLoader();
		ccm_sitemap_html = '';
	});
}

ccmSitemapLoad = function(instance_id, display_mode, select_mode, node, selectedPageID, onComplete) {
	if (select_mode == 'move_copy_delete' || select_mode == 'select_page') {
		ccmSitemapExploreNode(instance_id, display_mode, select_mode, node, selectedPageID);
	} else if (display_mode == 'full') {

		activateLabels(instance_id, display_mode, select_mode);
		if (select_mode != 'move_copy_delete' && select_mode != 'select_page') {
			activateReorder();
		}
		tr_doAnim = true;
		tr_parseSubnodes = false;
		ccm_sitemap_html = '';

	} else {
		if (select_mode != 'move_copy_delete' && select_mode != 'select_page') {
			$("ul[sitemap-instance-id=" + instance_id + "]").sortable({
				cursor: 'move',
				items: 'li[draggable=true]',
				opacity: 0.5,
				stop: function(sor) {
					var ss = $("ul[sitemap-instance-id=" + instance_id + "]").sortable('toArray');
					var queryString = '';
					for (i = 0; i < ss.length; i++) {
						if (ss[i] != '') {
							queryString += '&cID[]=' + ss[i].substring(9);
						}
					}

					$.getJSON(CCM_TOOLS_PATH + '/dashboard/sitemap_update.php', queryString, function(resp) {
						ccm_parseJSON(resp, function() {});
					});
				}
			});
		}
		activateLabels(instance_id, display_mode, select_mode);
	}
	
	if (onComplete) {
		onComplete();	
	}
}

ccm_sitemapSetupSearch = function(instance_id) {
	ccm_setupAdvancedSearch(instance_id); 
	ccm_sitemapSetupSearchPages(instance_id);
	ccm_searchActivatePostFunction[instance_id] = function() {
		ccm_sitemapSetupSearchPages(instance_id);
		ccm_sitemapSearchSetupCheckboxes(instance_id);	
	}
	ccm_sitemapSearchSetupCheckboxes(instance_id);	
}

ccm_sitemapSearchSetupCheckboxes = function(instance_id) {
	$("#ccm-" + instance_id + "-list-cb-all").click(function(e) {
		e.stopPropagation();
		if ($(this).prop('checked') == true) {
			$('.ccm-list-record td.ccm-' + instance_id + '-list-cb input[type=checkbox]').attr('checked', true);
			$("#ccm-" + instance_id + "-list-multiple-operations").attr('disabled', false);
		} else {
			$('.ccm-list-record td.ccm-' + instance_id + '-list-cb input[type=checkbox]').attr('checked', false);
			$("#ccm-" + instance_id + "-list-multiple-operations").attr('disabled', true);
		}
	});
	$("td.ccm-" + instance_id + "-list-cb input[type=checkbox]").click(function(e) {
		e.stopPropagation();
		if ($("td.ccm-" + instance_id + "-list-cb input[type=checkbox]:checked").length > 0) {
			$("#ccm-" + instance_id + "-list-multiple-operations").attr('disabled', false);
		} else {
			$("#ccm-" + instance_id + "-list-multiple-operations").attr('disabled', true);
		}
	});
	
	// if we're not in the dashboard, add to the multiple operations select menu

	$("#ccm-" + instance_id + "-list-multiple-operations").change(function() {
		var action = $(this).val();
		cIDstring = '';
		$("td.ccm-" + instance_id + "-list-cb input[type=checkbox]:checked").each(function() {
			cIDstring=cIDstring+'&cID[]='+$(this).val();
		});
		switch(action) {
			case "delete":
				jQuery.fn.dialog.open({
					width: 500,
					height: 400,
					modal: false,
					appendButtons: true,
					href: CCM_TOOLS_PATH + '/pages/delete?' + cIDstring + '&searchInstance=' + instance_id,
					title: ccmi18n_sitemap.deletePages				
				});
				break;
			case "design":
				jQuery.fn.dialog.open({
					width: 610,
					height: 405,
					modal: false,
					appendButtons: true,
					href: CCM_TOOLS_PATH + '/pages/design?' + cIDstring + '&searchInstance=' + instance_id,
					title: ccmi18n_sitemap.pageDesign				
				});
				break;
			case 'move_copy':
				jQuery.fn.dialog.open({
					width: 640,
					height: 340,
					modal: false,
					href: CCM_TOOLS_PATH + '/sitemap_overlay?instance_id=' + instance_id + '&select_mode=move_copy_delete&' + cIDstring,
					title: ccmi18n_sitemap.moveCopyPage				
				});
				break;
			case 'speed_settings':
				jQuery.fn.dialog.open({
					width: 610,
					height: 340,
					modal: false,
					appendButtons: true,
					href: CCM_TOOLS_PATH + '/pages/speed_settings?' + cIDstring,
					title: ccmi18n_sitemap.speedSettingsTitle				
				});
				break;
			case 'permissions':
				jQuery.fn.dialog.open({
					width: 430,
					height: 630,
					modal: false,
					appendButtons: true,
					href: CCM_TOOLS_PATH + '/pages/permissions?' + cIDstring,
					title: ccmi18n_sitemap.pagePermissionsTitle				
				});
				break;
			case "properties": 
				jQuery.fn.dialog.open({
					width: 630,
					height: 450,
					modal: false,
					href: CCM_TOOLS_PATH + '/pages/bulk_metadata_update?' + cIDstring,
					title: ccmi18n_sitemap.pagePropertiesTitle				
				});
				break;				
		}
		
		$(this).get(0).selectedIndex = 0;
	});
}

ccm_sitemapSetupSearchPages = function(instance_id) {
	$('#ccm-' + instance_id + '-list tr').click(function(e){
		var node = $(this);
		if (node.hasClass('ccm-results-list-header')) {
			return false;
		}
		
		if (node.attr('sitemap-select-mode') == 'select_page') {
			var callback = node.attr('sitemap-select-callback');
			if (callback == null || callback == '' || typeof(callback) == 'undefined') {
				callback = 'ccm_selectSitemapNode';
			}
			eval(callback + '(node.attr(\'cID\'), unescape(node.attr(\'cName\')));');
			jQuery.fn.dialog.closeTop();
		} else if (node.attr('sitemap-select-mode') == 'move_copy_delete') {
			var destCID = node.attr('cID');
			var origCID = node.attr('selected-page-id');
			selectMoveCopyTarget(node.attr('sitemap-instance-id'), node.attr('sitemap-display-mode'), node.attr('sitemap-select-mode'), destCID, origCID);
		} else {
			params = {
				'cID': node.attr('cID'), 
				'select_mode': node.attr('sitemap-select-mode'), 
				'display_mode': node.attr('sitemap-display-mode'), 
				'instance_id': node.attr('sitemap-instance-id'),  
				'isTrash': node.attr('tree-node-istrash'), 
				'inTrash': node.attr('tree-node-intrash'), 
				'canCompose': node.attr('tree-node-cancompose'), 
				'canEditProperties': node.attr('tree-node-can-edit-properties'), 
				'canEditSpeedSettings': node.attr('tree-node-can-edit-speed-settings'), 
				'canEditPermissions': node.attr('tree-node-can-edit-permissions'), 
				'canEditDesign': node.attr('tree-node-can-edit-design'), 
				'canViewVersions': node.attr('tree-node-can-view-versions'), 
				'canDelete': node.attr('tree-node-can-delete'), 
				'canAddSubpages': node.attr('tree-node-can-add-subpages'), 
				'canAddExternalLinks': node.attr('tree-node-can-add-external-links'), 
				'cNumChildren': node.attr('cNumChildren'), 
				'cAlias': node.attr('cAlias')
			};		
			showPageMenu(params, e);
		}
	});

}

ccm_sitemapSelectDisplayMode = function(instance_id, display_mode, select_mode, selectedPageID) {
	// finds the selector for the instance of the sitemap and reloads it to be this mode
	
	var ul = $("ul[sitemap-instance-id=" + instance_id + "]");
	ul.html('');
	ul.attr('sitemap-display-mode', display_mode);
	ul.attr('sitemap-select-mode', select_mode);
	ul.attr('sitemap-display-mode', display_mode);
	if (display_mode == 'explore') {
		var node =1;
	} else {
		var node = 0;
	}
	ccmSitemapLoad(instance_id, display_mode, select_mode, node, selectedPageID, function() {
		if (display_mode == 'explore') {
			$("div[sitemap-wrapper=1][sitemap-instance-id=" + instance_id + "]").addClass("ccm-sitemap-explore");
		} else {
			$("div[sitemap-wrapper=1][sitemap-instance-id=" + instance_id + "]").removeClass("ccm-sitemap-explore");
		}
	});
	
	// now we save the preference	
	$.get(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php?task=save_sitemap_display_mode&display_mode=" + display_mode);
}

ccm_sitemapDeletePages = function(searchInstance) {
	$("#ccm-" + searchInstance + "-delete-form").ajaxSubmit(function(resp) {
		ccm_parseJSON(resp, function() {	
			jQuery.fn.dialog.closeTop();
			ccm_deactivateSearchResults(searchInstance);
			$("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
				ccm_parseAdvancedSearchResponse(resp, searchInstance);
			});
		});
	});
}

ccm_sitemapUpdateDesign = function(searchInstance) {
	$("#ccm-" + searchInstance + "-design-form").ajaxSubmit(function(resp) {
		ccm_parseJSON(resp, function() {	
			jQuery.fn.dialog.closeTop();
			ccm_deactivateSearchResults(searchInstance);
			$("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
				ccm_parseAdvancedSearchResponse(resp, searchInstance);
			});
		});
	});
}

$(function() {
	/*
	$(document).ajaxError(function(event, request, settings) {
		ccmAlert.notice(ccmi18n_sitemap.loadErrorTitle, request.responseText);
	});
	*/
	
	$(document).click(function() {
		ccm_hideMenus();
		$("div.tree-label").removeClass('tree-label-selected');
	});

	$("#ccm-show-all-pages-cb").click(function() {
		var showSystemPages = $(this).get(0).checked == true ? 1 : 0;
		$.get(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php?show_system=" + showSystemPages, function(resp) {
			location.reload();
		});
	});
	

});
