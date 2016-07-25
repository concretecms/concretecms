!function(global, $) {
	'use strict';

	function ConcreteHelpDialog(options) {
		'use strict';
		var my = this;
		options = options || {};
		options = $.extend({
			width: 800,
			height: 400,
			title: 'Help',
			dialogClass: 'ccm-dialog-slim ccm-dialog-help-wrapper'
		}, options);
		my.options = options;
	}

	ConcreteHelpDialog.prototype = {

		open: function() {
			var my = this;
			if ($('#ccm-dialog-help').length) {
				my.options.element = '#ccm-dialog-help';
			} else {
				my.options.href = CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/help/introduction';
			}
			$.fn.dialog.open(my.options);
		}

	}

	global.ConcreteHelpDialog = ConcreteHelpDialog;

}(this, $);