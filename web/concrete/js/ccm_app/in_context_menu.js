

/**
 * Menuing functions
 */



$.fn.ccmmenu = function() {
	
	$.fn.ccmmenu.enable();

	return $.each($(this), function(i, obj) {
		var $this = $(obj), 
			$menulauncher;

		if (!$this.prop('has-menu')) {
			$this.prop('has-menu', true);

			if (!$this.attr('data-menu-handle')) {
				$menulauncher = $this;
			} else {
				$menulauncher = $('#' + $this.attr('data-menu-handle'));
			}

			var $menu = $('#' + $this.attr('data-menu'));
			$this.$menu = $menu;
			$this.highlightClass = $this.attr('data-menu-highlight-class');
			$this.highlightOffset = 9;
			if ($this.attr('data-menu-highlight-offset')) {
				$this.highlightOffset = $this.attr('data-menu-highlight-offset');
			}
			$menulauncher.mousemove(function(e) {
				$.fn.ccmmenu.over(e, $this, $menulauncher);
			});
		}
	});
}

$.fn.ccmmenu.resetHighlight = function() {
	$.fn.ccmmenu.$highlighter.hide();
	// remove any highlight classes
	$('[data-menu-highlight-class]').each(function() {
		var className = $(this).attr('data-menu-highlight-class');
		$('.' + className).removeClass(className);
	});
}

$.fn.ccmmenu.highlight = function($obj) {

	// we offset this because we're using outlines in the page and we want the highlighter to show up over the items.
	var offset = $obj.offset();
	var t = offset.top - $.fn.ccmmenu.$overmenu.highlightOffset;
	var l = offset.left - $.fn.ccmmenu.$overmenu.highlightOffset;
	var w = $obj.outerWidth() + ($.fn.ccmmenu.$overmenu.highlightOffset * 2);
	var h = $obj.outerHeight() + ($.fn.ccmmenu.$overmenu.highlightOffset * 2);

	$.fn.ccmmenu.$highlighter.css('width', w)
	.css('height', h)
	.css('top', t)
	.css('left', l)
	.css('border-top-left-radius', $obj.css('border-top-left-radius'))
	.css('border-bottom-left-radius', $obj.css('border-bottom-left-radius'))
	.css('border-top-right-radius', $obj.css('border-top-right-radius'))
	.css('border-bottom-right-radius', $obj.css('border-bottom-right-radius'))
	.removeClass().addClass($.fn.ccmmenu.$overmenu.highlightClass);

	$.fn.ccmmenu.$highlighter.show();
}

$.fn.ccmmenu.out = function(e) {
	if (!$.fn.ccmmenu.isactive) {
		$.fn.ccmmenu.$proxy.css("opacity", 0);
		$('.ccm-parent-menu-item-active').removeClass('ccm-parent-menu-item-active');
		$('.ccm-menu-item-active').removeClass('ccm-menu-item-active');
	}
}

/** 
 * Called especially after a delete, makes sure we're not screwing about with dom elements
 * that aren't there anymore
 */

$.fn.ccmmenu.reset = function() {
	$.fn.ccmmenu.disable();
	$.fn.ccmmenu.enable();
}

$.fn.ccmmenu.enable = function() {
	$.fn.ccmmenu.isenabled = true;
	if ($("#ccm-menu-click-proxy").length == 0) {
		$(document.body).append($("<div />", {'id': 'ccm-menu-click-proxy'}));
	}
	if ($("#ccm-menu-highlighter").length == 0) {
		$(document.body).append($("<div />", {'id': 'ccm-menu-highlighter'}));
	}

	if ($("#ccm-popover-menu-container").length == 0) {
		$(document.body).append($("<div />", {'id': 'ccm-popover-menu-container', 'class': 'ccm-ui'}));
	}
	$.fn.ccmmenu.$proxy = $('#ccm-menu-click-proxy');
	$.fn.ccmmenu.$highlighter = $('#ccm-menu-highlighter');
	$.fn.ccmmenu.$holder = $('#ccm-popover-menu-container');

	$.fn.ccmmenu.$proxy.on('mouseout.clickproxy', function(e) {
		$.fn.ccmmenu.out(e);
	});

	$.fn.ccmmenu.$proxy.on('mouseover.clickproxy', function(e) {
		$.fn.ccmmenu.over(e);
	});

	$.fn.ccmmenu.$proxy.unbind('click.clickproxy').on('click.clickproxy', function(e) {
		$.fn.ccmmenu.showmenu(e, $.fn.ccmmenu.$overmenu.$menu);
		$.fn.ccmmenu.highlight($.fn.ccmmenu.$overmenu);
		$.fn.ccmmenu.$overmenu.addClass($.fn.ccmmenu.$overmenu.highlightClass);
	});
}

$.fn.ccmmenu.disable = function() {
	$.fn.ccmmenu.out();
	$.fn.ccmmenu.isenabled = false;
	$.fn.ccmmenu.$proxy.remove();
	$.fn.ccmmenu.resetHighlight();
}

$.fn.ccmmenu.over = function(e, $this, $menulauncher) {

	if ($.fn.ccmmenu.isenabled && (!$.fn.ccmmenu.isactive)) {

		$('.ccm-menu-item-active').removeClass('ccm-menu-item-active');
		$('.ccm-parent-menu-item-active').removeClass('ccm-parent-menu-item-active');

		if ($menulauncher) {

			// we offset this because we're using outlines in the page and we want the highlighter to show up over the items.
			var offset = $menulauncher.offset();
			var t = offset.top - 5;
			var l = offset.left - 5;
			var w = $menulauncher.outerWidth() + 10;
			var h = $menulauncher.outerHeight() + 10;

			$.fn.ccmmenu.$proxy.css('width', w)
			.css('height', h)
			.css('top', t)
			.css('left', l)
			.css('border-top-left-radius', $menulauncher.css('border-top-left-radius'))
			.css('border-bottom-left-radius', $menulauncher.css('border-bottom-left-radius'))
			.css('border-top-right-radius', $menulauncher.css('border-top-right-radius'))
			.css('border-bottom-right-radius', $menulauncher.css('border-bottom-right-radius'));

			$.fn.ccmmenu.$overmenu = $this;
		}
		$.fn.ccmmenu.$overmenu.addClass('ccm-menu-item-active');
		$.fn.ccmmenu.$overmenu.parent().addClass('ccm-parent-menu-item-active');
	}
}

$.fn.ccmmenu.hide = function(e) {
	if (e) {
		e.stopPropagation();
	}
	if ($.fn.ccmmenu.$proxy) {
		$.fn.ccmmenu.isactive = false;
		$.fn.ccmmenu.$proxy.css("opacity", 0);
		$.fn.ccmmenu.resetHighlight();
		$.fn.ccmmenu.$holder.html('');
		$('.ccm-menu-item-active').removeClass('ccm-menu-item-active');
		$('.ccm-parent-menu-item-active').removeClass('ccm-parent-menu-item-active');
		$(document).unbind('click.disableccmmenu');
		$('div.popover').css('opacity', 0).hide();
	}
}

$.fn.ccmmenu.showmenu = function(e, $menu) {

	e.stopPropagation();

	if ($.fn.ccmmenu.isactive) {
		$('div.popover').css('opacity', 0).hide();
	}
	$.fn.ccmmenu.isactive = true;

	var $pp = $menu.clone(true, true);
	$pp.appendTo($.fn.ccmmenu.$holder);
	$pp.find('.dialog-launch').dialog();

	var posX = e.pageX + 2;
	var posY = e.pageY + 2;

	$pp.css('opacity', 0).show();
	var mheight = $pp.height(),
		mwidth = $pp.width();

	if ($(window).height() < (e.clientY + mheight + 30)) {
		posY = posY - mheight - 10;
		posX = posX - (mwidth / 2);
		$pp.removeClass('bottom');
		$pp.addClass('top');
	} else {
		posX = posX - (mwidth / 2);
		posY = posY + 10;
		$pp.removeClass('top');
		$pp.addClass('bottom');
	}	

	$pp.css("top", posY + "px");
	$pp.css("left", posX + "px");				
	$pp.show().css('opacity', 1);

	$pp.find('a').click(function(e) {
		$.fn.ccmmenu.hide(e);
	});

	$(document).on('click.disableccmmenu', function(e) {
		$.fn.ccmmenu.hide(e);
	});

}