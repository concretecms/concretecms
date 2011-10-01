$(function() {
	ccm_intelligentSearchActivateResults();	
	ccm_intelligentSearchDoOffsite($('#ccm-nav-intelligent-search').val());
});

	ccm_activateToolbar = function() {
		$("#ccm-toolbar li a").click(function() {
			$(this).parent().addClass('ccm-system-nav-selected');
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
				$("#ccm-edit-overlay").fadeIn(160, 'easeOutExpo');
				$(window).bind('click.ccm-edit', function() {
					$(".ccm-system-nav-selected").removeClass('ccm-system-nav-selected');
					$('#ccm-edit-overlay').fadeOut(90, 'easeOutExpo');
					$(window).unbind('click.ccm-edit');
				});
			}
			return false;
		});

	}
	var ajaxtimer = null;
	var ajaxquery = null;
	
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