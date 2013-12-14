/** 
 * Basic concrete5 toolbar class
 */

var CCMToolbar = function() {

	var $toolbar = $('#ccm-toolbar');
	var $searchInput = $('#ccm-nav-intelligent-search');
	var $searchResults = $('#ccm-intelligent-search-results');
	var remotesearchquery, ajaxtimer;

	setupHelpNotifications = function() {
		$(document.body).on('click', 'a[data-dismiss]', function() {
			var action = ($(this).attr('data-dismiss') == 'help-all') ? 'all' : 'this';
			$(this).parentsUntil('.ccm-notification-help').parent().queue(function() {
				$(this).css("opacity", 0);
				$(this).dequeue();
			}).delay(500).queue(function() {
				$(this).remove();
				$(this).dequeue();
			});

			$.ajax({
				type: 'post',
				data: {
					'type': $(this).attr('data-help-notification-type'),
					'identifier': $(this).attr('data-help-notification-identifier'),
					'action': action,
					'ccm_token': CCM_SECURITY_TOKEN
				},
				url: CCM_TOOLS_PATH + '/help/dismiss',
				success: function(r) {}
			});
		});
	}
	setupTooltips = function() {
		if ($("#ccm-tooltip-holder").length == 0) {
			$('<div />').attr('id','ccm-tooltip-holder').attr('class', 'ccm-ui').prependTo(document.body);
		}
		$('.launch-tooltip').tooltip({'container': '#ccm-tooltip-holder'});
	}

	setupChosen = function() {
		$('.chosen-select').chosen();
	}

	setupPanels = function() {
		$('<div />', {'id': 'ccm-panel-overlay'}).appendTo($(document.body));
		$('[data-launch-panel]').unbind().on('click', function() {
			var panelID = $(this).attr('data-launch-panel');
			$(this).toggleClass('ccm-launch-panel-active');
			var panel = CCMPanelManager.getByIdentifier(panelID);
			panel.toggle();
			return false;
		});
		$('html').addClass('ccm-panel-ready');

		ConcreteEvent.subscribe('panel.open',function(e) {
			var panel = e.eventData.panel;
			if (panel.options.identifier == 'page') {
				$('#' + panel.getDOMID()).find('[data-launch-panel-detail=\'page-composer\']').click();
			}
			$('a[data-toolbar-action=check-in]').on('click.close-check-in', function() {
				CCMPanelManager.exitPanelMode();
				return false;
			});
		});

		ConcreteEvent.subscribe('panel.close',function(e) {
			$('a[data-toolbar-action=check-in]').unbind('click.close-check-in');
		});

	}

	setupStatusBar = function() {
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

	setupIntelligentSearch = function() {
		$searchInput.bind('keydown.ccm-intelligent-search', function(e) {
			if (e.keyCode == 13 || e.keyCode == 40 || e.keyCode == 38) {
				e.preventDefault();
				e.stopPropagation();
	
				if (e.keyCode == 13 && $("a.ccm-intelligent-search-result-selected").length > 0) {
					var href = $("a.ccm-intelligent-search-result-selected").attr('href');
					if (!href || href == '#' || href == 'javascript:void(0)') {
						$("a.ccm-intelligent-search-result-selected").click();
					} else {
						window.location.href = href;
					}
				}
				var visibleitems = $("#ccm-intelligent-search-results li:visible");
				var sel;
				
				if (e.keyCode == 40 || e.keyCode == 38) {
					$.each(visibleitems, function(i, item) {
						if ($(item).children('a').hasClass('ccm-intelligent-search-result-selected')) {
							if (e.keyCode == 38) {
								io = visibleitems[i-1];
							} else {
								io = visibleitems[i+1];
							}
							sel = $(io).find('a');
						}
					});
					if (sel && sel.length > 0) {
						$("a.ccm-intelligent-search-result-selected").removeClass();
						$(sel).addClass('ccm-intelligent-search-result-selected');				
					}
				}
			} 
		});

		$searchInput.liveUpdate('ccm-intelligent-search-results', 'intelligent-search');
		$searchInput.bind('keyup.ccm-intelligent-search', function(e) {
			doRemoteSearchCall($(this).val());
		});
		$searchInput.bind('click', function(e) { if ( this.value=="") { 
			$searchResults.hide();
		}});

	}

	activateIntelligentSearchResults = function() {
		if ($("#ccm-intelligent-search-results div:visible").length == 0) {
			$("#ccm-intelligent-search-results").hide();
		}
		$("#ccm-intelligent-search-results a").hover(function() {
			$('a.ccm-intelligent-search-result-selected').removeClass();
			$(this).addClass('ccm-intelligent-search-result-selected');
		}, function() {
			$(this).removeClass('ccm-intelligent-search-result-selected');
		});
	}


	doRemoteSearchCall = function(query) {	
		query = jQuery.trim(query);
		if (!query) {
			return;
		}
		if (query.length > 2) {
			if (query == remotesearchquery) {
				return;
			}
			
			if (ajaxtimer) {
				window.clearTimeout(ajaxtimer);
			}
			remotesearchquery = query;
			ajaxtimer = window.setTimeout(function() {
				ajaxtimer = null;
				$("#ccm-intelligent-search-results-list-marketplace").parent().show();
				$("#ccm-intelligent-search-results-list-help").parent().show();
				$("#ccm-intelligent-search-results-list-your-site").parent().show();
				$("#ccm-intelligent-search-results-list-marketplace").parent().addClass('ccm-intelligent-search-results-module-loading');
				$("#ccm-intelligent-search-results-list-help").parent().addClass('ccm-intelligent-search-results-module-loading');
				$("#ccm-intelligent-search-results-list-your-site").parent().addClass('ccm-intelligent-search-results-module-loading');
	
				$.getJSON(CCM_TOOLS_PATH + '/marketplace/intelligent_search', {
					'q': remotesearchquery
				},
				function(r) {
					$("#ccm-intelligent-search-results-list-marketplace").parent().removeClass('ccm-intelligent-search-results-module-loading');
					$("#ccm-intelligent-search-results-list-marketplace").html('');
					for (i = 0; i < r.length; i++) {
						var rr= r[i];
						var _onclick = "ccm_getMarketplaceItemDetails(" + rr.mpID + ")";
						$("#ccm-intelligent-search-results-list-marketplace").append('<li><a href="javascript:void(0)" onclick="' + _onclick + '"><img src="' + rr.img + '" />' + rr.name + '</a></li>');
					}
					if (r.length == 0) {
						$("#ccm-intelligent-search-results-list-marketplace").parent().hide();
					}
					if ($('.ccm-intelligent-search-result-selected').length == 0) {
						$("#ccm-intelligent-search-results").find('li a').removeClass('ccm-intelligent-search-result-selected');
						$("#ccm-intelligent-search-results li:visible a:first").addClass('ccm-intelligent-search-result-selected');
					}
					activateIntelligentSearchResults();
				}).error(function() {
					$("#ccm-intelligent-search-results-list-marketplace").parent().hide();
				});
	
				$.getJSON(CCM_TOOLS_PATH + '/get_remote_help', {
					'q': remotesearchquery
				},
				function(r) {

					$("#ccm-intelligent-search-results-list-help").parent().removeClass('ccm-intelligent-search-results-module-loading');
					$("#ccm-intelligent-search-results-list-help").html('');
					for (i = 0; i < r.length; i++) {
						var rr= r[i];
						$("#ccm-intelligent-search-results-list-help").append('<li><a href="' + rr.href + '">' + rr.name + '</a></li>');
					}
					if (r.length == 0) {
						$("#ccm-intelligent-search-results-list-help").parent().hide();
					}
					if ($('.ccm-intelligent-search-result-selected').length == 0) {
						$("#ccm-intelligent-search-results").find('li a').removeClass('ccm-intelligent-search-result-selected');
						$("#ccm-intelligent-search-results li:visible a:first").addClass('ccm-intelligent-search-result-selected');
					}
					activateIntelligentSearchResults();

				}).error(function() {
					$("#ccm-intelligent-search-results-list-help").parent().hide();
				});

				$.getJSON(CCM_TOOLS_PATH + '/pages/intelligent_search', {
					'q': remotesearchquery
				},
				function(r) {

					$("#ccm-intelligent-search-results-list-your-site").parent().removeClass('ccm-intelligent-search-results-module-loading');
					$("#ccm-intelligent-search-results-list-your-site").html('');
					for (i = 0; i < r.length; i++) {
						var rr= r[i];
						$("#ccm-intelligent-search-results-list-your-site").append('<li><a href="' + rr.href + '">' + rr.name + '</a></li>');
					}
					if (r.length == 0) {
						$("#ccm-intelligent-search-results-list-your-site").parent().hide();
					}
					if ($('.ccm-intelligent-search-result-selected').length == 0) {
						$("#ccm-intelligent-search-results").find('li a').removeClass('ccm-intelligent-search-result-selected');
						$("#ccm-intelligent-search-results li:visible a:first").addClass('ccm-intelligent-search-result-selected');
					}
					activateIntelligentSearchResults();

				}).error(function() {
					$("#ccm-intelligent-search-results-list-your-site").parent().hide();
				});	
			}, 500);
		}
	}

	return {
		start: function() {
			if ($toolbar.length > 0) {

				$toolbar.find('.dialog-launch').dialog();

				setupStatusBar();
				setupIntelligentSearch();
				setupPanels();
				setupTooltips();
				setupChosen();
				setupHelpNotifications();

				// make sure that dashboard dropdown doesn't get dismissed if you mis-click inside it;
				$('#ccm-toolbar-menu-dashboard').on('click', function(e) {
					e.stopPropagation();
				});
			}
		},

		disable: function() {
			$('#ccm-toolbar-disabled').remove();
			$('<div />', {'id': 'ccm-toolbar-disabled'}).appendTo(document.body);
			setTimeout(function() {
				$('#ccm-toolbar-disabled').css('opacity', 1);
			}, 10);
		},

		enable: function() {
			$('#ccm-toolbar-disabled').remove();
		},

		disableDirectExit: function() {
			var $link = $('li.ccm-toolbar-page-edit a');
			if ($link.attr('data-launch-panel') != 'check-in') {
				$link.attr('data-launch-panel', 'check-in').on('click', function() {
					$(this).toggleClass('ccm-launch-panel-active');
					var panel = CCMPanelManager.getByIdentifier('check-in');
					panel.toggle();
					return false;
				});
			}
		}


	}

}();
