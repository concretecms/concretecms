!function(global, $) {
	'use strict';
	var steps = [{
		content: '<p><span class="h5">Dashboard Panel</span><br/>The dashboard is where you go to manage aspects of your site that have to do with more than the content on just one page. Click the button to continue.</p>',
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
		content: '<p><span class="h5">Sitemap</span><br/>Click to the sitemap to see a complete list of pages in your site. You can move, copy or delete entire parts of your site from the sitemap.</p>',
		highlightTarget: false,
		nextButton: true,
		my: 'right center',
		at: 'left center',
		setup: function(tour, options) {
			return {target: $('div#ccm-panel-dashboard ul.nav a[href$=sitemap]').eq(0)}
		}
	},{
		content: '<p><span class="h5">File Manager</span><br/>Add new files or manage existing images, documents and media from the file manager.</p>',
		highlightTarget: false,
		nextButton: true,
		my: 'right center',
		at: 'left center',
		setup: function(tour, options) {
			return {target: $('div#ccm-panel-dashboard ul.nav a[href$=files]').eq(0)}
		}
	},{
		content: '<p><span class="h5">Members</span><br/>Manage site members, editors and administrators, including users and groups.</p>',
		highlightTarget: false,
		nextButton: true,
		my: 'right center',
		at: 'left center',
		setup: function(tour, options) {
			return {target: $('div#ccm-panel-dashboard ul.nav a[href$=users]').eq(0)}
		}
	},{
		content: '<p><span class="h5">Reports</span><br/>View form and survey results from the Reports section.</p>',
		highlightTarget: false,
		nextButton: true,
		my: 'right center',
		at: 'left center',
		setup: function(tour, options) {
			return {target: $('div#ccm-panel-dashboard ul.nav a[href$=reports]').eq(0)}
		}
	},{
		content: '<p><span class="h5">System & Settings</span><br/>Fully customize your editing and administrative experience.</p>',
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