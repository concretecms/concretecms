$(function() {
	ccm_intelligentSearchActivateResults();	
	ccm_intelligentSearchDoOffsite($('#ccm-nav-intelligent-search').val());
});

	var ccm_quickNavTimer = false;
	
	ccm_showQuickNav = function(callback) {
		clearTimeout(ccm_quickNavTimer);
		if ($('#ccm-quick-nav').is(':visible')) {
			if (typeof(callback) == 'function') {
				callback();
			}
		} else {
			$("#ccm-quick-nav").fadeIn(120, 'easeOutExpo', function() {
				if (typeof(callback) == 'function') {
					callback();
				}
			});
		}
	}
	
	ccm_hideQuickNav = function() {
		$("#ccm-quick-nav").fadeOut(120, 'easeInExpo');
		clearTimeout(ccm_quickNavTimer);
	}
	
	ccm_addToQuickNav = function(cID) {
		ccm_showQuickNav(function() {
			$.getJSON(CCM_TOOLS_PATH + '/dashboard/add_to_quick_nav', {
				'cID': cID
			}, function(r) {
				
			});
			$("#ccm-quick-nav-favorites").append('<li />');
			var accepter = $("#ccm-quick-nav-favorites li:last-child");
			accepter.css('display','none');
			var l = $("#ccm-add-to-quick-nav");
			var title = l.parent().parent().parent().find('h3');
			title.css('display','inline');
			accepter.html('<a href="' + l.attr('ccm-quick-nav-href') + '" onclick="' + l.attr('ccm-quick-nav-onclick') + '">' + l.attr('ccm-quick-nav-title') + '</a>').css('visibility','hidden').show();
			title.effect("transfer", { to: accepter, 'easing': 'easeOutExpo'}, 600, function() {
				accepter.hide().css('visibility','visible').fadeIn(240, 'easeInExpo');			
				title.css('display','block');
				ccm_quickNavTimer = setTimeout(function() {
					ccm_hideQuickNav();
				}, 1000);
			});
		});
	}
	
	ccm_activateToolbar = function() {
		$("#ccm-toolbar li a").click(function() {
			$(this).parent().addClass('ccm-system-nav-selected');
		});
		$("#ccm-toolbar,#ccm-quick-nav").hover(function() {
			ccm_showQuickNav();
		}, function() {
			ccm_quickNavTimer = setTimeout(function() {
				ccm_hideQuickNav();
			}, 1000);
		});
		
		$('#ccm-dashboard-overlay-main').masonry({
		  itemSelector: '.ccm-dashboard-overlay-module', 
		  isResizable: false
		});
		$('#ccm-dashboard-overlay-packages').masonry({
		  itemSelector: '.ccm-dashboard-overlay-module',
		  isResizable: false
		});
	
		$("#ccm-dashboard-overlay").css('visibility','visible').hide();
	
		$("#ccm-nav-intelligent-search-wrapper").click(function() {
			$("#ccm-nav-intelligent-search").focus();
		});
		$("#ccm-nav-intelligent-search").focus(function() {
			$(".ccm-system-nav-selected").removeClass('ccm-system-nav-selected');
			$(this).parent().addClass("ccm-system-nav-selected");
			if ($("#ccm-dashboard-overlay").is(':visible')) {
				$('#ccm-dashboard-overlay').fadeOut(90, 'easeOutExpo');
				$(window).unbind('click.dashboard-nav');
			}
		});
		
		$("#ccm-nav-dashboard").click(function() {
			$(".ccm-system-nav-selected").removeClass('ccm-system-nav-selected');
			$(this).parent().addClass('ccm-system-nav-selected');
			$("#ccm-nav-intelligent-search").val('');
			$("#ccm-intelligent-search-results").fadeOut(90, 'easeOutExpo');
	
			if ($('#ccm-edit-overlay').is(':visible')) {
				$('#ccm-edit-overlay').fadeOut(90, 'easeOutExpo');
				$(window).unbind('click.ccm-edit');
			}
	
			if ($('#ccm-dashboard-overlay').is(':visible')) {
				$(".ccm-system-nav-selected").removeClass('ccm-system-nav-selected');
				$('#ccm-dashboard-overlay').fadeOut(90, 'easeOutExpo');
				$(window).unbind('click.dashboard-nav');
			} else {
				$("#ccm-dashboard-overlay").fadeIn(160, 'easeOutExpo');
				$(window).bind('click.dashboard-nav', function() {
					$(".ccm-system-nav-selected").removeClass('ccm-system-nav-selected');
					$('#ccm-dashboard-overlay').fadeOut(90, 'easeOutExpo');
					$(window).unbind('click.dashboard-nav');
				});
			}
			return false;
		});
	
		$("#ccm-nav-intelligent-search").bind('keydown.ccm-intelligent-search', function(e) {
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
	
		$("#ccm-nav-intelligent-search").bind('keyup.ccm-intelligent-search', function(e) {
			ccm_intelligentSearchDoOffsite($(this).val());
		});
	
		$("#ccm-nav-intelligent-search").blur(function() {
			$(this).parent().removeClass("ccm-system-nav-selected");
		});
		
		
		$("#ccm-nav-intelligent-search").liveUpdate('ccm-intelligent-search-results', 'intelligent-search');
		$("#ccm-nav-intelligent-search").bind('click', function(e) { if ( this.value=="") { 
			$("#ccm-intelligent-search-results").hide();
		}});
		
		$("#ccm-toolbar-nav-properties").dialog();
		$("#ccm-toolbar-add-subpage").dialog();
		$("#ccm-toolbar-nav-versions").dialog();
		$("#ccm-toolbar-nav-design").dialog();
		$("#ccm-toolbar-nav-permissions").dialog();
		$("#ccm-toolbar-nav-speed-settings").dialog();
		$("#ccm-toolbar-nav-move-copy").dialog();
		$("#ccm-toolbar-nav-delete").dialog();
	
		$("#ccm-nav-edit").click(function() {
			$(".ccm-system-nav-selected").removeClass('ccm-system-nav-selected');
			$(this).parent().addClass('ccm-system-nav-selected');
			$("#ccm-nav-intelligent-search").val('');
			$("#ccm-intelligent-search-results").fadeOut(90, 'easeOutExpo');
	
			if ($('#ccm-dashboard-overlay').is(':visible')) {
				$('#ccm-dashboard-overlay').fadeOut(90, 'easeOutExpo');
				$(window).unbind('click.dashboard-nav');
			}
	
			if ($('#ccm-edit-overlay').is(':visible')) {
				$(".ccm-system-nav-selected").removeClass('ccm-system-nav-selected');
				$('#ccm-edit-overlay').fadeOut(90, 'easeOutExpo');
				$(window).unbind('click.ccm-edit');
			} else {
				$("#ccm-edit-overlay").click(function(e) {
					e.stopPropagation();
				});
				setTimeout("$('#ccm-check-in-comments').focus();",300);
				$("#ccm-check-in-preview").click(function() {
					$("#ccm-approve-field").val('PREVIEW');
					$("#ccm-check-in").submit();
				});
			
				$("#ccm-check-in-discard").click(function() {
					$("#ccm-approve-field").val('DISCARD');
					$("#ccm-check-in").submit();
				});
			
				$("#ccm-check-in-publish").click(function() {
					$("#ccm-approve-field").val('APPROVE');
					$("#ccm-check-in").submit();
				});
				$("#ccm-edit-overlay").fadeIn(160, 'easeOutExpo', function() {
					$(this).find('a').click(function() {
						ccm_toolbarCloseEditMenu();
					});
				});
				$(window).bind('click.ccm-edit', function() {
					ccm_toolbarCloseEditMenu();				
				});
			}
			return false;
		});

	}
	var ajaxtimer = null;
	var ajaxquery = null;

	ccm_toolbarCloseEditMenu = function() {
		$(".ccm-system-nav-selected").removeClass('ccm-system-nav-selected');
		$('#ccm-edit-overlay').fadeOut(90, 'easeOutExpo');
		$(window).unbind('click.ccm-edit');
	}
	
	ccm_intelligentSearchActivateResults = function() {
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

	ccm_intelligentSearchDoOffsite = function(query) {	
		if (!query) {
			return;
		}
		if (query.trim().length > 2) {
			if (query.trim() == ajaxquery) {
				return;
			}
			
			if (ajaxtimer) {
				window.clearTimeout(ajaxtimer);
			}
			ajaxquery = query.trim();
			ajaxtimer = window.setTimeout(function() {
				ajaxtimer = null;
				$("#ccm-intelligent-search-results-list-marketplace").parent().show();
				$("#ccm-intelligent-search-results-list-help").parent().show();
				$("#ccm-intelligent-search-results-list-marketplace").parent().addClass('ccm-intelligent-search-results-module-loading');
				$("#ccm-intelligent-search-results-list-help").parent().addClass('ccm-intelligent-search-results-module-loading');
	
				$.getJSON(CCM_TOOLS_PATH + '/marketplace/intelligent_search', {
					'q': ajaxquery
				},
				function(r) {
					$("#ccm-intelligent-search-results-list-marketplace").parent().removeClass('ccm-intelligent-search-results-module-loading');
					$("#ccm-intelligent-search-results-list-marketplace").html('');
					for (i = 0; i < r.length; i++) {
						var rr= r[i];
						$("#ccm-intelligent-search-results-list-marketplace").append('<li><a href="' + rr.href + '"><img src="' + rr.img + '" />' + rr.name + '</a></li>');
					}
					if (r.length == 0) {
						$("#ccm-intelligent-search-results-list-marketplace").parent().hide();
					}
					if ($('.ccm-intelligent-search-result-selected').length == 0) {
						$("#ccm-intelligent-search-results").find('li a').removeClass('ccm-intelligent-search-result-selected');
						$("#ccm-intelligent-search-results li:visible a:first").addClass('ccm-intelligent-search-result-selected');
					}
					ccm_intelligentSearchActivateResults();
				}).error(function() {
					$("#ccm-intelligent-search-results-list-marketplace").parent().hide();
				});
	
				$.getJSON(CCM_TOOLS_PATH + '/get_remote_help', {
					'q': ajaxquery
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
					ccm_intelligentSearchActivateResults();

				}).error(function() {
					$("#ccm-intelligent-search-results-list-help").parent().hide();
				});
	
			}, 500);
		}
	}