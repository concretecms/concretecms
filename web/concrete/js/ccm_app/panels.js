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
			$panel.find('[data-swap-panel]').on('click', function() {
				alert('hi');
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
		/*,


		hideAll: function() {
			$('.ccm-panel-active').removeClass('ccm-panel-active');
			$('#ccm-panel-overlay').queue(function() {
				$(this).removeClass('ccm-panel-translucent');
				$(this).dequeue();
			}).delay(1000).hide(0);
			$('html').removeClass('ccm-panel-right').removeClass('ccm-panel-left');
		}
		*/


	}

}();
