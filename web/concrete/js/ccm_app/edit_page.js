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
		}


	}

}();