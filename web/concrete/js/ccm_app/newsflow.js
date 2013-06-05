ccm_closeNewsflow = function(r) {
	$ovl = ccm_getNewsflowOverlayWindow();
	$ovl.fadeOut(300, 'easeOutExpo');
	$('.ui-widget-overlay').fadeOut(300, 'easeOutExpo', function() {
		$(this).remove();
	});
}

ccm_setNewsflowPagingArrowHeight = function() {
	if ($("#ccm-marketplace-detail").length > 0) {
		var $ovl = $("#ccm-marketplace-detail");
	} else { 
		var $ovl = $("#newsflow-main");
	}
	
	var h = $ovl.height();
	$(".newsflow-paging-previous a, .newsflow-paging-next a").css('height', h + 'px');
	$(".newsflow-paging-previous, .newsflow-paging-next").css('height', h + 'px');
	$(".newsflow-paging-next").show();
	$(".newsflow-paging-previous").show();

}

ccm_setNewsflowOverlayDimensions = function() {
	if ($("#newsflow-overlay").length > 0) { 
		var w = $("#newsflow-overlay").width();
		var tw = $(window).width();
		var th = $(window).height();
		
		var optimalHeight = 650;
		var availableSpace = th - 80;
		
		// we use h strictly for the _top param below
		
		if (availableSpace > optimalHeight) {
			h = optimalHeight;
		} else {
			h = availableSpace;
		}		
		$("#newsflow-overlay").css('height', optimalHeight);

		var _left = (tw - w) / 2;
		var _top = (th - h) / 2;
		_top = _top + 29; // handle the top toolbar
		_left = _left + "px";
		_top = _top + "px";
		$("#newsflow-overlay").css('left', _left).css('top', _top);
	}
}

ccm_getNewsflowOverlayWindow = function() {
	if ($('#ccm-dashboard-content').length > 0 && $("#newsflow-main").length > 0 && $("#newsflow-overlay").length == 0) {
		var $ovl = $("#newsflow-main").parent();
	} else {
		// Ok. we're going to use #newsflow-overlay but we don't know if it's been added to the page yet
		if ($("#newsflow-overlay").length > 0) {
			var $ovl = $("#newsflow-overlay");
		} else {
			var $ovl = $('<div />').attr('id', 'newsflow-overlay').attr('class', 'ccm-ui').css('display','none').appendTo(document.body);
		}
	}
	return $ovl;
}
/** 
 * Newsflow
 */

ccm_showNewsflowOverlayWindow = function(url, callback) {
	
	// if we're NOT showing newsflow on a dashboard page, we load an overlay
	if ($('#ccm-dashboard-content').length > 0 && $("#newsflow-main").length > 0) {
	
	} else {
		if ($('.ui-widget-overlay').length < 1) {
			var $overlay = $('<div class="ui-widget-overlay"></div>').hide().appendTo('body');
		}
		$('.ui-widget-overlay').show();
	}

	// Make the overlay resize when a browser window is resized
	$(window).resize(function(){
		ccm_setNewsflowOverlayDimensions();
	});

	// load the content into it.
	// we get the div we're loading content into
	// if we're in the dashboard, it's going to be newsflow-main
	$ovl = ccm_getNewsflowOverlayWindow();	
	$ovl.load(url, function() {
		// if we're showing the overlay, we hide it
		$ovl.hide();
		
		// hide all the arrows too
		$(".newsflow-paging-next").hide();
		$(".newsflow-paging-previous").hide();

		$ovl.html($(this).html());

		if (callback) {
			callback();
		}

		ccm_setNewsflowOverlayDimensions();
		ccm_setupTrickleUpNewsflowStyles();
		
		$ovl.fadeIn('300', 'easeOutExpo', function() {
			ccm_setNewsflowPagingArrowHeight();
		});

	});
}

ccm_setupTrickleUpNewsflowStyles = function() {
	ovl = ccm_getNewsflowOverlayWindow();	
	ovl.find('.newsflow-em1').each(function() {
		$(this).parent().addClass('newsflow-em1');
	});
}

ccm_showDashboardNewsflowWelcome = function() {
	jQuery.fn.dialog.showLoader(ccmi18n.newsflowLoading);
	ccm_showNewsflowOverlayWindow(CCM_DISPATCHER_FILENAME + '/dashboard/home?_ccm_dashboard_external=1', function() {
		jQuery.fn.dialog.hideLoader();
	});
}

ccm_showNewsflowOffsite = function(id) {
	jQuery.fn.dialog.showLoader();
	ccm_showNewsflowOverlayWindow(CCM_TOOLS_PATH + '/newsflow?cID=' + id, function() {
		jQuery.fn.dialog.hideLoader();
	});
}

ccm_showAppIntroduction = function() {
	ccm_showNewsflowOverlayWindow(CCM_DISPATCHER_FILENAME + '/dashboard/welcome?_ccm_dashboard_external=1');
}

ccm_getNewsflowByPath = function(path) {
	jQuery.fn.dialog.showLoader();
	ccm_showNewsflowOverlayWindow(CCM_TOOLS_PATH + '/newsflow?cPath=' + path, function() {
		jQuery.fn.dialog.hideLoader();
	});
}
