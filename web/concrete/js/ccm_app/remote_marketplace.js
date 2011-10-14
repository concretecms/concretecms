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
					width: 350,
					modal: false,
					height: 240
				});
			} else {
				$.fn.dialog.open({
					title: ccmi18n.community,
					iframe: true,
					href:  CCM_TOOLS_PATH + '/marketplace/checkout?mpID=' + mpID,
					width: '90%',
					modal: false,
					height: '70%'
				});
			}

		} else {
			$.fn.dialog.open({
				title: ccmi18n.community,
				href:  CCM_TOOLS_PATH + '/marketplace/frame?mpID=' + mpID,
				width: '90%',
				modal: false,
				height: '70%'
			});
		}
	});
}