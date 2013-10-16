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

		switch(options.transition) {
			case 'slide':
				class += ' ccm-panel-transition-slide';
				break;
			default:
				class += ' ccm-panel-transition-none';
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

	this.onPanelLoad = function() {
		this.setupPanelDetails();
		this.setupSubPanels();
		ccm_event.publish('panel.open', [this]);
	}

	this.hide = function() {
		var detail = this.closePanelDetail();
		var delay = 0;
		if (detail) {
			delay = 550;
		}
		var obj = this;
		$(window).delay(delay).queue(function() {
			$('[data-launch-panel=\'' + obj.getIdentifier() + '\']').removeClass('ccm-launch-panel-active');
			$('#' + obj.getDOMID()).removeClass('ccm-panel-active');
			$('#ccm-panel-overlay').queue(function() {
				$(this).removeClass('ccm-panel-translucent');
				$(this).dequeue();
			}).delay(1000).hide(0);
			$('html').removeClass(obj.getPositionClass());
			obj.isOpen = false;
			$(this).dequeue();
		});
	}
	this.toggle = function() {
		if (this.isOpen) {
			this.hide();
		} else {
			this.show();				
		}
	}

	this.setupSubPanels = function() {
		var $panel = $('#' + this.getDOMID());
		var obj = this;
		$panel.find('[data-launch-sub-panel]').unbind().on('click', function() {
			var url = CCM_TOOLS_PATH + '/panels/' + $(this).attr('data-launch-sub-panel');
			$('<div />', {'class': 'ccm-panel-content ccm-panel-content-appearing'}).appendTo($panel.find('.ccm-panel-content-wrapper')).load(url + '?cID=' + CCM_CID, function(r) {
				$panel.find('.ccm-panel-content-visible').removeClass('ccm-panel-content-visible').addClass('ccm-panel-slide-left');
				$(this).removeClass('ccm-panel-content-appearing').addClass('ccm-panel-content-visible');
				obj.onPanelLoad();
			});
			return false;
		});
		$panel.find('[data-panel-navigation=back]').unbind().on('click', function() {
			$(this)
			.queue(function() {
				var $prev = $panel.find('.ccm-panel-content-visible').prev();
				$panel.find('.ccm-panel-content-visible').removeClass('ccm-panel-content-visible').addClass('ccm-panel-slide-right');
				$prev.removeClass('ccm-panel-slide-left').addClass('ccm-panel-content-visible');
				$(this).dequeue();
			})
			.delay(500)
			.queue(function() {
				$panel.find('.ccm-panel-slide-right').remove();
				$(this).dequeue();
			});
			return false;
		});
	}		

	this.closePanelDetail = function() {
		$('.ccm-panel-detail').queue(function() {
			$(this).css('opacity', 0);
			$(this).dequeue();
		}).delay(1000).queue(function() {
			$(this).hide();
			$(this).dequeue();
		})
		$('#ccm-panel-detail-form-actions-wrapper .ccm-panel-detail-form-actions').queue(function() {
			$(this).css('opacity', 0);
			$(this).dequeue(0)
		});
		$('.ccm-page').removeClass().addClass('ccm-page');
		$('html').removeClass('ccm-panel-detail-open');

		return $('.ccm-panel-detail').length > 0;
	}

	this.openPanelDetail = function(options) {
		var obj = this;
		var options = $.extend({
			transition: false,
			url: false
		}, options);
		if (!options.url) {
			options.url = CCM_TOOLS_PATH + '/panels/details/' + options.identifier;
		}
		var identifier = options.identifier;
		var detailID = 'ccm-panel-detail-' + identifier;
		$detail = $('<div />', {
			id: detailID,
			class: 'ccm-panel-detail'
		}).appendTo(document.body);
		$content = $('<div />', {
			class: 'ccm-panel-detail-content'
		}).appendTo($detail);				
		var transition = options.transition;
		if (!transition) {
			transition = 'none';
		}
		$('div.ccm-page')
		.queue(function() {
			$detail.addClass('ccm-panel-detail-transition-' + transition);
			$(this).addClass('ccm-panel-detail-transition-' + transition);
			$(this).dequeue();
		})
		.delay(3)
		.queue(function() {
			$detail.addClass('ccm-panel-detail-transition-' + transition + '-start');
			$(this).addClass('ccm-panel-detail-transition-' + transition + '-start');
			$(this).dequeue();
		});
		$('html').addClass('ccm-panel-detail-open');
		$content.load(options.url + '?cID=' + CCM_CID, function() {
			$('div.ccm-page').addClass('ccm-panel-detail-transition-complete');
			obj.loadPanelDetailActions($content);
			$detail.addClass('ccm-panel-detail-transition-complete');
		});
	}

	this.loadPanelDetailActions = function($content) {
		var $actions = $content.find('.ccm-panel-detail-form-actions');
		if ($actions.length) {
			$(document.body).delay(500)
			.queue(function() {
				$('<div />', {
					id: 'ccm-panel-detail-form-actions-wrapper',
					class: 'ccm-ui'
				}).appendTo(document.body);
				$actions.appendTo('#ccm-panel-detail-form-actions-wrapper');
				$(this).dequeue();
			})
			.delay(5)
			.queue(function() {
				$('#ccm-panel-detail-form-actions-wrapper .ccm-panel-detail-form-actions').css('opacity', 1);
				$(this).dequeue();
			});
			$('button[data-panel-detail-action=cancel]').on('click', function() {
				CCMPanelManager.exitPanelMode();
			});
			$('button[data-panel-detail-action=submit]').on('click', function() {
				$('[data-panel-detail-form]').ajaxSubmit({
					type: 'post',
					dataType: 'json',
					data: {'submitPanelDetailForm': 1, 'cID': CCM_CID},
					beforeSubmit: function() {
						jQuery.fn.dialog.showLoader();
					},
					error: function(r) {
				      ccmAlert.notice('Error', '<div class="alert alert-danger">' + r.responseText + '</div>');
				  	},
					success: function(r) {
						if (r.error) {
							ccmAlert.notice('Error', '<div class="alert alert-danger">' + r.errors.join("<br>") + '</div>');
						} else {
							if (r.redirectURL) {
								window.location.href = r.redirectURL;
							} else {
								CCMEditMode.showResponseNotification(r.message, 'ok', 'success');
							}
							CCMPanelManager.exitPanelMode();
						}
					},
					complete: function() {
						jQuery.fn.dialog.hideLoader();
					}
				});
			});
		}
	}

	this.setupPanelDetails = function() {
		var $panel = $('#' + this.getDOMID());
		var obj = this;
		$panel.find('[data-panel-menu=accordion]').each(function() {
			var $accordion = $(this);
			var $title = $(this).find('>nav>span');
			$title.text($(this).find('a[data-panel-accordion-tab-selected=true]').text());
			$title.unbind().on('click', function() {
				$accordion.toggleClass('ccm-panel-header-accordion-dropdown-visible');
			});
			$(this).find('>nav ul a').unbind().on('click', function() {
				var url = obj.options.url;
				var $content = $panel.find('.ccm-panel-content');
				$accordion.removeClass('ccm-panel-header-accordion-dropdown-visible');
				$title.html($(this).text());
				$content.load(url + '?cID=' + CCM_CID + '&tab=' + $(this).attr('data-panel-accordion-tab'), function() {
					obj.onPanelLoad();
				});
			})
		});
		$panel.find('[data-panel-menu=collapsible-list-group]').each(function() {
			var $clg = $(this);
			$clg.find('.list-group-item-collapse').on('click', function() {
				var $inner = $clg.find('.list-group-item-collapse-wrapper');
				var menuID = $clg.attr('data-panel-menu-id');
				var $title = $clg.find('.list-group-item-collapse span');
				var height = $inner.height();
				if ($clg.hasClass('ccm-panel-list-group-item-expanded')) {
					$title.text(ccmi18n.expand);
					ccm_event.publish('collapse.' + menuID);
					$inner.
					queue(function() {
						$(this).css('height', 0);
						$(this).dequeue();
					}).
					delay(305).
					queue(function() {
						$(this).hide();
						$(this).css('height', 'auto');
						$(this).dequeue();
					});
				} else {
					ccm_event.publish('expand.' + menuID);
					$title.text(ccmi18n.collapse);
					$inner.
					queue(function() {
						$(this).css('height', 0);
						$(this).show();
						$(this).dequeue();
					}).
					delay(5).
					queue(function() {
						$(this).css('height', height);
						$(this).dequeue();
					});
				}
				$clg.toggleClass('ccm-panel-list-group-item-expanded');
			});
		});
		$panel.find('[data-launch-panel-detail]').unbind().on('click', function() {
			$(this).addClass('ccm-panel-menu-item-active');
			var identifier = $(this).attr('data-launch-panel-detail');
			var panelDetailOptions = {'identifier': identifier};
			if ($(this).attr('data-panel-transition')) {
				panelDetailOptions.transition = $(this).attr('data-panel-transition');
			}
			if ($(this).attr('data-panel-url')) {
				panelDetailOptions.url = $(this).attr('data-panel-url');
			}
			obj.openPanelDetail(panelDetailOptions);
			return false;
		});
		obj.loadPanelDetailActions($panel);

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
		var obj = this;
		// hide all other panels
		var $panel = $('#' + this.getDOMID());
		$panel.find('.ccm-panel-content-wrapper').html('');
		$panel.addClass('ccm-panel-active ccm-panel-loading');
		$('<div />', {'class': 'ccm-panel-content ccm-panel-content-visible'}).appendTo($panel.find('.ccm-panel-content-wrapper')).load(this.options.url + "?cID=" + CCM_CID, function() {
			$panel.delay(1).queue(function() {
				$(this).removeClass('ccm-panel-loading').addClass('ccm-panel-loaded');
				$(this).dequeue();
			});
			obj.onPanelLoad();
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

		/** 
		 * Hides all panels, exit preview mode, hides detail content if active, etc..
		 */
		exitPanelMode: function() {

			for (i = 0; i < panels.length; i++) {
				if (panels[i].isOpen) {
					panels[i].hide();
				}
			}
			CCMEditMode.exitPreviewMode();
		},

		register: function(options) {
			var options = $.extend({
				translucent: true,
				position: 'left',
				primary: true,
				transition: 'slide',
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

			$stage = $('<div />', {
				'class': 'ccm-panel-content-wrapper ccm-ui'
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
