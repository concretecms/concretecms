/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global ccmi18n_helpGuides, Tourist, ConcreteHelpGuideManager */

;(function(global, $) {
	'use strict';

	var i18n = ccmi18n_helpGuides.toolbar;
	var steps = [{
		content: '<p><span class="h5">' + i18n[0].title + '</span><br/>' + i18n[0].text + '</p>',
		highlightTarget: true,
		nextButton: true,
		closeButton: true,
		target: $('[data-guide-toolbar-action=edit-page]'),
		my: 'top left',
		at: 'bottom center'
	},{
		content: '<p><span class="h5">' + i18n[1].title + '</span><br/>' + i18n[1].text + '</p>',
		highlightTarget: true,
		nextButton: true,
		closeButton: true,
		target: $('[data-guide-toolbar-action=page-settings]'),
		my: 'top left',
		at: 'bottom center'
	},{
		content: '<p><span class="h5">' + i18n[2].title + '</span><br/>' + i18n[2].text + '</p>',
		highlightTarget: true,
		nextButton: true,
		closeButton: true,
		target: $('[data-guide-toolbar-action=add-content]'),
		my: 'top left',
		at: 'bottom center'

	},{
		content: '<p><span class="h5">' + i18n[3].title + '</span><br/>' + i18n[3].text + '</p>',
		highlightTarget: true,
		nextButton: true,
		closeButton: true,
		target: $('[data-guide-toolbar-action=intelligent-search]'),
		my: 'top center',
		at: 'bottom center'

	},{
		content: '<p><span class="h5">' + i18n[4].title + '</span><br/>' + i18n[4].text + '</p>',
		highlightTarget: true,
		nextButton: true,
		closeButton: true,
		target: $('[data-guide-toolbar-action=sitemap]'),
		my: 'top right',
		at: 'bottom center'

	},{
		content: '<p><span class="h5">' + i18n[5].title + '</span><br/>' + i18n[5].text + '</p>',
		highlightTarget: true,
		nextButton: true,
		closeButton: true,
		target: $('[data-guide-toolbar-action=dashboard]'),
		my: 'top right',
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
	});
	tour.on('stop', function() {
		ConcreteHelpGuideManager.exitToolbarGuideMode();
	});

	ConcreteHelpGuideManager.register('toolbar', tour);

})(window, jQuery);
