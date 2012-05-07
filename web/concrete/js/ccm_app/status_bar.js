ccm_statusBar = {
	
	items: [],	

	addItem: function(item) {
		this.items.push(item);
	},

	activate: function() {
		if (this.items.length > 0) { 
			var d = '<div id="ccm-page-status-bar" class="ccm-ui">';
			for (i = 0; i < this.items.length; i++) {
				var it = this.items[i];
				var buttonStr = '';
				var buttons = it.getButtons();
				for (j = 0; j < buttons.length; j++) {
					if (buttons[j].getURL() != '') {
						buttonStr += '<a href="' + buttons[j].getURL() + '" class="btn ' + buttons[j].getCSSClass() + '">' + buttons[j].getLabel() + '</a>';
					} else { 
						buttonStr += '<input type="submit" name="action_' + buttons[j].getAction() + '" class="btn ' + buttons[j].getCSSClass() + '" value="' + buttons[j].getLabel() + '" />';
					}
				}
				var line = '<form method="post" action="' + it.getAction() + '"><div class="alert-message block-message ' + it.getCSSClass() + '"><span>' + it.getDescription() + '</span> <div class="ccm-page-status-bar-buttons">' + buttonStr + '</div></div></form>';
				d += line;
			}
			d += '</div>';
			$('#ccm-page-controls-wrapper').append(d);
		}
	}

}

ccm_statusBarItem = function() {

	this.css = '';
	this.description = '';
	this.buttons = [];
	this.action = '';
	
	this.setCSSClass = function(css) {
		this.css = css;
	}

	this.setDescription = function(description) {
		this.description = description;
	}
	
	this.getCSSClass = function() {
		return this.css;
	}
	
	this.getDescription = function() {
		return this.description;
	}
	
	this.addButton = function(btn) {
		this.buttons.push(btn);
	}
	
	this.getButtons = function() {
		return this.buttons;
	}
	
	this.setAction = function(action) {
		this.action = action;
	}
	
	this.getAction = function() {
		return this.action;
	}

}

ccm_statusBarItemButton = function() {
	
	this.css = '';
	this.label = '';
	this.action = '';
	this.url = '';
	
	this.setLabel = function(label) {
		this.label = label;
	}
	
	this.setCSSClass = function(css) {
		this.css = css;
	}
	
	this.setAction = function(action) {
		this.action = action;
	}
	
	this.getAction = function() {
		return this.action;
	}
	
	this.setURL = function(url) {
		this.url = url;
	}
	
	this.getURL = function() {
		return this.url;
	}
	
	this.getCSSClass = function() {
		return this.css;
	}
	
	this.getLabel = function() {
		return this.label;
	}

}