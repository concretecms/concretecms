/** 
 * Left and right panels
 */

var CCMPanel = function(options) {

	this.options = options;
	this.id = options.id;
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

	this.hide = function() {
		$('#ccm-panel-' + this.options.id).removeClass('ccm-panel-active');
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
			for (key in panels) {
				var panel = panels[key];
				if ((panel.id != this.id) && (panel.isOpen)) {
					panel.hide();
				}
			}
		}

		// hide all other panels
		$('#ccm-panel-' + this.options.id).addClass('ccm-panel-active');
	    CCMPanelManager.showOverlay(this.options.translucent);
		$('html').addClass(this.getPositionClass());
		this.isOpen = true;


	}


}

var CCMPanelManager = function(id, position) {

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
				primary: true
			}, options);
			var panel = new CCMPanel(options);
			panels[options.id] = panel;

			$('<div />', {
				'id': 'ccm-panel-' + options.id,
				'class': 'ccm-panel ' + panel.getPositionClass()
			}).appendTo($(document.body));

		},

		getByID: function(panelID) {
			return panels[panelID];
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
