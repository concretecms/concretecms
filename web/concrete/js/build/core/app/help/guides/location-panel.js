!function(global, $) {
	'use strict';
	var steps = [{
		content: '<p><span class="h5">Choose Location</span><br/>Click this button to choose the location of the page in your sitemap. If saved, the page will be moved to this location.</p>',
		highlightTarget: true,
		nextButton: true,
		setup: function(tour, options) {
			return {target: $('#ccm-panel-detail-page-location button[name=location]')}
		},
		my: 'left center',
		at: 'right center',
	},{
		content: '<p><span class="h5">Page URLs</span><br/>Control the URLs used to access your page here. Non-canonical URLs will redirect to your page; canonical URLs can be either generated or automatically or overridden. Sub-pages to this page start with canonical URLs by default.</p>',
		highlightTarget: true,
		nextButton: true,
		setup: function(tour, options) {
			return {target: $('#ccm-panel-detail-page-location p.lead').eq(1)}
		},
		my: 'right center',
		at: 'left center',
	}];

	var tour = new Tourist.Tour({
		steps: steps,
		tipClass: 'Bootstrap',
		tipOptions:{
			showEffect: 'slidein'
		}
	});

	ConcreteHelpGuideManager.register('location-panel', tour);

}(window, jQuery);