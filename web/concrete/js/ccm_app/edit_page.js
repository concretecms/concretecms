/**
 * concrete5 in context editing
 */

var CCMEditMode = function() {

		return {
			start: function() {
				if (!c5.editMode) {
					c5.editMode = new c5.EditMode();
				} else {
					c5.editMode.scanBlocks();
				}
			},

			exitPreviewMode: function() {
				$('html').removeClass('ccm-panel-preview-mode');
				$('#ccm-page-preview-frame').remove();
				$('html').removeClass('ccm-page-preview-mode');
			},

			launchPageComposer: function() {
				$('a[data-launch-panel=page]').toggleClass('ccm-launch-panel-active');
				CCMPanelManager.getByIdentifier('page').show();
				ccm_event.subscribe('panel.open',function(e) {
					var panel = e.eventData.panel;
					if (panel.options.identifier == 'page') {
						$('#' + panel.getDOMID()).find('[data-launch-panel-detail=\'page-composer\']').click();
					}
				});
			},

			setupBlockForm: function(form, bID, task) {
				form.ajaxForm({
					type: 'POST',
					iframe: true,
					beforeSubmit: function() {
						$('input[name=ccm-block-form-method]').val('AJAX');
						jQuery.fn.dialog.showLoader();
						if (typeof window.ccmValidateBlockForm == 'function') {
							r = window.ccmValidateBlockForm();
							if (ccm_isBlockError) {
								jQuery.fn.dialog.hideLoader();
								if(ccm_blockError) {
									ccmAlert.notice(ccmi18n.error, ccm_blockError + '</ul>');
								}
								ccm_resetBlockErrors();
								return false;
							}
						}
					},
					success: function(r) {
						CCMEditMode.parseBlockResponse(r, bID, task);
					}
				});
			},

			addBlockToScrapbook: function(cID, bID, arHandle) {
				CCMToolbar.disableDirectExit();
				// got to grab the message too, eventually
				$.ajax({
				type: 'POST',
				url: CCM_TOOLS_PATH + '/pile_manager',
				data: 'cID=' + cID + '&bID=' + bID + '&arHandle=' + encodeURIComponent(arHandle) + '&btask=add&scrapbookName=userScrapbook',
				success: function(resp) {
					ccmAlert.hud(ccmi18n.copyBlockToScrapbookMsg, 2000, 'add', ccmi18n.copyBlockToScrapbook);
				}});
			},

			deleteBlock: function(cID, bID, aID, arHandle, msg, callback) {
				if (confirm(msg)) {
					CCMToolbar.disableDirectExit();
					// got to grab the message too, eventually
					$d = $('[data-block-id=' + bID + '][data-area-id=' + aID + ']');
					$d.hide().remove();
					$.fn.ccmmenu.reset();
					ccmAlert.hud(ccmi18n.deleteBlockMsg, 2000, 'delete_small', ccmi18n.deleteBlock);
					var tb = parseInt($('[data-area-id=' + aID + ']').attr('data-total-blocks'));
					$('[data-area-id=' + aID + ']').attr('data-total-blocks', tb - 1);
					CCMEditMode.start();
					$.ajax({
						type: 'POST',
						url: CCM_DISPATCHER_FILENAME,
						data: 'cID=' + cID + '&ccm_token=' + CCM_SECURITY_TOKEN + '&isAjax=true&btask=remove&bID=' + bID + '&arHandle=' + encodeURIComponent(arHandle)
					});
					if (typeof(callback) == 'function') {
						callback();
					}
				}
			},

			parseBlockResponse: function(r, currentBlockID, task) {
				try {
					if (typeof(r) == 'string') {
						r = r.replace(/(<([^>]+)>)/ig,""); // because some plugins add bogus HTML after our JSON requests and screw everything up
						resp = eval('(' + r + ')');
					} else {
						resp = r;
					}

					if (resp.error == true) {
						var message = '<ul>'
						for (i = 0; i < resp.response.length; i++) {
							message += '<li>' + resp.response[i] + '<\/li>';
						}
						message += '<\/ul>';
						ccmAlert.notice(ccmi18n.error, message);
					} else {
						jQuery.fn.dialog.closeTop();
						$(document).trigger('blockWindowAfterClose');
						if (resp.cID) {
							cID = resp.cID;
						} else {
							cID = CCM_CID;
						}
						var action = CCM_TOOLS_PATH + '/edit_block_popup?cID=' + cID + '&bID=' + resp.bID + '&arHandle=' + encodeURIComponent(resp.arHandle) + '&btask=view_edit_mode';
						$.get(action,
							function(r) {
								if (task == 'add') {
									if ($('#ccm-add-new-block-placeholder').length > 0) {
										$('#ccm-add-new-block-placeholder').before(r).remove();
										saveArrangement(resp.bID, resp.aID, resp.aID);
									} else {
										$("#a" + resp.aID + " > div.ccm-area-block-list").append(r);
									}
								} else {
									$('[data-block-id=' + currentBlockID + '][data-area-id=' + resp.aID + ']').before(r).remove();
								}
								CCMInlineEditMode.exit();
								CCMToolbar.disableDirectExit();
								jQuery.fn.dialog.hideLoader();
								if (task == 'add') {
									var tb = parseInt($('div.ccm-area[data-area-id=' + resp.aID + ']').attr('data-total-blocks'));
									$('div.ccm-area[data-area-id=' + resp.aID + ']').attr('data-total-blocks', tb + 1);
									ccmAlert.hud(ccmi18n.addBlockMsg, 2000, 'ok', ccmi18n.addBlock);
									jQuery.fn.dialog.closeAll();
								} else {
									ccmAlert.hud(ccmi18n.updateBlockMsg, 2000, 'ok', ccmi18n.updateBlock);
								}
								CCMEditMode.start(); // refresh areas.
								$.fn.ccmmenu.reset();
								if (typeof window.ccm_parseBlockResponsePost == 'function') {
									ccm_parseBlockResponsePost(resp);
								}
							}
						);
					}
				} catch(e) {
					ccmAlert.notice(ccmi18n.error, r);
				}
			},

			showResponseNotification: function(message, icon, class) {
				$('<div id="ccm-notification-hud" class="ccm-ui ccm-notification ccm-notification-' + class + '"><i class="glyphicon glyphicon-' + icon + '"></i><div class="ccm-notification-inner">' + message + '</div></div>').
				appendTo(document.body).delay(5).queue(function() {
					$(this).css('opacity', 1);
					$(this).dequeue();
				}).delay(2000).queue(function() {
					$(this).css('opacity', 0);
					$(this).dequeue();
				}).delay(1000).queue(function() {
					$(this).remove();
					$(this).dequeue();
				});
			},

			setupAjaxForm: function($form) {
				$form.ajaxForm({
					type: 'post',
					dataType: 'json',
					beforeSubmit: function() {
						jQuery.fn.dialog.showLoader();
					},
					error: function(r) {
				      ccmAlert.notice('Error', '<div class="alert alert-danger">' + r.responseText + '</div>');
				  	},
					success: function(r) {
						if (r.error) {
							ccmAlert.notice('Error', '<div class="alert alert-danger">' + r.errors.join("<br>") + '</div>');
						} else {
							ccm_event.publish('AjaxFormSubmitSuccess', r, $form.get(0));
							if ($form.attr('data-dialog-form')) {
								jQuery.fn.dialog.closeTop();
							}
							CCMEditMode.showResponseNotification(r.message, 'ok', 'success');
							CCMPanelManager.exitPanelMode();
							if (r.redirectURL) {
								setTimeout(function() {
									window.location.href = r.redirectURL;
								}, 2000);
							}
						}
					},
					complete: function() {
						jQuery.fn.dialog.hideLoader();
					}
				});
				return $form;
			},
			activateAddBlocksPanel: function() {
				// Do nothing.
			}


		}

}();
