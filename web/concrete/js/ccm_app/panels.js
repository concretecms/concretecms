/** 
 * Left and right panels
 */

var CCMPanel = function(options) {

	this.options = options;
	this.isOpen = false;

	this.getPositionClass = function() {
		switch(options.position) {
			case 'left':
				var class = 'ccm-panel-left';
				break;
			case 'right':
				var class = 'ccm-panel-right';
				break;					
			}
		return class;
	}

	this.getIdentifier = function() {
		return this.options.identifier;
	}

	this.getDOMID = function() {
		return 'ccm-panel-' + this.options.identifier.replace('/', '-');
	}

	this.hide = function() {
		$('[data-launch-panel=\'' + this.getIdentifier() + '\']').removeClass('ccm-launch-panel-active');
		$('#' + this.getDOMID()).removeClass('ccm-panel-active');
		$('#ccm-panel-overlay').queue(function() {
			$(this).removeClass('ccm-panel-translucent');
			$(this).dequeue();
		}).delay(1000).hide(0);
		$('html').removeClass(this.getPositionClass());
		this.isOpen = false;
	}
	this.toggle = function() {
		if (this.isOpen) {
			this.hide();
		} else {
			this.show();				
		}
	}

	this.show = function() {

		if (this.options.primary) {
			// then it is the only panel that can be open on the screen
			// we hide any other open ones.
			var panels = CCMPanelManager.getPanels();
			for (i = 0; i < panels.length; i++) {
				var panel = panels[i];
				if ((panel.getIdentifier() != this.getIdentifier()) && (panel.isOpen)) {
					panel.hide();
				}
			}
		}
		// hide all other panels
		var $panel = $('#' + this.getDOMID());
		$panel.addClass('ccm-panel-active ccm-panel-loading');
		$panel.find('.ccm-panel-content').load(this.options.url, {'cID': CCM_CID}, function() {
			$panel.delay(1).queue(function() {
				$(this).removeClass('ccm-panel-loading').addClass('ccm-panel-loaded');
				$(this).dequeue();
			});
			$panel.find('[data-launch-panel-detail]').on('click', function() {
				var identifier = $(this).attr('data-launch-panel-detail').replace('/', '-');
				var detailID = 'ccm-panel-detail-' + identifier;
				$detail = $('<div />', {
					id: detailID,
					class: 'ccm-panel-detail'
				}).appendTo(document.body);
				$content = $('<div />', {
					class: 'ccm-panel-detail-content'
				}).appendTo($detail);				
				var transition = $(this).attr('data-panel-detail-transition');
				if (transition) {
					$('div.ccm-page')
					.queue(function() {
						$detail.addClass('ccm-transition-' + transition);
						$(this).addClass('ccm-transition-' + transition);
						$(this).dequeue();
					})
					.delay(3)
					.queue(function() {
						$detail.addClass('ccm-transition-' + transition + '-start');
						$(this).addClass('ccm-transition-' + transition + '-start');
						$(this).dequeue();
					})
				}
				$('html').addClass('ccm-panel-detail-open');
				var url = CCM_TOOLS_PATH + '/panels/details/' + $(this).attr('data-launch-panel-detail');
				$content.load(url, {'cID': CCM_CID}, function() {
					$('div.ccm-page').addClass('ccm-transition-complete');
					var $actions = $(this).find('.ccm-pane-detail-form-actions');
					if ($actions.length) {
						$(document.body).delay(500)
						.queue(function() {
							$('<div />', {
								id: 'ccm-pane-detail-form-actions-wrapper',
								class: 'ccm-ui'
							}).appendTo(document.body);
							$actions.appendTo('#ccm-pane-detail-form-actions-wrapper');
							$(this).dequeue();
						})
						.delay(5)
						.queue(function() {
							$('#ccm-pane-detail-form-actions-wrapper .ccm-pane-detail-form-actions').css('opacity', 1);
							$(this).dequeue();
						});
					}
					$detail.addClass('ccm-transition-complete');
				});
				return false;
			})
		});
	    CCMPanelManager.showOverlay(this.options.translucent);
		$('[data-launch-panel=\'' + this.getIdentifier() + '\']').addClass('ccm-launch-panel-active');
		$('html').addClass(this.getPositionClass());
		this.isOpen = true;


	}


}

var CCMPanelManager = function() {

	var panels = new Array();

	return {

		getPanels: function() {
			return panels;
		},

		showOverlay: function(translucent) {
			$('#ccm-panel-overlay')
			.clearQueue()
			.show(0)
			.delay(100)
			.queue(function() {
				if (translucent) {
					$(this).addClass('ccm-panel-translucent');
				} else {
					$(this).removeClass('ccm-panel-translucent');
				}
				$(this).dequeue();
	    	});
		},

		register: function(options) {
			var options = $.extend({
				translucent: true,
				position: 'left',
				primary: true,
				url: false
			}, options);
			if (!options.url) {
				options.url = CCM_TOOLS_PATH + '/panels/' + options.identifier;
			}
			var panel = new CCMPanel(options);
			panels.push(panel);

			$('<div />', {
				'id': panel.getDOMID(),
				'class': 'ccm-panel ' + panel.getPositionClass()
			}).appendTo($(document.body));

			$('<div />', {
				'class': 'ccm-panel-content ccm-ui'
			}).appendTo($('#' + panel.getDOMID()));
			$('<div />', {
				'class': 'ccm-panel-shadow-layer'
			}).appendTo($('#' + panel.getDOMID()));

			
		},

		getByIdentifier: function(panelID) {
			for (i = 0; i < panels.length; i++) {
				if (panels[i].getIdentifier() == panelID) {
					return panels[i];
				}
			}
		}

	}

}();
