(function($) {

$.fn.ccmdiscussion = function(options) {

	this.options = $.extend({
		'title': 'Add Topic',
		'buttonTitleCancel': 'Cancel',
		'buttonTitlePost': 'Post'
	}, options);

	var discussion = this;
	
	return $.each($(this), function(i, obj) {

		var $postbutton = $(this).find('[data-action=add-conversation]');
		var $postdialog = $(this).find('div[data-dialog-form=add-conversation]');

		$postbutton.on('click', function() {
			$postdialog.dialog({
				width: 620,
				height: 550,
				modal: true,
				dialogClass: 'ccm-discussion-dialog-post',
				title: discussion.options.title,
				open: function() {
					$('.ccm-discussion-form').ccmconversationattachments();
				},
				buttons: [
					{
						'text': discussion.options.buttonTitleCancel,
						'class': 'btn pull-left',
						'click': function() {
							$(this).dialog('close');
						}
					},
					{
						'text': discussion.options.buttonTitlePost,
						'class': 'btn pull-right btn-primary',
						'click': function() {

						}
					}
				]

			})
			return false;
		});

	});
}

})(jQuery);