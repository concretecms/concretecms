!function(global, $) {
	'use strict';
	var i18n = ccmi18n_helpGuides['personalize'];
	var steps = [{
		content: '<p><span class="h5">' + i18n[0].title + '</span><br/>' + i18n[0].text + '</p>',
		highlightTarget: true,
		nextButton: false,
		closeButton: true,
		target: $('[data-guide-toolbar-action=page-settings]'),
		my: 'top left',
		at: 'bottom center',
		setup: function(tour, options) {
			$('a[data-launch-panel=page]').on('click', function() {
				tour.view.tip.hide();
				ConcreteHelpGuideManager.hideOverlay();
			});
			ConcreteEvent.subscribe('PanelOpen.concretePersonalizeTour', function(e, data) {
				setTimeout(function() {
					var panel = data.panel.getIdentifier();
					if (panel == 'page') {
						tour.next();
					}
				}, 500);
			});
		}
	},{
		content: '<p><span class="h5">' + i18n[1].title + '</span><br/>' + i18n[1].text + '</p>',
		highlightTarget: true,
		nextButton: false,
		my: 'left center',
		closeButton: true,
		at: 'right center',
		setup: function(tour, options) {
			$('a[data-launch-panel-detail=page-design]').on('click', function() {
				tour.view.tip.hide();
			});
			ConcreteEvent.subscribe('PanelOpenDetail.concretePersonalizeTour', function(e, data) {
				setTimeout(function() {
					if (data.panel.identifier == 'page-design') {
						tour.next();
					}
				}, 500);
			});
			return {target: $('a[data-launch-panel-detail=page-design]').eq(0)}
		}
	},{
		content: '<p><span class="h5">' + i18n[2].title + '</span><br/>' + i18n[2].text + '</p>',
		highlightTarget: true,
		nextButton: true,
		closeButton: true,
		my: 'bottom center',
		at: 'top center',
		setup: function(tour, options) {
			return {target: $('span.ccm-page-design-theme-customize')}
		}
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
		ConcreteEvent.unsubscribe('PanelOpen.concretePersonalizeTour');
		ConcreteEvent.unsubscribe('PanelOpenDetail.concretePersonalizeTour');
	});

	ConcreteHelpGuideManager.register('personalize', tour);

}(window, jQuery);