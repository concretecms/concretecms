(function($) {

var methods = {

	init: function(options) {
		this.options = $.extend({
			'title': 'Add Topic',
			'buttonTitleCancel': 'Cancel',
			'buttonTitlePost': 'Post',
			'dialogWrapper': 'ccm-discussion-form'
		}, options);

		var discussion = this;

		return $.each($(this), function(i, obj) {

			var $obj = $(this);

			$obj.$postbutton = $obj.find('[data-action=add-conversation]');
			$obj.$postdialog = $obj.find('div[data-dialog-form=add-conversation]');
			$obj.options = discussion.options

			$obj.$postbutton.on('click', function() {
				$obj.$postdialog.dialog({
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
							'id': 'ccm-discussion-dialog-post-btn',
							'click': function() {
								$obj.ccmdiscussion('submitForm');
							}
						}
					]

				})
				return false;
			});

			var data = $obj.data('ccmdiscussion');
			if (!data) {
				$obj.data('ccmdiscussion', $obj);
			}
		});
	},

	getForm: function() {
		var $obj = this;
		return $('.' + $obj.options.dialogWrapper + ' form[data-form=discussion-form]');
	},

	triggerError: function(messages) {
		var $obj = this,
			html = '';

		if (!messages) {
			html = 'An unspecified error occurred.';
		} else {
			for (i = 0; i < messages.length; i++) {
				html += messages[i] + '<br/>';
			}
		}
		var $errors = $obj.ccmdiscussion('getForm').find('.ccm-conversation-errors');
		$errors.html(html).show();
		$errors.delay(3000).fadeOut('slow', function() {
			$(this).html('');
		});
	},

	submitForm: function($form) {
		var $obj = this,
			formData = $obj.ccmdiscussion('getForm').serializeArray(),
			posttoken = (this.options.posttoken) ? this.options.posttoken : '';

		formData.push({
			'name': 'cParentID',
			'value': this.options.cParentID
		}, {
			'name': 'ctID',
			'value': this.options.ctID
		}, {
			'name': 'token',
			'value': posttoken
		}, {
			'name': 'cnvDiscussionID',
			'value': this.options.cnvDiscussionID
		});

		$('#ccm-discussion-dialog-post-btn').prop('disabled', true);

		$.ajax({
			dataType: 'json',
			type: 'post',
			data: formData,
			url: CCM_TOOLS_PATH + '/conversations/discussion/add_conversation',
			success: function(r) {
				if (!r) {
					$obj.ccmdiscussion('triggerError');
					return false;
				}
				if (r.error) {
					$obj.ccmdiscussion('triggerError', r.messages);
					return false;
				}
				//obj.addMessageFromJSON($form, r);
				//obj.publish('conversationSubmitForm',{form:$form,response:r});
			},
			error: function(r) {
				$obj.ccmdiscussion('triggerError');
				return false;
			},
			complete: function(r) {
				$('#ccm-discussion-dialog-post-btn').prop('disabled', false);
			}
		});

	}

}

$.fn.ccmdiscussion = function(method) {

	if ( methods[method] ) {
		return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
	} else if ( typeof method === 'object' || ! method ) {
		return methods.init.apply( this, arguments );
	} else {
		$.error( 'Method ' +  method + ' does not exist on jQuery.tooltip' );
	}

}

})(jQuery);