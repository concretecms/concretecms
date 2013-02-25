/**
 * Conversations functions
 */

/** 
 * Plugin
 */
$.fn.ccmconversation = function(options) {
	
	return $.each($(this), function() {
		var $obj = $(this);
		var data = $obj.data('ccmconversation');
		if (!data) {
			$obj.data('ccmconversation', (data = new CCMConversation(this, options)));
		}
	});

}


/** 
 * Class
 */

var CCMConversation = function(element, options) {
	var obj = this;

	obj.$element = $(element);

	obj.options = $.extend({
		'method': 'ajax'
	}, options);

	var enablePosting = (obj.options.posttoken != '') ? 1 : 0;

	if (obj.options.method == 'ajax') {
		obj.$element.load(CCM_TOOLS_PATH + '/conversations/view_ajax', {
			'cnvID': obj.options.cnvID,
			'enablePosting': enablePosting
		}, function(r) {
			obj._init();
		});
	} else {
		obj.init();
	}
}

CCMConversation.prototype._init = function() {
	var obj = this;
	obj.$replyholder = obj.$element.find('div.ccm-conversation-add-reply');
	obj.$deleteholder = obj.$element.find('div.ccm-conversation-delete-message');
	obj.$messagelist = obj.$element.find('div.ccm-conversation-message-list');
	obj.$messagecnt = obj.$element.find('.ccm-conversation-message-count');
	obj.$postbuttons = obj.$element.find('button[data-submit=conversation-message]');
	obj.$sortselect = obj.$element.find('select[data-sort=conversation-message-list]');
	obj.$postbuttons.unbind().on('click', function() {
		obj._submitForm($(this));
		return false;
	});
	obj.$element.find('a[data-toggle=conversation-reply]').unbind().on('click', function() {
		var $replyform = obj.$replyholder.appendTo($(this).closest('div[data-conversation-message-id]'));
		$replyform.attr('data-form', 'conversation-reply').show();
		$replyform.find('button[data-submit=conversation-message]').attr('data-post-parent-id', $(this).attr('data-post-parent-id'));
		return false;
	});
	obj.$element.find('a[data-submit=delete-conversation-message]').unbind().on('click', function() {
		var $link = $(this);
		obj.$deletedialog = obj.$deleteholder.clone();
		obj.$deletedialog.dialog({
			modal: true,
			dialogClass: 'ccm-conversation-dialog',
			title: obj.$deleteholder.attr('data-dialog-title'),
			buttons: [
				{
					'text': obj.$deleteholder.attr('data-cancel-button-title'),
					'class': 'btn pull-left',
					'click': function() {
						obj.$deletedialog.dialog('close');
					}
				},
				{
					'text': obj.$deleteholder.attr('data-confirm-button-title'),
					'class': 'btn pull-right btn-danger',
					'click': function() {
						obj._deleteMessage($link.attr('data-conversation-message-id'));
					}
				}
			]
		});
		return false;
	});
	obj.$sortselect.unbind().on('change', function() {
		obj.$messagelist.load(CCM_TOOLS_PATH + '/conversations/view_ajax', {
			'cnvID': obj.options.cnvID,
			'task': 'get_messages',
			'orderBy': $(this).val()
		}, function(r) {
			obj._init();
		});
	});
}

CCMConversation.prototype._handlePostError = function($form, messages) {
	if (!messages) {
		var messages = ['An unspecified error occurred.'];
	}
	var s = '';
	$.each(messages, function(i, m) {
		s += m + '<br>';
	});
	$form.find('div.ccm-conversation-errors').html(s).show();
}

CCMConversation.prototype._deleteMessage = function(msgID) {

	var obj = this;
	var	formArray = [{
		'name': 'cnvMessageID',
		'value': msgID
	}];

	$.ajax({
		type: 'post',
		data: formArray,
		url: CCM_TOOLS_PATH + '/conversations/delete_message',
		success: function(html) {
			
			var $parent = $('div[data-conversation-message-id=' + msgID + ']');
			
			if ($parent.length) {
				$parent.after(html).remove();
			}
			obj._init();
			obj._updateCount();
			obj.$deletedialog.dialog('close');
		}
	});
}

CCMConversation.prototype._addMessageFromJSON = function($form, json) {
	var obj = this;
	var enablePosting = (obj.options.posttoken != '') ? 1 : 0;
	var	formArray = [{
		'name': 'cnvMessageID',
		'value': json.cnvMessageID
	}, {
		'name': 'enablePosting',
		'value': enablePosting
	}];

	$.ajax({
		type: 'post',
		data: formArray,
		url: CCM_TOOLS_PATH + '/conversations/message_detail',
		success: function(html) {
			
			var $parent = $('div[data-conversation-message-id=' + json.cnvMessageParentID + ']');
			$form.find('textarea').val('');
			
			if ($parent.length) {
				$parent.after(html);
				obj.$replyholder.appendTo(obj.$element);
				obj.$replyholder.hide();
			} else {
				obj.$element.find('.ccm-conversation-messages').prepend(html);
				obj.$element.find('.ccm-conversation-no-messages').hide();
			}

			obj._init();
			obj._updateCount();
		}
	});
}

CCMConversation.prototype._updateCount = function() {
	this.$messagecnt.load(CCM_TOOLS_PATH + '/conversations/count_header', {
		'cnvID': this.options.cnvID,
	});
}

CCMConversation.prototype._submitForm = function($btn) {
	var obj = this;
	var $form = $btn.closest('form');

	$btn.prop('disabled', true);
	$form.parent().addClass('ccm-conversation-form-submitted');
	var formArray = $form.serializeArray();
	var parentID = $btn.attr('data-post-parent-id');

	formArray.push({
		'name': 'task',
		'value': 'add'
	}, {
		'name': 'token',
		'value': obj.options.posttoken
	}, {
		'name': 'cnvID',
		'value': obj.options.cnvID
	}, {
		'name': 'cnvMessageParentID',
		'value': parentID
	});
	$.ajax({
		dataType: 'json',
		type: 'post',
		data: formArray,
		url: CCM_TOOLS_PATH + '/conversations/messages',
		success: function(r) {
			if (!r) {
				obj._handlePostError($form);
				return false;
			}
			if (r.error) {
				obj._handlePostError($form, r.messages);
				return false;
			}
			obj._addMessageFromJSON($form, r);
		},
		error: function(r) {
			obj._handlePostError($form);
			return false;
		},
		complete: function(r) {
			$btn.prop('disabled', false);
			$form.parent().closest('.ccm-conversation-form-submitted').removeClass('ccm-conversation-form-submitted');
		}
	});

}