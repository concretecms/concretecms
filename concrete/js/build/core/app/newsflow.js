!function(global, $) {
	'use strict';

	function ConcreteNewsflowDialog(options) {
		'use strict';
		var my = this;
		options = options || {};
		options = $.extend({
			width: '800',
			height: '450', // the dialog adds 100 pixels
			title: 'Help',
			href: CCM_DISPATCHER_FILENAME + '/dashboard/home?_ccm_dashboard_external=1',
			dialogClass: 'ccm-dialog-slim ccm-dialog-help-wrapper',

			onOpen: function($dialog) {
				my.positionArrows($dialog);
			}
		}, options);
		my.options = options;
	}

	ConcreteNewsflowDialog.prototype = {

		positionArrows: function($dialog) {
			// move newsflow arrows out of the body to where they will be properly displayed
			$dialog.find('.newsflow-paging-next').addClass('newsflow-paging-appended').appendTo($dialog.parent());
			$dialog.find('.newsflow-paging-previous').addClass('newsflow-paging-appended').appendTo($dialog.parent());
		},

		open: function() {
			var my = this;
			$.fn.dialog.open(my.options);
		}

	}

	ConcreteNewsflowDialog.loadEdition = function(editionID) {
		var my = new ConcreteNewsflowDialog();
		var path = CCM_TOOLS_PATH + '/newsflow?cID=' + editionID;
		jQuery.fn.dialog.showLoader();
		$.get(path, function(r) {
			$('.newsflow-paging-appended').remove();
			jQuery.fn.dialog.replaceTop(r);
			jQuery.fn.dialog.hideLoader();

			$dialog = jQuery.fn.dialog.getTop();
			my.positionArrows($dialog);
		});
	}

	global.ConcreteNewsflowDialog = ConcreteNewsflowDialog;

}(this, $);