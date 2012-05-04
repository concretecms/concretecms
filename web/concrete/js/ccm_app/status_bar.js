ccm_statusBar = {

	open: function(message) {
		var d = '<div id="ccm-page-status-bar">';
		d += message;
		d += '</div>';
		$('#ccm-page-controls-wrapper').append(d);
	}

}