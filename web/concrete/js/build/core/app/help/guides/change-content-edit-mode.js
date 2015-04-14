!function(global, $) {
	'use strict';

	var $area = $('div.ccm-area:not(.ccm-global-area)').eq(0);
	var $block = $area.find('.ccm-block-edit').eq(0);
	var steps = [{
		content: '<p><span class="h5">Edit Mode Active</span><br/>The highlighted button makes it obvious you\'re in edit mode.</p>',
		highlightTarget: false,
		nextButton: true,
		target: $('[data-guide-toolbar-action=check-in]'),
		my: 'top left',
		at: 'bottom center',
		setup: function(tour, options) {
			ConcreteHelpGuideManager.clearGuideToLaunchOnRefresh();
		}
	},{
		content: '<p><span class="h5">Edit the Block</span><br/>Just roll over any content on the page. Click or tap to get the edit menu for that block.</p>',
		highlightTarget: false,
		nextButton: false,
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
		content: '<p><span class="h5">Edit Menu</span><br/>Use this menu to edit a block\'s contents, change its display, or remove it entirely.</p>',
		highlightTarget: false,
		nextButton: true,
		setup: function(tour, options) {
			var target = $('div#ccm-popover-menu-container div.ccm-edit-mode-block-menu').eq(0);
			return {target: target}
		},
		teardown: function() {
			ConcreteMenuManager.getActiveMenu().hide();
		},
		my: 'left center',
		at: 'right center'
	},{
		content: '<p><span class="h5">Save Changes</span><br/>Your changes save as you go – but when you\'re done editing, don\'t forget to exit edit mode by clicking on the page\'s edit button again.</p>',
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