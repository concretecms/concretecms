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
					attribs = '';
					var innerButtonLeft = '';
					var innerButtonRight = '';
					if (buttons[j].getInnerButtonLeftHTML() != '') {
						innerButtonLeft = buttons[j].getInnerButtonLeftHTML() + ' ';
					}
					if (buttons[j].getInnerButtonRightHTML() != '') {
						innerButtonRight = ' ' + buttons[j].getInnerButtonRightHTML();
					}
					var _attribs = buttons[j].getAttributes();
					for (k in _attribs) {
						attribs += _attribs[k].key + '="' + _attribs[k].value + '" ';
					}
					if (buttons[j].getURL() != '') {
						buttonStr += '<a href="' + buttons[j].getURL() + '" ' + attribs + ' class="btn btn-small ' + buttons[j].getCSSClass() + '">' + innerButtonLeft + buttons[j].getLabel() + innerButtonRight + '</a>';
					} else { 
						buttonStr += '<button type="submit" ' + attribs + ' name="action_' + buttons[j].getAction() + '" class="btn-small btn ' + buttons[j].getCSSClass() + '">' + innerButtonLeft + buttons[j].getLabel() + innerButtonRight + '</button>';
					}
				}
				var line = '<form method="post" action="' + it.getAction() + '" id="ccm-status-bar-form-' + i + '" ' + (it.useAjaxForm ? 'class="ccm-status-bar-ajax-form"' : '') + '><div class="alert-message alert ' + it.getCSSClass() + '"><button type="button" class="close" data-dismiss="alert">×</button><span>' + it.getDescription() + '</span> <div class="ccm-page-status-bar-buttons">' + buttonStr + '</div></div></form>';
				d += line;
			}
			d += '</div>';
			$('#ccm-page-controls-wrapper').append(d);
			$('#ccm-page-status-bar .dialog-launch').dialog();
			$('#ccm-page-status-bar .alert').bind('closed', function() {
				$(this).remove();
				var visi = $('#ccm-page-status-bar .alert:visible').length;
				if (visi == 0) {
					$('#ccm-page-status-bar').remove();
				}
			});
			$('#ccm-page-status-bar .ccm-status-bar-ajax-form').ajaxForm({
				dataType: 'json',
				beforeSubmit: function() {
					jQuery.fn.dialog.showLoader();
				},
				success: function(r) {
					if (r.redirect) {
						window.location.href = r.redirect;
					}
				}
			});
		}
	}

}

ccm_statusBarItem = function() {

	this.css = '';
	this.description = '';
	this.buttons = [];
	this.action = '';
	this.useAjaxForm = false;
	
	this.setCSSClass = function(css) {
		this.css = css;
	}
	
	this.enableAjaxForm = function() {
		this.useAjaxForm = true;
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
	this.innerbuttonleft = '';
	this.innerbuttonright = '';
	this.label = '';
	this.action = '';
	this.url = '';
	this.attribs = new Array();
	
	this.setLabel = function(label) {
		this.label = label;
	}
	
	this.setCSSClass = function(css) {
		this.css = css;
	}

	this.setInnerButtonLeftHTML = function(html) {
		this.innerbuttonleft = html;
	}

	this.setInnerButtonRightHTML = function(html) {
		this.innerbuttonright = html;
	}
	
	this.setAction = function(action) {
		this.action = action;
	}
	
	this.getAttributes = function() {
		return this.attribs;
	}
	
	this.addAttribute = function(key, value) {
		this.attribs.push({'key': key, 'value': value});
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
	
	this.getInnerButtonLeftHTML = function() {
		return this.innerbuttonleft;
	}

	this.getInnerButtonRightHTML = function() {
		return this.innerbuttonright;
	}
	
	this.getLabel = function() {
		return this.label;
	}

}