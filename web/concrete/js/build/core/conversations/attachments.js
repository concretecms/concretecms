(function($, window) {
	
	var methods = {
	
		init: function(options) {
			var obj = options;
            obj.$element.on('click.cnv', 'a[data-toggle=conversation-reply]', function() {
				$('.ccm-conversation-wrapper').concreteConversationAttachments('clearDropzoneQueues');
			});
			obj.$element.on('click.cnv', 'a.attachment-delete', function(event){
				event.preventDefault();
				$(this).concreteConversationAttachments('attachmentDeleteTrigger', obj);
			});
            if((obj.$editMessageHolder) && (!(obj.$editMessageHolder.find('.dropzone').attr('data-dropzone-applied')))){
                obj.$editMessageHolder.find('.dropzone').not('[data-drozpone-applied="true"]').dropzone({  // dropzone reply form
                    'url': CCM_TOOLS_PATH + '/conversations/add_file',
                    'success' : function(file, raw) {
                        var self = this;
                        $(file.previewTemplate).click(function(){
                            self.removeFile(file);
                            $('input[rel="'+ $(this).attr('rel') +'"]').remove();
                        });
                        var response = JSON.parse(raw);
                        if(!response.error) {
                            $(this.element).closest('div.ccm-conversation-edit-message').find('form.aux-reply-form').append('<input rel="'+response.timestamp+'" type="hidden" name="attachments[]" value="'+response.id+'" />');
                        } else {
                            var $form = $('.preview.processing[rel="'+response.timestamp+'"]').closest('form');
                            obj.handlePostError($form, [response.error]);
                            $('.preview.processing[rel="'+response.timestamp+'"]').remove();
                            $form.children('.ccm-conversation-errors').delay(3000).fadeOut('slow', function() {
                                $(this).html('');
                            });
                        }
                    },
                    accept: function(file, done) {
                        var errors = [];
                        var attachmentCount = this.files.length;
                        if((obj.options.maxFiles > 0) && attachmentCount > obj.options.maxFiles) {
                            errors.push('Too many files');
                            var maxFilesExceeded = true;
                        }
                        var requiredExtensions = obj.options.fileExtensions.split(',');
                        if(file.name.split('.').pop().toLowerCase() && requiredExtensions.indexOf(file.name.split('.').pop().toLowerCase()) == -1 && requiredExtensions!='') {
                            errors.push('Invalid file extension');
                            var invalidFileExtension = true;
                        }
                        if((obj.options.maxFileSize > 0) && file.size > obj.options.maxFileSize * 1000000) {
                            errors.push('Max file size exceeded');
                            var maxFileSizeExceeded = true;
                        }

                        if(maxFileSizeExceeded || maxFilesExceeded || invalidFileExtension) {
                            var self = this;
                            $('input[rel="'+ $(file.previewTemplate).attr('rel') +'"]').remove();
                            var $form = $(file.previewTemplate).parent('.dropzone');
                            self.removeFile(file);
                            obj.handlePostError($form, errors);
                            $form.children('.ccm-conversation-errors').delay(3000).fadeOut('slow', function() {
                                $(this).html('');
                            });
                            var attachmentCount =- 1;
                            done('error');  // not displayed, just needs to have argument to trigger.
                        } else {
                            done();
                        }
                    },
                    'sending' : function(file, xhr, formData) {
                        $(file.previewTemplate).attr('rel', new Date().getTime());
                        formData.append("timestamp", $(file.previewTemplate).attr('rel'));
                        formData.append("tag", $(obj.$editMessageHOlder).parent('div').attr('rel'));
                        formData.append("fileCount", $(obj.$editMessageHolder).find('[name="attachments[]"]').length);
                    },
                    'init' : function() {
                        $(this.element).data('dropzone',this);
                    }
                });
            }
			if (obj.$newmessageform.dropzone && !($(obj.$newmessageform).attr('data-dropzone-applied'))) {  // dropzone new message form
				obj.$newmessageform.dropzone({
					accept: function(file, done) {
					    var errors = [];
						 var attachmentCount = this.files.length;
						 if((obj.options.maxFiles > 0) && attachmentCount > obj.options.maxFiles) {
						 	errors.push('Too many files');
						 	var maxFilesExceeded = true;
						 }
						 var requiredExtensions = obj.options.fileExtensions.split(',');
						 if(file.name.split('.').pop().toLowerCase() && requiredExtensions.indexOf(file.name.split('.').pop().toLowerCase()) == -1 && requiredExtensions!='') {
						 	errors.push('Invalid file extension');
						 	var invalidFileExtension = true;
						 }
						 if((obj.options.maxFileSize > 0) && file.size > obj.options.maxFileSize * 1000000) {
						 	errors.push('Max file size exceeded');
						 	var maxFileSizeExceeded = true;
						 }
						 
						 if(maxFileSizeExceeded || maxFilesExceeded || invalidFileExtension) {
							var self = this;
							$('input[rel="'+ $(file.previewTemplate).attr('rel') +'"]').remove();
							var $form = $(file.previewTemplate).parent('.dropzone');
							self.removeFile(file);
							obj.handlePostError($form, errors);
							$form.children('.ccm-conversation-errors').delay(3000).fadeOut('slow', function() {
								$(this).html('');
							});
							var attachmentCount =- 1;
							done('error'); // not displayed, just needs to have argument to trigger. 
						 } else { 
						 	done(); 
						 }
				 	 },
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

						$(file.previewTemplate).attr('rel', new Date().getTime());
						formData.append("timestamp", $(file.previewTemplate).attr('rel'));
						formData.append("tag", $(obj.$newmessageform).parent('div').attr('rel'));
						formData.append("fileCount", this.files.length);
					},
					'init' : function() {
						$(this.element).data('dropzone',this);
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
					accept: function(file, done) {
					    var errors = [];
						 var attachmentCount = this.files.length;
						 if((obj.options.maxFiles > 0) && attachmentCount > obj.options.maxFiles) {
						 	errors.push('Too many files');
						 	var maxFilesExceeded = true;
						 }
						 var requiredExtensions = obj.options.fileExtensions.split(',');
						 if(file.name.split('.').pop().toLowerCase() && requiredExtensions.indexOf(file.name.split('.').pop().toLowerCase()) == -1 && requiredExtensions!='') {
						 	errors.push('Invalid file extension');
						 	var invalidFileExtension = true;
						 }
						 if((obj.options.maxFileSize > 0) && file.size > obj.options.maxFileSize * 1000000) {
						 	errors.push('Max file size exceeded');
						 	var maxFileSizeExceeded = true;
						 }
						 
						 if(maxFileSizeExceeded || maxFilesExceeded || invalidFileExtension) {
							var self = this;
							$('input[rel="'+ $(file.previewTemplate).attr('rel') +'"]').remove();
							var $form = $(file.previewTemplate).parent('.dropzone');
							self.removeFile(file);
							obj.handlePostError($form, errors);
							$form.children('.ccm-conversation-errors').delay(3000).fadeOut('slow', function() {
								$(this).html('');
							});
							var attachmentCount =- 1;
							done('error');  // not displayed, just needs to have argument to trigger. 
						 } else { 
						 	done(); 
						 }
				 	 },
					'sending' : function(file, xhr, formData) {
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
								$(this).concreteConversationAttachments('deleteAttachment',{ 'cnvMessageAttachmentID' : link.attr('rel'), 'cnvObj' : obj, 'dialogObj' : obj.$attachmentdeletetdialog });
							}
						}
					]
				});
			} else {
				if (confirm('Remove this attachment?')) {
					$(this).concreteConversationAttachments('deleteAttachment',{ 'cnvMessageAttachmentID' : link.attr('rel'), 'cnvObj' : obj, 'dialogObj' :  obj.$attachmentdeletetdialog});
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
	
	$.fn.concreteConversationAttachments = function(method) {
	
		if ( methods[method] ) {
			return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on concreteConversationAttachments' );
		}    
	
	}

})(jQuery, window);
