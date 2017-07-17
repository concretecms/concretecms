/**
 * Basic concrete5 toolbar class
 */

var ConcreteToolbar = function() {

	var $toolbar = $('#ccm-toolbar');
	var $searchInput = $('#ccm-nav-intelligent-search');
	var $searchResults = $('#ccm-intelligent-search-results');
	var remotesearchquery, ajaxtimer;

    if ($searchInput.length) {
        $searchResults.css('right', $(window).width() - $searchInput.offset().left - $searchResults.width() - 1);
    }

	setupHelpNotifications = function() {
		$('.ccm-notification .dialog-launch').dialog();
		$('a[data-help-notification-toggle]').concreteHelpLauncher();
		$('a[data-help-launch-dialog=main]').on('click', function(e) {
			e.preventDefault();
			new ConcreteHelpDialog().open();
		});

		var manager = ConcreteHelpGuideManager.get();
		if (manager.getGuideToLaunchOnRefresh()) {
			var tour = ConcreteHelpGuideManager.getGuide(manager.getGuideToLaunchOnRefresh());
			tour.start();
		}
	}

	setupPageAlerts = function() {
		$(document.body).on('click', 'a[data-dismiss-alert=page-alert]', function(e) {
			e.stopPropagation();
			$(this).closest('.ccm-notification').queue(function() {
				$(this).addClass('animated fadeOut');
				$(this).dequeue();
			}).delay(500).queue(function() {
				$(this).remove();
				$(this).dequeue();
			});
			return false;
		});

		$('form[data-form=workflow]').ajaxForm({
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

		$('a[data-workflow-task]').on('click', function(e) {
			var action = $(this).attr('data-workflow-task'),
				$form = $(this).closest('form[data-form=workflow]');
			$form.append('<input type="hidden" name="action_' + action + '" value="' + action + '">');
			$form.submit();
		});


	}

	setupTooltips = function() {
		if ($("#ccm-tooltip-holder").length == 0) {
			$('<div />').attr('id','ccm-tooltip-holder').attr('class', 'ccm-ui').prependTo(document.body);
		}
		$('.launch-tooltip').tooltip({'container': '#ccm-tooltip-holder'});
	}

	setupPanels = function() {
		$('<div />', {'id': 'ccm-panel-overlay'}).appendTo($(document.body));
        $('[data-launch-panel]').each(function() {
            $(this).prepend('<span class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></span>')
        });

		$('[data-launch-panel]').unbind().on('click', function() {
            var $this = $(this);
			var panelID = $this.attr('data-launch-panel');
			var panel = ConcretePanelManager.getByIdentifier(panelID);
            if ( !panel.willBePinned() ) $this.toggleClass('ccm-launch-panel-loading');

            if ( panel.isPinable() )
            {
                var parent = $($this.parent());
                if ( panel.willBePinned() || panel.pinned() ) parent.toggleClass("ccm-toolbar-page-edit-mode-pinned ");
				if (panel.willBePinned()) {
					$this.attr('data-original-icon-class', $this.find('i').attr('class'));
					$this.find('i').removeClass().addClass('fa fa-lock');
				} else if ($this.attr('data-original-icon-class')) {
					$this.find('i').removeClass().addClass($this.attr('data-original-icon-class'));
					$this.removeAttr('data-original-icon-class');
				}
            }
			panel.toggle();
			return false;
		});
		$('html').addClass('ccm-panel-ready');

		ConcreteEvent.subscribe('PanelOpen',function(e, data) {
			var panel = data.panel;
			if (panel.options.identifier == 'page') {
				$('#' + panel.getDOMID()).find('[data-launch-panel-detail=\'page-composer\']').click();
			}
			$('a[data-toolbar-action=check-in]').on('click.close-check-in', function() {
				ConcretePanelManager.exitPanelMode();
				return false;
			});
		});

		ConcreteEvent.subscribe('PanelClose',function(e) {
			$('a[data-toolbar-action=check-in]').unbind('click.close-check-in');
		});

	};

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

   setupMobileNav = function(){
        $('.ccm-toolbar-mobile-menu-button').click(function(){
            $(this).toggleClass('ccm-mobile-close');   // slide out mobile nav
            $('.ccm-mobile-menu-overlay').slideToggle();
        });
        $('.ccm-mobile-menu-overlay .parent-ul a').click(function(event) {
            if(!($(this).parent('li').hasClass('last-li'))) {
                $(this).parent('li').siblings().children('ul').hide();
                if(!($(this).parent('li').children('ul').is(':visible'))) {
                    $(this).next('ul').show();
                    event.preventDefault();
                }
            }
        });
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
	};

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
				$("#ccm-intelligent-search-results-list-marketplace").parent().removeClass('ccm-intelligent-search-results-module-loaded');
				$("#ccm-intelligent-search-results-list-help").parent().removeClass('ccm-intelligent-search-results-module-loaded');
				$("#ccm-intelligent-search-results-list-your-site").parent().removeClass('ccm-intelligent-search-results-module-loaded');

				$.getJSON(CCM_DISPATCHER_FILENAME + '/ccm/system/marketplace/search', {
					'q': remotesearchquery
				},
				function(r) {
					$("#ccm-intelligent-search-results-list-marketplace").parent().addClass('ccm-intelligent-search-results-module-loaded');
					$("#ccm-intelligent-search-results-list-marketplace").html('');
					for (i = 0; i < r.length; i++) {
						var rr= r[i];
						var _onclick = "ConcreteMarketplace.getMoreInformation(" + rr.mpID + ")";
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

				$.getJSON(CCM_DISPATCHER_FILENAME + '/ccm/system/backend/get_remote_help', {
					'q': remotesearchquery
				},
				function(r) {

					$("#ccm-intelligent-search-results-list-help").parent().addClass('ccm-intelligent-search-results-module-loaded');
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

				$.getJSON(CCM_DISPATCHER_FILENAME + '/ccm/system/backend/intelligent_search', {
					'q': remotesearchquery
				},
				function(r) {

					$("#ccm-intelligent-search-results-list-your-site").parent().addClass('ccm-intelligent-search-results-module-loaded');
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

				setupIntelligentSearch();
				setupPanels();
				setupTooltips();
				setupPageAlerts();
				setupHelpNotifications();
                setupMobileNav();
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
			if ($link.attr('data-launch-panel') != 'check-in' && $link.attr('data-disable-panel') != 'check-in') {
				$link.attr('data-launch-panel', 'check-in').on('click', function() {
					$(this).toggleClass('ccm-launch-panel-active');
					var panel = ConcretePanelManager.getByIdentifier('check-in');
					panel.toggle();
					return false;
				});
			}
		}


	}
}();
