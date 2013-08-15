/** 
 * Left and right panels
 */

var CCMPanel = function(id, position) {

	var panels = new Array();
	var active = false;

	showPanel = function(panel) {
		// hide all other panels
		$('#ccm-panel-' + panel).addClass('ccm-panel-active');
		$('html').addClass(CCMPanel.getPanel(panel).class);
	}

	return {
		register: function(id, position) {
			switch(position) {
				case 'left':
					var class = 'ccm-panel-left';
					break;
				case 'right':
					var class = 'ccm-panel-right';
					break;					
			}
			$('<div />', {
				'id': 'ccm-panel-' + id,
				'class': 'ccm-panel ' + class
			}).appendTo($(document.body));

			panels[id] = {
				'id': id,
				'position': position,
				'class': class
			}
		},

		getPanel: function(panel) {
			return panels[panel];
		},

		hideAll: function() {
			$('.ccm-panel-active').removeClass('ccm-panel-active');
			$('html').removeClass('ccm-panel-right').removeClass('ccm-panel-left');
		},

		toggle: function(panel) {
			if (active && active == panel) {
				CCMPanel.hideAll();
				active = false;
				return;
			}
			CCMPanel.hideAll();
			active = panel;
			showPanel(panel);				
		},

	}

}();
