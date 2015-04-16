!function(global, $) {
	'use strict';
	var i18n = ccmi18n_helpGuides['dashboard'];
	var steps = [{
		content: '<p><span class="h5">' + i18n[0].title + '</span><br/>' + i18n[0].text + '</p>',
		highlightTarget: true,
		nextButton: false,
		target: $('[data-guide-toolbar-action=dashboard]'),
		my: 'top right',
		at: 'bottom center',
		setup: function(tour, options) {
			$('a[data-launch-panel=dashboard]').on('click', function() {
				tour.view.tip.hide();
				ConcreteHelpGuideManager.hideOverlay();
			});
			ConcreteEvent.subscribe('PanelOpen.concreteDashboardTour', function(e, data) {
				setTimeout(function() {
					var panel = data.panel.getIdentifier();
					if (panel == 'dashboard') {
						tour.next();
					}
				}, 500);
			});
		}
	},{
		content: '<p><span class="h5">' + i18n[1].title + '</span><br/>' + i18n[1].text + '</p>',
		highlightTarget: false,
		nextButton: true,
		my: 'right center',
		at: 'left center',
		setup: function(tour, options) {
			return {target: $('div#ccm-panel-dashboard ul.nav a[href$=sitemap]').eq(0)}
		}
	},{
		content: '<p><span class="h5">' + i18n[2].title + '</span><br/>' + i18n[2].text + '</p>',
		highlightTarget: false,
		nextButton: true,
		my: 'right center',
		at: 'left center',
		setup: function(tour, options) {
			return {target: $('div#ccm-panel-dashboard ul.nav a[href$=files]').eq(0)}
		}
	},{
		content: '<p><span class="h5">' + i18n[3].title + '</span><br/>' + i18n[3].text + '</p>',
		highlightTarget: false,
		nextButton: true,
		my: 'right center',
		at: 'left center',
		setup: function(tour, options) {
			return {target: $('div#ccm-panel-dashboard ul.nav a[href$=users]').eq(0)}
		}
	},{
		content: '<p><span class="h5">' + i18n[4].title + '</span><br/>' + i18n[4].text + '</p>',
		highlightTarget: false,
		nextButton: true,
		my: 'right center',
		at: 'left center',
		setup: function(tour, options) {
			return {target: $('div#ccm-panel-dashboard ul.nav a[href$=reports]').eq(0)}
		}
	},{
		content: '<p><span class="h5">' + i18n[5].title + '</span><br/>' + i18n[5].text + '</p>',
		highlightTarget: false,
		nextButton: true,
		my: 'right center',
		at: 'left center',
		setup: function(tour, options) {
			return {target: $('div#ccm-panel-dashboard ul.nav a[href$=system]').eq(0)}
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
		ConcreteEvent.unsubscribe('PanelOpen.concreteDashboardTour');
	});

	ConcreteHelpGuideManager.register('dashboard', tour);

}(window, jQuery);