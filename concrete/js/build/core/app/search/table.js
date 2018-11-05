/* jshint unused:vars, undef:true, browser:true, jquery:true */

;(function(global, $) {
	'use strict';

	$('table tr[data-search-row-url]').each(function() {
		$(this).hover(function() {
			$(this).addClass('ccm-search-select-hover');
		}, function() {
			$(this).removeClass('ccm-search-select-hover');
		});

		$(this).on('click', function() {
			window.location.href = $(this).attr('data-search-row-url');
		});

	});

})(window, jQuery);
