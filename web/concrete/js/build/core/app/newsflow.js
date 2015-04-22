!function(global, $) {
	'use strict';

	function ConcreteNewsflowDialog(options) {
		'use strict';
		var my = this;
		options = options || {};
		options = $.extend({
			width: 800,
			height: 400,
			title: 'Help',
			href: CCM_DISPATCHER_FILENAME + '/dashboard/home?_ccm_dashboard_external=1',
			dialogClass: 'ccm-dialog-slim ccm-dialog-help-wrapper'
		}, options);
		my.options = options;
	}

	ConcreteNewsflowDialog.prototype = {

		open: function() {
			var my = this;
			$.fn.dialog.open(my.options);
		}

	}

	global.ConcreteNewsflowDialog = ConcreteNewsflowDialog;

}(this, $);