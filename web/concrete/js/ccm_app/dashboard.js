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

ccm_getDashboardBackgroundImageData = function(image) {
	$.getJSON(CCM_TOOLS_PATH + '/dashboard/get_image_data', {
		'image': image
	}, function(r) {
		if (r) {
			var html = '<div>';
			html += '<strong>' + r.title + '</strong> / ';
			if (r.link) {
				html += '<a target="_blank" href="' + r.link + '">' + r.author + '</a>';
			} else {
				html += r.author;
			}
			if (r.description) {
				html += ' / ' + r.description;
			}
			$('<div id="ccm-dashboard-image-caption" class="ccm-ui"/>').html(html).appendTo(document.body).show();
		}
	});
}