!function(global, $) {
	'use strict';
	var steps = [{
		content: '<p><span class="h5">Properties Panel</span><br/>The properties panel controls data and details about the current page. You can also go there to make design customizations for the page. To open the properties panel, click the icon.</p>',
		highlightTarget: true,
		nextButton: false,
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
		content: '<p><span class="h5">Page Design</span><br/>From here you can change your page template and customize your page\'s styles.</p>',
		highlightTarget: true,
		nextButton: false,
		my: 'left center',
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
		content: '<p><span class="h5">Customize</span><br/>Click here to load the theme customizer for the page.</p>',
		highlightTarget: true,
		nextButton: true,
		my: 'bottom center',
		at: 'top center',
		setup: function(tour, options) {
			return {target: $('span.ccm-page-design-theme-customize')}
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
		ConcreteEvent.unsubscribe('PanelOpen.concretePersonalizeTour');
		ConcreteEvent.unsubscribe('PanelOpenDetail.concretePersonalizeTour');
	});

	ConcreteHelpGuideManager.register('personalize', tour);

}(window, jQuery);