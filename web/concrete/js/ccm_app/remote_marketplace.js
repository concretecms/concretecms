ccm_marketplaceLauncherOpenPost = function() {

	jQuery.fn.dialog.hideLoader();
	ccm_setNewsflowOverlayDimensions();
	$("#newsflow-overlay").css('top', '90px').fadeIn('300', 'easeOutExpo');
	$(".ccm-pagination a").click(function() {
		jQuery.fn.dialog.showLoader(false);
		$('#newsflow-overlay').load($(this).attr('href'), function() {
			ccm_marketplaceLauncherOpenPost();			
		});
		return false;
	});
	$("#ccm-marketplace-browser-form").ajaxForm({
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader(false);
		},
		success: function(r) {
			$('#newsflow-overlay').html(r);
			ccm_marketplaceLauncherOpenPost();
		}
	});
}

ccm_openThemeLauncher = function(mpID) {
	jQuery.fn.dialog.closeTop();
	
	$(window).resize(function(){
		ccm_setNewsflowOverlayDimensions();
	});

	var $overlay = $('<div class="ui-widget-overlay"></div>').hide().appendTo('body');
	$('.ui-widget-overlay').show();
	jQuery.fn.dialog.showLoader(ccmi18n.themeBrowserLoading);
	var mpIDstr = '';
	if (mpID) {
		mpIDstr = '&mpID=' + mpID;
	}
	$('<div />').attr('id', 'newsflow-overlay').attr('class', 'ccm-ui').css('display','none').appendTo(document.body).load(CCM_DISPATCHER_FILENAME + '/dashboard/extend/themes?_ccm_dashboard_external=1' + mpIDstr, function() {
		ccm_marketplaceLauncherOpenPost();
	});
}

ccm_openAddonLauncher = function(mpID) {
	$("#ccm-nav-intelligent-search").val('');
	$("#ccm-intelligent-search-results").fadeOut(90, 'easeOutExpo');

	jQuery.fn.dialog.closeTop();
	
	$(window).resize(function(){
		ccm_setNewsflowOverlayDimensions();
	});

	var $overlay = $('<div class="ui-widget-overlay"></div>').hide().appendTo('body');
	$('.ui-widget-overlay').show();
	jQuery.fn.dialog.showLoader(ccmi18n.addonBrowserLoading);	
	var mpIDstr = '';
	if (mpID) {
		mpIDstr = '&mpID=' + mpID;
	}
	$('<div />').attr('id', 'newsflow-overlay').attr('class', 'ccm-ui').css('display','none').appendTo(document.body).load(CCM_DISPATCHER_FILENAME + '/dashboard/extend/add-ons?_ccm_dashboard_external=1' + mpIDstr, function() {
		ccm_marketplaceLauncherOpenPost();
	});
}
ccm_marketplaceBrowserInit = function(mpID, autoSelect) {
	
	$(".ccm-marketplace-item").click(function() {
		window.scrollTo(0,0);
		$("#newsflow-paging-previous").hide();
		$("#newsflow-paging-next").hide();
		$("#ccm-marketplace-detail-inner").hide();
		$('.ccm-marketplace-detail-loading').show();	

		var mpID = $(this).attr('mpID');
		$('.ccm-marketplace-item-selected').removeClass('ccm-marketplace-item-selected').addClass('ccm-marketplace-item-unselected');
		$(this).removeClass('ccm-marketplace-item-unselected').addClass('ccm-marketplace-item-selected');
		$('#ccm-marketplace-detail').show();
		$('#ccm-marketplace-detail-inner').load(CCM_TOOLS_PATH + '/marketplace/details', {
			'mpID': mpID
		}, function() {
			ccm_marketplaceGetDetailPost();
		});
	});

	if (mpID) {
		$("#ccm-marketplace-detail-inner").hide();
		$('.ccm-marketplace-detail-loading').show();	
		$('#ccm-marketplace-detail').show();
		$('#ccm-marketplace-detail-inner').load(CCM_TOOLS_PATH + '/marketplace/details', {
			'mpID': mpID
		}, function() {
			ccm_marketplaceGetDetailPost();
		});
	} else {
		if (autoSelect == 'last') { 
			$("div.ccm-marketplace-results-info").last().parent().click();
		} else {
			$("div.ccm-marketplace-results-info").first().parent().click();
		}
	}
}

ccm_marketplaceBrowserSelectPrevious = function() {
	var items = $('.ccm-marketplace-item');
	var doSelect = false;
	var foundSomething = false;
	$(items.get().reverse()).each(function() {
		if (doSelect) {
			$(this).click();
			doSelect = false;
			foundSomething = true;
		} else { 
			if ($(this).hasClass('ccm-marketplace-item-selected')) {
				doSelect = true;
			}
		}
	});
	if (!foundSomething) {
		var href = $("#ccm-marketplace-browse-footer .ccm-page-left a").first().attr('href');
		href = href + '&prev=1';
		if ($('#newsflow').length > 0) { 
			jQuery.fn.dialog.showLoader(false);
			$('#newsflow-overlay').load(href, function() {
				ccm_marketplaceLauncherOpenPost();			
			});
		} else { 
			window.location.href = href;
		}
	}
}

ccm_marketplaceBrowserSelectNext = function() {
	var items = $('.ccm-marketplace-item');
	var doSelect = false;
	var foundSomething = false;
	items.each(function() {
		if (doSelect) {
			$(this).click();
			doSelect = false;
			foundSomething = true;
		} else { 
			if ($(this).hasClass('ccm-marketplace-item-selected')) {
				doSelect = true;
			}
		}
	});
	
	// if we make it down here...
	if (!foundSomething) {
		var href = $("#ccm-marketplace-browse-footer .ccm-page-right a").first().attr('href');
		if ($('#newsflow').length > 0) { 
			jQuery.fn.dialog.showLoader(false);
			$('#newsflow-overlay').load(href, function() {
				ccm_marketplaceLauncherOpenPost();			
			});
		} else { 
			window.location.href = href;
		}
	}
}

ccm_marketplaceBrowserSetupNextAndPrevious = function() {

	if ($('.ccm-marketplace-item-selected').attr('mpID') == $('.ccm-marketplace-item').first().attr('mpID') 
	&& $('#ccm-marketplace-browse-footer span.ccm-page-left a').length == 0) { 
		$("#newsflow-paging-previous").hide();
	} else {
		$("#newsflow-paging-previous").show();
	}

	if ($('.ccm-marketplace-item-selected').attr('mpID') == $('.ccm-marketplace-item').last().attr('mpID')
	&& $('#ccm-marketplace-browse-footer span.ccm-page-right a').length == 0) { 
		$("#newsflow-paging-next").hide();
	} else {
		$("#newsflow-paging-next").show();
	}
	
}


ccm_marketplaceGetDetailPost = function() {
	var h = $('#ccm-marketplace-detail').height();
	h = h + 40;
	$("#newsflow-paging-previous span, #newsflow-paging-next span").css('height', h + 'px');
	$("#newsflow-paging-previous, #newsflow-paging-next").css('height', h + 'px');
	$('.ccm-marketplace-detail-loading').hide();
	$("#ccm-marketplace-detail-inner").show();
	if ($(".ccm-marketplace-item-information-inner").height() < 325) {
		$(".ccm-marketplace-item-information-more").hide();
	}
	$("#ccm-marketplace-item-screenshots").nivoSlider({
		'controlNav': false,
		'effect': 'fade',
		'pauseOnHover': false,
		'directionNav': false
	});
	ccm_marketplaceBrowserSetupNextAndPrevious();
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