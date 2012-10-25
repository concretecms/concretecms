/** 
 * Remote Marketplace
 */

ccm_openThemeLauncher = function() {
	jQuery.fn.dialog.closeTop();
	jQuery.fn.dialog.showLoader();
	ccm_testMarketplaceConnection(function() {
		$.fn.dialog.open({
			title: ccmi18n.community,
			href:  CCM_TOOLS_PATH + '/marketplace/themes',
			width: '905',
			modal: false,
			height: '410'
		});
	}, 'open_theme_launcher');
}

ccm_testMarketplaceConnection = function(onComplete, task, mpID) {
	if (mpID) {
		mpIDStr = '&mpID=' + mpID;
	} else {
		mpIDStr = '';
	}
	
	if (!task) {
		task = '';
	}
	
	params = {'mpID': mpID};

	
	$.getJSON(CCM_TOOLS_PATH + '/marketplace/connect', params, function(resp) {
		if (resp.isConnected) {
			onComplete();
		} else {
			$.fn.dialog.open({
				title: ccmi18n.community,
				href:  CCM_TOOLS_PATH + '/marketplace/frame?task=' + task + mpIDStr,
				width: '90%',
				modal: false,
				height: '70%'
			});
			return false;
		}
	});
}

ccm_openAddonLauncher = function() {
	jQuery.fn.dialog.closeTop();
	jQuery.fn.dialog.showLoader();
	ccm_testMarketplaceConnection(function() {
		$.fn.dialog.open({
			title: ccmi18n.community,
			href:  CCM_TOOLS_PATH + '/marketplace/add-ons',
			width: '905',
			modal: false,
			height: '410'
		});
	}, 'open_addon_launcher');
}

ccm_setupMarketplaceDialogForm = function() {
	$(".ccm-pane-dialog-pagination").each(function() {
		$(this).closest('.ui-dialog-content').dialog('option', 'buttons', [{}]);
		$(this).closest('.ui-dialog').find('.ui-dialog-buttonpane .ccm-pane-dialog-pagination').remove();
		$(this).appendTo($(this).closest('.ui-dialog').find('.ui-dialog-buttonpane').addClass('ccm-ui'));
	});
	$('.ccm-pane-dialog-pagination a').click(function() {
		jQuery.fn.dialog.showLoader();
		$('#ccm-marketplace-browser-form').closest('.ui-dialog-content').load($(this).attr('href'), function() {
			jQuery.fn.dialog.hideLoader();
		});
		return false;
	});
	ccm_marketplaceBrowserInit(); 
	$("#ccm-marketplace-browser-form").ajaxForm({
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader();
		},
		success: function(resp) {
			jQuery.fn.dialog.hideLoader();
			$('#ccm-marketplace-browser-form').closest('.ui-dialog-content').html(resp);
		}
	});		
}

ccm_marketplaceBrowserInit = function() {
	$(".ccm-marketplace-item").click(function() {
		ccm_getMarketplaceItemDetails($(this).attr('mpID'));
	});
	
	$(".ccm-marketplace-item-thumbnail").mouseover(function() {
		var img = $(this).parent().find('div.ccm-marketplace-results-image-hover').clone().addClass('ccm-marketplace-results-image-hover-displayed').appendTo(document.body);
		var t = $(this).offset().top;
		var l = $(this).offset().left;
		l = l + 60;
		img.css('top', t).css('left', l);
		img.show();
	});
	
	$(".ccm-marketplace-item-thumbnail").mouseout(function() {
		$('.ccm-marketplace-results-image-hover-displayed').hide().remove();
	});
}

ccm_getMarketplaceItemDetails = function(mpID) {
	jQuery.fn.dialog.showLoader();
	$("#ccm-intelligent-search-results").hide();
	ccm_testMarketplaceConnection(function() {
		$.fn.dialog.open({
			title: ccmi18n.community,
			href:  CCM_TOOLS_PATH + '/marketplace/details?mpID=' + mpID,
			width: 820,
			appendButtons: true,
			modal: false,
			height: 640
		});
	}, 'get_item_details', mpID);
}

ccm_getMarketplaceItem = function(args) {
	var mpID = args.mpID;
	var closeTop = args.closeTop;
	
	this.onComplete = function() { }

	if (args.onComplete) {
		ccm_getMarketplaceItem.onComplete = args.onComplete;
	}
	
	if (closeTop) {
		jQuery.fn.dialog.closeTop(); // this is here due to a weird safari behavior
	}
	jQuery.fn.dialog.showLoader();
	// first, we check our local install to ensure that we're connected to the
	// marketplace, etc..
	params = {'mpID': mpID};
	$.getJSON(CCM_TOOLS_PATH + '/marketplace/connect', params, function(resp) {
		jQuery.fn.dialog.hideLoader();
		if (resp.isConnected) {
			if (!resp.purchaseRequired) {
				$.fn.dialog.open({
					title: ccmi18n.community,
					href:  CCM_TOOLS_PATH + '/marketplace/download?install=1&mpID=' + mpID,
					width: 500,
					appendButtons: true,
					modal: false,
					height: 400
				});
			} else {
				$.fn.dialog.open({
					title: ccmi18n.communityCheckout,
					iframe: true,
					href:  CCM_TOOLS_PATH + '/marketplace/checkout?mpID=' + mpID,
					width: '560px',
					modal: false,
					height: '400px'
				});
			}

		} else {
			$.fn.dialog.open({
				title: ccmi18n.community,
				href:  CCM_TOOLS_PATH + '/marketplace/frame?task=get&mpID=' + mpID,
				width: '90%',
				modal: false,
				height: '70%'
			});
		}
	});
}