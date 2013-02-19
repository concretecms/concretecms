/** 
 * Block types
 */

var ccmLiveSearchActive = false;

ccm_activateBlockTypeTabs = function($selector) {
	$('#ccm-overlay-block-types li[data-block-type-sets~=' + $selector.find('li[class=active] a').attr('data-tab') + ']').show();
	$selector.find('a').unbind().click(function() {
		$selector.find('li').removeClass('active');
		$(this).parent().addClass('active');
		$('#ccm-overlay-block-types li').hide();
		$('#ccm-overlay-block-types li[data-block-type-sets~=' + $(this).attr('data-tab') + ']').show();
		return false;
	});
}

ccm_doAddBlockType = function(cID, aID, arHandle, $link, fromdrag) {
	var btID = $link.attr('data-btID');
	var inline = parseInt($link.attr('data-supports-inline-editing'));
	var hasadd = parseInt($link.attr('data-has-add-template'));

	if (!hasadd) {
		var action = CCM_DISPATCHER_FILENAME + "?cID=" + cID + "&arHandle=" + encodeURIComponent(arHandle) + "&btID=" + btID + "&mode=edit&processBlock=1&add=1&ccm_token=" + CCM_SECURITY_TOKEN;
		$.get(action, function(r) { ccm_parseBlockResponse(r, false, 'add'); })
	} else if (inline) {
		ccm_loadInlineEditorAdd(cID, encodeURIComponent(arHandle), aID, btID);
	} else {
		jQuery.fn.dialog.open({
			onClose: function() {
				ccm_blockWindowAfterClose();
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

ccm_activateBlockTypeOverlay = function() {
	$('#ccm-block-type-search input').focus();
	if ($('#ccm-block-types-dragging').length == 0) {
		$('<div id="ccm-block-types-dragging" />').appendTo(document.body);
	}
	// remove any old add block type placeholders
	$('#ccm-add-new-block-placeholder').remove();
	if (!ccmLiveSearchActive) {
		$('#ccm-block-type-search input').liveUpdate('ccm-overlay-block-types');
		ccmLiveSearchActive = true;
	}

	$('#ccm-block-type-search input').on('keyup', function() {
		if ($(this).val() == '') {
			$('#ccm-block-types-wrapper ul.nav-tabs').css('visibility', 'visible');
			$('#ccm-block-types-wrapper ul.nav-tabs li[class=active] a').click();
		} else {
			$('#ccm-block-types-wrapper ul.nav-tabs').css('visibility', 'hidden');
		}
	});

	var ccm_blockTypeDropped = false;

	$('div.ccm-area').sortable({
		connectWith: 'div.ccm-area',
		placeholder: "ccm-block-type-drop-holder",
		receive: function(e, ui) {
			ccm_blockTypeDropped = true;
			ccm_doAddBlockType($(this).attr('data-cID'), $(this).attr('data-aID'), $(this).attr('data-area-handle'), ui.helper, true);
			$('div.ccm-area .ccm-overlay-draggable-block-type').replaceWith($('<div />', {'id': 'ccm-add-new-block-placeholder'}));
			$('.ccm-area-drag-active').removeClass('ccm-area-drag-active');
		}
	});
	
	$('a.ccm-overlay-clickable-block-type').on('click', function() {
		ccm_doAddBlockType($(this).attr('data-cID'), $(this).attr('data-aID'), $(this).attr('data-area-handle'), $(this));
		return false;
	});
	
	$('#ccm-overlay-block-types a.ccm-overlay-draggable-block-type').each(function() {
		var $li = $(this);
		$li.css('cursor', 'move');
		var $sortables = $('div.ccm-area[data-accepts-block-types~=' + $li.attr('data-block-type-handle') + ']');
		$li.draggable({
			helper: 'clone',
			appendTo: $('#ccm-block-types-dragging'),
			revert: false,
			start: function(e, ui) {
				$('#ccm-block-types-wrapper').parent().jqdialog('option', 'closeOnEscape', false);
				$.fn.ccmmenu.disable();
				$('#ccm-overlay-block-types').parent().parent().parent().fadeOut(100);
				$('.ui-widget-overlay').remove();
				$sortables.addClass('ccm-area-drag-active');
				$('#ccm-block-types-dragging a').css('background-color', '#4FDAFF');
			},
			stop: function() {
				$.fn.ccmmenu.enable();
				if (!ccm_blockTypeDropped) {
					// this got cancelled without a receive.
					jQuery.fn.dialog.closeAll();
					$('.ccm-area-drag-active').removeClass('ccm-area-drag-active');
				}
			},
			connectToSortable: $sortables
		});
	});
}
