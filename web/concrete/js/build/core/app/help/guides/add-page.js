!function(global, $) {
	'use strict';
	var steps = [{
		content: '<p><span class="h5">Pages Panel</span><br/>The pages is where you go to add a new page to your site, or jump between existing pages. To open the pages panel, click the icon.</p>',
		highlightTarget: true,
		nextButton: false,
		target: $('[data-guide-toolbar-action=sitemap]'),
		my: 'top right',
		at: 'bottom center',
		setup: function(tour, options) {
			$('a[data-launch-panel=sitemap]').on('click', function() {
				tour.view.tip.hide();
				ConcreteHelpGuideManager.hideOverlay();
			});
			ConcreteEvent.subscribe('PanelOpen.concreteAddPageTour', function(e, data) {
				setTimeout(function() {
					var panel = data.panel.getIdentifier();
					if (panel == 'sitemap') {
						tour.next();
					}
				}, 500);
			});
		}
	},{
		content: '<p><span class="h5">Page Types</span><br/>This is your list of page types. Click any of them to add a page.</p>',
		highlightTarget: true,
		nextButton: true,
		my: 'right center',
		at: 'left center',
		setup: function(tour, options) {
			return {target: $('ul.ccm-panel-sitemap-list li a').eq(0)}
		}
	},{
		content: '<p><span class="h5">Page Types</span><br/>This is your list of page types. Click any of them to add a page.</p>',
		highlightTarget: true,
		nextButton: true,
		my: 'right center',
		at: 'left center',
		setup: function(tour, options) {
			return {target: $('div#ccm-sitemap-panel-sitemap')}
		}
	},];

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
		ConcreteEvent.unsubscribe('PanelOpen.concreteAddPageTour');
	});

	ConcreteHelpGuideManager.register('add-page', tour);

}(window, jQuery);