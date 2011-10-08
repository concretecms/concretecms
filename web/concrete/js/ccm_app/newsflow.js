ccm_setNewsflowOverlayDimensions = function() {
	var w = $("#newsflow-overlay").width();
	var tw = $(window).width();
	var _left = (tw - w) / 2;
	_left = _left + "px";
	$("#newsflow-overlay").css('left', _left);
}

ccm_closeNewsflow = function() {
	$("#newsflow-overlay").fadeOut(300, 'easeOutExpo', function() {
		$("#newsflow-overlay").remove();
	});
	$('.ui-widget-overlay').fadeOut(300, 'easeOutExpo', function() {
		$(this).remove();
	});
}

ccm_showNewsflow = function() {
	$(window).resize(function(){
		ccm_setNewsflowOverlayDimensions();
	});

	var $overlay = $('<div class="ui-widget-overlay"></div>').hide().appendTo('body');
	
	$('.ui-widget-overlay').show();
	$('<div />').attr('id', 'newsflow-overlay').attr('class', 'ccm-ui').css('display','none').appendTo(document.body).load(CCM_DISPATCHER_FILENAME + '/dashboard/home?external=1', function() {
		ccm_setNewsflowOverlayDimensions();
		$("#newsflow-overlay").css('top', '90px').fadeIn('300', 'easeOutExpo');
	});
}
