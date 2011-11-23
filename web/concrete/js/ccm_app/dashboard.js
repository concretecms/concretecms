ccm_closeDashboardPane = function(r) {
	$(r).closest('div.ccm-pane').fadeOut(120, 'easeOutExpo');
}

var ccm_dashboardBreadcrumbHoverTimer = null;

ccm_activateDashboardBreadcrumbHover = function() {
	$(".ccm-pane-header h3").mouseover(function() {
		$('.ccm-dashboard-pane-header-up').show();
	});

	$(".ccm-pane-header").mouseout(function(e) {
		if (e.toElement && ($(e.toElement).hasClass('ccm-pane-body') || $(e.toElement).hasClass('ccm-pane-options'))) {
			$('.ccm-dashboard-pane-header-up').fadeOut(300, 'easeOutExpo');
		}
	});
	

}
