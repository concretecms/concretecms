ccm_closeDashboardPane = function(r) {
	$(r).closest('div.ccm-pane').fadeOut(120, 'easeOutExpo');
}

ccm_getDashboardBackgroundImageData = function(image, display) {
	$.getJSON(CCM_TOOLS_PATH + '/dashboard/get_image_data', {
		'image': image
	}, function(r) {
		if (r && display) {
			var html = '<div>';
			html += '<strong>' + r.title + '</strong> ' + ccmi18n.authoredBy + ' ';
			if (r.link) {
				html += '<a target="_blank" href="' + r.link + '">' + r.author + '</a>';
			} else {
				html += r.author;
			}
			$('<div id="ccm-dashboard-image-caption" class="ccm-ui"/>').html(html).appendTo(document.body).show();
			setTimeout(function() {
				$('#ccm-dashboard-image-caption').fadeOut(1000, 'easeOutExpo');
			}, 5000);
		}
	});
}

$(function() {
	ccm_activateToolbar();
	
	$("#ccm-page-help").popover({content: function() {
		var id = $(this).attr('id') + '-content';
		return $('#' + id).html();
		
	}, placement: 'bottom', html: true});
	$('.launch-tooltip').tooltip({placement: 'bottom'});
	if ($('#ccm-dashboard-result-message').length > 0) { 
		if ($('.ccm-pane').length > 0) { 
			var pclass = $('.ccm-pane').parent().attr('class');
			var gpclass = $('.ccm-pane').parent().parent().attr('class');
			var html = $('#ccm-dashboard-result-message').html();
			$('#ccm-dashboard-result-message').html('<div class="' + gpclass + '"><div class="' + pclass + '">' + html + '</div></div>').fadeIn(400);
		}
	} else {
		$("#ccm-dashboard-result-message").fadeIn(200);
	}
});
