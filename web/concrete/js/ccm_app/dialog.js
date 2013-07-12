/** 
 * concrete5 wrapper for jQuery UI
 */

$.widget.bridge( "jqdialog", $.ui.dialog );
// wrap our old dialog function in the new dialog() function.
jQuery.fn.dialog = function() {
	// Pass this over to jQuery UI Dialog in a few circumstances
	if (arguments.length > 0) {
		$(this).jqdialog(arguments[0], arguments[1], arguments[2]);
		return;
	} else if ($(this).is('div')) {
		$(this).jqdialog();
		return;
	}
	// LEGACY SUPPORT
	return $(this).each(function() {
		$(this).unbind('click.make-dialog').bind('click.make-dialog', function(e) {
			var href = $(this).attr('href');
			var width = $(this).attr('dialog-width');
			var height =$(this).attr('dialog-height');
			var title = $(this).attr('dialog-title');
			var onOpen = $(this).attr('dialog-on-open');
			var onDestroy = $(this).attr('dialog-on-destroy');
			/*
			 * no longer necessary. we auto detect
				var appendButtons = $(this).attr('dialog-append-buttons');
			*/
			var onClose = $(this).attr('dialog-on-close');
			var onDirectClose = $(this).attr('dialog-on-direct-close');
			obj = {
				modal: true,
				href: href,
				width: width,
				height: height,
				title: title,
				onOpen: onOpen,
				onDestroy: onDestroy,
				onClose: onClose,
				onDirectClose: onDirectClose
			}
			jQuery.fn.dialog.open(obj);
			return false;
		});
	});
}

jQuery.fn.dialog.close = function(num) {
	num++;
	$("#ccm-dialog-content" + num).jqdialog('close');
}

jQuery.fn.dialog.open = function(options) {
	if (typeof($.fn.ccmmenu) != 'undefined') {
		$.fn.ccmmenu.hide();
	}

	if (typeof(options.width) == 'string') {
		if (options.width == 'auto') {
			w = 'auto';
		} else {
			if (options.width.indexOf('%', 0) > 0) {
				w = options.width.replace('%', '');
				w = $(window).width() * (w / 100);
				w = w + 50;
			} else {
				w = parseInt(options.width) + 50;
			}
		}
	} else if (options.width) { 
		w = parseInt(options.width) + 50;
	} else {
		w = 550;
	}

	if (typeof(options.height) == 'string') {
		if (options.height == 'auto') {
			h = 'auto';
		} else {
			if (options.height.indexOf('%', 0) > 0) {
				h = options.height.replace('%', '');
				h = $(window).height() * (h / 100);
				h = h + 100;
			} else {
				h = parseInt(options.height) + 100;
			}
		}
	} else if (options.height) {
		h = parseInt(options.height) + 100;
	} else {
		h = 400;
	}
	if (h !== 'auto' && h > $(window).height()) {
		h = $(window).height();
	}

	options.width = w;
	options.height = h;

	var defaults = { 
		'modal': true,
		'escapeClose': true,
		'width': w,
		'height': h,

		'create': function() {
			$(this).parent().addClass('ccm-dialog-opening');
		},

		'open': function() {
			var $dialog = $(this);
			$dialog.parent().addClass('ccm-dialog-open');
			var nd = $(".ui-dialog").length;
			if (nd == 1) {
				$("body").attr('data-last-overflow', $('body').css('overflow'));
				$("body").css("overflow", "hidden");
			}
			var overlays = $('.ui-widget-overlay').length;
			$('.ui-widget-overlay').each(function(i, obj) {
				if ((i + 1) < overlays) {
					$(this).css('opacity', 0);
				}
			});

			jQuery.fn.dialog.activateDialogContents($dialog);
			
			if (typeof options.onOpen != "undefined") {
				if ((typeof options.onOpen) == 'function') {
					options.onOpen($dialog);
				} else {
					eval(options.onOpen);
				}
			}


		},
		'beforeClose': function() {
			var nd = $(".ui-dialog").length;
			if (nd == 1) {
				$("body").css("overflow", $('body').attr('data-last-overflow'));		
			}
		},
		'close': function(ev, u) {
			if (!options.element) {
				$(this).jqdialog('destroy').remove();
			}
			if (typeof options.onClose != "undefined") {
				if ((typeof options.onClose) == 'function') {
					options.onClose();
				} else {
					eval(options.onClose);
				}
			}
			if (typeof options.onDirectClose != "undefined" && ev.handleObj && (ev.handleObj.type == 'keydown' || ev.handleObj.type == 'click')) {
				if ((typeof options.onDirectClose) == 'function') {
					options.onDirectClose();
				} else {
					eval(options.onDirectClose);
				}
			}
			if (typeof options.onDestroy != "undefined") {
				if ((typeof options.onDestroy) == 'function') {
					options.onDestroy();
				} else {
					eval(options.onDestroy);
				}
			}
			var overlays = $('.ui-widget-overlay').length;
			$('.ui-widget-overlay').each(function(i, obj) {
				if ((i + 1) < overlays) {
					$(this).css('opacity', 0);
				} else {
					$(this).css('opacity', 1);
				}
			});
		}
	};

	var finalSettings = {'autoOpen': false};
	$.extend(finalSettings, defaults, options);

	if (finalSettings.element) {
		$(finalSettings.element).jqdialog(finalSettings).jqdialog();
		$(finalSettings.element).jqdialog('open');
	} else {
		jQuery.fn.dialog.showLoader();
		$.ajax({
			type: 'GET',
			url: finalSettings.href,
			success: function(r) {
				jQuery.fn.dialog.hideLoader();
				// note the order here is very important in order to actually run javascript in 
				// the pages we load while having access to the jqdialog object.
				$('<div />').jqdialog(finalSettings).html(r).jqdialog('open');
			}
		});			
	}
		
}

jQuery.fn.dialog.activateDialogContents = function($dialog) {
	// handle buttons
	if ($dialog.find('.dialog-buttons').length > 0) {
		$dialog.jqdialog('option', 'buttons', [{}]);
		$dialog.parent().find(".ui-dialog-buttonset").remove();
		$dialog.parent().find(".ui-dialog-buttonpane").html('');
		$dialog.find('.dialog-buttons').appendTo($dialog.parent().find('.ui-dialog-buttonpane').addClass("ccm-ui"));
	}

	// make dialogs
	$dialog.find('.dialog-launch').dialog();

	// automated close handling
	$dialog.find('.ccm-dialog-close').on('click', function() {
		$dialog.dialog('close');
	});

	// help handling
	if ($("#tooltip-holder").length == 0) {
		$('<div />').attr('id','tooltip-holder').attr('class', 'ccm-ui').prependTo(document.body);
	}
	if ($dialog.find('.dialog-help').length > 0) {
		$dialog.find('.dialog-help').hide();
		var helpContent = $dialog.find('.dialog-help').html();
		if (ccmi18n.helpPopup) {
			var helpText = ccmi18n.helpPopup;
		} else {
			var helpText = 'Help';
		}
		$dialog.parent().find('.ui-dialog-titlebar').addClass('ccm-ui').append('<button class="ui-dialog-titlebar-help ccm-menu-help-trigger"><i class="icon-info-sign"></i></button>');
		$dialog.parent().find('.ui-dialog-titlebar .ccm-menu-help-trigger').popover({content: function() {
			return helpContent;			
		}, placement: 'bottom', html: true, container: '#tooltip-holder', trigger: 'click'});
	}
}

jQuery.fn.dialog.getTop = function() {
	var nd = $(".ui-dialog:visible").length;
	return $($('.ui-dialog:visible')[nd-1]).find('.ui-dialog-content');
}

jQuery.fn.dialog.replaceTop = function(html) {
	$dialog = jQuery.fn.dialog.getTop();
	$dialog.html(html);
	jQuery.fn.dialog.activateDialogContents($dialog);
}

jQuery.fn.dialog.showLoader = function(text) {
	if ($('#ccm-dialog-loader').length < 1) {
		$("body").append("<div id='ccm-dialog-loader-wrapper' class='ccm-ui'><div class='progress progress-striped active' style='width: 300px'><div class='bar' style='width: 100%;'></div></div></div>");//add loader to the page
	}
	if (text != null) {
		$('#ccm-dialog-loader-text',$('#ccm-dialog-loader-wrapper')).remove();
		$("<div />").attr('id', 'ccm-dialog-loader-text').html(text).prependTo($("#ccm-dialog-loader-wrapper"));
	}

	var w = $("#ccm-dialog-loader-wrapper").width();
	var h = $("#ccm-dialog-loader-wrapper").height();
	var tw = $(window).width();
	var th = $(window).height();
	var _left = (tw - w) / 2;
	var _top = (th - h) / 2;
	$("#ccm-dialog-loader-wrapper").css('left', _left + 'px').css('top', _top + 'px');
	$('#ccm-dialog-loader-wrapper').show();//show loader
	//$('#ccm-dialog-loader-wrapper').fadeTo('slow', 0.2);
}

jQuery.fn.dialog.hideLoader = function() {
	$("#ccm-dialog-loader-wrapper").hide();
	$("#ccm-dialog-loader-text").remove();
}


jQuery.fn.dialog.closeTop = function() {
	$dialog = jQuery.fn.dialog.getTop();
	$dialog.jqdialog('close');
}

jQuery.fn.dialog.closeAll = function() {
	$($(".ui-dialog-content").get().reverse()).jqdialog('close');
}

