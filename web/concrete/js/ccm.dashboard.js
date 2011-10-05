ccm_closeDashboardPane = function(r) {
	$(r).closest('div.ccm-dashboard-pane').fadeOut(120, 'easeOutExpo');
}

ccm_dashboardToggleOptions = function(obj) {
	var pane = $(obj).parent().find('div.ccm-dashboard-pane-options-content');
	if ($(obj).hasClass('ccm-icon-option-closed')) {
		$(obj).removeClass('ccm-icon-option-closed').addClass('ccm-icon-option-open');
		pane.slideDown('fast', 'easeOutExpo');
	} else {
		$(obj).removeClass('ccm-icon-option-open').addClass('ccm-icon-option-closed');
		pane.slideUp('fast', 'easeOutExpo');
	}
}

