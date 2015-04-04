!function(global, $) {
	'use strict';

	function ConcreteHelpDialog(options) {
		'use strict';
		var my = this;
		options = options || {};
		options = $.extend({
			element: $('#ccm-dialog-help'),
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
			my.options.onOpen = function() {
				$('a[data-lightbox=iframe]').magnificPopup({
					disableOn: 700,
					type: 'iframe',
					mainClass: 'mfp-fade',
					removalDelay: 160,
					preloader: false,
					fixedContentPos: false
				});

				$('a[data-launch-guide]').on('click', function(e) {
					e.preventDefault();
					var tour = ConcreteHelpGuideManager.getGuide($(this).attr('data-launch-guide'));
					tour.start();

				});
			}
			$.fn.dialog.open(my.options);
		}

	}

	global.ConcreteHelpDialog = ConcreteHelpDialog;

}(this, $);