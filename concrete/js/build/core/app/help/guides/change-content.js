/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global Tourist, ccmi18n_helpGuides, ConcreteHelpGuideManager */

;(function(global, $) {
	'use strict';

	var i18n = ccmi18n_helpGuides['change-content'];
	var steps = [{
		content: '<p><span class="h5">' + i18n[0].title + '</span><br/>' + i18n[0].text + '</p>',
		highlightTarget: false,
		closeButton: true,
		nextButton: false,
		target: $('[data-guide-toolbar-action=edit-page]'),
		my: 'top left',
		at: 'bottom center'
	}];

	var tour = new Tourist.Tour({
		steps: steps,
		tipClass: 'Bootstrap',
		tipOptions:{
			showEffect: 'slidein'
		}
	});
	tour.on('start', function() {
		ConcreteHelpGuideManager.enterToolbarGuideMode();
		ConcreteHelpGuideManager.launchGuideOnRefresh('change-content-edit-mode');
	});
	tour.on('stop', function() {
		ConcreteHelpGuideManager.exitToolbarGuideMode();
	});

	ConcreteHelpGuideManager.register('change-content', tour);

})(window, jQuery);
