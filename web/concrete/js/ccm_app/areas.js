(function($) {

	$.fn.ccmarea = function() {
		return $.each($(this), function(i, this) {
			var $this = $(this);
			$this.find('.ccm-area-footer-handle').on('click', function(e) {
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
			});
		});
	}

}(jQuery));