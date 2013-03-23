(function($) {

$.fn.ccmdiscussion = function() {
	
	return $.each($(this), function(i, obj) {

		var $postbutton = $(this).find('[data-action=add-conversation]');
		var $postdialog = $(this).find('div[data-dialog-form=add-conversation]');
		$postbutton.on('click', function() {
			$postdialog.clone().dialog({
				width: 620,
				height: 450,
				modal: true,
				title: $postdialog.attr('data-dialog-title')
			})
			return false;
		});

	});
}

})(jQuery);