/** 
 * concrete5 in context editing
 */

var CCMEditMode = function() {

	setupMenus = function() {
		$('.ccm-area').ccmmenu();
		$('.ccm-block-edit').ccmmenu();
		$('.ccm-block-edit-layout').ccmmenu();
	}

	setupBlockMovement = function() {
		
		var $dropelement;
		
		$('div.ccm-area').sortable({
			items: 'div.ccm-block-edit',
			connectWith: 'div.ccm-area',
			placeholder: "ccm-block-type-drop-holder",
			opacity: 0.4,
			over: function(e, ui) {
				$(this).addClass('ccm-area-drag-over');
				var w = $(this).width();
				$(ui.helper).css('width', w + 'px');
				return true;
			},
			out: function() {
				$(this).removeClass('ccm-area-drag-over');
			},
			receive: function(e, ui) {
				$dropelement = ui.item;
			}

		});
		$('div.ccm-block-edit').each(function() {
			var $li = $(this);
			var $sortables = $('div.ccm-area[data-accepts-block-types~=' + $li.attr('data-block-type-handle') + ']');
			$li.draggable({
				helper: function() {
					var w = $(this).width();
					var h = $(this).height();
					var $d =  $('<div />', {'class': 'ccm-block-type-dragging'}).css('width', w).css('height', h);
					return $d;
				},
				start: function(e, ui) {
					$sortables.addClass('ccm-area-drag-active');
				},
				handle: '[data-inline-command=move-block]',
				
				stop: function(e, ui) {
					if ($dropelement) {
						$dropelement.remove();
						$dropelement = false;
					}
					$sortables.removeClass('ccm-area-drag-active');
		 			$("div.ccm-block-edit").removeClass('ccm-block-arrange-enabled');
		 			$('div.ccm-block-edit').draggable().draggable('destroy');

				},
				connectToSortable: $sortables
			});
		});
	}

	return {
		start: function() {
			
			setupMenus();
			setupBlockMovement();

		},

		setupBlockForm: function(form, bID, task) {
			form.ajaxForm({
				type: 'POST',
				iframe: true,
				beforeSubmit: function() {
					$('input[name=ccm-block-form-method]').val('AJAX');
					jQuery.fn.dialog.showLoader();
					return ccm_blockFormSubmit();
				},
				success: function(r) {
					ccm_parseBlockResponse(r, bID, task);
				}
			});
		},

		activateBlockTypesOverlay: function() {
			$('#ccm-dialog-block-types-sets ul a').on('click', function() {
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


	}

}();