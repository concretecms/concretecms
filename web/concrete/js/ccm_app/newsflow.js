ccm_setNewsflowOverlayDimensions = function() {
	var w = $("#newsflow-overlay").width();
	var h = $('#newsflow-overlay').height();
	var tw = $(window).width();
	var th = $(window).height();
	var _left = (tw - w) / 2;
	var _top = (th - h) / 2;
	_top = _top + 29; // handle the top toolbar
	_left = _left + "px";
	_top = _top + "px";
	$("#newsflow-overlay").css('left', _left).css('top', _top);
}

ccm_closeNewsflow = function() {
	$("#newsflow-overlay").fadeOut(300, 'easeOutExpo', function() {
		$("#newsflow-overlay").remove();
	});
	$('.ui-widget-overlay').fadeOut(300, 'easeOutExpo', function() {
		$(this).remove();
	});

	if ($('#ccm-dashboard-content div#newsflow-main').length > 0 && $('#ccm-dashboard-content div#newsflow-main').not(':visible')) { 
		$('#ccm-dashboard-content div#newsflow-main').fadeIn(300, 'easeOutExpo');
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
		ccm_setNewsflowPagingArrowHeight();
		$("#newsflow-overlay").fadeIn('300', 'easeOutExpo');
	});
}

ccm_createNewsflowWindow = function() {
	if ($('#newsflow-overlay').length < 1) {
		var $overlay = $('<div id="newsflow-overlay"></div>').hide().appendTo('body');
	} else {
		$("#newsflow-overlay").hide();
	}
}

ccm_showNewsflowOverlay = function() {
	if ($('#ccm-dashboard-content div#newsflow-main').length == 0) { 
		if ($('.ui-widget-overlay').length < 1) {
			var $overlay = $('<div class="ui-widget-overlay"></div>').hide().appendTo('body');
		}
		$('.ui-widget-overlay').show();
	}
}

ccm_setNewsflowPagingArrowHeight = function() {
	if ($('#newsflow-overlay').length > 0) { 
		var $ovl = $('#newsflow-overlay');
	} else {
		var $ovl = $('#newsflow-main');
	}
	var h = $ovl.height();
	$(".newsflow-paging-previous span, .newsflow-paging-next span").css('height', h + 'px');
	$(".newsflow-paging-previous, .newsflow-paging-next").css('height', h + 'px');
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
		$("#newsflow-overlay").html($(this).html());
		ccm_setNewsflowOverlayDimensions();
		ccm_setNewsflowPagingArrowHeight();
		$("#newsflow-overlay").fadeIn('300', 'easeOutExpo');
	});
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