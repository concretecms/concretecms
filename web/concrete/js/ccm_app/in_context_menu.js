

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
			$this.prop('disable-highlight', $this.attr('data-menu-disable-highlight'));

			if (!$this.attr('data-menu-handle')) {
				$menulauncher = $this;
			} else {
				$menulauncher = $('#' + $this.attr('data-menu-handle'));
			}

			var $menu = $('#' + $this.attr('data-menu'));
			$this.$menu = $menu;

			$menulauncher.mousemove(function(e) {
				$.fn.ccmmenu.over(e, $this, $menulauncher);
			});
		}
	});
}


$.fn.ccmmenu.out = function(e) {
	if (!$.fn.ccmmenu.isactive) {
		$.fn.ccmmenu.$highlighter.css("opacity", 0);
		$('.ccm-menu-item-active').removeClass('ccm-menu-item-active');
	}
}

$.fn.ccmmenu.enable = function() {
	$.fn.ccmmenu.isenabled = true;
	if ($("#ccm-highlighter").length == 0) {
		$(document.body).append($("<div />", {'id': 'ccm-highlighter'}));
	}
	if ($("#ccm-popover-menu-container").length == 0) {
		$(document.body).append($("<div />", {'id': 'ccm-popover-menu-container', 'class': 'ccm-ui'}));
	}
	$.fn.ccmmenu.$highlighter = $('#ccm-highlighter');
	$.fn.ccmmenu.$holder = $('#ccm-popover-menu-container');

	$.fn.ccmmenu.$highlighter.on('mouseout.highlighter', function(e) {
		$.fn.ccmmenu.out(e);
	});

	$.fn.ccmmenu.$highlighter.on('mouseover.highlighter', function(e) {
		$.fn.ccmmenu.over(e);
	});

	$.fn.ccmmenu.$highlighter.unbind('click.highlighter').on('click.highlighter', function(e) {
		$.fn.ccmmenu.showmenu(e, $.fn.ccmmenu.$overmenu);
	});
}

$.fn.ccmmenu.disable = function() {
	$.fn.ccmmenu.isenabled = false;
	$.fn.ccmmenu.$highlighter.remove();
}

$.fn.ccmmenu.over = function(e, $this, $menulauncher) {

	if ($.fn.ccmmenu.isenabled && (!$.fn.ccmmenu.isactive)) {

		$('.ccm-menu-item-active').removeClass('ccm-menu-item-active');

		if ($menulauncher) {

			var offset = $menulauncher.offset();
			$.fn.ccmmenu.$highlighter.css('width', $menulauncher.outerWidth())
			.css('height', $menulauncher.outerHeight())
			.css('top', offset.top)
			.css('left', offset.left)
			.css('border-top-left-radius', $menulauncher.css('border-top-left-radius'))
			.css('border-bottom-left-radius', $menulauncher.css('border-bottom-left-radius'))
			.css('border-top-right-radius', $menulauncher.css('border-top-right-radius'))
			.css('border-bottom-right-radius', $menulauncher.css('border-bottom-right-radius'));

			$.fn.ccmmenu.$overmenu = $this;
		}
		$.fn.ccmmenu.$overmenu.addClass('ccm-menu-item-active');
		if ($.fn.ccmmenu.$overmenu.prop('disable-highlight')) {
			$.fn.ccmmenu.$highlighter.css('opacity', 0);
		} else {
			$.fn.ccmmenu.$highlighter.css('opacity', '0.2');
		}
	}
}

$.fn.ccmmenu.hide = function(e) {
	if (e) {
		e.stopPropagation();
	}
	
	$.fn.ccmmenu.isactive = false;
	$.fn.ccmmenu.$highlighter.css("opacity", 0);
	$.fn.ccmmenu.$holder.html('');
	$('.ccm-menu-item-active').removeClass('ccm-menu-item-active');
	$(document.body).unbind('click.disableccmmenu');
	$('div.popover').css('opacity', 0).hide();
}

$.fn.ccmmenu.showmenu = function(e, $this) {

	e.stopPropagation();

	$.fn.ccmmenu.isactive = true;

	var $pp = $this.$menu.clone(true, true);
	$pp.appendTo($.fn.ccmmenu.$holder);

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

	$(document.body).on('click.disableccmmenu', function(e) {
		$.fn.ccmmenu.hide(e);
	});

}