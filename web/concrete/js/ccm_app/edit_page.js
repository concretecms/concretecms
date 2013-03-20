/** 
 * concrete5 in context editing
 */

var CCMEditMode = function() {

	var blockTypeDropSuccessful = false;
	var $sortableAreas;
	var $sortableDropElement = false;
	var $draggableElement = false;

	setupMenus = function() {
		$('.ccm-area').ccmmenu();
		$('.ccm-block-edit').ccmmenu();
		$('.ccm-block-edit-layout').ccmmenu();
	}

	saveAreaArrangement = function(cID, arHandle) {
	
		if (!cID) {
			cID = CCM_CID;
		}

		var serial = '';
		var $area = $('div.ccm-area[data-area-handle=' + arHandle + ']');
		areaStr = '&area[' + $area.attr('id').substring(1) + '][]=';

		$area.find('div.ccm-block-edit').each(function() {
			serial += areaStr + $(this).attr('data-block-id');
		});

	 	$.ajax({
	 		type: 'POST',
	 		url: CCM_DISPATCHER_FILENAME,
	 		data: 'cID=' + cID + '&ccm_token=' + CCM_SECURITY_TOKEN + '&btask=ajax_do_arrange' + serial
	 	});
	}

	parseBlockResponse = function(r, currentBlockID, task) {
		try { 
			r = r.replace(/(<([^>]+)>)/ig,""); // because some plugins add bogus HTML after our JSON requests and screw everything up
			resp = eval('(' + r + ')');
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
								saveAreaArrangement(cID, resp.arHandle);
							} else {
								$("#a" + resp.aID + " div.ccm-area-footer").before(r);
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
							ccmAlert.hud(ccmi18n.addBlockMsg, 2000, 'add', ccmi18n.addBlock);
							jQuery.fn.dialog.closeAll();
							CCMEditMode.start(); // refresh areas. 
						} else {
							ccmAlert.hud(ccmi18n.updateBlockMsg, 2000, 'success', ccmi18n.updateBlock);
						}
						if (typeof window.ccm_parseBlockResponsePost == 'function') {
							ccm_parseBlockResponsePost(resp);
						}
					}
				);
			}
		} catch(e) { 
			ccmAlert.notice(ccmi18n.error, r); 
		}
	}
	addBlockType = function(cID, aID, arHandle, $link, fromdrag) {
		var btID = $link.attr('data-btID');
		var inline = parseInt($link.attr('data-supports-inline-editing'));
		var hasadd = parseInt($link.attr('data-has-add-template'));

		if (!hasadd) {
			var action = CCM_DISPATCHER_FILENAME + "?cID=" + cID + "&arHandle=" + encodeURIComponent(arHandle) + "&btID=" + btID + "&mode=edit&processBlock=1&add=1&ccm_token=" + CCM_SECURITY_TOKEN;
			$.get(action, function(r) { parseBlockResponse(r, false, 'add'); })
		} else if (inline) {
			CCMInlineEditMode.loadAdd(cID, arHandle, aID, btID);
		} else {
			jQuery.fn.dialog.open({
				onClose: function() {
					$(document).trigger('blockWindowClose');
					if (fromdrag) {
						jQuery.fn.dialog.closeAll();
						var ccm_blockTypeDropped = false;
					}
				},
				modal: false,
				width: parseInt($link.attr('data-dialog-width')),
				height: parseInt($link.attr('data-dialog-height')) + 20,
				title: $link.attr('data-dialog-title'),
				href: CCM_TOOLS_PATH + '/add_block_popup?cID=' + cID + '&btID=' + btID + '&arHandle=' + encodeURIComponent(arHandle)
			});
		}
	}

	setupSortablesAndDroppables = function() {
		
		// empty areas are droppable. We have to 
		// declare them separately because sortable and droppable don't play as 
		// nicely together as they should.

		$emptyareas = $('div.ccm-area[data-total-blocks=0]');
		$emptyareas.droppable({
			hoverClass: 'ccm-area-drag-block-type-over',
			tolerance: 'pointer',
			accept: function($item) {
				var btHandle = $item.attr('data-block-type-handle');
				return $(this).attr('data-accepts-block-types').indexOf(btHandle) !== -1;
			},
			drop: function(e, ui) {
				if (ui.helper.is('.ccm-overlay-draggable-block-type')) {
					CCMEditMode.blockTypeDropSuccessful = true;
					// it's from the add block overlay
					addBlockType($(this).attr('data-cID'), $(this).attr('data-area-id'), $(this).attr('data-area-handle'), ui.helper, true);
				} else {
					ui.draggable.appendTo($(this));
					if (CCMEditMode.$draggableElement) {
						CCMEditMode.$sortableDropElement = CCMEditMode.$draggableElement;
					}
				}
			}
		});


		// areas with more than 1 block are sortable.
		$sortableAreas = $('div.ccm-area[data-total-blocks!=0]');
		$sortableAreas.sortable({
			items: 'div.ccm-block-edit',
			tolerance: 'pointer',
			placeholder: 'ui-state-highlight',
			opacity: 0.4,
			out: function() {
		
			},

			receive: function(e, ui) {
				if (ui.item.is('.ccm-overlay-draggable-block-type')) {
					CCMEditMode.blockTypeDropSuccessful = true;
					// it's from the add block overlay
					$(this).find('.ccm-overlay-draggable-block-type').replaceWith($('<div />', {'id': 'ccm-add-new-block-placeholder'}));
					addBlockType($(this).attr('data-cID'), $(this).attr('data-area-id'), $(this).attr('data-area-handle'), ui.helper, true);
				} else {
					CCMEditMode.$sortableDropElement = ui.item;
				}
			}
		});

		$sortableAreas.find('div.ccm-block-edit').each(function() {
			var $block = $(this);
			$block.draggable({
				cursor: 'move',
				cursorAt: {
					right: 10,
					top: 10
				},
				handle: '[data-inline-command=move-block]',
				helper: function() {
					var w = '100px';
					var h = '100px';
					var $d =  $('<div />', {'class': 'ccm-block-type-dragging'}).css('width', w).css('height', h);
					return $d;
				},
				connectToSortable: $sortableAreas.filter('[data-accepts-block-types~=' + $block.attr('data-block-type-handle') + ']'),
				stop: function() {
					$.fn.ccmmenu.enable();
					if (CCMEditMode.$sortableDropElement) {
						CCMEditMode.$sortableDropElement.remove();
						CCMEditMode.$sortableDropElement = false;
					}
					CCMToolbar.disableDirectExit();
					var _serial = '';
					$('div.ccm-area').each(function() {
						var $_area = $(this);
						$_area.find('div.ccm-block-edit').each(function() {
							var $_block = $(this);
							_serial += '&area[' + $_area.attr('data-area-id') + '][]=' + $_block.attr('data-block-id');
						});
					});

				 	$.ajax({
				 		type: 'POST',
						dataType: 'json',
				 		url: CCM_DISPATCHER_FILENAME,
				 		data: 'cID=' + CCM_CID + '&ccm_token=' + CCM_SECURITY_TOKEN + '&btask=ajax_do_arrange' + _serial,
				 		success: function(r) {
				 			ccm_parseJSON(r, function() {
				 				$('div.ccm-area').each(function() {
				 					var _arID = $(this).attr('data-area-id');
				 					var tb = 0;
				 					if (r.areas[_arID]) {
				 						var tb = r.areas[_arID];
				 					}
				 					$('div.ccm-area[data-area-id=' + _arID + ']').attr('data-total-blocks', tb);
				 				});
				 				CCMEditMode.start();
				 			});
				 		}
				 	});
				},
				start: function(e, ui) {
					// deactivate the menu on drag
					$.fn.ccmmenu.disable();
					CCMEditMode.$draggableElement = $(this);
				}
			});
		});


	}

	return {
		start: function() {			
			setupMenus();
			setupSortablesAndDroppables();
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
					parseBlockResponse(r, bID, task);
				}
			});
		},

		deleteBlock: function(cID, bID, aID, arHandle, msg) {
			if (confirm(msg)) {
				CCMToolbar.disableDirectExit();
				// got to grab the message too, eventually
				$d = $('[data-block-id=' + bID + '][data-area-id=' + aID + ']');
				$d.hide().remove();
				$.fn.ccmmenu.resethighlighter();
				ccmAlert.hud(ccmi18n.deleteBlockMsg, 2000, 'delete_small', ccmi18n.deleteBlock);
				var tb = parseInt($('[data-area-id=' + aID + ']').attr('data-total-blocks'));
				$('[data-area-id=' + aID + ']').attr('data-total-blocks', tb - 1);
				CCMEditMode.start();
				$.ajax({
					type: 'POST',
					url: CCM_DISPATCHER_FILENAME,
					data: 'cID=' + cID + '&ccm_token=' + CCM_SECURITY_TOKEN + '&isAjax=true&btask=remove&bID=' + bID + '&arHandle=' + arHandle
				});
				if (typeof window.ccm_parseBlockResponsePost == 'function') {
					ccm_parseBlockResponsePost({});
				}
			}	
		},

		activateBlockTypesOverlay: function() {
			$('#ccm-dialog-block-types-sets ul a').on('click', function() {
				$('#ccm-overlay-block-types li').hide();
				$('#ccm-overlay-block-types li[data-block-type-sets~=' + $(this).attr('data-tab') + ']').show();
				$('#ccm-dialog-block-types-sets ul a').removeClass('active');
				$(this).addClass('active');
			});

			$($('#ccm-dialog-block-types ul a').get(0)).trigger('click');

			$('#ccm-dialog-block-types').closest('.ui-dialog-content').addClass('ui-dialog-content-block-types');
			$('#ccm-block-type-search input').focus();
			if ($('#ccm-block-types-dragging').length == 0) {
				$('<div id="ccm-block-types-dragging" />').appendTo(document.body);
			}
			// remove any old add block type placeholders
			$('#ccm-add-new-block-placeholder').remove();
			$('#ccm-block-type-search input').liveUpdate('ccm-overlay-block-types');
			
			$('#ccm-block-type-search input').on('keyup', function() {
				if ($(this).val() == '') {
					$('#ccm-block-types-wrapper ul.nav-tabs').css('visibility', 'visible');
					$('#ccm-block-types-wrapper ul.nav-tabs li[class=active] a').click();
				} else {
					$('#ccm-block-types-wrapper ul.nav-tabs').css('visibility', 'hidden');
				}
			});

			$('#ccm-overlay-block-types a.ccm-overlay-draggable-block-type').each(function() {
				var $li = $(this);
				$li.css('cursor', 'move');
				$li.draggable({
					helper: 'clone',
					appendTo: $('#ccm-block-types-dragging'),
					revert: false,
					connectToSortable: $sortableAreas.filter('[data-accepts-block-types~=' + $li.attr('data-block-type-handle') + ']'),
					start: function(e, ui) {
						CCMEditMode.blockTypeDropSuccessful = false;

						// handle the dialog
						$('#ccm-block-types-wrapper').parent().jqdialog('option', 'closeOnEscape', false);
						$('#ccm-overlay-block-types').closest('.ui-dialog').fadeOut(100);
						$('.ui-widget-overlay').remove();

						// deactivate the menu on drag
						$.fn.ccmmenu.disable();						

					},
					stop: function() {
						$.fn.ccmmenu.enable();
						if (!CCMEditMode.blockTypeDropSuccessful) {
							// this got cancelled without a receive.
							jQuery.fn.dialog.closeAll();
						}
					}
				});
			});

			$('a.ccm-overlay-clickable-block-type').on('click', function() {
				addBlockType($(this).attr('data-cID'), $(this).attr('data-area-id'), $(this).attr('data-area-handle'), $(this));
				return false;
			});
			
			
		}


	}

}();