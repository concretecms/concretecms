var tr_activeNode = false;
var tr_doAnim = false; // we initial set it to false, but once we're done loading the initial state we can make it true
var tr_parseSubnodes = true;
var tr_maxSearchResults = 50;

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
		
			html += '<li><a class="ccm-icon" id="menuVisit' + obj.cID + '" href="' + CCM_REL + '/index.php?cID=' + obj.cID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/window_new.png)">Visit<\/span><\/a><\/li>';
			if (obj.cAlias == 'LINK') {
				html += '<li><a class="ccm-icon" dialog-width="350" dialog-height="300" dialog-title="Edit External Link" dialog-modal="false" id="menuLink' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=edit_external"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/add.png)">Edit External Link<\/span><\/a><\/li>';
			}
			html += '<li><a class="ccm-icon" id="menuDelete' + obj.cID + '" href="javascript:deletePage(' + obj.cID + ')"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/delete_small.png)">Delete<\/span><\/a><\/li>';

		
		} else if (obj.canWrite == 'false') {
		
			html += '<li><a class="ccm-icon" id="menuVisit' + obj.cID + '" href="' + CCM_REL + '/index.php?cID=' + obj.cID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/window_new.png)">Visit<\/span><\/a><\/li>';

		
		} else {
		

			html += '<li><a class="ccm-icon" id="menuVisit' + obj.cID + '" href="' + CCM_REL + '/index.php?cID=' + obj.cID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/window_new.png)">Visit<\/span><\/a><\/li>';
			html += '<li><a class="ccm-icon" dialog-width="640" dialog-height="310" dialog-modal="false" dialog-title="Properties" id="menuProperties' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=edit_metadata"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/edit_small.png)">Properties<\/span><\/a><\/li>';
			html += '<li><a class="ccm-icon" dialog-width="640" dialog-height="310" dialog-modal="false" dialog-title="Permissions" id="menuPermissions' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=edit_permissions"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/permissions_small.png)">Permissions<\/span><\/a><\/li>';
			html += '<li><a class="ccm-icon" dialog-width="680" dialog-height="420" dialog-modal="false" dialog-title="Design" id="menuDesign' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=set_theme"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/design_small.png)">Design<\/span><\/a><\/li>';
			html += '<li><a class="ccm-icon" dialog-width="640" dialog-height="340" dialog-modal="false" dialog-title="Versions" id="menuVersions' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/versions.php?rel=SITEMAP&cID=' + obj.cID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/versions_small.png)">Versions<\/span><\/a><\/li>';
			html += '<li><a class="ccm-icon" id="menuDelete' + obj.cID + '" href="javascript:deletePage(' + obj.cID + ')"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/delete_small.png)">Delete<\/span><\/a><\/li>';
			html += '<li class=\"header\"><\/li>';
			html += '<li><a class="ccm-icon" dialog-width="680" dialog-modal="false" dialog-height="440" dialog-title="Add New Page" id="menuSubPage' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=add"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/add.png)">Add Page<\/span><\/a><\/li>';
			html += '<li><a class="ccm-icon" dialog-width="350" dialog-modal="false" dialog-height="160" dialog-title="Add External Link" dialog-modal="false" id="menuLink' + obj.cID + '" href="' + CCM_TOOLS_PATH + '/edit_collection_popup.php?rel=SITEMAP&cID=' + obj.cID + '&ctask=add_external"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/add.png)">Add External Link<\/span><\/a><\/li>';

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
	if (confirm('Are you sure?')) {
		$.getJSON(CCM_TOOLS_PATH + '/dashboard/sitemap_delete_request.php', {'cID': cID}, function(resp) {
			parseJSON(resp, function() {
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
		parseJSON(resp, function() {});
		removeLoading(nodeID);	
	});
}

var SITEMAP_LAST_DIALOGUE_URL='';
parseTree = function(node, nodeID, deactivateSubNodes) { 

	var container = $("#tree-root" + nodeID);
	container.hide();
	var outerhtml = '<div class="dropzone tree-dz' + nodeID + '" tree-parent="' + nodeID + '" id="tree-dz' + nodeID + '-sub"><\/div>';
	container.html(outerhtml);
	
	var moveableClass = 'moveable';
	if (CCM_SITEMAP_MODE == 'move_copy_delete') {
		var moveableClass = '';
	}
		
	for (var i = 0; i < node.length; i++) {
		var html = "";
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

		html += '<li tree-node-type="' + treeNodeType + '" class="tree-node ' + typeClass + ' tree-branch' + nodeID + '" id="tree-node' + node[i].id + '"' + customIconSrc + '>';
		if (node[i].numSubpages > 0 && (!deactivateSubNodes)) {
			var subPageStr = (node[i].id == 1) ? '' : ' (' + node[i].numSubpages + ')';
			html += '<img src="' + CCM_IMAGE_PATH + '/spacer.gif" width="16" height="16" class="handle ' + moveableClass + '" /><a href="javascript:toggleSub(' + node[i].id + ')"><img src="' + CCM_IMAGE_PATH + '/dashboard/plus.jpg" width="9" height="9" class="tree-plus" id="tree-collapse' + node[i].id + '" /><\/a><div rel="' + CCM_REL + '/index.php?cID=' + node[i].id + '" class="' + labelClass + '" tree-node-alias="' + cAlias + '" tree-node-canwrite="' + canWrite + '" tree-node-title="' + escape(node[i].cvName) + '" id="tree-label' + node[i].id + '"><span>' + node[i].cvName + subPageStr + '</span><\/div><ul tree-root-state="closed" id="tree-root' + node[i].id + '"><\/ul>';
		} else {
			html += '<div tree-node-title="' + escape(node[i].cvName) + '" class="' + labelClass + '" tree-node-alias="' + cAlias + '" tree-node-canwrite="' + canWrite + '" id="tree-label' + node[i].id + '" rel="' + CCM_REL + '/index.php?cID=' + node[i].id + '"><img src="' + CCM_IMAGE_PATH + '/spacer.gif" width="16" height="16" class="handle ' + moveableClass + '" /><span>' + node[i].cvName + '</span><\/div>';
		}
		html += '<\/li><div class="dropzone tree-dz' + nodeID + '" tree-parent="' + nodeID + '" id="tree-dz' + node[i].id + '"><\/div>';
		
		existingHTML = container.html();
		container.html(existingHTML + html);
		
		
		if (node[i].selected == true) {
			$("#tree-label" + node[i].id).addClass('tree-label-selected-onload');
			if (CCM_SITEMAP_MODE == 'move_copy_delete') {
				deactivateSubNodes = true;
			}
		}
		
		if (node[i].subnodes && tr_parseSubnodes) {
			parseTree(node[i].subnodes, node[i].id, deactivateSubNodes);
		}		
		
		deactivateSubNodes = false;
	}
 
	if (CCM_SITEMAP_MODE == 'full') {
		
		//drop onto a page
		$('li.tree-branch' + nodeID + ' div.tree-label').droppable({
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
					title: 'Move/Copy Page',
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
		
		//addResortDroppable(nodeID);		
	}

	container.attr('tree-root-state', 'open');
	$("#tree-collapse" + nodeID).attr('src', CCM_IMAGE_PATH + '/dashboard/minus.jpg');
	
	if (!tr_doAnim) {
		container.show();
	} else {
		container.slideDown(300);
	}
	
}

selectLabel = function(e, node) {
	switch(CCM_SITEMAP_MODE) {
		case "move_copy_delete":
			var destCID = node.attr('id').substring(10);
			var origCID = CCM_CID;
				
			$.fn.dialog.open({
				title: 'Move/Copy Page',
				href: CCM_TOOLS_PATH + '/dashboard/sitemap_drag_request.php?origCID=' + origCID + '&destCID=' + destCID,
				width: 350,
				modal: false,
				height: 350,
				onClose: function() {
					//$("#tree").fadeIn(200);
				}

			});
			break;
		case "select_page":

			ccm_selectSitemapNode(node.attr('id').substring(10), unescape(node.attr('tree-node-title')));
			jQuery.fn.dialog.closeTop();

			break;
		default:
			node.addClass('tree-label-selected');
			if (tr_activeNode != false) {
				if (tr_activeNode.attr('id') != node.attr('id')) {
					tr_activeNode.removeClass('tree-label-selected');
				}
			}
			params = {'cID': node.attr('id').substring(10), 'canWrite': node.attr('tree-node-canwrite'), 'cAlias': node.attr('tree-node-alias')};
			showPageMenu(params, e);
			tr_activeNode = node;
	}
}

activateLabels = function() {
	$('div.tree-label').unbind();
	$('div.tree-label span').click(function(e) {
		selectLabel(e, $(this).parent())
	}); 
	if (CCM_SITEMAP_MODE == 'full') {
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
		'copyAll': copyAll		
	};
	
	$.getJSON(CCM_TOOLS_PATH + '/dashboard/sitemap_drag_request.php', params, function(resp) {
		// parse response
		parseJSON(resp, function() {
			
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
	$.getJSON(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php?node=" + nodeID, function(resp) {
		parseTree(resp, nodeID, false);	
		activateLabels();
		fixResortingDroppables();
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
	$("#ccm-search-results").slideUp(180, function() {
		this.style.display='none';
		$("#tree").show();			
	});
}
setupSearch = function() {
	$("#ccm-sitemap-search-toggle").click(function() {
		if ($(this).html() == 'Search Pages') {	
			$("#ccm-sitemap-search-inner").slideDown(300);
			$(this).html('&lt; Back to Sitemap');			
		} else {
			$("#ccm-search-results").hide();
			$("#ccm-sitemap-search-inner").slideUp(180, function() {
				$("#tree").show();			
			});
			$(this).html('Search Pages');	
		}
	});
	
	$("#ccm-dashboard-search").ajaxForm({
		beforeSubmit: function() {
			$("#tree").hide();
			$("h1").html('<span>Search Results</span>'); 
			$("#ccm-sitemap-throbber").show();
			$("#ccm-search-results-list").html('');
			$("#ccm-search-results-total").hide();

		},
		success: function(resp) {
			$("#tree").hide();
			$("#ccm-sitemap-throbber").hide();
			parseResults(resp);
			activateLabels();
			$("#ccm-search-results").show();
			$("#ccm-search-results-total").show();
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
	var container = $("#ccm-search-results-list");
	
	if (node.length == tr_maxSearchResults) {
		var navHTML="Viewing <b>" + tr_maxSearchResults + "</b> results (" + tr_maxSearchResults + " max)"; 
	} else if (node.length > 1) {
		var navHTML="Viewing <b>" + node.length + "</b> results";
	} else if (node.length == 1) {
		var navHTML="Viewing <b>" + node.length + "</b> result";
	} else {
		var navHTML="No results found.";
	}
	navHTML='<div id="returnToSitemap"><a href="#" onclick="ccmExitSearchResults();return false;">&laquo; Return to Sitemap</a></div>'+navHTML;


	$("#ccm-search-results-total").html(navHTML);
	
	$("#ccm-search-results-total").show();
	
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
		
		html += '<div class="search-result-meta">Created by <b>' + node[i].uName + '</b> on ' + node[i].cDateAdded + '</div>';
		
		html += '<div tree-node-title="' + escape(node[i].cvName) + '" class="' + labelClass + '" id="tree-label' + node[i].id + '" rel="' + CCM_REL + '/index.php?cID=' + node[i].id + '"><img src="' + CCM_IMAGE_PATH + '/spacer.gif" width="16" height="16" class="handle" /><span>' + node[i].cvName + '</span><\/div>';
		html += '<\/li><div class="dropzone tree-dz' + nodeID + '" tree-parent="' + nodeID + '" id="tree-dz' + node[i].id + '"><\/div>';
		
		existingHTML = container.html();
		container.html(existingHTML + html);
		
	}
}
	
	
function fixResortingDroppables(){
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
	$.getJSON(CCM_TOOLS_PATH + "/dashboard/sitemap_data.php", function(resp) {  
		parseTree(resp, 0, false);
		activateLabels();
		fixResortingDroppables();
		tr_doAnim = true;
		tr_parseSubnodes = false;
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