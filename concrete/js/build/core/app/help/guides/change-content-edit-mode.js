!function(global, $) {
	'use strict';
	var i18n = ccmi18n_helpGuides['change-content-edit-mode'];
	var $area = $('div.ccm-area:not(.ccm-global-area)').eq(0);
	var $block = $area.find('.ccm-block-edit').eq(0);
	var steps = [{
		content: '<p><span class="h5">' + i18n[0].title + '</span><br/>' + i18n[0].text + '</p>',
		highlightTarget: false,
		nextButton: true,
		closeButton: true,
		target: $('[data-guide-toolbar-action=check-in]'),
		my: 'top left',
		at: 'bottom center',
		setup: function(tour, options) {
			ConcreteHelpGuideManager.clearGuideToLaunchOnRefresh();
		}
	},{
		content: '<p><span class="h5">' + i18n[1].title + '</span><br/>' + i18n[1].text + '</p>',
		highlightTarget: false,
		nextButton: false,
		closeButton: true,
		target: $block,
		my: 'top left',
		at: 'bottom center',
		setup: function(tour, options) {
			ConcreteEvent.subscribe('ConcreteMenuShow.changeContentEditModeTour', function(e, args) {
				tour.next();
			});
		},
		teardown: function(tour, options) {
			ConcreteEvent.unsubscribe('ConcreteMenuShow.changeContentEditModeTour');
		}
	},{
		content: '<p><span class="h5">' + i18n[2].title + '</span><br/>' + i18n[2].text + '</p>',
		closeButton: true,
		highlightTarget: false,
		nextButton: true,
		setup: function(tour, options) {
			var target = $('div#ccm-popover-menu-container div.ccm-edit-mode-block-menu').eq(0);
			return {target: target}
		},
		teardown: function() {
			var menu = ConcreteMenuManager.getActiveMenu();
			if (menu) {
				menu.hide();
			}
		},
		my: 'left center',
		at: 'right center'
	},{
		content: '<p><span class="h5">' + i18n[3].title + '</span><br/>' + i18n[3].text + '</p>',
		highlightTarget: true,
		nextButton: true,
		target: $('[data-guide-toolbar-action=check-in]'),
		my: 'top left',
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

	});
	tour.on('stop', function() {
		$('.ccm-dialog-help-wrapper').show();
	});

	ConcreteHelpGuideManager.register('change-content-edit-mode', tour);

}(window, jQuery);