(function($) {

	$.fn.ccmmenu = function() {
		if ($('#ccm-highlighter').length == 0) {
			$(document.body).append($("<div />", {'id': 'ccm-highlighter'}));
		}
		$.fn.ccmmenu.$highlighter = $('#ccm-highlighter');

		return $.each($(this), function(i, this) {
			var $this = $(this), 
				$selector;

			if ($this.hasClass('ccm-menu-handle')) {
				$selector = $this;
			} else {
				$selector = $this.find('.ccm-menu-handle');
			}

			$selector.on('click', function(e) {
				
			});

			$selector.hover(function(e) {
				$.fn.ccmmenu.over(e, $this, function() {
					$.fn.ccmmenu.activate(e, $this)
				});
			});

			$.fn.ccmmenu.$highlighter.on('mouseout', function(e) {
				$.fn.ccmmenu.out(e, $this);
			});

			$.fn.ccmmenu.$highlighter.on('mouseover', function(e) {
				$.fn.ccmmenu.over(e, $this);
			});

		});
	}

	$.fn.ccmmenu.out = function(e, $this) {
		$.fn.ccmmenu.$highlighter.css("opacity", 0);
	}

	$.fn.ccmmenu.over = function(e, $this, clickfunction) {

		var offset = $this.offset();
		$.fn.ccmmenu.$highlighter.css('width', $this.outerWidth())
		.css('height', $this.outerHeight())
		.css('top', offset.top)
		.css('left', offset.left)
		.css('opacity', '0.6');

		$.fn.ccmmenu.$highlighter.on('click', clickfunction);
	}

	$.fn.ccmmenu.activate = function(e, $this) {
		$('div.popover').css('opacity', 0);
		var $pp = $this.find('div.popover');
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
		$pp.css('opacity', 1);

		$pp.find('a').click(function() {
			$pp.css('opacity', 0);
		});
	}


}(jQuery));