var ccm_pageMenus = new Array();
var ccm_deactivateTimer = false;
var ccm_deactivateTimerTime = 2000;
var ccm_topPaneDeactivated = false;
var ccm_topPaneTargetURL = false;
var ccm_selectedDomID = false;
var ccm_isBlockError = false;
var ccm_blockError = "";
var ccm_siteActivated = true;
var ccm_menuActivated = false;
var ccm_bcEnabled = false;
var ccm_bcEnabledTimer = false;

/* animated effects */
var ccm_animEffects = true;

ccm_menuInit = function(obj) {
	
	if (CCM_EDIT_MODE && (!CCM_ARRANGE_MODE)) {
		switch(obj.type) {
			case "BLOCK":
				$("#b" + obj.bID + "-" + obj.aID).mouseover(function(e) {
					if (!ccm_menuActivated) {
						ccm_activate(obj, "#b" + obj.bID + "-" + obj.aID);
					}
				});
				break;
			case "AREA":
				$("#a" + obj.aID + "controls").mouseover(function(e) {
				if (!ccm_menuActivated) {
					ccm_activate(obj, "#a" + obj.aID + "controls");
				}
				});
				break;
		}
	}	
}

ccm_showBlockMenu = function(obj, e) {
	ccm_hideMenus();
	e.stopPropagation();
	ccm_menuActivated = true;
	
	// now, check to see if this menu has been made
	var bobj = document.getElementById("ccm-block-menu" + obj.bID + "-" + obj.aID);

	if (!bobj) {
		
		// create the 1st instance of the menu
		el = document.createElement("DIV");
		el.id = "ccm-block-menu" + obj.bID + "-" + obj.aID;
		el.className = "ccm-menu";
		el.style.display = "none";
		document.body.appendChild(el);
		
		bobj = $("#ccm-block-menu" + obj.bID + "-" + obj.aID);
		bobj.css("position", "absolute");
		
		//contents  of menu
		var html = '<div class="ccm-menu-tl"><div class="ccm-menu-tr"><div class="ccm-menu-t"></div></div></div>';
		html += '<div class="ccm-menu-l"><div class="ccm-menu-r">';
		html += '<ul>';
		//html += '<li class="header"></li>';
		if (obj.canWrite) {
			html += (obj.editInline) ? '<li><a class="ccm-icon" id="menuEdit' + obj.bID + '-' + obj.aID + '" href="' + CCM_DISPATCHER_FILENAME + '?cID=' + CCM_CID + '&bID=' + obj.bID + '&arHandle=' + obj.arHandle + '&btask=edit#_edit' + obj.bID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/edit_small.png)">Edit</span></a></li>'
				: '<li><a class="ccm-icon" dialog-title="Edit" dialog-modal="true" dialog-width="' + obj.width + '" dialog-height="' + obj.height + '" id="menuEdit' + obj.bID + '-' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_block_popup.php?cID=' + CCM_CID + '&bID=' + obj.bID + '&arHandle=' + obj.arHandle + '&btask=edit"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/edit_small.png)">Edit</span></a></li>';
		}
		html += '<li><a class="ccm-icon" dialog-title="Add to Scrapbook" dialog-modal="false" dialog-width="200" dialog-height="100" id="menuAddToScrapbook' + obj.bID + '-' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/pile_manager.php?cID=' + CCM_CID + '&bID=' + obj.bID + '&arHandle=' + obj.arHandle + '&btask=add"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/paste_small.png)">Copy To Scrapbook</span></a></li>';

		if (obj.canArrange) {
			html += '<li><a class="ccm-icon" id="menuArrange' + obj.bID + '-' + obj.aID + '" href="' + CCM_DISPATCHER_FILENAME + '?cID=' + CCM_CID + '&btask=arrange"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/up_down.png)">Move</span></a></li>';
		}
		if (obj.canDelete) {
			html += '<li><a class="ccm-icon" id="menuDelete' + obj.bID + '-' + obj.aID + '" href="#" onclick="javascript:ccm_deleteBlock(' + obj.bID + ',' + obj.aID + ', \'' + obj.arHandle + '\', \'' + obj.deleteMessage + '\')"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/delete_small.png)">Delete</span></a></li>';
		}
		if (obj.canModifyGroups || obj.canAliasBlockOut) {
			html += '<li class="header"></li>';
		}
		if (obj.canWrite) {
			html += '<li><a class="ccm-icon" dialog-title="Change Template" dialog-width="300" dialog-height="100" id="menuChangeTemplate' + obj.bID + '-' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_block_popup.php?cID=' + CCM_CID + '&bID=' + obj.bID + '&arHandle=' + obj.arHandle + '&btask=template&modal=true&width=300&height=100" title="Change Template"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/wrench.png)">Change Template</span></a></li>';
		}
		if (obj.canModifyGroups) {
			html += '<li><a title="Edit Groups" class="ccm-icon" dialog-width="400" dialog-height="380" id="menuBlockGroups' + obj.bID + '-' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_block_popup.php?cID=' + CCM_CID + '&bID=' + obj.bID + '&arHandle=' + obj.arHandle + '&btask=groups" dialog-title="Set Permissions"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/permissions_small.png)">Set Permissions</span></a></li>';
		}
		if (obj.canAliasBlockOut) {
			html += '<li><a class="ccm-icon" dialog-width="550" dialog-height="450" id="menuBlockAliasOut' + obj.bID + '-' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_block_popup.php?cID=' + CCM_CID + '&bID=' + obj.bID + '&arHandle=' + obj.arHandle + '&btask=child_pages" dialog-title="Add to This Template\'s Pages"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/template_block.png)">Setup On Child Pages</span></a></li>';
		}

		html += '</ul>';
		html += '</div></div>';
		html += '<div class="ccm-menu-bl"><div class="ccm-menu-br"><div class="ccm-menu-b"></div></div></div>';
		bobj.append(html);
		
		// add dialog elements where necessary
		if (obj.canWrite && (!obj.editInline)) {
			$('a#menuEdit' + obj.bID + '-' + obj.aID).dialog();
		}
		if (obj.canWrite && (!obj.editInline)) {
			$('a#menuChangeTemplate' + obj.bID + '-' + obj.aID).dialog();
		}
		if (obj.canAliasBlockOut) {
			$('a#menuBlockAliasOut' + obj.bID + '-' + obj.aID).dialog();
		}
		if (obj.canModifyGroups) {
			$("#menuBlockGroups" + obj.bID + '-' + obj.aID).dialog();
		}
		$("#menuAddToScrapbook" + obj.bID + '-' + obj.aID).dialog();
		

	} else {
		bobj = $("#ccm-block-menu" + obj.bID + '-' + obj.aID);
	}
	
	ccm_fadeInMenu(bobj, e);

}

ccm_showAreaMenu = function(obj, e) {
	$.fn.dialog.open({
		title: 'Block Area',
		href: CCM_TOOLS_PATH + '/edit_area_popup.php?cID=' + CCM_CID + '&arHandle=' + obj.arHandle,
		width: 550,
		modal: false,
		height: 380
	});
}

ccm_hideHighlighter = function() {
	$("#ccm-highlighter").css('display', 'none');
}

ccm_addError = function(err) {
	ccm_isBlockError = true;
	ccm_blockError += err;
}

ccm_resetBlockErrors = function() {
	ccm_isBlockError = false;
	ccm_blockError = "";
}

ccm_deleteBlock = function(bID, aID, arHandle, msg) {
	if (confirm(msg)) {
		// got to grab the message too, eventually
		ccm_hideHighlighter();
		$.ajax({
 		type: 'POST',
 		url: CCM_DISPATCHER_FILENAME,
 		data: 'cID=' + CCM_CID + '&isAjax=true&btask=remove&bID=' + bID + '&arHandle=' + arHandle,
 		success: function(resp) {
 			$("#b" + bID + '-' + aID).fadeOut(300);
 		}});		
	}
}

ccm_hideMenus = function() {
	/* 1st, hide all items w/the css menu class */
	$("div.ccm-menu").hide();
	ccm_menuActivated = false;
}

ccm_activate = function(obj, domID) {
	if (ccm_topPaneDeactivated) {
		return false;
	}
	
	if (ccm_selectedDomID) {
		$(ccm_selectedDomID).removeClass('selected');
	}
	
	aobj = $(domID);
	aobj.addClass('selected');
	ccm_selectedDomID = domID;
	
	offs = aobj.offset({padding: true});

	/* put highlighter over item. THanks dimensions plugin! */
	
	$("#ccm-highlighter").css("width", aobj.outerWidth());
	$("#ccm-highlighter").css("height", aobj.outerHeight());
	$("#ccm-highlighter").css("top", offs.top);
	$("#ccm-highlighter").css("left", offs.left);
	$("#ccm-highlighter").css("display", "block");
	/*
	$("#ccmMenuHighlighter").mouseover(
		function() {clearTimeout(ccm_deactivateTimer)}
	);
	*/
	$("#ccm-highlighter").unbind('click');
	$("#ccm-highlighter").click(
		function(e) {
			switch(obj.type) {
				case "BLOCK":
					ccm_showBlockMenu(obj, e);
					break;
				case "AREA":
					ccm_showAreaMenu(obj,e);
					break;
			}
		}
	);
}

ccm_editInit = function() {
	ccm_setupHeaderMenu();
	$(document.body).append('<div style="position: absolute; display:none" id="ccm-highlighter">&nbsp;</div>');
	$(document).click(function() {ccm_hideMenus();});
	
	$("a").click(function(e) {
		ccm_hideMenus();
		return false;	
	});
	
	/* setup header actions */
	/*
	$('#ccm-main-nav li').each(function(){
		//this.onmouseover=function(){alert('this')}							
	});
	*/
	
	$("a#ccm-nav-mcd").click(function() {
		ccm_showPane(this, CCM_TOOLS_PATH + "/edit_collection_popup.php?ctask=mcd&cID=" + CCM_CID);
	});
	
	$("a#ccm-nav-versions").click(function() {
		ccm_showPane(this, CCM_TOOLS_PATH + "/versions.php?cID=" + CCM_CID);
	});
	
	$("a#ccm-nav-exit-edit").click(function() {
		ccm_showPane(this, CCM_TOOLS_PATH + "/check_in.php?cID=" + CCM_CID);
	});
	
	$("a#ccm-nav-permissions").click(function() {
		ccm_showPane(this, CCM_TOOLS_PATH + "/edit_collection_popup.php?ctask=edit_permissions&cID=" + CCM_CID);
	});

	$("a#ccm-nav-design").click(function() {
		ccm_showPane(this, CCM_TOOLS_PATH + "/edit_collection_popup.php?ctask=set_theme&cID=" + CCM_CID);
	});

	$("a#ccm-nav-properties").click(function() {
		ccm_showPane(this, CCM_TOOLS_PATH + "/edit_collection_popup.php?ctask=edit_metadata&cID=" + CCM_CID);
	});
	
	$("#ccm-highlighter").mouseout(function() {
		if (!ccm_menuActivated) {
			ccm_hideHighlighter();
		}
	});
		
}

ccm_hidePane = function(onDone) {
	var wrappane = $("#ccm-page-detail");
	$("li.ccm-nav-active").removeClass('ccm-nav-active');
	if (ccm_animEffects) {
		wrappane.fadeOut(120, function() {
			if(!ccm_onPaneCloseObj) ccm_activateSite();
			else ccm_siteActivated = true;
			$(window).unbind('keypress');
			ccm_activateHeader();
			if (typeof onDone == 'function') {
				onDone();
			}
		});
	} else {
		wrappane.hide();
		if(!ccm_onPaneCloseObj) ccm_activateSite();
		else ccm_siteActivated = true;
		$(window).unbind('keypress');
	
		ccm_activateHeader();
	
		if (typeof onDone == 'function') {
			onDone();
		}
	}
}

ccm_deactivateHeader = function(obj) { 
	ccm_topPaneDeactivated = true;
	$("ul#ccm-main-nav").addClass('ccm-pane-open'); 
	if(ccm_dialogOpen) $("ul#ccm-main-nav li").addClass('ccm-nav-rolloversOff');	
	else $("ul#ccm-main-nav li").addClass('ccm-nav-inactive');		
	ccm_hideBreadcrumb();
	if (obj) {
		$(obj).parent().removeClass('ccm-nav-inactive');
		$(obj).parent().addClass('ccm-nav-active');
	}
}

ccm_activateHeader = function() {
	ccm_topPaneDeactivated = false;
	$("ul#ccm-main-nav").removeClass('ccm-pane-open');
	$("ul#ccm-main-nav li").removeClass('ccm-nav-inactive');
	$("ul#ccm-main-nav li").removeClass('ccm-nav-rolloversOff');
}
var ccm_onPaneCloseObj=null;
var ccm_onPaneCloseTargetURL=null;
ccm_showPane = function(obj, targetURL) {
	if (ccm_topPaneDeactivated && ccm_dialogOpen) {
		return false;
	}	
	if(typeof(obj.blur)=='function') obj.blur();
	ccm_onPaneCloseObj=null;
	ccm_onPaneCloseTargetURL=null;
	if (ccm_topPaneDeactivated){
		ccm_onPaneCloseObj=obj;
		ccm_onPaneCloseTargetURL=targetURL;
		ccm_hidePane( function(){ ccm_showPane(ccm_onPaneCloseObj,ccm_onPaneCloseTargetURL) } );
		return false;
	} else {
		ccm_doShowPane(obj, targetURL);
	}
}

ccm_doShowPane = function(obj, targetURL) {
	// jump to the top of the page
	window.scrollTo(0,0);
	
	// loop through header nav items, turn them all off except our current one
	ccm_deactivateHeader(obj);

	ccm_deactivateSite(function() {;
		var wrappane = $("#ccm-page-detail");
		var conpane = $("#ccm-page-detail-content");
		ccm_hideMenus();
		ccm_hideHighlighter();
		ccm_topPaneTargetURL = targetURL;
		$(window).keypress(function(e) {
			if (e.keyCode == 27) {
				ccm_hidePane();
			}
		});
		
		$.ajax({
			type: 'GET',
			url: targetURL + "&random=" + (new Date().getTime()),
			success: function(msg) {
				conpane.html(msg);
				$("#ccm-page-detail-content .dialog-launch").dialog();			
				if (ccm_animEffects) {
					wrappane.fadeIn(120, function() {
						ccm_removeHeaderLoading();
					});
				} else {
					wrappane.show();
					ccm_removeHeaderLoading();
				}
			}
		});
	});
}

ccm_triggerSelectUser = function(uID, uName) {
	alert(uID);
	alert(uName);
}

ccm_setupUserSearch = function() {
	$('#ccm-user-search-results a').each(function(i) {
		var linkParts = $(this).attr('href').split('#')
		if(linkParts[1]) {
			if (linkParts[1].substring(0, 3) == 'sel') {
				var uIDuName = linkParts[1].substring(3).split('-');
				$(this).click(function() {
					ccm_triggerSelectUser(uIDuName[0], uIDuName[1]);
					$.fn.dialog.closeTop();
					return false;
				});
			}
		}
	});	
	$("#ccm-user-search").ajaxForm({
		beforeSubmit: function() {
			$("#ccm-user-search-wrapper").html("");	
		},
		success: function(resp) {
			$("#ccm-user-search-wrapper").html(resp);	
		}
	});
	
	/* setup paging */
	$("div#ccm-user-paging a").click(function() {
		$("#ccm-user-search-wrapper").html("");	
		$.ajax({
			type: "GET",
			url: $(this).attr('href'),
			success: function(resp) {
				//$("#ccm-dialog-throbber").css('visibility','hidden');
				$("#ccm-user-search-wrapper").html(resp);
			}
		});
		return false;
	});
}

ccm_triggerSelectGroup = function(gID, gName) {
	alert(gID);
	alert(gName);
}


ccm_setupGroupSearch = function() {
	$('div.ccm-group a').each(function(i) {
		var gla = $(this);
		$(this).click(function() {
			ccm_triggerSelectGroup(gla.attr('group-id'), gla.attr('group-name'));
			$.fn.dialog.closeTop();
			return false;
		});
	});	
	$("#ccm-group-search").ajaxForm({
		beforeSubmit: function() {
			$("#ccm-group-search-wrapper").html("");	
		},
		success: function(resp) {
			$("#ccm-group-search-wrapper").html(resp);	
		}
	});
	
	/* setup paging */
	$("div#ccm-group-paging a").click(function() {
		$("#ccm-group-search-wrapper").html("");	
		$.ajax({
			type: "GET",
			url: $(this).attr('href'),
			success: function(resp) {
				//$("#ccm-dialog-throbber").css('visibility','hidden');
				$("#ccm-group-search-wrapper").html(resp);
			}
		});
		return false;
	});
}

ccm_saveArrangement = function() {
	
	var serial = '';
	$('div.ccm-area').each(function() {
		areaStr = '&area[' + $(this).attr('id').substring(1) + '][]=';
		
		bArray = $(this).sortable('toArray');
		for (i = 0; i < bArray.length; i++ ) {
			if (bArray[i] != '' && bArray[i].substring(0, 1) == 'b') {
				// make sure to only go from b to -, meaning b28-9 becomes "28"
				serial += areaStr + bArray[i].substring(1, bArray[i].indexOf('-'));
			}
		}
	});
	
 	$.ajax({
 		type: 'POST',
 		url: CCM_DISPATCHER_FILENAME,
 		data: 'cID=' + CCM_CID + '&btask=ajax_do_arrange' + serial,
 		success: function(msg) {
			location.href= CCM_DISPATCHER_FILENAME + "?cID=" + CCM_CID;
 		}});
 		
}

ccm_arrangeInit = function() {
	//$(document.body).append('<img src="' + CCM_IMAGE_PATH + '/topbar_throbber.gif" width="16" height="16" id="ccm-topbar-loader" />');
	ccm_setupHeaderMenu();
	$("div.ccm-area").each(function() {
		$(this).sortable({
		connectWith: $("div.ccm-area"),
		accept: 'div.ccm-block-arrange',
		opacity: 0.5
	});
	});
	
	$("a#ccm-nav-save-arrange").click(function() {
		ccm_saveArrangement();
	});
}

ccm_init = function() {
	ccm_setupHeaderMenu();
	// blink notification if it exists
	$("#ccm-notification").fadeIn();
	$("a#ccm-nav-edit").click(function() {
		if (!ccm_topPaneDeactivated) {
			setTimeout(function() {
				// stupid safari? wtf?
				window.location.href = CCM_REL + '/index.php?cID=' + CCM_CID + '&ctask=check-out';
			}, 50);
		}
	});
	$("a#ccm-nav-add").click(function() {
		ccm_showPane(this, CCM_TOOLS_PATH + "/edit_collection_popup.php?ctask=add&cID=" + CCM_CID);
	});
}

ccm_selectSitemapNode = function(cID, cName) {
	alert(cID);
	alert(cName);
}

ccm_fadeInMenu = function(bobj, e) {
	var mwidth = bobj.width();
	var mheight = bobj.height();
	var posX = e.pageX + 2;
	var posY = e.pageY + 2;
	
	if ($(window).height() < e.clientY + mheight) {
		posY = e.pageY - mheight + 20;
	} else {
		posY = posY - 20;
	}
	
	if ($(window).width() < e.clientX + mwidth) {
		posX = e.pageX - mwidth + 15;
	} else {
		posX = posX - 15;
	}
	
	// the 15 and 20 is because of the way we're styling these menus
	
	bobj.css("top", posY + "px");
	bobj.css("left", posX + "px");
	
	if (ccm_animEffects) {
		bobj.fadeIn(180);
	} else {
		bobj.show();
	}
}

ccm_blockFormInit = function() {
	$("form.validate").submit(function() {
	if (typeof window.ccmValidateBlockForm == 'function') {
		window.ccmValidateBlockForm();
		if (ccm_isBlockError) {
			if(ccm_blockError) 
				alert(ccm_blockError);
			ccm_resetBlockErrors();
			return false;
		}
	}
	return true;
	});
}

ccm_setupGridStriping = function(tbl) {
	$("#" + tbl + " tr").removeClass();
	var j = 0;
	$("#" + tbl + " tr").each(function() {
		if ($(this).css('display') != 'none') {					
			if (j % 2 == 0) {
				$(this).addClass('ccm-row-alt');
			}
			j++;
		}
	});
}

ccm_setupHeaderMenu = function() {
	// preload
	var ccmLoadingIcon = new Image();
	ccmLoadingIcon.src = CCM_IMAGE_PATH + "/icons/icon_header_loading.gif";
	
	var ccmHeaderImg = new Image();// preload image
	ccmHeaderImg.src = CCM_IMAGE_PATH + "/bg_header_active.png";

	$("ul#ccm-main-nav a").click(function() {
		if (!ccm_topPaneDeactivated) {
			$(this).addClass('ccm-nav-loading');
		}
	});
	$("ul#ccm-system-nav a").click(function() {
		$(this).addClass('ccm-nav-loading');
	});
	$("a#ccm-nav-dashboard").click(function() {
		var dash = $(this).attr('href');
		setTimeout(function() {
			// stupid safari? wtf?
			window.location.href = dash;
		}, 50);
		
	});

	$("a#ccm-nav-help").click(function() {
		var helpurl = $(this).attr('helpurl');
		window.open(helpurl);
		ccm_removeHeaderLoading();

	});
	
	$("a#ccm-nav-logout").click(function() {
		var href = $(this).attr('href');
		setTimeout(function() {
			// stupid safari? wtf?
			window.location.href = href;
		}, 50);
		
	});
	
}

ccm_deactivateSite = function(onDone) {
	if (ccm_siteActivated == false) {
		return false;
	}
	
	if (ccm_animEffects) {
				
		$("#ccm-overlay").fadeIn(100, function() {
			ccm_siteActivated = false;
			if (typeof onDone == 'function') {
				onDone();
			}
		});
	
	} else {
		$("#ccm-overlay").show();
		ccm_siteActivated = false;
		if (typeof onDone == 'function') {
			onDone();
		}
	}
}

ccm_activateSite = function() {
	if (ccm_animEffects) {
		$("#ccm-overlay").fadeOut(100);
	} else {
		$("#ccm-overlay").hide();
	}
	ccm_siteActivated = true;
}

ccm_removeHeaderLoading = function() {
	$("a.ccm-nav-loading").removeClass('ccm-nav-loading');
}

parseJSON = function(resp, onNoError) {
	if (resp.error) {
		alert(resp.message);	
	} else {
		onNoError();
	}
}

if (CCM_ARRANGE_MODE) {
	$(ccm_arrangeInit);	
} else if (CCM_EDIT_MODE) {
	$(ccm_editInit);	
} else {
	$(ccm_init);
}

ccm_showBreadcrumb = function() {
/*$("#ccm-bc").animate({
	top: '50x',
	}, {
		duration: 200,
		easing: 'easeInBounce'
	});*/
	$("#ccm-bc").show();
	$("#ccm-bc").css('top', '49px');
}

ccm_hideBreadcrumb = function() {
	/*
	$("#ccm-bc").animate({
	top: '0px',
	}, {
		duration: 200,
		easing: 'easeOutQuad'
	});*/
	$("#ccm-bc").css('top', '0px');
	$("#ccm-bc").hide();
	ccm_bcEnabled = false;

}

ccm_setupBreadcrumb = function() {
	
	if ($("#ccm-bc").get().length > 0) {
		$("#ccm-page-controls").mouseover(function() {
			if (ccm_siteActivated) { 
				if (!ccm_bcEnabled) {
					ccm_showBreadcrumb();
				}
				ccm_bcEnabled = true;
			}
		});
		$("#ccm-bc").mouseover(function() {
			ccm_bcEnabled = true;
			if (ccm_bcEnabledTimer) {
				clearTimeout(ccm_bcEnabledTimer);
				ccm_bcEnabledTimer = false;
			}
		});
		$("#ccm-bc").mouseout(function() {
			ccm_bcEnabled = false;
			ccm_bcEnabledTimer = setTimeout(function() {
				if (!ccm_bcEnabled) {
					ccm_hideBreadcrumb();
				}
			}, 500);
		});
	}
}

$(function() {
	
	if (CCM_ENABLE_BREADCRUMB) {
		ccm_setupBreadcrumb();
	}
	
	b1 = new Image();// preload image
	b1.src = CCM_IMAGE_PATH + "/button_l.png";
	b2 = new Image();// preload image
	b2.src = CCM_IMAGE_PATH + "/button_l_active.png";
	b3 = new Image();// preload image
	b3.src = CCM_IMAGE_PATH + "/button_r.png";
	b4 = new Image();// preload image
	b4.src = CCM_IMAGE_PATH + "/button_r_active.png";
	b5 = new Image();// preload image
	b5.src = CCM_IMAGE_PATH + "/button_scroller_l_active.png";
	b6 = new Image();// preload image
	b6.src = CCM_IMAGE_PATH + "/button_scroller_r_active.png";
	
	// menu assets
	m1 = new Image();// preload image
	m1.src = CCM_IMAGE_PATH + "/bg_menu_rb.png";
	m2 = new Image();// preload image
	m2.src = CCM_IMAGE_PATH + "/bg_menu_b.png";
	m3 = new Image();// preload image
	m3.src = CCM_IMAGE_PATH + "/bg_menu_lb.png";
	m4 = new Image();// preload image
	m4.src = CCM_IMAGE_PATH + "/bg_menu_r.png";
	m5 = new Image();// preload image
	m5.src = CCM_IMAGE_PATH + "/bg_menu_l.png";
	m6 = new Image();// preload image
	m6.src = CCM_IMAGE_PATH + "/bg_menu_rt.png";
	m7 = new Image();// preload image
	m7.src = CCM_IMAGE_PATH + "/bg_menu_t.png";
	m8 = new Image();// preload image
	m8.src = CCM_IMAGE_PATH + "/bg_menu_lt.png";

	if ($.browser.msie) {
		ccm_animEffects = false;
	}

});