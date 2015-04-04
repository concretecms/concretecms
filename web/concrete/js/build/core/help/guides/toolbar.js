!function(global, $) {
	'use strict';
	var steps = [{
		content: '<p><span class="h5">Edit Mode</span><br/>Edit anything on this page by clicking the pencil icon.</p>',
		highlightTarget: true,
		nextButton: true,
		target: $('[data-guide-toolbar-action=edit-page]'),
		my: 'top left',
		at: 'bottom center'
	},{
		content: '<p><span class="h5">Settings</span><br/>Change the general look and options like SEO and permissions. Delete the page or roll versions back from here as well.</p>',
		highlightTarget: true,
		nextButton: true,
		target: $('[data-guide-toolbar-action=page-settings]'),
		my: 'top left',
		at: 'bottom center'
	},{
		content: '<p><span class="h5">Add Content</span><br/>Place a new block on the page. Copy one using the clipboard, or try a reusable stack.</p>',
		highlightTarget: true,
		nextButton: true,
		target: $('[data-guide-toolbar-action=add-content]'),
		my: 'top left',
		at: 'bottom center'

	},{
		content: '<p><span class="h5">Intelligent Search</span><br/>At a loss? Try searching here. You can find anything from pages in your site to settings and how-to documentation.</p>',
		highlightTarget: true,
		nextButton: true,
		target: $('[data-guide-toolbar-action=intelligent-search]'),
		my: 'top center',
		at: 'bottom center'

	},{
		content: '<p><span class="h5">Add Page</span><br/>Add a new page to your site, or quickly jump around your sitemap.</p>',
		highlightTarget: true,
		nextButton: true,
		target: $('[data-guide-toolbar-action=sitemap]'),
		my: 'top right',
		at: 'bottom center'

	},{
		content: '<p><span class="h5">Dashboard</span><br/>Anything that isn\'t specific to this page happens here. Manage users, files, reporting data, and site-wide settings.</p>',
		highlightTarget: true,
		nextButton: true,
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
		$('.ccm-dialog-help-wrapper').hide();
		$('#ccm-toolbar').addClass('ccm-toolbar-tour-guide');
	});
	tour.on('stop', function() {
		$('.ccm-dialog-help-wrapper').show();
		$('#ccm-toolbar').removeClass('ccm-toolbar-tour-guide');
	});

	ConcreteHelpGuideManager.register('toolbar', tour);

}(window, jQuery);