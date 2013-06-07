/** 
 * concrete5 in context editing
 */

var CCMEditMode = function() {

	var blockTypeDropSuccessful = false;

	setupMenus = function() {

		$('.ccm-area').each(function() {
			var totalblocks = parseInt($(this).attr('data-total-blocks'));
			var maxblocks = parseInt($(this).attr('data-maximum-blocks'));
			if (maxblocks > -1 && (totalblocks == maxblocks || totalblocks > maxblocks)) {
				$(this).find('div.ccm-area-footer li[data-list-item=block_limit_row]').hide();
			} else {
				$(this).find('div.ccm-area-footer li[data-list-item=block_limit_row]').show();
			}

			// remove the cached menus
			$(this).prop('has-menu', false);
			if (!$(this).attr('data-menu-handle')) {
				$menulauncher = $(this);
			} else {
				$menulauncher = $($(this).attr('data-menu-handle'));
			}
			$menulauncher.unbind('.ccmmenu');

			// if we have more than one block in here, we switch to using a different handle
			if (totalblocks > 0) {
				$(this).attr('data-menu-handle', '#area-menu-footer-' + $(this).attr('data-area-id'));
			} else {
				$(this).attr('data-menu-handle', '#a' + $(this).attr('data-area-id') + ', #area-menu-footer-' + $(this).attr('data-area-id'));
			}

		});
		$('.ccm-block-edit').each(function() {
			var $b = $(this);
			var bID = $b.attr('data-block-id');
			var aID = $b.attr('data-area-id');
			var arHandle = $b.closest('div.ccm-area').attr('data-area-handle');

			$b.find('a[data-menu-action=edit_inline]').unbind().on('click', function() {
				CCMInlineEditMode.editBlock(CCM_CID, aID, arHandle, bID, $(this).attr('data-menu-action-params'));
				return false;
			});
			$b.find('a[data-menu-action=block_dialog]').each(function() {
				var href = $(this).attr('data-menu-href');
				if (href.indexOf('?') !== -1) {
					href += '&cID=' + CCM_CID;
				} else {
					href += '?cID=' + CCM_CID;
				}
				href += '&arHandle=' + encodeURIComponent(arHandle) + '&bID=' + bID;
				$(this).attr('href', href);
				$(this).dialog();
				return false;
			});
			$b.find('a[data-menu-action=block_scrapbook]').unbind().on('click', function() {
				CCMEditMode.addBlockToScrapbook(CCM_CID, bID, arHandle);
				return false;
			});
			$b.find('a[data-menu-action=delete_block]').unbind().on('click', function() {
				CCMEditMode.deleteBlock(CCM_CID, bID, aID, arHandle, $(this).attr('data-menu-delete-message'));
				return false;
			});

		});		
	
		$('.ccm-area').ccmmenu();
		$('.ccm-block-edit').ccmmenu();

	}

	saveArrangement = function(sourceBlockID, sourceBlockAreaID, destinationBlockAreaID, sourceBlockTypeHandle) {
		var	cID = CCM_CID;
		jQuery.fn.dialog.showLoader();
		if (!sourceBlockTypeHandle) {
			var sourceBlockTypeHandle = '';
		}
		var serial = '&sourceBlockID=' + sourceBlockID + '&sourceBlockTypeHandle=' + sourceBlockTypeHandle + '&sourceBlockAreaID=' + sourceBlockAreaID + '&destinationBlockAreaID=' + destinationBlockAreaID
		var source = $('div.ccm-area[data-area-id=' + sourceBlockAreaID + ']');

		if (sourceBlockAreaID == destinationBlockAreaID) {
			var areaArray = [source];
		} else {
			var destination = $('div.ccm-area[data-area-id=' + destinationBlockAreaID + ']');
			var areaArray = [source, destination];
		}

		$.each(areaArray, function(idx, area) {
			var $area = $(area);
			areaStr = '&area[' + $area.attr('data-area-id') + '][]=';

			$area.find('> div.ccm-area-block-list > div.ccm-block-edit').each(function() {
				var bID = $(this).attr('data-block-id');
				if ($(this).attr('custom-style')) {
					bID += '-' + $(this).attr('custom-style');
				}
				serial += areaStr + bID;
			});
		});

	 	$.ajax({
	 		type: 'POST',
	 		url: CCM_DISPATCHER_FILENAME,
	 		dataType: 'json',
	 		data: 'cID=' + cID + '&ccm_token=' + CCM_SECURITY_TOKEN + '&btask=ajax_do_arrange' + serial,
	 		complete: function() {
		 		CCMEditMode.start();
		 	},
	 		success: function(r) {
	 			ccm_parseJSON(r, function() {
	 				jQuery.fn.dialog.hideLoader();
	 				if (sourceBlockTypeHandle == 'core_gathering_item') {
	 					destination.find('div[data-gathering-item-id=' + sourceBlockID + ']').remove();
	 					CCMEditMode.parseBlockResponse(r, r.bID, 'add');
	 				} else {
		 				if (source && destination) {
		 					// we are moving blocks from one area to another
							var stb = parseInt(source.attr('data-total-blocks'));
							var dtb = parseInt(destination.attr('data-total-blocks'));
							source.attr('data-total-blocks', stb - 1);
							destination.attr('data-total-blocks', dtb + 1);

							// we change the info on the block itself
							destination.find('div[data-block-id=' + sourceBlockID + ']').attr('data-area-id', destinationBlockAreaID);
						}
					}
					CCMToolbar.disableDirectExit();
				});
	 		}
	 	});
	}


	addBlockType = function(cID, aID, arHandle, $link, fromdrag) {
		var btID = $link.attr('data-btID');
		var inline = parseInt($link.attr('data-supports-inline-add'));
		var hasadd = parseInt($link.attr('data-has-add-template'));

		if (!hasadd) {
			var action = CCM_DISPATCHER_FILENAME + "?cID=" + cID + "&arHandle=" + encodeURIComponent(arHandle) + "&btID=" + btID + "&mode=edit&processBlock=1&add=1&ccm_token=" + CCM_SECURITY_TOKEN;
			$.get(action, function(r) { CCMEditMode.parseBlockResponse(r, false, 'add'); })
		} else if (inline) {
			CCMInlineEditMode.loadAdd(cID, arHandle, aID, btID);
		} else {
			jQuery.fn.dialog.open({
				onClose: function() {
					$(document).trigger('blockWindowClose');
					if (fromdrag) {
						jQuery.fn.dialog.closeAll();
					}
				},
				width: parseInt($link.attr('data-dialog-width')),
				height: parseInt($link.attr('data-dialog-height')) + 20,
				title: $link.attr('data-dialog-title'),
				href: CCM_TOOLS_PATH + '/add_block_popup?cID=' + cID + '&btID=' + btID + '&arHandle=' + encodeURIComponent(arHandle)
			});
		}
	}

	setupSortablesAndDroppables = function() {
		
		// clean up in case we're running twice
		$('div.ccm-area-block-dropzone').remove();
		$('.ui-droppable').droppable('destroy');

		// empty areas are droppable. We have to 
		// declare them separately because sortable and droppable don't play as 
		// nicely together as they should.

		$emptyareas = $('div.ccm-area[data-total-blocks=0]');
		$emptyareas.droppable({
			hoverClass: 'ccm-area-drag-block-type-over',
			tolerance: 'pointer',
			accept: function($item) {
				var totalblocks = parseInt($(this).attr('data-total-blocks'));
				var maxblocks = parseInt($(this).attr('data-maximum-blocks'));
				if (maxblocks == -1 || totalblocks < maxblocks) {
					var btHandle = $item.attr('data-block-type-handle');
					return $(this).attr('data-accepts-block-types').indexOf(btHandle) !== -1;
				}
				return false;
			},
			greedy: true,
			drop: function(e, ui) {
				$('.ccm-area-drag-block-type-over').removeClass('ccm-area-drag-block-type-over');
				if (ui.helper.is('.ccm-overlay-draggable-block-type')) {
					CCMEditMode.blockTypeDropSuccessful = true;
					// it's from the add block overlay
					addBlockType($(this).attr('data-cID'), $(this).attr('data-area-id'), $(this).attr('data-area-handle'), ui.helper, true);
				} else {
					// else we are dragging a block from some other area into this one.
					ui.draggable.appendTo($(this).find('.ccm-area-block-list'));
					var itemID = ui.draggable.attr('data-block-id');
					var btHandle = ui.draggable.attr('data-block-type-handle');
					if (btHandle == 'core_gathering_item') {
						var itemID = ui.draggable.attr('data-gathering-item-id');
					}
					saveArrangement(itemID, ui.draggable.attr('data-area-id'), $(this).attr('data-area-id'), btHandle);
				}
			}
		});

		var $dropzone = $('<div />').addClass('ccm-area-block-dropzone').append($('<div />').addClass('ccm-area-block-dropzone-inner'));
		$dropzone.clone().insertBefore($('.ccm-block-edit'));

		$nonemptyareas = $('div.ccm-area[data-total-blocks!=0] > div.ccm-area-block-list');
		$nonemptyareas.append($dropzone.clone());

		$('.ccm-area-block-dropzone').droppable({
			hoverClass: 'ccm-area-block-dropzone-over',
			tolerance: 'pointer',
			accept: function($item) {
				var btHandle = $item.attr('data-block-type-handle');
				if (btHandle == 'core_gathering_item') {
					return false;
				}

				var $area = $(this).closest('.ccm-area');
				var totalblocks = parseInt($area.attr('data-total-blocks'));
				var maxblocks = parseInt($area.attr('data-maximum-blocks'));
				if (maxblocks == -1 || totalblocks < maxblocks) {
					var btHandles = $area.attr('data-accepts-block-types');
					if (btHandles) {
						return btHandles.indexOf(btHandle) !== -1;
					}
				}
				return false;
			},
			drop: function(e, ui) {
				$('.ccm-area-drag-block-type-over').removeClass('ccm-area-drag-block-type-over');
				if (ui.helper.is('.ccm-overlay-draggable-block-type')) {
					CCMEditMode.blockTypeDropSuccessful = true;
					$(this).replaceWith($('<div />', {'id': 'ccm-add-new-block-placeholder'}));
					// it's from the add block overlay
					var $area = $('#ccm-add-new-block-placeholder').closest('.ccm-area');
					addBlockType($area.attr('data-cID'), $area.attr('data-area-id'), $area.attr('data-area-handle'), ui.helper, true);
				} else {
					var itemID = ui.draggable.attr('data-block-id');
					var btHandle = ui.draggable.attr('data-block-type-handle');
					/*if (btHandle == 'core_gathering_item') {
						var itemID = ui.draggable.attr('data-gathering-item-id');
					}*/

					var arID = ui.draggable.attr('data-area-id');
					var $area = $(this).closest('.ccm-area');
					$(this).replaceWith(ui.draggable.clone());
					ui.draggable.remove();
					setTimeout(function() {
						saveArrangement(itemID, arID, $area.attr('data-area-id'), btHandle);
					}, 100); // i don't know why but we need to wait a moment so that the original draggable is out of the DOM
				}
			}

		});

		$('[data-inline-command=move-block]').on('mousedown', function() {
			$('.ccm-area-block-dropzone').addClass('ccm-area-block-dropzone-active');
		});

		$('[data-inline-command=move-block]').on('mouseup', function() {
			$('.ccm-area-block-dropzone-active').removeClass('ccm-area-block-dropzone-active');
		});

		$('.ccm-block-edit').draggable({
			cursor: 'move',
			cursorAt: {
				right: 10,
				top: 10
			},
			handle: '[data-inline-command=move-block]',
			opacity: 0.5,
			helper: function() {
				var w = $(this).width();
				var h = $(this).height();
				if (h > 300) {
					h = 300;
				}
				var $d =  $('<div />', {'class': 'ccm-block-type-sorting'}).css('width', w).css('height', h);
				$d.append($(this).clone());
				return $d;
			},
			stop: function() {
				$.fn.ccmmenu.enable();
			},
			start: function(e, ui) {
				// deactivate the menu on drag
				$.fn.ccmmenu.disable();
			}
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
					CCMEditMode.parseBlockResponse(r, bID, task);
				}
			});
		},

		addBlockToScrapbook: function(cID, bID, arHandle) {
			CCMToolbar.disableDirectExit();
			// got to grab the message too, eventually
			$.ajax({
			type: 'POST',
			url: CCM_TOOLS_PATH + '/pile_manager.php',
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
								ccmAlert.hud(ccmi18n.addBlockMsg, 2000, 'add', ccmi18n.addBlock);
								jQuery.fn.dialog.closeAll();
							} else {
								ccmAlert.hud(ccmi18n.updateBlockMsg, 2000, 'success', ccmi18n.updateBlock);
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

		launchLayoutPresets: function(arLayoutID, token, task) {
			var url = CCM_TOOLS_PATH + '/area/layout_presets?arLayoutID=' + arLayoutID + '&ccm_token=' + token;
			if (task) {
				url += '&task=' + task;
			}
			jQuery.fn.dialog.open({
				width: 280,
				height: 200,
				href: url,
				title: ccmi18n.areaLayoutPresets, 
				onOpen: function() {
					$('#ccm-layout-save-preset-form select').on('change', function(r) {
						if ($(this).val() == '-1') {
							$('#ccm-layout-save-preset-name').show().focus();
							$('#ccm-layout-save-preset-override').hide();
						} else {
							$('#ccm-layout-save-preset-name').hide();
							$('#ccm-layout-save-preset-override').show();
						}
					}).trigger('change');
					/*
					$('.delete-area-layout-preset').ccmlayoutpresetdelete({'selector': selector, 'token': token});
					*/

					$('#ccm-layout-save-preset-form').on('submit', function() {

						$.fn.dialog.showLoader();
						
						var formdata = $('#ccm-layout-save-preset-form').serializeArray();
						formdata.push({'name': 'submit', 'value': 1});

						$.ajax({
							url: url,
							type: 'POST',
							data: formdata, 
							dataType: 'json',
							success: function(r) {
								$.fn.dialog.hideLoader();
								$.fn.dialog.closeAll();
							}
						});

						return false;
					});
		
				}
			});
		},

		activateBlockTypesOverlay: function() {
			$('#ccm-dialog-block-types .ccm-dialog-icon-item-grid-sets ul a').on('click', function() {
				$('#ccm-overlay-block-types li').hide();
				$('#ccm-overlay-block-types li[data-block-type-sets~=' + $(this).attr('data-tab') + ']').show();
				$('#ccm-dialog-block-types .ccm-dialog-icon-item-grid-sets ul a').removeClass('active');
				$(this).addClass('active');
				return false;
			});

			$($('#ccm-dialog-block-types ul a').get(0)).trigger('click');

			$('#ccm-dialog-block-types').closest('.ui-dialog-content').addClass('ui-dialog-content-icon-item-grid');
			$('#ccm-dialog-block-types .ccm-icon-item-grid-search input').focus();
			if ($('#ccm-block-types-dragging').length == 0) {
				$('<div id="ccm-block-types-dragging" />').appendTo(document.body);
			}
			// remove any old add block type placeholders
			$('#ccm-add-new-block-placeholder').remove();
			$('#ccm-dialog-block-types .ccm-icon-item-grid-search input').liveUpdate('ccm-overlay-block-types .ccm-overlay-icon-item-grid-list');
			
			$('#ccm-dialog-block-types .ccm-icon-item-grid-search input').on('keyup', function() {
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
					start: function(e, ui) {
						CCMEditMode.blockTypeDropSuccessful = false;
						$('.ccm-area-block-dropzone').addClass('ccm-area-block-dropzone-active');
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
