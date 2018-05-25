/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global Tourist, ccmi18n_helpGuides, ConcreteHelpGuideManager */ 

;(function(global, $) {
	'use strict';

	var i18n = ccmi18n_helpGuides['location-panel'];
	var steps = [{
		content: '<p><span class="h5">' + i18n[0].title + '</span><br/>' + i18n[0].text + '</p>',
		highlightTarget: true,
		nextButton: true,
		closeButton: true,
		setup: function(tour, options) {
			return {target: $('#ccm-panel-detail-page-location button[name=location]')};
		},
		my: 'left center',
		at: 'right center'
	},{
		content: '<p><span class="h5">' + i18n[1].title + '</span><br/>' + i18n[1].text + '</p>',
		highlightTarget: true,
		nextButton: true,
		closeButton: true,
		setup: function(tour, options) {
			return {target: $('#ccm-panel-detail-page-location p.lead').eq(1)};
		},
		my: 'right center',
		at: 'left center'
	}];

	var tour = new Tourist.Tour({
		steps: steps,
		tipClass: 'Bootstrap',
		tipOptions:{
			showEffect: 'slidein'
		}
	});

	ConcreteHelpGuideManager.register('location-panel', tour);

})(window, jQuery);
