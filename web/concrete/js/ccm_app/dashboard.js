/** 
 * Basic concrete5 toolbar class
 */

var CCMDashboard = function() {
	getBackgroundImageData = function(image, display) {
		$.getJSON(CCM_TOOLS_PATH + '/dashboard/get_image_data', {
			'image': image
		}, function(r) {
			if (r && display) {
				var html = '<div>';
				html += '<strong>' + r.title + '</strong> ' + ccmi18n.authoredBy + ' ';
				if (r.link) {
					html += '<a target="_blank" href="' + r.link + '">' + r.author + '</a>';
				} else {
					html += r.author;
				}
				$('<div id="ccm-dashboard-image-caption" class="ccm-ui"/>').html(html).appendTo(document.body).show();
				setTimeout(function() {
					$('#ccm-dashboard-image-caption').fadeOut(1000, 'easeOutExpo');
				}, 5000);
			}
		});
	}

	setupHelp = function() {
		var $ccmPageHelp = $("#ccm-page-help").popover({
			trigger: 'click',
			content: function() {
			var id = $(this).attr('id') + '-content';
			return $('#' + id).html();
			
		}, placement: 'bottom', html: true})
		.click(function(e) {
			e.stopPropagation();
		});
		$(document).click(function() {
			var $popover = $ccmPageHelp.data('popover');
			if ($popover) {
				$popover.hide();
			}
		});
	}

	setupResultMessages = function() {
		if ($('#ccm-dashboard-result-message').length > 0) { 
			if ($('.ccm-pane').length > 0) { 
				var pclass = $('.ccm-pane').parent().attr('class');
				var gpclass = $('.ccm-pane').parent().parent().attr('class');
				var html = $('#ccm-dashboard-result-message').html();
				$('#ccm-dashboard-result-message').html('<div class="' + gpclass + '"><div class="' + pclass + '">' + html + '</div></div>').fadeIn(400);
			}
		} else {
			$("#ccm-dashboard-result-message").fadeIn(200);
		}
	}

	setupTooltips = function() {
		$('.launch-tooltip').tooltip({placement: 'bottom'});
	}

	return {
		start: function(options) {
			if (options.image) {
				if (options.imagetime) {
					$.backstretch(options.image, {speed: options.imagetime});
				} else {
					$.backstretch(options.image);
				}
			}
			if (options.filename) {
				getBackgroundImageData(options.filename, options.displayCaption);
			}

			setupHelp();
			setupTooltips();
			setupResultMessages();

		},

		closePane: function(btn) {
			$(btn).closest('div.ccm-pane').fadeOut(120, 'easeOutExpo');
		},

		equalizeMenus: function() {
			if ($(window).width() < 560) {
				$('div.dashboard-icon-list div.well').css('visibility', 'visible');
				return false;
			}
			var j = -1;
			var i;
			var pos = 0;
			var menus = new Array();
			$('ul.nav-list').each(function() {		
				if ($(this).position().top != pos) {
					j++;
					menus[j] = new Array();
				}
				
				menus[j].push($(this));
				pos = $(this).position().top;
			});
			
			for (i = 0; i < menus.length; i++) {
				var h = 0;
				for (j = 0; j < menus[i].length; j++) {
					var mx = menus[i][j];
					if (mx.height() > h) {
						h = mx.height();
					}
				}	
				for (j = 0; j < menus[i].length; j++) {
					var mx = menus[i][j];
					mx.css('height', h);
				}
			}
			$('div.dashboard-icon-list div.well').css('visibility', 'visible');
		},

		toggleQuickNav: function() {

			var l = $("#ccm-add-to-quick-nav");
			if (l.hasClass('ccm-icon-favorite-selected')) {
				l.removeClass('ccm-icon-favorite-selected').addClass('ccm-icon-favorite');
			} else {
				l.removeClass('ccm-icon-favorite').addClass('ccm-icon-favorite-selected');
			}
			var accepter = $('#ccm-nav-dashboard');
			var title = l.parent().parent().parent().find('h3');
			title.css('display','inline');
			title.effect("transfer", { to: accepter, 'easing': 'easeOutExpo'}, 600);
			$.get(CCM_TOOLS_PATH + '/dashboard/add_to_quick_nav', {
				'cID': cID,
				'token': token
			}, function(r) {
				var div = $('<div />').html(r);
				$('#ccm-intelligent-search-results').html(div.find('#ccm-intelligent-search-results').html());
				$('#ccm-dashboard-overlay').html(div.find('#ccm-dashboard-overlay').html());
				div = false;
			});

		}

	}

}();
