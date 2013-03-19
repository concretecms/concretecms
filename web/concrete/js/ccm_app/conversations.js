/**
 * $.fn.ccmconversation
 * Functions for conversation handling
 *
 * Events:
 *    beforeInitializeConversation         : Before Conversation Initialized
 *    initializeConversation               : Conversation Initialized
 *    conversationLoaded                   : Conversation Loaded
 *    conversationPostError                : Error posting message
 *    conversationBeforeDeleteMessage      : Before deleting message
 *    conversationDeleteMessage            : Deleting message
 *    conversationDeleteMessageError       : Error deleting message
 *    conversationBeforeAddMessageFromJSON : Before adding message from json
 *    conversationAddMessageFromJSON       : After adding message from json
 *    conversationBeforeUpdateCount        : Before updating message count
 *    conversationUpdateCount              : After updating message count
 *    conversationBeforeSubmitForm         : Before submitting form
 *    conversationSubmitForm               : After submitting form
 */

(function($,window){
	"use strict";
	$.extend($.fn,{
		ccmconversation:function(options) {
			return this.each(function() {
				var $obj = $(this);
				var data = $obj.data('ccmconversation');
				if (!data) {
					$obj.data('ccmconversation', (data = new CCMConversation($obj, options)));
				}
			});
		}
	});
	var CCMConversation = function(element, options) {
		this.publish("beforeInitializeConversation",{element:element,options:options});
		this.init(element,options);
		this.publish("initializeConversation",{element:element,options:options});
	};
	CCMConversation.fn = CCMConversation.prototype = {
		publish: function(t,f) {
			f = f || {};
			f.CCMConversation = this;
			window.ccm_event.publish(t,f);
		},
		init: function(element,options) {
			var obj = this;

			obj.$element = element;
			obj.options = $.extend({
				'method': 'ajax',
				'displayMode': 'threaded',
				'paginate': false,
				'itemsPerPage': -1
			}, options);

			var enablePosting = (obj.options.posttoken != '') ? 1 : 0;
			var paginate = (obj.options.paginate) ? 1 : 0;
			var orderBy = (obj.options.orderBy);
			var enableOrdering = (obj.options.enableOrdering);
			var displayPostingForm = (obj.options.displayPostingForm);
			var insertNewMessages = (obj.options.insertNewMessages);

			if (obj.options.method == 'ajax') {
				$.post(CCM_TOOLS_PATH + '/conversations/view_ajax', {
					'cnvID': obj.options.cnvID,
					'enablePosting': enablePosting,
					'itemsPerPage': obj.options.itemsPerPage,
					'paginate': paginate,
					'displayMode': obj.options.displayMode,
					'orderBy': orderBy,
					'enableOrdering': enableOrdering,
					'displayPostingForm': displayPostingForm,
					'insertNewMessages': insertNewMessages
				}, function(r) {
					obj.$element.empty().append(r);
					obj.attachBindings();
					obj.publish('conversationLoaded');
				});
			} else {
				obj.attachBindings();
				obj.finishSetup();
				obj.publish('conversationLoaded');
			}
		},
		attachBindings: function() {
			var obj = this;

			var paginate = (obj.options.paginate) ? 1 : 0;
			var enablePosting = (obj.options.posttoken != '') ? 1 : 0;

			obj.$replyholder = obj.$element.find('div.ccm-conversation-add-reply');
			obj.$newmessageform = obj.$element.find('div.ccm-conversation-add-new-message form');
			obj.$deleteholder = obj.$element.find('div.ccm-conversation-delete-message');
			obj.$messagelist = obj.$element.find('div.ccm-conversation-message-list');
			obj.$messagecnt = obj.$element.find('.ccm-conversation-message-count');
			obj.$postbuttons = obj.$element.find('button[data-submit=conversation-message]');
			obj.$sortselect = obj.$element.find('select[data-sort=conversation-message-list]');
			obj.$loadmore = obj.$element.find('[data-load-page=conversation-message-list]');
			obj.$messages = obj.$element.find('div.ccm-conversation-messages');

			if (obj.$newmessageform.dropzone) {
				obj.$newmessageform.dropzone({
					'url': CCM_TOOLS_PATH + '/conversations/add_file'
				});
			}

			obj.$element.on('click', 'button[data-submit=conversation-message]', function() {
				obj.submitForm($(this));
				return false;
			});
			obj.$element.on('click', 'a[data-toggle=conversation-reply]', function() {
				var $replyform = obj.$replyholder.appendTo($(this).closest('div[data-conversation-message-id]'));
				$replyform.attr('data-form', 'conversation-reply').show();
				$replyform.find('button[data-submit=conversation-message]').attr('data-post-parent-id', $(this).attr('data-post-parent-id'));
				return false;
			});
			obj.$element.on('click', 'a[data-submit=delete-conversation-message]', function() {
				var $link = $(this);
				obj.$deletedialog = obj.$deleteholder.clone();
				if (obj.$deletedialog.dialog) {
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
									obj.deleteMessage($link.attr('data-conversation-message-id'));
								}
							}
						]
					});
				} else {
					if (confirm('Remove this message? Replies to it will not be removed.')) {
						obj.deleteMessage($link.attr('data-conversation-message-id'));
					}
				}
				return false;
			});

			obj.$element.on('change', 'select[data-sort=conversation-message-list]', function() {
				obj.$messagelist.load(CCM_TOOLS_PATH + '/conversations/view_ajax', {
					'cnvID': obj.options.cnvID,
					'task': 'get_messages',
					'enablePosting': enablePosting,
					'displayMode': obj.options.displayMode,
					'itemsPerPage': obj.options.itemsPerPage,
					'paginate': paginate,
					'orderBy': $(this).val(),
					'enableOrdering': obj.options.enableOrdering,
					'displayPostingForm': displayPostingForm,
					'insertNewMessages': insertNewMessages
				}, function(r) {
					obj.$replyholder.appendTo(obj.$element);
					obj.attachBindings();
				});
			});

			obj.$element.on('click', '[data-load-page=conversation-message-list]', function() {
				var nextPage = parseInt(obj.$loadmore.attr('data-next-page'));
				var totalPages = parseInt(obj.$loadmore.attr('data-total-pages'));
				var data = {
					'cnvID': obj.options.cnvID,
					'itemsPerPage': obj.options.itemsPerPage,
					'displayMode': obj.options.displayMode,
					'enablePosting': enablePosting,
					'page': nextPage,
					'orderBy': obj.$sortselect.val()
				};

				$.ajax({
					type: 'post',
					data: data,
					url: CCM_TOOLS_PATH + '/conversations/message_page',
					success: function(html) {
						obj.$messages.append(html);
						if ((nextPage + 1) > totalPages) {
							obj.$loadmore.hide();
						} else {
							obj.$loadmore.attr('data-next-page', nextPage + 1);
						}
					}
				});
			});

		},
		handlePostError: function($form, messages) {
			if (!messages) {
				var messages = ['An unspecified error occurred.'];
			}
			obj.publish('conversationPostError',{messages:messages});
			var s = '';
			$.each(messages, function(i, m) {
				s += m + '<br>';
			});
			$form.find('div.ccm-conversation-errors').html(s).show();
		},
		deleteMessage: function(msgID) {

			var obj = this;
			obj.publish('conversationBeforeDeleteMessage',{msgID:msgID});
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
					obj.updateCount();
					if (obj.$deletedialog.dialog)
						obj.$deletedialog.dialog('close');
					obj.publish('conversationDeleteMessage',{msgID:msgID});
				},
				error: function(e) {
					obj.publish('conversationDeleteMessageError',{msgID:msgID,error:arguments});
					alert('Something went wrong while deleting this message, please refresh and try again.');
				}
			});
		},
		addMessageFromJSON: function($form, json) {
			var obj = this;
			obj.publish('conversationBeforeAddMessageFromJSON',{json:json,form:$form});
			var enablePosting = (obj.options.posttoken != '') ? 1 : 0;
			var	formArray = [{
				'name': 'cnvMessageID',
				'value': json.cnvMessageID
			}, {
				'name': 'enablePosting',
				'value': enablePosting
			},  {
				'name': 'displayMode',
				'value': obj.options.displayMode
			}];

			$.ajax({
				type: 'post',
				data: formArray,
				url: CCM_TOOLS_PATH + '/conversations/message_detail',
				success: function(html) {

					var $parent = $('div[data-conversation-message-id=' + json.cnvMessageParentID + ']');

					if ($parent.length) {
						$parent.after(html);
						obj.$replyholder.appendTo(obj.$element);
						obj.$replyholder.hide();
					} else {
						if (obj.options.insertNewMessages == 'bottom') {
							obj.$messages.append(html);
						} else {
							obj.$messages.prepend(html);
						}
						obj.$element.find('.ccm-conversation-no-messages').hide();
					}

					obj.publish('conversationAddMessageFromJSON',{json:json,form:$form});
					obj.updateCount();
					window.location = '#cnvMessage' + json.cnvMessageID; 
				}
			});
		},
		updateCount: function() {
			var obj = this;
			obj.publish('conversationBeforeUpdateCount');
			obj.$messagecnt.load(CCM_TOOLS_PATH + '/conversations/count_header', {
				'cnvID': obj.options.cnvID,
			},function(){
				obj.publish('conversationUpdateCount');
			});
		},
		submitForm: function($btn) {
			var obj = this;
			obj.publish('conversationBeforeSubmitForm');
			var $form = $btn.closest('form');

			$btn.prop('disabled', true);
			$form.parent().addClass('ccm-conversation-form-submitted');
			var formArray = $form.serializeArray();
			var parentID = $btn.attr('data-post-parent-id');

			formArray.push({
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
				url: CCM_TOOLS_PATH + '/conversations/add_message',
				success: function(r) {
					if (!r) {
						obj.handlePostError($form);
						return false;
					}
					if (r.error) {
						obj.handlePostError($form, r.messages);
						return false;
					}
					obj.addMessageFromJSON($form, r);
					obj.publish('conversationSubmitForm');
				},
				error: function(r) {
					obj.handlePostError($form);
					return false;
				},
				complete: function(r) {
					$btn.prop('disabled', false);
					$form.parent().closest('.ccm-conversation-form-submitted').removeClass('ccm-conversation-form-submitted');
				}
			});

		}
	};
})(jQuery,window);
