(function($, window) {
	
	var methods = {
	
		init: function(options) {
			var obj = options;
			obj.$element.on('click.cnv', 'a[data-toggle=conversation-reply]', function() {
				$('.ccm-conversation-wrapper').ccmconversationattachments('clearDropzoneQueues');
			});
			obj.$element.on('click.cnv', 'a.attachment-delete', function(event){
				event.preventDefault();
				$(this).ccmconversationattachments('attachmentDeleteTrigger', obj);
			});
			if (obj.$newmessageform.dropzone && !($(obj.$newmessageform).attr('data-dropzone-applied'))) {  // dropzone new message form
				obj.$newmessageform.dropzone({
					'url': CCM_TOOLS_PATH + '/conversations/add_file',
					'success' : function(file, raw) {
						var self = this;
						$(file.previewTemplate).click(function(){
								$('input[rel="'+ $(this).attr('rel') +'"]').remove();
							self.removeFile(file);
						});
						var response = JSON.parse(raw);
						if(!response.error) {
							$('div[rel="' + response.tag + '"] form.main-reply-form').append('<input rel="'+response.timestamp+'" type="hidden" name="attachments[]" value="'+response.id+'" />');
						} else {
							var $form = $('.preview.processing[rel="'+response.timestamp+'"]').closest('form');
							obj.handlePostError($form, [response.error]);
							$('.preview.processing[rel="'+response.timestamp+'"]').remove();
							$form.children('.ccm-conversation-errors').delay(3000).fadeOut('slow', function() {
								$(this).html('');
							});
						}
					},
					'sending' : function(file, xhr, formData) {
						 var attachmentCount = this.files.length;
						 if(attachmentCount > obj.options.maxFiles) {
							var self = this;
							$('input[rel="'+ $(file.previewTemplate).attr('rel') +'"]').remove();
							file.processing = false;
							self.removeFile(file);
							alert('too many attachments');
							var attachmentCount =- 1; 
							return false;
						 }

						$(file.previewTemplate).attr('rel', new Date().getTime());
						formData.append("timestamp", $(file.previewTemplate).attr('rel'));
						formData.append("tag", $(obj.$newmessageform).parent('div').attr('rel'));
						formData.append("fileCount", this.files.length);
					},
					'init' : function() {
						$(this.element).data('dropzone',this);
						// this.on("complete", function(file) {
						 	//$('.preview.processing').click(function(){ 
							//	$('input[rel="'+ $(this).attr('rel') +'"]').remove();
							//	$(this).remove();
							//})
						//});
					} 
				});
				$(obj.$newmessageform).attr('data-dropzone-applied', 'true');
			}
			
			if(!($(obj.$replyholder.find('.dropzone')).attr('data-dropzone-applied'))){ 
				obj.$replyholder.find('.dropzone').not('[data-drozpone-applied="true"]').dropzone({  // dropzone reply form
					'url': CCM_TOOLS_PATH + '/conversations/add_file',
					'success' : function(file, raw) {
						var self = this;
						$(file.previewTemplate).click(function(){
							self.removeFile(file);
							$('input[rel="'+ $(this).attr('rel') +'"]').remove();
						});
						var response = JSON.parse(raw);
						if(!response.error) {
							$(this.element).closest('div.ccm-conversation-add-reply').find('form.aux-reply-form').append('<input rel="'+response.timestamp+'" type="hidden" name="attachments[]" value="'+response.id+'" />');
						} else {
							var $form = $('.preview.processing[rel="'+response.timestamp+'"]').closest('form');
							obj.handlePostError($form, [response.error]);
							$('.preview.processing[rel="'+response.timestamp+'"]').remove();
							$form.children('.ccm-conversation-errors').delay(3000).fadeOut('slow', function() {
								$(this).html('');
							});
						}
					},
					'sending' : function(file, xhr, formData) {
						 var attachmentCount = this.files.length;
						 if(attachmentCount > 3) {
							var self = this;
							$('input[rel="'+ $(file.previewTemplate).attr('rel') +'"]').remove();
							file.processing = false;
							self.removeFile(file);
							alert('too many attachments');
							var attachmentCount =- 1; 
							return false;
						 }
						 
						$(file.previewTemplate).attr('rel', new Date().getTime());
						formData.append("timestamp", $(file.previewTemplate).attr('rel'));
						formData.append("tag", $(obj.$newmessageform).parent('div').attr('rel'));
						formData.append("fileCount", $(obj.$replyHolder).find('[name="attachments[]"]').length);
					},
					'init' : function() { 
						$(this.element).data('dropzone',this);
					}
				});
			}
			$(obj.$replyholder.find('.dropzone')).attr('data-dropzone-applied', 'true');
			return $.each($(this), function(i, obj) {
				$(this).find('.ccm-conversation-attachment-container').each(function() {
					if($(this).is(':visible')) {
						$(this).toggle();
					}
				});
			});
		}, 
		
		attachmentDeleteTrigger: function(options){
			var obj = options;
			var link = $(this);
			obj.$attachmentdeletetdialog  = obj.$attachmentdeleteholder.clone();
			if (obj.$attachmentdeletetdialog.dialog) {
				obj.$attachmentdeletetdialog.dialog({
					modal: true,
					dialogClass: 'ccm-conversation-dialog',
					title: obj.$attachmentdeletetdialog.attr('data-dialog-title'),
					buttons: [
						{
							'text': obj.$attachmentdeleteholder.attr('data-cancel-button-title'),
							'class': 'btn pull-left',
							'click': function() {
								obj.$attachmentdeletetdialog.dialog('close');
							}
						},
						{
							'text': obj.$attachmentdeleteholder.attr('data-confirm-button-title'),
							'class': 'btn pull-right btn-danger',
							'click': function() {
								$(this).ccmconversationattachments('deleteAttachment',{ 'cnvMessageAttachmentID' : link.attr('rel'), 'cnvObj' : obj, 'dialogObj' : obj.$attachmentdeletetdialog });
							}
						}
					]
				});
			} else {
				if (confirm('Remove this message? Replies to it will not be removed.')) { 
					$(this).ccmconversationattachments('deleteAttachment',{ 'cnvMessageAttachmentID' : link.attr('rel'), 'cnvObj' : obj, 'dialogObj' :  obj.$attachmentdeletetdialog});
				}
			} 
			return false;
		},
		
		clearDropzoneQueues : function() {
			$('.preview.processing').each(function(){    // first remove any previous attachments and hide dropzone if it was open.
				$('input[rel="'+ $(this).attr('rel') +'"]').remove();
			});
			$('form.dropzone').each(function(){
				var d = $(this).data('dropzone');
				$.each(d.files,function(k,v){
					d.removeFile(v);
				});
			});
		},
		
		deleteAttachment: function(options) {
			var cnvMessageAttachmentID = options.cnvMessageAttachmentID;
			var obj = options.cnvObj; 
			var attachmentsDialog = options.dialogObj;
			/* var obj = this;
			obj.publish('conversationBeforeDeleteAttachment',{cnvMessageAttachmentID:cnvMessageAttachmentID}); */
			var	formArray = [{
				'name': 'cnvMessageAttachmentID',
				'value': cnvMessageAttachmentID
			}]; 
	
			$.ajax({
				type: 'post',
				data: formArray,
				url: CCM_TOOLS_PATH + '/conversations/delete_file',
				success: function(response) {
					var parsedData = JSON.parse(response);
					$('p[rel="'+parsedData.attachmentID+'"]').parent('.attachment-container').fadeOut(300, function() { $(this).remove() });
					if (attachmentsDialog.dialog) {
						attachmentsDialog.dialog('close');
						obj.publish('conversationDeleteAttachment',{cnvMessageAttachmentID:cnvMessageAttachmentID});
					}
				},
				error: function(e) {
					obj.publish('conversationDeleteAttachmentError',{cnvMessageAttachmentID:cnvMessageAttachmentID,error:arguments});
					window.alert('Something went wrong while deleting this attachment, please refresh and try again.');
				}
			});
		}
	}
	
	$.fn.ccmconversationattachments = function(method) {
	
		if ( methods[method] ) {
			return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.tooltip' );
		}    
	
	}

})(jQuery, window);
