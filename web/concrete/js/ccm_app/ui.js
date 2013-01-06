

/** 
 * UI
 */

var ccm_arrangeMode = false;
var ccm_inlineEditMode = false;
var ccm_selectedDomID = false;
var ccm_isBlockError = false;
var ccm_activeMenu = false;
var ccm_blockError = false;

ccm_reloadAreaMenuPermissions = function(aID, cID) {
    var config = window['ccm_areaMenuObj' + aID];
    if (config) {
		var action = CCM_TOOLS_PATH + '/reload_area_permissions_js.php' + 
		'?arHandle=' + config.arHandle +
		'&cID=' + cID +
		'&maximumBlocks=' + config.maximumBlocks;
		$.getScript( action );
	}
}

ccm_openAreaAddBlock = function(arHandle, addOnly, cID) {
	if (!addOnly) {	
		addOnly = 0;
	}
	
	if (!cID) {
		cID = CCM_CID;
	}
	
	$.fn.dialog.open({
		title: ccmi18n.blockAreaMenu,
		href: CCM_TOOLS_PATH + '/edit_area_popup.php?cID=' + cID + '&atask=add&arHandle=' + arHandle + '&addOnly=' + addOnly,
		width: 550,
		modal: false,
		height: 380
	});
}


ccm_hideHighlighter = function() {

}

ccm_addError = function(err) {
	if (!ccm_isBlockError) {
		ccm_blockError = '';
		ccm_blockError += '<ul>';
	}
	
	ccm_isBlockError = true;
	ccm_blockError += "<li>" + err + "</li>";;
}

ccm_resetBlockErrors = function() {
	ccm_isBlockError = false;
	ccm_blockError = "";
}

ccm_addToScrapbook = function(cID, bID, arHandle) {
	ccm_mainNavDisableDirectExit();
	// got to grab the message too, eventually
	ccm_hideHighlighter();
	$.ajax({
	type: 'POST',
	url: CCM_TOOLS_PATH + '/pile_manager.php',
	data: 'cID=' + cID + '&bID=' + bID + '&arHandle=' + arHandle + '&btask=add&scrapbookName=userScrapbook',
	success: function(resp) {
		ccm_hideHighlighter();
		ccmAlert.hud(ccmi18n.copyBlockToScrapbookMsg, 2000, 'add', ccmi18n.copyBlockToScrapbook);
	}});		

}

ccm_deleteBlock = function(cID, bID, aID, arHandle, msg) {
	if (confirm(msg)) {
		ccm_mainNavDisableDirectExit();
		// got to grab the message too, eventually
		ccm_hideHighlighter();
		$d = $("#b" + bID + '-' + aID);
		$d.hide().remove();
		ccmAlert.hud(ccmi18n.deleteBlockMsg, 2000, 'delete_small', ccmi18n.deleteBlock);
		$.ajax({
			type: 'POST',
			url: CCM_DISPATCHER_FILENAME,
			data: 'cID=' + cID + '&ccm_token=' + CCM_SECURITY_TOKEN + '&isAjax=true&btask=remove&bID=' + bID + '&arHandle=' + arHandle
		});
		ccm_reloadAreaMenuPermissions(aID, cID);
	}	
}

ccm_hideMenus = function() {
	/* 1st, hide all items w/the css menu class */
	ccm_activeMenu = false;
	$("div.ccm-menu").hide();
	$("div.ccm-menu").css('visibility', 'hidden');
	$("div.ccm-menu").show();
}

ccm_parseBlockResponse = function(r, currentBlockID, task) {
	try { 
		r = r.replace(/(<([^>]+)>)/ig,""); // because some plugins add bogus HTML after our JSON requests and screw everything up
		resp = eval('(' + r + ')');
		if (resp.error == true) {
			var message = '<ul>'
			for (i = 0; i < resp.response.length; i++) {						
				message += '<li>' + resp.response[i] + '<\/li>';
			}
			message += '<\/ul>';
			ccmAlert.notice(ccmi18n.error, message);
		} else {
			ccm_blockWindowClose();
			if (resp.cID) {
				cID = resp.cID; 
			} else {
				cID = CCM_CID;
			}
			var action = CCM_TOOLS_PATH + '/edit_block_popup?cID=' + cID + '&bID=' + resp.bID + '&arHandle=' + encodeURIComponent(resp.arHandle) + '&btask=view_edit_mode';	 
			$.get(action, 		
				function(r) { 
					if (task == 'add') {
						if ($("#a" + resp.aID + " div.ccm-area-styles-a"+ resp.aID).length > 0) {
							$("#a" + resp.aID + " div.ccm-area-styles-a"+ resp.aID).append(r);
						} else {
							$("#a" + resp.aID).append(r);
						}
						// inline support.
						$('#a' + resp.aID + '-bt' + resp.btID).remove();
					} else {
						$('#b' + currentBlockID + '-' + resp.aID).before(r).remove();
					}
					ccm_editMenuInit();
					ccm_exitInlineEditMode();
					if (task == 'add') {
						ccmAlert.hud(ccmi18n.addBlockMsg, 2000, 'add', ccmi18n.addBlock);
						jQuery.fn.dialog.closeAll();
					} else {
						ccmAlert.hud(ccmi18n.updateBlockMsg, 2000, 'success', ccmi18n.updateBlock);
					}
					if (typeof window.ccm_parseBlockResponsePost == 'function') {
						ccm_parseBlockResponsePost(resp);
					}
				}
			);
			ccm_reloadAreaMenuPermissions(resp.aID, cID);
		}
	} catch(e) { 
		ccmAlert.notice(ccmi18n.error, r); 
	}
}

ccm_mainNavDisableDirectExit = function(disableShow) {
	// make sure that exit edit mode is enabled
	$("#ccm-exit-edit-mode-direct").hide();
	if (!disableShow) {
		$("#ccm-exit-edit-mode-comment").show();
	}
}

ccm_setupBlockForm = function(form, currentBlockID, task) {
	form.ajaxForm({
		type: 'POST',
		iframe: true,
		beforeSubmit: function() {
			ccm_hideHighlighter();
			$('input[name=ccm-block-form-method]').val('AJAX');
			jQuery.fn.dialog.showLoader();
			return ccm_blockFormSubmit();
		},
		success: function(r) {
			ccm_parseBlockResponse(r, currentBlockID, task);
		}
	});
	
}



ccm_activate = function(obj, domID) { 
	if (ccm_arrangeMode || ccm_activeMenu || ccm_inlineEditMode) {
		return false;
	}
	

	
	if (ccm_selectedDomID) {
		$(ccm_selectedDomID).removeClass('ccm-menu-hotspot-active');
	}
	
	aobj = $(domID);
	aobj.addClass('ccm-menu-hotspot-active');
	ccm_selectedDomID = domID;
	
	offs = aobj.offset();

	
	$("#ccm-highlighter").css("width", aobj.outerWidth());
	$("#ccm-highlighter").css("height", aobj.outerHeight());
	$("#ccm-highlighter").css("top", offs.top);
	$("#ccm-highlighter").css("left", offs.left);
	$("#ccm-highlighter").fadeIn(120, 'easeOutExpo');
	/*
	$("#ccmMenuHighlighter").mouseover(
		function() {clearTimeout(ccm_deactivateTimer)}
	);
	*/
	$("#ccm-highlighter").mouseout(function(e) {
		if (!ccm_activeMenu) {
			if (!e.target) {
				ccm_hideHighlighter();
			} else if ($(e.toElement).parents('div.ccm-menu').length == 0) {
				ccm_hideHighlighter();
			}
		}
	});
	
	$("#ccm-highlighter").unbind('click');
	$("#ccm-highlighter").click(
		function(e) {
			switch(obj.type) {
				case "BLOCK":
					ccm_showBlockMenu(obj, e);
					break;
			}
		}
	);
}

ccm_editInit = function() {

	document.write = function() {
		// stupid javascript in html blocks
		void(0);
	}

	ccm_editMenuInit();
}

ccm_editMenuInit = function() {
	$('a.dialog-launch').dialog();
	$('.ccm-area-footer,.ccm-block-edit,.ccm-block-edit-layout').ccmmenu();
}

ccm_triggerSelectUser = function(uID, uName, uEmail) {
	alert(uID);
	alert(uName);
	alert(uEmail);
}

ccm_setupUserSearch = function(searchInstance) {
	$(".chosen-select").chosen();	
	
	$("#ccm-user-list-cb-all").click(function() {
		if ($(this).prop('checked') == true) {
			$('.ccm-list-record td.ccm-user-list-cb input[type=checkbox]').attr('checked', true);
			$("#ccm-user-list-multiple-operations").attr('disabled', false);
		} else {
			$('.ccm-list-record td.ccm-user-list-cb input[type=checkbox]').attr('checked', false);
			$("#ccm-user-list-multiple-operations").attr('disabled', true);
		}
	});
	$("td.ccm-user-list-cb input[type=checkbox]").click(function(e) {
		if ($("td.ccm-user-list-cb input[type=checkbox]:checked").length > 0) {
			$("#ccm-user-list-multiple-operations").attr('disabled', false);
		} else {
			$("#ccm-user-list-multiple-operations").attr('disabled', true);
		}
	});
	
	// if we're not in the dashboard, add to the multiple operations select menu

	$("#ccm-user-list-multiple-operations").change(function() {
		var action = $(this).val();
		switch(action) {
			case 'choose':
				var idstr = '';
				$("td.ccm-user-list-cb input[type=checkbox]:checked").each(function() {
					ccm_triggerSelectUser($(this).val(), $(this).attr('user-name'), $(this).attr('user-email'));
				});
				jQuery.fn.dialog.closeTop();
				break;
			case "properties": 
				uIDstring = '';
				$("td.ccm-user-list-cb input[type=checkbox]:checked").each(function() {
					uIDstring=uIDstring+'&uID[]='+$(this).val();
				});
				jQuery.fn.dialog.open({
					width: 630,
					height: 450,
					modal: false,
					href: CCM_TOOLS_PATH + '/users/bulk_properties?' + uIDstring,
					title: ccmi18n.properties				
				});
				break;
			case "activate": 
				uIDstring = '';
				$("td.ccm-user-list-cb input[type=checkbox]:checked").each(function() {
					uIDstring=uIDstring+'&uID[]='+$(this).val();
				});
				jQuery.fn.dialog.open({
					width: 630,
					height: 450,
					modal: false,
					href: CCM_TOOLS_PATH + '/users/bulk_activate?searchInstance='+ searchInstance + '&' + uIDstring,
					title: ccmi18n.user_activate				
				});
				break;
			case "deactivate": 
				uIDstring = '';
				$("td.ccm-user-list-cb input[type=checkbox]:checked").each(function() {
					uIDstring=uIDstring+'&uID[]='+$(this).val();
				});
				jQuery.fn.dialog.open({
					width: 630,
					height: 450,
					modal: false,
					href: CCM_TOOLS_PATH + '/users/bulk_deactivate?searchInstance='+ searchInstance + '&' + uIDstring,
					title: ccmi18n.user_deactivate
				});
				break;
			case "group_add": 
				uIDstring = '';
				$("td.ccm-user-list-cb input[type=checkbox]:checked").each(function() {
					uIDstring=uIDstring+'&uID[]='+$(this).val();
				});
				jQuery.fn.dialog.open({
					width: 630,
					height: 450,
					modal: false,
					href: CCM_TOOLS_PATH + '/users/bulk_group_add?searchInstance='+ searchInstance + '&' + uIDstring,
					title: ccmi18n.user_group_add		
				});
				break;
			case "group_remove": 
				uIDstring = '';
				$("td.ccm-user-list-cb input[type=checkbox]:checked").each(function() {
					uIDstring=uIDstring+'&uID[]='+$(this).val();
				});
				jQuery.fn.dialog.open({
					width: 630,
					height: 450,
					modal: false,
					href: CCM_TOOLS_PATH + '/users/bulk_group_remove?searchInstance='+ searchInstance + '&' + uIDstring,
					title: ccmi18n.user_group_remove				
				});
				break;
			case "delete": 
				uIDstring = '';
				$("td.ccm-user-list-cb input[type=checkbox]:checked").each(function() {
					uIDstring=uIDstring+'&uID[]='+$(this).val();
				});
				jQuery.fn.dialog.open({
					width: 630,
					height: 450,
					modal: false,
					href: CCM_TOOLS_PATH + '/users/bulk_delete?searchInstance='+ searchInstance + '&' + uIDstring,
					title: ccmi18n.user_delete				
				});
				break;
		}
		
		$(this).get(0).selectedIndex = 0;
	});


}

ccm_triggerSelectGroup = function(gID, gName) {
	alert(gID);
	alert(gName);
}

ccm_setupGroupSearchPaging = function() {
	$("div#ccm-group-paging").each(function() {
		$(this).closest('.ui-dialog-content').dialog('option', 'buttons', [{}]);
		$(this).closest('.ui-dialog').find('.ui-dialog-buttonpane .ccm-pane-dialog-pagination').remove();
		$(this).appendTo($(this).closest('.ui-dialog').find('.ui-dialog-buttonpane').addClass('ccm-ui'));
	});
}

ccm_setupGroupSearch = function(callback) {
	$('div.ccm-group a').unbind();
	if (callback) {
		func = window[callback];
	} else {
		func = ccm_triggerSelectGroup;
	}

	$('div.ccm-group a').each(function(i) {
		var gla = $(this);
		$(this).click(function() {
			func(gla.attr('group-id'), gla.attr('group-name'));
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
	ccm_setupGroupSearchPaging();
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

ccm_saveArrangement = function(cID) {
	
	if (!cID) {
		cID = CCM_CID;
	}

	ccm_mainNavDisableDirectExit();
	var serial = '';
	$('div.ccm-area').each(function() {
		areaStr = '&area[' + $(this).attr('id').substring(1) + '][]=';
		
		bArray = $(this).sortable('toArray');

		for (i = 0; i < bArray.length; i++ ) {
			if (bArray[i] != '' && bArray[i].substring(0, 1) == 'b') {
				// make sure to only go from b to -, meaning b28-9 becomes "28"
				var bID = bArray[i].substring(1, bArray[i].indexOf('-'));
				var bObj = $('#' + bArray[i]);
				if (bObj.attr('custom-style')) {
					bID += '-' + bObj.attr('custom-style');
				}
				serial += areaStr + bID;
			}
		}
	});

 	$.ajax({
 		type: 'POST',
 		url: CCM_DISPATCHER_FILENAME,
 		data: 'cID=' + cID + '&ccm_token=' + CCM_SECURITY_TOKEN + '&btask=ajax_do_arrange' + serial,
 		success: function(msg) {
 			$("div.ccm-area").removeClass('ccm-move-mode');
			$('div.ccm-block-arrange').each(function() {
				$(this).addClass('ccm-block');
				$(this).removeClass('ccm-block-arrange');
			});
			ccm_arrangeMode = false;
			$(".ccm-main-nav-edit-option").fadeIn(300);
			ccmAlert.hud(ccmi18n.arrangeBlockMsg, 2000, 'up_down', ccmi18n.arrangeBlock);
 		}});
}

ccm_arrangeInit = function() {
	//$(document.body).append('<img src="' + CCM_IMAGE_PATH + '/topbar_throbber.gif" width="16" height="16" id="ccm-topbar-loader" />');
	
	ccm_arrangeMode = true;
	
	ccm_hideHighlighter();
	
	$('div.ccm-block').each(function() {
		$(this).addClass('ccm-block-arrange');
		$(this).removeClass('ccm-block');
	});
	
	$(".ccm-main-nav-edit-option").fadeOut(300, function() {
		$(".ccm-main-nav-arrange-option").fadeIn(300);
	});
	
	$("div.ccm-area").each(function() {
		var cID = $(this).attr('cID');
		$(this).addClass('ccm-move-mode');
		$(this).sortable({
			items: 'div.ccm-block-arrange',
			connectWith: $("div.ccm-area-move-enabled"),
			accept: 'div.ccm-block-arrange',
			opacity: 0.5,
			stop: function() {
				ccm_saveArrangement(cID);
			}
		});
	});
}

if (typeof(ccm_selectSitemapNode) != 'function') {
	ccm_selectSitemapNode = function(cID, cName) {
		alert(cID);
		alert(cName);
	}
}

ccm_goToSitemapNode = function(cID, cName) {
	window.location.href= CCM_DISPATCHER_FILENAME + '?cID=' + cID;
}

ccm_fadeInMenu = function(bobj, e) {
	var mwidth = bobj.find('div.popover div.inner').width();
	var mheight = bobj.find('div.popover').height();
	bobj.hide();
	bobj.css('visibility', 'visible');
	
	var posX = e.pageX + 2;
	var posY = e.pageY + 2;

	if ($(window).height() < e.clientY + mheight) {
		posY = posY - mheight - 10;
		posX = posX - (mwidth / 2);
		bobj.find('div.popover').removeClass('below');
		bobj.find('div.popover').addClass('above');
	} else {
		posX = posX - (mwidth / 2);
		posY = posY + 10;
		bobj.find('div.popover').removeClass('above');
		bobj.find('div.popover').addClass('below');
	}	
	
	bobj.css("top", posY + "px");
	bobj.css("left", posX + "px");
	bobj.fadeIn(60);
	
}

ccm_blockWindowClose = function() {
	jQuery.fn.dialog.closeTop();
	ccm_blockWindowAfterClose();
}

ccm_blockWindowAfterClose = function() {
	ccmValidateBlockForm = function() {return true;}
}

ccm_blockFormSubmit = function() {
	if (typeof window.ccmValidateBlockForm == 'function') {
		r = window.ccmValidateBlockForm();
		if (ccm_isBlockError) {
			jQuery.fn.dialog.hideLoader();
			if(ccm_blockError) {
				ccmAlert.notice(ccmi18n.error, ccm_blockError + '</ul>');
			}
			ccm_resetBlockErrors();
			return false;
		}
	}
	return true;
}

ccm_paneToggleOptions = function(obj) {
	var pane = $(obj).parent().find('div.ccm-pane-options-content');
	if ($(obj).hasClass('ccm-icon-option-closed')) {
		$(obj).removeClass('ccm-icon-option-closed').addClass('ccm-icon-option-open');
		pane.slideDown('fast', 'easeOutExpo');
	} else {
		$(obj).removeClass('ccm-icon-option-open').addClass('ccm-icon-option-closed');
		pane.slideUp('fast', 'easeOutExpo');
	}
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

/** 
 * JavaScript localization. Provide a key and then reference that key in PHP somewhere (where it will be translated)
 */
ccm_t = function(key) {
	return $("input[name=ccm-string-" + key + "]").val();
}

/* Block Styles Customization Popup */
var ccmCustomStyle = {   
	tabs:function(aLink,tab){
		$('.ccm-styleEditPane').hide();
		$('#ccm-styleEditPane-'+tab).show();
		$(aLink.parentNode.parentNode).find('li').removeClass('ccm-nav-active');
		$(aLink.parentNode).addClass('ccm-nav-active');
		return false;
	},
	resetAll:function(){
		if (!confirm( ccmi18n.confirmCssReset)) {  
			return false;
		}
		jQuery.fn.dialog.showLoader();

		$('#ccm-reset-style').val(1);
		$('#ccmCustomCssForm').get(0).submit();
		return true;
	},
	showPresetDeleteIcon: function() {
		if ($('select[name=cspID]').val() > 0) {
			$("#ccm-style-delete-preset").show();		
		} else {
			$("#ccm-style-delete-preset").hide();
		}	
	},
	deletePreset: function() {
		var cspID = $('select[name=cspID]').val();
		if (cspID > 0) {
			
			if( !confirm(ccmi18n.confirmCssPresetDelete) ) return false;
			
			var action = $('#ccm-custom-style-refresh-action').val() + '&deleteCspID=' + cspID + '&subtask=delete_custom_style_preset';
			jQuery.fn.dialog.showLoader();
			
			$.get(action, function(r) {
				$("#ccm-custom-style-wrapper").html(r);
				jQuery.fn.dialog.hideLoader();
			});
		}
	},
	initForm: function() {
		if ($("#cspFooterPreset").length > 0) {
			$("#ccmCustomCssFormTabs input, #ccmCustomCssFormTabs select, #ccmCustomCssFormTabs textarea").bind('change click', function() {
				$("#cspFooterPreset").show();
				$("#cspFooterNoPreset").remove();
				$("#ccmCustomCssFormTabs input, #ccmCustomCssFormTabs select").unbind('change click');
			});		
		}
		$('input[name=cspPresetAction]').click(function() {
			if ($(this).val() == 'create_new_preset' && $(this).prop('checked')) {
				$('input[name=cspName]').attr('disabled', false).focus();
			} else { 
				$('input[name=cspName]').val('').attr('disabled', true); 
			}
		});
		ccmCustomStyle.showPresetDeleteIcon();
		
		ccmCustomStyle.lastPresetID=parseInt($('select[name=cspID]').val());
		
		$('select[name=cspID]').change(function(){ 
			var cspID = parseInt($(this).val());
			var selectedCsrID = parseInt($('input[name=selectedCsrID]').val());
			
			if(ccmCustomStyle.lastPresetID==cspID) return false;
			ccmCustomStyle.lastPresetID=cspID;
			
			jQuery.fn.dialog.showLoader();
			if (cspID > 0) {
				var action = $('#ccm-custom-style-refresh-action').val() + '&cspID=' + cspID;
			} else {
				var action = $('#ccm-custom-style-refresh-action').val() + '&csrID=' + selectedCsrID;
			}
			
			
			$.get(action, function(r) {
				$("#ccm-custom-style-wrapper").html(r);
				jQuery.fn.dialog.hideLoader();
			});
			
		});
		
		$('#ccmCustomCssForm').submit(function() {
			if ($('input[name=cspCreateNew]').prop('checked') == true) {
				if ($('input[name=cspName]').val() == '') { 
					$('input[name=cspName]').focus();
					alert(ccmi18n.errorCustomStylePresetNoName);
					return false;
				}
			}

			jQuery.fn.dialog.showLoader();		
			return true;
		});
		
		//IE bug fix 0 can't focus on txt fields if new block just added 
		if(!parseInt(ccmCustomStyle.lastPresetID))  
			setTimeout('$("#ccmCustomCssFormTabs input").attr("disabled", false).get(0).focus()',500);
	},
	validIdCheck:function(el,prevID){
		var selEl = $('#'+el.value); 
		if( selEl && selEl.get(0) && selEl.get(0).id!=prevID ){		
			$('#ccm-styles-invalid-id').css('display','block');
		}else{
			$('#ccm-styles-invalid-id').css('display','none');
		}
	}
};

