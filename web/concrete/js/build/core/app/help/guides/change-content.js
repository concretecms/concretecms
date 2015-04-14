!function(global, $) {
	'use strict';
	var steps = [{
		content: '<p><span class="h5">Enter Edit Mode</span><br/>First, click the "Edit Page" button. This will enter edit mode for this page.</p>',
		highlightTarget: false,
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

}(window, jQuery);