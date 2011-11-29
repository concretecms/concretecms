ccm_closeDashboardPane = function(r) {
	$(r).closest('div.ccm-pane').fadeOut(120, 'easeOutExpo');
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