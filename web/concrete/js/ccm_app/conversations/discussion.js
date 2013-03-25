(function($) {

$.fn.ccmdiscussion = function() {
	
	return $.each($(this), function(i, obj) {

		var $postbutton = $(this).find('[data-action=add-conversation]');
		var $postdialog = $(this).find('div[data-dialog-form=add-conversation]');
		$postbutton.on('click', function() {
			$postdialog.dialog({
				width: 620,
				height: 520,
				modal: true,
				title: $postdialog.attr('data-dialog-title'), 
				open: function() {
					/*
					$('div.ccm-discussion-form .ccm-conversation-attachment-container').each(function() {
						if($(this).is(':visible')) {
							$(this).toggle();
						}
					});
					*/
					$('.ccm-discussion-form').ccmconversationattachments();
				}
			})
			return false;
		});

	});
}

})(jQuery);