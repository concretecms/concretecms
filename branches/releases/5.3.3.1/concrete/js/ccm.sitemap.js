var tr_activeNode = false;
var tr_doAnim = false; // we initial set it to false, but once we're done loading the initial state we can make it true
var tr_parseSubnodes = true;
var tr_maxSearchResults = 50;
var tr_reorderMode = false;
var	tr_moveCopyMode = false;

if (CCM_SITEMAP_MODE == false) {
	var CCM_SITEMAP_MODE = 'full';
}

showPageMenu = function(obj, e) {
	ccm_hideMenus();
	e.stopPropagation();
	
	/* now, check to see if this menu has been made */
	var bobj = $("#ccm-page-menu" + obj.cID);
	
	if (!bobj.get(0)) {
		
		// create the 1st instance of the menu
		el = document.createElement("DIV");
		el.id = "ccm-page-menu" + obj.cID;
		el.className = "ccm-menu";
		el.style.display = "none";
		document.body.appendChild(el);
		
		bobj = $("#ccm-page-menu" + obj.cID);
		bobj.css("position", "absolute");
		
		/* contents  of menu */
		var html = '<div class="ccm-menu-tl"><div class="ccm-menu-tr"><div class="ccm-menu-t"></div></div></div>';
		html += '<div class="ccm-menu-l"><div class="ccm-menu-r">';
		html += "<ul>";
		
		if (obj.cAlias == 'LINK' || obj.cAlias == 'POINTER') {
		
			html += '<li><a class="ccm-icon" id="menuVisit' + obj.cID + '" href="' + CCM_REL + '/index.php?cID=' + obj.cID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/window_new.png)">' + ccmi18n_sitemap.visitExternalLink + '<\/span><\/a><\/li>';
			if (obj.cAlias == 'LINK') {
				html += '<li><a class="ccm-icon" dialog-width="350" dialog-height="300" dialog-title="' + ccmi18n_sitemap.editExternalLink + '" dialog-modal="false" id="menuLink' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=edit_external"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/add.png)">' + ccmi18n_sitemap.editExternalLink + '<\/span><\/a><\/li>';
			}
			html += '<li><a class="ccm-icon" id="menuDelete' + obj.cID + '" href="javascript:deletePage(' + obj.cID + ')"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/delete_small.png)">' + ccmi18n_sitemap.deleteExternalLink + '<\/span><\/a><\/li>';

		
		} else if (obj.canWrite == 'false') {
		
			html += '<li><a class="ccm-icon" id="menuVisit' + obj.cID + '" href="' + CCM_REL + '/index.php?cID=' + obj.cID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/window_new.png)">' + ccmi18n_sitemap.visitPage + '<\/span><\/a><\/li>';

		
		} else {
		
			html += '<li><a class="ccm-icon" id="menuVisit' + obj.cID + '" href="' + CCM_REL + '/index.php?cID=' + obj.cID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/window_new.png)">' + ccmi18n_sitemap.visitPage + '<\/span><\/a><\/li>';
			html += '<li><a class="ccm-icon" dialog-width="640" dialog-height="310" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.pageProperties + '" id="menuProperties' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=edit_metadata"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/edit_small.png)">' + ccmi18n_sitemap.pageProperties + '<\/span><\/a><\/li>';
			html += '<li><a class="ccm-icon" dialog-width="640" dialog-height="310" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.setPagePermissions + '" id="menuPermissions' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=edit_permissions"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/permissions_small.png)">' + ccmi18n_sitemap.setPagePermissions + '<\/span><\/a><\/li>';
			html += '<li><a class="ccm-icon" dialog-width="680" dialog-height="420" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.pageDesign + '" id="menuDesign' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=set_theme"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/design_small.png)">' + ccmi18n_sitemap.pageDesign + '<\/span><\/a><\/li>';
			html += '<li><a class="ccm-icon" dialog-width="640" dialog-height="340" dialog-modal="false" dialog-title="' + ccmi18n_sitemap.pageVersions + '" id="menuVersions' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/versions.php?rel=SITEMAP&cID=' + obj.cID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/versions_small.png)">' + ccmi18n_sitemap.pageVersions + '<\/span><\/a><\/li>';
			html += '<li><a class="ccm-icon" id="menuDelete' + obj.cID + '" href="javascript:deletePage(' + obj.cID + ')"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/delete_small.png)">' + ccmi18n_sitemap.deletePage + '<\/span><\/a><\/li>';
			html += '<li class=\"header\"><\/li>';
			html += '<li><a class="ccm-icon" id="menuReorder' + obj.cID + '" href="javascript:activateReorder(' + obj.cID + ')"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/up_down.png)">' + ccmi18n_sitemap.reorderPage + '<\/span><\/a><\/li>';
			html += '<li><a class="ccm-icon" id="menuMoveCopy' + obj.cID + '" href="javascript:activateMoveCopy(' + obj.cID + ')"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/up_down.png)">' + ccmi18n_sitemap.moveCopyPage + '<\/span><\/a><\/li>';
			html += '<li class=\"header\"><\/li>';
			if (obj.cNumChildren > 0 && obj.cID > 1) {
				html += '<li><a class="ccm-icon ccm-icon-sitemap-search" id="menuSearch' + obj.cID + '" href="javascript:searchSubPages(' + obj.cID + ')"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/magnifying.png)">' + ccmi18n_sitemap.searchPages + '<\/span><\/a><\/li>';
			}
			html += '<li><a class="ccm-icon" dialog-width="680" dialog-modal="false" dialog-height="440" dialog-title="' + ccmi18n_sitemap.addPage + '" id="menuSubPage' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=add"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/add.png)">' + ccmi18n_sitemap.addPage + '<\/span><\/a><\/li>';
			html += '<li><a class="ccm-icon" dialog-width="350" dialog-modal="false" dialog-height="160" dialog-title="' + ccmi18n_sitemap.addExternalLink + '" dialog-modal="false" id="menuLink' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=add_external"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/add.png)">' + ccmi18n_sitemap.addExternalLink + '<\/span><\/a><\/li>';

		} 
		
		html += '<\/ul>';

		html += '</div></div>';
		html += '<div class="ccm-menu-bl"><div class="ccm-menu-br"><div class="ccm-menu-b"></div></div></div>';

		bobj.append(html);

		$("#menuProperties" + obj.cID).dialog();
		$("#menuSubPage" + obj.cID).dialog();
		$("#menuDesign" + obj.cID).dialog();
		$("#menuLink" + obj.cID).dialog();
		$("#menuVersions" + obj.cID).dialog();
		$("#menuPermissions" + obj.cID).dialog();

	} else {
		bobj = $("#ccm-page-menu" + obj.cID);
	}
	
	ccm_fadeInMenu(bobj, e);
	
}

if (CCM_SITEMAP_MODE == 'full') {
	ccm_hidePane = function() {
		// overrides the typically UI hidepane because we're only seeing these on thickbox elements
		jQuery.fn.dialog.closeTop();
	}
}

deletePage = function(cID) {
	ccm_hideMenus();
	if (confirm(ccmi18n_sitemap.areYouSure)) {
		$.getJSON(CCM_TOOLS_PATH + '/dashboard/sitemap_delete_request.php', {'cID': cID, 'ccm_token': CCM_SECURITY_TOKEN}, function(resp) {
			ccm_parseJSON(resp, function() {
				deleteBranchFade(cID);
			});
		});
	}
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
		$('li.tree-node').draggable('destroy');
		if (!tr_moveCopyMode) {
			hideSitemapMessage();
		}
	}
}

activateMoveCopy = function(cID) {
	$(".ccm-tree-search-trigger").show();
	showSitemapMessage(ccmi18n_sitemap.moveCopyPageMessage);
	CCM_CID = cID;
	tr_moveCopyMode = true;
}

deactivateMoveCopy = function() {
	tr_moveCopyMode = false;
	CCM_SITEMAP_MODE = 'full';
	hideSitemapMessage();
	$(".ccm-tree-search-trigger").hide();

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

activateReorder = function(cID) {
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
	
	$('li.tree-node').draggable({
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
	showSitemapMessage(ccmi18n_sitemap.reorderPageMessage);
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
		deleteBranchDirect();
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

parseTree = function(node, nodeID, deactivateSubNodes) { 

	if (!node) {
		return false;
	}
	
	var container = $("#tree-root" + nodeID);
	//container.hide();
	ccm_sitemap_html += '<div class="dropzone tree-dz' + nodeID + '" tree-parent="' + nodeID + '" id="tree-dz' + nodeID + '-sub"><\/div>';
	
//	var moveableClass = 'moveable';
	var moveableClass = '';
	if (CCM_SITEMAP_MODE == 'move_copy_delete') {
		var moveableClass = '';
	}
		
	for (var i = 0; i < node.length; i++) {
		var typeClass = 'tree-node-document';
		var treeNodeType = 'document';
		// var labelClass = (deactivateSubNodes) ? "tree-label-inactive" : "tree-label";
		var labelClass = "tree-label";
		if (node[i].numSubpages > 0) {
			typeClass = 'tree-node-folder';
			treeNodeType = 'folder';
		}
		var customIconSrc = "";
		if (node[i].cIcon) {
			customIconSrc = ' style="background-image: url(' + node[i].cIcon + ')"';
		}
		
		var cAlias = node[i].cAlias;
		var canWrite = node[i].canWrite;
		var canDrag = (node[i].id > 1) ? "true" : "false";
		ccm_sitemap_html += '<li tree-node-type="' + treeNodeType + '" draggable="' + canDrag + '" class="tree-node ' + typeClass + ' tree-branch' + nodeID + '" id="tree-node' + node[i].id + '"' + customIconSrc + '>';
		if (node[i].numSubpages > 0 && (!deactivateSubNodes)) {
			var subPageStr = (node[i].id == 1) ? '' : ' (' + node[i].numSubpages + ')';
			ccm_sitemap_html += '<img src="' + CCM_IMAGE_PATH + '/spacer.gif" width="16" height="16" class="handle ' + moveableClass + '" />';
			ccm_sitemap_html += '<a href="javascript:toggleSub(' + node[i].id + ')">';
			ccm_sitemap_html += '<img src="' + CCM_IMAGE_PATH + '/dashboard/plus.jpg" width="9" height="9" class="tree-plus" id="tree-collapse' + node[i].id + '" /><\/a>';
			ccm_sitemap_html += '<div rel="' + CCM_REL + '/index.php?cID=' + node[i].id + '" class="' + labelClass + '" tree-node-alias="' + cAlias + '" ';
			ccm_sitemap_html += 'tree-node-canwrite="' + canWrite + '" tree-node-children="' + escape(node[i].numSubpages) + '" ';
			ccm_sitemap_html += 'tree-node-title="' + escape(node[i].cvName) + '" id="tree-label' + node[i].id + '">';
			ccm_sitemap_html += '<span>' + node[i].cvName + subPageStr + '</span><a class="ccm-tree-search-trigger" href="javascript:void(0)" onclick="searchSubPages(' + node[i].id + ')">';
			ccm_sitemap_html += '<img src="' + CCM_IMAGE_PATH + '/icons/magnifying.png" /></a><\/div>';
			ccm_sitemap_html += '<form onsubmit="return searchSitemapNode(' + node[i].id + ')" id="ccm-tree-search' + node[i].id + '" class="ccm-tree-search">';
			ccm_sitemap_html += '<a href="javascript:void(0)" onclick="closeSitemapSearch(' + node[i].id + ')" class="ccm-tree-search-close"><img src="' + CCM_IMAGE_PATH + '/icons/close.png" /></a>';
			ccm_sitemap_html += '<input type="text" name="submit" name="q" /> <a href="javascript:void(0)" onclick="searchSitemapNode(' + node[i].id + ')">';
			ccm_sitemap_html += '<img src="' + CCM_IMAGE_PATH + '/icons/magnifying.png" /></a></form>';
			ccm_sitemap_html += '<li><ul tree-root-state="closed" tree-root-node-id="' + node[i].id + '" id="tree-root' + node[i].id + '">';
			if (tr_parseSubnodes) {
				parseTree(node[i].subnodes, node[i].id, deactivateSubNodes);
			}		
			ccm_sitemap_html += '<\/ul>';
		} else {
			ccm_sitemap_html += '<div tree-node-title="' + escape(node[i].cvName) + '" tree-node-children="' + escape(node[i].numSubpages) + '" ';
			ccm_sitemap_html += 'class="' + labelClass + '" tree-node-alias="' + cAlias + '" tree-node-canwrite="' + canWrite + '" ';
			ccm_sitemap_html += 'id="tree-label' + node[i].id + '" rel="' + CCM_REL + '/index.php?cID=' + node[i].id + '">';
			ccm_sitemap_html += '<img src="' + CCM_IMAGE_PATH + '/spacer.gif" width="16" height="16" class="handle ' + moveableClass + '" /><span>' + node[i].cvName + '</span><\/div>';
		}
		ccm_sitemap_html += '</li><div class="dropzone tree-dz' + nodeID + '" tree-parent="' + nodeID + '" id="tree-dz' + node[i].id + '"><\/div>';

		if (node[i].selected == true) {
			$("#tree-label" + node[i].id).addClass('tree-label-selected-onload');
			if (CCM_SITEMAP_MODE == 'move_copy_delete') {
				deactivateSubNodes = true;
			}
		}
		
	}
 
	container.html(ccm_sitemap_html);

	if (!tr_doAnim) {
		container.show();
	} else {
		container.slideDown(300);
	}
	
	//ccm_sitemap_html = '';
	
}

selectMoveCopyTarget = function(destCID) {
	var origCID = CCM_CID;
	var dialog_title = ccmi18n_sitemap.moveCopyPage;
	var dialog_url = CCM_TOOLS_PATH + '/dashboard/sitemap_drag_request.php?origCID=' + origCID + '&sitemap_mode=' + CCM_SITEMAP_MODE + '&destCID=' + destCID;
	var dialog_height = 350;
	var dialog_width = 350;

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
		
	$.fn.dialog.open({
		title: dialog_title,
		href: dialog_url,
		width: dialog_width,
		modal: false,
		height: dialog_height,
		onClose: function() {
			//$("#tree").fadeIn(200);
			if (CCM_TARGET_ID != '') {
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
	if (CCM_SITEMAP_MODE == "move_copy_delete" || tr_moveCopyMode == true) {
		var destCID = node.attr('id').substring(10);
		selectMoveCopyTarget(destCID);
	} else if (CCM_SITEMAP_MODE == 'select_page') {
		ccm_selectSitemapNode(node.attr('id').substring(10), unescape(node.attr('tree-node-title')));
		jQuery.fn.dialog.closeTop();
	} else {
		node.addClass('tree-label-selected');
		if (tr_activeNode != false) {
			if (tr_activeNode.attr('id') != node.attr('id')) {
				tr_activeNode.removeClass('tree-label-selected');
			}
		}
		params = {'cID': node.attr('id').substring(10), 'canWrite': node.attr('tree-node-canwrite'), 'cNumChildren': node.attr('tree-node-children'), 'cAlias': node.attr('tree-node-alias')};
		showPageMenu(params, e);
		tr_activeNode = node;
	}
}

activateLabels = function() {
	$('div.tree-label span').unbind();
	$('div.tree-label span').click(function(e) {
		selectLabel(e, $(this).parent())
	}); 
	
	// now we make sure that all the items that are open are registered as open
	$("ul[tree-root-state=closed]").each(function() {
		var container = $(this);
		var nodeID = $(this).attr('tree-root-node-id');
		if ($(this).find('li').length > 0) {
			container.attr('tree-root-state', 'open');
			$("#tree-collapse" + nodeID).attr('src', CCM_IMAGE_PATH + '/dashboard/minus.jpg');
		}
	});

	if (CCM_SITEMAP_MODE == 'full') {
		$('img.handle').addClass('moveable');

	if (CCM_SITEMAP_MODE == 'full') {
		
			//drop onto a page
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
					//hideBranch(origCID);
				}
			}); 
			
			//addResortDroppable(nodeID);		
		}


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
	}

}

moveCopyAliasNode = function(reloadPage) {
	
	var origCID = $('#origCID').val();
	var destParentID = $('#destParentID').val();
	var destCID = $('#destCID').val();
	var ctask = $("input[name=ctask]:checked").val();
	var copyAll = $("input[name=copyAll]:checked").val();
	
	// DO THE DEED

	params = {
	
		'origCID': origCID,
		'destCID': destCID,
		'ctask': ctask,
		'ccm_token': CCM_SECURITY_TOKEN,
		'copyAll': copyAll		
	};

	$.getJSON(CCM_TOOLS_PATH + '/dashboard/sitemap_drag_request.php', params, function(resp) {
		// parse response
		ccm_parseJSON(resp, function() {
			
			alert(resp.message);
			
			if (reloadPage == true) {
				window.location.href = CCM_REL + "/index.php?cID=" + resp.cID;
				return false;
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
			
			openSub(destParentID, function() {openSub(destCID)});
			jQuery.fn.dialog.closeTop();
		});
	});	
}

searchSitemapNode = function(cID) {
	var q = $('form#ccm-tree-search' + cID + ' input').val();
	openSubSearch(cID, q);
	return false;
}

closeSitemapSearch = function(cID) {
	closeSub(cID);
	var container = $("#tree-root" + cID);
	$("#ccm-tree-search" + cID).hide();
	container.removeClass('ccm-sitemap-search-results');
	if (tr_moveCopyMode == true) {
		$("#ccm-tree-search-trigger" + cID).show();
	}
}

toggleSub = function(nodeID) {
	ccm_hideMenus();
	var container = $("#tree-root" + nodeID);
	if (container.attr('tree-root-state') == 'closed') {
		openSub(nodeID);
	} else {
		closeSub(nodeID);
	}
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

openSub = function(nodeID, onComplete) {
	setLoading(nodeID);
	var container = $("#tree-root" + nodeID);
	cancelReorder();
	ccm_sitemap_html = '';
	$.getJSON(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php?node=" + nodeID, function(resp) {
		parseTree(resp, nodeID, false);	
		activateLabels();
		setTimeout(function() {
			removeLoading(nodeID);
			if (onComplete != null) {
				onComplete();
			}			
		}, 200);
	});	
}

openSubSearch = function(nodeID, query, onComplete) {
	setLoading(nodeID);
	var container = $("#tree-root" + nodeID);
	container.addClass('ccm-sitemap-search-results');
	cancelReorder();
	$.getJSON(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php?node=" + nodeID, {'keywords': query}, function(resp) {
		parseTree(resp, nodeID, false);	
		activateLabels();
		setTimeout(function() {
			removeLoading(nodeID);
			if (onComplete != null) {
				onComplete();
			}			
		}, 200);
	});	
}


closeSub = function(nodeID) {
	var container = $("#tree-root" + nodeID);
	if (tr_doAnim) {
		setLoading(nodeID);
		container.slideUp(300, function() {
			removeLoading(nodeID);
			container.attr('tree-root-state', 'closed');
			container.html('');
			$("#tree-collapse" + nodeID).attr('src', CCM_IMAGE_PATH + '/dashboard/plus.jpg');
		});
	} else {	
		container.hide();
		container.attr('tree-root-state', 'closed');
		$("#tree-collapse" + nodeID).attr('src', CCM_IMAGE_PATH + '/dashboard/plus.jpg');
	}
	
	$.getJSON(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php?node=" + nodeID +'&ctask=close-node');
}

toggleMove = function() {
	if ($("#copyThisPage").get(0)) {
		$("#copyThisPage").get(0).disabled = true;
		$("#copyChildren").get(0).disabled = true;
	}
}

toggleAlias = function() {
	if ($("#copyThisPage").get(0)) {
		$("#copyThisPage").get(0).disabled = true;
		$("#copyChildren").get(0).disabled = true;
	}
}

toggleCopy = function() {
	if ($("#copyThisPage").get(0)) {
		$("#copyThisPage").get(0).disabled = false;
		$("#copyThisPage").get(0).checked = true;
		$("#copyChildren").get(0).disabled = false;
	}
}

ccmExitSearchResults = function(){
	$("#ccm-sitemap-search-results").slideUp(180, function() {
		this.style.display='none';
		$("#tree").show();			
	});
}
setupSearch = function() {
	$("#ccm-sitemap-search-toggle").click(function() {
		if ($(this).html() == ccmi18n_sitemap.searchPages) {	
			$("#ccm-sitemap-search-inner").slideDown(300);
			$(this).html('&lt; ' + ccmi18n_sitemap.backToSitemap);			
		} else {
			$("#ccm-sitemap-search-results").hide();
			$("#ccm-sitemap-search-inner").slideUp(180, function() {
				$("#tree").show();			
			});
			$(this).html(ccmi18n_sitemap.searchPages);	
		}
	});
	
	$("#ccm-dashboard-search").ajaxForm({
		beforeSubmit: function() {
			$("#tree").hide();
			$("h1").html('<span>' + ccmi18n_sitemap.searchResults + '</span>'); 
			$("#ccm-sitemap-throbber").show();
			$("#ccm-sitemap-search-results-list").html('');
			$("#ccm-sitemap-search-results-total").hide();

		},
		success: function(resp) {
			$("#tree").hide();
			$("#ccm-sitemap-throbber").hide();
			parseResults(resp);
			activateLabels();
			$("#ccm-sitemap-search-results").show();
			$("#ccm-sitemap-search-results-total").show();
		}
	
	});
	
	$("input[name=cStartDate]").datepicker({
		showAnim: 'fadeIn'
	});
	$("input[name=cEndDate]").datepicker({
		showAnim: 'fadeIn'
	});
}

parseResults = function(node) {
	var node = eval(node);
	var container = $("#ccm-sitemap-search-results-list");
	if (node.length == tr_maxSearchResults) {
		var navHTML= ccmi18n_sitemap.viewing + " <b>" + tr_maxSearchResults + "</b> " + ccmi18n_sitemap.results + " (<b>" + tr_maxSearchResults + "</b> " + ccmi18n_sitemap.max + ")"; 
	} else if (node.length > 0) {
		var navHTML= ccmi18n_sitemap.viewing + " <b>" + node.length + "</b> " + ccmi18n_sitemap.results;
	} else {
		var navHTML= ccmi18n_sitemap.noResults;
	}
	navHTML='<div id="returnToSitemap"><a href="#" onclick="ccmExitSearchResults();return false;">&laquo; ' + ccmi18n_sitemap.backToSitemap + '</a></div>'+navHTML;


	$("#ccm-sitemap-search-results-total").html(navHTML);
	
	$("#ccm-sitemap-search-results-total").show();
	
	for (var i = 0; i < node.length; i++) {
		var html = "";
		var typeClass = 'tree-node-document';
		var treeNodeType = 'document';
		var labelClass = "tree-label";
		var nodeID = node[i].cID;

		html += '<li class="search-result" id="search-result' + node[i].cID + '">';
		if (node[i].breadcrumb) {
			if (node[i].breadcrumb.length > 0) {
				html += '<div class="search-result-bc">';
				for (j = 0; j < node[i].breadcrumb.length; j++) {
					html += node[i].breadcrumb[j].cvName + " &gt; ";
				}
				html += '</div>';
			}
		}
		
		html += '<div class="search-result-meta">' + ccmi18n_sitemap.createdBy + ' <b>' + node[i].uName + '</b> ' + ccmi18n_sitemap.on + ' ' + node[i].cDateAdded + '</div>';
		
		html += '<div tree-node-title="' + escape(node[i].cvName) + '" class="' + labelClass + '" id="tree-label' + node[i].id + '" rel="' + CCM_REL + '/index.php?cID=' + node[i].id + '"><img src="' + CCM_IMAGE_PATH + '/spacer.gif" width="16" height="16" class="handle" /><span>' + node[i].cvName + '</span><\/div>';
		html += '<\/li><div class="dropzone tree-dz' + nodeID + '" tree-parent="' + nodeID + '" id="tree-dz' + node[i].id + '"><\/div>';
		
		existingHTML = container.html();
		container.html(existingHTML + html);
		
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

$(function() {
	$(document).ajaxError(function(event, request, settings) {
		alert(ccmi18n_sitemap.loadError + request.responseText);
	});
	
	$.getJSON(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php", {'mode' : CCM_SITEMAP_MODE}, function(resp) {  
		parseTree(resp, 0, false);
		activateLabels();
		tr_doAnim = true;
		tr_parseSubnodes = false;
		ccm_sitemap_html = '';
		if (CCM_SITEMAP_MODE == 'move_copy_delete') {
			$('.ccm-tree-search-trigger').show();
		}
	});
	
	$(document).click(function() {
		ccm_hideMenus();
		$("div.tree-label").removeClass('tree-label-selected');
		if (CCM_SITEMAP_MODE == 'full') {
			$("div.tree-label").removeClass('tree-label-selected-onload');	
		}
	});
	
	setupSearch();
	
	$("#ccm-show-all-pages-cb").click(function() {
		var showSystemPages = $(this).get(0).checked == true ? 1 : 0;
		$.getJSON(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php?show_system=" + showSystemPages, function(resp) {
			location.reload();
		});
	});
	

});
