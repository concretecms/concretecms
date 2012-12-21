(function($) {

	$.fn.ccmmenu = function() {
		
		$.fn.ccmmenu.enable();

		return $.each($(this), function(i, this) {
			var $this = $(this), 
				$selector;

			if ($this.attr('data-handle')) {
				$selector = $this;
			} else {
				$selector = $this.find('[data-handle]');
			}

			var $menu = $this.find('[data-menu=' + $selector.attr('data-handle') + ']');
			$this.$menu = $menu;

			$selector.mousemove(function(e) {
				$.fn.ccmmenu.over(e, $this);
			});

		});
	}


	$.fn.ccmmenu.out = function(e) {
		if (!$.fn.ccmmenu.isactive) {
			$.fn.ccmmenu.$highlighter.css("opacity", 0);
		}
	}

	$.fn.ccmmenu.enable = function() {
		$.fn.ccmmenu.isenabled = true;
		$(document.body).append($("<div />", {'id': 'ccm-highlighter'}));
		$.fn.ccmmenu.$highlighter = $('#ccm-highlighter');

		$.fn.ccmmenu.$highlighter.on('mouseout.highlighter', function(e) {
			$.fn.ccmmenu.out(e);
		});

		$.fn.ccmmenu.$highlighter.on('mouseover.highlighter', function(e) {
			$.fn.ccmmenu.over(e);
		});

		$.fn.ccmmenu.$highlighter.on('click.highlighter', function(e) {
			$.fn.ccmmenu.show(e, $.fn.ccmmenu.$overmenu);
		});
	}

	$.fn.ccmmenu.disable = function() {
		$.fn.ccmmenu.isenabled = false;
		$.fn.ccmmenu.$highlighter.remove();
	}

	$.fn.ccmmenu.over = function(e, $this) {

		if ($.fn.ccmmenu.isenabled && (!$.fn.ccmmenu.isactive)) {

			if ($this) {

				var offset = $this.offset();
				$.fn.ccmmenu.$highlighter.css('width', $this.outerWidth())
				.css('height', $this.outerHeight())
				.css('top', offset.top)
				.css('left', offset.left);

				$.fn.ccmmenu.$overmenu = $this;
			}

			$.fn.ccmmenu.$highlighter.css('opacity', '0.6');
		}
	}

	$.fn.ccmmenu.hide = function() {
		$.fn.ccmmenu.isactive = false;
		$.fn.ccmmenu.$highlighter.css("opacity", 0);
		$(document.body).unbind('click.disableccmmenu');
		$('div.popover').css('opacity', 0).hide();
	}

	$.fn.ccmmenu.show = function(e, $this) {

		e.stopPropagation();

		$.fn.ccmmenu.isactive = true;

		var $pp = $this.$menu;

		var posX = e.pageX + 2;
		var posY = e.pageY + 2;


		$pp.css('opacity', 0).show();
		var mheight = $pp.height(),
			mwidth = $pp.width();

		if ($(window).height() < e.clientY + mheight) {
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

		$pp.find('a').click(function() {
			$.fn.ccmmenu.hide();
		});

		$(document.body).on('click.disableccmmenu', function() {
			$.fn.ccmmenu.hide();
		});

	}


}(jQuery));