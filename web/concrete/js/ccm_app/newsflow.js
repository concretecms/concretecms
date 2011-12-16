/*

ccm_closeNewsflow = function(r) {
	if ($('#newsflow-overlay').length == 0) { 
		$("#newsflow-main").fadeOut(300, 'easeOutExpo');
		var accepter = $('#ccm-recent-page-' + CCM_CID);
		var l = $(r);
		ccm_showQuickNav(function() { 
			$(r).closest('div.newsflow').fadeOut(120, 'easeOutExpo');
			$(r).effect("transfer", { to: accepter, 'easing': 'easeOutExpo'}, 600, function() {
				accepter.hide().css('visibility','visible').fadeIn(240, 'easeInExpo');			
				title.css('display','block');
				ccm_quickNavTimer = setTimeout(function() {
					ccm_hideQuickNav();
				}, 1000);
			});
		});
	} else { 
		$("#newsflow-overlay").fadeOut(300, 'easeOutExpo', function() {
			$("#newsflow-overlay").remove();
			ccm_setNewsflowPagingArrowHeight();
		});
		$('.ui-widget-overlay').fadeOut(300, 'easeOutExpo', function() {
			$(this).remove();
		});
		if ($('#ccm-dashboard-content div#newsflow-main').length > 0 && $('#ccm-dashboard-content div#newsflow-main').not(':visible')) { 
			$('#ccm-dashboard-content div#newsflow-main').fadeIn(300, 'easeOutExpo', function() {
				ccm_setNewsflowPagingArrowHeight();
			});
		}
	}
}

ccm_showNewsflow = function(hideLoadingText) {
	$(window).resize(function(){
		ccm_setNewsflowOverlayDimensions();
	});

	ccm_showNewsflowOverlay();
	if (!hideLoadingText) {
		jQuery.fn.dialog.showLoader(ccmi18n.newsflowLoading);	
	} else {
		jQuery.fn.dialog.showLoader();	
	}
	$('<div />').attr('class', 'ccm-ui').css('display','none').appendTo(document.body).load(CCM_DISPATCHER_FILENAME + '/dashboard/home?_ccm_dashboard_external=1', function() {
		jQuery.fn.dialog.hideLoader();
		ccm_createNewsflowWindow();
		$("#newsflow-overlay").html($(this).html());
		ccm_setNewsflowOverlayDimensions();
		$("#newsflow-overlay").fadeIn('300', 'easeOutExpo', function() {
			ccm_setNewsflowPagingArrowHeight();
		});
	});
}

ccm_createNewsflowWindow = function() {
	if ($('#newsflow-overlay').length < 1) {
		var $overlay = $('<div id="newsflow-overlay"></div>').hide().appendTo('body');
	} else {
		$("#newsflow-overlay").hide();
	}
}


ccm_showNewsflowOffsite = function(id) {
	if (!id) {
		if ($('#ccm-dashboard-content div#newsflow-main').length > 0 && $('#ccm-dashboard-content div#newsflow-main').not(':visible')) { 
			ccm_closeNewsflow();
			$('#ccm-dashboard-content div#newsflow-main').fadeIn(300, 'easeOutExpo');
		} else {
			ccm_showNewsflow(true);
		}
		return;
	}
	$(window).resize(function(){
		ccm_setNewsflowOverlayDimensions();
	});
	
	ccm_showNewsflowOverlay();
	jQuery.fn.dialog.showLoader();	
	if ($('#ccm-dashboard-content div#newsflow-main').is(':visible')) { 
		$('#ccm-dashboard-content div#newsflow-main').fadeOut(300);
	}
	$('<div />').attr('id', 'newsflow-overlay').attr('class', 'ccm-ui').css('display','none').appendTo(document.body).load(CCM_TOOLS_PATH + '/newsflow?cID=' + id, function() {
		jQuery.fn.dialog.hideLoader();
		ccm_createNewsflowWindow();
		$(".newsflow-paging-next").hide();
		$(".newsflow-paging-previous").hide();
		$("#newsflow-overlay").html($(this).html());
		ccm_setNewsflowOverlayDimensions();
		$("#newsflow-overlay").fadeIn('300', 'easeOutExpo', function() {
			$(".newsflow-paging-next").show();
			$(".newsflow-paging-previous").show();
			ccm_setNewsflowPagingArrowHeight()
		});
	});
}

ccm_newsflowConnectToCommunity = function() {
	window.location.href = CCM_DISPATCHER_FILENAME + '/dashboard/extend/connect/-/register_step1';
}

ccm_getNewsflowByPath = function(path) {
	$(window).resize(function(){
		ccm_setNewsflowOverlayDimensions();
	});
	ccm_showNewsflowOverlay();
	jQuery.fn.dialog.showLoader();	
	if ($('#ccm-dashboard-content div#newsflow-main').is(':visible')) { 
		$('#ccm-dashboard-content div#newsflow-main').fadeOut(300);
	}
	$('<div />').attr('id', 'newsflow-overlay').attr('class', 'ccm-ui').css('display','none').appendTo(document.body).load(CCM_TOOLS_PATH + '/newsflow?cPath=' + path, function() {
		jQuery.fn.dialog.hideLoader();
		ccm_createNewsflowWindow();
		$("#newsflow-overlay").html($(this).html());
		ccm_setNewsflowOverlayDimensions();
		ccm_setNewsflowPagingArrowHeight();
		$("#newsflow-overlay").fadeIn('300', 'easeOutExpo');
	});
}


ccm_showAppIntroduction = function() {
	$(window).resize(function(){
		ccm_setNewsflowOverlayDimensions();
	});

	ccm_showNewsflowOverlay();
	$('<div />').attr('id', 'newsflow-overlay').attr('class', 'ccm-ui').css('display','none').appendTo(document.body).load(CCM_DISPATCHER_FILENAME + '/dashboard/welcome?_ccm_dashboard_external=1', function() {
		ccm_createNewsflowWindow();
		$("#newsflow-overlay").html($(this).html());
		ccm_setNewsflowOverlayDimensions();
		ccm_setNewsflowPagingArrowHeight();
		$("#newsflow-overlay").fadeIn('300', 'easeOutExpo');
	});
}
*/

ccm_closeNewsflow = function(r) {
	$ovl = ccm_getNewsflowOverlayWindow();
	$ovl.fadeOut(300, 'easeOutExpo');
	$('.ui-widget-overlay').fadeOut(300, 'easeOutExpo', function() {
		$(this).remove();
	});
}

ccm_setNewsflowPagingArrowHeight = function() {
	var $ovl = ccm_getNewsflowOverlayWindow();
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
		
		if (availableSpace > optimalHeight) {
			h = optimalHeight;
		} else {
			h = availableSpace;
		}		
		$("#newsflow-overlay").css('height', h);

		var _left = (tw - w) / 2;
		var _top = (th - h) / 2;
		_top = _top + 29; // handle the top toolbar
		_left = _left + "px";
		_top = _top + "px";
		$("#newsflow-overlay").css('left', _left).css('top', _top);
	}
}

ccm_getNewsflowOverlayWindow = function() {
	if ($('#ccm-dashboard-content').length > 0) {
		var $ovl = $("#newsflow-main");
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

ccm_showNewsflowOverlayWindow = function(url, callback) {
	
	// if we're NOT showing newsflow on a dashboard page, we load an overlay
	if ($('#ccm-dashboard-content').length == 0) { 
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

		if (callback) {
			callback();
		}
		
		$ovl.html($(this).html());
		ccm_setNewsflowOverlayDimensions();
		$ovl.fadeIn('300', 'easeOutExpo', function() {
			ccm_setNewsflowPagingArrowHeight();
		});
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
