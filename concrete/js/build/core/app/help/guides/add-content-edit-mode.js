/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global Tourist, ConcreteHelpGuideManager, ccmi18n_helpGuides */

;(function(global, $) {
	'use strict';

	var i18n = ccmi18n_helpGuides['add-content-edit-mode'];
	var steps = [{
		content: '<p><span class="h5">' + i18n[0].title + '</span><br/>' + i18n[0].text + '</p>',
		highlightTarget: false,
		nextButton: true,
		closeButton: true,
		target: $('[data-guide-toolbar-action=add-content]'),
		my: 'top left',
		at: 'bottom center',
		setup: function(tour, options) {
			ConcreteHelpGuideManager.clearGuideToLaunchOnRefresh();
		}
	},{
		content: '<p><span class="h5">' + i18n[1].title + '</span><br/>' + i18n[1].text + '</p>',
		highlightTarget: false,
		nextButton: true,
		closeButton: true,
		target: $('[data-guide-toolbar-action=add-content]'),
		my: 'top center',
		at: 'top center',
		setup: function(tour, options) {
			return {
				target: $('#ccm-panel-add-block .ccm-panel-content-inner')
			};
		},
		teardown: function(tour, options) {

		}
	},{
		content: '<p><span class="h5">' + i18n[2].title + '</span><br/>' + i18n[2].text + '</p>',
		highlightTarget: false,
		nextButton: true,
		closeButton: true,
		target: $('[data-guide-toolbar-action=add-content]'),
		my: 'left center',
		at: 'right center',
		setup: function(tour, options) {
			return {
				target: $('#ccm-panel-add-block .ccm-panel-header-accordion')
			};
		}
	},{
		content: '<p><span class="h5">' + i18n[3].title + '</span><br/>' + i18n[3].text + '</p>',
		highlightTarget: false,
		nextButton: true,
		closeButton: true,
		target: $('[data-guide-toolbar-action=add-content]'),
		my: 'left center',
		at: 'right center',
		setup: function(tour, options) {
			return {
				target: $('#ccm-panel-add-block input[data-input=search-blocks]')
			};
		}
	},{
		content: '<p><span class="h5">' + i18n[4].title + '</span><br/>' + i18n[4].text + '</p>',
		highlightTarget: false,
		nextButton: true,
		closeButton: true,
		target: $('[data-guide-toolbar-action=add-content]'),
		my: 'left center',
		at: 'right center',
		setup: function(tour, options) {
			return {
				target: $('#ccm-panel-add-block .ccm-panel-add-block-draggable-block-type').eq(0)
			};
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

	});
	tour.on('stop', function() {
		if ($('.ccm-dialog-help-wrapper').length) {
			// we haven't started the tour really yet
			$('.ccm-dialog-help-wrapper').show();
		} else {
			$.fn.dialog.showLoader();
			window.location.href = $('[data-toolbar-action=check-in]').attr('href');
		}
	});

	ConcreteHelpGuideManager.register('add-content-edit-mode', tour);

})(window, jQuery);
